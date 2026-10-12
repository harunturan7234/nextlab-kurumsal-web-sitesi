<?php
declare(strict_types=1);

header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('X-Frame-Options: SAMEORIGIN');

try {
    require __DIR__ . '/app/bootstrap.php';
    $path = rawurldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/');
    if (preg_match('~[\x00-\x1f]~', $path)) { http_response_code(400); exit('Bad request'); }
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    if ($path === '/api/chat') {
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store');
        if ($method !== 'POST') {
            header('Allow: POST'); http_response_code(405);
            echo json_encode(['error' => 'POST gerekli.']); exit;
        }
        $raw = file_get_contents('php://input', false, null, 0, 8193);
        if (strlen($raw) > 8192) { http_response_code(413); echo json_encode(['error' => 'İstek çok uzun.']); exit; }
        $payload = json_decode($raw, true);
        $message = is_array($payload) ? ($payload['message'] ?? null) : null;
        if (!is_string($message) || !trim($message) || preg_match_all('/./us', $message) > 800 || !preg_match('//u', $message)) {
            http_response_code(400); echo json_encode(['error' => 'Lütfen en fazla 800 karakterlik bir mesaj yazın.']); exit;
        }
        require __DIR__ . '/app/chat.php';
        echo json_encode(['text' => catalogAnswer(trim($message), $repo), 'source' => 'catalog'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
    if (!in_array($method, ['GET', 'HEAD'], true)) { http_response_code(405); header('Allow: GET, HEAD'); exit('Method not allowed'); }
    if (in_array($path, ['/index.php', '/index.html', '/hakkimizda', '/medya'], true)) {
        header('Location: ' . (['/hakkimizda'=>'/kurumsal', '/medya'=>'/distributorlukler'][$path] ?? '/'), true, 301); exit;
    }
    if ($path !== '/' && str_ends_with($path, '/')) { header('Location: ' . rtrim($path, '/'), true, 301); exit; }
    if ($path === '/robots.txt') {
        header('Content-Type: text/plain; charset=utf-8');
        echo "User-agent: *\nAllow: /\nDisallow: /arama\nDisallow: /api/\nSitemap: " . fullUrl('sitemap.xml') . "\n"; exit;
    }
    if ($path === '/sitemap.xml') {
        $urls = ['','kurumsal','iletisim','katalog','urunler','distributorlukler','sss','cerez-politikasi','blog'];
        foreach ($repo->categories() as $c) $urls[] = 'kategori/' . $c['slug'];
        foreach ($repo->allProducts() as $p) $urls[] = 'urun/' . $p['slug'];
        foreach ($repo->distributorBrands() as $b) $urls[] = 'distributorluk/' . $b['slug'];
        foreach ($repo->blogs() as $b) $urls[] = 'blog/' . $b['slug'];
        header('Content-Type: application/xml; charset=utf-8');
        echo '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        foreach ($urls as $u) echo '<url><loc>' . e(fullUrl($u)) . '</loc></url>';
        echo '</urlset>'; exit;
    }
    switch ($path) {
        case '/':
            renderPage('home', [
                'page'=>'home','title'=>'Temizlik, Hijyen ve Kişisel Bakım',
                'metaDescription'=>'Everyday kişisel bakım ürünleri, Nextlab ortam kokuları ve profesyonel temizlik ekipmanlarını keşfedin.',
                'banners'=>$repo->banners(),'categories'=>$repo->mainCategories(),
                'featuredProducts'=>$repo->featuredProducts(),'faqs'=>$repo->faqs(),'latestBlogs'=>array_slice($repo->blogs(),0,3),
            ]); break;
        case '/kurumsal':
            renderPage('about',['page'=>'corporate','title'=>'Kurumsal','metaDescription'=>'Nextlab Cosmetic, Everyday kişisel bakım ürünleri ve kurumsal ürün yaklaşımı.']); break;
        case '/iletisim':
            renderPage('contact',['page'=>'contact','title'=>'İletişim','metaDescription'=>'Nextlab ürün bilgileri, kurumsal talepler ve tedarik seçenekleri için bizimle iletişime geçin.']); break;
        case '/sss':
            renderPage('faq',['page'=>'faq','title'=>'Sıkça Sorulan Sorular','metaDescription'=>'Nextlab ürünleri, markalar, kullanım bilgisi ve pazaryeri mağazaları hakkında yanıtlar.','faqs'=>$repo->faqs()]); break;
        case '/cerez-politikasi':
            renderPage('cookie-policy',['page'=>'cookie-policy','title'=>'Çerez ve Site Kullanımı','metaDescription'=>'Nextlab sitesinde oturum depolaması, ürün rehberi ve harici bağlantılar hakkında bilgi.']); break;
        case '/katalog':
            renderPage('catalog',['page'=>'catalog','title'=>'Ürün Kataloğu','metaDescription'=>'Nextlab temizlik ve kişisel bakım ürün gruplarını birlikte inceleyin.','categories'=>$repo->mainCategories()]); break;
        case '/urunler':
            renderPage('category',['page'=>'products','title'=>'Tüm Ürünler','metaDescription'=>'Everyday, Nextlab ve Mastermop ürünlerini marka ve kategori bilgileriyle inceleyin.','category'=>['name'=>'Tüm Ürünler','slug'=>'urunler'],'categories'=>$repo->mainCategories(),'subCategories'=>[],'products'=>$repo->allProducts()]); break;
        case '/arama':
            $query = is_string($_GET['q'] ?? null) ? trim($_GET['q']) : '';
            if (strlen($query) > 400 || !preg_match('//u', $query)) $query = '';
            renderPage('search',['page'=>'search','title'=>$query ? '“' . $query . '” Arama Sonuçları' : 'Ürün Arama','metaDescription'=>'Nextlab ürünlerini ad, marka veya katalog koduna göre arayın.','query'=>$query,'products'=>preg_match_all('/./us',$query)>=2 ? $repo->search($query) : []]); break;
        case '/distributorlukler':
            renderPage('distributors',['page'=>'distributors','title'=>'Markalar ve İş Birlikleri','metaDescription'=>'Nextlab marka rehberi, profesyonel temizlik ürünleri ve kurumsal tedarik bilgileri.','brands'=>$repo->distributorBrands()]); break;
        case '/blog':
            $filter = is_string($_GET['kategori'] ?? null) ? $_GET['kategori'] : 'all';
            renderPage('blog',['page'=>'blog','title'=>'Ürün ve Yaşam Alanları Rehberi','metaDescription'=>'Everyday bakım ürünleri, profesyonel temizlik ekipmanları ve Nextlab mağazaları için pratik rehberler.','blogs'=>$repo->blogsByCategory($filter),'allBlogs'=>$repo->blogs(),'featuredBlog'=>$repo->blogs()[0] ?? null,'currentCategory'=>$filter,'categories'=>$repo->blogCategories()]); break;
        default:
            if (preg_match('~^/kategori/([a-z0-9-]+)$~D',$path,$m) && ($category=$repo->categoryBySlug($m[1]))) {
                $items = $repo->productsByCategory($category['id']);
                renderPage('category',['page'=>'products','title'=>$category['name'],'metaDescription'=>$category['description'],'category'=>$category,'categories'=>$repo->mainCategories(),'subCategories'=>$repo->subCategories($category['id']),'products'=>$items,
                    'structuredData'=>['@context'=>'https://schema.org','@type'=>'CollectionPage','name'=>$category['name'],'url'=>fullUrl(ltrim($path,'/')),'mainEntity'=>['@type'=>'ItemList','itemListElement'=>array_map(fn($p,$i)=>['@type'=>'ListItem','position'=>$i+1,'url'=>fullUrl('urun/'.$p['slug'])],$items,array_keys($items))]],
                ]);
            } elseif (preg_match('~^/urun/([a-z0-9-]+)$~D',$path,$m) && ($p=$repo->productBySlug($m[1]))) {
                $schema = ['@context'=>'https://schema.org','@type'=>'Product','name'=>$p['name'],'brand'=>['@type'=>'Brand','name'=>$p['brand_name']],'category'=>$p['category_name'],'description'=>$p['short_description'],'url'=>fullUrl('urun/'.$p['slug'])];
                if (!empty($p['image_path'])) $schema['image']=[fullUrl('assets/'.$p['image_path'])];
                if (!$p['internal_sku']) $schema['sku']=$p['sku'];
                renderPage('product',['page'=>'products','title'=>$p['name'],'metaDescription'=>$p['short_description'],'ogType'=>'product','ogImage'=>$p['image_path'] ? fullUrl('assets/'.$p['image_path']) : '', 'structuredData'=>$schema,'product'=>$p,'related'=>$repo->relatedProducts($p['category_id'],$p['id'])]);
            } elseif (preg_match('~^/distributorluk/([a-z0-9-]+)$~D',$path,$m) && ($brand=$repo->distributorBrandBySlug($m[1]))) {
                renderPage('distributor-detail',['page'=>'distributors','title'=>$brand['name'].' Marka Rehberi','metaDescription'=>$brand['description'],'brand'=>$brand,'allBrands'=>$repo->distributorBrands()]);
            } elseif (preg_match('~^/blog/([a-z0-9-]+)$~D',$path,$m) && ($article=$repo->blogBySlug($m[1]))) {
                if ($article['slug'] !== $m[1]) { header('Location: /blog/'.$article['slug'],true,301); exit; }
                $image = $article['image_path'] ? fullUrl('assets/'.$article['image_path']) : fullUrl('assets/images/verified/nextlab-logo-1200.webp');
                $schema = ['@context'=>'https://schema.org','@type'=>'BlogPosting','headline'=>$article['title'],'description'=>$article['subtitle'],'image'=>[$image],'datePublished'=>$article['isoDate'],'dateModified'=>$article['isoDate'],'author'=>['@type'=>'Organization','name'=>'Nextlab'],'publisher'=>['@type'=>'Organization','name'=>'Nextlab','logo'=>['@type'=>'ImageObject','url'=>fullUrl('assets/images/verified/nextlab-logo-380.webp')]],'mainEntityOfPage'=>fullUrl('blog/'.$article['slug'])];
                renderPage('blog-detail',['page'=>'blog-detail','title'=>$article['title'],'metaDescription'=>$article['subtitle'],'ogType'=>'article','ogImage'=>$image,'structuredData'=>$schema,'article'=>$article,'relatedArticles'=>$repo->relatedBlogs($article['id'])]);
            } else {
                renderPage('404',['page'=>'404','title'=>'Sayfa Bulunamadı','metaDescription'=>'Aradığınız sayfa bulunamadı. Nextlab ürün kataloğuna dönün.'],404);
            }
    }
} catch (Throwable $error) {
    error_log('Nextlab application error: ' . $error->getMessage());
    http_response_code(503);
    header('Content-Type: text/html; charset=utf-8');
    header('Retry-After: 300');
    echo '<!doctype html><html lang="tr"><meta charset="utf-8"><title>Nextlab</title><h1>Siteye şu anda ulaşılamıyor.</h1><p>Lütfen daha sonra yeniden deneyin.</p></html>';
}

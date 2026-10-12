const express = require('express');
const path = require('path');
const { catalogRepository, categories, products, banners, channels } = require('./data/catalog');
const { blogRepository } = require('./data/blogs');
const { handleChat } = require('./services/chatbot');

const app = express();
const port = Number(process.env.PORT || 3000);

app.set('view engine', 'ejs');
app.set('views', path.join(__dirname, 'views'));

// Body parser for JSON requests
app.use(express.json());

// Serve static assets
app.use('/assets', express.static(path.join(__dirname, 'assets')));
app.get('/robots.txt', (req, res) => {
  res.sendFile(path.join(__dirname, 'robots.txt'));
});

// Helper to render page inside layout
function renderPage(req, res, view, data = {}, status = 200) {
  const currentPath = req.path.replace(/^\/|\/$/g, '');
  const host = req.get('host') || `localhost:${port}`;
  const protocol = req.protocol || 'http';
  const baseUrl = `${protocol}://${host}`;

  const helpers = {
    currentPath,
    baseUrl,
    title: '',
    metaDescription: '',
    canonicalUrl: '',
    ogType: 'website',
    ogImage: '',
    structuredData: null,
    page: '',
    distributorBrands: catalogRepository.distributorBrands(),
    url: (p = '') => '/' + (p ? p.replace(/^\//, '') : ''),
    asset: (p = '') => '/assets/' + (p ? p.replace(/^\//, '') : ''),
    fullUrl: (p = '') => `${baseUrl}/${p ? p.replace(/^\//, '') : ''}`,
    ...data
  };

  req.app.render(`pages/${view}`, helpers, (err, content) => {
    if (err) {
      console.error(`Error rendering page ${view}:`, err);
      return res.status(500).send('Internal Server Error');
    }
    res.status(status).render('layout', {
      ...helpers,
      content
    });
  });
}

// 1. Home
app.get('/', (req, res) => {
  renderPage(req, res, 'home', {
    page: 'home',
    title: 'Anasayfa',
    metaDescription: 'Nextlab ve Everyday ürünlerini keşfedin.',
    banners: catalogRepository.banners(),
    categories: catalogRepository.mainCategories(),
    featuredProducts: catalogRepository.featuredProducts(8),
    faqs: catalogRepository.faqs(),
    latestBlogs: blogRepository.all().slice(0, 3)
  });
});

// 2. Corporate (Kurumsal / Hakkımızda)
const handleCorporate = (req, res) => {
  renderPage(req, res, 'about', {
    page: 'corporate',
    title: 'Kurumsal',
    metaDescription: 'Nextlab Cosmetic, sade ve etkili içerik yaklaşımıyla günlük kullanıma yönelik kişisel bakım ve hijyen ürünleri geliştirir.'
  });
};
app.get('/kurumsal', handleCorporate);
app.get('/hakkimizda', handleCorporate);

// SSS (Sıkça Sorulan Sorular)
app.get('/sss', (req, res) => {
  renderPage(req, res, 'faq', {
    page: 'faq',
    title: 'Sıkça Sorulan Sorular',
    metaDescription: 'Nextlab ve Everyday ürünleri, formülasyon standartları ve sipariş süreçleri hakkında sıkça sorulan sorular.',
    faqs: catalogRepository.faqs()
  });
});

// Çerez Politikası
app.get('/cerez-politikasi', (req, res) => {
  renderPage(req, res, 'cookie-policy', {
    page: 'cookie-policy',
    title: 'Çerez Politikası',
    metaDescription: 'Nextlab Sağlık ve Kozmetik internet sitesi çerez politikası ve kişisel verilerin korunması bilgilendirmesi.'
  });
});

// 3. Distribütörlükler (15 Kozmetik Markası)
app.get('/distributorlukler', (req, res) => {
  renderPage(req, res, 'distributors', {
    page: 'distributors',
    title: 'Distribütörlükler',
    metaDescription: 'Nextlab güvencesiyle Türkiye pazarına sunulan 15 küresel dermokozmetik ve cilt bakım markası.',
    brands: catalogRepository.distributorBrands()
  });
});

// Distribütörlük Tekil Marka Detayı
app.get('/distributorluk/:slug', (req, res) => {
  const brand = catalogRepository.distributorBrandBySlug(req.params.slug);
  if (!brand) {
    return res.status(404).redirect('/distributorlukler');
  }
  renderPage(req, res, 'distributor-detail', {
    page: 'distributors',
    title: `${brand.name} Distribütörlüğü`,
    metaDescription: `${brand.name} Türkiye yetkili distribütörlüğü. ${brand.highlight}`,
    brand,
    allBrands: catalogRepository.distributorBrands()
  });
});

// Media (Geriye dönük uyumluluk)
app.get('/medya', (req, res) => {
  res.redirect(301, '/distributorlukler');
});

// Blog Listesi
app.get('/blog', (req, res) => {
  const categoryFilter = req.query.kategori || 'all';
  const filteredBlogs = blogRepository.byCategory(categoryFilter);
  const featuredBlog = blogRepository.featured();

  renderPage(req, res, 'blog', {
    page: 'blog',
    title: 'Kozmetik & Cilt Bakımı Blogu',
    metaDescription: 'Nextlab ve dermo-kozmetik uzmanlarından cilt bariyeri, K-Beauty çift aşamalı temizlik, güneş koruması ve içerik bilimini ele alan 6 güncel rehber.',
    blogs: filteredBlogs,
    allBlogs: blogRepository.all(),
    featuredBlog,
    currentCategory: categoryFilter,
    categories: blogRepository.categories()
  });
});

// Blog Detay
app.get('/blog/:slug', (req, res) => {
  const article = blogRepository.bySlug(req.params.slug);
  if (!article) {
    return res.status(404).redirect('/blog');
  }

  const relatedArticles = blogRepository.related(article.id, 3);
  const host = req.get('host') || `localhost:${port}`;
  const protocol = req.protocol || 'http';
  const baseUrl = `${protocol}://${host}`;
  const ogImage = `${baseUrl}/assets/${article.image_path.replace(/^\//, '')}`;

  const structuredData = {
    '@context': 'https://schema.org',
    '@type': 'BlogPosting',
    headline: article.title,
    description: article.subtitle,
    image: [ogImage],
    datePublished: article.isoDate,
    dateModified: article.isoDate,
    author: {
      '@type': 'Person',
      name: article.author.name
    },
    publisher: {
      '@type': 'Organization',
      name: 'Nextlab Cosmetic',
      logo: {
        '@type': 'ImageObject',
        url: `${baseUrl}/assets/images/nextlab-logo-hq.png`
      }
    },
    mainEntityOfPage: `${baseUrl}/blog/${article.slug}`
  };

  renderPage(req, res, 'blog-detail', {
    page: 'blog-detail',
    title: article.title,
    metaDescription: article.subtitle,
    ogType: 'article',
    ogImage,
    structuredData,
    article,
    relatedArticles
  });
});

// 3. Contact
app.get('/iletisim', (req, res) => {
  renderPage(req, res, 'contact', {
    page: 'contact',
    title: 'İletişim',
    metaDescription: 'Nextlab kurumsal talepleriniz ve ürün bilgileri için iletişim sayfası.'
  });
});

// 4. Catalog
app.get('/katalog', (req, res) => {
  renderPage(req, res, 'catalog', {
    page: 'catalog',
    title: 'Katalog',
    metaDescription: 'Nextlab ürün ailesi ve dijital ürün kataloğu.',
    categories: catalogRepository.mainCategories()
  });
});

// 5. All Products
app.get('/urunler', (req, res) => {
  renderPage(req, res, 'category', {
    page: 'products',
    title: 'Tüm Ürünler',
    metaDescription: 'Nextlab ve Everyday ürün kataloğundaki tüm ürünler.',
    category: { name: 'Tüm Ürünler', slug: 'urunler', description: 'Tüm Nextlab ve Everyday ürünleri' },
    categories: catalogRepository.mainCategories(),
    subCategories: [],
    products: catalogRepository.allProducts()
  });
});

// 6. Search
app.get('/arama', (req, res) => {
  const query = String(req.query.q || '').trim();
  const searchResults = query.length >= 2 ? catalogRepository.search(query) : [];

  renderPage(req, res, 'search', {
    page: 'search',
    title: query ? `“${query}” Arama Sonuçları` : 'Arama',
    metaDescription: 'Nextlab ürün arama sayfası.',
    query,
    products: searchResults
  });
});

// 7. Category by slug
app.get('/kategori/:slug', (req, res) => {
  const category = catalogRepository.categoryBySlug(req.params.slug);
  if (!category) {
    return renderPage(req, res, '404', { page: '404', title: 'Sayfa Bulunamadı' }, 404);
  }

  const subCategories = catalogRepository.subCategories(category.id);
  const categoryProducts = catalogRepository.productsByCategory(category.id);

  renderPage(req, res, 'category', {
    page: 'products',
    title: category.name,
    metaDescription: category.description || 'Nextlab ürün kategorisi',
    category,
    categories: catalogRepository.mainCategories(),
    subCategories,
    products: categoryProducts
  });
});

// 8. Product by slug
app.get('/urun/:slug', (req, res) => {
  const product = catalogRepository.productBySlug(req.params.slug);
  if (!product) {
    return renderPage(req, res, '404', { page: '404', title: 'Sayfa Bulunamadı' }, 404);
  }

  const related = catalogRepository.relatedProducts(product.category_id, product.id, 4);
  const host = req.get('host') || `localhost:${port}`;
  const protocol = req.protocol || 'http';
  const baseUrl = `${protocol}://${host}`;
  const ogImage = `${baseUrl}/assets/${product.image_path.replace(/^\//, '')}`;

  const structuredData = {
    '@context': 'https://schema.org',
    '@type': 'Product',
    name: product.name,
    sku: product.sku,
    brand: { '@type': 'Brand', name: product.brand_name || 'Nextlab' },
    category: product.category_name,
    description: product.short_description,
    image: [ogImage],
    url: `${baseUrl}/urun/${product.slug}`
  };

  renderPage(req, res, 'product', {
    page: 'products',
    title: product.name,
    metaDescription: product.short_description,
    ogType: 'product',
    ogImage,
    structuredData,
    product,
    related
  });
});

// 9. Sitemap XML
app.get('/sitemap.xml', (req, res) => {
  const host = req.get('host') || `localhost:${port}`;
  const protocol = req.protocol || 'http';
  const baseUrl = `${protocol}://${host}`;
  const today = new Date().toISOString().split('T')[0];

  const urls = [
    { loc: baseUrl, date: today },
    { loc: `${baseUrl}/hakkimizda`, date: today },
    { loc: `${baseUrl}/iletisim`, date: today },
    { loc: `${baseUrl}/katalog`, date: today },
    { loc: `${baseUrl}/urunler`, date: today },
    { loc: `${baseUrl}/distributorlukler`, date: today },
    { loc: `${baseUrl}/sss`, date: today },
    { loc: `${baseUrl}/cerez-politikasi`, date: today },
    { loc: `${baseUrl}/blog`, date: today }
  ];

  catalogRepository.distributorBrands().forEach(b => {
    urls.push({ loc: `${baseUrl}/distributorluk/${b.slug}`, date: today });
  });

  blogRepository.all().forEach(b => {
    urls.push({ loc: `${baseUrl}/blog/${b.slug}`, date: b.isoDate || today });
  });

  catalogRepository.mainCategories().forEach(c => {
    urls.push({ loc: `${baseUrl}/kategori/${c.slug}`, date: c.updated_at || today });
  });

  catalogRepository.allProducts().forEach(p => {
    urls.push({ loc: `${baseUrl}/urun/${p.slug}`, date: p.updated_at || today });
  });

  let xml = '<?xml version="1.0" encoding="UTF-8"?>\n<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">\n';
  urls.forEach(item => {
    xml += `  <url><loc>${item.loc}</loc><lastmod>${item.date}</lastmod></url>\n`;
  });
  xml += '</urlset>';

  res.header('Content-Type', 'application/xml; charset=UTF-8');
  res.send(xml);
});

// 10. AI Chatbot API Endpoint
app.post('/api/chat', async (req, res) => {
  try {
    const { message, history } = req.body || {};
    if (!message || typeof message !== 'string' || !message.trim()) {
      return res.status(400).json({ error: 'Lütfen bir mesaj yazın.' });
    }
    const result = await handleChat(message, history);
    res.json(result);
  } catch (err) {
    console.error('Chat API error:', err);
    res.status(500).json({
      error: 'Asistan şu anda yanıt veremiyor.',
      text: 'Üzgünüm, şu anda yanıt oluşturulurken bir aksaklık yaşandı. Lütfen sorunuzu tekrar iletin veya info@nextlabcosmetic.com adresinden bizimle iletişime geçin.'
    });
  }
});

// 11. Fallback 404
app.use((req, res) => {
  renderPage(req, res, '404', {
    page: '404',
    title: 'Sayfa Bulunamadı',
    metaDescription: 'Aradığınız sayfa bulunamadı.'
  }, 404);
});

app.listen(port, '0.0.0.0', () => {
  console.log(`Nextlab server running on port ${port}`);
});

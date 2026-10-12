<?php
declare(strict_types=1);

function catalogAnswer(string $message, CatalogRepository $repo): string {
    $text = CatalogRepository::normalize($message);
    $channels = $repo->channels();
    if (preg_match('/fiyat|siparis|satin|trendyol|hepsiburada/', $text)) return "Güncel fiyat, stok ve teslimat bilgileri için mağazalarımızı ziyaret edebilirsiniz.\n\n[Trendyol mağazamız](" . $channels['trendyol'] . ")\n[Hepsiburada mağazamız](" . $channels['hepsiburada'] . ')';
    if (preg_match('/sertifika|gmp|iso|dermatolojik|uts|tedavi|gebe|hamile|hassas|alerji/', $text)) return 'Ürüne özel içerik, uygunluk ve sertifika bilgileri için ambalajı ve üretici belgelerini esas alın. Kişisel sağlık soruları için bir sağlık uzmanına danışın. [İletişim](/iletisim)';
    $terms = array_values(array_filter(preg_split('/\s+/', $text), fn($w) => strlen($w) > 2 && !in_array($w, ['icin','nedir','urun','urunleri','var','merhaba'], true)));
    $ranked = [];
    foreach ($repo->allProducts() as $p) {
        $search = CatalogRepository::normalize($p['name'] . ' ' . $p['category_name'] . ' ' . $p['brand_name']);
        $score = count(array_filter($terms, fn($w) => str_contains($search, $w)));
        if ($score) $ranked[] = ['product' => $p, 'score' => $score];
    }
    usort($ranked, fn($a,$b) => $b['score'] <=> $a['score']);
    if ($ranked) {
        $lines = array_map(fn($r) => '• [' . $r['product']['name'] . '](/urun/' . $r['product']['slug'] . ') — ' . $r['product']['short_description'], array_slice($ranked, 0, 4));
        return "Kataloğumuzdan ilgili ürünler:\n\n" . implode("\n", $lines) . "\n\nKullanım ve uyarılar için ambalaj talimatlarını izleyin.";
    }
    return "Nextlab ürün rehberine hoş geldiniz. Everyday bakım bantları, ağız bakımı, Nextlab ortam kokuları ve Ceymop temizlik ekipmanları hakkında ürün adıyla arama yapabilirsiniz.\n\n[Ürün kataloğu](/urunler) · [Kurumsal iletişim](/iletisim)";
}

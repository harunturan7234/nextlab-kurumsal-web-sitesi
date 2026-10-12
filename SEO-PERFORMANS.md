# SEO ve Performans Özeti

Tarih: 2026-10-12 · Yerel doğrulama (PHP 8.3).

## SEO — doğrulanan

| Öğe | Durum | Not |
|---|---|---|
| Sayfa bazlı `<title>` | ✅ | Her rota benzersiz; "… \| Nextlab" kalıbı |
| Meta description | ✅ | Her sayfa için özgün |
| Canonical | ✅ | `config.app.url` tabanlı mutlak URL |
| Open Graph | ✅ | type/site_name/title/description/url; ürün sayfasında `og:type=product` |
| Twitter/OG görsel | ✅ | Ürün ve blogda `og:image` |
| JSON-LD yapısal veri | ✅ | Ana: `Organization`; ürün: `Product`+`Brand`; kategori: `CollectionPage`+`ItemList`; blog: `BlogPosting` |
| SEO dostu URL | ✅ | `/kategori/<slug>`, `/urun/<slug>`, `/blog/<slug>` |
| XML sitemap | ✅ | `/sitemap.xml` dinamik (kategori+ürün+marka+blog) |
| robots.txt | ✅ | `/arama` ve `/api/` disallow; Sitemap satırı |
| Breadcrumb | ✅ | Kategori/ürün/blog sayfalarında |
| Görsel `alt` metinleri | ✅ | Ürün ve içerik görsellerinde |
| Başlık hiyerarşisi (H1–H6) | ✅ | Her sayfada tek H1 |
| 301 yönlendirme | ✅ | Eski yollar (`/hakkimizda`→`/kurumsal`, `/medya`→`/distributorlukler`), sondaki `/` temizliği |

> **İçerik ilkesi:** Doğrulanamayan sertifika, üretim kapasitesi, sağlık iddiası, fiyat,
> stok veya kullanıcı yorumu **eklenmedi**. Yer tutuculu ürünlerde sahte foto kullanılmadı.

## Performans — ölçülen (yerel, sıkıştırmasız → gzip)

| Kaynak | Ham | Gzip (≈) | Not |
|---|---|---|---|
| Ana sayfa HTML | 51 KB | ~10 KB | SSR, tek istek |
| `professional.css` | 11 KB | — | Kritik üzeri stiller |
| `style.css` | 86 KB | 16 KB | Ana stil; `.htaccess` `mod_deflate` sıkıştırır |
| `app.js` | 4 KB | — | `defer`; vanilla JS |
| Hero görseli (WebP) | ~40 KB | — | `fetchpriority=high`, `loading=eager` |
| Doğrulanmış görseller (toplam) | 1.2 MB | — | `srcset` ile yalnızca gereken boyut in

### Uygulanan performans teknikleri
- **WebP + srcset + sizes:** Tüm doğrulanmış görseller 3–4 genişlik türeviyle sunuluyor.
- **Lazy loading:** Hero dışındaki görseller `loading="lazy"`, `decoding="async"`.
- **Hero önceliklendirme:** İlk görsel `loading="eager"` + `fetchpriority="high"`; logo `preload` ile.
- **CLS önleme:** Görsellerde `width`/`height` öznitelikleri var.
- **mod_deflate:** HTML/CSS/JS/SVG/XML gzip'leniyor (`.htaccess`).
- **Cache-Control:** Görseller için `max-age=604800` (`.htaccess`).
- **Derlemesiz:** Üretimde Node/build adımı yok; saf PHP + statik varlık.

### İyileştirme fırsatları (opsiyonel, kritik değil)
- `style.css` (86 KB) büyük bir utility CSS; ileride kullanılmayan sınıflar ayıklanabilir.
- AVIF türevleri eklenebilir (bu makinede araç yok; hosting veya CI'da üretilebilir).
- Hosting'de HTTP/2 + tarayıcı cache + CDN ile hero teslim süresi daha da düşer.

## Güvenlik (performansla birlikte, §11)
- Güvenlik başlıkları: `X-Content-Type-Options`, `Referrer-Policy`, `X-Frame-Options` (hem PHP hem `.htaccess`).
- Çıktı kaçışı: tüm HTML çıktıları `e()` (htmlspecialchars, ENT_QUOTES) üzerinden — XSS'e karşı.
- PDO: gerçek prepared statements, `ATTR_EMULATE_PREPARES=false`.
- Dizin listeleme kapalı; `app/`, `data/`, `config.php`, `.sql`, `.json`, `.md` web'den `.htaccess` ile engelli.
- API girişi (`/api/chat`): boyut sınırı (8 KB), UTF-8 doğrulama, 800 karakter limiti, yalnızca POST.

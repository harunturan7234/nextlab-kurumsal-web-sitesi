# Test Raporu

Tarih: 2026-10-12 · Ortam: yerel (PHP 8.3.35 CLI + MySQL 8.0.46, `router.php`)
Test modu: hem `file` (JSON) hem `mysql` modunda doğrulandı.

## Özet

| Alan | Sonuç |
|---|---|
| PHP sözdizimi (tüm `.php`) | ✅ Tümü temiz (`php -l`) |
| Sayfa yönlendirmeleri | ✅ Tüm rotalar 200; bilinmeyen rota 404 |
| MySQL bağlantısı & veri | ✅ `database.sql` gerçek MySQL 8.0'a aktarıldı; site DB'den aynen render ediyor |
| İç bağlantılar | ✅ 55 benzersiz bağlantı tarandı, **0 kırık** |
| Dış bağlantılar (yeni sekme) | ✅ 41 `target="_blank"` bağlantının tamamında `rel="noopener"` |
| Arama | ✅ "burun"→8, "mop"→3, anlamsız sorgu→0 |
| Ürün detay / pazaryeri CTA | ✅ Trendyol/Hepsiburada yönlendirmeleri çalışıyor |
| SEO etiketleri | ✅ title, description, canonical, OG, JSON-LD (bkz. SEO-PERFORMANS.md) |
| Mobil (375px) | ✅ Yatay taşma yok; hamburger menü açılıyor |
| Kırık görsel | ✅ Yok — 6 ürün gerçek foto, 7 ürün zarif yer tutucu |

## 1. PHP sözdizimi
`index.php`, `router.php`, `app/` altındaki tüm PHP dosyaları `php -l` ile hatasız.

## 2. Rota / yönlendirme testi (mysql modu)

| Rota | Kod |
|---|---|
| `/` | 200 |
| `/urunler` | 200 |
| `/katalog` | 200 |
| `/kategori/burun-bantlari` | 200 |
| `/urun/everyday-yetiskin-burun-bandi-12li` | 200 |
| `/distributorlukler` · `/distributorluk/ceymop` | 200 |
| `/blog` · `/blog/<slug>` (6 yazı) | 200 |
| `/iletisim` · `/sss` · `/kurumsal` · `/cerez-politikasi` | 200 |
| `/arama?q=burun` | 200 |
| `/sitemap.xml` · `/robots.txt` | 200 |
| `/olmayan-sayfa` | **404** (doğru) |
| sona `/` (ör. `/urunler/`) | 301 → kanonik |

## 3. Veritabanı (MySQL)
- `database.sql` boş bir MySQL 8.0 veritabanına **hatasız içe aktarıldı**.
- Satır sayıları: 9 kategori, 13 ürün, 30 içerik (3 banner + 6 SSS + 15 marka + 5 blog + 1 kanal).
- `config.php` `data_mode=mysql` iken site tüm sayfaları **veritabanından** render etti; çıktı `file` moduyla birebir aynı, PHP uyarısı/hatası yok.
- Türkçe karakterler (utf8mb4) korundu.

## 4. Bağlantılar
- **İç:** 10 ana sayfadan toplanan 55 benzersiz dahili bağlantı; hepsi 200/301. Kırık yok.
- **Dış:** Instagram, LinkedIn, Trendyol, Hepsiburada, Google (harita). Tüm yeni-sekme bağlantıları `rel="noopener noreferrer"` ile güvenli; `aria-label` mevcut.

## 5. Görsel QA (masaüstü + mobil)
- **Masaüstü:** Header hizalı, logo net, hero slider (01/03) kontrollü, kategori ve "öne çıkanlar" kartları gerçek fotoğraflarla düzgün.
- **Mobil (375×812):** Hero başlığı/CTA düzgün sarıyor, ürün kartları yatay kaydırmalı, **yatay taşma yok** (`scrollWidth == innerWidth`), hamburger menü tüm bağlantıları açıyor.
- Görseli olmayan 7 ürün, kart düzenini bozmayan "Ürün fotoğrafı hazırlanıyor" yer tutucusuyla gösteriliyor.

## Test edilemeyen / kapsam dışı
- **Canlı Natro dağıtımı:** Gerçek hosting erişimi olmadığından yalnızca yerel doğrulama yapıldı. `.htaccess` kuralları yerelde `router.php` ile taklit edildi; gerçek Apache `mod_rewrite` davranışı hosting'de doğrulanmalıdır.
- **7 eksik ürün görseli:** Resmî kaynak (nextlabcosmetic.com) erişimi zaman aşımına uğradı; pazaryeri sayfaları bot korumalı. Ayrıntı: [`MISSING_ASSETS.md`](MISSING_ASSETS.md).
- **Sosyal medya hesap içerikleri:** Bağlantı biçimi doğrulandı; hedef hesaplar açılıp denetlenmedi.

# Nextlab Kurumsal Web Sitesi

PHP 8 ve MySQL/MariaDB ile çalışan, derleme gerektirmeyen kurumsal ürün katalog sitesi. Tasarım; verilen referansın bilgi mimarisi ve yerleşim yaklaşımı incelenerek, Nextlab için özgün kod ve geçici SVG görsellerle yeniden geliştirilmiştir.

## Gereksinimler

- PHP 8.0+ (PDO MySQL eklentisi; mbstring önerilir)
- MySQL 5.7+ veya MariaDB 10.3+
- Apache `mod_rewrite`

## Natro / public_html kurulumu

1. Bu klasördeki tüm dosya ve klasörleri `public_html` içine yükleyin.
2. Hosting panelinden boş bir MySQL veritabanı ve kullanıcı oluşturun.
3. `database.sql` dosyasını phpMyAdmin üzerinden içe aktarın.
4. Mevcut/ilk kurulum veritabanında `migrations/2026-10-11_professional_catalog.sql` dosyasını bir kez çalıştırın.
5. `config.example.php` dosyasını `config.php` adıyla kopyalayın.
6. `config.php` içindeki veritabanı adı, kullanıcı adı, parola ve `app.url` değerini gerçek bilgilerle değiştirin.
7. `robots.txt` içindeki sitemap alanını gerçek alan adıyla güncelleyin.
8. Alan adında `/`, `/urunler`, kategori, ürün detayı ve `/sitemap.xml` adreslerini kontrol edin.

`config.php` git tarafından paylaşılmamalı ve web sunucusu tarafından doğrudan erişilemez durumdadır. Üretimde `debug=false` ve `demo_mode=false` kalmalıdır.

## Yerel önizleme

Gerçek `config.php` bulunmadığında uygulama otomatik olarak salt okunur örnek veri modunda açılır:

```bash
php -S 127.0.0.1:8080 router.php
```

Ardından `http://127.0.0.1:8080` adresini açın. Apache ortamında `router.php` gerekmez; `.htaccess` yönlendirmeyi yapar.

## Mimari

- `index.php`: SEO uyumlu rotalar ve sayfa denetimi
- `app/CatalogRepository.php`: Tüm katalog veri erişimi; ileride panel aynı tabloları yönetebilir
- `app/views`: Ortak layout, header/footer ve tekrar kullanılan kart bileşenleri
- `assets`: Derleme gerektirmeyen CSS, JavaScript ve özgün yer tutucu görseller
- `database.sql`: Şema ve başlangıç verileri
- `sitemap.php`: Veritabanındaki kategori ve ürünlerden dinamik sitemap

Pazaryeri bağlantıları `marketplace_links` tablosunda çoklu mağaza yapısına hazırdır. Yönetim paneli bu sürümün kapsamı dışındadır.

## Güvenlik notları

PDO gerçek prepared statement kullanır, tüm HTML çıktıları kaçış işleminden geçer, dizin listeleme kapalıdır, hassas yapılandırma ve SQL dosyaları Apache üzerinden engellenir. İletişim formu bu ilk sürümde veri saklamaz; kullanıcının e-posta uygulamasını açar.

# Natro Kurulum Rehberi

Nextlab sitesini Natro paylaşımlı hosting üzerinde (PHP 8.x + MySQL/MariaDB, Apache)
yayına almak için adım adım rehber. Site **Node.js gerektirmez**; üretimde yalnızca
PHP ve Apache çalışır.

## 0. Dağıtım paketini hazırlayın

Yerelde, yüklenecek temiz klasörü üretin:

```bash
bash scripts/build-dist.sh
```

Bu komut şunları oluşturur:
- `dist/public_html/` — hosting'e yüklenecek klasör (yalnızca üretim dosyaları)
- `dist/nextlab-public_html.zip` — tek dosyada yükleme için zip

Paket Node.js, EJS şablonları, kaynak görseller (`source-assets/`), testler ve
geliştirme araçlarını **içermez**.

## 1. Dosyaları yükleyin

1. Natro **hPanel → Dosya Yöneticisi** (veya FTP/SFTP) ile alan adınızın `public_html`
   klasörünü açın.
2. `dist/nextlab-public_html.zip` dosyasını `public_html` içine yükleyip **"Çıkart"** deyin
   (ya da `dist/public_html/` içeriğini doğrudan yükleyin).
3. `.htaccess` dosyasının yüklendiğinden emin olun (gizli dosyalar görünmüyorsa Dosya
   Yöneticisi ayarından "gizli dosyaları göster"i açın). Apache yönlendirmesi buna bağlıdır.

## 2. Veritabanını oluşturun

1. Natro hPanel → **MySQL Veritabanları**.
2. Yeni bir **boş veritabanı** ve bir **kullanıcı** oluşturun; kullanıcıyı veritabanına
   **tüm yetkilerle** ekleyin. Veritabanı adı, kullanıcı adı ve parolayı not alın.
3. **phpMyAdmin**'i açın → oluşturduğunuz veritabanını seçin → **İçe Aktar** sekmesi →
   `database.sql` dosyasını seçip **Başlat**.
   - Alternatif (SSH erişimi varsa):
     ```bash
     mysql -u KULLANICI -p VERITABANI < database.sql
     ```
4. İçe aktarma sonrası `nextlab_categories`, `nextlab_products`, `nextlab_content`
   tablolarının dolu olduğunu doğrulayın (9 kategori, 13 ürün, 32 içerik satırı).

## 3. config.php dosyasını oluşturun

1. `public_html` içinde `config.example.php` dosyasını `config.php` adıyla kopyalayın.
2. İçindeki değerleri gerçek bilgilerle doldurun:

```php
<?php
return [
    'app' => [
        'url'       => 'https://www.alanadiniz.com', // gerçek alan adınız (sonda / yok)
        'data_mode' => 'mysql',                      // DB kullanımı için
        'debug'     => false,                        // üretimde MUTLAKA false
    ],
    'database' => [
        'host'     => 'localhost',   // Natro genelde 'localhost'
        'port'     => 3306,
        'name'     => 'VERITABANI_ADI',
        'user'     => 'KULLANICI_ADI',
        'password' => 'PAROLA',
    ],
];
```

> `config.php` asla GitHub'a gönderilmez ve `.htaccess` üzerinden web'den erişime kapalıdır.
> `data_mode` yerine `'file'` yazarsanız site veritabanı olmadan JSON kataloğuyla çalışır
> (hızlı önizleme / yedek mod).

## 4. robots.txt ve alan adı

- `index.php`, `/robots.txt` ve `/sitemap.xml` adreslerini **dinamik** üretir; ayrıca içlerindeki
  `Sitemap:` satırı `config.php`'deki `app.url`'den otomatik oluşur. Ekstra düzenleme gerekmez.
- Statik `robots.txt` da pakette bulunur; Apache yönlendirmesi dinamik sürümü sunar.

## 5. Yayını doğrulayın

Tarayıcıda şu adresleri kontrol edin (alan adınızla):

- `/` (ana sayfa, hero slider) · `/urunler` · `/katalog`
- `/kategori/burun-bantlari` (kategori) · `/urun/everyday-yetiskin-burun-bandi-12li` (ürün detay)
- `/distributorlukler` · `/blog` · `/iletisim` · `/sss`
- `/sitemap.xml` (XML görünmeli) · `/robots.txt`
- Olmayan bir adres (ör. `/olmayan-sayfa`) → **404** döndürmeli

## 6. HTTPS ve son kontroller

1. Natro'dan **ücretsiz SSL (Let's Encrypt)** sertifikasını etkinleştirin.
2. `config.php` içindeki `app.url`'in `https://` ile başladığından emin olun.
3. (Opsiyonel) HTTP → HTTPS ve www yönlendirmesini hPanel'den veya `.htaccess`'e ekleyin.
4. `debug=false` olduğunu bir kez daha doğrulayın.

## Güncelleme (ürün/görsel değişince)

1. Yerelde `data/catalog.json` / `data/blogs.json` güncelle.
2. `php scripts/export-sql.php > database.sql`
3. phpMyAdmin'den yeni `database.sql`'i içe aktar (tabloları yeniden kurar) **veya**
   ileride yönetim panelinden aynı tabloları düzenle (mimari buna hazırdır).
4. Görsel eklendiyse `dist`'i yeniden üretip `assets/` klasörünü yükle.

## Sorun giderme

| Belirti | Olası neden / çözüm |
|---|---|
| "Siteye şu anda ulaşılamıyor" | `config.php` DB bilgileri yanlış ya da `database.sql` içe aktarılmamış |
| Görseller kırık | `assets/` klasörü eksik yüklendi; `.htaccess` yüklenmemiş olabilir |
| Düz/temasız sayfa | `assets/css/` yüklenmedi veya yol yanlış; `.htaccess` kontrol et |
| 404 yerine Apache hatası | `mod_rewrite` kapalı veya `.htaccess` yok |
| Türkçe karakter bozuk | DB `utf8mb4` olmalı (database.sql bunu kurar), `config.php` doğru DB'yi göstermeli |

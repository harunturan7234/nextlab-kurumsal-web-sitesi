# Natro Kurulum Rehberi

Nextlab sitesi **PHP 8 + MySQL** ile çalışır, Node.js gerektirmez.
Kurulum tek formdan yapılır; elle dosya düzenlemenize gerek yoktur.

---

## Kurulum — 4 adım

### 1) Natro'da boş bir MySQL veritabanı oluşturun
hPanel → **MySQL Veritabanları** → yeni bir **veritabanı** ve **kullanıcı** oluşturun,
kullanıcıyı veritabanına **tüm yetkilerle** ekleyin.
Şu üç bilgiyi not alın: **veritabanı adı · kullanıcı adı · parola**.

> Veritabanının **boş** olması yeterli. Tabloları kurulum kendisi oluşturur;
> `database.sql` dosyasını elle içe aktarmanıza **gerek yoktur**.

### 2) Dosyaları yükleyin
`nextlab-NATRO-YUKLE.zip` dosyasını alan adınızın **`public_html`** klasörüne yükleyip
**"Çıkart"** deyin. (Alternatif: `dist/public_html/` içeriğini doğrudan yükleyin.)

> `.htaccess` dosyasının da yüklendiğinden emin olun. Görünmüyorsa Dosya
> Yöneticisi ayarlarından **"gizli dosyaları göster"** seçeneğini açın.

### 3) Kurulum sihirbazını açın
Tarayıcıda: **`https://alanadiniz.com/kurulum.php`**

Tek formda şunları girin:
- **Site adresi** — otomatik algılanır, genelde dokunmanıza gerek kalmaz
- **Veritabanı** — sunucu (`localhost`), port (`3306`), ad, kullanıcı, parola
- **Yönetici hesabı** — ad, e-posta ve **en az 10 karakterlik** parola

**Kurulumu Tamamla**'ya basın. Sihirbaz sizin yerinize şunları yapar:
tabloları kurar ve içeriği yükler · `config.php` dosyasını yazar ·
görsel yükleme klasörünü açar · yönetici hesabınızı oluşturur.

### 4) Bitti
**"Yönetim Paneline Giriş Yap"** ile `/admin` adresine girin.
Kurulum sayfası bu andan sonra kendini kilitler. İsterseniz `kurulum.php`
dosyasını sunucudan silebilirsiniz (zorunlu değil).

---

## Yönetim paneli — `https://alanadiniz.com/admin`

Sitenin **tüm** içeriği buradan yönetilir:

| Bölüm | İçerik |
|---|---|
| **Ürünler** | Ad, marka, kategori, kod, görsel, açıklama, kullanım, pazaryeri bağlantısı, öne çıkarma |
| **Kategoriler** | Ad, URL, üst kategori, açıklama, görsel |
| **Blog Yazıları** | Zengin metin editörü (başlık, liste, bağlantı), kapak görseli, özet, anahtar kelimeler |
| **Ana Sayfa Banner** | Hero slaytları: başlık, açıklama, buton, görsel |
| **S.S.S** | Soru-cevaplar (Google'da zengin sonuç olarak çıkar) |
| **Markalar** | Marka rehberi kayıtları |
| **Pazaryeri Mağazaları** | Trendyol / Hepsiburada bağlantıları |
| **Site Ayarları** | Sosyal medya, adres, e-posta, harita, kurumsal metin, SEO |
| **Görseller** | Görsel yükleme ve kitaplık |

### Taslak → Yayın mantığı (en önemli kısım)

| Durum | Anlamı |
|---|---|
| **Yayında** | Kayıt sitede görünüyor ve güncel |
| **Değişiklik var** | Düzenlediniz ama site **hâlâ eski hâlini** gösteriyor |
| **Taslak** | Yeni eklediniz, **henüz sitede görünmüyor** |

- **"Taslağa Kaydet"** → değişiklik saklanır, site **değişmez**
- **"Kaydet ve Yayına Al"** → değişiklik **anında** siteye yansır
- Listede her satırın sağındaki **"Yayına Al"** tek kaydı yayınlar
- Sağ üstteki **"Tümünü Yayına Al"** bekleyen tüm değişiklikleri tek seferde yayınlar
- **⋯ → Yayından kaldır** kaydı siteden gizler (silmez)
- **⋯ → Değişiklikleri geri al** yayınlanmamış düzenlemeleri iptal eder

---

## Güvenlik

Parolalar bcrypt ile saklanır · CSRF koruması · oturum sertleştirme ·
5 hatalı girişte 10 dakika kilit · PDO prepared statements · tüm çıktılarda XSS kaçışı ·
yüklenen dosyalarda betik çalıştırma kapalı · `config.php` web'den erişilemez ·
`/admin` arama motorlarına kapalı (noindex).

---

## Sonradan güncelleme

Site içeriğini değiştirmek için **sunucuya dosya yüklemenize gerek yoktur** —
her şey panelden yapılır ve "Yayına Al" ile anında yayınlanır.

Yalnızca tasarım/kod değişikliği gerekirse yerelde `bash scripts/build-dist.sh`
çalıştırıp `dist/public_html/` içeriğini yükleyin. **`config.php` dosyasının
üzerine yazmayın** (veritabanı bilgileriniz orada).

---

## Sorun giderme

| Belirti | Çözüm |
|---|---|
| Kurulumda "Veritabanına bağlanılamadı" | Veritabanı adı/kullanıcı/parola ve sunucu (`localhost`) bilgilerini kontrol edin |
| "config.php yazılamadı" | `public_html` klasörüne yazma izni verin (755) veya ekrandaki içerikle dosyayı elle oluşturup kurulumu tekrarlayın |
| Panelde görsel yüklenmiyor | `assets/images/uploads` klasörüne **755** izni verin |
| Görseller kırık / sayfa temasız | `assets/` klasörü eksik yüklenmiş; `.htaccess`'in yüklendiğini doğrulayın |
| 404 yerine sunucu hatası | `mod_rewrite` kapalı ya da `.htaccess` yüklenmemiş |
| "Kurulum zaten tamamlanmış" | Normaldir — güvenlik için kilitlenmiştir. `/admin` adresinden giriş yapın |

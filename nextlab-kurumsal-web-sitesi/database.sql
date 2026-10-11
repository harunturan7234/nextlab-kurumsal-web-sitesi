SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS marketplace_links;
DROP TABLE IF EXISTS product_images;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS brands;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS banners;
DROP TABLE IF EXISTS site_settings;

CREATE TABLE categories (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  parent_id INT UNSIGNED NULL,
  name VARCHAR(160) NOT NULL,
  slug VARCHAR(180) NOT NULL UNIQUE,
  description VARCHAR(500) NULL,
  image_path VARCHAR(255) NULL,
  sort_order INT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_category_parent FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL,
  INDEX idx_categories_parent_active (parent_id,is_active,sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE brands (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  slug VARCHAR(140) NOT NULL UNIQUE,
  logo_path VARCHAR(255) NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE products (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  category_id INT UNSIGNED NOT NULL,
  brand_id INT UNSIGNED NULL,
  sku VARCHAR(80) NOT NULL UNIQUE,
  name VARCHAR(200) NOT NULL,
  slug VARCHAR(220) NOT NULL UNIQUE,
  short_description VARCHAR(500) NULL,
  description TEXT NULL,
  image_path VARCHAR(255) NULL,
  meta_title VARCHAR(255) NULL,
  meta_description VARCHAR(320) NULL,
  is_featured TINYINT(1) NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  sort_order INT NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_product_category FOREIGN KEY (category_id) REFERENCES categories(id),
  CONSTRAINT fk_product_brand FOREIGN KEY (brand_id) REFERENCES brands(id) ON DELETE SET NULL,
  INDEX idx_products_listing (category_id,is_active,sort_order),
  FULLTEXT INDEX ftx_products_search (name,sku,short_description,description)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE product_images (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  product_id INT UNSIGNED NOT NULL,
  image_path VARCHAR(255) NOT NULL,
  alt_text VARCHAR(255) NULL,
  is_primary TINYINT(1) NOT NULL DEFAULT 0,
  sort_order INT NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_image_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
  INDEX idx_product_images (product_id,is_primary,sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE marketplace_links (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  product_id INT UNSIGNED NOT NULL,
  marketplace_name VARCHAR(120) NOT NULL,
  url VARCHAR(1000) NOT NULL,
  sort_order INT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_marketplace_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
  INDEX idx_marketplace_links (product_id,is_active,sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE banners (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(220) NOT NULL,
  subtitle VARCHAR(500) NULL,
  image_path VARCHAR(255) NOT NULL,
  mobile_image_path VARCHAR(255) NULL,
  button_text VARCHAR(80) NULL,
  button_url VARCHAR(500) NULL,
  sort_order INT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  starts_at DATETIME NULL,
  ends_at DATETIME NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_banners_active (is_active,sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE site_settings (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  setting_key VARCHAR(120) NOT NULL UNIQUE,
  setting_value TEXT NULL,
  setting_type ENUM('text','textarea','boolean','json') NOT NULL DEFAULT 'text',
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO categories (id,parent_id,name,slug,description,image_path,sort_order) VALUES
(1,NULL,'Temizlik ve Hijyen','temizlik-ve-hijyen','Profesyonel ve günlük temizlik çözümleri','images/category-cleaning.svg',10),
(2,NULL,'Kozmetik ve Kişisel Bakım','kozmetik-ve-kisisel-bakim','Özenli kişisel bakım ürünleri','images/category-care.svg',20),
(3,1,'Yüzey Temizleyiciler','yuzey-temizleyiciler','Sert yüzeylere yönelik ürünler',NULL,10),
(4,2,'Vücut Bakımı','vucut-bakimi','Günlük kişisel bakım ürünleri',NULL,10);

INSERT INTO brands (id,name,slug) VALUES (1,'Nextlab Pro','nextlab-pro'),(2,'Nextlab Care','nextlab-care');

INSERT INTO products (id,category_id,brand_id,sku,name,slug,short_description,description,image_path,is_featured,sort_order) VALUES
(1,1,1,'NXT-101','Çok Amaçlı Yüzey Temizleyici','cok-amacli-yuzey-temizleyici','Günlük kullanım için ferah kokulu yüzey temizleyici.','Farklı yıkanabilir yüzeylerde günlük temizlik için geliştirilmiştir. İz bırakmayan formülü ve ferah kokusuyla pratik kullanım sunar.','images/product-cleaner.svg',1,10),
(2,1,1,'NXT-102','Konsantre Sıvı Sabun','konsantre-sivi-sabun','Yoğun kullanıma uygun nazik el temizliği.','Ellerin günlük temizliği için dengeli ve kolay durulanan formül.','images/product-soap.svg',1,20),
(3,1,1,'NXT-103','Mutfak Hijyen Spreyi','mutfak-hijyen-spreyi','Mutfak yüzeyleri için güçlü günlük bakım.','Tezgâh ve sert mutfak yüzeylerinde kullanım için tasarlanan pratik sprey formu.','images/product-spray.svg',1,30),
(4,1,1,'NXT-104','Endüstriyel Zemin Bakım Ürünü','endustriyel-zemin-bakim-urunu','Geniş alanlar için ekonomik konsantre çözüm.','Profesyonel temizlik ekiplerinin geniş zeminlerde kullanımı için hazırlanmıştır.','images/product-floor.svg',0,40),
(5,2,2,'NXT-201','Nemlendirici El Kremi','nemlendirici-el-kremi','Günlük bakım için hızlı emilen yumuşak doku.','Gün içinde ihtiyaç duyulan nem desteğini sağlayan bakım kremi.','images/product-cream.svg',1,10),
(6,2,2,'NXT-202','Canlandırıcı Duş Jeli','canlandirici-dus-jeli','Taze kokulu, nazik günlük vücut temizliği.','Günlük duş rutini için nazikçe temizleyen jel formül.','images/product-gel.svg',1,20),
(7,2,2,'NXT-203','Besleyici Saç Bakım Şampuanı','besleyici-sac-bakim-sampuani','Yumuşak ve canlı görünen saçlar için bakım.','Saçın günlük temizliğine yardımcı olan dengeli şampuan.','images/product-shampoo.svg',1,30),
(8,2,2,'NXT-204','Günlük Vücut Losyonu','gunluk-vucut-losyonu','Hafif dokulu, konforlu günlük nem bakımı.','Cilde kolayca yayılan hafif dokusuyla günlük vücut bakımını tamamlar.','images/product-lotion.svg',0,40);

INSERT INTO product_images (product_id,image_path,alt_text,is_primary,sort_order)
SELECT id,image_path,name,1,0 FROM products;

INSERT INTO banners (title,subtitle,image_path,button_text,button_url,sort_order) VALUES
('Temizlikte güvenilir çözüm ortağınız','Profesyonel alanlardan evlere uzanan güçlü ürün ailesi','images/banner-cleaning.svg','Ürünleri İncele','urunler',10),
('Kişisel bakımda yalın ve etkili','Günlük rutininiz için özenle geliştirilen ürünler','images/banner-care.svg','Kategoriyi Keşfet','kategori/kozmetik-ve-kisisel-bakim',20);

INSERT INTO site_settings (setting_key,setting_value,setting_type) VALUES
('site_name','Nextlab','text'),('contact_email','info@nextlab.com.tr','text'),('contact_phone','+90 212 000 00 00','text');

SET FOREIGN_KEY_CHECKS=1;

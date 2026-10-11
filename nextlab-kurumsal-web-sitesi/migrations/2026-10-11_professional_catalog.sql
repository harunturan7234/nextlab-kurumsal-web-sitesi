-- Existing records are preserved. Previous placeholder products are only deactivated.
SET NAMES utf8mb4;

ALTER TABLE products ADD COLUMN usage_info TEXT NULL AFTER description;
ALTER TABLE products ADD COLUMN package_info VARCHAR(160) NULL AFTER usage_info;

INSERT INTO categories (id,parent_id,name,slug,description,image_path,sort_order,is_active) VALUES
(30,2,'Cilt Bakım Bantları','cilt-bakim-bantlari','Hidrokolloid ve burun bölgesine yönelik bakım bantları.','images/category-care.svg',10,1),
(40,1,'Ağız Bakımı','agiz-bakimi','Günlük ağız bakım ürünleri.','images/product-oral.svg',10,1),
(50,1,'Ortam Kokulandırma','ortam-kokulandirma','Ortam kokuları ve kokulandırma çözümleri.','images/product-fragrance.svg',20,1)
ON DUPLICATE KEY UPDATE name=VALUES(name),description=VALUES(description),image_path=VALUES(image_path),is_active=1;

UPDATE products SET is_active=0 WHERE id BETWEEN 1 AND 8 AND sku LIKE 'NXT-%';

INSERT INTO brands (id,name,slug,is_active) VALUES
(3,'Everyday','everyday',1),(4,'NextLab','nextlab',1)
ON DUPLICATE KEY UPDATE name=VALUES(name),is_active=1;

INSERT INTO products (id,category_id,brand_id,sku,name,slug,short_description,description,usage_info,package_info,image_path,is_featured,is_active,sort_order) VALUES
(101,30,3,'EVR-NB12','Everyday Yetişkin Burun Bandı 12’li','everyday-yetiskin-burun-bandi-12li','Yetişkin kullanımı için 12 adet burun bandı.','Everyday markasının yetişkin kullanımına yönelik 12’li burun bandı paketidir.','Ambalaj üzerindeki kullanım talimatlarını izleyin.','12 adet','images/product-nose-strip.svg',1,1,10),
(102,30,3,'EVR-NP06','Everyday Nose Patch Temizleyici Burun Bandı 6’lı','everyday-nose-patch-6li','Burun bölgesi için 6 adet temizleyici bakım bandı.','Burun bölgesindeki günlük bakım rutinini tamamlamak üzere sunulan 6’lı bant paketidir.','Temiz cilde ambalaj yönergelerine göre uygulayın.','6 adet','images/product-nose-patch.svg',1,1,20),
(103,30,3,'EVR-APP36','Everyday Hidrokolloid Acne Pimple Patch 36’lı','everyday-hidrokolloid-acne-pimple-patch-36li','Günlük cilt bakım rutini için 36 adet hidrokolloid bant.','Bölgesel cilt bakımında pratik kullanım için hazırlanan hidrokolloid bant paketidir.','Temiz ve kuru cilde ambalaj yönergelerine göre uygulayın.','36 adet','images/product-acne-patch.svg',1,1,30),
(104,40,3,'EVR-TBS','Everyday Diş Fırçası Soft','everyday-dis-fircasi-soft','Günlük ağız bakımı için soft diş fırçası.','Günlük ağız bakım rutininde kullanılmak üzere sunulur.','Diş hekiminizin önerilerine ve ambalaj talimatlarına uygun kullanın.','1 adet','images/product-toothbrush.svg',1,1,40),
(105,40,3,'EVR-DP03','Everyday Diş Temizleme Pedi 3’lü Set','everyday-dis-temizleme-pedi-3lu','Pratik kullanım için üçlü diş temizleme pedi seti.','Everyday üçlü diş temizleme pedi setidir.','Ambalaj üzerindeki kullanım ve uyarı bilgilerini izleyin.','3 adet','images/product-dental-pad.svg',0,1,50),
(106,50,4,'NXL-CRYSTAL350','NextLab Crystal Ortam Kokusu','nextlab-crystal-ortam-kokusu-350ml','Yeşil narenciye ve çiçeksi notalara sahip 350 ml ortam kokusu.','Uyumlu kokulandırma makinelerinde kullanım için sunulan ortam kokusudur.','Yalnızca uyumlu cihaz ve üretici talimatlarına göre kullanın.','350 ml','images/product-fragrance.svg',1,1,60),
(107,50,4,'NXL-SURRENDER350','NextLab Surrender Ortam Kokulandırma Esansı','nextlab-surrender-ortam-kokusu-350ml','Odunsu, misk ve amber notalarıyla 350 ml ortam kokusu.','Odunsu, misk ve amber notalarıyla tanımlanan ortam kokulandırma esansıdır.','Uyumlu cihazda ve ürün talimatlarına göre kullanın.','350 ml','images/product-fragrance-dark.svg',1,1,70),
(108,50,4,'NXL-COSTUS350','NextLab Costus Homme Ortam Kokusu','nextlab-costus-homme-ortam-kokusu-350ml','Aromatik ve çiçeksi notalara sahip 350 ml ortam kokusu.','Papatya, tarçın ve kekik notalarıyla tanımlanan ortam kokusudur.','Uyumlu cihazda ve ürün talimatlarına göre kullanın.','350 ml','images/product-fragrance.svg',0,1,80),
(109,50,4,'NXL-VIOLET350','NextLab Violet Ortam Kokusu','nextlab-violet-ortam-kokusu-350ml','Limon, greyfurt ve portakal çiçeği notalarıyla 350 ml ortam kokusu.','Çiçeksi ve narenciye karakterli ortam kokusudur.','Uyumlu cihazda ve ürün talimatlarına göre kullanın.','350 ml','images/product-fragrance-violet.svg',0,1,90),
(110,50,4,'NXL-DIFFUSER-W','NextLab Beyaz Koku Makinesi','nextlab-beyaz-koku-makinesi','NextLab ortam kokulandırma ürünleriyle kullanıma yönelik beyaz cihaz.','NextLab beyaz ortam kokulandırma makinesidir.','Yalnızca kullanım kılavuzuna göre çalıştırın.','1 cihaz','images/product-diffuser.svg',1,1,100)
ON DUPLICATE KEY UPDATE name=VALUES(name),description=VALUES(description),usage_info=VALUES(usage_info),package_info=VALUES(package_info),image_path=VALUES(image_path),is_active=1;

INSERT INTO marketplace_links (product_id,marketplace_name,url,sort_order,is_active) VALUES
(101,'Hepsiburada','https://www.hepsiburada.com/everyday-yetiskin-burun-bandi-12-li-pm-HBC00009PK8CI',10,1),
(103,'Hepsiburada','https://www.hepsiburada.com/hidrokolloid-acne-pimple-patch-yatistirici-bant-36-li-yatistirici-bant-36-li-pimple-patch-hidrokolloid-bant-pm-HBC00009OX5C4',10,1),
(104,'Hepsiburada','https://www.hepsiburada.com/everyday-dis-fircasi-soft-pm-HBC0000ACD8P0',10,1),
(105,'Hepsiburada','https://www.hepsiburada.com/everyday-dis-temizleme-pedi-3-lu-set-pm-HBC0000EG4NY9',10,1),
(110,'Hepsiburada','https://www.hepsiburada.com/nextlab-beyaz-koku-makinesi-pm-HBC0000EG4IBZ',10,1)
ON DUPLICATE KEY UPDATE url=VALUES(url),is_active=1;

INSERT INTO site_settings (setting_key,setting_value,setting_type) VALUES
('instagram_url','https://www.instagram.com/nextlabcosmetics/','text'),
('linkedin_url','https://www.linkedin.com/company/nextlabcosmetic/','text'),
('trendyol_store_url','https://www.trendyol.com/magaza/nextlab-saglik-ve-kozmetik-m-1110408?sst=0','text'),
('hepsiburada_store_url','https://www.hepsiburada.com/magaza/nextlab-saglik-ve-kozmetik-urunleri','text')
ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value);

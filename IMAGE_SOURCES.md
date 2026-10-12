# Görsel Kaynak Envanteri

Sitede kullanılan tüm gerçek (doğrulanmış) görsellerin kaynakları. Makine tarafından
okunabilir sürüm: [`data/image-sources.json`](data/image-sources.json). Tüm görseller
proje içine yerel WebP olarak kaydedilmiştir (`assets/images/verified/`), her biri 3–4
genişlik türeviyle `srcset` üzerinden sunulur.

> **Kullanım hakları:** Temizlik görselleri, Nextlab ile ticari iş birliği kapsamında
> yazılı izinle kullanılmaktadır. Everyday ve NextLab görselleri firmanın kendi ürünlerine
> aittir (resmî Trendyol mağazası / proje varlıkları). Pazaryeri ikonları yalnızca mağazaya
> yönlendirme amaçlı küçük rozetlerdir.
>
> **Not (site revizyonu):** Site sahibinin isteğiyle **"Ceymop" marka adı sitenin görünür
> içeriğinden tamamen kaldırıldı** (ürün kartları, metinler, blog, URL ve görsel dosya adları).
> Temizlik ürünleri ve gerçek fotoğrafları korundu; marka etiketi "Profesyonel Temizlik" /
> "Mastermop" oldu. Aşağıdaki kaynak satırları yalnızca iç izlenebilirlik (provenance)
> amaçlıdır; bu dosya sunucuya yüklenmez.

## Doğrulanmış ürün ve banner görselleri

| Anahtar | Kullanım | Kaynak | Ürün sayfası | Türevler (px) |
|---|---|---|---|---|
| everyday-burun-bandi | Ürün 1; hero 1 & 3; kişisel bakım/burun bantları | Trendyol CDN (dsmcdn) | [trendyol.com](https://www.trendyol.com/everyday/yetiskin-burun-bandi-12-li-p-955345961) | 320, 640, 960 |
| everyday-nose-patch | Ürün 2; hero 3 | Trendyol CDN (dsmcdn) | [trendyol.com](https://www.trendyol.com/everyday/nose-patch-siyah-nokta-ve-gozenek-icin-temizleyici-burun-bandi-6-li-p-1096654617) | 320, 640, 960 |
| everyday-akne-bandi | Ürün 3; hero 1 & 3; cilt bakım bantları | Trendyol CDN (dsmcdn) | [trendyol.com](https://www.trendyol.com/everyday/akne-bandi-36-adet-bandi-pimple-patch-hidrokolloid-bant-p-955932916) | 320, 640, 960 |
| mikrofiber-mop | Ürün 11; temizlik alt kategorisi | [ceymop.com.tr/media/cyp-134.webp](https://ceymop.com.tr/media/cyp-134.webp) | [ceymop.com.tr](https://ceymop.com.tr/urun/mikrofiber-mop-cyp-134) | 320, 640, 960 |
| mikrofiber-bez | Ürün 12; temizlik alt kategorisi | [ceymop.com.tr/media/cyp-019.webp](https://ceymop.com.tr/media/cyp-019.webp) | [ceymop.com.tr](https://ceymop.com.tr/urun/mikrofiber-bez-mastermop-cyp-019) | 320, 640, 960 |
| kat-arabasi | Ürün 13; temizlik alt kategorisi; hero 1 | [ceymop.com.tr/media/cyp-820.webp](https://ceymop.com.tr/media/cyp-820.webp) | [ceymop.com.tr](https://ceymop.com.tr/urun/kat-arabasi-cift-cop-toplamali-4-kovali-cyp-820) | 320, 640, 960 |
| temizlik-banner | Hero 2; temizlik kategorisi (masaüstü) | [ceymop.com.tr/media/ceymop-banner-01.webp](https://ceymop.com.tr/media/ceymop-banner-01.webp) | [ceymop.com.tr](https://ceymop.com.tr/) | 480, 960, 1280, 1920 |
| temizlik-mobil | Hero 2 (mobil) | [ceymop.com.tr/media/ceymop-banner-mobile-01.webp](https://ceymop.com.tr/media/ceymop-banner-mobile-01.webp) | [ceymop.com.tr](https://ceymop.com.tr/) | 480, 768, 1024 |

## Kurumsal / arayüz görselleri

| Anahtar | Kullanım | Kaynak | Türevler (px) |
|---|---|---|---|
| nextlab-logo | Header, footer, Open Graph | Proje varlığı: `assets/images/nextlab-logo-hq.png` (onaylı logo) | 190, 380, 760, 1200 |
| trendyol-icon | Header, footer, ürün detayı — Trendyol rozeti | Proje varlığı | 40, 80 |
| hepsiburada-icon | Header, footer, ürün detayı — Hepsiburada rozeti | Proje varlığı | 40, 80 |

Eksik / tedarik edilemeyen görseller için bkz. [`MISSING_ASSETS.md`](MISSING_ASSETS.md).

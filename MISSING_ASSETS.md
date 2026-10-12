# Eksik Görseller

Aşağıdaki 13 üründen **6'sında doğrulanmış gerçek fotoğraf** mevcuttur
(bkz. [`IMAGE_SOURCES.md`](IMAGE_SOURCES.md)). Kalan **7 ürün** için erişilebilir ve
kullanım hakkı bulunan orijinal görsel **henüz tedarik edilememiştir**.

Bu ürünler sitede sahte/temsilî fotoğrafla değil, zarif bir **"Ürün fotoğrafı
hazırlanıyor"** yer tutucusuyla gösterilir (tasarım bozulmaz, kart düzeni korunur).
Brief gereği engel aşma/bot korumasını zorlama girişimi yapılmamıştır.

## Tedarik edilemeyen ürün görselleri (7)

| Ürün | Marka | Hedef kaynak | Durum / neden |
|---|---|---|---|
| Everyday Diş Fırçası Soft | Everyday | Hepsiburada mağaza sayfası | Doğrudan ürün/CDN görseli bulunamadı; mağaza botları engelliyor |
| Everyday Diş Temizleme Pedi 3'lü Set | Everyday | [Hepsiburada ürün sayfası](https://www.hepsiburada.com/everyday-dis-temizleme-pedi-3-lu-set-pm-HBC0000EG4NY9) | Ürün sayfası var; otomatik erişim bot korumasıyla engellendi |
| NextLab Crystal Ortam Kokusu 350ml | NextLab | nextlabcosmetic.com / Hepsiburada | Resmî site erişimi zaman aşımına uğradı (ETIMEDOUT) |
| NextLab Surrender Ortam Kokulandırma Esansı | NextLab | nextlabcosmetic.com / Hepsiburada | Resmî site erişimi zaman aşımına uğradı |
| NextLab Costus Homme Ortam Kokusu 350ml | NextLab | nextlabcosmetic.com / Hepsiburada | Resmî site erişimi zaman aşımına uğradı |
| NextLab Violet Ortam Kokusu 350ml | NextLab | nextlabcosmetic.com / Hepsiburada | Resmî site erişimi zaman aşımına uğradı |
| NextLab Beyaz Koku Makinesi | NextLab | nextlabcosmetic.com / Hepsiburada | Resmî site erişimi zaman aşımına uğradı |

## Nasıl tamamlanır (önerilen iş akışı)

En pratik yol, Everyday görsellerinde işe yarayan yöntemdir — **doğrudan CDN görsel URL'si**
(HTML ürün sayfası değil):

1. Ürünün resmî sayfasını (nextlabcosmetic.com, Trendyol veya Hepsiburada) tarayıcıda açın.
2. Ürün fotoğrafına sağ tık → "Resmi yeni sekmede aç" → **doğrudan görsel URL'sini** kopyalayın
   (ör. Trendyol'da `cdn.dsmcdn.com/.../1_org_zoom.jpg`).
3. Dosyayı indirip `source-assets/` içine `<anahtar>.original` olarak kaydedin.
4. `data/image-sources.json` içine kaydı ekleyin (anahtar, source, page, use, widths).
5. Türevleri üretin:
   ```bash
   node scripts/build-images.cjs        # WebP + srcset türevleri (320/640/960…)
   ```
6. İlgili ürünün `data/catalog.json` kaydında `image_path`, `image_status` ("verified")
   ve `source_url`/`source_verified_at` alanlarını güncelleyin.
7. SQL'i yeniden üretin: `php scripts/export-sql.php > database.sql`.

> **Not:** Resmî site (nextlabcosmetic.com) bu çalışma sırasında sürekli zaman aşımına
> uğradı; birkaç gün içinde yeniden denenmelidir. Instagram/LinkedIn görselleri oturum
> gerektirdiğinden ve yeniden kullanım hakları net olmadığından kaynak olarak kullanılmadı.

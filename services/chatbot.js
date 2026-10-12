const { products, channels } = require('../data/catalog');
const normalize = value => value.toLocaleLowerCase('tr-TR').normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/ı/g, 'i');
function answer(message) {
  const text = normalize(message);
  if (/fiyat|siparis|satin|trendyol|hepsiburada/.test(text)) return 'Güncel fiyat, stok ve teslimat bilgileri için mağazalarımızı ziyaret edebilirsiniz.\n\n[Trendyol mağazamız](' + channels.trendyol + ')\n[Hepsiburada mağazamız](' + channels.hepsiburada + ')';
  if (/sertifika|gmp|iso|dermatolojik|uts|tedavi|gebe|hamile|hassas|alerji/.test(text)) return 'Ürüne özel içerik, uygunluk ve sertifika bilgileri için ambalajı ve üretici belgelerini esas alın. Kişisel sağlık soruları için bir sağlık uzmanına danışın. [İletişim](/iletisim)';
  const terms = text.split(/\s+/).filter(w => w.length > 2 && !['icin', 'nedir', 'urun', 'urunleri', 'var', 'merhaba'].includes(w));
  const ranked = products.map(p => ({ p, score: terms.filter(w => normalize(p.name + ' ' + p.category_name + ' ' + p.brand_name).includes(w)).length })).filter(x => x.score > 0).sort((a,b) => b.score - a.score);
  if (ranked.length) return 'Kataloğumuzdan ilgili ürünler:\n\n' + ranked.slice(0,4).map(({p}) => '• [' + p.name + '](/urun/' + p.slug + ') — ' + p.short_description).join('\n') + '\n\nKullanım ve uyarılar için ambalaj talimatlarını izleyin.';
  return 'Nextlab ürün rehberine hoş geldiniz. Everyday bakım bantları, ağız bakımı, Nextlab ortam kokuları ve Ceymop temizlik ekipmanları hakkında ürün adıyla arama yapabilirsiniz.\n\n[Ürün kataloğu](/urunler) · [Kurumsal iletişim](/iletisim)';
}
async function handleChat(message) { return { text: answer(message), source: 'catalog' }; }
module.exports = { handleChat, answer };

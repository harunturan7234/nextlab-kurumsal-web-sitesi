const { GoogleGenAI } = require('@google/genai');
const { products, distributorBrands, faqs } = require('../data/catalog');
const { blogs } = require('../data/blogs');

// Initialize Google GenAI client
let aiClient = null;
try {
  aiClient = new GoogleGenAI();
} catch (err) {
  console.warn('GoogleGenAI initialization notice:', err.message);
}

// Build knowledge base context string
function buildKnowledgeBase() {
  const allProducts = products || [];
  const allBrands = distributorBrands || [];
  const allBlogs = blogs || [];
  const allFaqs = faqs || [];

  let context = `
=== NEXTLAB KOZMETİK VE SAĞLIK ŞİRKET BİLGİSİ ===
Şirket: Nextlab Sağlık ve Kozmetik Ürünleri
Konum: Altunizade, Üsküdar / İstanbul
E-posta: info@nextlabcosmetic.com
Resmi Pazaryeri Mağazaları:
- Trendyol: https://www.trendyol.com/magaza/nextlab-saglik-ve-kozmetik-m-1110408?sst=0
- Hepsiburada: https://www.hepsiburada.com/magaza/nextlab-saglik-ve-kozmetik-urunleri
Kalite Standartları: T.C. Sağlık Bakanlığı ve ÜTS (Ürün Takip Sistemi) kayıtlıdır. ISO 9001 ve GMP (İyi Üretim Uygulamaları) sertifikalı tesislerde üretilir. Cruelty-Free (Hayvanlar üzerinde test edilmez).

=== RESMİ NEXTLAB & EVERYDAY ÜRÜNLERİ ===
`;

  allProducts.forEach(p => {
    context += `- ${p.name} (Marka: ${p.brand_name}, Kategori: ${p.category_name}, Paket: ${p.package_info || '-'}): ${p.short_description || p.description} Kullanım: ${p.usage || '-'}\n`;
  });

  context += `\n=== DİSTRİBÜTÖRLÜK MARKALARIMIZ (KORE DERMO-KOZMETİK & K-BEAUTY) ===\n`;
  allBrands.forEach(b => {
    context += `- ${b.name} (${b.origin}, ${b.category}): ${b.description} Öne çıkanlar: ${b.highlight}. Başlıca ürünler: ${(b.key_products || []).join(', ')}. Rozetler: ${(b.badges || []).join(', ')}\n`;
  });

  context += `\n=== BLOG VE UZMAN REHBERLERİMİZ ===\n`;
  allBlogs.forEach(b => {
    context += `- ${b.title} (Kategori: ${b.category}, Okuma: ${b.read_time}): Anahtar kelimeler: ${(b.keywords || []).map(k => k.keyword).join(', ')}. Konu: ${b.excerpt}\n`;
  });

  context += `\n=== SIKÇA SORULAN SORULAR VE CEVAPLARI ===\n`;
  allFaqs.forEach(f => {
    context += `S: ${f.question}\nC: ${f.answer}\n\n`;
  });

  return context;
}

const SYSTEM_INSTRUCTION = `Sen "Nextlab Akıllı Asistanı"sın; Nextlab Kozmetik'in web sitesinde yer alan, kozmetik, dermo-kozmetik, Kore cilt bakımı (K-Beauty), temizlik, hijyen, ortam kokulandırma ve kurumsal süreçler konusunda uzman yapay zekâ danışmanısın.

GÖREVLERİN VE UZMANLIK ALANLARIN:
1. Web Sitesi ve Ürünler:
   - Nextlab ortam kokulandırma cihazları (soğuk mikro-difüzyon teknolojisi) ve 350ml lüks esansları (Crystal, Surrender, Costus Homme, Violet).
   - Everyday serisi minimalist cilt bakımı (Hidrokolloid Acne Pimple Patch sivilce bantları, yetişkin burun bantları, yumuşak diş fırçaları, diş temizleme pedleri).
   - The Nextlab Home temizlik serisi (çok amaçlı yüzey temizleyici, çamaşır deterjanı, bulaşık sabunu).
   - Türkiye distribütörlüğünü yürüttüğümüz dünyaca ünlü Kore kozmetik (K-Beauty) markaları: Dr. Melaxin (BP Pore, Bio-Spicule), Anua (Heartleaf %77 Soothing, Niacinamide), Medi-Peel (Peptide 9, Bor-Tox), Cosrx (Snail 96 Mucin, BHA Blackhead Power), Skin1004 (Madagascar Centella Asiatica, Hyalu-Cica SPF50+), Beauty of Joseon (Relief Sun Rice+Probiotics, Ginseng), Round Lab (1025 Dokdo, Birch Juice), Tocobo (Cotton Soft Sun Stick).

2. Kozmetik & Cilt Bakımı Uzmanlığı:
   - Cilt tiplerine göre (yağlı, kuru, karma, hassas, akneye meyilli) doğru rutin oluşturma.
   - Kore cilt bakımının altın kuralı: Çift aşamalı temizlik (önce yağ bazlı temizleyici, ardından su bazlı nazik köpük/jel), tonik, esans, serum, nemlendirici ve gündüzleri mutlaka SPF50+ güneş koruyucu (Cam Cilt / Glass Skin rutini).
   - Aktif içerik rehberliği: C Vitamini, Niasinamid, Hyaluronik Asit, Retinol, Salisilik Asit (BHA), AHA, Centella Asiatica (Cica), Seramid, Peptitler, Salyangoz Müsini.
   - İçerik kombinasyon kuralları: Retinol ve asitler (AHA/BHA) aynı rutinde kullanılmamalı (dönüşümlü günlerde kullanılmalı); C Vitamini sabahları SPF altında güçlü koruma sağlar; Seramid cilt bariyerini onarır.

3. Satış, Sipariş ve Distribütörlük:
   - Ürünler doğrudan resmi Hepsiburada ve Trendyol Nextlab mağazalarında orijinal garantisiyle satılmaktadır.
   - Kurumsal alımlar, otel ve ofis kokulandırma projeleri veya B2B distribütörlük iş ortaklıkları için iletişim sayfası veya info@nextlabcosmetic.com üzerinden 24 saat içinde dönüş sağlanmaktadır.

İLETİŞİM VE CEVAPLAMA TARZI:
- Samimi, saygılı, bilgili, profesyonel ve çözüm odaklı Türkçe konuş.
- Cevaplarını gereksiz laf kalabalığına boğma; yapılandırılmış, maddeli veya vurgulu (kalın metinler, emoji başlıkları) kolay okunur şekilde sun.
- Tıbbi teşhis/tedavi iddiasında bulunma; dermo-kozmetik bakım ve rutin tavsiyeleri olarak sun.
- Soru soran kullanıcının cilt tipini veya ihtiyacını anlamak için gerekirse nazik yönlendirici sorular sor.

BİLGİ TABANI:
${buildKnowledgeBase()}
`;

// Smart local fallback in case of rate limits, offline state or temporary API issues
function generateSmartFallbackResponse(userMessage) {
  const msg = (userMessage || '').toLowerCase();

  if (msg.includes('burun band') || msg.includes('nose patch')) {
    return `**Everyday Burun Bantları** burun bölgesindeki gözenekleri ve siyah noktaları pratik bir şekilde arındırmak için özel olarak tasarlanmıştır:\n\n` +
      `• **Everyday Yetişkin Burun Bandı 12’li:** Günlük temizlik ve pürüzsüzlük sağlar.\n` +
      `• **Everyday Nose Patch 6’lı:** Yoğun burun bölgesi bakımı sunar.\n\n` +
      `**Nasıl Kullanılır?**\n` +
      `1. Burnunuzu su ile iyice ıslatın (bant kuru cilde yapışmaz).\n` +
      `2. Bandı burun kıvrımlarına tam oturacak şekilde yapıştırın.\n` +
      `3. 10-15 dakika kurumasını bekledikten sonra kenarlardan merkeze doğru yavaşça çıkarın.\n\n` +
      `Ürünlerimize resmi Trendyol ve Hepsiburada Nextlab mağazalarımızdan ulaşabilirsiniz.`;
  }

  if (msg.includes('sivilce') || msg.includes('acne') || msg.includes('pimple') || msg.includes('patch') || msg.includes('yama')) {
    return `**Everyday Hidrokolloid Acne Pimple Patch (36’lı Sivilce Bandı)**:\n\n` +
      `• **Görevi:** Sivilcenin içerisindeki iltihap ve fazla sebumu emer, dış etkenlere ve bakterilere karşı koruyucu bir kalkan oluşturur.\n` +
      `• **Kullanım Şekli:**\n` +
      `  1. Uygulama yapılacak bölgeyi yıkayıp **tamamen kurulayın** (üzerine krem veya yağ sürmeyin).\n` +
      `  2. Uygun boyuttaki yamayı sivilcenin üzerine yapıştırın.\n` +
      `  3. Yama sebumu çektikçe opak beyaz renge dönecektir. 6-8 saat sonra nazikçe çıkarabilirsiniz.\n\n` +
      `Makyaj altına da uygulanabilen şeffaf ve konforlu bir yapıya sahiptir.`;
  }

  if (msg.includes('çift aşamalı') || msg.includes('cift asamali') || msg.includes('cam cilt') || msg.includes('glass skin')) {
    return `**Kore Cilt Bakımının Altın Kuralı: Çift Aşamalı Temizlik ve Cam Cilt (Glass Skin)** ✨\n\n` +
      `1. **Adım 1 - Yağ Bazlı Temizleyici:** Gün boyu biriken güneş kremini, fazla sebumu ve makyajı gözenekleri tıkamadan nazikçe çözer (Örn: *Anua Heartleaf Pore Control Cleansing Oil* veya *Skin1004 Centella Light Cleansing Oil*).\n` +
      `2. **Adım 2 - Su Bazlı Temizleyici:** Ciltteki su bazlı kalıntıları ve kiri arındırır (Örn: *Cosrx Low pH Good Morning Gel*).\n` +
      `3. **Adım 3 - Yatıştırıcı Tonik & Esans:** Cildin pH dengesini sağlar, derinlemesine nem aşılar (*Round Lab 1025 Dokdo Toner* veya *Cosrx Snail 96 Mucin*).\n` +
      `4. **Adım 4 - Hedefe Yönelik Serum:** Gözenek, leke veya ışıltı için konsantre bakım.\n` +
      `5. **Adım 5 - Bariyer Nemlendirici & Gündüz SPF50+:** Nemi hapseder ve güneşten korur.\n\n` +
      `Hangi cilt tipine sahipsiniz? Size özel ürün eşleşmesi yapabilirim!`;
  }

  // K-Beauty Brands
  if (msg.includes('cosrx') || msg.includes('salyangoz') || msg.includes('snail') || msg.includes('muc') || msg.includes('mucin')) {
    return `**Cosrx & Salyangoz Müsini (Snail Mucin)** 🐌\n\n` +
      `Nextlab güvencesiyle sunulan **Cosrx**, minimal formüllerle kanıtlanmış cilt sonuçları sunan dünyaca ünlü bir Kore markasıdır:\n\n` +
      `• **Advanced Snail 96 Mucin Power Essence:** %96 saf salyangoz salgısı filtratı içerir. Cildin nem bariyerini güçlendirir, kızarıklıkları yatıştırır ve elastikiyeti artırır.\n` +
      `• **BHA Blackhead Power Liquid:** Gözeneklerin içine nüfuz ederek siyah noktaları ve ölü hücreleri temizler.\n` +
      `• **Low pH Good Morning Gel Cleanser:** Sabahları cildin asit mantosunu koruyarak nazikçe arındırır.\n\n` +
      `Salyangoz esansı özellikle nemsiz, bariyeri zayıflamış veya akne sonrası leke yaşayan ciltler için harika bir onarıcıdır.`;
  }

  if (msg.includes('beauty of joseon') || msg.includes('joseon') || msg.includes('pirinç') || msg.includes('pirinc') || msg.includes('ginseng')) {
    return `**Beauty of Joseon (Hanbang Geleneksel Bakımı)** 🌾\n\n` +
      `Kore hanedanlığı reçetelerini modern biyoteknolojiyle buluşturan **Beauty of Joseon** markasının en sevilen ürünleri:\n\n` +
      `• **Relief Sun: Rice + Probiotics SPF50+ PA++++:** %30 pirinç özü ve fermente tahıl probiyotikleri içerir. Asla beyazlık bırakmaz, parlama yapmaz ve nemlendirici gibi emilir.\n` +
      `• **Glow Serum (Propolis + Niacinamide):** Işıltı ve gözenek sıkılaştırma için mükemmel denge sağlar.\n` +
      `• **Ginseng Essence Water:** Cilt dolaşımını canlandırarak dolgunluk verir.\n\n` +
      `Hassas ciltler dahil tüm cilt tipleri için uygundur.`;
  }

  if (msg.includes('anua') || msg.includes('heartleaf')) {
    return `**Anua (Yatıştırıcı Cilt Bakımı)** 🍃\n\n` +
      `Anua, özellikle hassas, kızarık ve akneye meyilli ciltlerin kurtarıcısıdır:\n\n` +
      `• **Heartleaf 77% Soothing Toner:** Kore'de 1 numara yatıştırıcı toniktir. Cildin pH dengesini sağlar ve tahrişi anında sakinleştirir.\n` +
      `• **Heartleaf Pore Control Cleansing Oil:** Çift aşamalı temizliğin ilk adımı için mükemmeldir; gözeneklerdeki siyah noktaları ve sebumu nazikçe çözer.\n` +
      `• **Peach 70% Niacin Serum:** Mat ve donuk ciltlere canlılık ve şeftali ışıltısı kazandırır.`;
  }

  if (msg.includes('dr. melaxin') || msg.includes('melaxin') || msg.includes('gözenek') || msg.includes('gozenek') || msg.includes('spicule') || msg.includes('bp pore')) {
    return `**Dr. Melaxin (Biyomedikal Dermo-Kozmetik)** 🔬\n\n` +
      `Genişlemiş gözenekler ve yaşlanma karşıtı bakımda klinik başarı kanıtlamış seriler:\n\n` +
      `• **BP Pore Ampoule:** Gözeneklerin elastikiyetini geri kazandırarak küçülmesine yardımcı olur.\n` +
      `• **Bio-Spicule Yenileyici Seri:** Deniz süngerlerinden elde edilen mikro-iğnecik teknolojisiyle aktif bileşenlerin cildin derin katmanlarına ulaşmasını sağlar.\n` +
      `• **TXA Leke Serumu:** Güneş ve yaşlılık lekelerinde etkili aydınlatma sağlar.`;
  }

  if (msg.includes('skin1004') || msg.includes('centella') || msg.includes('cica') || msg.includes('madecassoside')) {
    return `**Skin1004 (Madagaskar Centella Asiatica)** 🌿\n\n` +
      `Saf Madagaskar Centella özleriyle formüle edilen cilt bariyeri koruyucusu:\n\n` +
      `• **Madagascar Centella Ampoule:** %100 saf Centella özü ile tahriş olmuş, nemsiz cildi sakinleştirir.\n` +
      `• **Hyalu-Cica Water-Fit Sun Serum SPF50+:** Su bazıyla yağlandırmayan, gün boyu nem veren güneş koruyucudur.\n` +
      `• **Centella Light Cleansing Oil:** Ağır his bırakmayan ultra hafif çift aşamalı temizleme yağı.`;
  }

  if (msg.includes('medi-peel') || msg.includes('medipeel') || msg.includes('peptit') || msg.includes('peptide') || msg.includes('kolajen') || msg.includes('bortox') || msg.includes('bor-tox')) {
    return `**Medi-Peel (Medikal Peptit & Kolajen Bakımı)** 💉\n\n` +
      `Estetik klinik standartlarında yaşlanma karşıtı bakım:\n\n` +
      `• **Bor-Tox Peptide Ampoule:** 5 farklı peptit kompleksi ve Fransız kolajeni ile ince çizgi ve kırışıklıklara dolgunluk verir.\n` +
      `• **Peptide 9 Volume Bio Tox Ampoule:** Cilt sarkmalarına karşı toparlayıcı lifting etkisi gösterir.\n` +
      `• **Red Lacto Collagen Clear:** Gözenekleri derinlemesine temizlerken kolajen desteği sağlar.`;
  }

  if (msg.includes('round lab') || msg.includes('dokdo') || msg.includes('huş ağacı') || msg.includes('birch')) {
    return `**Round Lab (Derin Deniz Mineralleri & Huş Ağacı)** 🌊\n\n` +
      `• **1025 Dokdo Toner & Cleanser:** Ulleungdo derin deniz sularındaki 74 zengin mineral ile cildi tahriş etmeden ölü derilerden arındırır ve neme doyurur.\n` +
      `• **Birch Juice Moisturizing Sun Cream SPF50+:** Huş ağacı suyu ile cildin nem bariyerini destekleyen ödüllü güneş kremidir.`;
  }

  if (msg.includes('tocobo') || msg.includes('sun stick')) {
    return `**Tocobo (%100 Vegan Güneş & Dudak Bakımı)** 💙\n\n` +
      `• **Cotton Soft Sun Stick SPF50+ PA++++:** Gözenek ve parlamayı kontrol eden matlaştırıcı stick form. Gün içinde makyaj üzerinden tazelemek için rakipsizdir.\n` +
      `• **Bio Watery Sun Cream:** Ciltte ağırlık yapmayan ferahlatıcı kimyasal filtre koruma.`;
  }

  // Ortam Kokulandırma (specifically for fragrance / diffusers)
  if (msg.includes('koku') || msg.includes('difüzör') || msg.includes('difuzor') || msg.includes('makine') || msg.includes('kokulandırma') || msg.includes('ortam')) {
    return `**Nextlab Profesyonel Ortam Kokulandırma Çözümleri** 🌸\n\n` +
      `Nextlab difüzörleri, esansı ısıtmadan 1 mikrondan küçük partiküllere ayıran **soğuk mikro-difüzyon teknolojisiyle** çalışır. Bu sayede kokunun yapısı bozulmaz ve 30-60 gün boyunca eşit dağılır.\n\n` +
      `**Öne Çıkan 350 ml Esanslarımız:**\n` +
      `• **Crystal:** Yeşil narenciye ve taze çiçeksi notalar.\n` +
      `• **Surrender:** Odunsu, misk ve amber derinliği.\n` +
      `• **Costus Homme:** Papatya, tarçın ve kekik aromatik harmanı.\n` +
      `• **Violet:** Limon, greyfurt ve portakal çiçeği ferahlığı.\n\n` +
      `Kurumsal ve ofis kokulandırma projeleri için info@nextlabcosmetic.com adresinden teklif alabilirsiniz.`;
  }

  // Temizlik & Hijyen
  if (msg.includes('temizlik') || msg.includes('hijyen') || msg.includes('deterjan') || msg.includes('yüzey') || msg.includes('yuzey') || msg.includes('bulaşık')) {
    return `**The Nextlab Home Temizlik & Hijyen Serisi** 🌿\n\n` +
      `Ev ve ofisinizde tavizsiz hijyen ve doğal ferahlık sağlayan temizlik ürünlerimiz:\n\n` +
      `• **Çok Amaçlı Yüzey Temizleyici:** Ahşap, mermer, seramik, fayans ve cam yüzeylerde iz bırakmadan derinlemesine arınma sağlar.\n` +
      `• **Çamaşır Deterjanı:** Güçlü leke çözücü ve kumaş dostu formül.\n` +
      `• **Bulaşık Sabunu:** Cildinizi kurutmayan bitkisel ve taze narenciye kokulu hijyen.\n\n` +
      `Tüm ürünlerimiz çevreye ve cilt bariyerine saygılı standartlarda üretilmektedir.`;
  }

  // Cilt Tipleri & Rutin Önerileri
  if (msg.includes('kuru cilt') || msg.includes('yağlı cilt') || msg.includes('yagli cilt') || msg.includes('karma cilt') || msg.includes('hassas cilt') || msg.includes('rutin')) {
    return `**Cilt Tipinize Göre Uzman Rutin Rehberi** 🧴\n\n` +
      `• **Kuru Ciltler:** Yağ bazlı temizleyici + Nemlendirici nazik köpük + Round Lab 1025 Dokdo Toner + Hyaluronik Asit & Seramid kremi + Beauty of Joseon nemlendirici SPF50+.\n` +
      `• **Yağlı / Akneli Ciltler:** Anua Heartleaf Gözenek Yağı + Cosrx Salicylic Acid Temizleyici + BHA Gözenek Likidi + Hafif su bazlı jel nemlendirici + Tocobo Mat Sun Stick + Everyday Acne Patch.\n` +
      `• **Karma Ciltler:** Çift aşamalı temizlik + Niasinamid serumu (T bölgesi sebum dengesi) + Centella yatıştırıcı krem + Su bazlı güneş koruyucu.\n` +
      `• **Hassas Ciltler:** Parfümsüz, EWG yeşil içerikli Skin1004 Centella serisi ve Anua Heartleaf %77 tonik.\n\n` +
      `Hangi cilt tipiniz veya endişeniz var, birlikte özel bir rutin oluşturalım mı?`;
  }

  if (msg.includes('diş') || msg.includes('dis') || msg.includes('ağız') || msg.includes('agiz')) {
    return `**Everyday Ağız Bakımı Ürünleri** 🪥\n\n` +
      `• **Everyday Diş Fırçası Soft:** Diş etlerini tahriş etmeden plakları nazikçe temizleyen ultra ince ve yumuşak kıl yapısına sahiptir.\n` +
      `• **Everyday Diş Temizleme Pedi 3’lü Set:** Gün içinde pratik hijyen ve ferahlık sunan diş temizleme pedleridir.\n\n` +
      `Ürünlerimize resmi Trendyol ve Hepsiburada mağazalarımızdan ulaşabilirsiniz.`;
  }

  if (msg.includes('nerede') || msg.includes('satın al') || msg.includes('satin al') || msg.includes('fiyat') || msg.includes('sipariş') || msg.includes('siparis') || msg.includes('trendyol') || msg.includes('hepsiburada')) {
    return `Orijinal **Nextlab** ve **Everyday** ürünlerimize resmi onaylı pazaryeri mağazalarımızdan doğrudan ulaşabilirsiniz:\n\n` +
      `🛍️ **Hepsiburada:** [Nextlab Sağlık ve Kozmetik Mağazası](https://www.hepsiburada.com/magaza/nextlab-saglik-ve-kozmetik-urunleri)\n` +
      `🛍️ **Trendyol:** [Nextlab Sağlık ve Kozmetik Mağazası](https://www.trendyol.com/magaza/nextlab-saglik-ve-kozmetik-m-1110408?sst=0)\n\n` +
      `Tüm siparişleriniz doğrudan üretici güvencesiyle ve orijinal ambalajında gönderilmektedir.`;
  }

  if (msg.includes('distribütör') || msg.includes('distributor') || msg.includes('bayi') || msg.includes('toptan') || msg.includes('iş ortak') || msg.includes('is ortak')) {
    return `**Distribütörlük ve B2B İş Ortaklığı** 🤝\n\n` +
      `Nextlab Cosmetic, hem kendi markaları (Everyday, Nextlab) hem de bünyesindeki dünya çapında ünlü Kore kozmetik markaları (Dr. Melaxin, Anua, Medi-Peel, Cosrx, Skin1004, Beauty of Joseon, Round Lab, Tocobo) için toptan satış ve bayilik iş ortaklıkları sunmaktadır.\n\n` +
      `• E-posta: **info@nextlabcosmetic.com**\n` +
      `• İletişim sayfamızdaki kurumsal formu doldurarak da başvurabilirsiniz.\n` +
      `Kurumsal temsilcimiz en geç 24 saat içinde sizinle irtibata geçer.`;
  }

  if (msg.includes('güneş') || msg.includes('gunes') || msg.includes('spf') || msg.includes('sun')) {
    return `**Güneş Koruma ve SPF Seçimi** ☀️\n\n` +
      `Cilt yaşlanmasının ve lekelerin %80'i UV ışınlarından kaynaklanır. Nextlab portföyünde yer alan en popüler güneş koruyucular:\n\n` +
      `• **Beauty of Joseon Relief Sun (Rice + Probiotics SPF50+):** Beyazlık bırakmayan pirinç özlü kimyasal filtre, nemli ve parlak bitiş.\n` +
      `• **Skin1004 Hyalu-Cica Water-Fit Sun Serum SPF50+:** Su gibi hafif, Centella yatıştırıcı koruma.\n` +
      `• **Round Lab Birch Juice Sun Cream:** Huş ağacı suyuyla neme doymuş cilt hissi.\n` +
      `• **Tocobo Cotton Soft Sun Stick SPF50+:** Mat bitişli pratik stick; gün içinde makyaj üstünden yenilemek için idealdir.\n\n` +
      `Hangi cilt tipine sahipsiniz, size en uygun güneş kremini önerelim?`;
  }

  if (msg.includes('leke') || msg.includes('c vitamini') || msg.includes('niasinamid') || msg.includes('niacinamide')) {
    return `**Cilt Lekeleri ve Aydınlatıcı Bakım** ✨\n\n` +
      `Cilt tonu eşitsizliği ve koyu lekeler için kanıtlanmış içerikler:\n\n` +
      `• **C Vitamini (Sabah Rutini):** Güçlü antioksidandır, kolajen üretimini tetikler ve cildi aydınlatır. Güneş kremiyle birlikte harika bir sinerji oluşturur.\n` +
      `• **Niasinamid (B3 Vitamini):** Melanin transferini baskılar, gözenekleri sıkılaştırır ve bariyeri onarır.\n` +
      `• **TXA (Traneksamik Asit):** İnatçı güneş ve akne lekeleri üzerinde klinik etki gösterir (Örn: *Dr. Melaxin TXA Serumu*).\n\n` +
      `Bu ürünleri düzenli kullanırken gündüzleri mutlaka SPF50+ güneş koruyucu kullanılması şarttır.`;
  }

  // General helpful fallback
  return `Nextlab Akıllı Asistanı olarak size yardımcı olmaktan memnuniyet duyarım!\n\n` +
    `Aşağıdaki konulardan dilediğinizi sorabilirsiniz:\n` +
    `• **Kişisel Bakım & Cilt:** Everyday sivilce bantları, burun bantları ve ağız bakım ürünleri\n` +
    `• **Kore Dermo-Kozmetik:** Anua, Cosrx, Skin1004, Beauty of Joseon, Dr. Melaxin, Medi-Peel vb. ürün ve rutin önerileri\n` +
    `• **Nextlab Ortam Kokulandırma:** Difüzör cihazları ve 350ml esans serisi\n` +
    `• **Temizlik & Hijyen:** The Nextlab Home yüzey ve deterjan ürünleri\n` +
    `• **Satış & Distribütörlük:** Trendyol/Hepsiburada mağazalarımız veya toptan iş ortaklığı süreçleri\n\n` +
    `Sorunuzu detaylandırabilir veya yukarıdaki öneri butonlarına tıklayabilirsiniz!`;
}

// Main chat handler
async function handleChat(message, history = []) {
  if (!message || typeof message !== 'string' || !message.trim()) {
    return {
      text: 'Lütfen sormak istediğiniz soruyu yazın.',
      source: 'validation'
    };
  }

  const trimmedMessage = message.trim();

  // Try Gemini models with smart fallback hierarchy
  if (aiClient && process.env.GEMINI_API_KEY) {
    const modelsToTry = ['gemini-3.8-flash', 'gemini-flash-latest', 'gemini-3.1-flash-lite'];

    for (const modelName of modelsToTry) {
      try {
        // Construct conversation contents with history
        const contents = [];

        // Format history (max last 6 turns for speed & context)
        if (Array.isArray(history) && history.length > 0) {
          const recentHistory = history.slice(-6);
          for (const item of recentHistory) {
            if (item && item.text && (item.role === 'user' || item.role === 'model')) {
              contents.push({
                role: item.role,
                parts: [{ text: String(item.text) }]
              });
            }
          }
        }

        // Add current user prompt
        contents.push({
          role: 'user',
          parts: [{ text: trimmedMessage }]
        });

        const response = await aiClient.models.generateContent({
          model: modelName,
          contents: contents,
          config: {
            systemInstruction: SYSTEM_INSTRUCTION,
            temperature: 0.7,
            maxOutputTokens: 1000
          }
        });

        if (response && response.text) {
          return {
            text: response.text,
            source: 'gemini',
            model: modelName
          };
        }
      } catch (err) {
        console.warn(`Model ${modelName} error:`, err.message || err);
        // Continue to next model or fallback
      }
    }
  }

  // If Gemini API is unreachable, rate limited or lacks key, return intelligent domain-specific answer
  const fallbackText = generateSmartFallbackResponse(trimmedMessage);
  return {
    text: fallbackText,
    source: 'knowledge-base',
    model: 'nextlab-expert-engine'
  };
}

module.exports = {
  handleChat,
  generateSmartFallbackResponse
};

const today = '2026-10-11';
const hepsiburadaStore = 'https://www.hepsiburada.com/magaza/nextlab-saglik-ve-kozmetik-urunleri';
const trendyolStore = 'https://www.trendyol.com/magaza/nextlab-saglik-ve-kozmetik-m-1110408?sst=0';

const categories = [
  { id: 1, parent_id: null, name: 'Temizlik ve Hijyen', slug: 'temizlik-ve-hijyen', description: 'Ağız bakımı ve ortam hijyenine yönelik doğrulanmış Nextlab ürünleri.', image_path: 'images/category-cleaning.svg', updated_at: today },
  { id: 2, parent_id: null, name: 'Kozmetik ve Kişisel Bakım', slug: 'kozmetik-ve-kisisel-bakim', description: 'Everyday markalı günlük cilt ve kişisel bakım ürünleri.', image_path: 'images/category-care.svg', updated_at: today },
  { id: 3, parent_id: 2, name: 'Cilt Bakım Bantları', slug: 'cilt-bakim-bantlari', description: 'Hidrokolloid ve burun bölgesine yönelik bakım bantları.', image_path: 'images/category-care.svg', updated_at: today },
  { id: 4, parent_id: 1, name: 'Ağız Bakımı', slug: 'agiz-bakimi', description: 'Günlük ağız bakım ürünleri.', image_path: 'images/product-oral.svg', updated_at: today },
  { id: 5, parent_id: 1, name: 'Ortam Kokulandırma', slug: 'ortam-kokulandirma', description: 'Ortam kokuları ve kokulandırma çözümleri.', image_path: 'images/product-fragrance.svg', updated_at: today },
];

const products = [
  {
    id: 1,
    category_id: 3,
    category_name: 'Cilt Bakım Bantları',
    category_slug: 'cilt-bakim-bantlari',
    brand_name: 'Everyday',
    sku: 'EVR-NB12',
    name: 'Everyday Yetişkin Burun Bandı 12’li',
    slug: 'everyday-yetiskin-burun-bandi-12li',
    short_description: 'Yetişkin kullanımı için 12 adet burun bandı.',
    description: 'Everyday markasının yetişkin kullanımına yönelik 12’li burun bandı paketidir. Ürün hakkında doğrulanmamış tedavi veya performans iddiası sunulmamaktadır.',
    usage: 'Ambalaj üzerindeki kullanım talimatlarını izleyin. Tek kullanımlık ürünü yeniden kullanmayın.',
    package_info: '12 adet',
    image_path: 'images/product-nose-strip.svg',
    is_featured: 1,
    marketplace_url: 'https://www.hepsiburada.com/everyday-yetiskin-burun-bandi-12-li-pm-HBC00009PK8CI',
    marketplace_name: 'Hepsiburada',
    updated_at: today
  },
  {
    id: 2,
    category_id: 3,
    category_name: 'Cilt Bakım Bantları',
    category_slug: 'cilt-bakim-bantlari',
    brand_name: 'Everyday',
    sku: 'EVR-NP06',
    name: 'Everyday Nose Patch Temizleyici Burun Bandı 6’lı',
    slug: 'everyday-nose-patch-6li',
    short_description: 'Burun bölgesi için 6 adet temizleyici bakım bandı.',
    description: 'Everyday Nose Patch, burun bölgesindeki günlük bakım rutinini tamamlamak üzere sunulan 6’lı bant paketidir.',
    usage: 'Temiz cilde, ürün ambalajında belirtilen yönergelere göre uygulayın.',
    package_info: '6 adet',
    image_path: 'images/product-nose-patch.svg',
    is_featured: 1,
    marketplace_url: hepsiburadaStore,
    marketplace_name: 'Hepsiburada mağazası',
    updated_at: today
  },
  {
    id: 3,
    category_id: 3,
    category_name: 'Cilt Bakım Bantları',
    category_slug: 'cilt-bakim-bantlari',
    brand_name: 'Everyday',
    sku: 'EVR-APP36',
    name: 'Everyday Hidrokolloid Acne Pimple Patch 36’lı',
    slug: 'everyday-hidrokolloid-acne-pimple-patch-36li',
    short_description: 'Günlük cilt bakım rutini için 36 adet hidrokolloid bant.',
    description: 'Everyday markalı 36’lı hidrokolloid bant paketi, bölgesel cilt bakımında pratik kullanım için hazırlanmıştır. Tıbbi tedavi ürünü olarak sunulmaz.',
    usage: 'Temiz ve kuru cilde uygulayın; ambalaj üzerindeki kullanım süresine uyun.',
    package_info: '36 adet',
    image_path: 'images/product-acne-patch.svg',
    is_featured: 1,
    marketplace_url: 'https://www.hepsiburada.com/hidrokolloid-acne-pimple-patch-yatistirici-bant-36-li-yatistirici-bant-36-li-pimple-patch-hidrokolloid-bant-pm-HBC00009OX5C4',
    marketplace_name: 'Hepsiburada',
    updated_at: today
  },
  {
    id: 4,
    category_id: 4,
    category_name: 'Ağız Bakımı',
    category_slug: 'agiz-bakimi',
    brand_name: 'Everyday',
    sku: 'EVR-TBS',
    name: 'Everyday Diş Fırçası Soft',
    slug: 'everyday-dis-fircasi-soft',
    short_description: 'Günlük ağız bakımı için soft diş fırçası.',
    description: 'Everyday Soft diş fırçası, günlük ağız bakım rutininde kullanılmak üzere sunulur.',
    usage: 'Diş hekiminizin önerilerine ve ambalaj talimatlarına uygun kullanın.',
    package_info: '1 adet',
    image_path: 'images/product-toothbrush.svg',
    is_featured: 1,
    marketplace_url: 'https://www.hepsiburada.com/everyday-dis-fircasi-soft-pm-HBC0000ACD8P0',
    marketplace_name: 'Hepsiburada',
    updated_at: today
  },
  {
    id: 5,
    category_id: 4,
    category_name: 'Ağız Bakımı',
    category_slug: 'agiz-bakimi',
    brand_name: 'Everyday',
    sku: 'EVR-DP03',
    name: 'Everyday Diş Temizleme Pedi 3’lü Set',
    slug: 'everyday-dis-temizleme-pedi-3lu',
    short_description: 'Pratik kullanım için üçlü diş temizleme pedi seti.',
    description: 'Everyday üçlü diş temizleme pedi setidir. Ürünün ayrıntılı uygulama bilgileri için ambalaj talimatı esas alınmalıdır.',
    usage: 'Ambalaj üzerindeki kullanım ve uyarı bilgilerini izleyin.',
    package_info: '3 adet',
    image_path: 'images/product-dental-pad.svg',
    is_featured: 0,
    marketplace_url: 'https://www.hepsiburada.com/everyday-dis-temizleme-pedi-3-lu-set-pm-HBC0000EG4NY9',
    marketplace_name: 'Hepsiburada',
    updated_at: today
  },
  {
    id: 6,
    category_id: 5,
    category_name: 'Ortam Kokulandırma',
    category_slug: 'ortam-kokulandirma',
    brand_name: 'NextLab',
    sku: 'NXL-CRYSTAL350',
    name: 'NextLab Crystal Ortam Kokusu',
    slug: 'nextlab-crystal-ortam-kokusu-350ml',
    short_description: 'Yeşil narenciye ve çiçeksi notalara sahip 350 ml ortam kokusu.',
    description: 'Crystal; yeşil narenciye ve çiçeksi notalarla tanımlanan, uyumlu kokulandırma makinelerinde kullanım için sunulan ortam kokusudur.',
    usage: 'Yalnızca uyumlu cihaz ve üretici talimatlarına göre kullanın.',
    package_info: '350 ml',
    image_path: 'images/product-fragrance.svg',
    is_featured: 1,
    marketplace_url: hepsiburadaStore,
    marketplace_name: 'Hepsiburada mağazası',
    updated_at: today
  },
  {
    id: 7,
    category_id: 5,
    category_name: 'Ortam Kokulandırma',
    category_slug: 'ortam-kokulandirma',
    brand_name: 'NextLab',
    sku: 'NXL-SURRENDER350',
    name: 'NextLab Surrender Ortam Kokulandırma Esansı',
    slug: 'nextlab-surrender-ortam-kokusu-350ml',
    short_description: 'Odunsu, misk ve amber notalarıyla 350 ml ortam kokusu.',
    description: 'Surrender; odunsu, misk ve amber notalarıyla tanımlanan 350 ml ortam kokulandırma esansıdır.',
    usage: 'Uyumlu cihazda ve ürün talimatlarına göre kullanın.',
    package_info: '350 ml',
    image_path: 'images/product-fragrance-dark.svg',
    is_featured: 1,
    marketplace_url: hepsiburadaStore,
    marketplace_name: 'Hepsiburada mağazası',
    updated_at: today
  },
  {
    id: 8,
    category_id: 5,
    category_name: 'Ortam Kokulandırma',
    category_slug: 'ortam-kokulandirma',
    brand_name: 'NextLab',
    sku: 'NXL-COSTUS350',
    name: 'NextLab Costus Homme Ortam Kokusu',
    slug: 'nextlab-costus-homme-ortam-kokusu-350ml',
    short_description: 'Aromatik ve çiçeksi notalara sahip 350 ml ortam kokusu.',
    description: 'Costus Homme; papatya, tarçın ve kekik notalarıyla tanımlanan aromatik ve çiçeksi ortam kokusudur.',
    usage: 'Uyumlu cihazda ve ürün talimatlarına göre kullanın.',
    package_info: '350 ml',
    image_path: 'images/product-fragrance.svg',
    is_featured: 0,
    marketplace_url: hepsiburadaStore,
    marketplace_name: 'Hepsiburada mağazası',
    updated_at: today
  },
  {
    id: 9,
    category_id: 5,
    category_name: 'Ortam Kokulandırma',
    category_slug: 'ortam-kokulandirma',
    brand_name: 'NextLab',
    sku: 'NXL-VIOLET350',
    name: 'NextLab Violet Ortam Kokusu',
    slug: 'nextlab-violet-ortam-kokusu-350ml',
    short_description: 'Limon, greyfurt ve portakal çiçeği notalarıyla 350 ml ortam kokusu.',
    description: 'Violet; limon, greyfurt ve portakal çiçeği notalarıyla tanımlanan çiçeksi ve narenciye karakterli ortam kokusudur.',
    usage: 'Uyumlu cihazda ve ürün talimatlarına göre kullanın.',
    package_info: '350 ml',
    image_path: 'images/product-fragrance-violet.svg',
    is_featured: 0,
    marketplace_url: hepsiburadaStore,
    marketplace_name: 'Hepsiburada mağazası',
    updated_at: today
  },
  {
    id: 10,
    category_id: 5,
    category_name: 'Ortam Kokulandırma',
    category_slug: 'ortam-kokulandirma',
    brand_name: 'NextLab',
    sku: 'NXL-DIFFUSER-W',
    name: 'NextLab Beyaz Koku Makinesi',
    slug: 'nextlab-beyaz-koku-makinesi',
    short_description: 'NextLab ortam kokulandırma ürünleriyle kullanıma yönelik beyaz cihaz.',
    description: 'NextLab beyaz ortam kokulandırma makinesidir. Cihazın kurulum ve kullanım ayrıntıları için güncel ürün kılavuzu esas alınmalıdır.',
    usage: 'Cihazı yalnızca kullanım kılavuzuna ve uyumlu sarf malzemelerine göre çalıştırın.',
    package_info: '1 cihaz',
    image_path: 'images/product-diffuser.svg',
    is_featured: 1,
    marketplace_url: 'https://www.hepsiburada.com/nextlab-beyaz-koku-makinesi-pm-HBC0000EG4IBZ',
    marketplace_name: 'Hepsiburada',
    updated_at: today
  }
];

const banners = [
  {
    id: 1,
    theme: 'theme-nextlab-green',
    badge: 'NEXTLAB YENİ NESİL CİLT BAKIMI',
    title: 'Az, Öz, Etkili Cilt Bakımı',
    subtitle: 'NextLab laboratuvarlarında yüksek saflık ve hedefe yönelik etkiyle geliştirilen formüller. Cildinize hak ettiği bilimsel ve zarif bakım.',
    bullets: ['NextLab Yeşil Seri', 'Yüksek Konsantrasyonlu Formül', 'Dermatolojik Onaylı'],
    button_text: 'Cilt Bakım Serisi',
    button_url: 'kategori/cilt-bakim-bantlari',
    secondary_text: 'Trendyol Mağazası ↗',
    secondary_url: trendyolStore,
    image_path: 'images/banner-nextlab-skincare.svg'
  },
  {
    id: 2,
    theme: 'theme-nextlab-home',
    badge: 'THE NEXTLAB HOME',
    title: 'Tavizsiz Hijyen, Doğal Ferahlık',
    subtitle: 'Çok amaçlı yüzey temizleyici, çamaşır deterjanı, bulaşık sabunu ve dezenfektan serisi ile evinizde derinlemesine hijyen ve taze narenciye kokusu.',
    bullets: ['Çok Amaçlı Yüzey Temizleyici', 'Güçlü Çamaşır Deterjanı', 'Bitkisel & Ferah Formül'],
    button_text: 'Temizlik Serisi',
    button_url: 'kategori/temizlik-ve-hijyen',
    secondary_text: 'Hepsiburada Mağazası ↗',
    secondary_url: hepsiburadaStore,
    image_path: 'images/banner-nextlab-home.svg'
  },
  {
    id: 3,
    theme: 'theme-botanical-care',
    badge: 'BOTANİK & KLİNİK BAKIM',
    title: 'B12, C Vitamini & Cica Cream',
    subtitle: 'Anti-aging B12 serum, ışıltı veren C vitamini, yoğun nemlendirici Cica krem ve SPF 50 sun drops ile doğal ve klinik bakım dengesi.',
    bullets: ['B12 Anti-Aging Serum', 'C Vitamin Aydınlatıcı', 'Cica Cream & SPF 50 Koruma'],
    button_text: 'Bakım Serisi',
    button_url: 'kategori/kozmetik-ve-kisisel-bakim',
    secondary_text: 'Trendyol Mağazası ↗',
    secondary_url: trendyolStore,
    image_path: 'images/banner-botanical-care.svg'
  },
  {
    id: 4,
    theme: 'theme-sunset-glow',
    badge: 'PREMIUM GLOW SERİSİ',
    title: 'Altın Işıltı & Derinlemesine Nem',
    subtitle: 'Glow radiance serum, dolgunlaştırıcı Hydra serum ve SPF 50 sun fluid ile cildinizi güneşten koruyan ve ışıldatan lüks bakım.',
    bullets: ['Glow Radiance Serum', 'Hydra Barrier Serum', 'SPF 50 Sun Fluid Koruma'],
    button_text: 'Tüm Koleksiyon',
    button_url: 'urunler',
    secondary_text: 'Hepsiburada Mağazası ↗',
    secondary_url: hepsiburadaStore,
    image_path: 'images/banner-sunset-glow.svg'
  }
];

const faqs = [
  {
    id: 1,
    category: 'Kalite & Standartlar',
    question: 'Nextlab ürünleri hangi kalite ve güvenlik standartlarına göre üretilir?',
    answer: 'Nextlab Cosmetic bünyesindeki tüm formülasyonlar T.C. Sağlık Bakanlığı ve ÜTS (Ürün Takip Sistemi) kayıtlıdır. Üretim süreçlerimiz ISO 9001 ve GMP (İyi Üretim Uygulamaları) sertifikalı modern tesislerde, mikrobiyolojik ve dermatolojik testlerden geçirilerek titizlikle yürütülmektedir.'
  },
  {
    id: 2,
    category: 'Cilt & Kişisel Bakım',
    question: 'Everyday serisi günlük kullanıma ve hassas cilt tiplerine uygun mudur?',
    answer: 'Evet. Everyday ürün ailesi, "Less is More" felsefesiyle formüle edilmiştir. Cildin doğal bariyerini destekleyen sade ve temiz etken maddeler içerir. Sivilce yamalarımız (Acne Patch), burun bantlarımız ve temizleme ürünlerimiz hassas ciltler dahil tüm cilt tipleri için günlük kullanıma uygundur.'
  },
  {
    id: 3,
    category: 'Ortam Kokulandırma',
    question: 'Nextlab ortam kokulandırma cihazları nasıl çalışır ve esans ömrü ne kadardır?',
    answer: 'Nextlab profesyonel difüzörleri, esansı ısıtmadan 1 mikrondan küçük zerreciklere ayrıştıran soğuk mikro-difüzyon teknolojisini kullanır. Akıllı zamanlayıcı ve ayarlanabilir koku yoğunluğu sayesinde kartuşlar (100 ml – 500 ml), kullanım yoğunluğuna bağlı olarak 30 ila 60 gün boyunca eşit ve kalıcı koku performansı sunar.'
  },
  {
    id: 4,
    category: 'Etik Değerler',
    question: 'Ürünleriniz hayvanlar üzerinde test ediliyor mu (Cruelty-Free)?',
    answer: 'Kesinlikle hayır. Nextlab Cosmetic ve Everyday markaları olarak hayvan refahına ve doğaya saygılıyız. Ar-Ge, formülasyon ve nihai ürün geliştirme süreçlerimizin hiçbir aşamasında hayvanlar üzerinde test yapılmamaktadır.'
  },
  {
    id: 5,
    category: 'Sipariş & Orijinallik',
    question: 'Orijinal Nextlab ve Everyday ürünlerine nereden ulaşabilirim?',
    answer: 'Tüm orijinal ürünlerimize doğrudan Trendyol ve Hepsiburada üzerindeki onaylı resmi "Nextlab Sağlık ve Kozmetik" mağazalarımızdan ulaşabilirsiniz. Siparişleriniz doğrudan üretici güvencesiyle, orijinal güvenlik ambalajında kapınıza ulaştırılır.'
  },
  {
    id: 6,
    category: 'Kurumsal & Toptan',
    question: 'Kurumsal kokulandırma, otel ve toptan alım projeleri için nasıl iletişime geçebilirim?',
    answer: 'Ofisler, oteller, mağazalar, spor salonları ve klinik projelerine yönelik özel kokulandırma danışmanlığı ile toptan tedarik imkanı sunuyoruz. İletişim sayfamızdaki formu doldurarak ya da info@nextlabcosmetic.com adresinden bizimle irtibata geçebilirsiniz. Kurumsal ekibimiz en geç 24 saat içinde dönüş sağlar.'
  },
  {
    id: 7,
    category: 'Kullanım Rehberi',
    question: 'Everyday Acne Patch (Sivilce Yaması) nasıl uygulanmalıdır?',
    answer: 'Uygulama yapılacak bölgeyi temizleyin ve tamamen kurulayın (öncesinde tonik, krem veya yağ uygulamayınız). Uygun çaptaki yamayı sivilcenin üzerine yerleştirip hafifçe bastırın. Hidrokolloid yapı sıvıyı ve sebumu emdikçe opak beyaz renge dönecektir. 6-8 saat sonra ya da beyazlaştığında nazikçe çıkarabilirsiniz.'
  },
  {
    id: 8,
    category: 'Temizlik & Hijyen',
    question: 'Temizlik ürünlerinde yüzey güvenliği ve kullanım alanları nelerdir?',
    answer: 'Nextlab temizlik ve hijyen serisi, yüzeylere zarar vermeden derinlemesine hijyen sağlamak üzere geliştirilmiştir. Özel formülleriyle mermer, fayans, paslanmaz çelik, cam ve ahşap gibi farklı yüzey gruplarında iz bırakmadan güvenle uygulanabilir.'
  }
];

const distributorBrands = [
  {
    id: 1,
    name: 'Dr. Melaxin',
    slug: 'dr-melaxin',
    origin: 'Güney Kore',
    category: 'Biyomedikal Dermo-Kozmetik',
    highlight: 'BP Pore Gözenek & Bio-Spicule Yenileyici Seri',
    description: 'Dr. Melaxin, klinik biyomedikal dermatoloji prensipleriyle yaşlanma belirtileri, genişlemiş gözenekler ve pigmentasyon sorunlarına yönelik hedefe yönelik formüller geliştirir.',
    badges: ['Klinik Onaylı', 'Bio-Spicule', 'Anti-Pore'],
    key_products: ['BP Pore Ampoule', 'Cemenrete Kalsiyum Krem', 'TXA Leke Serumu']
  },
  {
    id: 2,
    name: 'Anua',
    slug: 'anua',
    origin: 'Güney Kore',
    category: 'Yatıştırıcı Cilt Bakımı',
    highlight: 'Heartleaf %77 Soothing & Niacinamide Serisi',
    description: 'Anua, hassas cilt bariyerini sakinleştiren ve doğal bitkisel içeriklerle cilt konforunu maksimize eden minimalist formülasyon anlayışını benimser.',
    badges: ['Vegan', 'Heartleaf %77', 'Hassas Cilt'],
    key_products: ['Heartleaf 77% Soothing Toner', 'Heartleaf Pore Control Cleansing Oil', 'Peach 70% Niacin Serum']
  },
  {
    id: 3,
    name: 'Medi-Peel',
    slug: 'medi-peel',
    origin: 'Güney Kore',
    category: 'Medikal Peptit Bakımı',
    highlight: 'Peptide 9 & Kolajen Hacim Kazandırıcı Seri',
    description: 'Dermatolojik estetik kliniklerinin reçetelerinden ilham alan Medi-Peel, 9 farklı peptit kompleksi ve saf kolajen ile cilt elastikiyetini yeniden yapılandırır.',
    badges: ['Peptit 9 Kompleks', 'Lifting Etki', 'Fransız Kolajeni'],
    key_products: ['Bor-Tox Peptide Ampoule', 'Peptide 9 Volume Bio Tox Ampoule', 'Red Lacto Collagen Clear']
  },
  {
    id: 4,
    name: 'Cosrx',
    slug: 'cosrx',
    origin: 'Güney Kore',
    category: 'Fonksiyonel Klinik Bakım',
    highlight: 'Advanced Snail 96 Mucin & BHA Blackhead Power',
    description: 'Cosrx, cilt sorunlarına yalın ve yüksek konsantrasyonlu aktif içeriklerle yaklaşarak küresel çapta milyonlarca kullanıcının güvenini kazanmış ikonik markadır.',
    badges: ['Snail Secretion', 'AHA/BHA', 'Hipoalerjenik'],
    key_products: ['Advanced Snail 96 Mucin Power Essence', 'BHA Blackhead Power Liquid', 'Low pH Good Morning Gel']
  },
  {
    id: 5,
    name: 'Skin1004',
    slug: 'skin1004',
    origin: 'Güney Kore',
    category: 'Saf Botanik Bakım',
    highlight: 'Madagascar Centella Asiatica & Hyalu-Cica',
    description: 'Madagaskar’ın el değmemiş doğasından elde edilen yüksek saflıktaki Centella Asiatica özleriyle tahriş olmuş cilt bariyerini onarır.',
    badges: ['Saf Madagaskar Centella', 'EWG Green', 'Hafif Doku'],
    key_products: ['Madagascar Centella Ampoule', 'Hyalu-Cica Water-Fit Sun Serum SPF50+', 'Centella Light Cleansing Oil']
  },
  {
    id: 6,
    name: 'Beauty of Joseon',
    slug: 'beauty-of-joseon',
    origin: 'Güney Kore',
    category: 'Geleneksel Hanbang Kozmetiği',
    highlight: 'Relief Sun Rice + Probiotics & Ginseng Özleri',
    description: 'Kore hanedanlığı güzellik sırları olan Hanbang bitkilerini (pirinç suyu, ginseng, yeşil erik) modern biyoteknolojiyle buluşturur.',
    badges: ['Hanbang Biyoteknoloji', 'Pirinç & Probiyotik', 'Global Bestseller'],
    key_products: ['Relief Sun: Rice + Probiotics SPF50+', 'Ginseng Essence Water', 'Glow Serum: Propolis + Niacinamide']
  },
  {
    id: 7,
    name: 'Round Lab',
    slug: 'round-lab',
    origin: 'Güney Kore',
    category: 'Mineralli Cilt Nemlendirme',
    highlight: '1025 Dokdo Serisi & Huş Ağacı Nemi',
    description: 'Ulleungdo derin deniz mineralleri ve Inje Huş Ağacı suyu ile cildin nem bariyerini güçlendirerek yumuşak ve pürüzsüz doku sağlar.',
    badges: ['Derin Deniz Mineralleri', 'Huş Ağacı Suyu', 'Olive Young 1.'],
    key_products: ['1025 Dokdo Toner', 'Birch Juice Moisturizing Sun Cream', '1025 Dokdo Cleanser']
  },
  {
    id: 8,
    name: 'Tocobo',
    slug: 'tocobo',
    origin: 'Güney Kore',
    category: 'Vegan Güneş & Dudak Bakımı',
    highlight: 'Bio Watery Sun Cream & Cica Calming Sun Stick',
    description: 'Canlı renkleri, hafif dokuları ve %100 vegan sertifikasıyla modern tüketiciye zahmetsiz ve konforlu güneş koruma çözümleri sunar.',
    badges: ['%100 Vegan', 'Mat Bitiş', 'Reef-Safe SPF50+'],
    key_products: ['Cotton Soft Sun Stick SPF50+', 'Bio Watery Sun Cream', 'Glow Ritual Lip Balm']
  },
  {
    id: 9,
    name: 'Torriden',
    slug: 'torriden',
    origin: 'Güney Kore',
    category: 'Moleküler Hyaluronik Bakım',
    highlight: 'DIVE-IN Düşük Moleküler Hyaluronik Asit',
    description: '5 farklı molekül ağırlığındaki hyaluronik asit kompleksi sayesinde cildin en derin katmanlarına kadar nem iletimi sağlar.',
    badges: ['5D Hyaluronik Kompleks', 'Derin Nemlendirme', 'Hipoalerjenik'],
    key_products: ['DIVE-IN Low Molecular Hyaluronic Acid Serum', 'DIVE-IN Soothing Cream', 'Cellmazing Vitamin C Ampoule']
  },
  {
    id: 10,
    name: 'Isntree',
    slug: 'isntree',
    origin: 'Güney Kore',
    category: 'Eko-Dostu Fonksiyonel Kozmetik',
    highlight: 'Hyaluronic Acid Watery Sun Gel & Yeşil Çay',
    description: 'Temiz ve güvenilir doğal kaynaklardan üretilen Isntree, paraben ve yapay renklendirici içermeyen saf formüllere odaklanır.',
    badges: ['Temiz İçerik', '8 Kat Hyaluronik Asit', 'Cruelty-Free'],
    key_products: ['Hyaluronic Acid Watery Sun Gel SPF50+', 'Green Tea Fresh Toner', 'Chestnut BHA 2% Clear Liquid']
  },
  {
    id: 11,
    name: 'Klairs',
    slug: 'klairs',
    origin: 'Güney Kore',
    category: 'Hassas Cilt Çözümleri',
    highlight: 'Freshly Juiced C Vitamini & Supple Preparation',
    description: 'Hassas ve reaktif ciltler için özel olarak formüle edilen Klairs, nazik ama yüksek performanslı bakım rutinleri inşa eder.',
    badges: ['Hassas Ciltler İçin', 'Vegan Society', 'Saf C Vitamini'],
    key_products: ['Freshly Juiced Vitamin Drop', 'Supple Preparation Unscented Toner', 'Midnight Blue Calming Cream']
  },
  {
    id: 12,
    name: 'Haruharu Wonder',
    slug: 'haruharu-wonder',
    origin: 'Güney Kore',
    category: 'Fermente Antioksidan Bakım',
    highlight: 'Black Rice Fermente Siyah Pirinç & Bakuchiol',
    description: 'Patentli Ultra Deep Teknolojisi ile fermente siyah pirinç ve bambu sürgünü ekstrelerini cildin derin katmanlarına nüfuz ettirir.',
    badges: ['Patentli Ultra Deep Tech', 'Fermente Siyah Pirinç', 'Anti-Aging'],
    key_products: ['Black Rice Moisture Airyfit Daily Sunscreen', 'Black Rice Hyaluronic Toner', 'Black Rice Bakuchiol Eye Cream']
  },
  {
    id: 13,
    name: 'Pyunkang Yul',
    slug: 'pyunkang-yul',
    origin: 'Güney Kore',
    category: 'Doğu Tıbbı Minimalizmi',
    highlight: 'Essence Toner & Calming Moisture Bariyer',
    description: 'Ünlü Pyunkang Doğu Tıbbı Hastanesi tarafından geliştirilen formüller, gereksiz tüm kimyasallardan arındırılmış yalın içeriklerle cilt dengesini korur.',
    badges: ['Doğu Tıbbı Reçetesi', 'Kök Ekstreleri', 'Sıfır Koku'],
    key_products: ['Essence Toner', 'Calming Moisture Barrier Cream', 'ATO Lotion Blue Label']
  },
  {
    id: 14,
    name: 'Axis-Y',
    slug: 'axis-y',
    origin: 'Güney Kore',
    category: 'İklim İlhamlı Cilt Çözümleri',
    highlight: 'Dark Spot Correcting Glow Serum (%5 Niacinamide)',
    description: 'İklimsel faktörler ve çevresel kirliliğin neden olduğu leke, donukluk ve sebum dengesizliklerine özel 6+1+1 formül mimarisi sunar.',
    badges: ['6+1+1 İleri Formül', '%5 Niasinamid', 'Leke Karşıtı'],
    key_products: ['Dark Spot Correcting Glow Serum', 'Complete No-Stress Physical Sunscreen', 'Artichoke Intensive Skin Barrier Ampoule']
  },
  {
    id: 15,
    name: 'Purito',
    slug: 'purito',
    origin: 'Güney Kore',
    category: 'Sürdürülebilir Bariyer Bakımı',
    highlight: 'Wonder Releaf Centella & Oat-in Calming Gel',
    description: 'Doğaya ve cilde saygılı, geri dönüştürülebilir ambalajlı ve %100 şeffaf içerik listeleriyle üretilen hipoalerjenik bakım markası.',
    badges: ['Eko-Ambalaj', 'Yulaf & Centella', 'Bariyer Onarımı'],
    key_products: ['Wonder Releaf Centella Serum', 'Oat-in Calming Gel Cream', 'Daily Soft Touch Sunscreen SPF50+']
  }
];

const catalogRepository = {
  banners() {
    return banners;
  },
  faqs() {
    return faqs;
  },
  mainCategories() {
    return categories.filter(c => !c.parent_id);
  },
  categoryBySlug(slug) {
    return categories.find(c => c.slug === slug) || null;
  },
  subCategories(parentId) {
    return categories.filter(c => Number(c.parent_id) === Number(parentId));
  },
  allProducts() {
    return products;
  },
  featuredProducts(limit = 8) {
    return products.filter(p => p.is_featured).slice(0, limit);
  },
  productsByCategory(categoryId) {
    const childIds = this.subCategories(categoryId).map(c => c.id);
    const targetIds = [categoryId, ...childIds].map(Number);
    return products.filter(p => targetIds.includes(Number(p.category_id)));
  },
  productBySlug(slug) {
    return products.find(p => p.slug === slug) || null;
  },
  relatedProducts(categoryId, excludeId, limit = 4) {
    return this.productsByCategory(categoryId)
      .filter(p => Number(p.id) !== Number(excludeId))
      .slice(0, limit);
  },
  distributorBrands() {
    return distributorBrands;
  },
  distributorBrandBySlug(slug) {
    return distributorBrands.find(b => b.slug === slug) || null;
  },
  search(query) {
    const needle = query.trim().toLocaleLowerCase('tr-TR');
    return products.filter(p => {
      const searchTarget = `${p.name} ${p.sku} ${p.description || ''}`.toLocaleLowerCase('tr-TR');
      return searchTarget.includes(needle);
    });
  }
};

module.exports = {
  categories,
  products,
  banners,
  faqs,
  distributorBrands,
  channels: { trendyol: trendyolStore, hepsiburada: hepsiburadaStore },
  catalogRepository
};


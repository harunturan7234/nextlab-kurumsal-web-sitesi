#!/usr/bin/env bash
# Natro public_html dağıtım paketini oluşturur.
# Yalnızca PHP/Apache üretimi için gerekli dosyaları kopyalar;
# Node.js, EJS, kaynak görseller ve geliştirme araçlarını HARİÇ tutar.
#
# Kullanım:  bash scripts/build-dist.sh
# Çıktı:     dist/public_html/  (yüklenecek klasör)  +  dist/nextlab-public_html.zip
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
OUT="$ROOT/dist/public_html"
ZIP="$ROOT/dist/nextlab-public_html.zip"

rm -rf "$ROOT/dist"
mkdir -p "$OUT"

# --- Üretim giriş noktaları ve yapılandırma ---
cp "$ROOT/index.php"            "$OUT/"
cp "$ROOT/router.php"           "$OUT/"        # yalnızca yerel önizleme; Apache'de .htaccess yönlendirir
cp "$ROOT/.htaccess"            "$OUT/"
cp "$ROOT/config.example.php"   "$OUT/"        # config.php hosting'de buradan türetilir (asla gerçek config gönderilmez)
cp "$ROOT/robots.txt"           "$OUT/"
cp "$ROOT/database.sql"         "$OUT/"
[ -f "$ROOT/NATRO-KURULUM.md" ] && cp "$ROOT/NATRO-KURULUM.md" "$OUT/"

# --- Uygulama kodu (PHP) ---
cp -R "$ROOT/app"              "$OUT/app"

# --- Varlıklar: CSS / JS / görseller (kaynak-görseller HARİÇ) ---
mkdir -p "$OUT/assets"
cp -R "$ROOT/assets/css"       "$OUT/assets/css"
cp -R "$ROOT/assets/js"        "$OUT/assets/js"
cp -R "$ROOT/assets/images"    "$OUT/assets/images"

# --- Veri: çalışma zamanında gereken JSON dosyaları ---
# media.json her modda okunur; catalog/blogs yalnızca 'file' moduna (ve config.php yokken demo'ya) gerekir.
mkdir -p "$OUT/data"
cp "$ROOT/data/media.json"     "$OUT/data/"
cp "$ROOT/data/catalog.json"   "$OUT/data/"
cp "$ROOT/data/blogs.json"     "$OUT/data/"

# --- Hassas/gereksiz dosyaların sızmadığını doğrula ---
LEAK=0
for bad in config.php server.js node_modules views services lib scripts qa source-assets \
           package.json package-lock.json bun.lock metadata.json .git .env; do
  if [ -e "$OUT/$bad" ]; then echo "HATA: dağıtıma sızan dosya: $bad"; LEAK=1; fi
done
[ "$LEAK" -eq 0 ] || { echo "Dağıtım iptal edildi."; exit 1; }

# --- Zip paketi ---
( cd "$OUT" && zip -rq "$ZIP" . -x '*.DS_Store' )

echo "Dağıtım hazır:"
echo "  Klasör: $OUT"
echo "  Zip:    $ZIP"
echo "  Dosya sayısı: $(find "$OUT" -type f | wc -l | tr -d ' ')"
echo "  Toplam boyut: $(du -sh "$OUT" | cut -f1)"

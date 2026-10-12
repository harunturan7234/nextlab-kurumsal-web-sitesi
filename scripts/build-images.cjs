// Resize only: preserve aspect ratio, packaging, branding and all printed text.
const fs = require('node:fs');
const path = require('node:path');
const crypto = require('node:crypto');
const sharp = require('sharp');
const sources = require('../data/image-sources.json');
const root = path.resolve(__dirname, '..');
(async () => {
  const out = path.join(root, 'assets/images/verified');
  const originals = path.join(root, 'source-assets');
  fs.mkdirSync(out, { recursive: true });
  fs.mkdirSync(originals, { recursive: true });
  const manifest = {};
  for (const source of sources) {
    let input = source.input;
    const saved = path.join(originals, source.key + '.original');
    if (fs.existsSync(saved)) input = saved;
    const original = fs.readFileSync(input);
    fs.writeFileSync(saved, original);
    const meta = await sharp(original).metadata();
    const variants = [];
    for (const width of source.widths.filter(w => w <= meta.width)) {
      const file = `images/verified/${source.key}-${width}.webp`;
      const info = await sharp(original).rotate().resize({ width, withoutEnlargement: true }).webp({ quality: 86, effort: 5 }).toFile(path.join(root, 'assets', file));
      variants.push({ file, width: info.width, height: info.height, bytes: info.size });
    }
    for (const v of variants) manifest[v.file] = { ...v, variants, source: source.source, originalWidth: meta.width, originalHeight: meta.height, sha256: crypto.createHash('sha256').update(original).digest('hex') };
  }
  fs.writeFileSync(path.join(root, 'data/media.json'), JSON.stringify(manifest, null, 2));
  console.log(`${sources.length} originals, ${Object.keys(manifest).length} responsive WebP files`);
})().catch(error => { console.error(error); process.exit(1); });

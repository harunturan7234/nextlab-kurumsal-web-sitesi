const manifest = require('../data/media.json');
const escape = require('ejs').escapeXML;
const mediaSrcset = source => (manifest[source]?.variants || []).map(v => '/assets/' + v.file + ' ' + v.width + 'w').join(', ');
function imageTag(source, alt, options = {}) {
  if (!source) return '<span class="asset-pending"><span aria-hidden="true">＋</span><span>Ürün fotoğrafı<br>hazırlanıyor</span></span>';
  const m = manifest[source];
  const dimensions = m ? ' width="' + m.width + '" height="' + m.height + '"' : '';
  const responsive = m ? ' srcset="' + escape(mediaSrcset(source)) + '" sizes="' + escape(options.sizes || '100vw') + '"' : '';
  return '<img src="/assets/' + escape(source) + '" alt="' + escape(alt) + '"' + dimensions + responsive + ' decoding="async" loading="' + (options.priority ? 'eager' : 'lazy') + '"' + (options.priority ? ' fetchpriority="high"' : '') + '>';
}
module.exports = { imageTag, mediaSrcset };

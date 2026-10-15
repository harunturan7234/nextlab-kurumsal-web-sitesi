/* Nextlab Yönetim Paneli — arayüz etkileşimleri (bağımlılık yok) */
(function () {
  'use strict';

  // --- mobil menü ---
  var burger = document.querySelector('.burger');
  var side = document.querySelector('.side');
  if (burger && side) {
    burger.addEventListener('click', function () {
      var open = side.classList.toggle('open');
      burger.setAttribute('aria-expanded', String(open));
    });
    document.addEventListener('click', function (ev) {
      if (!side.contains(ev.target) && !burger.contains(ev.target)) side.classList.remove('open');
    });
  }

  // --- onay gerektiren formlar ---
  document.querySelectorAll('form[data-confirm]').forEach(function (f) {
    f.addEventListener('submit', function (ev) {
      if (!window.confirm(f.getAttribute('data-confirm'))) ev.preventDefault();
    });
  });

  // --- liste araması ---
  var q = document.getElementById('q');
  if (q) {
    q.addEventListener('input', function () {
      var term = q.value.trim().toLocaleLowerCase('tr');
      document.querySelectorAll('tbody tr[data-search]').forEach(function (tr) {
        tr.style.display = !term || tr.getAttribute('data-search').indexOf(term) !== -1 ? '' : 'none';
      });
    });
  }

  // --- slug otomatik üretimi ---
  function slugify(v) {
    var map = { 'İ': 'i', 'I': 'i', 'ı': 'i', 'Ş': 's', 'ş': 's', 'Ğ': 'g', 'ğ': 'g', 'Ü': 'u', 'ü': 'u', 'Ö': 'o', 'ö': 'o', 'Ç': 'c', 'ç': 'c' };
    return v.replace(/[İIıŞşĞğÜüÖöÇç]/g, function (c) { return map[c]; })
      .toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
  }
  document.querySelectorAll('input[data-slug-from]').forEach(function (slugInput) {
    var srcId = 'f_' + slugInput.getAttribute('data-slug-from');
    var src = document.getElementById(srcId);
    if (!src) return;
    var touched = slugInput.value.trim() !== '';
    slugInput.addEventListener('input', function () { touched = true; });
    src.addEventListener('input', function () {
      if (!touched) slugInput.value = slugify(src.value);
    });
  });

  // --- görsel alanı: önizleme, kitaplık, kaldır ---
  document.querySelectorAll('.imgfield').forEach(function (wrap) {
    var hidden = wrap.querySelector('input[type=hidden]');
    var file = wrap.querySelector('input[type=file]');
    var prev = wrap.querySelector('.imgprev');
    var path = wrap.querySelector('.imgpath');

    function show(src, label) {
      prev.innerHTML = src ? '<img alt="">' : '';
      if (src) prev.querySelector('img').src = src;
      prev.classList.toggle('none', !src);
      if (path) path.textContent = label || 'Görsel seçilmedi';
    }
    if (file) {
      file.addEventListener('change', function () {
        var f = file.files && file.files[0];
        if (f) show(URL.createObjectURL(f), f.name + ' (yüklenecek)');
      });
    }
    var clear = wrap.querySelector('.clearimg');
    if (clear) clear.addEventListener('click', function () {
      hidden.value = ''; if (file) file.value = ''; show('', '');
    });
    var pick = wrap.querySelector('.pick');
    if (pick) pick.addEventListener('click', function () {
      var dlg = document.getElementById('mediaPicker');
      if (!dlg) { window.location.href = '/admin/medya'; return; }
      dlg.__target = { hidden: hidden, show: show };
      dlg.showModal();
    });
  });

  var picker = document.getElementById('mediaPicker');
  if (picker) {
    picker.querySelectorAll('.pick-item').forEach(function (b) {
      b.addEventListener('click', function () {
        var p = b.getAttribute('data-path');
        if (picker.__target) {
          picker.__target.hidden.value = p;
          picker.__target.show('/assets/' + p, p);
        }
        picker.close();
      });
    });
  }

  // --- yol kopyala (kitaplık) ---
  document.querySelectorAll('.copy').forEach(function (b) {
    b.addEventListener('click', function () {
      var t = b.getAttribute('data-copy');
      (navigator.clipboard ? navigator.clipboard.writeText(t) : Promise.reject()).then(function () {
        var old = b.textContent; b.textContent = 'Kopyalandı ✓';
        setTimeout(function () { b.textContent = old; }, 1400);
      }).catch(function () { window.prompt('Görsel yolu:', t); });
    });
  });

  // --- zengin metin editörü ---
  document.querySelectorAll('.rt').forEach(function (rt) {
    var edit = rt.querySelector('.rt-edit');
    var src = rt.querySelector('.rt-src');
    var htmlMode = false;

    function sync() { if (!htmlMode) src.value = edit.innerHTML.trim(); }
    edit.addEventListener('input', sync);
    edit.addEventListener('blur', sync);

    rt.querySelectorAll('.rt-bar button').forEach(function (b) {
      b.addEventListener('click', function (ev) {
        ev.preventDefault();
        if (b.hasAttribute('data-html')) {
          htmlMode = !htmlMode;
          if (htmlMode) { src.value = edit.innerHTML.trim(); src.hidden = false; edit.style.display = 'none'; b.textContent = '✎ Görsel'; }
          else { edit.innerHTML = src.value; src.hidden = true; edit.style.display = ''; b.textContent = '</> HTML'; }
          return;
        }
        if (htmlMode) return;
        edit.focus();
        if (b.hasAttribute('data-link')) {
          var url = window.prompt('Bağlantı adresi (site içi için /urunler gibi yazın):', '/');
          if (url) document.execCommand('createLink', false, url);
        } else if (b.hasAttribute('data-block')) {
          document.execCommand('formatBlock', false, b.getAttribute('data-block'));
        } else {
          document.execCommand(b.getAttribute('data-cmd'), false, null);
        }
        sync();
      });
    });

    var form = rt.closest('form');
    if (form) form.addEventListener('submit', function () { if (!htmlMode) sync(); });
  });

  // --- kaydedilmemiş değişiklik uyarısı ---
  var editor = document.getElementById('editor');
  if (editor) {
    var dirty = false;
    editor.addEventListener('input', function () { dirty = true; });
    editor.addEventListener('submit', function () { dirty = false; });
    window.addEventListener('beforeunload', function (ev) {
      if (dirty) { ev.preventDefault(); ev.returnValue = ''; }
    });
  }
})();

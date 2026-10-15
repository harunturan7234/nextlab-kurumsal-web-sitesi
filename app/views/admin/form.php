<?php
/** @var Admin $admin */ /** @var array $e */ /** @var array|null $row */ /** @var array $data */ /** @var bool $isNew */
$pageTitle = $isNew ? ('Yeni ' . $e['singular']) : ($e['singular'] . ' düzenle');
$status = $row['status'] ?? 'draft';
$badge = ['published' => ['Yayında', 'ok'], 'modified' => ['Değişiklik var', 'warn'], 'draft' => ['Taslak', 'muted']][$status];
$action = !empty($e['single']) ? '/admin/' . $e['key'] : '/admin/' . $e['key'] . '/kaydet';
$media = $admin->mediaLibrary();
$catOptions = $admin->categoryOptions();

$fieldName = fn(string $k) => str_replace('.', '__', $k);

$groups = [];
foreach ($e['fields'] as $f) $groups[$f['group'] ?? ''][] = $f;

ob_start();
?>
<form method="post" action="<?= admin_e($action) ?>" enctype="multipart/form-data" class="editor" id="editor">
  <input type="hidden" name="_csrf" value="<?= admin_e($admin->csrfToken()) ?>">
  <?php if (!$isNew && empty($e['single'])): ?><input type="hidden" name="id" value="<?= (int)$row['id'] ?>"><?php endif; ?>

  <div class="editor-bar">
    <div class="eb-l">
      <a class="back" href="/admin/<?= admin_e($e['key']) ?>">← <?= admin_e($e['label']) ?></a>
      <?php if (empty($e['single'])): ?><span class="badge <?= $badge[1] ?>"><?= $badge[0] ?></span><?php endif; ?>
      <?php $pubSlug = (string) Admin::get($data, 'slug', ''); ?>
      <?php if (!$isNew && !empty($e['public']) && $pubSlug !== '' && $status !== 'draft'): ?>
        <a class="back" href="/<?= admin_e(sprintf($e['public'], $pubSlug)) ?>" target="_blank" rel="noopener">Sayfayı gör ↗</a>
      <?php endif; ?>
    </div>
    <div class="eb-r">
      <button class="btn" type="submit" name="_save" value="1">Taslağa Kaydet</button>
      <button class="btn btn-pub" type="submit" name="_publish" value="1">Kaydet ve Yayına Al</button>
    </div>
  </div>

  <?php foreach ($groups as $gname => $fields): ?>
    <section class="panel">
      <?php if ($gname !== ''): ?><h3><?= admin_e($gname) ?></h3><?php endif; ?>
      <?php foreach ($fields as $f):
        $k = $f['key']; $n = $fieldName($k); $t = $f['type'] ?? 'text';
        $v = Admin::get($data, $k, '');
        $req = !empty($f['required']) ? 'required' : '';
      ?>
      <div class="fld-row" data-type="<?= admin_e($t) ?>">
        <label class="fld" for="f_<?= admin_e($n) ?>">
          <span><?= admin_e($f['label']) ?><?= $req ? ' <i class="req">*</i>' : '' ?></span>

          <?php if ($t === 'textarea'): ?>
            <textarea id="f_<?= admin_e($n) ?>" name="<?= admin_e($n) ?>" rows="<?= (int)($f['rows'] ?? 3) ?>" <?= $req ?>><?= admin_e($v) ?></textarea>

          <?php elseif ($t === 'list'): ?>
            <textarea id="f_<?= admin_e($n) ?>" name="<?= admin_e($n) ?>" rows="<?= max(3, count((array)$v) + 1) ?>" placeholder="Her satıra bir madde"><?= admin_e(implode("\n", (array)$v)) ?></textarea>

          <?php elseif ($t === 'kvlist'): ?>
            <?php $lines = array_map(fn($i) => ($i['label'] ?? '') . '|' . ($i['url'] ?? ''), (array)$v); ?>
            <textarea id="f_<?= admin_e($n) ?>" name="<?= admin_e($n) ?>" rows="<?= max(3, count($lines) + 1) ?>" placeholder="Etiket|/hedef-adres"><?= admin_e(implode("\n", $lines)) ?></textarea>

          <?php elseif ($t === 'idlist'): ?>
            <input id="f_<?= admin_e($n) ?>" type="text" name="<?= admin_e($n) ?>" value="<?= admin_e(implode(', ', (array)$v)) ?>" placeholder="1, 2, 3">

          <?php elseif ($t === 'bool'): ?>
            <span class="switch">
              <input id="f_<?= admin_e($n) ?>" type="checkbox" name="<?= admin_e($n) ?>" value="1" <?= !empty($v) ? 'checked' : '' ?>>
              <i></i><em>Açık</em>
            </span>

          <?php elseif ($t === 'select'): ?>
            <select id="f_<?= admin_e($n) ?>" name="<?= admin_e($n) ?>" <?= $req ?>>
              <?php if (!empty($f['allow_empty'])): ?><option value=""><?= admin_e($f['allow_empty']) ?></option><?php endif; ?>
              <?php foreach ($catOptions as $oid => $oname): ?>
                <?php if (!$isNew && $k === 'parent_id' && (int)$oid === (int)($row['id'] ?? 0)) continue; ?>
                <option value="<?= (int)$oid ?>" <?= (string)$v === (string)$oid ? 'selected' : '' ?>><?= admin_e($oname) ?></option>
              <?php endforeach; ?>
            </select>

          <?php elseif ($t === 'image'): ?>
            <div class="imgfield">
              <div class="imgprev <?= $v ? '' : 'none' ?>">
                <?php if ($v): ?><img src="/assets/<?= admin_e($v) ?>" alt=""><?php endif; ?>
              </div>
              <div class="imgctl">
                <input type="hidden" name="<?= admin_e($n) ?>" id="f_<?= admin_e($n) ?>" value="<?= admin_e($v) ?>">
                <input type="file" name="<?= admin_e($n) ?>" accept="image/*" class="file" aria-label="<?= admin_e($f['label']) ?> yükle">
                <button type="button" class="btn btn-sm pick" data-target="f_<?= admin_e($n) ?>">Kitaplıktan seç</button>
                <?php if ($v): ?><button type="button" class="btn btn-sm clearimg" data-target="f_<?= admin_e($n) ?>">Kaldır</button><?php endif; ?>
                <code class="imgpath"><?= admin_e($v ?: 'Görsel seçilmedi') ?></code>
              </div>
            </div>

          <?php elseif ($t === 'richtext'): ?>
            <div class="rt" data-for="f_<?= admin_e($n) ?>">
              <div class="rt-bar">
                <button type="button" data-cmd="bold"><b>B</b></button>
                <button type="button" data-cmd="italic"><i>I</i></button>
                <button type="button" data-block="h2">H2</button>
                <button type="button" data-block="h3">H3</button>
                <button type="button" data-block="p">¶</button>
                <button type="button" data-cmd="insertUnorderedList">• Liste</button>
                <button type="button" data-link="1">🔗 Bağlantı</button>
                <button type="button" data-cmd="removeFormat">Temizle</button>
                <button type="button" class="rt-html" data-html="1">&lt;/&gt; HTML</button>
              </div>
              <div class="rt-edit" contenteditable="true" role="textbox" aria-multiline="true"><?= $v ?></div>
              <textarea class="rt-src" id="f_<?= admin_e($n) ?>" name="<?= admin_e($n) ?>" hidden><?= admin_e($v) ?></textarea>
            </div>

          <?php elseif ($t === 'slug'): ?>
            <input id="f_<?= admin_e($n) ?>" type="text" name="<?= admin_e($n) ?>" value="<?= admin_e($v) ?>"
                   data-slug-from="<?= admin_e($fieldName($f['from'] ?? '')) ?>" <?= $req ?> placeholder="otomatik oluşturulur">

          <?php elseif ($t === 'date'): ?>
            <input id="f_<?= admin_e($n) ?>" type="date" name="<?= admin_e($n) ?>" value="<?= admin_e(substr((string)$v, 0, 10)) ?>">

          <?php else: ?>
            <input id="f_<?= admin_e($n) ?>" type="<?= $t === 'url' ? 'url' : 'text' ?>" name="<?= admin_e($n) ?>"
                   value="<?= admin_e($v) ?>" <?= $req ?> placeholder="<?= admin_e($f['placeholder'] ?? '') ?>">
          <?php endif; ?>

          <?php if (!empty($f['help'])): ?><small class="help"><?= admin_e($f['help']) ?></small><?php endif; ?>
        </label>
      </div>
      <?php endforeach; ?>
    </section>
  <?php endforeach; ?>

  <div class="editor-foot">
    <button class="btn" type="submit">Taslağa Kaydet</button>
    <button class="btn btn-pub" type="submit" name="_publish" value="1">Kaydet ve Yayına Al</button>
    <?php if (!$isNew && empty($e['single'])): ?>
      <a class="btn btn-ghost" href="/admin/<?= admin_e($e['key']) ?>">Vazgeç</a>
    <?php endif; ?>
  </div>
</form>

<?php if ($media): ?>
<dialog id="mediaPicker" class="picker">
  <h3>Görsel kitaplığı</h3>
  <div class="picker-grid">
    <?php foreach ($media as $m): ?>
      <button type="button" class="pick-item" data-path="<?= admin_e($m) ?>">
        <img src="/assets/<?= admin_e($m) ?>" alt="" loading="lazy">
      </button>
    <?php endforeach; ?>
  </div>
  <div class="picker-foot">
    <a class="btn btn-sm" href="/admin/medya">Yeni görsel yükle</a>
    <button type="button" class="btn btn-sm" onclick="this.closest('dialog').close()">Kapat</button>
  </div>
</dialog>
<?php endif; ?>
<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';

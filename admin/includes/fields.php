<?php
/* ============================================================
   Perender kolom formulir + pembacaan nilai dari POST
   ============================================================ */

/**
 * Sisipkan akhiran ke dalam nama field, termasuk nama bertingkat.
 * "gambar" + "__current"                  => "gambar__current"
 * "children[x][0][file]" + "__current"    => "children[x][0][file__current]"
 */
function name_suffix(string $name, string $suffix): string
{
    if (str_ends_with($name, ']')) {
        return preg_replace('/\[([^\[\]]*)\]$/', '[$1' . $suffix . ']', $name);
    }
    return $name . $suffix;
}

/** Baca nilai POST untuk nama datar maupun bertingkat (a[b][c]). */
function post_get(string $name)
{
    if (!str_contains($name, '[')) {
        return $_POST[$name] ?? null;
    }
    preg_match('/^([^\[]+)/', $name, $root);
    $node = $_POST[$root[1]] ?? null;
    preg_match_all('/\[([^\]]*)\]/', $name, $parts);
    foreach ($parts[1] as $piece) {
        if (!is_array($node) || !array_key_exists($piece, $node)) {
            return null;
        }
        $node = $node[$piece];
    }
    return $node;
}

/** Opsi select: statis (`options`) atau dari kueri (`options_sql`). */
function field_options(array $field): array
{
    if (!empty($field['options'])) {
        return $field['options'];
    }
    if (!empty($field['options_sql'])) {
        $out = [];
        foreach (db_all($field['options_sql']) as $row) {
            $out[(string) $row['value']] = $row['label'];
        }
        return $out;
    }
    return [];
}

/**
 * Render satu kolom formulir.
 * $name dipakai sebagai atribut name; $value nilai saat ini.
 */
function render_field(string $name, array $field, $value): void
{
    $type     = $field['type'] ?? 'text';
    $id       = 'f_' . preg_replace('/[^a-z0-9]+/i', '_', $name);
    $required = !empty($field['required']) ? ' required' : '';
    $readonly = !empty($field['readonly']) ? ' readonly' : '';
    $col      = ($field['col'] ?? 'full') === 'half' ? 'col-half' : 'col-full';
    ?>
    <div class="field <?= $col ?>" data-field-type="<?= e($type) ?>">
      <?php if ($type !== 'checkbox'): ?>
      <label for="<?= e($id) ?>"><?= e($field['label']) ?><?= $required ? ' <span class="req">*</span>' : '' ?></label>
      <?php endif; ?>

      <?php switch ($type):
        case 'textarea': ?>
          <textarea id="<?= e($id) ?>" name="<?= e($name) ?>" rows="<?= (int) ($field['rows'] ?? 3) ?>"<?= $required . $readonly ?>><?= e((string) $value) ?></textarea>
        <?php break;

        case 'richtext': ?>
          <textarea id="<?= e($id) ?>" name="<?= e($name) ?>" rows="<?= (int) ($field['rows'] ?? 8) ?>" class="richtext"<?= $required . $readonly ?>><?= e((string) $value) ?></textarea>
        <?php break;

        case 'number': ?>
          <input type="number" id="<?= e($id) ?>" name="<?= e($name) ?>" value="<?= e((string) $value) ?>"<?= $required . $readonly ?>>
        <?php break;

        case 'date': ?>
          <input type="date" id="<?= e($id) ?>" name="<?= e($name) ?>" value="<?= e($value ? date('Y-m-d', strtotime((string) $value)) : '') ?>"<?= $required . $readonly ?>>
        <?php break;

        case 'password': ?>
          <input type="password" id="<?= e($id) ?>" name="<?= e($name) ?>" value="" autocomplete="new-password"<?= $readonly ?>>
        <?php break;

        case 'checkbox': ?>
          <label class="switch">
            <input type="hidden" name="<?= e($name) ?>" value="0">
            <input type="checkbox" id="<?= e($id) ?>" name="<?= e($name) ?>" value="1"<?= (int) $value === 1 ? ' checked' : '' ?>>
            <span class="track"></span>
            <span class="switch-label"><?= e($field['label']) ?></span>
          </label>
        <?php break;

        case 'select':
          $options = field_options($field); ?>
          <select id="<?= e($id) ?>" name="<?= e($name) ?>"<?= $required ?>>
            <?php if (isset($field['empty'])): ?><option value=""><?= e($field['empty']) ?></option><?php endif; ?>
            <?php foreach ($options as $optValue => $optLabel): ?>
            <option value="<?= e((string) $optValue) ?>"<?= (string) $value === (string) $optValue ? ' selected' : '' ?>><?= e($optLabel) ?></option>
            <?php endforeach; ?>
          </select>
        <?php break;

        case 'slug': ?>
          <input type="text" id="<?= e($id) ?>" name="<?= e($name) ?>" value="<?= e((string) $value) ?>"
                 data-slug-from="<?= e($field['from'] ?? '') ?>"<?= $required . $readonly ?>>
        <?php break;

        case 'icon': ?>
          <div class="icon-input">
            <span class="icon-preview"><i class="bi <?= e($value ?: 'bi-app') ?>"></i></span>
            <input type="text" id="<?= e($id) ?>" name="<?= e($name) ?>" value="<?= e((string) $value) ?>" placeholder="bi-airplane-fill" data-icon-input>
          </div>
        <?php break;

        case 'image': ?>
          <div class="upload-field">
            <?php if ($value): ?>
            <div class="upload-preview">
              <img src="<?= e(asset_url((string) $value)) ?>" alt="">
              <label class="upload-remove"><input type="checkbox" name="<?= e(name_suffix($name, '__remove')) ?>" value="1"> Hapus gambar</label>
            </div>
            <?php endif; ?>
            <input type="hidden" name="<?= e(name_suffix($name, '__current')) ?>" value="<?= e((string) $value) ?>">
            <input type="file" id="<?= e($id) ?>" name="<?= e($name) ?>" accept="image/*">
          </div>
        <?php break;

        case 'file': ?>
          <div class="upload-field">
            <?php if ($value): ?>
            <div class="upload-preview file">
              <a href="<?= e(asset_url((string) $value)) ?>" target="_blank" rel="noopener"><i class="bi bi-file-earmark-arrow-down"></i> <?= e(basename((string) $value)) ?></a>
              <label class="upload-remove"><input type="checkbox" name="<?= e(name_suffix($name, '__remove')) ?>" value="1"> Hapus berkas</label>
            </div>
            <?php endif; ?>
            <input type="hidden" name="<?= e(name_suffix($name, '__current')) ?>" value="<?= e((string) $value) ?>">
            <input type="file" id="<?= e($id) ?>" name="<?= e($name) ?>">
          </div>
        <?php break;

        default: ?>
          <input type="text" id="<?= e($id) ?>" name="<?= e($name) ?>" value="<?= e((string) $value) ?>"<?= $required . $readonly ?>>
        <?php endswitch; ?>

      <?php if (!empty($field['hint'])): ?><small class="hint"><?= e($field['hint']) ?></small><?php endif; ?>
    </div>
    <?php
}

/**
 * Ambil nilai satu kolom dari request untuk disimpan.
 * Mengembalikan [simpan?, nilai].
 */
function field_value(string $name, array $field, ?array $existing = null): array
{
    $type = $field['type'] ?? 'text';

    if ($type === 'image' || $type === 'file') {
        return upload_value($name, $type);
    }

    if ($type === 'password') {
        $raw = (string) (post_get($name) ?? '');
        if ($raw === '') {
            return [false, null];
        }
        return [true, password_hash($raw, PASSWORD_BCRYPT)];
    }

    if ($type === 'checkbox') {
        return [true, (string) (post_get($name) ?? '0') === '1' ? 1 : 0];
    }

    $raw = post_get($name);
    if ($raw === null) {
        return [false, null];
    }
    $raw = is_string($raw) ? trim($raw) : $raw;

    if ($type === 'number') {
        return [true, $raw === '' ? 0 : (int) $raw];
    }
    if ($type === 'date') {
        return [true, $raw === '' ? null : $raw];
    }
    if ($type === 'slug') {
        return [true, $raw === '' ? '' : slugify($raw)];
    }
    if ($type === 'select' && $raw === '') {
        return [true, null];
    }

    return [true, $raw === '' ? null : $raw];
}

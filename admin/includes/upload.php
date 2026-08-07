<?php
/* ============================================================
   Penanganan unggahan berkas media
   ============================================================ */

/**
 * Proses satu kolom unggahan.
 * Mengembalikan [simpan?, nilai] — nilai berupa path relatif "media/...".
 */
function upload_value(string $name, string $type): array
{
    $current = post_get(name_suffix($name, '__current'));

    if (!empty(post_get(name_suffix($name, '__remove')))) {
        delete_upload($current);
        return [true, null];
    }

    // Nama field bertingkat (mis. children[service_steps][0][file]) tidak
    // terbaca oleh $_FILES sebagai key datar; ambil dengan penelusuran.
    $file = resolve_file($name);
    if (!$file || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return [$current !== null, $current];
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        flash('danger', 'Unggahan gagal (kode ' . $file['error'] . ').');
        return [$current !== null, $current];
    }
    if ($file['size'] > UPLOAD_MAX_BYTES) {
        flash('danger', 'Ukuran berkas melebihi ' . round(UPLOAD_MAX_BYTES / 1048576) . ' MB.');
        return [$current !== null, $current];
    }

    $ext     = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = $type === 'image' ? UPLOAD_ALLOWED_IMAGE : array_merge(UPLOAD_ALLOWED_FILE, UPLOAD_ALLOWED_IMAGE);
    if (!in_array($ext, $allowed, true)) {
        flash('danger', 'Jenis berkas .' . $ext . ' tidak diizinkan.');
        return [$current !== null, $current];
    }
    if ($type === 'image' && $ext !== 'svg' && @getimagesize($file['tmp_name']) === false) {
        flash('danger', 'Berkas yang diunggah bukan gambar yang valid.');
        return [$current !== null, $current];
    }

    $dir = UPLOAD_PATH . '/media/' . date('Y-m');
    if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
        flash('danger', 'Folder unggahan tidak dapat dibuat.');
        return [$current !== null, $current];
    }

    $base     = slugify(pathinfo($file['name'], PATHINFO_FILENAME));
    $filename = $base . '-' . bin2hex(random_bytes(3)) . '.' . $ext;
    if (!move_uploaded_file($file['tmp_name'], $dir . '/' . $filename)) {
        flash('danger', 'Berkas gagal disimpan ke folder unggahan.');
        return [$current !== null, $current];
    }

    delete_upload($current);
    return [true, 'media/' . date('Y-m') . '/' . $filename];
}

/** Cari entri $_FILES untuk nama field datar maupun bertingkat. */
function resolve_file(string $name): ?array
{
    if (isset($_FILES[$name])) {
        return $_FILES[$name];
    }
    if (!preg_match('/^([^\[]+)((\[[^\]]*\])+)$/', $name, $m)) {
        return null;
    }
    $root = $m[1];
    if (!isset($_FILES[$root])) {
        return null;
    }
    preg_match_all('/\[([^\]]*)\]/', $m[2], $parts);
    $keys = $parts[1];

    $out = [];
    foreach (['name', 'type', 'tmp_name', 'error', 'size'] as $prop) {
        $node = $_FILES[$root][$prop] ?? null;
        foreach ($keys as $key) {
            if (!is_array($node) || !array_key_exists($key, $node)) {
                return null;
            }
            $node = $node[$key];
        }
        $out[$prop] = $node;
    }
    return $out;
}

/** Hapus berkas unggahan lama (hanya di dalam folder uploads). */
function delete_upload(?string $path): void
{
    if (!$path || !str_starts_with($path, 'media/')) {
        return;
    }
    $full = realpath(UPLOAD_PATH . '/' . $path);
    $root = realpath(UPLOAD_PATH);
    if ($full && $root && str_starts_with($full, $root) && is_file($full)) {
        @unlink($full);
    }
}

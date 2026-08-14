<?php
/* ============================================================
   Fungsi pembantu bersama (escaping, format, konten)
   ============================================================ */
require_once __DIR__ . '/db.php';

/** Escape HTML. */
function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Escape untuk atribut yang berisi URL. */
function eurl(?string $value): string
{
    $value = trim((string) $value);
    if ($value === '') {
        return '#';
    }
    return e($value);
}

/** Ubah teks polos multi-paragraf menjadi <p>. */
function paragraphs(?string $text, string $class = ''): string
{
    $text = trim((string) $text);
    if ($text === '') {
        return '';
    }
    $blocks = preg_split('/\R{2,}/', $text);
    $attr   = $class !== '' ? ' class="' . e($class) . '"' : '';
    $html   = '';
    foreach ($blocks as $block) {
        $block = trim($block);
        if ($block === '') {
            continue;
        }
        $html .= '<p' . $attr . '>' . nl2br(e($block)) . '</p>';
    }
    return $html;
}

/** Pecah string berpemisah koma menjadi array bersih. */
function split_list(?string $value, string $sep = ','): array
{
    if (trim((string) $value) === '') {
        return [];
    }
    return array_values(array_filter(array_map('trim', explode($sep, $value)), fn($v) => $v !== ''));
}

/** Tanggal panjang bahasa Indonesia: 14 Juli 2026. */
function tanggal_id(?string $date, bool $withDay = false): string
{
    if (!$date) {
        return '';
    }
    $ts = strtotime($date);
    if ($ts === false) {
        return '';
    }
    $bulan = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
              'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    $hari  = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    $out   = (int) date('j', $ts) . ' ' . $bulan[(int) date('n', $ts)] . ' ' . date('Y', $ts);
    return $withDay ? $hari[(int) date('w', $ts)] . ', ' . $out : $out;
}

/** Bulan singkat 3 huruf: Jul. */
function bulan_singkat(?string $date): string
{
    if (!$date) {
        return '';
    }
    $ts = strtotime($date);
    $b  = [1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    return $b[(int) date('n', $ts)] ?? '';
}

/** Buat slug URL-aman. */
function slugify(string $text): string
{
    $text = strtolower(trim($text));
    $text = str_replace(['&', '/'], [' dan ', '-'], $text);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim($text, '-') ?: 'item-' . substr(md5(microtime(true)), 0, 6);
}

/** Potong teks dengan elipsis. */
function excerpt(?string $text, int $limit = 140): string
{
    $text = trim(strip_tags((string) $text));
    if (mb_strlen($text) <= $limit) {
        return $text;
    }
    return mb_substr($text, 0, $limit) . '…';
}

/* ------------------------------------------------------------
   Pengambilan konten yang dipakai lintas halaman
   ------------------------------------------------------------ */

/** Seluruh pengaturan sebagai peta key => value (di-cache per request). */
function settings(): array
{
    static $cache = null;
    if ($cache === null) {
        $cache = [];
        foreach (db_all('SELECT setting_key, value FROM settings') as $row) {
            $cache[$row['setting_key']] = $row['value'];
        }
    }
    return $cache;
}

function setting(string $key, string $default = ''): string
{
    $all = settings();
    return isset($all[$key]) && $all[$key] !== null && $all[$key] !== '' ? $all[$key] : $default;
}

/** Meta satu halaman. */
function page_meta(string $key): array
{
    $row = db_one('SELECT * FROM page_meta WHERE page_key = ?', [$key]);
    return $row ?: ['page_key' => $key, 'meta_title' => setting('site_title'), 'meta_description' => setting('site_description'),
                    'eyebrow' => null, 'heading' => null, 'subtitle' => null, 'breadcrumb' => null];
}

/** Semua judul seksi satu halaman, dipetakan berdasarkan section_key. */
function sections(string $page): array
{
    static $cache = [];
    if (!isset($cache[$page])) {
        $cache[$page] = [];
        foreach (db_all('SELECT * FROM sections WHERE page = ? AND is_active = 1 ORDER BY sort', [$page]) as $row) {
            $cache[$page][$row['section_key']] = $row;
        }
    }
    return $cache[$page];
}

function section(string $page, string $key): array
{
    $all = sections($page);
    return $all[$key] ?? ['eyebrow' => null, 'title' => null, 'subtitle' => null, 'body' => null];
}

/** Item menu untuk satu lokasi (hanya level atas; anak diambil terpisah). */
function menu_items(string $location): array
{
    static $cache = [];
    if (!isset($cache[$location])) {
        $rows = db_all(
            'SELECT * FROM menu_items WHERE location = ? AND is_active = 1 ORDER BY sort, id',
            [$location]
        );
        $tree = [];
        foreach ($rows as $row) {
            if ($row['parent_id'] === null) {
                $row['children'] = [];
                $tree[$row['id']] = $row;
            }
        }
        foreach ($rows as $row) {
            if ($row['parent_id'] !== null && isset($tree[$row['parent_id']])) {
                $tree[$row['parent_id']]['children'][] = $row;
            }
        }
        $cache[$location] = array_values($tree);
    }
    return $cache[$location];
}

/** Tautan sosial aktif. */
function social_links(): array
{
    $map = [
        'social_facebook'  => ['bi-facebook', 'Facebook'],
        'social_instagram' => ['bi-instagram', 'Instagram'],
        'social_twitter'   => ['bi-twitter-x', 'Twitter'],
        'social_youtube'   => ['bi-youtube', 'YouTube'],
    ];
    $out = [];
    foreach ($map as $key => [$icon, $label]) {
        $url = setting($key);
        if ($url !== '') {
            $out[] = ['url' => $url, 'icon' => $icon, 'label' => $label];
        }
    }
    return $out;
}

/**
 * Render blok visual: gambar unggahan bila ada, jika tidak gunakan
 * gradien placeholder `.art art-N` dengan ikon Bootstrap — sama seperti desain awal.
 */
function art_block(?string $image, ?string $artClass, ?string $icon, string $extraClass = '', string $alt = ''): string
{
    $artClass = $artClass ?: 'art-1';
    if ($image) {
        $src = asset_url($image);
        return '<div class="art ' . e($artClass) . ' ' . e($extraClass) . '" style="background-image:url(' . e($src) . ');'
             . 'background-size:cover;background-position:center;"><img src="' . e($src) . '" alt="' . e($alt)
             . '" style="width:100%;height:100%;object-fit:cover;"></div>';
    }
    $iconHtml = $icon ? '<div class="art-inner"><i class="bi ' . e($icon) . '"></i></div>' : '';
    return '<div class="art ' . e($artClass) . ' ' . e($extraClass) . '">' . $iconHtml . '</div>';
}

/** URL untuk berkas media: unggahan (uploads/) atau aset bawaan (frontend/). */
function asset_url(?string $path): string
{
    $path = ltrim((string) $path, '/');
    if ($path === '') {
        return '';
    }
    if (preg_match('#^https?://#i', $path)) {
        return $path;
    }
    if (str_starts_with($path, 'media/') || str_starts_with($path, 'uploads/')) {
        return UPLOAD_URL . '/' . preg_replace('#^uploads/#', '', $path);
    }
    return FRONTEND_URL . '/' . $path;
}

/** Ubah URL YouTube/Vimeo menjadi URL sematan. */
function embed_url(?string $url): string
{
    $url = trim((string) $url);
    if ($url === '') {
        return '';
    }
    if (preg_match('#youtube\.com/watch\?v=([\w-]+)#i', $url, $m) ||
        preg_match('#youtu\.be/([\w-]+)#i', $url, $m)) {
        return 'https://www.youtube.com/embed/' . $m[1];
    }
    if (preg_match('#vimeo\.com/(\d+)#i', $url, $m)) {
        return 'https://player.vimeo.com/video/' . $m[1];
    }
    return $url;
}

/**
 * Ubah koordinat ARP bentuk derajat-menit-detik menjadi desimal.
 * Contoh masukan: 03° 17' 24.36" LS 102° 54' 50.08" BT
 * Arah diterima dalam istilah Indonesia (LS/LU/BT/BB) maupun Inggris (S/N/E/W).
 * Mengembalikan ['lat' => float, 'lng' => float] atau null bila tidak terbaca.
 */
function arp_to_latlng(?string $arp): ?array
{
    $arp = trim((string) $arp);
    if ($arp === '') {
        return null;
    }
    /* Sebagian data memakai koma sebagai tanda desimal (31,62") dan sebagai pemisah. */
    $arp   = preg_replace('/(\d),(\d)/', '$1.$2', $arp);
    $angka = "(\d+(?:\.\d+)?)";
    $sela  = '[\s,]*';
    $pola  = '#' . $angka . $sela . '°' . $sela . $angka . $sela . "'" . $sela . '(?:' . $angka . $sela . '"?' . $sela . ')?(LS|LU|S|N)'
           . $sela . $angka . $sela . '°' . $sela . $angka . $sela . "'" . $sela . '(?:' . $angka . $sela . '"?' . $sela . ')?(BT|BB|E|W)#i';
    if (!preg_match($pola, $arp, $m)) {
        return null;
    }
    $lat = (float) $m[1] + (float) $m[2] / 60 + (float) ($m[3] ?: 0) / 3600;
    $lng = (float) $m[5] + (float) $m[6] / 60 + (float) ($m[7] ?: 0) / 3600;

    $utara = in_array(strtoupper($m[4]), ['LU', 'N'], true);
    $timur = in_array(strtoupper($m[8]), ['BT', 'E'], true);

    /* Di luar rentang wilayah Indonesia berarti pembacaan meleset. */
    if ($lat > 90 || $lng > 180) {
        return null;
    }
    return ['lat' => $utara ? $lat : -$lat, 'lng' => $timur ? $lng : -$lng];
}

<?php
/* ============================================================
   Bootstrap situs publik — dimuat pertama oleh setiap halaman.
   ============================================================ */
require_once dirname(__DIR__) . '/shared/functions.php';

/** URL absolut ke berkas di dalam /frontend. */
function url(string $path = ''): string
{
    $path = ltrim($path, '/');
    if ($path === '') {
        return FRONTEND_URL . '/';
    }
    if (preg_match('#^(https?:)?//#i', $path) || str_starts_with($path, 'mailto:') || str_starts_with($path, 'tel:')) {
        return $path;
    }
    if (str_starts_with($path, '#')) {
        return $path;
    }
    return FRONTEND_URL . '/' . $path;
}

/**
 * Konteks halaman aktif — dipakai partial head/navbar.
 * Halaman memanggil page_start('berita', [...]) sebelum menyertakan header.
 */
$GLOBALS['dbu_page'] = ['key' => 'home', 'file' => 'index.php', 'title' => '', 'description' => '', 'breadcrumbs' => []];

function page_start(string $key, array $options = []): array
{
    $meta = page_meta($key);
    $ctx  = [
        'key'         => $key,
        'file'        => basename($_SERVER['SCRIPT_NAME'] ?? 'index.php'),
        'meta'        => $meta,
        'title'       => $options['title']       ?? ($meta['meta_title'] ?: setting('site_title')),
        'description' => $options['description'] ?? ($meta['meta_description'] ?: setting('site_description')),
        'breadcrumbs' => $options['breadcrumbs'] ?? [],
    ];
    $GLOBALS['dbu_page'] = $ctx;
    return $meta;
}

function page_ctx(): array
{
    return $GLOBALS['dbu_page'];
}

/** Sertakan satu partial. */
function partial(string $name, array $vars = []): void
{
    extract($vars, EXTR_SKIP);
    include __DIR__ . '/partials/' . $name . '.php';
}

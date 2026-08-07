<?php
require_once dirname(__DIR__) . '/bootstrap.php';

$slug = trim($_GET['slug'] ?? '');
$reg  = $slug !== ''
    ? db_one('SELECT * FROM regulations WHERE slug = ? AND is_active = 1', [$slug])
    : db_one('SELECT * FROM regulations WHERE is_active = 1 ORDER BY year DESC, sort LIMIT 1');

if (!$reg) {
    http_response_code(404);
    page_start('regulasi', ['title' => 'Regulasi tidak ditemukan', 'breadcrumbs' => [['label' => 'Regulasi', 'url' => 'pages/regulasi.php'], ['label' => 'Tidak ditemukan']]]);
    partial('header');
    echo '<section class="section"><div class="container" style="text-align:center;padding:60px 0;">'
       . '<h1 style="font-size:24px;margin-bottom:12px;">Regulasi tidak ditemukan</h1>'
       . '<a href="' . e(url('pages/regulasi.php')) . '" class="btn btn-primary btn-sm">Kembali ke Daftar Regulasi</a></div></section>';
    partial('footer');
    exit;
}

page_start('regulasi', [
    'title'       => $reg['number'] . ' — Regulasi — ' . setting('site_name'),
    'description' => excerpt($reg['about'], 200),
    'breadcrumbs' => [['label' => 'Regulasi', 'url' => 'pages/regulasi.php'], ['label' => $reg['number']]],
]);

$catLabel = db_value('SELECT label FROM regulation_categories WHERE slug = ?', [$reg['category']], $reg['category']);
$scopes   = db_all('SELECT * FROM regulation_scopes WHERE regulation_id = ? ORDER BY sort, id', [$reg['id']]);
$related  = db_all(
    'SELECT slug, number, title FROM regulations WHERE is_active = 1 AND id <> ? ORDER BY (category = ?) DESC, year DESC LIMIT 3',
    [$reg['id'], $reg['category']]
);

partial('header');
?>

<section class="section">
  <div class="container">
    <div class="grid" style="grid-template-columns:2fr 1fr; align-items:start; gap:40px;">

      <div>
        <div class="flex gap-8" style="margin-bottom:16px;">
          <span class="badge badge-gray"><?= e($catLabel) ?></span>
          <span class="badge <?= $reg['status'] === 'berlaku' ? 'badge-success' : 'badge-gray' ?>"><?= $reg['status'] === 'berlaku' ? 'Berlaku' : 'Dicabut' ?></span>
        </div>
        <h1 style="font-size:clamp(22px,2.8vw,30px); margin-bottom:8px;"><?= e($reg['number']) ?></h1>
        <p style="font-size:16px; color:var(--text-700); font-weight:600; margin-bottom:26px;"><?= e($reg['title']) ?></p>

        <?php if ($reg['about']): ?>
        <h3 style="font-size:16px; margin-bottom:12px;">Tentang Regulasi Ini</h3>
        <div style="font-size:14.5px; color:var(--text-700); margin-bottom:24px;"><?= paragraphs($reg['about']) ?></div>
        <?php endif; ?>

        <?php if ($scopes): ?>
        <h3 style="font-size:16px; margin-bottom:14px;">Ruang Lingkup Utama</h3>
        <ul style="display:flex; flex-direction:column; gap:12px; margin-bottom:10px;">
          <?php foreach ($scopes as $sc): ?>
          <li style="display:flex; gap:10px; font-size:14px; color:var(--text-700);"><i class="bi bi-check-circle-fill" style="color:var(--success)"></i> <?= e($sc['content']) ?></li>
          <?php endforeach; ?>
        </ul>
        <?php endif; ?>
      </div>

      <div>
        <div class="card card-pad" style="margin-bottom:24px;">
          <h4 style="font-size:14.5px; margin-bottom:16px;">Detail Regulasi</h4>
          <div style="display:flex; flex-direction:column; gap:14px; font-size:13.5px;">
            <div class="flex" style="justify-content:space-between;"><span style="color:var(--text-500);">Nomor</span><b><?= e($reg['number']) ?></b></div>
            <div class="flex" style="justify-content:space-between;"><span style="color:var(--text-500);">Kategori</span><b><?= e($catLabel) ?></b></div>
            <div class="flex" style="justify-content:space-between;"><span style="color:var(--text-500);">Tanggal Ditetapkan</span><b><?= e(tanggal_id($reg['date_set'])) ?></b></div>
            <div class="flex" style="justify-content:space-between;"><span style="color:var(--text-500);">Tanggal Diundangkan</span><b><?= e(tanggal_id($reg['date_published'])) ?></b></div>
            <div class="flex" style="justify-content:space-between;"><span style="color:var(--text-500);">Status</span><b style="color:<?= $reg['status'] === 'berlaku' ? 'var(--success)' : 'var(--text-500)' ?>"><?= $reg['status'] === 'berlaku' ? 'Berlaku' : 'Dicabut' ?></b></div>
          </div>
          <a href="<?= e($reg['file'] ? asset_url($reg['file']) : '#') ?>" class="btn btn-primary btn-block" style="margin-top:22px;"<?= $reg['file'] ? ' download' : '' ?>><i class="bi bi-download"></i> Unduh Peraturan (PDF)</a>
        </div>
        <?php if ($related): ?>
        <div class="card card-pad">
          <h4 style="font-size:14.5px; margin-bottom:16px;">Regulasi Terkait</h4>
          <ul style="display:flex; flex-direction:column; gap:12px;">
            <?php foreach ($related as $rel): ?>
            <li><a href="<?= e(url('pages/regulasi-detail.php?slug=' . urlencode($rel['slug']))) ?>" style="color:var(--primary-dark); font-weight:600; font-size:13.5px; display:flex; align-items:center; gap:8px;"><i class="bi bi-journal-text"></i> <?= e($rel['number']) ?> — <?= e(excerpt($rel['title'], 45)) ?></a></li>
            <?php endforeach; ?>
          </ul>
        </div>
        <?php endif; ?>
      </div>

    </div>
    <div style="margin-top:40px;">
      <a href="<?= e(url('pages/regulasi.php')) ?>" class="btn btn-outline-blue btn-sm"><i class="bi bi-arrow-left"></i> Kembali ke Daftar Regulasi</a>
    </div>
  </div>
</section>

<?php partial('footer'); ?>

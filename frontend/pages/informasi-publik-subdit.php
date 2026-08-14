<?php
/* Informasi publik per subdirektorat — dasbor data dipisah mengikuti unit kerjanya. */
require_once dirname(__DIR__) . '/bootstrap.php';

/* Dasbor mana milik unit mana. */
const SUBDIT_DASBOR = [
    'std'       => ['ip-register-bandara'],
    'tatanan'   => ['ip-emisi'],
    'prasarana' => ['ip-fasilitas-sisi-udara'],
    'darurat'   => ['ip-peralatan-darurat'],
    'sistem'    => ['ip-penyelenggaraan'],
];

$unitKey = trim($_GET['unit'] ?? '');
$unit    = $unitKey !== ''
    ? db_one('SELECT * FROM org_units WHERE unit_key = ? AND is_active = 1', [$unitKey])
    : null;

if (!$unit || !isset(SUBDIT_DASBOR[$unitKey])) {
    http_response_code(404);
    page_start('informasi-publik', [
        'title'       => 'Data subdirektorat tidak ditemukan',
        'breadcrumbs' => [['label' => 'Informasi Publik', 'url' => 'pages/informasi-publik'], ['label' => 'Tidak ditemukan']],
    ]);
    partial('header');
    echo '<section class="section"><div class="container" style="text-align:center;padding:60px 0;">'
       . '<h1 style="font-size:24px;margin-bottom:12px;">Data subdirektorat tidak ditemukan</h1>'
       . '<a href="' . e(url('pages/informasi-publik')) . '" class="btn btn-primary btn-sm">Kembali ke Informasi Publik</a>'
       . '</div></section>';
    partial('footer');
    exit;
}

$meta = page_start('informasi-publik', [
    'title'       => $unit['title'] . ' — Informasi Publik — ' . setting('site_name'),
    'description' => excerpt($unit['description'], 200),
    'breadcrumbs' => [['label' => 'Informasi Publik', 'url' => 'pages/informasi-publik'], ['label' => $unit['title']]],
]);

$dasbor   = SUBDIT_DASBOR[$unitKey];
$services = db_all('SELECT * FROM services WHERE is_active = 1 AND org_unit_id = ? ORDER BY sort, id', [$unit['id']]);

partial('header');
?>

<div class="page-title-block">
  <div class="container">
    <div class="eyebrow">Informasi Publik</div>
    <h1><?= e($unit['title']) ?></h1>
    <?php if ($unit['description']): ?><p><?= e($unit['description']) ?></p><?php endif; ?>
  </div>
</div>

<?php foreach ($dasbor as $partial): ?>
<?php partial($partial); ?>
<?php endforeach; ?>

<?php if (!$dasbor): ?>
<section class="section">
  <div class="container">
    <div class="info-box">
      <h3><i class="bi bi-info-circle"></i>&nbsp; Belum ada data tayang</h3>
      <p>Data informasi publik untuk <?= e($unit['title']) ?> belum tersedia. Isi dapat ditambahkan melalui panel CMS.</p>
    </div>
  </div>
</section>
<?php endif; ?>

<?php if ($services): ?>
<section class="section section-alt">
  <div class="container">
    <div class="section-head center">
      <div class="eyebrow" style="justify-content:center">Layanan</div>
      <h2>Layanan <?= e($unit['title']) ?></h2>
    </div>
    <div class="grid grid-3">
      <?php foreach ($services as $svc): ?>
      <div class="card service-card">
        <div class="ic"><i class="bi <?= e($svc['icon'] ?: 'bi-headset') ?>"></i></div>
        <h3><?= e($svc['name']) ?></h3>
        <?php if ($svc['description']): ?><p><?= e(excerpt($svc['description'], 120)) ?></p><?php endif; ?>
        <a href="<?= e(url('pages/layanan-detail?slug=' . urlencode($svc['slug']))) ?>" class="more">Selengkapnya <i class="bi bi-arrow-right"></i></a>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php partial('footer'); ?>

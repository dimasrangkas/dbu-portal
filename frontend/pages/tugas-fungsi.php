<?php
require_once dirname(__DIR__) . '/bootstrap.php';
$meta = page_start('tugas-fungsi', ['breadcrumbs' => [['label' => 'Tugas & Fungsi']]]);

$overview = db_one('SELECT * FROM tf_overview ORDER BY id LIMIT 1') ?: [];
$funcs    = db_all('SELECT * FROM tf_functions WHERE is_active = 1 ORDER BY sort, id');
$s        = sections('tugas-fungsi');

partial('header');
partial('page-title', ['meta' => $meta]);
?>

<!-- ===================== TUGAS ===================== -->
<?php if (!empty($overview['tugas'])): ?>
<section class="section" id="tugas">
  <div class="container" style="max-width:900px;">
    <div class="section-head center">
      <div class="eyebrow" style="justify-content:center"><?= e($overview['tugas_eyebrow'] ?: ($s['tugas']['eyebrow'] ?? 'Tugas')) ?></div>
      <h2><?= e($overview['tugas_title'] ?: ($s['tugas']['title'] ?? 'Tugas Direktorat Bandar Udara')) ?></h2>
    </div>
    <div class="info-box">
      <h3><i class="bi bi-bullseye"></i>&nbsp; Tugas</h3>
      <?= paragraphs($overview['tugas']) ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ===================== FUNGSI ===================== -->
<?php if ($funcs): ?>
<section class="section section-alt" id="fungsi">
  <div class="container" style="max-width:960px;">
    <div class="section-head center">
      <div class="eyebrow" style="justify-content:center"><?= e(($overview['fungsi_eyebrow'] ?? '') ?: ($s['fungsi']['eyebrow'] ?? 'Fungsi')) ?></div>
      <h2><?= e(($overview['fungsi_title'] ?? '') ?: ($s['fungsi']['title'] ?? 'Fungsi Direktorat Bandar Udara')) ?></h2>
      <?php if (!empty($overview['fungsi_intro'])): ?><p><?= e($overview['fungsi_intro']) ?></p><?php endif; ?>
    </div>
    <ol class="fungsi-list">
      <?php foreach ($funcs as $i => $f): ?>
      <li class="fungsi-item">
        <span class="fungsi-num"><?= $i + 1 ?></span>
        <div class="fungsi-body">
          <?php if ($f['icon']): ?><i class="bi <?= e($f['icon']) ?>"></i><?php endif; ?>
          <p><?= e($f['content']) ?></p>
        </div>
      </li>
      <?php endforeach; ?>
    </ol>
  </div>
</section>
<?php endif; ?>

<?php partial('footer'); ?>

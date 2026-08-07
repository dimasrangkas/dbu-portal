<?php
require_once dirname(__DIR__) . '/bootstrap.php';
$meta = page_start('tugas-fungsi', ['breadcrumbs' => [['label' => 'Tugas & Fungsi']]]);

$units = db_all('SELECT * FROM tf_units WHERE is_active = 1 ORDER BY sort, id');
$byUnit = [];
foreach (db_all('SELECT * FROM tf_details ORDER BY sort, id') as $d) {
    $byUnit[$d['unit_id']][] = $d;
}

partial('header');
partial('page-title', ['meta' => $meta]);
?>

<section class="section">
  <div class="container" style="max-width:960px;">
    <?php foreach ($units as $i => $unit): $details = $byUnit[$unit['id']] ?? []; ?>
    <div class="accordion-item<?= $i === 0 ? ' open' : '' ?>">
      <button class="accordion-head"><span><span class="num"><?= e($unit['number']) ?></span><?= e($unit['title']) ?></span><i class="bi bi-chevron-down"></i></button>
      <div class="accordion-body"><div class="accordion-body-in">
        <?php if ($unit['intro']): ?><p><?= e($unit['intro']) ?></p><?php endif; ?>
        <?php if ($details): ?>
        <div class="sub-grid">
          <?php foreach ($details as $d): ?>
          <div class="sub"><b><i class="bi <?= e($d['icon']) ?>"></i> <?= e($d['label']) ?></b><?= e($d['content']) ?></div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div></div>
    </div>
    <?php endforeach; ?>
  </div>
</section>

<?php partial('footer'); ?>

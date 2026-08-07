<?php
require_once dirname(__DIR__) . '/bootstrap.php';
$meta = page_start('organisasi', ['breadcrumbs' => [['label' => 'Organisasi']]]);

$units = db_all('SELECT * FROM org_units WHERE is_active = 1 ORDER BY sort, id');
$s     = sections('organisasi');

partial('header');
partial('page-title', ['meta' => $meta]);
?>

<section class="section">
  <div class="container">
    <?php if (!empty($s['intro']['subtitle'])): ?>
    <p class="text-center" style="color:var(--text-500); font-size:13.5px; margin-bottom:36px;">
      <i class="bi bi-cursor"></i>&nbsp; <?= e($s['intro']['subtitle']) ?>
    </p>
    <?php endif; ?>

    <div class="org-wrap">
      <div class="org-node top"><b><?= e($s['top']['title'] ?? 'Direktur Bandar Udara') ?></b><span><?= e($s['top']['subtitle'] ?? '') ?></span></div>
      <div class="org-connector"></div>
      <div class="org-children">
        <?php foreach ($units as $unit): ?>
        <div class="<?= e($unit['branch_class']) ?>">
          <div class="org-unit" data-unit="<?= e($unit['unit_key']) ?>">
            <div class="ic"><i class="bi <?= e($unit['icon']) ?>"></i></div>
            <b><?= e($unit['title']) ?></b>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

    <?php foreach ($units as $unit): $chips = split_list($unit['chips']); ?>
    <div class="org-detail" data-detail="<?= e($unit['unit_key']) ?>">
      <h3><i class="bi <?= e($unit['icon']) ?>"></i>&nbsp; <?= e($unit['title']) ?></h3>
      <p><?= e($unit['description']) ?></p>
      <?php if ($chips): ?>
      <div class="grid grid-3">
        <?php foreach ($chips as $chip): ?>
        <div class="chip"><i class="bi bi-check2"></i> <?= e($chip) ?></div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
    <?php endforeach; ?>
  </div>
</section>

<?php partial('footer'); ?>

<?php
require_once dirname(__DIR__) . '/bootstrap.php';
$meta = page_start('organisasi', ['breadcrumbs' => [['label' => 'Organisasi']]]);

$units = db_all('SELECT * FROM org_units WHERE is_active = 1 ORDER BY sort, id');
$unitFungsi = [];
foreach (db_all('SELECT * FROM org_unit_functions ORDER BY sort, id') as $fn) { $unitFungsi[$fn['unit_id']][] = $fn; }
$unitLayanan = [];
foreach (db_all('SELECT * FROM services WHERE is_active = 1 AND org_unit_id IS NOT NULL ORDER BY sort, id') as $sv) { $unitLayanan[$sv['org_unit_id']][] = $sv; }
$s     = sections('organisasi');

/* Dua pejabat per subdirektorat: kepala subdirektorat dan kepala tim. */
function unit_pejabat(array $unit): array {
    if ($unit['unit_key'] === 'tu') return [];
    return [
        ['name' => $unit['head_name'],       'position' => $unit['head_position']       ?: 'Kepala Subdirektorat', 'photo' => $unit['head_photo']],
        ['name' => $unit['team_lead_name'],  'position' => $unit['team_lead_position']  ?: 'Kepala Tim',           'photo' => $unit['team_lead_photo']],
    ];
}

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
        <?php foreach ($units as $unit):
            /* Kartu bagan cukup menampilkan kepala subdirektorat;
               kepala tim baru muncul pada panel detail saat kartu diklik. */
            $kepala = unit_pejabat($unit)[0] ?? null; ?>
        <div class="<?= e($unit['branch_class']) ?>">
          <div class="org-unit" data-unit="<?= e($unit['unit_key']) ?>">
            <?php if ($kepala): ?>
            <div class="org-photo" title="<?= e($kepala['position']) ?><?= $kepala['name'] ? ' — ' . $kepala['name'] : '' ?>">
              <?php if ($kepala['photo']): ?><img src="<?= e(asset_url($kepala['photo'])) ?>" alt="<?= e($kepala['name'] ?: $kepala['position']) ?>">
              <?php else: ?><span><i class="bi bi-person-fill"></i></span><?php endif; ?>
            </div>
            <?php else: ?>
            <div class="ic"><i class="bi <?= e($unit['icon']) ?>"></i></div>
            <?php endif; ?>
            <b><?= e($unit['title']) ?></b>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

    <?php foreach ($units as $unit):
        $chips   = split_list($unit['chips']);
        $fungsi  = $unitFungsi[$unit['id']]  ?? [];
        $layanan = $unitLayanan[$unit['id']] ?? [];
        $pejabat = unit_pejabat($unit); ?>
    <div class="org-detail" data-detail="<?= e($unit['unit_key']) ?>">
      <div class="org-detail-head">
        <h3><i class="bi <?= e($unit['icon']) ?>"></i>&nbsp; <?= e($unit['title']) ?></h3>
      </div>

      <?php if ($pejabat): ?>
      <div class="org-pejabat">
        <?php foreach ($pejabat as $p): ?>
        <div class="org-person">
          <div class="org-detail-photo">
            <?php if ($p['photo']): ?>
            <img src="<?= e(asset_url($p['photo'])) ?>" alt="<?= e($p['name'] ?: $p['position']) ?>">
            <?php else: ?>
            <span><i class="bi bi-person-fill"></i></span>
            <?php endif; ?>
          </div>
          <div class="org-person-text">
            <?php if ($p['name']): ?>
            <b><?= e($p['name']) ?></b>
            <?php else: ?>
            <b class="is-empty">Nama belum diisi</b>
            <?php endif; ?>
            <span><?= e($p['position']) ?></span>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <?php if ($unit['description']): ?><p><?= e($unit['description']) ?></p><?php endif; ?>

      <?php if ($unit['tugas']): ?>
      <h4 class="org-sub"><i class="bi bi-bullseye"></i> Tugas</h4>
      <div class="org-tugas"><?= paragraphs($unit['tugas']) ?></div>
      <?php endif; ?>

      <?php if ($fungsi): ?>
      <h4 class="org-sub"><i class="bi bi-list-check"></i> Fungsi</h4>
      <ol class="org-fungsi">
        <?php foreach ($fungsi as $fn): ?><li><?= e($fn['content']) ?></li><?php endforeach; ?>
      </ol>
      <?php endif; ?>

      <?php if ($layanan): ?>
      <h4 class="org-sub"><i class="bi bi-headset"></i> Layanan (<?= count($layanan) ?>)</h4>
      <div class="org-layanan">
        <?php foreach ($layanan as $svc): ?>
        <a class="org-layanan-item" href="<?= e(url('pages/layanan-detail?slug=' . urlencode($svc['slug']))) ?>">
          <i class="bi <?= e($svc['icon'] ?: 'bi-dot') ?>"></i><span><?= e($svc['name']) ?></span>
        </a>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <?php if ($chips): ?>
      <div class="grid grid-3" style="margin-top:18px;">
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

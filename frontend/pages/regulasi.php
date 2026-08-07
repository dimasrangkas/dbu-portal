<?php
require_once dirname(__DIR__) . '/bootstrap.php';
$meta = page_start('regulasi', ['breadcrumbs' => [['label' => 'Regulasi']]]);

$regs  = db_all('SELECT * FROM regulations WHERE is_active = 1 ORDER BY year DESC, sort, id');
$cats  = db_all('SELECT * FROM regulation_categories WHERE is_active = 1 ORDER BY sort, id');
$catMap = [];
foreach ($cats as $c) {
    $catMap[$c['slug']] = $c['label'];
}
$years = array_values(array_unique(array_map(fn($r) => $r['year'], $regs)));
rsort($years);

partial('header');
partial('page-title', ['meta' => $meta]);
?>

<section class="section">
  <div class="container">

    <div class="filter-bar">
      <div class="grow">
        <i class="bi bi-search"></i>
        <input type="text" placeholder="Cari nomor atau judul regulasi..." data-reg-search>
      </div>
      <select data-reg-year>
        <option value="all">Semua Tahun</option>
        <?php foreach ($years as $y): ?><option value="<?= (int) $y ?>"><?= (int) $y ?></option><?php endforeach; ?>
      </select>
      <select data-reg-cat>
        <option value="all">Semua Kategori</option>
        <?php foreach ($cats as $c): ?><option value="<?= e($c['slug']) ?>"><?= e($c['label']) ?></option><?php endforeach; ?>
      </select>
    </div>

    <div class="table-wrap">
      <table class="reg-table" data-reg-table>
        <thead>
          <tr><th>Nomor</th><th>Judul Regulasi</th><th>Tahun</th><th>Kategori</th><th>Status</th><th>Aksi</th></tr>
        </thead>
        <tbody>
          <?php foreach ($regs as $r):
              $detail = url('pages/regulasi-detail.php?slug=' . urlencode($r['slug'])); ?>
          <tr data-year="<?= (int) $r['year'] ?>" data-cat="<?= e($r['category']) ?>">
            <td><?= e($r['number']) ?></td>
            <td><?= e($r['title']) ?></td>
            <td><?= (int) $r['year'] ?></td>
            <td><span class="badge badge-gray"><?= e($catMap[$r['category']] ?? $r['category']) ?></span></td>
            <td><span class="badge <?= $r['status'] === 'berlaku' ? 'badge-success' : 'badge-gray' ?>"><?= $r['status'] === 'berlaku' ? 'Berlaku' : 'Dicabut' ?></span></td>
            <td>
              <a href="<?= e($detail) ?>" class="dl-link">Detail</a>
              <?php if ($r['file']): ?> &nbsp;<a href="<?= e(asset_url($r['file'])) ?>" class="dl-link" download><i class="bi bi-download"></i></a><?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <p data-reg-empty style="display:none; text-align:center; padding:40px; color:var(--text-500);">Tidak ada regulasi yang sesuai dengan pencarian/filter Anda.</p>
    </div>

  </div>
</section>

<?php partial('footer'); ?>

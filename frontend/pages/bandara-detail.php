<?php
/* Detail satu bandar udara. Sumber data: tabel `bandaras`. */
require_once dirname(__DIR__) . '/bootstrap.php';

$id      = (int) ($_GET['id'] ?? 0);
$bandara = $id ? db_one('SELECT * FROM bandaras WHERE id = ?', [$id]) : null;

if (!$bandara) {
    http_response_code(404);
    page_start('informasi-publik', [
        'title'       => 'Bandar udara tidak ditemukan',
        'breadcrumbs' => [['label' => 'Daftar Bandar Udara', 'url' => 'pages/informasi-publik'], ['label' => 'Tidak ditemukan']],
    ]);
    partial('header');
    echo '<section class="section"><div class="container" style="text-align:center;padding:60px 0;">'
       . '<h1 style="font-size:24px;margin-bottom:12px;">Bandar udara tidak ditemukan</h1>'
       . '<a href="' . e(url('pages/informasi-publik')) . '" class="btn btn-primary btn-sm">Kembali ke Daftar Bandar Udara</a>'
       . '</div></section>';
    partial('footer');
    exit;
}

$lokasi = array_filter([
    $bandara['kelurahan_desa'] ? 'Kel./Desa ' . $bandara['kelurahan_desa'] : '',
    $bandara['kecamatan']      ? 'Kec. ' . $bandara['kecamatan'] : '',
    $bandara['kabupaten_kota'],
    $bandara['provinsi'],
], 'strlen');

page_start('informasi-publik', [
    'title'       => $bandara['nama'] . ' — Daftar Bandar Udara — ' . setting('site_name'),
    'description' => 'Data Bandar Udara ' . $bandara['nama'] . ($bandara['kode_icao'] ? ' (' . $bandara['kode_icao'] . ')' : '')
                     . ($lokasi ? ' di ' . implode(', ', array_slice($lokasi, -2)) : '') . '.',
    'breadcrumbs' => [['label' => 'Daftar Bandar Udara', 'url' => 'pages/informasi-publik'], ['label' => $bandara['nama']]],
]);
partial('header');
?>

<div class="page-title-block">
  <div class="container">
    <div class="eyebrow">Bandar Udara</div>
    <h1><?= e($bandara['nama']) ?></h1>
    <p>
      <?php if ($bandara['kode_icao']): ?>ICAO <b><?= e($bandara['kode_icao']) ?></b><?php endif; ?>
      <?php if ($bandara['kode_iata']): ?> &middot; IATA <b><?= e($bandara['kode_iata']) ?></b><?php endif; ?>
      <?php if ($lokasi): ?> &middot; <?= e(implode(', ', array_slice($lokasi, -2))) ?><?php endif; ?>
    </p>
  </div>
</div>

<section class="section">
  <div class="container">

    <div class="grid grid-4" style="margin-bottom:28px;">
      <div class="card icon-card card-pad"><div class="ic"><i class="bi bi-globe2"></i></div><h3><?= e($bandara['tipe'] ?: '—') ?></h3><p>Penggunaan</p></div>
      <div class="card icon-card card-pad"><div class="ic"><i class="bi bi-award"></i></div><h3><?= e($bandara['kelas'] ?: '—') ?></h3><p>Kelas</p></div>
      <div class="card icon-card card-pad"><div class="ic"><i class="bi bi-people-fill"></i></div><h3><?= number_format((int) $bandara['jumlah_penumpang'], 0, ',', '.') ?></h3><p>Jumlah Penumpang</p></div>
      <div class="card icon-card card-pad"><div class="ic"><i class="bi bi-airplane-engines-fill"></i></div><h3><?= number_format((int) $bandara['jumlah_pesawat'], 0, ',', '.') ?></h3><p>Pergerakan Pesawat</p></div>
    </div>

    <div class="grid grid-2" style="align-items:start; gap:24px;">

      <div class="card card-pad">
        <h3 class="org-sub" style="margin-top:0;"><i class="bi bi-geo-alt"></i> Lokasi</h3>
        <table class="reg-table detail-table">
          <tbody>
            <tr><td>Alamat</td><td><?= e($bandara['alamat'] ?: '—') ?></td></tr>
            <tr><td>Kelurahan / Desa</td><td><?= e($bandara['kelurahan_desa'] ?: '—') ?></td></tr>
            <tr><td>Kecamatan</td><td><?= e($bandara['kecamatan'] ?: '—') ?></td></tr>
            <tr><td>Kabupaten / Kota</td><td><?= e($bandara['kabupaten_kota'] ?: '—') ?></td></tr>
            <tr><td>Provinsi</td><td><?= e($bandara['provinsi'] ?: '—') ?></td></tr>
            <tr><td>Lokasi ARP</td><td><?= e($bandara['lokasi_arp'] ?: '—') ?></td></tr>
          </tbody>
        </table>
      </div>

      <div class="card card-pad">
        <h3 class="org-sub" style="margin-top:0;"><i class="bi bi-building"></i> Pengelolaan</h3>
        <table class="reg-table detail-table">
          <tbody>
            <tr><td>Pengelola</td><td><?= e($bandara['pengelola'] ?: '—') ?></td></tr>
            <tr><td>Kantor Otoritas</td><td><?= e($bandara['kantor_otoritas'] ?: '—') ?></td></tr>
            <tr><td>Kelas</td><td><?= e($bandara['kelas'] ?: '—') ?></td></tr>
            <tr><td>Penggunaan</td><td><?= e($bandara['tipe'] ?: '—') ?></td></tr>
            <tr><td>Jumlah Kargo</td><td><?= number_format((int) $bandara['jumlah_kargo'], 0, ',', '.') ?></td></tr>
          </tbody>
        </table>
      </div>

      <div class="card card-pad">
        <h3 class="org-sub" style="margin-top:0;"><i class="bi bi-telephone"></i> Kontak</h3>
        <table class="reg-table detail-table">
          <tbody>
            <tr><td>Telepon</td><td><?= e($bandara['telepon'] ?: '—') ?></td></tr>
            <tr><td>Faksimile</td><td><?= e($bandara['fax'] ?: '—') ?></td></tr>
            <tr><td>Surel</td><td><?= $bandara['email'] ? '<a href="mailto:' . e($bandara['email']) . '">' . e($bandara['email']) . '</a>' : '—' ?></td></tr>
            <tr><td>Situs Web</td><td><?= $bandara['website'] ? '<a href="' . eurl($bandara['website']) . '" target="_blank" rel="noopener">' . e($bandara['website']) . '</a>' : '—' ?></td></tr>
          </tbody>
        </table>
      </div>

      <div class="card card-pad">
        <h3 class="org-sub" style="margin-top:0;"><i class="bi bi-tags"></i> Identitas</h3>
        <table class="reg-table detail-table">
          <tbody>
            <tr><td>Nama</td><td><?= e($bandara['nama']) ?></td></tr>
            <tr><td>Kode ICAO</td><td><?= e($bandara['kode_icao'] ?: '—') ?></td></tr>
            <tr><td>Kode IATA</td><td><?= e($bandara['kode_iata'] ?: '—') ?></td></tr>
            <tr><td>Koordinat ARP</td><td><?= e($bandara['lokasi_arp'] ?: '—') ?></td></tr>
            <?php if ($bandara['latitude'] || $bandara['longitude']): ?>
            <tr><td>Lintang / Bujur</td><td><?= e(trim($bandara['latitude'] . ' / ' . $bandara['longitude'], ' /')) ?></td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

    </div>

    <div style="margin-top:28px;">
      <a href="<?= e(url('pages/informasi-publik')) ?>" class="btn btn-outline btn-sm"><i class="bi bi-arrow-left"></i> Kembali ke Daftar Bandar Udara</a>
    </div>

  </div>
</section>

<?php partial('footer'); ?>

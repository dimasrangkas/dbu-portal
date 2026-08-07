<?php
require_once __DIR__ . '/bootstrap.php';
$meta = page_start('home');

$slides    = db_all('SELECT * FROM hero_slides WHERE is_active = 1 ORDER BY sort, id');
$stats     = db_all('SELECT * FROM stats WHERE is_active = 1 ORDER BY sort, id');
$director  = db_one('SELECT * FROM director_message ORDER BY id LIMIT 1') ?: [];
$aboutSec  = section('home', 'about');
$aboutCard = db_all('SELECT * FROM about_cards WHERE is_active = 1 ORDER BY sort, id');
$petaSec   = section('home', 'peta');
$regions   = db_all('SELECT * FROM regions WHERE is_active = 1 ORDER BY sort, id');
$quickSec  = section('home', 'quick');
$quick     = db_all('SELECT * FROM quick_links WHERE is_active = 1 ORDER BY sort, id');
$newsSec   = section('home', 'news');
$news      = db_all('SELECT * FROM news WHERE is_published = 1 ORDER BY published_at DESC, id DESC LIMIT 3');
$announce  = db_all('SELECT * FROM announcements WHERE is_active = 1 ORDER BY published_at DESC, sort LIMIT 4');
$svcSec    = section('home', 'services');
$services  = db_all('SELECT * FROM featured_services WHERE is_active = 1 ORDER BY sort, id');
$galSec    = section('home', 'gallery');
$photos    = db_all('SELECT * FROM gallery_photos WHERE is_active = 1 AND show_on_home = 1 ORDER BY sort, id LIMIT 8');
$video     = db_one('SELECT * FROM home_video ORDER BY id LIMIT 1') ?: [];
$partSec   = section('home', 'partners');
$partners  = db_all('SELECT * FROM partners WHERE is_active = 1 ORDER BY sort, id');
$newsltr   = section('home', 'newsletter');

partial('header');
?>

<!-- ===================== HERO CAROUSEL ===================== -->
<section class="hero" aria-label="Sorotan utama">
  <?php foreach ($slides as $i => $slide): ?>
  <div class="hero-slide<?= $i === 0 ? ' active' : '' ?>">
    <?= art_block($slide['image'], $slide['art_class'], $slide['icon'], '', $slide['title']) ?>
    <div class="scrim"></div>
    <?php if ($i === 0): ?>
    <svg class="flightpath" viewBox="0 0 800 500" preserveAspectRatio="none"><path d="M-20,420 C 200,380 350,460 800,300" stroke="#8FC4F2" stroke-width="2" fill="none"/></svg>
    <?php endif; ?>
    <div class="container">
      <div class="hero-content">
        <?php if ($slide['eyebrow']): ?><div class="eyebrow"><?= e($slide['eyebrow']) ?></div><?php endif; ?>
        <h1><?= e($slide['title']) ?></h1>
        <?php if ($slide['tagline']): ?><p class="tag"><?= e($slide['tagline']) ?></p><?php endif; ?>
        <div class="hero-actions">
          <?php if ($slide['btn1_label']): ?>
          <a href="<?= e(url($slide['btn1_url'])) ?>" class="btn btn-white"><?= e($slide['btn1_label']) ?></a>
          <?php endif; ?>
          <?php if ($slide['btn2_label']): ?>
          <a href="<?= e(url($slide['btn2_url'])) ?>" class="btn btn-outline"><?= e($slide['btn2_label']) ?> <i class="bi bi-arrow-right"></i></a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
  <button class="hero-arrow prev" aria-label="Sebelumnya"><i class="bi bi-chevron-left"></i></button>
  <button class="hero-arrow next" aria-label="Berikutnya"><i class="bi bi-chevron-right"></i></button>
  <div class="hero-dots"></div>
</section>

<!-- ===================== STATS BAND ===================== -->
<?php if ($stats): ?>
<div class="container" style="margin-top:-64px; position:relative; z-index:10;">
  <div class="stats-band">
    <div class="grid grid-<?= min(count($stats), 5) ?>">
      <?php foreach ($stats as $stat): ?>
      <div class="stat">
        <div class="num"><span data-count="<?= (int) $stat['value'] ?>"<?= $stat['suffix'] ? ' data-suffix="' . e($stat['suffix']) . '"' : '' ?>>0<?= e($stat['suffix']) ?></span></div>
        <div class="lbl"><?= e($stat['label']) ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>
<?php endif; ?>

<!-- ===================== SAMBUTAN DIREKTUR ===================== -->
<?php if ($director): ?>
<section class="section">
  <div class="container">
    <div class="welcome-grid">
      <div class="director-photo">
        <?= art_block($director['image'], $director['art_class'], $director['icon'], '', $director['director_name'] ?? '') ?>
        <?php if ($director['director_name']): ?>
        <div class="frame-tag"><b><?= e($director['director_name']) ?></b><span><?= e($director['director_position']) ?></span></div>
        <?php endif; ?>
      </div>
      <div>
        <?php if ($director['eyebrow']): ?><div class="eyebrow"><?= e($director['eyebrow']) ?></div><?php endif; ?>
        <h2 style="font-size:clamp(22px,2.8vw,30px); margin-bottom:18px;"><?= e($director['title']) ?></h2>
        <span class="quote-mark">&ldquo;</span>
        <div style="font-size:15.5px; color:var(--text-700); margin-top:-18px;">
          <?= paragraphs($director['body']) ?>
        </div>
        <?php if ($director['btn_label']): ?>
        <div style="margin-top:24px; display:flex; gap:14px; flex-wrap:wrap;">
          <a href="<?= e(url($director['btn_url'])) ?>" class="btn btn-primary btn-sm"><?= e($director['btn_label']) ?></a>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ===================== TENTANG DIREKTORAT ===================== -->
<section class="section section-alt">
  <div class="container">
    <div class="section-head center">
      <?php if ($aboutSec['eyebrow']): ?><div class="eyebrow" style="justify-content:center"><?= e($aboutSec['eyebrow']) ?></div><?php endif; ?>
      <h2><?= e($aboutSec['title']) ?></h2>
      <?php if ($aboutSec['subtitle']): ?><p><?= e($aboutSec['subtitle']) ?></p><?php endif; ?>
    </div>
    <div class="grid grid-<?= min(max(count($aboutCard), 1), 5) ?>">
      <?php foreach ($aboutCard as $card): ?>
      <div class="card icon-card card-pad">
        <div class="ic"><i class="bi <?= e($card['icon']) ?>"></i></div>
        <h3><?= e($card['title']) ?></h3>
        <p><?= e($card['description']) ?></p>
        <?php if ($card['url']): ?><a href="<?= e(url($card['url'])) ?>" class="more">Lihat detail <i class="bi bi-arrow-right"></i></a><?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ===================== DASBOR DATA (di luar cakupan CMS) ===================== -->
<?php partial('dashboard-carousel'); ?>

<!-- ===================== PETA WILAYAH ===================== -->
<section class="section" id="peta-wilayah">
  <div class="container">
    <div class="section-head center">
      <?php if ($petaSec['eyebrow']): ?><div class="eyebrow" style="justify-content:center"><?= e($petaSec['eyebrow']) ?></div><?php endif; ?>
      <h2><?= e($petaSec['title']) ?></h2>
      <?php if ($petaSec['subtitle']): ?><p><?= e($petaSec['subtitle']) ?></p><?php endif; ?>
    </div>
    <?php partial('peta-wilayah', ['regions' => $regions]); ?>
  </div>
</section>

<!-- ===================== AKSES CEPAT ===================== -->
<section class="section">
  <div class="container">
    <div class="section-head center">
      <?php if ($quickSec['eyebrow']): ?><div class="eyebrow" style="justify-content:center"><?= e($quickSec['eyebrow']) ?></div><?php endif; ?>
      <h2><?= e($quickSec['title']) ?></h2>
    </div>
    <div class="grid grid-4">
      <?php foreach ($quick as $link): ?>
      <a href="<?= e(url($link['url'])) ?>" class="card quick-card"><div class="ic"><i class="bi <?= e($link['icon']) ?>"></i></div><span><?= e($link['label']) ?></span></a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ===================== BERITA & PENGUMUMAN ===================== -->
<section class="section section-alt">
  <div class="container">
    <div class="section-head-row">
      <div class="section-head">
        <?php if ($newsSec['eyebrow']): ?><div class="eyebrow"><?= e($newsSec['eyebrow']) ?></div><?php endif; ?>
        <h2><?= e($newsSec['title']) ?></h2>
      </div>
      <a href="<?= e(url('pages/berita.php')) ?>" class="btn btn-outline-blue btn-sm">Lihat Semua Berita <i class="bi bi-arrow-right"></i></a>
    </div>
    <div class="grid" style="grid-template-columns:2fr 1fr; align-items:start;">
      <div class="grid grid-3">
        <?php foreach ($news as $item):
            $detail = url('pages/berita-detail.php?slug=' . urlencode($item['slug'])); ?>
        <div class="card news-card">
          <div class="thumb"><?= art_block($item['image'], $item['art_class'], $item['icon'], '', $item['title']) ?><span class="cat"><?= e(db_value('SELECT label FROM news_categories WHERE slug = ?', [$item['category']], $item['category'])) ?></span></div>
          <div class="body">
            <div class="date"><i class="bi bi-calendar3"></i> <?= e(tanggal_id($item['published_at'])) ?></div>
            <h3><?= e($item['title']) ?></h3>
            <p><?= e($item['excerpt']) ?></p>
            <a href="<?= e($detail) ?>" class="more">Baca selengkapnya <i class="bi bi-arrow-right"></i></a>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <div class="card card-pad">
        <h3 style="font-size:16px; margin-bottom:6px;">Pengumuman</h3>
        <div class="announce-list">
          <?php foreach ($announce as $a): ?>
          <a class="announce-item" href="<?= e(url($a['url'] ?: '#')) ?>">
            <div class="announce-date"><div class="d"><?= e(date('d', strtotime($a['published_at']))) ?></div><div class="m"><?= e(bulan_singkat($a['published_at'])) ?></div></div>
            <div class="announce-info"><h4><?= e($a['title']) ?></h4><span><?= e($a['category']) ?></span></div>
          </a>
          <?php endforeach; ?>
        </div>
        <a href="<?= e(url('pages/berita.php?cat=pengumuman')) ?>" class="btn btn-outline-blue btn-sm btn-block" style="margin-top:18px;">Semua Pengumuman</a>
      </div>
    </div>
  </div>
</section>

<!-- ===================== LAYANAN UNGGULAN ===================== -->
<section class="section">
  <div class="container">
    <div class="section-head center">
      <?php if ($svcSec['eyebrow']): ?><div class="eyebrow" style="justify-content:center"><?= e($svcSec['eyebrow']) ?></div><?php endif; ?>
      <h2><?= e($svcSec['title']) ?></h2>
    </div>
    <div class="grid grid-3">
      <?php foreach ($services as $svc): ?>
      <div class="card service-card">
        <div class="ic"><i class="bi <?= e($svc['icon']) ?>"></i></div>
        <h3><?= e($svc['title']) ?></h3>
        <p><?= e($svc['description']) ?></p>
        <?php if ($svc['url']): ?><a href="<?= e(url($svc['url'])) ?>" class="more">Ajukan Layanan <i class="bi bi-arrow-right"></i></a><?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ===================== GALERI ===================== -->
<section class="section section-sky">
  <div class="container">
    <div class="section-head-row">
      <div class="section-head">
        <?php if ($galSec['eyebrow']): ?><div class="eyebrow"><?= e($galSec['eyebrow']) ?></div><?php endif; ?>
        <h2><?= e($galSec['title']) ?></h2>
      </div>
      <a href="<?= e(url('pages/galeri.php')) ?>" class="btn btn-outline-blue btn-sm">Lihat Galeri Lengkap <i class="bi bi-arrow-right"></i></a>
    </div>
    <div class="gallery-grid">
      <?php foreach ($photos as $photo): ?>
      <div class="gallery-item" data-lightbox data-caption="<?= e($photo['caption'] ?: $photo['title']) ?>">
        <?= art_block($photo['image'], $photo['art_class'], $photo['icon'], '', $photo['title']) ?>
        <span class="zoom"><i class="bi bi-zoom-in"></i></span><span class="cap"><?= e($photo['title']) ?></span>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ===================== VIDEO PROFIL ===================== -->
<?php if ($video): ?>
<section class="section">
  <div class="container" style="max-width:920px;">
    <div class="section-head center">
      <?php if ($video['eyebrow']): ?><div class="eyebrow" style="justify-content:center"><?= e($video['eyebrow']) ?></div><?php endif; ?>
      <h2><?= e($video['title']) ?></h2>
    </div>
    <?php if ($video['video_url']): ?>
    <div class="video-frame">
      <iframe src="<?= e(embed_url($video['video_url'])) ?>" title="<?= e($video['title']) ?>" style="position:absolute;inset:0;width:100%;height:100%;border:0;" allowfullscreen loading="lazy"></iframe>
    </div>
    <?php else: ?>
    <div class="video-frame">
      <?= art_block($video['image'], $video['art_class'], $video['icon'], '', $video['title']) ?>
      <button class="play-btn" aria-label="Putar video"><i class="bi bi-play-fill"></i></button>
    </div>
    <?php endif; ?>
    <?php if ($video['caption']): ?>
    <p style="text-align:center; color:var(--text-500); font-size:13px; margin-top:14px;"><?= e($video['caption']) ?></p>
    <?php endif; ?>
  </div>
</section>
<?php endif; ?>

<!-- ===================== MITRA KERJA ===================== -->
<?php if ($partners): ?>
<section class="section section-alt" style="padding-top:56px; padding-bottom:56px;">
  <div class="container">
    <div class="section-head center" style="margin-bottom:32px;">
      <?php if ($partSec['eyebrow']): ?><div class="eyebrow" style="justify-content:center"><?= e($partSec['eyebrow']) ?></div><?php endif; ?>
      <h2 style="font-size:22px;"><?= e($partSec['title']) ?></h2>
    </div>
  </div>
  <div class="partner-strip">
    <div class="partner-track">
      <?php for ($pass = 0; $pass < 2; $pass++): foreach ($partners as $p): ?>
      <div class="partner-logo"><?php if ($p['logo']): ?><img src="<?= e(asset_url($p['logo'])) ?>" alt="<?= e($p['name']) ?>" style="max-height:34px;width:auto;"><?php else: ?><?= e($p['name']) ?><?php endif; ?></div>
      <?php endforeach; endfor; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ===================== NEWSLETTER ===================== -->
<section class="section" style="padding-top:0;">
  <div class="container">
    <div class="newsletter">
      <div>
        <h3><?= e($newsltr['title']) ?></h3>
        <p><?= e($newsltr['subtitle']) ?></p>
      </div>
      <form data-newsletter-form method="post" action="<?= e(url('submit.php')) ?>">
        <input type="hidden" name="form" value="newsletter">
        <input type="email" name="email" placeholder="Alamat email Anda" required>
        <button type="submit" class="btn btn-white">Berlangganan</button>
      </form>
    </div>
  </div>
</section>

<?php partial('footer'); ?>

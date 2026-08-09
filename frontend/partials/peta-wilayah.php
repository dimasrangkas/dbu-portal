<?php
/* Peta Otoritas Bandar Udara — bentuk peta dari indonesia-map.php, metadata wilayah dari basis data. */
$obuRegions = [];
foreach ($regions as $r) {
    $obuRegions[$r['region_code']] = [
        'attrs' => [
            'data-num'        => $r['numeral'],
            'data-title'      => $r['title'],
            'data-hq'         => $r['hq'],
            'data-coverage'   => $r['coverage'],
            'aria-label'      => 'Otoritas Bandar Udara ' . $r['title'],
        ],
    ];
}
?>
    <div class="map-wrap">
      <div class="map-toolbar">
        <div class="map-hint"><i class="bi bi-hand-index-thumb"></i> Arahkan kursor / ketuk wilayah pada peta</div>
        <div class="map-zoom-controls">
          <button type="button" class="icon-btn" data-map-zoom-out aria-label="Perkecil peta"><i class="bi bi-dash-lg"></i></button>
          <button type="button" class="icon-btn" data-map-zoom-reset aria-label="Atur ulang zoom"><i class="bi bi-arrow-counterclockwise"></i></button>
          <button type="button" class="icon-btn" data-map-zoom-in aria-label="Perbesar peta"><i class="bi bi-plus-lg"></i></button>
        </div>
      </div>

      <div class="map-stage" data-map-stage>
        <div class="map-zoom-inner" data-map-zoom-inner>
          <?php partial('indonesia-map', [
              'mapId'      => 'obu',
              'mapRegions' => $obuRegions,
              'mapMuted'   => false,
              'mapLabel'   => 'Peta wilayah kerja Otoritas Bandar Udara Indonesia',
          ]); ?>
        </div>

        <div class="map-tooltip" data-map-tooltip>
          <span class="map-tooltip-num" data-tt-num>I</span>
          <div class="map-tooltip-body">
            <h4 data-tt-title>Wilayah I</h4>
            <span class="map-tooltip-hq" data-tt-hq></span>
            <p data-tt-coverage></p>
          </div>
        </div>
      </div>

      <div class="map-legend">
        <?php foreach ($regions as $r): ?>
        <div class="map-legend-item" data-legend-region="<?= e($r['region_code']) ?>">
          <div class="map-legend-num"><?= e($r['numeral']) ?></div>
          <div><h4><?= e($r['title']) ?></h4><span><?= e($r['hq_short'] ?: $r['hq']) ?></span>
          <p><?= e($r['coverage_short'] ?: $r['coverage']) ?></p></div>
        </div>
        <?php endforeach; ?>
      </div>

      <p style="font-size:12px; color:var(--text-500); margin-top:14px;"><i class="bi bi-info-circle"></i> Peta bersifat skematik (bukan batas administratif presisi) dan disederhanakan untuk kebutuhan visualisasi wilayah kerja. Sumber pembagian wilayah: Wikipedia — Otoritas Bandar Udara.</p>
    </div>

<?php /** @var array $brokers */ ?>
<div class="broker-grid">
  <?php foreach ($brokers as $slug => $b): $stock = cars($slug); ?>
    <a class="broker-card" href="<?= e(site_url($slug)) ?>" style="--seller: <?= e($b['colors']['primary']) ?>">
      <div class="broker-cover" data-label="<?= e($b['short']) ?>"><?= $stock ? car_photo($stock[0], 0, 640) : '' ?></div>
      <div class="broker-body">
        <div class="seller-mark"><?= e(mb_substr($b['short'], 0, 1)) ?></div>
        <strong><?= e($b['name']) ?></strong>
        <span class="broker-meta"><?= icon('star', 12) ?> <?= e($b['rating']) ?> · <?= e(number_format($b['reviews'])) ?> reviews · <?= e($b['area']) ?></span>
        <span class="broker-meta"><?= count($stock) ?> models · <?= e(implode(', ', $b['makes'])) ?></span>
        <span class="broker-host"><?= e($slug . '.' . config('app')['base_domain']) ?> <?= icon('arrow', 14) ?></span>
      </div>
    </a>
  <?php endforeach; ?>
</div>

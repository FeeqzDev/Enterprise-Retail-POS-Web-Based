<?php
/** @var array $car  @var string $href  @var bool $showBroker */
$seller = config('brokers')[$car['broker']];
?>
<article class="car-card">
  <a class="car-card-media" href="<?= e($href) ?>" data-label="<?= e("{$car['make']} {$car['model']}") ?>">
    <?= car_photo($car, 0, 640) ?>
    <?php if ($car['promo']): ?><span class="badge badge-promo"><?= e($car['promo']) ?></span><?php endif; ?>
    <?php if (count($car['images']) > 1): ?><span class="badge badge-count"><?= count($car['images']) ?> photos</span><?php endif; ?>
  </a>
  <div class="car-card-body">
    <div class="car-card-meta"><?= e($car['year']) ?> · New · <?= e($car['body']) ?></div>
    <h3><a href="<?= e($href) ?>"><?= e("{$car['make']} {$car['model']}") ?> <span><?= e($car['variant']) ?></span></a></h3>
    <ul class="spec-chips">
      <li><?= icon('fuel', 14) ?> <?= e($car['fuel']) ?></li>
      <li><?= icon('gear', 14) ?> <?= e($car['transmission']) ?></li>
      <li><?= icon('engine', 14) ?> <?= e(number_format($car['engine'])) ?> cc</li>
    </ul>
    <div class="car-card-price">
      <strong><?= rm($car['price']) ?></strong>
      <span><?= rm(monthly_installment($car['price'])) ?>/mo</span>
    </div>
  </div>
  <?php if (!empty($showBroker)): ?>
    <div class="car-card-seller">
      <span><?= icon('shield', 14) ?> <?= e($seller['short']) ?></span>
      <span><?= icon('star', 12) ?> <?= e($seller['rating']) ?></span>
    </div>
  <?php endif; ?>
</article>

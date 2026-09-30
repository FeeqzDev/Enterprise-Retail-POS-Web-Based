<?php /** @var array $car  @var string $href */ ?>
<a class="car-card" href="<?= e($href) ?>">
  <div class="car-media"><?= car_svg($car) ?></div>
  <div class="car-body">
    <div class="car-make"><?= e($car['make']) ?> · <?= e($car['body']) ?></div>
    <h3><?= e($car['model']) ?> <span><?= e($car['variant']) ?></span></h3>
    <div class="car-price"><?= rm($car['price']) ?></div>
    <div class="car-monthly">from <?= rm(monthly_installment($car['price'])) ?>/mo</div>
    <?php if (!empty($showBroker)): ?>
      <div class="car-broker">Sold by <?= e(config('brokers')[$car['broker']]['name']) ?></div>
    <?php endif; ?>
  </div>
</a>

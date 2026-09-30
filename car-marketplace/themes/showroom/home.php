<?php
/** Toyota broker asked for a dark, premium "showroom" look with a spotlight car. */
/** @var array $site  @var array $cars */
$broker   = $site['broker'];
$featured = array_values(array_filter($cars, fn ($c) => $c['featured']))[0] ?? $cars[0];
?>
<section class="spotlight">
  <div class="wrap spotlight-grid">
    <div>
      <span class="eyebrow">Featured this month</span>
      <h1><?= e($featured['make'] . ' ' . $featured['model']) ?></h1>
      <p class="lead"><?= e($broker['tagline']) ?></p>
      <div class="spot-price"><?= rm($featured['price']) ?> <small>or <?= rm(monthly_installment($featured['price'])) ?>/mo</small></div>
      <a class="btn" href="/car/<?= e($featured['id']) ?>">Book a test drive</a>
      <a class="btn btn-ghost" href="#cars">Full lineup</a>
    </div>
    <div class="spot-media"><?= car_svg($featured) ?></div>
  </div>
</section>

<section class="wrap"><div class="usp">
  <div><strong>Official price</strong><span>No hidden charges</span></div>
  <div><strong>Fast approval</strong><span>Loan answer in 24 hours</span></div>
  <div><strong>Trade-in</strong><span>Best value for your old car</span></div>
</div></section>

<section class="wrap section" id="cars">
  <h2>The lineup</h2>
  <div class="car-grid">
    <?php foreach ($cars as $car): ?>
      <?= partial('car_card', ['car' => $car, 'href' => "/car/{$car['id']}"]) ?>
    <?php endforeach; ?>
  </div>
</section>

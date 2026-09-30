<?php
/** Default broker home. @var array $site  @var array $cars  @var array $featured */
$b    = $site['broker'];
$hero = $featured[0] ?? $cars[0];
?>
<section class="tenant-hero">
  <div class="tenant-hero-bg" data-label=""><?= car_photo($hero, 0, 1600, 'eager') ?></div>
  <div class="wrap tenant-hero-inner">
    <span class="eyebrow">Authorised <?= e(implode(' & ', $b['makes'])) ?> dealer · <?= e($b['area']) ?></span>
    <h1><?= e($b['tagline']) ?></h1>
    <div class="hero-rating"><?= icon('star', 16) ?> <strong><?= e($b['rating']) ?></strong> from <?= e(number_format($b['reviews'])) ?> Google reviews · Since <?= e($b['since']) ?></div>
    <div class="hero-actions">
      <a class="btn btn-primary btn-lg" href="/cars">Browse <?= count($cars) ?> models <?= icon('arrow', 18) ?></a>
      <a class="btn btn-light btn-lg" href="/car/<?= e($hero['id']) ?>#enquire">Book a test drive</a>
    </div>
  </div>
</section>

<section class="wrap usp-row">
  <div class="usp"><?= icon('shield', 22) ?><div><strong>Official pricing</strong><span>No hidden charges, ever</span></div></div>
  <div class="usp"><?= icon('wallet', 22) ?><div><strong>Loan in 24 hours</strong><span>We submit to 7+ banks</span></div></div>
  <div class="usp"><?= icon('handshake', 22) ?><div><strong>Best trade-in</strong><span>Free valuation of your old car</span></div></div>
  <div class="usp"><?= icon('calendar', 22) ?><div><strong>Fast delivery</strong><span>Selected models in 7 days</span></div></div>
</section>

<section class="wrap section">
  <div class="section-head">
    <div><span class="eyebrow">Our line-up</span><h2>New <?= e(implode(' & ', $b['makes'])) ?> models</h2></div>
    <a class="link-arrow" href="/cars">View all <?= icon('arrow', 16) ?></a>
  </div>
  <div class="car-grid">
    <?php foreach ($cars as $car): ?>
      <?= partial('car_card', ['car' => $car, 'href' => "/car/{$car['id']}"]) ?>
    <?php endforeach; ?>
  </div>
</section>

<?= partial('cta_band') ?>

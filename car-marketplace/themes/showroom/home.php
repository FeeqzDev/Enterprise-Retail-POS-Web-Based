<?php
/**
 * Toyota broker asked for a cinematic, premium showroom: full-bleed spotlight model,
 * then a model-by-model "line-up" strip instead of the default card grid.
 * @var array $site  @var array $cars  @var array $featured
 */
$b    = $site['broker'];
$spot = $featured[0] ?? $cars[0];
?>
<section class="spotlight">
  <div class="spotlight-bg" data-label=""><?= car_photo($spot, 0, 1920, 'eager') ?></div>
  <div class="wrap spotlight-inner">
    <span class="eyebrow">Featured this month</span>
    <h1><?= e($spot['model']) ?></h1>
    <p class="lead"><?= e($spot['variant']) ?> · <?= e($spot['power']) ?> hp · <?= e($spot['economy']) ?> L/100km</p>
    <div class="spot-price">
      <div><small>Official price</small><strong><?= rm($spot['price']) ?></strong></div>
      <div><small>Monthly from</small><strong><?= rm(monthly_installment($spot['price'])) ?></strong></div>
      <?php if ($spot['promo']): ?><div><small>This month</small><strong><?= e($spot['promo']) ?></strong></div><?php endif; ?>
    </div>
    <div class="hero-actions">
      <a class="btn btn-primary btn-lg" href="/car/<?= e($spot['id']) ?>#enquire">Book a test drive</a>
      <a class="btn btn-glass btn-lg" href="/car/<?= e($spot['id']) ?>">Explore <?= e($spot['model']) ?></a>
    </div>
  </div>
</section>

<section class="lineup">
  <div class="wrap">
    <div class="section-head">
      <div><span class="eyebrow">The line-up</span><h2>Find your Toyota</h2></div>
      <a class="link-arrow" href="/cars">Compare all <?= icon('arrow', 16) ?></a>
    </div>
    <?php foreach ($cars as $i => $car): ?>
      <article class="lineup-row <?= $i % 2 ? 'flip' : '' ?>">
        <a class="lineup-media" href="/car/<?= e($car['id']) ?>" data-label="<?= e($car['model']) ?>"><?= car_photo($car, 0, 1200) ?></a>
        <div class="lineup-text">
          <span class="eyebrow"><?= e($car['body']) ?> · <?= e($car['fuel']) ?></span>
          <h3><?= e($car['model']) ?> <span><?= e($car['variant']) ?></span></h3>
          <ul class="lineup-specs">
            <li><strong><?= e($car['power']) ?></strong> hp</li>
            <li><strong><?= e($car['torque']) ?></strong> Nm</li>
            <li><strong><?= e($car['economy']) ?></strong> L/100km</li>
          </ul>
          <div class="lineup-price"><?= rm($car['price']) ?> <small>or <?= rm(monthly_installment($car['price'])) ?>/mo</small></div>
          <a class="btn btn-outline" href="/car/<?= e($car['id']) ?>">View details</a>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
</section>

<section class="wrap usp-row">
  <div class="usp"><?= icon('shield', 22) ?><div><strong>Genuine Toyota</strong><span>Official warranty &amp; parts</span></div></div>
  <div class="usp"><?= icon('wallet', 22) ?><div><strong>Fast approval</strong><span>Loan answer in 24 hours</span></div></div>
  <div class="usp"><?= icon('handshake', 22) ?><div><strong>Trade-in</strong><span>Top value for your old car</span></div></div>
  <div class="usp"><?= icon('star', 22) ?><div><strong><?= e($b['rating']) ?> rating</strong><span><?= e(number_format($b['reviews'])) ?> Google reviews</span></div></div>
</section>

<?= partial('cta_band') ?>

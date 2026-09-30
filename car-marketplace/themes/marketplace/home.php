<?php
/** Marketplace home. @var array $cars  @var array $featured  @var array $brokers */
$app    = config('app');
$makes  = array_values(array_unique(array_column($cars, 'make')));
$bodies = array_values(array_unique(array_column($cars, 'body')));
$heroCar = find_car('toyota-corolla-cross-18g') ?? $cars[0];
$minPrice = [];
foreach ($cars as $c) {
    $minPrice[$c['make']] = min($minPrice[$c['make']] ?? PHP_INT_MAX, $c['price']);
}
$bodyCount = array_count_values(array_column($cars, 'body'));
?>
<section class="hero">
  <div class="hero-bg" data-label=""><?= car_photo($heroCar, 0, 1920, 'eager') ?></div>
  <div class="wrap hero-inner">
    <h1>Buy your new car<br>from trusted brokers</h1>
    <p><?= count($cars) ?> new models from <?= count($brokers) ?> verified brokers. Official prices, loan help and free test drives.</p>

    <form class="search-box" method="get" action="/cars">
      <label><span>Brand</span>
        <select name="make[]">
          <option value="">All brands</option>
          <?php foreach ($makes as $m): ?><option><?= e($m) ?></option><?php endforeach; ?>
        </select>
      </label>
      <label><span>Body type</span>
        <select name="body[]">
          <option value="">Any type</option>
          <?php foreach ($bodies as $b): ?><option><?= e($b) ?></option><?php endforeach; ?>
        </select>
      </label>
      <label><span>Budget</span>
        <select name="max">
          <option value="0">Any budget</option>
          <?php foreach ([50000, 80000, 100000, 130000, 170000] as $cap): ?><option value="<?= $cap ?>">Up to <?= rm($cap) ?></option><?php endforeach; ?>
        </select>
      </label>
      <button class="btn btn-primary btn-lg" type="submit"><?= icon('search', 18) ?> Search cars</button>
    </form>

    <div class="hero-trust">
      <span><?= icon('shield', 16) ?> Verified brokers only</span>
      <span><?= icon('wallet', 16) ?> Loan approval in 24h</span>
      <span><?= icon('handshake', 16) ?> Free trade-in valuation</span>
    </div>
  </div>
</section>

<section class="wrap section">
  <div class="section-head"><div><span class="eyebrow">Shop by brand</span><h2>Popular brands</h2></div></div>
  <div class="brand-row">
    <?php foreach ($makes as $m): ?>
      <a class="brand-tile" href="/cars?make[]=<?= e(urlencode($m)) ?>">
        <span class="brand-word brand-<?= e(strtolower($m)) ?>"><?= e(strtoupper($m)) ?></span>
        <small>From <?= rm($minPrice[$m]) ?></small>
      </a>
    <?php endforeach; ?>
  </div>
</section>

<section class="wrap section section-flush">
  <div class="section-head">
    <div><span class="eyebrow">Hot deals</span><h2>Featured new cars</h2></div>
    <a class="link-arrow" href="/cars">See all <?= count($cars) ?> cars <?= icon('arrow', 16) ?></a>
  </div>
  <div class="car-grid">
    <?php foreach (array_slice($featured, 0, 8) as $car): ?>
      <?= partial('car_card', ['car' => $car, 'href' => "/car/{$car['id']}", 'showBroker' => true]) ?>
    <?php endforeach; ?>
  </div>
</section>

<section class="wrap section">
  <div class="section-head"><div><span class="eyebrow">Shop by body type</span><h2>What are you looking for?</h2></div></div>
  <div class="body-row">
    <?php foreach ($bodyCount as $bt => $n): ?>
      <a class="body-tile" href="/cars?body[]=<?= e(urlencode($bt)) ?>"><?= icon('car', 28) ?><strong><?= e($bt) ?></strong><small><?= $n ?> model<?= $n > 1 ? 's' : '' ?></small></a>
    <?php endforeach; ?>
  </div>
</section>

<section class="how">
  <div class="wrap">
    <div class="section-head center"><div><span class="eyebrow">How it works</span><h2>New car in 3 simple steps</h2></div></div>
    <div class="steps">
      <div><span class="step-no">1</span><h3>Compare</h3><p>Browse official prices and monthly instalments from every broker in one place.</p></div>
      <div><span class="step-no">2</span><h3>Test drive</h3><p>Book online. The broker confirms your slot within 30 minutes.</p></div>
      <div><span class="step-no">3</span><h3>Drive home</h3><p>Your broker handles the loan, insurance, road tax and delivery.</p></div>
    </div>
  </div>
</section>

<section class="wrap section">
  <div class="section-head">
    <div><span class="eyebrow">Official partners</span><h2>Buy from verified brokers</h2></div>
    <a class="link-arrow" href="/brokers">All brokers <?= icon('arrow', 16) ?></a>
  </div>
  <?= partial('broker_grid', ['brokers' => $brokers]) ?>
</section>

<section class="cta-band">
  <div class="wrap cta-band-inner">
    <div><h2>Know your monthly before you visit</h2><p>Use our loan calculator to plan your budget in seconds.</p></div>
    <div class="cta-actions"><a class="btn btn-primary btn-lg" href="/loan-calculator">Open loan calculator</a></div>
  </div>
</section>

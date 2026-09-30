<?php
/** @var array $cars  @var array $filters  @var array $makes  @var array $bodies  @var array $brokers */
$app = config('app');
?>
<section class="hero">
  <div class="wrap">
    <h1>Find your next new car</h1>
    <p><?= e($app['tagline']) ?>. Compare prices, then deal directly with the broker.</p>

    <form class="search" method="get" action="/#cars">
      <select name="make" aria-label="Brand">
        <option value="">All brands</option>
        <?php foreach ($makes as $m): ?>
          <option <?= $filters['make'] === $m ? 'selected' : '' ?>><?= e($m) ?></option>
        <?php endforeach; ?>
      </select>
      <select name="body" aria-label="Body type">
        <option value="">Any body type</option>
        <?php foreach ($bodies as $b): ?>
          <option <?= $filters['body'] === $b ? 'selected' : '' ?>><?= e($b) ?></option>
        <?php endforeach; ?>
      </select>
      <select name="max" aria-label="Budget">
        <option value="0">Any budget</option>
        <?php foreach ([50000, 80000, 100000, 150000] as $cap): ?>
          <option value="<?= $cap ?>" <?= $filters['max'] === $cap ? 'selected' : '' ?>>Up to <?= rm($cap) ?></option>
        <?php endforeach; ?>
      </select>
      <button class="btn" type="submit">Search</button>
    </form>
  </div>
</section>

<section class="wrap section" id="brokers">
  <h2>Official brokers</h2>
  <div class="broker-grid">
    <?php foreach ($brokers as $slug => $b): ?>
      <a class="broker-card" href="<?= e(site_url($slug)) ?>" style="--primary: <?= e($b['colors']['primary']) ?>">
        <span class="broker-makes"><?= e(implode(', ', $b['makes'])) ?></span>
        <strong><?= e($b['name']) ?></strong>
        <span class="broker-host"><?= e($slug . '.' . $app['base_domain']) ?></span>
      </a>
    <?php endforeach; ?>
  </div>
</section>

<section class="wrap section" id="cars">
  <h2><?= count($cars) ?> new cars available</h2>
  <?php if (!$cars): ?>
    <p>No cars match those filters. <a href="/">Clear filters</a></p>
  <?php endif; ?>
  <div class="car-grid">
    <?php foreach ($cars as $car): ?>
      <?= partial('car_card', ['car' => $car, 'href' => site_url($car['broker'], "/car/{$car['id']}"), 'showBroker' => true]) ?>
    <?php endforeach; ?>
  </div>
</section>

<?php /** @var array $site  @var array $cars */ $broker = $site['broker']; ?>
<section class="hero">
  <div class="wrap">
    <h1><?= e($broker['name']) ?></h1>
    <p><?= e($broker['tagline']) ?></p>
    <a class="btn" href="#cars">View cars</a>
  </div>
</section>

<section class="wrap section" id="cars">
  <h2>Available now</h2>
  <div class="car-grid">
    <?php foreach ($cars as $car): ?>
      <?= partial('car_card', ['car' => $car, 'href' => "/car/{$car['id']}"]) ?>
    <?php endforeach; ?>
  </div>
</section>

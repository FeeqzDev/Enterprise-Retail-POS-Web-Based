<?php /** Broker "About us" page. @var array $site  @var array $cars */ $b = $site['broker']; ?>
<section class="page-head">
  <div class="wrap">
    <nav class="crumbs"><a href="/">Home</a> / <span>About us</span></nav>
    <h1>About <?= e($b['name']) ?></h1>
    <p class="page-sub"><?= e($b['tagline']) ?></p>
  </div>
</section>
<div class="wrap about">
  <div>
    <div class="about-photo" data-label="<?= e($b['short']) ?>"><?= car_photo($cars[0], min(1, count($cars[0]['images']) - 1), 1200) ?></div>
    <p class="about-text"><?= e($b['about']) ?></p>
    <div class="stats">
      <div><strong><?= date('Y') - $b['since'] ?>+</strong><span>years in business</span></div>
      <div><strong><?= e($b['rating']) ?>★</strong><span><?= e(number_format($b['reviews'])) ?> reviews</span></div>
      <div><strong><?= count($cars) ?></strong><span>models in stock</span></div>
    </div>
  </div>
  <aside class="card contact-card">
    <h2>Visit the showroom</h2>
    <p><?= icon('pin') ?> <?= e($b['address']) ?></p>
    <p><?= icon('clock') ?> <?= e($b['hours']) ?></p>
    <p><?= icon('phone') ?> <a href="tel:+<?= e($b['phone']) ?>"><?= e(display_phone($b['phone'])) ?></a></p>
    <a class="btn btn-primary btn-block" href="https://www.google.com/maps/search/?api=1&query=<?= e(rawurlencode($b['address'])) ?>" target="_blank" rel="noopener">Get directions</a>
    <a class="btn btn-whatsapp btn-block" href="<?= e(whatsapp_url($b['phone'])) ?>" target="_blank" rel="noopener"><?= icon('chat', 18) ?> WhatsApp us</a>
  </aside>
</div>

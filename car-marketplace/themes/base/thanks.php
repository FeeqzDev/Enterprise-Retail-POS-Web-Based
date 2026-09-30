<?php /** @var ?array $car  @var array $seller */ ?>
<section class="wrap section center thanks">
  <div class="thanks-icon"><?= icon('check', 36) ?></div>
  <h1>Enquiry sent!</h1>
  <p><?= e($seller['name']) ?> will call you shortly<?= $car ? ' about the ' . e(car_title($car)) : '' ?>.<br>Usually within 30 minutes during <?= e($seller['hours']) ?>.</p>
  <div class="hero-actions center">
    <a class="btn btn-whatsapp btn-lg" href="<?= e(whatsapp_url($seller['phone'])) ?>" target="_blank" rel="noopener"><?= icon('chat', 18) ?> Can't wait? WhatsApp now</a>
    <a class="btn btn-outline btn-lg" href="/cars">Keep browsing</a>
  </div>
</section>

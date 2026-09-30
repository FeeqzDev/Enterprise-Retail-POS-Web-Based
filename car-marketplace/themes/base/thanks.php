<?php /** @var array $site  @var ?array $car */ ?>
<section class="wrap section center">
  <h1>Thank you!</h1>
  <p><?= e($site['broker']['name']) ?> will contact you shortly<?= $car ? ' about the ' . e("{$car['make']} {$car['model']}") : '' ?>.</p>
  <a class="btn" href="/">Back to showroom</a>
</section>

<?php /** @var array $site  @var array $car */ $broker = $site['broker']; ?>
<section class="wrap section car-detail">
  <a class="back" href="/">← All cars</a>
  <div class="detail-grid">
    <div>
      <div class="detail-media"><?= car_svg($car) ?></div>
      <table class="specs">
        <tr><th>Year</th><td><?= e($car['year']) ?></td></tr>
        <tr><th>Body</th><td><?= e($car['body']) ?></td></tr>
        <tr><th>Fuel</th><td><?= e($car['fuel']) ?></td></tr>
        <tr><th>Transmission</th><td><?= e($car['transmission']) ?></td></tr>
        <tr><th>Seats</th><td><?= e($car['seats']) ?></td></tr>
      </table>
    </div>
    <div>
      <div class="car-make"><?= e($car['make']) ?></div>
      <h1><?= e($car['model']) ?> <span><?= e($car['variant']) ?></span></h1>
      <div class="car-price big"><?= rm($car['price']) ?></div>
      <p class="car-monthly">Est. <?= rm(monthly_installment($car['price'])) ?>/month (90% loan, 9 years)</p>

      <form class="enquiry" method="post" action="/enquiry">
        <h3>Book a test drive / get a quote</h3>
        <?php if (!empty($error)): ?><p class="error"><?= e($error) ?></p><?php endif; ?>
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="car_id" value="<?= e($car['id']) ?>">
        <label>Name <input name="name" required maxlength="100"></label>
        <label>Phone <input name="phone" type="tel" required placeholder="012-345 6789"></label>
        <label>Message <textarea name="message" rows="3" placeholder="Trade-in, preferred colour, etc."></textarea></label>
        <button class="btn" type="submit">Send enquiry</button>
        <a class="btn btn-ghost" href="https://wa.me/<?= e($broker['phone']) ?>?text=<?= e(rawurlencode("Hi, I'm interested in the {$car['make']} {$car['model']} {$car['variant']}")) ?>" target="_blank" rel="noopener">WhatsApp</a>
      </form>
    </div>
  </div>
</section>

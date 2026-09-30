<?php
/**
 * Car detail page: gallery, key specs, features, loan estimate, enquiry, similar cars.
 * @var array $site  @var array $car  @var array $seller  @var array $similar  @var ?string $error
 */
$loan    = config('app')['loan'];
$sellerUrl = $site['is_main'] ? site_url($car['broker']) : '/about';
$waText  = 'Hi ' . $seller['short'] . ", I'm interested in the " . car_title($car) . ' (' . rm($car['price']) . ').';
?>
<section class="page-head page-head-slim">
  <div class="wrap">
    <nav class="crumbs"><a href="/">Home</a> / <a href="/cars">New cars</a> / <a href="/cars?make[]=<?= e(urlencode($car['make'])) ?>"><?= e($car['make']) ?></a> / <span><?= e("{$car['model']} {$car['variant']}") ?></span></nav>
  </div>
</section>

<div class="wrap detail">
  <div class="detail-main">
    <div class="gallery" data-gallery>
      <div class="gallery-stage" data-label="<?= e("{$car['make']} {$car['model']}") ?>">
        <?= car_photo($car, 0, 1280, 'eager') ?>
        <?php if ($car['promo']): ?><span class="badge badge-promo"><?= e($car['promo']) ?></span><?php endif; ?>
      </div>
      <?php if (count($car['images']) > 1): ?>
        <div class="gallery-thumbs">
          <?php foreach ($car['images'] as $i => $src): ?>
            <button type="button" class="<?= $i === 0 ? 'active' : '' ?>" data-full="<?= e(photo_url($src, 1280)) ?>" data-label="<?= e($car['model']) ?>">
              <?= car_photo($car, $i, 240) ?>
            </button>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
      <div class="photo-credit">Photos: <?php foreach ($car['images'] as $i => $src): if ($url = photo_credit_url($src)): ?><a href="<?= e($url) ?>" target="_blank" rel="noopener">#<?= $i + 1 ?></a> <?php endif; endforeach; ?>Wikimedia Commons, CC BY-SA</div>
    </div>

    <section class="card">
      <h2>Key specifications</h2>
      <div class="spec-grid">
        <div><?= icon('engine') ?><span>Engine</span><strong><?= e(number_format($car['engine'])) ?> cc</strong></div>
        <div><?= icon('gauge') ?><span>Power</span><strong><?= e($car['power']) ?> hp</strong></div>
        <div><?= icon('gauge') ?><span>Torque</span><strong><?= e($car['torque']) ?> Nm</strong></div>
        <div><?= icon('gear') ?><span>Transmission</span><strong><?= e($car['transmission']) ?></strong></div>
        <div><?= icon('fuel') ?><span>Fuel</span><strong><?= e($car['fuel']) ?> · <?= e($car['economy']) ?> L/100km</strong></div>
        <div><?= icon('car') ?><span>Body type</span><strong><?= e($car['body']) ?></strong></div>
        <div><?= icon('seat') ?><span>Seats</span><strong><?= e($car['seats']) ?></strong></div>
        <div><?= icon('shield') ?><span>Airbags</span><strong><?= e($car['airbags']) ?></strong></div>
      </div>
    </section>

    <section class="card">
      <h2>Colours</h2>
      <div class="swatches">
        <?php foreach ($car['colours'] as $colourName => $hex): ?>
          <div class="swatch"><span style="background: <?= e($hex) ?>"></span><?= e($colourName) ?></div>
        <?php endforeach; ?>
      </div>
    </section>

    <section class="card">
      <h2>Highlights</h2>
      <ul class="feature-list">
        <?php foreach ($car['features'] as $f): ?><li><?= icon('check', 16) ?> <?= e($f) ?></li><?php endforeach; ?>
      </ul>
    </section>

    <section class="card" id="loan">
      <h2>Estimate your monthly instalment</h2>
      <?= partial('loan_widget', ['price' => $car['price']]) ?>
    </section>
  </div>

  <aside class="detail-side">
    <div class="card buy-box">
      <div class="car-card-meta"><?= e($car['year']) ?> · Brand new · <?= e($car['body']) ?></div>
      <h1><?= e("{$car['make']} {$car['model']}") ?> <span><?= e($car['variant']) ?></span></h1>
      <div class="buy-price"><?= rm($car['price']) ?></div>
      <div class="buy-monthly">From <strong><?= rm(monthly_installment($car['price'])) ?>/month</strong> · <?= (int) ($loan['margin'] * 100) ?>% loan, <?= e($loan['years']) ?> years</div>
      <?php if ($car['promo']): ?><div class="promo-line"><?= icon('check', 16) ?> <?= e($car['promo']) ?></div><?php endif; ?>

      <form class="enquiry" method="post" action="/enquiry" id="enquire">
        <?php if (!empty($error)): ?><p class="form-error"><?= e($error) ?></p><?php endif; ?>
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="car_id" value="<?= e($car['id']) ?>">
        <div class="segmented">
          <label><input type="radio" name="type" value="test_drive" checked><span>Test drive</span></label>
          <label><input type="radio" name="type" value="quote"><span>Get quote</span></label>
          <label><input type="radio" name="type" value="loan"><span>Loan check</span></label>
        </div>
        <input name="name" required maxlength="100" placeholder="Full name" aria-label="Full name" value="<?= e($_POST['name'] ?? '') ?>">
        <input name="phone" type="tel" required placeholder="Phone, e.g. 012-345 6789" aria-label="Phone" value="<?= e($_POST['phone'] ?? '') ?>">
        <textarea name="message" rows="2" placeholder="Preferred colour, trade-in, questions (optional)" aria-label="Message"><?= e($_POST['message'] ?? '') ?></textarea>
        <button class="btn btn-primary btn-block btn-lg" type="submit">Send enquiry</button>
        <a class="btn btn-whatsapp btn-block" href="<?= e(whatsapp_url($seller['phone'], $waText)) ?>" target="_blank" rel="noopener"><?= icon('chat', 18) ?> WhatsApp <?= e($seller['short']) ?></a>
        <p class="fine">By submitting, you agree to be contacted by <?= e($seller['name']) ?>.</p>
      </form>
    </div>

    <div class="card seller-card">
      <div class="seller-top">
        <div class="seller-mark" style="--seller: <?= e($seller['colors']['primary']) ?>"><?= e(mb_substr($seller['short'], 0, 1)) ?></div>
        <div>
          <strong><?= e($seller['name']) ?></strong>
          <span><?= icon('star', 12) ?> <?= e($seller['rating']) ?> (<?= e(number_format($seller['reviews'])) ?> reviews) · Verified broker</span>
        </div>
      </div>
      <p><?= icon('pin', 14) ?> <?= e($seller['address']) ?></p>
      <p><?= icon('clock', 14) ?> <?= e($seller['hours']) ?></p>
      <a class="link-arrow" href="<?= e($sellerUrl) ?>"><?= $site['is_main'] ? 'Visit showroom website' : 'About us' ?> <?= icon('arrow', 16) ?></a>
    </div>
  </aside>
</div>

<?php if ($similar): ?>
<section class="wrap section">
  <div class="section-head"><h2>You may also like</h2></div>
  <div class="car-grid">
    <?php foreach ($similar as $s): ?>
      <?= partial('car_card', ['car' => $s, 'href' => "/car/{$s['id']}", 'showBroker' => $site['is_main']]) ?>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<div class="mobile-buybar">
  <div><strong><?= rm($car['price']) ?></strong><span><?= rm(monthly_installment($car['price'])) ?>/mo</span></div>
  <a class="btn btn-primary" href="#enquire">Enquire now</a>
</div>

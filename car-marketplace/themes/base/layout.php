<?php
/** Default broker layout. @var array $site  @var string $content */
$b = $site['broker'];
$initials = implode('', array_map(fn ($w) => $w[0], array_slice(explode(' ', $b['short']), 0, 2)));
?>
<!doctype html>
<html lang="en">
<head><?= partial('head', ['title' => $title ?? null]) ?></head>
<body class="theme-<?= e($site['theme']) ?> tenant">
  <div class="topbar">
    <div class="wrap topbar-row">
      <span><?= icon('pin', 14) ?> <?= e($b['area']) ?></span>
      <span class="hide-sm"><?= icon('clock', 14) ?> <?= e($b['hours']) ?></span>
      <a href="tel:+<?= e($b['phone']) ?>"><?= icon('phone', 14) ?> <?= e(display_phone($b['phone'])) ?></a>
      <a class="topbar-network" href="<?= e(site_url(null)) ?>">Official broker on <?= e(config('app')['name']) ?></a>
    </div>
  </div>

  <header class="site-header">
    <div class="wrap header-row">
      <a class="brand" href="/">
        <span class="brand-mark"><?= e($initials) ?></span>
        <span class="brand-text"><strong><?= e($b['short']) ?></strong><small>Authorised <?= e(implode(' & ', $b['makes'])) ?> dealer</small></span>
      </a>
      <button class="nav-toggle" aria-label="Menu" data-nav-toggle><?= icon('menu', 22) ?></button>
      <nav class="main-nav" data-nav>
        <a href="/cars">New Cars</a>
        <a href="/loan-calculator">Loan Calculator</a>
        <a href="/about">About Us</a>
        <a class="btn btn-primary btn-sm" href="<?= e(whatsapp_url($b['phone'], "Hi {$b['short']}, I'd like to know more about your cars.")) ?>" target="_blank" rel="noopener"><?= icon('chat', 16) ?> WhatsApp</a>
      </nav>
    </div>
  </header>

  <main><?= $content ?></main>

  <footer class="site-footer">
    <div class="wrap footer-grid">
      <div>
        <div class="footer-brand"><?= e($b['name']) ?></div>
        <p><?= e($b['about']) ?></p>
      </div>
      <div>
        <h4>Models</h4>
        <?php foreach (cars($site['slug']) as $c): ?>
          <a href="/car/<?= e($c['id']) ?>"><?= e("{$c['make']} {$c['model']}") ?></a>
        <?php endforeach; ?>
      </div>
      <div>
        <h4>Visit us</h4>
        <p><?= icon('pin', 14) ?> <?= e($b['address']) ?></p>
        <p><?= icon('clock', 14) ?> <?= e($b['hours']) ?></p>
        <p><?= icon('phone', 14) ?> <?= e(display_phone($b['phone'])) ?> · <?= e($b['email']) ?></p>
      </div>
    </div>
    <div class="wrap footer-bottom">
      <span>© <?= date('Y') ?> <?= e($b['name']) ?></span>
      <span>Car photos: Wikimedia Commons contributors, CC BY-SA. Powered by <a href="<?= e(site_url(null)) ?>"><?= e(config('app')['name']) ?></a></span>
    </div>
  </footer>
</body>
</html>

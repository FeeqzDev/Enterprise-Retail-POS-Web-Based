<?php /** Main marketplace layout. @var array $site  @var string $content */ $app = config('app'); ?>
<!doctype html>
<html lang="en">
<head><?= partial('head', ['title' => $title ?? null]) ?></head>
<body class="theme-marketplace">
  <header class="site-header">
    <div class="wrap header-row">
      <a class="brand" href="/"><span class="logo">Kereta<span>Ku</span></span></a>
      <button class="nav-toggle" aria-label="Menu" data-nav-toggle><?= icon('menu', 22) ?></button>
      <nav class="main-nav" data-nav>
        <a href="/cars">Buy New Car</a>
        <a href="/brokers">Brokers</a>
        <a href="/loan-calculator">Loan Calculator</a>
        <a class="btn btn-outline btn-sm" href="/brokers#join">Become a partner</a>
      </nav>
    </div>
  </header>

  <main><?= $content ?></main>

  <footer class="site-footer">
    <div class="wrap footer-grid footer-grid-4">
      <div>
        <div class="logo">Kereta<span>Ku</span></div>
        <p><?= e($app['tagline']) ?>. Every broker on <?= e($app['name']) ?> is verified and sells at official prices.</p>
      </div>
      <div>
        <h4>Brands</h4>
        <?php foreach (array_unique(array_column(cars(), 'make')) as $m): ?>
          <a href="/cars?make[]=<?= e(urlencode($m)) ?>">New <?= e($m) ?> cars</a>
        <?php endforeach; ?>
      </div>
      <div>
        <h4>Body type</h4>
        <?php foreach (array_unique(array_column(cars(), 'body')) as $bt): ?>
          <a href="/cars?body[]=<?= e(urlencode($bt)) ?>"><?= e($bt) ?></a>
        <?php endforeach; ?>
      </div>
      <div>
        <h4>Company</h4>
        <a href="/brokers">Our brokers</a>
        <a href="/brokers#join">Become a partner</a>
        <a href="/loan-calculator">Loan calculator</a>
      </div>
    </div>
    <div class="wrap footer-bottom">
      <span>© <?= date('Y') ?> <?= e($app['name']) ?> Sdn Bhd (sample)</span>
      <span>Car photos: Wikimedia Commons contributors, CC BY-SA</span>
    </div>
  </footer>
</body>
</html>

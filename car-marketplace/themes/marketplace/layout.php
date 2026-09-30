<?php /** @var string $content  @var string $title */ $app = config('app'); ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($title ?? $app['name']) ?></title>
  <link rel="stylesheet" href="/themes/base.css">
  <link rel="stylesheet" href="/themes/marketplace.css">
</head>
<body class="theme-marketplace">
  <header class="site-header">
    <div class="wrap header-row">
      <a class="brand" href="/"><span class="logo-dot"></span><?= e($app['name']) ?></a>
      <nav>
        <a href="/#cars">New cars</a>
        <a href="/#brokers">Brokers</a>
        <a class="btn btn-small" href="/#brokers">Sell with us</a>
      </nav>
    </div>
  </header>
  <main><?= $content ?></main>
  <footer class="site-footer">
    <div class="wrap footer-row">
      <div>© <?= date('Y') ?> <?= e($app['name']) ?>. <?= e($app['tagline']) ?>.</div>
    </div>
  </footer>
</body>
</html>

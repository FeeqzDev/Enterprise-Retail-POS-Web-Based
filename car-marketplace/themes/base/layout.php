<?php
/** @var array $site  @var string $content  @var string $title */
$broker = $site['broker'];
$colors = $broker['colors'] ?? [];
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($title ?? config('app')['name']) ?></title>
  <link rel="stylesheet" href="/themes/base.css">
  <?php if ($site['theme'] !== 'base'): ?>
    <link rel="stylesheet" href="/themes/<?= e($site['theme']) ?>.css">
  <?php endif; ?>
  <?php if ($colors): ?>
    <style>:root{<?php foreach ($colors as $k => $v): ?>--<?= e($k) ?>:<?= e($v) ?>;<?php endforeach; ?>}</style>
  <?php endif; ?>
</head>
<body class="theme-<?= e($site['theme']) ?>">
  <header class="site-header">
    <div class="wrap header-row">
      <a class="brand" href="/"><?= e($broker['name'] ?? config('app')['name']) ?></a>
      <nav>
        <a href="/#cars">Cars</a>
        <?php if ($broker): ?>
          <a class="btn btn-small" href="https://wa.me/<?= e($broker['phone']) ?>" target="_blank" rel="noopener">WhatsApp us</a>
        <?php endif; ?>
      </nav>
    </div>
  </header>

  <main><?= $content ?></main>

  <footer class="site-footer">
    <div class="wrap footer-row">
      <?php if ($broker): ?>
        <div>
          <strong><?= e($broker['name']) ?></strong><br>
          <?= e($broker['address']) ?> · <?= e($broker['email']) ?>
        </div>
      <?php endif; ?>
      <a class="powered" href="<?= e(site_url(null)) ?>">Part of <?= e(config('app')['name']) ?> →</a>
    </div>
  </footer>
</body>
</html>

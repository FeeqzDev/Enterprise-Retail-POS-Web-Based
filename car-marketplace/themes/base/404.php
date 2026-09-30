<section class="wrap section center">
  <h1>404</h1>
  <p><?= e($message ?? 'Page not found.') ?></p>
  <a class="btn" href="<?= e(site_url(null)) ?>">Go to <?= e(config('app')['name']) ?></a>
</section>

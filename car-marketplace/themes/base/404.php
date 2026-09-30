<section class="wrap section center">
  <h1 class="big-404">404</h1>
  <p><?= e($message ?? 'Page not found.') ?></p>
  <a class="btn btn-primary" href="<?= e(site_url(null)) ?>">Go to <?= e(config('app')['name']) ?></a>
</section>

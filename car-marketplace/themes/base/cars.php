<?php
/**
 * Listing page with filter sidebar, sort and pagination.
 * @var array $site  @var array $filters  @var array $results  @var int $total  @var int $page  @var int $pages  @var array $options
 */
$labels = ['make' => 'Brand', 'body' => 'Body type', 'fuel' => 'Fuel'];
$prices = [0 => 'Any', 50000 => 'RM 50k', 80000 => 'RM 80k', 100000 => 'RM 100k', 130000 => 'RM 130k', 170000 => 'RM 170k'];
$active = array_merge($filters['make'], $filters['body'], $filters['fuel']);
?>
<section class="page-head">
  <div class="wrap">
    <nav class="crumbs"><a href="/">Home</a> / <span>New cars</span></nav>
    <h1>New cars for sale<?= $site['broker'] ? ' at ' . e($site['broker']['short']) : ' in Malaysia' ?></h1>
  </div>
</section>

<div class="wrap listing">
  <aside class="filters">
    <form method="get" action="/cars" data-autosubmit>
      <div class="filter-head"><strong>Filters</strong><a href="/cars">Reset</a></div>

      <label class="filter-search"><?= icon('search', 16) ?>
        <input name="q" value="<?= e($filters['q']) ?>" placeholder="Search model, e.g. Myvi">
      </label>

      <?php foreach ($labels as $key => $label): ?>
        <?php if (count($options[$key]) < 2) continue; ?>
        <fieldset>
          <legend><?= e($label) ?></legend>
          <?php foreach ($options[$key] as $opt): ?>
            <label class="check">
              <input type="checkbox" name="<?= e($key) ?>[]" value="<?= e($opt) ?>" <?= in_array($opt, $filters[$key], true) ? 'checked' : '' ?>>
              <span><?= e($opt) ?></span>
            </label>
          <?php endforeach; ?>
        </fieldset>
      <?php endforeach; ?>

      <fieldset>
        <legend>Price</legend>
        <div class="price-range">
          <select name="min" aria-label="Minimum price">
            <?php foreach ($prices as $v => $l): ?><option value="<?= $v ?>" <?= $filters['min'] === $v ? 'selected' : '' ?>><?= $v ? "From {$l}" : 'Min' ?></option><?php endforeach; ?>
          </select>
          <select name="max" aria-label="Maximum price">
            <?php foreach ($prices as $v => $l): ?><option value="<?= $v ?>" <?= $filters['max'] === $v ? 'selected' : '' ?>><?= $v ? "Up to {$l}" : 'Max' ?></option><?php endforeach; ?>
          </select>
        </div>
      </fieldset>
      <input type="hidden" name="sort" value="<?= e($filters['sort']) ?>">
      <button class="btn btn-primary btn-block" type="submit">Show results</button>
    </form>
  </aside>

  <section class="results">
    <div class="results-bar">
      <div>
        <strong><?= $total ?></strong> car<?= $total === 1 ? '' : 's' ?> found
        <?php foreach ($active as $tag): ?><span class="tag"><?= e($tag) ?></span><?php endforeach; ?>
      </div>
      <form method="get" action="/cars" class="sort">
        <?php foreach (['make', 'body', 'fuel'] as $k): foreach ($filters[$k] as $v): ?>
          <input type="hidden" name="<?= $k ?>[]" value="<?= e($v) ?>">
        <?php endforeach; endforeach; ?>
        <?php foreach (['min', 'max', 'q'] as $k): if ($filters[$k]): ?>
          <input type="hidden" name="<?= $k ?>" value="<?= e($filters[$k]) ?>">
        <?php endif; endforeach; ?>
        <label>Sort by
          <select name="sort" onchange="this.form.submit()">
            <?php foreach (['popular' => 'Recommended', 'price_asc' => 'Price: low to high', 'price_desc' => 'Price: high to low', 'name' => 'Name A–Z'] as $v => $l): ?>
              <option value="<?= $v ?>" <?= $filters['sort'] === $v ? 'selected' : '' ?>><?= $l ?></option>
            <?php endforeach; ?>
          </select>
        </label>
      </form>
    </div>

    <?php if (!$results): ?>
      <div class="empty">
        <h3>No cars match your filters</h3>
        <p>Try widening your budget or removing a filter.</p>
        <a class="btn btn-primary" href="/cars">Clear all filters</a>
      </div>
    <?php else: ?>
      <div class="car-grid car-grid-3">
        <?php foreach ($results as $car): ?>
          <?= partial('car_card', ['car' => $car, 'href' => "/car/{$car['id']}", 'showBroker' => $site['is_main']]) ?>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <?php if ($pages > 1): ?>
      <nav class="pagination" aria-label="Pages">
        <?php if ($page > 1): ?><a href="<?= e(listing_url($filters, ['page' => $page - 1])) ?>">‹ Prev</a><?php endif; ?>
        <?php for ($i = 1; $i <= $pages; $i++): ?>
          <a href="<?= e(listing_url($filters, ['page' => $i])) ?>" class="<?= $i === $page ? 'current' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
        <?php if ($page < $pages): ?><a href="<?= e(listing_url($filters, ['page' => $page + 1])) ?>">Next ›</a><?php endif; ?>
      </nav>
    <?php endif; ?>
  </section>
</div>

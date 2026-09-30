<?php
declare(strict_types=1);

const APP_ROOT = __DIR__ . '/..';

function config(string $name): array
{
    static $cache = [];
    return $cache[$name] ??= require APP_ROOT . "/config/{$name}.php";
}

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function rm(int|float $amount): string
{
    return 'RM ' . number_format($amount);
}

function monthly_installment(int $price): int
{
    ['margin' => $margin, 'years' => $years, 'flat_rate' => $rate] = config('app')['loan'];
    $loan = $price * $margin;
    return (int) round(($loan + $loan * $rate * $years) / ($years * 12));
}

/* ---------------------------------------------------------------- Tenancy */

/**
 * Work out which site is being requested from the Host header.
 *   keretaku.my / www.keretaku.my   -> main marketplace
 *   toyota.keretaku.my              -> broker "toyota"
 *   www.prestige-toyota.my          -> broker whose custom_domain matches
 * Returns null for an unknown subdomain.
 */
function resolve_site(string $hostHeader, ?string $forceBroker = null): ?array
{
    $app     = config('app');
    $brokers = config('brokers');
    $host    = strtolower(preg_replace('/:\d+$/', '', $hostHeader));
    $base    = strtolower($app['base_domain']);

    if ($app['debug'] && $forceBroker !== null && $forceBroker !== '') {
        return isset($brokers[$forceBroker]) ? broker_site($forceBroker) : null;
    }

    if ($host === $base || $host === "www.{$base}") {
        return main_site();
    }

    if (str_ends_with($host, ".{$base}")) {
        $slug = substr($host, 0, -strlen(".{$base}"));
        return isset($brokers[$slug]) ? broker_site($slug) : null;
    }

    foreach ($brokers as $slug => $broker) {
        if (!empty($broker['custom_domain']) && strtolower($broker['custom_domain']) === $host) {
            return broker_site($slug);
        }
    }

    return null;
}

function main_site(): array
{
    return ['is_main' => true, 'slug' => null, 'broker' => null, 'theme' => 'marketplace'];
}

function broker_site(string $slug): array
{
    $broker = config('brokers')[$slug];
    return ['is_main' => false, 'slug' => $slug, 'broker' => $broker, 'theme' => $broker['theme']];
}

/** Absolute URL to the main site or a broker subdomain, keeping the current port for local dev. */
function site_url(?string $slug, string $path = '/'): string
{
    $app    = config('app');
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $port   = preg_match('/:(\d+)$/', $_SERVER['HTTP_HOST'] ?? '', $m) ? ":{$m[1]}" : '';
    $host   = $slug ? "{$slug}.{$app['base_domain']}" : $app['base_domain'];
    return "{$scheme}://{$host}{$port}{$path}";
}

/* ------------------------------------------------------------------ Data */

function cars(?string $brokerSlug = null): array
{
    static $all = null;
    $all ??= require APP_ROOT . '/data/cars.php';
    return $brokerSlug === null
        ? $all
        : array_values(array_filter($all, fn ($c) => $c['broker'] === $brokerSlug));
}

function find_car(string $id, ?string $brokerSlug = null): ?array
{
    foreach (cars($brokerSlug) as $car) {
        if ($car['id'] === $id) {
            return $car;
        }
    }
    return null;
}

function save_lead(array $lead): void
{
    $line = json_encode($lead + ['created_at' => date('c')], JSON_UNESCAPED_SLASHES) . PHP_EOL;
    file_put_contents(APP_ROOT . '/storage/leads.jsonl', $line, FILE_APPEND | LOCK_EX);
}

/* ------------------------------------------------------------- Rendering */

/** Theme template lookup: themes/<theme>/<view>.php, falling back to themes/base/<view>.php. */
function template_path(string $theme, string $view): string
{
    $custom = APP_ROOT . "/themes/{$theme}/{$view}.php";
    return is_file($custom) ? $custom : APP_ROOT . "/themes/base/{$view}.php";
}

function partial(string $__view, array $__vars = []): string
{
    global $site;
    // EXTR_SKIP: template variables can never overwrite $__view / $site.
    extract($__vars, EXTR_SKIP);
    ob_start();
    require template_path($site['theme'], $__view);
    return (string) ob_get_clean();
}

function render(string $view, array $vars = [], int $status = 200): void
{
    http_response_code($status);
    $content = partial($view, $vars);
    echo partial('layout', $vars + ['content' => $content]);
}

function csrf_token(): string
{
    return $_SESSION['csrf'] ??= bin2hex(random_bytes(16));
}

/* ---------------------------------------------------------------- Photos */

/**
 * Image URL for a car photo. Plain file names are Wikimedia Commons files, served as a
 * resized thumbnail. Anything starting with "/" or "http" is used as-is (broker uploads).
 */
function photo_url(string $src, int $width = 800): string
{
    if (str_starts_with($src, '/') || str_starts_with($src, 'http')) {
        return $src;
    }
    return 'https://commons.wikimedia.org/wiki/Special:FilePath/' . rawurlencode(str_replace(' ', '_', $src)) . "?width={$width}";
}

/** Attribution link for a Commons photo, or null for the broker's own photos. */
function photo_credit_url(string $src): ?string
{
    if (str_starts_with($src, '/') || str_starts_with($src, 'http')) {
        return null;
    }
    return 'https://commons.wikimedia.org/wiki/File:' . rawurlencode(str_replace(' ', '_', $src));
}

/**
 * <img> for a car. If the photo fails to load, the wrapper (which carries data-label)
 * shows a clean branded placeholder instead of a broken image icon.
 */
function car_photo(array $car, int $index = 0, int $width = 800, string $loading = 'lazy'): string
{
    $src = $car['images'][$index] ?? $car['images'][0] ?? null;
    if ($src === null) {
        return '';
    }
    return sprintf(
        '<img src="%s" alt="%s" loading="%s" decoding="async" onerror="this.parentNode.classList.add(\'no-photo\');this.remove()">',
        e(photo_url($src, $width)),
        e(car_title($car) . ' photo ' . ($index + 1)),
        e($loading)
    );
}

function car_title(array $car): string
{
    return "{$car['make']} {$car['model']} {$car['variant']}";
}

/* --------------------------------------------------------------- Listing */

const PER_PAGE = 9;

/** Read listing filters from the query string, dropping anything unknown. */
function listing_filters(array $query, array $pool): array
{
    $pick = fn (string $key) => array_values(array_intersect(
        array_map('strval', (array) ($query[$key] ?? [])),
        array_unique(array_column($pool, $key))
    ));
    $sorts = ['price_asc', 'price_desc', 'name', 'popular'];

    return [
        'make' => $pick('make'),
        'body' => $pick('body'),
        'fuel' => $pick('fuel'),
        'min'  => max(0, (int) ($query['min'] ?? 0)),
        'max'  => max(0, (int) ($query['max'] ?? 0)),
        'q'    => mb_substr(trim((string) ($query['q'] ?? '')), 0, 60),
        'sort' => in_array($query['sort'] ?? '', $sorts, true) ? $query['sort'] : 'popular',
    ];
}

function filter_cars(array $pool, array $f): array
{
    $list = array_values(array_filter($pool, fn ($c) =>
        (!$f['make'] || in_array($c['make'], $f['make'], true)) &&
        (!$f['body'] || in_array($c['body'], $f['body'], true)) &&
        (!$f['fuel'] || in_array($c['fuel'], $f['fuel'], true)) &&
        (!$f['min']  || $c['price'] >= $f['min']) &&
        (!$f['max']  || $c['price'] <= $f['max']) &&
        ($f['q'] === '' || stripos(car_title($c) . ' ' . $c['body'], $f['q']) !== false)
    ));

    usort($list, match ($f['sort']) {
        'price_asc'  => fn ($a, $b) => $a['price'] <=> $b['price'],
        'price_desc' => fn ($a, $b) => $b['price'] <=> $a['price'],
        'name'       => fn ($a, $b) => strcmp(car_title($a), car_title($b)),
        default      => fn ($a, $b) => [$b['featured'], $a['price']] <=> [$a['featured'], $b['price']],
    });
    return $list;
}

/** URL for the current listing with some query parameters changed. */
function listing_url(array $filters, array $changes = []): string
{
    $params = array_filter(array_merge($filters, $changes), fn ($v) => $v !== [] && $v !== '' && $v !== 0 && $v !== null);
    if (($params['sort'] ?? '') === 'popular') {
        unset($params['sort']);
    }
    if (($params['page'] ?? 1) === 1) {
        unset($params['page']);
    }
    $qs = http_build_query($params);
    return '/cars' . ($qs ? "?{$qs}" : '');
}

function similar_cars(array $car, array $pool, int $limit = 4): array
{
    $others = array_filter($pool, fn ($c) => $c['id'] !== $car['id']);
    usort($others, fn ($a, $b) =>
        [$a['body'] !== $car['body'], abs($a['price'] - $car['price'])] <=>
        [$b['body'] !== $car['body'], abs($b['price'] - $car['price'])]);
    return array_slice($others, 0, $limit);
}

/** Minimal inline stroke icons (Lucide-style), so there are no icon-font dependencies. */
function icon(string $name, int $size = 18): string
{
    $paths = [
        'phone'    => '<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.4 1.8.7 2.7a2 2 0 0 1-.5 2.1L8 9.8a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.7.7a2 2 0 0 1 1.7 2z"/>',
        'pin'      => '<path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>',
        'clock'    => '<circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>',
        'star'     => '<path d="m12 2 3.1 6.3 6.9 1-5 4.9 1.2 6.8L12 17.8 5.8 21l1.2-6.8-5-4.9 6.9-1z" fill="currentColor"/>',
        'check'    => '<path d="M20 6 9 17l-5-5"/>',
        'fuel'     => '<path d="M3 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18M3 22h12M7 7h4M15 10h2a2 2 0 0 1 2 2v3a2 2 0 0 0 4 0V9l-3-3"/>',
        'gear'     => '<circle cx="12" cy="12" r="3"/><path d="M12 2v3M12 19v3M4.2 4.2l2.1 2.1M17.7 17.7l2.1 2.1M2 12h3M19 12h3M4.2 19.8l2.1-2.1M17.7 6.3l2.1-2.1"/>',
        'seat'     => '<path d="M7 18v3M17 18v3M5 11V6a3 3 0 0 1 3-3h1a3 3 0 0 1 3 3v8h5a2 2 0 0 1 2 2v2H7a2 2 0 0 1-2-2z"/>',
        'engine'   => '<path d="M3 10h2V8h3V6h6v2h3l2 3h2v6h-2l-2 3H8l-3-3H3z"/>',
        'car'      => '<path d="M5 17H3v-5l2-5h14l2 5v5h-2M5 12h14"/><circle cx="7.5" cy="17" r="2"/><circle cx="16.5" cy="17" r="2"/>',
        'shield'   => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/>',
        'wallet'   => '<path d="M20 12V8H6a2 2 0 0 1 0-4h12v4M4 6v12a2 2 0 0 0 2 2h14v-4"/><path d="M18 12a2 2 0 0 0 0 4h4v-4z"/>',
        'handshake'=> '<path d="m11 17 2 2a1 1 0 1 0 3-3M14 14l2.5 2.5a1 1 0 1 0 3-3l-3.9-3.9a3 3 0 0 0-4.2 0l-.9.9a1 1 0 1 1-3-3l2.8-2.8a5.8 5.8 0 0 1 7.1-.9l.5.3a4 4 0 0 0 2.8.4L21 3M21 14V3M3 3v11l6.5 6.5a1 1 0 1 0 3-3M3 3h8"/>',
        'search'   => '<circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>',
        'arrow'    => '<path d="M5 12h14M12 5l7 7-7 7"/>',
        'calendar' => '<rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>',
        'gauge'    => '<path d="m12 14 4-4M3.3 19a10 10 0 1 1 17.4 0"/>',
        'menu'     => '<path d="M4 6h16M4 12h16M4 18h16"/>',
        'chat'     => '<path d="M21 11.5a8.4 8.4 0 0 1-9 8.4 8.5 8.5 0 0 1-3.8-.9L3 21l1.9-5.7a8.5 8.5 0 0 1-.9-3.8 8.4 8.4 0 0 1 8.4-8.5h.5a8.5 8.5 0 0 1 8 8z"/>',
    ];
    return sprintf(
        '<svg class="icon" width="%d" height="%d" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">%s</svg>',
        $size, $size, $paths[$name] ?? ''
    );
}

function whatsapp_url(string $phone, string $text = ''): string
{
    return 'https://wa.me/' . preg_replace('/\D/', '', $phone) . ($text !== '' ? '?text=' . rawurlencode($text) : '');
}

function display_phone(string $phone): string
{
    // 60123456701 -> +60 12-345 6701
    return preg_match('/^60(\d{2})(\d{3})(\d{4})$/', $phone, $m) ? "+60 {$m[1]}-{$m[2]} {$m[3]}" : $phone;
}

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

function partial(string $view, array $vars = []): string
{
    global $site;
    extract($vars + ['site' => $site]);
    ob_start();
    require template_path($site['theme'], $view);
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

/** Placeholder car illustration tinted with the listing's paint colour. */
function car_svg(array $car): string
{
    $paint = preg_match('/^#[0-9a-f]{6}$/i', $car['paint']) ? $car['paint'] : '#999999';
    $label = e($car['make'] . ' ' . $car['model']);
    $tall  = in_array($car['body'], ['SUV', 'Pickup'], true);
    $roof  = $tall ? 'M44 44 Q52 18 78 16 L136 16 Q154 17 166 44 Z' : 'M50 46 Q64 24 88 22 L128 22 Q148 24 162 46 Z';
    $glass = $tall ? 'M56 42 Q62 24 80 22 L104 22 L104 42 Z M110 22 L134 22 Q148 24 156 42 L110 42 Z'
                   : 'M62 44 Q72 29 90 28 L106 28 L106 44 Z M112 28 L126 28 Q142 30 150 44 L112 44 Z';
    return <<<SVG
    <svg class="car-art" viewBox="0 0 220 96" role="img" aria-label="{$label}">
      <ellipse cx="110" cy="84" rx="92" ry="6" fill="rgba(0,0,0,.18)"/>
      <path d="{$roof}" fill="{$paint}"/>
      <path d="M18 70 Q18 50 40 46 L180 44 Q202 46 204 64 L204 72 Q204 76 198 76 L24 76 Q18 76 18 70 Z" fill="{$paint}"/>
      <path d="{$glass}" fill="rgba(20,30,45,.55)"/>
      <rect x="186" y="54" width="14" height="5" rx="2" fill="#ffe9a8"/>
      <rect x="20" y="54" width="10" height="5" rx="2" fill="#e04545"/>
      <circle cx="62" cy="76" r="14" fill="#1b1b1b"/><circle cx="62" cy="76" r="6" fill="#bfc3c9"/>
      <circle cx="160" cy="76" r="14" fill="#1b1b1b"/><circle cx="160" cy="76" r="6" fill="#bfc3c9"/>
    </svg>
    SVG;
}

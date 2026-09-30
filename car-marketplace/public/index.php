<?php
declare(strict_types=1);

require __DIR__ . '/../src/bootstrap.php';

// Let PHP's built-in dev server serve static files (css, images) directly.
if (PHP_SAPI === 'cli-server') {
    $file = __DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if (is_file($file)) {
        return false;
    }
}

session_start();

$site   = resolve_site($_SERVER['HTTP_HOST'] ?? 'localhost', $_GET['broker'] ?? null);
$path   = rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/', '/') ?: '/';
$method = $_SERVER['REQUEST_METHOD'];

if ($site === null) {
    $site = main_site();
    render('404', ['title' => 'Showroom not found', 'message' => 'There is no broker at this address.'], 404);
    return;
}

/* ------------------------------------------------------ Main marketplace */
if ($site['is_main']) {
    if ($path === '/' && $method === 'GET') {
        $filters = [
            'make'  => (string) ($_GET['make'] ?? ''),
            'body'  => (string) ($_GET['body'] ?? ''),
            'max'   => (int) ($_GET['max'] ?? 0),
        ];
        $list = array_filter(cars(), fn ($c) =>
            ($filters['make'] === '' || $c['make'] === $filters['make']) &&
            ($filters['body'] === '' || $c['body'] === $filters['body']) &&
            ($filters['max'] === 0   || $c['price'] <= $filters['max'])
        );
        usort($list, fn ($a, $b) => $a['price'] <=> $b['price']);

        render('home', [
            'title'   => config('app')['name'] . ' | ' . config('app')['tagline'],
            'cars'    => $list,
            'filters' => $filters,
            'makes'   => array_values(array_unique(array_column(cars(), 'make'))),
            'bodies'  => array_values(array_unique(array_column(cars(), 'body'))),
            'brokers' => config('brokers'),
        ]);
        return;
    }

    // Car pages live on the broker's own subdomain; send visitors there.
    if (preg_match('#^/car/([a-z0-9-]+)$#', $path, $m) && ($car = find_car($m[1]))) {
        header('Location: ' . site_url($car['broker'], "/car/{$car['id']}"), true, 302);
        return;
    }

    render('404', ['title' => 'Not found', 'message' => 'That page does not exist.'], 404);
    return;
}

/* ------------------------------------------------------- Broker subdomain */
$broker = $site['broker'];

if ($path === '/' && $method === 'GET') {
    render('home', ['title' => $broker['name'], 'cars' => cars($site['slug'])]);
    return;
}

if (preg_match('#^/car/([a-z0-9-]+)$#', $path, $m) && $method === 'GET') {
    $car = find_car($m[1], $site['slug']);
    if (!$car) {
        render('404', ['title' => 'Car not found', 'message' => 'This showroom does not carry that model.'], 404);
        return;
    }
    render('car', ['title' => "{$car['make']} {$car['model']} {$car['variant']} | {$broker['name']}", 'car' => $car]);
    return;
}

if ($path === '/enquiry' && $method === 'POST') {
    $car   = find_car((string) ($_POST['car_id'] ?? ''), $site['slug']);
    $name  = trim((string) ($_POST['name'] ?? ''));
    $phone = preg_replace('/[^0-9+]/', '', (string) ($_POST['phone'] ?? ''));

    if (!hash_equals(csrf_token(), (string) ($_POST['csrf'] ?? '')) || !$car || $name === '' || strlen($phone) < 9) {
        render('car', [
            'title' => $broker['name'],
            'car'   => $car ?? cars($site['slug'])[0],
            'error' => 'Please enter your name and a valid phone number.',
        ], 422);
        return;
    }

    save_lead([
        'broker'  => $site['slug'],
        'car_id'  => $car['id'],
        'name'    => mb_substr($name, 0, 100),
        'phone'   => $phone,
        'message' => mb_substr(trim((string) ($_POST['message'] ?? '')), 0, 1000),
    ]);
    header('Location: /thanks?car=' . urlencode($car['id']), true, 303);
    return;
}

if ($path === '/thanks' && $method === 'GET') {
    render('thanks', ['title' => 'Thank you | ' . $broker['name'], 'car' => find_car((string) ($_GET['car'] ?? ''), $site['slug'])]);
    return;
}

render('404', ['title' => 'Not found', 'message' => 'That page does not exist.'], 404);

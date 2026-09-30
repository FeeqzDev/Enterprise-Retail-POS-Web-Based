<?php
declare(strict_types=1);

require __DIR__ . '/../src/bootstrap.php';

// Let PHP's built-in dev server serve static files (css, js, images) directly.
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

$app    = config('app');
$broker = $site['broker'];
// The main site sells every broker's stock; a broker site only its own.
$pool   = $site['is_main'] ? cars() : cars($site['slug']);
$name   = $broker['name'] ?? $app['name'];

/* ------------------------------------------------------------------ Pages */

if ($path === '/' && $method === 'GET') {
    render('home', [
        'title'    => $site['is_main'] ? "{$app['name']} | {$app['tagline']}" : "{$name} | New {$broker['makes'][0]} cars in {$broker['area']}",
        'cars'     => $pool,
        'featured' => array_values(array_filter($pool, fn ($c) => $c['featured'])),
        'brokers'  => config('brokers'),
    ]);
    return;
}

if ($path === '/cars' && $method === 'GET') {
    $filters = listing_filters($_GET, $pool);
    $results = filter_cars($pool, $filters);
    $pages   = max(1, (int) ceil(count($results) / PER_PAGE));
    $page    = min($pages, max(1, (int) ($_GET['page'] ?? 1)));

    render('cars', [
        'title'   => "New cars for sale | {$name}",
        'filters' => $filters,
        'total'   => count($results),
        'results' => array_slice($results, ($page - 1) * PER_PAGE, PER_PAGE),
        'page'    => $page,
        'pages'   => $pages,
        'options' => [
            'make' => array_values(array_unique(array_column($pool, 'make'))),
            'body' => array_values(array_unique(array_column($pool, 'body'))),
            'fuel' => array_values(array_unique(array_column($pool, 'fuel'))),
        ],
    ]);
    return;
}

if (preg_match('#^/car/([a-z0-9-]+)$#', $path, $m) && $method === 'GET') {
    $car = find_car($m[1], $site['slug']);
    if (!$car) {
        render('404', ['title' => 'Car not found', 'message' => 'This car is not available here.'], 404);
        return;
    }
    render('car', [
        'title'   => car_title($car) . " for sale | {$name}",
        'car'     => $car,
        'seller'  => config('brokers')[$car['broker']],
        'similar' => similar_cars($car, $pool),
    ]);
    return;
}

if ($path === '/loan-calculator' && $method === 'GET') {
    render('loan', ['title' => "Car loan calculator | {$name}", 'car' => find_car((string) ($_GET['car'] ?? ''), $site['slug'])]);
    return;
}

if ($path === '/brokers' && $site['is_main'] && $method === 'GET') {
    render('brokers', ['title' => "Official brokers | {$app['name']}", 'brokers' => config('brokers')]);
    return;
}

if ($path === '/about' && !$site['is_main'] && $method === 'GET') {
    render('about', ['title' => "About us | {$name}", 'cars' => $pool]);
    return;
}

/* ---------------------------------------------------------------- Leads */

if ($path === '/enquiry' && $method === 'POST') {
    $car   = find_car((string) ($_POST['car_id'] ?? ''), $site['slug']);
    $leadName  = trim((string) ($_POST['name'] ?? ''));
    $phone = preg_replace('/[^0-9+]/', '', (string) ($_POST['phone'] ?? ''));
    $type  = in_array($_POST['type'] ?? '', ['test_drive', 'quote', 'loan'], true) ? $_POST['type'] : 'quote';

    if (!$car) {
        render('404', ['title' => 'Car not found', 'message' => 'This car is not available here.'], 404);
        return;
    }
    if (!hash_equals(csrf_token(), (string) ($_POST['csrf'] ?? '')) || $leadName === '' || strlen($phone) < 9) {
        render('car', [
            'title'   => car_title($car) . " | {$name}",
            'car'     => $car,
            'seller'  => config('brokers')[$car['broker']],
            'similar' => similar_cars($car, $pool),
            'error'   => 'Please enter your name and a valid phone number.',
        ], 422);
        return;
    }

    save_lead([
        'broker'  => $car['broker'],           // always routed to the selling broker
        'source'  => $site['is_main'] ? 'marketplace' : 'broker_site',
        'type'    => $type,
        'car_id'  => $car['id'],
        'name'    => mb_substr($leadName, 0, 100),
        'phone'   => $phone,
        'message' => mb_substr(trim((string) ($_POST['message'] ?? '')), 0, 1000),
    ]);
    header('Location: /thanks?car=' . urlencode($car['id']), true, 303);
    return;
}

if ($path === '/thanks' && $method === 'GET') {
    $car = find_car((string) ($_GET['car'] ?? ''), $site['slug']);
    render('thanks', [
        'title'  => "Thank you | {$name}",
        'car'    => $car,
        'seller' => $car ? config('brokers')[$car['broker']] : $broker,
    ]);
    return;
}

render('404', ['title' => 'Not found', 'message' => 'That page does not exist.'], 404);

<?php
/*
 * Broker (tenant) registry. Every key becomes a subdomain: toyota.<base_domain>.
 *
 * Design options per broker:
 *   theme         -> folder in /themes (templates) + /public/themes/<theme>.css (styling).
 *                    Missing templates fall back to /themes/base, so a theme can be
 *                    "CSS only" or override whole pages.
 *   colors        -> injected as CSS variables, so brokers sharing a theme still look like their own brand.
 *   custom_domain -> optional. Point the broker's own domain at this server and it resolves too.
 *
 * In production this would be a `brokers` table; the shape stays the same.
 */
return [
    'toyota' => [
        'name'     => 'Prestige Toyota Centre',
        'short'    => 'Prestige Toyota',
        'makes'    => ['Toyota'],
        'tagline'  => 'Drive the legend. Official Toyota pricing, fast approval.',
        'about'    => 'Prestige Toyota Centre has served Klang Valley drivers since 2004. Our 3S centre handles sales, service and genuine spare parts, and our finance desk works with seven banks to get you the best rate.',
        'phone'    => '60123456701',
        'email'    => 'sales@prestige-toyota.example',
        'address'  => 'Lot 12, Jalan Ipoh, 51200 Kuala Lumpur',
        'area'     => 'Kuala Lumpur',
        'hours'    => 'Mon–Sat 9am–7pm · Sun 10am–5pm',
        'rating'   => 4.8, 'reviews' => 1240, 'since' => 2004,
        'theme'    => 'showroom',
        'colors'   => ['primary' => '#e0101b', 'accent' => '#ffffff'],
        'custom_domain' => null,
    ],
    'proton' => [
        'name'     => 'Nusantara Proton Hub',
        'short'    => 'Nusantara Proton',
        'makes'    => ['Proton'],
        'tagline'  => 'Malaysian pride, modern drive.',
        'about'    => 'An authorised Proton 4S centre in Shah Alam with a 40-car indoor showroom, same-day loan submission and a dedicated after-sales team.',
        'phone'    => '60123456702',
        'email'    => 'hello@nusantara-proton.example',
        'address'  => 'Persiaran Kayangan, Seksyen 9, 40100 Shah Alam, Selangor',
        'area'     => 'Selangor',
        'hours'    => 'Daily 9am–6pm',
        'rating'   => 4.7, 'reviews' => 860, 'since' => 2011,
        'theme'    => 'sleek',
        'colors'   => ['primary' => '#0a2d6e', 'accent' => '#c8a24a'],
        'custom_domain' => null,
    ],
    'perodua' => [
        'name'     => 'Ceria Perodua Sales',
        'short'    => 'Ceria Perodua',
        'makes'    => ['Perodua'],
        'tagline'  => 'Your first car starts here. Low deposit, friendly service!',
        'about'    => 'We help first-time buyers every day: fresh graduates, young families and e-hailing drivers. Low deposit packages, loan help for all income types and fast delivery.',
        'phone'    => '60123456703',
        'email'    => 'ceria@perodua-sales.example',
        'address'  => 'Jalan Tebrau, 80250 Johor Bahru, Johor',
        'area'     => 'Johor',
        'hours'    => 'Daily 9am–9pm',
        'rating'   => 4.9, 'reviews' => 2310, 'since' => 2015,
        'theme'    => 'friendly',
        'colors'   => ['primary' => '#00a651', 'accent' => '#ffc20e'],
        'custom_domain' => null,
    ],
    // No custom design requested: uses the default "base" theme with their brand colour.
    'honda' => [
        'name'     => 'Metro Honda Dealer',
        'short'    => 'Metro Honda',
        'makes'    => ['Honda'],
        'tagline'  => 'The power of dreams, at Metro prices.',
        'about'    => 'Metro Honda is a family-run dealer on Penang island offering the full Honda range, trade-ins and in-house insurance.',
        'phone'    => '60123456704',
        'email'    => 'sales@metro-honda.example',
        'address'  => 'Jalan Sultan Azlan Shah, 11900 Bayan Lepas, Penang',
        'area'     => 'Penang',
        'hours'    => 'Mon–Sat 9am–6pm',
        'rating'   => 4.6, 'reviews' => 540, 'since' => 2009,
        'theme'    => 'base',
        'colors'   => ['primary' => '#cc0000', 'accent' => '#111827'],
        'custom_domain' => null,
    ],
];

<?php
/*
 * Broker (tenant) registry. Every key becomes a subdomain: toyota.<base_domain>.
 *
 * Design options per broker:
 *   theme        -> folder in /themes (templates) + /public/themes/<theme>.css (styling).
 *                   Missing templates fall back to /themes/base, so a theme can be
 *                   "CSS only" or override whole pages.
 *   colors       -> injected as CSS variables, so two brokers can share a theme
 *                   but still look like their own brand.
 *   custom_domain-> optional. Point the broker's own domain at this server and it resolves too.
 *
 * In production this would be a `brokers` table; the shape stays the same.
 */
return [
    'toyota' => [
        'name'     => 'Prestige Toyota Centre',
        'makes'    => ['Toyota'],
        'tagline'  => 'Drive the legend. Official Toyota pricing, fast approval.',
        'phone'    => '60123456701',
        'email'    => 'sales@prestige-toyota.example',
        'address'  => 'Jalan Ipoh, Kuala Lumpur',
        'theme'    => 'showroom',
        'colors'   => ['primary' => '#d71920', 'accent' => '#111111', 'bg' => '#0d0d0f', 'text' => '#f4f4f5'],
        'custom_domain' => null,
    ],
    'proton' => [
        'name'     => 'Nusantara Proton Hub',
        'makes'    => ['Proton'],
        'tagline'  => 'Malaysian pride, modern drive.',
        'phone'    => '60123456702',
        'email'    => 'hello@nusantara-proton.example',
        'address'  => 'Shah Alam, Selangor',
        'theme'    => 'sleek',
        'colors'   => ['primary' => '#0a2d6e', 'accent' => '#c8a24a', 'bg' => '#f5f7fb', 'text' => '#0f172a'],
        'custom_domain' => null,
    ],
    'perodua' => [
        'name'     => 'Ceria Perodua Sales',
        'makes'    => ['Perodua'],
        'tagline'  => 'Your first car starts here. Low deposit, friendly service!',
        'phone'    => '60123456703',
        'email'    => 'ceria@perodua-sales.example',
        'address'  => 'Johor Bahru, Johor',
        'theme'    => 'friendly',
        'colors'   => ['primary' => '#00a651', 'accent' => '#ffc20e', 'bg' => '#fffdf5', 'text' => '#1f2937'],
        'custom_domain' => null,
    ],
    // No custom design requested: uses the default "base" theme with their brand colour.
    'honda' => [
        'name'     => 'Metro Honda Dealer',
        'makes'    => ['Honda'],
        'tagline'  => 'The power of dreams, at Metro prices.',
        'phone'    => '60123456704',
        'email'    => 'sales@metro-honda.example',
        'address'  => 'Penang',
        'theme'    => 'base',
        'colors'   => ['primary' => '#cc0000', 'accent' => '#1f2937', 'bg' => '#ffffff', 'text' => '#111827'],
        'custom_domain' => null,
    ],
];

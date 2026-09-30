<?php
// Global settings for the marketplace.
return [
    'name'        => 'KeretaKu',
    'tagline'     => 'New cars from trusted brokers across Malaysia',

    // Root domain. Main site lives at this host, brokers at <slug>.<base_domain>.
    // For local dev, "localhost" works because browsers resolve *.localhost to 127.0.0.1.
    'base_domain' => getenv('BASE_DOMAIN') ?: 'localhost',

    // When true, ?broker=<slug> can force a tenant (handy if your browser can't use *.localhost).
    'debug'       => (getenv('APP_DEBUG') ?: '1') === '1',

    // Simple loan estimate shown on listings.
    'loan' => ['margin' => 0.90, 'years' => 9, 'flat_rate' => 0.03],
];

<?php
// Sample inventory. `broker` must match a key in config/brokers.php.
// `paint` is only used to colour the placeholder illustration; swap for real photos later.
return [
    ['id' => 'toyota-vios-15g',    'broker' => 'toyota',  'make' => 'Toyota',  'model' => 'Vios',      'variant' => '1.5G',              'year' => 2026, 'price' => 95500,  'body' => 'Sedan', 'fuel' => 'Petrol', 'transmission' => 'CVT', 'seats' => 5, 'paint' => '#b8bcc4', 'featured' => true],
    ['id' => 'toyota-yaris-cross', 'broker' => 'toyota',  'make' => 'Toyota',  'model' => 'Yaris Cross','variant' => '1.5 Hybrid',       'year' => 2026, 'price' => 125000, 'body' => 'SUV',   'fuel' => 'Hybrid', 'transmission' => 'e-CVT', 'seats' => 5, 'paint' => '#8a1c1c', 'featured' => false],
    ['id' => 'toyota-corolla-cross','broker' => 'toyota', 'make' => 'Toyota',  'model' => 'Corolla Cross','variant' => '1.8V',            'year' => 2026, 'price' => 138000, 'body' => 'SUV',   'fuel' => 'Petrol', 'transmission' => 'CVT', 'seats' => 5, 'paint' => '#f2f2f2', 'featured' => true],
    ['id' => 'toyota-hilux',       'broker' => 'toyota',  'make' => 'Toyota',  'model' => 'Hilux',     'variant' => '2.8 Rogue',         'year' => 2026, 'price' => 162000, 'body' => 'Pickup','fuel' => 'Diesel', 'transmission' => 'AT',  'seats' => 5, 'paint' => '#2b2b2b', 'featured' => false],

    ['id' => 'proton-saga-premium','broker' => 'proton',  'make' => 'Proton',  'model' => 'Saga',      'variant' => '1.3 Premium AT',    'year' => 2026, 'price' => 43000,  'body' => 'Sedan', 'fuel' => 'Petrol', 'transmission' => 'AT',  'seats' => 5, 'paint' => '#c7ccd4', 'featured' => true],
    ['id' => 'proton-s70-flagship','broker' => 'proton',  'make' => 'Proton',  'model' => 'S70',       'variant' => '1.5T Flagship',     'year' => 2026, 'price' => 94800,  'body' => 'Sedan', 'fuel' => 'Petrol', 'transmission' => 'DCT', 'seats' => 5, 'paint' => '#1c3f8a', 'featured' => false],
    ['id' => 'proton-x50-flagship','broker' => 'proton',  'make' => 'Proton',  'model' => 'X50',       'variant' => '1.5TGDi Flagship',  'year' => 2026, 'price' => 113300, 'body' => 'SUV',   'fuel' => 'Petrol', 'transmission' => 'DCT', 'seats' => 5, 'paint' => '#d9d9d9', 'featured' => true],
    ['id' => 'proton-x70-premium', 'broker' => 'proton',  'make' => 'Proton',  'model' => 'X70',       'variant' => '1.5TGDi Premium',   'year' => 2026, 'price' => 127800, 'body' => 'SUV',   'fuel' => 'Petrol', 'transmission' => 'DCT', 'seats' => 5, 'paint' => '#3a3a3a', 'featured' => false],

    ['id' => 'perodua-axia-av',    'broker' => 'perodua', 'make' => 'Perodua', 'model' => 'Axia',      'variant' => '1.0 AV',            'year' => 2026, 'price' => 49500,  'body' => 'Hatchback','fuel' => 'Petrol', 'transmission' => 'D-CVT', 'seats' => 5, 'paint' => '#e8553d', 'featured' => true],
    ['id' => 'perodua-myvi-av',    'broker' => 'perodua', 'make' => 'Perodua', 'model' => 'Myvi',      'variant' => '1.5 AV',            'year' => 2026, 'price' => 59900,  'body' => 'Hatchback','fuel' => 'Petrol', 'transmission' => 'D-CVT', 'seats' => 5, 'paint' => '#2f7fd6', 'featured' => true],
    ['id' => 'perodua-bezza-av',   'broker' => 'perodua', 'make' => 'Perodua', 'model' => 'Bezza',     'variant' => '1.3 AV',            'year' => 2026, 'price' => 49980,  'body' => 'Sedan', 'fuel' => 'Petrol', 'transmission' => 'D-CVT', 'seats' => 5, 'paint' => '#f5f5f5', 'featured' => false],
    ['id' => 'perodua-ativa-av',   'broker' => 'perodua', 'make' => 'Perodua', 'model' => 'Ativa',     'variant' => '1.0T AV',           'year' => 2026, 'price' => 73400,  'body' => 'SUV',   'fuel' => 'Petrol', 'transmission' => 'D-CVT', 'seats' => 5, 'paint' => '#6b8e23', 'featured' => false],

    ['id' => 'honda-city-v',       'broker' => 'honda',   'make' => 'Honda',   'model' => 'City',      'variant' => '1.5 V',             'year' => 2026, 'price' => 98000,  'body' => 'Sedan', 'fuel' => 'Petrol', 'transmission' => 'CVT', 'seats' => 5, 'paint' => '#9ea3aa', 'featured' => true],
    ['id' => 'honda-hrv-rs',       'broker' => 'honda',   'make' => 'Honda',   'model' => 'HR-V',      'variant' => '1.5 e:HEV RS',      'year' => 2026, 'price' => 150000, 'body' => 'SUV',   'fuel' => 'Hybrid', 'transmission' => 'e-CVT', 'seats' => 5, 'paint' => '#7a1f2b', 'featured' => false],
];

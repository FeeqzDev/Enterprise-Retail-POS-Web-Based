<?php /** Shared <head>. Theme CSS loads after base.css; broker colours load last so they win. */ ?>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title ?? config('app')['name']) ?></title>
<meta name="description" content="<?= e($site['broker']['tagline'] ?? config('app')['tagline']) ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Playfair+Display:wght@600;700&display=swap">
<link rel="stylesheet" href="/themes/base.css">
<?php if ($site['theme'] !== 'base'): ?>
<link rel="stylesheet" href="/themes/<?= e($site['theme']) ?>.css">
<?php endif; ?>
<?php if (!empty($site['broker']['colors'])): ?>
<style>:root{<?php foreach ($site['broker']['colors'] as $k => $v): ?>--<?= e($k) ?>:<?= e($v) ?>;<?php endforeach; ?>}</style>
<?php endif; ?>
<script src="/app.js" defer></script>

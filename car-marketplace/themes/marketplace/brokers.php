<?php /** @var array $brokers */ ?>
<section class="page-head">
  <div class="wrap">
    <nav class="crumbs"><a href="/">Home</a> / <span>Brokers</span></nav>
    <h1>Official brokers</h1>
    <p class="page-sub">Every broker is verified, sells at official prices and has their own showroom website.</p>
  </div>
</section>
<div class="wrap section-tight"><?= partial('broker_grid', ['brokers' => $brokers]) ?></div>

<section class="wrap section" id="join">
  <div class="join card">
    <div>
      <span class="eyebrow">For car brokers</span>
      <h2>Get your own showroom website on <?= e(config('app')['name']) ?></h2>
      <ul class="feature-list">
        <li><?= icon('check', 16) ?> Your own subdomain, or connect your own domain</li>
        <li><?= icon('check', 16) ?> Custom design with your brand colours and layout</li>
        <li><?= icon('check', 16) ?> Listed on the main marketplace to reach more buyers</li>
        <li><?= icon('check', 16) ?> Leads straight to your WhatsApp and dashboard</li>
      </ul>
    </div>
    <div class="join-cta">
      <strong>Talk to our partnership team</strong>
      <p>Set-up in 3 working days.</p>
      <a class="btn btn-primary btn-lg btn-block" href="mailto:partners@keretaku.example?subject=Broker%20partnership">Apply as a broker</a>
    </div>
  </div>
</section>

<?php /** @var ?array $car */ ?>
<section class="page-head">
  <div class="wrap">
    <nav class="crumbs"><a href="/">Home</a> / <span>Loan calculator</span></nav>
    <h1>Car loan calculator</h1>
    <p class="page-sub">See your monthly instalment before you visit the showroom.</p>
  </div>
</section>
<div class="wrap section-tight">
  <div class="card"><?= partial('loan_widget', ['price' => $car['price'] ?? 80000]) ?></div>
  <div class="loan-tips">
    <div class="card"><h3>Flat rate vs effective rate</h3><p>Malaysian car loans use a flat rate on the original loan amount. A 3% flat rate is roughly 5.5% effective.</p></div>
    <div class="card"><h3>What you need</h3><p>IC, driving licence, last 3 months' payslips and bank statements, and EPF statement. Self-employed buyers also need SSM and 6 months' statements.</p></div>
    <div class="card"><h3>Approval in 24 hours</h3><p>Brokers on <?= e(config('app')['name']) ?> submit to several banks at once so you get the best rate quickly.</p></div>
  </div>
</div>

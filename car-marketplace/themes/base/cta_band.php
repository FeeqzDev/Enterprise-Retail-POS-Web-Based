<?php $b = $site['broker']; ?>
<section class="cta-band">
  <div class="wrap cta-band-inner">
    <div>
      <h2>Not sure which model fits your budget?</h2>
      <p>Our sales advisors reply on WhatsApp within minutes<?= $b ? ', ' . e($b['hours']) : '' ?>.</p>
    </div>
    <div class="cta-actions">
      <a class="btn btn-light btn-lg" href="/loan-calculator">Calculate monthly</a>
      <?php if ($b): ?>
        <a class="btn btn-primary btn-lg" href="<?= e(whatsapp_url($b['phone'])) ?>" target="_blank" rel="noopener"><?= icon('chat', 18) ?> Chat with us</a>
      <?php endif; ?>
    </div>
  </div>
</section>

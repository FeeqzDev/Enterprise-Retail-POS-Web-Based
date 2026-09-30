<?php
/** Interactive loan calculator (flat rate, as used by Malaysian hire-purchase loans). @var int $price */
$loan = config('app')['loan'];
?>
<div class="loan" data-loan>
  <div class="loan-inputs">
    <label>Car price (RM)
      <input type="number" min="10000" step="100" value="<?= (int) $price ?>" data-loan-price>
    </label>
    <label>Down payment <output data-loan-dp-out><?= 100 - (int) ($loan['margin'] * 100) ?>%</output>
      <input type="range" min="0" max="50" step="5" value="<?= 100 - (int) ($loan['margin'] * 100) ?>" data-loan-dp>
    </label>
    <label>Loan period <output data-loan-years-out><?= e($loan['years']) ?> years</output>
      <input type="range" min="1" max="9" step="1" value="<?= e($loan['years']) ?>" data-loan-years>
    </label>
    <label>Interest rate (flat, % p.a.)
      <input type="number" min="0" max="10" step="0.05" value="<?= e($loan['flat_rate'] * 100) ?>" data-loan-rate>
    </label>
  </div>
  <div class="loan-result">
    <span>Monthly instalment</span>
    <strong data-loan-monthly><?= rm(monthly_installment($price)) ?></strong>
    <dl>
      <dt>Down payment</dt><dd data-loan-dp-amt>–</dd>
      <dt>Loan amount</dt><dd data-loan-amount>–</dd>
      <dt>Total interest</dt><dd data-loan-interest>–</dd>
    </dl>
    <small>Estimate only. Final rate depends on bank approval.</small>
  </div>
</div>

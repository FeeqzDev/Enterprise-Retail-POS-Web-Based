// Progressive enhancement only: every page works without JS.
(() => {
  const rm = (n) => 'RM ' + Math.round(n).toLocaleString('en-MY');

  // Mobile menu
  document.querySelectorAll('[data-nav-toggle]').forEach((btn) => {
    btn.addEventListener('click', () => document.querySelector('[data-nav]')?.classList.toggle('open'));
  });

  // Listing filters apply immediately when a checkbox or select changes
  document.querySelectorAll('form[data-autosubmit]').forEach((form) => {
    form.addEventListener('change', (e) => {
      if (e.target.matches('input[type=checkbox], select')) form.submit();
    });
  });

  // Gallery: thumbnails swap the main photo
  document.querySelectorAll('[data-gallery]').forEach((gallery) => {
    const stage = gallery.querySelector('.gallery-stage');
    gallery.querySelectorAll('.gallery-thumbs button').forEach((thumb) => {
      thumb.addEventListener('click', () => {
        gallery.querySelectorAll('.gallery-thumbs button').forEach((t) => t.classList.remove('active'));
        thumb.classList.add('active');
        let img = stage.querySelector('img');
        if (!img) {
          img = document.createElement('img');
          img.onerror = () => { stage.classList.add('no-photo'); img.remove(); };
          stage.prepend(img);
        }
        stage.classList.remove('no-photo');
        img.src = thumb.dataset.full;
      });
    });
  });

  // Loan calculator (flat-rate hire purchase, standard in Malaysia)
  document.querySelectorAll('[data-loan]').forEach((el) => {
    const $ = (s) => el.querySelector(s);
    const update = () => {
      const price = Math.max(0, +$('[data-loan-price]').value || 0);
      const dpPct = +$('[data-loan-dp]').value;
      const years = +$('[data-loan-years]').value;
      const rate = Math.max(0, +$('[data-loan-rate]').value || 0) / 100;
      const down = price * dpPct / 100;
      const loan = price - down;
      const interest = loan * rate * years;
      $('[data-loan-dp-out]').textContent = dpPct + '%';
      $('[data-loan-years-out]').textContent = years + (years === 1 ? ' year' : ' years');
      $('[data-loan-monthly]').textContent = rm((loan + interest) / (years * 12));
      $('[data-loan-dp-amt]').textContent = rm(down);
      $('[data-loan-amount]').textContent = rm(loan);
      $('[data-loan-interest]').textContent = rm(interest);
    };
    el.addEventListener('input', update);
    update();
  });
})();

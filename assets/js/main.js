/* Recipe Sharing Portal | interactions */
(function () {
  'use strict';
  var $ = function (s, r) { return (r || document).querySelector(s); };
  var $$ = function (s, r) { return Array.prototype.slice.call((r || document).querySelectorAll(s)); };

  /* Mobile menu */
  var toggle = $('.nav-toggle'), links = $('#nav-links');
  if (toggle && links) {
    toggle.addEventListener('click', function () {
      var open = links.classList.toggle('open');
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
  }

  /* Confirm before delete */
  $$('form[data-confirm]').forEach(function (f) {
    f.addEventListener('submit', function (e) {
      if (!window.confirm(f.getAttribute('data-confirm'))) { e.preventDefault(); }
    });
  });

  /* Print */
  $$('[data-print]').forEach(function (b) { b.addEventListener('click', function () { window.print(); }); });

  /* Method steps: tap to mark done */
  $$('.method-steps li').forEach(function (li) {
    var flip = function () { li.classList.toggle('done'); };
    li.addEventListener('click', flip);
    li.addEventListener('keydown', function (e) {
      if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); flip(); }
    });
  });

  /* ---------- Login / sign-up ---------- */
  var auth = $('.auth');
  if (!auth) { return; }

  function setMode(mode, push) {
    auth.setAttribute('data-mode', mode);
    $$('.tabs button').forEach(function (b) {
      b.setAttribute('aria-selected', b.getAttribute('data-mode') === mode ? 'true' : 'false');
    });
    document.title = (mode === 'login' ? 'Log in' : 'Create account') + ' | Recipe Sharing Portal';
    if (push && window.history && history.replaceState) {
      history.replaceState(null, '', mode === 'login' ? 'login.php' : 'register.php');
    }
    var first = $('[data-panel="' + mode + '"] input:not([type=hidden])');
    if (first && push) { setTimeout(function () { first.focus(); }, 120); }
  }
  $$('.tabs button').forEach(function (b) {
    b.addEventListener('click', function () { setMode(b.getAttribute('data-mode'), true); });
  });
  $$('[data-go]').forEach(function (b) {
    b.addEventListener('click', function () { setMode(b.getAttribute('data-go'), true); });
  });

  /* Show / hide password */
  $$('[data-toggle-pw]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var input = btn.parentNode.querySelector('input');
      var show = input.type === 'password';
      input.type = show ? 'text' : 'password';
      btn.textContent = show ? 'Hide' : 'Show';
      btn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
    });
  });

  /* Demo account */
  var demo = $('[data-demo]');
  if (demo) {
    demo.addEventListener('click', function () {
      $('#login_id').value = 'demo_kitchen';
      $('#login_pw').value = 'Demo@123';
      $$('[data-panel=login] .field').forEach(function (f) { f.classList.remove('bad'); });
    });
  }

  /* Inline validation */
  function fieldOf(input) { return input.closest('.field'); }
  function setError(input, msg) {
    var f = fieldOf(input), e = f.querySelector('.err');
    e.textContent = msg || '';
    f.classList.toggle('bad', !!msg);
    f.classList.toggle('ok', !msg && input.value !== '');
    input.setAttribute('aria-invalid', msg ? 'true' : 'false');
    return !msg;
  }
  function check(input) {
    var v = input.value.trim(), id = input.id;
    if (id === 'login_id') { return setError(input, v ? '' : 'Enter your username or email.'); }
    if (id === 'login_pw') { return setError(input, input.value ? '' : 'Enter your password.'); }
    if (id === 'r_user')   { return setError(input, /^[A-Za-z0-9_]{3,20}$/.test(v) ? '' : '3 to 20 characters: letters, numbers or underscores.'); }
    if (id === 'r_email')  { return setError(input, /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v) ? '' : 'Enter a valid email, like name@example.com.'); }
    if (id === 'r_pw')     { return setError(input, (input.value.length >= 8 && /[A-Za-z]/.test(input.value) && /\d/.test(input.value)) ? '' : 'Use 8+ characters with a letter and a number.'); }
    if (id === 'r_pw2')    { return setError(input, input.value && input.value === $('#r_pw').value ? '' : 'The passwords do not match.'); }
    return true;
  }
  $$('.form-panel input:not([type=hidden])').forEach(function (input) {
    input.addEventListener('blur', function () { if (input.value !== '') { check(input); } });
    input.addEventListener('input', function () { if (fieldOf(input).classList.contains('bad')) { check(input); } });
  });

  /* Password strength meter */
  var pw = $('#r_pw'), meter = $('.meter');
  if (pw && meter) {
    var note = $('em', meter);
    var labels = ['Use 8+ characters with a letter and a number', 'Weak: add more characters', 'Fair: add a number or symbol', 'Good', 'Strong'];
    pw.addEventListener('input', function () {
      var v = pw.value, s = 0;
      if (v.length >= 8) { s++; }
      if (/[A-Za-z]/.test(v) && /\d/.test(v)) { s++; }
      if (/[A-Z]/.test(v) && /[a-z]/.test(v)) { s++; }
      if (/[^A-Za-z0-9]/.test(v) || v.length >= 12) { s++; }
      if (!v) { s = 0; }
      meter.setAttribute('data-s', s);
      note.textContent = labels[s];
      var c = $('#r_pw2');
      if (c.value) { check(c); }
    });
  }

  /* Submit: validate, then show loading state */
  $$('.form-panel').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      var ok = true, firstBad = null;
      $$('input:not([type=hidden])', form).forEach(function (i) {
        if (!check(i)) { ok = false; firstBad = firstBad || i; }
      });
      if (!ok) {
        e.preventDefault();
        firstBad.focus();
        var card = $('.auth-card');
        card.classList.remove('shake'); void card.offsetWidth; card.classList.add('shake');
        return;
      }
      var btn = $('[data-loading]', form);
      btn.classList.add('is-loading');
      btn.textContent = btn.getAttribute('data-loading');
    });
  });

  /* Stage: pointer parallax and rotating dish names */
  var stage = $('#stage');
  var calm = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (stage && !calm) {
    stage.addEventListener('pointermove', function (e) {
      var r = stage.getBoundingClientRect();
      stage.style.setProperty('--mx', ((e.clientX - r.left) / r.width - .5).toFixed(3));
      stage.style.setProperty('--my', ((e.clientY - r.top) / r.height - .5).toFixed(3));
      $$('.plate-wrap', stage).forEach(function (p) {
        p.style.setProperty('--mx', stage.style.getPropertyValue('--mx'));
        p.style.setProperty('--my', stage.style.getPropertyValue('--my'));
      });
    });
  }
  var ticker = $('#ticker');
  if (ticker && !calm) {
    var dishes = ['Paneer Butter Masala', 'Mango Panna Cotta', 'Creamy Mushroom Pasta', 'Banana Oat Pancakes', 'Molten Chocolate Cake', 'Punjabi Chole', 'Mint Lime Soda', 'Veg Fried Rice'];
    var n = 0;
    setInterval(function () {
      ticker.classList.add('out');
      setTimeout(function () { n = (n + 1) % dishes.length; ticker.textContent = dishes[n]; ticker.classList.remove('out'); }, 300);
    }, 2600);
  }

  setMode(auth.getAttribute('data-mode'), false);
})();

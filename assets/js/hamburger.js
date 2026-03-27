/**
 * ハンバーガーメニュー：クリックでモバイルオーバーレイを開閉
 */
(function () {
  var hamburger = document.querySelector('.js-hamburger');
  var overlay = document.getElementById('mobile-nav');
  var body = document.body;

  if (!hamburger || !overlay) return;

  function open() {
    overlay.classList.add('is-open');
    overlay.setAttribute('aria-hidden', 'false');
    body.classList.add('is-mobile-nav-open');
    hamburger.classList.add('is-open');
    hamburger.setAttribute('aria-expanded', 'true');
    hamburger.setAttribute('aria-label', 'メニューを閉じる');
  }

  function close() {
    overlay.classList.remove('is-open');
    overlay.setAttribute('aria-hidden', 'true');
    body.classList.remove('is-mobile-nav-open');
    hamburger.classList.remove('is-open');
    hamburger.setAttribute('aria-expanded', 'false');
    hamburger.setAttribute('aria-label', 'メニューを開く');
  }

  function toggle() {
    if (overlay.classList.contains('is-open')) {
      close();
    } else {
      open();
    }
  }

  hamburger.addEventListener('click', toggle);

  overlay.addEventListener('click', function (e) {
    if (e.target === overlay) close();
  });

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && overlay.classList.contains('is-open')) close();
  });
})();

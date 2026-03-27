/**
 * アコーディオン（プラス/マイナスで子メニュー開閉）
 * モバイルナビ（#mobile-nav）とフッター（.l-footer__nav-wrap）の両方で動作
 */
(function () {
  document.addEventListener('click', function (e) {
    var toggle = e.target.closest('.js-mobile-nav-toggle, .js-footer-accordion-toggle');
    var link = e.target.closest('.p-mobile-nav__parent--toggle');
    var row = e.target.closest('.p-mobile-nav__parent-row');
    if (!toggle && !row) return;

    /* リンクを直接クリックした場合は遷移させる（トグルしない） */
    if (link && !toggle) return;

    row = row || toggle.closest('.p-mobile-nav__parent-row');
    if (!row) return;

    var column = row.closest('.p-mobile-nav__column');
    if (!column || !column.classList.contains('p-mobile-nav__column--has-children')) return;

    e.preventDefault();
    var children = row.nextElementSibling;
    while (children && !children.classList.contains('p-mobile-nav__children')) {
      children = children.nextElementSibling;
    }
    if (!children) return;

    var isOpen = children.classList.contains('is-open');

    var btn = row.querySelector('.js-mobile-nav-toggle, .js-footer-accordion-toggle');
    if (btn) {
      btn.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
      btn.setAttribute('aria-label', isOpen ? 'サブメニューを開く' : 'サブメニューを閉じる');
    }

    if (isOpen) {
      row.classList.remove('is-open');
      children.classList.remove('is-open');
    } else {
      row.classList.add('is-open');
      children.classList.add('is-open');
    }
  });
})();

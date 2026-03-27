/**
 * ページコンテンツのスクロールフェードイン
 * .page-content / .p-index 内のブロック要素がビューポートに入ったらふわっと表示
 * .hero-section は除外
 */
(function () {
    var selector = '.page-content .l-inner > *, .page-content .p-index > *, .p-index > *, .p-index .wp-block-group__inner-container > *, .page-content .p-recruit > *, .page-content .p-contact__inner > *, .page-content .wp-block-group__inner-container > *';
    var rootMargin = '0px 0px -40px 0px'; // 下から40px手前で発火
    var threshold = 0;

    function init() {
        var raw = document.querySelectorAll(selector);
        var seen = new Set();
        var elements = Array.prototype.filter.call(raw, function (el) {
            if (seen.has(el)) return false;
            seen.add(el);
            return !el.classList.contains('hero-section') &&
                   !el.classList.contains('hero-section-wrapper') &&
                   !el.closest('.hero-section');
        });
        if (elements.length === 0) return;

        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.setAttribute('data-js-scroll-visible', '');
                }
            });
        }, {
            root: null,
            rootMargin: rootMargin,
            threshold: threshold
        });

        elements.forEach(function (el) {
            el.setAttribute('data-js-scroll-fade-in', '');
            observer.observe(el);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();

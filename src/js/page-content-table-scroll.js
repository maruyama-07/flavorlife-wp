/**
 * .page-content 内の table を横スクロール用ラッパーで包む（主に SP）
 * オーバーフロー時のみヒント表示。スクロールでヒントを消す。
 */
(function () {
    var WRAP = 'u-table-scroll-wrap';
    var INNER = 'u-table-scroll-wrap__inner';
    var OVERFLOW = 'u-table-scroll-wrap--overflow';
    var END = 'u-table-scroll-wrap--end';
    var HINT = 'u-table-scroll-hint';
    var wrapList = [];
    var resizeTimer;

    function mqMobile() {
        return window.matchMedia('(max-width: 767px)').matches;
    }

    function wrapTable(table) {
        if (!table || table.closest('.' + WRAP)) {
            return null;
        }
        var parent = table.parentNode;
        if (!parent) {
            return null;
        }
        var wrap = document.createElement('div');
        wrap.className = WRAP;
        var inner = document.createElement('div');
        inner.className = INNER;
        parent.insertBefore(wrap, table);
        inner.appendChild(table);
        wrap.appendChild(inner);
        return wrap;
    }

    function updateOverflowState(wrap) {
        var inner = wrap.querySelector('.' + INNER);
        if (!inner) {
            return;
        }
        if (!mqMobile()) {
            wrap.classList.remove(OVERFLOW, END);
            var hintOff = wrap.querySelector('.' + HINT);
            if (hintOff) {
                hintOff.hidden = true;
            }
            return;
        }
        var canScroll = inner.scrollWidth > inner.clientWidth + 2;
        wrap.classList.toggle(OVERFLOW, canScroll);
        var atEnd = inner.scrollLeft >= inner.scrollWidth - inner.clientWidth - 2;
        wrap.classList.toggle(END, canScroll && atEnd);
        var hint = wrap.querySelector('.' + HINT);
        if (hint) {
            hint.hidden = !canScroll || hint.classList.contains('is-dismissed');
        }
    }

    function ensureHint(wrap) {
        if (wrap.querySelector('.' + HINT)) {
            return;
        }
        var p = document.createElement('p');
        p.className = HINT;
        p.setAttribute('aria-live', 'polite');
        p.textContent = '表は横にスクロールできます';
        p.hidden = true;
        wrap.appendChild(p);
    }

    function bindWrap(wrap) {
        var inner = wrap.querySelector('.' + INNER);
        if (!inner) {
            return;
        }
        ensureHint(wrap);
        inner.addEventListener(
            'scroll',
            function () {
                var hint = wrap.querySelector('.' + HINT);
                if (hint && !hint.classList.contains('is-dismissed')) {
                    hint.classList.add('is-dismissed');
                    hint.hidden = true;
                }
                updateOverflowState(wrap);
            },
            { passive: true }
        );
        wrapList.push(wrap);
        updateOverflowState(wrap);
    }

    function onResizeAll() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function () {
            wrapList.forEach(updateOverflowState);
        }, 120);
    }

    window.addEventListener('resize', onResizeAll);

    function init() {
        var roots = document.querySelectorAll('.page-content');
        if (!roots.length) {
            return;
        }
        roots.forEach(function (root) {
            root.querySelectorAll('table').forEach(function (table) {
                if (table.parentElement && table.parentElement.closest('table')) {
                    return;
                }
                if (table.closest('.' + WRAP)) {
                    return;
                }
                var wrap = wrapTable(table);
                if (wrap) {
                    bindWrap(wrap);
                }
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();

/**
 * スクール .page-content 内テーブルに ScrollHint（scroll-hint）を適用する。
 * ヒントアイコンはビューポート中央に固定（SCSS）し、
 * ・テーブル高さが MIN_TABLE_HEIGHT_FOR_VIEWPORT_FIXED px 以上のときのみ固定モード
 * ・横スクロールが必要なとき
 * ・表ブロックがビューポート内で十分見えているとき
 * のみ表示する。
 */
(function () {
    /** これ未満の表は ScrollHint の通常配置（ラッパー基準） */
    var MIN_TABLE_HEIGHT_FOR_VIEWPORT_FIXED = 600;
    var CLIP = 'is-scroll-hint-in-viewport';
    var ELIGIBLE = 'is-scroll-hint-viewport-fixed-eligible';
    var rafScheduled = false;
    /** 前回付与した .scroll-hint-icon-wrap（毎スクロールで remove/add しない＝ScrollHint の状態がリセットされないようにする） */
    var lastClippedIconWrap = null;

    function needsHorizontalScroll(inner) {
        if (!inner) {
            return false;
        }
        return inner.scrollWidth > inner.clientWidth + 2;
    }

    function intersectArea(rect, vw, vh) {
        var ix = Math.max(0, Math.min(rect.right, vw) - Math.max(0, rect.left));
        var iy = Math.max(0, Math.min(rect.bottom, vh) - Math.max(0, rect.top));
        return ix * iy;
    }

    /**
     * 横スクロール用の内側ラッパー（実テーブル幅）の矩形で判定する。
     * 外側 .u-table-scroll-wrap だけだと余白で誤判定しやすい。
     */
    function isMeaningfullyInViewport(rect, vw, vh) {
        if (rect.bottom <= 0 || rect.top >= vh) {
            return false;
        }
        var visibleH = Math.min(rect.bottom, vh) - Math.max(rect.top, 0);
        if (visibleH < 36) {
            return false;
        }
        /**
         * ヒーロー中心：表は画面の下の帯にしか入っていない → まだ出さない
         * （数値は厳しすぎると「表に入ったのにヒントが出ない」になるので緩め）
         */
        if (rect.top > vh * 0.62) {
            return false;
        }
        /**
         * フッター方面：表は上に抜け、画面上端に薄い帯だけ残る → 出さない
         */
        if (rect.bottom < vh * 0.11) {
            return false;
        }
        return true;
    }

    function scheduleRefresh() {
        if (rafScheduled) {
            return;
        }
        rafScheduled = true;
        window.requestAnimationFrame(function () {
            rafScheduled = false;
            refreshViewportHints();
        });
    }

    function refreshViewportHints() {
        var root = document.querySelector('.school-section .page-content');
        if (!root) {
            return;
        }
        var wraps = root.querySelectorAll('.u-table-scroll-wrap');
        if (!wraps.length) {
            return;
        }
        var vv = window.visualViewport;
        var vw = vv ? vv.width : window.innerWidth;
        var vh = vv ? vv.height : window.innerHeight;
        var bestIcon = null;
        var bestScore = 0;

        wraps.forEach(function (wrap) {
            var iconWrap = wrap.querySelector('.scroll-hint-icon-wrap');
            if (!iconWrap) {
                return;
            }

            var inner = wrap.querySelector('.u-table-scroll-wrap__inner');
            if (!needsHorizontalScroll(inner)) {
                wrap.classList.remove(ELIGIBLE);
                return;
            }

            var table = inner.querySelector('table');
            var tallEnough = !!(table && table.offsetHeight >= MIN_TABLE_HEIGHT_FOR_VIEWPORT_FIXED);
            wrap.classList.toggle(ELIGIBLE, tallEnough);
            if (!tallEnough) {
                return;
            }

            var r = inner.getBoundingClientRect();
            if (!isMeaningfullyInViewport(r, vw, vh)) {
                return;
            }

            var area = intersectArea(r, vw, vh);
            if (area > bestScore) {
                bestScore = area;
                bestIcon = iconWrap;
            }
        });

        var winner = bestIcon && bestScore > 0 ? bestIcon : null;
        if (winner === lastClippedIconWrap) {
            return;
        }
        if (lastClippedIconWrap && lastClippedIconWrap.isConnected) {
            lastClippedIconWrap.classList.remove(CLIP);
        }
        if (winner) {
            winner.classList.add(CLIP);
        }
        lastClippedIconWrap = winner;
    }

    function bindScrollHintViewport() {
        var root = document.querySelector('.school-section .page-content');
        if (!root) {
            return;
        }
        var wraps = root.querySelectorAll('.u-table-scroll-wrap');
        if (!wraps.length) {
            return;
        }

        var io = new IntersectionObserver(
            function () {
                scheduleRefresh();
            },
            {
                root: null,
                threshold: [0, 0.02, 0.05, 0.1, 0.2, 0.35, 0.5, 0.75, 1],
                rootMargin: '0px',
            }
        );
        wraps.forEach(function (w) {
            io.observe(w);
        });

        function onScrollLike() {
            scheduleRefresh();
        }

        window.addEventListener('scroll', onScrollLike, { passive: true, capture: true });
        document.addEventListener('scroll', onScrollLike, { passive: true, capture: true });
        document.documentElement.addEventListener('scroll', onScrollLike, { passive: true, capture: true });
        window.addEventListener('resize', scheduleRefresh, { passive: true });

        /** iOS 等：慣性スクロール中も追従 */
        document.body.addEventListener('touchmove', onScrollLike, { passive: true, capture: true });

        if (window.visualViewport) {
            window.visualViewport.addEventListener('scroll', onScrollLike, { passive: true });
            window.visualViewport.addEventListener('resize', scheduleRefresh, { passive: true });
        }

        /** scrollend は対応ブラウザのみ（戻りスクロール後の再計算） */
        window.addEventListener('scrollend', scheduleRefresh, { passive: true });

        /** 表の横スクロールではビューポート判定は変わらない。ここで refresh すると ScrollHint の再計算と競って挙動がリセットされやすいため付けない */

        /** 画像・フォント後のレイアウトずれに追従 */
        window.addEventListener('load', scheduleRefresh, { passive: true });

        scheduleRefresh();
        window.setTimeout(scheduleRefresh, 120);
        window.setTimeout(scheduleRefresh, 400);
    }

    function init() {
        if (typeof window.ScrollHint === 'undefined') {
            return;
        }
        if (!document.body.classList.contains('school-section')) {
            return;
        }
        if (!document.querySelector('.school-section .page-content .u-table-scroll-wrap__inner')) {
            return;
        }
        new window.ScrollHint('.school-section .page-content .u-table-scroll-wrap__inner', {
            suggestiveShadow: true,
            i18n: {
                scrollable: 'スクロールできます',
            },
        });

        window.requestAnimationFrame(function () {
            window.requestAnimationFrame(function () {
                bindScrollHintViewport();
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();

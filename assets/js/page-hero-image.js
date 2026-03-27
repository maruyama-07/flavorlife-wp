/**
 * ページヒーロー・MVスライド画像のロードアニメーション
 * .img-load に .loaded を付与して opacity + scale のトランジションを発火
 */
(function () {
    var selector = '.img-effect .img-load img, .img-effect.img-load img';

    function processImg(img) {
        var container = img.closest('.img-load');
        if (!container || container.classList.contains('loaded')) return;

        if (img.complete && img.naturalHeight > 0) {
            requestAnimationFrame(function () {
                requestAnimationFrame(function () {
                    container.classList.add('loaded');
                });
            });
        } else {
            img.addEventListener('load', function () {
                container.classList.add('loaded');
            });
        }
    }

    function init() {
        document.querySelectorAll(selector).forEach(processImg);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            init();
            setTimeout(init, 300); // Splide等の初期化後に再実行
        });
    } else {
        init();
        setTimeout(init, 300);
    }
})();

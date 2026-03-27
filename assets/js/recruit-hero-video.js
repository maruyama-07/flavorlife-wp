/**
 * Recruitページ ヒーロー動画の再生維持
 * バッファ不足やブラウザの一時停止時に再生を再開
 */
(function () {
    function init() {
        document.querySelectorAll('.js-hero-video').forEach(function (video) {
            function tryPlay() {
                if (video.paused && !video.ended) {
                    video.play().catch(function () {});
                }
            }

            video.addEventListener('stalled', function () {
                setTimeout(tryPlay, 1000);
            });
            video.addEventListener('suspend', function () {
                setTimeout(tryPlay, 500);
            });
            video.addEventListener('pause', function () {
                if (!video.ended) setTimeout(tryPlay, 300);
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();

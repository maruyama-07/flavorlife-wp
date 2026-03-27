<?php
/**
 * フロントページ - メディア＋テキストセクション
 */

$background_image = get_field('background_image');
$left_media = get_field('left_media');
$section_text = get_field('section_text');
$section_highlight = get_field('section_highlight');
$button_link = get_field('button_link');
$video_thumbnail = get_field('video_thumbnail');

// デバッグ用：何も入力されていない場合はメッセージを表示
if (!$left_media && !$section_text) {
    echo '<!-- メディア＋テキストセクション：フィールドが空です。WordPress管理画面で固定ページ「ホームページ」を編集し、「メディア＋テキストセクション」のフィールドに値を入力してください。 -->';
    return;
}
?>

<section class="p-front-media-text"
    <?php if ($background_image) : ?>style="background-image: url(<?php echo esc_url($background_image); ?>);"
    <?php endif; ?>>

    <div class="p-front-media-text__container">
        <!-- 左側：画像または動画 -->
        <?php if ($left_media) : ?>
        <div class="p-front-media-text__left">
            <?php
            $file_type = wp_check_filetype($left_media['url']);
            $is_video = strpos($file_type['type'], 'video') !== false;
            // ACF：動画サムネ（任意）
            $video_thumbnail = get_field('video_thumbnail'); // 画像フィールド（任意）
            $thumb_url = is_array($video_thumbnail) && !empty($video_thumbnail['url']) ? $video_thumbnail['url'] : '';
?>

            <?php if ($is_video) : ?>
            <div class="p-front-media-text__media js-front-video-media">

                <video loop muted playsinline preload="metadata" class="p-front-media-text__video js-front-video"
                    <?php echo $thumb_url ? 'style="display:none;"' : ''; ?>>
                    <source src="<?php echo esc_url($left_media['url']); ?>"
                        type="<?php echo esc_attr($file_type['type']); ?>">
                </video>

                <?php if ($thumb_url) : ?>
                <div class="p-front-media-text__thumb js-front-video-thumb">
                    <img src="<?php echo esc_url($thumb_url); ?>" alt="">
                </div>
                <?php endif; ?>

                <?php if (!$thumb_url) : ?>
                <button type="button" class="p-front-media-text__play js-front-video-play" aria-label="動画を再生">
                    <img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/images/common/top_movie_button.png"
                        alt="動画を再生">
                </button>
                <?php endif; ?>

            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- 右側：テキストコンテンツ（白背景） -->
        <div class="p-front-media-text__right  js-animate-content">
            <h2 class="p-front-media-text__title">Company</h2>
            <p class="p-front-media-text__subtitle c-head-sub">会社概要</p>
            <?php if ($section_text) : ?>
            <p class="p-front-media-text__text"><?php echo nl2br(esc_html($section_text)); ?></p>
            <?php endif; ?>

            <?php if ($section_highlight) : ?>
            <p class="p-front-media-text__highlight"><?php echo esc_html($section_highlight); ?></p>
            <?php endif; ?>

            <?php if ($button_link) : ?>
            <div class="p-front-media-text__button-wrap">
                <a href="/company" class="c-custom-button">
                    詳細はこちら
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 8.33 9.95">
                        <defs>
                            <style>
                            .cls-1 {
                                fill: none;
                                stroke: #fff;
                                stroke-miterlimit: 10;
                                stroke-width: 2px;
                            }
                            </style>
                        </defs>
                        <g>
                            <polyline class="cls-1" points=".57 9.13 6.57 4.98 .57 .82"></polyline>
                        </g>
                    </svg>
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>

</section>

<script>
document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".js-front-video-media").forEach((media) => {
        const video = media.querySelector(".js-front-video");
        const btn = media.querySelector(".js-front-video-play");
        const thumb = media.querySelector(".js-front-video-thumb");

        if (!video) return;

        const start = async () => {
            try {
                if (thumb) {
                    thumb.classList.add("is-hidden");
                    video.style.display = "";
                }

                video.muted = true;
                video.setAttribute("playsinline", "");
                video.playsInline = true;

                await video.play();
                if (btn) btn.classList.add("is-hidden");
            } catch (e) {
                console.warn("Video play failed:", e);
            }
        };

        if (btn) {
            btn.addEventListener("click", (ev) => {
                ev.preventDefault();
                start();
            });
        }

        if (thumb) {
            thumb.addEventListener("click", (ev) => {
                ev.preventDefault();
                start();
            });
        }
    });
});
// document.addEventListener("DOMContentLoaded", () => {
//     document.querySelectorAll(".p-front-media-text__media").forEach((media) => {
//         const video = media.querySelector("video");
//         const btn = media.querySelector(".js-front-video-play");
//         if (!video || !btn) return;

//         const start = async () => {
//             try {
//                 // iOS/Safari 対策（属性があっても念のため JS で強制）
//                 video.muted = true;
//                 video.setAttribute("muted", "");
//                 video.setAttribute("playsinline", "");
//                 video.playsInline = true;

//                 await video.play();
//                 btn.classList.add("is-hidden");
//             } catch (e) {
//                 console.warn("Video play failed:", e);
//             }
//         };

//         // ボタンでも、動画上クリックでも再生（体感ミス減る）
//         btn.addEventListener("click", (ev) => {
//             ev.preventDefault();
//             ev.stopPropagation();
//             start();
//         });

//         media.addEventListener("click", () => {
//             // ボタンが見えてる間だけクリックで再生
//             if (!btn.classList.contains("is-hidden")) start();
//         });
//     });
// });
</script>
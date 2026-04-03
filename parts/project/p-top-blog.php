<?php
/**
 * TOPページ - Blogセクション
 * 画像・本文は固定ページ（フロントページ）の ACF「TOP：Blogセクション」で編集
 */
$front_id = (int) get_option('page_on_front');

$default_img = get_template_directory_uri() . '/assets/images/common/top-blog.webp';
$img_url     = $default_img;
$img_alt     = '社長';
$body_text   = "アロマのこと、フランスのこと。\nお仕事のこと、日常のこと。\n代表取締役 興津秀憲の本音。";

if ($front_id > 0 && function_exists('get_field')) {
    $acf_img = get_field('top_blog_image', $front_id);
    if (is_array($acf_img) && !empty($acf_img['url'])) {
        $img_url = (string) $acf_img['url'];
        $img_alt = !empty($acf_img['alt']) ? (string) $acf_img['alt'] : $img_alt;
    } elseif (is_numeric($acf_img)) {
        $att_url = wp_get_attachment_image_url((int) $acf_img, 'full');
        if (is_string($att_url) && $att_url !== '') {
            $img_url = $att_url;
            $img_alt = (string) get_post_meta((int) $acf_img, '_wp_attachment_image_alt', true);
            if ($img_alt === '') {
                $img_alt = '社長';
            }
        }
    }

    $acf_text = get_field('top_blog_text', $front_id);
    if (is_string($acf_text) && trim($acf_text) !== '') {
        $body_text = $acf_text;
    }
}
?>

<section class="p-top-blog">
    <div class="p-top-blog__container  js-animate-content">
        <!-- 左側：人物画像 -->
        <div class="p-top-blog__image">
            <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($img_alt); ?>" loading="lazy" decoding="async">
        </div>

        <!-- 右側：テキストコンテンツ（白背景ボックス） -->
        <div class="p-top-blog__content">
            <h2 class="p-top-blog__title">Blog</h2>
            <p class="p-top-blog__subtitle">社長ブログ</p>
            <p class="p-top-blog__text">
                <?php echo nl2br(esc_html($body_text)); ?>
            </p>
            <a href="<?php echo esc_url(home_url('/blog')); ?>" class="c-custom-button">
                詳しく見る
                <svg class="c-custom-button__arrow" width="12" height="18" viewBox="0 0 12 18" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path d="M2 2L10 9L2 16" stroke="currentColor" stroke-width="3" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
            </a>
        </div>
    </div>
</section>

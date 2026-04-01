<?php
/**
 * 受講生の声・詳細：タイトル下の画像エリア
 * - ACF 画像あり: 左アイキャッチ / 右 ACF
 * - ACF なし: アイキャッチのみ中央（プレースホルダ可）
 */
$title = get_the_title();
$thumb = get_the_post_thumbnail_url(get_the_ID(), 'large');
if (!$thumb) {
    $thumb = get_theme_file_uri('assets/images/school/voice-nonImage.jpg');
}

$acf_image = function_exists('get_field') ? get_field('voice_school_detail_image', get_the_ID()) : null;
$acf_url = '';
$acf_alt = $title;
if (is_array($acf_image) && !empty($acf_image['url'])) {
    $acf_url = (string) $acf_image['url'];
    if (!empty($acf_image['alt'])) {
        $acf_alt = (string) $acf_image['alt'];
    }
} elseif (is_numeric($acf_image)) {
    $acf_url = (string) wp_get_attachment_image_url((int) $acf_image, 'large');
}

$has_acf = $acf_url !== '';
$section_class = 'p-school-voice-single-media' . ($has_acf ? ' p-school-voice-single-media--two-col' : ' p-school-voice-single-media--thumb-only');
?>
<section class="<?php echo esc_attr($section_class); ?>" aria-label="紹介画像">
    <div class="p-school-voice-single-media__inner l-inner">
        <?php if ($has_acf) : ?>
        <div class="p-school-voice-single-media__grid">
            <div class="p-school-voice-single-media__cell p-school-voice-single-media__cell--thumb">
                <img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr($title); ?>" loading="lazy" decoding="async">
            </div>
            <div class="p-school-voice-single-media__cell p-school-voice-single-media__cell--acf">
                <img src="<?php echo esc_url($acf_url); ?>" alt="<?php echo esc_attr($acf_alt); ?>" loading="lazy" decoding="async">
            </div>
        </div>
        <?php else : ?>
        <div class="p-school-voice-single-media__single">
            <img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr($title); ?>" loading="lazy" decoding="async">
        </div>
        <?php endif; ?>
    </div>
</section>

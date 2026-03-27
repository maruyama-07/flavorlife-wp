<?php
/**
 * ヒーローセクションブロックのレンダリング
 */

$bg_pc = isset($attributes['backgroundImagePC']['url']) ? $attributes['backgroundImagePC']['url'] : '';
$bg_sp = isset($attributes['backgroundImageSP']['url']) ? $attributes['backgroundImageSP']['url'] : '';
$text = isset($attributes['heroText']) ? $attributes['heroText'] : '';
$text_color = isset($attributes['textColor']) ? $attributes['textColor'] : 'white';
$text_position = isset($attributes['textPosition']) ? $attributes['textPosition'] : 'center';
$vertical_position = isset($attributes['verticalPosition']) ? $attributes['verticalPosition'] : 'center';
$height = isset($attributes['sectionHeight']) ? $attributes['sectionHeight'] : 'aspect-design';

// 固有のIDを生成
$unique_id = 'hero-' . uniqid();

// 背景画像のスタイル（PC用）
$style = '';
if (!empty($bg_pc)) {
    $style = 'background-image: url(' . esc_url($bg_pc) . ');';
}

// アスペクト比以外の高さ設定
if (strpos($height, 'aspect-') === false) {
    $style .= ' min-height: ' . esc_attr($height) . ';';
}

$classes = sprintf(
    'hero-section hero-section--text-%s hero-section--align-%s hero-section--vertical-%s',
    esc_attr($text_color),
    esc_attr($text_position),
    esc_attr($vertical_position)
);
?>

<div id="<?php echo esc_attr($unique_id); ?>" class="<?php echo $classes; ?>" data-height="<?php echo esc_attr($height); ?>" style="<?php echo $style; ?>">
    <div class="hero-section__inner">
        <?php if (!empty($text)) : ?>
            <div class="hero-section__text">
                <?php echo nl2br(wp_kses_post($text)); ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php if (!empty($bg_sp) && !empty($bg_pc)) : ?>
<style>
    /* PC用背景画像 */
    #<?php echo esc_attr($unique_id); ?> {
        background-image: url(<?php echo esc_url($bg_pc); ?>) !important;
    }
    
    /* SP用背景画像 */
    @media screen and (max-width: 767px) {
        #<?php echo esc_attr($unique_id); ?> {
            background-image: url(<?php echo esc_url($bg_sp); ?>) !important;
        }
    }
</style>
<?php endif; ?>

<?php
/**
 * お問い合わせセクション
 * 固定ページで contact_section_display が有効な場合に表示
 *
 * @param int $post_id 投稿ID（省略時は現在の投稿）
 */
$post_id = isset($args['post_id']) ? (int) $args['post_id'] : get_the_ID();
if (!$post_id || get_post_type($post_id) !== 'page') {
    return;
}

$display = get_field('contact_section_display', $post_id);
if (!$display) {
    return;
}

$intro = get_field('contact_intro', $post_id) ?: '資料請求・お問合せはこちらまで。お気軽にお問い合わせください。';
$show_phone = get_field('contact_show_phone', $post_id);
$show_form = get_field('contact_show_form', $post_id);
if ($show_phone === null) $show_phone = true;
if ($show_form === null) $show_form = true;

$phone_header = get_field('contact_phone_header', $post_id) ?: 'お電話でのお問い合わせはこちらから';
$phone_number = get_field('contact_phone_number', $post_id) ?: '0120-907-187';
$reception = get_field('contact_reception', $post_id) ?: '【受付時間】平日 9:00~18:00';
$form_header = get_field('contact_form_header', $post_id) ?: '資料請求・お問い合わせはこちらから';
$form_button = get_field('contact_form_button', $post_id) ?: 'お問い合わせフォーム';
$form_url = get_field('contact_form_url', $post_id);
$form_description = get_field('contact_form_description', $post_id);

$tel_url = 'tel:' . preg_replace('/[^0-9]/', '', $phone_number);
$has_boxes = $show_phone || $show_form;
?>

<section class="c-cta">
    <div class="l-inner">
        <p class="c-cta__intro"><?php echo function_exists('tool_acf_format_field_for_echo') ? tool_acf_format_field_for_echo((string) $intro) : esc_html($intro); ?></p>
        <?php if ($has_boxes) : ?>
        <div class="c-cta__boxes<?php echo ($show_phone && $show_form) ? '' : ' c-cta__boxes--single'; ?>">
            <?php if ($show_phone) : ?>
            <div class="c-cta__box">
                <p class="c-cta__box-header"><?php echo esc_html($phone_header); ?></p>
                <div class="c-cta__box-body">
                    <a href="<?php echo esc_attr($tel_url); ?>" class="c-cta__phone"><?php echo esc_html($phone_number); ?></a>
                    <p class="c-cta__reception"><?php echo esc_html($reception); ?></p>
                </div>
            </div>
            <?php endif; ?>
            <?php if ($show_form) : ?>
            <div class="c-cta__box">
                <p class="c-cta__box-header"><?php echo esc_html($form_header); ?></p>
                <div class="c-cta__box-body">
                    <?php if ($form_url) : ?>
                        <a href="<?php echo esc_url($form_url); ?>" class="c-cta__form-btn">
                            <?php echo esc_html($form_button); ?>
                            <span class="c-cta__form-arrow">&gt;</span>
                        </a>
                    <?php else : ?>
                        <span class="c-cta__form-btn c-cta__form-btn--disabled">
                            <?php echo esc_html($form_button); ?>
                        </span>
                    <?php endif; ?>
                    <?php if ($form_description) : ?>
                        <p class="c-cta__form-description"><?php echo function_exists('tool_acf_format_field_for_echo') ? tool_acf_format_field_for_echo((string) $form_description) : (function_exists('tool_format_text_with_sp_break') ? tool_format_text_with_sp_break((string) $form_description) : nl2br(esc_html((string) $form_description))); ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

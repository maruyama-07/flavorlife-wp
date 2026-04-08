<?php
/**
 * /school/order/ 専用ブロック（ACF）
 */
if (! function_exists('get_field')) {
    return;
}

$post_id = get_the_ID();
$heading = (string) get_field('school_order_heading', $post_id);
$intro   = (string) get_field('school_order_intro', $post_id);
$body    = (string) get_field('school_order_body', $post_id);

if ($heading === '' && $intro === '' && $body === '') {
    return;
}
?>
<section class="p-school-order" aria-label="お申込みのご案内">
    <div class="p-school-order__inner">
        <?php if ($heading !== '') : ?>
        <h2 class="p-school-order__title"><?php echo function_exists('tool_esc_acf_text_for_display') ? tool_esc_acf_text_for_display($heading) : esc_html($heading); ?></h2>
        <?php endif; ?>

        <?php if ($intro !== '') : ?>
        <div class="p-school-order__intro">
            <?php
            if (function_exists('tool_acf_echo_textarea_for_display')) {
                tool_acf_echo_textarea_for_display($intro);
            } else {
                echo wp_kses_post(wpautop($intro));
            }
            ?>
        </div>
        <?php endif; ?>

        <?php if ($body !== '') : ?>
        <div class="p-school-order__body">
            <?php
            // WYSIWYG（リンク・リスト等）は the_content と同じフィルタで整形
            echo apply_filters('the_content', $body);
            ?>
        </div>
        <?php endif; ?>
    </div>
</section>

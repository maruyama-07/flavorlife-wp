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
        <h2 class="p-school-order__title"><?php echo esc_html($heading); ?></h2>
        <?php endif; ?>

        <?php if ($intro !== '') : ?>
        <div class="p-school-order__intro">
            <?php echo wp_kses_post(wpautop($intro)); ?>
        </div>
        <?php endif; ?>

        <?php if ($body !== '') : ?>
        <div class="p-school-order__body">
            <?php echo wp_kses_post(wpautop($body)); ?>
        </div>
        <?php endif; ?>
    </div>
</section>

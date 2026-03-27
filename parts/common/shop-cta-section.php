<?php
/**
 * オンラインショップCTAセクション
 * ページ下部に表示。固定ページで shop_cta_display が有効な場合のみ表示
 * 画像・テキスト・ボタンはページごとにカスタマイズ可能
 *
 * @param int $post_id 投稿ID（省略時は現在の投稿）
 */
$post_id = isset($args['post_id']) ? (int) $args['post_id'] : get_the_ID();
if (!$post_id || get_post_type($post_id) !== 'page') {
    return;
}

$display = get_field('shop_cta_display', $post_id);
if (!$display) {
    return;
}

$image_url = get_field('shop_cta_image', $post_id);
$title = get_field('shop_cta_title', $post_id) ?: 'Online Shop';
$button_text = get_field('shop_cta_button_text', $post_id) ?: 'ご購入はこちら';
$button_url = get_field('shop_cta_button_url', $post_id);
$new_tab = (bool) get_field('shop_cta_new_tab', $post_id);

if (is_array($image_url)) {
    $image_url = $image_url['url'] ?? '';
}
?>

<div class="l-inner">
    <section class="c-shop-cta">
        <div class="c-shop-cta__bg"<?php echo $image_url ? ' style="background-image: url(' . esc_url($image_url) . ');"' : ''; ?>></div>
        <div class="c-shop-cta__inner">
            <h2 class="c-shop-cta__title"><?php echo esc_html($title); ?></h2>
            <?php if ($button_url) : ?>
            <a href="<?php echo esc_url($button_url); ?>" class="c-custom-button"<?php echo $new_tab ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>>
                <?php echo esc_html($button_text); ?>
                <svg class="c-custom-button__arrow" width="12" height="18" viewBox="0 0 12 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M2 2L10 9L2 16" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </a>
            <?php else : ?>
            <span class="c-custom-button c-custom-button--disabled">
                <?php echo esc_html($button_text); ?>
                <svg class="c-custom-button__arrow" width="12" height="18" viewBox="0 0 12 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M2 2L10 9L2 16" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </span>
            <?php endif; ?>
        </div>
    </section>
</div>

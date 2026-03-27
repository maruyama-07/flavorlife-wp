<?php
/**
 * 商品カード（ショートコード）
 * [product_card image="123" title="タイトル" border="gold"]説明文[/product_card]
 * エディタの「商品カード」ボタンから挿入
 */
add_shortcode('product_card', 'product_card_shortcode');

function product_card_shortcode($atts, $content = null)
{
    $args = shortcode_atts(array(
        'image'  => '',
        'title'  => '',
        'border' => '',
    ), $atts);

    $image_id = (int) $args['image'];
    $title    = esc_html($args['title']);
    $border   = trim($args['border']);
    $body     = wp_kses_post(wpautop(trim($content ?? '')));

    if (empty($title) && empty($body) && !$image_id) {
        return '';
    }

    $border_class = $border ? 'c-product-card--' . preg_replace('/[^a-z0-9-]/', '', $border) : '';

    ob_start();
?>
<div class="c-product-card<?php echo $border_class ? ' ' . esc_attr($border_class) : ''; ?>">
    <?php if ($image_id && wp_attachment_is_image($image_id)) : ?>
    <div class="c-product-card__image">
        <?php echo wp_get_attachment_image($image_id, 'thumbnail', false, array('class' => 'c-product-card__img')); ?>
    </div>
    <?php endif; ?>
    <div class="c-product-card__body">
        <?php if ($title) : ?>
        <h3 class="c-product-card__title"><?php echo $title; ?></h3>
        <?php endif; ?>
        <?php if ($body) : ?>
        <div class="c-product-card__text"><?php echo $body; ?></div>
        <?php endif; ?>
    </div>
</div>
<?php
    return ob_get_clean();
}

/**
 * 管理画面：TinyMCE に「商品カード」ボタンを追加
 */
function tool_product_card_mce_button($buttons)
{
    array_push($buttons, 'product_card');
    return $buttons;
}
add_filter('mce_buttons_2', 'tool_product_card_mce_button');

function tool_product_card_mce_plugin($plugin_array)
{
    $plugin_array['product_card'] = get_template_directory_uri() . '/assets/js/admin/product-card.js';
    return $plugin_array;
}
add_filter('mce_external_plugins', 'tool_product_card_mce_plugin');

/**
 * 管理画面：メディアライブラリを読み込み
 */
function tool_product_card_admin_scripts($hook)
{
    if ($hook !== 'post.php' && $hook !== 'post-new.php') {
        return;
    }
    wp_enqueue_media();
}
add_action('admin_enqueue_scripts', 'tool_product_card_admin_scripts');

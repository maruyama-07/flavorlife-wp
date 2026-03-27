<?php
/**
 * 3画像グリッド（ショートコード）
 * [image_grid ids="123,456,789"] または エディタの「3画像グリッド」ボタンから挿入
 */
add_shortcode('image_grid', 'image_grid_shortcode');

function image_grid_shortcode($atts)
{
    $args = shortcode_atts(array(
        'ids' => '',
    ), $atts);

    $ids = array_filter(array_map('intval', explode(',', $args['ids'])));
    if (empty($ids)) {
        return '';
    }

    $ids = array_slice($ids, 0, 3); // 最大3枚

    ob_start();
?>
<div class="c-image-grid">
    <?php foreach ($ids as $id) : ?>
        <?php if (wp_attachment_is_image($id)) : ?>
    <div class="c-image-grid__item">
        <?php echo wp_get_attachment_image($id, 'large', false, array('class' => 'c-image-grid__img')); ?>
    </div>
        <?php endif; ?>
    <?php endforeach; ?>
</div>
<?php
    return ob_get_clean();
}

/**
 * 管理画面：TinyMCE に「3画像グリッド」ボタンを追加
 */
function tool_image_grid_mce_button($buttons)
{
    array_push($buttons, 'image_grid');
    return $buttons;
}
add_filter('mce_buttons_2', 'tool_image_grid_mce_button');

function tool_image_grid_mce_plugin($plugin_array)
{
    $plugin_array['image_grid'] = get_template_directory_uri() . '/assets/js/admin/image-grid.js';
    return $plugin_array;
}
add_filter('mce_external_plugins', 'tool_image_grid_mce_plugin');

/**
 * 管理画面：メディアライブラリとスクリプトを読み込み
 */
function tool_image_grid_admin_scripts($hook)
{
    if ($hook !== 'post.php' && $hook !== 'post-new.php') {
        return;
    }
    wp_enqueue_media();
}
add_action('admin_enqueue_scripts', 'tool_image_grid_admin_scripts');

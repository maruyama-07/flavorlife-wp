<?php
/**
 * レスポンシブ画像（ショートコード）
 * PC用・SP用で別画像を表示
 * [responsive_image pc="123" sp="456"] または エディタの「レスポンシブ画像」ボタンから挿入
 */
add_shortcode('responsive_image', 'responsive_image_shortcode');

function responsive_image_shortcode($atts)
{
    $args = shortcode_atts(array(
        'pc' => '',
        'sp' => '',
    ), $atts);

    $pc_id = (int) $args['pc'];
    $sp_id = (int) $args['sp'];

    if (!$pc_id && !$sp_id) {
        return '';
    }

    ob_start();
?>
<div class="c-responsive-image">
    <?php if ($pc_id && wp_attachment_is_image($pc_id)) : ?>
    <div class="c-responsive-image__pc">
        <?php echo wp_get_attachment_image($pc_id, 'large', false, array('class' => 'c-responsive-image__img')); ?>
    </div>
    <?php endif; ?>
    <?php if ($sp_id && wp_attachment_is_image($sp_id)) : ?>
    <div class="c-responsive-image__sp">
        <?php echo wp_get_attachment_image($sp_id, 'large', false, array('class' => 'c-responsive-image__img')); ?>
    </div>
    <?php endif; ?>
</div>
<?php
    return ob_get_clean();
}

/**
 * 管理画面：TinyMCE に「レスポンシブ画像」ボタンを追加
 */
function tool_responsive_image_mce_button($buttons)
{
    array_push($buttons, 'responsive_image');
    return $buttons;
}
add_filter('mce_buttons_2', 'tool_responsive_image_mce_button');

function tool_responsive_image_mce_plugin($plugin_array)
{
    $plugin_array['responsive_image'] = get_template_directory_uri() . '/assets/js/admin/responsive-image.js';
    return $plugin_array;
}
add_filter('mce_external_plugins', 'tool_responsive_image_mce_plugin');

/**
 * 管理画面：メディアライブラリを読み込み
 */
function tool_responsive_image_admin_scripts($hook)
{
    if ($hook !== 'post.php' && $hook !== 'post-new.php') {
        return;
    }
    wp_enqueue_media();
}
add_action('admin_enqueue_scripts', 'tool_responsive_image_admin_scripts');

<?php
/**
 * サービス2カラム（ショートコード）
 * [service_columns image1="123" text1="テキスト" link1="/url" image2="456" text2="テキスト" link2="/url"]
 * クラシックエディタの「サービス2カラム」ボタンから挿入
 */
add_shortcode('service_columns', 'service_columns_shortcode');

function service_columns_shortcode($atts)
{
    $args = shortcode_atts(array(
        'image1' => '',
        'text1'   => '',
        'link1'   => '',
        'image2'  => '',
        'text2'   => '',
        'link2'   => '',
    ), $atts);

    $cols = array();
    for ($i = 1; $i <= 2; $i++) {
        $img_id = (int) $args['image' . $i];
        $text   = esc_html($args['text' . $i]);
        $link   = esc_url($args['link' . $i]);
        $cols[] = array(
            'image_id' => $img_id,
            'text'     => $text,
            'link'     => $link,
        );
    }

    if (empty($cols[0]['image_id']) && empty($cols[0]['text']) && empty($cols[1]['image_id']) && empty($cols[1]['text'])) {
        return '';
    }

    ob_start();
?>
<div class="p-service-columns">
    <?php foreach ($cols as $col) : ?>
    <div class="p-service-columns__item">
        <?php if ($col['link']) : ?>
        <a href="<?php echo $col['link']; ?>" class="p-service-columns__link">
        <?php endif; ?>
            <?php if ($col['image_id'] && wp_attachment_is_image($col['image_id'])) : ?>
            <div class="p-service-columns__image">
                <?php echo wp_get_attachment_image($col['image_id'], 'medium_large', false, array('class' => 'p-service-columns__img')); ?>
            </div>
            <?php endif; ?>
            <?php if ($col['text']) : ?>
            <p class="p-service-columns__text"><?php echo $col['text']; ?></p>
            <?php endif; ?>
        <?php if ($col['link']) : ?>
        </a>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
</div>
<?php
    return ob_get_clean();
}

/**
 * TinyMCE に「サービス2カラム」ボタンを追加
 */
function tool_service_columns_mce_button($buttons)
{
    array_push($buttons, 'service_columns');
    return $buttons;
}
add_filter('mce_buttons_2', 'tool_service_columns_mce_button');

function tool_service_columns_mce_plugin($plugin_array)
{
    $plugin_array['service_columns'] = get_template_directory_uri() . '/assets/js/admin/service-columns.js';
    return $plugin_array;
}
add_filter('mce_external_plugins', 'tool_service_columns_mce_plugin');

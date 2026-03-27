<?php
/**
 * ステップフロー（ショートコード）
 * [step num="1"]テキスト[/step] で使用。エディタの「ステップフロー」ボタンから挿入
 *
 * @example [step num="1"]当社営業本部までお電話、またはお問い合わせフォームでお問い合わせください。[/step]
 */
add_shortcode('step', 'step_flow_shortcode');

function step_flow_shortcode($atts, $content = null)
{
    $args = shortcode_atts(array(
        'num' => '1',
    ), $atts);

    $num    = esc_html($args['num']);
    $text   = wp_kses_post(wpautop(trim($content)));

    if (empty($text)) {
        return '';
    }

    ob_start();
?>
<div class="c-step-flow__item">
    <span class="c-step-flow__num"><?php echo $num; ?></span>
    <div class="c-step-flow__text"><?php echo $text; ?></div>
</div>
<?php
    return ob_get_clean();
}

/**
 * 管理画面：TinyMCE に「ステップフロー」ボタンを追加
 */
function tool_step_flow_mce_button($buttons)
{
    array_push($buttons, 'step_flow');
    return $buttons;
}
add_filter('mce_buttons_2', 'tool_step_flow_mce_button');

function tool_step_flow_mce_plugin($plugin_array)
{
    $plugin_array['step_flow'] = get_template_directory_uri() . '/assets/js/admin/step-flow.js';
    return $plugin_array;
}
add_filter('mce_external_plugins', 'tool_step_flow_mce_plugin');

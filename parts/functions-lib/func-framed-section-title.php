<?php
/**
 * 枠付きセクション見出し（ショートコード）
 * [framed_title main="日本語見出し" sub="英語サブ"] TinyMCE「枠付き見出し」から挿入
 *
 * main / sub がどちらも空のときは何も出力しない。片方だけでも可。
 *
 * @example [framed_title main="資格取得" sub="career advancement"]
 */
add_shortcode('framed_title', 'tool_framed_title_shortcode');

/**
 * @param array<string, string> $atts
 */
function tool_framed_title_shortcode($atts)
{
    $args = shortcode_atts(
        array(
            'main' => '',
            'sub'  => '',
        ),
        $atts,
        'framed_title'
    );

    $main = trim((string) $args['main']);
    $sub  = trim((string) $args['sub']);

    if ($main === '' && $sub === '') {
        return '';
    }

    ob_start();
    ?>
<div class="c-framed-section-title">
<?php if ($main !== '') : ?>
    <p class="c-framed-section-title__main"><?php echo esc_html($main); ?></p>
<?php endif; ?>
<?php if ($sub !== '') : ?>
    <p class="c-framed-section-title__sub"><?php echo esc_html($sub); ?></p>
<?php endif; ?>
</div>
<?php
    return ob_get_clean();
}

/**
 * TinyMCE に「枠付き見出し」ボタンを追加
 *
 * @param string[] $buttons
 * @return string[]
 */
function tool_framed_title_mce_button($buttons)
{
    $buttons[] = 'framed_title';

    return $buttons;
}
add_filter('mce_buttons_2', 'tool_framed_title_mce_button');

/**
 * @param array<string, string> $plugin_array
 * @return array<string, string>
 */
function tool_framed_title_mce_plugin($plugin_array)
{
    $plugin_array['framed_title'] = get_template_directory_uri() . '/assets/js/admin/framed-title.js';

    return $plugin_array;
}
add_filter('mce_external_plugins', 'tool_framed_title_mce_plugin');

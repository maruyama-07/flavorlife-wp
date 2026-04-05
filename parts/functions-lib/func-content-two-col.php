<?php
/**
 * 本文2カラム（TinyMCE）
 * コーポレート・スクール共通。マークアップのみ（ショートコードなし）
 */

/**
 * TinyMCE に「2カラム」ボタンを追加
 *
 * @param string[] $buttons
 * @return string[]
 */
function tool_content_two_col_mce_button($buttons)
{
    if (!is_array($buttons)) {
        return $buttons;
    }
    $buttons[] = 'content_two_col';

    return $buttons;
}
add_filter('mce_buttons_2', 'tool_content_two_col_mce_button', 12);

/**
 * @param array<string, string> $plugin_array
 * @return array<string, string>
 */
function tool_content_two_col_mce_plugin($plugin_array)
{
    if (!is_array($plugin_array)) {
        return $plugin_array;
    }
    $plugin_array['content_two_col'] = get_template_directory_uri() . '/assets/js/admin/content-two-col.js';

    return $plugin_array;
}
add_filter('mce_external_plugins', 'tool_content_two_col_mce_plugin');

/**
 * クラシックエディタ iframe 内でも2カラムが見えるよう content_style を付与（全画面共通）
 *
 * @param array<string, mixed> $init_array
 * @return array<string, mixed>
 */
function tool_content_two_col_tinymce_content_style($init_array)
{
    if (!is_admin()) {
        return $init_array;
    }

    $css = <<<'CSS'
.mce-content-body .c-content-two-col,.wp-block-freeform .c-content-two-col{display:flex!important;flex-wrap:wrap!important;justify-content:space-between!important;align-items:flex-start!important;width:100%!important;box-sizing:border-box!important;margin:1.5rem 0!important;}
.mce-content-body .c-content-two-col__col,.wp-block-freeform .c-content-two-col__col{flex:0 0 45%!important;max-width:45%!important;box-sizing:border-box!important;min-width:0!important;}
@media screen and (max-width:767px){.mce-content-body .c-content-two-col__col,.wp-block-freeform .c-content-two-col__col{flex:0 0 100%!important;max-width:100%!important;width:100%!important;}}
CSS;

    $css_one_line = trim(preg_replace('/ {2,}/', ' ', str_replace(array("\r\n", "\r", "\n"), ' ', $css)));
    $merged       = isset($init_array['content_style']) ? $init_array['content_style'] . ' ' . $css_one_line : $css_one_line;
    $init_array['content_style'] = trim(preg_replace('/ {2,}/', ' ', str_replace(array("\r\n", "\r", "\n"), ' ', $merged)));

    return $init_array;
}
add_filter('tiny_mce_before_init', 'tool_content_two_col_tinymce_content_style', 14);

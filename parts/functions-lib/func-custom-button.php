<?php
/**
 * カスタムボタン ショートコード
 * 
 * 使用例:
 * [custom_button url="https://example.com" text="詳細はこちら"]
 * [custom_button url="https://example.com" text="詳細はこちら" target="_blank"]
 */

function custom_button_shortcode($atts) {
    // デフォルト値を設定
    $args = shortcode_atts(array(
        'url' => '#',
        'text' => '詳細はこちら',
        'target' => '_self',
    ), $atts);
    
    // SVG矢印アイコン（太めのシェブロン）
    $arrow_svg = '<?xml version="1.0" encoding="UTF-8"?>
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 8.33 9.95">
    <defs>
        <style>
        .cls-1 {
            fill: none;
            stroke: #fff;
            stroke-miterlimit: 10;
            stroke-width: 2px;
        }
        </style>
    </defs>
    <g>
        <polyline class="cls-1" points=".57 9.13 6.57 4.98 .57 .82" />
    </g>
</svg>';

// 出力
$output = sprintf(
'<div class="c-custom-button-wrap"><a href="%s" class="c-custom-button" target="%s" rel="%s">%s%s</a></div>',
esc_url($args['url']),
esc_attr($args['target']),
$args['target'] === '_blank' ? 'noopener noreferrer' : '',
esc_html($args['text']),
$arrow_svg
);

return $output;
}
add_shortcode('custom_button', 'custom_button_shortcode');
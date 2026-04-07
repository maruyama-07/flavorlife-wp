<?php
/**
 * Google Map へのリンクボタン（アクセスページ等で利用）
 *
 * 使用例:
 * [google_maps_button url="https://maps.app.goo.gl/xxxx"]
 * [google_maps_button url="https://goo.gl/maps/xxx" text="Google Mapで開く"]
 * URL だけ本文に置く場合（属性が崩れるエディタ向け）:
 * [google_maps_button]https://maps.app.goo.gl/xxxx[/google_maps_button]
 */

/**
 * @param string $raw
 * @return string 空のときは無効な URL
 */
function tool_google_maps_button_sanitize_url($raw)
{
    if (! is_string($raw)) {
        return '';
    }
    $raw = html_entity_decode(trim($raw), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $raw = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $raw);
    if ($raw === '') {
        return '';
    }

    $url = esc_url($raw, array('http', 'https'));
    if ($url !== '') {
        return $url;
    }

    if (function_exists('sanitize_url')) {
        $try = sanitize_url($raw);
        if (is_string($try) && $try !== '') {
            return esc_url($try, array('http', 'https'));
        }
    }

    if (preg_match('#^https?://[^\s<>"\']+$#iu', $raw)) {
        return esc_url($raw, array('http', 'https'));
    }

    return '';
}

function tool_google_maps_button_shortcode($atts, $content = null)
{
    $args = shortcode_atts(
        array(
            'url'    => '',
            'text'   => 'google mapで開く',
            'target' => '_blank',
            'rel'    => 'noopener noreferrer',
        ),
        $atts,
        'google_maps_button'
    );

    $raw_url = isset($args['url']) ? (string) $args['url'] : '';
    if ($raw_url === '' && $content !== null && $content !== '') {
        $raw_url = strip_tags((string) $content);
    }

    $url = tool_google_maps_button_sanitize_url($raw_url);
    if ($url === '') {
        return '';
    }

    $text   = esc_html($args['text']);
    $target = $args['target'] === '_self' ? '_self' : '_blank';
    $rel    = $target === '_blank' ? 'noopener noreferrer' : sanitize_text_field($args['rel']);

    $icon_url   = get_theme_file_uri('assets/images/common/logos_google-maps.svg');
    $aria_label = 'Google Map を開く（' . sanitize_text_field($args['text']) . '）';

    $html = sprintf(
        '<a class="c-google-maps-button" href="%s" target="%s" rel="%s" aria-label="%s">'
        . '<span class="c-google-maps-button__icon" aria-hidden="true">'
        . '<img src="%s" alt="" width="24" height="35" decoding="async" loading="lazy">'
        . '</span>'
        . '<span class="c-google-maps-button__text">%s</span>'
        . '</a>',
        $url,
        esc_attr($target),
        esc_attr($rel),
        esc_attr($aria_label),
        esc_url($icon_url),
        $text
    );

    return $html;
}

add_shortcode('google_maps_button', 'tool_google_maps_button_shortcode');

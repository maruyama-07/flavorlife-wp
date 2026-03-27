<?php
/**
 * SNS設定（カスタマイザー）
 */

function my_customize_register($wp_customize)
{
    $wp_customize->add_section('sns_settings', array(
        'title' => 'SNS設定（コーポレート）',
        'description' => 'コーポレートサイトのフッター等で使用するSNSのURLです。',
        'priority' => 30,
    ));

    $wp_customize->add_setting('sns_instagram', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control('sns_instagram', array(
        'label' => 'Instagram URL',
        'section' => 'sns_settings',
        'type' => 'url',
    ));

    $wp_customize->add_setting('sns_facebook', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control('sns_facebook', array(
        'label' => 'Facebook URL',
        'section' => 'sns_settings',
        'type' => 'url',
    ));

    $wp_customize->add_setting('sns_line', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control('sns_line', array(
        'label' => 'LINE URL',
        'section' => 'sns_settings',
        'type' => 'url',
    ));

    $wp_customize->add_setting('sns_youtube', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control('sns_youtube', array(
        'label' => 'YouTube URL',
        'section' => 'sns_settings',
        'type' => 'url',
    ));

    $wp_customize->add_setting('sns_twitter', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control('sns_twitter', array(
        'label' => 'X (Twitter) URL',
        'section' => 'sns_settings',
        'type' => 'url',
    ));

    $wp_customize->add_setting('sns_tiktok', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control('sns_tiktok', array(
        'label' => 'TikTok URL',
        'section' => 'sns_settings',
        'type' => 'url',
    ));

    $wp_customize->add_section('sns_school_settings', array(
        'title' => 'SNS設定（スクール）',
        'description' => 'スクールセクションのモバイルメニュー下部等で表示するリンクです。「SNS設定（コーポレート）」とは別に設定してください。',
        'priority' => 31,
    ));

    $wp_customize->add_setting('sns_school_line', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control('sns_school_line', array(
        'label' => 'LINE URL',
        'section' => 'sns_school_settings',
        'type' => 'url',
    ));

    $wp_customize->add_setting('sns_school_instagram', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control('sns_school_instagram', array(
        'label' => 'Instagram URL',
        'section' => 'sns_school_settings',
        'type' => 'url',
    ));

    $wp_customize->add_setting('sns_school_facebook', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control('sns_school_facebook', array(
        'label' => 'Facebook URL',
        'section' => 'sns_school_settings',
        'type' => 'url',
    ));
}
add_action('customize_register', 'my_customize_register');

/**
 * スクール用モバイルナビ下部 SNS
 *
 * @return array<int, array{url: string, file: string, label: string}>
 */
function school_mobile_nav_sns_items()
{
    return array(
        array(
            'url' => (string) get_theme_mod('sns_school_line', ''),
            'file' => 'line.svg',
            'label' => 'LINE',
        ),
        array(
            'url' => (string) get_theme_mod('sns_school_instagram', ''),
            'file' => 'intagram.png',
            'label' => 'Instagram',
        ),
        array(
            'url' => (string) get_theme_mod('sns_school_facebook', ''),
            'file' => 'facebook.svg',
            'label' => 'Facebook',
        ),
    );
}

function display_sns_icons()
{
    $sns_list = array(
        'instagram' => array(
            'url' => get_theme_mod('sns_instagram'),
            'icon' => 'insta-icon.svg',
            'label' => 'Instagram',
        ),
        'facebook' => array(
            'url' => get_theme_mod('sns_facebook'),
            'icon' => 'facebook-icon.svg',
            'label' => 'Facebook',
        ),
        'line' => array(
            'url' => get_theme_mod('sns_line'),
            'icon' => 'line-icon.svg',
            'label' => 'LINE',
        ),
        'youtube' => array(
            'url' => get_theme_mod('sns_youtube'),
            'icon' => 'youtube-icon.svg',
            'label' => 'YouTube',
        ),
        'twitter' => array(
            'url' => get_theme_mod('sns_twitter'),
            'icon' => 'x-icon.svg',
            'label' => 'X (Twitter)',
        ),
        'tiktok' => array(
            'url' => get_theme_mod('sns_tiktok'),
            'icon' => 'tiktok-icon.svg',
            'label' => 'TikTok',
        ),
    );

    $output = '<div class="l-footer__sns">';

    foreach ($sns_list as $sns) {
        if (!empty($sns['url'])) {
            $icon_path = get_template_directory_uri() . '/assets/images/common/' . $sns['icon'];
            $output .= '<a href="' . esc_url($sns['url']) . '" class="l-footer__sns-item" target="_blank" rel="noopener noreferrer">';
            $output .= '<img src="' . esc_url($icon_path) . '" alt="' . esc_attr($sns['label']) . '">';
            $output .= '</a>';
        }
    }

    $output .= '</div>';

    echo $output;
}

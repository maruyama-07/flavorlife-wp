<?php
/**
 * スクールトップ「Seasonal Topics」セクション用 ACF
 */

add_action('acf/init', function () {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    $school_page_id = function_exists('school_section_get_root_page_id') ? school_section_get_root_page_id() : 0;
    if (!$school_page_id) {
        return;
    }

    acf_add_local_field_group(array(
        'key' => 'group_school_top_seasonal_topics',
        'title' => 'スクールトップ（Seasonal Topics）',
        'fields' => array(
            array(
                'key' => 'field_school_seasonal_title',
                'label' => '見出し（英語）',
                'name' => 'school_seasonal_title',
                'type' => 'text',
                'default_value' => 'Seasonal Topics',
            ),
            array(
                'key' => 'field_school_seasonal_subtitle',
                'label' => '見出し（日本語）',
                'name' => 'school_seasonal_subtitle',
                'type' => 'text',
                'default_value' => '季節のおすすめ',
            ),
            array(
                'key' => 'field_school_seasonal_image',
                'label' => '画像',
                'name' => 'school_seasonal_image',
                'type' => 'image',
                'return_format' => 'array',
                'preview_size' => 'large',
                'instructions' => '未設定時は assets/images/school/seasonal-topics.png を表示します。',
            ),
            array(
                'key' => 'field_school_seasonal_heading',
                'label' => 'トピックタイトル',
                'name' => 'school_seasonal_heading',
                'type' => 'textarea',
                'rows' => 3,
                'new_lines' => '',
                'instructions' => '改行できます。スマホ時のみ改行する箇所には {sp} を入力してください。',
            ),
            array(
                'key' => 'field_school_seasonal_body',
                'label' => '本文',
                'name' => 'school_seasonal_body',
                'type' => 'textarea',
                'rows' => 8,
                'new_lines' => '',
                'instructions' => 'スマホ時のみ改行する箇所には {sp} を入力してください。',
            ),
            array(
                'key' => 'field_school_seasonal_button_text',
                'label' => 'ボタンテキスト',
                'name' => 'school_seasonal_button_text',
                'type' => 'text',
                'default_value' => 'more',
            ),
            array(
                'key' => 'field_school_seasonal_button_url',
                'label' => 'ボタンURL',
                'name' => 'school_seasonal_button_url',
                'type' => 'url',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'page',
                    'operator' => '==',
                    'value' => (string) $school_page_id,
                ),
            ),
        ),
        'position' => 'acf_after_title',
        'style' => 'default',
    ));
});

/**
 * Seasonal Topics 表示データを取得
 *
 * @return array<string,string>
 */
function school_top_get_seasonal_topics_data()
{
    $root = function_exists('school_section_get_root_page_id') ? school_section_get_root_page_id() : 0;
    if (!$root || !is_page() || (int) get_queried_object_id() !== $root) {
        return array();
    }

    $page_id = (int) get_queried_object_id();
    $image = function_exists('get_field') ? get_field('school_seasonal_image', $page_id) : null;
    $image_url = is_array($image) && !empty($image['url']) ? (string) $image['url'] : '';
    if ($image_url === '') {
        $image_url = get_template_directory_uri() . '/assets/images/school/seasonal-topics.png';
    }

    return array(
        'title' => (string) (function_exists('get_field') ? get_field('school_seasonal_title', $page_id) : ''),
        'subtitle' => (string) (function_exists('get_field') ? get_field('school_seasonal_subtitle', $page_id) : ''),
        'image_url' => $image_url,
        'heading' => (string) (function_exists('get_field') ? get_field('school_seasonal_heading', $page_id) : ''),
        'body' => (string) (function_exists('get_field') ? get_field('school_seasonal_body', $page_id) : ''),
        'button_text' => (string) (function_exists('get_field') ? get_field('school_seasonal_button_text', $page_id) : ''),
        'button_url' => (string) (function_exists('get_field') ? get_field('school_seasonal_button_url', $page_id) : ''),
    );
}

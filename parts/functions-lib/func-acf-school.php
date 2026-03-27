<?php
/**
 * スクール設定ページ（school-settings）用 ACF
 */

add_action('acf/init', function () {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    $settings_page = get_page_by_path('school-settings');
    if (!$settings_page) {
        return;
    }

    acf_add_local_field_group(array(
        'key' => 'group_school_settings',
        'title' => 'スクール設定',
        'fields' => array(
            array(
                'key' => 'field_school_logo',
                'label' => 'ヘッダーロゴ',
                'name' => 'school_logo',
                'type' => 'image',
                'instructions' => '未設定の場合はテーマのデフォルトロゴを使用',
                'return_format' => 'array',
                'preview_size' => 'medium',
            ),
            array(
                'key' => 'field_school_footer_logo',
                'label' => 'フッターロゴ',
                'name' => 'school_footer_logo',
                'type' => 'image',
                'instructions' => '未設定の場合はテーマのデフォルトロゴを使用',
                'return_format' => 'array',
                'preview_size' => 'medium',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'page',
                    'operator' => '==',
                    'value' => (string) $settings_page->ID,
                ),
            ),
        ),
    ));
});

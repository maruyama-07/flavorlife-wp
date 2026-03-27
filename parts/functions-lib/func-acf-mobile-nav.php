<?php
/**
 * モバイルナビCTA用ACFフィールド（ACF Free対応：固定ページ＋固定フィールド）
 */

/**
 * 設定用固定ページを自動作成
 */
add_action('init', function () {
    if (!get_page_by_path('mobile-nav-cta-settings')) {
        wp_insert_post(array(
            'post_title' => 'モバイルナビCTA設定',
            'post_name' => 'mobile-nav-cta-settings',
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_author' => 1,
        ));
    }
}, 5);

/**
 * 外観メニューに「モバイルナビCTA」を追加（設定ページの編集へリンク）
 */
add_action('admin_menu', function () {
    $page = get_page_by_path('mobile-nav-cta-settings');
    if (!$page) return;

    add_theme_page(
        'モバイルナビCTA',
        'モバイルナビCTA',
        'edit_pages',
        'mobile-nav-cta',
        function () use ($page) {
            wp_redirect(admin_url('post.php?post=' . $page->ID . '&action=edit'));
            exit;
        }
    );
});

/**
 * ACFフィールドグループ（Repeater非使用・3項目固定）
 */
add_action('acf/init', function () {
    if (!function_exists('acf_add_local_field_group')) return;

    $settings_page = get_page_by_path('mobile-nav-cta-settings');
    if (!$settings_page) return;

    $page_id = $settings_page->ID;
    acf_add_local_field_group(array(
            'key' => 'group_mobile_nav_cta',
            'title' => 'モバイルナビCTA',
            'fields' => array(
                array(
                    'key' => 'field_mobile_nav_cta_tab1',
                    'label' => 'CTA 1',
                    'name' => '',
                    'type' => 'tab',
                ),
                array(
                    'key' => 'field_mobile_nav_cta_1_image',
                    'label' => '画像',
                    'name' => 'mobile_nav_cta_1_image',
                    'type' => 'image',
                    'return_format' => 'array',
                    'preview_size' => 'medium',
                ),
                array(
                    'key' => 'field_mobile_nav_cta_1_link',
                    'label' => 'リンクURL',
                    'name' => 'mobile_nav_cta_1_link',
                    'type' => 'url',
                ),
                array(
                    'key' => 'field_mobile_nav_cta_1_new_tab',
                    'label' => '新しいタブで開く',
                    'name' => 'mobile_nav_cta_1_new_tab',
                    'type' => 'true_false',
                    'default_value' => 0,
                ),
                array(
                    'key' => 'field_mobile_nav_cta_tab2',
                    'label' => 'CTA 2',
                    'name' => '',
                    'type' => 'tab',
                ),
                array(
                    'key' => 'field_mobile_nav_cta_2_image',
                    'label' => '画像',
                    'name' => 'mobile_nav_cta_2_image',
                    'type' => 'image',
                    'return_format' => 'array',
                    'preview_size' => 'medium',
                ),
                array(
                    'key' => 'field_mobile_nav_cta_2_link',
                    'label' => 'リンクURL',
                    'name' => 'mobile_nav_cta_2_link',
                    'type' => 'url',
                ),
                array(
                    'key' => 'field_mobile_nav_cta_2_new_tab',
                    'label' => '新しいタブで開く',
                    'name' => 'mobile_nav_cta_2_new_tab',
                    'type' => 'true_false',
                    'default_value' => 0,
                ),
                array(
                    'key' => 'field_mobile_nav_cta_tab3',
                    'label' => 'CTA 3',
                    'name' => '',
                    'type' => 'tab',
                ),
                array(
                    'key' => 'field_mobile_nav_cta_3_image',
                    'label' => '画像',
                    'name' => 'mobile_nav_cta_3_image',
                    'type' => 'image',
                    'return_format' => 'array',
                    'preview_size' => 'medium',
                ),
                array(
                    'key' => 'field_mobile_nav_cta_3_link',
                    'label' => 'リンクURL',
                    'name' => 'mobile_nav_cta_3_link',
                    'type' => 'url',
                ),
                array(
                    'key' => 'field_mobile_nav_cta_3_new_tab',
                    'label' => '新しいタブで開く',
                    'name' => 'mobile_nav_cta_3_new_tab',
                    'type' => 'true_false',
                    'default_value' => 0,
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'page',
                        'operator' => '==',
                        'value' => (string) $page_id,
                    ),
                ),
            ),
        ));
});

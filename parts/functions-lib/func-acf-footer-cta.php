<?php
/**
 * フッターCTA用ACFフィールド（テキスト＋リンク）
 */

/**
 * 設定用固定ページを自動作成
 */
add_action('init', function () {
    if (!get_page_by_path('footer-cta-settings')) {
        wp_insert_post(array(
            'post_title' => 'フッターCTA設定',
            'post_name' => 'footer-cta-settings',
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_author' => 1,
        ));
    }
}, 5);

/**
 * 外観メニューに「フッターCTA」を追加（設定ページの編集へリンク）
 */
add_action('admin_menu', function () {
    $page = get_page_by_path('footer-cta-settings');
    if (!$page) return;

    add_theme_page(
        'フッターCTA',
        'フッターCTA',
        'edit_pages',
        'footer-cta',
        function () use ($page) {
            wp_redirect(admin_url('post.php?post=' . $page->ID . '&action=edit'));
            exit;
        }
    );
});

/**
 * ACFフィールドグループ（2項目固定：テキスト＋リンク）
 */
add_action('acf/init', function () {
    if (!function_exists('acf_add_local_field_group')) return;

    $settings_page = get_page_by_path('footer-cta-settings');
    if (!$settings_page) return;

    $page_id = $settings_page->ID;
    acf_add_local_field_group(array(
        'key' => 'group_footer_cta',
        'title' => 'フッターCTA',
        'fields' => array(
            array(
                'key' => 'field_footer_cta_tab1',
                'label' => 'CTA 1（例：Contact）',
                'name' => '',
                'type' => 'tab',
            ),
            array(
                'key' => 'field_footer_cta_1_text',
                'label' => 'テキスト',
                'name' => 'footer_cta_1_text',
                'type' => 'text',
                'placeholder' => 'Contact',
            ),
            array(
                'key' => 'field_footer_cta_1_link',
                'label' => 'リンクURL',
                'name' => 'footer_cta_1_link',
                'type' => 'url',
                'placeholder' => 'https://example.com/contact',
            ),
            array(
                'key' => 'field_footer_cta_1_target',
                'label' => '新しいタブで開く',
                'name' => 'footer_cta_1_target',
                'type' => 'true_false',
                'default_value' => 0,
            ),
            array(
                'key' => 'field_footer_cta_tab2',
                'label' => 'CTA 2（例：Online Shop）',
                'name' => '',
                'type' => 'tab',
            ),
            array(
                'key' => 'field_footer_cta_2_text',
                'label' => 'テキスト',
                'name' => 'footer_cta_2_text',
                'type' => 'text',
                'placeholder' => 'Online Shop',
            ),
            array(
                'key' => 'field_footer_cta_2_link',
                'label' => 'リンクURL',
                'name' => 'footer_cta_2_link',
                'type' => 'url',
                'placeholder' => 'https://example.com/shop',
            ),
            array(
                'key' => 'field_footer_cta_2_target',
                'label' => '新しいタブで開く',
                'name' => 'footer_cta_2_target',
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

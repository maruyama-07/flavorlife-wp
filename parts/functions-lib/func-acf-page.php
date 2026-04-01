<?php
/**
 * 固定ページ用ACFフィールド設定
 */

add_filter('acf/load_field_groups', function ($field_groups, $post_type) {
    if ($post_type !== 'acf-field-group' || !is_admin()) {
        return $field_groups;
    }
    if (!function_exists('is_school_section_page') || !is_array($field_groups)) {
        return $field_groups;
    }
    $post_id = isset($_GET['post']) ? (int) $_GET['post'] : 0;
    if (!$post_id && function_exists('acf_get_valid_post_id')) {
        $vid = acf_get_valid_post_id(0);
        $post_id = is_numeric($vid) ? (int) $vid : 0;
    }
    if (!is_school_section_page($post_id)) {
        return $field_groups;
    }
    return array_values(array_filter($field_groups, function ($group) {
        return is_array($group) && isset($group['key']) && $group['key'] !== 'group_page_settings';
    }));
}, 25, 2);

add_filter('acf/load_field_group', function ($field_group) {
    if (!is_array($field_group) || $field_group['key'] !== 'group_page_settings' || !is_admin()) {
        return $field_group;
    }
    $post_id = isset($_GET['post']) ? (int) $_GET['post'] : 0;
    if ($post_id && get_post_type($post_id) === 'page') {
        $post = get_post($post_id);
        if ($post && $post->post_name === 'sh_evidenc') {
            return $field_group;
        }
    }
    $evidence_keys = array('field_evidence_tab', 'field_evidence_banner_text_1', 'field_evidence_banner_logo');
    $filtered = array();
    foreach ($field_group['fields'] as $field) {
        if (!in_array($field['key'], $evidence_keys, true)) {
            $filtered[] = $field;
        }
    }
    $field_group['fields'] = $filtered;
    return $field_group;
});

add_filter('admin_body_class', function ($classes) {
    $screen = get_current_screen();
    if (!$screen || $screen->id !== 'page') {
        return $classes;
    }
    $post_id = isset($_GET['post']) ? (int) $_GET['post'] : 0;
    if ($post_id && get_post_type($post_id) === 'page') {
        $post = get_post($post_id);
        if ($post && $post->post_name === 'sh_evidenc') {
            return $classes . ' page-slug-sh_evidenc';
        }
    }
    return $classes . ' hide-evidence-tab';
});

add_action('admin_enqueue_scripts', function ($hook) {
    if ($hook !== 'post.php' && $hook !== 'post-new.php') {
        return;
    }
    $screen = get_current_screen();
    if (!$screen || $screen->id !== 'page') {
        return;
    }
    $post_id = isset($_GET['post']) ? (int) $_GET['post'] : 0;
    $is_evidence_page = false;
    if ($post_id && get_post_type($post_id) === 'page') {
        $post = get_post($post_id);
        $is_evidence_page = ($post && $post->post_name === 'sh_evidenc');
    }
    if (!$is_evidence_page) {
        add_action('admin_head', function () {
            echo '<style>
                body.hide-evidence-tab .acf-tab-button[data-key="field_evidence_tab"],
                body.hide-evidence-tab .acf-field[data-name="evidence_banner_text_1"],
                body.hide-evidence-tab .acf-field[data-name="evidence_banner_logo"] { display: none !important; }
            </style>';
        }, 20);
    }
}, 10);

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
        'key' => 'group_page_settings',
        'title' => 'ページ設定',
        'fields' => array(
            array(
                'key' => 'field_page_subtitle',
                'label' => 'サブタイトル',
                'name' => 'page_subtitle',
                'type' => 'text',
                'instructions' => 'ページタイトル下に表示されるサブタイトルを入力してください',
                'required' => 0,
            ),
            array(
                'key' => 'field_hide_thumbnail',
                'label' => 'サムネイル画像を非表示',
                'name' => 'hide_thumbnail',
                'type' => 'true_false',
                'instructions' => 'チェックすると、ページ上部のサムネイル画像が非表示になります',
                'default_value' => 0,
                'ui' => 1,
            ),
            array(
                'key' => 'field_sp_thumbnail',
                'label' => 'スマホ用サムネイル画像',
                'name' => 'sp_thumbnail',
                'type' => 'image',
                'instructions' => 'スマホ表示時に別の画像を使用したい場合に設定してください（設定しない場合はアイキャッチ画像が使用されます）',
                'required' => 0,
                'return_format' => 'url',
                'preview_size' => 'medium',
            ),
            array(
                'key' => 'field_contact_section',
                'label' => 'お問い合わせセクション',
                'name' => '',
                'type' => 'tab',
            ),
            array(
                'key' => 'field_contact_section_display',
                'label' => 'お問い合わせセクションを表示',
                'name' => 'contact_section_display',
                'type' => 'true_false',
                'instructions' => 'チェックすると、ページ下部にお問い合わせセクションを表示します',
                'default_value' => 0,
                'ui' => 1,
            ),
            array(
                'key' => 'field_contact_show_phone',
                'label' => '電話ボックスを表示',
                'name' => 'contact_show_phone',
                'type' => 'true_false',
                'default_value' => 1,
                'ui' => 1,
            ),
            array(
                'key' => 'field_contact_show_form',
                'label' => 'フォームボックスを表示',
                'name' => 'contact_show_form',
                'type' => 'true_false',
                'default_value' => 1,
                'ui' => 1,
            ),
            array(
                'key' => 'field_contact_intro',
                'label' => '導入文',
                'name' => 'contact_intro',
                'type' => 'text',
                'default_value' => '資料請求・お問合せはこちらまで。お気軽にお問い合わせください。',
            ),
            array(
                'key' => 'field_contact_phone_header',
                'label' => '電話の見出し',
                'name' => 'contact_phone_header',
                'type' => 'text',
                'default_value' => 'お電話でのお問い合わせはこちらから',
            ),
            array(
                'key' => 'field_contact_phone_number',
                'label' => '電話番号',
                'name' => 'contact_phone_number',
                'type' => 'text',
                'default_value' => '0120-907-187',
            ),
            array(
                'key' => 'field_contact_reception',
                'label' => '受付時間',
                'name' => 'contact_reception',
                'type' => 'text',
                'default_value' => '【受付時間】平日 9:00~18:00',
            ),
            array(
                'key' => 'field_contact_form_header',
                'label' => 'フォームの見出し',
                'name' => 'contact_form_header',
                'type' => 'text',
                'default_value' => '資料請求・お問い合わせはこちらから',
            ),
            array(
                'key' => 'field_contact_form_button',
                'label' => 'フォームボタンの文言',
                'name' => 'contact_form_button',
                'type' => 'text',
                'default_value' => 'お問い合わせフォーム',
            ),
            array(
                'key' => 'field_contact_form_url',
                'label' => 'フォームのURL',
                'name' => 'contact_form_url',
                'type' => 'url',
            ),
            array(
                'key' => 'field_contact_form_description',
                'label' => 'フォームボックス説明文',
                'name' => 'contact_form_description',
                'type' => 'textarea',
                'rows' => 3,
                'default_value' => "ご希望の方はこちらからお問い合わせをお願いします。\n当社営業担当者よりご連絡させていただきます。",
                'instructions' => function_exists('tool_acf_paragraph_field_instructions') ? tool_acf_paragraph_field_instructions() : '',
            ),
            array(
                'key' => 'field_shop_cta_tab',
                'label' => 'オンラインショップCTA',
                'name' => '',
                'type' => 'tab',
            ),
            array(
                'key' => 'field_shop_cta_display',
                'label' => 'オンラインショップCTAを表示',
                'name' => 'shop_cta_display',
                'type' => 'true_false',
                'default_value' => 0,
                'ui' => 1,
            ),
            array(
                'key' => 'field_shop_cta_image',
                'label' => '背景画像',
                'name' => 'shop_cta_image',
                'type' => 'image',
                'return_format' => 'url',
                'preview_size' => 'medium',
            ),
            array(
                'key' => 'field_shop_cta_title',
                'label' => '見出しテキスト',
                'name' => 'shop_cta_title',
                'type' => 'text',
                'default_value' => 'Online Shop',
            ),
            array(
                'key' => 'field_shop_cta_button_text',
                'label' => 'ボタンのテキスト',
                'name' => 'shop_cta_button_text',
                'type' => 'text',
                'default_value' => 'ご購入はこちら',
            ),
            array(
                'key' => 'field_shop_cta_button_url',
                'label' => 'ボタンのリンクURL',
                'name' => 'shop_cta_button_url',
                'type' => 'url',
            ),
            array(
                'key' => 'field_shop_cta_new_tab',
                'label' => '新規タブで開く',
                'name' => 'shop_cta_new_tab',
                'type' => 'true_false',
                'default_value' => 0,
                'ui' => 1,
            ),
            array(
                'key' => 'field_evidence_tab',
                'label' => 'Evidenceバナー',
                'name' => '',
                'type' => 'tab',
            ),
            array(
                'key' => 'field_evidence_banner_text_1',
                'label' => 'テキスト',
                'name' => 'evidence_banner_text_1',
                'type' => 'textarea',
                'instructions' => 'スマホ時のみ改行したい位置に {{sp}} を入力してください。',
                'rows' => 2,
            ),
            array(
                'key' => 'field_evidence_banner_logo',
                'label' => 'ロゴ画像',
                'name' => 'evidence_banner_logo',
                'type' => 'image',
                'return_format' => 'array',
                'preview_size' => 'medium',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'page',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
    ));
}

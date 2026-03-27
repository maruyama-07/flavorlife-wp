<?php
/**
 * TOP Productセクション用ACFフィールド（固定12項目）
 * トップページの固定ページで、リンク・ロゴ・テキストを管理
 */

add_action('acf/init', function () {
    if (!function_exists('acf_add_local_field_group')) return;

    $defaults = array(
        array('type' => 'logo', 'text' => 'sleep hug', 'url' => 'https://sleephug.jp/', 'slug' => 'sleephug'),
        array('type' => 'text', 'text' => 'Essential Oils', 'url' => 'https://www.flavorlife.com/shopbrand/essentialoil/', 'slug' => 'essential-oils'),
        array('type' => 'text', 'text' => 'キャリアオイル', 'url' => 'https://www.flavorlife.com/shopbrand/allcarrieroil/', 'slug' => 'carrier-oil'),
        array('type' => 'text', 'text' => 'アロマディフューザー', 'url' => 'https://www.flavorlife.com/shopbrand/aromadiffuser/', 'slug' => 'diffuser'),
        array('type' => 'text', 'text' => 'ハーブティー', 'url' => 'https://www.flavorlife.com/shopbrand/herbtea/', 'slug' => 'herbtea'),
        array('type' => 'logo', 'text' => 'QUEEN MARY', 'url' => 'https://www.flavorlife.com/shopbrand/eoqm/', 'slug' => 'queenmary'),
        array('type' => 'logo', 'text' => 'hana to mi', 'url' => 'https://hana-to-mi.jp/', 'slug' => 'hanatomi'),
        array('type' => 'logo', 'text' => "'ala Lehua", 'url' => 'https://www.shop.alalehua.com/', 'slug' => 'alalehua'),
        array('type' => 'text', 'text' => 'その他', 'url' => '', 'slug' => 'other'),
        array('type' => 'text', 'text' => '', 'url' => '', 'slug' => ''),
        array('type' => 'text', 'text' => '', 'url' => '', 'slug' => ''),
        array('type' => 'text', 'text' => '', 'url' => '', 'slug' => ''),
    );

    $fields = array();
    for ($i = 1; $i <= 12; $i++) {
        $d = $defaults[$i - 1];
        $fields[] = array(
            'key' => 'field_top_product_tab_' . $i,
            'label' => 'Product ' . $i,
            'name' => '',
            'type' => 'tab',
        );
        $fields[] = array(
            'key' => 'field_top_product_type_' . $i,
            'label' => '表示タイプ',
            'name' => 'top_product_type_' . $i,
            'type' => 'radio',
            'choices' => array(
                'logo' => 'ロゴ画像',
                'text' => 'テキスト',
            ),
            'default_value' => $d['type'],
        );
        $fields[] = array(
            'key' => 'field_top_product_link_' . $i,
            'label' => 'リンクURL',
            'name' => 'top_product_link_' . $i,
            'type' => 'url',
            'instructions' => '例: https://example.com または /product/xxx',
            'default_value' => $d['url'],
        );
        $fields[] = array(
            'key' => 'field_top_product_new_tab_' . $i,
            'label' => '新しいタブで開く',
            'name' => 'top_product_new_tab_' . $i,
            'type' => 'true_false',
            'default_value' => 1,
        );
        $fields[] = array(
            'key' => 'field_top_product_image_' . $i,
            'label' => 'ロゴ画像',
            'name' => 'top_product_image_' . $i,
            'type' => 'image',
            'instructions' => '未設定の場合はテーマのデフォルト画像を使用',
            'return_format' => 'array',
            'preview_size' => 'medium',
            'conditional_logic' => array(
                array(
                    array('field' => 'field_top_product_type_' . $i, 'operator' => '==', 'value' => 'logo'),
                ),
            ),
        );
        $fields[] = array(
            'key' => 'field_top_product_text_' . $i,
            'label' => '表示テキスト',
            'name' => 'top_product_text_' . $i,
            'type' => 'text',
            'default_value' => $d['text'],
            'conditional_logic' => array(
                array(
                    array('field' => 'field_top_product_type_' . $i, 'operator' => '==', 'value' => 'text'),
                ),
            ),
        );
    }

    acf_add_local_field_group(array(
        'key' => 'group_top_product',
        'title' => 'TOP Product設定',
        'fields' => $fields,
        'location' => array(
            array(
                array(
                    'param' => 'page_type',
                    'operator' => '==',
                    'value' => 'front_page',
                ),
            ),
        ),
        'menu_order' => 5,
    ));
});

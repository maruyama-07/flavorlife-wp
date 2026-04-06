<?php
/**
 * /school/order/ 固定ページ用 ACF（お申込みサポート UI）
 */

add_action('acf/init', function () {
    if (! function_exists('acf_add_local_field_group')) {
        return;
    }

    $page = get_page_by_path('school/order');
    if (! $page instanceof WP_Post) {
        return;
    }

    acf_add_local_field_group(array(
        'key' => 'group_school_order',
        'title' => 'お申込み・サポート（/school/order/）',
        'fields' => array(
            array(
                'key' => 'field_school_order_heading',
                'label' => '見出し（白枠内タイトル）',
                'name' => 'school_order_heading',
                'type' => 'text',
                'instructions' => 'ページ上部ヒーローの H1 とは別に、本文エリア先頭に表示するタイトルです。',
                'required' => 0,
            ),
            array(
                'key' => 'field_school_order_intro',
                'label' => 'リード文',
                'name' => 'school_order_intro',
                'type' => 'textarea',
                'instructions' => '見出し直下の説明文（中央寄せ）。改行はそのまま反映されます。',
                'rows' => 4,
                'new_lines' => 'br',
            ),
            array(
                'key' => 'field_school_order_body',
                'label' => '本文（リスト・補足）',
                'name' => 'school_order_body',
                'type' => 'textarea',
                'instructions' => 'LINE・電話などの案内。URL を含めても構いません（左寄せのブロック内に表示）。',
                'rows' => 10,
                'new_lines' => 'br',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'page',
                    'operator' => '==',
                    'value' => (string) $page->ID,
                ),
            ),
        ),
    ));
});

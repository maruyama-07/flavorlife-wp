<?php
/**
 * スクール講座（course_school）・講座カテゴリー用 ACF
 */

add_action('acf/init', function () {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group(array(
        'key' => 'group_course_school',
        'title' => '講座情報',
        'fields' => array(
            array(
                'key' => 'field_course_school_recruiting',
                'label' => '募集中を表示',
                'name' => 'course_school_recruiting',
                'type' => 'true_false',
                'instructions' => 'オンのとき一覧に「募集中」バッジを表示します。本文エリアは講座詳細ページ（クラシックエディタ）に表示されます。',
                'default_value' => 1,
                'ui' => 1,
            ),
            array(
                'key' => 'field_course_school_point',
                'label' => 'POINT（キャッチ）',
                'name' => 'course_school_point',
                'type' => 'textarea',
                'rows' => 2,
                'instructions' => '未入力のときは POINT 行ごと非表示にします。',
            ),
            array(
                'key' => 'field_course_school_dates',
                'label' => '開催日・日程テキスト',
                'name' => 'course_school_dates',
                'type' => 'text',
                'instructions' => '例: 2/17 (火) ・ 3/17 (火) ・ 3/26 (木)',
            ),
            array(
                'key' => 'field_course_school_detail',
                'label' => '詳細テキスト',
                'name' => 'course_school_detail',
                'type' => 'textarea',
                'rows' => 4,
                'instructions' => '一覧カード左下の短い説明です。詳細ページの本文は「本文」欄に入力してください。',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'course_school',
                ),
            ),
        ),
        'position' => 'acf_after_title',
    ));

    acf_add_local_field_group(array(
        'key' => 'group_course_school_category',
        'title' => '講座カテゴリー表示',
        'fields' => array(
            array(
                'key' => 'field_course_school_cat_label_main',
                'label' => '表示テキスト（メイン）',
                'name' => 'course_school_cat_label_main',
                'type' => 'textarea',
                'rows' => 3,
                'new_lines' => '',
                'instructions' => '一覧バッジ・左ナビのメイン表示です。改行するとそのまま折り返して表示されます。空欄のときは上の「名前」をそのまま使います。',
                'placeholder' => '',
            ),
            array(
                'key' => 'field_course_school_cat_label_sub',
                'label' => '表示テキスト（サブ・小さめ）',
                'name' => 'course_school_cat_label_sub',
                'type' => 'text',
                'instructions' => '2行目。省略可。入力するとメインより小さい字で表示されます。',
                'placeholder' => '',
            ),
            array(
                'key' => 'field_course_school_cat_badge_tone',
                'label' => '一覧バッジの色',
                'name' => 'course_school_cat_badge_tone',
                'type' => 'select',
                'choices' => function_exists('course_school_get_badge_tone_choices')
                    ? course_school_get_badge_tone_choices()
                    : array(
                        'green' => 'グリーン',
                        'teal' => 'ティール',
                    ),
                'default_value' => 'teal',
                'allow_null' => 0,
                'multiple' => 0,
                'return_format' => 'value',
                'ui' => 1,
                'ajax' => 0,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'taxonomy',
                    'operator' => '==',
                    'value' => 'course_school_category',
                ),
            ),
        ),
    ));

    $course_page_id = function_exists('school_section_get_course_page_id') ? school_section_get_course_page_id() : 0;
    if ($course_page_id) {
        acf_add_local_field_group(array(
            'key' => 'group_course_page_top',
            'title' => '講座一覧ページ・イントロ＆カテゴリーカード',
            'fields' => array(
                array(
                    'key' => 'field_course_page_top_intro_lead',
                    'label' => 'リード文（上段）',
                    'name' => 'course_page_top_intro_lead',
                    'type' => 'textarea',
                    'rows' => 4,
                    'new_lines' => '',
                    'instructions' => '中央揃えで表示。改行可。',
                ),
                array(
                    'key' => 'field_course_page_top_intro_body',
                    'label' => '本文（下段・小さめ）',
                    'name' => 'course_page_top_intro_body',
                    'type' => 'textarea',
                    'rows' => 10,
                    'new_lines' => '',
                    'instructions' => 'リードより小さい字で表示。改行可。',
                ),
                array(
                    'key' => 'field_course_page_top_section_ja',
                    'label' => 'セクション見出し（日本語）',
                    'name' => 'course_page_top_section_ja',
                    'type' => 'text',
                ),
                array(
                    'key' => 'field_course_page_top_section_en',
                    'label' => 'セクション見出し（英語・小さめ）',
                    'name' => 'course_page_top_section_en',
                    'type' => 'text',
                ),
                array(
                    'key' => 'field_course_page_top_cards_note',
                    'label' => 'カテゴリーカード',
                    'name' => 'course_page_top_cards_note',
                    'type' => 'message',
                    'message' => 'カード1〜3で最大3枚まで設定できます。（リピーターはACF Pro専用のため、無料版向けに固定枠にしています）',
                    'new_lines' => 'wpautop',
                ),
                array(
                    'key' => 'field_course_page_top_card_1_title',
                    'label' => 'カード1・タイトル',
                    'name' => 'course_page_top_card_1_title',
                    'type' => 'text',
                ),
                array(
                    'key' => 'field_course_page_top_card_1_image',
                    'label' => 'カード1・画像',
                    'name' => 'course_page_top_card_1_image',
                    'type' => 'image',
                    'return_format' => 'array',
                    'preview_size' => 'medium',
                ),
                array(
                    'key' => 'field_course_page_top_card_1_link',
                    'label' => 'カード1・リンク先URL',
                    'name' => 'course_page_top_card_1_link',
                    'type' => 'url',
                    'instructions' => '例: 講座一覧URLに ?course_cat=カテゴリースラッグ',
                ),
                array(
                    'key' => 'field_course_page_top_card_2_title',
                    'label' => 'カード2・タイトル',
                    'name' => 'course_page_top_card_2_title',
                    'type' => 'text',
                ),
                array(
                    'key' => 'field_course_page_top_card_2_image',
                    'label' => 'カード2・画像',
                    'name' => 'course_page_top_card_2_image',
                    'type' => 'image',
                    'return_format' => 'array',
                    'preview_size' => 'medium',
                ),
                array(
                    'key' => 'field_course_page_top_card_2_link',
                    'label' => 'カード2・リンク先URL',
                    'name' => 'course_page_top_card_2_link',
                    'type' => 'url',
                ),
                array(
                    'key' => 'field_course_page_top_card_3_title',
                    'label' => 'カード3・タイトル',
                    'name' => 'course_page_top_card_3_title',
                    'type' => 'text',
                ),
                array(
                    'key' => 'field_course_page_top_card_3_image',
                    'label' => 'カード3・画像',
                    'name' => 'course_page_top_card_3_image',
                    'type' => 'image',
                    'return_format' => 'array',
                    'preview_size' => 'medium',
                ),
                array(
                    'key' => 'field_course_page_top_card_3_link',
                    'label' => 'カード3・リンク先URL',
                    'name' => 'course_page_top_card_3_link',
                    'type' => 'url',
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'page',
                        'operator' => '==',
                        'value' => (string) $course_page_id,
                    ),
                ),
            ),
        ));
    }
});

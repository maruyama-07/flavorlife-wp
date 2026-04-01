<?php
/**
 * 受講生の声：一覧イントロ（固定ページ）・カード用リードなど
 */

/**
 * 一覧のイントロ文（固定ページ「受講生の声」の ACF。未入力項目は下記デフォルト）
 *
 * @return array{heading_lines:string[],body:string}
 */
function school_voice_get_archive_intro()
{
    $defaults = array(
        'heading_lines' => array(
            '知識は一生のお守り。香りが支える私の物語。',
            '学生からプロまで、ここで見つけた新しい自分。',
        ),
        'body'          => 'スクールで学んだ知識は、卒業後の暮らしの中で、さまざまな形で花開いていきます。ここでは、受講生の皆さんが語る、学びと香りがもたらした変化の物語をご紹介します。',
    );
    $intro = $defaults;

    $page_id = 0;
    if (function_exists('school_section_is_voice_page') && school_section_is_voice_page()) {
        $page_id = (int) get_queried_object_id();
    }

    if ($page_id && function_exists('get_field')) {
        $raw_heading = get_field('voice_archive_intro_heading', $page_id);
        if (is_string($raw_heading) && trim($raw_heading) !== '') {
            $lines = preg_split('/\r\n|\r|\n/', $raw_heading);
            $lines = array_values(array_filter(array_map('trim', $lines), function ($line) {
                return $line !== '';
            }));
            if (!empty($lines)) {
                $intro['heading_lines'] = $lines;
            }
        }

        $body = get_field('voice_archive_intro_body', $page_id);
        if (is_string($body) && $body !== '') {
            $intro['body'] = $body;
        }
    }

    return apply_filters('school_voice_archive_intro', $intro);
}

/**
 * 一覧ページ下部の注記（固定ページ ACF。空なら空文字）
 *
 * @return string
 */
function school_voice_get_archive_footer_note()
{
    $page_id = 0;
    if (function_exists('school_section_is_voice_page') && school_section_is_voice_page()) {
        $page_id = (int) get_queried_object_id();
    } elseif (!empty($GLOBALS['school_voice_archive_fallback'])) {
        return '';
    }

    if (!$page_id && function_exists('get_page_by_path')) {
        $p = get_page_by_path('school/voice');
        $page_id = $p ? (int) $p->ID : 0;
    }

    if (!$page_id || !function_exists('get_field')) {
        return '';
    }

    $raw = get_field('voice_archive_footer_note', $page_id);

    return is_string($raw) ? trim($raw) : '';
}

add_action('acf/init', function () {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group(array(
        'key' => 'group_voice_school_card',
        'title' => '受講生の声（一覧カード）',
        'fields' => array(
            array(
                'key' => 'field_voice_school_quote',
                'label' => '一覧用キャッチ（「」付き推奨）',
                'name' => 'voice_school_quote',
                'type' => 'textarea',
                'rows' => 3,
                'instructions' => '未入力のときは抜粋（excerpt）を表示します。',
            ),
            array(
                'key' => 'field_voice_school_detail_image',
                'label' => '詳細ページ・右カラム画像',
                'name' => 'voice_school_detail_image',
                'type' => 'image',
                'return_format' => 'array',
                'preview_size' => 'medium',
                'instructions' => '設定すると、タイトル直下でアイキャッチ（左）とこの画像（右）の2カラム表示になります。未設定のときはアイキャッチのみ中央に表示します。',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'voice_school',
                ),
            ),
        ),
        'position' => 'acf_after_title',
    ));

    $voice_page = get_page_by_path('school/voice');
    if (!$voice_page) {
        return;
    }

    acf_add_local_field_group(array(
        'key' => 'group_voice_school_archive_page',
        'title' => '受講生の声（一覧・イントロ文）',
        'fields' => array(
            array(
                'key' => 'field_voice_archive_intro_heading',
                'label' => 'イントロ見出し（h2）',
                'name' => 'voice_archive_intro_heading',
                'type' => 'textarea',
                'rows' => 4,
                'instructions' => '改行ごとに1行として表示されます（未入力のときはデフォルトの文言が表示されます）。',
                'placeholder' => "知識は一生のお守り。香りが支える私の物語。\n学生からプロまで、ここで見つけた新しい自分。",
            ),
            array(
                'key' => 'field_voice_archive_intro_body',
                'label' => 'リード文',
                'name' => 'voice_archive_intro_body',
                'type' => 'textarea',
                'rows' => 4,
                'instructions' => '未入力のときはデフォルトの文言が表示されます。' . "\n\n" . (function_exists('tool_acf_paragraph_field_instructions') ? tool_acf_paragraph_field_instructions() : ''),
            ),
            array(
                'key' => 'field_voice_archive_footer_note',
                'label' => '一覧下部の注記（枠付き）',
                'name' => 'voice_archive_footer_note',
                'type' => 'textarea',
                'rows' => 5,
                'new_lines' => '',
                'instructions' => '一覧のカード・ページネーションの下に枠付きで表示します。未入力のときは非表示です。' . "\n\n" . (function_exists('tool_acf_paragraph_field_instructions') ? tool_acf_paragraph_field_instructions() : ''),
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'page',
                    'operator' => '==',
                    'value' => (string) (int) $voice_page->ID,
                ),
            ),
        ),
        'position' => 'acf_after_title',
        'menu_order' => 0,
    ));
});

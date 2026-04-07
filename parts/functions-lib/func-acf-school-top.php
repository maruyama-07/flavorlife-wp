<?php
/**
 * スクールトップ用 ACF（イントロ・4カード / Category / Seasonal Topics を1グループのタブで管理）
 */

add_action('after_setup_theme', function () {
    add_image_size('school-card-arch', 560, 720, true);
});

add_action('acf/init', function () {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    $school_page_id = function_exists('school_section_get_root_page_id') ? school_section_get_root_page_id() : 0;

    /**
     * 表示場所ルール（OR）
     * 1) スラッグ school の固定ページIDが取れるときはそのページに紐づけ
     * 2) 取れない本番など: 親なし（最上位）かつテンプレート「スクール用」の固定ページ
     *    ※早期 return しない。ID=0 だと従来はグループ自体が登録されず ACF が一切出なかった。
     */
    $location = array();
    if ($school_page_id > 0) {
        $location[] = array(
            array(
                'param' => 'page',
                'operator' => '==',
                'value' => (string) $school_page_id,
            ),
        );
    }
    $location[] = array(
        array(
            'param' => 'page_template',
            'operator' => '==',
            'value' => 'template-school.php',
        ),
        array(
            'param' => 'page_type',
            'operator' => '==',
            'value' => 'top_level',
        ),
    );

    if (is_admin() && $school_page_id < 1 && current_user_can('manage_options')) {
        add_action('admin_notices', function () {
            echo '<div class="notice notice-warning is-dismissible"><p>';
            echo esc_html('スクールトップ用ACF: スラッグ「school」の固定ページが見つかりません。');
            echo ' ';
            echo esc_html('フィールドは「テンプレート: スクール用」かつ「最上位の固定ページ」に表示されます。フロントの取得は school スラッグに依存するため、該当ページのスラッグを school にしてください。');
            echo '</p></div>';
        });
    }

    $paragraph_note = function_exists('tool_acf_paragraph_field_instructions') ? tool_acf_paragraph_field_instructions() : '';

    $fields = array(
        array(
            'key' => 'field_school_top_tab_intro',
            'label' => 'イントロ・4カード',
            'name' => '',
            'type' => 'tab',
            'placement' => 'top',
        ),
        array(
            'key' => 'field_school_top_intro_brand',
            'label' => 'イントロ見出し',
            'name' => 'school_top_intro_brand',
            'type' => 'text',
            'required' => 0,
            'default_value' => 'Flavorlife Aromatherapy School',
        ),
        array(
            'key' => 'field_school_top_intro_lead',
            'label' => 'イントロリード',
            'name' => 'school_top_intro_lead',
            'type' => 'text',
            'required' => 0,
            'default_value' => '確かな知識で、香りはもっと自由になる。',
        ),
        array(
            'key' => 'field_school_top_intro_body',
            'label' => 'イントロ本文',
            'name' => 'school_top_intro_body',
            'type' => 'textarea',
            'required' => 0,
            'rows' => 6,
            'new_lines' => '',
            'instructions' => '改行はフロントで反映されます。' . "\n\n" . $paragraph_note,
        ),
    );

    $card_labels = array(
        1 => '1列目（左）',
        2 => '2列目',
        3 => '3列目',
        4 => '4列目（右）',
    );
    foreach ($card_labels as $num => $label) {
        $fields[] = array(
            'key' => 'field_school_top_card_' . $num,
            'label' => 'カード: ' . $label,
            'name' => 'school_top_card_' . $num,
            'type' => 'post_object',
            'instructions' => $num === 1 ? '任意の固定ページを指定できます。未設定の列は school / course / first / voice のスラッグで自動表示します。' : '',
            'required' => 0,
            'post_type' => array('page'),
            'return_format' => 'object',
            'ui' => 1,
            'allow_null' => 1,
        );
    }

    $fields[] = array(
        'key' => 'field_school_top_tab_category',
        'label' => 'Categoryカード',
        'name' => '',
        'type' => 'tab',
        'placement' => 'top',
    );

    $cat_labels = array(
        1 => '1列目（左）',
        2 => '2列目（中）',
        3 => '3列目（右）',
    );
    foreach ($cat_labels as $num => $label) {
        $fields[] = array(
            'key' => 'field_school_category_title_' . $num,
            'label' => 'カテゴリカード: ' . $label . '（タイトル）',
            'name' => 'school_category_title_' . $num,
            'type' => 'text',
            'required' => 0,
        );
        $fields[] = array(
            'key' => 'field_school_category_image_' . $num,
            'label' => 'カテゴリカード: ' . $label . '（画像）',
            'name' => 'school_category_image_' . $num,
            'type' => 'image',
            'return_format' => 'array',
            'preview_size' => 'school-card-arch',
            'required' => 0,
        );
        $fields[] = array(
            'key' => 'field_school_category_link_' . $num,
            'label' => 'カテゴリカード: ' . $label . '（リンク先URL）',
            'name' => 'school_category_link_' . $num,
            'type' => 'url',
            'required' => 0,
            'instructions' => '未入力の列はフロントに表示されません。',
        );
        $fields[] = array(
            'key' => 'field_school_category_desc_' . $num,
            'label' => 'カテゴリカード: ' . $label . '（説明文）',
            'name' => 'school_category_desc_' . $num,
            'type' => 'textarea',
            'rows' => 4,
            'new_lines' => 'br',
            'required' => 0,
            'instructions' => $paragraph_note,
        );
    }

    $fields[] = array(
        'key' => 'field_school_top_tab_seasonal',
        'label' => 'Seasonal Topics',
        'name' => '',
        'type' => 'tab',
        'placement' => 'top',
    );

    $fields[] = array(
        'key' => 'field_school_seasonal_title',
        'label' => '見出し（英語）',
        'name' => 'school_seasonal_title',
        'type' => 'text',
        'default_value' => 'Seasonal Topics',
    );
    $fields[] = array(
        'key' => 'field_school_seasonal_subtitle',
        'label' => '見出し（日本語）',
        'name' => 'school_seasonal_subtitle',
        'type' => 'text',
        'default_value' => '季節のおすすめ',
    );
    $fields[] = array(
        'key' => 'field_school_seasonal_image',
        'label' => '画像',
        'name' => 'school_seasonal_image',
        'type' => 'image',
        'return_format' => 'array',
        'preview_size' => 'large',
        'instructions' => '未設定時は assets/images/school/seasonal-topics.png を表示します。',
    );
    $fields[] = array(
        'key' => 'field_school_seasonal_heading',
        'label' => 'トピックタイトル',
        'name' => 'school_seasonal_heading',
        'type' => 'textarea',
        'rows' => 3,
        'new_lines' => '',
        'instructions' => $paragraph_note,
    );
    $fields[] = array(
        'key' => 'field_school_seasonal_body',
        'label' => '本文',
        'name' => 'school_seasonal_body',
        'type' => 'textarea',
        'rows' => 8,
        'new_lines' => '',
        'instructions' => $paragraph_note,
    );
    $fields[] = array(
        'key' => 'field_school_seasonal_button_text',
        'label' => 'ボタンテキスト',
        'name' => 'school_seasonal_button_text',
        'type' => 'text',
        'default_value' => 'more',
    );
    $fields[] = array(
        'key' => 'field_school_seasonal_button_url',
        'label' => 'ボタンURL',
        'name' => 'school_seasonal_button_url',
        'type' => 'url',
    );

    acf_add_local_field_group(array(
        'key' => 'group_school_top_page',
        'title' => 'スクールトップ',
        'fields' => $fields,
        'location' => $location,
        'position' => 'acf_after_title',
        'style' => 'default',
    ));
});

function school_top_get_card_pages()
{
    $root = function_exists('school_section_get_root_page_id') ? school_section_get_root_page_id() : 0;
    if (!$root || !is_page() || (int) get_queried_object_id() !== $root) {
        return array();
    }

    $page_id = (int) get_queried_object_id();
    $defaults = school_top_default_card_pages();
    $out = array();

    for ($i = 1; $i <= 4; $i++) {
        $p = function_exists('get_field') ? get_field('school_top_card_' . $i, $page_id) : null;
        if ($p instanceof WP_Post) {
            $out[] = $p;
            continue;
        }
        if (!empty($defaults[$i])) {
            $out[] = $defaults[$i];
        }
    }

    return $out;
}

/**
 * @return array{brand:string, lead:string, body:string}
 */
function school_top_get_intro_content()
{
    $defaults = array(
        'brand' => 'Flavorlife Aromatherapy School',
        'lead' => '確かな知識で、香りはもっと自由になる。',
        'body' => "アロマテラピーは曖昧な世界ではありません。\n植物学・身体・心理の関係性を体系的に学び、“わかる”から“扱える”へ導く、学びの場。\nそして楽しく学ぶことが、精油の理解を深め、香りを暮らしに取り入れるプロの道をつくります。\n当スクールでは初めての方でも、知識のある方でも、発見と奥深さを学べるコースを取り揃えています。",
    );

    $root = function_exists('school_section_get_root_page_id') ? school_section_get_root_page_id() : 0;
    if (!$root || !is_page() || (int) get_queried_object_id() !== $root || !function_exists('get_field')) {
        return $defaults;
    }

    $page_id = (int) get_queried_object_id();
    $brand = trim((string) get_field('school_top_intro_brand', $page_id));
    $lead = trim((string) get_field('school_top_intro_lead', $page_id));
    $body = trim((string) get_field('school_top_intro_body', $page_id));

    return array(
        'brand' => $brand !== '' ? $brand : $defaults['brand'],
        'lead' => $lead !== '' ? $lead : $defaults['lead'],
        'body' => $body !== '' ? $body : $defaults['body'],
    );
}

/**
 * @return array<int, WP_Post|null>
 */
function school_top_default_card_pages()
{
    $root = function_exists('school_section_get_root_page_id') ? school_section_get_root_page_id() : 0;
    $base = $root ? (string) get_post_field('post_name', $root) : 'school';
    $paths = array(
        1 => $base,
        2 => $base . '/course',
        3 => $base . '/first',
        4 => $base . '/voice',
    );
    $out = array();

    foreach ($paths as $slot => $path) {
        $p = get_page_by_path($path);
        $out[$slot] = ($p instanceof WP_Post) ? $p : null;
    }

    return $out;
}

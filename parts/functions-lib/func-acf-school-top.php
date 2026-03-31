<?php
/**
 * スクールトップ「イントロ＋4カード」用 ACF
 */

add_action('after_setup_theme', function () {
    add_image_size('school-card-arch', 560, 720, true);
});

add_action('acf/init', function () {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    $school_page_id = function_exists('school_section_get_root_page_id') ? school_section_get_root_page_id() : 0;
    if (!$school_page_id) {
        return;
    }

    $fields = array(
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
            'instructions' => '改行はフロントで <br> に変換されます。',
        ),
    );
    $labels = array(
        1 => '1列目（左）',
        2 => '2列目',
        3 => '3列目',
        4 => '4列目（右）',
    );

    foreach ($labels as $num => $label) {
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

    acf_add_local_field_group(array(
        'key' => 'group_school_top_intro_cards',
        'title' => 'スクールトップ（イントロ・4カード）',
        'fields' => $fields,
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

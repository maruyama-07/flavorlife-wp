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

    $card_fields = array();
    $labels = array(
        1 => '1列目（左）',
        2 => '2列目',
        3 => '3列目',
        4 => '4列目（右）',
    );

    foreach ($labels as $num => $label) {
        $card_fields[] = array(
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
        'fields' => $card_fields,
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

<?php
/**
 * スクールトップ「Category」セクション用 ACF
 */

add_action('acf/init', function () {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    $school_page_id = function_exists('school_section_get_root_page_id') ? school_section_get_root_page_id() : 0;
    if (!$school_page_id) {
        return;
    }

    $fields = array();
    $labels = array(
        1 => '1列目（左）',
        2 => '2列目（中）',
        3 => '3列目（右）',
    );

    foreach ($labels as $num => $label) {
        $fields[] = array(
            'key' => 'field_school_category_page_' . $num,
            'label' => 'カテゴリカード: ' . $label . '（ページ）',
            'name' => 'school_category_page_' . $num,
            'type' => 'post_object',
            'post_type' => array('page'),
            'return_format' => 'object',
            'ui' => 1,
            'allow_null' => 1,
            'required' => 0,
        );
        $fields[] = array(
            'key' => 'field_school_category_desc_' . $num,
            'label' => 'カテゴリカード: ' . $label . '（説明文）',
            'name' => 'school_category_desc_' . $num,
            'type' => 'textarea',
            'rows' => 4,
            'new_lines' => 'br',
            'required' => 0,
            'instructions' => '未入力時は選択ページの抜粋（excerpt）を表示します。' . "\n\n" . (function_exists('tool_acf_paragraph_field_instructions') ? tool_acf_paragraph_field_instructions() : ''),
        );
    }

    acf_add_local_field_group(array(
        'key' => 'group_school_top_category_cards',
        'title' => 'スクールトップ（Categoryカード）',
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

/**
 * Category セクション表示データ
 *
 * @return array<int, array{post:WP_Post, description:string}>
 */
function school_top_get_category_items()
{
    $root = function_exists('school_section_get_root_page_id') ? school_section_get_root_page_id() : 0;
    if (!$root || !is_page() || (int) get_queried_object_id() !== $root) {
        return array();
    }

    $page_id = (int) get_queried_object_id();
    $defaults = school_top_default_category_pages();
    $out = array();

    for ($i = 1; $i <= 3; $i++) {
        $p = function_exists('get_field') ? get_field('school_category_page_' . $i, $page_id) : null;
        if (!$p instanceof WP_Post) {
            $p = !empty($defaults[$i]) && $defaults[$i] instanceof WP_Post ? $defaults[$i] : null;
        }
        if (!$p instanceof WP_Post) {
            continue;
        }

        $description = function_exists('get_field') ? (string) get_field('school_category_desc_' . $i, $page_id) : '';
        if ($description === '') {
            $description = (string) get_post_field('post_excerpt', $p->ID);
        }

        $out[] = array(
            'post' => $p,
            'description' => $description,
        );
    }

    return $out;
}

/**
 * @return array<int, WP_Post|null>
 */
function school_top_default_category_pages()
{
    $root = function_exists('school_section_get_root_page_id') ? school_section_get_root_page_id() : 0;
    $base = $root ? (string) get_post_field('post_name', $root) : 'school';
    $paths = array(
        1 => $base . '/aroma',
        2 => $base . '/herb',
        3 => $base . '/oneday',
    );

    $out = array();
    foreach ($paths as $slot => $path) {
        $p = get_page_by_path($path);
        $out[$slot] = ($p instanceof WP_Post) ? $p : null;
    }
    return $out;
}

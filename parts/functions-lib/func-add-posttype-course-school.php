<?php
/**
 * スクール講座一覧（/school/course/ 固定ページ＋WP_Query）
 * CPT: course_school / タクソノミー: course_school_category
 */

add_filter('query_vars', function ($vars) {
    $vars[] = 'course_cat';
    return $vars;
});

add_action('init', 'course_school_register_post_type', 5);
function course_school_register_post_type()
{
    register_post_type(
        'course_school',
        array(
            'label' => 'スクール講座',
            'labels' => array(
                'name' => 'スクール講座',
                'singular_name' => 'スクール講座',
                'add_new_item' => '講座を追加',
                'edit_item' => '講座を編集',
                'all_items' => 'スクール講座一覧',
            ),
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_rest' => true,
            'publicly_queryable' => true,
            'exclude_from_search' => true,
            'has_archive' => false,
            'rewrite' => array(
                'slug' => 'school/lesson',
                'with_front' => false,
            ),
            'capability_type' => 'post',
            'map_meta_cap' => true,
            'menu_position' => 16,
            'menu_icon' => 'dashicons-welcome-learn-more',
            'supports' => array('title', 'editor', 'thumbnail', 'page-attributes'),
        )
    );
}

/**
 * 一覧バッジの色プリセット（スラッグ・表示名・HEX）。
 * 実際の背景色は src/scss/school/object/_p-school-course.scss の
 * .p-school-course-card__badge--{スラッグ} と必ず揃えること。
 *
 * @return array<string, array{label:string, hex:string}>
 */
function course_school_get_badge_tone_presets()
{
    return array(
        'green' => array(
            'label' => 'グリーン',
            'hex' => '#339966',
        ),
        'teal' => array(
            'label' => 'ティール',
            'hex' => '#336666',
        ),
        'navy' => array('label' => 'ネイビー', 'hex' => '#666633'),
        'blue' => array('label' => 'ブルー', 'hex' => '#1565c0'),
        'purple' => array('label' => 'パープル', 'hex' => '#6a1b9a'),
        'brown' => array('label' => 'ブラウン', 'hex' => '#998067'),
        'orange' => array('label' => 'オレンジ', 'hex' => '#e65100'),
        'rose' => array('label' => 'ローズ', 'hex' => '#ad1457'),
        'slate' => array('label' => 'スレート', 'hex' => '#455a64'),
        'olive' => array('label' => 'オリーブ', 'hex' => '#558b2f'),
    );
}

/**
 * @return string[]
 */
function course_school_get_badge_tone_slugs()
{
    return array_keys(course_school_get_badge_tone_presets());
}

/**
 * ACF セレクト用: value => 表示文字列
 *
 * @return array<string, string>
 */
function course_school_get_badge_tone_choices()
{
    $choices = array();
    foreach (course_school_get_badge_tone_presets() as $slug => $row) {
        $choices[$slug] = $row['label'] . ' — ' . $row['hex'];
    }
    return $choices;
}

/**
 * @param string $tone
 * @return string
 */
function course_school_sanitize_badge_tone($tone)
{
    $tone = is_string($tone) ? $tone : '';
    $allowed = course_school_get_badge_tone_slugs();
    return in_array($tone, $allowed, true) ? $tone : 'teal';
}

/**
 * 講座カテゴリーの一覧・ナビ表示用ラベル（改行・サブ行対応）。
 * ACF のメイン（テキストエリア可）／サブが未入力のときは WordPress の「名前」を1行で使う。
 *
 * @param WP_Term $term
 * @return array{main:string, sub:string}
 */
function course_school_category_get_label_parts($term)
{
    $fallback = (isset($term->name) && is_string($term->name)) ? $term->name : '';
    if (!function_exists('get_field')) {
        return array('main' => $fallback, 'sub' => '');
    }
    $tid = 'course_school_category_' . (int) $term->term_id;
    $main = get_field('course_school_cat_label_main', $tid);
    $sub  = get_field('course_school_cat_label_sub', $tid);
    $main = is_string($main) ? trim($main) : '';
    $sub  = is_string($sub) ? trim($sub) : '';

    if ($main === '' && $sub === '') {
        return array('main' => $fallback, 'sub' => '');
    }
    if ($main === '') {
        $main = $fallback;
    }
    return array('main' => $main, 'sub' => $sub);
}

add_filter('use_block_editor_for_post_type', 'course_school_disable_block_editor', 10, 2);
function course_school_disable_block_editor($use, $post_type)
{
    if ($post_type === 'course_school') {
        return false;
    }
    return $use;
}

add_action('init', 'course_school_register_taxonomy', 6);
function course_school_register_taxonomy()
{
    register_taxonomy(
        'course_school_category',
        'course_school',
        array(
            'labels' => array(
                'name' => '講座カテゴリー',
                'singular_name' => '講座カテゴリー',
                'search_items' => 'カテゴリーを検索',
                'all_items' => 'すべてのカテゴリー',
                'edit_item' => 'カテゴリーを編集',
                'add_new_item' => 'カテゴリーを追加',
            ),
            'public' => true,
            'show_ui' => true,
            'show_in_rest' => true,
            'hierarchical' => true,
            'rewrite' => false,
        )
    );
}

add_action('init', function () {
    $v = (int) get_option('course_school_rewrite_v', 0);
    if ($v < 3) {
        flush_rewrite_rules(false);
        update_option('course_school_rewrite_v', 3);
    }
}, 999);

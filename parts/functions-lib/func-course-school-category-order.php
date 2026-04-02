<?php
/**
 * 講座カテゴリー（course_school_category）の表示順
 * 管理画面「講座カテゴリー」一覧でドラッグ＆ドロップ並び替え → term meta に保存
 */

define('COURSE_SCHOOL_CAT_ORDER_META', '_course_school_cat_order');

add_action('init', function () {
    register_term_meta(
        'course_school_category',
        COURSE_SCHOOL_CAT_ORDER_META,
        array(
            'type' => 'integer',
            'single' => true,
            'show_in_rest' => false,
            'sanitize_callback' => function ($value) {
                return (int) $value;
            },
        )
    );
});

/**
 * @param int $term_id
 * @return int|null メタ未設定時は null（名前順の二次ソート用）
 */
function course_school_category_get_order_meta($term_id)
{
    $term_id = (int) $term_id;
    if ($term_id < 1) {
        return null;
    }
    $v = get_term_meta($term_id, COURSE_SCHOOL_CAT_ORDER_META, true);
    if ($v === '' || $v === false) {
        return null;
    }
    return (int) $v;
}

/**
 * フロント・ナビ用：講座カテゴリータームを管理画面の並び順で返す
 *
 * @return WP_Term[]
 */
function course_school_get_terms_ordered()
{
    $terms = get_terms(array(
        'taxonomy' => 'course_school_category',
        'hide_empty' => false,
    ));

    if (is_wp_error($terms) || !is_array($terms)) {
        return array();
    }

    usort(
        $terms,
        function ($a, $b) {
            $oa = course_school_category_get_order_meta($a->term_id);
            $ob = course_school_category_get_order_meta($b->term_id);
            if ($oa !== null && $ob !== null && $oa !== $ob) {
                return $oa <=> $ob;
            }
            if ($oa !== null && $ob === null) {
                return -1;
            }
            if ($oa === null && $ob !== null) {
                return 1;
            }
            return strcasecmp($a->name, $b->name);
        }
    );

    return $terms;
}

add_action('created_term', 'course_school_category_assign_order_on_create', 10, 3);
function course_school_category_assign_order_on_create($term_id, $tt_id, $taxonomy)
{
    if ($taxonomy !== 'course_school_category') {
        return;
    }
    $term_id = (int) $term_id;
    $max     = 0;
    $all     = get_terms(array(
        'taxonomy'   => 'course_school_category',
        'hide_empty' => false,
        'fields'     => 'ids',
    ));
    if (is_wp_error($all) || !is_array($all)) {
        update_term_meta($term_id, COURSE_SCHOOL_CAT_ORDER_META, 0);
        return;
    }
    foreach ($all as $tid) {
        $tid = (int) $tid;
        if ($tid === $term_id) {
            continue;
        }
        $o = course_school_category_get_order_meta($tid);
        if ($o !== null && $o > $max) {
            $max = $o;
        }
    }
    update_term_meta($term_id, COURSE_SCHOOL_CAT_ORDER_META, $max + 1);
}

add_action('admin_enqueue_scripts', 'course_school_category_order_admin_assets');
function course_school_category_order_admin_assets($hook_suffix)
{
    if ($hook_suffix !== 'edit-tags.php') {
        return;
    }
    if (!isset($_GET['taxonomy']) || $_GET['taxonomy'] !== 'course_school_category') {
        return;
    }

    $tax = get_taxonomy('course_school_category');
    if (!$tax || !current_user_can($tax->cap->manage_terms)) {
        return;
    }

    wp_enqueue_script('jquery-ui-sortable');

    $path = get_template_directory() . '/assets/js/admin-course-school-category-order.js';
    $url  = get_template_directory_uri() . '/assets/js/admin-course-school-category-order.js';
    $ver  = file_exists($path) ? (string) filemtime($path) : null;

    wp_enqueue_script(
        'course-school-category-order',
        $url,
        array('jquery', 'jquery-ui-sortable'),
        $ver,
        true
    );

    wp_localize_script(
        'course-school-category-order',
        'courseSchoolCategoryOrder',
        array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('course_school_save_category_order'),
        )
    );
}

add_action('admin_head', 'course_school_category_order_admin_head_style');
function course_school_category_order_admin_head_style()
{
    $screen = function_exists('get_current_screen') ? get_current_screen() : null;
    if (!$screen || empty($screen->taxonomy) || $screen->taxonomy !== 'course_school_category') {
        return;
    }
    echo '<style>#the-list tr[id^="tag-"]{cursor:move;}#the-list tr.ui-sortable-helper{background:#f6f7f7;box-shadow:0 1px 3px rgba(0,0,0,.08);}</style>';
}

add_action('admin_notices', 'course_school_category_order_admin_notice');
function course_school_category_order_admin_notice()
{
    $screen = function_exists('get_current_screen') ? get_current_screen() : null;
    if (!$screen || empty($screen->taxonomy) || $screen->taxonomy !== 'course_school_category') {
        return;
    }
    $tax = get_taxonomy('course_school_category');
    if (!$tax || !current_user_can($tax->cap->manage_terms)) {
        return;
    }
    echo '<div class="notice notice-info is-dismissible"><p>';
    echo esc_html('行をドラッグして並び替えると、講座一覧ページのカテゴリーナビの順序に反映されます。');
    echo '</p></div>';
}

add_action('wp_ajax_course_school_save_category_order', 'course_school_ajax_save_category_order');
function course_school_ajax_save_category_order()
{
    check_ajax_referer('course_school_save_category_order', 'nonce');

    $tax = get_taxonomy('course_school_category');
    if (!$tax || !current_user_can($tax->cap->manage_terms)) {
        wp_send_json_error(array('message' => 'forbidden'), 403);
    }

    $order = isset($_POST['order']) ? sanitize_text_field(wp_unslash($_POST['order'])) : '';
    $ids   = array_filter(array_map('intval', explode(',', $order)));

    if ($ids === array()) {
        wp_send_json_error(array('message' => 'empty'), 400);
    }

    $pos = 0;
    foreach ($ids as $term_id) {
        if ($term_id < 1) {
            continue;
        }
        $term = get_term($term_id, 'course_school_category');
        if (!$term || is_wp_error($term)) {
            continue;
        }
        update_term_meta($term_id, COURSE_SCHOOL_CAT_ORDER_META, $pos);
        $pos++;
    }

    wp_send_json_success(array('saved' => true));
}

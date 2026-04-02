<?php
/**
 * スクール講座（course_school）一覧の表示順
 * 管理画面「スクール講座」一覧でドラッグ＆ドロップ → menu_order に保存
 */

add_action('admin_enqueue_scripts', 'course_school_post_order_admin_assets');
function course_school_post_order_admin_assets($hook_suffix)
{
    if ($hook_suffix !== 'edit.php') {
        return;
    }
    if (!isset($_GET['post_type']) || $_GET['post_type'] !== 'course_school') {
        return;
    }

    $pto = get_post_type_object('course_school');
    if (!$pto || !current_user_can($pto->cap->edit_posts)) {
        return;
    }

    wp_enqueue_script('jquery-ui-sortable');

    $path = get_template_directory() . '/assets/js/admin-course-school-post-order.js';
    $url  = get_template_directory_uri() . '/assets/js/admin-course-school-post-order.js';
    $ver  = file_exists($path) ? (string) filemtime($path) : null;

    wp_enqueue_script(
        'course-school-post-order',
        $url,
        array('jquery', 'jquery-ui-sortable'),
        $ver,
        true
    );

    wp_localize_script(
        'course-school-post-order',
        'courseSchoolPostOrder',
        array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('course_school_save_post_order'),
        )
    );
}

add_action('admin_head', 'course_school_post_order_admin_head_style');
function course_school_post_order_admin_head_style()
{
    $screen = function_exists('get_current_screen') ? get_current_screen() : null;
    if (!$screen || $screen->id !== 'edit-course_school') {
        return;
    }
    echo '<style>#the-list tr[id^="post-"]{cursor:move;}#the-list tr.ui-sortable-helper{background:#f6f7f7;box-shadow:0 1px 3px rgba(0,0,0,.08);}</style>';
}

add_action('admin_notices', 'course_school_post_order_admin_notice');
function course_school_post_order_admin_notice()
{
    $screen = function_exists('get_current_screen') ? get_current_screen() : null;
    if (!$screen || $screen->id !== 'edit-course_school') {
        return;
    }
    $pto = get_post_type_object('course_school');
    if (!$pto || !current_user_can($pto->cap->edit_posts)) {
        return;
    }
    echo '<div class="notice notice-info is-dismissible"><p>';
    echo esc_html('行をドラッグして並び替えると、講座一覧ページの並び順に反映されます（同じ順序のときは日付が新しい順になります）。');
    echo '</p></div>';
}

add_action('wp_ajax_course_school_save_post_order', 'course_school_ajax_save_post_order');
function course_school_ajax_save_post_order()
{
    check_ajax_referer('course_school_save_post_order', 'nonce');

    $pto = get_post_type_object('course_school');
    if (!$pto || !current_user_can($pto->cap->edit_posts)) {
        wp_send_json_error(array('message' => 'forbidden'), 403);
    }

    $order = isset($_POST['order']) ? sanitize_text_field(wp_unslash($_POST['order'])) : '';
    $ids   = array_filter(array_map('intval', explode(',', $order)));

    if ($ids === array()) {
        wp_send_json_error(array('message' => 'empty'), 400);
    }

    $pos = 0;
    foreach ($ids as $post_id) {
        if ($post_id < 1) {
            continue;
        }
        $post = get_post($post_id);
        if (!$post || $post->post_type !== 'course_school') {
            continue;
        }
        if (!current_user_can('edit_post', $post_id)) {
            continue;
        }
        wp_update_post(array(
            'ID'         => $post_id,
            'menu_order' => $pos,
        ));
        $pos++;
    }

    wp_send_json_success(array('saved' => true));
}

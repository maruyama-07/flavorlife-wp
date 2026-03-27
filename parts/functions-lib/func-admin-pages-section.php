<?php
/**
 * 固定ページ一覧（管理画面）を「コーポレート / スクール」で切り替え。
 * スクール = スラッグ school の固定ページおよびその子孫（func-school.php と同じ判定）。
 */

/**
 * @param string $html
 * @param callable(string):string $map_url
 * @return string
 */
function school_admin_edit_page_patch_view_href($html, $map_url)
{
    return (string) preg_replace_callback(
        '/href=(["\'])([^"\']*)\1/',
        function ($m) use ($map_url) {
            $url = $map_url($m[2]);
            return 'href=' . $m[1] . esc_url($url) . $m[1];
        },
        $html
    );
}

/**
 * 一覧のベースURL（親・検索・ステータスなどを維持しつつ page_section のみ差し替え可能にする）
 *
 * @return string
 */
function school_admin_edit_pages_list_base_url()
{
    $args = array('post_type' => 'page');
    $preserve = array('parent_id', 'post_status', 's', 'orderby', 'order', 'author', 'm', 'lang');
    foreach ($preserve as $key) {
        if (!isset($_GET[$key]) || $_GET[$key] === '') {
            continue;
        }
        $args[$key] = wp_unslash($_GET[$key]);
    }
    return add_query_arg($args, admin_url('edit.php'));
}

/**
 * @return int
 */
function school_admin_count_corporate_pages()
{
    $school_ids = school_section_get_page_ids();
    global $wpdb;
    if (empty($school_ids)) {
        return (int) $wpdb->get_var(
            "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'page' AND post_status NOT IN ('trash','auto-draft')"
        );
    }
    $placeholders = implode(',', array_fill(0, count($school_ids), '%d'));
    $sql = $wpdb->prepare(
        "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'page' AND post_status NOT IN ('trash','auto-draft') AND ID NOT IN ($placeholders)",
        $school_ids
    );
    return (int) $wpdb->get_var($sql);
}

/**
 * サブビュー（公開済み・下書き等）のリンクに page_section を引き継ぐ／「すべて」では解除する。
 *
 * @param array<string, string> $views
 * @return array<string, string>
 */
function school_admin_edit_page_views_preserve_section($views)
{
    if (empty($_GET['page_section'])) {
        return $views;
    }
    $section = sanitize_key(wp_unslash($_GET['page_section']));
    if ($section !== 'school' && $section !== 'corporate') {
        return $views;
    }
    foreach ($views as $key => $html) {
        if ($key === 'section_corporate' || $key === 'section_school') {
            continue;
        }
        if ($key === 'all') {
            $views[$key] = school_admin_edit_page_patch_view_href($html, function ($url) {
                return remove_query_arg('page_section', $url);
            });
            continue;
        }
        $views[$key] = school_admin_edit_page_patch_view_href($html, function ($url) use ($section) {
            return add_query_arg('page_section', $section, $url);
        });
    }
    return $views;
}

/**
 * @param array<string, string> $views
 * @return array<string, string>
 */
function school_admin_edit_page_views_add_section_tabs($views)
{
    global $typenow;
    if (!current_user_can('edit_pages')) {
        return $views;
    }
    $is_page_list = ($typenow === 'page');
    if (!$is_page_list && function_exists('get_current_screen')) {
        $screen = get_current_screen();
        $is_page_list = $screen && $screen->id === 'edit-page';
    }
    if (!$is_page_list) {
        return $views;
    }
    if (!school_section_get_root_page_id()) {
        return $views;
    }

    $base = school_admin_edit_pages_list_base_url();
    $base = remove_query_arg('page_section', $base);
    $base = remove_query_arg('paged', $base);

    $current = isset($_GET['page_section']) ? sanitize_key(wp_unslash($_GET['page_section'])) : '';
    $school_ids = school_section_get_page_ids();
    $school_count = count($school_ids);
    $corporate_count = school_admin_count_corporate_pages();

    $url_corp = esc_url(add_query_arg('page_section', 'corporate', $base));
    $url_school = esc_url(add_query_arg('page_section', 'school', $base));

    $class_corp = ($current === 'corporate') ? ' class="current"' : '';
    $class_school = ($current === 'school') ? ' class="current"' : '';

    $label_corp = 'コーポレート <span class="count">(' . number_format_i18n($corporate_count) . ')</span>';
    $label_school = 'スクール <span class="count">(' . number_format_i18n($school_count) . ')</span>';

    $tabs = array(
        'section_corporate' => '<a href="' . $url_corp . '"' . $class_corp . '>' . $label_corp . '</a>',
        'section_school' => '<a href="' . $url_school . '"' . $class_school . '>' . $label_school . '</a>',
    );

    $new = array();
    $inserted = false;
    foreach ($views as $key => $html) {
        $new[$key] = $html;
        if ($key === 'all') {
            $new['section_corporate'] = $tabs['section_corporate'];
            $new['section_school'] = $tabs['section_school'];
            $inserted = true;
        }
    }
    if (!$inserted) {
        $new = array_merge($new, $tabs);
    }
    return $new;
}

/**
 * @param WP_Query $query
 */
function school_admin_pre_get_posts_filter_by_section($query)
{
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }
    global $pagenow;
    if ($pagenow !== 'edit.php') {
        return;
    }
    if (empty($_GET['post_type']) || $_GET['post_type'] !== 'page') {
        return;
    }
    if (!current_user_can('edit_pages')) {
        return;
    }
    $section = isset($_GET['page_section']) ? sanitize_key(wp_unslash($_GET['page_section'])) : '';
    if ($section !== 'school' && $section !== 'corporate') {
        return;
    }

    $school_ids = school_section_get_page_ids();
    if ($section === 'school') {
        if (empty($school_ids)) {
            $query->set('post__in', array(0));
        } else {
            $query->set('post__in', $school_ids);
        }
        return;
    }
    if (!empty($school_ids)) {
        $query->set('post__not_in', $school_ids);
    }
}

add_filter('views_edit-page', 'school_admin_edit_page_views_add_section_tabs', 5);
add_filter('views_edit-page', 'school_admin_edit_page_views_preserve_section', 99);
add_action('pre_get_posts', 'school_admin_pre_get_posts_filter_by_section');

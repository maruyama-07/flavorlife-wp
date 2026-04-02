<?php
/**
 * 求人一覧・求人詳細（template-recruit の一覧アイテム用）
 * 旧: 同テンプレの子固定ページ → CPT で運用
 */

add_action('init', 'recruit_job_register_post_type', 5);
function recruit_job_register_post_type()
{
    register_post_type(
        'recruit_job',
        array(
            'label' => '求人情報',
            'labels' => array(
                'name' => '求人情報',
                'singular_name' => '求人情報',
                'add_new_item' => '求人を追加',
                'edit_item' => '求人を編集',
                'all_items' => '求人情報一覧',
                'search_items' => '求人を検索',
            ),
            'public' => true,
            'has_archive' => false,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            // News=5, Topics=6, Blog=7 の直後（重複すると順序が崩れるため 8 固定）
            'menu_position' => 8,
            'menu_icon' => 'dashicons-id',
            'show_in_rest' => true,
            'supports' => array('title', 'editor', 'thumbnail', 'revisions', 'page-attributes'),
            // rewrite を有効にすると管理画面でスラッグ編集が使える（false だと ?recruit_job= 扱いになり編集UIが出にくい）
            'rewrite' => array(
                'slug'       => 'recruit',
                'with_front' => false,
            ),
            'capability_type' => 'post',
            'map_meta_cap' => true,
        )
    );
}

add_action('init', function () {
    $v = (int) get_option('recruit_job_rewrite_v', 0);
    if ($v < 5) {
        flush_rewrite_rules(false);
        update_option('recruit_job_rewrite_v', 5);
    }
}, 999);

/**
 * 求人詳細テンプレート（親）の固定ページID（一覧の見出し・お問い合わせ等に利用）
 *
 * @return int 0 = 未検出
 */
function recruit_job_get_hub_page_id()
{
    static $cached = null;
    if ($cached !== null) {
        return $cached;
    }
    $cached = 0;
    $pages  = get_pages(array(
        'meta_key'    => '_wp_page_template',
        'meta_value'  => 'template-recruit.php',
        'sort_column' => 'menu_order',
        'sort_order'  => 'ASC',
        'number'      => 50,
    ));
    if (empty($pages)) {
        return $cached;
    }
    foreach ($pages as $p) {
        if ((int) $p->post_parent === 0) {
            $cached = (int) $p->ID;
            break;
        }
    }
    if ($cached === 0 && isset($pages[0])) {
        $cached = (int) $pages[0]->ID;
    }

    return $cached;
}

/**
 * 管理画面一覧：タイトル列の直後にサブタイトル（ACF recruit_job_subtitle）列を表示
 */
add_filter('manage_edit-recruit_job_columns', 'recruit_job_admin_list_columns');
function recruit_job_admin_list_columns($columns)
{
    $out = array();
    foreach ($columns as $key => $label) {
        $out[$key] = $label;
        if ($key === 'title') {
            $out['recruit_job_subtitle'] = 'サブタイトル';
        }
    }

    return $out;
}

add_action('manage_recruit_job_posts_custom_column', 'recruit_job_admin_list_column_subtitle', 10, 2);
function recruit_job_admin_list_column_subtitle($column, $post_id)
{
    if ($column !== 'recruit_job_subtitle') {
        return;
    }
    $sub = function_exists('get_field') ? get_field('recruit_job_subtitle', (int) $post_id) : '';
    $sub = is_string($sub) ? trim($sub) : '';
    echo $sub !== '' ? esc_html($sub) : '<span aria-hidden="true">—</span>';
}

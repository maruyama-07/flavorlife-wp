<?php
/**
 * スクール講師紹介（一覧グリッド用）
 *
 * 人数の上限なく追加可能。並び順は管理画面の「順序」（menu_order）で制御。
 * フロントの単体URLは出さない（一覧のボタンはACFの外部リンクなどを利用）。
 */

add_action('init', 'school_instructor_register_post_type');
function school_instructor_register_post_type()
{
    register_post_type(
        'school_instructor',
        array(
            'label' => 'スクール講師',
            'labels' => array(
                'name' => 'スクール講師',
                'singular_name' => 'スクール講師',
                'add_new_item' => '講師を追加',
                'edit_item' => '講師を編集',
                'all_items' => 'スクール講師一覧',
                'search_items' => '講師を検索',
            ),
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_rest' => true,
            'publicly_queryable' => false,
            'exclude_from_search' => true,
            'has_archive' => false,
            'rewrite' => false,
            'capability_type' => 'post',
            'map_meta_cap' => true,
            'menu_position' => 15,
            'menu_icon' => 'dashicons-groups',
            'supports' => array('title', 'editor', 'thumbnail', 'page-attributes'),
        )
    );
}

/**
 * テーマ有効化・更新後にリライト情報を整合（CPT 追加時）
 */
add_action('init', function () {
    $v = (int) get_option('school_instructor_rewrite_v', 0);
    if ($v < 1) {
        flush_rewrite_rules(false);
        update_option('school_instructor_rewrite_v', 1);
    }
}, 999);

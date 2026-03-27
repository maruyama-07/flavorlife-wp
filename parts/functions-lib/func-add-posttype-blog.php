<?php
/**
 * Blogカスタム投稿タイプの設定
 * アーカイブURL: /blog/
 */
add_action('init', 'tool_register_post_type_blog');

function tool_register_post_type_blog()
{
    register_post_type(
        'blog',
        array(
            'labels' => array(
                'name'          => 'Blog',
                'singular_name' => 'Blog',
                'add_new'       => '新規追加',
                'add_new_item'  => 'ブログ記事を追加',
                'edit_item'     => 'ブログ記事を編集',
                'new_item'      => '新規ブログ記事',
                'view_item'     => 'ブログ記事を表示',
                'search_items'  => 'ブログ記事を検索',
                'not_found'     => 'ブログ記事が見つかりません',
                'all_items'     => 'ブログ記事一覧',
            ),
            'public'        => true,
            'has_archive'   => true,
            'rewrite'       => array('slug' => 'blog'),
            'menu_position' => 5,
            'show_in_rest'  => true,
            'supports'      => array('title', 'editor', 'thumbnail', 'revisions'),
        )
    );
}

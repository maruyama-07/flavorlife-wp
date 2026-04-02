<?php
/**
 * Blogカスタム投稿タイプの設定
 * アーカイブURL: /blog/
 * タクソノミー: blog_category（カテゴリー）
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
            // News(投稿)=5, Topics=6 の次に並ぶよう 7（求人情報は 8）
            'menu_position' => 7,
            'show_in_rest'  => true,
            'supports'      => array('title', 'editor', 'thumbnail', 'revisions'),
        )
    );
}

add_action('init', 'tool_register_taxonomy_blog_category', 6);

function tool_register_taxonomy_blog_category()
{
    register_taxonomy(
        'blog_category',
        'blog',
        array(
            'labels' => array(
                'name'          => 'ブログカテゴリー',
                'singular_name' => 'ブログカテゴリー',
                'search_items'  => 'カテゴリーを検索',
                'all_items'     => 'すべてのカテゴリー',
                'edit_item'     => 'カテゴリーを編集',
                'update_item'   => 'カテゴリーを更新',
                'add_new_item'  => 'カテゴリーを追加',
                'new_item_name' => '新しいカテゴリー名',
            ),
            'public'            => true,
            'hierarchical'      => true,
            'show_ui'           => true,
            'show_in_rest'      => true,
            'show_admin_column' => true,
            'rewrite'           => array(
                'slug'         => 'blog-category',
                'with_front'   => false,
                'hierarchical' => true,
            ),
        )
    );
}

add_action('init', function () {
    $v = (int) get_option('tool_blog_category_rewrite_v', 0);
    if ($v < 1) {
        flush_rewrite_rules(false);
        update_option('tool_blog_category_rewrite_v', 1);
    }
}, 999);

/**
 * ブログ投稿の表示用カテゴリー名（先頭のターム）
 *
 * @param int|null $post_id 省略時は現在の投稿
 * @return string
 */
function tool_get_blog_category_label($post_id = null)
{
    $post_id = $post_id ? (int) $post_id : get_the_ID();
    if ($post_id < 1 || get_post_type($post_id) !== 'blog') {
        return '';
    }
    $terms = get_the_terms($post_id, 'blog_category');
    if (empty($terms) || is_wp_error($terms)) {
        return '';
    }
    $term = array_shift($terms);

    return $term instanceof WP_Term ? (string) $term->name : '';
}

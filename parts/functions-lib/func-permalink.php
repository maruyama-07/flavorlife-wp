<?php
/**
 * パーマリンク設定
 * News（post）のURLを /news/記事スラッグ/ 形式にする
 */

/**
 * テーマ有効化時にリライトルールをフラッシュ
 */
add_action('after_switch_theme', 'tool_flush_rewrite_rules');

function tool_flush_rewrite_rules()
{
    tool_add_news_rewrite_rules();
    flush_rewrite_rules();
}

/**
 * News投稿のリライトルールを追加
 */
add_action('init', 'tool_add_news_rewrite_rules');

function tool_add_news_rewrite_rules()
{
    add_rewrite_rule(
        '^news/([^/]+)/?$',
        'index.php?name=$matches[1]',
        'top'
    );
}

/**
 * News投稿のパーマリンクを /news/スラッグ/ 形式に変更
 */
add_filter('post_link', 'tool_news_post_link', 10, 3);

function tool_news_post_link($permalink, $post, $leavename)
{
    $post = get_post($post);
    if ($post && $post->post_type === 'post') {
        return home_url('/news/' . $post->post_name . '/');
    }
    return $permalink;
}

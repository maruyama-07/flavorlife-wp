<?php
/**
 * News（スクール）ティッカー用カスタム投稿タイプ
 */
add_action('init', 'my_add_custom_post_news_school');
function my_add_custom_post_news_school()
{
    register_post_type(
        'news_school',
        array(
            'label' => 'News（スクール）',
            'labels' => array(
                'name' => 'News（スクール）',
                'all_items' => 'News（スクール）',
            ),
            'public' => true,
            'show_ui' => true,
            'show_in_rest' => true,
            'has_archive' => false,
            'rewrite' => array('slug' => 'school-news', 'with_front' => false),
            'menu_position' => 13,
            'menu_icon' => 'dashicons-megaphone',
            'supports' => array('title', 'editor', 'thumbnail'),
        )
    );
}

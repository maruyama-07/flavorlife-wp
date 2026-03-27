<?php
/**
 * MV（スクール）用カスタム投稿タイプ
 */
add_action('init', 'my_add_custom_post_mv_school');
function my_add_custom_post_mv_school()
{
    register_post_type(
        'mv_slider_school',
        array(
            'label' => 'MV（スクール）',
            'labels' => array(
                'name' => 'MV（スクール）',
                'all_items' => 'MV（スクール）',
            ),
            // public=false だと環境・プラグインによってフロントの WP_Query で拾えないことがある
            'public' => true,
            'publicly_queryable' => false,
            'exclude_from_search' => true,
            'show_ui' => true,
            'show_in_rest' => true,
            'has_archive' => false,
            'rewrite' => false,
            'menu_position' => 12,
            'menu_icon' => 'dashicons-format-gallery',
            'supports' => array('title'),
        )
    );
}

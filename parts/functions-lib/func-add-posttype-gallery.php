<?php
/**
 * galleryのカスタム投稿を設定
 */
add_action('init', 'my_add_custom_post_gallery');
function my_add_custom_post_gallery()
{
  // gallery
  register_post_type(
    'gallery',
    array(
      'label' => 'ギャラリー',
      'labels' => array(
        'name' => 'ギャラリー',
        'all_items' => 'ギャラリー',
      ),
      'public' => true,
      'has_archive' => false,
      'menu_position' => 12,   //メニュー表示位置
      'supports' => array(
        'title',
      ),
    )
  );
}

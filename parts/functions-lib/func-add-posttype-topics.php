<?php
/**
 * Worksのカスタム投稿を設定
 */
add_action('init', 'my_add_custom_post_topics');
function my_add_custom_post_topics()
{
  // 投稿タイプ'works'を登録
  register_post_type(
    'topics', // 新しい投稿タイプの名前
    array(
      'label' => 'Topics', // 管理画面に表示される投稿タイプの名前
      'labels' => array( // 投稿タイプの詳細な表示名の設定
        'name' => 'Topics', // 投稿タイプの複数形の名前を設定
        'all_items' => 'Topics', // 全投稿一覧のリンクのテキスト
      ),
      'public' => true,
      'has_archive' =>  true,
      'menu_position' => 6,   //メニュー表示位置
      'show_in_rest' => true, // ブロックエディタを有効にする
      'supports' => array(
        'title',
        'editor',
        'thumbnail',
        'revisions',
      ),
    )
  );
}
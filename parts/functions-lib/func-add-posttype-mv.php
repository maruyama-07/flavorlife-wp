<?php
/**
 * MVスライダーのカスタム投稿を設定
 */
add_action('init', 'my_add_custom_post_mv');
function my_add_custom_post_mv()
{
  // 投稿タイプ'mv_slider'を登録
  register_post_type(
    'mv_slider', // 新しい投稿タイプの名前
    array(
      'label' => 'MVスライダー', // 管理画面に表示される投稿タイプの名前
      'labels' => array( // 投稿タイプの詳細な表示名の設定
        'name' => 'MVスライダー', // 投稿タイプの複数形の名前を設定
        'all_items' => 'MVスライダー', // 全投稿一覧のリンクのテキスト
      ),
      'public' => false, // この投稿タイプを公開するかどうか（公開する場合はtrue, 非公開の場合はfalse）
      'show_ui' => true,  // 管理画面にこの投稿タイプのメニューを表示するかどうか
      'show_in_rest' => true, // ブロックエディタを有効にする
      'has_archive' => false, // この投稿タイプにアーカイブページを作成するかどうか
      'menu_position' => 11, // 管理画面メニュー上での表示位置
      'menu_icon' => 'dashicons-format-gallery',
      'supports' => array( // この投稿タイプがサポートする機能
        'title', // タイトルをサポート
        // 'editor', // 本文エディタを有効にします。
        // 'author', // 投稿者ボックスを有効にします。
        // 'thumbnail', // アイキャッチ画像を有効にします。
        // 'excerpt', // 投稿抜粋を有効にします。
        // 'comments' // コメントを有効にします。
      ),
    ),
  );
}

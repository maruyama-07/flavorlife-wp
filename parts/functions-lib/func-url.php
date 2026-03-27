<?php
/**
 * func-url
 * URL関数
 */

/**
 * パスを定義
 */


/* テンプレートパスを返す */
function temp_path() {
  echo esc_url(get_template_directory_uri());
}
/* assetsパスを返す */
function assets_path() {
  echo esc_url(get_template_directory_uri() . '/assets');
}
/* 画像パスを返す */
function img_path() {
  echo esc_url(get_template_directory_uri() . '/assets/images');
}
/* mediaフォルダへのURL */
function uploads_path() {
  echo esc_url(wp_upload_dir()['baseurl']);
}

/* ホームURLのパスを返す
 *
 * $page = worksの場合、https://xxxx.co.jp/works/ を返す
 * 呼び出しは、<?php page_path('works'); ?> で実施する
 *
*/
function page_path( $page= "" ) {
  $page = $page . '/';
  echo esc_url(home_url($page));
}



?>

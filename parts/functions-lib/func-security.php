<?php
/**
 * func-security
 *  セキュリティ対策
 *
 */

/**
 * wordpressバージョン情報の削除
 * @see　https://digitalnavi.net/wordpress/6921/
 */
remove_action('wp_head', 'wp_generator');

/**
 * 投稿者一覧ページを自動で生成されないようにする
 * @see　https://mucca-design.com/auther-archive-ineffective/
 */
function disable_author_archive()
{
  if (preg_match('#/author/.+#', $_SERVER['REQUEST_URI'])) {
    wp_redirect(esc_url(home_url('/404.php')));
    exit;
  }
}
add_action('init', 'disable_author_archive');

/**
 * SVGアップロードを管理者のみ許可（TOP Productロゴ等で使用）
 * 管理者以外は従来どおりアップロード不可
 */
add_filter('upload_mimes', function ($mimes) {
    if (current_user_can('manage_options')) {
        $mimes['svg'] = 'image/svg+xml';
    }
    return $mimes;
});

<?php
/**
 * SEO SIMPLE PACK 補正
 *
 * ・フロントページ：generate_description() が is_front_page() を先に判定し、
 *   固定ページのメタボックス（ssp_meta_description）を読まない → フィルターで補正。
 * ・カスタム投稿タイプのアーカイブ（例: /blog/）：プラグインは共通キー pt_archive_desc のみ。
 *   blog / topics などアーカイブを複数持つと説明文が共有される。
 *   投稿タイプ別に変えたい場合は下記フィルター tool_ssp_{投稿タイプ}_archive_meta_description を使う。
 *
 * @package tool_wordpress_template
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * 固定フロントページ：ページ個別ディスクリプションを meta / og に反映
 *
 * @param string $description プラグインが既に snippet 置換済みの文字列。
 * @return string
 */
function tool_ssp_front_page_description($description)
{
    if (!is_front_page()) {
        return $description;
    }

    $page_id = (int) get_option('page_on_front');
    if ($page_id <= 0) {
        return $description;
    }

    $raw = get_post_meta($page_id, 'ssp_meta_description', true);
    if ($raw === '' || $raw === null) {
        return $description;
    }

    $trimmed = trim((string) $raw);
    if ($trimmed === '') {
        return $description;
    }

    if (!class_exists('SSP_Output')) {
        return $description;
    }

    return SSP_Output::replace_snippets($trimmed, 'description');
}

add_filter('ssp_output_description', 'tool_ssp_front_page_description', 10, 1);

/**
 * カスタム投稿タイプアーカイブ：投稿タイプごとにメタディスクリプションを上書き（任意）
 *
 * 例・ブログ一覧のみ文面を変える（functions.php など）:
 * add_filter(
 *     'tool_ssp_blog_archive_meta_description',
 *     function ($empty, $plugin_description) {
 *         return 'ここに /blog/ 用の説明文（%_tagline_% 等のスニペット可）';
 *     },
 *     10,
 *     2
 * );
 *
 * @param string $description プラグインが pt_archive_desc 等から組み立て snippet 置換済みの文字列。
 * @return string
 */
function tool_ssp_cpt_archive_description_override($description)
{
    if (!is_post_type_archive()) {
        return $description;
    }

    $qo = get_queried_object();
    if (!$qo instanceof WP_Post_Type || $qo->name === '') {
        return $description;
    }

    $slug = $qo->name;
    /**
     * 第1引数は未使用時の空文字。第2引数はプラグイン出力（フォールバック参照用）。
     *
     * @param string $empty              ''
     * @param string $plugin_description プラグインが出力しようとしている説明
     */
    $override = apply_filters("tool_ssp_{$slug}_archive_meta_description", '', $description);

    if (!is_string($override)) {
        return $description;
    }

    $trimmed = trim($override);
    if ($trimmed === '') {
        return $description;
    }

    if (!class_exists('SSP_Output')) {
        return $trimmed;
    }

    return SSP_Output::replace_snippets($trimmed, 'description');
}

add_filter('ssp_output_description', 'tool_ssp_cpt_archive_description_override', 20, 1);

<?php
/**
 * スクールトップ「Category」セクション表示用（フィールド定義は func-acf-school-top.php のタブに統合）
 */

/**
 * Category セクション表示データ
 *
 * @return array<int, array{title:string, link:string, image_url:string, description:string}>
 */
function school_top_get_category_items()
{
    $root = function_exists('school_section_get_root_page_id') ? school_section_get_root_page_id() : 0;
    if (!$root || !is_page() || (int) get_queried_object_id() !== $root || !function_exists('get_field')) {
        return array();
    }

    $page_id = (int) get_queried_object_id();
    $out = array();

    for ($i = 1; $i <= 3; $i++) {
        $link = trim((string) get_field('school_category_link_' . $i, $page_id));
        if ($link === '') {
            continue;
        }

        $title = trim((string) get_field('school_category_title_' . $i, $page_id));
        $img = get_field('school_category_image_' . $i, $page_id);
        $image_url = '';
        if (is_array($img) && !empty($img['url'])) {
            $image_url = (string) $img['url'];
        }
        $description = (string) get_field('school_category_desc_' . $i, $page_id);

        $out[] = array(
            'title' => $title,
            'link' => $link,
            'image_url' => $image_url,
            'description' => $description,
        );
    }

    return $out;
}

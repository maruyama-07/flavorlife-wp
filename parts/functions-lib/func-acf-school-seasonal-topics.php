<?php
/**
 * スクールトップ「Seasonal Topics」表示用（フィールド定義は func-acf-school-top.php のタブに統合）
 */

/**
 * Seasonal Topics 表示データを取得
 *
 * @return array<string,string>
 */
function school_top_get_seasonal_topics_data()
{
    $root = function_exists('school_section_get_root_page_id') ? school_section_get_root_page_id() : 0;
    if (!$root || !is_page() || (int) get_queried_object_id() !== $root) {
        return array();
    }

    $page_id = (int) get_queried_object_id();
    $image = function_exists('get_field') ? get_field('school_seasonal_image', $page_id) : null;
    $image_url = is_array($image) && !empty($image['url']) ? (string) $image['url'] : '';
    if ($image_url === '') {
        $image_url = get_template_directory_uri() . '/assets/images/school/seasonal-topics.png';
    }

    return array(
        'title' => (string) (function_exists('get_field') ? get_field('school_seasonal_title', $page_id) : ''),
        'subtitle' => (string) (function_exists('get_field') ? get_field('school_seasonal_subtitle', $page_id) : ''),
        'image_url' => $image_url,
        'heading' => (string) (function_exists('get_field') ? get_field('school_seasonal_heading', $page_id) : ''),
        'body' => (string) (function_exists('get_field') ? get_field('school_seasonal_body', $page_id) : ''),
        'button_text' => (string) (function_exists('get_field') ? get_field('school_seasonal_button_text', $page_id) : ''),
        'button_url' => (string) (function_exists('get_field') ? get_field('school_seasonal_button_url', $page_id) : ''),
    );
}

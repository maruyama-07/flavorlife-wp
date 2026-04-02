<?php
/**
 * 求人情報 CPT（recruit_job）用 ACF
 * 旧固定ページの「サブタイトル（page_subtitle）」に相当する文言を一覧・詳細ヘッダーで使用
 */

add_action('acf/init', function () {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group(array(
        'key' => 'group_recruit_job',
        'title' => '求人サブタイトル',
        'fields' => array(
            array(
                'key' => 'field_recruit_job_subtitle',
                'label' => 'サブタイトル',
                'name' => 'recruit_job_subtitle',
                'type' => 'text',
                'instructions' => '一覧のタグ部分と、詳細ページ見出し下のサブタイトルに表示します（未入力のときは非表示）。',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'recruit_job',
                ),
            ),
        ),
        'position' => 'acf_after_title',
    ));
});

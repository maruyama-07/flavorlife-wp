<?php
/**
 * スクール講師（school_instructor）用 ACF
 */

add_action('acf/init', function () {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group(array(
        'key' => 'group_school_instructor',
        'title' => '講師・リンク',
        'fields' => array(
            array(
                'key' => 'field_school_instructor_furigana',
                'label' => 'フリガナ',
                'name' => 'school_instructor_furigana',
                'type' => 'text',
            ),
            array(
                'key' => 'field_school_instructor_link',
                'label' => 'ボタン・リンクURL（任意）',
                'name' => 'school_instructor_link',
                'type' => 'url',
                'instructions' => '本文でモーダルを出す場合、モーダル下部に「外部リンク」として表示されます。本文が空のときのみ、ボタンが直接このURLへ遷移します。',
            ),
            array(
                'key' => 'field_school_instructor_link_new_tab',
                'label' => '新しいタブで開く',
                'name' => 'school_instructor_link_new_tab',
                'type' => 'true_false',
                'instructions' => 'オンにすると、リンクを新しいタブで開きます。',
                'default_value' => 0,
                'ui' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'school_instructor',
                ),
            ),
        ),
        'position' => 'acf_after_title',
    ));
});

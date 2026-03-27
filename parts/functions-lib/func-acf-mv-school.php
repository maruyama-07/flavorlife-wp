<?php
/**
 * MV（スクール）スライド用 ACF（p-splide と同一フィールド名で共有）
 */
add_action('acf/init', function () {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group(array(
        'key' => 'group_mv_slider_school_slide',
        'title' => 'スライド設定',
        'fields' => array(
            array(
                'key' => 'field_mv_school_slide_img_pc',
                'label' => 'PC用画像',
                'name' => 'slide_img_pc',
                'type' => 'image',
                'return_format' => 'id',
                'preview_size' => 'medium',
            ),
            array(
                'key' => 'field_mv_school_slide_img_sp',
                'label' => 'スマホ用画像',
                'name' => 'slide_img_sp',
                'type' => 'image',
                'return_format' => 'id',
                'preview_size' => 'medium',
            ),
            array(
                'key' => 'field_mv_school_slide_img_alt',
                'label' => '代替テキスト',
                'name' => 'slide_img_alt',
                'type' => 'text',
            ),
            array(
                'key' => 'field_mv_school_slide_img_url',
                'label' => 'リンクURL',
                'name' => 'slide_img_url',
                'type' => 'url',
            ),
            array(
                'key' => 'field_mv_school_slide_img_tab',
                'label' => '新規タブで開く',
                'name' => 'slide_img_tab',
                'type' => 'true_false',
                'ui' => 1,
            ),
            array(
                'key' => 'field_mv_school_slide_text',
                'label' => 'スライドテキスト',
                'name' => 'slide_text',
                'type' => 'textarea',
                'rows' => 3,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'mv_slider_school',
                ),
            ),
        ),
    ));
});

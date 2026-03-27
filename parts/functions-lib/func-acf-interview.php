<?php
/**
 * インタビューページ用ACFフィールド
 */

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
        'key' => 'group_interview',
        'title' => 'インタビュープロフィール',
        'fields' => array(
            array(
                'key' => 'field_interview_image',
                'label' => 'プロフィール画像',
                'name' => 'interview_image',
                'type' => 'image',
                'instructions' => '円形で表示されます',
                'return_format' => 'array',
                'preview_size' => 'medium',
            ),
            array(
                'key' => 'field_interview_title',
                'label' => '役職',
                'name' => 'interview_title',
                'type' => 'text',
                'default_value' => '営業部 部長',
            ),
            array(
                'key' => 'field_interview_name_ja',
                'label' => '名前（日本語）',
                'name' => 'interview_name_ja',
                'type' => 'text',
                'default_value' => '榎原 昭徳',
            ),
            array(
                'key' => 'field_interview_name_en',
                'label' => '名前（ローマ字）',
                'name' => 'interview_name_en',
                'type' => 'text',
                'default_value' => 'AKINORI EBARA',
            ),
            array(
                'key' => 'field_interview_certifications',
                'label' => '資格・認定',
                'name' => 'interview_certifications',
                'type' => 'text',
                'default_value' => 'AEAJ認定アロマテラピーアドバイザー/アロマブレンドデザイナー',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'page_template',
                    'operator' => '==',
                    'value' => 'template-interview.php',
                ),
            ),
        ),
    ));
}

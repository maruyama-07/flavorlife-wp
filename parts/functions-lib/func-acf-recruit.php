<?php
/**
 * Recruitページ用ACFフィールド（page-hero動画）
 * 固定ページでテンプレート「求人詳細」を選択した場合のみ表示
 */

if (!function_exists('acf_add_local_field_group')) {
    return;
}

acf_add_local_field_group(array(
    'key' => 'group_recruit_hero_video',
    'title' => 'Recruit ヒーロー動画',
    'fields' => array(
        array(
            'key' => 'field_recruit_hero_video_tab',
            'label' => 'ヒーロー動画',
            'name' => '',
            'type' => 'tab',
        ),
        array(
            'key' => 'field_recruit_hero_video',
            'label' => 'ヒーロー動画（PC）',
            'name' => 'recruit_hero_video',
            'type' => 'file',
            'instructions' => '動画を設定すると、画像の代わりに動画が表示されます。MP4形式を推奨。画像と同じ比率・サイズで表示されます。途中で止まる場合は、解像度720p以下・ビットレート2Mbps程度に圧縮してください。',
            'required' => 0,
            'return_format' => 'url',
            'library' => 'all',
            'mime_types' => 'mp4,webm',
        ),
        array(
            'key' => 'field_recruit_hero_video_sp',
            'label' => 'ヒーロー動画（スマホ）',
            'name' => 'recruit_hero_video_sp',
            'type' => 'file',
            'instructions' => 'スマホ表示時に別の動画を使用する場合に設定（未設定の場合はPC用動画を使用）',
            'required' => 0,
            'return_format' => 'url',
            'library' => 'all',
            'mime_types' => 'mp4,webm',
        ),
        array(
            'key' => 'field_recruit_hero_video_poster',
            'label' => '動画ポスター画像',
            'name' => 'recruit_hero_video_poster',
            'type' => 'image',
            'instructions' => '動画読み込み中の表示画像（任意）。未設定の場合は黒背景で表示されます。',
            'required' => 0,
            'return_format' => 'url',
            'preview_size' => 'medium',
        ),
    ),
    'location' => array(
        array(
            array(
                'param' => 'page_template',
                'operator' => '==',
                'value' => 'template-recruit.php',
            ),
        ),
    ),
    'menu_order' => 5,
    'position' => 'normal',
    'style' => 'default',
));

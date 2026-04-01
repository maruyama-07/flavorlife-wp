<?php
/**
 * フロントページ用ACFフィールド
 */

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
        'key' => 'group_frontpage_media_text',
        'title' => 'メディア＋テキストセクション',
        'fields' => array(
            // 背景画像
            array(
                'key' => 'field_background_image',
                'label' => '背景画像',
                'name' => 'background_image',
                'type' => 'image',
                'instructions' => 'セクション全体の背景画像を選択してください',
                'required' => 0,
                'return_format' => 'url',
            ),
            // 左側メディア
            array(
                'key' => 'field_left_media',
                'label' => '左側：画像または動画',
                'name' => 'left_media',
                'type' => 'file',
                'instructions' => '左側に表示する画像または動画ファイルを選択してください',
                'required' => 0,
                'mime_types' => 'jpg,jpeg,png,gif,webp,mp4,mov,webm',
                'return_format' => 'array',
            ),
            // 右側テキスト
            array(
                'key' => 'field_section_text',
                'label' => 'テキスト',
                'name' => 'section_text',
                'type' => 'textarea',
                'instructions' => '本文を入力してください。' . "\n\n" . (function_exists('tool_acf_paragraph_field_instructions') ? tool_acf_paragraph_field_instructions() : ''),
                'required' => 0,
                'rows' => 3,
            ),
            array(
                'key' => 'field_section_highlight',
                'label' => '強調テキスト',
                'name' => 'section_highlight',
                'type' => 'text',
                'instructions' => 'Pure.のような強調テキスト（オプション）',
                'required' => 0,
            ),
            array(
                'key' => 'field_button_link',
                'label' => 'ボタンリンク',
                'name' => 'button_link',
                'type' => 'url',
                'instructions' => '「詳しく見る」ボタンのリンク先URL',
                'required' => 0,
            ),
            // 動画サムネイル（任意）
            array(
    'key' => 'field_video_thumbnail',
    'label' => '動画サムネイル（任意）',
    'name' => 'video_thumbnail',
    'type' => 'image',
    'instructions' => '左側メディアが動画の時に、最初に表示するサムネイル画像です。未設定なら動画をそのまま表示します。',
    'required' => 0,
    'return_format' => 'array', // ← url取得しやすいので array 推奨
    'preview_size' => 'medium',
    'library' => 'all',
  
    // （任意）左側メディアが入力されている時だけ表示
    'conditional_logic' => array(
      array(
        array(
          'field' => 'field_left_media',
          'operator' => '!=empty',
        ),
      ),
    ),
  ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'page_type',
                    'operator' => '==',
                    'value' => 'front_page',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
    ));
}
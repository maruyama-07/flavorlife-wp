<?php
/**
 * TinyMCEエディタの設定
 * クラシックエディタの「段落」ドロップダウンにカスタム見出しスタイルを追加
 * 「スタイル」ドロップダウンに下余白のオプションを追加
 */

/**
 * エディタに最小限のスタイルのみ読み込み
 * フル style.css はアニメーション・レイアウトで読み込み後5秒ほどエディタがガクつくため、
 * 見出し・余白ユーティリティのみの軽量版を使用
 */
add_editor_style('assets/css/editor-style.css');

/**
 * スタイルドロップダウン（styleselect）をツールバーに追加
 */
function tool_add_styleselect_to_toolbar($buttons)
{
    if (!in_array('styleselect', $buttons, true)) {
        array_unshift($buttons, 'styleselect');
    }
    return $buttons;
}
add_filter('mce_buttons_2', 'tool_add_styleselect_to_toolbar');

/**
 * フォントサイズドロップダウンを px 基準にする。
 * 既定の 12pt 等は画面では 16px 前後に換算され、本文 p の 1rem と見た目が同じになりやすい。
 *
 * TinyMCE（modern テーマ）は fontsize_formats を「半角スペース」だけで分割する。
 * 「10px 10px; 12px 12px;」のように書くと「10px」「10px;」の2項目になりセミコロン付きが重複表示されるため、
 * 値はスペース区切り1列にする。表示名と値を変えたいときだけ「小=12px」のように = を使う。
 */
function tool_tinymce_fontsize_formats_px($init_array)
{
    $init_array['fontsize_formats'] =
        '10px 12px 14px 16px 18px 20px 24px 28px 32px';

    return $init_array;
}
add_filter('tiny_mce_before_init', 'tool_tinymce_fontsize_formats_px', 15);

/**
 * 下余白スタイルを「スタイル」ドロップダウンに追加
 * 段落・見出しなどに適用可能。既存コンテンツには影響しない（適用時のみクラスが付与される）
 */
function tool_add_margin_styles($init_array)
{
    $margin_formats = array(
        // ページ内リンク用ID付与（リンクは /shop#tokyo のように作成）
        array(
            'title'   => 'ID付与（ページ内リンク用）',
            'items'   => array(
                array(
                    'title'      => 'tokyo',
                    'selector'   => 'p,h1,h2,h3,h4,h5,h6,div',
                    'attributes' => array('id' => 'tokyo'),
                ),
                array(
                    'title'      => 'osaka',
                    'selector'   => 'p,h1,h2,h3,h4,h5,h6,div',
                    'attributes' => array('id' => 'osaka'),
                ),
                array(
                    'title'      => 'nagoya',
                    'selector'   => 'p,h1,h2,h3,h4,h5,h6,div',
                    'attributes' => array('id' => 'nagoya'),
                ),
            ),
        ),
        array(
            'title'   => '下余白',
            'items'   => array(
                array(
                    'title'   => 'なし',
                    'selector' => 'p,h1,h2,h3,h4,h5,h6,div',
                    'classes' => 'u-mb-none',
                ),
                array(
                    'title'   => '小',
                    'selector' => 'p,h1,h2,h3,h4,h5,h6,div',
                    'classes' => 'u-mb-small',
                ),
                array(
                    'title'   => '中',
                    'selector' => 'p,h1,h2,h3,h4,h5,h6,div',
                    'classes' => 'u-mb-medium',
                ),
                array(
                    'title'   => '大',
                    'selector' => 'p,h1,h2,h3,h4,h5,h6,div',
                    'classes' => 'u-mb-large',
                ),
            ),
        ),
        array(
            'title' => '行間',
            'items' => array(
                array(
                    'title'    => '継承・リセット',
                    'selector' => 'p,h1,h2,h3,h4,h5,h6,div,li,blockquote',
                    'classes'  => 'u-lh-inherit',
                ),
                array(
                    'title'    => '1.5（やや詰める）',
                    'selector' => 'p,h1,h2,h3,h4,h5,h6,div,li,blockquote',
                    'classes'  => 'u-lh-150',
                ),
                array(
                    'title'    => '1.75（広め）',
                    'selector' => 'p,h1,h2,h3,h4,h5,h6,div,li,blockquote',
                    'classes'  => 'u-lh-175',
                ),
                array(
                    'title'    => '2（ゆったり）',
                    'selector' => 'p,h1,h2,h3,h4,h5,h6,div,li,blockquote',
                    'classes'  => 'u-lh-200',
                ),
                array(
                    'title'    => '2.2（教材・本文向け）',
                    'selector' => 'p,h1,h2,h3,h4,h5,h6,div,li,blockquote',
                    'classes'  => 'u-lh-220',
                ),
            ),
        ),
    );

    if (!empty($init_array['style_formats'])) {
        $existing = json_decode($init_array['style_formats'], true);
        if (is_array($existing)) {
            $init_array['style_formats'] = wp_json_encode(array_merge($existing, $margin_formats));
        } else {
            $init_array['style_formats'] = wp_json_encode($margin_formats);
        }
    } else {
        $init_array['style_formats'] = wp_json_encode($margin_formats);
    }

    return $init_array;
}
add_filter('tiny_mce_before_init', 'tool_add_margin_styles', 20);

/**
 * 見出しスタイルを「段落」ドロップダウンに追加
 */
function tool_add_heading_styles($init_array)
{
    // カスタムフォーマットを登録（block_formatsで参照するため）
    $custom_formats = array(
        'h2_head_line'   => array(
            'block'   => 'h2',
            'classes' => 'c-head-line',
        ),
        'h3_head_line'   => array(
            'block'   => 'h3',
            'classes' => 'c-head-line',
        ),
        'h2_head_center' => array(
            'block'   => 'h2',
            'classes' => 'c-head-center',
        ),
        'h3_head_center' => array(
            'block'   => 'h3',
            'classes' => 'c-head-center',
        ),
        'h2_head_question' => array(
            'block'   => 'h2',
            'classes' => 'c-head-question',
        ),
        'h3_head_question' => array(
            'block'   => 'h3',
            'classes' => 'c-head-question',
        ),
    );

    // 既存のformatsとマージ（上書きしないようカスタムを後に）
    if (!empty($init_array['formats'])) {
        $existing = json_decode($init_array['formats'], true);
        if (is_array($existing)) {
            $custom_formats = array_merge($existing, $custom_formats);
        }
    }
    $init_array['formats'] = wp_json_encode($custom_formats);

    // block_formats（段落ドロップダウン）に追加
    $block_formats = isset($init_array['block_formats']) ? $init_array['block_formats'] : '段落=p; 見出し1=h1; 見出し2=h2; 見出し3=h3; 見出し4=h4; 見出し5=h5; 見出し6=h6; 整形済み=pre';
    $custom_blocks = '見出し2（左線付き）=h2_head_line; 見出し3（左線付き）=h3_head_line; 見出し2（中央寄せ）=h2_head_center; 見出し3（中央寄せ）=h3_head_center; 見出し2（質問）=h2_head_question; 見出し3（質問）=h3_head_question';
    $init_array['block_formats'] = $block_formats . '; ' . $custom_blocks;

    // エディタ：画像の直後の要素で改行し、ブラウザ表示と揃える
    $clear_style = 'figure + * { clear: both; }';
    $init_array['content_style'] = isset($init_array['content_style']) ? $init_array['content_style'] . ' ' . $clear_style : $clear_style;

    return $init_array;
}
add_filter('tiny_mce_before_init', 'tool_add_heading_styles');

/**
 * 行間（line-height）を数値・単位で指定する TinyMCE プラグイン
 */
function tool_line_height_mce_plugin($plugins)
{
    $plugins['tool_line_height'] = get_template_directory_uri() . '/assets/js/admin/tool-line-height.js?v=' . filemtime(get_template_directory() . '/assets/js/admin/tool-line-height.js');
    return $plugins;
}
add_filter('mce_external_plugins', 'tool_line_height_mce_plugin');

function tool_line_height_mce_button($buttons)
{
    $buttons[] = 'tool_line_height';
    return $buttons;
}
add_filter('mce_buttons_2', 'tool_line_height_mce_button');

/**
 * ツール用モーダル（行間など）の管理画面スタイル
 */
function tool_tinymce_tool_modal_admin_assets($hook)
{
    if ($hook !== 'post.php' && $hook !== 'post-new.php') {
        return;
    }
    $path = get_template_directory() . '/assets/css/admin-tinymce-tool-modals.css';
    if (!is_readable($path)) {
        return;
    }
    wp_enqueue_style(
        'tool-tinymce-tool-modals',
        get_template_directory_uri() . '/assets/css/admin-tinymce-tool-modals.css',
        array(),
        (string) filemtime($path)
    );
}
add_action('admin_enqueue_scripts', 'tool_tinymce_tool_modal_admin_assets', 20);

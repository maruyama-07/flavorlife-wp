<?php
/**
 * クラシックエディタの改行をpタグで出力する
 * - TinyMCE: forced_root_block を p に固定（プラグイン上書き対策）
 * - 既存コンテンツ: 段落相当のdivをpに変換
 */

/**
 * TinyMCE：段落を必ずpタグで出力（優先度を高くしてプラグイン設定を上書き）
 */
add_filter('tiny_mce_before_init', 'tool_force_paragraph_block', 999);
function tool_force_paragraph_block($init)
{
    $init['forced_root_block'] = 'p';
    // 空オブジェクトは文字列で渡す（配列だと class-wp-editor.php の _parse_init で conversion エラー）
    $init['forced_root_block_attrs'] = '{}';
    return $init;
}

/**
 * the_content：段落相当のdivをpに変換（既存コンテンツ対応）
 * レイアウト用div（wp-block-*等）は除外。ネストしたdivは内側から順に変換
 */
add_filter('the_content', 'tool_convert_paragraph_divs_to_p', 5);
function tool_convert_paragraph_divs_to_p($content)
{
    if (empty($content) || strpos($content, '<div') === false) {
        return $content;
    }

    $block_classes = array(
        'wp-block-group', 'wp-block-columns', 'wp-block-column', 'wp-block-group__inner-container',
        'wp-block-buttons', 'wp-block-button', 'wp-block-media-text', 'wp-block-cover',
        'wp-block-image', 'wp-block-gallery', 'wp-block-table', 'wp-block-embed',
        'wp-block-separator', 'wp-block-spacer', 'wp-block-list', 'wp-block-quote',
        'wp-block-pullquote', 'wp-block-code', 'wp-block-preformatted', 'wp-block-verse',
        'wp-block-file', 'wp-block-audio', 'wp-block-video', 'wp-block-heading',
        'has-background', 'alignwide', 'alignfull', 'aligncenter', 'alignleft', 'alignright',
        // クラシックエディタの div（中に p があり p に変換すると無効ネストになる）
        'c-school-editor-banner',
        'c-school-editor-full-bg',
        'c-school-editor-full-bg__inner',
        'c-school-arrow-label',
    );

    $max_iterations = 20;
    $iteration = 0;

    while (strpos($content, '<div') !== false && $iteration < $max_iterations) {
        $iteration++;
        $prev = $content;
        $content = preg_replace_callback(
            '/<div(\s[^>]*)?>((?:(?!<div[\s>]).)*?)<\/div>/s',
            function ($m) use ($block_classes) {
                $attrs = $m[1] ?? '';
                $inner = $m[2];

                // 講座紹介2カラム等：BEM 子要素も含め div のまま保つ（p 化でレイアウトが壊れる）
                if (preg_match('/class=["\']([^"\']*)["\']/', $attrs, $class_m)) {
                    if (strpos($class_m[1], 'c-school-course-intro') !== false) {
                        return $m[0];
                    }
                    // 本文2カラム（TinyMCE・c-content-two-col）：div→p にすると wpautop 前に DOM が壊れる
                    if (strpos($class_m[1], 'c-content-two-col') !== false) {
                        return $m[0];
                    }
                }

                // class属性があればレイアウト用かチェック
                if (preg_match('/class=["\']([^"\']*)["\']/', $attrs, $class_m)) {
                    $classes = preg_split('/\s+/', trim($class_m[1]));
                    foreach ($classes as $c) {
                        if ($c === 'l-inner') {
                            return $m[0];
                        }
                        foreach ($block_classes as $block) {
                            if (strpos($c, $block) !== false) {
                                return $m[0]; // 変換しない
                            }
                        }
                    }
                }

                return '<p' . $attrs . '>' . $inner . '</p>';
            },
            $content
        );
        if ($content === $prev) {
            break;
        }
    }

    return $content;
}

/**
 * the_content：開始〜閉じの間が空白・改行のみの p を真の空に正規化する
 * - CSS の :empty は子テキストノード（改行1文字含む）があるとマッチしないため、
 *   data-start / data-end 付きの空行用 p などで white-space: pre-wrap が効かない問題を防ぐ
 * - wpautop 後に実行（優先度 15）
 */
add_filter('the_content', 'tool_normalize_whitespace_only_empty_p', 15);
function tool_normalize_whitespace_only_empty_p($content)
{
    if ($content === '' || strpos($content, '<p') === false) {
        return $content;
    }

    return preg_replace('/<p(\s[^>]*)?>\s*<\/p>/u', '<p$1></p>', $content);
}

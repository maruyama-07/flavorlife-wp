<?php
/**
 * 本文内の画像をセマンティックに変換
 * <p><img></p> → <figure><img></figure>
 */

/**
 * pタグで囲まれたimgをfigureに変換
 */
function tool_content_img_to_figure($content)
{
    if (empty($content)) {
        return $content;
    }

    // <p>〜</p> 内が img のみ（リンクで囲まれている場合も含む）の場合に figure に変換
    $content = preg_replace_callback(
        '/<p([^>]*)>(\s*(?:<a[^>]*>\s*)?<img([^>]+)>(?:\s*<\/a>)?\s*)<\/p>/is',
        function ($matches) {
            $p_attrs = $matches[1];
            $inner   = $matches[2];

            // pのclassを引き継ぎ、wp-content-figure を追加
            if (preg_match('/class=["\']([^"\']*)["\']/', $p_attrs, $m)) {
                $figure_attrs = preg_replace('/class=["\']([^"\']*)["\']/', 'class="wp-content-figure ' . $m[1] . '"', $p_attrs);
            } else {
                $figure_attrs = trim($p_attrs) ? $p_attrs . ' class="wp-content-figure"' : ' class="wp-content-figure"';
            }

            return '<figure' . $figure_attrs . '>' . $inner . '</figure>';
        },
        $content
    );

    return $content;
}
add_filter('the_content', 'tool_content_img_to_figure', 20);

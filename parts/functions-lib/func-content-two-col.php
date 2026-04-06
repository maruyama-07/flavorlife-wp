<?php
/**
 * 本文2カラム（TinyMCE）
 * コーポレート・スクール共通。マークアップのみ（ショートコードなし）
 */

/**
 * TinyMCE に「2カラム」ボタンを追加
 *
 * @param string[] $buttons
 * @return string[]
 */
function tool_content_two_col_mce_button($buttons)
{
    if (!is_array($buttons)) {
        return $buttons;
    }
    $buttons[] = 'content_two_col';

    return $buttons;
}
add_filter('mce_buttons_2', 'tool_content_two_col_mce_button', 12);

/**
 * @param array<string, string> $plugin_array
 * @return array<string, string>
 */
function tool_content_two_col_mce_plugin($plugin_array)
{
    if (!is_array($plugin_array)) {
        return $plugin_array;
    }
    $ctc_js = get_theme_file_path('assets/js/admin/content-two-col.js');
    $ctc_ver = is_readable($ctc_js) ? (string) filemtime($ctc_js) : '1';
    $plugin_array['content_two_col'] = get_template_directory_uri() . '/assets/js/admin/content-two-col.js?ver=' . rawurlencode($ctc_ver);

    return $plugin_array;
}
add_filter('mce_external_plugins', 'tool_content_two_col_mce_plugin');

/**
 * クラシックエディタ iframe 内でも2カラムが見えるよう content_style を付与（全画面共通）
 *
 * @param array<string, mixed> $init_array
 * @return array<string, mixed>
 */
function tool_content_two_col_tinymce_content_style($init_array)
{
    if (!is_admin()) {
        return $init_array;
    }

    $css = <<<'CSS'
.mce-content-body .c-content-two-col,.wp-block-freeform .c-content-two-col{display:flex!important;flex-wrap:wrap!important;justify-content:space-between!important;align-items:flex-start!important;width:100%!important;box-sizing:border-box!important;margin:1.5rem 0!important;}
.mce-content-body .c-content-two-col__col,.wp-block-freeform .c-content-two-col__col{flex:0 0 45%!important;max-width:45%!important;box-sizing:border-box!important;min-width:0!important;}
@media screen and (max-width:767px){.mce-content-body .c-content-two-col__col,.wp-block-freeform .c-content-two-col__col{flex:0 0 100%!important;max-width:100%!important;width:100%!important;}}
CSS;

    $css_one_line = trim(preg_replace('/ {2,}/', ' ', str_replace(array("\r\n", "\r", "\n"), ' ', $css)));
    $merged       = isset($init_array['content_style']) ? $init_array['content_style'] . ' ' . $css_one_line : $css_one_line;
    $init_array['content_style'] = trim(preg_replace('/ {2,}/', ' ', str_replace(array("\r\n", "\r", "\n"), ' ', $merged)));

    return $init_array;
}
add_filter('tiny_mce_before_init', 'tool_content_two_col_tinymce_content_style', 14);

/**
 * 本文全体を DOMDocument::saveHTML で再出力すると、libxml が属性を
 * class="&quot;foo&quot;" のように壊し、2カラム以外（講座紹介・画像など）も一斉に崩れる。
 * 列内の <p> 化は tool_content_two_col_wrap_col_inner_with_p で正規表現のみ行う。
 *
 * 保存・編集・表示で、余分に入った &quot; を属性値から除去する。
 *
 * @param string   $content
 * @param int|null $post_id content_edit_pre から（未使用）
 * @return string
 */
function tool_content_two_col_fix_double_entity_attribute_quotes($content, $post_id = null)
{
    unset($post_id);

    if (! is_string($content) || $content === '' || strpos($content, '&quot;') === false) {
        return $content;
    }

    $out   = $content;
    $prev  = '';
    $guard = 0;
    while ($out !== $prev && $guard < 6) {
        $prev = $out;
        $out  = str_replace('="&quot;', '="', $out);
        $out  = str_replace("='&quot;", "='", $out);
        $out  = str_replace('&quot;"', '"', $out);
        $out  = str_replace("&quot;'", "'", $out);
        $guard++;
    }

    return $out;
}
add_filter('the_content', 'tool_content_two_col_fix_double_entity_attribute_quotes', 2);
add_filter('content_save_pre', 'tool_content_two_col_fix_double_entity_attribute_quotes', 7);
add_filter('content_edit_pre', 'tool_content_two_col_fix_double_entity_attribute_quotes', 7, 2);

/**
 * ACF WYSIWYG でも同様の属性崩れを修復
 *
 * @param mixed               $value
 * @param int|string          $post_id
 * @param array<string,mixed> $field
 * @return mixed
 */
function tool_content_two_col_acf_fix_wysiwyg_attribute_quotes($value, $post_id, $field)
{
    unset($post_id);

    if (empty($field['type']) || $field['type'] !== 'wysiwyg' || ! is_string($value)) {
        return $value;
    }

    return tool_content_two_col_fix_double_entity_attribute_quotes($value);
}
add_filter('acf/load_value', 'tool_content_two_col_acf_fix_wysiwyg_attribute_quotes', 5, 3);
add_filter('acf/update_value', 'tool_content_two_col_acf_fix_wysiwyg_attribute_quotes', 5, 3);

/**
 * .c-content-two-col__col 直下がテキスト／インラインのみのとき 1 つの <p> で包む。
 * 全文を DOMDocument::saveHTML しないため、他ブロックの属性は壊さない。
 * 列内に子 <div> や ul/ol/table/見出しがある場合はスキップ（誤変換防止）。
 *
 * @param string   $content
 * @param int|null $post_id
 * @return string
 */
function tool_content_two_col_wrap_col_inner_with_p($content, $post_id = null)
{
    unset($post_id);

    if (! is_string($content) || $content === '' || strpos($content, 'c-content-two-col__col') === false) {
        return $content;
    }

    $out = preg_replace_callback(
        '/<div(\s[^>]*\bclass\s*=\s*["\'][^"\']*\bc-content-two-col__col\b[^"\']*["\'][^>]*)>([\s\S]*?)<\/div>/iu',
        'tool_content_two_col_wrap_col_inner_callback',
        $content
    );

    return is_string($out) ? $out : $content;
}

/**
 * @param array<int,string> $m
 * @return string
 */
function tool_content_two_col_wrap_col_inner_callback($m)
{
    $open  = $m[1];
    $inner = $m[2];

    if (preg_match('/<div\b/i', $inner)) {
        return $m[0];
    }

    if (preg_match('/<\s*(ul|ol|table|h[1-6])\b/i', $inner)) {
        return $m[0];
    }

    $t = trim($inner);
    if ($t === '') {
        return '<div' . $open . '><p><br></p></div>';
    }

    if (preg_match('/^<(p|ul|ol|blockquote|table|figure)\b/i', $t)) {
        return $m[0];
    }

    $with_br = preg_replace('/\r\n|\r|\n/', '<br />', $t);

    return '<div' . $open . '><p>' . $with_br . '</p></div>';
}
add_filter('the_content', 'tool_content_two_col_wrap_col_inner_with_p', 3);
add_filter('content_save_pre', 'tool_content_two_col_wrap_col_inner_with_p', 8);
add_filter('content_edit_pre', 'tool_content_two_col_wrap_col_inner_with_p', 8, 2);

/**
 * @param mixed               $value
 * @param int|string          $post_id
 * @param array<string,mixed> $field
 * @return mixed
 */
function tool_content_two_col_acf_wrap_col_inner_with_p($value, $post_id, $field)
{
    unset($post_id);

    if (empty($field['type']) || $field['type'] !== 'wysiwyg' || ! is_string($value)) {
        return $value;
    }

    return tool_content_two_col_wrap_col_inner_with_p($value);
}
add_filter('acf/load_value', 'tool_content_two_col_acf_wrap_col_inner_with_p', 6, 3);
add_filter('acf/update_value', 'tool_content_two_col_acf_wrap_col_inner_with_p', 6, 3);

/**
 * wpautop（優先度10）より前に実行: .c-content-two-col ブロック全体を退避する。
 * 退避しないと列内の改行がすべて <p> に分割され、列の外へ段落があふれる。
 *
 * @param string $content
 * @return string
 */
function tool_content_two_col_protect_before_wpautop($content)
{
    if ($content === '' || strpos($content, 'c-content-two-col') === false) {
        return $content;
    }

    if (! class_exists('DOMDocument')) {
        return $content;
    }

    $GLOBALS['tool_content_two_col_protected'] = array();

    libxml_use_internal_errors(true);

    $wrapper_id = 'tool-ctc-pre-' . wp_generate_password(8, false, false);
    $html = '<!DOCTYPE html><html><head><meta charset="UTF-8"></head><body><div id="' . esc_attr($wrapper_id) . '">' . $content . '</div></body></html>';

    $doc = new DOMDocument();
    if (! @$doc->loadHTML($html)) {
        libxml_clear_errors();
        return $content;
    }

    $root = $doc->getElementById($wrapper_id);
    if (! $root) {
        libxml_clear_errors();
        return $content;
    }

    $candidates = array();
    foreach ($root->getElementsByTagName('div') as $div) {
        $cls = $div->getAttribute('class');
        if ($cls === '' || ! preg_match('/\bc-content-two-col\b/', $cls) || preg_match('/\bc-content-two-col__/', $cls)) {
            continue;
        }
        // 別の .c-content-two-col ラッパーの内側にあるものは除外（外側から順に退避する）
        $ancestor = $div->parentNode;
        $inside_other_wrapper = false;
        while ($ancestor && $ancestor !== $root && $ancestor->nodeType === XML_ELEMENT_NODE) {
            if ($ancestor->tagName === 'div') {
                $ac = $ancestor->getAttribute('class');
                if ($ac !== '' && preg_match('/\bc-content-two-col\b/', $ac) && ! preg_match('/\bc-content-two-col__/', $ac)) {
                    $inside_other_wrapper = true;
                    break;
                }
            }
            $ancestor = $ancestor->parentNode;
        }
        if ($inside_other_wrapper) {
            continue;
        }
        $candidates[] = $div;
    }

    $idx = 0;
    foreach ($candidates as $child) {
        $GLOBALS['tool_content_two_col_protected'][ $idx ] = $doc->saveHTML($child);
        $comment = $doc->createComment('TOOLCTCBLOCK' . $idx);
        if ($child->parentNode) {
            $child->parentNode->replaceChild($comment, $child);
        }
        $idx++;
    }

    if ($idx === 0) {
        libxml_clear_errors();
        return $content;
    }

    $inner = '';
    foreach ($root->childNodes as $ch) {
        $inner .= $doc->saveHTML($ch);
    }

    libxml_clear_errors();
    return $inner;
}
// 優先度4: func-content-paragraph の div→p（5）より前に退避する
add_filter('the_content', 'tool_content_two_col_protect_before_wpautop', 4);

/**
 * wpautop の直後に退避した 2 カラム HTML を戻す。
 *
 * @param string $content
 * @return string
 */
function tool_content_two_col_restore_after_wpautop($content)
{
    if (empty($GLOBALS['tool_content_two_col_protected']) || ! is_array($GLOBALS['tool_content_two_col_protected'])) {
        return $content;
    }

    foreach ($GLOBALS['tool_content_two_col_protected'] as $i => $block_html) {
        $token = 'TOOLCTCBLOCK' . (int) $i;
        $escaped = preg_quote($token, '/');
        $patterns = array(
            '/<!--\s*' . $escaped . '\s*-->/',
            '/<p>\s*<!--\s*' . $escaped . '\s*-->\s*<\/p>/i',
        );
        foreach ($patterns as $pat) {
            if (preg_match($pat, $content)) {
                $content = preg_replace_callback(
                    $pat,
                    function () use ($block_html) {
                        return $block_html;
                    },
                    $content,
                    1
                );
                break;
            }
        }
    }

    $GLOBALS['tool_content_two_col_protected'] = array();

    return $content;
}
add_filter('the_content', 'tool_content_two_col_restore_after_wpautop', 11);

/**
 * フロント表示用: wpautop により p.c-content-two-col / p.c-content-two-col__col になると、
 * p の中に p を入れられずブラウザが DOM を壊し2カラムにならないため div に変換する。
 *
 * @param string $content
 * @return string
 */
function tool_content_two_col_fix_invalid_p_tags($content)
{
    if ($content === '' || strpos($content, 'c-content-two-col') === false) {
        return $content;
    }

    if (! class_exists('DOMDocument')) {
        return $content;
    }

    libxml_use_internal_errors(true);

    $wrapper_id = 'tool-ctc-' . wp_generate_password(8, false, false);
    $html = '<!DOCTYPE html><html><head><meta charset="UTF-8"></head><body><div id="' . esc_attr($wrapper_id) . '">' . $content . '</div></body></html>';

    $doc = new DOMDocument();
    $loaded = @$doc->loadHTML($html);
    if (! $loaded) {
        libxml_clear_errors();
        return $content;
    }

    $wrapper = $doc->getElementById($wrapper_id);
    if (! $wrapper) {
        libxml_clear_errors();
        return $content;
    }

    $ps = array();
    foreach ($wrapper->getElementsByTagName('p') as $p) {
        $ps[] = $p;
    }

    foreach ($ps as $p) {
        $class = $p->getAttribute('class');
        if ($class === '') {
            continue;
        }
        $is_col = (bool) preg_match('/\bc-content-two-col__col\b/', $class);
        $is_wrap = (bool) preg_match('/\bc-content-two-col\b/', $class) && ! preg_match('/\bc-content-two-col__/', $class);
        if (! $is_col && ! $is_wrap) {
            continue;
        }

        $div = $doc->createElement('div');
        if ($p->hasAttributes()) {
            foreach ($p->attributes as $attr) {
                $div->setAttribute($attr->name, $attr->value);
            }
        }
        while ($p->firstChild) {
            $div->appendChild($p->firstChild);
        }
        if ($p->parentNode) {
            $p->parentNode->replaceChild($div, $p);
        }
    }

    $inner = '';
    foreach ($wrapper->childNodes as $child) {
        $inner .= $doc->saveHTML($child);
    }

    libxml_clear_errors();
    return $inner;
}
add_filter('the_content', 'tool_content_two_col_fix_invalid_p_tags', 12);

/**
 * wpautop が div 内テキストを外に出し、空の c-content-two-col__col と後続の p に分断するのを修復する。
 *
 * @param string $content
 * @return string
 */
function tool_content_two_col_repair_wpautop_split($content)
{
    if ($content === '' || strpos($content, 'c-content-two-col') === false) {
        return $content;
    }

    if (! class_exists('DOMDocument')) {
        return $content;
    }

    libxml_use_internal_errors(true);

    $wrapper_id = 'tool-ctc-rp-' . wp_generate_password(8, false, false);
    $html = '<!DOCTYPE html><html><head><meta charset="UTF-8"></head><body><div id="' . esc_attr($wrapper_id) . '">' . $content . '</div></body></html>';

    $doc = new DOMDocument();
    if (! @$doc->loadHTML($html)) {
        libxml_clear_errors();
        return $content;
    }

    $root = $doc->getElementById($wrapper_id);
    if (! $root) {
        libxml_clear_errors();
        return $content;
    }

    $wraps = array();
    foreach ($root->getElementsByTagName('div') as $div) {
        $class = $div->getAttribute('class');
        if ($class !== '' && preg_match('/\bc-content-two-col\b/', $class) && ! preg_match('/\bc-content-two-col__/', $class)) {
            $wraps[] = $div;
        }
    }

    foreach ($wraps as $wrap) {
        tool_content_two_col_repair_one_block($wrap);
    }

    $inner = '';
    foreach ($root->childNodes as $child) {
        $inner .= $doc->saveHTML($child);
    }

    libxml_clear_errors();
    return $inner;
}
add_filter('the_content', 'tool_content_two_col_repair_wpautop_split', 13);

/**
 * @param DOMElement $wrap
 */
function tool_content_two_col_repair_one_block(DOMElement $wrap)
{
    $cols_in_wrap = array();
    foreach ($wrap->childNodes as $ch) {
        if ($ch->nodeType !== XML_ELEMENT_NODE) {
            continue;
        }
        /** @var DOMElement $ch */
        if ($ch->tagName === 'div' && preg_match('/\bc-content-two-col__col\b/', $ch->getAttribute('class'))) {
            $cols_in_wrap[] = $ch;
        }
    }

    $all_cols_filled = true;
    foreach ($cols_in_wrap as $c) {
        if (! $c->hasChildNodes()) {
            $all_cols_filled = false;
            break;
        }
    }
    if (count($cols_in_wrap) >= 1 && $all_cols_filled) {
        return;
    }

    $parent = $wrap->parentNode;
    if (! $parent) {
        return;
    }

    $bucket = array();
    $node = $wrap->nextSibling;
    while ($node) {
        $next = $node->nextSibling;

        if ($node->nodeType === XML_TEXT_NODE) {
            if (trim($node->textContent) === '') {
                if ($parent) {
                    $parent->removeChild($node);
                }
                $node = $next;
                continue;
            }
            break;
        }

        if ($node->nodeType !== XML_ELEMENT_NODE) {
            break;
        }

        /** @var DOMElement $node */
        $tag = $node->tagName;
        $cls = $node->getAttribute('class');

        if ($tag === 'div' && preg_match('/\bc-content-two-col__col\b/', $cls)) {
            $bucket[] = $node;
            $node = $next;
            continue;
        }

        if ($tag === 'p') {
            $bucket[] = $node;
            $node = $next;
            continue;
        }

        break;
    }

    foreach ($bucket as $el) {
        if ($el->nodeType !== XML_ELEMENT_NODE) {
            continue;
        }
        /** @var DOMElement $el */
        if ($el->tagName === 'div' && preg_match('/\bc-content-two-col__col\b/', $el->getAttribute('class'))) {
            $wrap->appendChild($el);
        }
    }

    $cols = array();
    foreach ($wrap->childNodes as $ch) {
        if ($ch->nodeType !== XML_ELEMENT_NODE) {
            continue;
        }
        /** @var DOMElement $ch */
        if ($ch->tagName === 'div' && preg_match('/\bc-content-two-col__col\b/', $ch->getAttribute('class'))) {
            $cols[] = $ch;
        }
    }

    if (empty($cols)) {
        return;
    }

    $nonempty_ps = array();
    foreach ($bucket as $el) {
        if ($el->nodeType !== XML_ELEMENT_NODE || $el->tagName !== 'p') {
            continue;
        }
        if (trim($el->textContent) !== '') {
            $nonempty_ps[] = $el;
        } elseif ($el->parentNode) {
            $el->parentNode->removeChild($el);
        }
    }

    $n_cols = count($cols);
    $n_p = count($nonempty_ps);
    for ($i = 0; $i < $n_cols && $i < $n_p; $i++) {
        $cols[$i]->appendChild($nonempty_ps[$i]);
    }
}

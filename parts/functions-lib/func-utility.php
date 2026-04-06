<?php
/**
 * func-utility
 * ユーティリティ関数
 */

/**
 * WP-PageNavi: Back/Next を常時表示し、無効時は span.disabled を出力
 */
add_filter('wp_pagenavi', 'tool_pagenavi_always_show_prev_next', 10, 2);

function tool_pagenavi_always_show_prev_next($html, $args)
{
    $query = isset($args['query']) ? $args['query'] : $GLOBALS['wp_query'];
    $paged = max(1, (int) $query->get('paged'));
    $total  = (int) $query->max_num_pages;
    $prev_text = '&lt; Back';
    $next_text = 'Next &gt;';
    $wrapper_tag = isset($args['wrapper_tag']) ? $args['wrapper_tag'] : 'div';

    // 戻れない時: wp-pagenavi 内の先頭に無効な Back を追加
    if ($paged <= 1 && strpos($html, 'previouspostslink') === false) {
        $html = preg_replace(
            '/(<'.$wrapper_tag.'[^>]*class=[\'"]wp-pagenavi[\'"][^>]*>)\s*\n/',
            '$1'."\n<span class='previouspostslink disabled'>{$prev_text}</span>\n",
            $html,
            1
        );
    }

    // 次へ行けない時: wp-pagenavi 内の末尾（</div>の直前）に無効な Next を追加
    if ($paged >= $total && $total > 0 && strpos($html, 'nextpostslink') === false) {
        $html = preg_replace(
            '/(\n)(\s*<\/'.$wrapper_tag.'>\s*)$/',
            '$1<span class=\'nextpostslink disabled\'>'.$next_text.'</span>$1$2',
            $html,
            1
        );
    }

    return $html;
}

/**
 * 投稿が指定した日数以内であるか判定（未設定の場合は7日）
 */
function new_posting($days = 7 ,$entry_time = null){
  $today = date_i18n('U');
  if(!$entry_time) {
    $entry = get_the_time('U');
  }
  $posting = date('U',($today - $entry)) / 86400;
  if( $days > $posting) {
    return true;
  }
  return false;
}

/**
 * 投稿・固定ページの本文（the_content）でも条件付き改行トークンを変換
 * - {sp} / {{sp}} … スマホのみ &lt;br class="u-br-sp"&gt;
 * - {pc} / {{pc}} … PC（768px以上）のみ &lt;br class="u-br-pc"&gt;
 * クラシックエディタの「ビジュアル」ではそのまま文字として表示され、フロントで置換される
 */
add_filter('the_content', 'tool_the_content_replace_sp_break', 12);

function tool_the_content_replace_sp_break($content)
{
    if ($content === '' || $content === null) {
        return $content;
    }
    $content = (string) $content;
    if (
        strpos($content, '{sp}') === false && strpos($content, '{{sp}}') === false
        && strpos($content, '{pc}') === false && strpos($content, '{{pc}}') === false
    ) {
        return $content;
    }

    $content = str_replace(array('{{sp}}', '{sp}'), '<br class="u-br-sp">', $content);
    $content = str_replace(array('{{pc}}', '{pc}'), '<br class="u-br-pc">', $content);

    return $content;
}

/**
 * ACFテキストエリア（段落）向けの整形（コーポレート・スクール共通）
 * - 改行は nl2br で反映
 * - {sp} / {{sp}} はスマホのみ改行、{pc} / {{pc}} は PC のみ改行（768px 以上）
 * - 固定ページ・投稿本文では tool_the_content_replace_sp_break（the_content）でも同トークンを解釈
 * 管理画面の説明文は tool_acf_paragraph_field_instructions() をフィールドに付与
 *
 * @param string $text
 * @return string
 */
function tool_format_text_with_sp_break($text)
{
    $text = (string) $text;
    // ACF format_value 済みの条件付き改行 br を先に退避（二重処理時に esc_html でタグが文字化けしないよう）
    $text = preg_replace('/<br\b[^>]*\bu-br-sp\b[^>]*>/i', '__SP_BR__', $text);
    $text = preg_replace('/<br\b[^>]*\bu-br-pc\b[^>]*>/i', '__PC_BR__', $text);
    // ACF new_lines=br 等の通常 <br> を改行へ（属性付きも含む）
    $text = preg_replace('/<br\b[^>]*>/i', "\n", $text);
    // 長いトークンを先に退避（{{sp}} 内に {sp} が含まれるため）
    $text = str_replace(array('{{sp}}', '{sp}'), '__SP_BR__', $text);
    $text = str_replace(array('{{pc}}', '{pc}'), '__PC_BR__', $text);

    $escaped = esc_html($text);
    // nl2br() は <br /> 挿入後も改行文字を残すため、<br>→\n に戻した文字列と組み合わせると \n\n になり二重 <br /> になる。改行は置換のみにする。
    $escaped = preg_replace('/\r\n|\r|\n/', '<br />', $escaped);
    $escaped = str_replace('__SP_BR__', '<br class="u-br-sp">', $escaped);
    $escaped = str_replace('__PC_BR__', '<br class="u-br-pc">', $escaped);

    return wp_kses($escaped, array('br' => array('class' => true)));
}

/**
 * ACF text / textarea の表示用エスケープ（{sp}/{pc} 変換後の br を許可）
 * esc_html() は br を潰すため、get_field 結果の表示にはこちらを使う
 *
 * @param string $text
 * @return string
 */
function tool_esc_acf_text_for_display($text)
{
    return wp_kses((string) $text, array('br' => array('class' => true)));
}

/**
 * ACF text / textarea をフロント表示用に整形（get_field 直後を想定）
 * - format_value で既に HTML 化されている場合に esc_html すると &lt;br&gt; になるため使い分け
 * - トークン未処理・u-br 付き br・通常の &lt;br&gt;・プレーン改行を判別
 *
 * @param string $value
 * @return string
 */
function tool_acf_format_field_for_echo($value)
{
    $value = (string) $value;
    if ($value === '') {
        return '';
    }
    if (
        strpos($value, '{sp}') !== false || strpos($value, '{{sp}}') !== false
        || strpos($value, '{pc}') !== false || strpos($value, '{{pc}}') !== false
    ) {
        return tool_format_text_with_sp_break($value);
    }
    if (tool_acf_text_has_sp_pc_break_html($value)) {
        return tool_esc_acf_text_for_display($value);
    }
    if (preg_match('/<br\b/i', $value)) {
        return wp_kses($value, array(
            'br' => array('class' => true, 'style' => true),
        ));
    }

    return nl2br(esc_html($value));
}

/**
 * tool_format_text_with_sp_break 適用済みか（条件付き改行用 br が含まれる）
 *
 * @param string $html
 * @return bool
 */
function tool_acf_text_has_sp_pc_break_html($html)
{
    return (bool) preg_match('/class=[\'"]u-br-(sp|pc)[\'"]/', (string) $html);
}

/**
 * ACF textarea を表示（トークンありは wpautop なし・なしは従来どおり wpautop）
 *
 * @param string $html get_field 済みの文字列
 */
function tool_acf_echo_textarea_for_display($html)
{
    $html = (string) $html;
    if ($html === '') {
        return;
    }
    if (tool_acf_text_has_sp_pc_break_html($html)) {
        echo tool_esc_acf_text_for_display($html);
    } else {
        echo wp_kses_post(wpautop($html));
    }
}

/**
 * ACF の text / textarea で {sp}・{pc} をフロント表示時に解釈する
 * - 管理画面では raw のまま（編集欄に HTML が出ないよう）
 * - the_content は通らないため get_field 単体では従来トークンが効かなかった
 */
add_filter('acf/format_value/type=text', 'tool_acf_format_value_sp_break_tokens', 20, 3);
add_filter('acf/format_value/type=textarea', 'tool_acf_format_value_sp_break_tokens', 20, 3);

function tool_acf_format_value_sp_break_tokens($value, $post_id, $field)
{
    if (!is_string($value) || $value === '') {
        return $value;
    }
    if (is_admin()) {
        return $value;
    }
    if (
        strpos($value, '{sp}') === false && strpos($value, '{{sp}}') === false
        && strpos($value, '{pc}') === false && strpos($value, '{{pc}}') === false
    ) {
        return $value;
    }
    if (!function_exists('tool_format_text_with_sp_break')) {
        return $value;
    }

    return tool_format_text_with_sp_break($value);
}

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
    // ACFの new_lines=br で混入する <br> を一旦改行へ戻す
    $text = preg_replace('/<br\s*\/?>/i', "\n", $text);
    // 長いトークンを先に退避（{{sp}} 内に {sp} が含まれるため）
    $text = str_replace(array('{{sp}}', '{sp}'), '__SP_BR__', $text);
    $text = str_replace(array('{{pc}}', '{pc}'), '__PC_BR__', $text);

    $escaped = esc_html($text);
    $escaped = nl2br($escaped);
    $escaped = str_replace('__SP_BR__', '<br class="u-br-sp">', $escaped);
    $escaped = str_replace('__PC_BR__', '<br class="u-br-pc">', $escaped);

    return wp_kses($escaped, array('br' => array('class' => true)));
}

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
 * ACFテキストエリア（段落）向けの整形（コーポレート・スクール共通）
 * - 改行は nl2br で反映
 * - {sp} または {{sp}} はスマホのみ改行（&lt;br class="u-br-sp"&gt;）。スタイルは .u-br-sp（style.css / school-style）
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
    // {sp} は nl2br 前にプレースホルダへ退避
    $text = str_replace(array('{{sp}}', '{sp}'), '__SP_BR__', $text);

    $escaped = esc_html($text);
    $escaped = nl2br($escaped);
    $escaped = str_replace('__SP_BR__', '<br class="u-br-sp">', $escaped);
    return wp_kses($escaped, array('br' => array('class' => true)));
}

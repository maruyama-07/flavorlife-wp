<?php
/**
 * スクール紹介（/school/about/）専用：講師紹介グリッド
 *
 * データは「スクール講師」カスタム投稿タイプで管理（人数上限なし・順序は投稿の「順序」）。
 * 本文に [school_instructors] を挿入すると UI を出力。
 * 見出し文言のみ固定ページの ACF（school_about_instructors_title）。
 *
 * 旧5枠の固定フィールドにだけデータがある場合は、CPTが空のとき限りフォールバック表示。
 */

add_action('acf/init', function () {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    $about_id = function_exists('school_section_get_about_page_id') ? school_section_get_about_page_id() : 0;
    if (!$about_id) {
        return;
    }

    acf_add_local_field_group(array(
        'key' => 'group_school_about_instructors',
        'title' => 'スクール紹介・講師一覧',
        'fields' => array(
            array(
                'key' => 'field_school_about_instructors_tab',
                'label' => '講師紹介（ショートコード）',
                'name' => '',
                'type' => 'tab',
                'placement' => 'top',
            ),
            array(
                'key' => 'field_school_about_instructors_note',
                'label' => '',
                'name' => '',
                'type' => 'message',
                'message' => '講師の追加・写真・リンクは左メニュー「スクール講師」から行ってください（人数に上限はありません）。並び順は各投稿の「公開」欄の「順序」で小さい数字が先です。<br>本文の表示したい位置にショートコード <code>[school_instructors]</code> を1行で入力してください。',
                'new_lines' => 'wpautop',
            ),
            array(
                'key' => 'field_school_about_instructors_title',
                'label' => '見出しテキスト',
                'name' => 'school_about_instructors_title',
                'type' => 'text',
                'default_value' => '講師紹介',
                'placeholder' => '講師紹介',
            ),
            array(
                'key' => 'field_school_about_bottom_links_tab',
                'label' => 'ページ下部リンク（/school/about 用）',
                'name' => '',
                'type' => 'tab',
                'placement' => 'top',
            ),
            array(
                'key' => 'field_school_about_bottom_links_note',
                'label' => '',
                'name' => '',
                'type' => 'message',
                'message' => 'スクール紹介ページ最下部に表示する2カラム導線です。スマホは1カラムになります。',
                'new_lines' => 'wpautop',
            ),
            array(
                'key' => 'field_school_about_bottom_link_1_image',
                'label' => '左カード・画像',
                'name' => 'school_about_bottom_link_1_image',
                'type' => 'image',
                'return_format' => 'array',
                'preview_size' => 'medium',
            ),
            array(
                'key' => 'field_school_about_bottom_link_1_text',
                'label' => '左カード・テキスト',
                'name' => 'school_about_bottom_link_1_text',
                'type' => 'text',
            ),
            array(
                'key' => 'field_school_about_bottom_link_1_url',
                'label' => '左カード・リンクURL',
                'name' => 'school_about_bottom_link_1_url',
                'type' => 'url',
            ),
            array(
                'key' => 'field_school_about_bottom_link_1_new_tab',
                'label' => '左カード・新しいタブで開く',
                'name' => 'school_about_bottom_link_1_new_tab',
                'type' => 'true_false',
                'default_value' => 0,
                'ui' => 1,
            ),
            array(
                'key' => 'field_school_about_bottom_link_2_image',
                'label' => '右カード・画像',
                'name' => 'school_about_bottom_link_2_image',
                'type' => 'image',
                'return_format' => 'array',
                'preview_size' => 'medium',
            ),
            array(
                'key' => 'field_school_about_bottom_link_2_text',
                'label' => '右カード・テキスト',
                'name' => 'school_about_bottom_link_2_text',
                'type' => 'text',
            ),
            array(
                'key' => 'field_school_about_bottom_link_2_url',
                'label' => '右カード・リンクURL',
                'name' => 'school_about_bottom_link_2_url',
                'type' => 'url',
            ),
            array(
                'key' => 'field_school_about_bottom_link_2_new_tab',
                'label' => '右カード・新しいタブで開く',
                'name' => 'school_about_bottom_link_2_new_tab',
                'type' => 'true_false',
                'default_value' => 0,
                'ui' => 1,
            ),
            array(
                'key' => 'field_school_about_intro_tab',
                'label' => '2カラム紹介（/school/about 用）',
                'name' => '',
                'type' => 'tab',
                'placement' => 'top',
            ),
            array(
                'key' => 'field_school_about_intro_note',
                'label' => '',
                'name' => '',
                'type' => 'message',
                'message' => '左に本文、右に画像＋キャプションを表示します。本文は改行で段落分けできます。',
                'new_lines' => 'wpautop',
            ),
            array(
                'key' => 'field_school_about_intro_text',
                'label' => '左カラム本文',
                'name' => 'school_about_intro_text',
                'type' => 'textarea',
                'rows' => 12,
                // 改行はDBに素の改行で保存（テーマ側で段落化）。br 変換だと段落空けが崩れやすい
                'new_lines' => '',
            ),
            array(
                'key' => 'field_school_about_intro_image',
                'label' => '右カラム画像',
                'name' => 'school_about_intro_image',
                'type' => 'image',
                'return_format' => 'array',
                'preview_size' => 'medium',
            ),
            array(
                'key' => 'field_school_about_intro_caption',
                'label' => '右カラムキャプション',
                'name' => 'school_about_intro_caption',
                'type' => 'textarea',
                'rows' => 3,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'page',
                    'operator' => '==',
                    'value' => (string) $about_id,
                ),
            ),
        ),
        'position' => 'normal',
        'style' => 'default',
    ));
});

/**
 * CPT「school_instructor」から一覧用配列を生成
 *
 * @return array<int, array{name:string,url:string,img_url:string,img_alt:string,new_tab:bool}>
 */
function school_about_instructors_get_items_from_cpt()
{
    $placeholder = get_theme_file_uri('assets/images/school/voice-nonImage.jpg');
    $q = new WP_Query(array(
        'post_type'           => 'school_instructor',
        'post_status'         => 'publish',
        'posts_per_page'      => -1,
        'orderby'             => 'date',
        'order'               => 'ASC',
        'ignore_sticky_posts' => true,
        'no_found_rows'       => true,
    ));

    $out = array();
    if (!$q->have_posts()) {
        wp_reset_postdata();
        return $out;
    }

    while ($q->have_posts()) {
        $q->the_post();
        $pid   = get_the_ID();
        $title = get_the_title();
        $name  = is_string($title) ? trim($title) : '';

        $url = '';
        $new_tab = false;
        if (function_exists('get_field')) {
            $link = get_field('school_instructor_link', $pid);
            $url  = is_string($link) ? trim($link) : '';
            $nt = get_field('school_instructor_link_new_tab', $pid);
            $new_tab = ($nt === true || $nt === 1 || $nt === '1');
        }

        $img_url = get_the_post_thumbnail_url($pid, 'large');
        $img_url = is_string($img_url) ? $img_url : '';
        if ($img_url === '') {
            $img_url = $placeholder;
        }
        $img_alt = $name;
        $thumb_id = get_post_thumbnail_id($pid);
        if ($thumb_id) {
            $alt = get_post_meta($thumb_id, '_wp_attachment_image_alt', true);
            if (is_string($alt) && $alt !== '') {
                $img_alt = $alt;
            }
        }

        if ($name === '' && $img_url === '' && $url === '') {
            continue;
        }

        $out[] = array(
            'name'    => $name,
            'url'     => $url,
            'img_url' => $img_url,
            'img_alt' => $img_alt !== '' ? $img_alt : $name,
            'new_tab' => $new_tab,
        );
    }
    wp_reset_postdata();

    return $out;
}

/**
 * 旧：固定ページに直書きした5枠ACF（後方互換）
 *
 * @param int $post_id 固定ページ school/about の ID
 * @return array<int, array{name:string,url:string,img_url:string,img_alt:string,new_tab:bool}>
 */
function school_about_instructors_get_items_legacy_page($post_id)
{
    $post_id = (int) $post_id;
    if ($post_id < 1 || !function_exists('get_field')) {
        return array();
    }

    $placeholder = get_theme_file_uri('assets/images/school/voice-nonImage.jpg');
    $out = array();
    for ($i = 1; $i <= 5; $i++) {
        $img = get_field('school_about_inst_' . $i . '_photo', $post_id);
        $name = get_field('school_about_inst_' . $i . '_name', $post_id);
        $url  = get_field('school_about_inst_' . $i . '_url', $post_id);

        $name = is_string($name) ? trim($name) : '';
        $url  = is_string($url) ? trim($url) : '';
        $img_url = '';
        $img_alt = $name;
        if (is_array($img) && !empty($img['url'])) {
            $img_url = (string) $img['url'];
            if (!empty($img['alt'])) {
                $img_alt = (string) $img['alt'];
            }
        } elseif (is_numeric($img)) {
            $img_url = (string) wp_get_attachment_image_url((int) $img, 'large');
        }
        if ($img_url === '') {
            $img_url = $placeholder;
        }

        if ($name === '' && $img_url === '' && $url === '') {
            continue;
        }

        $out[] = array(
            'name'    => $name,
            'url'     => $url,
            'img_url' => $img_url,
            'img_alt' => $img_alt !== '' ? $img_alt : $name,
            'new_tab' => false,
        );
    }

    return $out;
}

/**
 * @param int $post_id 固定ページ school/about の ID（旧データフォールバック用）
 * @return array<int, array{name:string,url:string,img_url:string,img_alt:string,new_tab:bool}>
 */
function school_about_instructors_get_items($post_id)
{
    $from_cpt = school_about_instructors_get_items_from_cpt();
    if (!empty($from_cpt)) {
        return $from_cpt;
    }

    return school_about_instructors_get_items_legacy_page($post_id);
}

add_shortcode('school_instructors', 'school_instructors_shortcode');

function school_instructors_shortcode($atts)
{
    $atts = shortcode_atts(array(
        'id' => '',
    ), $atts, 'school_instructors');

    $post_id = (int) $atts['id'];
    if ($post_id < 1) {
        $post_id = function_exists('school_section_get_about_page_id') ? school_section_get_about_page_id() : 0;
    }
    if ($post_id < 1) {
        return '';
    }

    $title = '';
    if (function_exists('get_field')) {
        $title = get_field('school_about_instructors_title', $post_id);
    }
    if (!is_string($title) || trim($title) === '') {
        $title = '講師紹介';
    } else {
        $title = trim($title);
    }

    $items = school_about_instructors_get_items($post_id);
    $items = apply_filters('school_about_instructors_items', $items, $post_id);
    if (empty($items)) {
        return '';
    }

    ob_start();
    get_template_part('parts/project/p-school-about-instructors', null, array(
        'school_about_instructors_title' => $title,
        'school_about_instructors_items'   => $items,
    ));
    return (string) ob_get_clean();
}

/**
 * /school/about 下部導線（2カード）データ
 *
 * @param int $post_id
 * @return array<int, array{img_url:string,img_alt:string,text:string,url:string,new_tab:bool}>
 */
function school_about_bottom_links_get_items($post_id)
{
    $post_id = (int) $post_id;
    if ($post_id < 1 || !function_exists('get_field')) {
        return array();
    }

    $items = array();
    for ($i = 1; $i <= 2; $i++) {
        $img = get_field('school_about_bottom_link_' . $i . '_image', $post_id);
        $text = get_field('school_about_bottom_link_' . $i . '_text', $post_id);
        $url = get_field('school_about_bottom_link_' . $i . '_url', $post_id);
        $new_tab = get_field('school_about_bottom_link_' . $i . '_new_tab', $post_id);

        $img_url = '';
        $img_alt = '';
        if (is_array($img) && !empty($img['url'])) {
            $img_url = (string) $img['url'];
            $img_alt = !empty($img['alt']) ? (string) $img['alt'] : '';
        } elseif (is_numeric($img)) {
            $img_url = (string) wp_get_attachment_image_url((int) $img, 'large');
        }

        $text = is_string($text) ? trim($text) : '';
        $url = is_string($url) ? trim($url) : '';
        $is_new_tab = ($new_tab === true || $new_tab === 1 || $new_tab === '1');

        if ($img_url === '' && $text === '' && $url === '') {
            continue;
        }

        $items[] = array(
            'img_url' => $img_url,
            'img_alt' => $img_alt !== '' ? $img_alt : $text,
            'text' => $text,
            'url' => $url,
            'new_tab' => $is_new_tab,
        );
    }

    return $items;
}

/**
 * 左カラム本文。空行（Enter 2 回）で段落、段落内の改行は 1 行分の <br>。
 * 旧設定で <br> 保存のデータも正規化する。
 *
 * @param string $raw
 * @return string
 */
function school_about_intro_format_text($raw)
{
    $raw = (string) $raw;
    if ($raw === '') {
        return '';
    }

    $raw = preg_replace('/<br\s*\/?>\s*<br\s*\/?>/i', "\n\n", $raw);
    $raw = preg_replace('/<br\s*\/?>/i', "\n", $raw);
    $raw = wp_strip_all_tags($raw);
    $raw = str_replace(array("\r\n", "\r"), "\n", $raw);
    $raw = preg_replace("/\n{3,}/", "\n\n", $raw);
    $raw = trim($raw);

    if ($raw === '') {
        return '';
    }

    $blocks = preg_split('/\n\s*\n/', $raw);
    if (!is_array($blocks)) {
        return '';
    }

    $out = '';
    foreach ($blocks as $block) {
        $block = trim($block);
        if ($block === '') {
            continue;
        }
        $inner = nl2br(esc_html($block), false);
        $out .= '<p>' . $inner . '</p>';
    }

    return $out;
}

/**
 * /school/about 2カラム紹介データ
 *
 * @param int $post_id
 * @return array{text:string,img_url:string,img_alt:string,caption:string}
 */
function school_about_intro_get_data($post_id)
{
    $post_id = (int) $post_id;
    if ($post_id < 1 || !function_exists('get_field')) {
        return array(
            'text' => '',
            'img_url' => '',
            'img_alt' => '',
            'caption' => '',
        );
    }

    $text = get_field('school_about_intro_text', $post_id);
    $img = get_field('school_about_intro_image', $post_id);
    $caption = get_field('school_about_intro_caption', $post_id);

    $img_url = '';
    $img_alt = '';
    if (is_array($img) && !empty($img['url'])) {
        $img_url = (string) $img['url'];
        $img_alt = !empty($img['alt']) ? (string) $img['alt'] : '';
    } elseif (is_numeric($img)) {
        $img_url = (string) wp_get_attachment_image_url((int) $img, 'large');
    }

    return array(
        'text' => is_string($text) ? trim($text) : '',
        'img_url' => $img_url,
        'img_alt' => $img_alt,
        'caption' => is_string($caption) ? trim($caption) : '',
    );
}

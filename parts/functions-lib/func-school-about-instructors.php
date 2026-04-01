<?php
/**
 * スクール紹介（/school/about/）専用：講師紹介グリッド
 * - ACF で画像・名前・リンクを最大5名まで登録（無料版 ACF 向けに固定枠）
 * - 本文に [school_instructors] を挿入すると UI を出力（データは常に当該固定ページから取得）
 */

add_action('acf/init', function () {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    $about_id = function_exists('school_section_get_about_page_id') ? school_section_get_about_page_id() : 0;
    if (!$about_id) {
        return;
    }

    $fields = array(
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
            'message' => '本文の表示したい位置にショートコード <code>[school_instructors]</code> を1行で入力すると、ここで設定した内容が反映されます。',
            'new_lines' => '',
        ),
        array(
            'key' => 'field_school_about_instructors_title',
            'label' => '見出しテキスト',
            'name' => 'school_about_instructors_title',
            'type' => 'text',
            'default_value' => '講師紹介',
            'placeholder' => '講師紹介',
        ),
    );

    for ($i = 1; $i <= 5; $i++) {
        $fields[] = array(
            'key' => 'field_school_about_inst_' . $i . '_photo',
            'label' => '講師' . $i . '・写真',
            'name' => 'school_about_inst_' . $i . '_photo',
            'type' => 'image',
            'return_format' => 'array',
            'preview_size' => 'medium',
        );
        $fields[] = array(
            'key' => 'field_school_about_inst_' . $i . '_name',
            'label' => '講師' . $i . '・表示名（ボタン文言）',
            'name' => 'school_about_inst_' . $i . '_name',
            'type' => 'text',
        );
        $fields[] = array(
            'key' => 'field_school_about_inst_' . $i . '_url',
            'label' => '講師' . $i . '・リンク先 URL',
            'name' => 'school_about_inst_' . $i . '_url',
            'type' => 'url',
            'instructions' => '未入力のときはボタンを押せません（見た目のみ）。',
        );
    }

    acf_add_local_field_group(array(
        'key' => 'group_school_about_instructors',
        'title' => 'スクール紹介・講師一覧',
        'fields' => $fields,
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
 * @param int $post_id 固定ページ school/about の ID
 * @return array<int, array{name:string,url:string,img_url:string,img_alt:string}>
 */
function school_about_instructors_get_items($post_id)
{
    $post_id = (int) $post_id;
    if ($post_id < 1 || !function_exists('get_field')) {
        return array();
    }

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

        if ($name === '' && $img_url === '' && $url === '') {
            continue;
        }

        $out[] = array(
            'name'    => $name,
            'url'     => $url,
            'img_url' => $img_url,
            'img_alt' => $img_alt !== '' ? $img_alt : $name,
        );
    }

    return $out;
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

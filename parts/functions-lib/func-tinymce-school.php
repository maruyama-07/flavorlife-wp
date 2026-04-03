<?php
/**
 * スクール系のクラシックエディタ専用 TinyMCE 拡張
 * （対象画面のみ「スタイル」に茶色バナー等を追加）
 */

/**
 * スクール本文を編集する管理画面か
 *
 * @return bool
 */
function tool_school_tinymce_is_target_screen()
{
    if (!is_admin()) {
        return false;
    }

    global $pagenow;
    if (!in_array($pagenow, array('post.php', 'post-new.php'), true)) {
        return false;
    }

    $school_types = apply_filters(
        'tool_school_tinymce_post_types',
        array(
            'course_school',
            'voice_school',
            'news_school',
        )
    );

    $post_type = '';
    if ($pagenow === 'post-new.php') {
        $post_type = isset($_GET['post_type'])
            ? sanitize_key(wp_unslash((string) $_GET['post_type']))
            : 'post';
    } elseif ($pagenow === 'post.php') {
        $edit_id = 0;
        if (isset($_GET['post']) && ctype_digit((string) $_GET['post'])) {
            $edit_id = (int) $_GET['post'];
        }
        if ($edit_id > 0) {
            $post_type = (string) get_post_type($edit_id);
        } else {
            global $post;
            if ($post instanceof WP_Post) {
                $post_type = $post->post_type;
            }
        }
    }

    if ($post_type !== '' && in_array($post_type, $school_types, true)) {
        return true;
    }

    // /school ルート配下の固定ページ（テンプレートに依存しない）
    if ($post_type === 'page' && function_exists('is_school_section_page')) {
        if ($pagenow === 'post-new.php') {
            return is_school_section_page(0);
        }
        $page_id = 0;
        if (isset($_GET['post']) && ctype_digit((string) $_GET['post'])) {
            $page_id = (int) $_GET['post'];
        } else {
            global $post;
            if ($post instanceof WP_Post && $post->post_type === 'page') {
                $page_id = (int) $post->ID;
            }
        }

        return $page_id > 0 && is_school_section_page($page_id);
    }

    return false;
}

/**
 * 「スタイル」ドロップダウンに茶色バナー（div）を追加
 *
 * @param array $init_array
 * @return array
 */
function tool_school_tinymce_register_banner_style($init_array)
{
    if (!tool_school_tinymce_is_target_screen()) {
        return $init_array;
    }

    $school_formats = array(
        array(
            'title' => 'スクール',
            'items' => array(
                array(
                    'title'    => '茶色バナー（30px・中央・白文字）',
                    'block'    => 'div',
                    'classes'  => 'c-school-editor-banner',
                    'wrapper'  => true,
                ),
            ),
        ),
    );

    if (!empty($init_array['style_formats'])) {
        $existing = json_decode($init_array['style_formats'], true);
        if (is_array($existing)) {
            $init_array['style_formats'] = wp_json_encode(array_merge($existing, $school_formats));
        } else {
            $init_array['style_formats'] = wp_json_encode($school_formats);
        }
    } else {
        $init_array['style_formats'] = wp_json_encode($school_formats);
    }

    return $init_array;
}
add_filter('tiny_mce_before_init', 'tool_school_tinymce_register_banner_style', 25);

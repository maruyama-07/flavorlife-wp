<?php
/**
 * スクール系のクラシックエディタ専用 TinyMCE 拡張
 * （対象画面のみ「スタイル」に茶色バナー等を追加）
 */

/**
 * スクール系 TinyMCE 対象の投稿タイプ
 *
 * @return string[]
 */
function tool_school_tinymce_post_types_list()
{
    return apply_filters(
        'tool_school_tinymce_post_types',
        array(
            'course_school',
            'voice_school',
            'news_school',
            'school_instructor',
        )
    );
}

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

    $school_types = tool_school_tinymce_post_types_list();

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
                array(
                    'title'    => '全幅背景（既定 #F9F5F2）',
                    'block'    => 'div',
                    'classes'  => 'c-school-editor-full-bg',
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

/**
 * 全幅背景（.c-school-editor-full-bg）のエディタ iframe 内プレビュー
 *
 * @param array<string, mixed> $init_array
 * @return array<string, mixed>
 */
function tool_school_tinymce_full_bg_content_style($init_array)
{
    if (!tool_school_tinymce_is_target_screen()) {
        return $init_array;
    }

    $css = <<<'CSS'
.mce-content-body .c-school-editor-full-bg,.wp-block-freeform .c-school-editor-full-bg{display:block!important;box-sizing:border-box!important;width:100%!important;max-width:100%!important;margin:1rem 0!important;padding:2.5rem 0!important;background-color:#f9f5f2;}
.mce-content-body .c-school-editor-full-bg>*,.wp-block-freeform .c-school-editor-full-bg>*{max-width:1240px!important;margin-left:auto!important;margin-right:auto!important;padding:0 1rem!important;box-sizing:border-box!important;}
.mce-content-body .c-school-editor-full-bg>*+*,.wp-block-freeform .c-school-editor-full-bg>*+*{margin-top:1em!important;}
CSS;

    $css_one_line = trim(preg_replace('/ {2,}/', ' ', str_replace(array("\r\n", "\r", "\n"), ' ', $css)));
    $merged       = isset($init_array['content_style']) ? $init_array['content_style'] . ' ' . $css_one_line : $css_one_line;
    $init_array['content_style'] = trim(preg_replace('/ {2,}/', ' ', str_replace(array("\r\n", "\r", "\n"), ' ', $merged)));

    return $init_array;
}
add_filter('tiny_mce_before_init', 'tool_school_tinymce_full_bg_content_style', 97);

/**
 * スクール系ビジュアルエディタ：講座2カラム・本文CTAボタンなど、add_editor_style では負けやすい箇所を
 * content_style（iframe 内インライン）で補強する。
 * ※ CTA の背景は !important にしない（インライン background-color で上書き可能にする）。
 *
 * @param array<string, mixed> $init_array
 * @return array<string, mixed>
 */
function tool_school_tinymce_course_intro_content_style($init_array)
{
    if (!tool_school_tinymce_is_target_screen()) {
        return $init_array;
    }

    $css = <<<'CSS'
.c-school-course-intro{display:flex!important;flex-direction:row!important;flex-wrap:wrap!important;align-items:flex-start!important;gap:1.75rem!important;width:100%!important;max-width:100%!important;box-sizing:border-box!important;margin:2rem 0!important;}
.c-school-course-intro--media-right{flex-direction:row-reverse!important;}
.c-school-course-intro__media{flex:0 0 var(--c-school-course-intro-img,42%)!important;max-width:100%!important;box-sizing:border-box!important;}
.c-school-course-intro__media img{display:block!important;width:100%!important;height:auto!important;}
.c-school-course-intro__body{flex:1 1 0%!important;min-width:0!important;box-sizing:border-box!important;}
.c-school-course-intro__title{margin:0 0 1.25rem!important;padding-left:0.75rem!important;border-left:0.3125rem solid #d8d8d8!important;font-size:1.875rem!important;font-weight:600!important;line-height:1.35!important;color:#042c1b!important;}
.c-school-course-intro__text{margin-bottom:1.75rem!important;font-size:1rem!important;line-height:2.2!important;color:#2f2f2f!important;}
.c-school-course-intro__text p{margin:0 0 1em!important;line-height:2.2!important;}
.c-school-course-intro__text p:last-child{margin-bottom:0!important;}
.c-school-course-intro__body>.c-school-course-intro__title:first-child{margin-top:0!important;}
.c-school-course-intro__body>.c-school-course-intro__text:first-child{margin-top:0!important;}
.c-school-course-intro__text>p:first-child{margin-top:0!important;}
.c-school-course-intro__body>h1:first-child,.c-school-course-intro__body>h2:first-child,.c-school-course-intro__body>h3:first-child,.c-school-course-intro__body>h4:first-child,.c-school-course-intro__body>h5:first-child{margin-top:0!important;}
.c-school-course-intro__body>.c-school-course-intro__text:first-child h2,.c-school-course-intro__body>.c-school-course-intro__text:first-child h3,.c-school-course-intro__body>.c-school-course-intro__text:first-child h4,.c-school-course-intro__body>.c-school-course-intro__text:first-child h5{margin-top:0!important;}
.c-school-course-intro__cta{text-align:center!important;}
.c-school-course-intro__cta .l-header-school__cta{display:inline-flex!important;align-items:center!important;justify-content:center!important;min-width:10.75rem!important;min-height:2.5rem!important;border-radius:999px!important;font-size:0.875rem!important;letter-spacing:0.08em!important;color:#fff!important;text-decoration:none!important;background:#699!important;padding:0.875rem 1.875rem!important;box-sizing:border-box!important;}
@media screen and (max-width:767px){.c-school-course-intro{flex-direction:column!important;flex-wrap:wrap!important;}.c-school-course-intro--media-right{flex-direction:column-reverse!important;}.c-school-course-intro__media{flex:1 1 100%!important;width:100%!important;}.c-school-course-intro__body{flex:0 1 auto!important;width:100%!important;max-width:100%!important;}}
@media screen and (min-width:768px){.c-school-course-intro{flex-wrap:nowrap!important;align-items:stretch!important;}.c-school-course-intro__body{display:flex!important;flex-direction:column!important;min-height:0!important;}.c-school-course-intro__cta{margin-top:auto!important;}}
.p-school-content-cta-wrap{margin:1.5rem 0!important;box-sizing:border-box!important;}
.p-school-content-cta-wrap{text-align:left;}
.p-school-content-cta-wrap.aligncenter{text-align:center!important;}
.p-school-content-cta-wrap.alignright{text-align:right!important;}
.p-school-content-cta-wrap.alignleft{text-align:left!important;}
.p-school-content-cta-wrap .l-header-school__cta{display:inline-flex!important;align-items:center!important;justify-content:center!important;min-width:10.75rem!important;min-height:2.5rem!important;border-radius:999px!important;font-size:0.875rem!important;letter-spacing:0.08em!important;color:#fff!important;text-decoration:none!important;padding:0.875rem 1.875rem!important;box-sizing:border-box!important;background-color:#699;}
CSS;

    /*
     * WP_Editor::_parse_init() は content_style をダブルクォートで囲むだけでエスケープしない。
     * 改行が入ると tinyMCEPreInit の JS が壊れ、エディタが真っ白になる。
     */
    $css_one_line = trim(preg_replace('/ {2,}/', ' ', str_replace(array("\r\n", "\r", "\n"), ' ', $css)));
    $merged       = isset($init_array['content_style']) ? $init_array['content_style'] . ' ' . $css_one_line : $css_one_line;
    $init_array['content_style'] = trim(preg_replace('/ {2,}/', ' ', str_replace(array("\r\n", "\r", "\n"), ' ', $merged)));

    return $init_array;
}
add_filter('tiny_mce_before_init', 'tool_school_tinymce_course_intro_content_style', 99);

/**
 * 茶色矢印（.c-school-brown-arrow）のエディタ iframe 内プレビュー
 * ※ school-editor-style はスクール固定ページのみのため、CPT 編集時もここで補完する
 *
 * @param array<string, mixed> $init_array
 * @return array<string, mixed>
 */
function tool_school_tinymce_brown_arrow_content_style($init_array)
{
    if (!tool_school_tinymce_is_target_screen()) {
        return $init_array;
    }

    $css = <<<'CSS'
.c-school-brown-arrow{display:block!important;clear:both!important;text-align:center!important;margin:1.5rem auto!important;max-width:100%!important;box-sizing:border-box!important;}
.c-school-brown-arrow img{max-width:100%!important;height:auto!important;vertical-align:top!important;}
.c-school-brown-arrow__pc{display:inline-block!important;}
.c-school-brown-arrow__sp{display:none!important;}
@media screen and (max-width:767px){.c-school-brown-arrow{margin:1rem auto!important;}.c-school-brown-arrow__pc{display:none!important;}.c-school-brown-arrow__sp{display:inline-block!important;}}
CSS;

    $css_one_line = trim(preg_replace('/ {2,}/', ' ', str_replace(array("\r\n", "\r", "\n"), ' ', $css)));
    $merged       = isset($init_array['content_style']) ? $init_array['content_style'] . ' ' . $css_one_line : $css_one_line;
    $init_array['content_style'] = trim(preg_replace('/ {2,}/', ' ', str_replace(array("\r\n", "\r", "\n"), ' ', $merged)));

    return $init_array;
}
add_filter('tiny_mce_before_init', 'tool_school_tinymce_brown_arrow_content_style', 98);

/**
 * 矢印ラベル（.c-school-arrow-label）のエディタ iframe 内プレビュー
 *
 * @param array<string, mixed> $init_array
 * @return array<string, mixed>
 */
function tool_school_tinymce_arrow_label_content_style($init_array)
{
    if (! tool_school_tinymce_is_target_screen()) {
        return $init_array;
    }

    $css = <<<'CSS'
.mce-content-body .c-school-arrow-label,.wp-block-freeform .c-school-arrow-label{display:flex!important;align-items:center!important;justify-content:center!important;max-width:100%!important;margin:0 0 1.25rem!important;padding:0.875rem 2rem 0.875rem 1.5rem!important;vertical-align:middle!important;box-sizing:border-box!important;background-color:#f5f3ef!important;color:#000!important;font-size:1.75rem!important;font-weight:500!important;line-height:1.5!important;letter-spacing:0.02em!important;clip-path:polygon(0 0,calc(100% - 1.875rem) 0,100% 50%,calc(100% - 1.875rem) 100%,0 100%)!important;}
.mce-content-body .c-school-arrow-label__inner,.wp-block-freeform .c-school-arrow-label__inner{display:block!important;}
@media screen and (max-width:767px){.mce-content-body .c-school-arrow-label,.wp-block-freeform .c-school-arrow-label{padding:0.625rem 1.5rem 0.625rem 1.125rem!important;font-size:1rem!important;}}
CSS;

    $css_one_line = trim(preg_replace('/ {2,}/', ' ', str_replace(array("\r\n", "\r", "\n"), ' ', $css)));
    $merged       = isset($init_array['content_style']) ? $init_array['content_style'] . ' ' . $css_one_line : $css_one_line;
    $init_array['content_style'] = trim(preg_replace('/ {2,}/', ' ', str_replace(array("\r\n", "\r", "\n"), ' ', $merged)));

    return $init_array;
}
add_filter('tiny_mce_before_init', 'tool_school_tinymce_arrow_label_content_style', 97);

/**
 * 横線見出し（h2.c-school-heading）のビジュアルエディタ iframe 内プレビュー
 * （add_editor_style だけでは上書きされない環境向けに content_style で補強）
 *
 * @param array<string, mixed> $init_array
 * @return array<string, mixed>
 */
function tool_school_tinymce_school_heading_content_style($init_array)
{
    if (!tool_school_tinymce_is_target_screen()) {
        return $init_array;
    }

    $css = <<<'CSS'
.mce-content-body h2.c-school-heading,.wp-block-freeform h2.c-school-heading{box-sizing:border-box;margin:2.5rem 0 1.25rem;padding:0 0 0.75rem 1rem;font-size:2rem;font-weight:600;line-height:1.35;letter-spacing:0.02em;color:#042c1b;border-left:0.3125rem solid currentColor;border-bottom:1px solid currentColor;}
.mce-content-body h2.c-school-heading.c-school-heading--no-underline,.wp-block-freeform h2.c-school-heading.c-school-heading--no-underline{border-bottom:none;}
.mce-content-body h2.c-school-heading:first-child,.wp-block-freeform h2.c-school-heading:first-child{margin-top:0;}
@media screen and (max-width:767px){.mce-content-body h2.c-school-heading,.wp-block-freeform h2.c-school-heading{font-size:1.375rem;padding-left:0.75rem;padding-bottom:0.3125rem;margin-top:2rem;margin-bottom:1rem;border-left-width:0.375rem;}}
.mce-content-body h2.c-school-heading-bar,.wp-block-freeform h2.c-school-heading-bar{position:relative;box-sizing:border-box;margin:2.5rem 0 1.5rem;padding:0 0 1.25rem;font-size:2rem;font-weight:600;line-height:1.35;text-align:center;letter-spacing:0.04em;color:#000!important;}
.mce-content-body h2.c-school-heading-bar .c-school-heading-bar__rule,.wp-block-freeform h2.c-school-heading-bar .c-school-heading-bar__rule{position:absolute;left:50%;bottom:0;display:block;width:100px;height:4px;transform:translateX(-50%);background-color:var(--c-school-heading-bar-rule,#cdb030);}
.mce-content-body h2.c-school-heading-bar:not(:has(.c-school-heading-bar__rule))::after,.wp-block-freeform h2.c-school-heading-bar:not(:has(.c-school-heading-bar__rule))::after{content:'';position:absolute;left:50%;bottom:0;display:block;width:100px;height:4px;transform:translateX(-50%);background-color:var(--c-school-heading-bar-rule,#cdb030);}
.mce-content-body h2.c-school-heading-bar:has(.c-school-heading-bar__rule)::after,.wp-block-freeform h2.c-school-heading-bar:has(.c-school-heading-bar__rule)::after{content:none;}
.mce-content-body h2.c-school-heading-bar:first-child,.wp-block-freeform h2.c-school-heading-bar:first-child{margin-top:0;}
@media screen and (max-width:767px){.mce-content-body h2.c-school-heading-bar,.wp-block-freeform h2.c-school-heading-bar{font-size:1.375rem;margin-top:2rem;margin-bottom:1.5rem;padding-bottom:1rem;}.mce-content-body h2.c-school-heading-bar .c-school-heading-bar__rule,.wp-block-freeform h2.c-school-heading-bar .c-school-heading-bar__rule,.mce-content-body h2.c-school-heading-bar:not(:has(.c-school-heading-bar__rule))::after,.wp-block-freeform h2.c-school-heading-bar:not(:has(.c-school-heading-bar__rule))::after{width:5rem;height:3px;}}
CSS;

    $css_one_line = trim(preg_replace('/ {2,}/', ' ', str_replace(array("\r\n", "\r", "\n"), ' ', $css)));
    $merged       = isset($init_array['content_style']) ? $init_array['content_style'] . ' ' . $css_one_line : $css_one_line;
    $init_array['content_style'] = trim(preg_replace('/ {2,}/', ' ', str_replace(array("\r\n", "\r", "\n"), ' ', $merged)));

    return $init_array;
}
add_filter('tiny_mce_before_init', 'tool_school_tinymce_school_heading_content_style', 96);

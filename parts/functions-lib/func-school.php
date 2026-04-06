<?php
/**
 * School セクション（/school および子ページ）
 * ヘッダー・フッター・CSS・テンプレート切り替え
 */

/**
 * スクールのルート固定ページID。
 * 固定運用: スラッグ school のページを起点として扱う。
 *
 * @return int 0 = 未設定
 */
function school_section_get_root_page_id()
{
    $page = get_page_by_path('school');
    return $page ? (int) $page->ID : 0;
}

/**
 * 現在のクエリがスクールのルート（トップ）固定ページか
 */
function school_section_is_queried_root()
{
    if (!is_page()) {
        return false;
    }
    $root = school_section_get_root_page_id();
    return $root && (int) get_queried_object_id() === $root;
}

/**
 * スクール直下の「受講生の声」固定ページ（/school/voice/）か
 *
 * @return bool
 */
function school_section_is_voice_page()
{
    if (!is_page()) {
        return false;
    }
    $root = school_section_get_root_page_id();
    if (!$root) {
        return false;
    }
    $page = get_queried_object();
    if (!$page instanceof WP_Post || $page->post_name !== 'voice') {
        return false;
    }

    return (int) $page->post_parent === $root;
}

/**
 * スクール紹介固定ページ（/school/about/）の投稿 ID
 *
 * @return int 0 = 該当なし
 */
function school_section_get_about_page_id()
{
    $page = get_page_by_path('school/about');
    return $page ? (int) $page->ID : 0;
}

/**
 * 現在の表示が /school/about/ 相当の固定ページか
 *
 * @return bool
 */
function school_section_is_about_page()
{
    if (!is_page()) {
        return false;
    }
    $pid = school_section_get_about_page_id();
    return $pid && (int) get_queried_object_id() === $pid;
}

/**
 * 講座一覧固定ページ（/school/course/）の投稿 ID
 *
 * @return int 0 = 該当なし
 */
function school_section_get_course_page_id()
{
    $page = get_page_by_path('school/course');
    return $page ? (int) $page->ID : 0;
}

/**
 * 現在の表示が /school/course/ 相当の固定ページか
 *
 * @return bool
 */
function school_section_is_course_page()
{
    if (!is_page()) {
        return false;
    }
    $root = school_section_get_root_page_id();
    if (!$root) {
        return false;
    }
    $page = get_queried_object();
    if (!$page instanceof WP_Post || $page->post_name !== 'course') {
        return false;
    }

    return (int) $page->post_parent === $root;
}

/**
 * 申込・受講の流れ等（/school/order/）固定ページの投稿 ID
 *
 * @return int 0 = 該当なし
 */
function school_section_get_order_page_id()
{
    $page = get_page_by_path('school/order');
    return $page ? (int) $page->ID : 0;
}

/**
 * 現在の表示が /school/order/ 相当の固定ページか
 *
 * @return bool
 */
function school_section_is_order_page()
{
    if (! is_page()) {
        return false;
    }
    $root = school_section_get_root_page_id();
    if (! $root) {
        return false;
    }
    $page = get_queried_object();
    if (! $page instanceof WP_Post || $page->post_name !== 'order') {
        return false;
    }

    return (int) $page->post_parent === $root;
}

/**
 * 講座一覧のカテゴリーフィルター（URL: ?course_cat=スラッグ）
 *
 * @return string 空 = 全件
 */
function school_course_get_filter_category_slug()
{
    $slug = '';
    if (isset($_GET['course_cat'])) {
        $slug = sanitize_title(wp_unslash((string) $_GET['course_cat']));
    } elseif (get_query_var('course_cat')) {
        $slug = sanitize_title((string) get_query_var('course_cat'));
    }

    return $slug;
}

/**
 * スクールと同一ブランドのレイアウト（固定ページ school 配下 ＋ 受講生の声 CPT）
 *
 * @return bool
 */
function is_school_brand_layout()
{
    if (is_school_section()) {
        return true;
    }
    if (!empty($GLOBALS['school_voice_archive_fallback'])) {
        return true;
    }
    if (!empty($GLOBALS['school_section_404'])) {
        return true;
    }
    if (is_singular('voice_school')) {
        return true;
    }
    /** 講座 CPT（/school/course/スラッグ/）。固定ページ is_school_section には含まれない */
    if (is_singular('course_school')) {
        return true;
    }

    return false;
}

add_action('customize_register', function ($wp_customize) {
    $wp_customize->add_section('school_section_settings', array(
        'title' => 'スクールページ',
        'description' => 'スクールページ全体の設定です。',
        'priority' => 35,
    ));

    // バッジリンク（ヘッダー / モバイルナビで共通利用）
    $wp_customize->add_setting('school_aeaj_url', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control('school_aeaj_url', array(
        'label' => 'AEAJリンクURL',
        'section' => 'school_section_settings',
        'type' => 'url',
    ));

    $wp_customize->add_setting('school_jamha_url', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control('school_jamha_url', array(
        'label' => 'JAMHAリンクURL',
        'section' => 'school_section_settings',
        'type' => 'url',
    ));
});

add_action('init', function () {
    if (!get_page_by_path('school-settings')) {
        wp_insert_post(array(
            'post_title' => 'スクール設定',
            'post_name' => 'school-settings',
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_author' => 1,
        ));
    }
}, 5);

add_action('admin_menu', function () {
    $page = get_page_by_path('school-settings');
    if (!$page) {
        return;
    }

    add_theme_page(
        'スクール設定',
        'スクール設定',
        'edit_pages',
        'school-settings',
        function () use ($page) {
            wp_redirect(admin_url('post.php?post=' . $page->ID . '&action=edit'));
            exit;
        }
    );
});

add_filter('template_include', function ($template) {
    if (is_school_section()) {
        $school_template = get_template_directory() . '/template-school.php';
        if (file_exists($school_template)) {
            return $school_template;
        }
    }
    return $template;
}, 5);

add_filter('body_class', function ($classes) {
    if (is_school_brand_layout()) {
        $classes[] = 'school-section';
    }
    if (is_school_privacy_policy_page()) {
        $classes[] = 'school-privacy-policy';
    }
    return $classes;
});

/**
 * school ページまたはその子孫ページか
 *
 * @return bool
 */
function is_school_section()
{
    if (!is_page()) {
        return false;
    }
    $root_id = school_section_get_root_page_id();
    if (!$root_id) {
        return false;
    }
    $current_id = (int) get_queried_object_id();
    if ($current_id === $root_id) {
        return true;
    }
    $ancestors = get_post_ancestors($current_id);
    return in_array($root_id, array_map('intval', $ancestors), true);
}

/**
 * スクール配下「プライバシーポリシー」固定ページID（パス school/privacy-policy）
 *
 * @return int 0 = 該当なし
 */
function school_section_get_privacy_policy_page_id()
{
    $page = get_page_by_path('school/privacy-policy');
    return $page ? (int) $page->ID : 0;
}

/**
 * 現在の表示が /school/privacy-policy/ 相当の固定ページか
 */
function is_school_privacy_policy_page()
{
    if (!is_page()) {
        return false;
    }
    $pid = school_section_get_privacy_policy_page_id();
    return $pid && (int) get_queried_object_id() === $pid;
}

/**
 * 固定ページがスクール配下か（管理画面用）
 *
 * @param int $post_id
 * @return bool
 */
function is_school_section_page($post_id)
{
    $post_id = (int) $post_id;
    $root_id = school_section_get_root_page_id();
    if (!$root_id) {
        return false;
    }

    if (!$post_id) {
        $parent = isset($_GET['parent_id']) ? (int) $_GET['parent_id'] : 0;
        if ($parent) {
            return is_school_section_page($parent);
        }
        return false;
    }

    if (get_post_type($post_id) !== 'page') {
        return false;
    }

    if ($post_id === $root_id) {
        return true;
    }

    $ancestors = get_post_ancestors($post_id);
    return in_array($root_id, array_map('intval', $ancestors), true);
}

/**
 * 固定ページテンプレート選択肢: 「スクール用」は school 配下ページのときだけ表示。
 */
add_filter('theme_page_templates', function ($post_templates, $wp_theme, $post) {
    if (!isset($post_templates['template-school.php'])) {
        return $post_templates;
    }
    $post_id = ($post instanceof WP_Post) ? (int) $post->ID : 0;
    if (!is_school_section_page($post_id)) {
        unset($post_templates['template-school.php']);
    }
    return $post_templates;
}, 10, 3);

/**
 * スクール階層に属する固定ページID一覧（ルート「school」＋子孫）。管理画面一覧フィルタ用。
 *
 * 注意: get_pages() の post_status に 'any' は使えない（検証で false になり子が取れない）。
 * WP_Query は 'any' を解釈できるため、全ページ取得後に get_page_children で枝を切り出す。
 *
 * @return int[]
 */
function school_section_get_page_ids()
{
    static $cache = null;
    if ($cache !== null) {
        return $cache;
    }
    $root_id = school_section_get_root_page_id();
    if (!$root_id) {
        $cache = array();
        return $cache;
    }
    $ids = array($root_id);
    $query = new WP_Query(array(
        'post_type' => 'page',
        'post_status' => 'any',
        'posts_per_page' => -1,
        'orderby' => 'menu_order title',
        'order' => 'ASC',
        'no_found_rows' => true,
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false,
        'ignore_sticky_posts' => true,
    ));
    $pages = $query->posts;
    if (!empty($pages)) {
        $descendants = get_page_children($root_id, $pages);
        foreach ($descendants as $p) {
            $ids[] = (int) $p->ID;
        }
    }
    $cache = array_values(array_unique(array_map('intval', $ids)));
    return $cache;
}

/**
 * 外観 > メニュー「スクールメニュー」未割当時のデフォルト項目。
 *
 * @param string $context 'header' | 'mobile'（モバイルは先頭にホームを付与）
 * @return array<int, array{label:string, url:string}>
 */
function school_nav_menu_get_default_items($context = 'header')
{
    $common = array(
        array('label' => 'スクール紹介', 'url' => home_url('/school/')),
        array('label' => '講座のご案内', 'url' => home_url('/school/course/')),
        array('label' => 'はじめての方へ', 'url' => home_url('/school/first/')),
        array('label' => '講座スケジュール', 'url' => home_url('/school/schedule/')),
        array('label' => '受講生の声', 'url' => home_url('/school/voice/')),
        array('label' => 'アクセス', 'url' => home_url('/school/access/')),
    );
    if ($context === 'mobile') {
        array_unshift($common, array('label' => 'ホーム', 'url' => home_url('/')));
    }
    return $common;
}

/**
 * @param array<string, mixed> $args wp_nav_menu に渡した引数（配列化済み）
 */
function school_nav_menu_fallback_header($args)
{
    $menu_class = isset($args['menu_class']) && is_string($args['menu_class'])
        ? $args['menu_class']
        : 'l-header-school__nav-list';
    echo '<ul class="' . esc_attr($menu_class) . '">';
    foreach (school_nav_menu_get_default_items('header') as $item) {
        echo '<li><a href="' . esc_url($item['url']) . '">' . esc_html($item['label']) . '</a></li>';
    }
    echo '</ul>';
}

/**
 * @param array<string, mixed> $args wp_nav_menu に渡した引数（配列化済み）
 */
function school_nav_menu_fallback_mobile($args)
{
    $menu_class = isset($args['menu_class']) && is_string($args['menu_class'])
        ? $args['menu_class']
        : 'p-mobile-nav--school__list';
    echo '<ul class="' . esc_attr($menu_class) . '">';
    foreach (school_nav_menu_get_default_items('mobile') as $item) {
        echo '<li><a href="' . esc_url($item['url']) . '">' . esc_html($item['label']) . '</a></li>';
    }
    echo '</ul>';
}

/**
 * モバイルのスクールメニュー先頭に「ホーム」を常時追加。
 * 外観メニューに未設定でも（フォールバック時も）ホームを出す運用を維持する。
 *
 * @param string               $items
 * @param array<string, mixed> $args
 * @return string
 */
function school_mobile_nav_prepend_home_item($items, $args)
{
    if (!is_object($args)) {
        return $items;
    }
    if (
        (!isset($args->theme_location) || $args->theme_location !== 'school') ||
        (!isset($args->menu_id) || $args->menu_id !== 'school-nav-mobile')
    ) {
        return $items;
    }

    $home_url = esc_url(home_url('/school'));
    if (strpos((string) $items, 'href="' . "/school/" . '"') !== false) {
        return $items;
    }

    $home_item = '<li class="menu-item menu-item-type-custom menu-item-home"><a href="' . $home_url . '">ホーム</a></li>';
    return $home_item . $items;
}
add_filter('wp_nav_menu_items', 'school_mobile_nav_prepend_home_item', 10, 2);

/**
 * スクールセクション用フロントアセット。
 * func-enqueue-assets の my_script_init 先頭から呼ぶ（コーポレートの my-style は読まない）。
 */
function theme_enqueue_school_section_assets()
{
    global $slider_library;
    $slider_library = 'splide';

    wp_enqueue_style('NotoSansJP', '//fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&display=swap', array(), null);

    wp_deregister_script('jquery');
    wp_enqueue_script('jquery', '//code.jquery.com/jquery-3.6.1.min.js', array(), '3.6.1');

    wp_enqueue_script('slider-script', '//cdn.jsdelivr.net/npm/@splidejs/splide@4.0.7/dist/js/splide.min.js', array(), '4.0.7', true);
    wp_enqueue_style('school-splide', '//cdn.jsdelivr.net/npm/@splidejs/splide@4.0.7/dist/css/splide.min.css', array(), '4.0.7', 'all');

    wp_enqueue_script('gsap', get_template_directory_uri() . '/assets/js/gsap.min.js', array(), filemtime(get_theme_file_path('assets/js/gsap.min.js')), true);
    wp_enqueue_script('gsap-scrolltrigger', get_template_directory_uri() . '/assets/js/ScrollTrigger.min.js', array('gsap'), filemtime(get_theme_file_path('assets/js/ScrollTrigger.min.js')), true);
    wp_enqueue_script('my-script', get_template_directory_uri() . '/assets/js/script.js', array('slider-script'), filemtime(get_theme_file_path('assets/js/script.js')), true);
    wp_enqueue_script('anim-script', get_template_directory_uri() . '/assets/js/anim.js', array('slider-script'), filemtime(get_theme_file_path('assets/js/anim.js')), true);
    wp_enqueue_script('hamburger', get_template_directory_uri() . '/assets/js/hamburger.js', array(), filemtime(get_theme_file_path('assets/js/hamburger.js')), true);
    wp_enqueue_script(
        'page-hero-image',
        get_template_directory_uri() . '/assets/js/page-hero-image.js',
        array(),
        filemtime(get_theme_file_path('assets/js/page-hero-image.js')),
        true
    );
    wp_enqueue_script(
        'page-content-table-scroll',
        get_template_directory_uri() . '/assets/js/page-content-table-scroll.js',
        array(),
        filemtime(get_theme_file_path('assets/js/page-content-table-scroll.js')),
        true
    );
    wp_enqueue_script(
        'school-instructor-modal',
        get_template_directory_uri() . '/assets/js/school-instructor-modal.js',
        array(),
        file_exists(get_theme_file_path('assets/js/school-instructor-modal.js'))
            ? filemtime(get_theme_file_path('assets/js/school-instructor-modal.js'))
            : '1.0',
        true
    );
    $school_css_path = get_theme_file_path('assets/css/school-style.css');
    wp_enqueue_style(
        'school-style',
        get_template_directory_uri() . '/assets/css/school-style.css',
        array('school-splide'),
        file_exists($school_css_path) ? filemtime($school_css_path) : '1.0',
        'all'
    );

    wp_dequeue_style('wp-pagenavi');

    $slider_settings = array(
        'library' => $slider_library,
    );
    wp_add_inline_script('my-script', 'var sliderSettings = ' . json_encode($slider_settings) . ';', 'before');
}

/**
 * コーポレート用クラシックエディタボタン（c- / p- 系ショートコード用）。
 * スクール配下では school-style を読まないためプレビューが崩れる → ツールバーから除外。
 * スクール専用ボタンは別途 mce_buttons_2 / mce_external_plugins で追加予定。
 *
 * @return string[]
 */
function school_section_corporate_mce_button_slugs()
{
    return array(
        'image_grid',
        'step_flow',
        'product_card',
        'service_columns',
        'two_column_cards',
        'responsive_image',
    );
}

function school_section_is_page_edit_admin_screen()
{
    if (!is_admin() || !function_exists('get_current_screen')) {
        return false;
    }
    $screen = get_current_screen();
    if (!$screen || $screen->base !== 'post' || $screen->post_type !== 'page') {
        return false;
    }

    return true;
}

/**
 * 固定ページの編集画面で対象となる投稿 ID（新規は 0。親は is_school_section_page 側で parent_id を参照）
 */
function school_section_get_edited_page_id()
{
    if (isset($_GET['post']) && ctype_digit((string) $_GET['post'])) {
        return (int) $_GET['post'];
    }
    global $post;
    if ($post instanceof WP_Post && $post->post_type === 'page') {
        return (int) $post->ID;
    }

    return 0;
}

function school_section_should_hide_corporate_mce_tools()
{
    if (!is_admin() || !function_exists('get_current_screen')) {
        return false;
    }
    $screen = get_current_screen();
    if (!$screen || $screen->base !== 'post') {
        return false;
    }

    /** スクール講座・受講生の声・スクールニュース等：コーポレート用挿入ボタンを出さない */
    $school_types = function_exists('tool_school_tinymce_post_types_list')
        ? tool_school_tinymce_post_types_list()
        : array();
    if ($school_types !== array() && isset($screen->post_type) && in_array($screen->post_type, $school_types, true)) {
        return true;
    }

    if (!school_section_is_page_edit_admin_screen()) {
        return false;
    }

    return is_school_section_page(school_section_get_edited_page_id());
}

/**
 * @param string[] $buttons
 * @return string[]
 */
function school_section_mce_buttons_2_hide_corporate($buttons)
{
    if (!school_section_should_hide_corporate_mce_tools() || !is_array($buttons)) {
        return $buttons;
    }

    return array_values(array_diff($buttons, school_section_corporate_mce_button_slugs()));
}
add_filter('mce_buttons_2', 'school_section_mce_buttons_2_hide_corporate', 999);

/**
 * @param array<string, string> $plugins
 * @return array<string, string>
 */
function school_section_mce_external_plugins_hide_corporate($plugins)
{
    if (!school_section_should_hide_corporate_mce_tools() || !is_array($plugins)) {
        return $plugins;
    }
    foreach (school_section_corporate_mce_button_slugs() as $slug) {
        unset($plugins[$slug]);
    }

    return $plugins;
}
add_filter('mce_external_plugins', 'school_section_mce_external_plugins_hide_corporate', 999);

/**
 * スクール固定ページ編集時のみ TinyMCE プレビュー用スタイル（.c-school-heading）
 */
function school_section_add_editor_style_for_school_pages()
{
    global $typenow;

    $school_types = function_exists('tool_school_tinymce_post_types_list')
        ? tool_school_tinymce_post_types_list()
        : array();
    if ($school_types !== array() && in_array($typenow, $school_types, true)) {
        add_editor_style('assets/css/school-editor-style.css');

        return;
    }

    if ($typenow !== 'page') {
        return;
    }
    $post_id = school_section_get_edited_page_id();
    if ($post_id && is_school_section_page($post_id)) {
        add_editor_style('assets/css/school-editor-style.css');

        return;
    }
    if (!$post_id && is_school_section_page(0)) {
        add_editor_style('assets/css/school-editor-style.css');
    }
}
add_action('admin_init', 'school_section_add_editor_style_for_school_pages', 20);

/**
 * TinyMCE モーダル（ボタン挿入など）でフッターが入力欄に重なる問題の修正用 CSS（wp-admin 本体）
 */
function school_section_enqueue_admin_tinymce_dialog_fix($hook)
{
    if ($hook !== 'post.php' && $hook !== 'post-new.php') {
        return;
    }

    $school_tinymce = function_exists('tool_school_tinymce_is_target_screen') && tool_school_tinymce_is_target_screen();

    // 講座紹介2カラムなど：wp.media（親ウィンドウ）を TinyMCE から使う
    if ($school_tinymce) {
        wp_enqueue_media();
    }

    // スクール固定ページに加え、スクール系 CPT 編集時もモーダル用 CSS を読み込む
    if (!school_section_should_hide_corporate_mce_tools() && !$school_tinymce) {
        return;
    }

    $path = get_theme_file_path('assets/css/admin-school-tinymce.css');
    if (!is_readable($path)) {
        return;
    }
    wp_enqueue_style(
        'school-admin-tinymce',
        get_template_directory_uri() . '/assets/css/admin-school-tinymce.css',
        array(),
        (string) filemtime($path)
    );
}
add_action('admin_enqueue_scripts', 'school_section_enqueue_admin_tinymce_dialog_fix', 20);

/**
 * スクール固定ページ編集時：TinyMCE のフォントファミリーに base.scss と同系統の Google Fonts を出す。
 * （school-editor-style.css で @import 済み。本文は Sawarabi Mincho に近い既定を維持）
 *
 * @param array<string, mixed> $init_array
 * @return array<string, mixed>
 */
function school_section_tinymce_font_formats($init_array)
{
    if (!school_section_should_hide_corporate_mce_tools()) {
        return $init_array;
    }

    $formats = array(
        'デフォルト（継承）=inherit',
        'Sawarabi Mincho=\'Sawarabi Mincho\', serif',
        'Inter=Inter, sans-serif',
        'Noto Sans JP=\'Noto Sans JP\', sans-serif',
        'Baskervville=Baskervville, serif',
        'ABeeZee=ABeeZee, sans-serif',
        'Arial=arial, helvetica, sans-serif',
        'Georgia=georgia, serif',
    );

    $init_array['font_formats'] = implode('; ', $formats);

    return $init_array;
}
add_filter('tiny_mce_before_init', 'school_section_tinymce_font_formats', 25);

/**
 * スクール用 TinyMCE：横線+下線の見出し（h2.c-school-heading）
 *
 * @param string[] $buttons
 * @return string[]
 */
function school_section_mce_buttons_add_location_heading($buttons)
{
    if (!school_section_should_hide_corporate_mce_tools() || !is_array($buttons)) {
        return $buttons;
    }
    $buttons[] = 'school_location_heading';
    $buttons[] = 'school_location_heading_plain';
    $buttons[] = 'school_heading_bar';

    return $buttons;
}
add_filter('mce_buttons_2', 'school_section_mce_buttons_add_location_heading', 1000);

/**
 * @param array<string, string> $plugins
 * @return array<string, string>
 */
function school_section_mce_external_plugins_add_location_heading($plugins)
{
    if (!school_section_should_hide_corporate_mce_tools() || !is_array($plugins)) {
        return $plugins;
    }
    $plugins['school_location_heading'] = get_template_directory_uri() . '/assets/js/admin/school-location-heading.js';

    return $plugins;
}
add_filter('mce_external_plugins', 'school_section_mce_external_plugins_add_location_heading', 1000);

/**
 * TinyMCE 用：CTA のデフォルト href（ヘッダー CTA と同じお申し込み先）
 */
function school_section_print_tinymce_cta_default_href()
{
    if (!school_section_should_hide_corporate_mce_tools()) {
        return;
    }
    printf(
        '<script>window.schoolTinymceCtaHref=%s;</script>' . "\n",
        wp_json_encode(home_url('/school/contact/'))
    );
}
add_action('admin_head', 'school_section_print_tinymce_cta_default_href', 5);

/**
 * スクール用 TinyMCE：ヘッダーと同じピル型ボタン（.l-header-school__cta、初期は target=_blank）
 *
 * @param string[] $buttons
 * @return string[]
 */
function school_section_mce_buttons_add_cta($buttons)
{
    if (!school_section_should_hide_corporate_mce_tools() || !is_array($buttons)) {
        return $buttons;
    }
    $buttons[] = 'school_cta_button';

    return $buttons;
}
add_filter('mce_buttons_2', 'school_section_mce_buttons_add_cta', 1000);

/**
 * @param array<string, string> $plugins
 * @return array<string, string>
 */
function school_section_mce_external_plugins_add_cta($plugins)
{
    if (!school_section_should_hide_corporate_mce_tools() || !is_array($plugins)) {
        return $plugins;
    }
    $plugins['school_cta_button'] = get_template_directory_uri() . '/assets/js/admin/school-cta-button.js';

    return $plugins;
}
add_filter('mce_external_plugins', 'school_section_mce_external_plugins_add_cta', 1000);

/**
 * スクール用 TinyMCE：茶色バナー（div.c-school-editor-banner）
 *
 * @param string[] $buttons
 * @return string[]
 */
function school_section_mce_buttons_add_editor_banner($buttons)
{
    if (
        !function_exists('tool_school_tinymce_is_target_screen')
        || !tool_school_tinymce_is_target_screen()
        || !is_array($buttons)
    ) {
        return $buttons;
    }
    $buttons[] = 'school_editor_banner';

    return $buttons;
}
add_filter('mce_buttons_2', 'school_section_mce_buttons_add_editor_banner', 1000);

/**
 * @param array<string, string> $plugins
 * @return array<string, string>
 */
function school_section_mce_external_plugins_add_editor_banner($plugins)
{
    if (
        !function_exists('tool_school_tinymce_is_target_screen')
        || !tool_school_tinymce_is_target_screen()
        || !is_array($plugins)
    ) {
        return $plugins;
    }
    $plugins['school_editor_banner'] = get_template_directory_uri() . '/assets/js/admin/school-editor-banner.js';

    return $plugins;
}
add_filter('mce_external_plugins', 'school_section_mce_external_plugins_add_editor_banner', 1000);

/**
 * スクール用 TinyMCE：全幅背景ブロック（div.c-school-editor-full-bg）
 *
 * @param string[] $buttons
 * @return string[]
 */
function school_section_mce_buttons_add_editor_full_bg($buttons)
{
    if (
        !function_exists('tool_school_tinymce_is_target_screen')
        || !tool_school_tinymce_is_target_screen()
        || !is_array($buttons)
    ) {
        return $buttons;
    }
    $buttons[] = 'school_editor_full_bg';

    return $buttons;
}
add_filter('mce_buttons_2', 'school_section_mce_buttons_add_editor_full_bg', 1000);

/**
 * @param array<string, string> $plugins
 * @return array<string, string>
 */
function school_section_mce_external_plugins_add_editor_full_bg($plugins)
{
    if (
        !function_exists('tool_school_tinymce_is_target_screen')
        || !tool_school_tinymce_is_target_screen()
        || !is_array($plugins)
    ) {
        return $plugins;
    }
    $path = get_template_directory() . '/assets/js/admin/school-editor-full-bg.js';
    $plugins['school_editor_full_bg'] = get_template_directory_uri() . '/assets/js/admin/school-editor-full-bg.js'
        . (file_exists($path) ? '?v=' . filemtime($path) : '');

    return $plugins;
}
add_filter('mce_external_plugins', 'school_section_mce_external_plugins_add_editor_full_bg', 1000);

/**
 * TinyMCE「茶色矢印」用 SVG URL（スクール本文編集画面）
 */
function school_section_print_tinymce_brown_arrow_urls()
{
    if (!function_exists('tool_school_tinymce_is_target_screen') || !tool_school_tinymce_is_target_screen()) {
        return;
    }
    printf(
        '<script>window.schoolTinymceBrownArrow=%s;</script>' . "\n",
        wp_json_encode(
            array(
                'pc' => get_template_directory_uri() . '/assets/images/school/brown-arronw.svg',
                'sp' => get_template_directory_uri() . '/assets/images/school/brown-arronw-sp.svg',
            )
        )
    );
}
add_action('admin_head', 'school_section_print_tinymce_brown_arrow_urls', 5);

/**
 * スクール用 TinyMCE：茶色矢印（PC/SP で画像切替）
 *
 * @param string[] $buttons
 * @return string[]
 */
function school_section_mce_buttons_add_brown_arrow($buttons)
{
    if (
        !function_exists('tool_school_tinymce_is_target_screen')
        || !tool_school_tinymce_is_target_screen()
        || !is_array($buttons)
    ) {
        return $buttons;
    }
    $buttons[] = 'school_brown_arrow';

    return $buttons;
}
add_filter('mce_buttons_2', 'school_section_mce_buttons_add_brown_arrow', 1000);

/**
 * @param array<string, string> $plugins
 * @return array<string, string>
 */
function school_section_mce_external_plugins_add_brown_arrow($plugins)
{
    if (
        !function_exists('tool_school_tinymce_is_target_screen')
        || !tool_school_tinymce_is_target_screen()
        || !is_array($plugins)
    ) {
        return $plugins;
    }
    $plugins['school_brown_arrow'] = get_template_directory_uri() . '/assets/js/admin/school-brown-arrow.js?v=' . filemtime(get_template_directory() . '/assets/js/admin/school-brown-arrow.js');

    return $plugins;
}
add_filter('mce_external_plugins', 'school_section_mce_external_plugins_add_brown_arrow', 1000);

/**
 * スクール用 TinyMCE：右向き矢印ラベル（#F5F3EF）
 *
 * @param string[] $buttons
 * @return string[]
 */
function school_section_mce_buttons_add_arrow_label($buttons)
{
    if (
        ! function_exists('tool_school_tinymce_is_target_screen')
        || ! tool_school_tinymce_is_target_screen()
        || ! is_array($buttons)
    ) {
        return $buttons;
    }
    $buttons[] = 'school_arrow_label';

    return $buttons;
}
add_filter('mce_buttons_2', 'school_section_mce_buttons_add_arrow_label', 1000);

/**
 * @param array<string, string> $plugins
 * @return array<string, string>
 */
function school_section_mce_external_plugins_add_arrow_label($plugins)
{
    if (
        ! function_exists('tool_school_tinymce_is_target_screen')
        || ! tool_school_tinymce_is_target_screen()
        || ! is_array($plugins)
    ) {
        return $plugins;
    }
    $path = get_template_directory() . '/assets/js/admin/school-arrow-label.js';
    $plugins['school_arrow_label'] = get_template_directory_uri() . '/assets/js/admin/school-arrow-label.js'
        . (is_readable($path) ? '?v=' . (string) filemtime($path) : '');

    return $plugins;
}
add_filter('mce_external_plugins', 'school_section_mce_external_plugins_add_arrow_label', 1000);

/**
 * スクール用 TinyMCE：講座紹介2カラム（画像幅 % 可変）
 *
 * @param string[] $buttons
 * @return string[]
 */
function school_section_mce_buttons_add_course_intro($buttons)
{
    if (
        !function_exists('tool_school_tinymce_is_target_screen')
        || !tool_school_tinymce_is_target_screen()
        || !is_array($buttons)
    ) {
        return $buttons;
    }
    $buttons[] = 'school_course_intro';

    return $buttons;
}
add_filter('mce_buttons_2', 'school_section_mce_buttons_add_course_intro', 1000);

/**
 * @param array<string, string> $plugins
 * @return array<string, string>
 */
function school_section_mce_external_plugins_add_course_intro($plugins)
{
    if (
        !function_exists('tool_school_tinymce_is_target_screen')
        || !tool_school_tinymce_is_target_screen()
        || !is_array($plugins)
    ) {
        return $plugins;
    }
    $plugins['school_course_intro'] = get_template_directory_uri() . '/assets/js/admin/school-course-intro.js';

    return $plugins;
}
add_filter('mce_external_plugins', 'school_section_mce_external_plugins_add_course_intro', 1000);

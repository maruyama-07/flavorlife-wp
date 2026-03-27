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
    if (is_school_section()) {
        $classes[] = 'school-section';
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

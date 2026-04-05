<?php

/**
 * func-enqueue-assets
 * CSSとJavaScriptの読み込み
 *
 * @codex https://wpdocs.osdn.jp/%E3%83%8A%E3%83%93%E3%82%B2%E3%83%BC%E3%82%B7%E3%83%A7%E3%83%B3%E3%83%A1%E3%83%8B%E3%83%A5%E3%83%BC
 */
function my_script_init()
{
  global $slider_library;
  $slider_library = 'splide'; //splide,swiper,slickから選択する

  // スクールセクション・受講生の声 CPT は school-style.css 等（func-school の theme_enqueue_school_section_assets）
  if (function_exists('is_school_brand_layout') && is_school_brand_layout()) {
    if (function_exists('theme_enqueue_school_section_assets')) {
      theme_enqueue_school_section_assets();
    }
    return;
  }

  // フォントの設定
  wp_enqueue_style('NotoSansJP', '//fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&display=swap', array(), null);

  // WordPressがデフォルトで提供するjQueryは使用しない
  wp_deregister_script('jquery');
  wp_enqueue_script('jquery', '//code.jquery.com/jquery-3.6.1.min.js', array(), '3.6.1');


  // スライダーライブラリに基づいて適切なスクリプトとスタイルシートを読み込みます
  if ($slider_library === 'swiper') {
    // JavaScript
    wp_enqueue_script('slider-script', '//unpkg.com/swiper@8/swiper-bundle.min.js', array(), '', true);
    // CSS
    wp_enqueue_style('slider-style', '//unpkg.com/swiper@8/swiper-bundle.min.css', array(), '', 'all');

  } elseif ($slider_library === 'slick') {
    // JavaScript
    wp_enqueue_script('slider-script', '//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js', array('jquery'), '1.8.1', true);
    // CSS
    wp_enqueue_style('slider-style', '//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css', array(), '1.8.1', 'all');
    wp_enqueue_style('slider-theme-style', '//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css', array(), '1.8.1', 'all');
  } elseif ($slider_library === 'splide') {
    // JavaScript
    wp_enqueue_script('slider-script', '//cdn.jsdelivr.net/npm/@splidejs/splide@4.0.7/dist/js/splide.min.js', array(), '4.0.7', true);
    // CSS
    wp_enqueue_style('slider-style', '//cdn.jsdelivr.net/npm/@splidejs/splide@4.0.7/dist/css/splide.min.css', array(), '4.0.7', 'all');
  }

  // GSAP
  wp_enqueue_script('gsap', get_template_directory_uri() . '/assets/js/gsap.min.js', array(), filemtime(get_theme_file_path('assets/js/gsap.min.js')), true);
  wp_enqueue_script('gsap-scrolltrigger', get_template_directory_uri() . '/assets/js/ScrollTrigger.min.js', array('gsap'), filemtime(get_theme_file_path('assets/js/ScrollTrigger.min.js')), true);
  // 基本JavaScript
  wp_enqueue_script('my-script', get_template_directory_uri() . '/assets/js/script.js', array('slider-script'), filemtime(get_theme_file_path('assets/js/script.js')), true);
  // アニメーションJavaScript
  wp_enqueue_script('anim-script', get_template_directory_uri() . '/assets/js/anim.js', array('slider-script'), filemtime(get_theme_file_path('assets/js/anim.js')), true);
  
  // ヘッダースクロール制御JavaScript（TOPページのみ）
  if (is_front_page()) {
    wp_enqueue_script('header-scroll', get_template_directory_uri() . '/assets/js/header-scroll.js', array('jquery'), filemtime(get_theme_file_path('assets/js/header-scroll.js')), true);
  }

  // ハンバーガーメニュー
  wp_enqueue_script('hamburger', get_template_directory_uri() . '/assets/js/hamburger.js', array(), filemtime(get_theme_file_path('assets/js/hamburger.js')), true);
  // モバイルナビアコーディオン
  wp_enqueue_script('mobile-nav-accordion', get_template_directory_uri() . '/assets/js/mobile-nav-accordion.js', array(), filemtime(get_theme_file_path('assets/js/mobile-nav-accordion.js')), true);
  // ページヒーロー画像ロードアニメーション
  wp_enqueue_script('page-hero-image', get_template_directory_uri() . '/assets/js/page-hero-image.js', array(), filemtime(get_theme_file_path('assets/js/page-hero-image.js')), true);
  // Recruitページ ヒーロー動画の再生維持
  if (is_page_template('template-recruit.php')) {
    wp_enqueue_script('recruit-hero-video', get_template_directory_uri() . '/assets/js/recruit-hero-video.js', array(), filemtime(get_theme_file_path('assets/js/recruit-hero-video.js')), true);
  }
  // ページコンテンツスクロールフェードイン
  wp_enqueue_script('page-content-scroll', get_template_directory_uri() . '/assets/js/page-content-scroll.js', array(), filemtime(get_theme_file_path('assets/js/page-content-scroll.js')), true);
  wp_enqueue_script(
    'page-content-table-scroll',
    get_template_directory_uri() . '/assets/js/page-content-table-scroll.js',
    array(),
    filemtime(get_theme_file_path('assets/js/page-content-table-scroll.js')),
    true
  );
  
  // 基本CSS
  wp_enqueue_style('my-style', get_template_directory_uri() . '/assets/css/style.css', array('slider-style'), filemtime(get_theme_file_path('assets/css/style.css')), 'all');

  // WP-PageNaviプラグインのデフォルトCSSを無効化（テーマでカスタムスタイルを適用）
  wp_dequeue_style('wp-pagenavi');


  // スライダーを使用する場合、必要
  $slider_settings = array(
    'library' => $slider_library,
  );
  // JavaScriptで$slider_settingsを使用するための処理
  wp_add_inline_script('my-script', 'var sliderSettings = ' . json_encode($slider_settings) . ';', 'before');

}
add_action('wp_enqueue_scripts', 'my_script_init');
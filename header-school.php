<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="format-detection" content="telephone=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <meta property="og:title" content="" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="" />
    <meta property="og:site_name" content="" />
    <meta property="og:description" content="" />

    <!-- Google tag (gtag.js) — 計測IDを変えるときは下記2か所の G-RX2MF5REQ7 を書き換え -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-RX2MF5REQ7"></script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', 'G-RX2MF5REQ7');
    </script>

    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <?php wp_body_open(); ?>
    <?php get_template_part('parts/common/scroll-anim-fouc-head'); ?>
    <?php
    $school_aeaj_url = (string) get_theme_mod('school_aeaj_url', '');
    $school_jamha_url = (string) get_theme_mod('school_jamha_url', '');
    // 互換: 旧設定（school-settings ACF）が残っていればフォールバック
    if (($school_aeaj_url === '' || $school_jamha_url === '') && function_exists('get_field')) {
        $school_settings_page = get_page_by_path('school-settings');
        $school_settings_id = $school_settings_page ? (int) $school_settings_page->ID : 0;
        if ($school_settings_id) {
            if ($school_aeaj_url === '') {
                $school_aeaj_url = (string) get_field('school_aeaj_url', $school_settings_id);
            }
            if ($school_jamha_url === '') {
                $school_jamha_url = (string) get_field('school_jamha_url', $school_settings_id);
            }
        }
    }
    ?>
    <div id="page" class="l-site l-site--school">
        <header class="l-header-school" id="header">
            <div class="l-header-school__inner">
                <div class="l-header-school__top">
                    <div class="l-header-school__branding">
                        <p class="l-header-school__site-label">フレーバーライフ アロマテラピースクール</p>
                        <a class="l-header-school__logo" href="<?php echo esc_url(home_url('/school')); ?>">
                            <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/common/logo-green.svg'); ?>" alt="Flavor Life">
                        </a>
                    </div>
                    <div class="l-header-school__badges">
                        <?php if (!empty($school_aeaj_url)) : ?>
                        <a href="<?php echo esc_url($school_aeaj_url); ?>" target="_blank" rel="noopener noreferrer">
                        <?php endif; ?>
                        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/common/aeaj-logo.png'); ?>" alt="AEAJ認定スクール">
                        <?php if (!empty($school_aeaj_url)) : ?>
                        </a>
                        <?php endif; ?>
                        <?php if (!empty($school_jamha_url)) : ?>
                        <a href="<?php echo esc_url($school_jamha_url); ?>" target="_blank" rel="noopener noreferrer">
                        <?php endif; ?>
                        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/common/jamha.png'); ?>" alt="JAMHA認定校">
                        <?php if (!empty($school_jamha_url)) : ?>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="l-header-school__bottom">
                    <nav class="l-header-school__nav" aria-label="スクールヘッダーナビゲーション">
                        <?php
                        wp_nav_menu(array(
                            'theme_location' => 'school',
                            'container' => false,
                            'menu_id' => 'school-nav-header',
                            'menu_class' => 'l-header-school__nav-list',
                            'fallback_cb' => 'school_nav_menu_fallback_header',
                            'depth' => 1,
                        ));
                        ?>
                    </nav>
                    <a class="l-header-school__cta" href="<?php echo esc_url(home_url('/school/contact/')); ?>">お申し込みはこちら</a>
                </div>
            </div>
        </header>
        <button class="l-header-school__hamburger js-hamburger" type="button" aria-label="メニューを開く" aria-controls="mobile-nav" aria-expanded="false">
            <span class="l-header-school__hamburger-bar"></span>
            <span class="l-header-school__hamburger-bar"></span>
            <span class="l-header-school__hamburger-bar"></span>
        </button>
        <?php get_template_part('parts/common/mobile-nav-school-overlay'); ?>

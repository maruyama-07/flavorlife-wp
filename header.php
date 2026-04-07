<?php
if (function_exists('is_school_section') && is_school_section()) {
    locate_template('header-school.php', true);
    return;
}
?>
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
    <div id="page" class="l-site">
        <header class="l-header" id="header">
            <div class="l-header__inner">
                <div class="l-header__logo">
                    <a href="<?php echo home_url(); ?>">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/common/logo.svg" alt="logo">
                    </a>
                </div>
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'global',
                    'menu_class' => 'l-header__nav',
                    'container' => false,
                    'fallback_cb' => false,
                    'items_wrap' => '<ul class="%2$s">%3$s</ul>',
                    'walker' => new Custom_Walker_Nav_Menu()
                ));
                ?>
                <div class="l-header__hamburger js-hamburger">
                    <span class="l-header__hamburger-bar"></span>
                    <span class="l-header__hamburger-bar"></span>
                </div>
            </div>
        </header>
        <?php get_template_part('parts/common/mobile-nav-overlay'); ?>
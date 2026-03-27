<?php
if (function_exists('is_school_section') && is_school_section()) {
    locate_template('footer-school.php', true);
    return;
}
?>
<?php get_template_part('parts/p-top-sns'); ?>

<a href="#top" class="l-footer__top-link">
    <div></div>
</a>

<footer id="footer" class="l-footer">
    <?php
    $cta_settings_page = get_page_by_path('footer-cta-settings');
    $cta_items = array();
    if ($cta_settings_page && function_exists('get_field')) {
        for ($i = 1; $i <= 2; $i++) {
            $text = get_field('footer_cta_' . $i . '_text', $cta_settings_page->ID);
            $link = get_field('footer_cta_' . $i . '_link', $cta_settings_page->ID);
            $target = get_field('footer_cta_' . $i . '_target', $cta_settings_page->ID);
            if (!empty($text) && !empty($link)) {
                $cta_items[] = array(
                    'text' => $text,
                    'link' => $link,
                    'target' => $target,
                );
            }
        }
    }
    if (count($cta_items) > 0) :
    ?>
    <div class="l-footer__cta">
        <?php foreach ($cta_items as $item) : ?>
        <a href="<?php echo esc_url($item['link']); ?>" class="l-footer__cta-item"<?php echo $item['target'] ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>>
            <?php echo esc_html($item['text']); ?>
        </a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
    <div class="l-footer__inner">
        <div class="l-footer__main">
            <!-- メニュー（SP時は上部・アコーディオン、PC時はグリッド） -->
            <nav class="l-footer__nav-wrap">
                <div class="l-footer__menu p-mobile-nav__menu">
                    <div class="l-footer__columns p-mobile-nav__columns">
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'footer',
                        'menu_class' => 'l-footer__columns-inner',
                        'container' => false,
                        'fallback_cb' => false,
                        'items_wrap' => '%3$s',
                        'walker' => new Footer_Columns_Walker_Nav_Menu()
                    ));
                    ?>
                    </div>
                </div>
            </nav>
            <!-- SNSブロック -->
            <div class="l-footer__sns-wrap">
                <?php display_sns_icons(); ?>
            </div>
            <!-- 下部：ロゴ・Privacy policy・Copyright -->
            <div class="l-footer__bottom">
                <div class="l-footer__bottom-left">
                    <a href="<?php echo home_url(); ?>" class="l-footer__logo">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/common/logo-name.svg" alt="<?php bloginfo('name'); ?>">
                    </a>
                    <a href="<?php echo home_url('/privacy-policy'); ?>" class="l-footer__policy-item">Privacy policy</a>
                </div>
                <div class="l-footer__copyright">
                    <small>©Copyright <?php echo date('Y'); ?> FlavorLife Co.,Ltd.</small>
                </div>
            </div>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</div>

</body>

</html>
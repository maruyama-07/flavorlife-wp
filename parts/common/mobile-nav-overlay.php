<?php
/**
 * モバイルナビゲーションオーバーレイ
 * js-hamburger クリックで表示（グローバルメニュー／ContactカラムにNews+お問い合わせを統合）
 */
?>
<div class="p-mobile-nav" id="mobile-nav" aria-hidden="true">
    <div class="p-mobile-nav__inner">
        <nav class="p-mobile-nav__menu">
            <div class="p-mobile-nav__columns">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'global',
                    'menu_class' => 'p-mobile-nav__columns-inner',
                    'container' => false,
                    'fallback_cb' => false,
                    'items_wrap' => '%3$s',
                    'walker' => new Mobile_Walker_Nav_Menu()
                ));
                ?>
            </div>
        </nav>
        <?php
        $cta_settings_page = get_page_by_path('mobile-nav-cta-settings');
        $cta_items = array();
        if ($cta_settings_page && function_exists('get_field')) {
            for ($i = 1; $i <= 3; $i++) {
                $image = get_field('mobile_nav_cta_' . $i . '_image', $cta_settings_page->ID);
                $link = get_field('mobile_nav_cta_' . $i . '_link', $cta_settings_page->ID);
                $new_tab = (bool) get_field('mobile_nav_cta_' . $i . '_new_tab', $cta_settings_page->ID);
                if ($image && !empty($image['url']) && $link) {
                    $cta_items[] = array('link' => $link, 'image' => $image, 'new_tab' => $new_tab);
                }
            }
        }
        if (count($cta_items) > 0) :
        ?>
        <div class="p-mobile-nav__cta">
            <?php foreach ($cta_items as $item) : ?>
            <a href="<?php echo esc_url($item['link']); ?>" class="p-mobile-nav__cta-item"<?php echo !empty($item['new_tab']) ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>>
                <span class="p-mobile-nav__cta-image">
                    <img src="<?php echo esc_url($item['image']['url']); ?>" alt="">
                </span>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

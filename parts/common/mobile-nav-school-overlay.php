<?php
/**
 * スクール用モバイルナビオーバーレイ
 */
?>
<div class="p-mobile-nav p-mobile-nav--school" id="mobile-nav" aria-hidden="true">
    <div class="p-mobile-nav--school__inner">
        <div class="p-mobile-nav--school__cta">
            <a href="<?php echo esc_url(home_url('/school/entry/')); ?>" class="p-mobile-nav--school__cta-item">
                <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/school/document.png'); ?>" alt="">
                <span>お申し込み</span>
            </a>
            <a href="<?php echo esc_url(home_url('/school/contact/')); ?>" class="p-mobile-nav--school__cta-item">
                <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/school/mail.png'); ?>" alt="">
                <span>お問い合わせ</span>
            </a>
        </div>

        <nav class="p-mobile-nav--school__menu" aria-label="スクールモバイルナビゲーション">
            <?php
            wp_nav_menu(array(
                'theme_location' => 'school',
                'container' => false,
                'menu_id' => 'school-nav-mobile',
                'menu_class' => 'p-mobile-nav--school__list',
                'fallback_cb' => 'school_nav_menu_fallback_mobile',
                'depth' => 1,
            ));
            ?>
        </nav>

        <?php
        $school_nav_sns = function_exists('school_mobile_nav_sns_items') ? school_mobile_nav_sns_items() : array();
        $school_icon_base = get_template_directory_uri() . '/assets/images/school/';
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
        $school_nav_has_sns = false;
        foreach ($school_nav_sns as $sns_item) {
            if (!empty($sns_item['url'])) {
                $school_nav_has_sns = true;
                break;
            }
        }
        ?>
        <div class="p-mobile-nav--school__footer">
            <div class="p-mobile-nav--school__footer-panel">
                <div class="p-mobile-nav--school__footer-brand">
                    <div class="p-mobile-nav--school__footer-branding">
                        <p class="p-mobile-nav--school__footer-label">フレーバーライフ アロマテラピースクール</p>
                        <a href="<?php echo esc_url(home_url('/school')); ?>" class="p-mobile-nav--school__footer-logo">
                            <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/common/logo-green.svg'); ?>" alt="Flavor Life">
                        </a>
                    </div>
                    <div class="p-mobile-nav--school__footer-badges">
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
                <?php if ($school_nav_has_sns) : ?>
                <div class="p-mobile-nav--school__footer-sns">
                    <?php foreach ($school_nav_sns as $sns) : ?>
                        <?php if (empty($sns['url'])) {
                            continue;
                        } ?>
                    <a href="<?php echo esc_url($sns['url']); ?>" class="p-mobile-nav--school__footer-sns-link" target="_blank" rel="noopener noreferrer">
                        <img src="<?php echo esc_url($school_icon_base . $sns['file']); ?>" alt="<?php echo esc_attr($sns['label']); ?>">
                    </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            <div class="p-mobile-nav--school__footer-copy">
                <small>&copy; Flavorlife., Ltd. ALL Rights Reserved.</small>
            </div>
        </div>
    </div>
</div>

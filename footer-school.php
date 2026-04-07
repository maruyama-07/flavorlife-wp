<section class="l-footer-school-contact">
    <div class="l-footer-school-contact__overlay">
        <div class="l-footer-school-contact__inner">
            <h2 class="l-footer-school-contact__title">Contact</h2>
            <p class="l-footer-school-contact__subtitle">ご相談・お問い合わせ</p>
            <div class="l-footer-school-contact__items">
                <a class="l-footer-school-contact__item" href="<?php echo esc_url(home_url('/school/contact/')); ?>">
                    <span class="l-footer-school-contact__subitem">
                        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/school/receipt.png'); ?>" alt="">
                        <span>資料請求</span>
                    </span>
                    <span class="l-footer-school-contact__subitem">
                        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/school/document.png'); ?>" alt="">
                        <span>お申し込み</span>
                    </span>
                    <span class="l-footer-school-contact__subitem">
                        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/school/mail.png'); ?>" alt="">
                        <span>お問い合わせ</span>
                    </span>
                </a>
            </div>
        </div>
    </div>
</section>
<footer class="l-footer-school" id="footer">
    <div class="l-footer-school__inner">
        <div class="l-footer-school__main">
            <div class="l-footer-school__brand">
                <a href="<?php echo esc_url(home_url('/school')); ?>" class="l-footer-school__logo">
                    <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/common/logo-green.svg'); ?>" alt="Flavor Life">
                </a>
                <p class="l-footer-school__sub">フレーバーライフ アロマテラピースクール</p>
                <p class="l-footer-school__info">
                    〒185-0012 東京都国分寺市本町4-1-12 4F<br>
                    TEL：042-329-8565<br>
                    E-mail：school@flavorlife.co.jp
                </p>
                <?php
                $school_sns_items = function_exists('school_mobile_nav_sns_items') ? school_mobile_nav_sns_items() : array();
                $school_sns_icon_base = get_template_directory_uri() . '/assets/images/school/';
                $school_sns_has_any = false;
                foreach ($school_sns_items as $it) {
                    if (!empty($it['url'])) {
                        $school_sns_has_any = true;
                        break;
                    }
                }
                ?>
                <?php if ($school_sns_has_any) : ?>
                <div class="l-footer-school__sns">
                    <?php foreach ($school_sns_items as $sns) : ?>
                        <?php if (empty($sns['url'])) {
                            continue;
                        } ?>
                    <a href="<?php echo esc_url($sns['url']); ?>" class="l-footer-school__sns-link" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr($sns['label']); ?>">
                        <img src="<?php echo esc_url($school_sns_icon_base . $sns['file']); ?>" alt="<?php echo esc_attr($sns['label']); ?>">
                    </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            <div class="l-footer-school__links">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'school_footer',
                    'container'      => false,
                    'menu_class'     => '',
                    'depth'          => 2,
                    'fallback_cb'    => 'school_footer_nav_fallback_all',
                    'items_wrap'     => '%3$s',
                    'walker'         => new School_Footer_Nav_Walker(),
                ));
                ?>
            </div>
        </div>
    </div>
    <div class="l-footer-school__copyrightbar">
        <small>&copy; Flavorlife.,Ltd. ALL Rights Reserved.</small>
    </div>
</footer>

<?php wp_footer(); ?>
</div>

</body>

</html>

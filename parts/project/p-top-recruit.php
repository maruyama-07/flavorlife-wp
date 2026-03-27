<?php
/**
 * TOPページ - Recruitエリア（c-image-text）
 */
?>

<section class="c-image-text">
    <div class="c-image-text__container js-animate-content">
        <div class="c-image-text__image">
            <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/common/aroma-banner.jpg'); ?>" alt="About Aromatherapy">
        </div>
        <div class="c-image-text__content">
            <h2 class="c-image-text__title">About Aroma therapy</h2>
            <p class="c-image-text__subtitle">アロマや精油について</p>
            <h3 class="c-image-text__head">心と身体の健康に役立てる自然療法</h3>
            <p class="c-image-text__text">アロマテラピーとは、自然の木々や草花など、植物の香りによって、心と身体の健康に役立てる自然療法です。</p>
            <a href="<?php echo esc_url(home_url('/about-aroma')); ?>" class="c-custom-button">
                詳細はこちら
                <svg class="c-custom-button__arrow" width="12" height="18" viewBox="0 0 12 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M2 2L10 9L2 16" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </a>
        </div>
    </div>
</section>

<section class="c-image-text c-image-text--reversed">
    <div class="c-image-text__container js-animate-content">
        <div class="c-image-text__image">
            <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/common/recruit-banner.jpg'); ?>" alt="Recruit">
        </div>
        <div class="c-image-text__content">
            <h2 class="c-image-text__title">Recruit</h2>
            <p class="c-image-text__subtitle">求人情報</p>
            <h3 class="c-image-text__head">香りと暮らしに関心がある人へ</h3>
            <p class="c-image-text__text">
                香りはもちろん、<br>
                人の日常を支える仕事があります。<br>
                一緒に、やさしい価値をつくりませんか？
            </p>
            <a href="<?php echo esc_url(home_url('/recruit')); ?>" class="c-custom-button ">
                詳細はこちら
                <svg class="c-custom-button__arrow" width="12" height="18" viewBox="0 0 12 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M2 2L10 9L2 16" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </a>
        </div>
    </div>
</section>

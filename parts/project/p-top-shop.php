<?php
/**
 * TOPページ - Shopセクション
 */
?>

<section class="p-top-shop">
    <div class="p-top-shop__inner l-inner  js-animate-content">
        <!-- ヘッダー -->
        <div class="p-top-shop__header">
            <h2 class="p-top-shop__title">Shop</h2>
            <p class="c-head-sub">直営店</p>
        </div>

        <!-- キャッチコピー -->
        <!-- <p class="p-top-shop__catchcopy">新ブレンド精油が睡眠前後の感覚にポジティブな変化</p> -->

        <!-- コンテンツエリア -->
        <div class="p-top-shop__content">
            <!-- 左側：店舗リスト -->
            <div class="p-top-shop__stores">
                <!-- TOKYO店 -->
                <div class="p-top-shop__store-item">
                    <a href="<?php echo esc_url(home_url('/shop#tokyo')); ?>" class="p-top-shop__store-link">
                        <div class="p-top-shop__store-image">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/common/shop01.webp"
                                alt="TOKYO店">
                        </div>
                        <div class="p-top-shop__store-info">
                            <h3 class="p-top-shop__store-name">TOKYO</h3>
                            <p class="p-top-shop__store-location">国分寺</p>
                        </div>
                        <span class="c-btn-arrow">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <circle cx="12" cy="12" r="11.5" stroke="currentColor" fill="#0f2b0f"></circle>
                                <path d="M10 8L14 12L10 16" stroke="#fff" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round"></path>
                            </svg>
                        </span>
                    </a>
                </div>

                <!-- OSAKA店 -->
                <div class="p-top-shop__store-item">
                    <a href="<?php echo esc_url(home_url('/shop#osaka')); ?>" class="p-top-shop__store-link">
                        <div class="p-top-shop__store-image">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/common/shop02.webp"
                                alt="OSAKA店">
                        </div>
                        <div class="p-top-shop__store-info">
                            <h3 class="p-top-shop__store-name">OSAKA</h3>
                            <p class="p-top-shop__store-location">大丸心斎橋店</p>
                        </div>

                        <span class="c-btn-arrow">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <circle cx="12" cy="12" r="11.5" stroke="currentColor" fill="#0f2b0f"></circle>
                                <path d="M10 8L14 12L10 16" stroke="#fff" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round"></path>
                            </svg>
                        </span>
                    </a>
                </div>
            </div>

            <!-- 右側：オンラインショップ -->
            <div class="p-top-shop__online">
                <div class="p-top-shop__online-bg">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/common/shop-item.jpg"
                        alt="Online Shop">
                </div>
                <div class="p-top-shop__online-content">
                    <h3 class="p-top-shop__online-title">Online Shop</h3>
                    <a href="https://www.flavorlife.com/" target="_blank" class="c-custom-button">
                        ご購入はこちら
                        <svg class="c-custom-button__arrow" width="12" height="18" viewBox="0 0 12 18" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M2 2L10 9L2 16" stroke="currentColor" stroke-width="3" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
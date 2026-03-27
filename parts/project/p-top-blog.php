<?php
/**
 * TOPページ - Blogセクション
 */
?>

<section class="p-top-blog">
    <div class="p-top-blog__container  js-animate-content">
        <!-- 左側：人物画像 -->
        <div class="p-top-blog__image">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/common/top-blog.webp" alt="社長">
        </div>

        <!-- 右側：テキストコンテンツ（白背景ボックス） -->
        <div class="p-top-blog__content">
            <h2 class="p-top-blog__title">Blog</h2>
            <p class="p-top-blog__subtitle">社長ブログ</p>
            <p class="p-top-blog__text">
                アロマのこと、フランスのこと。<br>
                お仕事のこと、日常のこと。<br>
                代表取締役 奥津秀康の本音。
            </p>
            <a href="<?php echo esc_url(home_url('/blog')); ?>" class="c-custom-button">
                詳しく見る
                <svg class="c-custom-button__arrow" width="12" height="18" viewBox="0 0 12 18" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path d="M2 2L10 9L2 16" stroke="currentColor" stroke-width="3" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
            </a>
        </div>
    </div>
</section>
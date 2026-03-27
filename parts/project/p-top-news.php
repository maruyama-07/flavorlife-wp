<?php
/**
 * TOPページ - Newsセクション
 */

$args = array(
    'post_type' => 'post', // 'post'はNews
    'posts_per_page' => 2,
    'order' => 'DESC',
    'orderby' => 'date',
);

$news_query = new WP_Query($args);
?>

<section class="p-top-news">
    <div class="p-top-news__inner l-inner  js-animate-content">
        <!-- ヘッダー -->
        <div class="p-top-news__header">
            <div class="p-top-news__heading">
                <h2 class="p-top-news__title">News</h2>
                <p class="p-top-news__subtitle">お知らせ</p>
            </div>
            <a href="<?php echo get_permalink(get_option('page_for_posts')); ?>" class="p-top-news__more">More</a>
        </div>

        <!-- 記事リスト -->
        <?php if ($news_query->have_posts()) : ?>
        <ul class="p-top-news__list">
            <?php while ($news_query->have_posts()) : $news_query->the_post(); ?>
            <li class="p-top-news__item">
                <a href="<?php the_permalink(); ?>" class="p-top-news__link">
                    <!-- サムネイル -->
                    <div class="p-top-news__thumbnail">
                        <?php if (has_post_thumbnail()) : ?>
                        <?php the_post_thumbnail('thumbnail'); ?>
                        <?php else : ?>
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/common/no-image.svg"
                            alt="No Image">
                        <?php endif; ?>
                    </div>

                    <!-- コンテンツ -->
                    <div class="p-top-news__content">
                        <time class="p-top-news__date" datetime="<?php echo get_the_date('Y-m-d'); ?>">
                            <?php echo get_the_date('Y.m.d'); ?>
                        </time>
                        <h3 class="p-top-news__item-title">
                            <?php the_title(); ?>
                        </h3>
                    </div>

                    <!-- 矢印 -->
                    <span class="c-btn-arrow">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="12" cy="12" r="11.5" stroke="currentColor" fill="#0f2b0f" />
                            <path d="M10 8L14 12L10 16" stroke="#fff" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </span>
                </a>
            </li>
            <?php endwhile; ?>
        </ul>
        <?php else : ?>
        <p class="p-top-news__empty">お知らせはまだありません。</p>
        <?php endif; ?>

        <?php wp_reset_postdata(); ?>
    </div>
</section>
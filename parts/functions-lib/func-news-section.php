<?php
/**
 * ニュースセクションのショートコード
 * 使い方: [news_section count="2"]
 */

function news_section_shortcode($atts) {
    // デフォルト設定
    $args = shortcode_atts(array(
        'count' => 2, // 表示件数（デフォルト2件）
        'category' => '', // カテゴリースラッグで絞り込み（オプション）
    ), $atts);
    // WP_Queryの引数
    $query_args = array(
        'post_type' => 'post',
        'posts_per_page' => intval($args['count']),
        'orderby' => 'date',
        'order' => 'DESC',
    );

    // カテゴリー指定がある場合
    if (!empty($args['category'])) {
        $query_args['category_name'] = $args['category'];
    }

    $news_query = new WP_Query($query_args);

    // 出力開始
    ob_start();
    ?>
<section class="p-news-section">
    <div class="p-news-section__inner l-inner">
        <div class="p-news-section__header">
            <h2 class="p-news-section__title c-section-title">News</h2>
            <p class="p-news-section__subtitle">お知らせ</p>
            <a href="<?php echo get_permalink(get_option('page_for_posts')); ?>" class="p-news-section__more">More</a>
        </div>

        <?php if ($news_query->have_posts()) : ?>
        <ul class="p-news-section__list">
            <?php while ($news_query->have_posts()) : $news_query->the_post(); ?>
            <li class="p-news-section__item">
                <a href="<?php the_permalink(); ?>" class="p-news-section__link">
                    <div class="p-news-section__thumbnail">
                        <?php if (has_post_thumbnail()) : ?>
                        <?php the_post_thumbnail('medium'); ?>
                        <?php else : ?>
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/common/no-image.svg"
                            alt="No Image">
                        <?php endif; ?>
                    </div>
                    <div class="p-news-section__content">
                        <time class="p-news-section__date" datetime="<?php echo get_the_date('Y-m-d'); ?>">
                            <?php echo get_the_date('Y.m.d'); ?>
                        </time>
                        <h3 class="p-news-section__item-title">
                            <?php the_title(); ?>
                        </h3>
                        <span class="p-news-section__arrow">→</span>
                    </div>
                </a>
            </li>
            <?php endwhile; ?>
        </ul>
        <?php else : ?>
        <p class="p-news-section__empty">お知らせはまだありません。</p>
        <?php endif; ?>

        <?php wp_reset_postdata(); ?>
    </div>
</section>
<?php
    return ob_get_clean();
}
add_shortcode('news_section', 'news_section_shortcode');
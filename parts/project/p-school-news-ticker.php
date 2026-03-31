<?php
/**
 * スクールトップ：ニュースティッカー（news_school）
 */
$news_query = new WP_Query(array(
    'post_type' => 'news_school',
    'posts_per_page' => 1,
    'post_status' => 'publish',
    'orderby' => 'date',
    'order' => 'DESC',
));
if (!$news_query->have_posts()) {
    wp_reset_postdata();
    return;
}
?>
<div class="p-school-news-ticker">
    <div class="p-school-news-ticker__inner l-inner">
        <p class="p-school-news-ticker__label">News</p>
        <div class="p-school-news-ticker__track" role="marquee">
            <ul class="p-school-news-ticker__list">
                <?php
                while ($news_query->have_posts()) :
                    $news_query->the_post();
                    ?>
                <li class="p-school-news-ticker__item">
                    <time class="p-school-news-ticker__date" datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date('Y.m.d')); ?></time>
                    <a class="p-school-news-ticker__title" href="<?php echo esc_url(get_permalink()); ?>"><?php echo esc_html(get_the_title()); ?></a>
                </li>
                <?php
                endwhile;
                wp_reset_postdata();
                ?>
            </ul>
        </div>
        <span class="p-school-news-ticker__decoration" aria-hidden="true">&gt;</span>
    </div>
</div>

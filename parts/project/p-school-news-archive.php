<?php
/**
 * スクールお知らせ 一覧（/school/news/ 固定ページ or 404 フォールバック）
 */
$paged = 1;
if (!empty($GLOBALS['school_news_archive_fallback_paged'])) {
    $paged = (int) $GLOBALS['school_news_archive_fallback_paged'];
} else {
    $qv_paged = (int) get_query_var('paged');
    $qv_page  = (int) get_query_var('page');
    if ($qv_paged > 0) {
        $paged = max(1, $qv_paged);
    } elseif ($qv_page > 0) {
        $paged = max(1, $qv_page);
    }
}

// 1ページあたり件数（既定 10。テーマで変更する場合は school_news_archive_posts_per_page フィルター）
$per_page = (int) apply_filters('school_news_archive_posts_per_page', 10);
if ($per_page < 1) {
    $per_page = 10;
}

$news_q = new WP_Query(array(
    'post_type'           => 'news_school',
    'posts_per_page'      => $per_page,
    'paged'               => $paged,
    'post_status'         => 'publish',
    'orderby'             => 'date',
    'order'               => 'DESC',
    'ignore_sticky_posts' => true,
    'no_found_rows'       => false,
));

if (!$news_q->have_posts() && $paged > 1) {
    wp_reset_postdata();
    $paged  = 1;
    $news_q = new WP_Query(array(
        'post_type'           => 'news_school',
        'posts_per_page'      => $per_page,
        'paged'               => 1,
        'post_status'         => 'publish',
        'orderby'             => 'date',
        'order'               => 'DESC',
        'ignore_sticky_posts' => true,
        'no_found_rows'       => false,
    ));
}

$is_fallback = !empty($GLOBALS['school_news_archive_fallback']);
$pagination_base = trailingslashit(home_url('/school/news')) . 'page/%#%/';
if (!$is_fallback && function_exists('school_section_is_news_page') && school_section_is_news_page()) {
    $pagination_base = trailingslashit(get_permalink()) . 'page/%#%/';
}
?>
<section class="p-school-news-list">
    <?php if ($news_q->have_posts()) : ?>
    <div class="p-school-news-list__inner l-inner">
        <ul class="p-school-news-list__items">
            <?php
            while ($news_q->have_posts()) :
                $news_q->the_post();
                ?>
            <li class="p-school-news-list__item">
                <a class="p-school-news-list__link" href="<?php echo esc_url(get_permalink()); ?>">
                    <time class="p-school-news-list__date" datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date('Y.m.d')); ?></time>
                    <span class="p-school-news-list__title"><?php echo esc_html(get_the_title()); ?></span>
                </a>
            </li>
                <?php
            endwhile;
            wp_reset_postdata();
            ?>
        </ul>
    </div>

    <nav class="p-school-news-list__pagination l-inner" aria-label="お知らせのページ送り">
        <?php
        $pagination_args = array(
            'total'     => $news_q->max_num_pages,
            'current'   => $paged,
            'type'      => 'list',
            'prev_text' => '&lt; Back',
            'next_text' => 'Next &gt;',
            'mid_size'  => 1,
            'end_size'  => 1,
            'base'      => $pagination_base,
            'format'    => '',
        );
        $pagination_html = paginate_links($pagination_args);
        if (trim((string) $pagination_html) === '' && (int) $news_q->max_num_pages <= 1) {
            $pagination_html =
                '<ul class="page-numbers">' .
                '<li><span class="page-numbers prev disabled" aria-disabled="true">&lt; Back</span></li>' .
                '<li><span class="page-numbers current" aria-current="page">1</span></li>' .
                '<li><span class="page-numbers next disabled" aria-disabled="true">Next &gt;</span></li>' .
                '</ul>';
        }
        echo $pagination_html;
        ?>
    </nav>
    <?php else : ?>
    <div class="p-school-news-list__empty l-inner">
        <p>お知らせはまだありません。</p>
    </div>
    <?php endif; ?>
</section>

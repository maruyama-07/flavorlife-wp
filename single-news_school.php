<?php
/**
 * News（スクール）お知らせ 詳細
 */
get_header('school');
$news_list_url = function_exists('school_section_get_news_page_id') && school_section_get_news_page_id()
    ? get_permalink(school_section_get_news_page_id())
    : home_url('/school/news/');
?>
<main class="l-main l-main--school p-school-news-single">
    <?php
    while (have_posts()) :
        the_post();
        get_template_part('parts/project/p-school-news-single-hero');
        ?>
    <div class="page-content">
        <div class="l-inner">
            <?php the_content(); ?>
            <div class="p-school-news-single__back" data-js-scroll-fade-in="" data-js-scroll-visible="">
                <a href="<?php echo esc_url($news_list_url); ?>" class="l-header-school__cta">一覧へ戻る</a>
            </div>
        </div>
    </div>
        <?php
    endwhile;
    ?>
</main>
<?php
get_footer('school');

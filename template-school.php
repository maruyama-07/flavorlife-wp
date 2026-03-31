<?php
/**
 * Template Name: スクール用
 * Description: /school および子ページ用
 */

get_header('school'); ?>

<main class="l-main l-main--school">
    <?php while (have_posts()) : the_post(); ?>
        <?php if (function_exists('school_section_is_queried_root') && school_section_is_queried_root()) : ?>
            <div class="p-school-top">
                <?php get_template_part('parts/project/p-school-mv-splide'); ?>
                <?php get_template_part('parts/project/p-school-news-ticker'); ?>
                <?php get_template_part('parts/common/p-school-top-intro'); ?>
                <?php get_template_part('parts/common/p-school-category'); ?>
                <?php get_template_part('parts/common/p-school-seasonal-topics'); ?>
            </div>
        <?php else : ?>
            <?php get_template_part('parts/common/p-school-subpage-hero'); ?>
        <?php endif; ?>
        <?php
        // スクールトップ（/school）のみ本文ブロックは出さない（下層は the_content を表示）
        $school_hide_page_content = function_exists('school_section_is_queried_root') && school_section_is_queried_root();
        if (!$school_hide_page_content) :
            ?>
        <div class="page-content">
            <div class="l-inner">
                <?php the_content(); ?>
            </div>
        </div>
            <?php
        endif;
        ?>
    <?php endwhile; ?>
</main>

<?php get_footer('school'); ?>

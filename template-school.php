<?php
/**
 * Template Name: スクール用
 * Description: /school および子ページ用
 */

get_header('school'); ?>

<?php
$main_class = 'l-main';
if (function_exists('school_section_is_about_page') && school_section_is_about_page()) {
    $main_class .= ' l-main--school p-school-about';
}
?>
<main class="<?php echo esc_attr($main_class); ?>">
    <?php if (function_exists('school_section_is_course_page') && school_section_is_course_page()) : ?>
        <?php
        while (have_posts()) :
            the_post();
            get_template_part('parts/common/p-school-subpage-hero');
        endwhile;
        rewind_posts();
        ?>
        <?php get_template_part('parts/project/p-school-course-archive'); ?>
    <?php elseif (function_exists('school_section_is_voice_page') && school_section_is_voice_page()) : ?>
        <?php
        while (have_posts()) :
            the_post();
            get_template_part('parts/common/p-school-subpage-hero');
        endwhile;
        rewind_posts();
        ?>
        <?php get_template_part('parts/project/p-school-voice-archive'); ?>
    <?php else : ?>
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
                if (function_exists('school_section_is_about_page') && school_section_is_about_page()) {
                    get_template_part('parts/project/p-school-about-intro', null, array(
                        'post_id' => get_the_ID(),
                    ));
                    get_template_part('parts/project/p-school-about-bottom-links', null, array(
                        'post_id' => get_the_ID(),
                    ));
                }
            endif;
            ?>
        <?php endwhile; ?>
    <?php endif; ?>
</main>

<?php get_footer('school'); ?>
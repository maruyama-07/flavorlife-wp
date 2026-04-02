<?php
/**
 * スクール講座 詳細
 */
get_header('school');
?>
<main class="l-main l-main--school p-school-course-single">
    <?php
    while (have_posts()) :
        the_post();
        get_template_part('parts/common/p-school-subpage-hero');
        ?>
    <div class="page-content">
        <div class="l-inner">
            <?php the_content(); ?>
        </div>
    </div>
        <?php
    endwhile;
    ?>
</main>
<?php
get_footer('school');

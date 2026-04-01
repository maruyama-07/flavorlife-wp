<?php
/**
 * 受講生の声 単体
 */
get_header('school');
?>
<main class="l-main l-main--school p-school-voice-single">
    <?php
    while (have_posts()) :
        the_post();
        get_template_part('parts/project/p-school-voice-single-hero');
        get_template_part('parts/project/p-school-voice-single-media');
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

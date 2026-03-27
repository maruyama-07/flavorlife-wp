<?php get_header(); ?>

<main>

    <?php get_template_part('parts/project/p-top-mv');
  ?>

    <?php 
    // get_template_part('parts/project/p-top-gallery');
  ?>

    <?php //get_template_part('parts/project/p-top-post'); ?>

    <?php //get_template_part('parts/project/p-top-works');
  ?>
    <?php //get_template_part('parts/project/p-top-post-list'); ?>

    <?php //get_template_part('parts/project/p-top-works-list'); ?>

    <?php get_template_part('parts/project/p-front-media-text'); ?>



    <div class="l-inner p-index ">
        <?php the_content(); ?>
    </div>


    <?php get_template_part('parts/project/p-top-product'); ?>
    <?php 
    // get_template_part('parts/project/p-top-shop'); 
    ?>
    <?php 
    get_template_part('parts/project/p-top-recruit'); 
    ?>

    <?php get_template_part('parts/project/p-top-news'); ?>


    <?php get_template_part('parts/project/p-top-blog'); ?>

</main>

<?php get_footer(); ?>
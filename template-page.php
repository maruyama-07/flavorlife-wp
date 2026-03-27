<?php
/**
 * Template Name: 標準ページ
 * Description: サムネイル画像付きの標準ページテンプレート
 */

get_header(); ?>

<main class="l-main page-template">
    <?php while (have_posts()) : the_post(); ?>
        
        <!-- ページヒーロー -->
        <?php get_template_part('parts/common/page-hero'); ?>
        
        <!-- ページコンテンツ -->
        <div class="page-content">
            <div class="l-inner p-index">
                <?php the_content(); ?>
            </div>
        </div>

        <?php get_template_part('parts/project/p-evidence-banner', null, array('post_id' => get_the_ID())); ?>

        <?php get_template_part('parts/common/contact-section', null, array('post_id' => get_the_ID())); ?>
        <?php get_template_part('parts/common/shop-cta-section', null, array('post_id' => get_the_ID())); ?>
        
    <?php endwhile; ?>
</main>

<?php get_footer(); ?>

<?php
/**
 * Template Name: インタビューページ
 * Description: インタビュー用テンプレート。プロフィールカードをpage-heroの下に表示します。
 */

get_header(); ?>

<main class="l-main page-template page-template--interview">
    <?php while (have_posts()) : the_post(); ?>
        
        <!-- ページヒーロー -->
        <?php get_template_part('parts/common/page-hero'); ?>
        
        <!-- インタビュープロフィール（page-hero__contentの下） -->
        <?php get_template_part('parts/common/interview-profile', null, array('post_id' => get_the_ID())); ?>
        
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

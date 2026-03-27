<?php
/**
 * Template Name: シンプルページ（グレー背景）
 * Description: サムネイルなし・グレー背景のシンプルページテンプレート（Contact、Privacy Policy用）
 */

get_header(); ?>

<main class="l-main page-template--simple">
    <?php while (have_posts()) : the_post(); ?>
        
        <!-- ページヘッダー（サムネイルなし） -->
        <div class="page-header">
            <div class="l-inner">
                <h1 class="page-header__title"><?php the_title(); ?></h1>
                <?php
                $page_subtitle = get_field('page_subtitle');
                if ($page_subtitle) :
                ?>
                    <p class="page-header__subtitle c-head-sub"><?php echo esc_html($page_subtitle); ?></p>
                <?php endif; ?>
            </div>
        </div>
        
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

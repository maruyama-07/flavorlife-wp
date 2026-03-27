<?php
/**
 * 投稿詳細テンプレート（News / Blog 共通）
 * 標準テンプレートと同様の構造：ヒーロー画像 + 日付（左）+ タイトル（中央）
 */
get_header();
?>
<main class="l-main">
    <div class="p-single">
        <?php if (have_posts()) : ?>
            <?php while (have_posts()) : the_post(); ?>
                <?php 
                get_template_part('parts/common/page-hero'); 
                ?>
                <div class="page-content">
                    <div class="l-inner p-index">
                        <?php the_content(); ?>
                        <div class="c-custom-button-wrap" data-js-scroll-fade-in="" data-js-scroll-visible="">
                            <a href="/blog" class="c-custom-button" target="_self" rel="">
                                戻る
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 8.33 9.95">
                                <defs>
                                    <style>
                                        .cls-1 {
                                            fill: none;
                                            stroke: #fff;
                                            stroke-miterlimit: 10;
                                            stroke-width: 2px;
                                        }
                                    </style>
                                </defs>
                                    <g>
                                        <polyline class="cls-1" points=".57 9.13 6.57 4.98 .57 .82"></polyline>
                                    </g>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
                <?php 
                // get_template_part('parts/common/p-pager-list'); 
                ?>
            <?php endwhile; ?>
        <?php endif; ?>
        <?php wp_reset_postdata(); ?>
    
        <?php //get_template_part('parts/post/p-post-connect'); ?>
    </div>
</main>
<?php get_footer(); ?>

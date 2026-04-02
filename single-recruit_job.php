<?php
/**
 * 求人情報（単体）
 * 旧: template-recruit の子固定ページと同等の見た目
 */
get_header();
?>
<main class="l-main page-template page-template--recruit">
    <?php
    while (have_posts()) :
        the_post();
        $hub_id = function_exists('recruit_job_get_hub_page_id') ? recruit_job_get_hub_page_id() : 0;
        $parent_title    = $hub_id ? get_the_title($hub_id) : '';
        $parent_subtitle = ($hub_id && function_exists('get_field')) ? get_field('page_subtitle', $hub_id) : '';
        $page_subtitle   = function_exists('get_field') ? get_field('recruit_job_subtitle', get_the_ID()) : '';
        ?>
    <div class="p-recruit-header">
        <div class="l-inner">
            <?php if ($parent_title !== '') : ?>
            <p class="page-hero__title"><?php echo esc_html($parent_title); ?></p>
            <?php endif; ?>
            <?php if ($parent_subtitle) : ?>
            <p class="p-recruit-header__parent-subtitle"><?php echo esc_html($parent_subtitle); ?></p>
            <?php endif; ?>
            <hr class="p-recruit-header__line">
            <h1 class="p-recruit-header__title"><?php the_title(); ?></h1>
            <?php if ($page_subtitle) : ?>
            <p class="p-recruit-header__subtitle"><?php echo esc_html($page_subtitle); ?></p>
            <?php endif; ?>
            <hr class="p-recruit-header__line">
        </div>
    </div>

    <div class="page-content">
        <div class="l-inner p-recruit">
            <?php the_content(); ?>
        </div>
    </div>

        <?php
        get_template_part('parts/project/p-evidence-banner', null, array('post_id' => get_the_ID()));
        $footer_context_id = $hub_id ? $hub_id : get_the_ID();
        get_template_part('parts/common/contact-section', null, array('post_id' => $footer_context_id));
        get_template_part('parts/common/shop-cta-section', null, array('post_id' => $footer_context_id));
        ?>
    <?php endwhile; ?>
</main>
<?php
get_footer();

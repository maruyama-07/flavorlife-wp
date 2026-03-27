<?php
/**
 * Template Name: 求人詳細
 * Description: 募集要項ページ用テンプレート。ページタイトルとACFサブテキストを表示し、求人一覧を表示します。
 */

get_header(); ?>

<main class="l-main page-template page-template--recruit">
    <?php while (have_posts()) : the_post(); ?>
        
        <?php
        $page_subtitle = get_field('page_subtitle');
        $all_children = get_pages(array(
            'parent'      => get_the_ID(),
            'sort_column' => 'menu_order,post_title',
            'post_status' => 'publish',
        ));
        // 求人詳細テンプレートを選択した子ページのみ
        $child_pages = array_filter($all_children, function ($child) {
            return get_page_template_slug($child->ID) === 'template-recruit.php';
        });
        $child_pages = array_values($child_pages);
        $parent_id = wp_get_post_parent_id(get_the_ID());
        $hide_thumbnail = get_field('hide_thumbnail');
        $has_thumbnail = has_post_thumbnail();
        ?>
        
        <?php if (empty($child_pages) && $parent_id) : ?>
            <!-- 求人詳細子ページ（営業職など）のヘッダー -->
            <?php
            $parent_title = get_the_title($parent_id);
            $parent_subtitle = get_field('page_subtitle', $parent_id);
            ?>
            <div class="p-recruit-header">
                <div class="l-inner">
                    <p class="page-hero__title"><?php echo esc_html($parent_title); ?></p>
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
        <?php elseif ((!$hide_thumbnail && $has_thumbnail) || get_field('recruit_hero_video')) : ?>
            <?php
            $hero_video = get_field('recruit_hero_video');
            $hero_video_sp = get_field('recruit_hero_video_sp');
            $hero_video_poster = get_field('recruit_hero_video_poster');
            $sp_thumbnail = get_field('sp_thumbnail');
            $pc_thumbnail = get_the_post_thumbnail_url(get_the_ID(), 'full');
            if (!$sp_thumbnail) $sp_thumbnail = $pc_thumbnail;
            $unique_id = 'page-hero-' . get_the_ID();
            $has_video = !empty($hero_video);
            ?>
            <div id="<?php echo esc_attr($unique_id); ?>" class="page-hero<?php echo $has_video ? '' : ' img-effect'; ?>">
                <div class="page-hero__image<?php echo $has_video ? '' : ' img-load'; ?>">
                    <?php if ($has_video) :
                        $video_type = (strpos($hero_video, '.webm') !== false) ? 'video/webm' : 'video/mp4';
                        $video_sp_type = !empty($hero_video_sp) && (strpos($hero_video_sp, '.webm') !== false) ? 'video/webm' : 'video/mp4';
                    ?>
                        <?php if (!empty($hero_video_sp)) : ?>
                        <video class="page-hero__video page-hero__video--sp js-hero-video" autoplay muted loop playsinline preload="auto"<?php echo $hero_video_poster ? ' poster="' . esc_url($hero_video_poster) . '"' : ''; ?>>
                            <source src="<?php echo esc_url($hero_video_sp); ?>" type="<?php echo esc_attr($video_sp_type); ?>">
                        </video>
                        <video class="page-hero__video page-hero__video--pc js-hero-video" autoplay muted loop playsinline preload="auto"<?php echo $hero_video_poster ? ' poster="' . esc_url($hero_video_poster) . '"' : ''; ?>>
                            <source src="<?php echo esc_url($hero_video); ?>" type="<?php echo esc_attr($video_type); ?>">
                        </video>
                        <?php else : ?>
                        <video class="page-hero__video js-hero-video" autoplay muted loop playsinline preload="auto"<?php echo $hero_video_poster ? ' poster="' . esc_url($hero_video_poster) . '"' : ''; ?>>
                            <source src="<?php echo esc_url($hero_video); ?>" type="<?php echo esc_attr($video_type); ?>">
                        </video>
                        <?php endif; ?>
                    <?php else : ?>
                    <img src="<?php echo esc_url($pc_thumbnail); ?>" alt="<?php the_title(); ?>">
                    <?php endif; ?>
                </div>
            </div>
            <div class="page-hero__content">
                <h1 class="page-hero__title"><?php the_title(); ?></h1>
                <?php if ($page_subtitle) : ?>
                    <p class="page-hero__subtitle c-head-sub"><?php echo esc_html($page_subtitle); ?></p>
                <?php endif; ?>
                <?php if (!empty($child_pages)) : ?>
                    <div class="l-inner">
                    <ul class="p-recruit-list">
                        <?php foreach ($child_pages as $child) : ?>
                            <li class="p-recruit-list__item">
                                <a href="<?php echo esc_url(get_permalink($child)); ?>" class="p-recruit-list__link">
                                    <span class="p-recruit-list__icon">f</span>
                                    <span class="p-recruit-list__title"><?php echo esc_html($child->post_title); ?></span>
                                    <?php
                                    $child_tag = get_field('page_subtitle', $child->ID);
                                    if ($child_tag) :
                                    ?>

                                        <span class="p-recruit-list__tag"><?php echo esc_html($child_tag); ?></span>
                                    <?php endif; ?>
                                    <svg class="p-recruit-list__arrow" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="12" cy="12" r="11.5" stroke="currentColor" fill="#0f2b0f"></circle>
                                        <path d="M10 8L14 12L10 16" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                    </svg>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    </div>
                <?php endif; ?>
            </div>
            <?php if (!$has_video && $sp_thumbnail !== $pc_thumbnail) : ?>
            <style>
            @media screen and (max-width: 767px) {
                #<?php echo esc_attr($unique_id); ?> .page-hero__image img {
                    content: url(<?php echo esc_url($sp_thumbnail); ?>);
                }
            }
            </style>
            <?php endif; ?>
        <?php else : ?>
            <!-- サムネイル非表示・未設定の場合はシンプルなヘッダー -->
            <div class="p-recruit-list-header">
                <div class="l-inner">
                    <h1 class="p-recruit-list-header__title"><?php the_title(); ?></h1>
                    <?php if ($page_subtitle) : ?>
                        <p class="p-recruit-list-header__subtitle"><?php echo esc_html($page_subtitle); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($child_pages)) : ?>
                        <ul class="p-recruit-list">
                            <?php foreach ($child_pages as $child) : ?>
                                <li class="p-recruit-list__item">
                                    <a href="<?php echo esc_url(get_permalink($child)); ?>" class="p-recruit-list__link">
                                        <span class="p-recruit-list__icon">f</span>
                                        <span class="p-recruit-list__title"><?php echo esc_html($child->post_title); ?></span>
                                        <?php
                                        $child_tag = get_field('page_subtitle', $child->ID);
                                        if ($child_tag) :
                                        ?>
                                            <span class="p-recruit-list__tag"><?php echo esc_html($child_tag); ?></span>
                                        <?php endif; ?>
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="12" cy="12" r="11.5" stroke="currentColor" fill="#0f2b0f"></circle>
                                            <path d="M10 8L14 12L10 16" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                        </svg>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- ページコンテンツ -->
        <div class="page-content">
            <div class="l-inner p-recruit">
                <?php the_content(); ?>
            </div>
        </div>

        <?php get_template_part('parts/project/p-evidence-banner', null, array('post_id' => get_the_ID())); ?>

        <?php get_template_part('parts/common/contact-section', null, array('post_id' => get_the_ID())); ?>
        <?php get_template_part('parts/common/shop-cta-section', null, array('post_id' => get_the_ID())); ?>
        
    <?php endwhile; ?>
</main>

<?php get_footer(); ?>

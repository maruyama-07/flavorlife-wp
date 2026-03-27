<?php
/**
 * News/Blog アーカイブ一覧
 * 使用: archive-blog.php, home.php（News一覧）
 *
 * get_template_part の第3引数で渡す args:
 *   title: メインタイトル（例: BLOG, News）
 *   subtitle: サブタイトル（例: 社長ブログ, お知らせ）
 */
$args    = wp_parse_args($args ?? array(), array('title' => '', 'subtitle' => ''));
$title   = $args['title'];
$subtitle = $args['subtitle'];
?>
<main class="l-main">
    <section class="p-archive-list">
        <div class="p-archive-list__inner">
            <div class="p-archive-list__header">
                <h1 class="p-archive-list__title"><?php echo esc_html($title); ?></h1>
                <?php if ($subtitle) : ?>
                <p class="p-archive-list__subtitle"><?php echo esc_html($subtitle); ?></p>
                <?php endif; ?>
            </div>

            <?php if (have_posts()) : ?>
            <ul class="p-archive-list__items">
                <?php while (have_posts()) : the_post(); ?>
                <li class="p-archive-list__item">
                    <a href="<?php the_permalink(); ?>" class="p-archive-list__link">
                        <div class="p-archive-list__thumbnail">
                            <?php if (has_post_thumbnail()) : ?>
                            <?php the_post_thumbnail('thumbnail'); ?>
                            <?php else : ?>
                            <img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/images/common/no-image.svg" alt="">
                            <?php endif; ?>
                        </div>
                        <div class="p-archive-list__content">
                            <time class="p-archive-list__date" datetime="<?php echo esc_attr(get_the_date('Y-m-d')); ?>">
                                <?php echo esc_html(get_the_date('Y.m.d')); ?>
                            </time>
                            <h2 class="p-archive-list__item-title"><?php the_title(); ?></h2>
                        </div>
                        <span class="p-archive-list__arrow">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="12" cy="12" r="11.5" stroke="currentColor" fill="#0f2b0f" />
                                <path d="M10 8L14 12L10 16" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>
                    </a>
                </li>
                <?php endwhile; ?>
            </ul>
            <?php get_template_part('parts/common/p-pager'); ?>
            <?php else : ?>
            <p class="p-archive-list__empty">記事はまだありません。</p>
            <?php endif; ?>
        </div>
    </section>
</main>

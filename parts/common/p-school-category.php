<?php
if (!function_exists('school_section_is_queried_root') || !school_section_is_queried_root()) {
    return;
}

$category_items = function_exists('school_top_get_category_items') ? school_top_get_category_items() : array();
if (empty($category_items)) {
    return;
}

$arrow_svg_path = get_theme_file_path('assets/images/school/arrow-btn.svg');
$arrow_svg = '';
if (is_readable($arrow_svg_path)) {
    $arrow_svg = (string) file_get_contents($arrow_svg_path);
}
?>
<section class="p-school-category" aria-labelledby="p-school-category-heading">
    <div class="p-school-category__inner ">
        <header class="p-school-category__header">
            <h2 id="p-school-category-heading" class="p-school-category__title">Category</h2>
            <p class="p-school-category__subtitle">カテゴリーで選ぶ</p>
        </header>

        <div class="l-inner p-school-category__list">
            <?php foreach ($category_items as $item) : ?>
                <?php
                if (empty($item['post']) || !$item['post'] instanceof WP_Post) {
                    continue;
                }
                $page = $item['post'];
                $title = get_the_title($page->ID);
                $link = get_permalink($page->ID);
                $desc = !empty($item['description']) ? (string) $item['description'] : '';
                $thumb_url = get_the_post_thumbnail_url($page->ID, 'school-card-arch');
                if (!$thumb_url) {
                    $thumb_url = get_the_post_thumbnail_url($page->ID, 'large');
                }
                ?>
            <a class="p-school-category__item" href="<?php echo esc_url($link); ?>">
                <div class="p-school-category__item-head">
                    <p class="p-school-category__item-title"><?php echo esc_html($title); ?></p>
                    <span class="p-school-category__item-arrow" aria-hidden="true">
                        <?php if ($arrow_svg !== '') : ?>
                            <?php echo $arrow_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        <?php else : ?>
                            <span>&rarr;</span>
                        <?php endif; ?>
                    </span>
                </div>
                <div class="p-school-category__item-image">
                    <?php if ($thumb_url) : ?>
                    <img src="<?php echo esc_url($thumb_url); ?>" alt="<?php echo esc_attr($title); ?>" loading="lazy" decoding="async">
                    <?php endif; ?>
                </div>
                <div class="p-school-category__item-content">
                    <?php if ($desc !== '') : ?>
                    <p class="p-school-category__item-desc">
                        <?php
                        echo function_exists('tool_format_text_with_sp_break')
                            ? tool_format_text_with_sp_break($desc)
                            : nl2br(esc_html((string) $desc));
                        ?>
                    </p>
                    <?php endif; ?>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

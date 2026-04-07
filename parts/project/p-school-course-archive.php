<?php
/**
 * 講座一覧（/school/course/）
 */
$course_page_id = function_exists('school_section_get_course_page_id') ? school_section_get_course_page_id() : 0;
$permalink      = $course_page_id ? get_permalink($course_page_id) : home_url('/school/course/');

/** カテゴリ切替・ページ送り後も一覧ブロック位置にスクロール（フラグメント） */
$school_course_layout_id = 'school-course-layout';

$cat_slug = function_exists('school_course_get_filter_category_slug') ? school_course_get_filter_category_slug() : '';

$paged = 1;
$qv_paged = (int) get_query_var('paged');
$qv_page  = (int) get_query_var('page');
if ($qv_paged > 0) {
    $paged = max(1, $qv_paged);
} elseif ($qv_page > 0) {
    $paged = max(1, $qv_page);
}

$per_page = (int) apply_filters('school_course_archive_posts_per_page', 10);
if ($per_page < 1) {
    $per_page = 10;
}

$query_args = array(
    'post_type'           => 'course_school',
    'posts_per_page'      => $per_page,
    'paged'               => $paged,
    'post_status'         => 'publish',
    'orderby'             => array(
        'menu_order' => 'ASC',
        'date'       => 'DESC',
    ),
    'ignore_sticky_posts' => true,
    'no_found_rows'       => false,
);

if ($cat_slug !== '') {
    $query_args['tax_query'] = array(
        array(
            'taxonomy' => 'course_school_category',
            'field'    => 'slug',
            'terms'    => $cat_slug,
        ),
    );
}

$course_q = new WP_Query($query_args);

if (!$course_q->have_posts() && $paged > 1) {
    wp_reset_postdata();
    $paged    = 1;
    $query_args['paged'] = 1;
    $course_q = new WP_Query($query_args);
}

$pagination_add = array();
if ($cat_slug !== '') {
    $pagination_add['course_cat'] = $cat_slug;
}

$pagination_base = trailingslashit((string) $permalink) . 'page/%#%/';

$terms = function_exists('course_school_get_terms_ordered')
    ? course_school_get_terms_ordered()
    : array();

if (!is_array($terms)) {
    $terms = array();
}

/**
 * @param WP_Term $term
 * @return string
 */
$school_course_term_badge_tone = function ($term) {
    if (!function_exists('get_field')) {
        return 'teal';
    }
    $tone = get_field('course_school_cat_badge_tone', 'course_school_category_' . (int) $term->term_id);
    $tone = is_string($tone) ? $tone : '';
    if (function_exists('course_school_sanitize_badge_tone')) {
        return course_school_sanitize_badge_tone($tone);
    }
    return in_array($tone, array('green', 'teal'), true) ? $tone : 'teal';
};
?>
<section class="p-school-course">
    <?php get_template_part('parts/project/p-school-course-top', null, array('post_id' => (int) $course_page_id)); ?>
    <div id="<?php echo esc_attr($school_course_layout_id); ?>" class="p-school-course__layout l-inner js-animate-content">
        <aside class="p-school-course__aside" aria-label="講座カテゴリー">
            <h2 class="p-school-course__aside-title">講座カテゴリー</h2>
            <nav class="p-school-course__nav">
                <ul class="p-school-course__nav-list">
                    <li class="p-school-course__nav-item<?php echo $cat_slug === '' ? ' is-current' : ''; ?>">
                        <a class="p-school-course__nav-link" href="<?php echo esc_url($permalink . '#' . $school_course_layout_id); ?>">
                            <span class="p-school-course__nav-label">すべて</span>
                            <span class="p-school-course__nav-arrow" aria-hidden="true">
                                <svg width="8" height="12" viewBox="0 0 8 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2 2L6 6L2 10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </span>
                        </a>
                    </li>
                    <?php foreach ($terms as $term) : ?>
                        <?php
                        $t_slug = isset($term->slug) ? (string) $term->slug : '';
                        $is_current = ($cat_slug !== '' && $cat_slug === $t_slug);
                        $term_url = add_query_arg('course_cat', $t_slug, $permalink) . '#' . $school_course_layout_id;
                        ?>
                    <li class="p-school-course__nav-item<?php echo $is_current ? ' is-current' : ''; ?>">
                        <a class="p-school-course__nav-link" href="<?php echo esc_url($term_url); ?>">
                            <?php
                            $nav_label = function_exists('course_school_category_get_label_parts')
                                ? course_school_category_get_label_parts($term)
                                : array('main' => $term->name, 'sub' => '');
                            ?>
                            <span class="p-school-course__nav-label">
                                <span class="p-school-course__nav-label-main"><?php echo esc_html($nav_label['main']); ?></span>
                                <?php if ($nav_label['sub'] !== '') : ?>
                                <span class="p-school-course__nav-label-sub"><?php echo esc_html($nav_label['sub']); ?></span>
                                <?php endif; ?>
                            </span>
                            <span class="p-school-course__nav-arrow" aria-hidden="true">
                                <svg width="8" height="12" viewBox="0 0 8 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2 2L6 6L2 10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </span>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </nav>
        </aside>

        <div class="p-school-course__main">
            <?php if ($course_q->have_posts()) : ?>
            <ul class="p-school-course__list">
                <?php
                while ($course_q->have_posts()) :
                    $course_q->the_post();
                    $pid = get_the_ID();

                    $point  = function_exists('get_field') ? get_field('course_school_point', $pid) : '';
                    $point  = is_string($point) ? trim($point) : '';
                    $dates  = function_exists('get_field') ? get_field('course_school_dates', $pid) : '';
                    $dates  = is_string($dates) ? trim($dates) : '';
                    $detail = function_exists('get_field') ? get_field('course_school_detail', $pid) : '';
                    $detail = is_string($detail) ? trim($detail) : '';

                    $recruiting = true;
                    if (function_exists('get_field')) {
                        $r = get_field('course_school_recruiting', $pid);
                        $recruiting = ($r === false || $r === 0 || $r === '0') ? false : true;
                    }

                    $thumb = get_the_post_thumbnail_url($pid, 'large');
                    $thumb = is_string($thumb) ? $thumb : '';

                    $badge_main = '';
                    $badge_sub  = '';
                    $tone       = 'teal';
                    $post_terms = get_the_terms($pid, 'course_school_category');
                    if ($post_terms && !is_wp_error($post_terms) && isset($post_terms[0])) {
                        $bt = $post_terms[0];
                        $tone = $school_course_term_badge_tone($bt);
                        if (function_exists('course_school_category_get_label_parts')) {
                            $parts      = course_school_category_get_label_parts($bt);
                            $badge_main = $parts['main'];
                            $badge_sub  = $parts['sub'];
                        } else {
                            $badge_main = $bt->name;
                        }
                    }
                    ?>
                <li class="p-school-course__item">
                    <a class="p-school-course-card" href="<?php echo esc_url(get_permalink()); ?>">
                        <div class="p-school-course-card__body">
                            <?php if ($badge_main !== '') : ?>
                            <div class="p-school-course-card__badge-row">
                                <span class="p-school-course-card__badge p-school-course-card__badge--<?php echo esc_attr($tone); ?>">
                                    <span class="p-school-course-card__badge-main"><?php echo esc_html($badge_main); ?></span>
                                    <?php if ($badge_sub !== '') : ?>
                                    <span class="p-school-course-card__badge-sub"><?php echo esc_html($badge_sub); ?></span>
                                    <?php endif; ?>
                                </span>
                            </div>
                            <?php endif; ?>
                            <div class="p-school-course-card__title-row">
                                <?php if ($recruiting) : ?>
                                <span class="p-school-course-card__status">募集中</span>
                                <?php endif; ?>
                                <h2 class="p-school-course-card__title"><?php the_title(); ?></h2>
                            </div>
                            <?php if ($point !== '') : ?>
                            <div class="p-school-course-card__point">
                                <span class="p-school-course-card__point-label">POINT</span>
                                <p class="p-school-course-card__point-text"><?php echo function_exists('tool_acf_format_field_for_echo') ? tool_acf_format_field_for_echo($point) : esc_html($point); ?></p>
                            </div>
                            <?php endif; ?>
                            <?php if ($dates !== '') : ?>
                            <p class="p-school-course-card__dates">
                                <span class="p-school-course-card__dates-icon" aria-hidden="true">
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12.667 2.667H3.333C2.597 2.667 2 3.264 2 4v9.333c0 .737.597 1.334 1.333 1.334h9.334c.736 0 1.333-.597 1.333-1.334V4c0-.736-.597-1.333-1.333-1.333zM10.667 1.333V4M5.333 1.333V4M2 6.667h12" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </span>
                                <span class="p-school-course-card__dates-text"><?php echo function_exists('tool_acf_format_field_for_echo') ? tool_acf_format_field_for_echo($dates) : esc_html($dates); ?></span>
                            </p>
                            <?php endif; ?>
                            <div class="p-school-course-card__rule" role="presentation"></div>
                            <?php if ($detail !== '') : ?>
                            <div class="p-school-course-card__detail"><?php echo function_exists('tool_acf_format_field_for_echo') ? tool_acf_format_field_for_echo($detail) : esc_html($detail); ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="p-school-course-card__media">
                            <?php if ($thumb !== '') : ?>
                            <img class="p-school-course-card__img" src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" loading="lazy" decoding="async">
                            <?php else : ?>
                            <div class="p-school-course-card__media-ph" aria-hidden="true"></div>
                            <?php endif; ?>
                        </div>
                    </a>
                </li>
                    <?php
                endwhile;
                wp_reset_postdata();
                ?>
            </ul>

            <?php if ((int) $course_q->max_num_pages > 1) : ?>
            <nav class="p-school-course__pagination" aria-label="講座一覧のページ送り">
                <?php
                echo paginate_links(array(
                    'total'     => (int) $course_q->max_num_pages,
                    'current'   => $paged,
                    'type'      => 'list',
                    'prev_text' => '&lt; Back',
                    'next_text' => 'Next &gt;',
                    'mid_size'  => 1,
                    'end_size'  => 1,
                    'base'      => $pagination_base,
                    'format'    => '',
                    'add_args'  => $pagination_add,
                    'add_fragment' => $school_course_layout_id,
                ));
                ?>
            </nav>
            <?php endif; ?>

            <?php else : ?>
            <p class="p-school-course__empty">該当する講座がありません。</p>
            <?php endif; ?>
        </div>
    </div>
</section>

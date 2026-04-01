<?php
/**
 * 受講生の声 一覧（/school/voice/ 固定ページ or 404 フォールバック）
 */
$intro = function_exists('school_voice_get_archive_intro')
    ? school_voice_get_archive_intro()
    : array(
        'heading_lines' => array(
            '知識は一生のお守り。香りが支える私の物語。',
            '学生からプロまで、ここで見つけた新しい自分。',
        ),
        'body'          => 'スクールで学んだ知識は、卒業後の暮らしの中で、さまざまな形で花開いていきます。ここでは、受講生の皆さんが語る、学びと香りがもたらした変化の物語をご紹介します。',
    );

$paged = 1;
if (!empty($GLOBALS['school_voice_archive_fallback_paged'])) {
    $paged = (int) $GLOBALS['school_voice_archive_fallback_paged'];
} else {
    // 固定ページテンプレ内の一覧: paged と page の両方を確認（WP は静的ページで page を使うことがある）
    $qv_paged = (int) get_query_var('paged');
    $qv_page  = (int) get_query_var('page');
    if ($qv_paged > 0) {
        $paged = max(1, $qv_paged);
    } elseif ($qv_page > 0) {
        $paged = max(1, $qv_page);
    }
}

$per_page = (int) apply_filters('school_voice_archive_posts_per_page', 9);
if ($per_page < 1) {
    $per_page = 9;
}

$voice_q = new WP_Query(array(
    'post_type'           => 'voice_school',
    'posts_per_page'      => $per_page,
    'paged'               => $paged,
    'post_status'         => 'publish',
    'orderby'             => 'date',
    'order'               => 'ASC',
    'ignore_sticky_posts' => true,
    'no_found_rows'       => false,
));

// 指定ページに投稿が無い（ページ番号だけ大きい・クエリ変数の取り違え等）→ 1 ページ目で再取得
if (!$voice_q->have_posts() && $paged > 1) {
    wp_reset_postdata();
    $paged   = 1;
    $voice_q = new WP_Query(array(
        'post_type'           => 'voice_school',
        'posts_per_page'      => $per_page,
        'paged'               => 1,
        'post_status'         => 'publish',
        'orderby'             => 'date',
        'order'               => 'ASC',
        'ignore_sticky_posts' => true,
        'no_found_rows'       => false,
    ));
}

$is_fallback = !empty($GLOBALS['school_voice_archive_fallback']);
$pagination_base = trailingslashit(home_url('/school/voice')) . 'page/%#%/';
if (!$is_fallback && function_exists('school_section_is_voice_page') && school_section_is_voice_page()) {
    $pagination_base = trailingslashit(get_permalink()) . 'page/%#%/';
}

$voice_thumb_placeholder = get_theme_file_uri('assets/images/school/voice-nonImage.jpg');
$footer_note = function_exists('school_voice_get_archive_footer_note') ? school_voice_get_archive_footer_note() : '';
?>
<section class="p-school-voice">
    <div class="p-school-voice__intro l-inner">
        <h2 class="p-school-voice__intro-title">
            <?php
            $heading_lines = isset($intro['heading_lines']) && is_array($intro['heading_lines'])
                ? $intro['heading_lines']
                : array();
            foreach ($heading_lines as $line) :
                ?>
            <span class="p-school-voice__intro-line"><?php echo esc_html((string) $line); ?></span>
                <?php
            endforeach;
            ?>
        </h2>
        <div class="p-school-voice__intro-body">
            <?php
            if (function_exists('tool_format_text_with_sp_break')) {
                echo tool_format_text_with_sp_break((string) $intro['body']);
            } else {
                echo nl2br(esc_html((string) $intro['body']));
            }
            ?>
        </div>
    </div>

    <?php if ($voice_q->have_posts()) : ?>
    <div class="p-school-voice__grid-wrap l-inner">
        <ul class="p-school-voice__grid">
            <?php
            while ($voice_q->have_posts()) :
                $voice_q->the_post();
                $thumb = get_the_post_thumbnail_url(get_the_ID(), 'voice_school_arch');
                if (!$thumb) {
                    $thumb = get_the_post_thumbnail_url(get_the_ID(), 'large');
                }
                if (!$thumb) {
                    $thumb = $voice_thumb_placeholder;
                }
                $quote = '';
                if (function_exists('get_field')) {
                    $quote = (string) get_field('voice_school_quote', get_the_ID());
                }
                if ($quote === '') {
                    $quote = get_the_excerpt();
                }
                $quote = trim(wp_strip_all_tags($quote));
                ?>
            <li class="p-school-voice-card">
                <article class="p-school-voice-card__inner">
                    <div class="p-school-voice-card__media">
                        <img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" loading="lazy" decoding="async">
                    </div>
                    <p class="p-school-voice-card__name"><?php echo esc_html(get_the_title()); ?></p>
                    <?php if ($quote !== '') : ?>
                    <p class="p-school-voice-card__quote"><?php echo esc_html($quote); ?></p>
                    <?php endif; ?>
                    <a class="p-school-voice-card__btn" href="<?php echo esc_url(get_permalink()); ?>">
                        <span class="p-school-voice-card__btn-label">詳細を見る</span>
                        <span class="p-school-voice-card__btn-icon" aria-hidden="true">
                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 2L8 6L4 10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </span>
                    </a>
                </article>
            </li>
                <?php
            endwhile;
            wp_reset_postdata();
            ?>
        </ul>
    </div>

    <nav class="p-school-voice__pagination l-inner" aria-label="受講生の声のページ送り">
        <?php
        echo paginate_links(array(
            'total'     => $voice_q->max_num_pages,
            'current'   => $paged,
            'type'      => 'list',
            'prev_text' => '&lt; Back',
            'next_text' => 'Next &gt;',
            'mid_size'  => 1,
            'end_size'  => 1,
            'base'      => $pagination_base,
            'format'    => '',
        ));
        ?>
    </nav>
    <?php else : ?>
    <div class="p-school-voice__empty l-inner">
        <p>投稿がありません。</p>
    </div>
    <?php endif; ?>

    <?php if ($footer_note !== '') : ?>
    <div class="p-school-voice__footer-note l-inner">
        <div class="p-school-voice__footer-note-inner">
            <?php
            if (function_exists('tool_format_text_with_sp_break')) {
                echo tool_format_text_with_sp_break($footer_note);
            } else {
                echo nl2br(esc_html($footer_note));
            }
            ?>
        </div>
    </div>
    <?php endif; ?>
</section>

<?php
if (!function_exists('school_section_is_queried_root') || !school_section_is_queried_root()) {
    return;
}

$card_pages = function_exists('school_top_get_card_pages') ? school_top_get_card_pages() : array();
$intro = function_exists('school_top_get_intro_content') ? school_top_get_intro_content() : array(
    'brand' => 'Flavorlife Aromatherapy School',
    'lead' => '確かな知識で、香りはもっと自由になる。',
    'body' => "アロマテラピーは曖昧な世界ではありません。\n植物学・身体・心理の関係性を体系的に学び、“わかる”から“扱える”へ導く、学びの場。\nそして楽しく学ぶことが、精油の理解を深め、香りを暮らしに取り入れるプロの道をつくります。\n当スクールでは初めての方でも、知識のある方でも、発見と奥深さを学べるコースを取り揃えています。",
);
?>
<div class="p-school-top-block">
    <section class="p-school-top-intro" aria-labelledby="p-school-top-intro-heading">
        <div class="p-school-top-intro__curve" aria-hidden="true"></div>
        <div class="p-school-top-intro__inner">
            <h1 id="p-school-top-intro-heading" class="p-school-top-intro__brand"><?php echo function_exists('tool_acf_format_field_for_echo') ? tool_acf_format_field_for_echo((string) $intro['brand']) : esc_html((string) $intro['brand']); ?></h1>
            <p class="p-school-top-intro__lead"><?php echo function_exists('tool_acf_format_field_for_echo') ? tool_acf_format_field_for_echo((string) $intro['lead']) : esc_html((string) $intro['lead']); ?></p>
            <div class="p-school-top-intro__body">
                <p>
                    <?php
                    echo function_exists('tool_acf_format_field_for_echo')
                        ? tool_acf_format_field_for_echo((string) $intro['body'])
                        : (function_exists('tool_format_text_with_sp_break')
                            ? tool_format_text_with_sp_break((string) $intro['body'])
                            : nl2br(esc_html((string) $intro['body'])));
                    ?>
                </p>
            </div>
        </div>
    </section>

    <?php if (!empty($card_pages)) : ?>
    <section class="p-school-top-cards" aria-label="スクールのご案内">
        <div class="p-school-top-cards__inner">
            <?php foreach ($card_pages as $idx => $card) : ?>
                <?php
                if (!$card instanceof WP_Post) {
                    continue;
                }
                $thumb_url = get_the_post_thumbnail_url($card->ID, 'school-card-arch');
                if (!$thumb_url) {
                    $thumb_url = get_the_post_thumbnail_url($card->ID, 'large');
                }
                $title = get_the_title($card->ID);
                $link = get_permalink($card->ID);
                ?>
            <a href="<?php echo esc_url($link); ?>" class="p-school-top-cards__item">
                <div class="p-school-top-cards__media">
                    <?php if ($thumb_url) : ?>
                    <img src="<?php echo esc_url($thumb_url); ?>" alt="<?php echo esc_attr($title); ?>"<?php echo $idx > 0 ? ' loading="lazy" decoding="async"' : ''; ?>>
                    <?php endif; ?>
                </div>
                <p class="p-school-top-cards__label"><?php echo esc_html($title); ?></p>
            </a>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>
</div>

<?php
/**
 * 講座一覧ページ：イントロ・セクション見出し・カテゴリータグカード（/school/course/ 固定ページの ACF）
 */
$post_id = isset($args['post_id']) ? (int) $args['post_id'] : 0;
if ($post_id < 1) {
    $post_id = function_exists('school_section_get_course_page_id') ? school_section_get_course_page_id() : 0;
}
if ($post_id < 1 || !function_exists('get_field')) {
    return;
}

$lead   = get_field('course_page_top_intro_lead', $post_id);
$body   = get_field('course_page_top_intro_body', $post_id);
$sec_ja = get_field('course_page_top_section_ja', $post_id);
$sec_en = get_field('course_page_top_section_en', $post_id);

$lead   = is_string($lead) ? trim($lead) : '';
$body   = is_string($body) ? trim($body) : '';
$sec_ja = is_string($sec_ja) ? trim($sec_ja) : '';
$sec_en = is_string($sec_en) ? trim($sec_en) : '';

$cards = array();
for ($i = 1; $i <= 3; $i++) {
    $t = get_field('course_page_top_card_' . $i . '_title', $post_id);
    $t = is_string($t) ? trim($t) : '';
    if ($t === '') {
        continue;
    }
    $link = get_field('course_page_top_card_' . $i . '_link', $post_id);
    $link = is_string($link) ? trim($link) : '';
    $img  = get_field('course_page_top_card_' . $i . '_image', $post_id);
    $cards[] = array(
        'title' => $t,
        'link'  => $link,
        'image' => $img,
    );
}

$has_intro   = ($lead !== '' || $body !== '');
$has_section = ($sec_ja !== '' || $sec_en !== '');
$has_cards   = $cards !== array();

if (!$has_intro && !$has_section && !$has_cards) {
    return;
}
?>
<div class="p-school-course-top">
    <div class="l-inner p-school-course-top__inner">
        <?php if ($has_intro) : ?>
        <div class="p-school-course-top__intro">
            <?php if ($lead !== '') : ?>
            <p class="p-school-course-top__lead"><?php echo function_exists('tool_acf_format_field_for_echo') ? tool_acf_format_field_for_echo($lead) : esc_html($lead); ?></p>
            <?php endif; ?>
            <?php if ($body !== '') : ?>
            <p class="p-school-course-top__body"><?php echo function_exists('tool_acf_format_field_for_echo') ? tool_acf_format_field_for_echo($body) : esc_html($body); ?></p>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if ($has_section) : ?>
        <div class="p-school-course-top__section" role="group" aria-label="セクション見出し">
            <?php if ($sec_ja !== '') : ?>
            <p class="p-school-course-top__section-ja"><?php echo function_exists('tool_acf_format_field_for_echo') ? tool_acf_format_field_for_echo($sec_ja) : esc_html($sec_ja); ?></p>
            <?php endif; ?>
            <?php if ($sec_en !== '') : ?>
            <p class="p-school-course-top__section-en" lang="en"><?php echo function_exists('tool_acf_format_field_for_echo') ? tool_acf_format_field_for_echo($sec_en) : esc_html($sec_en); ?></p>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if ($has_cards) : ?>
        <ul class="p-school-course-top__cards">
            <?php foreach ($cards as $row) : ?>
                <?php
                if (!is_array($row)) {
                    continue;
                }
                $title = isset($row['title']) ? trim((string) $row['title']) : '';
                if ($title === '') {
                    continue;
                }
                $link = isset($row['link']) ? trim((string) $row['link']) : '';
                $link = $link !== '' ? $link : '#';
                $img  = isset($row['image']) ? $row['image'] : null;
                $img_url = '';
                $img_alt = $title;
                if (is_array($img) && !empty($img['url'])) {
                    $img_url = (string) $img['url'];
                    $img_alt = !empty($img['alt']) ? (string) $img['alt'] : $title;
                } elseif (is_numeric($img)) {
                    $img_url = (string) wp_get_attachment_image_url((int) $img, 'large');
                }
                ?>
            <li class="p-school-course-top__card-item">
                <a class="p-school-course-top__card" href="<?php echo esc_url($link); ?>">
                    <span class="p-school-course-top__card-hole" aria-hidden="true"></span>
                    <span class="p-school-course-top__card-title"><?php echo esc_html($title); ?></span>
                    <span class="p-school-course-top__card-media">
                        <?php if ($img_url !== '') : ?>
                        <img class="p-school-course-top__card-img" src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($img_alt); ?>" loading="lazy" decoding="async">
                        <?php endif; ?>
                    </span>
                    <span class="p-school-course-top__card-btn">
                        <span class="p-school-course-top__card-btn-text">講座を見る</span>
                    </span>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>
    </div>
</div>

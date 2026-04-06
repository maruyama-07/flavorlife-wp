<?php
/**
 * スクール紹介ページ 2カラム紹介
 */
$post_id = isset($args['post_id']) ? (int) $args['post_id'] : get_the_ID();
if ($post_id < 1 || !function_exists('school_about_intro_get_data')) {
    return;
}

$data = school_about_intro_get_data($post_id);
$text = isset($data['text']) ? (string) $data['text'] : '';
$img_url = isset($data['img_url']) ? (string) $data['img_url'] : '';
$img_alt = isset($data['img_alt']) ? (string) $data['img_alt'] : '';
$caption = isset($data['caption']) ? (string) $data['caption'] : '';

if ($text === '' && $img_url === '' && $caption === '') {
    return;
}
?>
<section class="p-school-about-intro">
    <div class="p-school-about-intro__inner l-inner">
        <div class="p-school-about-intro__grid">
            <div class="p-school-about-intro__text">
                <?php
                if ($text !== '') {
                    echo school_about_intro_format_text((string) $text);
                }
                ?>
            </div>
            <?php if ($img_url !== '' || $caption !== '') : ?>
            <figure class="p-school-about-intro__media">
                <?php if ($img_url !== '') : ?>
                <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($img_alt !== '' ? $img_alt : ''); ?>" loading="lazy" decoding="async">
                <?php endif; ?>
                <?php if ($caption !== '') : ?>
                <figcaption class="p-school-about-intro__caption"><?php echo function_exists('tool_acf_format_field_for_echo') ? tool_acf_format_field_for_echo($caption) : nl2br(esc_html($caption)); ?></figcaption>
                <?php endif; ?>
            </figure>
            <?php endif; ?>
        </div>
    </div>
</section>

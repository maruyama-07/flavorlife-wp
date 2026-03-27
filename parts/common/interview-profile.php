<?php
/**
 * インタビュープロフィール
 * 画像・役職・名前・資格を表示
 *
 * @param int $post_id 投稿ID
 */
$post_id = isset($args['post_id']) ? (int) $args['post_id'] : get_the_ID();
if (!$post_id) {
    return;
}

$image = get_field('interview_image', $post_id);
$title = get_field('interview_title', $post_id);
$name_ja = get_field('interview_name_ja', $post_id);
$name_en = get_field('interview_name_en', $post_id);
$certifications = get_field('interview_certifications', $post_id);

if (!$image && !$title && !$name_ja && !$name_en && !$certifications) {
    return;
}

$image_url = '';
if ($image) {
    $image_url = is_array($image) ? ($image['url'] ?? '') : $image;
}
?>

<div class="p-interview-profile">
    <div class="l-inner">
        <?php if ($image_url) : ?>
            <div class="p-interview-profile__image">
                <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($name_ja ?: ''); ?>">
            </div>
        <?php endif; ?>
        <?php if ($title) : ?>
            <p class="p-interview-profile__title"><?php echo esc_html($title); ?></p>
        <?php endif; ?>
        <?php if ($name_ja) : ?>
            <p class="p-interview-profile__name-ja"><?php echo esc_html($name_ja); ?></p>
        <?php endif; ?>
        <?php if ($name_en) : ?>
            <p class="p-interview-profile__name-en"><?php echo esc_html($name_en); ?></p>
        <?php endif; ?>
        <?php if ($certifications) : ?>
            <p class="p-interview-profile__certifications"><?php echo esc_html($certifications); ?></p>
        <?php endif; ?>
    </div>
</div>

<?php
/**
 * Evidenceバナー（sh_evidenc ページ専用）
 * 緑背景・白テキスト・ロゴ
 *
 * @param int $post_id 投稿ID（省略時は現在の投稿）
 */
$post_id = isset($args['post_id']) ? (int) $args['post_id'] : get_the_ID();
if (!$post_id) {
    return;
}
$post = get_post($post_id);
if (!$post || $post->post_type !== 'page' || $post->post_name !== 'sh_evidenc') {
    return;
}

$text_1 = get_field('evidence_banner_text_1', $post_id);
$logo   = get_field('evidence_banner_logo', $post_id);

if (empty($text_1) && empty($logo)) {
    return;
}

$logo_url = is_array($logo) ? ($logo['url'] ?? '') : $logo;
$text_1_escaped = function_exists('tool_acf_format_field_for_echo')
    ? tool_acf_format_field_for_echo((string) $text_1)
    : (function_exists('tool_format_text_with_sp_break')
        ? tool_format_text_with_sp_break($text_1)
        : nl2br(esc_html((string) $text_1)));
?>

<section class="p-evidence-banner">
    <div class="p-evidence-banner__inner l-inner">
        <?php if ($text_1) : ?>
        <div class="p-evidence-banner__text"><?php echo $text_1_escaped; ?></div>
        <?php endif; ?>
        <?php if ($logo_url) : ?>
        <div class="p-evidence-banner__logo">
            <img src="<?php echo esc_url($logo_url); ?>" alt="Flavor Life" class="p-evidence-banner__logo-img">
        </div>
        <?php else : ?>
        <p class="p-evidence-banner__logo-text">Flavor Life</p>
        <?php endif; ?>
    </div>
</section>

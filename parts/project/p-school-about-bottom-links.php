<?php
/**
 * スクール紹介ページ下部リンク（2カラム）
 */
$post_id = isset($args['post_id']) ? (int) $args['post_id'] : get_the_ID();
if ($post_id < 1 || !function_exists('school_about_bottom_links_get_items')) {
    return;
}
$items = school_about_bottom_links_get_items($post_id);
if (empty($items)) {
    return;
}
?>
<section class="p-school-about-bottom-links">
    <div class="p-school-about-bottom-links__inner l-inner">
        <div class="p-school-about-bottom-links__grid">
            <?php foreach ($items as $item) : ?>
                <?php
                $text = isset($item['text']) ? (string) $item['text'] : '';
                $url = isset($item['url']) ? (string) $item['url'] : '';
                $img_url = isset($item['img_url']) ? (string) $item['img_url'] : '';
                $img_alt = isset($item['img_alt']) ? (string) $item['img_alt'] : $text;
                $new_tab = !empty($item['new_tab']);
                $tag = $url !== '' ? 'a' : 'div';
                $attrs = '';
                if ($url !== '') {
                    $attrs .= ' href="' . esc_url($url) . '"';
                    if ($new_tab) {
                        $attrs .= ' target="_blank" rel="noopener noreferrer"';
                    }
                }
                ?>
            <<?php echo $tag; ?> class="p-school-about-bottom-links__card"<?php echo $attrs; ?>>
                <?php if ($img_url !== '') : ?>
                <div class="p-school-about-bottom-links__image">
                    <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($img_alt); ?>" loading="lazy" decoding="async">
                </div>
                <?php endif; ?>
                <?php if ($text !== '') : ?>
                <p class="p-school-about-bottom-links__text"><?php echo esc_html($text); ?></p>
                <?php endif; ?>
            </<?php echo $tag; ?>>
            <?php endforeach; ?>
        </div>
    </div>
</section>

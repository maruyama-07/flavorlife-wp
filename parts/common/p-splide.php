<?php
/**
 * MV 等共通スライド（ACF 画像は get_field 優先、get_post_meta はフォールバック）
 */
if (!function_exists('p_splide_get_attachment_id')) {
    function p_splide_get_attachment_id($post_id, $meta_key)
    {
        if (function_exists('get_field')) {
            $v = get_field($meta_key, $post_id);
            if (is_numeric($v)) {
                return (int) $v;
            }
            if (is_array($v)) {
                if (!empty($v['ID'])) {
                    return (int) $v['ID'];
                }
                if (!empty($v['id'])) {
                    return (int) $v['id'];
                }
            }
        }
        $raw = get_post_meta($post_id, $meta_key, true);
        return is_numeric($raw) ? (int) $raw : 0;
    }

    function p_splide_attachment_src($attachment_id, $size = 'full')
    {
        if (!$attachment_id) {
            return '';
        }
        $src = wp_get_attachment_image_src((int) $attachment_id, $size);
        return (is_array($src) && !empty($src[0])) ? $src[0] : '';
    }

    function p_splide_get_meta_text($post_id, $key)
    {
        if (function_exists('get_field')) {
            $v = get_field($key, $post_id);
            if ($v !== null && $v !== false && $v !== '') {
                return is_string($v) ? $v : (string) $v;
            }
        }
        return (string) get_post_meta($post_id, $key, true);
    }
}
?>
<!-- スライダーのコンテナを作成 -->
<div id="<?php echo esc_attr($args['post_type']); ?>" class="splide">
    <div class="splide__track">
        <div class="splide__list">
            <?php
            $the_query = new WP_Query($args);
            if ($the_query->have_posts()) :
                while ($the_query->have_posts()) :
                    $the_query->the_post();
                    $pid = get_the_ID();

                    $image_pc_id = p_splide_get_attachment_id($pid, 'slide_img_pc');
                    $image_sp_id = p_splide_get_attachment_id($pid, 'slide_img_sp');
                    $image_pc_src = p_splide_attachment_src($image_pc_id);
                    $image_sp_src = p_splide_attachment_src($image_sp_id);

                    // SP のみ設定されている場合は PC 表示にも使う
                    if ($image_pc_src === '' && $image_sp_src !== '') {
                        $image_pc_src = $image_sp_src;
                    }

                    $image_alt = p_splide_get_meta_text($pid, 'slide_img_alt');
                    $image_url = p_splide_get_meta_text($pid, 'slide_img_url');
                    $slide_text = p_splide_get_meta_text($pid, 'slide_text');

                    $tab_raw = function_exists('get_field') ? get_field('slide_img_tab', $pid) : get_post_meta($pid, 'slide_img_tab', true);
                    $image_tab = ($tab_raw === true || $tab_raw === 1 || $tab_raw === '1') ? '_blank' : '_self';

                    if ($image_pc_src !== '' || $image_sp_src !== '') :
                        $tag = !empty($image_url) ? 'a' : 'div';
                        $href = !empty($image_url) ? ' href="' . esc_url($image_url) . '" target="' . esc_attr($image_tab) . '" rel="noreferrer noopener"' : '';
                        $img_src = $image_pc_src !== '' ? $image_pc_src : $image_sp_src;
                        ?>
            <<?php echo $tag . $href; ?> class="splide__slide">
                <picture<?php echo !empty($args['img_effect']) ? ' class="img-effect img-load"' : ''; ?>>
                    <?php if (!empty($image_sp_src) && $image_sp_src !== $img_src) : ?>
                    <source media="(max-width: 767px)" srcset="<?php echo esc_url($image_sp_src); ?>">
                    <?php endif; ?>
                    <img src="<?php echo esc_url($img_src); ?>" alt="<?php echo esc_attr($image_alt); ?>"
                        loading="lazy">
                </picture>
                <?php if (!empty($slide_text)) : ?>
                <div class="splide__slide-text">
                    <p><?php echo nl2br(esc_html($slide_text)); ?></p>
                </div>
                <?php endif; ?>
            </<?php echo $tag; ?>>
                        <?php
                    endif;
                endwhile;
            endif;
            wp_reset_postdata();
            ?>
        </div>
    </div>
</div>

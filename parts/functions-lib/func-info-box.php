<?php
/**
 * 情報ボックス（ショートコード）
 * クラシックエディタで [info_box title="見出し"]本文[/info_box] で使用
 *
 * @example [info_box title="ビジネスメンバーズクラブとは"]フレーバーライフ社の精油...[/info_box]
 */
add_shortcode('info_box', 'info_box_shortcode');

function info_box_shortcode($atts, $content = null)
{
    $args = shortcode_atts(array(
        'title' => '',
    ), $atts);

    if (empty($args['title']) && empty($content)) {
        return '';
    }

    $title = esc_html($args['title']);
    $body  = wp_kses_post(wpautop(trim($content)));

    ob_start();
?>
<div class="c-info-box">
    <?php if ($title) : ?>
    <h3 class="c-info-box__title"><?php echo $title; ?></h3>
    <?php endif; ?>
    <?php if ($body) : ?>
    <div class="c-info-box__body"><?php echo $body; ?></div>
    <?php endif; ?>
</div>
<?php
    return ob_get_clean();
}

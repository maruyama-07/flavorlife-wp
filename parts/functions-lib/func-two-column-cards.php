<?php
/**
 * 2カラムカード（ショートコード）
 * 画像＋テキスト＋c-custom-button の2カラムレイアウト
 * [two_column_cards image1="123" text1="テキスト" link1="/url" image2="456" text2="テキスト" link2="/url"]
 * クラシックエディタの「2カラムカード」ボタンから挿入
 */
add_shortcode('two_column_cards', 'two_column_cards_shortcode');

function two_column_cards_shortcode($atts)
{
    $args = shortcode_atts(array(
        'image1' => '',
        'text1'   => '',
        'link1'   => '',
        'new_tab1' => '',
        'image2'  => '',
        'text2'   => '',
        'link2'   => '',
        'new_tab2' => '',
    ), $atts);

    $arrow_svg = '<svg class="c-custom-button__arrow" width="12" height="18" viewBox="0 0 12 18" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2 2L10 9L2 16" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" /></svg>';

    $cols = array();
    for ($i = 1; $i <= 2; $i++) {
        $img_id = (int) $args['image' . $i];
        $text   = esc_html($args['text' . $i]);
        $link   = esc_url($args['link' . $i] ?: '#');
        $new_tab = !empty($args['new_tab' . $i]);
        $cols[] = array(
            'image_id' => $img_id,
            'text'     => $text,
            'link'     => $link,
            'new_tab'  => $new_tab,
        );
    }

    if (empty($cols[0]['image_id']) && empty($cols[0]['text']) && empty($cols[1]['image_id']) && empty($cols[1]['text'])) {
        return '';
    }

    ob_start();
?>
<div class="p-two-column-cards js-animate-content">
    <?php foreach ($cols as $col) : ?>
    <div class="p-two-column-cards__item">
        <?php if ($col['image_id'] && wp_attachment_is_image($col['image_id'])) : ?>
        <div class="p-two-column-cards__image">
            <?php echo wp_get_attachment_image($col['image_id'], 'medium_large', false, array('class' => 'p-two-column-cards__img')); ?>
        </div>
        <?php endif; ?>
        <?php if ($col['text']) : ?>
        <p class="p-two-column-cards__text"><?php echo $col['text']; ?></p>
        <?php endif; ?>
        <?php if ($col['link']) : ?>
        <div class="c-custom-button-wrap">
            <a href="<?php echo $col['link']; ?>" class="c-custom-button"<?php echo $col['new_tab'] ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>>
                詳細はこちら
                <?php echo $arrow_svg; ?>
            </a>
        </div>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
</div>
<?php
    return ob_get_clean();
}

/**
 * TinyMCE に「2カラムカード」ボタンを追加
 */
add_filter('mce_buttons_2', function ($buttons) {
    array_push($buttons, 'two_column_cards');
    return $buttons;
});

add_filter('mce_external_plugins', function ($plugin_array) {
    $plugin_array['two_column_cards'] = get_template_directory_uri() . '/assets/js/admin/two-column-cards.js';
    return $plugin_array;
});

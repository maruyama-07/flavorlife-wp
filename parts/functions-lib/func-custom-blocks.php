<?php
/**
 * Gutenberg カスタムブロックの登録
 */

function register_custom_blocks() {
    // ヒーローセクションブロック
    register_block_type(get_template_directory() . '/blocks/hero-section');
}
add_action('init', 'register_custom_blocks');

/**
 * ブロックのフロントエンド表示
 */
function render_hero_section_block($attributes) {
    $bg_pc = isset($attributes['backgroundImagePC']['url']) ? $attributes['backgroundImagePC']['url'] : '';
    $bg_sp = isset($attributes['backgroundImageSP']['url']) ? $attributes['backgroundImageSP']['url'] : '';
    $text = isset($attributes['heroText']) ? $attributes['heroText'] : '';
    $text_color = isset($attributes['textColor']) ? $attributes['textColor'] : 'white';
    $text_position = isset($attributes['textPosition']) ? $attributes['textPosition'] : 'center';
    $vertical_position = isset($attributes['verticalPosition']) ? $attributes['verticalPosition'] : 'center';
    $height = isset($attributes['sectionHeight']) ? $attributes['sectionHeight'] : '70vh';
    
    // 背景画像のスタイル
    $bg_image = $bg_pc;
    if (!empty($bg_sp) && wp_is_mobile()) {
        $bg_image = $bg_sp;
    }
    
    $style = sprintf(
        'background-image: url(%s); min-height: %s;',
        esc_url($bg_image),
        esc_attr($height)
    );
    
    $classes = sprintf(
        'hero-section hero-section--text-%s hero-section--align-%s hero-section--vertical-%s',
        esc_attr($text_color),
        esc_attr($text_position),
        esc_attr($vertical_position)
    );
    
    ob_start();
    ?>
    <div class="<?php echo $classes; ?>" style="<?php echo $style; ?>">
        <div class="hero-section__inner">
            <?php if (!empty($text)) : ?>
                <div class="hero-section__text">
                    <?php echo wp_kses_post($text); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <?php if (!empty($bg_sp) && !empty($bg_pc)) : ?>
    <style>
        @media screen and (max-width: 767px) {
            .hero-section {
                background-image: url(<?php echo esc_url($bg_sp); ?>) !important;
            }
        }
    </style>
    <?php endif; ?>
    <?php
    return ob_get_clean();
}

/**
 * ブロックエディタ用のアセットを登録
 */
function enqueue_block_editor_assets() {
    wp_enqueue_script(
        'hero-section-block',
        get_template_directory_uri() . '/blocks/hero-section/hero-section.js',
        array('wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-i18n'),
        filemtime(get_template_directory() . '/blocks/hero-section/hero-section.js')
    );
    
    wp_enqueue_style(
        'hero-section-editor',
        get_template_directory_uri() . '/blocks/hero-section/editor.css',
        array(),
        filemtime(get_template_directory() . '/blocks/hero-section/editor.css')
    );
}
add_action('enqueue_block_editor_assets', 'enqueue_block_editor_assets');

/* hero-section のスタイルは src/scss/object/component/c-hero-section.scss で管理（メイン style.css に含まれる） */

<?php
/**
 * Recruitセクション用カスタム投稿タイプ
 * ヒーローセクションと同様に管理画面で設定し、ショートコードで貼り付け
 */

add_action('init', 'create_recruit_section_post_type');
function create_recruit_section_post_type() {
    register_post_type(
        'recruit_section',
        array(
            'label' => 'Recruitセクション',
            'labels' => array(
                'name' => 'Recruitセクション',
                'all_items' => 'Recruitセクション一覧',
                'add_new_item' => 'Recruitセクションの新規追加',
                'edit_item' => 'Recruitセクションの編集',
            ),
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => true,
            'menu_icon' => 'dashicons-groups',
            'supports' => array('title'),
        )
    );
}

add_action('add_meta_boxes', 'add_recruit_section_meta_boxes');
function add_recruit_section_meta_boxes() {
    add_meta_box(
        'recruit_section_settings',
        'Recruitセクション設定',
        'recruit_section_meta_box_callback',
        'recruit_section',
        'normal',
        'high'
    );
}

function recruit_section_meta_box_callback($post) {
    wp_nonce_field('recruit_section_meta_box', 'recruit_section_meta_box_nonce');
    wp_enqueue_media();

    $image = get_post_meta($post->ID, 'recruit_section_image', true);
    $title = get_post_meta($post->ID, 'recruit_section_title', true);
    $subtitle = get_post_meta($post->ID, 'recruit_section_subtitle', true);
    $head = get_post_meta($post->ID, 'recruit_section_head', true);
    $text = get_post_meta($post->ID, 'recruit_section_text', true);
    $button_url = get_post_meta($post->ID, 'recruit_section_button_url', true);
    $button_text = get_post_meta($post->ID, 'recruit_section_button_text', true);
    ?>
    <style>
        .recruit-meta-box { padding: 20px; }
        .recruit-field { margin-bottom: 20px; }
        .recruit-field label { display: block; font-weight: bold; margin-bottom: 8px; }
        .recruit-field input[type="text"], .recruit-field textarea { width: 100%; max-width: 500px; padding: 8px; }
        .recruit-field textarea { min-height: 100px; }
        .recruit-image-preview { max-width: 300px; margin-top: 10px; }
        .recruit-image-preview img { width: 100%; height: auto; }
        .button { margin-right: 10px; }
    </style>
    <div class="recruit-meta-box">
        <?php $layout = get_post_meta($post->ID, 'recruit_section_layout', true) ?: 'image-left'; ?>
        <div class="recruit-field">
            <label>レイアウト（PC時）</label>
            <select id="recruit_section_layout" name="recruit_section_layout">
                <option value="image-left" <?php selected($layout, 'image-left'); ?>>画像左・テキスト右</option>
                <option value="image-right" <?php selected($layout, 'image-right'); ?>>テキスト左・画像右</option>
            </select>
            <p class="description">SP時は常に画像上・テキスト下です</p>
        </div>
        <div class="recruit-field">
            <label>画像</label>
            <input type="hidden" id="recruit_section_image" name="recruit_section_image" value="<?php echo esc_attr($image); ?>">
            <button type="button" class="button upload-image-button" data-target="recruit_section_image">画像を選択</button>
            <button type="button" class="button remove-image-button" data-target="recruit_section_image">画像を削除</button>
            <div class="recruit-image-preview" id="recruit_section_image_preview">
                <?php if ($image) : ?><img src="<?php echo esc_url($image); ?>" alt=""><?php endif; ?>
            </div>
        </div>
        <div class="recruit-field">
            <label>タイトル（例：Recruit）</label>
            <input type="text" id="recruit_section_title" name="recruit_section_title" value="<?php echo esc_attr($title); ?>" placeholder="Recruit">
        </div>
        <div class="recruit-field">
            <label>サブタイトル（例：求人情報）</label>
            <input type="text" id="recruit_section_subtitle" name="recruit_section_subtitle" value="<?php echo esc_attr($subtitle); ?>" placeholder="求人情報">
        </div>
        <div class="recruit-field">
            <label>見出し（例：香りと暮らしに関心がある人へ）</label>
            <input type="text" id="recruit_section_head" name="recruit_section_head" value="<?php echo esc_attr($head); ?>" placeholder="香りと暮らしに関心がある人へ">
        </div>
        <div class="recruit-field">
            <label>本文（改行可）</label>
            <textarea id="recruit_section_text" name="recruit_section_text" placeholder="香りはもちろん、&#10;人の日常を支える仕事があります。&#10;一緒に、やさしい価値をつくりませんか？"><?php echo esc_textarea($text); ?></textarea>
            <?php if (function_exists('tool_acf_paragraph_field_instructions')) : ?>
            <p class="description"><?php echo esc_html(tool_acf_paragraph_field_instructions()); ?></p>
            <?php endif; ?>
        </div>
        <div class="recruit-field">
            <label>ボタンURL（例：/recruit）</label>
            <input type="text" id="recruit_section_button_url" name="recruit_section_button_url" value="<?php echo esc_attr($button_url); ?>" placeholder="/recruit または https://...">
        </div>
        <div class="recruit-field">
            <label>ボタンテキスト</label>
            <input type="text" id="recruit_section_button_text" name="recruit_section_button_text" value="<?php echo esc_attr($button_text ?: '詳細はこちら'); ?>" placeholder="詳細はこちら">
        </div>
        <div class="recruit-field" style="background: #f0f0f0; padding: 15px; border-radius: 4px;">
            <label>ショートコード（ページに貼り付けてください）</label>
            <input type="text" value='[recruit_section id="<?php echo $post->ID; ?>"]' readonly onclick="this.select();" style="background: #fff; width: 100%; max-width: 400px;">
        </div>
    </div>
    <script>
    jQuery(document).ready(function($) {
        $('.upload-image-button').on('click', function(e) {
            e.preventDefault();
            var target = $(this).data('target');
            var mediaUploader = wp.media({ title: '画像を選択', button: { text: '選択' }, multiple: false });
            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                $('#' + target).val(attachment.url);
                $('#' + target + '_preview').html('<img src="' + attachment.url + '" alt="">');
            });
            mediaUploader.open();
        });
        $('.remove-image-button').on('click', function(e) {
            e.preventDefault();
            var target = $(this).data('target');
            $('#' + target).val('');
            $('#' + target + '_preview').html('');
        });
    });
    </script>
    <?php
}

add_action('save_post', 'save_recruit_section_meta');
function save_recruit_section_meta($post_id) {
    if (!isset($_POST['recruit_section_meta_box_nonce'])) return;
    if (!wp_verify_nonce($_POST['recruit_section_meta_box_nonce'], 'recruit_section_meta_box')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    if (isset($_POST['recruit_section_image'])) {
        update_post_meta($post_id, 'recruit_section_image', esc_url_raw($_POST['recruit_section_image']));
    }
    if (isset($_POST['recruit_section_button_url'])) {
        $val = sanitize_text_field($_POST['recruit_section_button_url']);
        if (!empty($val) && strpos($val, 'http') !== 0) {
            $val = home_url('/' . ltrim($val, '/'));
        }
        update_post_meta($post_id, 'recruit_section_button_url', $val);
    }
    if (isset($_POST['recruit_section_layout'])) {
        update_post_meta($post_id, 'recruit_section_layout', sanitize_text_field($_POST['recruit_section_layout']));
    }
    $text_fields = array('recruit_section_title', 'recruit_section_subtitle', 'recruit_section_head', 'recruit_section_button_text');
    foreach ($text_fields as $key) {
        if (isset($_POST[$key])) {
            update_post_meta($post_id, $key, sanitize_text_field($_POST[$key]));
        }
    }
    if (isset($_POST['recruit_section_text'])) {
        update_post_meta($post_id, 'recruit_section_text', sanitize_textarea_field($_POST['recruit_section_text']));
    }
}

/**
 * c-image-text ブロックをレンダー（ショートコード・p-top-recruit 両方で使用）
 * @param int $post_id recruit_section の ID。0 の場合は最初の投稿またはデフォルト
 * @return string HTML
 */
function render_c_image_text_block($post_id = 0) {
    if (!$post_id) {
        $posts = get_posts(array('post_type' => 'recruit_section', 'posts_per_page' => 1, 'post_status' => 'publish'));
        $post_id = !empty($posts) ? $posts[0]->ID : 0;
    }
    if (!$post_id) {
        return render_c_image_text_block_default();
    }

    $image = get_post_meta($post_id, 'recruit_section_image', true);
    $title = get_post_meta($post_id, 'recruit_section_title', true);
    $subtitle = get_post_meta($post_id, 'recruit_section_subtitle', true);
    $head = get_post_meta($post_id, 'recruit_section_head', true);
    $text = get_post_meta($post_id, 'recruit_section_text', true);
    $button_url = get_post_meta($post_id, 'recruit_section_button_url', true);
    $button_text = get_post_meta($post_id, 'recruit_section_button_text', true) ?: '詳細はこちら';
    $layout = get_post_meta($post_id, 'recruit_section_layout', true) ?: 'image-left';

    $image_src = $image ?: get_template_directory_uri() . '/assets/images/common/recruit-back.webp';
    $layout_class = ($layout === 'image-right') ? ' c-image-text--reversed' : '';
    if (empty($button_url)) {
        $button_url = home_url('/recruit');
    } elseif (strpos($button_url, 'http') !== 0) {
        $button_url = home_url('/' . ltrim($button_url, '/'));
    }

    ob_start();
    ?>
    <section class="c-image-text<?php echo esc_attr($layout_class); ?>">
        <div class="c-image-text__container js-animate-content">
            <div class="c-image-text__image">
                <img src="<?php echo esc_url($image_src); ?>" alt="<?php echo esc_attr($title ?: 'Recruit'); ?>">
            </div>
            <div class="c-image-text__content">
                <?php if ($title) : ?><h2 class="c-image-text__title"><?php echo esc_html($title); ?></h2><?php endif; ?>
                <?php if ($subtitle) : ?><p class="c-image-text__subtitle"><?php echo esc_html($subtitle); ?></p><?php endif; ?>
                <?php if ($head) : ?><h3 class="c-image-text__head"><?php echo esc_html($head); ?></h3><?php endif; ?>
                <?php if ($text) : ?><p class="c-image-text__text"><?php echo function_exists('tool_format_text_with_sp_break') ? tool_format_text_with_sp_break((string) $text) : nl2br(esc_html((string) $text)); ?></p><?php endif; ?>
                <a href="<?php echo esc_url($button_url); ?>" class="c-custom-button c-custom-button--black">
                    <?php echo esc_html($button_text); ?>
                    <svg class="c-custom-button__arrow" width="12" height="18" viewBox="0 0 12 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2 2L10 9L2 16" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </a>
            </div>
        </div>
    </section>
    <?php
    return ob_get_clean();
}

function render_c_image_text_block_default() {
    $image_src = get_template_directory_uri() . '/assets/images/common/recruit-back.webp';
    ob_start();
    ?>
    <section class="c-image-text">
        <div class="c-image-text__container js-animate-content">
            <div class="c-image-text__image">
                <img src="<?php echo esc_url($image_src); ?>" alt="About Aromatherapy">
            </div>
            <div class="c-image-text__content">
                <h2 class="c-image-text__title">About Aromatherapy</h2>
                <p class="c-image-text__subtitle">アロマや精油について</p>
                <h3 class="c-image-text__head">心と身体の健康に役立てる自然療法</h3>
                <p class="c-image-text__text">アロマテラピーとは、自然の木々や草花など、植物の香りによって、心と身体の健康に役立てる自然療法です。</p>
                <a href="<?php echo esc_url(home_url('/aromatherapy')); ?>" class="c-custom-button c-custom-button--black">詳細はこちら
                    <svg class="c-custom-button__arrow" width="12" height="18" viewBox="0 0 12 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2 2L10 9L2 16" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </a>
            </div>
        </div>
    </section>
    <?php
    return ob_get_clean();
}

add_shortcode('recruit_section', 'recruit_section_shortcode');
function recruit_section_shortcode($atts) {
    $atts = shortcode_atts(array('id' => 0), $atts);
    return render_c_image_text_block(intval($atts['id']));
}

add_filter('manage_recruit_section_posts_columns', 'recruit_section_columns');
function recruit_section_columns($columns) {
    return array(
        'cb' => $columns['cb'],
        'title' => 'タイトル',
        'shortcode' => 'ショートコード',
        'date' => '日付'
    );
}

add_action('manage_recruit_section_posts_custom_column', 'recruit_section_column_content', 10, 2);
function recruit_section_column_content($column, $post_id) {
    if ($column === 'shortcode') {
        echo '<code>[recruit_section id="' . $post_id . '"]</code>';
    }
}

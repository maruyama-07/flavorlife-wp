<?php
/**
 * ヒーローセクション用カスタム投稿タイプ
 */

add_action('init', 'create_hero_section_post_type');
function create_hero_section_post_type() {
    register_post_type(
        'hero_section',
        array(
            'label' => 'ヒーローセクション',
            'labels' => array(
                'name' => 'ヒーローセクション',
                'all_items' => 'ヒーローセクション一覧',
                'add_new_item' => 'ヒーローセクションの新規追加',
                'edit_item' => 'ヒーローセクションの編集',
            ),
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => true,
            'menu_icon' => 'dashicons-format-image',
            'supports' => array('title'),
        )
    );
}

/**
 * メタボックスの追加
 */
add_action('add_meta_boxes', 'add_hero_section_meta_boxes');
function add_hero_section_meta_boxes() {
    add_meta_box(
        'hero_section_settings',
        'ヒーローセクション設定',
        'hero_section_meta_box_callback',
        'hero_section',
        'normal',
        'high'
    );
}

function hero_section_meta_box_callback($post) {
    wp_nonce_field('hero_section_meta_box', 'hero_section_meta_box_nonce');
    
    // 保存された値を取得
    $bg_pc = get_post_meta($post->ID, 'hero_bg_pc', true);
    $bg_sp = get_post_meta($post->ID, 'hero_bg_sp', true);
    $text = get_post_meta($post->ID, 'hero_text', true);
    $text_color = get_post_meta($post->ID, 'hero_text_color', true) ?: 'white';
    $text_position = get_post_meta($post->ID, 'hero_text_position', true) ?: 'center';
    $vertical_position = get_post_meta($post->ID, 'hero_vertical_position', true) ?: 'center';
    $height = get_post_meta($post->ID, 'hero_height', true) ?: 'aspect-design';
    ?>
    
    <style>
        .hero-meta-box { padding: 20px; }
        .hero-field { margin-bottom: 20px; }
        .hero-field label { display: block; font-weight: bold; margin-bottom: 8px; }
        .hero-field input[type="text"], .hero-field textarea, .hero-field select { width: 100%; padding: 8px; }
        .hero-field textarea { min-height: 80px; }
        .hero-image-preview { max-width: 300px; margin-top: 10px; }
        .hero-image-preview img { width: 100%; height: auto; }
        .button { margin-right: 10px; }
    </style>
    
    <div class="hero-meta-box">
        <!-- 背景画像（PC） -->
        <div class="hero-field">
            <label>背景画像（PC用）</label>
            <input type="hidden" id="hero_bg_pc" name="hero_bg_pc" value="<?php echo esc_attr($bg_pc); ?>">
            <button type="button" class="button upload-image-button" data-target="hero_bg_pc">画像を選択</button>
            <button type="button" class="button remove-image-button" data-target="hero_bg_pc">画像を削除</button>
            <div class="hero-image-preview" id="hero_bg_pc_preview">
                <?php if ($bg_pc) : ?>
                    <img src="<?php echo esc_url($bg_pc); ?>" alt="PC背景">
                <?php endif; ?>
            </div>
        </div>
        
        <!-- 背景画像（SP） -->
        <div class="hero-field">
            <label>背景画像（スマホ用・オプション）</label>
            <input type="hidden" id="hero_bg_sp" name="hero_bg_sp" value="<?php echo esc_attr($bg_sp); ?>">
            <button type="button" class="button upload-image-button" data-target="hero_bg_sp">画像を選択</button>
            <button type="button" class="button remove-image-button" data-target="hero_bg_sp">画像を削除</button>
            <div class="hero-image-preview" id="hero_bg_sp_preview">
                <?php if ($bg_sp) : ?>
                    <img src="<?php echo esc_url($bg_sp); ?>" alt="SP背景">
                <?php endif; ?>
            </div>
        </div>
        
        <!-- テキスト -->
        <div class="hero-field">
            <label>テキスト（オプション）</label>
            <textarea id="hero_text" name="hero_text"><?php echo esc_textarea($text); ?></textarea>
            <p class="description"><?php echo esc_html(function_exists('tool_acf_paragraph_field_instructions') ? tool_acf_paragraph_field_instructions() : 'テキストを入力してください。改行も反映されます。'); ?></p>
        </div>
        
        <!-- 高さ設定 -->
        <div class="hero-field">
            <label>セクション高さ</label>
            <select id="hero_height" name="hero_height">
                <option value="aspect-design" <?php selected($height, 'aspect-design'); ?>>デザインカンプ（1200×650 / 750×550）</option>
                <option value="aspect-16-9" <?php selected($height, 'aspect-16-9'); ?>>アスペクト比 16:9</option>
                <option value="aspect-21-9" <?php selected($height, 'aspect-21-9'); ?>>アスペクト比 21:9（シネマ）</option>
                <option value="aspect-4-3" <?php selected($height, 'aspect-4-3'); ?>>アスペクト比 4:3</option>
                <option value="aspect-1-1" <?php selected($height, 'aspect-1-1'); ?>>アスペクト比 1:1（正方形）</option>
                <option value="50vh" <?php selected($height, '50vh'); ?>>画面の高さ 50%</option>
                <option value="70vh" <?php selected($height, '70vh'); ?>>画面の高さ 70%</option>
                <option value="100vh" <?php selected($height, '100vh'); ?>>画面の高さ 100%</option>
                <option value="500px" <?php selected($height, '500px'); ?>>カスタム 500px</option>
                <option value="600px" <?php selected($height, '600px'); ?>>カスタム 600px</option>
                <option value="650px" <?php selected($height, '650px'); ?>>カスタム 650px</option>
                <option value="800px" <?php selected($height, '800px'); ?>>カスタム 800px</option>
                <option value="auto" <?php selected($height, 'auto'); ?>>自動（コンテンツに合わせる）</option>
            </select>
        </div>
        
        <!-- テキスト色 -->
        <div class="hero-field">
            <label>テキスト色</label>
            <select id="hero_text_color" name="hero_text_color">
                <option value="white" <?php selected($text_color, 'white'); ?>>白</option>
                <option value="black" <?php selected($text_color, 'black'); ?>>黒</option>
            </select>
        </div>
        
        <!-- 水平位置 -->
        <div class="hero-field">
            <label>テキストの水平位置</label>
            <select id="hero_text_position" name="hero_text_position">
                <option value="left" <?php selected($text_position, 'left'); ?>>左</option>
                <option value="center" <?php selected($text_position, 'center'); ?>>中央</option>
                <option value="right" <?php selected($text_position, 'right'); ?>>右</option>
            </select>
        </div>
        
        <!-- 垂直位置 -->
        <div class="hero-field">
            <label>テキストの垂直位置</label>
            <select id="hero_vertical_position" name="hero_vertical_position">
                <option value="top" <?php selected($vertical_position, 'top'); ?>>上</option>
                <option value="center" <?php selected($vertical_position, 'center'); ?>>中央</option>
                <option value="bottom" <?php selected($vertical_position, 'bottom'); ?>>下</option>
            </select>
        </div>
        
        <!-- ショートコード表示 -->
        <div class="hero-field" style="background: #f0f0f0; padding: 15px; border-radius: 4px;">
            <label>ショートコード（ページに貼り付けてください）</label>
            <input type="text" value='[hero id="<?php echo $post->ID; ?>"]' readonly onclick="this.select();" style="background: #fff;">
            <p class="description">このコードをコピーして、ページ編集画面に貼り付けてください。</p>
        </div>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        // 画像アップロード
        $('.upload-image-button').on('click', function(e) {
            e.preventDefault();
            var target = $(this).data('target');
            var button = $(this);
            
            var mediaUploader = wp.media({
                title: '画像を選択',
                button: { text: '選択' },
                multiple: false
            });
            
            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                $('#' + target).val(attachment.url);
                $('#' + target + '_preview').html('<img src="' + attachment.url + '" alt="">');
            });
            
            mediaUploader.open();
        });
        
        // 画像削除
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

/**
 * メタデータの保存
 */
add_action('save_post', 'save_hero_section_meta');
function save_hero_section_meta($post_id) {
    if (!isset($_POST['hero_section_meta_box_nonce'])) return;
    if (!wp_verify_nonce($_POST['hero_section_meta_box_nonce'], 'hero_section_meta_box')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;
    
    // URL系のフィールド
    $url_fields = array('hero_bg_pc', 'hero_bg_sp');
    foreach ($url_fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, $field, esc_url_raw($_POST[$field]));
        }
    }
    
    // テキストフィールド（改行を保持）
    if (isset($_POST['hero_text'])) {
        update_post_meta($post_id, 'hero_text', sanitize_textarea_field($_POST['hero_text']));
    }
    
    // その他のフィールド
    $other_fields = array('hero_text_color', 'hero_text_position', 'hero_vertical_position', 'hero_height');
    foreach ($other_fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
        }
    }
}

/**
 * ショートコード
 */
add_shortcode('hero', 'hero_section_shortcode');
function hero_section_shortcode($atts) {
    $atts = shortcode_atts(array(
        'id' => 0
    ), $atts);
    
    $post_id = intval($atts['id']);
    if (!$post_id) return '';
    
    // メタデータ取得
    $bg_pc = get_post_meta($post_id, 'hero_bg_pc', true);
    $bg_sp = get_post_meta($post_id, 'hero_bg_sp', true);
    $text = get_post_meta($post_id, 'hero_text', true);
    $text_color = get_post_meta($post_id, 'hero_text_color', true) ?: 'white';
    $text_position = get_post_meta($post_id, 'hero_text_position', true) ?: 'center';
    $vertical_position = get_post_meta($post_id, 'hero_vertical_position', true) ?: 'center';
    $height = get_post_meta($post_id, 'hero_height', true) ?: 'aspect-design';
    
    // 固有のIDを生成
    $unique_id = 'hero-section-' . $post_id;
    
    // 背景画像（PC用）
    $style = '';
    if (!empty($bg_pc)) {
        $style = 'background-image: url(' . esc_url($bg_pc) . ');';
    }
    
    // アスペクト比以外の高さ設定
    if (strpos($height, 'aspect-') === false) {
        $style .= ' min-height: ' . esc_attr($height) . ';';
    }
    
    $classes = sprintf(
        'hero-section hero-section--text-%s hero-section--align-%s hero-section--vertical-%s',
        esc_attr($text_color),
        esc_attr($text_position),
        esc_attr($vertical_position)
    );
    
    ob_start();
    ?>
    <div id="<?php echo esc_attr($unique_id); ?>" class="<?php echo $classes; ?>" data-height="<?php echo esc_attr($height); ?>" style="<?php echo $style; ?>">
        <div class="hero-section__inner">
            <?php if (!empty($text)) : ?>
                <div class="hero-section__text">
                    <?php echo function_exists('tool_acf_format_field_for_echo') ? tool_acf_format_field_for_echo((string) $text) : (function_exists('tool_format_text_with_sp_break') ? tool_format_text_with_sp_break((string) $text) : nl2br(esc_html((string) $text))); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <?php if (!empty($bg_sp) && !empty($bg_pc)) : ?>
    <style>
        /* PC用背景画像 */
        #<?php echo esc_attr($unique_id); ?> {
            background-image: url(<?php echo esc_url($bg_pc); ?>) !important;
        }
        
        /* SP用背景画像 */
        @media screen and (max-width: 767px) {
            #<?php echo esc_attr($unique_id); ?> {
                background-image: url(<?php echo esc_url($bg_sp); ?>) !important;
            }
        }
    </style>
    <?php endif; ?>
    <?php
    return ob_get_clean();
}

/**
 * 管理画面のカラム表示をカスタマイズ
 */
add_filter('manage_hero_section_posts_columns', 'hero_section_columns');
function hero_section_columns($columns) {
    $new_columns = array(
        'cb' => $columns['cb'],
        'title' => 'タイトル',
        'shortcode' => 'ショートコード',
        'preview' => 'プレビュー',
        'date' => '日付'
    );
    return $new_columns;
}

add_action('manage_hero_section_posts_custom_column', 'hero_section_column_content', 10, 2);
function hero_section_column_content($column, $post_id) {
    switch ($column) {
        case 'shortcode':
            echo '<code>[hero id="' . $post_id . '"]</code>';
            echo '<button type="button" class="button button-small" onclick="navigator.clipboard.writeText(\'[hero id=&quot;' . $post_id . '&quot;]\'); alert(\'コピーしました！\');">コピー</button>';
            break;
        case 'preview':
            $bg_pc = get_post_meta($post_id, 'hero_bg_pc', true);
            if ($bg_pc) {
                echo '<img src="' . esc_url($bg_pc) . '" style="width: 100px; height: auto;">';
            } else {
                echo '画像なし';
            }
            break;
    }
}

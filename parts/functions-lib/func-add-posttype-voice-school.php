<?php
/**
 * 受講生の声（スクール）
 *
 * 一覧 URL は /school/voice/ の固定ページ（親: school）で表示する。
 * CPT の has_archive はオフ。
 * 単体は /school/voice-detail/記事スラッグ/（一覧ページと同じ school/voice プレフィックスにすると
 * 固定ページの子として解釈され 404 になるため別スラッグにする）
 */

add_action('init', 'school_voice_register_post_type');
function school_voice_register_post_type()
{
    register_post_type(
        'voice_school',
        array(
            'label' => '受講生の声',
            'labels' => array(
                'name' => '受講生の声',
                'singular_name' => '受講生の声',
                'add_new_item' => '受講生の声を追加',
                'edit_item' => '受講生の声を編集',
                'all_items' => '受講生の声一覧',
            ),
            'public' => true,
            'show_ui' => true,
            'show_in_rest' => true,
            'has_archive' => false,
            'rewrite' => array(
                'slug' => 'school/voice-detail',
                'with_front' => false,
            ),
            'menu_position' => 14,
            'menu_icon' => 'dashicons-format-quote',
            'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
        )
    );
}

/**
 * サイトURLがスキームなし（//host/...）のとき get_permalink が // で始まりクリックできない場合があるため補正
 *
 * @param string  $permalink
 * @param WP_Post $post
 * @return string
 */
function school_voice_fix_permalink_scheme($permalink, $post)
{
    if (!is_object($post) || $post->post_type !== 'voice_school') {
        return $permalink;
    }
    if (!is_string($permalink) || strlen($permalink) < 3) {
        return $permalink;
    }
    if (strncmp($permalink, '//', 2) !== 0) {
        return $permalink;
    }

    return (is_ssl() ? 'https:' : 'http:') . $permalink;
}
add_filter('post_type_link', 'school_voice_fix_permalink_scheme', 10, 2);

/**
 * 受講生の声 単体用リライト（CPT 登録だけだと環境によってマッチしないことがあるため明示）
 */
add_action('init', function () {
    add_rewrite_rule(
        '^school/voice-detail/([^/]+)/?$',
        'index.php?post_type=voice_school&name=$matches[1]',
        'top'
    );
}, 20);

/**
 * テーマ更新後にリライトを再生成（手動でパーマリンク保存しなくても効くように）
 */
add_action('init', function () {
    $v = (int) get_option('school_voice_rewrite_rules_v', 0);
    if ($v < 5) {
        flush_rewrite_rules(false);
        update_option('school_voice_rewrite_rules_v', 5);
    }
}, 999);

/**
 * /school/voice-detail/スラッグ/ が 404 になる環境向けに、スラッグから投稿を解決して単体表示に切り替える
 */
function school_voice_try_recover_404_single()
{
    if (!is_404()) {
        return;
    }

    $path = school_voice_get_request_path_trimmed();
    if (!preg_match('#^school/voice-detail/(.+?)/?$#u', $path, $m)) {
        return;
    }

    $slug = rawurldecode($m[1]);
    $slug = trim($slug, '/');
    if ($slug === '') {
        return;
    }

    $q = new WP_Query(array(
        'post_type' => 'voice_school',
        'name' => $slug,
        'post_status' => 'publish',
        'posts_per_page' => 1,
    ));

    if (!$q->have_posts()) {
        global $wpdb;
        $post_id = (int) $wpdb->get_var($wpdb->prepare(
            "SELECT ID FROM {$wpdb->posts} WHERE post_type = 'voice_school' AND post_status = 'publish' AND post_name = %s LIMIT 1",
            $slug
        ));
        if ($post_id) {
            $q = new WP_Query(array(
                'p' => $post_id,
                'post_type' => 'voice_school',
                'posts_per_page' => 1,
            ));
        }
    }

    if (!$q->have_posts()) {
        return;
    }

    global $wp_query;
    $wp_query = $q;
    $GLOBALS['wp_query'] = $wp_query;
    $GLOBALS['wp_the_query'] = $wp_query;

    $wp_query->is_404 = false;
    $wp_query->is_archive = false;
    $wp_query->is_post_type_archive = false;
    $wp_query->is_singular = true;
    $wp_query->is_single = true;
    $wp_query->is_page = false;
    $wp_query->is_home = false;
    $wp_query->is_attachment = false;
    $wp_query->queried_object = get_post($q->posts[0]);
    $wp_query->queried_object_id = (int) $q->posts[0]->ID;

    status_header(200);
    nocache_headers();
}
add_action('template_redirect', 'school_voice_try_recover_404_single', -1);

/**
 * /school/ 配下の 404 ではスクール用スタイルを当てる（404-school.php 用フラグ）
 */
add_action('template_redirect', function () {
    if (!is_404()) {
        return;
    }
    $path = school_voice_get_request_path_trimmed();
    if ($path !== '' && strpos($path, 'school/') === 0) {
        $GLOBALS['school_section_404'] = true;
    }
}, 100);

add_filter('404_template', function ($template) {
    if (empty($GLOBALS['school_section_404'])) {
        return $template;
    }
    $t = get_template_directory() . '/404-school.php';
    if (is_readable($t)) {
        return $t;
    }

    return $template;
});

/**
 * リクエストパス（ホームのサブディレクトリを除いた先頭パス）
 *
 * @return string 例: school/voice または school/voice/page/2
 */
function school_voice_get_request_path_trimmed()
{
    $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
    $path = (string) wp_parse_url($uri, PHP_URL_PATH);
    $path = trim($path, '/');
    $home_path = (string) wp_parse_url(home_url('/'), PHP_URL_PATH);
    $home_path = trim($home_path, '/');
    if ($home_path !== '' && strpos($path, $home_path) === 0) {
        $path = trim(substr($path, strlen($home_path)), '/');
    }

    return $path;
}

/**
 * /school/voice/ または /school/voice/page/N/ の一覧表示用リクエストか
 *
 * @return array{paged:int}|null
 */
function school_voice_parse_archive_request_path()
{
    $path = school_voice_get_request_path_trimmed();
    if ($path === 'school/voice') {
        return array('paged' => 1);
    }
    if (preg_match('#^school/voice/page/(\d+)/?$#', $path, $m)) {
        return array('paged' => max(1, (int) $m[1]));
    }

    return null;
}

/**
 * 固定ページ「school」の子として /school/voice/ を用意（未作成時のみ）
 * school-settings と同様に init で補完する。
 */
add_action('init', function () {
    if (apply_filters('school_voice_auto_create_page', true) !== true) {
        return;
    }
    $root = get_page_by_path('school');
    if (!$root) {
        return;
    }
    if (get_page_by_path('school/voice')) {
        return;
    }
    wp_insert_post(array(
        'post_title' => '受講生の声',
        'post_name' => 'voice',
        'post_status' => 'publish',
        'post_type' => 'page',
        'post_parent' => (int) $root->ID,
        'post_author' => 1,
    ));
}, 5);

add_action('after_setup_theme', function () {
    add_image_size('voice_school_arch', 560, 720, true);
});

/**
 * 固定ページが無く 404 になったときも一覧を表示（アセット用フラグを立てる）
 */
add_action('template_redirect', function () {
    if (!is_404()) {
        return;
    }
    if (get_page_by_path('school/voice')) {
        return;
    }
    $parsed = school_voice_parse_archive_request_path();
    if ($parsed === null) {
        return;
    }
    $GLOBALS['school_voice_archive_fallback'] = true;
    $GLOBALS['school_voice_archive_fallback_paged'] = $parsed['paged'];
    status_header(200);
    nocache_headers();
    get_header('school');
    echo '<main class="l-main l-main--school">';
    get_template_part('parts/project/p-school-voice-archive');
    echo '</main>';
    get_footer('school');
    exit;
}, 0);

add_action('after_switch_theme', function () {
    flush_rewrite_rules();
});

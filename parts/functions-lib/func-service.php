<?php
/**
 * Service ページ階層の設定
 *
 * 運用:
 * 1. 固定ページ「service」（スラッグ: service）を親として作成
 * 2. oem, btob, bmc を service の子ページに設定 → /service/oem/, /service/btob/, /service/bmc/
 * 3. /service/ へのアクセスは TOP#service にリダイレクト
 * 4. ヘッダー・フッターの「service」リンクは TOP#service へ
 */

/**
 * /service/ アクセス時は TOP#service にリダイレクト
 */
add_action('template_redirect', function () {
    if (!is_page('service')) {
        return;
    }
    wp_safe_redirect(home_url('/#service'), 302);
    exit;
});

/**
 * メニューの「service」リンクを TOP#service に差し替え
 */
add_filter('wp_nav_menu_objects', function ($items, $args) {
    $service_page = get_page_by_path('service');
    if (!$service_page) {
        return $items;
    }
    $service_url = get_permalink($service_page);
    $top_service_url = home_url('/#service');

    foreach ($items as $item) {
        if ($item->object_id == $service_page->ID || $item->url === $service_url) {
            $item->url = $top_service_url;
        }
    }
    return $items;
}, 10, 2);

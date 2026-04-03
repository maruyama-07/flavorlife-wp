<?php
/**
 * Google アナリティクス（gtag.js）
 *
 * 計測IDはGA管理画面で発行された G- から始まるIDを使用。
 * フィルターで差し替え・無効化可能:
 *   add_filter('theme_google_analytics_measurement_id', fn () => 'G-XXXX');
 *   add_filter('theme_google_analytics_measurement_id', '__return_empty_string'); // 出力停止
 */

add_action('wp_head', 'theme_output_google_analytics_gtag', 5);
function theme_output_google_analytics_gtag()
{
    if (is_admin() || wp_doing_ajax() || wp_is_json_request()) {
        return;
    }

    $id = apply_filters('theme_google_analytics_measurement_id', 'G-RX2MF5REQ7');
    $id = is_string($id) ? trim($id) : '';
    if ($id === '' || !preg_match('/^G-[A-Z0-9]+$/', $id)) {
        return;
    }

    $src = 'https://www.googletagmanager.com/gtag/js?id=' . rawurlencode($id);
    ?>
<script async src="<?php echo esc_url($src, array('https')); ?>"></script>
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
gtag('config', '<?php echo esc_js($id); ?>');
</script>
    <?php
}


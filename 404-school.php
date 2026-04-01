<?php
/**
 * /school/ 配下の 404（スクールヘッダー・フッター）
 */
get_header('school');
?>
<main class="l-main l-main--school">
    <div class="p-school-404 l-inner">
        <h1 class="p-school-404__code">404</h1>
        <p class="p-school-404__title">お探しのページは見つかりませんでした。</p>
        <p class="p-school-404__lead">URLが間違っているか、ページが移動した可能性があります。</p>
        <div class="p-school-404__actions">
            <a class="p-school-404__btn" href="<?php echo esc_url(home_url('/school/')); ?>">スクールトップへ</a>
            <a class="p-school-404__btn p-school-404__btn--sub" href="<?php echo esc_url(home_url('/')); ?>">サイトトップへ</a>
        </div>
    </div>
</main>
<?php
get_footer('school');

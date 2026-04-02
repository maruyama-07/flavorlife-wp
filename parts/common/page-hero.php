<?php
/**
 * ページヒーロー画像
 * 固定ページ: アイキャッチ + タイトル + サブタイトル
 * 投稿詳細: アイキャッチ + 日付（左）+ タイトル（中央）
 */

$is_single_post = is_single() && in_array(get_post_type(), array('post', 'blog', 'topics'), true);

// ACFフィールド取得（固定ページ用）
$hide_thumbnail = get_field('hide_thumbnail');
$sp_thumbnail = get_field('sp_thumbnail');
$page_subtitle = get_field('page_subtitle');

// 固定ページ: サムネイル非表示の場合は画像のみスキップ（タイトル・サブタイトルは表示）
$skip_image = !$is_single_post && $hide_thumbnail;

$pc_thumbnail = get_the_post_thumbnail_url(get_the_ID(), 'full');
if (!$skip_image && !$pc_thumbnail) {
    $skip_image = true;
}

if (!$sp_thumbnail) {
    $sp_thumbnail = $pc_thumbnail;
}

$unique_id = 'page-hero-' . get_the_ID();
?>

<?php if ($pc_thumbnail && !$skip_image && !$is_single_post) : ?>
<div id="<?php echo esc_attr($unique_id); ?>" class="page-hero img-effect">
    <div class="page-hero__image img-load">
        <img src="<?php echo esc_url($pc_thumbnail); ?>" alt="<?php the_title(); ?>">
    </div>
</div>
<?php endif; ?>

<div class="page-hero__content l-inner">
    <?php if ($is_single_post) : ?>
    <div class="page-hero__meta">
        <time class="page-hero__date" datetime="<?php echo esc_attr(get_the_date('Y-m-d')); ?>">
            <?php echo esc_html(get_the_date('Y.m.d')); ?>
        </time>
        <?php
        if (get_post_type() === 'blog' && function_exists('tool_get_blog_category_label')) {
            $blog_cat_label = tool_get_blog_category_label();
            if ($blog_cat_label !== '') {
                echo '<span class="c-blog-category-badge">' . esc_html($blog_cat_label) . '</span>';
            }
        }
        ?>
    </div>
    <?php endif; ?>
    <h1 class="page-hero__title"><?php the_title(); ?></h1>
    <?php if ($page_subtitle) : ?>
    <p class="page-hero__subtitle c-head-sub"><?php echo esc_html($page_subtitle); ?></p>
    <?php endif; ?>
</div>

<?php if ($pc_thumbnail && !$skip_image && !$is_single_post && $sp_thumbnail !== $pc_thumbnail) : ?>
<style>
@media screen and (max-width: 767px) {
    #<?php echo esc_attr($unique_id); ?>.page-hero__image img {
        content: url(<?php echo esc_url($sp_thumbnail); ?>);
    }
}
</style>
<?php endif; ?>
<?php
/**
 * スクール下層ページ用ヒーロー
 * - サムネイルあり: 左タイトル + 右画像
 * - サムネイルなし: タイトルを全幅中央
 */
$title = get_the_title();
$thumb_pc = get_the_post_thumbnail_url(get_the_ID(), 'full');
$thumb_sp = '';
if (function_exists('get_field')) {
    $thumb_sp = (string) get_field('sp_thumbnail');
}
$thumb_sp = $thumb_sp !== '' ? $thumb_sp : $thumb_pc;
$has_thumb = !empty($thumb_pc);
?>
<section class="p-school-subpage-hero<?php echo $has_thumb ? ' is-has-thumb' : ' is-no-thumb'; ?>">
  <div class="p-school-subpage-hero__inner">
    <div class="p-school-subpage-hero__title-wrap">
      <h1 class="p-school-subpage-hero__title"><?php echo esc_html($title); ?></h1>
    </div>
    <?php if ($has_thumb) : ?>
      <div class="p-school-subpage-hero__media img-effect">
        <picture class="img-load">
          <?php if (!empty($thumb_sp) && $thumb_sp !== $thumb_pc) : ?>
            <source media="(max-width: 767px)" srcset="<?php echo esc_url($thumb_sp); ?>">
          <?php endif; ?>
          <img src="<?php echo esc_url($thumb_pc); ?>" alt="<?php echo esc_attr($title); ?>">
        </picture>
      </div>
    <?php endif; ?>
  </div>
</section>

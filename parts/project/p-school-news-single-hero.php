<?php
/**
 * スクールお知らせ・詳細ヒーロー（サムネイルなし）
 */
$title = get_the_title();
?>
<section class="p-school-subpage-hero is-no-thumb p-school-news-single__hero" aria-labelledby="p-school-news-single-hero-title">
  <div class="p-school-subpage-hero__inner">
    <div class="p-school-subpage-hero__title-wrap">
      <div class="p-school-subpage-hero__title-stack">
        <h1 id="p-school-news-single-hero-title" class="p-school-subpage-hero__title"><?php echo esc_html($title); ?></h1>
        <time class="p-school-news-single__hero-date" datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date('Y.m.d')); ?></time>
      </div>
    </div>
  </div>
</section>

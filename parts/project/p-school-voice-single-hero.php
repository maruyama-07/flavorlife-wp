<?php
/**
 * 受講生の声・詳細ヒーロー
 * 常にサムネイルなしレイアウト（タイトル全幅・ベージュ背景）
 */
$title = get_the_title();
?>
<section class="p-school-subpage-hero is-no-thumb p-school-voice-single__hero" aria-labelledby="p-school-voice-single-hero-title">
  <div class="p-school-subpage-hero__inner">
    <div class="p-school-subpage-hero__title-wrap">
      <div class="p-school-subpage-hero__title-stack">
        <p class="p-school-voice-single__hero-label">受講生の声</p>
        <h1 id="p-school-voice-single-hero-title" class="p-school-subpage-hero__title"><?php echo esc_html($title); ?></h1>
      </div>
    </div>
  </div>
</section>

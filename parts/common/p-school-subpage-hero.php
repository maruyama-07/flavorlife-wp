<?php
/**
 * スクール下層ページ用ヒーロー
 * - サムネイルあり: 左タイトル + 右画像
 * - サムネイルなし: タイトルを全幅中央
 */
$title = get_the_title();
if (isset($title_override) && is_string($title_override) && $title_override !== '') {
    $title = $title_override;
} elseif (function_exists('school_section_get_news_page_id') && is_page()) {
    $news_page_id = school_section_get_news_page_id();
    if ($news_page_id && (int) get_queried_object_id() === $news_page_id) {
        $title = 'シーズナルトピックス';
    }
}
$thumb_pc = get_the_post_thumbnail_url(get_the_ID(), 'full');
$thumb_sp = '';
if (function_exists('get_field')) {
    $thumb_sp = (string) get_field('sp_thumbnail');
}
$thumb_sp = $thumb_sp !== '' ? $thumb_sp : $thumb_pc;
$has_thumb = !empty($thumb_pc);
/** 講座詳細は一覧カード用サムネのみで、ヒーローは常に is-no-thumb に統一 */
if (is_singular('course_school')) {
    $has_thumb = false;
}

$course_category_labels = array();
if (is_singular('course_school')) {
    $course_terms = get_the_terms(get_the_ID(), 'course_school_category');
    if (is_array($course_terms)) {
        foreach ($course_terms as $ct) {
            if ($ct instanceof WP_Term) {
                $course_category_labels[] = $ct->name;
            }
        }
    }
}
?>
<section class="p-school-subpage-hero<?php echo $has_thumb ? ' is-has-thumb' : ' is-no-thumb'; ?>">
  <div class="p-school-subpage-hero__inner">
    <div class="p-school-subpage-hero__title-wrap">
      <div class="p-school-subpage-hero__title-stack">
        <h1 class="p-school-subpage-hero__title"><?php echo esc_html($title); ?></h1>
        <?php if ($course_category_labels !== array()) : ?>
        <p class="p-school-subpage-hero__categories"><?php echo esc_html(implode(' ・ ', $course_category_labels)); ?></p>
        <?php endif; ?>
      </div>
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

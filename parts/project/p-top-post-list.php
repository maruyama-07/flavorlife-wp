<?php
$post_type = 'post';
$post_type_data = get_post_type_object($post_type);
$post_type_label = $post_type_data->labels->name;
$link_post_type = 'event'; // 固定ページのパーマリンクを設定する
if ( wp_is_mobile() ) {
  $posts_per_page = 2;
} else {
  $posts_per_page = 4;
}

$args = array(
  'post_type' => $post_type,
  'posts_per_page' => $posts_per_page,
  'order' => 'DESC', //降順
  'orderby' => 'date', //日付で並び替える
);
?>
<section class="p-post-list">
  <div class="p-post-list__wrap">
    <div class="p-post-list__inner l-inner">
      <h2 class="p-post-list__title"><?php echo $post_type_label; ?></h2>
      <div class="p-post-list__wrap">
        <ul class="p-post-list__items">
          <?php get_template_part('parts/post/p-post-list-subloop', null, $args); ?>
        </ul>
      </div>
      <div class="p-post-list__btn">
        <a class="c-btn" href="<?php echo esc_url(home_url($link_post_type)); ?>">詳しく見る</a>
      </div>
    </div>
  </div>
</section>

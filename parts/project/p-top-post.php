<?php
global $slider_library;
$post_type = 'post';
$post_type_data = get_post_type_object($post_type);
$post_type_label = $post_type_data->labels->name;
?>
<div class="p-top-post">
  <div class="p-top-post__inner">
    <h2 class="c-common-title">EVENT</h2>
    <!-- 投稿記事のラベルをタイトルにする場合は、以下を使用します -->
    <!-- <h2 class="c-common-title"><?php //echo $post_type_label; ?></h2> -->
    <?php
    $args = array(
      'post_type' => $post_type,
      'posts_per_page' => -1, // 取得する投稿数を設定（−1は無制限）
      'order' => 'DESC', //並び順を指定
      'orderby' => 'date',  // 並び変える項目を設定
    );

    if ($slider_library === 'swiper') {
      get_template_part('parts/common/p-swiper-post', null, $args);
    } elseif ($slider_library === 'slick') {
      get_template_part('parts/common/p-slick-post', null, $args);
    } else {
      get_template_part('parts/common/p-splide-post', null, $args);
    }
    ?>
  </div>
</div>
<?php
?>

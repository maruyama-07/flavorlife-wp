<div class="p-top-gallery">
  <div class="p-top-gallery__inner">
    <h2 class="c-common-title">gallery（Slider Advanced Custom Field）</h2>
    <?php
    $args = array(
      'post_type' => 'gallery', //カスタム投稿タイプを設定
      'posts_per_page' => -1, // 取得する投稿数を設定（−1は無制限）
      'order' => 'DESC', //並び順を指定
      'orderby' => 'date',  // 並び変える項目を設定
    );

    global $slider_library;
    if ($slider_library === 'swiper') {
      get_template_part('parts/common/p-swiper', null, $args);
    } elseif ($slider_library === 'slick') {
      get_template_part('parts/common/p-slick', null, $args);
    } else {
      get_template_part('parts/common/p-splide', null, $args);
    }

    ?>
  </div>
</div>


<div class="p-top-mv-swiper">
  <div class="p-top-mv-swiper__inner">
    <h2 class="c-common-title">MV（Slider Advanced Custom Field）</h2>
    <?php
    $args = array(
      'post_type' => 'mv_slider',//カスタム投稿タイプを設定
      'posts_per_page' => -1, // 取得する投稿数を設定（−1は無制限）
      'order' => 'DESC', //並び順を指定
      'orderby' => 'date',  // 並び変える項目を設定
    );
    get_template_part('parts/common/p-swiper', null, $args);
    ?>
  </div>
</div>

<div class="l-post-connect p-post-connect">
  <div class="p-post-connect__inner l-inner">
    <p class="p-post-connect__title">関連記事</p>
    <?php
      $current_post_type = get_post_type(get_the_ID());
      $current_category = get_the_category();
      $args = array(
        'post_type' => array($current_post_type),
        'posts_per_page' => 4, //取得する件数
        'post__not_in' => array($post->ID), //現在の記事は含めない
        'order'  => 'DESC',
        'orderby' => 'date', //日付で並び替える
        // 'cat' => $current_category[0]->term_id,
      );
      // カスタム投稿でカテゴリを設定しない場合に備えて別途定義
      if (!empty($current_category)) {
        $args['cat'] = $current_category[0]->term_id;
      }
      $the_query = new WP_Query($args);
    ?>
    <ul class="p-post-connect__items p-card-list">
      <!-- ループ -->
      <?php if ($the_query->have_posts()) : ?>
        <?php while ($the_query->have_posts()) : ?>
          <?php
          $the_query->the_post();
          ?>
          <?php get_template_part('parts/post/p-post-card'); ?>
        <?php endwhile;
        ?>
      <?php else : ?>
        <li>該当の記事はありません</li>
      <?php endif; ?>
    </ul>
  </div>
</div>

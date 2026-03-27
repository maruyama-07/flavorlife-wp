<!-- スライダーのコンテナを作成 -->
<div id="<?php echo esc_attr($args['post_type']); ?>" class="splide">
  <div class="splide__track">
    <div class="splide__list">
      <?php
      // 新規WP_Queryオブジェクトを作成し、クエリを実行
      $the_query = new WP_Query($args);
      if ($the_query->have_posts()) : // 投稿がある場合
        while ($the_query->have_posts()) : $the_query->the_post(); // 投稿ループの開始
          // 各フィールドの情報を取得
          if (has_post_thumbnail()) : ?>
            <div class="splide__slide">
              <a href="<?php the_permalink(); ?>">
                <?php the_post_thumbnail(); ?>
                <h3 class="c-contents-title">
                  <?php the_title(); ?>
                </h3>
                <?php if (get_post_type() == 'works') : ?>
                  <time class="c-contents-date" datetime="<?php the_time('Y-m-d'); ?>"><?php the_time('Y/m/d'); ?></time>
                <?php endif; ?>
              </a>
            </div>
      <?php
          endif;
        endwhile;
      endif;
      wp_reset_postdata(); // クエリをリセット
      ?>
    </div>
  </div>
</div>

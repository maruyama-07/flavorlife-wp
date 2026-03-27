<!-- スライダーのコンテナを作成 -->
<div id="<?php echo esc_attr($args['post_type']); ?>" class="swiper">
  <div class="swiper-wrapper">
    <?php
    // 新規WP_Queryオブジェクトを作成し、クエリを実行
    $the_query = new WP_Query($args);
    if ($the_query->have_posts()) : // 投稿がある場合
      while ($the_query->have_posts()) : $the_query->the_post(); // 投稿ループの開始
        // 各フィールドの情報を取得
        $image_pc_id = get_post_meta(get_the_ID(), 'slide_img_pc', true);
        $image_pc = wp_get_attachment_image_src($image_pc_id, 'large');
        //$image_pcが配列であることを確認し、配列の場合、[0]番目の要素（画像のURL）を取得、そうでない場合、""とする
        $image_pc_src = is_array($image_pc) ? $image_pc[0] : '';
        $image_sp_id = get_post_meta(get_the_ID(), 'slide_img_sp', true);
        $image_sp = wp_get_attachment_image_src($image_sp_id, 'large');
        //$image_spが配列であることを確認し、配列の場合、[0]番目の要素（画像のURL）を取得、そうでない場合、$image_pc_srcとする
        $image_sp_src = is_array($image_sp) ? $image_sp[0] : $image_pc_src;

        $image_alt = get_post_meta(get_the_ID(), 'slide_img_alt', true);
        $image_url = get_post_meta(get_the_ID(), 'slide_img_url', true);
        $image_tab = get_post_meta(get_the_ID(), 'slide_img_tab', true) == 1 ? '_blank' : '_self'; // 新規タブで開くかどうかを指定
        // リンクが設定されている場合、a要素を作成。そうでない場合、div要素を作成
        if (!empty($image_pc_src)) :
          $tag = !empty($image_url) ? 'a' : 'div';
          $href = !empty($image_url) ? ' href="' . esc_url($image_url) . '" target="' . esc_attr($image_tab) . '" rel="noreferrer noopener"' : '';
    ?>
          <<?php echo $tag . $href; ?> class="swiper-slide">
            <picture>
              <!-- スマホ用画像とPC用画像を指定 -->
              <source media="(max-width: 767px)" srcset="<?php echo esc_url($image_sp_src); ?>">
              <img src="<?php echo esc_url($image_pc_src); ?>" alt="<?php echo esc_attr($image_alt); ?>" loading="lazy">
            </picture>

            <!-- 以下は、image_textを使用する場合使います -->
            <?php //if (!empty($image_text)) :
            ?>
            <!-- <p><?php //echo nl2br(esc_html($image_text));
                    ?></p> -->
            <?php //endif;
            ?>

            <!-- リンクが設定されている場合、a要素を閉じる。そうでない場合、div要素を閉じる -->
          </<?php echo $tag; ?>>
    <?php
        endif;
      endwhile;
    endif;
    wp_reset_postdata(); // クエリをリセット
    ?>
  </div>
  <div class="swiper-pagination"></div>
  <div class="swiper-button-prev"></div>
  <div class="swiper-button-next"></div>
</div>

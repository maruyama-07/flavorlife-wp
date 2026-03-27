<div class="p-top-mv-splide">
    <div class="p-top-mv-splide__inner">
        <h2 style="display: none;" class=" c-common-title">MV（Slider Advanced Custom Field）</h2>
        <?php
    $args = array(
      'post_type' => 'mv_slider',//カスタム投稿タイプを設定
      'posts_per_page' => -1, // 取得する投稿数を設定（−1は無制限）
      'order' => 'ASC', //並び順を指定（古い順）
      'orderby' => 'date',  // 並び変える項目を設定
      'img_effect' => true, // 画像ロード時のふわっとアニメーション
    );
    get_template_part('parts/common/p-splide' ,null , $args);
    ?>

        <!-- TOPICS最新記事 -->
        <?php
    $topics_args = array(
      'post_type' => 'topics',
      'posts_per_page' => 1,
      'orderby' => 'date',
      'order' => 'DESC'
    );
    $topics_query = new WP_Query($topics_args);
    
    if ($topics_query->have_posts()) :
      while ($topics_query->have_posts()) : $topics_query->the_post();
        $link_url = get_field('topics_custom_link') ?: get_permalink();
        $new_tab = (bool) get_field('topics_new_tab');
      ?>
        <div class="p-top-mv-topics">
            <h2 class="p-top-mv-topics__label">TOPICS</h2>
            <a href="<?php echo esc_url($link_url); ?>" class="p-top-mv-topics__link"<?php echo $new_tab ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>>
                <h3 class="p-top-mv-topics__text">
                    <?php echo esc_html(get_the_title()); ?>
                </h3>
                <!-- 矢印 -->
                <span class="c-btn-arrow">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="12" cy="12" r="11.5" stroke="currentColor" fill="#0f2b0f" />
                        <path d="M10 8L14 12L10 16" stroke="#fff" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </span>
            </a>
        </div>
        <?php
      endwhile;
      wp_reset_postdata();
    endif;
    ?>
    </div>
</div>
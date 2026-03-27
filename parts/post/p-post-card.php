<!-- カード -->
<li class="p-post-card">
  <a href="<?php the_permalink(); ?>" class="p-post-card__link">
    <?php
    if (has_post_thumbnail()) {
      $thumbID = get_post_thumbnail_id($post->ID);
      $thumbAlt = get_post_meta($thumbID, '_wp_attachment_image_alt', true);
      $image_url = get_the_post_thumbnail_url($post->ID, 'large');
    } else {
      $image_url = get_template_directory_uri() . '/assets/images/common/template.jpg';
      $thumbAlt = "";
    }
    ?>
    <div class="p-post-card__body">
      <!-- 投稿が7日以内であるか判定 -->
      <?php if (new_posting()) : ?>
        <div class="p-post-card__new">NEW</div>
      <?php endif; ?>
      <figure class="p-post-card__img">
        <img src="<?php echo $image_url; ?>" alt="<?php echo $thumbAlt; ?>" loading="lazy">
      </figure>
      <div class="p-post-card__info">
        <?php
        $categories = get_the_category();
        // カスタム投稿の場合 タクソノミー名を指定する必要あり
        // $categories = get_the_terms(get_the_ID(), 'area');
        if (!empty($categories)) :
        ?>
          <ul class="p-post-card__category">
            <li><?php echo esc_html($categories[0]->name); ?></li>
          </ul>
        <?php
        endif;
        ?>
        <div class="p-post-card__date">
          <!-- 投稿日 -->
          <time datetime="<?php the_time('Y-m-d'); ?>">
            <?php the_time('Y.m.d'); ?>
          </time>
        </div>
      </div>
      <div class="p-post-card__title">
        <?php the_title(); ?>
      </div>
    </div>
  </a>
</li>

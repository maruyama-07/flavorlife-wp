<?php
  $post_type = get_query_var('post_type') ? get_query_var('post_type') : 'post';
  $post_type_data = get_post_type_object($post_type);
  $post_type_label = $post_type_data->labels->name;
?>
<main>
  <section class="p-post-list">
    <div class="p-post-list__wrap">
      <div class="p-post-list__inner l-inner">
        <h1 class="p-post-list__title"><?php echo $post_type_label; ?></h1>
        <?php if (function_exists('bcn_display')) : ?>
          <div class="p-post-list__breadcrumb c-breadcrumb" typeof="BreadcrumbList" vocab="http://schema.org/">
            <?php bcn_display(); ?>
          </div>
        <?php endif; ?>
        <!-- ブログカードリスト -->
        <ul class="p-post-list__items">
          <?php if (have_posts()) : ?>
            <?php while (have_posts()) : the_post(); ?>
              <?php get_template_part('parts/post/p-post-card'); ?>
            <?php endwhile; ?>
          <?php else : ?>
            <li class="p-post-list__not-found">該当の記事はありません。</li>
          <?php endif; ?>
        </ul>
        <?php get_template_part('parts/common/p-pager'); ?>

      </div>
    </div>
  </section>
</main>

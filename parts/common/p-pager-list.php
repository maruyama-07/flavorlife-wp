<div class="p-pager-list">
  <div class="p-pager-list__btn">
    <?php next_post_link('%link', '&lt; 前の記事'); ?>
  </div>
  <div class="p-pager-list__btn">
    <?php
    $post_type = get_post_type();
    $post_type_data = get_post_type_object($post_type);
    $post_type_label = $post_type_data->labels->name;
    $link_post_type = ($post_type == 'post') ? 'event' : $post_type;
    ?>
    <a href="<?php echo esc_url(home_url($link_post_type)); ?>"><?php echo $post_type_label; ?>一覧</a>
  </div>
  <div class="p-pager-list__btn">
    <?php previous_post_link('%link', '次の記事 &gt;'); ?>
  </div>
</div>

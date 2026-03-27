<div class="l-pager">
  <?php
  if (function_exists('wp_pagenavi')) {
      $pagenavi_output = wp_pagenavi(array(
          'echo'    => false,
          'options' => array(
              'pages_text'    => '',
              'current_text' => '%PAGE_NUMBER%',
              'page_text'     => '%PAGE_NUMBER%',
              'first_text'    => '',
              'last_text'     => '',
              'prev_text'     => '&lt; Back',
              'next_text'     => 'Next &gt;',
              'dotleft_text'  => '',
              'dotright_text' => '',
          ),
      ));
      if ($pagenavi_output) {
          echo $pagenavi_output;
      } else {
          // 1ページのみの場合: 両方無効で表示
          $query = $GLOBALS['wp_query'];
          if ($query->max_num_pages <= 1) {
              echo '<div class="wp-pagenavi" role="navigation">';
              echo '<span class="previouspostslink disabled">&lt; Back</span>';
              echo '<span class="current">1</span>';
              echo '<span class="nextpostslink disabled">Next &gt;</span>';
              echo '</div>';
          }
      }
  } else {
      the_posts_pagination(array(
          'mid_size'  => 1,
          'prev_next' => true,
          'prev_text' => __('&lt; Back'),
          'next_text' => __('Next &gt;'),
          'type'      => 'list',
      ));
  }
  ?>
</div>

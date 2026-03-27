<div class="p-school-mv-splide">
    <div class="p-school-mv-splide__inner">
        <h2 style="display: none;">MV（スクール）</h2>
        <?php
        $args = array(
          'post_type' => 'mv_slider_school',
          'posts_per_page' => -1,
          'orderby' => 'date',
          'order' => 'ASC',
          'post_status' => 'publish',
          'no_found_rows' => true,
          'suppress_filters' => true,
          'img_effect' => true,
        );
        get_template_part('parts/common/p-splide', null, $args);
        ?>
    </div>
</div>

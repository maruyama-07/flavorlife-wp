<?php
/**
 * Topicsアーカイブテンプレート
 * URL: /topics/
 */
get_header();

get_template_part('parts/archive/p-archive-list', null, array(
    'title'    => 'TOPICS',
    'subtitle' => 'トピックス',
));

get_footer();

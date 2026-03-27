<?php
/**
 * Blogアーカイブテンプレート
 * URL: /blog/
 */
get_header();

get_template_part('parts/archive/p-archive-list', null, array(
    'title'    => 'BLOG',
    'subtitle' => '社長ブログ',
));

get_footer();

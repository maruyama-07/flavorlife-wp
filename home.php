<?php
/**
 * Newsアーカイブテンプレート（投稿一覧ページ）
 * 表示設定で「投稿ページ」に指定した固定ページのURLで表示
 */
get_header();

get_template_part('parts/archive/p-archive-list', null, array(
    'title'    => 'News',
    'subtitle' => 'お知らせ',
));

get_footer();

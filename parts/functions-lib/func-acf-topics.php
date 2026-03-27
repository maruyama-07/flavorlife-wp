<?php
/**
 * Topics投稿用ACFフィールド
 * TOPページMVのTOPICS表示で、記事ページ以外へのリンクを設定可能
 */

if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
        'key' => 'group_topics_mv_link',
        'title' => 'TOPICS リンク設定',
        'fields' => array(
            array(
                'key' => 'field_topics_custom_link',
                'label' => 'カスタムリンクURL',
                'name' => 'topics_custom_link',
                'type' => 'url',
                'instructions' => '設定すると、TOPページMVのTOPICS表示で記事ページの代わりにこのURLへリンクします。内部ページ・外部URLどちらも指定可能。未設定の場合は記事ページへリンクします。',
            ),
            array(
                'key' => 'field_topics_new_tab',
                'label' => '新規タブで開く',
                'name' => 'topics_new_tab',
                'type' => 'true_false',
                'instructions' => 'チェックすると、リンクを新規タブで開きます',
                'default_value' => 0,
                'ui' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'topics',
                ),
            ),
        ),
    ));
}
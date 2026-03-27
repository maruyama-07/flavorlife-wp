<?php
/**
 * カスタムメニューWalkerクラス
 * メニューの出力形式をカスタマイズ
 */

// ヘッダーメニュー用Walker（PC時サブメニュー対応）
class Custom_Walker_Nav_Menu extends Walker_Nav_Menu {

    /**
     * サブメニューの開始
     */
    function start_lvl(&$output, $depth = 0, $args = array()) {
        if ($depth === 0) {
            $output .= '<ul class="l-header__submenu">';
        } else {
            $output .= '<ul class="l-header__submenu l-header__submenu--nested">';
        }
    }

    /**
     * サブメニューの終了
     */
    function end_lvl(&$output, $depth = 0, $args = array()) {
        $output .= '</ul>';
    }

    /**
     * メニュー項目の開始タグ
     */
    function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {
        $classes = empty($item->classes) ? array() : (array) $item->classes;
        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item));

        if ($depth === 0) {
            $output .= '<li class="l-header__item ' . esc_attr($class_names) . '">';
            $attributes = '';
            $attributes .= !empty($item->url) ? ' href="' . esc_attr($item->url) . '"' : '';
            $attributes .= !empty($item->target) ? ' target="' . esc_attr($item->target) . '"' : '';
            $output .= '<a class="l-header__item-link"' . $attributes . '>';
            $output .= apply_filters('the_title', $item->title, $item->ID);
            $output .= '</a>';
        } else {
            $output .= '<li class="l-header__submenu-item">';
            $attributes = '';
            $attributes .= !empty($item->url) ? ' href="' . esc_attr($item->url) . '"' : '';
            $attributes .= !empty($item->target) ? ' target="' . esc_attr($item->target) . '"' : '';
            $output .= '<a class="l-header__submenu-link"' . $attributes . '>';
            $output .= apply_filters('the_title', $item->title, $item->ID);
            $output .= '</a>';
        }
    }

    /**
     * メニュー項目の終了タグ
     */
    function end_el(&$output, $item, $depth = 0, $args = array()) {
        $output .= '</li>';
    }
}

// モバイルオーバーレイメニュー用Walker
class Mobile_Walker_Nav_Menu extends Walker_Nav_Menu {

    /**
     * Contact項目かどうか判定（News+Contact統合カラムに差し替える）
     */
    private function is_contact_item($item) {
        $contact_url = home_url('/contact');
        return stripos($item->url, 'contact') !== false
            || $item->title === 'Contact'
            || $item->title === 'お問い合わせ';
    }

    function start_lvl(&$output, $depth = 0, $args = array()) {
        if ($depth === 0) {
            $output .= '<ul class="p-mobile-nav__children">';
        }
    }

    function end_lvl(&$output, $depth = 0, $args = array()) {
        if ($depth === 0) {
            $output .= '</ul>';
        }
    }

    function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {
        if ($depth === 0 && $this->is_contact_item($item)) {
            // Contactカラムを News + Contact（お問い合わせサブ）に差し替え
            $news_url = get_permalink(get_option('page_for_posts')) ?: home_url('/news/');
            $contact_url = !empty($item->url) ? $item->url : home_url('/contact');
            $output .= '<div class="p-mobile-nav__column p-mobile-nav__column--news-contact p-mobile-nav__column--has-children">';
            $output .= '<div class="p-mobile-nav__parent-row">';
            $output .= '<a href="' . esc_url($news_url) . '" class="p-mobile-nav__parent p-mobile-nav__parent--toggle">News</a>';
            $output .= '<button type="button" class="p-mobile-nav__toggle js-mobile-nav-toggle" aria-expanded="false" aria-label="サブメニューを開く">';
            $output .= '<span class="p-mobile-nav__toggle-icon"></span>';
            $output .= '</button>';
            $output .= '</div>';
            $output .= '<hr class="p-mobile-nav__line">';
            $output .= '<ul class="p-mobile-nav__children">';
            $output .= '<li class="p-mobile-nav__child"><a href="' . esc_url($news_url) . '" class="p-mobile-nav__child-link">お知らせ</a></li>';
            $output .= '</ul>';
            $output .= '<div class="p-mobile-nav__parent-row">';
            $output .= '<a href="' . esc_url($contact_url) . '" class="p-mobile-nav__parent p-mobile-nav__parent--toggle">Contact</a>';
            $output .= '<button type="button" class="p-mobile-nav__toggle js-mobile-nav-toggle" aria-expanded="false" aria-label="サブメニューを開く">';
            $output .= '<span class="p-mobile-nav__toggle-icon"></span>';
            $output .= '</button>';
            $output .= '</div>';
            $output .= '<hr class="p-mobile-nav__line">';
            $output .= '<ul class="p-mobile-nav__children">';
            $output .= '<li class="p-mobile-nav__child"><a href="' . esc_url($contact_url) . '" class="p-mobile-nav__child-link">お問い合わせ</a></li>';
            $output .= '</ul>';
            return;
        }
        if ($depth === 0) {
            $has_children = in_array('menu-item-has-children', (array) $item->classes);
            $column_class = 'p-mobile-nav__column' . ($has_children ? ' p-mobile-nav__column--has-children' : '');
            $output .= '<div class="' . esc_attr($column_class) . '">';
            $attributes = !empty($item->url) ? ' href="' . esc_attr($item->url) . '"' : ' href="#"';
            $attributes .= !empty($item->target) ? ' target="' . esc_attr($item->target) . '"' : '';
            if ($has_children) {
                $output .= '<div class="p-mobile-nav__parent-row">';
                $output .= '<a class="p-mobile-nav__parent p-mobile-nav__parent--toggle"' . $attributes . '>';
                $output .= esc_html($item->title);
                $output .= '</a>';
                $output .= '<button type="button" class="p-mobile-nav__toggle js-mobile-nav-toggle" aria-expanded="false" aria-label="サブメニューを開く">';
                $output .= '<span class="p-mobile-nav__toggle-icon"></span>';
                $output .= '</button>';
                $output .= '</div>';
            } else {
                $output .= '<a class="p-mobile-nav__parent"' . $attributes . '>';
                $output .= esc_html($item->title);
                $output .= '</a>';
            }
            $output .= '<hr class="p-mobile-nav__line">';
        } else {
            $attributes = !empty($item->url) ? ' href="' . esc_attr($item->url) . '"' : ' href="#"';
            $attributes .= !empty($item->target) ? ' target="' . esc_attr($item->target) . '"' : '';
            $output .= '<li class="p-mobile-nav__child"><a class="p-mobile-nav__child-link"' . $attributes . '>' . esc_html($item->title) . '</a></li>';
        }
    }

    function end_el(&$output, $item, $depth = 0, $args = array()) {
        if ($depth === 0) {
            $output .= '</div>';
        } else {
            $output .= '</li>';
        }
    }
}

// フッターメニュー用Walker（従来のフラットリスト用・互換）
class Footer_Walker_Nav_Menu extends Walker_Nav_Menu {
    function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {
        $classes = empty($item->classes) ? array() : (array) $item->classes;
        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item));
        $output .= '<li class="l-footer__item ' . esc_attr($class_names) . '">';
        $attributes = '';
        $attributes .= !empty($item->url) ? ' href="' . esc_attr($item->url) . '"' : '';
        $attributes .= !empty($item->target) ? ' target="' . esc_attr($item->target) . '"' : '';
        $output .= '<a class="l-footer__item-link"' . $attributes . '>';
        $output .= apply_filters('the_title', $item->title, $item->ID);
        $output .= '</a>';
    }
    function end_el(&$output, $item, $depth = 0, $args = array()) {
        $output .= '</li>';
    }
}

/**
 * フッターカラム用Walker
 * p-mobile-nav__menu と同じ仕様（SP時アコーディオン開閉）
 * PC時はグリッド、SP時は親＋トグル＋子のアコーディオン構造
 */
class Footer_Columns_Walker_Nav_Menu extends Walker_Nav_Menu {

    /**
     * Contact項目かどうか判定（News+Contact統合カラムに差し替える）
     */
    private function is_contact_item($item) {
        return stripos($item->url, 'contact') !== false
            || $item->title === 'Contact'
            || $item->title === 'お問い合わせ';
    }

    /**
     * Contact項目の子は表示しない（News+Contactで差し替えるため）
     */
    function display_element($element, &$children_elements, $max_depth, $depth, $args, &$output) {
        if ($depth === 0 && $this->is_contact_item($element)) {
            $id_field = $this->db_fields['id'];
            $id = $element->$id_field;
            if (!empty($children_elements[$id])) {
                unset($children_elements[$id]);
            }
        }
        parent::display_element($element, $children_elements, $max_depth, $depth, $args, $output);
    }

    function start_lvl(&$output, $depth = 0, $args = array()) {
        if ($depth === 0) {
            $output .= '<ul class="p-mobile-nav__children l-footer__children">';
        }
    }

    function end_lvl(&$output, $depth = 0, $args = array()) {
        if ($depth === 0) {
            $output .= '</ul>';
        }
    }

    function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {
        $attributes = '';
        $attributes .= !empty($item->url) ? ' href="' . esc_attr($item->url) . '"' : '';
        $attributes .= !empty($item->target) ? ' target="' . esc_attr($item->target) . '"' : '';

        if ($depth === 0 && $this->is_contact_item($item)) {
            // Contactカラムを News + Contact（お知らせ・お問い合わせ）に差し替え
            $news_url = get_permalink(get_option('page_for_posts')) ?: home_url('/news/');
            $contact_url = !empty($item->url) ? $item->url : home_url('/contact');
            $output .= '<div class="l-footer__column p-mobile-nav__column p-mobile-nav__column--news-contact p-mobile-nav__column--has-children">';
            $output .= '<div class="p-mobile-nav__parent-row l-footer__parent-row">';
            $output .= '<a href="' . esc_url($news_url) . '" class="p-mobile-nav__parent p-mobile-nav__parent--toggle l-footer__parent">News</a>';
            $output .= '<button type="button" class="p-mobile-nav__toggle js-mobile-nav-toggle js-footer-accordion-toggle" aria-expanded="false" aria-label="サブメニューを開く">';
            $output .= '<span class="p-mobile-nav__toggle-icon l-footer__toggle-icon"></span>';
            $output .= '</button>';
            $output .= '</div>';
            $output .= '<hr class="p-mobile-nav__line l-footer__line">';
            $output .= '<ul class="p-mobile-nav__children l-footer__children">';
            $output .= '<li class="p-mobile-nav__child"><a href="' . esc_url($news_url) . '" class="p-mobile-nav__child-link l-footer__child-link">お知らせ</a></li>';
            $output .= '</ul>';
            $output .= '<hr class="p-mobile-nav__line l-footer__line">';
            $output .= '<div class="p-mobile-nav__parent-row l-footer__parent-row">';
            $output .= '<a href="' . esc_url($contact_url) . '" class="p-mobile-nav__parent p-mobile-nav__parent--toggle l-footer__parent">Contact</a>';
            $output .= '<button type="button" class="p-mobile-nav__toggle js-mobile-nav-toggle js-footer-accordion-toggle" aria-expanded="false" aria-label="サブメニューを開く">';
            $output .= '<span class="p-mobile-nav__toggle-icon l-footer__toggle-icon"></span>';
            $output .= '</button>';
            $output .= '</div>';
            $output .= '<hr class="p-mobile-nav__line l-footer__line">';
            $output .= '<ul class="p-mobile-nav__children l-footer__children">';
            $output .= '<li class="p-mobile-nav__child"><a href="' . esc_url($contact_url) . '" class="p-mobile-nav__child-link l-footer__child-link">お問い合わせ</a></li>';
            $output .= '</ul>';
            $output .= '</div>';
            return;
        }

        if ($depth === 0) {
            $has_children = in_array('menu-item-has-children', (array) $item->classes);
            $column_class = 'l-footer__column p-mobile-nav__column' . ($has_children ? ' p-mobile-nav__column--has-children' : '');
            $output .= '<div class="' . esc_attr($column_class) . '">';
            $attributes_attr = !empty($item->url) ? ' href="' . esc_attr($item->url) . '"' : ' href="#"';
            $attributes_attr .= !empty($item->target) ? ' target="' . esc_attr($item->target) . '"' : '';
            if ($has_children) {
                $output .= '<div class="p-mobile-nav__parent-row l-footer__parent-row">';
                $output .= '<a class="p-mobile-nav__parent p-mobile-nav__parent--toggle l-footer__parent"' . $attributes_attr . '>';
                $output .= esc_html($item->title);
                $output .= '</a>';
                $output .= '<button type="button" class="p-mobile-nav__toggle js-mobile-nav-toggle js-footer-accordion-toggle" aria-expanded="false" aria-label="サブメニューを開く">';
                $output .= '<span class="p-mobile-nav__toggle-icon l-footer__toggle-icon"></span>';
                $output .= '</button>';
                $output .= '</div>';
                $output .= '<hr class="p-mobile-nav__line l-footer__line">';
            } else {
                $output .= '<div class="p-mobile-nav__parent-row l-footer__parent-row l-footer__parent-row--no-toggle">';
                $output .= '<a class="p-mobile-nav__parent l-footer__parent"' . $attributes_attr . '>';
                $output .= esc_html($item->title);
                $output .= '</a>';
                $output .= '</div>';
                $output .= '<hr class="p-mobile-nav__line l-footer__line">';
            }
        } else {
            $output .= '<li class="p-mobile-nav__child"><a class="p-mobile-nav__child-link l-footer__child-link"' . $attributes . '>' . esc_html($item->title) . '</a></li>';
        }
    }

    function end_el(&$output, $item, $depth = 0, $args = array()) {
        if ($depth === 0 && !$this->is_contact_item($item)) {
            $output .= '</div>';
        }
    }
}

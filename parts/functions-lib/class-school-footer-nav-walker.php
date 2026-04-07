<?php
/**
 * スクールフッター: 1メニューで3列に分割
 * - 第1階層の親 = 1列（タイトル = リンク名・URLは # 推奨）
 * - 第2階層 = その列のリンク
 * - 親に CSS クラス footer-col--no-heading … 見出し <p> を出さない（右列など）
 * - 親に footer-col--single … .l-footer-school__column--single を付与
 * - 親ラベルが未編集の既定「メニュー項目」（英語環境は Menu Item）のときも見出しを出さない
 */

if (!class_exists('School_Footer_Nav_Walker')) {
    class School_Footer_Nav_Walker extends Walker_Nav_Menu
    {
        /**
         * 未編集の親ラベル（外観→メニューの既定「メニュー項目」等）のときは見出しを出さない
         *
         * @param string $title
         */
        private function heading_is_placeholder_label($title)
        {
            $t = trim((string) $title);
            if ($t === '') {
                return true;
            }
            if ($t === 'メニュー項目' || $t === 'Menu Item') {
                return true;
            }

            return (bool) apply_filters('school_footer_nav_heading_is_placeholder', false, $t, $title);
        }

        /**
         * @param stdClass $args
         */
        public function start_lvl(&$output, $depth = 0, $args = null)
        {
            if ((int) $depth !== 0) {
                return;
            }
            $output .= '<ul>';
        }

        /**
         * @param stdClass $args
         */
        public function end_lvl(&$output, $depth = 0, $args = null)
        {
            if ((int) $depth !== 0) {
                return;
            }
            $output .= '</ul>';
        }

        /**
         * @param stdClass $args
         */
        public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0)
        {
            if ((int) $depth === 0) {
                $classes = is_array($item->classes) ? $item->classes : array();
                $col     = 'l-footer-school__column';
                if (in_array('footer-col--single', $classes, true)) {
                    $col .= ' l-footer-school__column--single';
                }
                $output .= '<div class="' . esc_attr($col) . '">';
                $no_heading = in_array('footer-col--no-heading', $classes, true);
                if (!$no_heading && !$this->heading_is_placeholder_label((string) $item->title)) {
                    $output .= '<p class="l-footer-school__heading">' . esc_html(trim((string) $item->title)) . '</p>';
                }

                return;
            }

            parent::start_el($output, $item, $depth, $args, $id);
        }

        /**
         * @param stdClass $args
         */
        public function end_el(&$output, $item, $depth = 0, $args = null)
        {
            if ((int) $depth === 0) {
                $output .= '</div>';

                return;
            }

            parent::end_el($output, $item, $depth, $args);
        }
    }
}

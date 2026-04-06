<?php
/**
 * 講師紹介グリッド（school_instructors ショートコード）
 */
$args = isset($args) && is_array($args) ? $args : array();
$title = isset($args['school_about_instructors_title'])
    ? (string) $args['school_about_instructors_title']
    : '講師紹介';
$items = (isset($args['school_about_instructors_items']) && is_array($args['school_about_instructors_items']))
    ? $args['school_about_instructors_items']
    : array();
$has_modal_section = !empty($args['school_about_instructors_has_modal']);
if (empty($items)) {
    return;
}
?>
<section class="p-school-about-instructors" aria-labelledby="school-about-instructors-heading">
    <div class="p-school-about-instructors__inner">
        <h2 id="school-about-instructors-heading" class="p-school-about-instructors__title"><?php echo esc_html($title); ?></h2>
        <div class="p-school-about-instructors__grid">
            <?php foreach ($items as $row) : ?>
                <?php
                $name    = isset($row['name']) ? (string) $row['name'] : '';
                $url     = isset($row['url']) ? (string) $row['url'] : '';
                $img_url = isset($row['img_url']) ? (string) $row['img_url'] : '';
                $img_alt = isset($row['img_alt']) ? (string) $row['img_alt'] : $name;
                $new_tab = !empty($row['new_tab']);
                $has_link = $url !== '';
                $has_modal = !empty($row['has_modal']);
                $post_id   = isset($row['post_id']) ? (int) $row['post_id'] : 0;
                $furigana  = isset($row['furigana']) ? (string) $row['furigana'] : '';
                $modal_html = isset($row['modal_html']) ? (string) $row['modal_html'] : '';
                $btn_label = $name !== '' ? $name : '詳細を見る';
                ?>
            <?php if ($has_modal && $post_id > 0) : ?>
            <?php
            $modal_aria = $name !== '' ? $name . 'の詳細を開く' : '講師の詳細を開く';
            ?>
            <div
                class="p-school-about-instructors__item p-school-about-instructors__item--modal js-school-instructor-modal-open"
                data-instructor-id="<?php echo esc_attr((string) $post_id); ?>"
                role="button"
                tabindex="0"
                aria-haspopup="dialog"
                aria-label="<?php echo esc_attr($modal_aria); ?>"
            >
                <div class="p-school-about-instructors__media">
                    <?php if ($img_url !== '') : ?>
                    <img class="p-school-about-instructors__img" src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($img_alt); ?>" loading="lazy" decoding="async">
                    <?php else : ?>
                    <div class="p-school-about-instructors__placeholder">
                        <span class="p-school-about-instructors__placeholder-text">追加画像（未支給）</span>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="p-school-about-instructors__btn p-school-about-instructors__btn--modal" aria-hidden="true">
                    <span class="p-school-about-instructors__btn-label"><?php echo esc_html($btn_label); ?></span>
                    <span class="p-school-about-instructors__btn-icon"></span>
                </div>
                <div id="instructor-modal-payload-<?php echo (int) $post_id; ?>" class="p-school-about-instructors__modal-payload" hidden>
                    <div class="p-school-about-instructors__modal-payload-furigana"><?php echo esc_html($furigana); ?></div>
                    <div class="p-school-about-instructors__modal-payload-name"><?php echo esc_html($name); ?></div>
                    <div class="p-school-about-instructors__modal-payload-body">
                        <?php echo $modal_html; ?>
                    </div>
                    <?php if ($has_link) : ?>
                    <div class="p-school-about-instructors__modal-payload-link" data-url="<?php echo esc_url($url); ?>" data-new-tab="<?php echo $new_tab ? '1' : '0'; ?>"></div>
                    <?php endif; ?>
                </div>
            </div>
            <?php elseif ($has_link) : ?>
            <div class="p-school-about-instructors__item">
                <div class="p-school-about-instructors__media">
                    <?php if ($img_url !== '') : ?>
                    <img class="p-school-about-instructors__img" src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($img_alt); ?>" loading="lazy" decoding="async">
                    <?php else : ?>
                    <div class="p-school-about-instructors__placeholder">
                        <span class="p-school-about-instructors__placeholder-text">追加画像（未支給）</span>
                    </div>
                    <?php endif; ?>
                </div>
                <a class="p-school-about-instructors__btn" href="<?php echo esc_url($url); ?>"<?php echo $new_tab ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>>
                    <span class="p-school-about-instructors__btn-label"><?php echo esc_html($name !== '' ? $name : '詳細を見る'); ?></span>
                    <span class="p-school-about-instructors__btn-icon" aria-hidden="true"></span>
                </a>
            </div>
            <?php else : ?>
            <div class="p-school-about-instructors__item">
                <div class="p-school-about-instructors__media">
                    <?php if ($img_url !== '') : ?>
                    <img class="p-school-about-instructors__img" src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($img_alt); ?>" loading="lazy" decoding="async">
                    <?php else : ?>
                    <div class="p-school-about-instructors__placeholder">
                        <span class="p-school-about-instructors__placeholder-text">追加画像（未支給）</span>
                    </div>
                    <?php endif; ?>
                </div>
                <span class="p-school-about-instructors__btn p-school-about-instructors__btn--static"<?php echo $name === '' ? ' aria-hidden="true"' : ''; ?>>
                    <span class="p-school-about-instructors__btn-label"><?php echo esc_html($name !== '' ? $name : '—'); ?></span>
                    <span class="p-school-about-instructors__btn-icon" aria-hidden="true"></span>
                </span>
            </div>
            <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
    <?php if ($has_modal_section) : ?>
    <dialog class="c-school-instructor-modal" id="school-instructor-dialog" aria-labelledby="school-instructor-dialog-name">
        <button type="button" class="c-school-instructor-modal__close js-school-instructor-modal-close" aria-label="閉じる">&times;</button>
        <div class="c-school-instructor-modal__inner">
            <header class="c-school-instructor-modal__header">
                <p class="c-school-instructor-modal__furigana js-school-instructor-modal-furigana" id="school-instructor-dialog-furigana"></p>
                <h3 class="c-school-instructor-modal__name js-school-instructor-modal-name" id="school-instructor-dialog-name"></h3>
            </header>
            <div class="c-school-instructor-modal__body js-school-instructor-modal-body"></div>
            <div class="c-school-instructor-modal__footer js-school-instructor-modal-footer" hidden></div>
        </div>
    </dialog>
    <?php endif; ?>
</section>

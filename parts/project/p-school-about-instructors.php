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
                ?>
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
                <?php if ($has_link) : ?>
                <a class="p-school-about-instructors__btn" href="<?php echo esc_url($url); ?>"<?php echo $new_tab ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>>
                    <span class="p-school-about-instructors__btn-label"><?php echo esc_html($name !== '' ? $name : '詳細を見る'); ?></span>
                    <span class="p-school-about-instructors__btn-icon" aria-hidden="true"></span>
                </a>
                <?php else : ?>
                <span class="p-school-about-instructors__btn p-school-about-instructors__btn--static"<?php echo $name === '' ? ' aria-hidden="true"' : ''; ?>>
                    <span class="p-school-about-instructors__btn-label"><?php echo esc_html($name !== '' ? $name : '—'); ?></span>
                    <span class="p-school-about-instructors__btn-icon" aria-hidden="true"></span>
                </span>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

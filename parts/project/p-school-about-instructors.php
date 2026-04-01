<?php
/**
 * 講師紹介グリッド（school_instructors ショートコード）
 *
 * @var string $school_about_instructors_title
 * @var array  $school_about_instructors_items
 */
$title = isset($school_about_instructors_title) ? (string) $school_about_instructors_title : '講師紹介';
$items = (isset($school_about_instructors_items) && is_array($school_about_instructors_items))
    ? $school_about_instructors_items
    : array();
if (empty($items)) {
    return;
}
?>
<section class="p-school-about-instructors" aria-labelledby="school-about-instructors-heading">
    <div class="p-school-about-instructors__inner">
        <h2 id="school-about-instructors-heading" class="p-school-about-instructors__title"><?php echo esc_html($title); ?></h2>
        <ul class="p-school-about-instructors__grid">
            <?php foreach ($items as $row) : ?>
                <?php
                $name    = isset($row['name']) ? (string) $row['name'] : '';
                $url     = isset($row['url']) ? (string) $row['url'] : '';
                $img_url = isset($row['img_url']) ? (string) $row['img_url'] : '';
                $img_alt = isset($row['img_alt']) ? (string) $row['img_alt'] : $name;
                $has_link = $url !== '';
                ?>
            <li class="p-school-about-instructors__item">
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
                <a class="p-school-about-instructors__btn" href="<?php echo esc_url($url); ?>">
                    <span class="p-school-about-instructors__btn-label"><?php echo esc_html($name !== '' ? $name : '詳細を見る'); ?></span>
                    <span class="p-school-about-instructors__btn-icon" aria-hidden="true">
                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 2L8 6L4 10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                </a>
                <?php else : ?>
                <span class="p-school-about-instructors__btn p-school-about-instructors__btn--static"<?php echo $name === '' ? ' aria-hidden="true"' : ''; ?>>
                    <span class="p-school-about-instructors__btn-label"><?php echo esc_html($name !== '' ? $name : '—'); ?></span>
                    <span class="p-school-about-instructors__btn-icon" aria-hidden="true">
                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 2L8 6L4 10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                </span>
                <?php endif; ?>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>

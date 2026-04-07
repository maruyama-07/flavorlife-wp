<?php
if (!function_exists('school_section_is_queried_root') || !school_section_is_queried_root()) {
    return;
}

$data = function_exists('school_top_get_seasonal_topics_data') ? school_top_get_seasonal_topics_data() : array();
if (empty($data)) {
    return;
}

$title = $data['title'] !== '' ? $data['title'] : 'Seasonal Topics';
$subtitle = $data['subtitle'] !== '' ? $data['subtitle'] : '季節のおすすめ';
$image_url = $data['image_url'] ?? '';
$heading = $data['heading'] ?? '';
$body = $data['body'] ?? '';
$button_text = $data['button_text'] !== '' ? $data['button_text'] : 'more';
$button_url = $data['button_url'] ?? '';
?>
<section class="p-school-seasonal-topics js-animate-content" aria-labelledby="p-school-seasonal-topics-heading">
    <div class="p-school-seasonal-topics__inner">
        <header class="p-school-seasonal-topics__header">
            <h2 id="p-school-seasonal-topics-heading" class="p-school-seasonal-topics__title"><?php echo function_exists('tool_acf_format_field_for_echo') ? tool_acf_format_field_for_echo((string) $title) : esc_html($title); ?></h2>
            <p class="p-school-seasonal-topics__subtitle"><?php echo function_exists('tool_acf_format_field_for_echo') ? tool_acf_format_field_for_echo((string) $subtitle) : esc_html($subtitle); ?></p>
        </header>

        <div class="p-school-seasonal-topics__content">
            <?php if ($image_url !== '') : ?>
            <div class="p-school-seasonal-topics__media">
                <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($heading !== '' ? $heading : $title); ?>" loading="lazy" decoding="async">
            </div>
            <?php endif; ?>

            <div class="p-school-seasonal-topics__body">
                <?php if ($heading !== '') : ?>
                <h3 class="p-school-seasonal-topics__heading">
                    <?php
                    echo function_exists('tool_acf_format_field_for_echo')
                        ? tool_acf_format_field_for_echo((string) $heading)
                        : (function_exists('tool_format_text_with_sp_break')
                            ? tool_format_text_with_sp_break($heading)
                            : nl2br(esc_html((string) $heading)));
                    ?>
                </h3>
                <?php endif; ?>
                <?php if ($body !== '') : ?>
                <p class="p-school-seasonal-topics__text">
                    <?php
                    echo function_exists('tool_acf_format_field_for_echo')
                        ? tool_acf_format_field_for_echo((string) $body)
                        : (function_exists('tool_format_text_with_sp_break')
                            ? tool_format_text_with_sp_break($body)
                            : nl2br(esc_html((string) $body)));
                    ?>
                </p>
                <?php endif; ?>
                <?php if ($button_url !== '') : ?>
                <p class="p-school-seasonal-topics__action">
                    <a href="<?php echo esc_url($button_url); ?>" class="p-school-seasonal-topics__button"><?php echo esc_html($button_text); ?></a>
                </p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

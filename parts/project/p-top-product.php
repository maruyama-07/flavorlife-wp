<?php
/**
 * TOPページ - Productセクション
 * リンク・ロゴ・テキストはトップページの固定ページで管理
 */

$page_id = (int) get_option('page_on_front');
if (!$page_id && is_front_page()) {
    $page_id = get_queried_object_id();
}

$default_logos = array(
    1 => 'sleep-hug-logo.svg',
    6 => 'queenMary-logo.svg',
    7 => 'hanatomi.svg',
    8 => 'alaLehua.svg',
);

$defaults = array(
    array('type' => 'logo', 'text' => 'sleep hug', 'url' => 'https://sleephug.jp/', 'slug' => 'sleephug'),
    array('type' => 'text', 'text' => 'Essential Oils', 'url' => 'https://www.flavorlife.com/shopbrand/essentialoil/', 'slug' => 'essential-oils'),
    array('type' => 'text', 'text' => 'キャリアオイル', 'url' => 'https://www.flavorlife.com/shopbrand/allcarrieroil/', 'slug' => 'carrier-oil'),
    array('type' => 'text', 'text' => 'アロマディフューザー', 'url' => 'https://www.flavorlife.com/shopbrand/aromadiffuser/', 'slug' => 'diffuser'),
    array('type' => 'text', 'text' => 'ハーブティー', 'url' => 'https://www.flavorlife.com/shopbrand/herbtea/', 'slug' => 'herbtea'),
    array('type' => 'logo', 'text' => 'QUEEN MARY', 'url' => 'https://www.flavorlife.com/shopbrand/eoqm/', 'slug' => 'queenmary'),
    array('type' => 'logo', 'text' => 'hana to mi', 'url' => 'https://hana-to-mi.jp/', 'slug' => 'hanatomi'),
    array('type' => 'logo', 'text' => "'ala Lehua", 'url' => 'https://www.shop.alalehua.com/', 'slug' => 'alalehua'),
    array('type' => 'text', 'text' => 'その他', 'url' => '#', 'slug' => 'other'),
    array('type' => 'text', 'text' => '', 'url' => '', 'slug' => ''),
    array('type' => 'text', 'text' => '', 'url' => '', 'slug' => ''),
    array('type' => 'text', 'text' => '', 'url' => '', 'slug' => ''),
);
?>

<section id="product" class="p-top-product">
    <div class="p-top-product__bg">
        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/common/product-back.jpg" alt="Product">
    </div>

    <div class="p-top-product__inner l-inner js-animate-content">
        <!-- ヘッダー -->
        <div class="p-top-product__header">
            <h2 class="p-top-product__title">Product</h2>
            <p class="p-top-product__subtitle">オリジナルブランド</p>
        </div>

        <!-- プロダクトグリッド -->
        <div class="p-top-product__grid">
            <?php for ($i = 1; $i <= 12; $i++) :
                $d = $defaults[$i - 1];
                $acf_available = $page_id && function_exists('get_field');
                $link_raw = $acf_available ? get_field('top_product_link_' . $i, $page_id) : null;
                $text_raw = $acf_available ? get_field('top_product_text_' . $i, $page_id) : null;
                $image = $acf_available ? get_field('top_product_image_' . $i, $page_id) : null;

                $type = $acf_available ? get_field('top_product_type_' . $i, $page_id) : $d['type'];
                $new_tab = $acf_available ? (bool) get_field('top_product_new_tab_' . $i, $page_id) : true;
                if (empty($type)) $type = $d['type'];

                $link = !empty(trim($link_raw ?? '')) ? $link_raw : $d['url'];
                $text = !empty(trim($text_raw ?? '')) ? $text_raw : $d['text'];

                $link = esc_url($link);
                $target = $new_tab ? ' target="_blank" rel="noopener noreferrer"' : '';

                $has_link = !empty(trim($link_raw ?? ''));
                $has_content = ($type === 'logo' && (($image && !empty($image['url'])) || !empty($default_logos[$i]) || !empty(trim($text_raw ?? '')))) || ($type === 'text' && !empty(trim($text_raw ?? '')));
                if ($acf_available && (!$has_link || !$has_content)) continue;
            ?>
            <a href="<?php echo $link; ?>"<?php echo $target; ?> class="p-top-product__item">
                <div class="p-top-product__item-content<?php echo $type === 'logo' ? ' p-top-product__item-content--logo' : ''; ?>">
                    <?php if ($type === 'logo') :
                        if ($image && !empty($image['url'])) : ?>
                            <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($text); ?>">
                        <?php elseif (!empty($default_logos[$i])) : ?>
                            <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/svg/' . $default_logos[$i]); ?>" alt="<?php echo esc_attr($text); ?>">
                        <?php else : ?>
                            <p class="p-top-product__item-text"><?php echo esc_html($text); ?></p>
                        <?php endif;
                    else : ?>
                        <p class="p-top-product__item-text"><?php echo esc_html($text); ?></p>
                    <?php endif; ?>
                </div>
            </a>
            <?php endfor; ?>
        </div>

        <div class="p-top-product__aeaj">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/common/aeaj-logo.svg" alt="AEAJ">
        </div>
    </div>
</section>

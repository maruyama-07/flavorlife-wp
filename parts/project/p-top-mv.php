<?php
global $slider_library;
if ($slider_library === 'swiper') {
  get_template_part('parts/project/p-top-mv-swiper');

} elseif ($slider_library === 'slick') {
  get_template_part('parts/project/p-top-mv-slick');

} else {
  get_template_part('parts/project/p-top-mv-splide');
}
?>

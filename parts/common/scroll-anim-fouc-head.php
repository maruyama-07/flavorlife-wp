<?php
/**
 * GSAP スクロールアニメの FOUC 防止: anim.js より前に html にフラグを付与
 */
?>
<script>
document.documentElement.classList.add('js-scroll-anim-pending');
</script>
<noscript>
<style>
  html.js-scroll-anim-pending .js-animate-content .p-school-top-intro__brand,
  html.js-scroll-anim-pending .js-animate-content .p-school-top-intro__lead,
  html.js-scroll-anim-pending .js-animate-content .p-school-top-intro__body,
  html.js-scroll-anim-pending .js-animate-content .p-school-top-cards__item,
  html.js-scroll-anim-pending .js-animate-content .p-school-category__header > *,
  html.js-scroll-anim-pending .js-animate-content .p-school-category__item,
  html.js-scroll-anim-pending .js-animate-content .p-school-seasonal-topics__header > *,
  html.js-scroll-anim-pending .js-animate-content .p-school-seasonal-topics__media,
  html.js-scroll-anim-pending .js-animate-content .p-school-seasonal-topics__body > *,
  html.js-scroll-anim-pending .js-animate-content h1,
  html.js-scroll-anim-pending .js-animate-content h2,
  html.js-scroll-anim-pending .js-animate-content h3,
  html.js-scroll-anim-pending .js-animate-content p,
  html.js-scroll-anim-pending .js-animate-content li,
  html.js-scroll-anim-pending .js-animate-content figure,
  html.js-scroll-anim-pending .js-animate-content img,
  html.js-scroll-anim-pending .js-animate-content .c-custom-button,
  html.js-scroll-anim-pending .js-animate-content .p-top-mv-topics,
  html.js-scroll-anim-pending .js-animate-content .p-school-course-top__intro,
  html.js-scroll-anim-pending .js-animate-content .p-school-course-top__section,
  html.js-scroll-anim-pending .js-animate-content .p-school-course-top__card-item,
  html.js-scroll-anim-pending .js-animate-content .p-school-course__aside,
  html.js-scroll-anim-pending .js-animate-content .p-school-course__item,
  html.js-scroll-anim-pending .js-animate-content .p-school-course__pagination,
  html.js-scroll-anim-pending .js-animate-content .p-school-course__empty,
  html.js-scroll-anim-pending .js-animate-content .p-school-voice__intro,
  html.js-scroll-anim-pending .js-animate-content .p-school-voice-card,
  html.js-scroll-anim-pending .js-animate-content .p-school-voice__pagination,
  html.js-scroll-anim-pending .js-animate-content .p-school-voice__empty,
  html.js-scroll-anim-pending .js-animate-content .p-school-voice__footer-note,
  html.js-scroll-anim-pending .js-animate-content .p-school-about-intro__text,
  html.js-scroll-anim-pending .js-animate-content .p-school-about-intro__media,
  html.js-scroll-anim-pending .js-animate-content .p-school-about-bottom-links__card,
  html.js-scroll-anim-pending body.school-section .page-content .l-inner > *,
  html.js-scroll-anim-pending body.school-section .page-content .p-index > * {
    opacity: 1 !important;
    visibility: visible !important;
    transform: none !important;
  }
</style>
</noscript>

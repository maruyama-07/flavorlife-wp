/**
 * ヘッダースクロール制御
 * スクロール位置に応じてヘッダーの色を変更
 */
(function() {
  'use strict';
  
  // ホームページでのみ実行
  if (!document.body.classList.contains('home')) {
    return;
  }
  
  const header = document.querySelector('.l-header');
  const mvSlider = document.querySelector('.p-top-mv-splide');
  
  if (!header || !mvSlider) {
    console.log('Header or MV Slider not found');
    return;
  }
  
  console.log('Header scroll script initialized');
  
  function updateHeaderStyle() {
    const mvSliderRect = mvSlider.getBoundingClientRect();
    const mvSliderBottom = mvSliderRect.bottom;
    const headerHeight = header.offsetHeight;
    
    // ヘッダーがMVスライダー内にいるか判定
    if (mvSliderBottom > headerHeight) {
      // MVスライダー内 → デフォルトの白文字を維持（is-scrolledを削除）
      header.classList.remove('is-scrolled');
    } else {
      // MVスライダーを抜けた → 黒文字に変更
      header.classList.add('is-scrolled');
      console.log('Added is-scrolled class');
    }
  }
  
  // 初回実行
  updateHeaderStyle();
  
  // スクロール時に実行（throttle処理）
  let ticking = false;
  window.addEventListener('scroll', function() {
    if (!ticking) {
      window.requestAnimationFrame(function() {
        updateHeaderStyle();
        ticking = false;
      });
      ticking = true;
    }
  });
  
  // リサイズ時も更新
  window.addEventListener('resize', updateHeaderStyle);
})();

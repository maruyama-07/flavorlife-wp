<?php
/**
 * Functions
 */

// 基本設定
get_template_part('parts/functions-lib/func-base');

// Google アナリティクス（gtag・計測IDは func-analytics-gtag 内フィルターで変更可）
get_template_part('parts/functions-lib/func-analytics-gtag');

// カスタムメニューWalker
get_template_part('parts/functions-lib/func-menu-walker');

// SNS設定
get_template_part('parts/functions-lib/func-sns-settings');

// カスタムブロック
get_template_part('parts/functions-lib/func-custom-blocks');

// フロントページ用ACFフィールド
get_template_part('parts/functions-lib/func-acf-frontpage');

// TOP Productセクション用ACFフィールド（固定ページで管理）
get_template_part('parts/functions-lib/func-acf-product');

// 固定ページ用ACFフィールド
get_template_part('parts/functions-lib/func-acf-page');

// インタビューページ用ACFフィールド
get_template_part('parts/functions-lib/func-acf-interview');

// モバイルナビCTA用ACFフィールド
get_template_part('parts/functions-lib/func-acf-mobile-nav');

// フッターCTA用ACFフィールド（テキスト＋リンク）
get_template_part('parts/functions-lib/func-acf-footer-cta');

// Recruitページ用ACFフィールド（ヒーロー動画）
get_template_part('parts/functions-lib/func-acf-recruit');

// // Topics用ACFフィールド（MVリンク設定）
get_template_part('parts/functions-lib/func-acf-topics');

// ニュースセクション（ショートコード）
get_template_part('parts/functions-lib/func-news-section');

// カスタムボタン（ショートコード）
get_template_part('parts/functions-lib/func-custom-button');

// TinyMCEエディタの設定
get_template_part('parts/functions-lib/func-tinymce-config');
// スクール系クラシックエディタ専用 TinyMCE（茶色バナー等）
get_template_part('parts/functions-lib/func-tinymce-school');

// 段落をpタグで出力（TinyMCE設定＋既存divのp変換）
get_template_part('parts/functions-lib/func-content-paragraph');

// 本文内画像のfigure変換（p>img → figure>img）
get_template_part('parts/functions-lib/func-content-figure');

// セキュリティー対応
get_template_part('parts/functions-lib/func-security');

// ショートコードの設定
get_template_part('parts/functions-lib/func-shortcode');

// 情報ボックス（ショートコード）
get_template_part('parts/functions-lib/func-info-box');

// 3画像グリッド（ショートコード）
get_template_part('parts/functions-lib/func-image-grid');

// ステップフロー（ショートコード）
get_template_part('parts/functions-lib/func-step-flow');

// 商品カード（ショートコード）
get_template_part('parts/functions-lib/func-product-card');

// サービス2カラム（ショートコード）
get_template_part('parts/functions-lib/func-service-columns');

// 2カラムカード（ショートコード：画像＋テキスト＋c-custom-button）
get_template_part('parts/functions-lib/func-two-column-cards');

// レスポンシブ画像（ショートコード）
get_template_part('parts/functions-lib/func-responsive-image');

// URLのショートカット設定
get_template_part('parts/functions-lib/func-url');

// パーマリンク設定（News: /news/記事スラッグ/）
get_template_part('parts/functions-lib/func-permalink');

// Service ページ階層・リダイレクト・メニューリンク
get_template_part('parts/functions-lib/func-service');

// School セクション（/school および子ページ用のヘッダー・フッター・CSS切り替え）
get_template_part('parts/functions-lib/func-school');
// スクール紹介（/school/about/）講師一覧ショートコード＋ACF
get_template_part('parts/functions-lib/func-school-about-instructors');

// 固定ページ一覧：コーポレート / スクール切り替え（管理画面）
get_template_part('parts/functions-lib/func-admin-pages-section');

// スクール設定用ACFフィールド
get_template_part('parts/functions-lib/func-acf-school');

// スクールトップ（イントロ・4カード）用ACF
get_template_part('parts/functions-lib/func-acf-school-top');
// スクールトップ（Category）用ACF
get_template_part('parts/functions-lib/func-acf-school-category');
// スクールトップ（Seasonal Topics）用ACF
get_template_part('parts/functions-lib/func-acf-school-seasonal-topics');

// URLのショートカット設定
get_template_part('parts/functions-lib/func-utility');

// （MV（スクール）用）カスタム投稿タイプ
get_template_part('parts/functions-lib/func-add-posttype-mv-school');
get_template_part('parts/functions-lib/func-acf-mv-school');
// （News（スクール）用）カスタム投稿タイプ
get_template_part('parts/functions-lib/func-add-posttype-news-school');
// （受講生の声（スクール）用）カスタム投稿タイプ
get_template_part('parts/functions-lib/func-add-posttype-voice-school');
get_template_part('parts/functions-lib/func-acf-school-voice');
// （スクール講座一覧用）カスタム投稿タイプ・ACF
get_template_part('parts/functions-lib/func-add-posttype-course-school');
get_template_part('parts/functions-lib/func-course-school-category-order');
get_template_part('parts/functions-lib/func-course-school-post-order');
get_template_part('parts/functions-lib/func-acf-school-course');
// （スクール講師紹介グリッド用）カスタム投稿タイプ
get_template_part('parts/functions-lib/func-add-posttype-school-instructor');
get_template_part('parts/functions-lib/func-acf-school-instructor');

// スクリプト、スタイルシートの設定
get_template_part('parts/functions-lib/func-enqueue-assets');
// get_template_part('parts/functions-lib/func-enqueue-assets_noslider'); //スライダーを使用しない場合

// （MV用）カスタムフィールドの設定
get_template_part('parts/functions-lib/func-add-posttype-mv');

// （ヒーローセクション用）カスタム投稿タイプの設定
get_template_part('parts/functions-lib/func-add-posttype-hero');

// （Recruitセクション用）カスタム投稿タイプの設定
get_template_part('parts/functions-lib/func-add-posttype-recruit-section');
get_template_part('parts/functions-lib/func-add-posttype-recruit-job');
get_template_part('parts/functions-lib/func-acf-recruit-job');

// （gallery用）カスタムフィールドの設定
get_template_part('parts/functions-lib/func-add-posttype-gallery');

// （Works用）カスタムフィールドの設定
get_template_part('parts/functions-lib/func-add-posttype-topics');

// （Event用）投稿の名称変更
get_template_part('parts/functions-lib/func-add-posttype-post');

// （Blog用）カスタム投稿タイプの設定
get_template_part('parts/functions-lib/func-add-posttype-blog');

// メール送信設定
// add_action('phpmailer_init', function($phpmailer) {
//     $phpmailer->isSMTP();
// });
?>

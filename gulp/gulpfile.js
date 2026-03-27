const { src, dest, watch, series, parallel } = require('gulp');
const sass = require('gulp-sass')(require('sass'));
const sassGlob = require('gulp-sass-glob-use-forward');
const autoprefixer = require('gulp-autoprefixer');
const cleanCSS = require('gulp-clean-css');
const sourcemaps = require('gulp-sourcemaps');
const imagemin = require('gulp-imagemin');
const plumber = require('gulp-plumber');
const notify = require('gulp-notify');
const browserSync = require('browser-sync').create();
const pngquant = require('imagemin-pngquant');

function stylesSchool() {
  console.log('[DEBUG] stylesSchool: Starting...');
  return src('../src/scss/school/school-style.scss')
    .pipe(plumber({ errorHandler: notify.onError('Error: <%= error.message %>') }))
    .pipe(sassGlob())
    .pipe(sass().on('error', sass.logError))
    .pipe(autoprefixer())
    .pipe(dest('../assets/css'))
    .on('end', () => console.log('[DEBUG] stylesSchool: Completed!'));
}

function styles() {
  console.log('[DEBUG] styles: Starting...');
  console.log('[DEBUG] styles: Reading source...');
  return src('../src/scss/style.scss')
    .on('data', () => console.log('[DEBUG] styles: File found'))
    .pipe(plumber({ errorHandler: notify.onError('Error: <%= error.message %>') }))
    .on('data', () => console.log('[DEBUG] styles: After plumber'))
    .pipe(sassGlob())
    .on('data', () => console.log('[DEBUG] styles: After sassGlob'))
    .pipe(sass().on('error', sass.logError))
    .on('data', () => console.log('[DEBUG] styles: After sass'))
    .pipe(autoprefixer())
    .on('data', () => console.log('[DEBUG] styles: After autoprefixer'))
    .pipe(dest('../assets/css'))
    .on('end', () => console.log('[DEBUG] styles: Completed!'));
}

function images() {
  console.log('[DEBUG] images: Starting...');
  return src('../src/images/**/*', { encoding: false })
    .pipe(plumber({ errorHandler: notify.onError('Error: <%= error.message %>') }))
    .pipe(dest('../assets/images'))
    .on('end', () => console.log('[DEBUG] images: Completed!'));
}

function javascript() {
  console.log('[DEBUG] javascript: Starting...');
  return src('../src/js/**/*')
    .pipe(plumber({ errorHandler: notify.onError('Error: <%= error.message %>') }))
    .pipe(dest('../assets/js'))
    .on('end', () => console.log('[DEBUG] javascript: Completed!'));
}

const browserSyncOption = {
  proxy: "http://localhost:10035/", // ローカルにある「Site Domain」に合わせる
  notify: false, // ブラウザ更新時に出てくる通知を非表示にする
  open: false, // ブラウザを自動で開かない
  reloadDelay: 200, // リロード前の遅延（ミリ秒）
}

function watchFiles() {
  console.log('[DEBUG] watchFiles: Starting BrowserSync...');
  browserSync.init(browserSyncOption, function() {
    console.log('[DEBUG] watchFiles: BrowserSync started!');
  });
  watch('../src/scss/school/**/*.scss', series(stylesSchool, function(done) {
    browserSync.reload();
    done();
  }));
  watch(['../src/scss/**/*.scss', '!../src/scss/school/**/*.scss'], series(styles, function(done) {
    browserSync.reload();
    done();
  }));
  watch('../src/images/**/*', series(images, function(done) {
    browserSync.reload();
    done();
  }));
  watch('../src/js/**/*.js', series(javascript, function(done) {
    browserSync.reload();
    done();
  }));
  watch(['../*.php', '../parts/**/*.php', '../template-parts/**/*.php']).on('change', browserSync.reload);
}

exports.styles = styles;
exports.stylesSchool = stylesSchool;
exports.images = images;
exports.javascript = javascript;
exports.watch = watchFiles;
exports.default = series(parallel(styles, stylesSchool, images, javascript), watchFiles);

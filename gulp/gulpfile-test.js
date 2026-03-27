const { src, dest, watch, series, parallel } = require('gulp');
const sass = require('gulp-sass')(require('sass'));
const browserSync = require('browser-sync').create();

// 最小限のstylesタスク
function styles() {
  console.log('Starting styles task...');
  return src('../src/scss/style.scss')
    .pipe(sass().on('error', sass.logError))
    .pipe(dest('../assets/css'))
    .on('end', () => console.log('Styles task completed!'));
}

// 最小限のwatchFiles
function watchFiles() {
  console.log('Starting watch...');
  browserSync.init({
    proxy: "http://localhost:10035/",
    notify: false,
    open: false,
  });
  watch('../src/scss/**/*.scss', styles);
  console.log('Watch started!');
}

exports.styles = styles;
exports.watch = watchFiles;
exports.default = series(styles, watchFiles);

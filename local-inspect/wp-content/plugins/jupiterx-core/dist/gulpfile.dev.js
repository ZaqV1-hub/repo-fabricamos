"use strict";

var _require = require('gulp'),
    src = _require.src,
    dest = _require.dest,
    series = _require.series,
    watch = _require.watch;

var zip = require('gulp-zip');

var del = require('del');

var run = require('gulp-run-command')["default"];

var sass = require('gulp-sass');

var autoprefixer = require('gulp-autoprefixer');

var uglify = require('gulp-uglify');

var stripDebug = require('gulp-strip-debug');

var rename = require('gulp-rename');

var bro = require('gulp-bro');

var babelify = require('babelify');

var gulpLoadPlugins = require('gulp-load-plugins');

var sassLint = require('gulp-sass-lint');
/**
 * Automatically load and store all Gulp plugins.
 */


var $ = gulpLoadPlugins({
  rename: {
    'gulp-clean-css': 'cleanCSS'
  }
});
var extensions = {
  raven: {
    path: function path(_path) {
      return 'includes/extensions/raven/' + _path;
    }
  }
};
var paths = {
  core: {},
  extensions: {
    raven: {
      styles: {
        srcFull: extensions.raven.path('assets/src/scss/**/*.scss'),
        src: extensions.raven.path('assets/src/scss/*.scss'),
        dest: extensions.raven.path('assets/css/')
      },
      scripts: {
        srcFull: extensions.raven.path('assets/src/js/**/*.js'),
        src: [extensions.raven.path('assets/src/js/admin/*.js'), extensions.raven.path('assets/src/js/editor/*.js'), extensions.raven.path('assets/src/js/frontend/*.js')],
        dest: extensions.raven.path('assets/js/')
      },
      fonts: {
        fontName: 'jupiterx',
        svgIcons: [extensions.raven.path('assets/fonts/svg/jupiterx/*.svg'), extensions.raven.path('assets/fonts/svg/font-awesome/*.svg')]
      }
    }
  }
};
/**
 * Build Fonts.
 */

function buildFonts() {
  return src(paths.extensions.raven.fonts.svgIcons).pipe($.iconfont({
    fontName: paths.extensions.raven.fonts.fontName,
    formats: ['ttf', 'eot', 'woff', 'woff2', 'svg'],
    fontHeight: 1000,
    normalize: true
  })).on('glyphs', function (glyphs) {
    src(extensions.raven.path('assets/fonts/templates/icons.scss')).pipe($.consolidate('lodash', {
      glyphs: glyphs,
      fontName: paths.extensions.raven.fonts.fontName,
      fontPath: '../fonts/',
      className: 'jupiterx-icon'
    })).pipe(dest(extensions.raven.path('assets/src/scss')));
  }).pipe(dest(extensions.raven.path('assets/fonts/')));
}
/*
 * Build styles.
 */


function buildStyles() {
  return src(paths.extensions.raven.styles.src).pipe(sass({
    outputStyle: 'expanded'
  }).on('error', sass.logError)).pipe(autoprefixer({
    browsers: ['last 2 versions'],
    cascade: false
  })).pipe($.save('before-dest')).pipe(dest(paths.extensions.raven.styles.dest)).pipe($.cleanCSS()).pipe($.rename({
    suffix: '.min'
  })).pipe(dest(paths.extensions.raven.styles.dest)) // RTL
  .pipe($.save.restore('before-dest')).pipe($.rtlcss()).pipe($.rename({
    suffix: '-rtl'
  })).pipe(dest(paths.extensions.raven.styles.dest)).pipe($.cleanCSS()).pipe($.rename({
    suffix: '.min'
  })).pipe(dest(paths.extensions.raven.styles.dest));
}
/*
 * Build scripts.
 */


function buildScripts() {
  return src(paths.extensions.raven.scripts.src, {
    sourcemaps: true
  }).pipe(bro({
    transform: [babelify.configure({
      presets: ['@babel/preset-env']
    })]
  })) // eslint-disable-next-line no-console
  .on('error', console.log).pipe(dest(paths.extensions.raven.scripts.dest)).pipe(stripDebug()).pipe(uglify()).pipe(rename({
    suffix: '.min'
  })).pipe(dest(paths.extensions.raven.scripts.dest));
}
/*
 * Lint Sass.
 */


function lintSass() {
  return src(paths.extensions.raven.styles.srcFull).pipe(sassLint({
    options: {
      configFile: '.sass-lint.yml'
    }
  })).pipe(sassLint.format()).pipe(sassLint.failOnError());
}
/**
 * Task to clean.
 */


function clean() {
  return del(['release', '*.zip', extensions.raven.path('assets/css'), extensions.raven.path('assets/js')]);
}
/**
 * Create Zip.
 */


function releaseZip() {
  return src(['release/**']).pipe(zip('jupiterx-core.zip')) // eslint-disable-next-line no-undef
  .pipe(dest(__dirname).on('end', function () {
    // Move files from release/jupiterx-core to release/
    src('release/jupiterx-core/**').pipe(dest('release').on('end', function () {
      return del('release/jupiterx-core');
    }));
  }));
}

function release() {
  return src(['**',
  /* Ignore raven extension files */
  '!' + extensions.raven.path('assets/fonts/templates/**'), '!' + extensions.raven.path('assets/fonts/svg/**'), '!' + extensions.raven.path('assets/src/**'),
  /* Ignore core files */
  '!includes/control-panel-2/src/**', '!README.md', '!cypress/**', '!build/**', '!node_modules/**', '!visual-diff/**', '!vendor/**', '!wpcs/**', '!*.{lock,json,xml,js,yml}']).pipe(dest('release/jupiterx-core', {
    mode: '0755'
  }));
}
/*
 * Watch Raven extension files.
 */


module.exports.watchRaven = function () {
  return watch(paths.extensions.raven.scripts.srcFull, series(buildScripts)), watch(paths.extensions.raven.styles.srcFull, series(buildStyles));
};

module.exports["default"] = series(run('npm run make:pot'), run('npm run build'), run('npm run lint:js'), lintSass, buildStyles, buildScripts, buildFonts);
module.exports.test = series(run('npm run lint:js'), lintSass);
module.exports.release = series(clean, run('npm run build'), // run( 'npm run lint:js' ),
lintSass, buildStyles, buildScripts, // run( 'npm run make:pot' ),
buildFonts, release, releaseZip);
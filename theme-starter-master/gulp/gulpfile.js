'use strict';

// Requires + Variables
var gulp        = require('gulp'),
    sass        = require('gulp-sass'),
    browserSync = require('browser-sync').create(),
    cssmin      = require('gulp-cssmin'),
    rename      = require('gulp-rename'),
    prefix      = require('gulp-autoprefixer'),
    uglify      = require('gulp-uglify'),
    concat      = require('gulp-concat'),
    sourcemaps 	= require('gulp-sourcemaps'),
    sassLint    = require('gulp-sass-lint'),
    eslint      = require('gulp-eslint'),
    scripts = [
      '../assets/js/lib/flickity.pkgd.min.js',
      '../assets/js/site.nav.js',
      '../assets/js/site.js'
    ];

// Configure css tasks
gulp.task('sass', ['sass-lint'], function () {
  return gulp.src('../assets/scss/main.scss')
    .pipe(sourcemaps.init())
    .pipe(sass({
      outputStyle: 'compressed',
      includePaths: ['node_modules/superior-scss/src']
    }).on('error', sass.logError))
    .pipe(prefix({
      browsers: ['last 3 versions', 'Firefox >= 3', 'Safari >= 5'],
      cascade: false
    }))
    .pipe(rename({suffix: '.min'}))
    .pipe(sourcemaps.write('.', {sourceRoot:'../../assets/scss',includeContent: false}))
    .pipe(gulp.dest('../dist/css/'))
    .pipe(browserSync.stream());
});

gulp.task('sass-lint', function() {
  return gulp.src('../assets/**/*.scss')
    .pipe(sassLint({
      configFile: '.scss-lint-config.yml'
    }))
    .pipe(sassLint.format())
    .pipe(sassLint.failOnError())
})

gulp.task('browserSync', function(){
  browserSync.init({
    open: false,
    proxy: 'http://localhost/', // <--change me to your local environment url
    // timestamps: false,
    port: 3000
  });
})

// Configure js tasks
gulp.task('js', ['js-lint'], function() {
  return gulp.src(scripts)
    .pipe(uglify())
    .pipe(concat('app.js'))
    .pipe(rename({suffix: '.min'}))
    .pipe(gulp.dest('../dist/js'))
  	browserSync.reload();
});

gulp.task('js-lint', function() {
  return gulp.src(scripts)
    .pipe(eslint({
      configFile: '.eslintrc',
      fix: true
    }))
    .pipe(eslint.format())
    .pipe(eslint.failAfterError());
});


gulp.task('watch', ['browserSync', 'sass', 'js'], function(){
  gulp.watch('../assets/scss/**/*.scss', ['sass']);
  gulp.watch(['../*.php', '../**/*.php'], browserSync.reload);
  gulp.watch('../assets/js/**/*.js', ['js']);
})


// Default task
gulp.task('default', ['sass', 'js', 'browserSync', 'watch']);

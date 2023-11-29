import gulp from "gulp";
import uglify from 'gulp-uglify';
import concat from 'gulp-concat';
import minify from 'gulp-minify';
import gulpSass from "gulp-sass";
import nodeSass from "node-sass";
import path from 'path';
import config from './patternlab-config.json' assert { type: "json" };

const sass = gulpSass( nodeSass );

//const gulp = require('gulp');
//const uglify = require('gulp-uglify');
//const concat = require('gulp-concat');
//const minify = require('gulp-minify');

function paths( ) {
  return config.paths;
}

gulp.task('build-js', function() {
  return gulp.src('source/js/*.js')
    .pipe(concat('production.min.js'))
    .pipe(minify())
    .pipe(uglify())
    .pipe(gulp.dest('public/js'));
});

gulp.task('build-css', function(){
  return gulp.src(path.resolve(paths().source.css, '**/*.scss'))
    .pipe(sass().on('error', sass.logError))
    .pipe(gulp.dest(path.resolve(paths().public.css)));
});


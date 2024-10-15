const { src, dest, series, watch } = require('gulp');
const concat = require('gulp-concat');
const rename = require("gulp-rename");
const uglify = require('gulp-uglify-es').default;
const sourcemaps = require('gulp-sourcemaps');

const sourceFiles = [
    'src/_header.js',
    'src/CotontiApplication.js',
    'src/base.js',
];

const buildJs = () =>
    src(sourceFiles)
    .pipe(sourcemaps.init())
    .pipe(concat('base.js'))
    .pipe(sourcemaps.write())
    .pipe(dest('../'))
    .pipe(rename('base.min.js'))
    .pipe(uglify())
    .pipe(sourcemaps.write())
    //.pipe(sourcemaps.write('./'))
    .pipe(dest('../'));

/**
 * Build base.js
 * Use command
 * > gulp build
 */
exports.build = series(buildJs);

/**
 * File watcher
 * > gulp watch
 *
 * Default delay is 200 ms
 */
exports.watch = () => watch('src/*.js', { delay: 100 }, series(buildJs));


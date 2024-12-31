const { src, dest, series, watch } = require('gulp');
const webpack = require('webpack-stream');
const concat = require('gulp-concat');
const rename = require("gulp-rename");
const uglify = require('gulp-uglify-es').default;
const clean = require('gulp-clean');

const modules = [
    'src/CotontiApplication.js',
];

let mode = 'production'

const cleanDistDirectory = () =>
    src('./dist/', {read: false, allowEmpty: true})
        .pipe(clean());

const buildWorker = () =>
    src(['src/serverEvents/driver/sharedWorkerSSE.js'], {sourcemaps: true})
        .pipe(
            webpack({
                mode: mode,
                devtool: 'inline-source-map'
            })
        )
        .pipe(rename('sharedWorkerSSE.min.js'))
        .pipe(dest('../', {sourcemaps: true}));

const buildModules = () =>
    src(modules, {sourcemaps: true})
        .pipe(
            webpack({
                mode: mode,
                devtool: 'inline-source-map'
            })
        )
        .pipe(rename('modules.js'))
        .pipe(dest('./dist', {sourcemaps: true}));

const buildJs = () =>
    src([
        'src/_header.js',
        'dist/modules.js',
        'src/base.js'
    ], {sourcemaps: true})
        .pipe(concat('base.js'))
        .pipe(dest('../', {sourcemaps: true}))
        .pipe(rename('base.min.js'))
        .pipe(uglify())
        .pipe(dest('../', {sourcemaps: true}));

/**
 * Build base.js
 * Use command
 * > gulp build
 */
exports.build = series(cleanDistDirectory, buildWorker, buildModules, buildJs, cleanDistDirectory);

/**
 * File watcher
 * > gulp watch
 *
 * Default delay is 200 ms
 */
exports.watch = () => {
    mode = 'development';
    return watch('src/**/*.js', {delay: 100}, series(buildWorker, buildModules, buildJs));
}

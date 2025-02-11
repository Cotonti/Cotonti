const { src, dest, series, watch } = require('gulp');
const clean = require('gulp-clean');
const concat = require('gulp-concat');
const rename = require("gulp-rename");
const sourcemaps = require('gulp-sourcemaps');
const uglify = require('gulp-uglify-es').default;
const webpack = require('webpack-stream');

const modules = [
    'src/CotontiApplication.js',
];

let mode = 'production'

const cleanDistDirectory = () =>
    src('./dist/', {read: false, allowEmpty: true})
        .pipe(clean());

const buildWorker = () =>
    src(['src/serverEvents/sharedWorker.js'], {sourcemaps: true})
        .pipe(sourcemaps.init({ loadMaps: true }))
        .pipe(
            webpack({
                mode: mode,
                devtool: 'source-map',
                output: {
                    filename: 'sharedWorkerServerEvents.min.js',
                }
            })
        )
        .pipe(sourcemaps.write('./'))
        .pipe(dest('../'));

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
        .pipe(sourcemaps.init())
        .pipe(concat('base.js'))
        .pipe(dest('../', {sourcemaps: true}))
        .pipe(rename('base.min.js'))
        .pipe(uglify())
        .pipe(sourcemaps.write('./'))
        .pipe(dest('../'));

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

var gulp = require('gulp'),
    concat = require('gulp-concat'),
    minifyJs = require('gulp-uglify'),
    uglifycss = require('gulp-uglifycss'),
    less = require('gulp-less'),
    rename = require("gulp-rename"),
    notify = require("gulp-notify"),
    clean = require('gulp-clean');

gulp.task('vendors-css', function () {
    gulp.src([
        'web_src/frontend-vendors/bootstrap/dist/css/bootstrap.css',
        'web_src/frontend-vendors/bootstrap/dist/css/bootstrap-theme.css',
        'web_src/frontend-vendors/font-awesome/css/font-awesome.css'
    ])
        .pipe(concat('vendors.min.css'))
        .pipe(uglifycss())
        .pipe(rename("vendors.min.css"))
        .pipe(gulp.dest('web/css/'));
});

gulp.task('custom-css', function() {
    gulp.src(['web_src/css/main.less'])
        .pipe(less({compress: true}))
        .pipe(uglifycss())
        .pipe(rename("custom.min.css"))
        .pipe(gulp.dest('web/css/'))
        .pipe(notify("Gulp watch: custom-css task completed."));
});

gulp.task('vendors-js', function() {
    gulp.src([
        'web_src/frontend-vendors/jquery/dist/jquery.js',
        'web_src/frontend-vendors/bootstrap/dist/js/bootstrap.js',
        'web_src/frontend-vendors/moment/moment.js'
    ])
        .pipe(concat('vendors-js.min.js'))
        .pipe(minifyJs())
        .pipe(rename("vendors.min.js"))
        .pipe(gulp.dest('web/js/'));
});

gulp.task('custom-js', function() {
    gulp.src('web/js/**/*.js')
        .pipe(concat('app.min.js'))
        .pipe(minifyJs())
        .pipe(rename("custom.min.js"))
        .pipe(gulp.dest('web/js/'))
        .pipe(notify("Gulp watch: custom-js task completed."));
});

gulp.task('fonts', function(){
    gulp.src([
        'web_src/frontend-vendors/bootstrap/fonts/*',
        'web_src/frontend-vendors/font-awesome/fonts/*'
    ])
        .pipe(gulp.dest('web/fonts/'));
});

gulp.task('images', function(){
    gulp.src([
        'web_src/img/*'
    ])
        .pipe(gulp.dest('web/img/'));
});

gulp.task('clean', function () {
    return gulp.src(['web/css/*', 'web/js/*', 'web/fonts/*', 'web/img/*'])
        .pipe(clean());
});

gulp.task('default', ['clean'], function () {
    var tasks = ['vendors-css', 'custom-css', 'vendors-js', 'custom-js', 'fonts', 'images'];

    tasks.forEach(function (val) {
        gulp.start(val);
    });
});

gulp.task('watch', function () {
    var css = gulp.watch('web_src/css/*.css', ['custom-css']),
        js = gulp.watch('web_src/js/**/*.js', ['custom-js']);
});

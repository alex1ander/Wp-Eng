// Подключаем модули Gulp
const gulp = require('gulp');
const sass = require('gulp-sass')(require('sass'));
const autoprefixer = require('autoprefixer'); // Используем модуль autoprefixer
const postcss = require('gulp-postcss'); // Для работы с autoprefixer
const cleanCSS = require('gulp-clean-css');
const rename = require('gulp-rename');
const watch = require('gulp-watch');

// Пути к файлам
const paths = {
    scss: 'assets/scss/**/*.scss', // Исходные файлы SCSS
    css: 'assets/css/',            // Папка для готовых файлов CSS
};

// Задача для компиляции SCSS в CSS
gulp.task('styles', function () {
    return gulp.src(paths.scss)
        .pipe(sass().on('error', sass.logError)) // Компиляция SCSS
        .pipe(postcss([autoprefixer()])) // Добавление префиксов через postcss
        .pipe(cleanCSS()) // Минификация CSS
        .pipe(rename({ suffix: '.min' })) // Переименование файла
        .pipe(gulp.dest(paths.css)); // Сохранение в папке css
});

// Задача для отслеживания изменений
gulp.task('watch', function () {
    gulp.watch(paths.scss, gulp.series('styles'));
});

// Задача по умолчанию
gulp.task('default', gulp.series('styles', 'watch'));

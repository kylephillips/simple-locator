var gulp = require('gulp');
var sass = require('gulp-sass');
var autoprefix = require('gulp-autoprefixer');
var livereload = require('gulp-livereload');
var notify = require('gulp-notify');
var minifycss = require('gulp-minify-css');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var pump = require('pump');

// Paths
var scss = 'assets/scss/**/*';
var css = 'assets/css/';

var js_source = [
	'assets/js/source/simple-locator.functions-deprecated.js',
	'assets/js/source/simple-locator.single-location.js',
	'assets/js/source/simple-locator.form.js',
	'assets/js/source/simple-locator.geocoder.js',
	'assets/js/source/simple-locator.results-map-non-ajax.js',
	'assets/js/source/simple-locator.results-map.js',
	'assets/js/source/simple-locator.results-list.js',
	'assets/js/source/simple-locator.infowindow-open.js',
	'assets/js/source/simple-locator.geolocation.js',
	'assets/js/source/simple-locator.places-autocomplete.js',
	'assets/js/source/simple-locator.default-map.js',
	'assets/js/source/simple-locator.all-locations.js',
	'assets/js/source/simple-locator.errors.js',
	'assets/js/source/simple-locator.factory.js'
];

var js_admin_source = [
	'assets/js/source/admin/simple-locator-admin.modals.js',
	'assets/js/source/admin/simple-locator-admin.general-settings.js',
	'assets/js/source/admin/simple-locator-admin.post-type.js',
	'assets/js/source/admin/simple-locator-admin.post-edit.js',
	'assets/js/source/admin/simple-locator-admin.default-map.js',
	'assets/js/source/admin/simple-locator-admin.results-display.js',
	'assets/js/source/admin/simple-locator-admin.map-display.js',
	'assets/js/source/admin/simple-locator-admin.search-history.js',
	'assets/js/source/admin/simple-locator-admin.import-upload.js',
	'assets/js/source/admin/simple-locator-admin.import-column-map.js',
	'assets/js/source/admin/simple-locator-admin.import-import.js',
	'assets/js/source/admin/simple-locator-admin.listing-map.js',
	'assets/js/source/admin/simple-locator-admin.export-templates.js',
	'assets/js/source/admin/simple-locator-admin.quick-edit.js',
	'assets/js/source/admin/simple-locator-admin.factory.js'
];

var js_compiled = 'assets/js/';


/**
* Process the styles
*/
var styles = function(){
	return gulp.src(scss)
	.pipe(sass({ outputStyle: 'compressed' }))
	.pipe(autoprefix('last 15 version'))
	.pipe(gulp.dest(css))
	.pipe(livereload())
	.pipe(notify('Simple Locator styles compiled & compressed.'))
}

/**
* Admin JS
*/
var admin_scripts = function(){
	return gulp.src(js_admin_source)
	.pipe(concat('simple-locator-admin.min.js'))
	.pipe(gulp.dest(js_compiled))
	.pipe(uglify())
	.pipe(gulp.dest(js_compiled))
}

/**
* Front end js
*/
var scripts = function(){
	return gulp.src(js_source)
	.pipe(concat('simple-locator.min.js'))
	.pipe(gulp.dest(js_compiled))
	.pipe(uglify())
	.pipe(gulp.dest(js_compiled))
}


/**
* Watch Task
*/
gulp.task('watch', function(){
	livereload.listen();
	gulp.watch(scss, gulp.series(styles));
	gulp.watch(js_source, gulp.series(scripts));
	gulp.watch(js_admin_source, gulp.series(admin_scripts));
});


/**
* Default
*/
gulp.task('default', gulp.series(styles, scripts, admin_scripts, 'watch'));
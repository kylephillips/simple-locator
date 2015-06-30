var gulp = require('gulp');
var sass = require('gulp-sass');
var autoprefix = require('gulp-autoprefixer');
var livereload = require('gulp-livereload');
var notify = require('gulp-notify');
var plumber = require('gulp-plumber');
var jshint = require('gulp-jshint');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');

// Paths
var scss = 'assets/scss/**/*';
var css = 'assets/css/';

var js_source = [
	'assets/js/source/simple-locator.js',
	'assets/js/source/simple-locator-single.js',
	'assets/js/source/simple-locator-all-locations.js',
	'assets/js/source/simple-locator-non-ajax-results.js'
]

var js_admin_source = [
	'assets/js/source/bs-transition.js',
	'assets/js/source/bs-modal.js',
	'assets/js/source/simple-locator-admin.js',
	'assets/js/source/simple-locator-import.js'
];

var js_admin_maps_source = 'assets/js/source/simple-locator-admin-maps.js';
var js_admin_defaultmap_source = 'assets/js/source/simple-locator-admin-defaultmap.js';

var js_compiled = 'assets/js/';


/**
* Smush the admin Styles and output
*/
gulp.task('scss', function(){
	return gulp.src(scss)
		.pipe(sass({ outputStyle: 'compressed' }))
		.pipe(autoprefix('last 15 version'))
		.pipe(gulp.dest(css))
		.pipe(plumber())
		.pipe(livereload())
		.pipe(notify('Simple Locator styles compiled & compressed.'));
});

/**
* Admin JS
*/
gulp.task('admin_scripts', function(){
	return gulp.src(js_admin_source)
		.pipe(concat('simple-locator-admin.js'))
		.pipe(gulp.dest(js_compiled))
		.pipe(uglify())
		.pipe(gulp.dest(js_compiled))
});

/**
* Admin Maps JS
*/
gulp.task('admin_maps_scripts', function(){
	return gulp.src(js_admin_maps_source)
		.pipe(gulp.dest(js_compiled))
		.pipe(uglify())
		.pipe(gulp.dest(js_compiled))
});

/**
* Admin Default Map
*/
gulp.task('admin_default_map_scripts', function(){
	return gulp.src(js_admin_defaultmap_source)
		.pipe(gulp.dest(js_compiled))
		.pipe(uglify())
		.pipe(gulp.dest(js_compiled))
});


/**
* Front end js
*/
gulp.task('scripts', function(){
	return gulp.src(js_source)
		.pipe(gulp.dest(js_compiled))
		.pipe(uglify())
		.pipe(gulp.dest(js_compiled))
});


/**
* Watch Task
*/
gulp.task('watch', function(){
	livereload.listen(8000);
	gulp.watch(scss, ['scss']);
	gulp.watch(js_source, ['scripts']);
	gulp.watch(js_admin_source, ['admin_scripts']);
	gulp.watch(js_admin_maps_source, ['admin_maps_scripts']);
	gulp.watch(js_admin_defaultmap_source, ['admin_default_map_scripts']);
});


/**
* Default
*/
gulp.task('default', [
	'scss', 
	'scripts', 
	'admin_scripts', 
	'admin_maps_scripts', 
	'admin_default_map_scripts',
	'watch'
]);
var gulp = require('gulp');
var sass = require('gulp-sass');
var autoprefix = require('gulp-autoprefixer');
var livereload = require('gulp-livereload');
var notify = require('gulp-notify');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var pump = require('pump');

// Paths
var scss = 'assets/scss/**/*';
var css = 'assets/css/';

var js_source = [
	//'assets/js/source/simple-locator-non-ajax-results.js',
	'assets/js/source/simple-locator.functions-deprecated.js',
	'assets/js/source/simple-locator.single-location.js',
	'assets/js/source/simple-locator.form.js',
	'assets/js/source/simple-locator.geocoder.js',
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
	//'assets/js/source/simple-locator-admin.js',
	'assets/js/source/admin/simple-locator-admin.modals.js',
	'assets/js/source/admin/simple-locator-admin.post-edit.js',
	//'assets/js/source/simple-locator-import.js',
	'assets/js/source/admin/simple-locator-admin.default-map.js',
	'assets/js/source/admin/simple-locator-admin.results-display.js',
	'assets/js/source/admin/simple-locator-admin.map-display.js',
	'assets/js/source/admin/simple-locator-admin.search-history.js',
	'assets/js/source/admin/simple-locator-admin.factory.js'
];

var js_compiled = 'assets/js/';


/**
* Smush the admin Styles and output
*/
gulp.task('scss', function(callback){
	pump([
		gulp.src(scss),
		sass({ outputStyle: 'compressed' }),
		autoprefix('last 15 version'),
		gulp.dest(css),
		livereload(),
		notify('Simple Locator styles compiled & compressed.')
	], callback);
});

/**
* Admin JS
*/
gulp.task('admin_scripts', function(callback){
	pump([
		gulp.src(js_admin_source),
		concat('simple-locator-admin.min.js'),
		gulp.dest(js_compiled),
		uglify(),
		gulp.dest(js_compiled)
	], callback);
});

/**
* Front end js
*/
gulp.task('scripts', function(callback){
	pump([
		gulp.src(js_source),
		concat('simple-locator.min.js'),
		gulp.dest(js_compiled),
		//uglify(),
		gulp.dest(js_compiled)
	], callback);
});


/**
* Watch Task
*/
gulp.task('watch', function(){
	livereload.listen();
	gulp.watch(scss, ['scss']);
	gulp.watch(js_source, ['scripts']);
	gulp.watch(js_admin_source, ['admin_scripts']);
});


/**
* Default
*/
gulp.task('default', [
	'scss', 
	'scripts', 
	'admin_scripts', 
	'watch'
]);
// include gulp
var gulp = require('gulp'); 
 
// include plug-ins
//var sass = require('gulp-ruby-sass');			// Sass
var sass = require('gulp-sass');			// Sass
var prefix = require('gulp-autoprefixer');		// Autoprefixr
var minifycss = require('gulp-minify-css');		// Minify CSS
var concat = require('gulp-concat');			// Concat files
var uglify = require('gulp-uglify');			// Uglify javascript
var rename = require('gulp-rename');			// Rename files
var util = require('gulp-util');				// Writing stuff
var jshint = require('gulp-jshint');			// jshint
var clean = require('gulp-clean');

/**
 * Compile all CSS for the site
 */
gulp.task( 'sass', function() {
	gulp.src([
		'assets/src/sass/unplayd.scss'])								// Gets the apps scss
		.pipe(sass())												// Compile sass
		.on('error', function (err) { console.log(err.message); })  // Handle sass errors
		.pipe(concat('unplayd.css'))									// Concat all css
		.pipe(rename({suffix: '.min'}))								// Rename it
		.pipe(minifycss())											// Minify the CSS
		.pipe(gulp.dest('assets/css/'));						// Set the destination to assets/css
	util.log(util.colors.yellow('Sass compiled & minified'));	// Output to terminal
});
gulp.task( 'sass-admin', function() {
	gulp.src([
		'assets/src/sass/unplayd-admin.scss'])								// Gets the apps scss
		.pipe(sass())												// Compile sass
		.on('error', function (err) { console.log(err.message); })  // Handle sass errors
		.pipe(concat('unplayd-admin.css'))									// Concat all css
		.pipe(rename({suffix: '.min'}))								// Rename it
		.pipe(minifycss())											// Minify the CSS
		.pipe(gulp.dest('assets/css/'));						// Set the destination to assets/css
	util.log(util.colors.yellow('Admin: Sass compiled & minified'));	// Output to terminal
});

/**
 * Get all the JS, concat and uglify
 */
gulp.task('javascripts', function(){
	gulp.src([
		//'assets/src/vendors/jquery/dist/jquery.js',
		
		// moving on...
		'assets/src/js/unplayd.js'])								// Gets all the user JS _*.js from assets/js
		.on('error', function (err) { console.log(err.message); })  // Handle sass errors
		.pipe(concat('unplayd.js'))						// Concat all the scripts
		.pipe(rename({suffix: '.min'}))					// Rename it
		.pipe(uglify())									// Uglify & minify it
		.pipe(gulp.dest('assets/js/'))					// Set destination to assets/js
		util.log(util.colors.yellow('Javascripts compiled and minified'));
});
gulp.task('javascripts-admin', function(){
	gulp.src([
		//'assets/src/vendors/jquery/dist/jquery.js',
		'assets/src/vendors/select2/select2.js',
		// moving on...
		'assets/src/js/unplayd-admin.js'])								// Gets all the user JS _*.js from assets/js
		.on('error', function (err) { console.log(err.message); })  // Handle sass errors
		.pipe(concat('unplayd-admin.js'))						// Concat all the scripts
		.pipe(rename({suffix: '.min'}))					// Rename it
		.pipe(uglify())									// Uglify & minify it
		.pipe(gulp.dest('assets/js/'))					// Set destination to assets/js
		util.log(util.colors.yellow('Admin: Javascripts compiled and minified'));
});

/**
 * Move task.
 */
gulp.task('move', function() {
	gulp.src("assets/src/vendors/select2/select2-spinner.gif")
		.pipe(gulp.dest('assets/images/'));
	gulp.src("assets/src/vendors/select2/select2.png")
		.pipe(gulp.dest('assets/images/'));
	gulp.src("assets/src/vendors/select2/select2x2.png")
		.pipe(gulp.dest('assets/images/'));
	util.log(util.colors.yellow('Select2 assets moved'));	// Output to terminal
});


/**
 * Clean up
 */
gulp.task('clean', function() {
  return gulp.src('**/.DS_Store', { read: false })
  .pipe(clean());
});

/**
 * Watch task.
 */
gulp.task('watch', function(){
	
	gulp.watch("assets/src/js/**/*.js", ['javascripts']);		// Watch and run sass on changes
	gulp.watch("assets/src/sass/**/*.scss", ['sass']);	// Watch and run sass on changes
	util.log(util.colors.yellow('Started file watcher'));

});

/**
 * Compile task.
 */
gulp.task('compile', ['sass', 'sass-admin', 'javascripts', 'javascripts-admin', 'move', 'clean']);

/**
 * Default gulp task.
 */
gulp.task('default', ['sass', 'sass-admin', 'javascripts', 'javascripts-admin', 'move', 'clean', 'watch']);
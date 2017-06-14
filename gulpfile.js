var gulp        = require('gulp');                  // Gulp!

var sass        = require('gulp-sass');             // Sass
var prefix      = require('gulp-autoprefixer');     // Autoprefixr
var minifycss   = require('gulp-clean-css');       // Minify CSS
var concat      = require('gulp-concat');           // Concat files
var uglify      = require('gulp-uglify');           // Uglify javascript
var rename      = require('gulp-rename');           // Rename files
var util        = require('gulp-util');             // Writing stuff
//var livereload = require('gulp-livereload');      // LiveReload
var header      = require('gulp-header');           // Add header to files

var pkg     = require('./package.json');
var banner  = ['/**<%= pkg.name %>',
               '@version v<%= pkg.version %>',
               'Built: ' + util.date() + '',
               '*/',
               ''].join(' ') + '\n';

//
//      Compile all CSS for the site
//
//////////////////////////////////////////////////////////////////////


gulp.task('sass', function (){
    gulp.src('scss/*.scss')                    // Build Our Stylesheet
        .pipe(sass({style: 'compressed', errLogToConsole: true, sourceComments: 'map',
                           sourceMap: 'scss'}))  // Compile scss
        //.pipe(rename({suffix: '.min'}))                              // Rename it
        .pipe(minifycss())                                         // Minify the CSS
        .pipe(header(banner, { pkg : pkg }))
        .pipe(gulp.dest('css'));                            // Set the destination to assets/css
    //        .pipe(livereload());                                       // Reloads server
    util.log(util.colors.yellow('Sass compiled & minified'));  // Output to terminal
});

gulp.task('javascript', function() {
    gulp.src('js/src/*.js')
        .pipe(uglify())
        .pipe(rename({suffix: '.min'}))
        .pipe(gulp.dest('js'))
});
//////////////////////////////////////////////////////////////////////


gulp.task('watch', function(){

    // Watch and run sass on changes
    gulp.watch("scss/*.scss", ['sass']);
    gulp.watch("js/src/*.js", ['javascript']);
});

gulp.task('default', ['sass', 'javascript']);
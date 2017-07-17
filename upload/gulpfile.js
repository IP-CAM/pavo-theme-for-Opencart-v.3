const gulp = require( 'gulp' );
const sass = require( 'gulp-sass' );
const sourcemaps = require( 'gulp-sourcemaps' );
const concat = require( 'gulp-concat' );
const uglify = require( 'gulp-uglify' );
const babel = require( 'gulp-babel' );
const fs = require( 'fs' );
const path = require( 'path' );
const rename = require( 'gulp-rename' );
const glob = require( 'glob' );
const browserify = require( 'browserify' );
const through2 = require( 'through2' );

const scriptSources = [
	'admin/view/javascript/pavothemer/classes/class-*.js',
	'catalog/view/theme/**/javascript/classes/class-*.js'
];

const scripts = [];
for ( let src of scriptSources ) {
	let srcs = glob.sync( src );
	for ( let sc of srcs ) {
		scripts.push( sc );
	}
}

const styles = [
	'admin/view/stylesheet/pavothemer/*.scss'
];

const themes = glob.sync( 'catalog/view/theme/*' );
for ( let theme of themes ) {
	let themeName = path.basename( theme );
	styles.push( 'catalog/view/theme/'+ themeName +'/sass/stylesheet.scss' );
	styles.push( 'catalog/view/theme/'+ themeName +'/sass/stylesheet-rtl.scss' );
}

// scripts convert es6 to es5
gulp.task( 'scripts', () => {
	console.log( 'gulp task scripts is running' );

	return gulp.src( scriptSources )
		.pipe( sourcemaps.init() )
		// this section is required for compile export function
		.pipe(through2.obj(function (file, enc, next) {
            browserify(file.path, { debug: process.env.NODE_ENV === 'development' })
                .transform(require('babelify'))
                .bundle(function (err, res) {
                    if (err) { return next(err); }

                    file.contents = res;
                    next(null, file);
                });
        }))
        // end this section is required for compile export function
		.pipe( babel() )
		.pipe( uglify() )
		.pipe( sourcemaps.write( 'maps', {
			mapFile: ( mapFilePath ) => {
				return mapFilePath.replace( 'classes', 'maps' );
			}
		} ) )
		.pipe( rename( ( path ) => {
			path.basename = path.basename.replace( 'class-', '' );
			path.dirname = path.dirname.replace( '/classes', '' );
		}) )
		.on( 'error', ( e ) => {
			console.log( 'ERROR: ' + e );
		} )
		.pipe( gulp.dest( ( file ) => {
			var regex = /admin\/view/i;

			// check admin scripts
			if ( regex.test( file.base ) ) {
				return file.base.replace( 'classes', '' );
			} else {
				return file.base;
			}
			return file.base;
		} ) );

} );

// sass
gulp.task( 'sass', () => {
	return gulp.src( styles )
		.pipe( sourcemaps.init() )
        .pipe( sass({ outputStyle: 'compressed' }).on( 'error', sass.logError ) )
        .pipe( sourcemaps.write( 'maps', {
			mapFile: ( mapFilePath ) => {
				return mapFilePath.replace( '/sass', '/maps' );
			}
		} ) )
        .pipe( gulp.dest( ( file ) => {
        	if ( path.basename( file.base ) === 'sass' ) {
        		return path.dirname( file.base ) + '/stylesheet';
        	}

            return file.base;
        } ) );
} );

// default task
gulp.task( 'default', () => {

} );

gulp.task( 'watch', () => {
	console.log( 'gulp is watching changes' )
	gulp.watch( scriptSources, ['scripts'] );
	gulp.watch( styles, ['sass'] );
} );

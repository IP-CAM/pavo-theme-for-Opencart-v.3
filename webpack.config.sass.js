const path = require( 'path' );
const webpack = require( 'webpack' );
const glob = require( 'glob' );
const ExtractTextPlugin = require( 'extract-text-webpack-plugin' );

const sasses = {};
var files = [];
var adminFiles = glob.sync( 'upload/*/view/stylesheet/pavothemer/*.scss' );
var catalogFiles = glob.sync( 'upload/*/view/theme/*/sass/stylesheet*.scss' );
files = adminFiles.concat( catalogFiles );

for ( let src of files ) {
	var name = '';
	var folder_name = path.basename( path.dirname( src ) );
	// catalog
	if ( folder_name === 'sass' ) {
		name = src.replace( 'sass/' + path.basename( src ), 'stylesheet/' + path.basename( src, '.scss' ) );
	} else if ( folder_name === 'pavothemer' ) {
		name = src.replace( '.scss', '' );
	}
	if ( name ) {
		sasses[name] = path.resolve( __dirname, src );
	}
}

module.exports = {
	entry: sasses,
	output: {
		filename: "[name].min.css",
		path: path.join( __dirname, '' )
	},
	module: {
		loaders: [
			{
				test: /\.js$/,
				exclude: /node_modules/,
				loader: 'babel-loader',
				query: {
					presets: [ 'es2015', 'stage-0' ]
				}
			},
			{
				test: /\.css$/,
				loader: [ 'style-loader', 'css-loader' ]
			},
			{
				test: /\.scss$/,
				exclude: /node_modules/,
				loader: ExtractTextPlugin.extract([ 'css-loader', 'sass-loader' ])
			},
			{
				test: /\.(jpg|jpeg|png)$/,
				loader: 'url-loader'
			}
		]
	},
	devtool: 'inline-source-map',
 	stats: {
     	colors: true
 	},
	plugins: [
		new webpack.DefinePlugin({
	      'process.env.NODE_ENV': JSON.stringify( process.env.NODE_ENV )
	    }),
	    new webpack.optimize.OccurrenceOrderPlugin(),
	    new webpack.optimize.UglifyJsPlugin({
	      	compress: { warnings: true },
	      	mangle: true,
	      	sourcemap: false,
	      	beautify: false,
	      	dead_code: true
	    }),
	    new ExtractTextPlugin({
		    filename: "[name].min.css",
		    disable: process.env.NODE_ENV === 'development'
		})
	]
}
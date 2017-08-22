const path = require( 'path' );
const webpack = require( 'webpack' );
const glob = require( 'glob' );
const ExtractTextPlugin = require( 'extract-text-webpack-plugin' );
const OptimizeCssAssetsPlugin = require('optimize-css-assets-webpack-plugin');

const sasses = {};
var files = [];
var adminFiles = glob.sync( 'upload/*/view/stylesheet/pavothemer/*.scss' );
var catalogFiles = glob.sync( 'upload/*/view/theme/*/sass/stylesheet*.scss' );
files = adminFiles.concat( catalogFiles );
var skinsFiles = glob.sync( 'upload/*/view/theme/*/sass/skins/*.scss' );

files = files.concat( skinsFiles );

for ( let src of files ) {
	var name = '';
	var folder_name = path.basename( path.dirname( src ) );
	// catalog
	if ( folder_name === 'sass' ) {
		name = src.replace( 'sass/' + path.basename( src ), 'stylesheet/' + path.basename( src, '.scss' ) );
	} else if ( folder_name === 'pavothemer' ) {
		name = src.replace( '.scss', '' );
	} else if ( folder_name === 'skins' ) {
		name = src.replace( 'sass/skins/' + path.basename( src ), 'stylesheet/skins/' + path.basename( src, '.scss' ) );
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
				exclude: /node_modules/,
				loader: [ 'style-loader', 'css-loader?minimize=true' ]
			},
			{
				test: /\.scss$/,
				exclude: /node_modules/,
				loader: ExtractTextPlugin.extract([ 'css-loader?minimize', 'sass-loader' ])
			},
			{
				// image extensions, fonts extensions
				test: /\.(jpg|jpeg|png|ttf|woff|woff2|eot|svg|)$/,
				exclude: /node_modules/,
				loader: 'url-loader'
			}
		]
	},
	// devtool: 'inline-source-map',
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
		,
		// minify style files
		new OptimizeCssAssetsPlugin({
	      	cssProcessorOptions: {
	      		discardComments: {
	      			removeAll: true
	      		},
	      		map: {
      				inline: false
    			}
	      	},
	      	canPrint: true
	    })
	]
}
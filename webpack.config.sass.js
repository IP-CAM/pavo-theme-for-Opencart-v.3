const path = require( 'path' );
const webpack = require( 'webpack' );
const glob = require( 'glob' );
const ExtractTextPlugin = require( 'extract-text-webpack-plugin' );

const sasses = {};
var files = [];
var adminFiles = glob.sync( 'upload/*/view/stylesheet/pavothemer/*.scss' );
var catalogFiles = glob.sync( 'upload/*/view/theme/*/sass/stylesheet*.scss' );
files = adminFiles.concat( catalogFiles );
var builderFiles = [];
var builderThemeFiles = glob.sync( 'upload/*/view/stylesheet/pavobuilder/*.scss' );
var builderFilesCore = glob.sync( 'upload/*/view/javascript/pavobuilder/*.scss' );
builderFiles = builderThemeFiles.concat( builderFilesCore );
files = files.concat( builderFiles );
var skinsFiles = glob.sync( 'upload/*/view/theme/*/sass/skins/*.scss' );

files = files.concat( skinsFiles );

for ( let src of files ) {
	var name = '';
	var folder_name = path.basename( path.dirname( src ) );
	// catalog
	if ( folder_name === 'sass' ) {
		name = src.replace( 'sass/' + path.basename( src ), 'stylesheet/' + path.basename( src, '.scss' ) );
	} else if ( folder_name === 'pavothemer' || folder_name === 'pavobuilder' ) {
		name = path.dirname( src ) + '/dist/' + path.basename( src, '.scss' );
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
				test: /\.css$/,
				// exclude: /node_modules/,
				loader: [ 'style-loader', 'css-loader' ]
			},
			{
				test: /\.scss$/,
				exclude: /node_modules/,
				loader: ExtractTextPlugin.extract([ 'css-loader?minimize', 'sass-loader' ])
			},
			{
				// image extensions, fonts extensions
				test: /\.(png|jpg|jpeg|ttf|woff|woff2|eot|svg|gif|)$/,
				exclude: /node_modules/,
				loader: [ 'url-loader?emitFile=false' ]//, 'file-loader?emitFile=false'
			}
		]
	},
	devtool: 'eval-source-map',
 	stats: {
     	colors: true
 	},
	plugins: [
	    new ExtractTextPlugin({
		    filename: "[name].min.css",
		    disable: process.env.NODE_ENV === 'development'
		})
	]
}
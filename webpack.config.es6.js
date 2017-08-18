const path = require( 'path' );
const webpack = require( 'webpack' );
const glob = require( 'glob' );
const ExtractTextPlugin = require( 'extract-text-webpack-plugin' );

const jsess = {};
var files = glob.sync( 'upload/*/view/javascript/pavothemer/es6/*.es6.js' );
for ( let src of files ) {
	var name = path.dirname( src ) + '/' + path.basename( src, '.es6.js' );
	name = name.replace( path.join( __dirname, 'upload' ), '' ).replace( 'es6/', '' );
	jsess[name] = path.resolve( __dirname, src );
}

module.exports = {
	entry: jsess,
	output: {
		filename: "[name].min.js",
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
	devtool: 'source-map',
	plugins: [
		new webpack.DefinePlugin({
	      'process.env.NODE_ENV': JSON.stringify( process.env.NODE_ENV )
	    }),
	    new webpack.optimize.OccurrenceOrderPlugin(),
	    new webpack.optimize.UglifyJsPlugin({
	      	compress: { warnings: false },
	      	mangle: true,
	      	sourcemap: false,
	      	beautify: false,
	      	dead_code: true
	    })
	]
}
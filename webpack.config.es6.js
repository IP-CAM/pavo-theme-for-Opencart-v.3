const path = require( 'path' );
const webpack = require( 'webpack' );
const glob = require( 'glob' );
const ExtractTextPlugin = require( 'extract-text-webpack-plugin' );

const jsess = {};
var themerFiles = glob.sync( 'upload/*/view/javascript/pavothemer/*.js' );
var builderFiles = glob.sync( 'upload/*/view/javascript/pavobuilder/*.js' );
var files = themerFiles.concat( builderFiles );
for ( let src of files ) {
	var name = path.dirname( src ) + '/dist/' + path.basename( src, '.js' );
	name = name.replace( path.join( __dirname, 'upload' ), '' );
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
			}
		]
	},
	devtool: 'eval-source-map',// 'inline-source-map',
	plugins: [
		new webpack.DefinePlugin({
	      	'process.env.NODE_ENV': JSON.stringify( process.env.NODE_ENV )
	    }),
	    new webpack.optimize.OccurrenceOrderPlugin(),
	    // disable ProvidePlugin because jquery is already in opencart website
	 	// new webpack.ProvidePlugin({
		//     $: "jquery",
		//     jQuery: "jquery",
		//     "window.jQuery": "jquery",
		//     jquery: "jquery"
		// }),
	    new webpack.optimize.UglifyJsPlugin({
	      	compress: { warnings: false },
	      	mangle: true,
	      	sourcemap: true,
	      	beautify: false,
	      	dead_code: true
	    })
	],
	resolve: {
		extensions: [ '.js' ],
		alias: {
			'jquery-ui': 'jquery-ui/ui'
		}
	}
}
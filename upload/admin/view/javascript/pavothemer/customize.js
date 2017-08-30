import ThemeCustomize from './src/theme-customize';
const $ = jQuery;

const Pavo = {
	customize ( type = '', callback = null ) {
		let value = ThemeCustomize.change( type );
		if ( callback && typeof callback == 'function' ) {
			callback.apply( null, [ ThemeCustomize, value ] );
		}
	}
}

Pavo.customize( 'xxx', ( customize, value ) => {

	// refresh
	customize.update( ( value ) => {
		
	} );
} );

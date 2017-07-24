import ThemeCustomize from './theme-customize';
const $ = jQuery;

const Pavo = {
	customize ( type = '', callback = null ) {
		let value = ThemeCustomize.change( type );
		if ( callback && typeof callback == 'function' ) {
			callback.apply( null, [ ThemeCustomize, value ] );
		}
	}
}

Pavo.customize( 'xxx', ( value ) => {
	console.debug( value );
} );

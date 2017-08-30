const $ = jQuery;

/**
 * Control class
 */
class ThemeControl {

	constructor() {

	}

}

/**
 * Customize class
 */
class ThemeCustomize {

	_constrols = {}

	/**
	 * If has changed _isDirty will be TRUE, and FALSE or not
	 * @since 1.0.0
	 */
	_isDirty = false

	_cachedType = false
	_cachedNewVal = false
	_refresh = false

	/**
	 * Constructor class
	 * @since 1.0.0
	 */
	constructor() {
		console.debug( 'PavoTheme Customize was initialized!!!' );
	}

	/**
	 * On change customize control value
	 * @since 1.0.0
	 */
	change( type = '' ) {
		if ( this._constrols.length == 0 ) {
			this._constrols = window.PavoCustomizeParams;
		}
		if ( ! type || document.getElementById( type ) ) return;
		if ( typeof this._constrols[ type ] == 'undefined' ) return;

		let newVal = '';
		if ( this._constrols[ type ] !== newVal ) {
			this._isDirty = true;
		}

		this._cachedType = type;
		this._cachedNewVal = newVal;
	}

	/**
	 * Update customize action
	 * @since 1.0.0
	 */
	update( callback = '' ) {
		if ( typeof callback == 'function' ) {
			this._constrols[ this._cachedType ] = this._cachedNewVal;
			callback.apply( null, [ ThemeCustomize, this._cachedNewVal ] );

			if ( this._isDirty && this._refresh == true ) {
				this.refresh();
			}

			this._isDirty = false;
		}
	}

	/**
	 * Refresh Iframe
	 * @since 1.0.0
	 */
	refresh() {
		document.getElementById( 'pavo-iframe' ).contentDocument.location.reload( true );
	}

}

export default new ThemeCustomize();
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

	_constrols = []

	/**
	 * If has changed _isDirty will be TRUE, and FALSE or not
	 * @since 1.0.0
	 */
	_isDirty = false

	/**
	 * Constructor class
	 * @since 1.0.0
	 */
	constructor() {
		console.debug( 'ThemeCustomize is initialized!!!' );
	}

	/**
	 * On change customize control value
	 * @since 1.0.0
	 */
	change( type = '' ) {
		if ( ! type || $( '#' + type ).length == 0 ) return;
		// if ( this._constrols.indexOf( type ) == -1 ) return;

		// this._constrols[]
	}

	/**
	 * Update customize action
	 * @since 1.0.0
	 */
	update() {

	}

	/**
	 * Refresh Iframe
	 * @since 1.0.0
	 */
	refresh() {

	}

	/**
	 * Check has changes
	 *
	 * @since 1.0.0
	 * @var boolean
	 */
	_isDirty() {
		return this._isDirty;	
	}

}

export default new ThemeCustomize();
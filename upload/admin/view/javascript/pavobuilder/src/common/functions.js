import $ from 'jquery';
import _ from 'underscore';
import Backbone from 'Backbone';
import spectrum from 'spectrum-colorpicker';
import 'spectrum-colorpicker/spectrum.css';
import GoogleMap from '../views/globals/google-map';

function toJSON( data = {}, ignores = [] ) {
	if ( data instanceof Backbone.Model || data instanceof Backbone.Collection ) {
		data = data.toJSON();
	}

	_.map( data, ( value, name ) => {
		if ( value instanceof Object ) {
			data[name] = this.toJSON( value );
		} else {
			data[name] = value;
		}
	} );

	return data;
}

/**
 * init thirparty script
 * ex: colorpicker, datepicker
 */
function init_thirdparty_scripts( model ) {
	// colorPicker
	let inputs = $( '.pa-colorpicker-input' );
	for ( let i = 0; i < inputs.length; i++ ) {
		let input = inputs[i];
		$( input ).spectrum({
			color: $( input ).val()
		});
	}

	// maps
	let maps = $( '.pa_google_map.form-horizontal' );
	for ( let i = 0; i < maps.length; i++ ) {
		let map = maps[i];
		new GoogleMap( $( map ), model ).render();
	}
}

export default {
	toJSON,
	init_thirdparty_scripts
}
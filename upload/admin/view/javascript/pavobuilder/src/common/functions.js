import Backbone from 'Backbone';
import _ from 'underscore';
import $ from 'jquery';
import spectrum from 'spectrum-colorpicker';
import 'spectrum-colorpicker/spectrum.css';

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
 * ex: colorpicker, datepicker ..
 */
function init_thirparty_scripts() {
	// colorPicker
	let inputs = $( '.pa-colorpicker-input' );
	for ( let i = 0; i < inputs.length; i++ ) {
		let input = inputs[i];
		$( input ).spectrum({
			color: $( input ).val()
		});
	}
}

export default {
	toJSON,
	init_thirparty_scripts
}
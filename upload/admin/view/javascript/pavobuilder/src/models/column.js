import Backbone from 'Backbone';
import ElementsCollection from '../collections/elements';

export default class ColumnModel extends Backbone.Model {

	initialize( data = { settings: { class: 'pa-col-sm-12' }, elements: {} } ) {
		// super( data );
		this.set( 'elements', new ElementsCollection( data.elements ) )
	}

	defaults() {
		return {
			settings: {},
			elements: new ElementsCollection(),
			editabled: false,
			adding: false
		};
	}
	
}
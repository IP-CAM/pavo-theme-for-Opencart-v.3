import Backbone from 'Backbone';
import ElementsCollection from '../collections/elements';

export default class ColumnModel extends Backbone.Model {

	initialize( data = { settings: { class: 'pa-col-sm-12', styles: {} }, elements: {} } ) {
		// super( data );
		this.set( 'elements', new ElementsCollection( data.elements ) );
	}

	defaults() {
		return {
			settings: {
				element: 'pa_column'
			},
			elements: new ElementsCollection(),
			editing: false,
			adding: false
		};
	}

}
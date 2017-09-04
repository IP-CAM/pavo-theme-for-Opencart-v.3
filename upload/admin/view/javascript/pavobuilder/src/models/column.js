import Backbone from 'Backbone';
import ElementsCollection from '../collections/elements';

export default class ColumnModel extends Backbone.Model {

	initialize( data = { settings: {}, elements: {} } ) {
		// super( data );
		this.set( 'elements', new ElementsCollection( data.elements ) )
	}

	defaults() {
		return {
			settings: {},
			elements: new ElementsCollection()
		}
	}
	
}
import Backbone from 'Backbone';

export default class RowModel extends Backbone.Model {

	constructor( data = {} ) {
		super();
	}

	defaults() {
		return {
			settings: {},
			columns: {}
		}
	}
	
}
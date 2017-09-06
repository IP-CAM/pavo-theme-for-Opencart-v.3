import Backbone from 'Backbone';
import ColumnsCollection from '../collections/columns';

export default class RowModel extends Backbone.Model {

	initialize( data = { settings: {}, columns: {}, editing: false } ) {
		this.set( 'columns', new ColumnsCollection( data.columns ) );
	}

	defaults() {
		return {
			settings: {},
			columns: new ColumnsCollection(),
			editing: false
		}
	}
	
}
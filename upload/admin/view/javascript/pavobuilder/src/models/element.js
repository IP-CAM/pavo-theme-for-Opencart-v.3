import Backbone from 'Backbone';
import ColumnsCollection from '../collections/columns';
import RowModel from './row';

export default class ElementModel extends Backbone.Model {

	initialize( data = { settings: {}, row: {}, columns: {} } ) {
		if ( data.row ) {
			this.set( 'row', new RowModel( data.row ) );
			if ( data.columns ) {
				this.get( 'row' ).add( data.columns );
			}
		}
	}

	defaults() {
		return {
			settings 	: {
				element: 'pa_element'
			},
			mask	 	: {},
			editing  	: false
		};
	}

}
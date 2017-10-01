import Backbone from 'Backbone';
import ColumnsCollection from '../collections/columns';

export default class RowModel extends Backbone.Model {

	initialize( data = { settings: {}, columns: [], editing: false } ) {
		this.set( 'columns', new ColumnsCollection( data.columns ) );
		this.on( 'change:screen', this._switchScreenMode );
	}

	defaults() {
		return {
			settings: {
				element: 'pa_row'
			},
			columns: new ColumnsCollection(),
			editing: false,
			element_type: 'widget',
			widget : 'pa_row'
		}
	}

	_switchScreenMode( model ) {
		model.get( 'columns' ).map( ( column ) => {
			column.set( 'screen', model.get( 'screen' ) );
		} );
	}

}
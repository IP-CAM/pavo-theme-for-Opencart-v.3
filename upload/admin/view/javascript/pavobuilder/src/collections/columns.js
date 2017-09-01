import Backbone from 'Backbone';
import ColumnModel from '../models/column'

export default class ColumnsCollection extends Backbone.Collection {

	constructor() {
		super();
		this.model = ColumnModel;
	}

	/**
	 * Move item sort models
	 */
	moveItem( fromIndex = 0, toIndex = 0 ) {
		this.models.splice( toIndex, 0, this.models.splice( fromIndex, 1 )[0] );
        this.trigger( 'move' );
	}
	
}
import Backbone from 'Backbone';
import RowModel from '../models/row';

export default class RowsCollection extends Backbone.Collection {

	initialize( rows ) {
		this.model = RowModel;
		this.on( 'add', this._addParam, this );
	}

	/**
	 * Move item sort models
	 */
	moveItem( fromIndex = 0, toIndex = 0 ) {
		this.models.splice( toIndex, 0, this.models.splice( fromIndex, 1 )[0] );
        this.trigger( 'move' );
	}

	_addParam( model ) {
		let settings = model.get( 'settings' );
		settings.element = 'pa_row';
		model.set( 'settings', settings );
	}

}
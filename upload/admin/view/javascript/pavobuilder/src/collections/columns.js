import Backbone from 'Backbone';
import ColumnModel from '../models/column'
import _ from 'underscore';

export default class ColumnsCollection extends Backbone.Collection {

	initialize( columns ) {
		this.model = ColumnModel;
		this.on( 'update', this._editabled, this );
	}

	/**
	 * Move item sort models
	 */
	moveItem( fromIndex = 0, toIndex = 0 ) {
		this.models.splice( toIndex, 0, this.models.splice( fromIndex, 1 )[0] );
        this.trigger( 'move' );
	}

	/**
	 * Editabled
	 */
	_editabled() {
		this.models.map( ( model ) => {
			if ( this.indexOf( model ) > 0 ) {
				model.set( 'editabled', true );
			} else {
				model.set( 'editabled', false );
			}
		} );
	}

}
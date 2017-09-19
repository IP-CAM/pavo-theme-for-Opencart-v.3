import Backbone from 'Backbone';
import ColumnModel from '../models/column'
import _ from 'underscore';

export default class ColumnsCollection extends Backbone.Collection {

	initialize( columns ) {
		this.model = ColumnModel;
		this.on( 'update', this._editabled, this );
		this.on( 'add', this._addParam, this );
	}

	/**
	 * Move item sort models
	 */
	moveItem( fromIndex = 0, toIndex = 0 ) {
		this.models.splice( toIndex, 0, this.models.splice( fromIndex, 1 )[0] );
        this.trigger( 'move' );
	}

	/**
	 * Set Editabled is TRUE
	 * And change reRender status allow view change
	 */
	_editabled() {
		this.models.map( ( model ) => {
			let editabled = model.get( 'editabled' );
			let nextEditabled = this.indexOf( model ) < this.length - 1;
			model.set( 'editabled', nextEditabled );
			if ( editabled != nextEditabled ) {
				model.set( 'reRender', true );
			}
		} );
	}

	_addParam( model ) {
		let settings = model.get( 'settings' );
		settings.element = 'pa_column';
		model.set( 'settings', settings );
	}

}
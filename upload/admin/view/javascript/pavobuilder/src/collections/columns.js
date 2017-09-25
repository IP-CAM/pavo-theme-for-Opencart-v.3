import Backbone from 'Backbone';
import ColumnModel from '../models/column'
import _ from 'underscore';

export default class ColumnsCollection extends Backbone.Collection {

	initialize( columns ) {
		this.model = ColumnModel;
		this.on( 'update', this._editabled, this );
		this.on( 'add', this._addParam, this );
		this.on( 'remove', this._calculatorColumnWidth );
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
		this.models.map( ( model, index ) => {
			let editabled = model.get( 'editabled' );
			let nextEditabled = index < this.length - 1;
			model.set( 'editabled', nextEditabled );
			if ( editabled != nextEditabled ) {
				model.set( 'reRender', true );
			}
		} );
	}

	_addParam( model ) {
		let settings = {
			...model.get( 'settings' ),
			...{
				element: 'pa_column'
			}
		};

		model.set( 'settings', settings );
		model.set( 'editabled', this.indexOf( model ) < this.length );
	}

	/**
	 * re-calculator column width
	 */
	_calculatorColumnWidth( model ) {
		let numberColumn = this.length;
		let percentWidth = 100 / numberColumn;
		this.map( ( model ) => {
			let settings = {
				...model.get( 'settings' ),
				...{
					element: 'pa_column',
					class: 'pa-col-sm-' + Math.floor( 12 / numberColumn ),
					styles: {
						width : percentWidth
					}
				}
			};
			// settings.element = 'pa_column';
			// settings.class = 'pa-col-sm-' + Math.floor( 12 / numberColumn );
			// settings.styles.width = percentWidth;
			model.set( 'settings', settings );
			model.set( 'reRender', true );
		} );
	}

}
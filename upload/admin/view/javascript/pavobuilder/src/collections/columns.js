import Backbone from 'Backbone';
import ColumnModel from '../models/column'
import _ from 'underscore';

export default class ColumnsCollection extends Backbone.Collection {

	initialize( columns = [] ) {
		this.model = ColumnModel;
		this.on( 'update', this._update );
		this.on( 'add', this._addParam );
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
	_update() {
		this.models.map( ( model, index ) => {
			// editabled
			let screen = model.get( 'screen' );
			if ( screen === 'sm'|| screen === 'xs' ) {
				model.set( 'editabled', true );
			} else {
				let editabled = model.get( 'editabled' );
				let nextEditabled = index < this.length - 1;
				model.set( 'editabled', nextEditabled );
				if ( editabled != nextEditabled ) {
					model.set( 'reRender', true );
				}
			}
		} );
	}

	_addParam( model ) {
		// settings
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
			let responsive = model.get( 'responsive' );
			responsive[model.get( 'screen' )] = {
				cols: Math.floor( 12 / numberColumn ),
				width: percentWidth
			}
			model.set( 'responsive', responsive );
			model.set( 'reRender', true );
		} );
	}

}
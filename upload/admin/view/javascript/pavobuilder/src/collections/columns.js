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
			// resizabled
			let screen = model.get( 'screen' );
			if ( screen === 'sm' || screen === 'xs' ) {
				model.set( 'resizabled', true );
			} else {
				let resizabled = model.get( 'resizabled' );
				let nextEditabled = index < this.length - 1;
				model.set( 'resizabled', nextEditabled );
				if ( resizabled != nextEditabled ) {
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
		model.set( 'resizabled', this.indexOf( model ) < this.length );
	}

	/**
	 * re-calculator column width
	 */
	_calculatorColumnWidth( model, collection, y ) {
		let numberColumn = this.length;
		let percentWidth = 100 / numberColumn;
		if ( 12 % numberColumn === 0 ) {
			this.map( ( model ) => {
				let responsive = model.get( 'responsive' );
				responsive[model.get( 'screen' )] = {
					cols: Math.floor( 12 / numberColumn ),
					width: percentWidth
				}
				model.set( 'responsive', responsive );
				model.set( 'reRender', true );
			} );
		} else {
			let responsive = model.get( 'responsive' );
			let prevModel = this.at( y.index - 1 );
			if ( prevModel !== undefined ) {
				let newResponsive = prevModel.get( 'responsive' );
				_.map( responsive, ( data, screen ) => {
					if ( screen === 'lg' || screen === 'md' ) {
						newResponsive[screen].cols = parseInt( newResponsive[screen].cols ) + parseInt( responsive[screen].cols );
						let width = 0;
						if ( responsive[screen].styles  !== undefined && responsive[screen].styles.width !== undefined ) {
							width = parseInt( width ) + parseInt( responsive[screen].styles.width );// + parseInt( responsive[screen].cols );
						}
						if ( newResponsive[screen].styles !== undefined && newResponsive[screen].styles.width  !== undefined ) {
							width = parseInt( width ) + parseInt( newResponsive[screen].styles.width );
						}
					}
				} );
				prevModel.set( {
					reRender 	: true,
					responsive 	: newResponsive
				} )
			}
		}
	}

}
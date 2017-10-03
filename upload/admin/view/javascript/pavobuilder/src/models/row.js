import Backbone from 'Backbone';
import _ from 'underscore';
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
			widget : 'pa_row',
			screen : 'lg'
		}
	}

	_switchScreenMode( model ) {
		let screen = model.get( 'screen' );
		model.get( 'columns' ).map( ( column, index ) => {
			let oldScreen = column.get( 'screen' );
			column.set( 'screen', screen );
			let responsive = column.get( 'responsive' );
			let defaults = {
				lg: {},
				md: {},
				sm: {},
				xs: {},
			}

			if ( responsive[oldScreen] !== undefined ) {
				_.map( defaults, ( ob, key ) => {
					if ( responsive[key] === undefined || responsive[key].length === 0 ) {
						responsive[key] = responsive[oldScreen];
					}
				} );
			}

			column.set( 'responsive', responsive );

			if ( screen == 'sm' || screen == 'xs' ) {
				column.set( 'resizabled', true );
			} else {
				let resizabled = ( index + 1 ) < model.get( 'columns' ).length;
				column.set( 'resizabled', resizabled );
			}
		} );
	}

}
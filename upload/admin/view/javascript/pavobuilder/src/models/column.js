import Backbone from 'Backbone';
import _ from 'underscore';
import ElementsCollection from '../collections/elements';

export default class ColumnModel extends Backbone.Model {

	initialize( data = { settings: { styles: {} }, elements: [] } ) {
		this.set( 'elements', new ElementsCollection( data.elements ) );
		this.on( 'change:screen', this._switchScreenMode );
	}

	defaults() {
		return {
			settings : {
				element : 'pa_column'
			},
			responsive : {
				lg : {
					cols: 12,
					styles: {
						width : 100,
					}
				},
				md : {
					cols: 12
				},
				sm : {
					cols: 12
				},
				xs : {
					cols: 12
				}
			},
			elements : new ElementsCollection(),
			editing : false,
			adding : false,
			element_type : 'widget',
			reRender: false,
			widget : 'pa_column',
			screen: 'lg'
		};
	}

	_switchScreenMode( model, old ) {
		let screen = model.get( 'screen' );
		if ( screen == 'sm' || screen == 'xs' ) {
			this.set( 'editabled', true );
		}
		this.set( 'reRender', true );
	}

}
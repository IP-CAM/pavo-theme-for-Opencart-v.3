import Backbone from 'Backbone';
import ElementsCollection from '../collections/elements';

export default class ColumnModel extends Backbone.Model {

	initialize( data = { settings: { class: 'pa-col-sm-12', styles: {} }, elements: [] } ) {
		this.set( 'elements', new ElementsCollection( data.elements ) );
		this.on( 'change:screen', this._switchScreenMode );
	}

	defaults() {
		return {
			settings : {
				element : 'pa_column',
				styles : {
					width : '100%'
				},
				responsive : {
					normal : {
						cols: 12
					},
					laptop : {
						cols: 12
					},
					tablet : {
						cols: 12
					},
					mobile : {
						cols : 12
					}
				}
			},
			elements : new ElementsCollection(),
			editing : false,
			adding : false,
			element_type : 'widget',
			reRender: false,
			widget : 'pa_column'
		};
	}

	_switchScreenMode( model ) {
		// this.set( 'reRender', true );
	}

}
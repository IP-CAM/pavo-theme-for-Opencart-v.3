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
				element : 'pa_column'
			},
			responsive : {
				lg : {
					cols: 12,
					width : '100%',
					styles: {}
				},
				md : {
					cols: 12,
					width : '100%',
					styles: {}
				},
				sm : {
					cols: 12,
					width : '100%',
					styles: {}
				},
				xs : {
					cols : 12,
					width : '100%',
					styles: {}
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

	_switchScreenMode( model ) {
		if ( model.get( 'screen' ) !== 'lg' ) {

		}
		this.set( 'reRender', true );
	}

}
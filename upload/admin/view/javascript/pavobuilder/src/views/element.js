import Backbone from 'Backbone';
import _ from 'underscore';

export default class Column extends Backbone.View {

	initialize( element = {} ) {
		this.element = element;
		this.listenTo( this.element, 'destroy', this.remove );
		this.listenTo( this.element, 'change', this.render );
	}

	/**
	 * render html
	 */
	render() {
		this.template = _.template( $( '#pa-element-template' ).html(), { variable: 'data' } )( this.element );
		this.setElement( this.template );

		return this;
	}

	_removeHandler() {

	}

	_updateHandler() {

	}

}
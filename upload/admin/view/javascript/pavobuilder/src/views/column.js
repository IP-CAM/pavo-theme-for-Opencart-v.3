import Backbone from 'Backbone';
import _ from 'underscore';
import Element from './element';

export default class Column extends Backbone.View {

	constructor( collection = {} ) {
		super();

		// set columns is a collection
		this.elements = collection;
	}

	initialize() {
		
	}

	render() {
		_.map( this.elements, ( element, index ) => {
			// this.addElement( index, element );
		} );
	}

	addElement( index = -1, model = {} ) {
		// var template = _.template( $( '#pavobuilder-row-template' ).html() )( data );
		this.$el.append( new Element( model ) );
	}

	removeElement( index = -1 ) {

	}

	updateElement( index = -1 ) {

	}

}
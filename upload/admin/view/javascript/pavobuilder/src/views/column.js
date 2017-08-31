import Backbone from 'Backbone';
import _ from 'underscore';
import ElementsCollection from '../collections/elements';
import Element from './element';

export default class Column extends Backbone.View {

	constructor( elements = {} ) {
		super();

		// set columns is a collection
		this.elementsCollection = new ElementsCollection( elements );
	}

	render() {
		if ( this.elementsCollection.models ) {
			_.map( this.elementsCollection.models, ( element, index ) => {
				// map element models and add it as Element to ColumnView
				this.addElement( index, element );
			} );
		}
	}

	addElement( index = -1, model = {} ) {
		// var template = _.template( $( '#pavobuilder-row-template' ).html() )( data );
		this.$el.append( new Element( model.toJSON() ) );
	}

	removeElement( index = -1 ) {

	}

	updateElement( index = -1 ) {

	}

}
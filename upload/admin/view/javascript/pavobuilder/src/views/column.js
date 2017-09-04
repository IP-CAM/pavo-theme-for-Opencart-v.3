import Backbone from 'Backbone';
import _ from 'underscore';
import Element from './element';

export default class Column extends Backbone.View {

	initialize( column = { settings: {}, 'elements' : {} } ) {
		this.column = column;

		this.template = _.template( $( '#pa-column-template' ).html(), { variable: 'data' } )( this.column );
		this.setElement( this.template );
	}

	render() {
		if ( this.column.get( 'elements' ).models.length > 0 ) {
			_.map( this.column.get( 'elements' ).models, ( element ) => {
				// map element models and add it as Element to ColumnView
				this.addElement( element );
			} );
		} else {
			this.$el.addClass( 'empty-element' );
		}
		return this;
	}

	addElement( model = {} ) {
		this.$el.append( new Element( model.toJSON() ) );
		this.$el.find( '.pa-column-container' ).append( new Element( model ).render().el );
	}

	removeElement() {

	}

	updateElement() {

	}

}
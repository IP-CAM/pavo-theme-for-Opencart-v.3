import Backbone from 'Backbone';
import _ from 'underscore';
import Element from './element';

export default class Column extends Backbone.View {

	initialize( column = { settings: {}, 'elements' : {}, editabled: false } ) {
		this.column = column;

		this.events = {
			'click .pa-delete-column'	: 'deleteRowHandler'
		};

		this.listenTo( this.column, 'destroy', this.remove );
		this.listenTo( this.column, 'change:editabled', () => {
			this.$el.replaceWith( this.render().el );
		} );
		// delegate event
		// this.delegateEvents();
	}

	render() {

		var data = this.column.toJSON();
		data.cid = this.column.cid;
		this.template = _.template( $( '#pa-column-template' ).html(), { variable: 'data' } )( data );
		this.setElement( this.template );
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

	deleteRowHandler( e ) {
		e.preventDefault();
		// this.
		if ( confirm( this.$el.find( '.pa-delete-column' ).data( 'confirm' ) ) ) {
			// this.remove();
			this.column.destroy();
		}

		return false;
	}

}
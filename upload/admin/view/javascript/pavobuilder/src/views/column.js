import Backbone from 'Backbone';
import _ from 'underscore';
import ColumnModel from '../models/column'
import Element from './element';

export default class Column extends Backbone.View {

	initialize( column = { settings: {}, 'elements' : {}, editabled: false } ) {
		this.column = column;

		this.events = {
			'click .pa-delete-column'	: 'deleteColumnHandler'
		};

		this.listenTo( this.column, 'destroy', this.remove );
		// re-render html layout
		// because if is index > 0, we need resize column control
		this.listenTo( this.column, 'change:editabled', this._reRender );
		this.listenTo( this.column, 'change:reRender', this._reRender );
		// delegate event
		// this.delegateEvents();
	}

	/**
	 * Render html
	 */
	render() {

		var data = this.column.toJSON();
		data.cid = this.column.cid;

		this.template = _.template( $( '#pa-column-template' ).html(), { variable: 'data' } )( data );
		this.setElement( this.template );

		if ( this.column.get( 'elements' ).length > 0 ) {
			_.map( this.column.get( 'elements' ).models, ( element ) => {
				// map element models and add it as Element to ColumnView
				this.addElement( element );
			} );
		} else {
			this.$el.addClass( 'empty-element' );
		}

		// console.log( this.$( 'body' ).find( this.$el ) );
		
		return this;
	}

	/**
	 * Add element
	 */
	addElement( model = {} ) {
		this.$el.append( new Element( model.toJSON() ) );
		this.$el.find( '.pa-column-container' ).append( new Element( model ).render().el );
	}

	/**
	 * ReRender html layout
	 */
	_reRender( model ) {
		if ( model.get( 'reRender' ) ) {
			this.$el.replaceWith( this.render().el );
			this.column.set( 'reRender', false );
		}
	}

	/**
	 * Delete Column Handler
	 */
	deleteColumnHandler( e ) {
		e.preventDefault();
		// this.
		if ( confirm( this.$el.find( '.pa-delete-column' ).data( 'confirm' ) ) ) {
			// this.remove();
			this.column.destroy();
		}

		return false;
	}

}
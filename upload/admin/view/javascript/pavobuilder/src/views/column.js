import Backbone from 'Backbone';
import _ from 'underscore';
import ColumnModel from '../models/column'
import ElementsPopup from './globals/elements-popup';
import Element from './element';

export default class Column extends Backbone.View {

	initialize( column = { settings: {}, 'elements' : {}, editabled: false } ) {
		this.column = column;

		this.events = {
			'click .pa-delete-column'	: '_deleteColumnHandler',
			'click .pa-add-element'		: '_renderElementsPopup',
			'click .pa-clone'			: '_cloneHandler',
			'click .pa-edit-column'		: '_editHandler'
		};

		this.listenTo( this.column, 'destroy', this.remove );
		// re-render html layout
		// because if is index > 0, we need resize column control
		this.listenTo( this.column, 'change:editabled', this._reRender );
		this.listenTo( this.column, 'change:reRender', this._reRender );
		this.listenTo( this.column.get( 'elements' ), 'remove', this._onRemoveElements );

		// delegate event
		this.delegateEvents();
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
			this.column.get( 'elements' ).map( ( element ) => {
				// map element models and add it as Element to ColumnView
				this.addElement( element );
			} );
		} else {
			this.$el.addClass( 'empty-element' );
		}
		// this.$el.sortable();
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
		if ( this.column.get( 'reRender' ) ) {
			this.$el.replaceWith( this.render().el );
			this.column.set( 'reRender', false );
		}
	}

	/**
	 * Delete Column Handler
	 */
	_deleteColumnHandler( e ) {
		e.preventDefault();
		// this.
		if ( confirm( this.$el.find( '.pa-delete-column' ).data( 'confirm' ) ) ) {
			// this.remove();
			this.column.destroy();
		}

		return false;
	}

	/**
	 * Render Elements Popup list
	 */
	_renderElementsPopup( e ) {
		e.preventDefault();
		let button = $( e.target );
		let controls = button.parents( '.column-controls:first' );
		if ( controls.hasClass( 'top' ) ) {
			this.column.set( 'adding_position', 0 );
		} else {
			this.column.set( 'adding_position', this.column.get( 'elements' ).length );
		}

		// toggle 'adding'
		this.column.set( 'adding', ! this.column.get( 'adding' ) );
		// elements for select
		this.elementsList = new ElementsPopup( this.column );
		return false;
	}

	/**
	 * Clone click handler
	 */
	_cloneHandler( e ) {
		e.preventDefault();

		let button = $( e.target ).parents( '.pa-element-content:first' );
		let cid = button.data( 'cid' );
		let model = this.column.get( 'elements' ).get( { cid: cid } );
		let newModel = model.clone();
		let index = this.column.get( 'elements' ).indexOf( model );

		this.column.get( 'elements' ).add( newModel, { at: parseInt( index ) + 1 } );
		this.column.set( 'reRender', true );
		return false;
	}

	_onRemoveElements() {
		if ( this.column.get( 'elements' ).length === 0 ) {
			this.$el.addClass( 'empty-element' );
		}
	}

	/**
	 * Edit column handler
	 */
	_editHandler( e ) {
		e.preventDefault();

		return false;
	}

}
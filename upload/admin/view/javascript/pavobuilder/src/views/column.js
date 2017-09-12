import Backbone from 'Backbone';
import _ from 'underscore';
import sortable from 'jquery-ui/ui/widgets/sortable';
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
		// this.listenTo( this.column, 'change:editabled', this._reRender );
		this.listenTo( this.column, 'change:reRender', this._reRender );
		this.listenTo( this.column.get( 'elements' ), 'remove', this._onRemoveElement );
		this.listenTo( this.column.get( 'elements' ), 'add', this._onAddElement );

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
				this.addElement( element, this.column.get( 'elements' ) );
			} );
		} else {
			this.$el.addClass( 'empty-element' );
		}

		this.$( '.pa-column-container' ).sortable({
			connectWith : '.pa-column-container',
			items 		: '.pa-element-content',
			handle 		: '.pa-reorder',
			cursor 		: 'move',
			placeholder : 'pa-sortable-placeholder',
			receive 	: this._receive.bind( this ),
			start 		: this._start.bind( this ),
			tolerance	: 'pointer',
			update 		: this._update.bind( this )
		});

		return this;
	}

	/**
	 * Add element
	 */
	addElement( model = {}, collection = {}, data = {} ) {
		if ( data.at !== undefined && collection.length > 0 ) {
			$( this.$( '.pa-element-content' ).get( parseInt( data.at ) ) ).after( new Element( model ).render().el );
		} else {
			this.$( '.pa-column-container' ).append( new Element( model ).render().el );
		}
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

		this.column.get( 'elements' ).add( newModel, { at: parseInt( index ) } );
		this.column.set( 'reRender', true );
		return false;
	}

	/**
	 * on remove element
	 */
	_onRemoveElement() {
		if ( this.column.get( 'elements' ).length === 0 ) {
			this.$el.addClass( 'empty-element' );
		}
	}

	/**
	 * On add new element to ElementsCollection
	 */
	_onAddElement( element, collection, data ) {
		this.addElement( element, this.column.get( 'elements' ), data );
	}

	/**
	 * Edit column handler
	 */
	_editHandler( e ) {
		e.preventDefault();

		return false;
	}

	/**
	 * Recieved element from other column
	 */
	_receive( e, ui ) {
		this.column.get( 'elements' ).add( ui.item.element.toJSON(), { at: ui.item.index() - 1, sort: false } );
		console.log( this.column );
        ui.item.element.destroy();
	}

	/**
	 * Start drag event
	 * set indexStart, elements - collection data, element - model
	 */
	_start( e, ui ) {
		ui.item.indexStart = ui.item.index();
        ui.item.elements = this.column.get( 'elements' );
        ui.item.element = this.column.get( 'elements' ).at( ui.item.index() );
	}

	/**
	 * Update when sortable
	 * just useful when update elements inside drop event
	 */
	_update( e, ui ) {
		// resort elements collection
		this.column.get( 'elements' ).moveItem( ui.item.indexStart, ui.item.index() );
		// trigger drop event element
		ui.item.trigger( 'drop', ui.item.index() );
	}

}
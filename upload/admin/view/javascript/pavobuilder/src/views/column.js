import Backbone from 'Backbone';
import _ from 'underscore';
import sortable from 'jquery-ui/ui/widgets/sortable';
import ColumnModel from '../models/column'
import EditForm from './globals/edit-form';
import ElementsPopup from './globals/elements-popup';
import Element from './element';
import resizable from 'jquery-ui/ui/widgets/resizable';

export default class Column extends Backbone.View {

	initialize( column = { settings: {}, 'elements' : {}, editabled: false } ) {
		this.column = column;

		this.events = {
			'click .pa-delete-column'	: '_deleteColumnHandler',
			'click .pa-add-element'		: '_renderElementsPopup',
			'click .pa-clone'			: '_cloneHandler',
			'click .pa-edit-column'		: '_editHandler',
			'resize'					: ( e, ui ) => {

				let columns = 12;
				let fullWidth = this.$el.parent().width();
				if ( fullWidth == 0 ) return;
				let columnWidth = fullWidth / columns;
				let totalCol;
				let target = ui.element;
		        let next = target.next();

				let currentCol = Math.round( target.width() / columnWidth );
        		let nextColumnCount = Math.round( next.width() / columnWidth );

        		let settings = this.column.get( 'settings' );

        		settings = Object.assign( settings, {
    				class : 'pa-col-sm-' + currentCol,
        			width : ( ui.size.width * 100 ) / fullWidth
        		} );
        		this.column.set( 'settings', settings );
			},
			// trigger save next column when resize events
			'trigger_save_next_column'			: ( e, data = { cid: '', settings: {} } ) => {
				if ( data.cid == this.column.cid ) {
					let settings = Object.assign( this.column.get( 'settings' ), data.settings );
					this.column.set( 'settings', data.settings );
					this.column.set( 'reRender', true );
				}
			}
		};

		this.listenTo( this.column, 'destroy', this.remove );
		// re-render html layout
		// because if is index > 0, we need resize column control
		this.listenTo( this.column, 'change:reRender', this._reRender );
		this.listenTo( this.column, 'change:editing', this._renderEditColumnForm );

		this.listenTo( this.column.get( 'elements' ), 'remove', this._onRemoveElement );
		this.listenTo( this.column.get( 'elements' ), 'add', this.addElement );

		// delegate event
		this.delegateEvents();
	}

	/**
	 * Render html
	 */
	render() {

		new Promise( ( resolve, reject ) => {
			var data = this.column.toJSON();
			data.cid = this.column.cid;

			this.template = _.template( $( '#pa-column-template' ).html(), { variable: 'data' } )( data );
			this.setElement( this.template );

			if ( this.column.get( 'elements' ).length > 0 ) {
				this.column.get( 'elements' ).map( ( element, at, collection ) => {
					// map element models and add it as Element to ColumnView
					this.addElement( element, collection, { at: at } );
				} );
			} else {
				this.$el.addClass( 'empty-element' );
			}

			resolve();
		} ).then( () => {
			// sortable
			this.$( '.pa-column-container' ).sortable({
				connectWith : '.pa-column-container',
				items 		: '.pa-element-content',
				handle 		: '.pa-reorder, > .right-controls > .pa-reorder-row',
				cursor 		: 'move',
				placeholder : 'pa-sortable-placeholder',
				receive 	: this._receive.bind( this ),
				start 		: this._start.bind( this ),
				tolerance	: 'pointer',
				update 		: this._update.bind( this )
			});

			// resizable
			if ( this.column.get( 'editabled' ) ) {
				let columns = 12;
				let fullWidth = this.$el.parent().outerWidth();
				if ( fullWidth == 0 ) return;
				let columnWidth = fullWidth / columns;
				let totalCol;

				this.$el.resizable({
				    handles: 'e',
				    start: ( event, ui ) => {
				      	let target = ui.element;
				        let next = target.next();
				        let targetCol = Math.floor( target.outerWidth() / columnWidth );
				        let nextCol = Math.floor( next.outerWidth() / columnWidth );
				      	// set totalColumns globally
				      	totalCol = targetCol + nextCol;
				      	// ui.size.nextCol = nextCol;
				      	// ui.size.currentCol = targetCol;
				      	ui.size.nextOriginWidth = next.outerWidth();

				      	target.resizable( 'option', 'minWidth', columnWidth );
				      	target.resizable( 'option', 'maxWidth', ( ( totalCol - 1 ) * columnWidth ) );
				    },
				    resize: ( event, ui ) => {
				      	let target = ui.element,
				        	next = target.next();

						let currentCol = Math.floor( target.outerWidth() / columnWidth );

			        	if ( ui.size.width > ui.originalSize.width ) {
			        		next.width( ui.size.nextOriginWidth - ( ui.size.width - ui.originalSize.width ) );
			        		// next.width( ( ( ui.size.nextOriginWidth - ( ui.size.width - ui.originalSize.width ) ) * 100 / fullWidth ) + '%' );
			        	} else {
			        		next.width( ui.size.nextOriginWidth + ( ui.originalSize.width - ui.size.width ) );
		        			// next.width( ( ( ui.size.nextOriginWidth + ( ui.originalSize.width - ui.size.width ) ) * 100 / fullWidth ) + '%' );
			        	}
				    },
				    stop: ( event, ui ) => {
				    	let columns = 12;
						let fullWidth = this.$el.parent().width();
						let columnWidth = fullWidth / columns;
				    	let target = ui.element;
				        let next = target.next();
        				let nextColumnCount = Math.round( next.width() / columnWidth );

		        		new Promise( ( resolve, reject ) => {

			        		// trigger save next column
			        		next.trigger( 'trigger_save_next_column', {
			        			cid: next.data( 'cid' ),
			    				settings: {
			    					class : 'pa-col-sm-' + nextColumnCount,
			        				width : ( next.width() * 100 ) / fullWidth
			    				}
			        		} );

			        		resolve();
		        		} ).then( () => {
				    		this.column.set( 'reRender', true );
		        		} );
				    }
		  		});
			}
		} );

		return this;
	}

	/**
	 * Add element
	 */
	addElement( model = {}, collection = {}, data = {} ) {
		if ( this.$( '> .pa-element-wrapper > .pa-column-container > .pa-element-content' ).length > data.at && data.at ) {
			$( this.$( '> .pa-element-wrapper > .pa-column-container > .pa-element-content' ).get( parseInt( data.at ) - 1 ) ).after( new Element( model ).render().el );
		} else if ( data.at == 0 ) {
			this.$( '> .pa-element-wrapper > .pa-column-container' ).prepend( new Element( model ).render().el );
		} else {
			this.$( '> .pa-element-wrapper > .pa-column-container' ).append( new Element( model ).render().el );
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
		if ( confirm( this.$( '.pa-delete-column' ).data( 'confirm' ) ) ) {
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

		let index = this.column.get( 'elements' ).indexOf( model );

		let newModel = model.clone();

		this.column.get( 'elements' ).add( newModel, { at: parseInt( index ) + 1 } );
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
	 * Edit column handler
	 */
	_editHandler( e ) {
		e.preventDefault();
		this.column.set( 'editing', true );
		return false;
	}

	/**
	 * Recieved element from other column
	 */
	_receive( e, ui ) {
		new Promise( ( resolve, reject ) => {
			let index = ui.item.index();
			this.column.get( 'elements' ).add( ui.item.element.toJSON(), { at: index } );
			resolve();
		} ).then( () => {
			ui.item.element.destroy();
		} );
	}

	/**
	 * Start drag event
	 * set indexStart, elements - collection data, element - model
	 */
	_start( e, ui ) {
		ui.item.indexStart = ui.item.index();
        ui.item.elements = this.column.get( 'elements' );
        ui.item.element = this.column.get( 'elements' ).at( ui.item.indexStart );
	}

	/**
	 * Update when sortable
	 * just useful when update elements inside drop event
	 */
	_update( e, ui ) {

		if ( ui.item.elements !== this.column.get( 'elements' ) ) {
			return;
		}
		let index = ui.item.index();
		// resort elements collection
		this.column.get( 'elements' ).moveItem( ui.item.indexStart, index );
		// trigger drop event element
		ui.item.trigger( 'drop', index );
	}

	/**
	 * render edit form if 'editing' is true
	 */
	_renderEditColumnForm( model ) {
		if ( model.get( 'editing' ) === true ) {
			// row edit form
			let editForm = new EditForm( model, PA_PARAMS.languages.entry_edit_column_text, PA_PARAMS.element_fields.pa_column );
		}
	}

}
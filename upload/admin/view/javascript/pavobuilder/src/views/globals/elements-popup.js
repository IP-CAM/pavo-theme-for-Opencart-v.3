import Backbone from 'Backbone';
import _ from 'underscore';

export default class ElementsPopup extends Backbone.View {

	/**
	 * Initialize popup class
	 */
	initialize( column = {} ) {
		this.column = column;

		// trigger remove popup when model have been destroyed
		this.listenTo( this.column, 'destroy', this.remove );
		this.listenTo( this.column, 'change:adding', this._toggleShow );

		this.events = {
			'click .element-item'	: '_addElementHandler'
		};

		// render after class called
		this.render();
	}

	/**
	 * Render popup
	 */
	render() {
		if ( this.column.get( 'adding' ) ) {
			this.template = $( '#pa-elements-panel' ).html();
			this.template = _.template( this.template, { variable: 'data' } )( this.column.toJSON() );
			this.setElement( this.template );

			$( 'body' ).append( this.el );
			$( 'body' ).find( this.$el ).modal( 'show' );
			$( 'body' ).find( this.$el ).on( 'hidden.bs.modal', ( e ) => {
				this.column.set( 'adding', false );
			} );

			// $( '#nav-elements-all' ).
			this.$el.find( '.pa-col-sm-3' ).map( ( index, element ) => {
				let clone = $( element ).clone();
				$( '#nav-elements-all' ).append( clone );
			} );
		}

		return this;
	}

	/**
	 * Toggle show popup
	 */
	_toggleShow( model ) {
		if ( ! model.get( 'adding' ) ) {
			this.remove();
		}
	}

	/**
	 * Close
	 */
	_close() {
		$( 'body' ).find( this.$el ).modal( 'hide' );
		this.column.set( 'adding', false );
	}

	/**
	 * Add element to current column
	 */
	_addElementHandler( e ) {
		e.preventDefault();
		let button = $( e.target );
		if ( e.target.nodeName !== 'A' ) {
			button = $( e.target ).parents( 'a:first' );
		}

		let settings = button.data();
		if ( settings.elements !== undefined ) {
			delete settings.elements;
		}
console.log( settings );
		if ( settings.widget === 'pa_row' ) {
			settings.row = {
					settings: {
						element: 'pa_row'
					},
					columns: {
						settings: {
							class: 'pa-col-sm-12',
							element: 'pa_column'
						},
						elements: []
					}
				};
		}

		// add new element to column
		this.column.get( 'elements' ).add( settings, { at: this.column.get( 'adding_position' ) } );
		// set 'reRender' true to re-generate column
		// this.column.set( 'reRender', true );
		this.column.set( 'adding_position', false );
		// close model
		this._close();
		return false;
	}

}
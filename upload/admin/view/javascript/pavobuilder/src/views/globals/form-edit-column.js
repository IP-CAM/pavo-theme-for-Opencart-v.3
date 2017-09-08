import Backbone from 'Backbone';
import _ from 'underscore';

export default class FormEditColumn extends Backbone.View {

	/**
	 * Constructor class
	 */
	initialize( column = { settings: {}, elements: {} } ) {
		// super();
		// column is ColumnModel
		this.column = column;
		this.template = _.template( $( '#pa-edit-column-template' ).html(), { variable: 'data' } );
		this.listenTo( this.column, 'change:editing', this._toggle_form );
		this.listenTo( this.column, 'destroy', this.remove );

		this.events = {
			'click .btn.pa-close'	: '_closeHandler',
			'click .btn.pa-update'	: '_updateHandler'
		}

		this.render();
	}

	/**
	 * Render html
	 */
	render() {
		if ( this.column.get( 'editing' ) ) {
			let template = this.template( this.column.toJSON() );
			this.setElement( template );
			$( 'body' ).append( this.el );
			$( 'body' ).find( this.$el ).modal( 'show' );
			$( 'body' ).find( this.$el ).on( 'hidden.bs.modal', ( e ) => {
				this.column.set( 'editing', false );
			} );
		}
		return this;
	}

	/**
	 * Toggle show edit form
	 */
	_toggle_form( model ) {
		if ( ! model.get( 'editing' ) ) {
			this.remove();
		}
	}

	/**
	 * Close handler click
	 */
	_closeHandler( e ) {
		e.preventDefault();
		this._close();
		return false;
	}

	/**
	 * Close modal and, set 'editing' false
	 *
	 * when model change 'setting' to false view will be lose
	 */
	_close() {
		$( 'body' ).find( this.$el ).modal( 'hide' );
		this.column.set( 'editing', false );
	}

	/**
	 * Update column settings
	 */
	_updateHandler( e ) {
		e.preventDefault();

		new Promise( ( resolve, reject ) => {
			let data = this.$el.find( '#pa-edit-column-settings' ).serializeArray();
			let settings = this.serializeFormJSON( data );
			// settings = { ...this.column.get('settings'), settings };
			this.column.set( 'settings', settings );
			// call close method
			resolve();
		} ).then(() => {
			this._close();
		});
		return false;
	}

	/**
	 * Convert serialize string data to json data
	 */
	serializeFormJSON ( serialize = '' ) {

        var results = {};

        serialize.map( ( ob, name ) => {
        	if ( results[ob.name] ) {
                if ( ! results[ob.name].push) {
                    results[ob.name] = [ results[ob.name] ];
                }
                results[ob.name].push(ob.value || '');
            } else {
                results[ob.name] = ob.value || '';
            }
        } );

        return results;
    };

}
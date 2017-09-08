import Backbone from 'Backbone';
import _ from 'underscore';

export default class FormEditRow extends Backbone.View {

	/**
	 * Constructor class
	 */
	initialize( row = { settings: {}, columns: {} } ) {
		// super();
		// row is RowModel
		this.row = row;
		this.template = _.template( $( '#pa-edit-row-template' ).html(), { variable: 'data' } );
		this.listenTo( this.row, 'change:editing', this._toggle_form );
		this.listenTo( this.row, 'destroy', this.remove );

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
		if ( this.row.get( 'editing' ) ) {
			let template = this.template( this.row.toJSON() );
			this.setElement( template );
			$( 'body' ).append( this.el );
			$( 'body' ).find( this.$el ).modal( 'show' );
			$( 'body' ).find( this.$el ).on( 'hidden.bs.modal', ( e ) => {
				this.row.set( 'editing', false );
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
		this.row.set( 'editing', false );
	}

	/**
	 * Update row settings
	 */
	_updateHandler( e ) {
		e.preventDefault();

		new Promise( ( resolve, reject ) => {
			let data = this.$el.find( '#pa-edit-row-settings' ).serializeArray();
			let settings = this.serializeFormJSON( data );
			// settings = { ...this.row.get('settings'), settings };
			this.row.set( 'settings', settings );
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
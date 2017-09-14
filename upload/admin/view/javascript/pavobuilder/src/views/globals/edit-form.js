import Backbone from 'Backbone';
import _ from 'underscore';
import Common from '../../common/functions';
import serializeJSON from 'jquery-serializejson';

export default class EditForm extends Backbone.View {

	/**
	 * Constructor class
	 */
	initialize( data = { settings: {}, columns: {} }, title = '', fields = [] ) {
		// super();
		// data is a Model
		this.data = data;
		this.title = title;
		this.fields = fields;

		this.template = _.template( $( '#pa-edit-form-template' ).html(), { variable: 'data' } );
		this.listenTo( this.data, 'change:editing', this._toggle_form );
		this.listenTo( this.data, 'destroy', this.remove );

		this.events = {
			'click .btn.pa-close'	: '_closeHandler',
			'click .btn.pa-update'	: '_updateHandler',
			'change #animate-select': '_effectChange'
		}

		this.render();
	}

	/**
	 * Render html
	 */
	render() {
		if ( this.data.get( 'editing' ) ) {
			let data = this.data.toJSON();
			data.edit_title = this.title;
			let template = this.template( data );
			this.setElement( template );
			$( 'body' ).append( this.el );
			$( 'body' ).find( this.$el ).modal( 'show' );
			$( 'body' ).find( this.$el ).on( 'hidden.bs.modal', ( e ) => {
				this.data.set( 'editing', false );
			} );

			let tabs = [];
			_.map( this.fields, ( fields, tab ) => {
				tabs.push({
					tab: tab,
					label: fields.label
				});
			} );

			this.$( '#pa-edit-form-settings' ).html( _.template( $( '#pa-modal-panel' ).html(), { variable: 'data' } )( { tabs: tabs } ) );

			let settings = this.data.get( 'settings' );
			// render fields inside modal content
			_.map( this.fields, ( fields, tab ) => {
				_.map( fields.fields, ( field, key ) => {
					this.$( '#nav-' + tab ).append( _.template( $( '#pa-' + field.type + '-form-field' ).html(), { variable: 'data' } )( { field: field, settings: settings } ) );
				} );
			} );

			// init thirparty scripts
			Common.init_thirparty_scripts();
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
		this.data.set( 'editing', false );
	}

	/**
	 * Update data settings
	 */
	_updateHandler( e ) {
		e.preventDefault();

		new Promise( ( resolve, reject ) => {
			let settings = this.$el.find( '#pa-edit-form-settings' ).serializeJSON();
			settings = { ...this.data.get('settings'), ...settings };
			this.data.set( 'settings', settings );
			// call close method
			resolve();
		} ).then(() => {
			this._close();
		});
		return false;
	}

	/**
	 * change data effect
	 */
	_effectChange( e ) {
		e.preventDefault();
		let select = $( e.target );
		let effect = select.val();

		this.$( '#animate-preview' ).attr( 'class', 'animated' );
		setTimeout( () => {
			this.$( '#animate-preview' ).addClass( effect );
		}, 100 );
		return false;
	}

}
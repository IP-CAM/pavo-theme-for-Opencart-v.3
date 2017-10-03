import Backbone from 'Backbone';
import _ from 'underscore';
import Common from '../../common/functions';
import serializeJSON from 'jquery-serializejson';

export default class EditForm extends Backbone.View {

	/**
	 * Constructor class
	 */
	initialize( data = { settings: {} }, title = '', fields = [] ) {
		// super();
		// data is a Model
		this.data = data;
		this.title = title;
		this.fields = fields;

		// this.data.set( 'fields', fields );
		this.template = _.template( $( '#pa-edit-form-template' ).html(), { variable: 'data' } );
		this.listenTo( this.data, 'change:editing', this._toggle_form );
		this.listenTo( this.data, 'destroy', this.remove );

		this.events = {
			'click .btn.pa-close'		: '_closeHandler',
			'click .btn.pa-update'		: '_updateHandler',
			'change #animate-select'	: '_effectChange'
		}

		this.render();
	}

	/**
	 * Render html
	 */
	render() {
		if ( this.data.get( 'editing' ) == true ) {
			let data = this.data.toJSON();
			data.edit_title = this.title;
			let template = this.template( data );
			this.setElement( template );
			$( 'body' ).append( this.el );
			$( 'body' ).find( this.$el ).modal( 'show' );
			$( 'body' ).find( this.$el ).on( 'hidden.bs.modal', ( e ) => {
				this.data.set( 'editing', false );
			} );
			// render fields
			this.renderFields();
		}
		return this;
	}

	/**
	 * render edit fields
	 */
	renderFields() {
		if ( this.data.get( 'widget' ) !== undefined && this.fields.length == 0 ) {
			let settings = this.data.get( 'settings' );
			if ( PA_PARAMS.element_fields[ this.data.get( 'widget' ) ] !== undefined ) {
				this.fields = PA_PARAMS.element_fields[ this.data.get( 'widget' ) ];
				this.$( '#pa-edit-form-settings' ).addClass( this.data.get( 'widget' ) )
			}
		}

		let tabs = [];
		_.map( this.fields, ( fields, tab ) => {
			tabs.push({
				tab: tab,
				label: fields.label// != undefined ? PA_PARAMS.languages[fields.label] : ''
			});
		} );

		if ( tabs.length > 0 ) {
			this.$( '#pa-edit-form-settings' ).html( _.template( $( '#pa-modal-panel' ).html(), { variable: 'data' } )( { tabs: tabs } ) );
			let settings = this.data.get( 'settings' );
			// clone new settings
			let cloneSettings = {...settings};
			// render fields inside modal content
			_.map( this.fields, ( fields, tab ) => {
				// clone to new object is required
				let clonefields = { ...fields };
				// if ( clonefields.label != undefined ){
				// 	clonefields.label = PA_PARAMS.languages[clonefields.label];
				// }
				_.map( clonefields.fields, ( field, key ) => {
					let cloneField = { ...field };
					// if ( cloneField.label != undefined ){
					// 	cloneField.label = PA_PARAMS.languages[cloneField.label];
					// }

					// if ( cloneField.options != undefined ) {
					// 	for ( let i = 0; i < cloneField.options.length; i++ ) {
					// 		if ( cloneField.options[i].label !== undefined ) {
					// 			cloneField.options[i].label = PA_PARAMS.languages[cloneField.options[i].label] !== undefined ? PA_PARAMS.languages[cloneField.options[i].label] : cloneField.options[i].label;
					// 		}
					// 	}
					// }
					if ( cloneField.type == 'select-animate' ) {
						cloneField.type = 'select';
						cloneField.groups = true;
						cloneField.options = PA_PARAMS.animate_groups ? PA_PARAMS.animate_groups : PA_PARAMS.animates;
					}

					// set default values
					cloneField.value = cloneSettings[cloneField.name] !== undefined ? cloneSettings[cloneField.name] : ( cloneField.default !== undefined ? cloneField.default : false );
					this.$( '#nav-' + tab ).append( _.template( $( '#pa-' + cloneField.type + '-form-field' ).html(), { variable: 'data' } )( { field: cloneField, settings: cloneSettings } ) );
				} );
			} );
		}

		// init thirdparty scripts
		Common.init_thirdparty_scripts( this.data );
	}

	/**
	 * Toggle show edit form
	 */
	_toggle_form( model ) {
		if ( ! model.get( 'editing' ) ) {
			$( 'body' ).find( this.$el ).modal( 'hide' );
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
		$( 'body' ).find( '.sp-container' ).remove();
		this.data.set( 'editing', false );
	}

	/**
	 * Update data settings
	 */
	_updateHandler( e ) {
		e.preventDefault();
		new Promise( ( resolve, reject ) => {
			let settings = this.$el.find( '#pa-edit-form-settings' ).serializeJSON();
			if ( this.data === 'pa_column' ) {
				console.log( settings );
			} else {
				settings = { ...this.data.get('settings'), ...settings };
			}
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
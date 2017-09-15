import Backbone from 'Backbone';
import _ from 'underscore';
import $ from 'jquery';
import EditForm from './globals/edit-form';

export default class Element extends Backbone.View {

	initialize( element = {} ) {
		this.element = element;

		this.events = {
			'click .pa-delete'		: '_removeHandler',
			'click .pa-edit'		: '_editHandler',
			'click .pa-reorder'		: () => {
				return false;
			}
		};
		this.listenTo( this.element, 'destroy', this.remove );
		this.listenTo( this.element, 'change', this.reRender );
		this.listenTo( this.element, 'change:editing', this.renderElementEditForm );
	}

	/**
	 * render html
	 */
	render() {
		let data = this.element.toJSON();
		data.cid = this.element.cid;
		this.template = _.template( $( '#pa-element-template' ).html(), { variable: 'data' } )( data );
		this.setElement( this.template );

		return this;
	}

	/**
	 * Remove click handler
	 */
	_removeHandler( e ) {
		e.preventDefault();
		if ( confirm( this.$el.data( 'confirm' ) ) ) {
			this.element.destroy();
		}
		return false;
	}

	/**
	 * Edit click handler
	 */
	_editHandler( e ) {
		e.preventDefault();
		this.element.set( 'editing', ! this.element.get( 'editing' ) );
		if ( this.element.get( 'element_type' ) == 'module' ) {
			let url = PA_PARAMS.site_url + 'admin/index.php?route=extension/module/' + this.element.get( 'moduleCode' ) + '&module_id='+ this.element.get( 'moduleId' ) + '&user_token=' + PA_PARAMS.user_token;
			let html = '<div class="loading text-center"><i class="fa fa-spinner fa-spin" aria-hidden="true"></i></div>';
			html += '<iframe id="pa-iframe-edit-module" src="'+url+'"></iframe>';
			this.editForm.$( '#pa-edit-form-settings' ).replaceWith( html );
			let loaded = true;
			this.editForm.$( '#pa-iframe-edit-module' ).on( 'load', () => {
				loaded = ! loaded;
				if ( loaded ) {
					this.element.set( 'editing', false );
				} else {
					this.editForm.$( '.loading' ).remove();
			 		this.editForm.$( '#pa-iframe-edit-module' ).contents().find( '#header' ).remove();
			 		this.editForm.$( '#pa-iframe-edit-module' ).contents().find( '#column-left' ).remove();
			 		this.editForm.$( '#pa-iframe-edit-module' ).contents().find( '#footer' ).remove();
			 		this.editForm.$( '.pa-update' ).remove();
				}
 			} );
			// $.ajax({
			// 	url: PA_PARAMS.site_url + 'admin/index.php?route=extension/module/pavobuilder/editModule&module_id='+ this.element.get( 'moduleId' ) +'&user_token=' + PA_PARAMS.user_token,
			// 	type: 'GET',
			// 	data: this.element.toJSON(),
			// 	beforeSend: function(){
			// 		this.element.set( 'editing', ! this.element.get( 'editing' ) );
			// 	}.bind( this )
			// }).done( ( html ) => {
			// 	this.editForm.$( '#pa-edit-form-settings' ).html( html );
			// } ).fail( () => {

			// } );
		}
		return false;
	}

	/**
	 * re-render if model has changed
	 */
	reRender() {
		this.$el.replaceWith( this.render().el );
	}

	/**
	 * Render Edit Element Form
	 */
	renderElementEditForm( model ) {
		if ( model.get( 'editing' ) === true ) {
			this.editForm = new EditForm( model, PA_PARAMS.languages.entry_edit_element_text );
		}
	}

}
import Backbone from 'Backbone';
import _ from 'underscore';
import Row from './row';
import $ from 'jquery';
import EditForm from './globals/edit-form';

export default class Element extends Backbone.View {

	initialize( element = {} ) {
		this.element = element;

		this.events = {
			'click .pa-delete:not(.pa-delete-row)'						: '_removeHandler',
			'click .pa-edit:not(.pa-edit-row)'							: '_editHandler',
			'click .pa-edit-column-num'									: '_changeColumnsInnerHandler',
			'click .pa-reorder'											: () => {
				return false;
			}
		};
		this.listenTo( this.element, 'destroy', this.remove );
		this.listenTo( this.element, 'change', this.reRender );
		this.listenTo( this.element, 'change:editing', this.renderElementEditForm );
		this.listenTo( this.element.get( 'row' ), 'destroy', () => {
			this.element.destroy();
		} );
	}

	/**
	 * render html
	 */
	render() {
		let data = this.element.toJSON();
		data.cid = this.element.cid;
		if ( this.element.get( 'row' ) !== undefined ) {
			let wrapper = '<div class="pa-element-content pa_row" data-cid="' + data.cid + '" data-confirm="' + PA_PARAMS.languages.confirm_element_column + '"></div>';
			this.template = $( wrapper ).append( new Row( this.element.get( 'row' ) ).render().el ).get( 0 );
		} else {
			let widget = this.element.get( 'widget' );
			if ( widget && PA_PARAMS.element_mask[widget] !== undefined ) {
				data = { ...data, ...PA_PARAMS.element_mask[widget] };
			}
			this.template = _.template( $( '#pa-element-template' ).html(), { variable: 'data' } )( data );
		}
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

	_setEditRowHandler( e ) {
		e.preventDefault();
		this.element.get( 'row' ).set( 'editing', true );
		return false;
	}

	/**
	 * Edit click handler
	 */
	_editHandler( e ) {
		e.preventDefault();
		this.element.set( 'editing', true );
		if ( this.element.get( 'element_type' ) == 'module' ) {
			let url = PA_PARAMS.site_url + 'admin/index.php?route=extension/module/' + this.element.get( 'moduleCode' ) + '&module_id='+ this.element.get( 'moduleId' ) + '&user_token=' + PA_PARAMS.user_token;
			let html = '<div class="loading text-center"><i class="fa fa-spinner fa-spin" aria-hidden="true"></i></div>';
			html += '<iframe id="pa-iframe-edit-module" src="' + url + '"></iframe>';
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

	_changeColumnsInnerHandler( e ) {
		e.preventDefault();
		if ( ! this.element.get( 'widget' ) || this.element.get( 'widget' ) != 'pa_row' )
			return false;
		let button = $( e.target );
		let columns_count = button.data('columns');
		let classWrapper = 'pa-col-sm-' + Math.floor( 12 / parseInt( columns_count ) );

		let newColumnsObject = [];
		for ( let i = 0; i < columns_count; i++ ) {
			newColumnsObject.push({
				class: classWrapper
			});
		}

		if ( newColumnsObject.length >= this.element.get( 'row' ).get( 'columns' ).length ) {
			// current columns < columns number selected
			for ( let i = 0; i < newColumnsObject.length; i++ ) {
				let model = this.element.get( 'row' ).get( 'columns' ).at( i );
				if ( typeof model !== 'undefined' ) {
					let settings = model.get( 'settings' );
					settings.class = newColumnsObject[i].class;
					model.set( 'settings', settings );
					model.set( 'reRender', true );
				} else {
					let newModel = {
						settings: {
							class: newColumnsObject[i].class,
							elements: []
						}
					};
					this.element.get( 'row' ).get( 'columns' ).add( newModel );
				}
			}
		} else {
			// current columns > columns number selected
			var elements = [];
			var lastest_column_index = false;
			this.element.get( 'row' ).get( 'columns' ).map( ( model, index ) => {
				if ( typeof newColumnsObject[index] !== 'undefined' ) {
					let settings = model.get( 'settings' );
					settings.class = newColumnsObject[index].class;
					model.set( 'settings', settings );
					model.set( 'reRender', true );

					// lastest index if columns collection 
					lastest_column_index = index;
				} else if ( lastest_column_index !== false ) {
					new Promise(function(resolve, reject) {
						var cloneModel = model;
						// check elements inside column if > 0, we will add it to lastest column
						if ( typeof cloneModel.get( 'elements' ) !== 'undefined' && cloneModel.get( 'elements' ).length > 0 ) {
							elements.push( cloneModel.get( 'elements' ).toJSON() );
						}

						if ( index == lastest_column_index ) {
							this.element.get( 'row' ).get( 'columns' ).at( lastest_column_index ).set( 'elements', elements );
						}

						// call destroy method after update columns collection
						resolve();
				    }).then( () => {
				    	model.destroy();
				    });
				}
			} );
		}
		return false;
	}

}
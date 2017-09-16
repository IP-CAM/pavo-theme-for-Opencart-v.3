import Backbone from 'Backbone';
import _ from 'underscore';
import Column from './column';
import EditForm from './globals/edit-form';

export default class Row extends Backbone.View {

	/**
	 * Constructor class
	 */
	initialize( row = { settings: {}, columns: {} } ) {
		// set backbone model
		this.row = row;

		this.events = {
			'click .pa-delete-row'		: '_deleteRowHandler',
			'click .pa-add-column'		: '_addColumnHandler',
			'click .pa-edit-column-num'	: '_changeColumnsHandler',
			'click .pa-edit-row'		: '_setEditRowHandler',
			'click .pa-reorder'			: () => {
				return false;
			}
		}

		// listen this.row model
		this.listenTo( this.row, 'destroy', this.remove );
		this.listenTo( this.row.get( 'columns' ), 'add', this.addColumn );
		this.listenTo( this.row, 'change:editing', this.renderEditRowForm );
	}

	/**
	 * Render html
	 */
	render() {
		let data = this.row.toJSON();
		data.cid = this.row.cid;
		this.template = _.template( $( '#pa-row-template' ).html(), { variable: 'data' } )( data );
		this.setElement( this.template );
		// each collection
		if ( this.row.get( 'columns' ).length > 0 ) {
			this.row.get( 'columns' ).map( ( model ) => {
				// map column models add add it to Row View
				this.addColumn( model );
			} );
		}

		setTimeout( () => {
			this.$el.removeClass( 'row-fade-in' );
		}, 1000 );
		return this;
	}

	/**
	 * Add Column
	 */
	addColumn( model = {} ) {
		this.$el.find( '.pav-row-container' ).append( new Column( model ).render().el );
	}

	/**
	 * Delete row handler
	 */
	_deleteRowHandler() {
		// this.
		if ( confirm( this.$( '.pa-delete-row' ).data( 'confirm' ) ) ) {
			// this.remove();
			this.row.destroy();
		}
		return false;
	}

	/**
	 * Add column handler
	 */
	_addColumnHandler( e ) {
		// stop event default
		e.preventDefault();
		let columns = this.row.get( 'columns' ).length + 1;
		let classWrapper = 'pa-col-sm-' + Math.floor( 12 / parseInt( columns ) );
		if ( this.row.get( 'columns' ).length >= 12 ) {
			classWrapper = 'pa-col-sm-12';
		}

		this.row.get( 'columns' ).map( ( model ) => {
			let settings = model.get( 'settings' );
			settings.class = classWrapper;
			model.set( 'settings', settings );
			model.set( 'reRender', true );
		} );
		this.row.get( 'columns' ).add({
			settings: {
				element: 'pa_column',
				class: classWrapper
			}
		});
		return false;
	}

	/**
	 * Change Columns of row
	 */
	_changeColumnsHandler( e ) {
		e.preventDefault();

		let button = $( e.target );
		let columns_count = button.data('columns');
		let classWrapper = 'pa-col-sm-' + Math.floor( 12 / parseInt( columns_count ) );

		let newColumnsObject = [];
		for ( let i = 0; i < columns_count; i++ ) {
			newColumnsObject.push({
				class: classWrapper
			});
		}

		if ( newColumnsObject.length >= this.row.get( 'columns' ).length ) {
			// current columns < columns number selected
			for ( let i = 0; i < newColumnsObject.length; i++ ) {
				let model = this.row.get( 'columns' ).at( i );
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
					this.row.get( 'columns' ).add( newModel );
				}
			}
		} else {
			// current columns > columns number selected
			var elements = [];
			var lastest_column_index = false;
			this.row.get( 'columns' ).map( ( model, index ) => {
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
						// don't destroy here
						// model.destroy();

						if ( index == lastest_column_index ) {
							this.row.get( 'columns' ).at( lastest_column_index ).set( 'elements', elements );
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

	/**
	 * Set edit row mode
	 */
	_setEditRowHandler( e ) {
		e.preventDefault();
		this.row.set( 'editing', true );
		return false;
	}

	/**
	 * render edit row form
	 */
	renderEditRowForm( model ) {
		if ( model.get( 'editing' ) === true ) {
			// row edit form
			let editForm = new EditForm( model, PA_PARAMS.languages.entry_edit_row_text, PA_PARAMS.element_fields.pa_row );
		}
	}

}
import Backbone from 'Backbone';
import _ from 'underscore';
import Column from './column';
import EditForm from './globals/edit-form';
import resizable from 'jquery-ui/ui/widgets/resizable';

export default class Row extends Backbone.View {

	/**
	 * Constructor class
	 */
	initialize( row = { settings: {}, columns: {} } ) {
		// set backbone model
		this.row = row;

		this.events = {
			'click > .pa-controls .pa-delete-row'					: '_deleteRowHandler',
			'click > .pa-row-column-control .pa-add-column'			: '_addColumnHandler',
			'click > .pa-row-column-control .pa-edit-column-num'	: '_changeColumnsHandler',
			'click > .pa-row-container > .row-controls > .left-controls > .pa-edit-row, > .row-controls > .left-controls > .pa-edit-row'		: '_setEditRowHandler',
			'click .pa-reorder'			: () => {
				return false;
			}
		}

		// listen this.row model
		this.listenTo( this.row, 'destroy', this.remove );
		this.listenTo( this.row.get( 'columns' ), 'add', this.addColumn );
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
		if ( this.row.get( 'columns' ) && this.row.get( 'columns' ).length > 0 ) {
			this.row.get( 'columns' ).map( ( model, index ) => {
				model.set( 'editabled', index < this.row.get( 'columns' ).length - 1 );
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
		let column = new Column( model ).render().el;
		this.$( '> .pa-element-wrapper > .pav-row-container' ).append( column );
	}

	/**
	 * Delete row handler
	 */
	_deleteRowHandler( e ) {
		e.preventDefault();
		if ( confirm( this.$( '.pa-delete-row' ).data( 'confirm' ) ) ) {
			this.row.destroy();
		}
		return false;
	}

	/**
	 * Add column handler
	 */
	_addColumnHandler( e ) {
		e.preventDefault();

		// define column width relationship
		let screens = {
			lg: 1,
			md: 1,
			sm: 6,
			xs: 12
		};
		// current screen
		let currentScreen = this.row.get( 'screen' );
		let screen_modes = [ 'lg', 'md', 'sm', 'xs' ];
		let RowWidth = this.$el.innerWidth();

		if ( this.row.get( 'columns' ).length == 12 ) {
			alert( PA_PARAMS.languages.entry_column_is_maximum );
		} else {
			let calcols = Math.floor( 12 / ( this.row.get( 'columns' ).length + 1 ) );
			let columnWidth = calcols * ( RowWidth / 12 ) * 100 / RowWidth;

			new Promise( ( resolve, reject ) => {
				let lastest = false;
				this.row.get( 'columns' ).map( ( column ) => {
					let responsive = column.get( 'responsive' );
					_.map( screen_modes, ( screen ) => {
						responsive[screen].cols = calcols;
						if ( responsive[screen].styles === undefined ) {
							responsive[screen].styles = {};
						}
						responsive[screen].styles.width = columnWidth;
					} );

					column.set( 'responsive', responsive );
					column.set( 'reRender', true );
				} );
				resolve( lastest );
			} ).then( ( lastest ) => {
				let responsive = {};
				_.map( screen_modes, ( sc ) => {
					responsive[sc] = {
						cols: calcols,
						styles: {
							width: 100 - ( columnWidth * this.row.get( 'columns' ).length )
						}
					}
				} );
				this.row.get( 'columns' ).add( {
					screen: currentScreen,
					responsive: responsive
				} );
			} );
		}

		return false;
	}

	/**
	 * Change Columns of row
	 */
	_changeColumnsHandler( e ) {
		e.preventDefault();

		let button = $( e.target );
		let columns_count = button.data('columns');
		let cols = Math.floor( 12 / parseInt( columns_count ) );
		let screen = this.row.get( 'screen' );
		let screens = [ 'lg', 'md', 'sm', 'xs' ];

		let newColumnsObject = [];
		for ( let i = 0; i < columns_count; i++ ) {
			newColumnsObject.push({
				cols: cols
			});
		}

		if ( newColumnsObject.length >= this.row.get( 'columns' ).length ) {
			// current columns < columns number selected
			for ( let i = 0; i < newColumnsObject.length; i++ ) {
				let model = this.row.get( 'columns' ).at( i );
				if ( typeof model !== 'undefined' ) {
					let responsive = { ...model.get( 'responsive' ) };

					_.map( screens, ( sc ) => {
						if ( responsive[sc].styles !== undefined && responsive[sc].styles.width !== undefined ) {
							delete responsive[sc].styles.width;
						}
						responsive[sc].cols = cols;
					} );
					model.set( 'responsive', responsive );
					model.set( 'reRender', true );
				} else {
					let newModel = {
						settings: {
							elements: []
						}
					};

					newModel.responsive = {};
					_.map( screens, ( sc ) => {
						newModel.responsive[sc] = {
							cols: cols
						}
					} );

					this.row.get( 'columns' ).add( newModel );
				}
			}
		} else {
			// current columns > columns number selected
			var elements = [];
			var lastest_column_index = false;
			this.row.get( 'columns' ).map( ( model, index ) => {
				if ( typeof newColumnsObject[index] !== 'undefined' ) {
					let responsive = { ...model.get( 'responsive' ) };

					_.map( screens, ( sc ) => {
						responsive[sc].cols = newColumnsObject[index].cols;
						// delete width style
						if ( responsive[sc].styles !== undefined && responsive[sc].styles.width != undefined ) {
							delete responsive[sc].styles.width;
						}
					} );
					model.set( 'responsive', responsive );
					model.set( 'reRender', true );

					// lastest index if columns collection
					lastest_column_index = index;
				} else if ( lastest_column_index !== false ) {
					new Promise( ( resolve, reject ) => {
						var cloneModel = model;
						// check elements inside column if > 0, we will add it to lastest column
						if ( typeof cloneModel.get( 'elements' ) !== 'undefined' && cloneModel.get( 'elements' ).length > 0 ) {
							let settings = cloneModel.get( 'elements' ).toJSON();
							elements.push( settings );
						}

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
		// row edit form
		let editForm = new EditForm( this.row, PA_PARAMS.languages.entry_edit_row_text, PA_PARAMS.element_fields.pa_row );
		return false;
	}

}
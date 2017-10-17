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
			'click .pa-add-column'									: '_addColumnHandler',
			'click .pa-edit-column-num'								: '_changeColumnsHandler',
			'click > .pa-row-container > .row-controls > .left-controls > .pa-edit-row, > .row-controls > .left-controls > .pa-edit-row'		: '_setEditRowHandler',
			'click .pa-reorder, .pa-set-column'			: () => {
				return false;
			}
		}

		// listen this.row model
		this.listenTo( this.row, 'destroy', this.remove );
		this.listenTo( this.row.get( 'columns' ), 'add', this.addColumn );
		this.listenTo( this.row, 'change:screen', ( model ) => {
			let screen = model.get( 'screen' );
			if ( screen == 'lg' || screen == 'md' ) {
				this.$( '.pa-set-column, .pa-add-column' ).removeClass( 'hide' );
			} else {
				this.$( '.pa-set-column, .pa-add-column' ).addClass( 'hide' );
			}
		} );
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
				model.set( 'resizabled', index < this.row.get( 'columns' ).length - 1 );
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
		// current screen
		let currentScreen = this.row.get( 'screen' );
		let screen_modes = [ 'lg', 'md', 'sm', 'xs' ];
		let RowWidth = this.$el.innerWidth();

		if ( this.row.get( 'columns' ).length == 12 ) {
			alert( PA_PARAMS.languages.entry_column_is_maximum );
		} else {
			let calcols = Math.floor( 12 / ( this.row.get( 'columns' ).length + 1 ) );
			let columnWidth = calcols * ( RowWidth / 12 ) * 100 / RowWidth;
			let columnWidthPercent = ( RowWidth / 12 ) * 100 / RowWidth;

			new Promise( ( resolve, reject ) => {

				let screens = [ 'lg', 'md', 'sm', 'xs' ];
				let columnsData = {};
				// calculate responsive columns
				_.map( screens, ( screen ) => {
					let surplus = 0;
					let newRps = {};
					let successed = false;
					for ( let index = this.row.get( 'columns' ).length - 1; index >= 0; index-- ) {
						if ( successed && ! surplus ) return false;
						let breakCal = false;
						columnsData[index] = columnsData[index] == undefined ? {} : columnsData[index];
						// if ( columnsData[index][screen] !== undefined ) return;
						let column = this.row.get( 'columns' ).at( index );
						let colResponsive = column.get( 'responsive' );
						// resposive attributes
						let cols = colResponsive[screen] !== undefined && colResponsive[screen].cols !== undefined ? colResponsive[screen].cols : 1;
						let styles = colResponsive[screen] !== undefined && colResponsive[screen].styles ? colResponsive[screen].styles : false;
						let width = styles && styles.width !== undefined ? styles.width : false;

						if ( cols > 1 || ( cols == 1 && width ) || ( cols === 1 && successed ) ) {
							switch ( screen ) {
								case 'lg':
								case 'md':
									let nosuccess = false;
									if ( successed ) {
										nosuccess = true;
									}

									let newsurplus = 0;
									if ( width ) {
										newsurplus = width - cols * columnWidthPercent;
									}
									surplus = parseFloat( surplus ) + parseFloat( newsurplus );
									// if cols > 1 always true
									if ( cols > 1 && ! successed ) {
										cols = cols - 1;
										successed = true;
									}

									if ( surplus ) {
										if ( surplus > 0 ) {
											if ( surplus > columnWidthPercent ) {
												if ( nosuccess ) {
													cols = parseInt( cols ) + 1;
													surplus = surplus - columnWidthPercent;
												} else {
													surplus = surplus - columnWidthPercent;
												}
											}
										} else {
											if ( surplus + columnWidthPercent < 0 ) {
												cols = ! nosuccess ? cols - 1 : cols;
												surplus = surplus + columnWidthPercent;
											}
										}
									}

									columnsData[index][screen] = {
										cols: cols
									};

									if ( surplus && cols >= 1 ) {
										width = parseFloat( columnWidthPercent * cols ) + surplus;
										if ( successed && cols >= 1 && width >= columnWidthPercent ) {
											columnsData[index][screen].styles = {
												width : width
											}
											surplus = false;
										}
									}

								break;

								case 'sm':

								break;

								case 'xs':

								break;
							}
						}
					}
				} );

				resolve( columnsData );
			} ).then( ( cols ) => {
				_.map( cols, ( data, index ) => {
					if ( data ) {
						let column = this.row.get( 'columns' ).at( index );
						let responsive = { ...column.get( 'responsive' ), ...data };
						column.set( {
							reRender: true,
							responsive: responsive
						} );
					}
				} );

				let newColumnsObject = {
					screen: currentScreen,
					responsive: {
						lg: {
							cols: this.row.get( 'columns' ).length == 0 ? 12 : 1
						},
						md: {
							cols: this.row.get( 'columns' ).length == 0 ? 12 : 1
						},
						sm: {
							cols: 6
						},
						xs: {
							cols: 12
						}
					}
				};
				this.row.get( 'columns' ).add( newColumnsObject );
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
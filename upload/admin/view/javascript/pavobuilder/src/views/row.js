import Backbone from 'Backbone';
import _ from 'underscore';
import ColumsCollection from '../collections/columns';
import Column from './column';

export default class Row extends Backbone.View {

	/**
	 * Constructor class
	 */
	constructor( row = { settings: {}, columns: {} } ) {
		super();
		// set backbone model
		this.row = row;

		// set columns is a collection
		this.row.columns = new ColumsCollection( row.columns );

		this.template = _.template( $( '#pavobuilder-row-template' ).html(), { variable: 'data' } )( this.row );
		this.setElement( this.template );

		this.events = {
			'click .pv-delete-row'		: 'deleteRowHandler'
		}

		// listen this.row model
		this.listenTo( this.row, 'destroy', this.remove );

		// delegate event
		this.delegateEvents();
	}

	/**
	 * Render html
	 */
	render() {
		// each collection
		if ( this.row.columns.models.length > 0 ) {
			_.map( this.row.columns.models, ( model ) => {
				// map column models add add it to Row View
				this.addColumn( model );
			} );
		} else {
			this.addColumn( -1, {} );
		}

		setTimeout( () => {
			this.$el.removeClass( 'row-fade-in' );
		}, 1000 );
		return this;
	}

	/**
	 * Delete row handler
	 */
	deleteRowHandler() {
		this.row.destroy();
		return false;
	}

	/**
	 * Add Column
	 */
	addColumn( model = {} ) {
		this.row.columns.add( model );
	}

	/**
	 * Remove Column
	 */
	removeColumn( index = -1 ) {

	}

	/**
	 * Update Column
	 */
	updateColumn( index = -1 ) {

	}

}
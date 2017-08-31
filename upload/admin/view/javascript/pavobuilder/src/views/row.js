import Backbone from 'Backbone';
import _ from 'underscore';
import ColumsCollection from '../collections/columns';
import Column from './column';

export default class Row extends Backbone.View {

	constructor( row = { settings: {}, columns: {} } ) {
		super();
		// set backbone model
		this.row = row;
		// set columns is a collection
		this.columnsCollection = new ColumsCollection( row.columns );

		var template = _.template( $( '#pavobuilder-row-template' ).html(), { variable: 'data' } )( this.row );
		this.setElement( template );

		this.events = {
			'click .pv-delete-row'		: 'deleteRowHandler',
			'click .pv-edit-row'		: 'editRowHandler'
		}

		this.listenTo( this.row, 'destroy', this.remove );

		// add event
		this.delegateEvents();
	}

	render() {
		if ( this.columnsCollection.models.length > 0 ) {
			_.map( this.columnsCollection.models, ( model ) => {
				// map column models add add it to Row View
				this.addColumn( model );
			} );
		} else {
			this.addColumn( -1, {} );
		}
		return this;
	}

	// edit row handler
	editRowHandler() {

		return false;
	}

	// delete row handler
	deleteRowHandler() {
		this.row.destroy();
		return false;
	}

	/**
	 * Add Column
	 */
	addColumn( model = {} ) {
		this.columnsCollection.add( model );
	}

	removeColumn( index = -1 ) {

	}

	updateColumn( index = -1 ) {

	}

}
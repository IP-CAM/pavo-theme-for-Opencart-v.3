import Backbone from 'Backbone';
import _ from 'underscore';
// import ColumnsCollection from '../collections/columns';
import Column from './column';

export default class Row extends Backbone.View {

	/**
	 * Constructor class
	 */
	initialize( row = { settings: {}, columns: {} } ) {
		// set backbone model
		this.row = row;

		this.template = _.template( $( '#pa-row-template' ).html(), { variable: 'data' } )( this.row );
		this.setElement( this.template );

		this.events = {
			'click .pa-delete-row'		: 'deleteRowHandler',
			'click .pa-add-column'		: 'addColumnHandler'
		}

		// listen this.row model
		this.listenTo( this.row, 'destroy', this.remove );
		this.listenTo( this.row.get( 'columns' ), 'update', this.render );

		// delegate event
		this.delegateEvents();
	}

	/**
	 * Render html
	 */
	render() {
		// each collection
		if ( this.row.get( 'columns' ).models.length > 0 ) {
			_.map( this.row.get( 'columns' ).models, ( model ) => {
				// map column models add add it to Row View
				this.addColumn( model );
			} );
		} else {
			// console.log( this.row.get( 'columns' ) );
			this.addColumn({ settings: {}, columns: [] });
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
	 * Add column handler
	 */
	addColumnHandler( e ) {
		// stop event default
		e.preventDefault();
		this.row.get( 'columns' ).add({});
		return false;
	}

	/**
	 * Add Column
	 */
	addColumn( model = {} ) {
		this.$el.find( '.pav-row-container' ).append( new Column( model ).render().el );
	}

	/**
	 * Remove Column
	 */
	removeColumn() {

	}

	/**
	 * Update Column
	 */
	updateColumn( column = {} ) {
		console.log( column );
	}

}
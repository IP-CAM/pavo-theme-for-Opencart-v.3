import Backbone from 'Backbone';
import _ from 'underscore';
import RowsCollection from '../collections/rows';
import Rows from './rows';

export default class Builder extends Backbone.View {

	constructor( rows = {} ) {
		super();
		// set data is collection of row, it will pass to Rows View
		this.rowsCollection = new RowsCollection( rows );
		this.$el = $( '#pavobuilder-container' );

		// events
		this.events = {
			'click #pavobuilder-add-element' : 'addRowHandler'
		}

		// add event
		this.delegateEvents();
	}

	/**
	 * Render html rows template
	 */
	render() {
		// set rows data
		this.rows = new Rows( {
			rows: this.rowsCollection
		} );
		this.rows.render().el;
		return this;
	}

	/**
	 * Add row event handler
	 * add empty row to RowsCollection
	 */
	addRowHandler( e ) {
		e.preventDefault();
		// add row model to collection
		this.rowsCollection.add({});
		return false;
	}

}
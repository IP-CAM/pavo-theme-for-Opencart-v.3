import Backbone from 'Backbone';
import _ from 'underscore';
import RowsCollection from '../collections/rows';
import Rows from './rows';

export default class Builder extends Backbone.View {

	constructor( rows = [] ) {
		super();
		// set data is collection of row, it will pass to Rows View
		this.rowsCollection = new RowsCollection( rows );
		// console.log( this.rowsCollection );
		this.$el = $( '#pa-container' );

		// events
		this.events = {
			'click #pa-add-element' : 'addRowHandler'
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

		this.$el.find( '.pa-element-content' ).sortable();
		return this;
	}

	/**
	 * Add row event handler
	 * add empty row to RowsCollection
	 */
	addRowHandler( e ) {
		e.preventDefault();
		// add row model to collection
		let model = {
			settings: {},
			columns: [
				{ settings: { class: 'pa-col-sm-12' } }
			]
		};
		this.rowsCollection.add( model );
		return false;
	}

}
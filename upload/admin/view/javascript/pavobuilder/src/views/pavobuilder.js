import Backbone from 'Backbone';
import _ from 'underscore';
import Rows from './rows';

export default class Builder extends Backbone.View {

	constructor( data = {} ) {
		super();
		// set data is collection of row, it will pass wo Rows View
		this.data = data;
		this.el = '#pavobuilder-container';
		this.$el = $( this.el );

		this.setElement( $( '#pavobuilder-container' ) );
		// events
		this.events = {
			'click #pavobuilder-add-element' : 'addRow'
		}
		// add event
		this.delegateEvents();
	}

	render() {
		// set rows data
		this.rows = new Rows( this.data );
		this.$el.html( this.rows.render() );

		return this;
	}

	addRow( e ) {
		e.preventDefault();
		console.log( 'add row handler' );

		return false;
	}

}
import Backbone from 'Backbone';
import _ from 'underscore';
import Rows from './rows';

export default class Builder extends Backbone.View {

	initialize( data = {} ) {
		// set data is collection of row, it will pass wo Rows View
		this.data = data;
		this.$el = $( '#pavobuilder-container' );

		// events
		this.events = {
			'click #pavobuilder-add-element' : 'addRow'
		}
	}

	render() {
		// set rows data
		this.rows = new Rows( this.data );
		console.log( this.rows );
		// this.$el.html( this.rows.render().el );

		return this;
	}

	addRow( e ) {
		e.preventDefault();
		console.log( 'add row handler' );
	}

}
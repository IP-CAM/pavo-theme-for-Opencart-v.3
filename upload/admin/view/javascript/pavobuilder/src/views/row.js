import Backbone from 'Backbone';
import _ from 'underscore';
import Column from './column';

export default class Row extends Backbone.View {

	initialize( collection = {} ) {
		// set columns is a collection
		this.columns = collection;
	}

	render() {
		// _.map( this.columns, ( column, index ) => {
		// 	this.addColumn( index, column );
		// } );
	}

	addColumn( index = -1, model = {} ) {
		// // var template = _.template( $( '#pavobuilder-row-template' ).html() )( data );
		// this.$el.append( new Column( model ) );
	}

	removeColumn( index = -1 ) {

	}

	updateColumn( index = -1 ) {

	}

}
import Backbone from 'Backbone';
import _ from 'underscore';
import RowsCollection from '../collections/rows';
import Row from './row';

export default class Rows extends Backbone.View {

	initialize( rows = {} ) {
		// set this.rows is a collection
		this.rows = new RowsCollection( rows );

		this.$el = $( '#pavobuilder-container' );

		this.events = {
			// 'click .pv-add-element' : 'this.addRow'
		}

		// listen collection
		this.listenTo( this.rows, 'change', this.render );
		this.listenTo( this.rows, 'add', this.addRow );
		this.listenTo( this.rows, 'remove', this.removeRow );
		this.listenTo( this.rows, 'update', this.updateRow );

		// render method
		this.render();

		return this;
	}

	render() {
		if ( this.rows ) {
			_.map( this.rows.models, ( row, index ) => {
				this.addRow( index, row );
			} );
		}
	}

	addRow( index = -1, model = {} ) {
		// var template = _.template( $( '#pavobuilder-row-template' ).html() )( data );
		this.$el.append( new Row( model ) );
	}

	removeRow( index = -1 ) {

	}

	updateRow( index = -1 ) {

	}

}
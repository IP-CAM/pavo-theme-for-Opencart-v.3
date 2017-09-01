import Backbone from 'Backbone';
import _ from 'underscore';

export default class FormEditRow extends Backbone.View {

	/**
	 * Constructor class
	 */
	constructor( row = { settings: {}, columns: {} } ) {
		super();
		// row is RowModel
		this.row = row;
		this.template = _.template( $( '#pavobuilder-edit-row-template' ).html(), { variable: 'data' } );
	}

	/**
	 * Render html
	 */
	render() {
		let template = this.template( this.row );
		this.setElement( template );

		return this;
	}

}
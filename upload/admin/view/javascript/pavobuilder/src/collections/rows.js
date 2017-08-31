import Backbone from 'Backbone';
import RowModel from '../models/row'

export default class RowsCollection extends Backbone.Collection {

	constructor( rows = {} ) {
		super();
		this.model = RowModel;
	}

}
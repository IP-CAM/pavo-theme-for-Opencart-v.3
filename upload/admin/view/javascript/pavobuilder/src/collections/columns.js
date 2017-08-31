import Backbone from 'Backbone';
import ColumnModel from '../models/column'

export default class ColumnsCollection extends Backbone.Collection {

	constructor() {
		super();
		this.model = ColumnModel;
	}
	
}
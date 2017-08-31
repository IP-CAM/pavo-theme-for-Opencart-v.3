import Backbone from 'Backbone';
import ElementModel from '../models/element'

export default class ElementsCollection extends Backbone.Collection {

	constructor() {
		super();
		this.model = ElementModel;
	}
	
}
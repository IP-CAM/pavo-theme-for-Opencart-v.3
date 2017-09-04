import Backbone from 'Backbone';
import _ from 'underscore';

export default class Element extends Backbone.Model {

	initialize( data = {} ) {
		// super();
	}

	defaults() {
		return {
			settings: {}
		};
	}

}
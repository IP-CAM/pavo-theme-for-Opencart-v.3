import Backbone from 'Backbone';
import _ from 'underscore';

export default class Element extends Backbone.Model {

	initialize( data = {} ) {}

	defaults() {
		return {
			settings : {
				element: 'pa_element'
			},
			mask	 : {},
			editing  : false
		};
	}

}
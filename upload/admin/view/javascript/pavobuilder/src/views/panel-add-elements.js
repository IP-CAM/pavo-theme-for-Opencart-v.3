import Backbone from 'Backbone';

export default class ElementsView extends Backbone.View {

	initialize( element = { settings: {} } ) {
		// super();
		this.template = _.template( $( '#pa-elements-panel' ).html(), { variable: 'data' } );
	}

	render() {
		let template = this.template( this.row );
		this.setElement( template );
		return this;
	}
	
}
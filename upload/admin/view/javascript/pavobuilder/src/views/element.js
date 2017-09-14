import Backbone from 'Backbone';
import _ from 'underscore';
import EditForm from './globals/edit-form';

export default class Column extends Backbone.View {

	initialize( element = {} ) {
		this.element = element;
		this.listenTo( this.element, 'destroy', this.remove );
		this.listenTo( this.element, 'change', this.render );
		this.listenTo( this.element, 'change:editing', this.renderElementEditForm );

		this.events = {
			'click .pa-delete'		: '_removeHandler',
			'click .pa-edit'		: '_editHandler'
		};
	}

	/**
	 * render html
	 */
	render() {
		let data = this.element.toJSON();
		data.cid = this.element.cid;
		this.template = _.template( $( '#pa-element-template' ).html(), { variable: 'data' } )( data );
		this.setElement( this.template );

		return this;
	}

	/**
	 * Remove click handler
	 */
	_removeHandler( e ) {
		e.preventDefault();
		if ( confirm( this.$el.data( 'confirm' ) ) ) {
			this.element.destroy();
		}
		return false;
	}

	/**
	 * Edit click handler
	 */
	_editHandler( e ) {
		e.preventDefault();
		this.element.set( 'editing', true );
		return false;
	}

	/**
	 * Render Edit Element Form
	 */
	renderElementEditForm( model ) {
		if ( model.get( 'editing' ) ) {
			new EditForm( model, PA_VARS.entry_edit_element_text );
		}
	}

}
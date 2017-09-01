import Backbone from 'Backbone';
import _ from 'underscore';
import Row from './row';
import FormEditRow from './form-edit-row';

export default class Rows extends Backbone.View {

	constructor( data = { rows : {} } ) {
		super();
		// set this.rows is a collection
		this.rows = data.rows;
		this.$el = $( '#pavobuilder-content' );

		// listen to collection status
		this.listenTo( this.rows, 'add', this.addRow );
		// // when model of collection had change on View Room
		// this.listenTo( this.rows, 'change', () => {
		// 	console.log( this.rows );
		// } );
		this.events = {
			'click .pv-clone-row' 	: 'cloneRow',
			'click .pv-edit-row'	: 'toggleEditRow'
		};

		// row edit form
		this.rowEditForm = new FormEditRow( { settings: {}, columns: {}, editing : false } );
		$( 'body' ).prepend( this.rowEditForm.render().el );

		// add event
		this.delegateEvents();
	}

	/**
	 * Render html method
	 */
	render() {
		if ( this.rows.models.length > 0 ) {
			_.map( this.rows.models, ( data ) => {
				this.addRow( data );
			} );
		}

		// set sortable
		this.$el.sortable({
			placeholder: 'pavobuilder-sortable-placeholder',
			handle: '.pv-reorder-row',
			// sortable updated callback
			start: this.dragRow,
			stop: this.dropRown.bind( this )
		});

		return this;
	}

	/**
	 * Add row view
	 */
	addRow( model = {}, collection = {}, status = {} ) {
		if ( typeof status.at === 'undefined' ) {
			this.$el.append( new Row( model ).render().el );
		} else {
			let rows = this.$el.find( '.pv-row-container' );
			rows.map( ( i, row ) => {
				let newIndex = parseInt( status.at ) - 1;
				if ( newIndex == i ) {
					$( rows[newIndex] ).after( new Row( model ).render().el )
				}
			} );
		}
	}

	/**
	 * Clone Row
	 */
	cloneRow( e ) {
		let cid = $( e.target ).parents( '.pv-row-container:first' ).data( 'cid' );
		let model = this.rows.get( { cid: cid } );
		let index = this.rows.indexOf( model );
		let newModel = model.clone();
		this.rows.add( newModel, { at: parseInt( index ) + 1 } );
		return false;
	}

	/**
	 * Toggle edit row
	 */
	toggleEditRow( e ) {

		let model = {};
		if ( model.get( 'editing' ) === true ) {
			this.rowEditForm.$el.removeClass( 'hide' ).addClass( 'fade in' );
		} else {
			this.rowEditForm.$el.removeClass( 'fade in' ).addClass( 'hide' );
		}

		return false;
	}

	/**
	 * Start Drag event row
	 */
	dragRow( event, ui ) {
		ui.item.indexStart = ui.item.index();
	}

	/**
	 * Drop row
	 */
	dropRown( event, ui ) {
		this.rows.moveItem( ui.item.indexStart, ui.item.index() );
	}

}
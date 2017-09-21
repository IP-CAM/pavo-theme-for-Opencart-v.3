import Backbone from 'Backbone';
import _ from 'underscore';
import Row from './row';
import Common from '../common/functions';

export default class Rows extends Backbone.View {

	initialize( data = { rows : {} } ) {
		// set this.rows is a collection
		this.rows = data.rows;
		this.$el = $( '#pa-content' );

		// listen to collection status
		this.listenTo( this.rows, 'add', this.addRow );
		this.events = {
			'click > .pa-row-container > .row-controls > .left-controls > .pa-clone-row' 					: '_cloneRowHandler'
		};

		// add event
		this.delegateEvents();
	}

	/**
	 * Render html method
	 */
	render() {
		if ( this.rows.length > 0 ) {
			this.rows.map( ( model ) => {
				this.addRow( model );
			} );
		}

		// set sortable
		this.$el.sortable({
			placeholder: 'pa-sortable-placeholder',
			handle: '> .row-controls .pa-reorder-row',
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
			let rows = this.$( '> .pa-row-container' );
			rows.map( ( i, row ) => {
				let newIndex = parseInt( status.at ) - 1;
				if ( newIndex == i ) {
					$( rows[newIndex] ).after( new Row( collection.get( model ) ).render().el )
				}
			} );
		}
	}

	/**
	 * Clone Row
	 */
	_cloneRowHandler( e ) {
		e.preventDefault();
		let cid = $( e.target ).parents( '.pa-row-container:first' ).data( 'cid' );
		let model = this.rows.get( { cid: cid } );

		let index = this.rows.indexOf( model );
		let mmodel = model.clone();
		let newModel = Common.toJSON( model.toJSON() );

		this.rows.add( newModel, { at: parseInt( index ) + 1 } );
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
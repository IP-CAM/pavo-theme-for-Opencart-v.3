import Backbone from 'Backbone';
import _ from 'underscore';
import Row from './row';

export default class Rows extends Backbone.View {

	initialize( data = { rows : {} } ) {
		// set this.rows is a collection
		this.rows = data.rows;
		this.$el = $( '#pa-content' );

		// listen to collection status
		this.listenTo( this.rows, 'add', this.addRow );
		this.events = {
			'click .pa-clone-row' 	: '_cloneRowHandler'
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
			handle: '.pa-reorder-row',
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
			let rows = this.$el.find( '.pa-row-container' );
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
		let newModel = this._clone( model.toJSON() );

		this.rows.add( newModel, { at: parseInt( index ) + 1 } );
		return false;
	}

	/**
	 * Clone row model
	 */
	_clone( data = {} ) {
		if ( data instanceof Backbone.Model || data instanceof Backbone.Collection ) {
			data = data.toJSON();
		}
		_.map( data, ( value, name ) => {
			if ( value instanceof Object ) {
				data[name] = this._clone( value );
			} else {
				data[name] = value;
			}
		} );

		return data;
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
import Backbone from 'Backbone';
import _ from 'underscore';
import Row from './row';
import FormEditRow from './form-edit-row';

export default class Rows extends Backbone.View {

	initialize( data = { rows : {} } ) {
		// super( data );
		// set this.rows is a collection
		this.rows = data.rows;
		this.$el = $( '#pa-content' );

		// listen to collection status
		this.listenTo( this.rows, 'add', this.addRow );
		// // when model of collection had change on View Room
		// this.listenTo( this.rows, 'change', () => {
		// 	console.log( this.rows );
		// } );
		this.events = {
			'click .pa-clone-row' 	: 'cloneRowHandler',
			'click .pa-edit-row'	: 'toggleEditRowHandler'
		};

		// add event
		this.delegateEvents();
	}

	/**
	 * Render html method
	 */
	render() {
		if ( this.rows.models.length > 0 ) {
			_.map( this.rows.models, ( model ) => {
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
					$( rows[newIndex] ).after( new Row( model ).render().el )
				}
			} );
		}
	}

	/**
	 * Clone Row
	 */
	cloneRowHandler( e ) {
		e.preventDefault();
		let cid = $( e.target ).parents( '.pa-row-container:first' ).data( 'cid' );
		let model = this.rows.get( { cid: cid } );
		let index = this.rows.indexOf( model );
		let newModel = this._clone( model.toJSON() );

		this.rows.add( newModel, { at: parseInt( index ) + 1 } );
		return false;
	}

	/**
	 * Clone model
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
	 * Toggle edit row
	 */
	toggleEditRowHandler( e ) {
		let button = $( e.target );
		let model_cid = button.parents( '.pa-row-container:first' ).data('cid');
		let model = this.rows.get( { 'cid': model_cid } );

		model.set( 'editing', ! model.get( 'editing' ) );

		if ( model.get( 'editing' ) === true ) {
			// row edit form
			this.rowEditForm = new FormEditRow( model );
			$( 'body' ).append( this.rowEditForm.render().el );
			// this.rowEditForm.$el.modal( 'show' );
			$('#pa-inspector').modal( 'show' );
			$('#pa-inspector').on( 'hidden.bs.modal', () => {
				model.set( 'editing', false );
				this.rowEditForm.remove();
			} );
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
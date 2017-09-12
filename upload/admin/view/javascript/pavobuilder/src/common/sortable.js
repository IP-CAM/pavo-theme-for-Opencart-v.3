import $ from 'jquery';
import plugin from './plugin';

class Sortable {

	events = [
			'mousedown',
			'dragstart',
			'dragover',
			'dragleave',
			'drag',
			'dragend',
			'drop'
		]

	defaults() {
		return {
			items 			: [],
			placeholder		: '',
			helper			: '',
			handle			: ''
		};
	}

	/**
	 * Constructor class
	 */
    constructor( element, options ) {
        this.el = element;
        this.$el = $( this.el );
        this.options = options;

        if ( this.options.items ) {
	        // init
	    	this.el.setAttribute( 'draggable', true );
	    	this.el.setAttribute( 'droppable', true );
        	for ( let item of this.options.items ) {

		    	new Promise( ( resolve, reject ) => {
		    		item = this.$el.find( item );
		    		let handle = this.$el.find( this.options.handle );
		    		if ( item.length ) {
		    			handle = handle !== undefined ? handle.get( 0 ) : null;
		    			resolve( {
		    				item: item.get( 0 ),
		    				handle: handle
		    			} );
		    		}
		    	} ).then( ( data ) => {
		    		let item = data.item;
		    		let handle = data.handle;
		    		// set draggable, droppable
			    	item.setAttribute( 'draggable', true );
			    	item.setAttribute( 'droppable', true );
			    	if ( handle ) {
			    		handle.setAttribute( 'draggable', true );
			    		handle.setAttribute( 'droppable', true );
			    	}
			    	// map
			        this.events.map( ( event ) => {
			        	if ( handle != null ) {
			        		handle.addEventListener( event, this[event].bind(this), false );
			        	} else {
			        		item.addEventListener( event, this[event].bind(this), false );
			        		// this.el.addEventListener( event, this[event].bind(this), false );
			        	}
			        } );
		    	} );
        	}
        }
    }

    mousedown( e ) {
    	let handle = this.$el.find( this.options.handle );
    	if ( handle.length > 0 && ( handle.get( 0 ) == e.target || $.contains( handle.get( 0 ), e.target ) ) ) {
    		this.dragstart( e );
    	}
    }

    dragstart( e ) {
    	console.log( 1 );
    	// $( this.$el ).addClass( 'pa-ui-dragging pa-ui-placeholder' );

    	// if ( e.dataTransfer !== undefined && e.dataTransfer.setDragImage !== undefined && this.options.helper ) {
    	// 	e.dataTransfer.setDragImage( document.getElementById( this.options.helper[0] ), this.options.helper[1], this.options.helper[2] );
    	// }

    	// e.preventDefault();
    	// return false;
    }

    drag( e ) {
    	console.log( 2 );
    }

    dragover( e ) {
    	console.log(3);
    }

    dragleave( e ) {
    	console.log(4);
    }

    dragend( e ) {
    	console.log( 5 );
    	// $( this.$el ).removeClass( 'pa-ui-dragging pa-ui-placeholder' );

    	// this.el.setAttribute( 'droppable', false );
    	// this.el.setAttribute( 'draggable', false );
    }

    drop( e ) {
    	console.log(6);
    }
}

plugin( 'sortable', Sortable, true );
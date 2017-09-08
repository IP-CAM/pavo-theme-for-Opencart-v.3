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

        // this.el.addEventListener( '' )
    	let handle = this.$el.find( this.options.handle );
        this.events.map( ( event ) => {
        	if ( handle.length > 0 ) {
        		handle.get(0).addEventListener( event, this[event].bind(this), false );
        	} else {
        		this.el.addEventListener( event, this[event].bind(this), false );
        	}
        } );
    }

    mousedown( e ) {
    	this.el.setAttribute( 'draggable', true );
    	this.el.setAttribute( 'droppable', true );
    	let handle = this.$el.find( this.options.handle );
    	if ( handle.length > 0 && ( handle.get( 0 ) == e.target || $.contains( handle.get( 0 ), e.target ) ) ) {
    		this.dragstart( e );
    	}
    }

    dragstart( e ) {
    	$( this.$el ).addClass( 'pa-ui-dragging pa-ui-placeholder' );
    	$(e).push( 'originalEvent' );
    	console.log( e.originalEvent, e );

  //   	return false;
  //   	var img = document.createElement("img");
  //   	img.src = "http://kryogenix.org/images/hackergotchi-simpler.png";
  //   	e.dataTransfer.setDragImage(
		// 	img,
		// 	35,
		// 	35
		// );
    }

    drag( e ) {
    	console.log(5);
    }

    dragover( e ) {
    	console.log(3);
    }

    dragleave( e ) {
    	console.log(4);
    }

    dragend( e ) {
    	$( this.$el ).removeClass( 'pa-ui-dragging pa-ui-placeholder' );

    	this.el.setAttribute( 'droppable', false );
    	this.el.setAttribute( 'draggable', false );
    }

    drop( e ) {
    	console.log(7);
    }
}

plugin( 'sortable', Sortable, true );
import $ from 'jquery';
import Builder from './src/views/pavobuilder';
import Common from './src/common/functions';

$( document ).ready(() => {
	// init view
	let HomePageBuilder = new Builder( window.PA_PARAMS.content );
	HomePageBuilder.render();

	$( document ).on( 'submit', '#pavohomebuilder-layout-edit', ( e ) => {
		// e.preventDefault();
		let textarea = $( '#pavo-home-pagebuilder-content' );
		textarea.text( JSON.stringify( Common.toJSON( HomePageBuilder.rowsCollection, [ 'editabled', 'adding', 'reRender', 'adding_position' ] ) ) );
		return true;
	} );
});

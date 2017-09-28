import $ from 'jquery';
import Builder from './src/views/pavobuilder';
import Common from './src/common/functions';
import Loader from './src/views/globals/loader';
import serializeJSON from 'jquery-serializejson';

let ctrlPress = false;
$( document ).ready(() => {
	// init view
	let HomePageBuilder = new Builder( window.PA_PARAMS.content );
	let loading = false;

	$( document ).on( 'submit', '#pavohomebuilder-layout-edit', ( e ) => {
		let textarea = $( '#pavo-home-pagebuilder-content' );
		textarea.text( JSON.stringify( Common.toJSON( HomePageBuilder.model.get( 'rows' ), [ 'editabled', 'adding', 'reRender', 'adding_position' ] ) ) );
		return true;
	} );

	$( document ).on( 'keydown', ( e ) => {
		if ( e.keyCode === 91 ) {
			ctrlPress = true;
		}

		if ( e.keyCode === 83 && ctrlPress ) {
			e.preventDefault();
			if ( loading ) {
				return;
			}

			loading = true;
			let data = $( '#pavohomebuilder-layout-edit' ).serializeJSON();
			data.content = Common.toJSON( HomePageBuilder.model.get( 'rows' ), [ 'editabled', 'adding', 'reRender', 'adding_position' ] );
			let pageTitle = $( 'title' ).text();

			let Loading = new Loader({
				loading: true,
				callback: () => {
					$.ajax({
						url: PA_PARAMS.editLayoutURL,
						type: 'POST',
						data: data,
						beforeSend: () => {
							Loading.$el.html( '<div id="loader" class="loading"></div>' );
							$( 'title' ).text( PA_PARAMS.languages.updating_text );
						}
					}).always( () => {
						$( 'title' ).text( pageTitle );
						setTimeout( () => {
							loading = false;
							Loading.model.set( 'loading', false );
						}, 1500 );
					} ).done( ( res ) => {
						if ( res.success !== undefined ) {
							Loading.$( '#loader' ).removeClass( 'loading' ).html( '<span>' + res.success + '</span>' );
						}
					} ).fail( () => {
						
					} );
				}
			});
			$( 'body' ).append( Loading.render().el );
			return false;
		}
	} ).on( 'keyup', ( e ) => {
		ctrlPress = false;
	} );
});
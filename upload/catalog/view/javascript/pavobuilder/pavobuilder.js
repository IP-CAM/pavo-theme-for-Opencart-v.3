import GoogleMap from './src/google-map';

$( document ).ready( () => {

	let google_maps = $( '.pa_google_map' );
	for ( let i = 0; i < google_maps.length; i++ ) {
		new GoogleMap( google_maps[i] );
	}

} );
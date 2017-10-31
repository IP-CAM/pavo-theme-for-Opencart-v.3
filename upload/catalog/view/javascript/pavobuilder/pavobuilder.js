import $ from 'jquery';
import GoogleMap from './src/google-map';
import VideoResponsive from './src/video-responsive';

$( document ).ready( () => {

	let google_maps = $( '.pa_google_map' );
	for ( let i = 0; i < google_maps.length; i++ ) {
		new GoogleMap( google_maps[i] );
	}

	let videos = $( '.pa-bg-video' );
	for ( let i = 0; i < videos.length; i++ ) {
		let video = new VideoResponsive( videos );

		let resizing = false;
		$( window ).resize( function() {

			if ( resizing ) return;

			resizing = new Promise( ( resolve, reject ) => {
				setTimeout( () => {
					resolve();
				}, 1000 );
			} ).then( () => {
				video.setSize();
				resizing = false;
			} );
		})
	}

} );
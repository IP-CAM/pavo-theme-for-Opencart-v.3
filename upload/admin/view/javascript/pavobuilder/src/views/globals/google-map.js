import $ from 'jquery';
import Backbone from 'Backbone';
import _ from 'underscore';
import serializeJSON from 'jquery-serializejson';

export default class GoogleMap extends Backbone.View {

	defaults() {
		return {
	          	center: { lat: -33.8688, lng: 151.2195 },
	          	zoom: 13,
	          	mapTypeControl: 1,
	          	mapTypeId: 'roadmap'
	        };
	}

	initialize ( $el, model ) {
		this.setElement( $el );

		let data = this.$( 'input, select, textarea' ).serializeJSON();
		this.model = model;

		this.events = {
			'change input[name="zoom_enabled"]' : ( e ) => {
				if ( ! this.map ) return false;
				this.map.setOptions({ zoomControl : $( e.target ).is( ':checked' ) });
			},
			'change input[name="zoom"]' : ( e ) => {
				if ( ! this.map ) return false;
				this.map.setZoom( parseInt( $( e.target ).val() ) );
			},
			'change input[name="scroll_enabled"]' : ( e ) => {
				if ( ! this.map ) return false;
				this.map.setOptions({ scrollwheel : $( e.target ).is( ':checked' ) });
			},
			'change input[name="draggable"]' : ( e ) => {
				if ( ! this.map ) return false;
				this.map.setOptions({ draggable : $( e.target ).is( ':checked' ) });
			},
			'change select[name="mapTypeId"]'	: ( e ) => {
				if ( ! this.map ) return false;
				this.map.setMapTypeId( $( e.target ).val() );
			},
			'change input[name="map_type_control_enabled"]' : ( e ) => {
				if ( ! this.map ) return false;
				this.map.setOptions({ mapTypeControl : $( e.target ).is( ':checked' ) });
			}
		};
		this.delegateEvents();
	}

	/**
	 * render google map html
	 */
	render() {
		if ( google === undefined ) {
			this.$( '.pa-google-map' ).append( '<div class="alert alert-danger alert-dismissible"><i class="fa fa-exclamation-circle"></i> ' + PA_PARAMS.languages.entry_missing_google_map_key + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>' );
		} else {
			let mapData = Object.assign( this.defaults(), this.model.get( 'settings' ) );
			let settings = this.model.get( 'settings' );
			mapData.center = {
				lat: settings.lat !== undefined && ! isNaN( settings.lat ) && settings.lat ? parseFloat( settings.lat ) : -33.8688,
				lng: settings.lng !== undefined && ! isNaN( settings.lng ) && settings.lng ? parseFloat( settings.lng ) : 151.2195
			}
			mapData.zoom = parseInt( mapData.zoom );
			mapData.draggable = parseInt( mapData.draggable );

			this.map = new google.maps.Map( this.$( '.pa-google-map' ).get( 0 ), mapData );
		}
	}

}
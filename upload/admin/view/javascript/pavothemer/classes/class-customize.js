export default class PavoThemeCustomize {
	constructor() {
		
	}
}

(function($){

	$( document ).ready(function(){
		// init customize
		const Customize = new PavoThemeCustomize();
		var windowHeight = $( window ).height();
		var topHeight = $( '#top-panel' ).height();
		$( '#main-preview' ).height( windowHeight - topHeight );
	});

})(jQuery);
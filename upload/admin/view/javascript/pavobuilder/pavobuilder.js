import $ from 'jquery';
import Builder from './src/views/pavobuilder';

$( document ).ready(() => {
	// init view
	new Builder().render();
});
import $ from 'jquery';
import Builder from './src/views/pavobuilder';

$( document ).ready(() => {
	// init view
	new Builder([
			{
				settings: { xxx: 'DKM' },
				columns: [
					{ id: 1, elements: {} },
					{ id: 2, elements: {} }
				]
			}
		]).render();
});
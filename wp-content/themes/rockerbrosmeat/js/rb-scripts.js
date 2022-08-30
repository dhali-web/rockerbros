/**
 * rb-scripts.js
 *
 */
( function( $ ) {

	// Add class to the sub menu navigation
	$('.woocommerce-MyAccount-navigation > ul').addClass('list list-subnav');

	//Change html order of variations
		$('.product-item').each(function(){
				$(this).find('.variations').insertAfter($(this).find('.quantity'));
		});

} )( jQuery );
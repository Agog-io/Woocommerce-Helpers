<?php
	
add_filter( 'woocommerce_checkout_cart_item_quantity', 'addtextocart', 1, 3 );

add_filter( 'woocommerce_cart_item_price', 'addtextocart', 1, 3 );

function addtextocart( $product_name, $values, $cart_item_key ) {
		//you can get metadata from product using $values['product_id']
		//$values[] contains all line items values , you can add them in the add to cart proccess and they are persistent throughout the checkout procedure
		if ( /*here goes your logic*/ ) {
			$return_string = $product_name . "here goes your text";			
		} else {
			return $product_name;
		}
}
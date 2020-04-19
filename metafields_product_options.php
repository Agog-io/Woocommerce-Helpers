<?php
/**
* This example adds a checkbox  below "sale price " on the product edit page
* that edits a product meta field (if its on the "hideskroutz" meta goes to 1 , else the meta gets deleted).

* There are a lot of options where to put the field
* To be more specific:
* woocommerce_product_options_advanced
* woocommerce_product_options_attributes
* woocommerce_product_options_dimensions
* woocommerce_product_options_downloads
* woocommerce_product_options_general_product_data
* woocommerce_product_options_inventory_product_data
* woocommerce_product_options_pricing
* woocommerce_product_options_related
* woocommerce_product_options_reviews
* woocommerce_product_options_shipping
* woocommerce_product_options_sku
* woocommerce_product_options_sold_individually
* woocommerce_product_options_stock
* woocommerce_product_options_stock_fields
* woocommerce_product_options_stock_status
* woocommerce_product_options_tax
* woocommerce_product_option_terms
* just replace one of those in the add_action of the function
* the save field works without any modification

* There are also option on what kind of input this field is
* woocommerce_wp_text_input
* woocommerce_wp_hidden_input
* woocommerce_wp_textarea_input
* woocommerce_wp_checkbox
* woocommerce_wp_select
* woocommerce_wp_radio
* You can also include images via the wp media selector like this https://wordpress.stackexchange.com/questions/235406/how-do-i-select-an-image-from-media-library-in-my-plugin

* You can then display the fields here in your templates

*/

function hideskroutzprod() {
 global $post;

 $meta = get_post_meta( $post->ID, 'hideskroutz', true );
 if($meta == 1){
   $meta = "yes";
 }
 $args = array(
   'id'            => 'hideskroutz',
   'label'         => __( 'Κρύψιμο Skroutz', 'cfwc' ),
   'class'					=> 'hideskroutz',
   'value'       => $meta

 );
 woocommerce_wp_checkbox( $args );
}
add_action( 'woocommerce_product_options_pricing', 'hideskroutzprod' );

/**
* Save the custom field
* @since 1.0.0
*/
function hideskroutzprodsave( $post_id ) {
 $product = wc_get_product( $post_id );
 $title = isset( $_POST['hideskroutz'] ) ? $_POST['hideskroutz'] : '';
 if(sanitize_text_field( $title ) == "yes"){
   $product->update_meta_data( 'hideskroutz', "1" );
 }
 else{
   delete_post_meta($post_id ,'hideskroutz');
 }
 $product->save();
}
add_action( 'woocommerce_process_product_meta', 'hideskroutzprodsave' );

?>

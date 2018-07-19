<?php
/**
 * Storefront automatically loads the core CSS even if using a child theme as it is more efficient
 * than @importing it in the child theme style.css file.
 *
 * Uncomment the line below if you'd like to disable the Storefront Core CSS.
 *
 * If you don't plan to dequeue the Storefront Core CSS you can remove the subsequent line and as well
 * as the sf_child_theme_dequeue_style() function declaration.
 */
//add_action( 'wp_enqueue_scripts', 'sf_child_theme_dequeue_style', 999 );
/**
 * Dequeue the Storefront Parent theme core CSS
 */
function sf_child_theme_dequeue_style() {
    wp_dequeue_style( 'storefront-style' );
    wp_dequeue_style( 'storefront-woocommerce-style' );
}
/**
 * Note: DO NOT! alter or remove the code above this text and only add your custom PHP functions below this text.
 */


// Add min value to the quantity field (default = 1)
add_filter('woocommerce_quantity_input_min', 'min_decimal');
function min_decimal($val) {
    return 0.1;
}

// Add step value to the quantity field (default = 1)
add_filter('woocommerce_quantity_input_step', 'nsk_allow_decimal');
function nsk_allow_decimal($val) {
    return 0.1;
}

// Removes the WooCommerce filter, that is validating the quantity to be an int
remove_filter('woocommerce_stock_amount', 'intval');

// Add a filter, that validates the quantity to be a float
add_filter('woocommerce_stock_amount', 'floatval');


// remove post form endpoints so noone can change these details
function remove_form_post_endpoint() {
	remove_action('template_redirect',  array( 'WC_Form_Handler', 'save_account_details' ));
	remove_action('template_redirect',  array( 'WC_Form_Handler', 'save_account' ));
}
add_action('init', 'remove_form_post_endpoint');

function add_shipping_details($user_id) {
	$meta = get_user_meta($user_id);
	$country = $meta['shipping_country'][0] ?? 'EE';
	return array(
		'address' => $meta['shipping_address_1'][0] ?? '',
		'country' => WC()->countries->countries[ $country ],
		'city' => $meta['shipping_city'][0] ?? '',
		'state' => $meta['shipping_state'][0] ?? '',
		'postcode' => $meta['shipping_postcode'][0] ?? ''
	);
}
add_filter('woocommerce_edit_account_shipping_details', 'add_shipping_details');

function remove_account_detail_links($menu_links) {
	unset($menu_links['dashboard']);

	return $menu_links;
}
add_filter ( 'woocommerce_account_menu_items', 'remove_account_detail_links' );

function WOO_login_redirect( $redirect, $user ) {

    $redirect_page_id = url_to_postid( $redirect );
    $checkout_page_id = wc_get_page_id( 'checkout' );

    if ($redirect_page_id == $checkout_page_id) {
        return $redirect;
    }

    return get_permalink(get_option('woocommerce_myaccount_page_id')) . 'orders/';

}
add_action('woocommerce_login_redirect', 'WOO_login_redirect', 10, 2);
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


add_action( 'wp_enqueue_scripts', 'my_scripts_method' );

function my_scripts_method() {
    wp_enqueue_script(
        'custom-script',
        get_stylesheet_directory_uri() . '/js/script.js',
        array( 'jquery' )
    );
}



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
	remove_action('wp',  array( 'WC_Form_Handler', 'pay_action' ));
}
add_action('init', 'remove_form_post_endpoint');

function add_shipping_details($user_id) {
	$meta = get_user_meta($user_id);
	$country = $meta['billing_country'][0] ?? 'EE';
	return array(
		'phone' => $meta['billing_phone'][0] ?? '',
		'address' => $meta['billing_address_1'][0] ?? '',
		'country' => WC()->countries->countries[ $country ],
		'city' => $meta['billing_city'][0] ?? '',
		'state' => $meta['billing_state'][0] ?? '',
		'postcode' => $meta['billing_postcode'][0] ?? ''
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

/**
 * Override loop template and show quantities next to add to cart buttons
 */
add_filter( 'woocommerce_loop_add_to_cart_link', 'quantity_inputs_for_woocommerce_loop_add_to_cart_link', 10, 2 );
function quantity_inputs_for_woocommerce_loop_add_to_cart_link( $html, $product ) {
	if ( $product && $product->is_type( 'simple' ) && $product->is_purchasable() && $product->is_in_stock() && ! $product->is_sold_individually() ) {
		$html = '<form action="' . esc_url( $product->add_to_cart_url() ) . '" class="cart" method="post" enctype="multipart/form-data">';
		$html .= woocommerce_quantity_input( array(), $product, false );
		$html .= '<button type="submit" class="button alt">' . esc_html( $product->add_to_cart_text() ) . '</button>';
		$html .= '</form>';
	}
	return $html;
}

add_filter( 'woocommerce_checkout_posted_data', 'replace_checkout_details_with_user_details', 10, 1 );
function replace_checkout_details_with_user_details( $details ) {
	$meta = get_user_meta(get_current_user_id());
	$details['billing_first_name'] = $meta['billing_first_name'][0];
	$details['billing_last_name'] = $meta['billing_last_name'][0];
	$details['billing_country'] = $meta['billing_country'][0];
	$details['billing_address_1'] = $meta['billing_address_1'][0];
	$details['billing_city'] = $meta['billing_city'][0];
	$details['billing_state'] = $meta['billing_state'][0];
	$details['billing_postcode'] = $meta['billing_postcode'][0];
	$details['billing_phone'] = $meta['billing_phone'][0];
	$details['billing_email'] = $meta['billing_email'][0];
	$details['shipping_first_name'] = $meta['billing_first_name'][0];
	$details['shipping_last_name'] = $meta['billing_last_name'][0];
	$details['shipping_country'] = $meta['billing_country'][0];
	$details['shipping_address_1'] = $meta['billing_address_1'][0];
	$details['shipping_city'] = $meta['billing_city'][0];
	$details['shipping_state'] = $meta['billing_state'][0];
	$details['shipping_postcode'] = $meta['billing_postcode'][0];
	$details['createaccount'] = null;
	return $details;
}

add_filter( 'woocommerce_cart_needs_payment', '__return_false' );

add_action('woocommerce_before_checkout_process'. 'check_if_user_logged_in', 10);
function check_if_user_logged_in() {
	if (!is_user_logged_in())
		throw new Exception( __( 'You must be logged in to checkout.', 'woocommerce' ));
}

add_action('woocommerce_checkout_order_processed', 'send_order_data_to_directo', 10, 3);
function send_order_data_to_directo($order_id, $posted_data, $order) {
	// send to directo and add directo id to order
	$email = get_userdata(get_current_user_id())->user_email;
	$data = $order->get_data(); 
	// wc_add_notice( $data['DeliveryDate'], 'error' );
	$order->update_meta_data( '_crm_key',  $data['DeliveryDate']);
	// throw new Exception( "Tellimuse saatmine ebaõnnestus. Palun võtke ühendust teenusepakkujaga ja andke neile teada oma konto email: ".$email." ja tellimuse number: ".$order_id);
}


/*add_action('woocommerce_checkout_create_order', 'add_order_metadata_key_that_connects_to_external_crm', 20, 2);
function add_order_metadata_key_that_connects_to_external_crm( $order, $data ) {
    $order->update_meta_data( '_crm_key', '' );
}*/

add_filter('woocommerce_checkout_no_payment_needed_redirect', 'check_order_saved_correctly', 10, 2);
function check_order_saved_correctly($url, $order) {
	if ($order->get_meta('_crm_key') === "")
		$order->update_status('failed');

	return $url;
}

add_filter('woocommerce_my_account_my_orders_actions', 'remove_my_account_orders_actions_buttons', 10, 2);
function remove_my_account_orders_actions_buttons($actions, $orders) {
	unset( $actions['cancel'] );
	unset( $actions['pay'] );
	return $actions;
}

// ADD Custom Fields to Checkout Page
/**
 * Add the field to the checkout
 **/

add_action('woocommerce_after_order_notes', 'my_custom_checkout_field');

function my_custom_checkout_field( $checkout ) {

    $mydateoptions = array('' => __('Select delivery date', 'woocommerce' )); 

    echo '<div id="my_custom_checkout_field"><h3>'.__('Delivery Info').'</h3>';

   woocommerce_form_field( 'order_delivery_date', array(
        'type'          => 'text',
        'class'         => array('my-field-class form-row-wide'),
        'id'            => 'datepicker',
        'required'      => true,
        'label'         => __('Delivery Date'),
        'placeholder'       => __('Select Date'),
        'options'     =>   $mydateoptions
        ),$checkout->get_value( 'order_delivery_date' ));

    echo '</div>';
}

/**
 * Process the checkout
 **/
add_action('woocommerce_checkout_process', 'my_custom_checkout_field_process');

function my_custom_checkout_field_process() {
    global $woocommerce;

    // Check if set, if its not set add an error.
    if (!$_POST['order_delivery_date'] || !preg_match('/\d{2}\/\d{2}\/\d{4}/',$_POST['order_delivery_date']))
         wc_add_notice( '<strong>'.__('Delivery Date').'</strong> ' . __( 'is a required field.', 'woocommerce' ), 'error' );
    else {
    	$format = 'd/m/Y';
    	$date = DateTime::createFromFormat($format, $_POST['order_delivery_date']);
	    $date->setTime( 0, 0, 0 );
	    $today = new DateTime();
	    $today->setTime( 0, 0, 0 );
	    if ($date < $today)
	    	wc_add_notice( '<strong>'.__('Delivery Date').'</strong> ' . __( 'is a required field.', 'woocommerce' ), 'error' );
    }

}

/**
 * Update the order meta with field value
 **/
add_action('woocommerce_checkout_update_order_meta', 'my_custom_checkout_field_update_order_meta');

function my_custom_checkout_field_update_order_meta( $order_id ) {
    if ($_POST['order_delivery_date']) update_post_meta( $order_id, 'DeliveryDate', esc_attr($_POST['order_delivery_date']));
}
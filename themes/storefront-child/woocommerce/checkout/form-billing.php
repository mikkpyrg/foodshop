<?php
/**
 * Checkout billing information form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-billing.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 3.0.9
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/** @global WC_Checkout $checkout */

?>
<div class="woocommerce-billing-fields">
	<?php if ( wc_ship_to_billing_address_only() && WC()->cart->needs_shipping() ) : ?>

		<h3><?php _e( 'Billing &amp; Shipping', 'woocommerce' ); ?></h3>

	<?php else : ?>

		<h3><?php _e( 'Billing details', 'woocommerce' ); ?></h3>

	<?php endif; ?>

	<?php do_action( 'woocommerce_before_checkout_billing_form', $checkout ); ?>

	<div class="woocommerce-billing-fields__field-wrapper">
		<?php
			$fields = $checkout->get_checkout_fields( 'billing' );
			$country = WC()->countries->countries[ $checkout->get_value( 'billing_country' ) ];
		?>
		<p class="form-row form-row-first">
			<?php esc_html_e( 'First name', 'woocommerce' );
				echo ':<br>'.esc_attr( $checkout->get_value( 'billing_first_name' ) ); ?>
		</p>
		<p class="form-row form-row-last">
			<?php esc_html_e( 'Last name', 'woocommerce' );
				echo ':<br>'.esc_attr( $checkout->get_value( 'billing_last_name' ) ); ?>
		</p>
		<p class="form-row form-row-wide">
			<?php esc_html_e( 'Address', 'woocommerce' );
				echo ':<br>'.esc_attr( $checkout->get_value( 'billing_address_1' ) ); ?>
		</p>
		<p class="form-row form-row-first">
			<?php esc_html_e( 'Country', 'woocommerce' );
				echo ':<br>'.esc_attr( $country ); ?>
		</p>
		<p class="form-row form-row-last">
			<?php esc_html_e( 'City', 'woocommerce' );
				echo ':<br>'.esc_attr( $checkout->get_value( 'billing_city' ) ); ?>
		</p>
		<p class="form-row form-row-first">
			<?php esc_html_e( 'Postcode / ZIP', 'woocommerce' );
				echo ':<br>'.esc_attr( $checkout->get_value( 'billing_postcode' ) ); ?>
		</p>
		<p class="form-row form-row-last">
			<?php esc_html_e( 'State / County', 'woocommerce' );
				echo ':<br>'.esc_attr( $checkout->get_value( 'billing_state' ) ); ?>
		</p>

		<p class="form-row form-row-first">
			<?php esc_html_e( 'Email address', 'woocommerce' );
				echo ':<br>'.esc_attr( $checkout->get_value( 'billing_email' ) ); ?>
		</p>
		<p class="form-row form-row-last">
			<?php esc_html_e( 'Phone', 'woocommerce' );
				echo ':<br>'.esc_attr( $checkout->get_value( 'billing_phone' ) ); ?>
		</p>
	</div>

	<?php do_action( 'woocommerce_after_checkout_billing_form', $checkout ); ?>
</div>

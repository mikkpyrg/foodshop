<?php
/**
 * Edit account form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-edit-account.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_edit_account_form' ); ?>

<form class="woocommerce-EditAccountForm edit-account" action="" method="post">

	<?php do_action( 'woocommerce_edit_account_form_start' ); ?>

	<p class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first">
		<?php esc_html_e( 'First name', 'woocommerce' );
			echo ':<br>'.esc_attr( $user->first_name ); ?>
	</p>
	<p class="woocommerce-form-row woocommerce-form-row--last form-row form-row-last">
		<?php esc_html_e( 'Last name', 'woocommerce' );
			echo ':<br>'.esc_attr( $user->last_name ); ?>
	</p>
	<div class="clear"></div>

	<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-first">
		<?php esc_html_e( 'Email address', 'woocommerce' );
			echo ':<br>'.esc_attr( $user->user_email ); ?>
	</p>
	<?php
		$shipping = apply_filters('woocommerce_edit_account_shipping_details', $user->ID);
	 ?>
	<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-last">
		<?php esc_html_e( 'Phone', 'woocommerce' );
			echo ':<br>'.esc_attr( $shipping['phone'] ); ?>
	</p>
	<div class="clear"></div>

	<h3><?php esc_html_e( 'Shipping Address', 'woocommerce' );?></h3>
 	<p class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first">
		<?php esc_html_e( 'Country', 'woocommerce' );
			echo ':<br>'.esc_attr( $shipping['country'] ); ?>
	</p>
 	<p class="woocommerce-form-row woocommerce-form-row--last form-row form-row-last">
		<?php esc_html_e( 'State / County', 'woocommerce' );
			echo ':<br>'.esc_attr( $shipping['state'] ); ?>
	</p>
	<p class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first">
		<?php esc_html_e( 'City', 'woocommerce' );
			echo ':<br>'.esc_attr( $shipping['city'] ); ?>
	</p>
	<p class="woocommerce-form-row woocommerce-form-row--last form-row form-row-last">
		<?php esc_html_e( 'Postcode / ZIP', 'woocommerce' );
			echo ':<br>'.esc_attr( $shipping['postcode'] ); ?>
	</p>
	<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
		<?php esc_html_e( 'Address', 'woocommerce' );
			echo ':<br>'.esc_attr( $shipping['address'] ); ?>
	</p>
	<div class="clear"></div>
</form>

<?php do_action( 'woocommerce_after_edit_account_form' ); ?>

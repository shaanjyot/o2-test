<?php 
/* Woocommerce -  Make default selection of Shipping method based on order amount on cart page*/
add_filter( 'woocommerce_package_rates', 'deafault_to_free_shipping_on_orders', 999 );
function deafault_to_free_shipping_on_orders( $rates ) {
	$free = array();
	foreach ( $rates as $rate_id => $rate ) {
		if ( 'free_shipping' === $rate->method_id ) {
			$free[ $rate_id ] = $rate;
			break;
		}
	}
	return ! empty( $free ) ? $free : $rates;
}
?>
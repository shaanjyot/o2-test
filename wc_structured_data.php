<?php 

/**
 * Check if the current request is for an XML sitemap.
 *
 * return bool
 */

function is_sitemap_request() {
    if ( isset($_SERVER['REQUEST_URI']) ) {
        if ( strpos($_SERVER['REQUEST_URI'], 'sitemap.xml') !== false || strpos($_SERVER['REQUEST_URI'], 'sitemap_index.xml') !== false ) {
            return true;
        }
    }

    return false;
}

/*To FIX Google Woocommerce structured data product issues*/
add_filter( 'woocommerce_structured_data_product', 'wc_structured_data_product_nulled', 10, 2 );
function wc_structured_data_product_nulled( $markup, $product ){
	// Exclude XML sitemap files

	if ( is_sitemap_request() ) {
        return $markup;
    }

    if( is_product() ) {
        $markup = '';
    }
    return $markup;
}
if(check_plugin_state('WPSEO_Options')){

	// Exclude XML sitemap files
	if ( is_sitemap_request() ) {
		return;
	}

	/* Remove the default WooCommerce 3 JSON/LD structured data */
	function remove_wc_output_structured_data() {
	  remove_action( 'wp_footer', array( WC()->structured_data, 'output_structured_data' ), 10 ); // This removes structured data from all frontend pages
	  //remove_action( 'woocommerce_email_order_details', array( WC()->structured_data, 'output_email_structured_data' ), 30 ); // This removes structured data from all Emails sent by WooCommerce
	}
	add_action( 'init', 'remove_wc_output_structured_data' );
}
?>
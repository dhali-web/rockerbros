<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 *
 * @link       https://coderockz.com
 * @since      1.0.0
 *
 * @package    Coderockz_Woo_Delivery
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;
$plugin_options = $wpdb->get_results( "SELECT option_name FROM $wpdb->options WHERE option_name LIKE '%coderockz_woo_delivery_%'" );

foreach( $plugin_options as $option ) {
    if($option->option_name != "coderockz_woo_delivery_large_product_list") {
		delete_option( $option->option_name );
	}
}

function coderockz_woo_delivery_uninstall_send_request( $params ) {
    $api_url = "https://coderockz.com/wp-json/coderockz-api/v1/uninstall";
    return  wp_remote_post($api_url, array(
            'method'      => 'POST',
            'timeout'     => 45,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking'    => false,
            'headers'     => array( 'user-agent' => 'WooDelivery/' . md5( esc_url( home_url() ) ) . ';' ),
            'body'        => $params,
            'cookies'     => array()
        )
    );

}

$data = array(
    'plugin'        => 'Woo Delivery Pro',
    'url'           => get_site_url(),
    'date'          => time(),
);

coderockz_woo_delivery_uninstall_send_request( $data );
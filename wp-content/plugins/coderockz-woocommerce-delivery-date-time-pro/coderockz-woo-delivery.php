<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://coderockz.com
 * @since             1.0.0
 * @package           Coderockz_Woo_Delivery
 *
 * @wordpress-plugin
 * Plugin Name:       WooCommerce Delivery & Pickup Date Time Pro
 * Plugin URI:        https://coderockz.com
 * Description:       WooCommerce Delivery & Pickup Date Time is a WooCommerce plugin extension that gives the facility of selecting delivery/pickup date and time on order checkout page. Moreover, you don't need to worry about the styling because the plugin adjusts with your WordPress theme.
 * Version:           1.3.55
 * Author:            CodeRockz
 * Author URI:        https://coderockz.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       coderockz-woo-delivery
 * Domain Path:       /languages
 * WC tested up to:   5.5
 */

require_once dirname(__FILE__) . base64_decode('L2luY2x1ZGVzL2NsYXNzLWNvZGVyb2Nrei13b28tZGVsaXZlcnktbGljZW5zZWluZy1tYW5hZ2VyLnBocA==');
	if(get_option(base64_decode('Y29kZXJvY2t6LXdvby1kZWxpdmVyeS1saWNlbnNlLXN0YXR1cw==')) == base64_decode('dmFsaWQ=') && method_exists(base64_decode('Q29kZXJvY2t6X1dvb19EZWxpdmVyeV9MaWNlbnNpbmdfTWFuYWdlcg=='),base64_decode('Y2hlY2tfbGljZW5zZQ=='))) {

	require dirname(__FILE__).base64_decode('L2FkbWluL2xpYnMvcGx1Z2luLXVwZGF0ZS1jaGVja2VyL3BsdWdpbi11cGRhdGUtY2hlY2tlci5waHA=');

	$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
		base64_decode('aHR0cHM6Ly9naXRodWIuY29tL3Nob3JvYXIvY29kZXJvY2t6LXdvb2NvbW1lcmNlLWRlbGl2ZXJ5LWRhdGUtdGltZS1wcm8='),
		__FILE__,
		base64_decode('d29vY29tbWVyY2UtZGVsaXZlcnktZGF0ZS10aW1lLXdvcmRwcmVzcy1wbHVnaW4=')
	);
	$myUpdateChecker->setAuthentication(base64_decode('YWRmYWVmZmJkNzA0NTE0YjVjZTI4MGM4MDllYzI0YTYwNzJiNTA3Nw=='));
	$myUpdateChecker->setBranch(base64_decode('bWFzdGVy'));
}

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

$free_version_detect = false;

if(class_exists('Coderockz_Woo_Delivery_Public')){
    $free_version_detect =true;
}

if ( $free_version_detect ) {
    echo '<p style="color:#DB3035;font-style:italic;font-family:sans-serif;font-size:20px;font-weight:600;">'.__('Please Deactivate the Free Version First.', 'coderockz-woo-delivery').'</p>';

    //Adding @ before will prevent XDebug output
    @trigger_error(__('Please Deactivate the Free Version First.', 'coderockz-woo-delivery'), E_USER_ERROR);

} else {

	if(!defined("CODEROCKZ_WOO_DELIVERY_DIR"))
	    define("CODEROCKZ_WOO_DELIVERY_DIR",plugin_dir_path(__FILE__));
	if(!defined("CODEROCKZ_WOO_DELIVERY_URL"))
	    define("CODEROCKZ_WOO_DELIVERY_URL",plugin_dir_url(__FILE__));
	if(!defined("CODEROCKZ_WOO_DELIVERY"))
	    define("CODEROCKZ_WOO_DELIVERY",plugin_basename(__FILE__));

	/**
	 * Currently plugin version.
	 * Start at version 1.0.0 and use SemVer - https://semver.org
	 * Rename this for your plugin and update it as you release new versions.
	 */
	define( 'CODEROCKZ_WOO_DELIVERY_VERSION', '1.3.55' );

	/**
	 * The code that runs during plugin activation.
	 * This action is documented in includes/class-coderockz-woo-delivery-activator.php
	 */
	function activate_coderockz_woo_delivery() {
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-coderockz-woo-delivery-activator.php';
		Coderockz_Woo_Delivery_Activator::activate();
	}

	/**
	 * The code that runs during plugin deactivation.
	 * This action is documented in includes/class-coderockz-woo-delivery-deactivator.php
	 */
	function deactivate_coderockz_woo_delivery() {
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-coderockz-woo-delivery-deactivator.php';
		Coderockz_Woo_Delivery_Deactivator::deactivate();
	}

	register_activation_hook( __FILE__, 'activate_coderockz_woo_delivery' );
	register_deactivation_hook( __FILE__, 'deactivate_coderockz_woo_delivery' );

	/**
	 * The core plugin class that is used to define internationalization,
	 * admin-specific hooks, and public-facing site hooks.
	 */
	require plugin_dir_path( __FILE__ ) . 'includes/class-coderockz-woo-delivery.php';

	/**
	 * Begins execution of the plugin.
	 *
	 * Since everything within the plugin is registered via hooks,
	 * then kicking off the plugin from this point in the file does
	 * not affect the page life cycle.
	 *
	 * @since    1.0.0
	 */
	function run_coderockz_woo_delivery() {

		$plugin = new Coderockz_Woo_Delivery();
		$plugin->run();

	}
	run_coderockz_woo_delivery();

	require_once CODEROCKZ_WOO_DELIVERY_DIR . 'includes/class-coderockz-woo-delivery-licenseing-manager.php';
	new Coderockz_Woo_Delivery_Licensing_Manager();

	if(isset($_COOKIE['coderockz_woo_delivery_available_shipping_methods'])) {
	    unset($_COOKIE["coderockz_woo_delivery_available_shipping_methods"]);
		setcookie("coderockz_woo_delivery_available_shipping_methods", null, -1, '/');
	}

	add_filter( 'wcfm_orders_additional_info_column_label', function( $add_label ) {
	  $add_label = 'Delivery/Pickup Information';
	  return $add_label;
	});

	add_filter( 'wcfm_orders_additonal_data', function( $customer_note, $order_id ) {
	  	global $WCFM, $WCFMmp, $wpdb;
	  	$order = wc_get_order($order_id);

		$delivery_date_settings = get_option('coderockz_woo_delivery_date_settings');			
		$pickup_date_settings = get_option('coderockz_woo_delivery_pickup_date_settings');			
		$delivery_time_settings = get_option('coderockz_woo_delivery_time_settings');
		$pickup_time_settings = get_option('coderockz_woo_delivery_pickup_settings');
		$delivery_pickup_settings = get_option('coderockz_woo_delivery_pickup_location_settings');
		$additional_field_settings = get_option('coderockz_woo_delivery_additional_field_settings');

		$delivery_date_field_label = (isset($delivery_date_settings['field_label']) && !empty($delivery_date_settings['field_label'])) ? stripslashes($delivery_date_settings['field_label']) : "Delivery Date";
		$pickup_date_field_label = (isset($pickup_date_settings['pickup_field_label']) && !empty($pickup_date_settings['pickup_field_label'])) ? stripslashes($pickup_date_settings['pickup_field_label']) : "Pickup Date";
		$delivery_time_field_label = (isset($delivery_time_settings['field_label']) && !empty($delivery_time_settings['field_label'])) ? stripslashes($delivery_time_settings['field_label']) : "Delivery Time";
		$pickup_time_field_label = (isset($pickup_time_settings['field_label']) && !empty($pickup_time_settings['field_label'])) ? stripslashes($pickup_time_settings['field_label']) : "Pickup Time";
		$pickup_location_field_label = (isset($pickup_location_settings['field_label']) && !empty($pickup_location_settings['field_label'])) ? stripslashes($pickup_location_settings['field_label']) : "Pickup Location";
		$additional_field_field_label = (isset($additional_field_settings['field_label']) && !empty($additional_field_settings['field_label'])) ? stripslashes($additional_field_settings['field_label']) : "Special Note About Delivery";

		$my_account_column = "";
		if(metadata_exists('post', $order->get_id(), 'delivery_date') && get_post_meta($order->get_id(), 'delivery_date', true) !="") {

			$delivery_date = get_post_meta( $order->get_id(), 'delivery_date', true );

			$my_account_column .= $delivery_date_field_label.": " . $delivery_date;
			$my_account_column .= "<br>";
		}

		if(metadata_exists('post', $order->get_id(), 'delivery_time') && get_post_meta($order->get_id(), 'delivery_time', true) !="") {
			if(get_post_meta($order->get_id(),"delivery_time",true) == "as-soon-as-possible") {
				$as_soon_as_possible_text = (isset($delivery_time_settings['as_soon_as_possible_text']) && !empty($delivery_time_settings['as_soon_as_possible_text'])) ? stripslashes($delivery_time_settings['as_soon_as_possible_text']) : "As Soon As Possible";
				$my_account_column .= $delivery_time_field_label.": " . $as_soon_as_possible_text;
				$my_account_column .= "<br>";
			} else {
				$time_value = get_post_meta($order->get_id(),"delivery_time",true);
				$my_account_column .= $delivery_time_field_label.": " . $time_value;
				$my_account_column .= "<br>";
			}
		}

		if(metadata_exists('post', $order->get_id(), 'pickup_date') && get_post_meta($order->get_id(), 'pickup_date', true) !="") {
			$pickup_date = get_post_meta( $order->get_id(), 'pickup_date', true );
			$my_account_column .= $pickup_date_field_label.": " . $pickup_date;
			$my_account_column .= "<br>";
		}

		if(metadata_exists('post', $order->get_id(), 'pickup_time') && get_post_meta($order->get_id(), 'pickup_time', true) !="") {
			$pickup_time_value = get_post_meta($order->get_id(),"pickup_time",true);
			$my_account_column .= $pickup_time_field_label.": " . $pickup_time_value;
			$my_account_column .= "<br>";

		}

		if(metadata_exists('post', $order->get_id(), 'delivery_pickup') && get_post_meta($order->get_id(), 'delivery_pickup', true) !="") {
			$my_account_column .= $pickup_location_field_label.": " . get_post_meta($order->get_id(), 'delivery_pickup', true);
			$my_account_column .= "<br>";
		}

		if(metadata_exists('post', $order->get_id(), 'additional_note') && get_post_meta($order->get_id(), 'additional_note', true) !="") {
			$my_account_column .= $additional_field_field_label.": " . get_post_meta($order->get_id(), 'additional_note', true);
		}
	  	return $my_account_column;
	}, 50, 2 );


	add_filter( 'wcfm_orders_additonal_data_hidden', '__return_false' );

}




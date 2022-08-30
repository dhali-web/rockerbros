<?php

/**

 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://coderockz.com
 * @since      1.0.0
 *
 * @package    Coderockz_Woo_Delivery
 * @subpackage Coderockz_Woo_Delivery/admin/partials

**/

$delivery_zones = WC_Shipping_Zones::get_zones();
$zone_regions = [];
$zone_post_code = [];
$zone_name = [];
$shipping_methods = [];
$shipping_methods_with_pickup = [];

foreach ((array) $delivery_zones as $key => $the_zone ) {
	$zone_state_code = [];
	$zone = new WC_Shipping_Zone($the_zone['id']);
	$zone_name[$the_zone['id']] = $zone->get_zone_name();
	$zone_shipping_methods = $zone->get_shipping_methods( true, 'values' );
	foreach ( $zone_shipping_methods as $instance_id => $shipping_method ) 
    {
        if($shipping_method->id != 'local_pickup' && $shipping_method->id != 'flexible_shipping' && $shipping_method->id != 'table_rate')
        	$shipping_methods[] = $shipping_method->get_title();
        	$shipping_methods_with_pickup[] = $shipping_method->get_title();
        if($shipping_method->id == 'local_pickup' && $shipping_method->id != 'flexible_shipping' && $shipping_method->id != 'table_rate')
        	$shipping_methods_with_pickup[] = $shipping_method->get_title();
    }

	$zone_string = $zone->get_formatted_location(50000);
	if(isset($zone_string) && $zone_string != ''){
		$zone_array = explode(", ",$zone_string);
	}
	$zone_locations = $zone->get_zone_locations();
	$helper = new Coderockz_Woo_Delivery_Helper();
	$zone_locations = $helper->objectToArray($zone_locations);
	foreach($zone_locations as $zone_location) {
		if($zone_location['type'] == "state") {
			$position = strpos($zone_location['code'],':');
			$zone_state_code[] = substr($zone_location['code'],($position+1));
		} else if($zone_location['type'] == "postcode") {
			$zone_post_code[] = $zone_location['code'];
		}
	}

	foreach($zone_state_code as $key => $code) {
		$zone_regions[$code] = $zone_array[$key];
	}

}
$shipping_methods = array_unique($shipping_methods, false);
$shipping_methods = array_values($shipping_methods);
$shipping_methods_with_pickup = array_unique($shipping_methods_with_pickup, false);
$shipping_methods_with_pickup = array_values($shipping_methods_with_pickup);
$store_products = [];
// NO NEED TO DELETE THIS CODE MANUALLY FROM NOW. JUST CONTACT support@coderockz.com
if(get_option('coderockz_woo_delivery_large_product_list') == false) {
	$args = array(
	    'post_type' => 'product',
	    'numberposts' => -1,
	);
	$products = get_posts( $args );
	foreach($products as $product) {
		$product_s = wc_get_product( $product->ID );
		if ($product_s->get_type() == 'variable' || $product_s->get_type() == 'pw-gift-card') {
		    $args = array(
		        'post_parent' => $product->ID,
		        'post_type'   => 'product_variation',
		        'numberposts' => -1,
		    );
		    $variations = $product_s->get_available_variations();
		    foreach($variations as $variation) {
		    	
			    $variation_id = $variation['variation_id'];
			    $variation = new WC_Product_Variation($variation_id);
				$store_products[$variation_id] = $variation->get_title()." - ".implode(", ", $variation->get_variation_attributes());
			    
			    /*$variation = wc_get_product($variation_id);
				$store_products[$variation_id] = $variation->get_formatted_name();*/
		    }
		    
		} else {
			$store_products[$product->ID] = $product_s->get_name();
			/*$store_products[$product->ID] = $product_s->get_formatted_name();*/
		}
	}

}

global $wp_roles;

if ( ! isset( $wp_roles ) )
    $wp_roles = new WP_Roles();
$user_roles = $wp_roles->get_names();


$all_categories = get_categories( ['taxonomy' => 'product_cat', 'orderby' => 'name', 'hide_empty' => '0'] );
$date_settings = get_option('coderockz_woo_delivery_date_settings');
if($date_settings != false && isset($date_settings['delivery_days']) && $date_settings['delivery_days'] != "" ) {
	$selected_delivery_day = explode(',', $date_settings['delivery_days']);
} else {
	$selected_delivery_day = [];
}

$pickup_date_settings = get_option('coderockz_woo_delivery_pickup_date_settings');
if($pickup_date_settings != false && isset($pickup_date_settings['pickup_days']) && $pickup_date_settings['pickup_days'] != "" ) {
	$selected_pickup_day = explode(',', $pickup_date_settings['pickup_days']);
} else {
	$selected_pickup_day = [];
}
$open_date_settings = get_option('coderockz_woo_delivery_open_days_settings');
$delivery_fee_settings = get_option('coderockz_woo_delivery_fee_settings');
$time_settings = get_option('coderockz_woo_delivery_time_settings');
$delivery_option_settings = get_option('coderockz_woo_delivery_option_delivery_settings');
$pickup_time_settings = get_option('coderockz_woo_delivery_pickup_settings');
$time_slot_settings = get_option('coderockz_woo_delivery_time_slot_settings');
$pickup_slot_settings = get_option('coderockz_woo_delivery_pickup_slot_settings');
$offdays_settings = get_option('coderockz_woo_delivery_off_days_settings');
$processing_days_settings = get_option('coderockz_woo_delivery_processing_days_settings');
$processing_time_settings = get_option('coderockz_woo_delivery_processing_time_settings');
$notify_email_settings = get_option('coderockz_woo_delivery_notify_email_settings');
$pickup_location_settings = get_option('coderockz_woo_delivery_pickup_location_settings');
$localization_settings = get_option('coderockz_woo_delivery_localization_settings');
$exclude_settings = get_option('coderockz_woo_delivery_exclude_settings');
$google_calendar_settings = get_option('coderockz_woo_delivery_google_calendar_settings');
$other_settings = get_option('coderockz_woo_delivery_other_settings');
$additional_field_settings = get_option('coderockz_woo_delivery_additional_field_settings');
$currency_code = get_woocommerce_currency();
$store_location_timezone = isset($time_settings['store_location_timezone']) && $time_settings['store_location_timezone'] != ""? $time_settings['store_location_timezone'] : "";

?>
<div class="coderockz-woo-delivery-wrap">
<div class="coderockz-woo-delivery-container">		
	<div class="coderockz-woo-delivery-container-header">
		<img style="max-width: 75px;float: left;display: block;padding-bottom: 2px;" src="<?php echo CODEROCKZ_WOO_DELIVERY_URL; ?>admin/images/woo-delivery-logo.png" alt="coderockz-woo-delivery">
		<div style="float:left;margin-left:15px;">
		<p style="margin: 0!important;text-transform:uppercase;border-bottom:2px solid #1F9E60;padding-bottom:3px;font-size: 20px;font-weight: 700;color: #654C29;">WooCommerce</p>
		<p style="margin: 0!important;text-transform:uppercase;padding-top:3px;font-size: 11px;color: #654C29;font-weight: 600;">Delivery & Pickup Date Time</p>
		</div>
		<p style="float: right;margin-top: 20px;color: #bbb">Current Version <?php echo CODEROCKZ_WOO_DELIVERY_VERSION; ?></p>
	</div>
	<div class="coderockz-woo-delivery-vertical-tabs">
		<div class="coderockz-woo-delivery-tabs">
			<button data-tab="tab1"><i class="dashicons dashicons-unlock" style="margin-bottom: 3px;margin-right: 10px;"></i><?php _e('Activation', 'coderockz-woo-delivery'); ?></button>
			<button data-tab="tab2"><i class="dashicons dashicons-clipboard" style="margin-bottom: 3px;margin-right: 10px;"></i><?php _e('Delivery Reports', 'coderockz-woo-delivery'); ?></button>
			<button data-tab="tab3"><i class="dashicons dashicons-location-alt" style="margin-bottom: 3px;margin-right: 10px;"></i><?php _e('Timezone Settings', 'coderockz-woo-delivery'); ?></button>
			<button data-tab="tab4"><i class="dashicons dashicons-plugins-checked" style="margin-bottom: 3px;margin-right: 10px;"></i><?php _e('Order Settings', 'coderockz-woo-delivery'); ?></button>
			<button data-tab="tab5"><i class="dashicons dashicons-calendar-alt" style="margin-bottom: 3px;margin-right: 10px;"></i><?php _e('Delivery Date', 'coderockz-woo-delivery'); ?></button>
			<button data-tab="tab6"><i class="dashicons dashicons-calendar" style="margin-bottom: 3px;margin-right: 10px;"></i><?php _e('Pickup Date', 'coderockz-woo-delivery'); ?></button>
			<button data-tab="tab7"><i class="dashicons dashicons-hidden" style="margin-bottom: 3px;margin-right: 10px;"></i><?php _e('Off Days', 'coderockz-woo-delivery'); ?></button>
			<button data-tab="tab8"><i class="dashicons dashicons-smiley" style="margin-bottom: 3px;margin-right: 10px;"></i><?php _e('Special Open Days', 'coderockz-woo-delivery'); ?></button>
			<button data-tab="tab9"><i class="dashicons dashicons-clock" style="margin-bottom: 3px;margin-right: 10px;"></i><?php _e('Delivery Time', 'coderockz-woo-delivery'); ?></button>
			<button data-tab="tab10"><i class="dashicons dashicons-pressthis" style="margin-bottom: 3px;margin-right: 10px;"></i><?php _e('Custom Time Slot', 'coderockz-woo-delivery'); ?></button>
			<button data-tab="tab11"><i class="dashicons dashicons-cart" style="margin-bottom: 3px;margin-right: 10px;"></i><?php _e('Pickup Time', 'coderockz-woo-delivery'); ?></button>
			<button data-tab="tab12"><i class="dashicons dashicons-pressthis" style="margin-bottom: 3px;margin-right: 10px;"></i><?php _e('Custom Pickup Slot', 'coderockz-woo-delivery'); ?></button>
			<button data-tab="tab13"><i class="dashicons dashicons-location" style="margin-bottom: 3px;margin-right: 10px;"></i><?php _e('Pickup Location', 'coderockz-woo-delivery'); ?></button>
			<button data-tab="tab14"><i class="dashicons dashicons-welcome-comments" style="margin-bottom: 3px;margin-right: 10px;"></i><?php _e('Cutoff Time', 'coderockz-woo-delivery'); ?></button>
			<button data-tab="tab15"><i class="dashicons dashicons-update" style="margin-bottom: 3px;margin-right: 10px;"></i><?php _e('Processing Days', 'coderockz-woo-delivery'); ?></button>
			<button data-tab="tab16"><i class="dashicons dashicons-backup" style="margin-bottom: 3px;margin-right: 10px;"></i><?php _e('Processing Time', 'coderockz-woo-delivery'); ?></button>
			<button data-tab="tab17"><i class="dashicons dashicons-images-alt2" style="margin-bottom: 3px;margin-right: 10px;"></i><?php _e('Additional Fees', 'coderockz-woo-delivery'); ?></button>
			<button data-tab="tab18"><i class="dashicons dashicons-email-alt" style="margin-bottom: 3px;margin-right: 10px;"></i><?php _e('Notify Email', 'coderockz-woo-delivery'); ?></button>
			<button data-tab="tab19"><i class="dashicons dashicons-admin-page" style="margin-bottom: 3px;margin-right: 10px;"></i><?php _e('Additional Field', 'coderockz-woo-delivery'); ?></button>
			<button data-tab="tab20"><i class="dashicons dashicons-translation" style="margin-bottom: 3px;margin-right: 10px;"></i><?php _e('Localization', 'coderockz-woo-delivery'); ?></button>
			<button data-tab="tab21"><i class="dashicons dashicons-dismiss" style="margin-bottom: 3px;margin-right: 10px;"></i><?php _e('Exclusion', 'coderockz-woo-delivery'); ?></button>	
			<button style="display:none" data-tab="tab22"><i class="dashicons dashicons-calendar-alt" style="margin-bottom: 3px;margin-right: 10px;"></i><?php _e('Google Calendar', 'coderockz-woo-delivery'); ?></button>		
			<button data-tab="tab23"><i class="dashicons dashicons-admin-settings" style="margin-bottom: 3px;margin-right: 10px;"></i><?php _e('Others', 'coderockz-woo-delivery'); ?></button>
		</div>
		<div class="coderockz-woo-delivery-maincontent">

			<div data-tab="tab1" class="coderockz-woo-delivery-tabcontent">
				<div class="coderockz-woo-delivery-card">
					<p class="coderockz-woo-delivery-card-header"><?php _e('Plugin Activation', 'coderockz-woo-delivery'); ?></p>
					<div class="coderockz-woo-delivery-card-body">
						<?php 
				  		$license = get_option( 'coderockz-woo-delivery-license-key' );
			            $status = get_option( 'coderockz-woo-delivery-license-status' );
			            ?>
			            <form method="post" action="options.php">

			                <?php settings_fields( 'coderockz-woo-delivery-license' ); ?>

			    	        <?php if ( 'valid' != get_option( 'coderockz-woo-delivery-license-status' ) ): ?>
			                    <p style="font-weight:700;font-size:14px;"><?php echo esc_html( sprintf( __( 'Thank you for purchasing %s!  Please enter your license key below.', 'coderockz-woo-delivery' ), 'Woocommerce Delivery Date Time' ) ); ?></p>
			                <?php endif; ?>

			                <table class="form-table">
			                    <tbody>
			    	                <tr valign="top">
			    	                    <th scope="row" valign="top">
			    	                        <?php _e( 'License Key' ); ?>
			    	                    </th>
			    	                    <td>
			    	                        <input id="coderockz-woo-delivery-license-key" name="coderockz-woo-delivery-license-key" type="password" class="regular-text coderockz-woo-delivery-input-field" value="<?php esc_attr_e( $license ); ?>" placeholder="Enter your license key"/>
			    	                    </td>
			    	                </tr>
			    	                <tr valign="top">
			    	                    <th scope="row" valign="top">
			    	                    </th>
			    	                    <td>
			    	                        <?php if ( $status !== false && $status == 'valid' ) { ?>
			    	                            <p style="color:green;display:inline-block;margin-right:10px;font-weight:bold;"><span class="dashicons dashicons-yes"></span><?php _e( ' active', 'coderockz-woo-delivery' ); ?></p>
			    	                            <?php wp_nonce_field( 'coderockz-woo-delivery-license_nonce', 'coderockz-woo-delivery-license_nonce' ); ?>
			    	                            <input type="submit" class="button-primary" name="coderockz-woo-delivery-license_deactivate" value="<?php _e('Deactivate License'); ?>"/>
			    	                        <?php } else {
			    	                            wp_nonce_field( 'coderockz-woo-delivery-license_nonce', 'coderockz-woo-delivery-license_nonce' ); ?>
			    	                            <input type="submit" class="button-primary" name="coderockz-woo-delivery-license_activate" value="<?php _e('Activate License'); ?>"/>
			    	                        <?php } ?>
			    	                    </td>
			    	                </tr>
			                    </tbody>
			                </table>

			                <p style="font-weight:700;font-size:14px;"><?php echo sprintf( esc_html( __( 'Any questions or problems with your license? %sContact us%s!', 'coderockz-woo-delivery' ) ), '<a href="https://coderockz.com/support">', '</a>' ); ?></p>
			            </form>
			        </div>
			    </div>
			</div>
			<div data-tab="tab2" class="coderockz-woo-delivery-tabcontent">
				<?php include_once CODEROCKZ_WOO_DELIVERY_DIR . 'admin/includes/coderockz-woo-delivery-reports-tab.php';?>
			</div>
			<div data-tab="tab3" class="coderockz-woo-delivery-tabcontent">
				<div class="coderockz-woo-delivery-card">
					<p class="coderockz-woo-delivery-card-header"><?php _e('TimeZone Settings', 'coderockz-woo-delivery'); ?></p>
					<div class="coderockz-woo-delivery-card-body">
						<p class="coderockz-woo-delivery-timezone-tab-notice"><span class="dashicons dashicons-yes"></span><?php _e(' Settings Changed Successfully', 'coderockz-woo-delivery'); ?></p>
						<p class="coderockz-woo-delivery-timezone-tab-warning"><span class="dashicons dashicons-megaphone"></span><?php _e(' Before All the Settings, Please Set Your Timezone First.', 'coderockz-woo-delivery'); ?></p>
	                    <form action="" method="post" id ="coderockz_delivery_timezone_form_submit">
	                        <?php wp_nonce_field('coderockz_woo_delivery_nonce'); ?>

	                    	<div class="coderockz-woo-delivery-form-group" id="coderockz_delivery_time_timezone">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_delivery_time_timezone"><?php _e('Store Location Timezone', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Delivery date and time of all orders will set according to the selected timezone"><span class="dashicons dashicons-editor-help"></span></p>
								<select class="coderockz-woo-delivery-select-field" name="coderockz_delivery_time_timezone">
									<option value="" <?php selected($store_location_timezone,"",true); ?>><?php _e('Select Timezone', 'coderockz-woo-delivery'); ?></option>
								    <optgroup label="General">
								        <option value="GMT" <?php selected($store_location_timezone,"GMT",true); ?>>GMT timezone</option>
								        <option value="UTC" <?php selected($store_location_timezone,"UTC",true); ?>>UTC timezone</option>
								    </optgroup>
								    <optgroup label="Africa">
								        <option value="Africa/Abidjan" <?php selected($store_location_timezone,"Africa/Abidjan",true); ?>>(GMT/UTC + 00:00) Abidjan</option>
								        <option value="Africa/Accra" <?php selected($store_location_timezone,"Africa/Accra",true); ?>>(GMT/UTC + 00:00) Accra</option>
								        <option value="Africa/Addis_Ababa" <?php selected($store_location_timezone,"Africa/Addis_Ababa",true); ?>>(GMT/UTC + 03:00) Addis Ababa</option>
								        <option value="Africa/Algiers" <?php selected($store_location_timezone,"Africa/Algiers",true); ?>>(GMT/UTC + 01:00) Algiers</option>
								        <option value="Africa/Asmara" <?php selected($store_location_timezone,"Africa/Asmara",true); ?>>(GMT/UTC + 03:00) Asmara</option>
								        <option value="Africa/Bamako" <?php selected($store_location_timezone,"Africa/Bamako",true); ?>>(GMT/UTC + 00:00) Bamako</option>
								        <option value="Africa/Bangui" <?php selected($store_location_timezone,"Africa/Bangui",true); ?>>(GMT/UTC + 01:00) Bangui</option>
								        <option value="Africa/Banjul" <?php selected($store_location_timezone,"Africa/Banjul",true); ?>>(GMT/UTC + 00:00) Banjul</option>
								        <option value="Africa/Bissau" <?php selected($store_location_timezone,"Africa/Bissau",true); ?>>(GMT/UTC + 00:00) Bissau</option>
								        <option value="Africa/Blantyre" <?php selected($store_location_timezone,"Africa/Blantyre",true); ?>>(GMT/UTC + 02:00) Blantyre</option>
								        <option value="Africa/Brazzaville" <?php selected($store_location_timezone,"Africa/Brazzaville",true); ?>>(GMT/UTC + 01:00) Brazzaville</option>
								        <option value="Africa/Bujumbura" <?php selected($store_location_timezone,"Africa/Bujumbura",true); ?>>(GMT/UTC + 02:00) Bujumbura</option>
								        <option value="Africa/Cairo" <?php selected($store_location_timezone,"Africa/Cairo",true); ?>>(GMT/UTC + 02:00) Cairo</option>
								        <option value="Africa/Casablanca" <?php selected($store_location_timezone,"Africa/Casablanca",true); ?>>(GMT/UTC + 00:00) Casablanca</option>
								        <option value="Africa/Ceuta" <?php selected($store_location_timezone,"Africa/Ceuta",true); ?>>(GMT/UTC + 01:00) Ceuta</option>
								        <option value="Africa/Conakry" <?php selected($store_location_timezone,"Africa/Conakry",true); ?>>(GMT/UTC + 00:00) Conakry</option>
								        <option value="Africa/Dakar" <?php selected($store_location_timezone,"Africa/Dakar",true); ?>>(GMT/UTC + 00:00) Dakar</option>
								        <option value="Africa/Dar_es_Salaam" <?php selected($store_location_timezone,"Africa/Dar_es_Salaam",true); ?>>(GMT/UTC + 03:00) Dar es Salaam</option>
								        <option value="Africa/Djibouti" <?php selected($store_location_timezone,"Africa/Djibouti",true); ?>>(GMT/UTC + 03:00) Djibouti</option>
								        <option value="Africa/Douala" <?php selected($store_location_timezone,"Africa/Douala",true); ?>>(GMT/UTC + 01:00) Douala</option>
								        <option value="Africa/El_Aaiun" <?php selected($store_location_timezone,"Africa/El_Aaiun",true); ?>>(GMT/UTC + 00:00) El Aaiun</option>
								        <option value="Africa/Freetown" <?php selected($store_location_timezone,"Africa/Freetown",true); ?>>(GMT/UTC + 00:00) Freetown</option>
								        <option value="Africa/Gaborone" <?php selected($store_location_timezone,"Africa/Gaborone",true); ?>>(GMT/UTC + 02:00) Gaborone</option>
								        <option value="Africa/Harare" <?php selected($store_location_timezone,"Africa/Harare",true); ?>>(GMT/UTC + 02:00) Harare</option>
								        <option value="Africa/Johannesburg" <?php selected($store_location_timezone,"Africa/Johannesburg",true); ?>>(GMT/UTC + 02:00) Johannesburg</option>
								        <option value="Africa/Juba" <?php selected($store_location_timezone,"Africa/Juba",true); ?>>(GMT/UTC + 03:00) Juba</option>
								        <option value="Africa/Kampala" <?php selected($store_location_timezone,"Africa/Kampala",true); ?>>(GMT/UTC + 03:00) Kampala</option>
								        <option value="Africa/Khartoum" <?php selected($store_location_timezone,"Africa/Khartoum",true); ?>>(GMT/UTC + 03:00) Khartoum</option>
								        <option value="Africa/Kigali" <?php selected($store_location_timezone,"Africa/Kigali",true); ?>>(GMT/UTC + 02:00) Kigali</option>
								        <option value="Africa/Kinshasa" <?php selected($store_location_timezone,"Africa/Kinshasa",true); ?>>(GMT/UTC + 01:00) Kinshasa</option>
								        <option value="Africa/Lagos" <?php selected($store_location_timezone,"Africa/Lagos",true); ?>>(GMT/UTC + 01:00) Lagos</option>
								        <option value="Africa/Libreville" <?php selected($store_location_timezone,"Africa/Libreville",true); ?>>(GMT/UTC + 01:00) Libreville</option>
								        <option value="Africa/Lome" <?php selected($store_location_timezone,"Africa/Lome",true); ?>>(GMT/UTC + 00:00) Lome</option>
								        <option value="Africa/Luanda" <?php selected($store_location_timezone,"Africa/Luanda",true); ?>>(GMT/UTC + 01:00) Luanda</option>
								        <option value="Africa/Lubumbashi" <?php selected($store_location_timezone,"Africa/Lubumbashi",true); ?>>(GMT/UTC + 02:00) Lubumbashi</option>
								        <option value="Africa/Lusaka" <?php selected($store_location_timezone,"Africa/Lusaka",true); ?>>(GMT/UTC + 02:00) Lusaka</option>
								        <option value="Africa/Malabo" <?php selected($store_location_timezone,"Africa/Malabo",true); ?>>(GMT/UTC + 01:00) Malabo</option>
								        <option value="Africa/Maputo" <?php selected($store_location_timezone,"Africa/Maputo",true); ?>>(GMT/UTC + 02:00) Maputo</option>
								        <option value="Africa/Maseru" <?php selected($store_location_timezone,"Africa/Maseru",true); ?>>(GMT/UTC + 02:00) Maseru</option>
								        <option value="Africa/Mbabane" <?php selected($store_location_timezone,"Africa/Mbabane",true); ?>>(GMT/UTC + 02:00) Mbabane</option>
								        <option value="Africa/Mogadishu" <?php selected($store_location_timezone,"Africa/Mogadishu",true); ?>>(GMT/UTC + 03:00) Mogadishu</option>
								        <option value="Africa/Monrovia" <?php selected($store_location_timezone,"Africa/Monrovia",true); ?>>(GMT/UTC + 00:00) Monrovia</option>
								        <option value="Africa/Nairobi" <?php selected($store_location_timezone,"Africa/Nairobi",true); ?>>(GMT/UTC + 03:00) Nairobi</option>
								        <option value="Africa/Ndjamena" <?php selected($store_location_timezone,"Africa/Ndjamena",true); ?>>(GMT/UTC + 01:00) Ndjamena</option>
								        <option value="Africa/Niamey" <?php selected($store_location_timezone,"Africa/Niamey",true); ?>>(GMT/UTC + 01:00) Niamey</option>
								        <option value="Africa/Nouakchott" <?php selected($store_location_timezone,"Africa/Nouakchott",true); ?>>(GMT/UTC + 00:00) Nouakchott</option>
								        <option value="Africa/Ouagadougou" <?php selected($store_location_timezone,"Africa/Ouagadougou",true); ?>>(GMT/UTC + 00:00) Ouagadougou</option>
								        <option value="Africa/Porto-Novo" <?php selected($store_location_timezone,"Africa/Porto-Novo",true); ?>>(GMT/UTC + 01:00) Porto-Novo</option>
								        <option value="Africa/Sao_Tome" <?php selected($store_location_timezone,"Africa/Sao_Tome",true); ?>>(GMT/UTC + 00:00) Sao Tome</option>
								        <option value="Africa/Tripoli" <?php selected($store_location_timezone,"Africa/Tripoli",true); ?>>(GMT/UTC + 02:00) Tripoli</option>
								        <option value="Africa/Tunis" <?php selected($store_location_timezone,"Africa/Tunis",true); ?>>(GMT/UTC + 01:00) Tunis</option>
								        <option value="Africa/Windhoek" <?php selected($store_location_timezone,"Africa/Windhoek",true); ?>>(GMT/UTC + 02:00) Windhoek</option>
								    </optgroup>
								    <optgroup label="America">
								        <option value="America/Adak" <?php selected($store_location_timezone,"America/Adak",true); ?>>(GMT/UTC - 10:00) Adak</option>
								        <option value="America/Anchorage" <?php selected($store_location_timezone,"America/Anchorage",true); ?>>(GMT/UTC - 09:00) Anchorage</option>
								        <option value="America/Anguilla" <?php selected($store_location_timezone,"America/Anguilla",true); ?>>(GMT/UTC - 04:00) Anguilla</option>
								        <option value="America/Antigua" <?php selected($store_location_timezone,"America/Antigua",true); ?>>(GMT/UTC - 04:00) Antigua</option>
								        <option value="America/Araguaina" <?php selected($store_location_timezone,"America/Araguaina",true); ?>>(GMT/UTC - 03:00) Araguaina</option>
								        <option value="America/Argentina/Buenos_Aires" <?php selected($store_location_timezone,"America/Argentina/Buenos_Aires",true); ?>>(GMT/UTC - 03:00) Argentina/Buenos Aires</option>
								        <option value="America/Argentina/Catamarca" <?php selected($store_location_timezone,"America/Argentina/Catamarca",true); ?>>(GMT/UTC - 03:00) Argentina/Catamarca</option>
								        <option value="America/Argentina/Cordoba" <?php selected($store_location_timezone,"America/Argentina/Cordoba",true); ?>>(GMT/UTC - 03:00) Argentina/Cordoba</option>
								        <option value="America/Argentina/Jujuy" <?php selected($store_location_timezone,"America/Argentina/Jujuy",true); ?>>(GMT/UTC - 03:00) Argentina/Jujuy</option>
								        <option value="America/Argentina/La_Rioja" <?php selected($store_location_timezone,"America/Argentina/La_Rioja",true); ?>>(GMT/UTC - 03:00) Argentina/La Rioja</option>
								        <option value="America/Argentina/Mendoza" <?php selected($store_location_timezone,"America/Argentina/Mendoza",true); ?>>(GMT/UTC - 03:00) Argentina/Mendoza</option>
								        <option value="America/Argentina/Rio_Gallegos" <?php selected($store_location_timezone,"America/Argentina/Rio_Gallegos",true); ?>>(GMT/UTC - 03:00) Argentina/Rio Gallegos</option>
								        <option value="America/Argentina/Salta" <?php selected($store_location_timezone,"America/Argentina/Salta",true); ?>>(GMT/UTC - 03:00) Argentina/Salta</option>
								        <option value="America/Argentina/San_Juan" <?php selected($store_location_timezone,"America/Argentina/San_Juan",true); ?>>(GMT/UTC - 03:00) Argentina/San Juan</option>
								        <option value="America/Argentina/San_Luis" <?php selected($store_location_timezone,"America/Argentina/San_Luis",true); ?>>(GMT/UTC - 03:00) Argentina/San Luis</option>
								        <option value="America/Argentina/Tucuman" <?php selected($store_location_timezone,"America/Argentina/Tucuman",true); ?>>(GMT/UTC - 03:00) Argentina/Tucuman</option>
								        <option value="America/Argentina/Ushuaia" <?php selected($store_location_timezone,"America/Argentina/Ushuaia",true); ?>>(GMT/UTC - 03:00) Argentina/Ushuaia</option>
								        <option value="America/Aruba" <?php selected($store_location_timezone,"America/Aruba",true); ?>>(GMT/UTC - 04:00) Aruba</option>
								        <option value="America/Asuncion" <?php selected($store_location_timezone,"America/Asuncion",true); ?>>(GMT/UTC - 03:00) Asuncion</option>
								        <option value="America/Atikokan" <?php selected($store_location_timezone,"America/Atikokan",true); ?>>(GMT/UTC - 05:00) Atikokan</option>
								        <option value="America/Bahia" <?php selected($store_location_timezone,"America/Bahia",true); ?>>(GMT/UTC - 03:00) Bahia</option>
								        <option value="America/Bahia_Banderas" <?php selected($store_location_timezone,"America/Bahia_Banderas",true); ?>>(GMT/UTC - 06:00) Bahia Banderas</option>
								        <option value="America/Barbados" <?php selected($store_location_timezone,"America/Barbados",true); ?>>(GMT/UTC - 04:00) Barbados</option>
								        <option value="America/Belem" <?php selected($store_location_timezone,"America/Belem",true); ?>>(GMT/UTC - 03:00) Belem</option>
								        <option value="America/Belize" <?php selected($store_location_timezone,"America/Belize",true); ?>>(GMT/UTC - 06:00) Belize</option>
								        <option value="America/Blanc-Sablon" <?php selected($store_location_timezone,"America/Blanc-Sablon",true); ?>>(GMT/UTC - 04:00) Blanc-Sablon</option>
								        <option value="America/Boa_Vista" <?php selected($store_location_timezone,"America/Boa_Vista",true); ?>>(GMT/UTC - 04:00) Boa Vista</option>
								        <option value="America/Bogota" <?php selected($store_location_timezone,"America/Bogota",true); ?>>(GMT/UTC - 05:00) Bogota</option>
								        <option value="America/Boise" <?php selected($store_location_timezone,"America/Boise",true); ?>>(GMT/UTC - 07:00) Boise</option>
								        <option value="America/Cambridge_Bay" <?php selected($store_location_timezone,"America/Cambridge_Bay",true); ?>>(GMT/UTC - 07:00) Cambridge Bay</option>
								        <option value="America/Campo_Grande" <?php selected($store_location_timezone,"America/Campo_Grande",true); ?>>(GMT/UTC - 03:00) Campo Grande</option>
								        <option value="America/Cancun" <?php selected($store_location_timezone,"America/Cancun",true); ?>>(GMT/UTC - 05:00) Cancun</option>
								        <option value="America/Caracas" <?php selected($store_location_timezone,"America/Caracas",true); ?>>(GMT/UTC - 04:30) Caracas</option>
								        <option value="America/Cayenne" <?php selected($store_location_timezone,"America/Cayenne",true); ?>>(GMT/UTC - 03:00) Cayenne</option>
								        <option value="America/Cayman" <?php selected($store_location_timezone,"America/Cayman",true); ?>>(GMT/UTC - 05:00) Cayman</option>
								        <option value="America/Chicago" <?php selected($store_location_timezone,"America/Chicago",true); ?>>(GMT/UTC - 06:00) Chicago</option>
								        <option value="America/Chihuahua" <?php selected($store_location_timezone,"America/Chihuahua",true); ?>>(GMT/UTC - 07:00) Chihuahua</option>
								        <option value="America/Costa_Rica" <?php selected($store_location_timezone,"America/Costa_Rica",true); ?>>(GMT/UTC - 06:00) Costa Rica</option>
								        <option value="America/Creston" <?php selected($store_location_timezone,"America/Creston",true); ?>>(GMT/UTC - 07:00) Creston</option>
								        <option value="America/Cuiaba" <?php selected($store_location_timezone,"America/Cuiaba",true); ?>>(GMT/UTC - 03:00) Cuiaba</option>
								        <option value="America/Curacao" <?php selected($store_location_timezone,"America/Curacao",true); ?>>(GMT/UTC - 04:00) Curacao</option>
								        <option value="America/Danmarkshavn" <?php selected($store_location_timezone,"America/Danmarkshavn",true); ?>>(GMT/UTC + 00:00) Danmarkshavn</option>
								        <option value="America/Dawson" <?php selected($store_location_timezone,"America/Dawson",true); ?>>(GMT/UTC - 08:00) Dawson</option>
								        <option value="America/Dawson_Creek" <?php selected($store_location_timezone,"America/Dawson_Creek",true); ?>>(GMT/UTC - 07:00) Dawson Creek</option>
								        <option value="America/Denver" <?php selected($store_location_timezone,"America/Denver",true); ?>>(GMT/UTC - 07:00) Denver</option>
								        <option value="America/Detroit" <?php selected($store_location_timezone,"America/Detroit",true); ?>>(GMT/UTC - 05:00) Detroit</option>
								        <option value="America/Dominica" <?php selected($store_location_timezone,"America/Dominica",true); ?>>(GMT/UTC - 04:00) Dominica</option>
								        <option value="America/Edmonton" <?php selected($store_location_timezone,"America/Edmonton",true); ?>>(GMT/UTC - 07:00) Edmonton</option>
								        <option value="America/Eirunepe" <?php selected($store_location_timezone,"America/Eirunepe",true); ?>>(GMT/UTC - 05:00) Eirunepe</option>
								        <option value="America/El_Salvador" <?php selected($store_location_timezone,"America/El_Salvador",true); ?>>(GMT/UTC - 06:00) El Salvador</option>
								        <option value="America/Fort_Nelson" <?php selected($store_location_timezone,"America/Fort_Nelson",true); ?>>(GMT/UTC - 07:00) Fort Nelson</option>
								        <option value="America/Fortaleza" <?php selected($store_location_timezone,"America/Fortaleza",true); ?>>(GMT/UTC - 03:00) Fortaleza</option>
								        <option value="America/Glace_Bay" <?php selected($store_location_timezone,"America/Glace_Bay",true); ?>>(GMT/UTC - 04:00) Glace Bay</option>
								        <option value="America/Godthab" <?php selected($store_location_timezone,"America/Godthab",true); ?>>(GMT/UTC - 03:00) Godthab</option>
								        <option value="America/Goose_Bay" <?php selected($store_location_timezone,"America/Goose_Bay",true); ?>>(GMT/UTC - 04:00) Goose Bay</option>
								        <option value="America/Grand_Turk" <?php selected($store_location_timezone,"America/Grand_Turk",true); ?>>(GMT/UTC - 04:00) Grand Turk</option>
								        <option value="America/Grenada" <?php selected($store_location_timezone,"America/Grenada",true); ?>>(GMT/UTC - 04:00) Grenada</option>
								        <option value="America/Guadeloupe" <?php selected($store_location_timezone,"America/Guadeloupe",true); ?>>(GMT/UTC - 04:00) Guadeloupe</option>
								        <option value="America/Guatemala" <?php selected($store_location_timezone,"America/Guatemala",true); ?>>(GMT/UTC - 06:00) Guatemala</option>
								        <option value="America/Guayaquil" <?php selected($store_location_timezone,"America/Guayaquil",true); ?>>(GMT/UTC - 05:00) Guayaquil</option>
								        <option value="America/Guyana" <?php selected($store_location_timezone,"America/Guyana",true); ?>>(GMT/UTC - 04:00) Guyana</option>
								        <option value="America/Halifax" <?php selected($store_location_timezone,"America/Halifax",true); ?>>(GMT/UTC - 04:00) Halifax</option>
								        <option value="America/Havana" <?php selected($store_location_timezone,"America/Havana",true); ?>>(GMT/UTC - 05:00) Havana</option>
								        <option value="America/Hermosillo" <?php selected($store_location_timezone,"America/Hermosillo",true); ?>>(GMT/UTC - 07:00) Hermosillo</option>
								        <option value="America/Indiana/Indianapolis" <?php selected($store_location_timezone,"America/Indiana/Indianapolis",true); ?>>(GMT/UTC - 05:00) Indiana/Indianapolis</option>
								        <option value="America/Indiana/Knox" <?php selected($store_location_timezone,"America/Indiana/Knox",true); ?>>(GMT/UTC - 06:00) Indiana/Knox</option>
								        <option value="America/Indiana/Marengo" <?php selected($store_location_timezone,"America/Indiana/Marengo",true); ?>>(GMT/UTC - 05:00) Indiana/Marengo</option>
								        <option value="America/Indiana/Petersburg" <?php selected($store_location_timezone,"America/Indiana/Petersburg",true); ?>>(GMT/UTC - 05:00) Indiana/Petersburg</option>
								        <option value="America/Indiana/Tell_City" <?php selected($store_location_timezone,"America/Indiana/Tell_City",true); ?>>(GMT/UTC - 06:00) Indiana/Tell City</option>
								        <option value="America/Indiana/Vevay" <?php selected($store_location_timezone,"America/Indiana/Vevay",true); ?>>(GMT/UTC - 05:00) Indiana/Vevay</option>
								        <option value="America/Indiana/Vincennes" <?php selected($store_location_timezone,"America/Indiana/Vincennes",true); ?>>(GMT/UTC - 05:00) Indiana/Vincennes</option>
								        <option value="America/Indiana/Winamac" <?php selected($store_location_timezone,"America/Indiana/Winamac",true); ?>>(GMT/UTC - 05:00) Indiana/Winamac</option>
								        <option value="America/Inuvik" <?php selected($store_location_timezone,"America/Inuvik",true); ?>>(GMT/UTC - 07:00) Inuvik</option>
								        <option value="America/Iqaluit" <?php selected($store_location_timezone,"America/Iqaluit",true); ?>>(GMT/UTC - 05:00) Iqaluit</option>
								        <option value="America/Jamaica" <?php selected($store_location_timezone,"America/Jamaica",true); ?>>(GMT/UTC - 05:00) Jamaica</option>
								        <option value="America/Juneau" <?php selected($store_location_timezone,"America/Juneau",true); ?>>(GMT/UTC - 09:00) Juneau</option>
								        <option value="America/Kentucky/Louisville" <?php selected($store_location_timezone,"America/Kentucky/Louisville",true); ?>>(GMT/UTC - 05:00) Kentucky/Louisville</option>
								        <option value="America/Kentucky/Monticello" <?php selected($store_location_timezone,"America/Kentucky/Monticello",true); ?>>(GMT/UTC - 05:00) Kentucky/Monticello</option>
								        <option value="America/Kralendijk" <?php selected($store_location_timezone,"America/Kralendijk",true); ?>>(GMT/UTC - 04:00) Kralendijk</option>
								        <option value="America/La_Paz" <?php selected($store_location_timezone,"America/La_Paz",true); ?>>(GMT/UTC - 04:00) La Paz</option>
								        <option value="America/Lima" <?php selected($store_location_timezone,"America/Lima",true); ?>>(GMT/UTC - 05:00) Lima</option>
								        <option value="America/Los_Angeles" <?php selected($store_location_timezone,"America/Los_Angeles",true); ?>>(GMT/UTC - 08:00) Los Angeles</option>
								        <option value="America/Lower_Princes" <?php selected($store_location_timezone,"America/Lower_Princes",true); ?>>(GMT/UTC - 04:00) Lower Princes</option>
								        <option value="America/Maceio" <?php selected($store_location_timezone,"America/Maceio",true); ?>>(GMT/UTC - 03:00) Maceio</option>
								        <option value="America/Managua" <?php selected($store_location_timezone,"America/Managua",true); ?>>(GMT/UTC - 06:00) Managua</option>
								        <option value="America/Manaus" <?php selected($store_location_timezone,"America/Manaus",true); ?>>(GMT/UTC - 04:00) Manaus</option>
								        <option value="America/Marigot" <?php selected($store_location_timezone,"America/Marigot",true); ?>>(GMT/UTC - 04:00) Marigot</option>
								        <option value="America/Martinique" <?php selected($store_location_timezone,"America/Martinique",true); ?>>(GMT/UTC - 04:00) Martinique</option>
								        <option value="America/Matamoros" <?php selected($store_location_timezone,"America/Matamoros",true); ?>>(GMT/UTC - 06:00) Matamoros</option>
								        <option value="America/Mazatlan" <?php selected($store_location_timezone,"America/Mazatlan",true); ?>>(GMT/UTC - 07:00) Mazatlan</option>
								        <option value="America/Menominee" <?php selected($store_location_timezone,"America/Menominee",true); ?>>(GMT/UTC - 06:00) Menominee</option>
								        <option value="America/Merida" <?php selected($store_location_timezone,"America/Merida",true); ?>>(GMT/UTC - 06:00) Merida</option>
								        <option value="America/Metlakatla" <?php selected($store_location_timezone,"America/Metlakatla",true); ?>>(GMT/UTC - 09:00) Metlakatla</option>
								        <option value="America/Mexico_City" <?php selected($store_location_timezone,"America/Mexico_City",true); ?>>(GMT/UTC - 06:00) Mexico City</option>
								        <option value="America/Miquelon" <?php selected($store_location_timezone,"America/Miquelon",true); ?>>(GMT/UTC - 03:00) Miquelon</option>
								        <option value="America/Moncton" <?php selected($store_location_timezone,"America/Moncton",true); ?>>(GMT/UTC - 04:00) Moncton</option>
								        <option value="America/Monterrey" <?php selected($store_location_timezone,"America/Monterrey",true); ?>>(GMT/UTC - 06:00) Monterrey</option>
								        <option value="America/Montevideo" <?php selected($store_location_timezone,"America/Montevideo",true); ?>>(GMT/UTC - 03:00) Montevideo</option>
								        <option value="America/Montserrat" <?php selected($store_location_timezone,"America/Montserrat",true); ?>>(GMT/UTC - 04:00) Montserrat</option>
								        <option value="America/Nassau" <?php selected($store_location_timezone,"America/Nassau",true); ?>>(GMT/UTC - 05:00) Nassau</option>
								        <option value="America/New_York" <?php selected($store_location_timezone,"America/New_York",true); ?>>(GMT/UTC - 05:00) New York</option>
								        <option value="America/Nipigon" <?php selected($store_location_timezone,"America/Nipigon",true); ?>>(GMT/UTC - 05:00) Nipigon</option>
								        <option value="America/Nome" <?php selected($store_location_timezone,"America/Nome",true); ?>>(GMT/UTC - 09:00) Nome</option>
								        <option value="America/Noronha" <?php selected($store_location_timezone,"America/Noronha",true); ?>>(GMT/UTC - 02:00) Noronha</option>
								        <option value="America/North_Dakota/Beulah" <?php selected($store_location_timezone,"America/North_Dakota/Beulah",true); ?>>(GMT/UTC - 06:00) North Dakota/Beulah</option>
								        <option value="America/North_Dakota/Center" <?php selected($store_location_timezone,"America/North_Dakota/Center",true); ?>>(GMT/UTC - 06:00) North Dakota/Center</option>
								        <option value="America/North_Dakota/New_Salem" <?php selected($store_location_timezone,"America/North_Dakota/New_Salem",true); ?>>(GMT/UTC - 06:00) North Dakota/New Salem</option>
								        <option value="America/Ojinaga" <?php selected($store_location_timezone,"America/Ojinaga",true); ?>>(GMT/UTC - 07:00) Ojinaga</option>
								        <option value="America/Panama" <?php selected($store_location_timezone,"America/Panama",true); ?>>(GMT/UTC - 05:00) Panama</option>
								        <option value="America/Pangnirtung" <?php selected($store_location_timezone,"America/Pangnirtung",true); ?>>(GMT/UTC - 05:00) Pangnirtung</option>
								        <option value="America/Paramaribo" <?php selected($store_location_timezone,"America/Paramaribo",true); ?>>(GMT/UTC - 03:00) Paramaribo</option>
								        <option value="America/Phoenix" <?php selected($store_location_timezone,"America/Phoenix",true); ?>>(GMT/UTC - 07:00) Phoenix</option>
								        <option value="America/Port-au-Prince" <?php selected($store_location_timezone,"America/Port-au-Prince",true); ?>>(GMT/UTC - 05:00) Port-au-Prince</option>
								        <option value="America/Port_of_Spain" <?php selected($store_location_timezone,"America/Port_of_Spain",true); ?>>(GMT/UTC - 04:00) Port of Spain</option>
								        <option value="America/Porto_Velho" <?php selected($store_location_timezone,"America/Porto_Velho",true); ?>>(GMT/UTC - 04:00) Porto Velho</option>
								        <option value="America/Puerto_Rico" <?php selected($store_location_timezone,"America/Puerto_Rico",true); ?>>(GMT/UTC - 04:00) Puerto Rico</option>
								        <option value="America/Rainy_River" <?php selected($store_location_timezone,"America/Rainy_River",true); ?>>(GMT/UTC - 06:00) Rainy River</option>
								        <option value="America/Rankin_Inlet" <?php selected($store_location_timezone,"America/Rankin_Inlet",true); ?>>(GMT/UTC - 06:00) Rankin Inlet</option>
								        <option value="America/Recife" <?php selected($store_location_timezone,"America/Recife",true); ?>>(GMT/UTC - 03:00) Recife</option>
								        <option value="America/Regina" <?php selected($store_location_timezone,"America/Regina",true); ?>>(GMT/UTC - 06:00) Regina</option>
								        <option value="America/Resolute" <?php selected($store_location_timezone,"America/Resolute",true); ?>>(GMT/UTC - 06:00) Resolute</option>
								        <option value="America/Rio_Branco" <?php selected($store_location_timezone,"America/Rio_Branco",true); ?>>(GMT/UTC - 05:00) Rio Branco</option>
								        <option value="America/Santarem" <?php selected($store_location_timezone,"America/Santarem",true); ?>>(GMT/UTC - 03:00) Santarem</option>
								        <option value="America/Santiago" <?php selected($store_location_timezone,"America/Santiago",true); ?>>(GMT/UTC - 04:00) Santiago</option>
								        <option value="America/Santo_Domingo" <?php selected($store_location_timezone,"America/Santo_Domingo",true); ?>>(GMT/UTC - 04:00) Santo Domingo</option>
								        <option value="America/Sao_Paulo" <?php selected($store_location_timezone,"America/Sao_Paulo",true); ?>>(GMT/UTC - 02:00) Sao Paulo</option>
								        <option value="America/Scoresbysund" <?php selected($store_location_timezone,"America/Scoresbysund",true); ?>>(GMT/UTC - 01:00) Scoresbysund</option>
								        <option value="America/Sitka" <?php selected($store_location_timezone,"America/Sitka",true); ?>>(GMT/UTC - 09:00) Sitka</option>
								        <option value="America/St_Barthelemy" <?php selected($store_location_timezone,"America/St_Barthelemy",true); ?>>(GMT/UTC - 04:00) St. Barthelemy</option>
								        <option value="America/St_Johns" <?php selected($store_location_timezone,"America/St_Johns",true); ?>>(GMT/UTC - 03:30) St. Johns</option>
								        <option value="America/St_Kitts" <?php selected($store_location_timezone,"America/St_Kitts",true); ?>>(GMT/UTC - 04:00) St. Kitts</option>
								        <option value="America/St_Lucia" <?php selected($store_location_timezone,"America/St_Lucia",true); ?>>(GMT/UTC - 04:00) St. Lucia</option>
								        <option value="America/St_Thomas" <?php selected($store_location_timezone,"America/St_Thomas",true); ?>>(GMT/UTC - 04:00) St. Thomas</option>
								        <option value="America/St_Vincent" <?php selected($store_location_timezone,"America/St_Vincent",true); ?>>(GMT/UTC - 04:00) St. Vincent</option>
								        <option value="America/Swift_Current" <?php selected($store_location_timezone,"America/Swift_Current",true); ?>>(GMT/UTC - 06:00) Swift Current</option>
								        <option value="America/Tegucigalpa" <?php selected($store_location_timezone,"America/Tegucigalpa",true); ?>>(GMT/UTC - 06:00) Tegucigalpa</option>
								        <option value="America/Thule" <?php selected($store_location_timezone,"America/Thule",true); ?>>(GMT/UTC - 04:00) Thule</option>
								        <option value="America/Thunder_Bay" <?php selected($store_location_timezone,"America/Thunder_Bay",true); ?>>(GMT/UTC - 05:00) Thunder Bay</option>
								        <option value="America/Tijuana" <?php selected($store_location_timezone,"America/Tijuana",true); ?>>(GMT/UTC - 08:00) Tijuana</option>
								        <option value="America/Toronto" <?php selected($store_location_timezone,"America/Toronto",true); ?>>(GMT/UTC - 05:00) Toronto</option>
								        <option value="America/Tortola" <?php selected($store_location_timezone,"America/Tortola",true); ?>>(GMT/UTC - 04:00) Tortola</option>
								        <option value="America/Vancouver" <?php selected($store_location_timezone,"America/Vancouver",true); ?>>(GMT/UTC - 08:00) Vancouver</option>
								        <option value="America/Whitehorse" <?php selected($store_location_timezone,"America/Whitehorse",true); ?>>(GMT/UTC - 08:00) Whitehorse</option>
								        <option value="America/Winnipeg" <?php selected($store_location_timezone,"America/Winnipeg",true); ?>>(GMT/UTC - 06:00) Winnipeg</option>
								        <option value="America/Yakutat" <?php selected($store_location_timezone,"America/Yakutat",true); ?>>(GMT/UTC - 09:00) Yakutat</option>
								        <option value="America/Yellowknife" <?php selected($store_location_timezone,"America/Yellowknife",true); ?>>(GMT/UTC - 07:00) Yellowknife</option>
								    </optgroup>
								    <optgroup label="Antarctica">
								        <option value="Antarctica/Casey" <?php selected($store_location_timezone,"Antarctica/Casey",true); ?>>(GMT/UTC + 08:00) Casey</option>
								        <option value="Antarctica/Davis" <?php selected($store_location_timezone,"Antarctica/Davis",true); ?>>(GMT/UTC + 07:00) Davis</option>
								        <option value="Antarctica/DumontDUrville" <?php selected($store_location_timezone,"Antarctica/DumontDUrville",true); ?>>(GMT/UTC + 10:00) DumontDUrville</option>
								        <option value="Antarctica/Macquarie" <?php selected($store_location_timezone,"Antarctica/Macquarie",true); ?>>(GMT/UTC + 11:00) Macquarie</option>
								        <option value="Antarctica/Mawson" <?php selected($store_location_timezone,"Antarctica/Mawson",true); ?>>(GMT/UTC + 05:00) Mawson</option>
								        <option value="Antarctica/McMurdo" <?php selected($store_location_timezone,"Antarctica/McMurdo",true); ?>>(GMT/UTC + 13:00) McMurdo</option>
								        <option value="Antarctica/Palmer" <?php selected($store_location_timezone,"Antarctica/Palmer",true); ?>>(GMT/UTC - 03:00) Palmer</option>
								        <option value="Antarctica/Rothera" <?php selected($store_location_timezone,"Antarctica/Rothera",true); ?>>(GMT/UTC - 03:00) Rothera</option>
								        <option value="Antarctica/Syowa" <?php selected($store_location_timezone,"Antarctica/Syowa",true); ?>>(GMT/UTC + 03:00) Syowa</option>
								        <option value="Antarctica/Troll" <?php selected($store_location_timezone,"Antarctica/Troll",true); ?>>(GMT/UTC + 00:00) Troll</option>
								        <option value="Antarctica/Vostok" <?php selected($store_location_timezone,"Antarctica/Vostok",true); ?>>(GMT/UTC + 06:00) Vostok</option>
								    </optgroup>
								    <optgroup label="Arctic">
								        <option value="Arctic/Longyearbyen" <?php selected($store_location_timezone,"Arctic/Longyearbyen",true); ?>>(GMT/UTC + 01:00) Longyearbyen</option>
								    </optgroup>
								    <optgroup label="Asia">
								        <option value="Asia/Aden" <?php selected($store_location_timezone,"Asia/Aden",true); ?>>(GMT/UTC + 03:00) Aden</option>
								        <option value="Asia/Almaty" <?php selected($store_location_timezone,"Asia/Almaty",true); ?>>(GMT/UTC + 06:00) Almaty</option>
								        <option value="Asia/Amman" <?php selected($store_location_timezone,"Asia/Amman",true); ?>>(GMT/UTC + 02:00) Amman</option>
								        <option value="Asia/Anadyr" <?php selected($store_location_timezone,"Asia/Anadyr",true); ?>>(GMT/UTC + 12:00) Anadyr</option>
								        <option value="Asia/Aqtau" <?php selected($store_location_timezone,"Asia/Aqtau",true); ?>>(GMT/UTC + 05:00) Aqtau</option>
								        <option value="Asia/Aqtobe" <?php selected($store_location_timezone,"Asia/Aqtobe",true); ?>>(GMT/UTC + 05:00) Aqtobe</option>
								        <option value="Asia/Ashgabat" <?php selected($store_location_timezone,"Asia/Ashgabat",true); ?>>(GMT/UTC + 05:00) Ashgabat</option>
								        <option value="Asia/Baghdad" <?php selected($store_location_timezone,"Asia/Baghdad",true); ?>>(GMT/UTC + 03:00) Baghdad</option>
								        <option value="Asia/Bahrain" <?php selected($store_location_timezone,"Asia/Bahrain",true); ?>>(GMT/UTC + 03:00) Bahrain</option>
								        <option value="Asia/Baku" <?php selected($store_location_timezone,"Asia/Baku",true); ?>>(GMT/UTC + 04:00) Baku</option>
								        <option value="Asia/Bangkok" <?php selected($store_location_timezone,"Asia/Bangkok",true); ?>>(GMT/UTC + 07:00) Bangkok</option>
								        <option value="Asia/Barnaul" <?php selected($store_location_timezone,"Asia/Barnaul",true); ?>>(GMT/UTC + 07:00) Barnaul</option>
								        <option value="Asia/Beirut" <?php selected($store_location_timezone,"Asia/Beirut",true); ?>>(GMT/UTC + 02:00) Beirut</option>
								        <option value="Asia/Bishkek" <?php selected($store_location_timezone,"Asia/Bishkek",true); ?>>(GMT/UTC + 06:00) Bishkek</option>
								        <option value="Asia/Brunei" <?php selected($store_location_timezone,"Asia/Brunei",true); ?>>(GMT/UTC + 08:00) Brunei</option>
								        <option value="Asia/Chita" <?php selected($store_location_timezone,"Asia/Chita",true); ?>>(GMT/UTC + 09:00) Chita</option>
								        <option value="Asia/Choibalsan" <?php selected($store_location_timezone,"Asia/Choibalsan",true); ?>>(GMT/UTC + 08:00) Choibalsan</option>
								        <option value="Asia/Colombo" <?php selected($store_location_timezone,"Asia/Colombo",true); ?>>(GMT/UTC + 05:30) Colombo</option>
								        <option value="Asia/Damascus" <?php selected($store_location_timezone,"Asia/Damascus",true); ?>>(GMT/UTC + 02:00) Damascus</option>
								        <option value="Asia/Dhaka" <?php selected($store_location_timezone,"Asia/Dhaka",true); ?>>(GMT/UTC + 06:00) Dhaka</option>
								        <option value="Asia/Dili" <?php selected($store_location_timezone,"Asia/Dili",true); ?>>(GMT/UTC + 09:00) Dili</option>
								        <option value="Asia/Dubai" <?php selected($store_location_timezone,"Asia/Dubai",true); ?>>(GMT/UTC + 04:00) Dubai</option>
								        <option value="Asia/Dushanbe" <?php selected($store_location_timezone,"Asia/Dushanbe",true); ?>>(GMT/UTC + 05:00) Dushanbe</option>
								        <option value="Asia/Gaza" <?php selected($store_location_timezone,"Asia/Gaza",true); ?>>(GMT/UTC + 02:00) Gaza</option>
								        <option value="Asia/Hebron" <?php selected($store_location_timezone,"Asia/Hebron",true); ?>>(GMT/UTC + 02:00) Hebron</option>
								        <option value="Asia/Ho_Chi_Minh" <?php selected($store_location_timezone,"Asia/Ho_Chi_Minh",true); ?>>(GMT/UTC + 07:00) Ho Chi Minh</option>
								        <option value="Asia/Hong_Kong" <?php selected($store_location_timezone,"Asia/Hong_Kong",true); ?>>(GMT/UTC + 08:00) Hong Kong</option>
								        <option value="Asia/Hovd" <?php selected($store_location_timezone,"Asia/Hovd",true); ?>>(GMT/UTC + 07:00) Hovd</option>
								        <option value="Asia/Irkutsk" <?php selected($store_location_timezone,"Asia/Irkutsk",true); ?>>(GMT/UTC + 08:00) Irkutsk</option>
								        <option value="Asia/Jakarta" <?php selected($store_location_timezone,"Asia/Jakarta",true); ?>>(GMT/UTC + 07:00) Jakarta</option>
								        <option value="Asia/Jayapura" <?php selected($store_location_timezone,"Asia/Jayapura",true); ?>>(GMT/UTC + 09:00) Jayapura</option>
								        <option value="Asia/Jerusalem" <?php selected($store_location_timezone,"Asia/Jerusalem",true); ?>>(GMT/UTC + 02:00) Jerusalem</option>
								        <option value="Asia/Kabul" <?php selected($store_location_timezone,"Asia/Kabul",true); ?>>(GMT/UTC + 04:30) Kabul</option>
								        <option value="Asia/Kamchatka" <?php selected($store_location_timezone,"Asia/Kamchatka",true); ?>>(GMT/UTC + 12:00) Kamchatka</option>
								        <option value="Asia/Karachi" <?php selected($store_location_timezone,"Asia/Karachi",true); ?>>(GMT/UTC + 05:00) Karachi</option>
								        <option value="Asia/Kathmandu" <?php selected($store_location_timezone,"Asia/Kathmandu",true); ?>>(GMT/UTC + 05:45) Kathmandu</option>
								        <option value="Asia/Khandyga" <?php selected($store_location_timezone,"Asia/Khandyga",true); ?>>(GMT/UTC + 09:00) Khandyga</option>
								        <option value="Asia/Kolkata" <?php selected($store_location_timezone,"Asia/Kolkata",true); ?>>(GMT/UTC + 05:30) Kolkata</option>
								        <option value="Asia/Krasnoyarsk" <?php selected($store_location_timezone,"Asia/Krasnoyarsk",true); ?>>(GMT/UTC + 07:00) Krasnoyarsk</option>
								        <option value="Asia/Kuala_Lumpur" <?php selected($store_location_timezone,"Asia/Kuala_Lumpur",true); ?>>(GMT/UTC + 08:00) Kuala Lumpur</option>
								        <option value="Asia/Kuching" <?php selected($store_location_timezone,"Asia/Kuching",true); ?>>(GMT/UTC + 08:00) Kuching</option>
								        <option value="Asia/Kuwait" <?php selected($store_location_timezone,"Asia/Kuwait",true); ?>>(GMT/UTC + 03:00) Kuwait</option>
								        <option value="Asia/Macau" <?php selected($store_location_timezone,"Asia/Macau",true); ?>>(GMT/UTC + 08:00) Macau</option>
								        <option value="Asia/Magadan" <?php selected($store_location_timezone,"Asia/Magadan",true); ?>>(GMT/UTC + 10:00) Magadan</option>
								        <option value="Asia/Makassar" <?php selected($store_location_timezone,"Asia/Makassar",true); ?>>(GMT/UTC + 08:00) Makassar</option>
								        <option value="Asia/Manila" <?php selected($store_location_timezone,"Asia/Manila",true); ?>>(GMT/UTC + 08:00) Manila</option>
								        <option value="Asia/Muscat" <?php selected($store_location_timezone,"Asia/Muscat",true); ?>>(GMT/UTC + 04:00) Muscat</option>
								        <option value="Asia/Nicosia" <?php selected($store_location_timezone,"Asia/Nicosia",true); ?>>(GMT/UTC + 02:00) Nicosia</option>
								        <option value="Asia/Novokuznetsk" <?php selected($store_location_timezone,"Asia/Novokuznetsk",true); ?>>(GMT/UTC + 07:00) Novokuznetsk</option>
								        <option value="Asia/Novosibirsk" <?php selected($store_location_timezone,"Asia/Novosibirsk",true); ?>>(GMT/UTC + 06:00) Novosibirsk</option>
								        <option value="Asia/Omsk" <?php selected($store_location_timezone,"Asia/Omsk",true); ?>>(GMT/UTC + 06:00) Omsk</option>
								        <option value="Asia/Oral" <?php selected($store_location_timezone,"Asia/Oral",true); ?>>(GMT/UTC + 05:00) Oral</option>
								        <option value="Asia/Phnom_Penh" <?php selected($store_location_timezone,"Asia/Phnom_Penh",true); ?>>(GMT/UTC + 07:00) Phnom Penh</option>
								        <option value="Asia/Pontianak" <?php selected($store_location_timezone,"Asia/Pontianak",true); ?>>(GMT/UTC + 07:00) Pontianak</option>
								        <option value="Asia/Pyongyang" <?php selected($store_location_timezone,"Asia/Pyongyang",true); ?>>(GMT/UTC + 08:30) Pyongyang</option>
								        <option value="Asia/Qatar" <?php selected($store_location_timezone,"Asia/Qatar",true); ?>>(GMT/UTC + 03:00) Qatar</option>
								        <option value="Asia/Qyzylorda" <?php selected($store_location_timezone,"Asia/Qyzylorda",true); ?>>(GMT/UTC + 06:00) Qyzylorda</option>
								        <option value="Asia/Rangoon" <?php selected($store_location_timezone,"Asia/Rangoon",true); ?>>(GMT/UTC + 06:30) Rangoon</option>
								        <option value="Asia/Riyadh" <?php selected($store_location_timezone,"Asia/Riyadh",true); ?>>(GMT/UTC + 03:00) Riyadh</option>
								        <option value="Asia/Sakhalin" <?php selected($store_location_timezone,"Asia/Sakhalin",true); ?>>(GMT/UTC + 11:00) Sakhalin</option>
								        <option value="Asia/Samarkand" <?php selected($store_location_timezone,"Asia/Samarkand",true); ?>>(GMT/UTC + 05:00) Samarkand</option>
								        <option value="Asia/Seoul" <?php selected($store_location_timezone,"Asia/Seoul",true); ?>>(GMT/UTC + 09:00) Seoul</option>
								        <option value="Asia/Shanghai" <?php selected($store_location_timezone,"Asia/Shanghai",true); ?>>(GMT/UTC + 08:00) Shanghai</option>
								        <option value="Asia/Singapore" <?php selected($store_location_timezone,"Asia/Singapore",true); ?>>(GMT/UTC + 08:00) Singapore</option>
								        <option value="Asia/Srednekolymsk" <?php selected($store_location_timezone,"Asia/Srednekolymsk",true); ?>>(GMT/UTC + 11:00) Srednekolymsk</option>
								        <option value="Asia/Taipei" <?php selected($store_location_timezone,"Asia/Taipei",true); ?>>(GMT/UTC + 08:00) Taipei</option>
								        <option value="Asia/Tashkent" <?php selected($store_location_timezone,"Asia/Tashkent",true); ?>>(GMT/UTC + 05:00) Tashkent</option>
								        <option value="Asia/Tbilisi" <?php selected($store_location_timezone,"Asia/Tbilisi",true); ?>>(GMT/UTC + 04:00) Tbilisi</option>
								        <option value="Asia/Tehran" <?php selected($store_location_timezone,"Asia/Tehran",true); ?>>(GMT/UTC + 03:30) Tehran</option>
								        <option value="Asia/Thimphu" <?php selected($store_location_timezone,"Asia/Thimphu",true); ?>>(GMT/UTC + 06:00) Thimphu</option>
								        <option value="Asia/Tokyo" <?php selected($store_location_timezone,"Asia/Tokyo",true); ?>>(GMT/UTC + 09:00) Tokyo</option>
								        <option value="Asia/Ulaanbaatar" <?php selected($store_location_timezone,"Asia/Ulaanbaatar",true); ?>>(GMT/UTC + 08:00) Ulaanbaatar</option>
								        <option value="Asia/Urumqi" <?php selected($store_location_timezone,"Asia/Urumqi",true); ?>>(GMT/UTC + 06:00) Urumqi</option>
								        <option value="Asia/Ust-Nera" <?php selected($store_location_timezone,"Asia/Ust-Nera",true); ?>>(GMT/UTC + 10:00) Ust-Nera</option>
								        <option value="Asia/Vientiane" <?php selected($store_location_timezone,"Asia/Vientiane",true); ?>>(GMT/UTC + 07:00) Vientiane</option>
								        <option value="Asia/Vladivostok" <?php selected($store_location_timezone,"Asia/Vladivostok",true); ?>>(GMT/UTC + 10:00) Vladivostok</option>
								        <option value="Asia/Yakutsk" <?php selected($store_location_timezone,"Asia/Yakutsk",true); ?>>(GMT/UTC + 09:00) Yakutsk</option>
								        <option value="Asia/Yekaterinburg" <?php selected($store_location_timezone,"Asia/Yekaterinburg",true); ?>>(GMT/UTC + 05:00) Yekaterinburg</option>
								        <option value="Asia/Yerevan" <?php selected($store_location_timezone,"Asia/Yerevan",true); ?>>(GMT/UTC + 04:00) Yerevan</option>
								    </optgroup>
								    <optgroup label="Atlantic">
								        <option value="Atlantic/Azores" <?php selected($store_location_timezone,"Atlantic/Azores",true); ?>>(GMT/UTC - 01:00) Azores</option>
								        <option value="Atlantic/Bermuda" <?php selected($store_location_timezone,"Atlantic/Bermuda",true); ?>>(GMT/UTC - 04:00) Bermuda</option>
								        <option value="Atlantic/Canary" <?php selected($store_location_timezone,"Atlantic/Canary",true); ?>>(GMT/UTC + 00:00) Canary</option>
								        <option value="Atlantic/Cape_Verde" <?php selected($store_location_timezone,"Atlantic/Cape_Verde",true); ?>>(GMT/UTC - 01:00) Cape Verde</option>
								        <option value="Atlantic/Faroe" <?php selected($store_location_timezone,"Atlantic/Faroe",true); ?>>(GMT/UTC + 00:00) Faroe</option>
								        <option value="Atlantic/Madeira" <?php selected($store_location_timezone,"Atlantic/Madeira",true); ?>>(GMT/UTC + 00:00) Madeira</option>
								        <option value="Atlantic/Reykjavik" <?php selected($store_location_timezone,"Atlantic/Reykjavik",true); ?>>(GMT/UTC + 00:00) Reykjavik</option>
								        <option value="Atlantic/South_Georgia" <?php selected($store_location_timezone,"Atlantic/South_Georgia",true); ?>>(GMT/UTC - 02:00) South Georgia</option>
								        <option value="Atlantic/St_Helena" <?php selected($store_location_timezone,"Atlantic/St_Helena",true); ?>>(GMT/UTC + 00:00) St. Helena</option>
								        <option value="Atlantic/Stanley" <?php selected($store_location_timezone,"Atlantic/Stanley",true); ?>>(GMT/UTC - 03:00) Stanley</option>
								    </optgroup>
								    <optgroup label="Australia">
								        <option value="Australia/Adelaide" <?php selected($store_location_timezone,"Australia/Adelaide",true); ?>>(GMT/UTC + 10:30) Adelaide</option>
								        <option value="Australia/Brisbane" <?php selected($store_location_timezone,"Australia/Brisbane",true); ?>>(GMT/UTC + 10:00) Brisbane</option>
								        <option value="Australia/Broken_Hill" <?php selected($store_location_timezone,"Australia/Broken_Hill",true); ?>>(GMT/UTC + 10:30) Broken Hill</option>
								        <option value="Australia/Currie" <?php selected($store_location_timezone,"Australia/Currie",true); ?>>(GMT/UTC + 11:00) Currie</option>
								        <option value="Australia/Darwin" <?php selected($store_location_timezone,"Australia/Darwin",true); ?>>(GMT/UTC + 09:30) Darwin</option>
								        <option value="Australia/Eucla" <?php selected($store_location_timezone,"Australia/Eucla",true); ?>>(GMT/UTC + 08:45) Eucla</option>
								        <option value="Australia/Hobart" <?php selected($store_location_timezone,"Australia/Hobart",true); ?>>(GMT/UTC + 11:00) Hobart</option>
								        <option value="Australia/Lindeman" <?php selected($store_location_timezone,"Australia/Lindeman",true); ?>>(GMT/UTC + 10:00) Lindeman</option>
								        <option value="Australia/Lord_Howe" <?php selected($store_location_timezone,"Australia/Lord_Howe",true); ?>>(GMT/UTC + 11:00) Lord Howe</option>
								        <option value="Australia/Melbourne" <?php selected($store_location_timezone,"Australia/Melbourne",true); ?>>(GMT/UTC + 11:00) Melbourne</option>
								        <option value="Australia/Perth" <?php selected($store_location_timezone,"Australia/Perth",true); ?>>(GMT/UTC + 08:00) Perth</option>
								        <option value="Australia/Sydney" <?php selected($store_location_timezone,"Australia/Sydney",true); ?>>(GMT/UTC + 11:00) Sydney</option>
								    </optgroup>
								    <optgroup label="Europe">
								        <option value="Europe/Amsterdam" <?php selected($store_location_timezone,"Europe/Amsterdam",true); ?>>(GMT/UTC + 01:00) Amsterdam</option>
								        <option value="Europe/Andorra" <?php selected($store_location_timezone,"Europe/Andorra",true); ?>>(GMT/UTC + 01:00) Andorra</option>
								        <option value="Europe/Astrakhan" <?php selected($store_location_timezone,"Europe/Astrakhan",true); ?>>(GMT/UTC + 04:00) Astrakhan</option>
								        <option value="Europe/Athens" <?php selected($store_location_timezone,"Europe/Athens",true); ?>>(GMT/UTC + 02:00) Athens</option>
								        <option value="Europe/Belgrade" <?php selected($store_location_timezone,"Europe/Belgrade",true); ?>>(GMT/UTC + 01:00) Belgrade</option>
								        <option value="Europe/Berlin" <?php selected($store_location_timezone,"Europe/Berlin",true); ?>>(GMT/UTC + 01:00) Berlin</option>
								        <option value="Europe/Bratislava" <?php selected($store_location_timezone,"Europe/Bratislava",true); ?>>(GMT/UTC + 01:00) Bratislava</option>
								        <option value="Europe/Brussels" <?php selected($store_location_timezone,"Europe/Brussels",true); ?>>(GMT/UTC + 01:00) Brussels</option>
								        <option value="Europe/Bucharest" <?php selected($store_location_timezone,"Europe/Bucharest",true); ?>>(GMT/UTC + 02:00) Bucharest</option>
								        <option value="Europe/Budapest" <?php selected($store_location_timezone,"Europe/Budapest",true); ?>>(GMT/UTC + 01:00) Budapest</option>
								        <option value="Europe/Busingen" <?php selected($store_location_timezone,"Europe/Busingen",true); ?>>(GMT/UTC + 01:00) Busingen</option>
								        <option value="Europe/Chisinau" <?php selected($store_location_timezone,"Europe/Chisinau",true); ?>>(GMT/UTC + 02:00) Chisinau</option>
								        <option value="Europe/Copenhagen" <?php selected($store_location_timezone,"Europe/Copenhagen",true); ?>>(GMT/UTC + 01:00) Copenhagen</option>
								        <option value="Europe/Dublin" <?php selected($store_location_timezone,"Europe/Dublin",true); ?>>(GMT/UTC + 00:00) Dublin</option>
								        <option value="Europe/Gibraltar" <?php selected($store_location_timezone,"Europe/Gibraltar",true); ?>>(GMT/UTC + 01:00) Gibraltar</option>
								        <option value="Europe/Guernsey" <?php selected($store_location_timezone,"Europe/Guernsey",true); ?>>(GMT/UTC + 00:00) Guernsey</option>
								        <option value="Europe/Helsinki" <?php selected($store_location_timezone,"Europe/Helsinki",true); ?>>(GMT/UTC + 02:00) Helsinki</option>
								        <option value="Europe/Isle_of_Man" <?php selected($store_location_timezone,"Europe/Isle_of_Man",true); ?>>(GMT/UTC + 00:00) Isle of Man</option>
								        <option value="Europe/Istanbul" <?php selected($store_location_timezone,"Europe/Istanbul",true); ?>>(GMT/UTC + 02:00) Istanbul</option>
								        <option value="Europe/Jersey" <?php selected($store_location_timezone,"Europe/Jersey",true); ?>>(GMT/UTC + 00:00) Jersey</option>
								        <option value="Europe/Kaliningrad" <?php selected($store_location_timezone,"Europe/Kaliningrad",true); ?>>(GMT/UTC + 02:00) Kaliningrad</option>
								        <option value="Europe/Kiev" <?php selected($store_location_timezone,"Europe/Kiev",true); ?>>(GMT/UTC + 02:00) Kiev</option>
								        <option value="Europe/Lisbon" <?php selected($store_location_timezone,"Europe/Lisbon",true); ?>>(GMT/UTC + 00:00) Lisbon</option>
								        <option value="Europe/Ljubljana" <?php selected($store_location_timezone,"Europe/Ljubljana",true); ?>>(GMT/UTC + 01:00) Ljubljana</option>
								        <option value="Europe/London" <?php selected($store_location_timezone,"Europe/London",true); ?>>(GMT/UTC + 01:00) London</option>
								        <option value="Europe/Luxembourg" <?php selected($store_location_timezone,"Europe/Luxembourg",true); ?>>(GMT/UTC + 01:00) Luxembourg</option>
								        <option value="Europe/Madrid" <?php selected($store_location_timezone,"Europe/Madrid",true); ?>>(GMT/UTC + 01:00) Madrid</option>
								        <option value="Europe/Malta" <?php selected($store_location_timezone,"Europe/Malta",true); ?>>(GMT/UTC + 01:00) Malta</option>
								        <option value="Europe/Mariehamn" <?php selected($store_location_timezone,"Europe/Mariehamn",true); ?>>(GMT/UTC + 02:00) Mariehamn</option>
								        <option value="Europe/Minsk" <?php selected($store_location_timezone,"Europe/Minsk",true); ?>>(GMT/UTC + 03:00) Minsk</option>
								        <option value="Europe/Monaco" <?php selected($store_location_timezone,"Europe/Monaco",true); ?>>(GMT/UTC + 01:00) Monaco</option>
								        <option value="Europe/Moscow" <?php selected($store_location_timezone,"Europe/Moscow",true); ?>>(GMT/UTC + 03:00) Moscow</option>
								        <option value="Europe/Oslo" <?php selected($store_location_timezone,"Europe/Oslo",true); ?>>(GMT/UTC + 01:00) Oslo</option>
								        <option value="Europe/Paris" <?php selected($store_location_timezone,"Europe/Paris",true); ?>>(GMT/UTC + 01:00) Paris</option>
								        <option value="Europe/Podgorica" <?php selected($store_location_timezone,"Europe/Podgorica",true); ?>>(GMT/UTC + 01:00) Podgorica</option>
								        <option value="Europe/Prague" <?php selected($store_location_timezone,"Europe/Prague",true); ?>>(GMT/UTC + 01:00) Prague</option>
								        <option value="Europe/Riga" <?php selected($store_location_timezone,"Europe/Riga",true); ?>>(GMT/UTC + 02:00) Riga</option>
								        <option value="Europe/Rome" <?php selected($store_location_timezone,"Europe/Rome",true); ?>>(GMT/UTC + 01:00) Rome</option>
								        <option value="Europe/Samara" <?php selected($store_location_timezone,"Europe/Samara",true); ?>>(GMT/UTC + 04:00) Samara</option>
								        <option value="Europe/San_Marino" <?php selected($store_location_timezone,"Europe/San_Marino",true); ?>>(GMT/UTC + 01:00) San Marino</option>
								        <option value="Europe/Sarajevo" <?php selected($store_location_timezone,"Europe/Sarajevo",true); ?>>(GMT/UTC + 01:00) Sarajevo</option>
								        <option value="Europe/Simferopol" <?php selected($store_location_timezone,"Europe/Simferopol",true); ?>>(GMT/UTC + 03:00) Simferopol</option>
								        <option value="Europe/Skopje" <?php selected($store_location_timezone,"Europe/Skopje",true); ?>>(GMT/UTC + 01:00) Skopje</option>
								        <option value="Europe/Sofia" <?php selected($store_location_timezone,"Europe/Sofia",true); ?>>(GMT/UTC + 02:00) Sofia</option>
								        <option value="Europe/Stockholm" <?php selected($store_location_timezone,"Europe/Stockholm",true); ?>>(GMT/UTC + 01:00) Stockholm</option>
								        <option value="Europe/Tallinn" <?php selected($store_location_timezone,"Europe/Tallinn",true); ?>>(GMT/UTC + 02:00) Tallinn</option>
								        <option value="Europe/Tirane" <?php selected($store_location_timezone,"Europe/Tirane",true); ?>>(GMT/UTC + 01:00) Tirane</option>
								        <option value="Europe/Ulyanovsk" <?php selected($store_location_timezone,"Europe/Ulyanovsk",true); ?>>(GMT/UTC + 04:00) Ulyanovsk</option>
								        <option value="Europe/Uzhgorod" <?php selected($store_location_timezone,"Europe/Uzhgorod",true); ?>>(GMT/UTC + 02:00) Uzhgorod</option>
								        <option value="Europe/Vaduz" <?php selected($store_location_timezone,"Europe/Vaduz",true); ?>>(GMT/UTC + 01:00) Vaduz</option>
								        <option value="Europe/Vatican" <?php selected($store_location_timezone,"Europe/Vatican",true); ?>>(GMT/UTC + 01:00) Vatican</option>
								        <option value="Europe/Vienna" <?php selected($store_location_timezone,"Europe/Vienna",true); ?>>(GMT/UTC + 01:00) Vienna</option>
								        <option value="Europe/Vilnius" <?php selected($store_location_timezone,"Europe/Vilnius",true); ?>>(GMT/UTC + 02:00) Vilnius</option>
								        <option value="Europe/Volgograd" <?php selected($store_location_timezone,"Europe/Volgograd",true); ?>>(GMT/UTC + 03:00) Volgograd</option>
								        <option value="Europe/Warsaw" <?php selected($store_location_timezone,"Europe/Warsaw",true); ?>>(GMT/UTC + 01:00) Warsaw</option>
								        <option value="Europe/Zagreb" <?php selected($store_location_timezone,"Europe/Zagreb",true); ?>>(GMT/UTC + 01:00) Zagreb</option>
								        <option value="Europe/Zaporozhye" <?php selected($store_location_timezone,"Europe/Zaporozhye",true); ?>>(GMT/UTC + 02:00) Zaporozhye</option>
								        <option value="Europe/Zurich" <?php selected($store_location_timezone,"Europe/Zurich",true); ?>>(GMT/UTC + 01:00) Zurich</option>
								    </optgroup>
								    <optgroup label="Indian">
								        <option value="Indian/Antananarivo" <?php selected($store_location_timezone,"Indian/Antananarivo",true); ?>>(GMT/UTC + 03:00) Antananarivo</option>
								        <option value="Indian/Chagos" <?php selected($store_location_timezone,"Indian/Chagos",true); ?>>(GMT/UTC + 06:00) Chagos</option>
								        <option value="Indian/Christmas" <?php selected($store_location_timezone,"Indian/Christmas",true); ?>>(GMT/UTC + 07:00) Christmas</option>
								        <option value="Indian/Cocos" <?php selected($store_location_timezone,"Indian/Cocos",true); ?>>(GMT/UTC + 06:30) Cocos</option>
								        <option value="Indian/Comoro" <?php selected($store_location_timezone,"Indian/Comoro",true); ?>>(GMT/UTC + 03:00) Comoro</option>
								        <option value="Indian/Kerguelen" <?php selected($store_location_timezone,"Indian/Kerguelen",true); ?>>(GMT/UTC + 05:00) Kerguelen</option>
								        <option value="Indian/Mahe" <?php selected($store_location_timezone,"Indian/Mahe",true); ?>>(GMT/UTC + 04:00) Mahe</option>
								        <option value="Indian/Maldives" <?php selected($store_location_timezone,"Indian/Maldives",true); ?>>(GMT/UTC + 05:00) Maldives</option>
								        <option value="Indian/Mauritius" <?php selected($store_location_timezone,"Indian/Mauritius",true); ?>>(GMT/UTC + 04:00) Mauritius</option>
								        <option value="Indian/Mayotte" <?php selected($store_location_timezone,"Indian/Mayotte",true); ?>>(GMT/UTC + 03:00) Mayotte</option>
								        <option value="Indian/Reunion" <?php selected($store_location_timezone,"Indian/Reunion",true); ?>>(GMT/UTC + 04:00) Reunion</option>
								    </optgroup>
								    <optgroup label="Pacific">
								        <option value="Pacific/Apia" <?php selected($store_location_timezone,"Pacific/Apia",true); ?>>(GMT/UTC + 14:00) Apia</option>
								        <option value="Pacific/Auckland" <?php selected($store_location_timezone,"Pacific/Auckland",true); ?>>(GMT/UTC + 13:00) Auckland</option>
								        <option value="Pacific/Bougainville" <?php selected($store_location_timezone,"Pacific/Bougainville",true); ?>>(GMT/UTC + 11:00) Bougainville</option>
								        <option value="Pacific/Chatham" <?php selected($store_location_timezone,"Pacific/Chatham",true); ?>>(GMT/UTC + 13:45) Chatham</option>
								        <option value="Pacific/Chuuk" <?php selected($store_location_timezone,"Pacific/Chuuk",true); ?>>(GMT/UTC + 10:00) Chuuk</option>
								        <option value="Pacific/Easter" <?php selected($store_location_timezone,"Pacific/Easter",true); ?>>(GMT/UTC - 05:00) Easter</option>
								        <option value="Pacific/Efate" <?php selected($store_location_timezone,"Pacific/Efate",true); ?>>(GMT/UTC + 11:00) Efate</option>
								        <option value="Pacific/Enderbury" <?php selected($store_location_timezone,"Pacific/Enderbury",true); ?>>(GMT/UTC + 13:00) Enderbury</option>
								        <option value="Pacific/Fakaofo" <?php selected($store_location_timezone,"Pacific/Fakaofo",true); ?>>(GMT/UTC + 13:00) Fakaofo</option>
								        <option value="Pacific/Fiji" <?php selected($store_location_timezone,"Pacific/Fiji",true); ?>>(GMT/UTC + 12:00) Fiji</option>
								        <option value="Pacific/Funafuti" <?php selected($store_location_timezone,"Pacific/Funafuti",true); ?>>(GMT/UTC + 12:00) Funafuti</option>
								        <option value="Pacific/Galapagos" <?php selected($store_location_timezone,"Pacific/Galapagos",true); ?>>(GMT/UTC - 06:00) Galapagos</option>
								        <option value="Pacific/Gambier" <?php selected($store_location_timezone,"Pacific/Gambier",true); ?>>(GMT/UTC - 09:00) Gambier</option>
								        <option value="Pacific/Guadalcanal" <?php selected($store_location_timezone,"Pacific/Guadalcanal",true); ?>>(GMT/UTC + 11:00) Guadalcanal</option>
								        <option value="Pacific/Guam" <?php selected($store_location_timezone,"Pacific/Guam",true); ?>>(GMT/UTC + 10:00) Guam</option>
								        <option value="Pacific/Honolulu" <?php selected($store_location_timezone,"Pacific/Honolulu",true); ?>>(GMT/UTC - 10:00) Honolulu</option>
								        <option value="Pacific/Johnston" <?php selected($store_location_timezone,"Pacific/Johnston",true); ?>>(GMT/UTC - 10:00) Johnston</option>
								        <option value="Pacific/Kiritimati" <?php selected($store_location_timezone,"Pacific/Kiritimati",true); ?>>(GMT/UTC + 14:00) Kiritimati</option>
								        <option value="Pacific/Kosrae" <?php selected($store_location_timezone,"Pacific/Kosrae",true); ?>>(GMT/UTC + 11:00) Kosrae</option>
								        <option value="Pacific/Kwajalein" <?php selected($store_location_timezone,"Pacific/Kwajalein",true); ?>>(GMT/UTC + 12:00) Kwajalein</option>
								        <option value="Pacific/Majuro" <?php selected($store_location_timezone,"Pacific/Majuro",true); ?>>(GMT/UTC + 12:00) Majuro</option>
								        <option value="Pacific/Marquesas" <?php selected($store_location_timezone,"Pacific/Marquesas",true); ?>>(GMT/UTC - 09:30) Marquesas</option>
								        <option value="Pacific/Midway" <?php selected($store_location_timezone,"Pacific/Midway",true); ?>>(GMT/UTC - 11:00) Midway</option>
								        <option value="Pacific/Nauru" <?php selected($store_location_timezone,"Pacific/Nauru",true); ?>>(GMT/UTC + 12:00) Nauru</option>
								        <option value="Pacific/Niue" <?php selected($store_location_timezone,"Pacific/Niue",true); ?>>(GMT/UTC - 11:00) Niue</option>
								        <option value="Pacific/Norfolk" <?php selected($store_location_timezone,"Pacific/Norfolk",true); ?>>(GMT/UTC + 11:00) Norfolk</option>
								        <option value="Pacific/Noumea" <?php selected($store_location_timezone,"Pacific/Noumea",true); ?>>(GMT/UTC + 11:00) Noumea</option>
								        <option value="Pacific/Pago_Pago" <?php selected($store_location_timezone,"Pacific/Pago_Pago",true); ?>>(GMT/UTC - 11:00) Pago Pago</option>
								        <option value="Pacific/Palau" <?php selected($store_location_timezone,"Pacific/Palau",true); ?>>(GMT/UTC + 09:00) Palau</option>
								        <option value="Pacific/Pitcairn" <?php selected($store_location_timezone,"Pacific/Pitcairn",true); ?>>(GMT/UTC - 08:00) Pitcairn</option>
								        <option value="Pacific/Pohnpei" <?php selected($store_location_timezone,"Pacific/Pohnpei",true); ?>>(GMT/UTC + 11:00) Pohnpei</option>
								        <option value="Pacific/Port_Moresby" <?php selected($store_location_timezone,"Pacific/Port_Moresby",true); ?>>(GMT/UTC + 10:00) Port Moresby</option>
								        <option value="Pacific/Rarotonga" <?php selected($store_location_timezone,"Pacific/Rarotonga",true); ?>>(GMT/UTC - 10:00) Rarotonga</option>
								        <option value="Pacific/Saipan" <?php selected($store_location_timezone,"Pacific/Saipan",true); ?>>(GMT/UTC + 10:00) Saipan</option>
								        <option value="Pacific/Tahiti" <?php selected($store_location_timezone,"Pacific/Tahiti",true); ?>>(GMT/UTC - 10:00) Tahiti</option>
								        <option value="Pacific/Tarawa" <?php selected($store_location_timezone,"Pacific/Tarawa",true); ?>>(GMT/UTC + 12:00) Tarawa</option>
								        <option value="Pacific/Tongatapu" <?php selected($store_location_timezone,"Pacific/Tongatapu",true); ?>>(GMT/UTC + 13:00) Tongatapu</option>
								        <option value="Pacific/Wake" <?php selected($store_location_timezone,"Pacific/Wake",true); ?>>(GMT/UTC + 12:00) Wake</option>
								        <option value="Pacific/Wallis" <?php selected($store_location_timezone,"Pacific/Wallis",true); ?>>(GMT/UTC + 12:00) Wallis</option>
								    </optgroup>
								</select>
	                    	</div>

	                        <input class="coderockz-woo-delivery-submit-btn" type="submit" name="coderockz_delivery_timezone_form_submit" value="<?php _e('Save Changes', 'coderockz-woo-delivery'); ?>" />

	                    </form>
                	</div>

                </div>
			</div>
			<div data-tab="tab4" class="coderockz-woo-delivery-tabcontent">
				<div class="coderockz-woo-delivery-card">
					<p class="coderockz-woo-delivery-card-header"><?php _e('Order Type Settings', 'coderockz-woo-delivery'); ?></p>
					<div class="coderockz-woo-delivery-card-body">
						<p class="coderockz-woo-delivery-delivery-option-notice"><span class="dashicons dashicons-yes"></span><?php _e(' Settings Changed Successfully', 'coderockz-woo-delivery'); ?></p>
	                    <form action="" method="post" id ="coderockz_delivery_delivery_option_form_submit">
	                        <?php wp_nonce_field('coderockz_woo_delivery_nonce'); ?>

	                        <div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label" style="width:426px!important"><?php _e('Give Option to choose from Delivery or Pickup', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="Enable it if you want to give the freedom to customer whether he wants Home delivery or he picks the ordered products from a pickup location. Default is disable."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_enable_option_time_pickup">
							       <input type="checkbox" name="coderockz_enable_option_time_pickup" id="coderockz_enable_option_time_pickup" <?php echo (isset($delivery_option_settings['enable_option_time_pickup']) && !empty($delivery_option_settings['enable_option_time_pickup'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_delivery_option_label"><?php _e('Order Type Field Label', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Order Type field label. Default is Order Type."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_woo_delivery_delivery_option_label" name="coderockz_woo_delivery_delivery_option_label" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($delivery_option_settings['delivery_option_label']) && !empty($delivery_option_settings['delivery_option_label'])) ? stripslashes($delivery_option_settings['delivery_option_label']) : "" ?>" placeholder="" autocomplete="off"/>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_option_delivery_label"><?php _e('Delivery Option Label', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Order Type's Home Delivery option label. Default is Delivery."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_woo_delivery_option_delivery_label" name="coderockz_woo_delivery_option_delivery_label" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($delivery_option_settings['delivery_label']) && !empty($delivery_option_settings['delivery_label'])) ? stripslashes($delivery_option_settings['delivery_label']) : "" ?>" placeholder="" autocomplete="off"/>
	                    	</div>

	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_option_pickup_label"><?php _e('Self Pickup Option Label', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Order Type's Self Pickup option label. Default is Pickup."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_woo_delivery_option_pickup_label" name="coderockz_woo_delivery_option_pickup_label" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($delivery_option_settings['pickup_label']) && !empty($delivery_option_settings['pickup_label'])) ? stripslashes($delivery_option_settings['pickup_label']) : "" ?>" placeholder="" autocomplete="off"/>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_pre_selected_order_type"><?php _e('Pre Selected Order Type On Checkout Page', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="If you want to give preseclted value on the checkout page, specifiy it here"><span class="dashicons dashicons-editor-help"></span></p>
	                    		<select class="coderockz-woo-delivery-select-field" name="coderockz_woo_delivery_pre_selected_order_type">
	                    			<option value="" <?php if(isset($delivery_option_settings['pre_selected_order_type']) && $delivery_option_settings['pre_selected_order_type'] == ""){ echo "selected"; } ?>><?php _e('Select Pre Selected Order Type', 'coderockz-woo-delivery'); ?></option>
									<option value="delivery" <?php if(isset($delivery_option_settings['pre_selected_order_type']) && $delivery_option_settings['pre_selected_order_type'] == "delivery"){ echo "selected"; } ?>>Delivery</option>
									<option value="pickup" <?php if(isset($delivery_option_settings['pre_selected_order_type']) && $delivery_option_settings['pre_selected_order_type'] == "pickup"){ echo "selected"; } ?>>Pickup</option>
									
								</select>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_option_no_result_notice"><?php _e('No Delivery or Pickup Message', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="If a certain day has no pickup or delivery then the text is appears in the order type box. Default is No Delivery or Pickup Today."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_woo_delivery_option_no_result_notice" name="coderockz_woo_delivery_option_no_result_notice" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($delivery_option_settings['no_result_notice']) && !empty($delivery_option_settings['no_result_notice'])) ? stripslashes($delivery_option_settings['no_result_notice']) : "" ?>" placeholder="" autocomplete="off"/>
	                    	</div>

	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_maximum_delivery_pickup_per_day"><?php _e('Maximum (Delivery+Pickup) Per Day', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="If you want to limit your order per day wether it is delivery or pickup, put the quantity here. Keep blank for unlimited order."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input onkeyup="if(!Number.isInteger(Number(this.value)) || this.value < 1) this.value = null;" id="coderockz_woo_delivery_maximum_delivery_pickup_per_day" name="coderockz_woo_delivery_maximum_delivery_pickup_per_day" type="number" class="coderockz-woo-delivery-number-field" value="<?php echo (isset($delivery_option_settings['maximum_delivery_pickup_per_day']) && !empty($delivery_option_settings['maximum_delivery_pickup_per_day'])) ? stripslashes(esc_attr($delivery_option_settings['maximum_delivery_pickup_per_day'])) : ""; ?>" placeholder="" autocomplete="off"/>
	                    	</div>

	                    	<div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label"><?php _e('Dynamically Enable/Disable Delivery/Pickup Based on WooCommerce Shipping', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="Enable it if you want to see the delivery or pickup option based on your WoCommerce Shipping. Default is disable."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_woo_delivery_enable_dynamic_order_type">
							       <input type="checkbox" name="coderockz_woo_delivery_enable_dynamic_order_type" id="coderockz_woo_delivery_enable_dynamic_order_type" <?php echo (isset($delivery_option_settings['enable_dynamic_order_type']) && !empty($delivery_option_settings['enable_dynamic_order_type'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>
	                    	<div class="coderockz_woo_delivery_dynamic_order_type_notice" style="display:none">
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_dynamic_order_type_no_delivery"><?php _e('No Delivery Notice For Given Address', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="If you want to show a notice under the order type selection box about no delivery for the address taht customer given. Default is not showing any notice."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_woo_delivery_dynamic_order_type_no_delivery" name="coderockz_woo_delivery_dynamic_order_type_no_delivery" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($delivery_option_settings['dynamic_order_type_no_delivery']) && !empty($delivery_option_settings['dynamic_order_type_no_delivery'])) ? stripslashes($delivery_option_settings['dynamic_order_type_no_delivery']) : "" ?>" placeholder="" autocomplete="off"/>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_dynamic_order_type_no_pickup"><?php _e('No Pickup Notice For Given Address', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="If you want to show a notice under the order type selection box about no pickup for the address taht customer given. Default is not showing any notice."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_woo_delivery_dynamic_order_type_no_pickup" name="coderockz_woo_delivery_dynamic_order_type_no_pickup" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($delivery_option_settings['dynamic_order_type_no_pickup']) && !empty($delivery_option_settings['dynamic_order_type_no_pickup'])) ? stripslashes($delivery_option_settings['dynamic_order_type_no_pickup']) : "" ?>" placeholder="" autocomplete="off"/>
	                    	</div>

	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_dynamic_order_type_no_delivery_pickup"><?php _e('No Delivery Or Pickup Notice For Given Address', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="If you want to show a notice under the order type selection box about no delivery or pickup for the address taht customer given. Default is not showing any notice."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_woo_delivery_dynamic_order_type_no_delivery_pickup" name="coderockz_woo_delivery_dynamic_order_type_no_delivery_pickup" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($delivery_option_settings['dynamic_order_type_no_delivery_pickup']) && !empty($delivery_option_settings['dynamic_order_type_no_delivery_pickup'])) ? stripslashes($delivery_option_settings['dynamic_order_type_no_delivery_pickup']) : "" ?>" placeholder="" autocomplete="off"/>
	                    	</div>
	                    	</div>

	                        <input class="coderockz-woo-delivery-submit-btn" type="submit" name="coderockz_delivery_delivery_option_form_submit" value="<?php _e('Save Changes', 'coderockz-woo-delivery'); ?>" />

	                    </form>
                	</div>

                </div>
                <div class="coderockz-woo-delivery-card">
					<p class="coderockz-woo-delivery-card-header"><?php _e('Delivery Restriction', 'coderockz-woo-delivery'); ?></p>
					<div class="coderockz-woo-delivery-card-body">
						<p class="coderockz-woo-delivery-delivery-restriction-notice"><span class="dashicons dashicons-yes"></span><?php _e(' Settings Changed Successfully', 'coderockz-woo-delivery'); ?></p>
						<p class="coderockz-woo-delivery-timezone-tab-warning"><span class="dashicons dashicons-megaphone"></span><?php _e('Only working if Order Type is Enabled', 'coderockz-woo-delivery'); ?></p>
	                    <form action="" method="post" id ="coderockz_delivery_delivery_restriction_form_submit">
	                        <?php wp_nonce_field('coderockz_woo_delivery_nonce'); ?>

	                        <div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label"  style = "width: 365px!important;"><?php _e('Enable Delivery Restriction', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="Enable it if you want to restrict the delivery option until a certain amount is not in the cart. Default is disable."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_woo_delivery_enable_delivery_restriction">
							       <input type="checkbox" name="coderockz_woo_delivery_enable_delivery_restriction" id="coderockz_woo_delivery_enable_delivery_restriction" <?php echo (isset($delivery_option_settings['enable_delivery_restriction']) && !empty($delivery_option_settings['enable_delivery_restriction'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>

	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label style = "width: 365px!important;" class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_minimum_amount_cart_restriction"><?php _e('Minimum Amount in Cart to Show Delivey Option', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Need minimum amout in the cart to show Delivery option the checkout page or Order type dropdown. Default is no restriction."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input style="width:100px!important;" id="coderockz_woo_delivery_minimum_amount_cart_restriction" name="coderockz_woo_delivery_minimum_amount_cart_restriction" type="text" onkeyup="if(isNaN(parseFloat(Number(this.value))) || isNaN(parseInt(Number(this.value), 10)) || this.value < 1) this.value = null;" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($delivery_option_settings['minimum_amount_cart_restriction']) && !empty($delivery_option_settings['minimum_amount_cart_restriction'])) ? esc_attr($delivery_option_settings['minimum_amount_cart_restriction']) : ""; ?>" placeholder="" autocomplete="off"/><span class="coderockz-woo-delivery-currency-code"><?php echo $currency_code; ?></span>
	                    	</div>

	                    	<div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label" style = "width: 365px!important;"><?php _e('Order Total Calculating Including Discount', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="Enable it if you want to calculate the minimum cart amount including discount. Default is disable."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_woo_delivery_calculating_include_discount">
							       <input type="checkbox" name="coderockz_woo_delivery_calculating_include_discount" id="coderockz_woo_delivery_calculating_include_discount" <?php echo (isset($delivery_option_settings['calculating_include_discount']) && !empty($delivery_option_settings['calculating_include_discount'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>

	                    	<div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label" style = "width: 365px!important;"><?php _e('Order Total Calculating Including Tax', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="Enable it if you want to calculate the minimum cart amount including tax. Default is disable."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_woo_delivery_calculating_include_tax">
							       <input type="checkbox" name="coderockz_woo_delivery_calculating_include_tax" id="coderockz_woo_delivery_calculating_include_tax" <?php echo (isset($delivery_option_settings['calculating_include_tax']) && !empty($delivery_option_settings['calculating_include_tax'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>

	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label style = "width: 365px!important;" class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_delivery_restriction_notice"><?php _e('Minimum Cart Amount Notice', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="If the cart total not reach the certain amount then the notice appears in top. Default is Your cart amount must have 'X' to get the Delivery Option"><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_woo_delivery_delivery_restriction_notice" name="coderockz_woo_delivery_delivery_restriction_notice" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($delivery_option_settings['delivery_restriction_notice']) && !empty($delivery_option_settings['delivery_restriction_notice'])) ? $delivery_option_settings['delivery_restriction_notice'] : "" ?>" placeholder="" autocomplete="off"/>
	                    	</div>

	                        <input class="coderockz-woo-delivery-submit-btn" type="submit" name="coderockz_delivery_delivery_restriction_form_submit" value="<?php _e('Save Changes', 'coderockz-woo-delivery'); ?>" />

	                    </form>
                	</div>

                </div>

                <div class="coderockz-woo-delivery-card">
					<p class="coderockz-woo-delivery-card-header"><?php _e('Pickup Restriction', 'coderockz-woo-delivery'); ?></p>
					<div class="coderockz-woo-delivery-card-body">
						<p class="coderockz-woo-delivery-pickup-restriction-notice"><span class="dashicons dashicons-yes"></span><?php _e(' Settings Changed Successfully', 'coderockz-woo-delivery'); ?></p>
						<p class="coderockz-woo-delivery-timezone-tab-warning"><span class="dashicons dashicons-megaphone"></span><?php _e('Only working if Order Type is Enabled', 'coderockz-woo-delivery'); ?></p>
						
	                    <form action="" method="post" id ="coderockz_delivery_pickup_restriction_form_submit">
	                        <?php wp_nonce_field('coderockz_woo_delivery_nonce'); ?>

	                        <div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label"  style = "width: 365px!important;"><?php _e('Enable Pickup Restriction', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="Enable it if you want to restrict the pickup option until a certain amount is not in the cart. Default is disable."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_woo_delivery_enable_pickup_restriction">
							       <input type="checkbox" name="coderockz_woo_delivery_enable_pickup_restriction" id="coderockz_woo_delivery_enable_pickup_restriction" <?php echo (isset($delivery_option_settings['enable_pickup_restriction']) && !empty($delivery_option_settings['enable_pickup_restriction'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>

	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label style = "width: 365px!important;" class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_minimum_amount_cart_restriction_pickup"><?php _e('Minimum Amount in Cart to Show Pickup Option', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Need minimum amout in the cart to show Pickup option the checkout page or Order type dropdown. Default is no restriction."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input style="width:100px!important;" id="coderockz_woo_delivery_minimum_amount_cart_restriction_pickup" name="coderockz_woo_delivery_minimum_amount_cart_restriction_pickup" type="text" onkeyup="if(isNaN(parseFloat(Number(this.value))) || isNaN(parseInt(Number(this.value), 10)) || this.value < 1) this.value = null;" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($delivery_option_settings['minimum_amount_cart_restriction_pickup']) && !empty($delivery_option_settings['minimum_amount_cart_restriction_pickup'])) ? esc_attr($delivery_option_settings['minimum_amount_cart_restriction_pickup']) : ""; ?>" placeholder="" autocomplete="off"/><span class="coderockz-woo-delivery-currency-code"><?php echo $currency_code; ?></span>
	                    	</div>

	                    	<div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label" style = "width: 365px!important;"><?php _e('Order Total Calculating Including Discount', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="Enable it if you want to calculate the minimum cart amount including discount. Default is disable."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_woo_delivery_calculating_include_discount_pickup">
							       <input type="checkbox" name="coderockz_woo_delivery_calculating_include_discount_pickup" id="coderockz_woo_delivery_calculating_include_discount_pickup" <?php echo (isset($delivery_option_settings['calculating_include_discount_pickup']) && !empty($delivery_option_settings['calculating_include_discount_pickup'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>

	                    	<div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label" style = "width: 365px!important;"><?php _e('Order Total Calculating Including Tax', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="Enable it if you want to calculate the minimum cart amount including tax. Default is disable."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_woo_delivery_calculating_include_tax_pickup">
							       <input type="checkbox" name="coderockz_woo_delivery_calculating_include_tax_pickup" id="coderockz_woo_delivery_calculating_include_tax_pickup" <?php echo (isset($delivery_option_settings['calculating_include_tax_pickup']) && !empty($delivery_option_settings['calculating_include_tax_pickup'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>

	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label style = "width: 365px!important;" class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_pickup_restriction_notice"><?php _e('Minimum Cart Amount Notice', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="If the cart total not reach the certain amount then the notice appears in top. Default is Your cart amount must have 'X' to get the Pickup Option"><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_woo_delivery_pickup_restriction_notice" name="coderockz_woo_delivery_pickup_restriction_notice" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($delivery_option_settings['pickup_restriction_notice']) && !empty($delivery_option_settings['pickup_restriction_notice'])) ? $delivery_option_settings['pickup_restriction_notice'] : "" ?>" placeholder="" autocomplete="off"/>
	                    	</div>

	                        <input class="coderockz-woo-delivery-submit-btn" type="submit" name="coderockz_delivery_pickup_restriction_form_submit" value="<?php _e('Save Changes', 'coderockz-woo-delivery'); ?>" />

	                    </form>
                	</div>

                </div>

                <div class="coderockz-woo-delivery-card">
					<p class="coderockz-woo-delivery-card-header"><?php _e('Category/Product Wise Delivery Restriction', 'coderockz-woo-delivery'); ?></p>
					<div class="coderockz-woo-delivery-card-body">
						<p class="coderockz-woo-delivery-timezone-tab-warning"><span class="dashicons dashicons-megaphone"></span><?php _e('Only working if Order Type is Enabled', 'coderockz-woo-delivery'); ?></p>
						<?php if(get_option('coderockz_woo_delivery_large_product_list') !== false) { ?>
							<p class="coderockz-woo-delivery-timezone-tab-warning"><span class="dashicons dashicons-megaphone"></span><?php _e('In Hide Delivery For Individual Product option, if product is simple input the product ID and if product is variable input the variation ID', 'coderockz-woo-delivery'); ?></p>
						<?php } ?>
						<p class="coderockz-woo-delivery-category-product-delivery-restriction-settings-notice"></p>
	                    <form action="" method="post" id ="coderockz_woo_delivery_category_product_delivery_restriction_settings_form_submit">
	                        <?php wp_nonce_field('coderockz_woo_delivery_nonce'); ?>
	                        
	                        <div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Hide Delivery For Product Categories', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Select the product categories for which you don't want to give the facility of delivery. So only pickup option is showing in the checkout page."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<select id="coderockz_woo_delivery_restrict_delivery_categories" name="coderockz_woo_delivery_restrict_delivery_categories[]" class="coderockz_woo_delivery_restrict_delivery_categories" multiple>
                                
                                <?php

                                $restrict_delivery_categories = [];

								if(isset($delivery_option_settings['restrict_delivery_categories']) && !empty($delivery_option_settings['restrict_delivery_categories'])) {
									foreach ($delivery_option_settings['restrict_delivery_categories'] as $hide_cat) {
										$restrict_delivery_categories[] = stripslashes($hide_cat);
									}
								}
                                foreach ($all_categories as $cat) {

                                	$selected = isset($delivery_option_settings['restrict_delivery_categories']) && !empty($delivery_option_settings['restrict_delivery_categories']) && in_array(htmlspecialchars_decode($cat->name),$restrict_delivery_categories) ? "selected" : "";
                                    echo '<option value="'.$cat->name.'" '.$selected.'>'.$cat->name.'</option>';
                                }
                                ?>
                                </select>
	                    	</div>
	                    	<?php if(get_option('coderockz_woo_delivery_large_product_list') == false) { ?>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Hide Delivery For Individual Product', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Select the product for which you don't want to give the facility of delivery. So only pickup option is showing in the checkout page."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<select id="coderockz_woo_delivery_restrict_delivery_individual_product" name="coderockz_woo_delivery_restrict_delivery_individual_product[]" class="coderockz_woo_delivery_restrict_delivery_individual_product" multiple>
                                
                                <?php
                                foreach ($store_products as $key=>$value) {

                                	$selected = isset($delivery_option_settings['restrict_delivery_products']) && !empty($delivery_option_settings['restrict_delivery_products']) && in_array($key,$delivery_option_settings['restrict_delivery_products']) ? "selected" : "";
                                    echo '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
                                }
                                ?>
                                </select>
	                    	</div>
	                    	<?php } else { ?>

                    		<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Hide Delivery For Individual Product', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Select the product for which you don't want to give the facility of delivery. So only pickup option is showing in the checkout page."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<?php 
	                        	$restrict_delivery_individual_product = isset($delivery_option_settings['restrict_delivery_products']) && !empty($delivery_option_settings['restrict_delivery_products']) ? $delivery_option_settings['restrict_delivery_products'] : array();
	                        	$restrict_delivery_individual_product = implode(",",$restrict_delivery_individual_product);
	                        	?>
	                    		<input id="coderockz_woo_delivery_restrict_delivery_individual_product_input" name="coderockz_woo_delivery_restrict_delivery_individual_product_input" type="text" class="coderockz_woo_delivery_restrict_delivery_individual_product_input coderockz-woo-delivery-input-field" value="<?php echo $restrict_delivery_individual_product; ?>" placeholder="<?php _e('Comma(,) separated Product/Variation ID', 'coderockz-woo-delivery'); ?>" autocomplete="off"/>
	                    	</div>

	                    	<?php } ?>
	                        <div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label"><?php _e('Show Also If Cart Has Other Categories Or Products'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="If there is delivery restriction category's products or delivery restriction products in the cart then whatever there are other category's products or other products, the Delivery option is hidden. Enable it if you want to reverse it."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_delivery_restrict_delivery_reverse_current_condition">
							       <input type="checkbox" name="coderockz_delivery_restrict_delivery_reverse_current_condition" id="coderockz_delivery_restrict_delivery_reverse_current_condition" <?php echo (isset($delivery_option_settings['restrict_delivery_reverse_current_condition']) && !empty($delivery_option_settings['restrict_delivery_reverse_current_condition'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>

	                        <input class="coderockz-woo-delivery-submit-btn" type="submit" name="coderockz_woo_delivery_category_product_delivery_restriction_settings_form_submit" value="<?php _e('Save Changes', 'coderockz-woo-delivery'); ?>" />
	                    </form>
                	</div>

                </div>


                <div class="coderockz-woo-delivery-card">
					<p class="coderockz-woo-delivery-card-header"><?php _e('Category/Product Wise Pickup Restriction', 'coderockz-woo-delivery'); ?></p>
					<div class="coderockz-woo-delivery-card-body">
						<p class="coderockz-woo-delivery-timezone-tab-warning"><span class="dashicons dashicons-megaphone"></span><?php _e('Only working if Order Type is Enabled', 'coderockz-woo-delivery'); ?></p>
						<?php if(get_option('coderockz_woo_delivery_large_product_list') !== false) { ?>
							<p class="coderockz-woo-delivery-timezone-tab-warning"><span class="dashicons dashicons-megaphone"></span><?php _e('In Hide Delivery For Individual Product option, if product is simple input the product ID and if product is variable input the variation ID', 'coderockz-woo-delivery'); ?></p>
						<?php } ?>
						<p class="coderockz-woo-delivery-category-product-pickup-restriction-settings-notice"></p>
	                    <form action="" method="post" id ="coderockz_woo_delivery_category_product_pickup_restriction_settings_form_submit">
	                        <?php wp_nonce_field('coderockz_woo_delivery_nonce'); ?>
	                        
	                        <div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Hide Pickup For Product Categories', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Select the product categories for which you don't want to give the facility of pickup. So only delivery option is showing in the checkout page."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<select id="coderockz_woo_delivery_restrict_pickup_categories" name="coderockz_woo_delivery_restrict_pickup_categories[]" class="coderockz_woo_delivery_restrict_pickup_categories" multiple>
                                
                                <?php

                                $restrict_pickup_categories = [];
								if(isset($delivery_option_settings['restrict_pickup_categories']) && !empty($delivery_option_settings['restrict_pickup_categories'])) {
									foreach ($delivery_option_settings['restrict_pickup_categories'] as $hide_cat) {
										$restrict_pickup_categories[] = stripslashes($hide_cat);
									}
								}
                                foreach ($all_categories as $cat) {

                                	$selected = isset($delivery_option_settings['restrict_pickup_categories']) && !empty($delivery_option_settings['restrict_pickup_categories']) && in_array(htmlspecialchars_decode($cat->name),$restrict_pickup_categories) ? "selected" : "";
                                    echo '<option value="'.$cat->name.'" '.$selected.'>'.$cat->name.'</option>';
                                }
                                ?>
                                </select>
	                    	</div>
	                    	<?php if(get_option('coderockz_woo_delivery_large_product_list') == false) { ?>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Hide Pickup For Individual Product', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Select the product for which you don't want to give the facility of delivery. So only pickup option is showing in the checkout page."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<select id="coderockz_woo_delivery_restrict_pickup_individual_product" name="coderockz_woo_delivery_restrict_pickup_individual_product[]" class="coderockz_woo_delivery_restrict_pickup_individual_product" multiple>
                                
                                <?php
                                foreach ($store_products as $key=>$value) {

                                	$selected = isset($delivery_option_settings['restrict_pickup_products']) && !empty($delivery_option_settings['restrict_pickup_products']) && in_array($key,$delivery_option_settings['restrict_pickup_products']) ? "selected" : "";
                                    echo '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
                                }
                                ?>
                                </select>
	                    	</div>

	                    	<?php } else { ?>

                    		<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Hide Delivery For Individual Product', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Select the product for which you don't want to give the facility of delivery. So only pickup option is showing in the checkout page."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<?php 
	                        	$restrict_pickup_individual_product = isset($delivery_option_settings['restrict_pickup_products']) && !empty($delivery_option_settings['restrict_pickup_products']) ? $delivery_option_settings['restrict_pickup_products'] : array();
	                        	$restrict_pickup_individual_product = implode(",",$restrict_pickup_individual_product);
	                        	?>
	                    		<input id="coderockz_woo_delivery_restrict_pickup_individual_product_input" name="coderockz_woo_delivery_restrict_pickup_individual_product_input" type="text" class="coderockz_woo_delivery_restrict_pickup_individual_product_input coderockz-woo-delivery-input-field" value="<?php echo $restrict_pickup_individual_product; ?>" placeholder="<?php _e('Comma(,) separated Product/Variation ID', 'coderockz-woo-delivery'); ?>" autocomplete="off"/>
	                    	</div>

	                    	<?php } ?>

	                        <div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label"><?php _e('Show Also If Cart Has Other Categories Or Products'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="If there is pickup restriction category's products or pickup restriction products in the cart then whatever there are other category's products or other products, the Delivery option is hidden. Enable it if you want to reverse it."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_delivery_restrict_pickup_reverse_current_condition">
							       <input type="checkbox" name="coderockz_delivery_restrict_pickup_reverse_current_condition" id="coderockz_delivery_restrict_pickup_reverse_current_condition" <?php echo (isset($delivery_option_settings['restrict_pickup_reverse_current_condition']) && !empty($delivery_option_settings['restrict_pickup_reverse_current_condition'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>

	                        <input class="coderockz-woo-delivery-submit-btn" type="submit" name="coderockz_woo_delivery_category_product_pickup_restriction_settings_form_submit" value="<?php _e('Save Changes', 'coderockz-woo-delivery'); ?>" />
	                    </form>
                	</div>

                </div>


                <div class="coderockz-woo-delivery-card">
					<p class="coderockz-woo-delivery-card-header"><?php _e('Free Shipping Restriction', 'coderockz-woo-delivery'); ?></p>
					<div class="coderockz-woo-delivery-card-body">
						<p class="coderockz-woo-delivery-free-shipping-restriction-notice"><span class="dashicons dashicons-yes"></span><?php _e(' Settings Changed Successfully', 'coderockz-woo-delivery'); ?></p>
	                    <form action="" method="post" id ="coderockz_delivery_free_shipping_restriction_form_submit">
	                        <?php wp_nonce_field('coderockz_woo_delivery_nonce'); ?>
	                        <div style="border:1px solid #ddd;border-radius: 4px;">
	                        <div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label"  style = "width: 365px!important;"><?php _e('Enable Free Shipping Restriction', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="Enable it if you want to restrict the free shipping method until a certain amount is not in the cart. Default is disable."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_woo_delivery_enable_free_shipping_restriction">
							       <input type="checkbox" name="coderockz_woo_delivery_enable_free_shipping_restriction" id="coderockz_woo_delivery_enable_free_shipping_restriction" <?php echo (isset($delivery_option_settings['enable_free_shipping_restriction']) && !empty($delivery_option_settings['enable_free_shipping_restriction'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label style = "width: 365px!important;" class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_minimum_amount_shipping_restriction"><?php _e('Minimum Amount in Cart to Show Free Shipping', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Need minimum amout in the cart to show free shipping method in the checkout page or Order type dropdown. Default is no restriction."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input style="width:100px!important;" id="coderockz_woo_delivery_minimum_amount_shipping_restriction" name="coderockz_woo_delivery_minimum_amount_shipping_restriction" type="text" onkeyup="if(isNaN(parseFloat(Number(this.value))) || isNaN(parseInt(Number(this.value), 10)) || this.value < 1) this.value = null;" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($delivery_option_settings['minimum_amount_shipping_restriction']) && !empty($delivery_option_settings['minimum_amount_shipping_restriction'])) ? esc_attr($delivery_option_settings['minimum_amount_shipping_restriction']) : ""; ?>" placeholder="" autocomplete="off"/><span class="coderockz-woo-delivery-currency-code"><?php echo $currency_code; ?></span>
	                    	</div>

	                    	<div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label" style = "width: 365px!important;"><?php _e('Order Total Calculating Including Discount', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="Enable it if you want to calculate the minimum cart amount including discount. Default is disable."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_woo_delivery_calculating_include_discount_free_shipping">
							       <input type="checkbox" name="coderockz_woo_delivery_calculating_include_discount_pickup" id="coderockz_woo_delivery_calculating_include_discount_free_shipping" <?php echo (isset($delivery_option_settings['calculating_include_discount_free_shipping']) && !empty($delivery_option_settings['calculating_include_discount_free_shipping'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>

	                    	<div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label" style = "width: 365px!important;"><?php _e('Order Total Calculating Including Tax', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="Enable it if you want to calculate the minimum cart amount including tax. Default is disable."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_woo_delivery_calculating_include_tax_free_shipping">
							       <input type="checkbox" name="coderockz_woo_delivery_calculating_include_tax_free_shipping" id="coderockz_woo_delivery_calculating_include_tax_free_shipping" <?php echo (isset($delivery_option_settings['calculating_include_tax_free_shipping']) && !empty($delivery_option_settings['calculating_include_tax_free_shipping'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>

	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label style = "width: 365px!important;" class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_free_shipping_restriction_notice"><?php _e('Minimum Cart Amount Notice For Free Shipping', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="If the cart total not reach the certain amount then the notice appears in top. Default is Your cart amount must have 'X' to get the Free Shipping"><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_woo_delivery_free_shipping_restriction_notice" name="coderockz_woo_delivery_free_shipping_restriction_notice" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($delivery_option_settings['free_shipping_restriction_notice']) && !empty($delivery_option_settings['free_shipping_restriction_notice'])) ? $delivery_option_settings['free_shipping_restriction_notice'] : "" ?>" placeholder="" autocomplete="off"/>
	                    	</div>

	                    	</div>

	                    	<div style="border:1px solid #ddd;border-radius: 4px;margin: 10px 0;">
	                        <div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label"  style = "width: 365px!important;"><?php _e('Enable Free Shipping Only For Current Day Delivery', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="Enable it if you want to see the free shipping only for current day delivery. Default is disable."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_woo_delivery_enable_free_shipping_current_day">
							       <input type="checkbox" name="coderockz_woo_delivery_enable_free_shipping_current_day" id="coderockz_woo_delivery_enable_free_shipping_current_day" <?php echo (isset($delivery_option_settings['enable_free_shipping_current_day']) && !empty($delivery_option_settings['enable_free_shipping_current_day'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label"  style = "width: 365px!important;"><?php _e('Disable Free Shipping For Current Day Delivery', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="Enable it if you want to see the free shipping for other's day delivery that means disable free shipping for current day delivery."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_woo_delivery_disable_free_shipping_current_day">
							       <input type="checkbox" name="coderockz_woo_delivery_disable_free_shipping_current_day" id="coderockz_woo_delivery_disable_free_shipping_current_day" <?php echo (isset($delivery_option_settings['disable_free_shipping_current_day']) && !empty($delivery_option_settings['disable_free_shipping_current_day'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Hide Free Shipping For Weekdays', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="If you don't want to give your customers the free shipping facility for specific weekday, input those weekdays here."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<select id="coderockz_woo_delivery_hide_free_shipping_weekday" name="coderockz_woo_delivery_hide_free_shipping_weekday[]" class="coderockz_woo_delivery_hide_free_shipping_weekday" multiple>
                                <?php
                                $weekday = array("0"=>"Sunday", "1"=>"Monday", "2"=>"Tuesday", "3"=>"Wednesday", "4"=>"Thursday", "5"=>"Friday", "6"=>"Saturday");
	                                foreach ($weekday as $key => $value) {
	                                	$selected = isset($delivery_option_settings['hide_free_shipping_weekday']) && !empty($delivery_option_settings['hide_free_shipping_weekday']) && in_array($key,$delivery_option_settings['hide_free_shipping_weekday']) ? "selected" : "";
	                                    echo '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';		
	                                }
                                ?>
                                </select>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label"  style = "width: 365px!important;"><?php _e('Hide Other Shipping Methods If Free Shipping Available', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="Enable it if you want to hide other shipping methods when free shipping available. Default is showing all shipping methods."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_woo_delivery_enable_hide_other_shipping_method">
							       <input type="checkbox" name="coderockz_woo_delivery_enable_hide_other_shipping_method" id="coderockz_woo_delivery_enable_hide_other_shipping_method" <?php echo (isset($delivery_option_settings['enable_hide_other_shipping_method']) && !empty($delivery_option_settings['enable_hide_other_shipping_method'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>
	                    	</div>

	                        <input class="coderockz-woo-delivery-submit-btn" type="submit" name="coderockz_delivery_free_shipping_restriction_form_submit" value="<?php _e('Save Changes', 'coderockz-woo-delivery'); ?>" />

	                    </form>
                	</div>

                </div>
                <div class="coderockz-woo-delivery-card">
					<p class="coderockz-woo-delivery-card-header"><?php _e('Hide Delivery Option', 'coderockz-woo-delivery'); ?></p>
					<div class="coderockz-woo-delivery-card-body">
						<p class="coderockz-woo-delivery-disable-delivery-notice"><span class="dashicons dashicons-yes"></span><?php _e(' Settings Changed Successfully', 'coderockz-woo-delivery'); ?></p>
						<p class="coderockz-woo-delivery-timezone-tab-warning"><span class="dashicons dashicons-megaphone"></span><?php _e('If you put weekdays here, your customers are not showing the Delivery option for those weekdays. So they can\'t place a order as delivery for those weekdays. It\'s totally hidden the Delivery Option from your Order Type Dropdown for those weekdays. It\'s not as a weekend in delivery date calendar.'); ?></p>
						<p class="coderockz-woo-delivery-timezone-tab-warning"><span class="dashicons dashicons-megaphone"></span><?php _e('Only working if Order Type is Enabled', 'coderockz-woo-delivery'); ?></p>
						<p class="coderockz-woo-delivery-timezone-tab-warning"><span class="dashicons dashicons-megaphone"></span><?php _e('Be carefull: If a day is in both Disable Delivery Option & Disable Pickup Option, then no one can order that day because he can\'t select any option from order type and he can\'t place any order that day'); ?></p>
	                    <form action="" method="post" id ="coderockz_woo_delivery_disable_delivery_form_submit">
	                        <?php wp_nonce_field('coderockz_woo_delivery_nonce'); ?>

	                        <div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Hide Delivery Option From Order Type', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="If you don't want to give your customers the delivery facility for specific days, input those days here."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<select id="coderockz_woo_delivery_disable_delivery_facility_days" name="coderockz_woo_delivery_disable_delivery_facility_days[]" class="coderockz_woo_delivery_disable_delivery_facility_days" multiple>
                                <?php
                                $weekday = array("0"=>"Sunday", "1"=>"Monday", "2"=>"Tuesday", "3"=>"Wednesday", "4"=>"Thursday", "5"=>"Friday", "6"=>"Saturday");
	                                foreach ($weekday as $key => $value) {
	                                	$selected = isset($delivery_option_settings['disable_delivery_facility']) && !empty($delivery_option_settings['disable_delivery_facility']) && in_array($key,$delivery_option_settings['disable_delivery_facility']) ? "selected" : "";
	                                    echo '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';		
	                                }
                                ?>
                                </select>
	                    	</div>

	                        <input class="coderockz-woo-delivery-submit-btn" type="submit" name="coderockz_woo_delivery_disable_delivery_form_submit" value="<?php _e('Save Changes', 'coderockz-woo-delivery'); ?>" />

	                    </form>
                	</div>

                </div>
                <div class="coderockz-woo-delivery-card">
					<p class="coderockz-woo-delivery-card-header"><?php _e('Hide Pickup Option', 'coderockz-woo-delivery'); ?></p>
					<div class="coderockz-woo-delivery-card-body">
						<p class="coderockz-woo-delivery-disable-pickup-notice"><span class="dashicons dashicons-yes"></span><?php _e(' Settings Changed Successfully', 'coderockz-woo-delivery'); ?></p>
						<p class="coderockz-woo-delivery-timezone-tab-warning"><span class="dashicons dashicons-megaphone"></span><?php _e('If you put weekdays here, your customers are not showing the Pickup option for those weekdays. So they can\'t place a order as pickup for those weekdays. It\'s totally hidden the Pickup Option from your Order Type Dropdown for those weekdays. It\'s not as a weekend in pickup date calendar.'); ?></p>
						<p class="coderockz-woo-delivery-timezone-tab-warning"><span class="dashicons dashicons-megaphone"></span><?php _e('Only working if Order Type is Enabled', 'coderockz-woo-delivery'); ?></p>
						<p class="coderockz-woo-delivery-timezone-tab-warning"><span class="dashicons dashicons-megaphone"></span><?php _e('Be carefull: If a day is in both Disable Delivery Option & Disable Pickup Option, then no one can order that day because he can\'t select any option from order type and he can\'t place any order that day'); ?></p>
	                    <form action="" method="post" id ="coderockz_woo_delivery_disable_pickup_form_submit">
	                        <?php wp_nonce_field('coderockz_woo_delivery_nonce'); ?>

	                        <div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Hide Pickup Option From Order Type', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="If you don't want to give your customers the pickup facility for specific days, input those days here."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<select id="coderockz_woo_delivery_disable_pickup_facility_days" name="coderockz_woo_delivery_disable_pickup_facility_days[]" class="coderockz_woo_delivery_disable_pickup_facility_days" multiple>
                                <?php
                                $weekday = array("0"=>"Sunday", "1"=>"Monday", "2"=>"Tuesday", "3"=>"Wednesday", "4"=>"Thursday", "5"=>"Friday", "6"=>"Saturday");
	                                foreach ($weekday as $key => $value) {
	                                	$selected = isset($delivery_option_settings['disable_pickup_facility']) && !empty($delivery_option_settings['disable_pickup_facility']) && in_array($key,$delivery_option_settings['disable_pickup_facility']) ? "selected" : "";
	                                    echo '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';		
	                                }
                                ?>
                                </select>
	                    	</div>

	                        <input class="coderockz-woo-delivery-submit-btn" type="submit" name="coderockz_woo_delivery_disable_pickup_form_submit" value="<?php _e('Save Changes', 'coderockz-woo-delivery'); ?>" />

	                    </form>
                	</div>

                </div>
			</div>
			<div data-tab="tab5" class="coderockz-woo-delivery-tabcontent">
				
				<div class="coderockz-woo-delivery-card">
					<p class="coderockz-woo-delivery-card-header"><?php _e('General Delivery Date Settings', 'coderockz-woo-delivery'); ?></p>
					<div class="coderockz-woo-delivery-card-body">
						<p class="coderockz-woo-delivery-date-tab-notice"><span class="dashicons dashicons-yes"></span><?php _e(' Settings Changed Successfully', 'coderockz-woo-delivery'); ?></p>
	                    <form action="" method="post" id ="coderockz_delivery_date_form_submit">
	                        <?php wp_nonce_field('coderockz_woo_delivery_nonce'); ?>

	                    	<div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label"><?php _e('Enable Delivery Date', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="Enable Delivery Date input field in woocommerce order checkout page."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_enable_delivery_date">
							       <input type="checkbox" name="coderockz_enable_delivery_date" id="coderockz_enable_delivery_date" <?php echo (isset($date_settings['enable_delivery_date']) && !empty($date_settings['enable_delivery_date'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label"><?php _e('Make Delivery Date Field Mandatory', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="Make Delivery Date input field mandatory in woocommerce order checkout page. Default is optional."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_delivery_date_mandatory">
							       <input type="checkbox" name="coderockz_delivery_date_mandatory" id="coderockz_delivery_date_mandatory" <?php echo (isset($date_settings['delivery_date_mandatory']) && !empty($date_settings['delivery_date_mandatory'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_delivery_date_field_label"><?php _e('Date Field Heading for Delivery', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Delivery Date input field heading. Default is Delivery Date."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_delivery_date_field_label" name="coderockz_delivery_date_field_label" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($date_settings['field_label']) && !empty($date_settings['field_label'])) ? stripslashes(esc_attr($date_settings['field_label'])) : "" ?>" placeholder="" autocomplete="off"/>
	                    	</div>

	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_delivery_date_field_placeholder"><?php _e('Date Field Placeholder for Delivery', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Delivery Date input field placeholder. Default is Delivery Date."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_delivery_date_field_placeholder" name="coderockz_delivery_date_field_placeholder" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($date_settings['field_placeholder']) && !empty($date_settings['field_placeholder'])) ? stripslashes(esc_attr($date_settings['field_placeholder'])) : "" ?>" placeholder="" autocomplete="off"/>
	                    	</div>

	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_delivery_date_selectable_date"><?php _e('Allow Delivery in Next Available Days', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="User can only select the number of date from calander that is specified Here. Other dates are disabled. Only numerical value is excepted. Default is 365 days."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input onkeyup="if(!Number.isInteger(Number(this.value)) || this.value < 1) this.value = null;" id="coderockz_delivery_date_selectable_date" name="coderockz_delivery_date_selectable_date" type="number" class="coderockz-woo-delivery-number-field" value="<?php echo (isset($date_settings['selectable_date']) && !empty($date_settings['selectable_date'])) ? stripslashes(esc_attr($date_settings['selectable_date'])) : ""; ?>" placeholder="" autocomplete="off"/>
	                    	</div>

	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_delivery_date_selectable_date"><?php _e('Allow Delivery Until Date', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="User can only select a date until the date that is specified Here. Input date format YYYY-MM-DD."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_delivery_date_selectable_date_until" name="coderockz_delivery_date_selectable_date_until" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($date_settings['selectable_date_until']) && !empty($date_settings['selectable_date_until'])) ? stripslashes(esc_attr($date_settings['selectable_date_until'])) : ""; ?>" placeholder="YYYY-MM-DD" autocomplete="off"/>
	                    	</div>

	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_delivery_date_maximum_order_per_day"><?php _e('Maximum Delivery Per Day', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="If you want to limit your delivery per day, put the delivery quantity here. Keep blank for unlimited delivery."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input onkeyup="if(!Number.isInteger(Number(this.value)) || this.value < 1) this.value = null;" id="coderockz_delivery_date_maximum_order_per_day" name="coderockz_delivery_date_maximum_order_per_day" type="number" class="coderockz-woo-delivery-number-field" value="<?php echo (isset($date_settings['maximum_order_per_day']) && !empty($date_settings['maximum_order_per_day'])) ? stripslashes(esc_attr($date_settings['maximum_order_per_day'])) : ""; ?>" placeholder="" autocomplete="off"/>
	                    	</div>

	                    	<div class="coderockz-woo-delivery-form-group" id="coderockz_delivery_date_calendar_locale">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_delivery_date_calendar_locale"><?php _e('Calendar Locale', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Delivery Date's calendar will showing in selected language. Default is English."><span class="dashicons dashicons-editor-help"></span></p>
	                    		<select class="coderockz-woo-delivery-select-field" name="coderockz_delivery_date_calendar_locale">
	                    			<option value="default" <?php if(isset($date_settings['calendar_locale']) && $date_settings['calendar_locale'] == "default"){ echo "selected"; } ?>>English</option>
									<option value="ar" <?php if(isset($date_settings['calendar_locale']) && $date_settings['calendar_locale'] == "ar"){ echo "selected"; } ?>>Arabic</option>
									<option value="at" <?php if(isset($date_settings['calendar_locale']) && $date_settings['calendar_locale'] == "at"){ echo "selected"; } ?>>Austria</option>
									<option value="az" <?php if(isset($date_settings['calendar_locale']) && $date_settings['calendar_locale'] == "az"){ echo "selected"; } ?>>Azerbaijan</option>
									<option value="be" <?php if(isset($date_settings['calendar_locale']) && $date_settings['calendar_locale'] == "be"){ echo "selected"; } ?>>Belarusian</option>
									<option value="bg" <?php if(isset($date_settings['calendar_locale']) && $date_settings['calendar_locale'] == "bg"){ echo "selected"; } ?>>Bulgarian</option>
									<option value="bn" <?php if(isset($date_settings['calendar_locale']) && $date_settings['calendar_locale'] == "bn"){ echo "selected"; } ?>>Bangla</option>
									<option value="bs" <?php if(isset($date_settings['calendar_locale']) && $date_settings['calendar_locale'] == "bs"){ echo "selected"; } ?>>Bosnian</option>
									<option value="cat" <?php if(isset($date_settings['calendar_locale']) && $date_settings['calendar_locale'] == "cat"){ echo "selected"; } ?>>Catalan</option>
									<option value="cs" <?php if(isset($date_settings['calendar_locale']) && $date_settings['calendar_locale'] == "cs"){ echo "selected"; } ?>>Czech</option>
									<option value="cy" <?php if(isset($date_settings['calendar_locale']) && $date_settings['calendar_locale'] == "cy"){ echo "selected"; } ?>>Welsh</option>
									<option value="da" <?php if(isset($date_settings['calendar_locale']) && $date_settings['calendar_locale'] == "da"){ echo "selected"; } ?>>Danish</option>
									<option value="de" <?php if(isset($date_settings['calendar_locale']) && $date_settings['calendar_locale'] == "de"){ echo "selected"; } ?>>German</option>
									<option value="eo" <?php if(isset($date_settings['calendar_locale']) && $date_settings['calendar_locale'] == "eo"){ echo "selected"; } ?>>Esperanto</option>
									<option value="es" <?php if(isset($date_settings['calendar_locale']) && $date_settings['calendar_locale'] == "es"){ echo "selected"; } ?>>Spanish</option>
									<option value="et" <?php if(isset($date_settings['calendar_locale']) && $date_settings['calendar_locale'] == "et"){ echo "selected"; } ?>>Estonian</option>
									<option value="fi" <?php if(isset($date_settings['calendar_locale']) && $date_settings['calendar_locale'] == "fi"){ echo "selected"; } ?>>Finnish</option>
									<option value="fr" <?php if(isset($date_settings['calendar_locale']) && $date_settings['calendar_locale'] == "fr"){ echo "selected"; } ?>>French</option>
									<option value="fo" <?php if(isset($date_settings['calendar_locale']) && $date_settings['calendar_locale'] == "fo"){ echo "selected"; } ?>>Faroese</option>
									<option value="fa" <?php if(isset($date_settings['calendar_locale']) && $date_settings['calendar_locale'] == "fa"){ echo "selected"; } ?>>Farsi (Persian)</option>
									<option value="ga" <?php if(isset($date_settings['calendar_locale']) && $date_settings['calendar_locale'] == "ga"){ echo "selected"; } ?>>Gaelic Irish</option>
									<option value="gr" <?php if(isset($date_settings['calendar_locale']) && $date_settings['calendar_locale'] == "gr"){ echo "selected"; } ?>>Greek</option>
									<option value="he" <?php if(isset($date_settings['calendar_locale']) && $date_settings['calendar_locale'] == "he"){ echo "selected"; } ?>>Hebrew</option>
									<option value="hi" <?php if(isset($date_settings['calendar_locale']) && $date_settings['calendar_locale'] == "hi"){ echo "selected"; } ?>>Hindi</option>
									<option value="hr" <?php if(isset($date_settings['calendar_locale']) && $date_settings['calendar_locale'] == "hr"){ echo "selected"; } ?>>Croatian</option>
									<option value="hu" <?php if(isset($date_settings['calendar_locale']) && $date_settings['calendar_locale'] == "hu"){ echo "selected"; } ?>>Hungarian</option>
									<option value="id" <?php if(isset($date_settings['calendar_locale']) && $date_settings['calendar_locale'] == "id"){ echo "selected"; } ?>>Indonesian</option>
									<option value="is" <?php if(isset($date_settings['calendar_locale']) && $date_settings['calendar_locale'] == "is"){ echo "selected"; } ?>>Icelandic</option>
									<option value="it" <?php if(isset($date_settings['calendar_locale']) && $date_settings['calendar_locale'] == "it"){ echo "selected"; } ?>>Italian</option>
									<option value="ja" <?php if(isset($date_settings['calendar_locale']) && $date_settings['calendar_locale'] == "ja"){ echo "selected"; } ?>>Japanese</option>
									<option value="ka" <?php if(isset($date_settings['calendar_locale']) && $date_settings['calendar_locale'] == "ka"){ echo "selected"; } ?>>Georgian</option>
									<option value="km" <?php if(isset($date_settings['calendar_locale']) && $date_settings['calendar_locale'] == "km"){ echo "selected"; } ?>>Khmer</option>
									<option value="ko" <?php if(isset($date_settings['calendar_locale']) && $date_settings['calendar_locale'] == "ko"){ echo "selected"; } ?>>Republic of Korea</option>
									<option value="kz" <?php if(isset($date_settings['calendar_locale']) && $date_settings['calendar_locale'] == "kz"){ echo "selected"; } ?>>Kazakh</option>
									<option value="lt" <?php if(isset($date_settings['calendar_locale']) && $date_settings['calendar_locale'] == "lt"){ echo "selected"; } ?>>Lithuanian</option>
									<option value="lv" <?php if(isset($date_settings['calendar_locale']) && $date_settings['calendar_locale'] == "lv"){ echo "selected"; } ?>>Latvian</option>
									<option value="mk" <?php if(isset($date_settings['calendar_locale']) && $date_settings['calendar_locale'] == "mk"){ echo "selected"; } ?>>Macedonian</option>
									<option value="mn" <?php if(isset($date_settings['calendar_locale']) && $date_settings['calendar_locale'] == "mn"){ echo "selected"; } ?>>Mongolian</option>
									<option value="ms" <?php if(isset($date_settings['calendar_locale']) && $date_settings['calendar_locale'] == "ms"){ echo "selected"; } ?>>Malaysian</option>
									<option value="my" <?php if(isset($date_settings['calendar_locale']) && $date_settings['calendar_locale'] == "my"){ echo "selected"; } ?>>Burmese</option>
									<option value="nl" <?php if(isset($date_settings['calendar_locale']) && $date_settings['calendar_locale'] == "nl"){ echo "selected"; } ?>>Dutch</option>
									<option value="no" <?php if(isset($date_settings['calendar_locale']) && $date_settings['calendar_locale'] == "no"){ echo "selected"; } ?>>Norwegian</option>
									<option value="pa" <?php if(isset($date_settings['calendar_locale']) && $date_settings['calendar_locale'] == "pa"){ echo "selected"; } ?>>Punjabi</option>
									<option value="pl" <?php if(isset($date_settings['calendar_locale']) && $date_settings['calendar_locale'] == "pl"){ echo "selected"; } ?>>Polish</option>
									<option value="pt" <?php if(isset($date_settings['calendar_locale']) && $date_settings['calendar_locale'] == "pt"){ echo "selected"; } ?>>Portuguese</option>
									<option value="ro" <?php if(isset($date_settings['calendar_locale']) && $date_settings['calendar_locale'] == "ro"){ echo "selected"; } ?>>Romanian</option>
									<option value="ru" <?php if(isset($date_settings['calendar_locale']) && $date_settings['calendar_locale'] == "ru"){ echo "selected"; } ?>>Russian</option>
									<option value="sk" <?php if(isset($date_settings['calendar_locale']) && $date_settings['calendar_locale'] == "sk"){ echo "selected"; } ?>>Slovak</option>
									<option value="sl" <?php if(isset($date_settings['calendar_locale']) && $date_settings['calendar_locale'] == "sl"){ echo "selected"; } ?>>Slovenian</option>
									<option value="si" <?php if(isset($date_settings['calendar_locale']) && $date_settings['calendar_locale'] == "si"){ echo "selected"; } ?>>Sinhala</option>
									<option value="sq" <?php if(isset($date_settings['calendar_locale']) && $date_settings['calendar_locale'] == "sq"){ echo "selected"; } ?>>Albanian</option>
									<option value="sr-cyr" <?php if(isset($date_settings['calendar_locale']) && $date_settings['calendar_locale'] == "sr-cyr"){ echo "selected"; } ?>>Serbian Cyrillic</option>
									<option value="sr" <?php if(isset($date_settings['calendar_locale']) && $date_settings['calendar_locale'] == "sr"){ echo "selected"; } ?>>Serbian</option>
									<option value="sv" <?php if(isset($date_settings['calendar_locale']) && $date_settings['calendar_locale'] == "sv"){ echo "selected"; } ?>>Swedish</option>
									<option value="th" <?php if(isset($date_settings['calendar_locale']) && $date_settings['calendar_locale'] == "th"){ echo "selected"; } ?>>Thai</option>
									<option value="tr" <?php if(isset($date_settings['calendar_locale']) && $date_settings['calendar_locale'] == "tr"){ echo "selected"; } ?>>Turkish</option>
									<option value="uk" <?php if(isset($date_settings['calendar_locale']) && $date_settings['calendar_locale'] == "uk"){ echo "selected"; } ?>>Ukrainian</option>
									<option value="uz" <?php if(isset($date_settings['calendar_locale']) && $date_settings['calendar_locale'] == "uz"){ echo "selected"; } ?>>Uzbek</option>
									<option value="vn" <?php if(isset($date_settings['calendar_locale']) && $date_settings['calendar_locale'] == "vn"){ echo "selected"; } ?>>Vietnamese</option>
									<option value="zh" <?php if(isset($date_settings['calendar_locale']) && $date_settings['calendar_locale'] == "zh"){ echo "selected"; } ?>>Mandarin</option>
								</select>
	                    	</div>

	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_calendar_theme"><?php _e('Calendar Theme', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="If you want to change the calendar theme, select your desire theme from here."><span class="dashicons dashicons-editor-help"></span></p>
	                    		<select class="coderockz-woo-delivery-select-field" name="coderockz_woo_delivery_calendar_theme">
								    <option value="" <?php if(isset($date_settings['calendar_theme']) && $date_settings['calendar_theme'] == ""){ echo "selected"; } ?>>Default</option>
									<option value="dark" <?php if(isset($date_settings['calendar_theme']) && $date_settings['calendar_theme'] == "dark"){ echo "selected"; } ?>>Dark</option>
									<option value="material_blue" <?php if(isset($date_settings['calendar_theme']) && $date_settings['calendar_theme'] == "material_blue"){ echo "selected"; } ?>>Material Blue</option>
									<option value="material_green" <?php if(isset($date_settings['calendar_theme']) && $date_settings['calendar_theme'] == "material_green"){ echo "selected"; } ?>>Material Green</option>
									<option value="material_red" <?php if(isset($date_settings['calendar_theme']) && $date_settings['calendar_theme'] == "material_red"){ echo "selected"; } ?>>Material Red</option>
									<option value="material_orange" <?php if(isset($date_settings['calendar_theme']) && $date_settings['calendar_theme'] == "material_orange"){ echo "selected"; } ?>>Material Orange</option>
									<option value="airbnb" <?php if(isset($date_settings['calendar_theme']) && $date_settings['calendar_theme'] == "airbnb"){ echo "selected"; } ?>>Airbnb</option>
									<option value="confetti" <?php if(isset($date_settings['calendar_theme']) && $date_settings['calendar_theme'] == "confetti"){ echo "selected"; } ?>>Confetti</option>
								</select>
	                    		
	                    	</div>

	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_delivery_date_week_starts_from"><?php _e('Week Starts From', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Delivery Date's calendar will start from the day that is selected Here. Default is Sunday."><span class="dashicons dashicons-editor-help"></span></p>
	                    		<select class="coderockz-woo-delivery-select-field" name="coderockz_delivery_date_week_starts_from">
	                    			<option value="" <?php if(isset($date_settings['week_starts_from']) && $date_settings['week_starts_from'] == ""){ echo "selected"; } ?>><?php _e('Select Day', 'coderockz-woo-delivery'); ?></option>
									<option value="0" <?php if(isset($date_settings['week_starts_from']) && $date_settings['week_starts_from'] == "0"){ echo "selected"; } ?>>Sunday</option>
									<option value="1" <?php if(isset($date_settings['week_starts_from']) && $date_settings['week_starts_from'] == "1"){ echo "selected"; } ?>>Monday</option>
									<option value="2" <?php if(isset($date_settings['week_starts_from']) && $date_settings['week_starts_from'] == "2"){ echo "selected"; } ?>>Tuesday</option>
									<option value="3" <?php if(isset($date_settings['week_starts_from']) && $date_settings['week_starts_from'] == "3"){ echo "selected"; } ?>>Wednesday</option>
									<option value="4" <?php if(isset($date_settings['week_starts_from']) && $date_settings['week_starts_from'] == "4"){ echo "selected"; } ?>>Thursday</option>
									<option value="5" <?php if(isset($date_settings['week_starts_from']) && $date_settings['week_starts_from'] == "5"){ echo "selected"; } ?>>Friday</option>
									<option value="6" <?php if(isset($date_settings['week_starts_from']) && $date_settings['week_starts_from'] == "6"){ echo "selected"; } ?>>Saturday</option>
								</select>
	                    	</div>

	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_delivery_date_format"><?php _e('Delivery Date Format', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Date format that is used in everywhere which is available by this plugin. Default is F j, Y ( ex. March 6, 2011 )."><span class="dashicons dashicons-editor-help"></span></p>
	                    		<select class="coderockz-woo-delivery-select-field" name="coderockz_delivery_date_format">
	                    			<option value="F j, Y" <?php if(isset($date_settings['date_format']) && $date_settings['date_format'] == "F j, Y"){ echo "selected"; } ?>>F j, Y ( ex. March 6, 2011 )</option>
									<option value="d-m-Y" <?php if(isset($date_settings['date_format']) && $date_settings['date_format'] == "d-m-Y"){ echo "selected"; } ?>>d-m-Y ( ex. 29-03-2011 )</option>
									<option value="m/d/Y" <?php if(isset($date_settings['date_format']) && $date_settings['date_format'] == "m/d/Y"){ echo "selected"; } ?>>m/d/Y ( ex. 03/29/2011 )</option>
									<option value="d.m.Y" <?php if(isset($date_settings['date_format']) && $date_settings['date_format'] == "d.m.Y"){ echo "selected"; } ?>>d.m.Y ( ex. 29.03.2011 )</option>
									
								</select>
	                    	</div>

	                    	<div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label"><?php _e('Add Weekday Name in Delivery Date', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="If you want to add the weekday name in the delivery date then enable it. Default is disable."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_woo_delivery_add_weekday_name">
							       <input type="checkbox" name="coderockz_woo_delivery_add_weekday_name" id="coderockz_woo_delivery_add_weekday_name" <?php echo (isset($date_settings['add_weekday_name']) && !empty($date_settings['add_weekday_name'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>

	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label coderockz-woo-delivery-checkbox-label" for="coderockz_delivery_date_delivery_days"><?php _e('Delivery Days', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip coderockz-woo-delivery-checkbox-tooltip" tooltip="Delivery is only available in those days that are checked. Other dates corresponding to the unchecked days are disabled in the calendar."><span class="dashicons dashicons-editor-help"></span></p>
	                    		<div id="coderockz_delivery_date_delivery_days" style="display:inline-block">
	                    		<input type="checkbox" name="coderockz_delivery_date_delivery_days[]" value="6" <?php echo in_array("6",$selected_delivery_day) ? "checked" : "";?>><label class="coderockz-woo-delivery-checkbox-field-text">Saturday</label><br/>
								<input type="checkbox" name="coderockz_delivery_date_delivery_days[]" value="0" <?php echo in_array("0",$selected_delivery_day) ? "checked" : "";?>><label class="coderockz-woo-delivery-checkbox-field-text">Sunday</label><br/>
								<input type="checkbox" name="coderockz_delivery_date_delivery_days[]" value="1" <?php echo in_array("1",$selected_delivery_day) ? "checked" : "";?>><label class="coderockz-woo-delivery-checkbox-field-text">Monday</label><br/>
								<input type="checkbox" name="coderockz_delivery_date_delivery_days[]" value="2" <?php echo in_array("2",$selected_delivery_day) ? "checked" : "";?>><label class="coderockz-woo-delivery-checkbox-field-text">Tuesday</label><br/>
								<input type="checkbox" name="coderockz_delivery_date_delivery_days[]" value="3" <?php echo in_array("3",$selected_delivery_day) ? "checked" : "";?>><label class="coderockz-woo-delivery-checkbox-field-text">Wednesday</label><br/>
								<input type="checkbox" name="coderockz_delivery_date_delivery_days[]" value="4" <?php echo in_array("4",$selected_delivery_day) ? "checked" : "";?>><label class="coderockz-woo-delivery-checkbox-field-text">Thursday</label><br/>
								<input type="checkbox" name="coderockz_delivery_date_delivery_days[]" value="5" <?php echo in_array("5",$selected_delivery_day) ? "checked" : "";?>><label class="coderockz-woo-delivery-checkbox-field-text">Friday</label><br/>
								</div>
	                    	</div>

	                    	<div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label"><?php _e('Disable Same Day Delivery', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="Disable same day delivery according to your timezone. Default is disable."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_disable_same_day_delivery">
							       <input type="checkbox" name="coderockz_disable_same_day_delivery" id="coderockz_disable_same_day_delivery" <?php echo (isset($date_settings['disable_same_day_delivery']) && !empty($date_settings['disable_same_day_delivery'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>

	                    	<div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label"><?php _e('Auto Select 1st Available Date', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="Enable the option if you want to select the first available date automatically and shown in the delivery date field. Default is disable."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_auto_select_first_date">
							       <input type="checkbox" name="coderockz_auto_select_first_date" id="coderockz_auto_select_first_date" <?php echo (isset($date_settings['auto_select_first_date']) && !empty($date_settings['auto_select_first_date'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>

	                        <input class="coderockz-woo-delivery-submit-btn" type="submit" name="coderockz_delivery_date_form_submit" value="<?php _e('Save Changes', 'coderockz-woo-delivery'); ?>" />

	                    </form>
                	</div>

                </div>

			</div>

			<div data-tab="tab6" class="coderockz-woo-delivery-tabcontent">
				<div class="coderockz-woo-delivery-card">
					<p class="coderockz-woo-delivery-card-header"><?php _e('General Pickup Date Settings', 'coderockz-woo-delivery'); ?></p>
					<div class="coderockz-woo-delivery-card-body">
						<p class="coderockz-woo-delivery-pickup-date-tab-notice"><span class="dashicons dashicons-yes"></span><?php _e(' Settings Changed Successfully', 'coderockz-woo-delivery'); ?></p>
	                    <form action="" method="post" id ="coderockz_delivery_pickup_date_form_submit">
	                        <?php wp_nonce_field('coderockz_woo_delivery_nonce'); ?>

	                    	<div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label"><?php _e('Enable Pickup Date', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="Enable Pickup Date input field in woocommerce order checkout page."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_enable_pickup_date">
							       <input type="checkbox" name="coderockz_enable_pickup_date" id="coderockz_enable_pickup_date" <?php echo (isset($pickup_date_settings['enable_pickup_date']) && !empty($pickup_date_settings['enable_pickup_date'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label"><?php _e('Make Pickup Date Field Mandatory', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="Make Pickup Date input field mandatory in woocommerce order checkout page. Default is optional."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_pickup_date_mandatory">
							       <input type="checkbox" name="coderockz_pickup_date_mandatory" id="coderockz_pickup_date_mandatory" <?php echo (isset($pickup_date_settings['pickup_date_mandatory']) && !empty($pickup_date_settings['pickup_date_mandatory'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>

	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_pickup_date_field_label"><?php _e('Date Field Heading for Pickup', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Pickup Date input field heading. Default is Pickup Date."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_pickup_date_field_label" name="coderockz_pickup_date_field_label" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($pickup_date_settings['pickup_field_label']) && !empty($pickup_date_settings['pickup_field_label'])) ? stripslashes(esc_attr($pickup_date_settings['pickup_field_label'])) : "" ?>" placeholder="" autocomplete="off"/>
	                    	</div>

	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_delivery_pickup_field_placeholder"><?php _e('Date Field Placeholder for Pickup', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Pickup Date input field placeholder. Default is Pickup Date."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_delivery_pickup_field_placeholder" name="coderockz_delivery_pickup_field_placeholder" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($pickup_date_settings['pickup_field_placeholder']) && !empty($pickup_date_settings['pickup_field_placeholder'])) ? stripslashes(esc_attr($pickup_date_settings['pickup_field_placeholder'])) : "" ?>" placeholder="" autocomplete="off"/>
	                    	</div>

	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_pickup_date_selectable_date"><?php _e('Allow Pickup in Next Available Days', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="User can only select the number of date from calander that is specified Here. Other dates are disabled. Only numerical value is excepted. Default is 365 days."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input onkeyup="if(!Number.isInteger(Number(this.value)) || this.value < 1) this.value = null;" id="coderockz_pickup_date_selectable_date" name="coderockz_pickup_date_selectable_date" type="number" class="coderockz-woo-delivery-number-field" value="<?php echo (isset($pickup_date_settings['selectable_date']) && !empty($pickup_date_settings['selectable_date'])) ? stripslashes(esc_attr($pickup_date_settings['selectable_date'])) : "" ?>" placeholder="" autocomplete="off"/>
	                    	</div>

	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_pickup_date_selectable_date"><?php _e('Allow Pickup Until Date', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="User can only select a date until the date that is specified Here. Input date format YYYY-MM-DD."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_pickup_date_selectable_date_until" name="coderockz_pickup_date_selectable_date_until" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($pickup_date_settings['selectable_date_until']) && !empty($pickup_date_settings['selectable_date_until'])) ? stripslashes(esc_attr($pickup_date_settings['selectable_date_until'])) : ""; ?>" placeholder="YYYY-MM-DD" autocomplete="off"/>
	                    	</div>

	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_delivery_date_maximum_pickup_per_day"><?php _e('Maximum Pickup Per Day', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="If you want to limit your pickup per day, put the pickup quantity here. Keep blank for unlimited pickup."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input onkeyup="if(!Number.isInteger(Number(this.value)) || this.value < 1) this.value = null;" id="coderockz_delivery_date_maximum_pickup_per_day" name="coderockz_delivery_date_maximum_pickup_per_day" type="number" class="coderockz-woo-delivery-number-field" value="<?php echo (isset($pickup_date_settings['maximum_pickup_per_day']) && !empty($pickup_date_settings['maximum_pickup_per_day'])) ? stripslashes(esc_attr($pickup_date_settings['maximum_pickup_per_day'])) : "" ?>" placeholder="" autocomplete="off"/>
	                    	</div>

	                    	<div class="coderockz-woo-delivery-form-group" id="coderockz_pickup_date_calendar_locale">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_pickup_date_calendar_locale"><?php _e('Calendar Locale', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Delivery Date's calendar will showing in selected language. Default is English."><span class="dashicons dashicons-editor-help"></span></p>
	                    		<select class="coderockz-woo-delivery-select-field" name="coderockz_pickup_date_calendar_locale">
	                    			<option value="default" <?php if(isset($pickup_date_settings['calendar_locale']) && $pickup_date_settings['calendar_locale'] == "default"){ echo "selected"; } ?>>English</option>
									<option value="ar" <?php if(isset($pickup_date_settings['calendar_locale']) && $pickup_date_settings['calendar_locale'] == "ar"){ echo "selected"; } ?>>Arabic</option>
									<option value="at" <?php if(isset($pickup_date_settings['calendar_locale']) && $pickup_date_settings['calendar_locale'] == "at"){ echo "selected"; } ?>>Austria</option>
									<option value="az" <?php if(isset($pickup_date_settings['calendar_locale']) && $pickup_date_settings['calendar_locale'] == "az"){ echo "selected"; } ?>>Azerbaijan</option>
									<option value="be" <?php if(isset($pickup_date_settings['calendar_locale']) && $pickup_date_settings['calendar_locale'] == "be"){ echo "selected"; } ?>>Belarusian</option>
									<option value="bg" <?php if(isset($pickup_date_settings['calendar_locale']) && $pickup_date_settings['calendar_locale'] == "bg"){ echo "selected"; } ?>>Bulgarian</option>
									<option value="bn" <?php if(isset($pickup_date_settings['calendar_locale']) && $pickup_date_settings['calendar_locale'] == "bn"){ echo "selected"; } ?>>Bangla</option>
									<option value="bs" <?php if(isset($pickup_date_settings['calendar_locale']) && $pickup_date_settings['calendar_locale'] == "bs"){ echo "selected"; } ?>>Bosnian</option>
									<option value="cat" <?php if(isset($pickup_date_settings['calendar_locale']) && $pickup_date_settings['calendar_locale'] == "cat"){ echo "selected"; } ?>>Catalan</option>
									<option value="cs" <?php if(isset($pickup_date_settings['calendar_locale']) && $pickup_date_settings['calendar_locale'] == "cs"){ echo "selected"; } ?>>Czech</option>
									<option value="cy" <?php if(isset($pickup_date_settings['calendar_locale']) && $pickup_date_settings['calendar_locale'] == "cy"){ echo "selected"; } ?>>Welsh</option>
									<option value="da" <?php if(isset($pickup_date_settings['calendar_locale']) && $pickup_date_settings['calendar_locale'] == "da"){ echo "selected"; } ?>>Danish</option>
									<option value="de" <?php if(isset($pickup_date_settings['calendar_locale']) && $pickup_date_settings['calendar_locale'] == "de"){ echo "selected"; } ?>>German</option>
									<option value="eo" <?php if(isset($pickup_date_settings['calendar_locale']) && $pickup_date_settings['calendar_locale'] == "eo"){ echo "selected"; } ?>>Esperanto</option>
									<option value="es" <?php if(isset($pickup_date_settings['calendar_locale']) && $pickup_date_settings['calendar_locale'] == "es"){ echo "selected"; } ?>>Spanish</option>
									<option value="et" <?php if(isset($pickup_date_settings['calendar_locale']) && $pickup_date_settings['calendar_locale'] == "et"){ echo "selected"; } ?>>Estonian</option>
									<option value="fi" <?php if(isset($pickup_date_settings['calendar_locale']) && $pickup_date_settings['calendar_locale'] == "fi"){ echo "selected"; } ?>>Finnish</option>
									<option value="fr" <?php if(isset($pickup_date_settings['calendar_locale']) && $pickup_date_settings['calendar_locale'] == "fr"){ echo "selected"; } ?>>French</option>
									<option value="fo" <?php if(isset($pickup_date_settings['calendar_locale']) && $pickup_date_settings['calendar_locale'] == "fo"){ echo "selected"; } ?>>Faroese</option>
									<option value="fa" <?php if(isset($pickup_date_settings['calendar_locale']) && $pickup_date_settings['calendar_locale'] == "fa"){ echo "selected"; } ?>>Farsi (Persian)</option>
									<option value="ga" <?php if(isset($pickup_date_settings['calendar_locale']) && $pickup_date_settings['calendar_locale'] == "ga"){ echo "selected"; } ?>>Gaelic Irish</option>
									<option value="gr" <?php if(isset($pickup_date_settings['calendar_locale']) && $pickup_date_settings['calendar_locale'] == "gr"){ echo "selected"; } ?>>Greek</option>
									<option value="he" <?php if(isset($pickup_date_settings['calendar_locale']) && $pickup_date_settings['calendar_locale'] == "he"){ echo "selected"; } ?>>Hebrew</option>
									<option value="hi" <?php if(isset($pickup_date_settings['calendar_locale']) && $pickup_date_settings['calendar_locale'] == "hi"){ echo "selected"; } ?>>Hindi</option>
									<option value="hr" <?php if(isset($pickup_date_settings['calendar_locale']) && $pickup_date_settings['calendar_locale'] == "hr"){ echo "selected"; } ?>>Croatian</option>
									<option value="hu" <?php if(isset($pickup_date_settings['calendar_locale']) && $pickup_date_settings['calendar_locale'] == "hu"){ echo "selected"; } ?>>Hungarian</option>
									<option value="id" <?php if(isset($pickup_date_settings['calendar_locale']) && $pickup_date_settings['calendar_locale'] == "id"){ echo "selected"; } ?>>Indonesian</option>
									<option value="is" <?php if(isset($pickup_date_settings['calendar_locale']) && $pickup_date_settings['calendar_locale'] == "is"){ echo "selected"; } ?>>Icelandic</option>
									<option value="it" <?php if(isset($pickup_date_settings['calendar_locale']) && $pickup_date_settings['calendar_locale'] == "it"){ echo "selected"; } ?>>Italian</option>
									<option value="ja" <?php if(isset($pickup_date_settings['calendar_locale']) && $pickup_date_settings['calendar_locale'] == "ja"){ echo "selected"; } ?>>Japanese</option>
									<option value="ka" <?php if(isset($pickup_date_settings['calendar_locale']) && $pickup_date_settings['calendar_locale'] == "ka"){ echo "selected"; } ?>>Georgian</option>
									<option value="km" <?php if(isset($pickup_date_settings['calendar_locale']) && $pickup_date_settings['calendar_locale'] == "km"){ echo "selected"; } ?>>Khmer</option>
									<option value="ko" <?php if(isset($pickup_date_settings['calendar_locale']) && $pickup_date_settings['calendar_locale'] == "ko"){ echo "selected"; } ?>>Republic of Korea</option>
									<option value="kz" <?php if(isset($pickup_date_settings['calendar_locale']) && $pickup_date_settings['calendar_locale'] == "kz"){ echo "selected"; } ?>>Kazakh</option>
									<option value="lt" <?php if(isset($pickup_date_settings['calendar_locale']) && $pickup_date_settings['calendar_locale'] == "lt"){ echo "selected"; } ?>>Lithuanian</option>
									<option value="lv" <?php if(isset($pickup_date_settings['calendar_locale']) && $pickup_date_settings['calendar_locale'] == "lv"){ echo "selected"; } ?>>Latvian</option>
									<option value="mk" <?php if(isset($pickup_date_settings['calendar_locale']) && $pickup_date_settings['calendar_locale'] == "mk"){ echo "selected"; } ?>>Macedonian</option>
									<option value="mn" <?php if(isset($pickup_date_settings['calendar_locale']) && $pickup_date_settings['calendar_locale'] == "mn"){ echo "selected"; } ?>>Mongolian</option>
									<option value="ms" <?php if(isset($pickup_date_settings['calendar_locale']) && $pickup_date_settings['calendar_locale'] == "ms"){ echo "selected"; } ?>>Malaysian</option>
									<option value="my" <?php if(isset($pickup_date_settings['calendar_locale']) && $pickup_date_settings['calendar_locale'] == "my"){ echo "selected"; } ?>>Burmese</option>
									<option value="nl" <?php if(isset($pickup_date_settings['calendar_locale']) && $pickup_date_settings['calendar_locale'] == "nl"){ echo "selected"; } ?>>Dutch</option>
									<option value="no" <?php if(isset($pickup_date_settings['calendar_locale']) && $pickup_date_settings['calendar_locale'] == "no"){ echo "selected"; } ?>>Norwegian</option>
									<option value="pa" <?php if(isset($pickup_date_settings['calendar_locale']) && $pickup_date_settings['calendar_locale'] == "pa"){ echo "selected"; } ?>>Punjabi</option>
									<option value="pl" <?php if(isset($pickup_date_settings['calendar_locale']) && $pickup_date_settings['calendar_locale'] == "pl"){ echo "selected"; } ?>>Polish</option>
									<option value="pt" <?php if(isset($pickup_date_settings['calendar_locale']) && $pickup_date_settings['calendar_locale'] == "pt"){ echo "selected"; } ?>>Portuguese</option>
									<option value="ro" <?php if(isset($pickup_date_settings['calendar_locale']) && $pickup_date_settings['calendar_locale'] == "ro"){ echo "selected"; } ?>>Romanian</option>
									<option value="ru" <?php if(isset($pickup_date_settings['calendar_locale']) && $pickup_date_settings['calendar_locale'] == "ru"){ echo "selected"; } ?>>Russian</option>
									<option value="sk" <?php if(isset($pickup_date_settings['calendar_locale']) && $pickup_date_settings['calendar_locale'] == "sk"){ echo "selected"; } ?>>Slovak</option>
									<option value="sl" <?php if(isset($pickup_date_settings['calendar_locale']) && $pickup_date_settings['calendar_locale'] == "sl"){ echo "selected"; } ?>>Slovenian</option>
									<option value="si" <?php if(isset($pickup_date_settings['calendar_locale']) && $pickup_date_settings['calendar_locale'] == "si"){ echo "selected"; } ?>>Sinhala</option>
									<option value="sq" <?php if(isset($pickup_date_settings['calendar_locale']) && $pickup_date_settings['calendar_locale'] == "sq"){ echo "selected"; } ?>>Albanian</option>
									<option value="sr-cyr" <?php if(isset($pickup_date_settings['calendar_locale']) && $pickup_date_settings['calendar_locale'] == "sr-cyr"){ echo "selected"; } ?>>Serbian Cyrillic</option>
									<option value="sr" <?php if(isset($pickup_date_settings['calendar_locale']) && $pickup_date_settings['calendar_locale'] == "sr"){ echo "selected"; } ?>>Serbian</option>
									<option value="sv" <?php if(isset($pickup_date_settings['calendar_locale']) && $pickup_date_settings['calendar_locale'] == "sv"){ echo "selected"; } ?>>Swedish</option>
									<option value="th" <?php if(isset($pickup_date_settings['calendar_locale']) && $pickup_date_settings['calendar_locale'] == "th"){ echo "selected"; } ?>>Thai</option>
									<option value="tr" <?php if(isset($pickup_date_settings['calendar_locale']) && $pickup_date_settings['calendar_locale'] == "tr"){ echo "selected"; } ?>>Turkish</option>
									<option value="uk" <?php if(isset($pickup_date_settings['calendar_locale']) && $pickup_date_settings['calendar_locale'] == "uk"){ echo "selected"; } ?>>Ukrainian</option>
									<option value="uz" <?php if(isset($pickup_date_settings['calendar_locale']) && $pickup_date_settings['calendar_locale'] == "uz"){ echo "selected"; } ?>>Uzbek</option>
									<option value="vn" <?php if(isset($pickup_date_settings['calendar_locale']) && $pickup_date_settings['calendar_locale'] == "vn"){ echo "selected"; } ?>>Vietnamese</option>
									<option value="zh" <?php if(isset($pickup_date_settings['calendar_locale']) && $pickup_date_settings['calendar_locale'] == "zh"){ echo "selected"; } ?>>Mandarin</option>
								</select>
	                    	</div>

	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_pickup_calendar_theme"><?php _e('Calendar Theme', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="If you want to change the calendar theme, select your desire theme from here."><span class="dashicons dashicons-editor-help"></span></p>
	                    		<select class="coderockz-woo-delivery-select-field" name="coderockz_woo_delivery_pickup_calendar_theme">
								    <option value="" <?php if(isset($date_settings['calendar_theme']) && $date_settings['calendar_theme'] == ""){ echo "selected"; } ?>>Default</option>
									<option value="dark" <?php if(isset($date_settings['calendar_theme']) && $date_settings['calendar_theme'] == "dark"){ echo "selected"; } ?>>Dark</option>
									<option value="material_blue" <?php if(isset($date_settings['calendar_theme']) && $date_settings['calendar_theme'] == "material_blue"){ echo "selected"; } ?>>Material Blue</option>
									<option value="material_green" <?php if(isset($date_settings['calendar_theme']) && $date_settings['calendar_theme'] == "material_green"){ echo "selected"; } ?>>Material Green</option>
									<option value="material_red" <?php if(isset($date_settings['calendar_theme']) && $date_settings['calendar_theme'] == "material_red"){ echo "selected"; } ?>>Material Red</option>
									<option value="material_orange" <?php if(isset($date_settings['calendar_theme']) && $date_settings['calendar_theme'] == "material_orange"){ echo "selected"; } ?>>Material Orange</option>
									<option value="airbnb" <?php if(isset($date_settings['calendar_theme']) && $date_settings['calendar_theme'] == "airbnb"){ echo "selected"; } ?>>Airbnb</option>
									<option value="confetti" <?php if(isset($date_settings['calendar_theme']) && $date_settings['calendar_theme'] == "confetti"){ echo "selected"; } ?>>Confetti</option>
								</select>
	                    		
	                    	</div>

	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_pickup_date_week_starts_from"><?php _e('Week Starts From', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Pickup Date's calendar will start from the day that is selected Here. Default is Sunday."><span class="dashicons dashicons-editor-help"></span></p>
	                    		<select class="coderockz-woo-delivery-select-field" name="coderockz_pickup_date_week_starts_from">
	                    			<option value="" <?php if(isset($pickup_date_settings['week_starts_from']) && $pickup_date_settings['week_starts_from'] == ""){ echo "selected"; } ?>><?php _e('Select Day', 'coderockz-woo-delivery'); ?></option>
									<option value="0" <?php if(isset($pickup_date_settings['week_starts_from']) && $pickup_date_settings['week_starts_from'] == "0"){ echo "selected"; } ?>>Sunday</option>
									<option value="1" <?php if(isset($pickup_date_settings['week_starts_from']) && $pickup_date_settings['week_starts_from'] == "1"){ echo "selected"; } ?>>Monday</option>
									<option value="2" <?php if(isset($pickup_date_settings['week_starts_from']) && $pickup_date_settings['week_starts_from'] == "2"){ echo "selected"; } ?>>Tuesday</option>
									<option value="3" <?php if(isset($pickup_date_settings['week_starts_from']) && $pickup_date_settings['week_starts_from'] == "3"){ echo "selected"; } ?>>Wednesday</option>
									<option value="4" <?php if(isset($pickup_date_settings['week_starts_from']) && $pickup_date_settings['week_starts_from'] == "4"){ echo "selected"; } ?>>Thursday</option>
									<option value="5" <?php if(isset($pickup_date_settings['week_starts_from']) && $pickup_date_settings['week_starts_from'] == "5"){ echo "selected"; } ?>>Friday</option>
									<option value="6" <?php if(isset($pickup_date_settings['week_starts_from']) && $pickup_date_settings['week_starts_from'] == "6"){ echo "selected"; } ?>>Saturday</option>
								</select>
	                    	</div>

	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_pickup_date_format"><?php _e('Pickup Date Format', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Date format that is used in everywhere which is available by this plugin. Default is F j, Y ( ex. March 6, 2011 )."><span class="dashicons dashicons-editor-help"></span></p>
	                    		<select class="coderockz-woo-delivery-select-field" name="coderockz_pickup_date_format">
	                    			<option value="F j, Y" <?php if(isset($pickup_date_settings['date_format']) && $pickup_date_settings['date_format'] == "F j, Y"){ echo "selected"; } ?>>F j, Y ( ex. March 6, 2011 )</option>
									<option value="d-m-Y" <?php if(isset($pickup_date_settings['date_format']) && $pickup_date_settings['date_format'] == "d-m-Y"){ echo "selected"; } ?>>d-m-Y ( ex. 29-03-2011 )</option>
									<option value="m/d/Y" <?php if(isset($pickup_date_settings['date_format']) && $pickup_date_settings['date_format'] == "m/d/Y"){ echo "selected"; } ?>>m/d/Y ( ex. 03/29/2011 )</option>
									<option value="d.m.Y" <?php if(isset($pickup_date_settings['date_format']) && $pickup_date_settings['date_format'] == "d.m.Y"){ echo "selected"; } ?>>d.m.Y ( ex. 29.03.2011 )</option>
									
								</select>
	                    	</div>

	                    	<div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label"><?php _e('Add Weekday Name in Pickup Date', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="If you want to add the weekday name in the pickup date then enable it. Default is disable."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_woo_delivery_pickup_add_weekday_name">
							       <input type="checkbox" name="coderockz_woo_delivery_pickup_add_weekday_name" id="coderockz_woo_delivery_pickup_add_weekday_name" <?php echo (isset($pickup_date_settings['add_weekday_name']) && !empty($pickup_date_settings['add_weekday_name'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>

	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label coderockz-woo-delivery-checkbox-label" for="coderockz_pickup_date_delivery_days"><?php _e('Pickup Days', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip coderockz-woo-delivery-checkbox-tooltip" tooltip="Pickup is only available in those days that are checked. Other dates corresponding to the unchecked days are disabled in the calendar."><span class="dashicons dashicons-editor-help"></span></p>
	                    		<div id="coderockz_pickup_date_delivery_days" style="display:inline-block">
	                    		<input type="checkbox" name="coderockz_pickup_date_delivery_days[]" value="6" <?php echo in_array("6",$selected_pickup_day) ? "checked" : "";?>><label class="coderockz-woo-delivery-checkbox-field-text">Saturday</label><br/>
								<input type="checkbox" name="coderockz_pickup_date_delivery_days[]" value="0" <?php echo in_array("0",$selected_pickup_day) ? "checked" : "";?>><label class="coderockz-woo-delivery-checkbox-field-text">Sunday</label><br/>
								<input type="checkbox" name="coderockz_pickup_date_delivery_days[]" value="1" <?php echo in_array("1",$selected_pickup_day) ? "checked" : "";?>><label class="coderockz-woo-delivery-checkbox-field-text">Monday</label><br/>
								<input type="checkbox" name="coderockz_pickup_date_delivery_days[]" value="2" <?php echo in_array("2",$selected_pickup_day) ? "checked" : "";?>><label class="coderockz-woo-delivery-checkbox-field-text">Tuesday</label><br/>
								<input type="checkbox" name="coderockz_pickup_date_delivery_days[]" value="3" <?php echo in_array("3",$selected_pickup_day) ? "checked" : "";?>><label class="coderockz-woo-delivery-checkbox-field-text">Wednesday</label><br/>
								<input type="checkbox" name="coderockz_pickup_date_delivery_days[]" value="4" <?php echo in_array("4",$selected_pickup_day) ? "checked" : "";?>><label class="coderockz-woo-delivery-checkbox-field-text">Thursday</label><br/>
								<input type="checkbox" name="coderockz_pickup_date_delivery_days[]" value="5" <?php echo in_array("5",$selected_pickup_day) ? "checked" : "";?>><label class="coderockz-woo-delivery-checkbox-field-text">Friday</label><br/>
								</div>
	                    	</div>

	                    	<div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label"><?php _e('Disable Same Day Pickup', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="Disable same day pickup according to your timezone. Default is disable."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_disable_same_day_pickup">
							       <input type="checkbox" name="coderockz_disable_same_day_pickup" id="coderockz_disable_same_day_pickup" <?php echo (isset($pickup_date_settings['disable_same_day_pickup']) && !empty($pickup_date_settings['disable_same_day_pickup'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>

	                    	<div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label"><?php _e('Auto Select 1st Available Date', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="Enable the option if you want to select the first available date automatically and shown in the pickup date field. Default is disable."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_auto_select_first_pickup_date">
							       <input type="checkbox" name="coderockz_auto_select_first_pickup_date" id="coderockz_auto_select_first_pickup_date" <?php echo (isset($pickup_date_settings['auto_select_first_pickup_date']) && !empty($pickup_date_settings['auto_select_first_pickup_date'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>

	                        <input class="coderockz-woo-delivery-submit-btn" type="submit" name="coderockz_delivery_pickup_date_form_submit" value="<?php _e('Save Changes', 'coderockz-woo-delivery'); ?>" />

	                    </form>
                	</div>

                </div>

			</div>

			<div data-tab="tab7" class="coderockz-woo-delivery-tabcontent">
				<div class="coderockz-woo-delivery-card">
					<p class="coderockz-woo-delivery-card-header"><?php _e('Delivery Off Days', 'coderockz-woo-delivery'); ?></p>
					<div class="coderockz-woo-delivery-card-body">
						<p class="coderockz-woo-delivery-date-tab-offdays-notice"></p>
						<input class="coderockz-woo-delivery-add-year-btn" type="button" value="<?php _e('Add New Year', 'coderockz-woo-delivery'); ?>">
	                    <form action="" method="post" id ="coderockz_delivery_date_offdays_form_submit">
	                        <?php wp_nonce_field('coderockz_woo_delivery_nonce'); ?>
	                        <div id="coderockz-woo-delivery-offdays" class="coderockz-woo-delivery-offdays">
							    
	                        	<?php
	                        		$month_array = ['january','february','march','april','may','june','july','august','september','october','november','december'];
									$offdays_html = "";
									$offdays_years = $date_settings;
									if(isset($offdays_years['off_days']) && !empty($offdays_years['off_days'])) {
										foreach($offdays_years['off_days'] as $year=>$months) {
											
											$offdays_html .= '<div class="coderockz-woo-delivery-add-year-html coderockz-woo-delivery-form-group">';
											if(array_keys($offdays_years['off_days'])[0] == $year) {
												$offdays_html .= '<img class="coderockz-arrow" src="'. CODEROCKZ_WOO_DELIVERY_URL .'/admin/images/arrow.png" alt="" style="width: 20px;vertical-align: top;margin-top: 12px;margin-right: 15px;">';	

											} else {
												$offdays_html .= '<button class="coderockz-offdays-year-remove"><span class="dashicons dashicons-trash"></span></button>';
											}
											
											$offdays_html .= '<input style="width:125px" class="coderockz-woo-delivery-input-field coderockz_woo_delivery_offdays_year" maxlength="4" type="text" value="'.$year.'" placeholder="Year (ex. 2019)" style="vertical-align:top;" autocomplete="off" name="coderockz_woo_delivery_offdays_year_'.$year.'">';
											$offdays_html .= '<div style="display:inline-block;" class="coderockz_woo_delivery_offdays_another_month coderockz_woo_delivery_offdays_another_month_'.$year.'">';
											foreach($months as $month=>$date) {
												$offdays_html .= '<div class="coderockz_woo_delivery_offdays_add_another_month">';
												$offdays_html .= '<select style="width:125px!important" class="coderockz-woo-delivery-select-field" name="coderockz_woo_delivery_offdays_month_'.$year.'[]">';
												$offdays_html .= '<option value="">Select Month</option>';
												foreach($month_array as $single_month) {
													$single_month == $month ? $selected = "selected" : $selected = "";
													$offdays_html .= '<option value="'.$single_month.'"'.$selected.'>'.ucfirst($single_month).'</option>';
												}
												$offdays_html .= '</select>';
												$offdays_html .= '<input id="coderockz_woo_delivery_offdays_dates" type="text" class="coderockz-woo-delivery-input-field" value="'.$date.'" placeholder="'.__('Comma(,) Separated Date', 'coderockz-woo-delivery').'" style="width:200px;vertical-align:top;" autocomplete="off" name="coderockz_woo_delivery_offdays_dates_'.$month.'_'.$year.'">';
												if(array_keys($months)[0] != $month) {
													
													$offdays_html .= '<button class="coderockz-offdays-month-remove"><span class="dashicons dashicons-trash"></span></button>';
												}
												$offdays_html .= '</div>';
											}
											$offdays_html .= '</div>';
											$offdays_html .= '<br>
												    	  <span style="position:relative;left:18%">
														    <input class="coderockz-woo-delivery-add-month-btn" type="button" value="'.__('Add Month', 'coderockz-woo-delivery').'">
														    <div class="coderockz-woo-delivery-dummy-btn" style="position:absolute; left:0; right:0; top:0; bottom:0; cursor: pointer;"></div>
														  </span>';
											
											$offdays_html .= '</div>';
										}
										echo $offdays_html;
									} else {
	                        	?>

							    <div class="coderockz-woo-delivery-add-year-html coderockz-woo-delivery-form-group">
							    	<img class="coderockz-arrow" src="<?php echo CODEROCKZ_WOO_DELIVERY_URL ?>/admin/images/arrow.png" alt="" style="width: 20px;vertical-align: top;margin-top: 12px;margin-right: 15px;">
							        <input style="width:125px" class="coderockz-woo-delivery-input-field coderockz_woo_delivery_offdays_year" maxlength="4" type="text" value="<?php  ?>" placeholder="Year (ex. 2019)" style="vertical-align:top;" autocomplete="off"/>
							        <div class="coderockz_woo_delivery_offdays_another_month" style="display:inline-block;">
								        <div class="coderockz_woo_delivery_offdays_add_another_month">
									        <select style="width:125px!important" class="coderockz-woo-delivery-select-field" disabled="disabled">
									        	<option value="">Select Month</option>
									        	<?php
									        	$month_array = ['january','february','march','april','may','june','july','august','september','october','november','december'];
									        	foreach($month_array as $single_month) {
													echo '<option value="'.$single_month.'">'.ucfirst($single_month).'</option>';
												}
									        	?>
									            
										    </select>
									        <input style="width:200px" id="coderockz_woo_delivery_offdays_dates" type="text" class="coderockz-woo-delivery-input-field" value="<?php  ?>" placeholder="<?php _e('Comma(,) Separated Date', 'coderockz-woo-delivery'); ?>" style="vertical-align:top;" autocomplete="off" disabled="disabled"/>
								    	</div>
							    	</div>
							    	<br/>
							    	<span style="position:relative;left:18%">
									  <input class="coderockz-woo-delivery-add-month-btn" type="button" value="<?php _e('Add Month', 'coderockz-woo-delivery'); ?>" disabled="disabled">
									  <div class="coderockz-woo-delivery-dummy-btn" style="position:absolute; left:0; right:0; top:0; bottom:0; cursor: pointer;"></div>
									</span>


							    </div>
								<?php } ?>
							</div>
	                        <input class="coderockz-woo-delivery-submit-btn" type="submit" name="coderockz_delivery_date_offdays_form_submit" value="<?php _e('Save Changes', 'coderockz-woo-delivery'); ?>" />

	                    </form>
                	</div>

                </div>
				<div class="coderockz-woo-delivery-card">
					<p class="coderockz-woo-delivery-card-header"><?php _e('Pickup Off Days', 'coderockz-woo-delivery'); ?></p>
					<div class="coderockz-woo-delivery-card-body">
						<p class="coderockz-woo-delivery-date-tab-pickup-offdays-notice"></p>
						<input class="coderockz-woo-delivery-pickup-add-year-btn" type="button" value="<?php _e('Add New Year', 'coderockz-woo-delivery'); ?>">
	                    <form action="" method="post" id ="coderockz_delivery_date_pickup_offdays_form_submit">
	                        <?php wp_nonce_field('coderockz_woo_delivery_nonce'); ?>
	                        <div id="coderockz-woo-delivery-pickup-offdays" class="coderockz-woo-delivery-pickup-offdays">
							    
	                        	<?php
	                        		$month_array = ['january','february','march','april','may','june','july','august','september','october','november','december'];
									$offdays_html = "";
									$offdays_years = $pickup_date_settings;
									if(isset($offdays_years['pickup_off_days']) && !empty($offdays_years['pickup_off_days'])) {
										foreach($offdays_years['pickup_off_days'] as $year=>$months) {
											
											$offdays_html .= '<div class="coderockz-woo-delivery-pickup-add-year-html coderockz-woo-delivery-form-group">';
											if(array_keys($offdays_years['pickup_off_days'])[0] == $year) {
												$offdays_html .= '<img class="coderockz-arrow" src="'. CODEROCKZ_WOO_DELIVERY_URL .'/admin/images/arrow.png" alt="" style="width: 20px;vertical-align: top;margin-top: 12px;margin-right: 15px;">';	

											} else {
												$offdays_html .= '<button class="coderockz-pickup-offdays-year-remove"><span class="dashicons dashicons-trash"></span></button>';
											}
											
											$offdays_html .= '<input style="width:125px" class="coderockz-woo-delivery-input-field coderockz_woo_delivery_pickup_offdays_year" maxlength="4" type="text" value="'.$year.'" placeholder="Year (ex. 2019)" style="vertical-align:top;" autocomplete="off" name="coderockz_woo_delivery_pickup_offdays_year_'.$year.'">';
											$offdays_html .= '<div style="display:inline-block;" class="coderockz_woo_delivery_pickup_offdays_another_month coderockz_woo_delivery_pickup_offdays_another_month_'.$year.'">';
											foreach($months as $month=>$date) {
												$offdays_html .= '<div class="coderockz_woo_delivery_pickup_offdays_add_another_month">';
												$offdays_html .= '<select style="width:125px!important" class="coderockz-woo-delivery-select-field" name="coderockz_woo_delivery_pickup_offdays_month_'.$year.'[]">';
												$offdays_html .= '<option value="">Select Month</option>';
												foreach($month_array as $single_month) {
													$single_month == $month ? $selected = "selected" : $selected = "";
													$offdays_html .= '<option value="'.$single_month.'"'.$selected.'>'.ucfirst($single_month).'</option>';
												}
												$offdays_html .= '</select>';
												$offdays_html .= '<input id="coderockz_woo_delivery_pickup_offdays_dates" type="text" class="coderockz-woo-delivery-input-field" value="'.$date.'" placeholder="'.__('Comma(,) Separated Date', 'coderockz-woo-delivery').'" style="width:200px;vertical-align:top;" autocomplete="off" name="coderockz_woo_delivery_pickup_offdays_dates_'.$month.'_'.$year.'">';
												if(array_keys($months)[0] != $month) {
													
													$offdays_html .= '<button class="coderockz-pickup-offdays-month-remove"><span class="dashicons dashicons-trash"></span></button>';
												}
												$offdays_html .= '</div>';
											}
											$offdays_html .= '</div>';
											$offdays_html .= '<br>
												    	  <span style="position:relative;left:18%">
														    <input class="coderockz-woo-delivery-pickup-add-month-btn" type="button" value="'.__('Add Month', 'coderockz-woo-delivery').'">
														    <div class="coderockz-woo-delivery-pickup-dummy-btn" style="position:absolute; left:0; right:0; top:0; bottom:0; cursor: pointer;"></div>
														  </span>';
											
											$offdays_html .= '</div>';
										}
										echo $offdays_html;
									} else {
	                        	?>

							    <div class="coderockz-woo-delivery-pickup-add-year-html coderockz-woo-delivery-form-group">
							    	<img class="coderockz-arrow" src="<?php echo CODEROCKZ_WOO_DELIVERY_URL ?>/admin/images/arrow.png" alt="" style="width: 20px;vertical-align: top;margin-top: 12px;margin-right: 15px;">
							        <input style="width:125px" class="coderockz-woo-delivery-input-field coderockz_woo_delivery_pickup_offdays_year" maxlength="4" type="text" value="<?php  ?>" placeholder="Year (ex. 2019)" style="vertical-align:top;" autocomplete="off"/>
							        <div class="coderockz_woo_delivery_pickup_offdays_another_month" style="display:inline-block;">
								        <div class="coderockz_woo_delivery_pickup_offdays_add_another_month">
									        <select style="width:125px!important" class="coderockz-woo-delivery-select-field" disabled="disabled">
									        	<option value="">Select Month</option>
									        	<?php
									        	$month_array = ['january','february','march','april','may','june','july','august','september','october','november','december'];
									        	foreach($month_array as $single_month) {
													echo '<option value="'.$single_month.'">'.ucfirst($single_month).'</option>';
												}
									        	?>
									            
										    </select>
									        <input style="width:200px" id="coderockz_woo_delivery_pickup_offdays_dates" type="text" class="coderockz-woo-delivery-input-field" value="<?php  ?>" placeholder="<?php _e('Comma(,) Separated Date', 'coderockz-woo-delivery'); ?>" style="vertical-align:top;" autocomplete="off" disabled="disabled"/>
								    	</div>
							    	</div>
							    	<br/>
							    	<span style="position:relative;left:18%">
									  <input class="coderockz-woo-delivery-pickup-add-month-btn" type="button" value="<?php _e('Add Month', 'coderockz-woo-delivery'); ?>" disabled="disabled">
									  <div class="coderockz-woo-delivery-pickup-dummy-btn" style="position:absolute; left:0; right:0; top:0; bottom:0; cursor: pointer;"></div>
									</span>


							    </div>
								<?php } ?>
							</div>
	                        <input class="coderockz-woo-delivery-submit-btn" type="submit" name="coderockz_delivery_date_pickup_offdays_form_submit" value="<?php _e('Save Changes', 'coderockz-woo-delivery'); ?>" />

	                    </form>
                	</div>

                </div>
                <div class="coderockz-woo-delivery-card">
					<p class="coderockz-woo-delivery-card-header"><?php _e('Next Week Off', 'coderockz-woo-delivery'); ?></p>
					<div class="coderockz-woo-delivery-card-body">
						<p class="coderockz-woo-delivery-next-week-off-settings-notice"></p>
						<p class="coderockz-woo-delivery-timezone-tab-warning"><span class="dashicons dashicons-megaphone"></span><?php _e(' "Week Starts From" from the delivery/pickup date tab is count as the first day of the week. Default is Sunday', 'coderockz-woo-delivery'); ?></p>
	                    <form action="" method="post" id ="coderockz_woo_delivery_next_week_off_settings_form_submit">
	                        <?php wp_nonce_field('coderockz_woo_delivery_nonce'); ?>
	                        
	                        <div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Next Week Off Categories', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Select the categories for which you don't want to give the facility of delivery/pickup for the next week. Customer only select current week date as delivery/pickup date."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<select id="coderockz_woo_delivery_next_week_off_categories" name="coderockz_woo_delivery_next_week_off_categories[]" class="coderockz_woo_delivery_next_week_off_categories" multiple>
                                
                                <?php

                                $next_week_off_categories = [];
								if(isset($offdays_settings['next_week_off_categories']) && !empty($offdays_settings['next_week_off_categories'])) {
									foreach ($offdays_settings['next_week_off_categories'] as $hide_cat) {
										$next_week_off_categories[] = stripslashes($hide_cat);
									}
								}
                                foreach ($all_categories as $cat) {

                                	$selected = isset($offdays_settings['next_week_off_categories']) && !empty($offdays_settings['next_week_off_categories']) && in_array(htmlspecialchars_decode($cat->name),$next_week_off_categories) ? "selected" : "";
                                    echo '<option value="'.$cat->name.'" '.$selected.'>'.$cat->name.'</option>';
                                }
                                ?>
                                </select>
	                    	</div>

	                        <input class="coderockz-woo-delivery-submit-btn" type="submit" name="coderockz_woo_delivery_next_week_off_settings_form_submit" value="<?php _e('Save Changes', 'coderockz-woo-delivery'); ?>" />
	                    </form>
                	</div>

                </div>
                <div class="coderockz-woo-delivery-card">
					<p class="coderockz-woo-delivery-card-header"><?php _e('Next Month Off', 'coderockz-woo-delivery'); ?></p>
					<div class="coderockz-woo-delivery-card-body">
						<p class="coderockz-woo-delivery-next-month-off-settings-notice"></p>
	                    <form action="" method="post" id ="coderockz_woo_delivery_next_month_off_settings_form_submit">
	                        <?php wp_nonce_field('coderockz_woo_delivery_nonce'); ?>
	                        
	                        <div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Next Month Off Categories', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Select the categories for which you don't want to give the facility of delivery/pickup for the next month. Customer only select current month date as delivery/pickup date."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<select id="coderockz_woo_delivery_next_month_off_categories" name="coderockz_woo_delivery_next_month_off_categories[]" class="coderockz_woo_delivery_next_month_off_categories" multiple>
                                
                                <?php

                                $next_month_off_categories = [];
								if(isset($offdays_settings['next_month_off_categories']) && !empty($offdays_settings['next_month_off_categories'])) {
									foreach ($offdays_settings['next_month_off_categories'] as $hide_cat) {
										$next_month_off_categories[] = stripslashes($hide_cat);
									}
								}
                                foreach ($all_categories as $cat) {

                                	$selected = isset($offdays_settings['next_month_off_categories']) && !empty($offdays_settings['next_month_off_categories']) && in_array(htmlspecialchars_decode($cat->name),$next_month_off_categories) ? "selected" : "";
                                    echo '<option value="'.$cat->name.'" '.$selected.'>'.$cat->name.'</option>';
                                }
                                ?>
                                </select>
	                    	</div>

	                        <input class="coderockz-woo-delivery-submit-btn" type="submit" name="coderockz_woo_delivery_next_month_off_settings_form_submit" value="<?php _e('Save Changes', 'coderockz-woo-delivery'); ?>" />
	                    </form>
                	</div>

                </div>
                <div class="coderockz-woo-delivery-card">
					<p class="coderockz-woo-delivery-card-header"><?php _e('Category Wise Off Days', 'coderockz-woo-delivery'); ?></p>
					<div class="coderockz-woo-delivery-card-body">
						<p class="coderockz-woo-delivery-category-wise-offdays-notice"></p>
						
	                    <form action="" method="post" id ="coderockz_category_wise_offdays_form_submit">
	                        <?php wp_nonce_field('coderockz_woo_delivery_nonce'); ?>

	                        <div class="coderockz-woo-delivery-category-wise-offdays">

	                        <?php
	                        $category_wise_offdays_html = "";
	                        
							if(isset($offdays_settings['category_wise_offdays']) && !empty($offdays_settings['category_wise_offdays'])) {
								foreach($offdays_settings['category_wise_offdays'] as $category => $days) {
									$category_wise_offdays_html .= '<div class="coderockz-woo-delivery-form-group">';
							        $category_wise_offdays_html .= '<img class="coderockz-arrow" src="'.CODEROCKZ_WOO_DELIVERY_URL.'/admin/images/arrow.png" alt="" style="width: 20px;vertical-align: top;margin-top: 12px;margin-right: 15px;">';
									$category_wise_offdays_html .= '<select class="coderockz-woo-delivery-select-field" name="coderockz_delivery_category_wise_offdays_category[]">
								    <option value="">'.__('Select Category', 'coderockz-woo-delivery').'</option>';
									foreach ($all_categories as $cat) {
										$selected = (htmlspecialchars_decode($cat->name) == stripslashes($category)) ? "selected" : "";
										$category_wise_offdays_html .= '<option value="'.str_replace(" ","--",$cat->name).'"'.$selected.'>'.$cat->name.'</option>';
									}
									$category_wise_offdays_html .= '</select>';
									$category_wise_offdays_html .= '<select name="coderockz-delivery-category-wise-offdays-category-weekday-'.str_replace(" ","--",$category).'[]" class="coderockz_delivery_category_wise_offdays_category_weekday" placeholder="'.__('Disable Delivery Days', 'coderockz-woo-delivery').'" multiple>';

	                                $weekday = array("0"=>"Sunday", "1"=>"Monday", "2"=>"Tuesday", "3"=>"Wednesday", "4"=>"Thursday", "5"=>"Friday", "6"=>"Saturday");
	                                foreach ($weekday as $key => $value) {
	                                	$selected = isset($offdays_settings['category_wise_offdays'][$category]['weekday_offdays']) && !empty($offdays_settings['category_wise_offdays'][$category]['weekday_offdays']) && in_array($key,$offdays_settings['category_wise_offdays'][$category]['weekday_offdays']) ? "selected" : "";
	                                    $category_wise_offdays_html .= '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';		
	                                }

	                                $category_wise_offdays_html .= '</select>';
	                                $specific_date_offdays = isset($offdays_settings['category_wise_offdays'][$category]['specific_date_offdays']) && !empty($offdays_settings['category_wise_offdays'][$category]['specific_date_offdays']) ? implode(",",$offdays_settings['category_wise_offdays'][$category]['specific_date_offdays']) : "";
	                                $category_wise_offdays_html .= '<input type="text" class="coderockz_delivery_category_wise_offdays_category_specific_date coderockz-woo-delivery-input-field" name="coderockz-delivery-category-wise-offdays-category-specific-date-'.str_replace(" ","--",$category).'" value="'.$specific_date_offdays.'" style="vertical-align:top;width: 430px!important;" autocomplete="off" placeholder="'.__('Comma(,) Separated Date, format yyyy-mm-dd(Ex. 2020-12-24)', 'coderockz-woo-delivery').'"/>';
									if(array_keys($offdays_settings['category_wise_offdays'])[0] != $category){
										$category_wise_offdays_html .= '<button class="coderockz-woo-delivery-category-wise-offdays-remove"><span class="dashicons dashicons-trash"></span></button>';
									}
									
							        $category_wise_offdays_html .= '</div>';
								}
								echo $category_wise_offdays_html;
							} else {

	                        ?>
		                        <div class="coderockz-woo-delivery-form-group">
		                        	<img class="coderockz-arrow" src="<?php echo CODEROCKZ_WOO_DELIVERY_URL ?>/admin/images/arrow.png" alt="" style="width: 20px;vertical-align: top;margin-top: 12px;margin-right: 15px;">
								    <select class="coderockz-woo-delivery-select-field" name="coderockz_delivery_category_wise_offdays_category[]" autocomplete="off">
								    <option value=""><?php _e('Select Category', 'coderockz-woo-delivery'); ?></option>
									<?php
	                                foreach ($all_categories as $cat) {
										echo '<option value="'.str_replace(" ","--",$cat->name).'">'.$cat->name.'</option>';
									}
	                                ?>
									</select>
								    <select name="coderockz_delivery_category_wise_offdays_category_weekday[]" class="coderockz_delivery_category_wise_offdays_category_weekday" placeholder="<?php _e('Disable Delivery Days', 'coderockz-woo-delivery'); ?>" multiple  autocomplete="off">
	                                <?php
	                                $weekday = array("0"=>"Sunday", "1"=>"Monday", "2"=>"Tuesday", "3"=>"Wednesday", "4"=>"Thursday", "5"=>"Friday", "6"=>"Saturday");
	                                foreach ($weekday as $key => $value) {
	                                    echo '<option value="'.$key.'">'.$value.'</option>';		
	                                }
	                                ?>
	                                </select>
	                                <input type="text" class="coderockz-woo-delivery-input-field coderockz_delivery_category_wise_offdays_category_specific_date" name="coderockz_delivery_category_wise_offdays_category_specific_date" style="vertical-align:top;width: 430px!important;" autocomplete="off" placeholder="<?php _e('Comma(,) Separated Date, format yyyy-mm-dd(Ex. 2020-12-24)', 'coderockz-woo-delivery'); ?>"/>
		                    	</div>
		                    <?php } ?>
	                    	</div>
	                    	<button class="coderockz-woo-delivery-add-category-wise-offdays-btn"><span class="dashicons dashicons-plus"></span></button>
	                        <input class="coderockz-woo-delivery-submit-btn" type="submit" name="coderockz_category_wise_offdays_form_submit" value="<?php _e('Save Changes', 'coderockz-woo-delivery'); ?>" />
	                    </form>
                	</div>

                </div>
                <div class="coderockz-woo-delivery-card">
					<p class="coderockz-woo-delivery-card-header"><?php _e('Product Wise Off Days', 'coderockz-woo-delivery'); ?></p>
					<div class="coderockz-woo-delivery-card-body">
						<?php if(get_option('coderockz_woo_delivery_large_product_list') !== false) { ?>
							<p class="coderockz-woo-delivery-timezone-tab-warning"><span class="dashicons dashicons-megaphone"></span><?php _e('One ID per line. If product is simple input the product ID and if product is variable input the variation ID', 'coderockz-woo-delivery'); ?></p>
						<?php } ?>
						<p class="coderockz-woo-delivery-product-wise-offdays-notice"></p>
						
	                    <form action="" method="post" id ="coderockz_product_wise_offdays_form_submit">
	                        <?php wp_nonce_field('coderockz_woo_delivery_nonce'); ?>
	                        <?php if(get_option('coderockz_woo_delivery_large_product_list') == false) { ?>
	                        
	                        <div class="coderockz-woo-delivery-product-wise-offdays">

	                        <?php
	                        $product_wise_offdays_html = "";
							if(isset($offdays_settings['product_wise_offdays']) && !empty($offdays_settings['product_wise_offdays'])) {
								foreach($offdays_settings['product_wise_offdays'] as $product => $days) {
									$product_wise_offdays_html .= '<div class="coderockz-woo-delivery-form-group">';
							        $product_wise_offdays_html .= '<img class="coderockz-arrow" src="'.CODEROCKZ_WOO_DELIVERY_URL.'/admin/images/arrow.png" alt="" style="width: 20px;vertical-align: top;margin-top: 12px;margin-right: 15px;">';
									$product_wise_offdays_html .= '<select class="coderockz-woo-delivery-select-field" name="coderockz_delivery_product_wise_offdays_product[]">
								    <option value="">'.__('Select Product', 'coderockz-woo-delivery').'</option>';
									foreach ($store_products as $key=>$value) {
										$selected = ($key == $product) ? "selected" : "";
										$product_wise_offdays_html .= '<option value="'.$key.'"'.$selected.'>'.$value.'</option>';
									}
									$product_wise_offdays_html .= '</select>';
									$product_wise_offdays_html .= '<select name="coderockz-delivery-product-wise-offdays-product-weekday-'.str_replace(" ","--",$product).'[]" class="coderockz_delivery_product_wise_offdays_product_weekday" placeholder="'.__('Disable Delivery Days', 'coderockz-woo-delivery').'" multiple>';

	                                $weekday = array("0"=>"Sunday", "1"=>"Monday", "2"=>"Tuesday", "3"=>"Wednesday", "4"=>"Thursday", "5"=>"Friday", "6"=>"Saturday");
	                                foreach ($weekday as $key => $value) {
	                                	$selected = isset($offdays_settings['product_wise_offdays']) && !empty($offdays_settings['product_wise_offdays']) && in_array($key,$offdays_settings['product_wise_offdays'][$product]) ? "selected" : "";
	                                    $product_wise_offdays_html .= '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';		
	                                }

	                                $product_wise_offdays_html .= '</select>';
									if(array_keys($offdays_settings['product_wise_offdays'])[0] != $product){
										$product_wise_offdays_html .= '<button class="coderockz-woo-delivery-product-wise-offdays-remove"><span class="dashicons dashicons-trash"></span></button>';
									}
									
							        $product_wise_offdays_html .= '</div>';
								}
								echo $product_wise_offdays_html;
							} else {

	                        ?>
		                        <div class="coderockz-woo-delivery-form-group">
		                        	<img class="coderockz-arrow" src="<?php echo CODEROCKZ_WOO_DELIVERY_URL ?>/admin/images/arrow.png" alt="" style="width: 20px;vertical-align: top;margin-top: 12px;margin-right: 15px;">
								    <select class="coderockz-woo-delivery-select-field" name="coderockz_delivery_product_wise_offdays_product[]" autocomplete="off">
								    <option value="">Select product</option>
									<?php
	                                foreach ($store_products as $key=>$value) {
										echo '<option value="'.$key.'">'.$value.'</option>';
									}
	                                ?>
									</select>
								    <select name="coderockz_delivery_product_wise_offdays_product_weekday[]" class="coderockz_delivery_product_wise_offdays_product_weekday" placeholder="<?php _e('Disable Delivery Days', 'coderockz-woo-delivery'); ?>" multiple  autocomplete="off">
	                                <?php
	                                $weekday = array("0"=>"Sunday", "1"=>"Monday", "2"=>"Tuesday", "3"=>"Wednesday", "4"=>"Thursday", "5"=>"Friday", "6"=>"Saturday");
	                                foreach ($weekday as $key => $value) {
	                                    echo '<option value="'.$key.'">'.$value.'</option>';		
	                                }
	                                ?>
	                                </select>
		                    	</div>
		                    <?php } ?>
	                    	</div>
	                    	<?php } else { ?>


	                    	<div class="coderockz-woo-delivery-product-wise-offdays">

	                        <?php
	                        $product_wise_offdays_html = "";
							if(isset($offdays_settings['product_wise_offdays']) && !empty($offdays_settings['product_wise_offdays'])) {
								foreach($offdays_settings['product_wise_offdays'] as $product => $days) {
									$product_wise_offdays_html .= '<div class="coderockz-woo-delivery-form-group">';
							        $product_wise_offdays_html .= '<img class="coderockz-arrow" src="'.CODEROCKZ_WOO_DELIVERY_URL.'/admin/images/arrow.png" alt="" style="width: 20px;vertical-align: top;margin-top: 12px;margin-right: 15px;">';
									$product_wise_offdays_html .= '<input name="coderockz_delivery_product_wise_offdays_product_input" type="text" class="coderockz-woo-delivery-input-field" value="'.$product.'" placeholder="Product/Variation ID" autocomplete="off"/>';
									$product_wise_offdays_html .= '<select name="coderockz-delivery-product-wise-offdays-product-weekday-'.str_replace(" ","--",$product).'[]" class="coderockz_delivery_product_wise_offdays_product_weekday" placeholder="'.__('Disable Delivery Days', 'coderockz-woo-delivery').'" multiple>';

	                                $weekday = array("0"=>"Sunday", "1"=>"Monday", "2"=>"Tuesday", "3"=>"Wednesday", "4"=>"Thursday", "5"=>"Friday", "6"=>"Saturday");
	                                foreach ($weekday as $key => $value) {
	                                	$selected = isset($offdays_settings['product_wise_offdays']) && !empty($offdays_settings['product_wise_offdays']) && in_array($key,$offdays_settings['product_wise_offdays'][$product]) ? "selected" : "";
	                                    $product_wise_offdays_html .= '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';		
	                                }

	                                $product_wise_offdays_html .= '</select>';
									if(array_keys($offdays_settings['product_wise_offdays'])[0] != $product){
										$product_wise_offdays_html .= '<button class="coderockz-woo-delivery-product-wise-offdays-remove"><span class="dashicons dashicons-trash"></span></button>';
									}
									
							        $product_wise_offdays_html .= '</div>';
								}
								echo $product_wise_offdays_html;
							} else {

	                        ?>
		                        <div class="coderockz-woo-delivery-form-group">
		                        	<img class="coderockz-arrow" src="<?php echo CODEROCKZ_WOO_DELIVERY_URL ?>/admin/images/arrow.png" alt="" style="width: 20px;vertical-align: top;margin-top: 12px;margin-right: 15px;">
									<input name="coderockz_delivery_product_wise_offdays_product_input" type="text" class="coderockz-woo-delivery-input-field" placeholder="Product/Variation ID" autocomplete="off"/>
								    <select name="coderockz_delivery_product_wise_offdays_product_weekday[]" class="coderockz_delivery_product_wise_offdays_product_weekday" placeholder="<?php _e('Disable Delivery Days', 'coderockz-woo-delivery'); ?>" multiple  autocomplete="off">
	                                <?php
	                                $weekday = array("0"=>"Sunday", "1"=>"Monday", "2"=>"Tuesday", "3"=>"Wednesday", "4"=>"Thursday", "5"=>"Friday", "6"=>"Saturday");
	                                foreach ($weekday as $key => $value) {
	                                    echo '<option value="'.$key.'">'.$value.'</option>';		
	                                }
	                                ?>
	                                </select>
		                    	</div>
		                    <?php } ?>
	                    	</div>

	                    	<?php } ?>
	                    	<button class="coderockz-woo-delivery-add-product-wise-offdays-btn"><span class="dashicons dashicons-plus"></span></button>
	                        <input class="coderockz-woo-delivery-submit-btn" type="submit" name="coderockz_product_wise_offdays_form_submit" value="<?php _e('Save Changes', 'coderockz-woo-delivery'); ?>" />
	                    </form>
                	</div>

                </div>
                <div class="coderockz-woo-delivery-card">
					<p class="coderockz-woo-delivery-card-header"><?php _e('Shipping Zone Wise Off Days', 'coderockz-woo-delivery'); ?></p>
					<div class="coderockz-woo-delivery-card-body">
						<p class="coderockz-woo-delivery-zone-wise-offdays-notice"></p>
						<p class="coderockz-woo-delivery-timezone-tab-warning"><span class="dashicons dashicons-megaphone"></span><?php _e(' If you add more states or postcodes in the Zone from WooCommerce shipping settings that you have already used to make Zone wise off days, Then you have to again Save Changes here.', 'coderockz-woo-delivery'); ?></p>
	                    <form action="" method="post" id ="coderockz_zone_wise_offdays_form_submit">
	                        <?php wp_nonce_field('coderockz_woo_delivery_nonce'); ?>

	                        <!-- <div class="coderockz-woo-delivery-zone-wise-offdays"> -->

	                        <?php
	                        $zone_wise_offdays_html = "";
	                        $applicable_for = array("both"=>"For Both", "delivery"=>"For Delivery", "pickup"=>"For Pickup");
	                        $i = 1;
	                        foreach ($applicable_for as $applicable_key => $applicable_value) {
							if(isset($offdays_settings['zone_wise_offdays'][$applicable_key]) && !empty($offdays_settings['zone_wise_offdays'][$applicable_key])) {
								foreach($offdays_settings['zone_wise_offdays'][$applicable_key] as $zone => $days) {
									$zone_wise_offdays_html .= '<div class="coderockz-woo-delivery-zone-wise-offdays">';
									$zone_wise_offdays_html .= '<div class="coderockz-woo-delivery-form-group">';
							        $zone_wise_offdays_html .= '<img class="coderockz-arrow" src="'.CODEROCKZ_WOO_DELIVERY_URL.'/admin/images/arrow.png" alt="" style="width: 20px;vertical-align: top;margin-top: 12px;margin-right: 15px;">';
									$zone_wise_offdays_html .= '<select class="coderockz-woo-delivery-select-field coderockz_delivery_zone_wise_offdays_zone" name="coderockz_delivery_zone_wise_offdays_zone[]">
								    <option value="">Select Shipping Zone</option>';
									foreach ($zone_name as $key => $value) {
										$selected = ($key == $zone) ? "selected" : "";
										$zone_wise_offdays_html .= '<option value="'.str_replace(" ","--",$key).'"'.$selected.'>'.$value.'</option>';
									}
									$zone_wise_offdays_html .= '</select>';
									$zone_wise_offdays_html .= '<select name="coderockz-delivery-zone-wise-offdays-zone-weekday-'.str_replace(" ","--",$zone).'-'.$applicable_key.'[]" class="coderockz_delivery_zone_wise_offdays_zone_weekday" placeholder="'.__('Disable Delivery Days', 'coderockz-woo-delivery').'" multiple>';

	                                $weekday = array("0"=>"Sunday", "1"=>"Monday", "2"=>"Tuesday", "3"=>"Wednesday", "4"=>"Thursday", "5"=>"Friday", "6"=>"Saturday");
	                                if(isset($offdays_settings['zone_wise_offdays'][$applicable_key][$zone]['off_days']) && $offdays_settings['zone_wise_offdays'][$applicable_key][$zone]['off_days'] !="") {
	                                	$zone_offdays = explode(",",$offdays_settings['zone_wise_offdays'][$applicable_key][$zone]['off_days']);
	                                } else {
	                                	$zone_offdays = array();
	                                }
	                                
	                                foreach ($weekday as $key => $value) {
	                                	
	                                	if(in_array($key,$zone_offdays)) {
	                                		$zone_wise_offdays_html .= '<option value="'.$key.'" selected>'.$value.'</option>';

	                                	} else {
	                                		$zone_wise_offdays_html .= '<option value="'.$key.'">'.$value.'</option>';
	                                	}
	                                    		
	                                }

	                                $zone_wise_offdays_html .= '</select>';
	                                $zone_wise_offdays_html .= '<select class="coderockz-woo-delivery-select-field coderockz_delivery_zone_wise_offdays_applicable_for" name="coderockz_delivery_zone_wise_offdays_applicable_for_'.str_replace(" ","--",$zone).'_'.$applicable_key.'" autocomplete="off">';
	                                
	                                /*$selected_applicable_for = isset($offdays_settings['zone_wise_offdays'][$zone]['applicable_for']) && $offdays_settings['zone_wise_offdays'][$zone]['applicable_for'] !="" ? $offdays_settings['zone_wise_offdays'][$zone]['applicable_for'] : "both";*/
	                                foreach ($applicable_for as $key => $value) {
	                                	if($key == $applicable_key) {
	                                    	$zone_wise_offdays_html .= '<option value="'.$key.'" selected='.$selected.'>'.$value.'</option>';
	                                    } else {
	                                    	$zone_wise_offdays_html .= '<option value="'.$key.'">'.$value.'</option>';
	                                    }		
	                                }
									$zone_wise_offdays_html .= '</select>';
									if($i!=1){
										$zone_wise_offdays_html .= '<button class="coderockz-woo-delivery-zone-wise-offdays-remove"><span class="dashicons dashicons-trash"></span></button>';
									}

									$i = $i+1;
									
							        $zone_wise_offdays_html .= '</div>';
							        $zone_wise_offdays_html .= '</div>';
								}

							}

							}

							if($zone_wise_offdays_html != "") {
								echo $zone_wise_offdays_html;
							} else {

	                        ?>
		                        <div class="coderockz-woo-delivery-zone-wise-offdays">
		                        <div class="coderockz-woo-delivery-form-group">
		                        	<img class="coderockz-arrow" src="<?php echo CODEROCKZ_WOO_DELIVERY_URL ?>/admin/images/arrow.png" alt="" style="width: 20px;vertical-align: top;margin-top: 12px;margin-right: 15px;">
								    <select class="coderockz-woo-delivery-select-field coderockz_delivery_zone_wise_offdays_zone" name="coderockz_delivery_zone_wise_offdays_zone[]" autocomplete="off">
								    <option value="">Select Shipping Zone</option>
									<?php
	                                foreach ($zone_name as $key => $value) {
	                                    echo '<option value="'.str_replace(" ","--",$key).'">'.$value.'</option>';
	                                }
	                                ?>
									</select>
								    <select id="coderockz_delivery_zone_wise_offdays_zone_weekday" name="coderockz_delivery_zone_wise_offdays_zone_weekday[]" class="coderockz_delivery_zone_wise_offdays_zone_weekday" placeholder="<?php _e('Disable Delivery Days', 'coderockz-woo-delivery'); ?>" multiple  autocomplete="off">
	                                <?php
	                                $weekday = array("0"=>"Sunday", "1"=>"Monday", "2"=>"Tuesday", "3"=>"Wednesday", "4"=>"Thursday", "5"=>"Friday", "6"=>"Saturday");
	                                foreach ($weekday as $key => $value) {
	                                    echo '<option value="'.$key.'">'.$value.'</option>';		
	                                }
	                                ?>
	                                </select>
	                                <select class="coderockz-woo-delivery-select-field coderockz_delivery_zone_wise_offdays_applicable_for" name="coderockz_delivery_zone_wise_offdays_applicable_for[]" autocomplete="off">
									<?php
	                                $applicable_for = array("both"=>"For Both", "delivery"=>"For Delivery", "pickup"=>"For Pickup");
	                                foreach ($applicable_for as $key => $value) {
	                                    echo '<option value="'.$key.'">'.$value.'</option>';		
	                                }
	                                ?>
									</select>
		                    	</div>
		                    	</div>
		                    <?php } ?>
	                    	
	                    	<button class="coderockz-woo-delivery-add-zone-wise-offdays-btn"><span class="dashicons dashicons-plus"></span></button>
	                        <input class="coderockz-woo-delivery-submit-btn" type="submit" name="coderockz_zone_wise_offdays_form_submit" value="<?php _e('Save Changes', 'coderockz-woo-delivery'); ?>" />
	                    </form>
                	</div>

                </div>
                <div class="coderockz-woo-delivery-card">
					<p class="coderockz-woo-delivery-card-header"><?php _e('Shipping State Wise Off Days', 'coderockz-woo-delivery'); ?></p>
					<div class="coderockz-woo-delivery-card-body">
						<p class="coderockz-woo-delivery-state-wise-offdays-notice"></p>
						
	                    <form action="" method="post" id ="coderockz_state_wise_offdays_form_submit">
	                        <?php wp_nonce_field('coderockz_woo_delivery_nonce'); ?>

	                        <div class="coderockz-woo-delivery-state-wise-offdays">

	                        <?php
	                        $state_wise_offdays_html = "";
							if(isset($offdays_settings['state_wise_offdays']) && !empty($offdays_settings['state_wise_offdays'])) {
								foreach($offdays_settings['state_wise_offdays'] as $state => $days) {
									$state_wise_offdays_html .= '<div class="coderockz-woo-delivery-form-group">';
							        $state_wise_offdays_html .= '<img class="coderockz-arrow" src="'.CODEROCKZ_WOO_DELIVERY_URL.'/admin/images/arrow.png" alt="" style="width: 20px;vertical-align: top;margin-top: 12px;margin-right: 15px;">';
									$state_wise_offdays_html .= '<select class="coderockz-woo-delivery-select-field" name="coderockz_delivery_state_wise_offdays_state[]">
								    <option value="">Select Shipping State</option>';
									foreach ($zone_regions as $key => $value) {
										$selected = ($key == $state) ? "selected" : "";
										$state_wise_offdays_html .= '<option value="'.str_replace(" ","--",$key).'" '.$selected.'>'.$value.'</option>';
									}
									$state_wise_offdays_html .= '</select>';
									$state_wise_offdays_html .= '<select name="coderockz-delivery-state-wise-offdays-state-weekday-'.str_replace(" ","--",$state).'[]" class="coderockz_delivery_state_wise_offdays_state_weekday" placeholder="'.__('Disable Delivery Days', 'coderockz-woo-delivery').'" multiple autocomplete="off">';

	                                $weekday = array("0"=>"Sunday", "1"=>"Monday", "2"=>"Tuesday", "3"=>"Wednesday", "4"=>"Thursday", "5"=>"Friday", "6"=>"Saturday");
	                                foreach ($weekday as $key => $value) {
	                                	$selected = isset($offdays_settings['state_wise_offdays']) && !empty($offdays_settings['state_wise_offdays']) && in_array($key,$offdays_settings['state_wise_offdays'][$state]) ? "selected" : "";
	                                    $state_wise_offdays_html .= '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';		
	                                }

	                                $state_wise_offdays_html .= '</select>';
									if(array_keys($offdays_settings['state_wise_offdays'])[0] != $state){
										$state_wise_offdays_html .= '<button class="coderockz-woo-delivery-state-wise-offdays-remove"><span class="dashicons dashicons-trash"></span></button>';
									}
									
							        $state_wise_offdays_html .= '</div>';
								}
								echo $state_wise_offdays_html;
							} else {

	                        ?>
		                        <div class="coderockz-woo-delivery-form-group">
		                        	<img class="coderockz-arrow" src="<?php echo CODEROCKZ_WOO_DELIVERY_URL ?>/admin/images/arrow.png" alt="" style="width: 20px;vertical-align: top;margin-top: 12px;margin-right: 15px;">
								    <select class="coderockz-woo-delivery-select-field" name="coderockz_delivery_state_wise_offdays_state[]" autocomplete="off">
								    <option value="">Select Shipping State</option>
									<?php
	                                foreach ($zone_regions as $key => $value) {
	                                	echo '<option value="'.str_replace(" ","--",$key).'">'.$value.'</option>';
	                                    
	                                }
	                                ?>
									</select>
								    <select name="coderockz_delivery_state_wise_offdays_state_weekday[]" class="coderockz_delivery_state_wise_offdays_state_weekday" placeholder="<?php _e('Disable Delivery Days', 'coderockz-woo-delivery'); ?>" multiple  autocomplete="off">
	                                <?php
	                                $weekday = array("0"=>"Sunday", "1"=>"Monday", "2"=>"Tuesday", "3"=>"Wednesday", "4"=>"Thursday", "5"=>"Friday", "6"=>"Saturday");
	                                foreach ($weekday as $key => $value) {
	                                    echo '<option value="'.$key.'">'.$value.'</option>';		
	                                }
	                                ?>
	                                </select>
		                    	</div>
		                    <?php } ?>
	                    	</div>
	                    	<button class="coderockz-woo-delivery-add-state-wise-offdays-btn"><span class="dashicons dashicons-plus"></span></button>
	                        <input class="coderockz-woo-delivery-submit-btn" type="submit" name="coderockz_state_wise_offdays_form_submit" value="<?php _e('Save Changes', 'coderockz-woo-delivery'); ?>" />
	                    </form>
                	</div>

                </div>

                <div class="coderockz-woo-delivery-card">
					<p class="coderockz-woo-delivery-card-header"><?php _e('Shipping PostCode Wise Off Days', 'coderockz-woo-delivery'); ?></p>
					<div class="coderockz-woo-delivery-card-body">
						<p class="coderockz-woo-delivery-postcode-wise-offdays-notice"></p>
						
	                    <form action="" method="post" id ="coderockz_postcode_wise_offdays_form_submit">
	                        <?php wp_nonce_field('coderockz_woo_delivery_nonce'); ?>

	                        <div class="coderockz-woo-delivery-postcode-wise-offdays">

	                        <?php
	                        $postcode_wise_offdays_html = "";
	                        
							if(isset($offdays_settings['postcode_wise_offdays']) && !empty($offdays_settings['postcode_wise_offdays'])) {
								foreach($offdays_settings['postcode_wise_offdays'] as $postcode => $days) {
									$postcode_wise_offdays_html .= '<div class="coderockz-woo-delivery-form-group">';
							        $postcode_wise_offdays_html .= '<img class="coderockz-arrow" src="'.CODEROCKZ_WOO_DELIVERY_URL.'/admin/images/arrow.png" alt="" style="width: 20px;vertical-align: top;margin-top: 12px;margin-right: 15px;">';
									$postcode_wise_offdays_html .= '<select class="coderockz-woo-delivery-select-field" name="coderockz_delivery_postcode_wise_offdays_postcode[]">
								    <option value="">Select Shipping Postcode</option>';
									foreach ($zone_post_code as $key => $value) {
										$selected = ($value == $postcode) ? "selected" : "";
										$postcode_wise_offdays_html .= '<option value="'.str_replace(array(" ","..."),array("--","___"),$value).'" '.$selected.'>'.$value.'</option>';
									}
									$postcode_wise_offdays_html .= '</select>';
									$postcode_wise_offdays_html .= '<select name="coderockz-delivery-postcode-wise-offdays-postcode-weekday-'.str_replace(" ","--",$postcode).'[]" class="coderockz_delivery_postcode_wise_offdays_postcode_weekday" placeholder="'.__('Disable Delivery Days', 'coderockz-woo-delivery').'" multiple autocomplete="off">';

	                                $weekday = array("0"=>"Sunday", "1"=>"Monday", "2"=>"Tuesday", "3"=>"Wednesday", "4"=>"Thursday", "5"=>"Friday", "6"=>"Saturday");
	                                foreach ($weekday as $key => $value) {
	                                	$selected = isset($offdays_settings['postcode_wise_offdays']) && !empty($offdays_settings['postcode_wise_offdays']) && in_array($key,$offdays_settings['postcode_wise_offdays'][$postcode]) ? "selected" : "";
	                                    $postcode_wise_offdays_html .= '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';		
	                                }

	                                $postcode_wise_offdays_html .= '</select>';
									if(array_keys($offdays_settings['postcode_wise_offdays'])[0] != $postcode){
										$postcode_wise_offdays_html .= '<button class="coderockz-woo-delivery-postcode-wise-offdays-remove"><span class="dashicons dashicons-trash"></span></button>';
									}
									
							        $postcode_wise_offdays_html .= '</div>';
								}
								echo $postcode_wise_offdays_html;
							} else {

	                        ?>
		                        <div class="coderockz-woo-delivery-form-group">
		                        	<img class="coderockz-arrow" src="<?php echo CODEROCKZ_WOO_DELIVERY_URL ?>/admin/images/arrow.png" alt="" style="width: 20px;vertical-align: top;margin-top: 12px;margin-right: 15px;">
								    <select class="coderockz-woo-delivery-select-field" name="coderockz_delivery_postcode_wise_offdays_postcode[]" autocomplete="off">
								    <option value="">Select Shipping Postcode</option>
									<?php

	                                foreach ($zone_post_code as $key => $value) {
	                                	echo '<option value="'.str_replace(array(" ","..."),array("--","___"),$value).'">'.$value.'</option>';
	                                    
	                                }
	                                ?>
									</select>
								    <select name="coderockz_delivery_postcode_wise_offdays_postcode_weekday[]" class="coderockz_delivery_postcode_wise_offdays_postcode_weekday" placeholder="<?php _e('Disable Delivery Days', 'coderockz-woo-delivery'); ?>" multiple autocomplete="off">
	                                <?php
	                                $weekday = array("0"=>"Sunday", "1"=>"Monday", "2"=>"Tuesday", "3"=>"Wednesday", "4"=>"Thursday", "5"=>"Friday", "6"=>"Saturday");
	                                foreach ($weekday as $key => $value) {
	                                    echo '<option value="'.$key.'">'.$value.'</option>';		
	                                }
	                                ?>
	                                </select>
		                    	</div>
		                    <?php } ?>
	                    	</div>
	                    	<button class="coderockz-woo-delivery-add-postcode-wise-offdays-btn"><span class="dashicons dashicons-plus"></span></button>
	                        <input class="coderockz-woo-delivery-submit-btn" type="submit" name="coderockz_postcode_wise_offdays_form_submit" value="<?php _e('Save Changes', 'coderockz-woo-delivery'); ?>" />
	                    </form>
                	</div>

                </div>
                
			</div>

			<div data-tab="tab8" class="coderockz-woo-delivery-tabcontent">
				<div class="coderockz-woo-delivery-card">
					<p class="coderockz-woo-delivery-card-header"><?php _e('Special Open Days For Delivery', 'coderockz-woo-delivery'); ?></p>
					<div class="coderockz-woo-delivery-card-body">
						<p class="coderockz-woo-delivery-date-tab-opendays-notice"></p>
						<p class="coderockz-woo-delivery-timezone-tab-warning"><span class="dashicons dashicons-megaphone"></span><?php _e(' If you want to enable a date that will  disable in future as a reason of weekends, zone wise offdays, state wise offdays or zip wise offdays, then specifiy the date here.', 'coderockz-woo-delivery'); ?></p>
						<input class="coderockz-woo-delivery-add-year-btn-delivery-opendays" type="button" value="<?php _e('Add New Year', 'coderockz-woo-delivery'); ?>">
	                    <form action="" method="post" id ="coderockz_delivery_date_delivery_opendays_form_submit">
	                        <?php wp_nonce_field('coderockz_woo_delivery_nonce'); ?>
	                        <div id="coderockz-woo-delivery-delivery-opendays" class="coderockz-woo-delivery-delivery-opendays">
							    
	                        	<?php
	                        		$month_array = ['january','february','march','april','may','june','july','august','september','october','november','december'];
									$opendays_html = "";
									$opendays_years = $date_settings;
									if(isset($opendays_years['open_days']) && !empty($opendays_years['open_days'])) {
										foreach($opendays_years['open_days'] as $year=>$months) {
											
											$opendays_html .= '<div class="coderockz-woo-delivery-add-year-html-delivery-opendays coderockz-woo-delivery-form-group">';
											if(array_keys($opendays_years['open_days'])[0] == $year) {
												$opendays_html .= '<img class="coderockz-arrow" src="'. CODEROCKZ_WOO_DELIVERY_URL .'/admin/images/arrow.png" alt="" style="width: 20px;vertical-align: top;margin-top: 12px;margin-right: 15px;">';	

											} else {
												$opendays_html .= '<button class="coderockz-delivery-opendays-year-remove"><span class="dashicons dashicons-trash"></span></button>';
											}
											
											$opendays_html .= '<input style="width:125px" class="coderockz-woo-delivery-input-field coderockz_woo_delivery_delivery_opendays_year" maxlength="4" type="text" value="'.$year.'" placeholder="Year (ex. 2019)" style="vertical-align:top;" autocomplete="off" name="coderockz_woo_delivery_delivery_opendays_year_'.$year.'">';
											$opendays_html .= '<div style="display:inline-block;" class="coderockz_woo_delivery_delivery_opendays_another_month coderockz_woo_delivery_delivery_opendays_another_month_'.$year.'">';
											foreach($months as $month=>$date) {
												$opendays_html .= '<div class="coderockz_woo_delivery_delivery_opendays_add_another_month">';
												$opendays_html .= '<select style="width:125px!important" class="coderockz-woo-delivery-select-field" name="coderockz_woo_delivery_delivery_opendays_month_'.$year.'[]">';
												$opendays_html .= '<option value="">Select Month</option>';
												foreach($month_array as $single_month) {
													$single_month == $month ? $selected = "selected" : $selected = "";
													$opendays_html .= '<option value="'.$single_month.'"'.$selected.'>'.ucfirst($single_month).'</option>';
												}
												$opendays_html .= '</select>';
												$opendays_html .= '<input id="coderockz_woo_delivery_delivery_opendays_dates" type="text" class="coderockz-woo-delivery-input-field" value="'.$date.'" placeholder="'.__('Comma(,) Separated Date', 'coderockz-woo-delivery').'" style="width:200px;vertical-align:top;" autocomplete="off" name="coderockz_woo_delivery_delivery_opendays_dates_'.$month.'_'.$year.'">';
												if(array_keys($months)[0] != $month) {
													
													$opendays_html .= '<button class="coderockz-delivery-opendays-month-remove"><span class="dashicons dashicons-trash"></span></button>';
												}
												$opendays_html .= '</div>';
											}
											$opendays_html .= '</div>';
											$opendays_html .= '<br>
												    	  <span style="position:relative;left:18%">
														    <input class="coderockz-woo-delivery-add-month-btn-delivery-opendays" type="button" value="'.__('Add Month', 'coderockz-woo-delivery').'">
														    <div class="coderockz-woo-delivery-dummy-btn-delivery-opendays" style="position:absolute; left:0; right:0; top:0; bottom:0; cursor: pointer;"></div>
														  </span>';
											
											$opendays_html .= '</div>';
										}
										echo $opendays_html;
									} else {
	                        	?>

							    <div class="coderockz-woo-delivery-add-year-html-delivery-opendays coderockz-woo-delivery-form-group">
							    	<img class="coderockz-arrow" src="<?php echo CODEROCKZ_WOO_DELIVERY_URL ?>/admin/images/arrow.png" alt="" style="width: 20px;vertical-align: top;margin-top: 12px;margin-right: 15px;">
							        <input style="width:125px" class="coderockz-woo-delivery-input-field coderockz_woo_delivery_delivery_opendays_year" maxlength="4" type="text" value="<?php  ?>" placeholder="Year (ex. 2019)" style="vertical-align:top;" autocomplete="off"/>
							        <div class="coderockz_woo_delivery_delivery_opendays_another_month" style="display:inline-block;">
								        <div class="coderockz_woo_delivery_delivery_opendays_add_another_month">
									        <select style="width:125px!important" class="coderockz-woo-delivery-select-field" disabled="disabled">
									        	<option value="">Select Month</option>
									        	<?php
									        	$month_array = ['january','february','march','april','may','june','july','august','september','october','november','december'];
									        	foreach($month_array as $single_month) {
													echo '<option value="'.$single_month.'">'.ucfirst($single_month).'</option>';
												}
									        	?>
									            
										    </select>
									        <input style="width:200px" id="coderockz_woo_delivery_delivery_opendays_dates" type="text" class="coderockz-woo-delivery-input-field" value="<?php  ?>" placeholder="<?php _e('Comma(,) Separated Date', 'coderockz-woo-delivery'); ?>" style="vertical-align:top;" autocomplete="off" disabled="disabled"/>
								    	</div>
							    	</div>
							    	<br/>
							    	<span style="position:relative;left:18%">
									  <input class="coderockz-woo-delivery-add-month-btn-delivery-opendays" type="button" value="<?php _e('Add Month', 'coderockz-woo-delivery'); ?>" disabled="disabled">
									  <div class="coderockz-woo-delivery-dummy-btn-delivery-opendays" style="position:absolute; left:0; right:0; top:0; bottom:0; cursor: pointer;"></div>
									</span>


							    </div>
								<?php } ?>
							</div>
	                        <input class="coderockz-woo-delivery-submit-btn" type="submit" name="coderockz_delivery_date_delivery_opendays_form_submit" value="<?php _e('Save Changes', 'coderockz-woo-delivery'); ?>" />

	                    </form>
                	</div>

                </div>
                <div class="coderockz-woo-delivery-card">
					<p class="coderockz-woo-delivery-card-header"><?php _e('Special Open Days For Pickup', 'coderockz-woo-delivery'); ?></p>
					<div class="coderockz-woo-delivery-card-body">
						<p class="coderockz-woo-delivery-date-tab-pickup-opendays-notice"></p>
						<p class="coderockz-woo-delivery-timezone-tab-warning"><span class="dashicons dashicons-megaphone"></span><?php _e(' If you want to enable a date that will  disable in future as a reason of weekends, zone wise offdays, state wise offdays or zip wise offdays, then specifiy the date here.', 'coderockz-woo-delivery'); ?></p>
						<input class="coderockz-woo-delivery-add-year-btn-pickup-opendays" type="button" value="<?php _e('Add New Year', 'coderockz-woo-delivery'); ?>">
	                    <form action="" method="post" id ="coderockz_delivery_date_pickup_opendays_form_submit">
	                        <?php wp_nonce_field('coderockz_woo_delivery_nonce'); ?>
	                        <div id="coderockz-woo-delivery-pickup-opendays" class="coderockz-woo-delivery-pickup-opendays">
							    
	                        	<?php
	                        		$month_array = ['january','february','march','april','may','june','july','august','september','october','november','december'];
									$opendays_html = "";
									$opendays_years = $pickup_date_settings;
									if(isset($opendays_years['open_days']) && !empty($opendays_years['open_days'])) {
										foreach($opendays_years['open_days'] as $year=>$months) {
											
											$opendays_html .= '<div class="coderockz-woo-delivery-add-year-html-pickup-opendays coderockz-woo-delivery-form-group">';
											if(array_keys($opendays_years['open_days'])[0] == $year) {
												$opendays_html .= '<img class="coderockz-arrow" src="'. CODEROCKZ_WOO_DELIVERY_URL .'/admin/images/arrow.png" alt="" style="width: 20px;vertical-align: top;margin-top: 12px;margin-right: 15px;">';	

											} else {
												$opendays_html .= '<button class="coderockz-pickup-opendays-year-remove"><span class="dashicons dashicons-trash"></span></button>';
											}
											
											$opendays_html .= '<input style="width:125px" class="coderockz-woo-delivery-input-field coderockz_woo_delivery_pickup_opendays_year" maxlength="4" type="text" value="'.$year.'" placeholder="Year (ex. 2019)" style="vertical-align:top;" autocomplete="off" name="coderockz_woo_delivery_pickup_opendays_year_'.$year.'">';
											$opendays_html .= '<div style="display:inline-block;" class="coderockz_woo_delivery_pickup_opendays_another_month coderockz_woo_delivery_pickup_opendays_another_month_'.$year.'">';
											foreach($months as $month=>$date) {
												$opendays_html .= '<div class="coderockz_woo_delivery_pickup_opendays_add_another_month">';
												$opendays_html .= '<select style="width:125px!important" class="coderockz-woo-delivery-select-field" name="coderockz_woo_delivery_pickup_opendays_month_'.$year.'[]">';
												$opendays_html .= '<option value="">Select Month</option>';
												foreach($month_array as $single_month) {
													$single_month == $month ? $selected = "selected" : $selected = "";
													$opendays_html .= '<option value="'.$single_month.'"'.$selected.'>'.ucfirst($single_month).'</option>';
												}
												$opendays_html .= '</select>';
												$opendays_html .= '<input id="coderockz_woo_delivery_pickup_opendays_dates" type="text" class="coderockz-woo-delivery-input-field" value="'.$date.'" placeholder="'.__('Comma(,) Separated Date', 'coderockz-woo-delivery').'" style="width:200px;vertical-align:top;" autocomplete="off" name="coderockz_woo_delivery_pickup_opendays_dates_'.$month.'_'.$year.'">';
												if(array_keys($months)[0] != $month) {
													
													$opendays_html .= '<button class="coderockz-pickup-opendays-month-remove"><span class="dashicons dashicons-trash"></span></button>';
												}
												$opendays_html .= '</div>';
											}
											$opendays_html .= '</div>';
											$opendays_html .= '<br>
												    	  <span style="position:relative;left:18%">
														    <input class="coderockz-woo-delivery-add-month-btn-pickup-opendays" type="button" value="'.__('Add Month', 'coderockz-woo-delivery').'">
														    <div class="coderockz-woo-delivery-dummy-btn-pickup-opendays" style="position:absolute; left:0; right:0; top:0; bottom:0; cursor: pointer;"></div>
														  </span>';
											
											$opendays_html .= '</div>';
										}
										echo $opendays_html;
									} else {
	                        	?>

							    <div class="coderockz-woo-delivery-add-year-html-pickup-opendays coderockz-woo-delivery-form-group">
							    	<img class="coderockz-arrow" src="<?php echo CODEROCKZ_WOO_DELIVERY_URL ?>/admin/images/arrow.png" alt="" style="width: 20px;vertical-align: top;margin-top: 12px;margin-right: 15px;">
							        <input style="width:125px" class="coderockz-woo-delivery-input-field coderockz_woo_delivery_pickup_opendays_year" maxlength="4" type="text" value="<?php  ?>" placeholder="Year (ex. 2019)" style="vertical-align:top;" autocomplete="off"/>
							        <div class="coderockz_woo_delivery_pickup_opendays_another_month" style="display:inline-block;">
								        <div class="coderockz_woo_delivery_pickup_opendays_add_another_month">
									        <select style="width:125px!important" class="coderockz-woo-delivery-select-field" disabled="disabled">
									        	<option value="">Select Month</option>
									        	<?php
									        	$month_array = ['january','february','march','april','may','june','july','august','september','october','november','december'];
									        	foreach($month_array as $single_month) {
													echo '<option value="'.$single_month.'">'.ucfirst($single_month).'</option>';
												}
									        	?>
									            
										    </select>
									        <input style="width:200px" id="coderockz_woo_delivery_pickup_opendays_dates" type="text" class="coderockz-woo-delivery-input-field" value="<?php  ?>" placeholder="<?php _e('Comma(,) Separated Date', 'coderockz-woo-delivery'); ?>" style="vertical-align:top;" autocomplete="off" disabled="disabled"/>
								    	</div>
							    	</div>
							    	<br/>
							    	<span style="position:relative;left:18%">
									  <input class="coderockz-woo-delivery-add-month-btn-pickup-opendays" type="button" value="<?php _e('Add Month', 'coderockz-woo-delivery'); ?>" disabled="disabled">
									  <div class="coderockz-woo-delivery-dummy-btn-pickup-opendays" style="position:absolute; left:0; right:0; top:0; bottom:0; cursor: pointer;"></div>
									</span>


							    </div>
								<?php } ?>
							</div>
	                        <input class="coderockz-woo-delivery-submit-btn" type="submit" name="coderockz_delivery_date_pickup_opendays_form_submit" value="<?php _e('Save Changes', 'coderockz-woo-delivery'); ?>" />

	                    </form>
                	</div>

                </div>

                <div class="coderockz-woo-delivery-card">
					<p class="coderockz-woo-delivery-card-header"><?php _e('Category Wise Special Open Days', 'coderockz-woo-delivery'); ?></p>
					<div class="coderockz-woo-delivery-card-body">
						<p class="coderockz-woo-delivery-category-open-days-notice"></p>
						
	                    <form action="" method="post" id ="coderockz_category_open_days_form_submit">
	                        <?php wp_nonce_field('coderockz_woo_delivery_nonce'); ?>

	                        <div class="coderockz-woo-delivery-category-opendays">

	                        <?php
	                        $category_open_days_html = "";
							if(isset($open_date_settings['category_open_days']) && !empty($open_date_settings['category_open_days'])) {
								foreach($open_date_settings['category_open_days'] as $category => $openday_date) {
									$category_open_days_html .= '<div class="coderockz-woo-delivery-form-group">';
							        $category_open_days_html .= '<img class="coderockz-arrow" src="'.CODEROCKZ_WOO_DELIVERY_URL.'/admin/images/arrow.png" alt="" style="width: 20px;vertical-align: top;margin-top: 12px;margin-right: 15px;">';
									$category_open_days_html .= '<select class="coderockz-woo-delivery-select-field" name="coderockz_delivery_open_days_categories[]">
								    <option value="">'.__('Select Category', 'coderockz-woo-delivery').'</option>';
									foreach ($all_categories as $cat) {
										$selected = (htmlspecialchars_decode($cat->name) == stripslashes($category)) ? "selected" : "";
										$category_open_days_html .= '<option value="'.str_replace(" ","--",$cat->name).'"'.$selected.'>'.$cat->name.'</option>';
									}
									$category_open_days_html .= '</select>';
									$category_open_days_html .= '<input type="text" class="coderockz-woo-delivery-input-field" name="coderockz-woo-delivery-open-days-date-'.str_replace(" ","--",$category).'" value="'.$openday_date.'" style="vertical-align:top;width: 430px!important;" autocomplete="off" placeholder="'.__('Comma(,) Separated Date, format yyyy-mm-dd(Ex. 2020-12-24)', 'coderockz-woo-delivery').'"/>';
									
									if(array_keys($open_date_settings['category_open_days'])[0] != $category){
										$category_open_days_html .= '<button class="coderockz-woo-delivery-category-opendays-remove"><span class="dashicons dashicons-trash"></span></button>';
									}
							        $category_open_days_html .= '</div>';
								}
								echo $category_open_days_html;
							} else {

	                        ?>
		                        <div class="coderockz-woo-delivery-category-opendays-single">
		                        <div class="coderockz-woo-delivery-form-group">
		                        	<img class="coderockz-arrow" src="<?php echo CODEROCKZ_WOO_DELIVERY_URL ?>/admin/images/arrow.png" alt="" style="width: 20px;vertical-align: top;margin-top: 12px;margin-right: 15px;">
								    <select class="coderockz-woo-delivery-select-field" name="coderockz_delivery_open_days_categories[]">
								    <option value=""><?php _e('Select Category', 'coderockz-woo-delivery'); ?></option>
									<?php
									foreach ($all_categories as $cat) {
										echo '<option value="'.str_replace(" ","--",$cat->name).'">'.$cat->name.'</option>';
									}
									?>
									</select>
								    <input type="text" class="coderockz-woo-delivery-input-field coderockz-woo-delivery-add-opendays-category-date" value="" style="vertical-align:top;width: 430px!important;" autocomplete="off" placeholder="<?php _e('Comma(,) Separated Date, format yyyy-mm-dd(Ex. 2020-12-24)', 'coderockz-woo-delivery'); ?>" disabled="disabled"/>
		                    	</div>
		                    	</div>
		                    <?php } ?>
	                    	</div>
	                    	<button class="coderockz-woo-delivery-add-opendays-category-btn"><span class="dashicons dashicons-plus"></span></button>
	                        <input class="coderockz-woo-delivery-submit-btn" type="submit" name="coderockz_category_open_days_form_submit" value="<?php _e('Save Changes', 'coderockz-woo-delivery'); ?>" />
	                    </form>
                	</div>

                </div>
                
			</div>

			<div data-tab="tab9" class="coderockz-woo-delivery-tabcontent">
				<div class="coderockz-woo-delivery-card">
					<p class="coderockz-woo-delivery-card-header"><?php _e('General Time Settings', 'coderockz-woo-delivery'); ?></p>
					<div class="coderockz-woo-delivery-card-body">
						<p class="coderockz-woo-delivery-time-tab-notice"><span class="dashicons dashicons-yes"></span><?php _e(' Settings Changed Successfully', 'coderockz-woo-delivery'); ?></p>
	                    <form action="" method="post" id ="coderockz_delivery_time_form_submit">
	                        <?php wp_nonce_field('coderockz_woo_delivery_nonce'); ?>

	                    	<div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label"><?php _e('Enable Delivery Time', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="Enable Delivery Time select field in woocommerce order checkout page."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_enable_delivery_time">
							       <input type="checkbox" name="coderockz_enable_delivery_time" id="coderockz_enable_delivery_time" <?php echo (isset($time_settings['enable_delivery_time']) && !empty($time_settings['enable_delivery_time'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label"><?php _e('Make Delivery Time Field Mandatory', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="Make Delivery Time select field mandatory in woocommerce order checkout page. Default is optional."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_delivery_time_mandatory">
							       <input type="checkbox" name="coderockz_delivery_time_mandatory" id="coderockz_delivery_time_mandatory" <?php echo (isset($time_settings['delivery_time_mandatory']) && !empty($time_settings['delivery_time_mandatory'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_delivery_time_field_label"><?php _e('Delivery Time Field Heading', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Delivery Time select field heading. Default is Delivery Time."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_delivery_time_field_label" name="coderockz_delivery_time_field_label" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($time_settings['field_label']) && !empty($time_settings['field_label'])) ? stripslashes(esc_attr($time_settings['field_label'])) : "" ?>" placeholder="" autocomplete="off"/>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_delivery_time_field_placeholder"><?php _e('Delivery Time Field Placeholder', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Delivery Time select field label and placeholder. Default is Delivery Time."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_delivery_time_field_placeholder" name="coderockz_delivery_time_field_placeholder" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($time_settings['field_placeholder']) && !empty($time_settings['field_placeholder'])) ? stripslashes(esc_attr($time_settings['field_placeholder'])) : "" ?>" placeholder="" autocomplete="off"/>
	                    	</div>
	                    	<?php 
                    			$start_hour = "";
            					$start_min = "";
            					$start_format= "am";
                    			
                    			if(isset($time_settings['delivery_time_starts']) && $time_settings['delivery_time_starts'] !='') {
                    				$delivery_time_starts = (int)$time_settings['delivery_time_starts'];

                    				if($delivery_time_starts == 0) {
		            					$start_hour = "12";
		            					$start_min = "00";
		            					$start_format= "am";
		            				} elseif($delivery_time_starts > 0 && $delivery_time_starts <= 59) {

                    					$start_hour = "12";
                    					$start_min = sprintf("%02d", $delivery_time_starts);
                    					$start_format= "am";
                    				} elseif($delivery_time_starts > 59 && $delivery_time_starts <= 719) {
										$start_min = sprintf("%02d", (int)$delivery_time_starts%60);
										$start_hour = sprintf("%02d", ((int)$delivery_time_starts-$start_min)/60);
										$start_format= "am";
										
                    				} else {
										$start_min = sprintf("%02d", (int)$delivery_time_starts%60);
										$start_hour = sprintf("%02d", ((int)$delivery_time_starts-$start_min)/60);
										if($start_hour>12) {
											$start_hour = sprintf("%02d", $start_hour-12);
										}
										$start_format= "pm";
                    				}

                    			}
                    		?>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_delivery_time_slot_starts"><?php _e('Time Slot Starts From', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Delivery Time starts from the time that is specified here. Only numerical value is accepted."><span class="dashicons dashicons-editor-help"></span></p>
	                    		<div id="coderockz_delivery_time_slot_starts" class="coderockz_delivery_time_slot_starts">
	                    			
	                        	<input name="coderockz_delivery_time_slot_starts_hour" type="number" class="coderockz-woo-delivery-number-field" max="12" min="1" onkeyup="if(!Number.isInteger(Number(this.value)) || this.value > 12 || this.value < 1) this.value = null;" value="<?php echo $start_hour; ?>" placeholder="Hour" autocomplete="off"/>
	                        	<input name="coderockz_delivery_time_slot_starts_min" type="number" class="coderockz-woo-delivery-number-field" max="59" min="0" onkeyup="if(!Number.isInteger(Number(this.value)) || this.value > 59 || this.value < 0) this.value = null;" value="<?php echo $start_min; ?>" placeholder="Minute" autocomplete="off"/>
	                        	<select class="coderockz-woo-delivery-select-field" name="coderockz_delivery_time_slot_starts_format">
									<option value="am" <?php selected($start_format,"am",true); ?>>AM</option>
									<option value="pm" <?php selected($start_format,"pm",true); ?>>PM</option>
								</select>
	                        	</div>
	                    	</div>
	                    	<?php 
                    			$end_hour = "";
            					$end_min = "";
            					$end_format= "am";
                    			
                    			if(isset($time_settings['delivery_time_ends']) && $time_settings['delivery_time_ends'] !='') {
                    				$delivery_time_ends = (int)$time_settings['delivery_time_ends'];
                    				if($delivery_time_ends == 0) {
		            					$end_hour = "12";
		            					$end_min = "00";
		            					$end_format= "am";
		            				} elseif($delivery_time_ends > 0 && $delivery_time_ends <= 59) {
                    					$end_hour = "12";
                    					$end_min = sprintf("%02d", $delivery_time_ends);
                    					$end_format= "am";
                    				} elseif($delivery_time_ends > 59 && $delivery_time_ends <= 719) {
										$end_min = sprintf("%02d", (int)$delivery_time_ends%60);
										$end_hour = sprintf("%02d", ((int)$delivery_time_ends-$end_min)/60);
										$end_format= "am";
										
                    				} elseif($delivery_time_ends > 719 && $delivery_time_ends <= 1439) {
										$end_min = sprintf("%02d", (int)$delivery_time_ends%60);
										$end_hour = sprintf("%02d", ((int)$delivery_time_ends-$end_min)/60);
										if($end_hour>12) {
											$end_hour = sprintf("%02d", $end_hour-12);
										}
										$end_format= "pm";
                    				} elseif($delivery_time_ends == 1440) {
										$end_min = "00";
										$end_hour = "12";
										$end_format= "am";
                    				}


                    			}
                    		?>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_delivery_time_slot_ends"><?php _e('Time Slot Ends At', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Delivery Time ends at the time that is specified here. Only numerical value is accepted."><span class="dashicons dashicons-editor-help"></span></p>
	                    		<div id="coderockz_delivery_time_slot_ends" class="coderockz_delivery_time_slot_ends">
	                        	<input name="coderockz_delivery_time_slot_ends_hour" type="number" class="coderockz-woo-delivery-number-field" max="12" min="1" onkeyup="if(!Number.isInteger(Number(this.value)) || this.value > 12 || this.value < 1) this.value = null;" value="<?php echo $end_hour; ?>" placeholder="Hour" autocomplete="off"/>
	                        	<input name="coderockz_delivery_time_slot_ends_min" type="number" class="coderockz-woo-delivery-number-field" max="59" min="0" onkeyup="if(!Number.isInteger(Number(this.value)) || this.value > 59 || this.value < 0) this.value = null;" value="<?php echo $end_min; ?>" placeholder="Minute" autocomplete="off"/>
	                        	<select class="coderockz-woo-delivery-select-field" name="coderockz_delivery_time_slot_ends_format">
									<option value="am" <?php selected($end_format,"am",true); ?>>AM</option>
									<option value="pm" <?php selected($end_format,"pm",true); ?>>PM</option>
								</select>
	                        	</div>
	                        	<p class="coderockz_end_time_greater_notice">End Time Must after Start Time</p>
	                    	</div>
	                    	<?php
	                    		$duration = ""; 
	                    		$identity = "min";
	                    		$time_settings = $time_settings;
                    			if(isset($time_settings['each_time_slot']) && !empty($time_settings['each_time_slot'])) {
                    				$time_slot_duration = (int)$time_settings['each_time_slot'];
                    				if($time_slot_duration <= 59) {
                    					$duration = $time_slot_duration;
                    				} else {
                    					$time_slot_duration = $time_slot_duration/60;
                    					$helper = new Coderockz_Woo_Delivery_Helper();
                    					if($helper->containsDecimal($time_slot_duration)){
                    						$duration = $time_slot_duration*60;
                    						$identity = "min";
                    					} else {
                    						$duration = $time_slot_duration;
                    						$identity = "hour";
                    					}
                    				}
                    			}
	                    	?>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_delivery_time_slot_duration"><?php _e('Each Time Slot Duration', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Each delivery time slot duration that is specified here. Only numerical value is accepted."><span class="dashicons dashicons-editor-help"></span></p>
	                    		<div id="coderockz_delivery_time_slot_duration" class="coderockz_delivery_time_slot_duration">
	                        	<input name="coderockz_delivery_time_slot_duration_time" type="number" min="1" onkeyup="if(!Number.isInteger(Number(this.value)) || this.value < 1) this.value = null;" class="coderockz-woo-delivery-number-field" value="<?php echo $duration; ?>" placeholder="" autocomplete="off"/>
	                        	<select class="coderockz-woo-delivery-select-field" name="coderockz_delivery_time_slot_duration_format">
									<option value="min" <?php selected($identity,"min",true); ?>>Minutes</option>
									<option value="hour" <?php selected($identity,"hour",true); ?>>Hour</option>
								</select>
	                        	</div>
	                    	</div>

	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_delivery_time_maximum_order"><?php _e('Maximum Order Per Time Slot', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Each time slot take maximum number of orders that is specified here. After reaching the maximum order, the time slot is disabled automaticaly. Only numerical value is accepted. Blank this field means each time slot takes unlimited order."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_delivery_time_maximum_order" name="coderockz_delivery_time_maximum_order" type="number" class="coderockz-woo-delivery-number-field" min="1" onkeyup="if(!Number.isInteger(Number(this.value)) || this.value < 1) this.value = null;" value="<?php echo (isset($time_settings['max_order_per_slot']) && !empty($time_settings['max_order_per_slot'])) ? stripslashes(esc_attr($time_settings['max_order_per_slot'])) : ""; ?>" placeholder="" autocomplete="off"/>
	                    	</div>

	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_delivery_time_format"><?php _e('Delivery Time format', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Time format that is used in everywhere which is available by this plugin. Default is 12 Hours."><span class="dashicons dashicons-editor-help"></span></p>
	                    		<select class="coderockz-woo-delivery-select-field" name="coderockz_delivery_time_format">

	                    			<option value="" <?php if(isset($time_settings['time_format']) && $time_settings['time_format'] == ""){ echo "selected"; } ?>><?php _e('Select Time Format', 'coderockz-woo-delivery'); ?></option>
									<option value="12" <?php if(isset($time_settings['time_format']) && $time_settings['time_format'] == "12"){ echo "selected"; } ?>>12 Hours</option>
									<option value="24" <?php if(isset($time_settings['time_format']) && $time_settings['time_format'] == "24"){ echo "selected"; } ?>>24 Hours</option>
								</select>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label"><?php _e('Add an Option As Soon As Possible', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="Enable the option if you want to show an option As Soon As Possible in the delivery time field."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_woo_delivry_as_soon_as_possible_option">
							       <input type="checkbox" name="coderockz_woo_delivry_as_soon_as_possible_option" id="coderockz_woo_delivry_as_soon_as_possible_option" <?php echo (isset($time_settings['enable_as_soon_as_possible_option']) && !empty($time_settings['enable_as_soon_as_possible_option'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_as_soon_as_possible_text"><?php _e('Text for As Soon As Possible Option', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="As Soon As Possible text. Default is As Soon As Possible."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_woo_delivery_as_soon_as_possible_text" name="coderockz_woo_delivery_as_soon_as_possible_text" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($time_settings['as_soon_as_possible_text']) && !empty($time_settings['as_soon_as_possible_text'])) ? stripslashes(esc_attr($time_settings['as_soon_as_possible_text'])) : "" ?>" placeholder="" autocomplete="off"/>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label"><?php _e('Don\'t Consider Order for Maximum Limit if Delivery Status Completed ' , 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="if you want to free up a slot by making the delivery status completed that is already reached for maximum limit then enable the option."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_delivery_free_up_slot_for_delivery_completed">
							       <input type="checkbox" name="coderockz_delivery_free_up_slot_for_delivery_completed" id="coderockz_delivery_free_up_slot_for_delivery_completed" <?php echo (isset($time_settings['free_up_slot_for_delivery_completed']) && !empty($time_settings['free_up_slot_for_delivery_completed'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label"><?php _e('Disable Current Time Slot', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="Make the time slot disabled that has the current time. In default, the time slot isn't disabled that has the current time."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_delivery_time_disable_current_time_slot">
							       <input type="checkbox" name="coderockz_delivery_time_disable_current_time_slot" id="coderockz_delivery_time_disable_current_time_slot" <?php echo (isset($time_settings['disabled_current_time_slot']) && !empty($time_settings['disabled_current_time_slot'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label"><?php _e('Auto Select 1st Available Time', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="Enable the option if you want to select the first available time based on date automatically and shown in the delivery time field. Default is disable."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_auto_select_first_time">
							       <input type="checkbox" name="coderockz_auto_select_first_time" id="coderockz_auto_select_first_time" <?php echo (isset($time_settings['auto_select_first_time']) && !empty($time_settings['auto_select_first_time'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label"><?php _e('Hide Searchbox From Time Field Dropdown', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="If you want to hide the searchbox from delivery time field dropdown, enable it. Default is Showing searhbox."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_hide_searchbox_time_field_dropdown">
							       <input type="checkbox" name="coderockz_hide_searchbox_time_field_dropdown" id="coderockz_hide_searchbox_time_field_dropdown" <?php echo (isset($time_settings['hide_searchbox']) && !empty($time_settings['hide_searchbox'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>
	                        <input class="coderockz-woo-delivery-submit-btn" type="submit" name="coderockz_delivery_time_form_submit" value="<?php _e('Save Changes', 'coderockz-woo-delivery'); ?>" />

	                    </form>
                	</div>

                </div>
			</div>
			<div data-tab="tab10" class="coderockz-woo-delivery-tabcontent">
				<div class="coderockz-woo-delivery-card">
					<p class="coderockz-woo-delivery-card-header"><?php _e('Custom Time Slot Settings', 'coderockz-woo-delivery'); ?></p>
					<div class="coderockz-woo-delivery-card-body">
						<p class="coderockz-woo-delivery-time-slot-notice"><span class="dashicons dashicons-yes"></span><?php _e(' Settings Changed Successfully', 'coderockz-woo-delivery'); ?></p>
						<p class="coderockz-woo-delivery-timezone-tab-warning"><span class="dashicons dashicons-megaphone"></span><?php _e(' Enable custom time slot makes the Time Slot Starts From, Time Slot Ends At, Each Time Slot Duration, Maximum Order Per Time Slot field unworkable from Delivery Time Tab', 'coderockz-woo-delivery'); ?></p>
						
						<form action="" method="post" id ="coderockz_woo_delivery_custom_time_slot_settings_submit">
							<?php wp_nonce_field('coderockz_woo_delivery_nonce'); ?>
		                    <div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label"><?php _e('Enable Custom Time Slot', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="If you want to make time slot as you want, enable this option. Default is disable."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_woo_delivery_enable_custom_time_slot">
							       <input type="checkbox" name="coderockz_woo_delivery_enable_custom_time_slot" id="coderockz_woo_delivery_enable_custom_time_slot" <?php echo (isset($time_slot_settings['enable_custom_time_slot']) && !empty($time_slot_settings['enable_custom_time_slot'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>
	                    	<input class="coderockz-woo-delivery-submit-btn" type="submit" name="coderockz_woo_delivery_custom_time_slot_settings_submit" value="<?php _e('Save Changes', 'coderockz-woo-delivery'); ?>" />
	                    </form>
                	</div>

                </div>
				<div class="coderockz-woo-delivery-card">
					<p class="coderockz-woo-delivery-card-header"><?php _e('Add Custom Time Slot', 'coderockz-woo-delivery'); ?></p>
					<div class="coderockz-woo-delivery-card-body">
                    	<p class="coderockz-woo-delivery-timezone-tab-warning"><span class="dashicons dashicons-megaphone"></span><?php _e(' To know how to make your custom timeslots perfectly ', 'coderockz-woo-delivery'); ?><a href="https://coderockz.com/documentations/make-custom-timeslots-perfectly" target="_blank"><?php _e('click here', 'coderockz-woo-delivery'); ?></a></p>
                    	<input class="coderockz-woo-delivery-add-time-slot-btn" type="button" value="<?php _e('Add New Time Slot', 'coderockz-woo-delivery'); ?>">
	                    <div id="coderockz-woo-delivery-time-slot-accordion">
                    	  <div class="coderockz-woo-delivery-time-slot-accordion-header" style="display:none;">
                    	  	<p style="font-size:16px;margin:0;font-weight:700;color:#fff!important;"><span class="coderockz-woo-delivery-slot-start-hour">Start Time</span><span class="coderockz-woo-delivery-slot-start-min"></span><span class="coderockz-woo-delivery-slot-start-format"></span> - <span class="coderockz-woo-delivery-slot-end-hour">End Time</span><span class="coderockz-woo-delivery-slot-end-min"></span><span class="coderockz-woo-delivery-slot-end-format"></span></p>
                    	  </div>
						  <div data-plugin-url="<?php echo CODEROCKZ_WOO_DELIVERY_URL; ?>" class="coderockz-woo-delivery-time-slot-accordion-content" style="display:none;">
						  	<div class="coderockz-woo-delivery-form-group" style="margin: 0;position: absolute;right: 45px;top: -31px;">
							    <label class="coderockz-woo-delivery-toogle-switch">
							       <input type="checkbox" class="coderockz-woo-delivery-enable-custom-time-slot"/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
                    		</div>
                    		<span class="dashicons dashicons-trash coderockz-woo-delivery-time-slot-accordion-delete" style="margin: 0;position: absolute;right: 5px;top: -30px;color:#fff;cursor: pointer;"></span>
                    		<p class="coderockz-woo-delivery-custom-time-slot-notice"><span class="dashicons dashicons-yes"></span><?php _e(' Settings Changed Successfully', 'coderockz-woo-delivery'); ?></p>
	                    	
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_delivery_time_slot_starts"><?php _e('Time Slot Starts From', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Delivery Time Slot starts from the time that is specified here. Only numerical value is accepted."><span class="dashicons dashicons-editor-help"></span></p>
	                    		<div class="coderockz_delivery_time_slot_starts coderockz_woo_delivery_custom_time_slot_starts">
	                    			
	                        	<input type="number" class="coderockz_woo_delivery_custom_time_slot_starts_hour coderockz-woo-delivery-number-field" max="12" min="1" onkeyup="if(!Number.isInteger(Number(this.value)) || this.value > 12 || this.value < 1) this.value = null;" value="" placeholder="Hour" autocomplete="off"/>
	                        	<input type="number" class="coderockz_woo_delivery_custom_time_slot_starts_min coderockz-woo-delivery-number-field" max="59" min="0" onkeyup="if(!Number.isInteger(Number(this.value)) || this.value > 59 || this.value < 0) this.value = null;" value="" placeholder="Minute" autocomplete="off"/>
	                        	<select class="coderockz_woo_delivery_custom_time_slot_starts_format coderockz-woo-delivery-select-field">
									<option value="am">AM</option>
									<option value="pm">PM</option>
								</select>
	                        	</div>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Time Slot Ends At', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Delivery Time Slot ends at the time that is specified here. Only numerical value is accepted."><span class="dashicons dashicons-editor-help"></span></p>
	                    		<div class="coderockz_delivery_time_slot_ends coderockz_woo_delivery_custom_time_slot_ends">
	                        	<input type="number" class="coderockz_woo_delivery_custom_time_slot_ends_hour coderockz-woo-delivery-number-field" max="12" min="1" onkeyup="if(!Number.isInteger(Number(this.value)) || this.value > 12 || this.value < 1) this.value = null;" value="" placeholder="Hour" autocomplete="off"/>
	                        	<input type="number" class="coderockz_woo_delivery_custom_time_slot_ends_min coderockz-woo-delivery-number-field" max="59" min="0" onkeyup="if(!Number.isInteger(Number(this.value)) || this.value > 59 || this.value < 0) this.value = null;" value="" placeholder="Minute" autocomplete="off"/>
	                        	<select class="coderockz_woo_delivery_custom_time_slot_ends_format coderockz-woo-delivery-select-field" name="coderockz_delivery_time_slot_ends_format">
									<option value="am">AM</option>
									<option value="pm">PM</option>
								</select>
	                        	</div>
	                        	<p class="coderockz_custom_end_time_greater_notice">End Time Must after Start Time</p>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label"><?php _e('Want to split the timeslot?', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="Enable it if you want to split the timeslot in several timeslot according a fixed interval. Default is disable."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch">
							       <input type="checkbox" class="coderockz_woo_delivery_enable_custom_time_slot_split"/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-split-time-duration-section">
		                    	<div class="coderockz-woo-delivery-form-group">
		                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Each splited Time Slot Duration', 'coderockz-woo-delivery'); ?></label>
		                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Each splited delivery time slot duration that is specified here. Only numerical value is accepted."><span class="dashicons dashicons-editor-help"></span></p>
		                    		<div class="coderockz_split_time_slot_duration">
			                        	<input type="number" min="1" onkeyup="if(!Number.isInteger(Number(this.value)) || this.value < 1) this.value = null;" class="coderockz-woo-delivery-number-field coderockz_split_time_slot_duration_time" value="" placeholder="" autocomplete="off"/>
			                        	<select class="coderockz-woo-delivery-select-field coderockz_split_time_slot_duration_format">
											<option value="min">Minutes</option>
											<option value="hour">Hour</option>
										</select>
		                        	</div>
		                        	<p class="coderockz_split_time_slot_duration_notice">Time slot duration is required</p>
		                    	</div>

		                    	<div class="coderockz-woo-delivery-form-group">
		                        	<span class="coderockz-woo-delivery-form-label"><?php _e('Make each timeslot single', 'coderockz-woo-delivery'); ?></span>
		                        	<p class="coderockz-woo-delivery-tooltip" tooltip="Enable it if you want to make every timeslot single as the timeslot value is the starting time of the time range. Default is disable."><span class="dashicons dashicons-editor-help"></span></p>
								    <label class="coderockz-woo-delivery-toogle-switch">
								       <input type="checkbox" class="coderockz_woo_delivery_enable_custom_splited_time_slot_single"/>
								       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
								    </label>
		                    	</div>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-single-time-section">
	                    		<div class="coderockz-woo-delivery-form-group">
		                        	<span class="coderockz-woo-delivery-form-label"><?php _e('Make each timeslot single', 'coderockz-woo-delivery'); ?></span>
		                        	<p class="coderockz-woo-delivery-tooltip" tooltip="Enable it if you want to make every timeslot single as the timeslot value is the starting time of the time range. Default is disable."><span class="dashicons dashicons-editor-help"></span></p>
								    <label class="coderockz-woo-delivery-toogle-switch">
								       <input type="checkbox" class="coderockz_woo_delivery_enable_custom_time_slot_single"/>
								       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
								    </label>
		                    	</div>
		                    </div>
		                    <div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label"><?php _e('Hide This TimeSlot for Current Date', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="Enable it if you want to hide this timeslot current date. Default is disable."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch">
							       <input type="checkbox" class="coderockz_woo_delivery_hide_time_slot_current_date"/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_timeslot_closing_time"><?php _e('Hide this timeslot at', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="If you don't want to show the timeslot after a certain time if the current date is selected as delivery date, put the time here. Only numerical value is accepted."><span class="dashicons dashicons-editor-help"></span></p>
	                    		<div id="coderockz_woo_delivery_timeslot_closing_time" class="coderockz_woo_delivery_timeslot_closing_time">
	                    			
	                        	<input name="coderockz_woo_delivery_timeslot_closing_time_hour" type="number" class="coderockz-woo-delivery-number-field coderockz_woo_delivery_timeslot_closing_time_hour" max="12" min="1" onkeyup="if(!Number.isInteger(Number(this.value)) || this.value > 12 || this.value < 1) this.value = null;" value="" placeholder="Hour" autocomplete="off"/>
	                        	<input name="coderockz_woo_delivery_timeslot_closing_time_min" type="number" class="coderockz-woo-delivery-number-field coderockz_woo_delivery_timeslot_closing_time_min" max="59" min="0" onkeyup="if(!Number.isInteger(Number(this.value)) || this.value > 59 || this.value < 0) this.value = null;" value="" placeholder="Minute" autocomplete="off"/>
	                        	<select class="coderockz-woo-delivery-select-field coderockz_woo_delivery_timeslot_closing_time_format" name="coderockz_woo_delivery_timeslot_closing_time_format">
									<option value="am">AM</option>
									<option value="pm">PM</option>
								</select>
	                        	</div>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Maximum Order For This Slot', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Each time slot take maximum number of orders that is specified here. After reaching the maximum order, the time slot is disabled automaticaly. Only numerical value is accepted. Blank this field means each time slot takes unlimited order."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input type="number" class="coderockz-woo-delivery-custom-time-slot-order coderockz-woo-delivery-number-field" min="1" onkeyup="if(!Number.isInteger(Number(this.value)) || this.value < 1) this.value = null;" value="" placeholder="" autocomplete="off"/>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Show This Timeslot Only At', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="If you want to show the timeslot only at specific date then specify here the dates"><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input style="width:495px" type="text" class="coderockz-woo-delivery-custom-time-slot-specific-date coderockz-woo-delivery-input-field" value="" placeholder="<?php _e('Comma(,) Separated Date, format yyyy-mm-dd(Ex. 2020-12-24)', 'coderockz-woo-delivery'); ?>" autocomplete="off"/>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Hide This Timeslot At', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="If you want to hide the timeslot at specific date then specify here the dates"><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input style="width:495px" type="text" class="coderockz-woo-delivery-custom-time-slot-specific-date-close coderockz-woo-delivery-input-field" value="" placeholder="<?php _e('Comma(,) Separated Date, format yyyy-mm-dd(Ex. 2020-12-24)', 'coderockz-woo-delivery'); ?>" autocomplete="off"/>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Time Slot Fee', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="If anyone select this time slot, fee specified here is applied with the total."><span class="dashicons dashicons-editor-help"></span></p>
	                        	
	                        	<input type="text" class="coderockz-woo-delivery-custom-time-slot-fee coderockz-woo-delivery-input-field" onkeyup="if(isNaN(parseFloat(Number(this.value))) || isNaN(parseInt(Number(this.value), 10))) this.value = null;" value="" style="width:245px;border-radius: 3px 0 0 3px;" autocomplete="off" placeholder="Fee"/><span class="coderockz-woo-delivery-currency-code" style="width:40px;"><?php echo $currency_code; ?></span>
	                    	</div>

	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label coderockz-woo-delivery-checkbox-label"><?php _e('Hide For', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip coderockz-woo-delivery-checkbox-tooltip" tooltip="This time slot will be hidden for the selected days."><span class="dashicons dashicons-editor-help"></span></p>
	                    		<div class="coderockz-woo-delivery-slot-enable-for" style="display:inline-block">
	                    		<input type="checkbox" name="coderockz_woo_delivery_custom_time_slot_disable" value="6"><label class="coderockz-woo-delivery-checkbox-field-text">Saturday</label><br/>
								<input type="checkbox" name="coderockz_woo_delivery_custom_time_slot_disable" value="0"><label class="coderockz-woo-delivery-checkbox-field-text">Sunday</label><br/>
								<input type="checkbox" name="coderockz_woo_delivery_custom_time_slot_disable" value="1"><label class="coderockz-woo-delivery-checkbox-field-text">Monday</label><br/>
								<input type="checkbox" name="coderockz_woo_delivery_custom_time_slot_disable" value="2"><label class="coderockz-woo-delivery-checkbox-field-text">Tuesday</label><br/>
								<input type="checkbox" name="coderockz_woo_delivery_custom_time_slot_disable" value="3"><label class="coderockz-woo-delivery-checkbox-field-text">Wednesday</label><br/>
								<input type="checkbox" name="coderockz_woo_delivery_custom_time_slot_disable" value="4"><label class="coderockz-woo-delivery-checkbox-field-text">Thursday</label><br/>
								<input type="checkbox" name="coderockz_woo_delivery_custom_time_slot_disable" value="5"><label class="coderockz-woo-delivery-checkbox-field-text">Friday</label><br/>
								</div>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Hide This Timeslot For Categories', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="If you want to disable the timeslot for a specific category then select the category from here."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<select id="coderockz_woo_delivery_timeslot_hide_categories" name="coderockz_woo_delivery_timeslot_hide_categories[]" class="coderockz_woo_delivery_timeslot_hide_categories" multiple>
                                
                                <?php
                                foreach ($all_categories as $cat) {

                                    echo '<option value="'.$cat->name.'">'.$cat->name.'</option>';
                                }
                                ?>
                                </select>
	                    	</div>
	                    	<?php if(get_option('coderockz_woo_delivery_large_product_list') == false) { ?>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Hide This Timeslot For Products', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="If you want to hide the timeslot for a specific product then select the product from here."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<select id="coderockz_woo_delivery_timeslot_hide_products" name="coderockz_woo_delivery_timeslot_hide_products[]" class="coderockz_woo_delivery_timeslot_hide_products" multiple>
                                
                                <?php
                                foreach ($store_products as $key=>$value) {
                                    echo '<option value="'.$key.'">'.$value.'</option>';
                                }
                                ?>
                                </select>
	                    	</div>
	                    	<?php } else { ?>

                    		<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Hide This Timeslot For Products', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="If you want to hide the timeslot for a specific product then select the product from here."><span class="dashicons dashicons-editor-help"></span></p>

	                    		<input name="coderockz_woo_delivery_timeslot_hide_products_input" type="text" class="coderockz_woo_delivery_timeslot_hide_products_input coderockz-woo-delivery-input-field" value="" placeholder="Comma separated Product/Variation ID" autocomplete="off"/>
	                    	</div>

	                    	<?php } ?>
	                    	<div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label"><?php _e('Show If Cart Has Other Categories Or Products', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="If there is hidden category's products or hidden products in the cart then whatever there are other category's products or other products, the timeslot is hidden. If you want to reverse this enable it."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch">
							       <input type="checkbox" class="coderockz_woo_delivery_time_slot_shown_other_categories_products"/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_custom_time_slot_more_settings"><?php _e('Want More Settings Based On', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="If you select 'Shipping State' then you can get more settings based on shipping state and if you select 'Shipping Postcode/ZIP' then you can get more settings based on shipping postcode"><span class="dashicons dashicons-editor-help"></span></p>
	                    		<select class="coderockz-woo-delivery-select-field coderockz_woo_delivery_custom_time_slot_more_settings" name="coderockz_woo_delivery_custom_time_slot_more_settings">
									<option value="">Select your choice</option>
									<option value="zone">Shipping Zone</option>
									<option value="state">Shipping State</option>
									<option value="postcode">Shipping Postcode/ZIP</option>
								</select>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-custom_timeslot-more-zone">
		                    	<div class="coderockz-woo-delivery-form-group">
		                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Hide For Shipping Zone', 'coderockz-woo-delivery'); ?></label>
		                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Timeslot is hidden for the selected shipping zone."><span class="dashicons dashicons-editor-help"></span></p>
		                        	<select id="coderockz_woo_delivery_custom_time_disable_zone" name="coderockz_woo_delivery_custom_time_disable_zone[]" class="coderockz_woo_delivery_custom_time_disable_zone" multiple>
	                                <?php
	                                foreach ($zone_name as $key => $value) {
	                                    echo '<option value="'.$key.'">'.$value.'</option>';
	                                }
	                                ?>
	                                </select>
		                    	</div>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-custom_timeslot-more-state">
		                    	<div class="coderockz-woo-delivery-form-group">
		                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Hide For Shipping Zone Regions', 'coderockz-woo-delivery'); ?></label>
		                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Timeslot is hidden for the selected shipping zone regions."><span class="dashicons dashicons-editor-help"></span></p>
		                        	<select id="coderockz_woo_delivery_custom_time_disable_regions" name="coderockz_woo_delivery_custom_time_disable_regions[]" class="coderockz_woo_delivery_custom_time_disable_regions" multiple>
	                                <?php
	                                foreach ($zone_regions as $key => $value) {
	                                    echo '<option value="'.$key.'">'.$value.'</option>';
	                                }
	                                ?>
	                                </select>
		                    	</div>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-custom_timeslot-more-postcode">           	<div class="coderockz-woo-delivery-form-group">
		                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Hide For Shipping Zone PostCode/Zip', 'coderockz-woo-delivery'); ?></label>
		                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Timeslot is hidden for the selected shipping zone postcode/zip."><span class="dashicons dashicons-editor-help"></span></p>
		                        	<select id="coderockz_woo_delivery_custom_time_disable_postcode" name="coderockz_woo_delivery_custom_time_disable_postcode[]" class="coderockz_woo_delivery_custom_time_disable_postcode" multiple>
	                                <?php
	                                foreach ($zone_post_code as $key => $value) {
	                                    echo '<option value="'.$value.'">'.$value.'</option>';		                                }
	                                ?>
	                                </select>
		                    	</div>
	                    	</div>
						    <button class="coderockz-woo-delivery-custom-time-slot-saving coderockz-woo-delivery-submit-btn"><?php _e('Save Changes', 'coderockz-woo-delivery'); ?></button>
						  </div>
						  <?php
						  	
						  	if(isset($time_slot_settings['time_slot']) && count($time_slot_settings['time_slot'])>0){
						  		$helper = new Coderockz_Woo_Delivery_Helper();
								$sorted_custom_slot = $helper->array_sort_by_column($time_slot_settings['time_slot'],'start');
						  		foreach($sorted_custom_slot as $individual_time_slot) {
						  			$start_hour = "";
			    					$start_min = "";
			    					$start_format= "am";
			            			
			            			if(isset($individual_time_slot['start']) && $individual_time_slot['start'] !='') {
			            				$delivery_time_starts = (int)$individual_time_slot['start'];

			            				if($delivery_time_starts == 0) {
			            					$start_hour = "12";
			            					$start_min = "00";
			            					$start_format= "am";
			            				} elseif($delivery_time_starts > 0 && $delivery_time_starts <= 59) {

			            					$start_hour = "12";
			            					$start_min = sprintf("%02d", $delivery_time_starts);
			            					$start_format= "am";
			            				} elseif($delivery_time_starts > 59 && $delivery_time_starts <= 719) {
											$start_min = sprintf("%02d", (int)$delivery_time_starts%60);
											$start_hour = sprintf("%02d", ((int)$delivery_time_starts-$start_min)/60);
											$start_format= "am";
											
			            				} elseif($delivery_time_starts > 719 && $delivery_time_starts <= 1439) {
											$start_min = sprintf("%02d", (int)$delivery_time_starts%60);
											$start_hour = sprintf("%02d", ((int)$delivery_time_starts-$start_min)/60);
											if($start_hour>12) {
												$start_hour = sprintf("%02d", $start_hour-12);
											}
											$start_format= "pm";
			            				} elseif($delivery_time_starts == 1440) {
			            					$start_hour = "12";
			    							$start_min = "00";
			    							$start_format= "am";
			            				}

			            			}

			            			$end_hour = "";
	            					$end_min = "";
	            					$end_format= "am";
	                    			
	                    			if(isset($individual_time_slot['end']) && $individual_time_slot['end'] !='') {
	                    				$delivery_time_ends = (int)$individual_time_slot['end'];
	                    				if($delivery_time_ends == 0) {
			            					$end_hour = "12";
			            					$end_min = "00";
			            					$end_format= "am";
			            				} elseif($delivery_time_ends > 0 && $delivery_time_ends <= 59) {
	                    					$end_hour = "12";
	                    					$end_min = sprintf("%02d", $delivery_time_ends);
	                    					$end_format= "am";
	                    				} elseif($delivery_time_ends > 59 && $delivery_time_ends <= 719) {
											$end_min = sprintf("%02d", (int)$delivery_time_ends%60);
											$end_hour = sprintf("%02d", ((int)$delivery_time_ends-$end_min)/60);
											$end_format= "am";
											
	                    				} elseif ($delivery_time_ends > 719 && $delivery_time_ends <= 1439) {
											$end_min = sprintf("%02d", (int)$delivery_time_ends%60);
											$end_hour = sprintf("%02d", ((int)$delivery_time_ends-$end_min)/60);
											if($end_hour>12) {
												$end_hour = sprintf("%02d", $end_hour-12);
											}
											$end_format= "pm";

	                    				} elseif($delivery_time_ends == 1440) {
											$end_min = "00";
											$end_hour = "12";
											$end_format= "am";
											
	                    				} 

	                    			}

	                    			?>

	                    			<div class="coderockz-woo-delivery-time-slot-accordion-header">
		                    	  	<p style="font-size:16px;margin:0;font-weight:700;color:#fff!important;"><span class="coderockz-woo-delivery-slot-start-hour"><?php echo $start_hour; ?></span><span class="coderockz-woo-delivery-slot-start-min">:<?php echo $start_min; ?></span><span class="coderockz-woo-delivery-slot-start-format"> <?php echo strtoupper($start_format); ?></span> - <span class="coderockz-woo-delivery-slot-end-hour"><?php echo $end_hour; ?></span><span class="coderockz-woo-delivery-slot-end-min">:<?php echo $end_min; ?></span><span class="coderockz-woo-delivery-slot-end-format"> <?php echo strtoupper($end_format); ?></span></p>
		                    	  </div>
								  <div class="coderockz-woo-delivery-time-slot-accordion-content">
								  	<div class="coderockz-woo-delivery-form-group" style="margin: 0;position: absolute;right: 45px;top: -31px;">
									    <label class="coderockz-woo-delivery-toogle-switch">
									       <input type="checkbox" class="coderockz-woo-delivery-enable-custom-time-slot" <?php echo isset($individual_time_slot['enable']) && !empty($individual_time_slot['enable'])  ? "checked" : "";?>/>
									       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
									    </label>
		                    		</div>
		                    		<span class="dashicons dashicons-trash coderockz-woo-delivery-time-slot-accordion-delete" style="margin: 0;position: absolute;right: 5px;top: -30px;color:#fff;cursor: pointer;"></span>
		                    		<p class="coderockz-woo-delivery-custom-time-slot-notice"><span class="dashicons dashicons-yes"></span><?php _e(' Settings Changed Successfully', 'coderockz-woo-delivery'); ?></p>
			                    	<div class="coderockz-woo-delivery-form-group">
			                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_delivery_time_slot_starts"><?php _e('Time Slot Starts From', 'coderockz-woo-delivery'); ?></label>
			                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Delivery Time Slot starts from the time that is specified here. Only numerical value is accepted."><span class="dashicons dashicons-editor-help"></span></p>
			                    		<div class="coderockz_delivery_time_slot_starts coderockz_woo_delivery_custom_time_slot_starts">
			                    			
			                        	<input type="number" class="coderockz_woo_delivery_custom_time_slot_starts_hour coderockz-woo-delivery-number-field" max="12" min="1" onkeyup="if(!Number.isInteger(Number(this.value)) || this.value > 12 || this.value < 1) this.value = null;" value="<?php echo $start_hour; ?>" placeholder="Hour" autocomplete="off"/>
			                        	<input type="number" class="coderockz_woo_delivery_custom_time_slot_starts_min coderockz-woo-delivery-number-field" max="59" min="0" onkeyup="if(!Number.isInteger(Number(this.value)) || this.value > 59 || this.value < 0) this.value = null;" value="<?php echo $start_min; ?>" placeholder="Minute" autocomplete="off"/>
			                        	<select class="coderockz_woo_delivery_custom_time_slot_starts_format coderockz-woo-delivery-select-field">
											<option value="am" <?php echo $start_format == "am"? " selected" : ""; ?>>AM</option>
											<option value="pm" <?php echo $start_format == "pm"? " selected" : ""; ?>>PM</option>
										</select>
			                        	</div>
			                    	</div>
			                    	<div class="coderockz-woo-delivery-form-group">
			                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Time Slot Ends At', 'coderockz-woo-delivery'); ?></label>
			                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Delivery Time Slot ends at the time that is specified here. Only numerical value is accepted."><span class="dashicons dashicons-editor-help"></span></p>
			                    		<div class="coderockz_delivery_time_slot_ends coderockz_woo_delivery_custom_time_slot_ends">
			                        	<input type="number" class="coderockz_woo_delivery_custom_time_slot_ends_hour coderockz-woo-delivery-number-field" max="12" min="1" onkeyup="if(!Number.isInteger(Number(this.value)) || this.value > 12 || this.value < 1) this.value = null;" value="<?php echo $end_hour; ?>" placeholder="Hour" autocomplete="off"/>
			                        	<input type="number" class="coderockz_woo_delivery_custom_time_slot_ends_min coderockz-woo-delivery-number-field" max="59" min="0" onkeyup="if(!Number.isInteger(Number(this.value)) || this.value > 59 || this.value < 0) this.value = null;" value="<?php echo $end_min; ?>" placeholder="Minute" autocomplete="off"/>
			                        	<select class="coderockz_woo_delivery_custom_time_slot_ends_format coderockz-woo-delivery-select-field" name="coderockz_delivery_time_slot_ends_format">
											<option value="am" <?php echo $end_format == "am"? " selected" : ""; ?>>AM</option>
											<option value="pm" <?php echo $end_format == "pm"? " selected" : ""; ?>>PM</option>
										</select>
			                        	</div>
			                        	<p class="coderockz_custom_end_time_greater_notice">End Time Must after Start Time</p>
			                    	</div>
			                    	<div class="coderockz-woo-delivery-form-group">
			                        	<span class="coderockz-woo-delivery-form-label"><?php _e('Want to split the timeslot?', 'coderockz-woo-delivery'); ?></span>
			                        	<p class="coderockz-woo-delivery-tooltip" tooltip="Enable it if you want to split the timeslot in several timeslot according a fixed interval. Default is disable."><span class="dashicons dashicons-editor-help"></span></p>
									    <label class="coderockz-woo-delivery-toogle-switch">
									       <input type="checkbox" class="coderockz_woo_delivery_enable_custom_time_slot_split" <?php echo isset($individual_time_slot['enable_split']) && !empty($individual_time_slot['enable_split'])  ? "checked" : "";?>/>
									       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
									    </label>
			                    	</div>

			                    	<?php
		                    		$split_slot_duration = ""; 
		                    		$split_slot_identity = "min";
	                    			if(isset($individual_time_slot['split_slot_duration']) && !empty($individual_time_slot['split_slot_duration'])) {
	                    				$split_time_slot_duration = (int)$individual_time_slot['split_slot_duration'];
	                    				if($split_time_slot_duration <= 59) {
	                    					$split_slot_duration = $split_time_slot_duration;
	                    				} else {
	                    					$split_time_slot_duration = $split_time_slot_duration/60;
	                    					$helper = new Coderockz_Woo_Delivery_Helper();
	                    					if($helper->containsDecimal($split_time_slot_duration)){
	                    						$split_slot_duration = $split_time_slot_duration*60;
	                    						$split_slot_identity = "min";
	                    					} else {
	                    						$split_slot_duration = $split_time_slot_duration;
	                    						$split_slot_identity = "hour";
	                    					}
	                    				}
	                    			}
			                    	?>
			                    	<div class="coderockz-woo-delivery-split-time-duration-section">
				                    	<div class="coderockz-woo-delivery-form-group">
				                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_split_time_slot_duration"><?php _e('Each splited Time Slot Duration', 'coderockz-woo-delivery'); ?></label>
				                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Each splited delivery time slot duration that is specified here. Only numerical value is accepted."><span class="dashicons dashicons-editor-help"></span></p>
				                    		<div id="coderockz_split_time_slot_duration" class="coderockz_split_time_slot_duration">
					                        	<input class="coderockz_split_time_slot_duration_time coderockz-woo-delivery-number-field" type="number" min="1" onkeyup="if(!Number.isInteger(Number(this.value)) || this.value < 1) this.value = null;" value="<?php echo $split_slot_duration; ?>" placeholder="" autocomplete="off"/>
					                        	<select class="coderockz-woo-delivery-select-field coderockz_split_time_slot_duration_format" name="coderockz_split_time_slot_duration_format">
													<option value="min" <?php selected($split_slot_identity,"min",true); ?>>Minutes</option>
													<option value="hour" <?php selected($split_slot_identity,"hour",true); ?>>Hour</option>
												</select>
				                        	</div>
				                        	<p class="coderockz_split_time_slot_duration_notice">Time slot duration is required</p>
				                    	</div>


				                    	<div class="coderockz-woo-delivery-form-group">
				                        	<span class="coderockz-woo-delivery-form-label"><?php _e('Make each splited timeslot single', 'coderockz-woo-delivery'); ?></span>
				                        	<p class="coderockz-woo-delivery-tooltip" tooltip="Enable it if you want to make every timeslot single as the timeslot value is the starting time of the time range. Default is disable."><span class="dashicons dashicons-editor-help"></span></p>
										    <label class="coderockz-woo-delivery-toogle-switch">
										       <input type="checkbox" class="coderockz_woo_delivery_enable_custom_splited_time_slot_single" <?php echo isset($individual_time_slot['enable_single_splited_slot']) && !empty($individual_time_slot['enable_single_splited_slot'])  ? "checked" : "";?>/>
										       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
										    </label>
				                    	</div>
			                    	</div>
			                    	<div class="coderockz-woo-delivery-single-time-section">
			                    		<div class="coderockz-woo-delivery-form-group">
				                        	<span class="coderockz-woo-delivery-form-label"><?php _e('Make this timeslot single', 'coderockz-woo-delivery'); ?></span>
				                        	<p class="coderockz-woo-delivery-tooltip" tooltip="Enable it if you want to make this timeslot single as the timeslot value is the starting time of the time range. Default is disable."><span class="dashicons dashicons-editor-help"></span></p>
										    <label class="coderockz-woo-delivery-toogle-switch">
										       <input type="checkbox" class="coderockz_woo_delivery_enable_custom_time_slot_single" <?php echo isset($individual_time_slot['enable_single_slot']) && !empty($individual_time_slot['enable_single_slot'])  ? "checked" : "";?>/>
										       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
										    </label>
				                    	</div>
			                    	</div>
		                    		<div class="coderockz-woo-delivery-form-group">
			                        	<span class="coderockz-woo-delivery-form-label"><?php _e('Hide This TimeSlot for Current Date', 'coderockz-woo-delivery'); ?></span>
			                        	<p class="coderockz-woo-delivery-tooltip" tooltip="Enable it if you want to hide this timeslot current date. Default is disable."><span class="dashicons dashicons-editor-help"></span></p>
									    <label class="coderockz-woo-delivery-toogle-switch">
									       <input type="checkbox" class="coderockz_woo_delivery_hide_time_slot_current_date" <?php echo isset($individual_time_slot['hide_time_slot_current_date']) && !empty($individual_time_slot['hide_time_slot_current_date'])  ? "checked" : "";?>/>
									       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
									    </label>
			                    	</div>

			                    	<?php 
	                    			$timeslot_closing_hour = "";
	            					$timeslot_closing_min = "";
	            					$timeslot_closing_format= "am";
	                    			
	                    			if(isset($individual_time_slot['timeslot_closing_time']) && $individual_time_slot['timeslot_closing_time'] !='') {
	                    				$timeslot_closing_time = (int)$individual_time_slot['timeslot_closing_time'];

	                    				if($timeslot_closing_time > 0 && $timeslot_closing_time <= 59) {

	                    					$timeslot_closing_hour = "12";
	                    					$timeslot_closing_min = sprintf("%02d", $timeslot_closing_time);
	                    					$timeslot_closing_format= "am";
	                    				} elseif($timeslot_closing_time > 59 && $timeslot_closing_time <= 719) {
											$timeslot_closing_min = sprintf("%02d", (int)$timeslot_closing_time%60);
											$timeslot_closing_hour = sprintf("%02d", ((int)$timeslot_closing_time-$timeslot_closing_min)/60);
											$timeslot_closing_format= "am";
											
	                    				} elseif($timeslot_closing_time > 719 && $timeslot_closing_time <= 1439) {
											$timeslot_closing_min = sprintf("%02d", (int)$timeslot_closing_time%60);
											$timeslot_closing_hour = sprintf("%02d", ((int)$timeslot_closing_time-$timeslot_closing_min)/60);
											if($timeslot_closing_hour>12) {
												$timeslot_closing_hour = sprintf("%02d", $timeslot_closing_hour-12);
											}
											$timeslot_closing_format= "pm";
	                    				} elseif($timeslot_closing_time === 0) {
											$timeslot_closing_min = "00";
											$timeslot_closing_hour = "12";
											$timeslot_closing_format= "am";
	                    				}

	                    			}
		                    		?>
			                    	<div class="coderockz-woo-delivery-form-group">
			                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_timeslot_closing_time"><?php _e('Hide this timeslot at', 'coderockz-woo-delivery'); ?></label>
			                    		<p class="coderockz-woo-delivery-tooltip" tooltip="If you don't want to show the timeslot after a certain time if the current date is selected as delivery date, put the time here. Only numerical value is accepted."><span class="dashicons dashicons-editor-help"></span></p>
			                    		<div id="coderockz_woo_delivery_timeslot_closing_time" class="coderockz_woo_delivery_timeslot_closing_time">
			                    			
			                        	<input name="coderockz_woo_delivery_timeslot_closing_time_hour" type="number" class="coderockz-woo-delivery-number-field coderockz_woo_delivery_timeslot_closing_time_hour" max="12" min="1" onkeyup="if(!Number.isInteger(Number(this.value)) || this.value > 12 || this.value < 1) this.value = null;" value="<?php echo $timeslot_closing_hour; ?>" placeholder="Hour" autocomplete="off"/>
			                        	<input name="coderockz_woo_delivery_timeslot_closing_time_min" type="number" class="coderockz-woo-delivery-number-field coderockz_woo_delivery_timeslot_closing_time_min" max="59" min="0" onkeyup="if(!Number.isInteger(Number(this.value)) || this.value > 59 || this.value < 0) this.value = null;" value="<?php echo $timeslot_closing_min; ?>" placeholder="Minute" autocomplete="off"/>
			                        	<select class="coderockz-woo-delivery-select-field coderockz_woo_delivery_timeslot_closing_time_format" name="coderockz_woo_delivery_timeslot_closing_time_format">
											<option value="am" <?php selected($timeslot_closing_format,"am",true); ?>>AM</option>
											<option value="pm" <?php selected($timeslot_closing_format,"pm",true); ?>>PM</option>
										</select>
			                        	</div>
			                    	</div>

			                    	<div class="coderockz-woo-delivery-form-group">
			                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Show This Timeslot Only At', 'coderockz-woo-delivery'); ?></label>
			                    		<p class="coderockz-woo-delivery-tooltip" tooltip="If you want to show the timeslot only at specific date then specify here the dates"><span class="dashicons dashicons-editor-help"></span></p>
			                        	<input style="width:495px" type="text" class="coderockz-woo-delivery-custom-time-slot-specific-date coderockz-woo-delivery-input-field" value="<?php echo isset($individual_time_slot['only_specific_date']) && $individual_time_slot['only_specific_date'] !='' ? $individual_time_slot['only_specific_date'] : ""; ?>" placeholder="<?php _e('Comma(,) Separated Date, format yyyy-mm-dd(Ex. 2020-12-24)', 'coderockz-woo-delivery'); ?>" autocomplete="off"/>
			                    	</div>
			                    	<div class="coderockz-woo-delivery-form-group">
			                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Hide This Timeslot At', 'coderockz-woo-delivery'); ?></label>
			                    		<p class="coderockz-woo-delivery-tooltip" tooltip="If you want to hide the timeslot only at specific date then specify here the dates"><span class="dashicons dashicons-editor-help"></span></p>
			                        	<input style="width:495px" type="text" class="coderockz-woo-delivery-custom-time-slot-specific-date-close coderockz-woo-delivery-input-field" value="<?php echo isset($individual_time_slot['only_specific_date_close']) && $individual_time_slot['only_specific_date_close'] !='' ? $individual_time_slot['only_specific_date_close'] : ""; ?>" placeholder="<?php _e('Comma(,) Separated Date, format yyyy-mm-dd(Ex. 2020-12-24)', 'coderockz-woo-delivery'); ?>" autocomplete="off"/>
			                    	</div>
			                    	<div class="coderockz-woo-delivery-form-group">
			                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Maximum Order For This Slot', 'coderockz-woo-delivery'); ?></label>
			                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Each time slot take maximum number of orders that is specified here. After reaching the maximum order, the time slot is disabled automatically. Only numerical value is accepted."><span class="dashicons dashicons-editor-help"></span></p>
			                        	<input type="number" class="coderockz-woo-delivery-custom-time-slot-order coderockz-woo-delivery-number-field" min="1" onkeyup="if(!Number.isInteger(Number(this.value)) || this.value < 1) this.value = null;" value="<?php echo isset($individual_time_slot['max_order']) && $individual_time_slot['max_order'] !='' ? $individual_time_slot['max_order'] : ""; ?>" placeholder="" autocomplete="off"/>
			                    	</div>
			                    	<div class="coderockz-woo-delivery-form-group">
			                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Time Slot Fee', 'coderockz-woo-delivery'); ?></label>
			                    		<p class="coderockz-woo-delivery-tooltip" tooltip="If anyone select this time slot, fee specified here is applied with the total."><span class="dashicons dashicons-editor-help"></span></p>
			                        	
			                        	<input type="text" class="coderockz-woo-delivery-custom-time-slot-fee coderockz-woo-delivery-input-field" onkeyup="if(isNaN(parseFloat(Number(this.value))) || isNaN(parseInt(Number(this.value), 10))) this.value = null;" value="<?php echo isset($individual_time_slot['fee']) && $individual_time_slot['fee'] !='' ? $individual_time_slot['fee'] : ""; ?>" style="width:245px;border-radius: 3px 0 0 3px;" autocomplete="off" placeholder="Fee"/><span class="coderockz-woo-delivery-currency-code" style="width:40px;"><?php echo $currency_code; ?></span>
			                    	</div>

			                    	<div class="coderockz-woo-delivery-form-group">
			                    		<label class="coderockz-woo-delivery-form-label coderockz-woo-delivery-checkbox-label"><?php _e('Hide For', 'coderockz-woo-delivery'); ?></label>
			                    		<p class="coderockz-woo-delivery-tooltip coderockz-woo-delivery-checkbox-tooltip" tooltip="This time slot will be hidden for the selected days."><span class="dashicons dashicons-editor-help"></span></p>
			                    		<div class="coderockz-woo-delivery-slot-enable-for" style="display:inline-block">
			                    		<input type="checkbox" name="coderockz_woo_delivery_custom_time_slot_disable" value="6" <?php echo isset($individual_time_slot['disable_for']) && !empty($individual_time_slot['disable_for']) && in_array("6",$individual_time_slot['disable_for']) ? "checked" : "";?>><label class="coderockz-woo-delivery-checkbox-field-text">Saturday</label><br/>
										<input type="checkbox" name="coderockz_woo_delivery_custom_time_slot_disable" value="0" <?php echo isset($individual_time_slot['disable_for']) && !empty($individual_time_slot['disable_for']) && in_array("0",$individual_time_slot['disable_for']) ? "checked" : "";?>><label class="coderockz-woo-delivery-checkbox-field-text">Sunday</label><br/>
										<input type="checkbox" name="coderockz_woo_delivery_custom_time_slot_disable" value="1" <?php echo isset($individual_time_slot['disable_for']) && !empty($individual_time_slot['disable_for']) && in_array("1",$individual_time_slot['disable_for']) ? "checked" : "";?>><label class="coderockz-woo-delivery-checkbox-field-text">Monday</label><br/>
										<input type="checkbox" name="coderockz_woo_delivery_custom_time_slot_disable" value="2" <?php echo isset($individual_time_slot['disable_for']) && !empty($individual_time_slot['disable_for']) && in_array("2",$individual_time_slot['disable_for']) ? "checked" : "";?>><label class="coderockz-woo-delivery-checkbox-field-text">Tuesday</label><br/>
										<input type="checkbox" name="coderockz_woo_delivery_custom_time_slot_disable" value="3" <?php echo isset($individual_time_slot['disable_for']) && !empty($individual_time_slot['disable_for']) && in_array("3",$individual_time_slot['disable_for']) ? "checked" : "";?>><label class="coderockz-woo-delivery-checkbox-field-text">Wednesday</label><br/>
										<input type="checkbox" name="coderockz_woo_delivery_custom_time_slot_disable" value="4" <?php echo isset($individual_time_slot['disable_for']) && !empty($individual_time_slot['disable_for']) && in_array("4",$individual_time_slot['disable_for']) ? "checked" : "";?>><label class="coderockz-woo-delivery-checkbox-field-text">Thursday</label><br/>
										<input type="checkbox" name="coderockz_woo_delivery_custom_time_slot_disable" value="5" <?php echo isset($individual_time_slot['disable_for']) && !empty($individual_time_slot['disable_for']) && in_array("5",$individual_time_slot['disable_for']) ? "checked" : "";?>><label class="coderockz-woo-delivery-checkbox-field-text">Friday</label><br/>
										</div>
			                    	</div>
			                    	<div class="coderockz-woo-delivery-form-group">
			                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Hide This Timeslot For Categories', 'coderockz-woo-delivery'); ?></label>
			                    		<p class="coderockz-woo-delivery-tooltip" tooltip="If you want to hide the timeslot for a specific category then select the category from here."><span class="dashicons dashicons-editor-help"></span></p>
			                        	<select id="coderockz_woo_delivery_timeslot_hide_categories2" name="coderockz_woo_delivery_timeslot_hide_categories[]" class="coderockz_woo_delivery_timeslot_hide_categories2" multiple>
		                                
		                                <?php
		                                $timeslot_hide_categories = [];
		                                if(isset($individual_time_slot['hide_categories']) && !empty($individual_time_slot['hide_categories'])) {
			                                foreach ($individual_time_slot['hide_categories'] as $hide_cat) {
			                                	$timeslot_hide_categories[] = stripslashes($hide_cat);
			                                }
		                            	}

		                                foreach ($all_categories as $cat) {
		                                	$selected = isset($individual_time_slot['hide_categories']) && !empty($individual_time_slot['hide_categories']) && in_array(htmlspecialchars_decode($cat->name),$timeslot_hide_categories) ? "selected" : "";
		                                    echo '<option value="'.$cat->name.'" '.$selected.'>'.$cat->name.'</option>';
		                                }
		                                ?>
		                                </select>
			                    	</div>
			                    	<?php if(get_option('coderockz_woo_delivery_large_product_list') == false) { ?>
			                    	<div class="coderockz-woo-delivery-form-group">
			                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Hide This Timeslot For Products', 'coderockz-woo-delivery'); ?></label>
			                    		<p class="coderockz-woo-delivery-tooltip" tooltip="If you want to hide the timeslot for a specific product then select the product from here."><span class="dashicons dashicons-editor-help"></span></p>
			                        	<select id="coderockz_woo_delivery_timeslot_hide_products2" name="coderockz_woo_delivery_timeslot_hide_products[]" class="coderockz_woo_delivery_timeslot_hide_products2" multiple>
		                                
		                                <?php
		                                foreach ($store_products as $key=>$value) {

		                                	$selected = isset($individual_time_slot['hide_products']) && !empty($individual_time_slot['hide_products']) && in_array($key,$individual_time_slot['hide_products']) ? "selected" : "";
		                                    echo '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
		                                }
		                                ?>
		                                </select>
			                    	</div>
			                    	<?php } else { ?>

		                    		<div class="coderockz-woo-delivery-form-group">
			                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Hide This Timeslot For Products', 'coderockz-woo-delivery'); ?></label>
			                    		<p class="coderockz-woo-delivery-tooltip" tooltip="If you want to hide the timeslot for a specific product then select the product from here."><span class="dashicons dashicons-editor-help"></span></p>
			                        	<?php 
			                        	$hide_products_input = isset($individual_time_slot['hide_products']) && !empty($individual_time_slot['hide_products']) ? $individual_time_slot['hide_products'] : array();
			                        	$hide_products_input = implode(",",$hide_products_input);
			                        	?>
			                    		<input name="coderockz_woo_delivery_restrict_delivery_individual_product_input" type="text" class="coderockz_woo_delivery_timeslot_hide_products2_input coderockz-woo-delivery-input-field" value="<?php echo $hide_products_input; ?>" placeholder="Comma separated Product/Variation ID" autocomplete="off"/>
			                    	</div>

			                    	<?php } ?>
			                    	<div class="coderockz-woo-delivery-form-group">
			                        	<span class="coderockz-woo-delivery-form-label"><?php _e('Show If Cart Has Other Categories Or Products', 'coderockz-woo-delivery'); ?></span>
			                        	<p class="coderockz-woo-delivery-tooltip" tooltip="If there is hidden category's products or hidden products in the cart then whatever there are other category's products or other products, th timeslot is hidden. If you want to reverse this enable it."><span class="dashicons dashicons-editor-help"></span></p>
									    <label class="coderockz-woo-delivery-toogle-switch">
									       <input type="checkbox" class="coderockz_woo_delivery_time_slot_shown_other_categories_products" <?php echo isset($individual_time_slot['time_slot_shown_other_categories_products']) && !empty($individual_time_slot['time_slot_shown_other_categories_products'])  ? "checked" : "";?>/>
									       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
									    </label>
			                    	</div>
			                    	<div class="coderockz-woo-delivery-form-group">
			                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_custom_time_slot_more_settings"><?php _e('Want More Settings Based On', 'coderockz-woo-delivery'); ?></label>
			                    		<p class="coderockz-woo-delivery-tooltip" tooltip="If you select 'Shipping State' then you can get more settings based on shipping state and if you select 'Shipping Postcode/ZIP' then you can get more settings based on shipping postcode"><span class="dashicons dashicons-editor-help"></span></p>
			                    		<select class="coderockz-woo-delivery-select-field coderockz_woo_delivery_custom_time_slot_more_settings" name="coderockz_woo_delivery_custom_time_slot_more_settings">
											<option value="" <?php if(isset($individual_time_slot['more_settings']) && $individual_time_slot['more_settings'] == ""){ echo "selected"; } ?>>Select your choice</option>
											<option value="zone" <?php if(isset($individual_time_slot['more_settings']) && $individual_time_slot['more_settings'] == "zone"){ echo "selected"; } ?>>Shipping Zone</option>
											<option value="state" <?php if(isset($individual_time_slot['more_settings']) && $individual_time_slot['more_settings'] == "state"){ echo "selected"; } ?>>Shipping State</option>
											<option value="postcode" <?php if(isset($individual_time_slot['more_settings']) && $individual_time_slot['more_settings'] == "postcode"){ echo "selected"; } ?>>Shipping Postcode/ZIP</option>
										</select>
			                    	</div>
			                    	<div class="coderockz-woo-delivery-custom_timeslot-more-zone">
				                    	<div class="coderockz-woo-delivery-form-group">
				                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Hide For Shipping Zone', 'coderockz-woo-delivery'); ?></label>
				                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Timeslot is hidden for the selected shipping zone."><span class="dashicons dashicons-editor-help"></span></p>
				                        	<select id="coderockz_woo_delivery_custom_time_disable_zone2" name="coderockz_woo_delivery_custom_time_disable_zone[]" class="coderockz_woo_delivery_custom_time_disable_zone2" multiple>
			                                <?php
			                                foreach ($zone_name as $key => $value) {
			                                	$selected = isset($individual_time_slot['disable_zone']) && !empty($individual_time_slot['disable_zone']) && in_array($key,$individual_time_slot['disable_zone']) ? "selected" : "";
			                                    echo '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
			                                }
			                                ?>
			                                </select>
				                    	</div>
			                    	</div>
			                    	<div class="coderockz-woo-delivery-custom_timeslot-more-state">
				                    	<div class="coderockz-woo-delivery-form-group">
				                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Hide For Shipping Zone Regions', 'coderockz-woo-delivery'); ?></label>
				                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Timeslot is hidden for the selected shipping zone regions."><span class="dashicons dashicons-editor-help"></span></p>
				                        	<select id="coderockz_woo_delivery_custom_time_disable_regions2" name="coderockz_woo_delivery_custom_time_disable_regions[]" class="coderockz_woo_delivery_custom_time_disable_regions2" multiple>
			                                <?php
			                                foreach ($zone_regions as $key => $value) {
			                                	$selected = isset($individual_time_slot['disable_state']) && !empty($individual_time_slot['disable_state']) && in_array($key,$individual_time_slot['disable_state']) ? "selected" : "";
			                                    echo '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
			                                }
			                                ?>
			                                </select>
				                    	</div>
			                    	</div>
		                    		
			                    	<div class="coderockz-woo-delivery-custom_timeslot-more-postcode">	
			                    		<div class="coderockz-woo-delivery-form-group">
				                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Hide For Shipping Zone PostCode/Zip', 'coderockz-woo-delivery'); ?></label>
				                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Timeslot is hidden for the selected shipping zone postcode/zip."><span class="dashicons dashicons-editor-help"></span></p>
				                        	<select id="coderockz_woo_delivery_custom_time_disable_postcode2" name="coderockz_woo_delivery_custom_time_disable_postcode[]" class="coderockz_woo_delivery_custom_time_disable_postcode2" multiple>
			                                <?php
			                                foreach ($zone_post_code as $key => $value) {
			                                	$selected = isset($individual_time_slot['disable_postcode']) && !empty($individual_time_slot['disable_postcode']) && in_array($value,$individual_time_slot['disable_postcode']) ? "selected" : "";
			                                    echo '<option value="'.$value.'" '.$selected.'>'.$value.'</option>';		                                }
			                                ?>
			                                </select>
				                    	</div>
			                    	</div>
								    <button class="coderockz-woo-delivery-custom-time-slot-saving coderockz-woo-delivery-submit-btn"><?php _e('Save Changes', 'coderockz-woo-delivery'); ?></button>
								  </div>

	                    		<?php
						  		}
						  		
						  	}
						  	
						  ?>
	                	</div>
                	</div>

                </div>
			</div>
			<div data-tab="tab11" class="coderockz-woo-delivery-tabcontent">
				<div class="coderockz-woo-delivery-card">
					<p class="coderockz-woo-delivery-card-header"><?php _e('General Pickup Time Settings', 'coderockz-woo-delivery'); ?></p>
					<div class="coderockz-woo-delivery-card-body">
						<p class="coderockz-woo-delivery-pickup-time-tab-notice"><span class="dashicons dashicons-yes"></span><?php _e(' Settings Changed Successfully', 'coderockz-woo-delivery'); ?></p>
	                    <form action="" method="post" id ="coderockz_pickup_time_form_submit">
	                        <?php wp_nonce_field('coderockz_woo_delivery_nonce'); ?>

	                    	<div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label"><?php _e('Enable Pickup Time', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="Enable Pickup Time select field in woocommerce order checkout page."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_enable_pickup_time">
							       <input type="checkbox" name="coderockz_enable_pickup_time" id="coderockz_enable_pickup_time" <?php echo (isset($pickup_time_settings['enable_pickup_time']) && !empty($pickup_time_settings['enable_pickup_time'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label"><?php _e('Make Pickup Time Field Mandatory', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="Make Pickup Time select field mandatory in woocommerce order checkout page. Default is optional."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_pickup_time_mandatory">
							       <input type="checkbox" name="coderockz_pickup_time_mandatory" id="coderockz_pickup_time_mandatory" <?php echo (isset($pickup_time_settings['pickup_time_mandatory']) && !empty($pickup_time_settings['pickup_time_mandatory'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_pickup_time_field_label"><?php _e('Pickup Time Field Heading', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Pickup Time select field heading. Default is Pickup Time."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_pickup_time_field_label" name="coderockz_pickup_time_field_label" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($pickup_time_settings['field_label']) && !empty($pickup_time_settings['field_label'])) ? stripslashes(esc_attr($pickup_time_settings['field_label'])) : "" ?>" placeholder="" autocomplete="off"/>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_pickup_time_field_placeholder"><?php _e('Pickup Time Field Placeholder', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Pickup Time select field label and placeholder. Default is Pickup Time."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_pickup_time_field_placeholder" name="coderockz_pickup_time_field_placeholder" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($pickup_time_settings['field_placeholder']) && !empty($pickup_time_settings['field_placeholder'])) ? stripslashes(esc_attr($pickup_time_settings['field_placeholder'])) : "" ?>" placeholder="" autocomplete="off"/>
	                    	</div>
	                    	<?php 
                    			$pickup_start_hour = "";
            					$pickup_start_min = "";
            					$pickup_start_format= "am";
                    			
                    			if(isset($pickup_time_settings['pickup_time_starts']) && $pickup_time_settings['pickup_time_starts'] !='') {
                    				$pickup_time_starts = (int)$pickup_time_settings['pickup_time_starts'];

                    				if($pickup_time_starts == 0) {
		            					$pickup_start_hour = "12";
		            					$pickup_start_min = "00";
		            					$pickup_start_format= "am";
		            				} elseif($pickup_time_starts > 0 && $pickup_time_starts <= 59) {

                    					$pickup_start_hour = "12";
                    					$pickup_start_min = sprintf("%02d", $pickup_time_starts);
                    					$pickup_start_format= "am";
                    				} elseif($pickup_time_starts > 59 && $pickup_time_starts <= 719) {
										$pickup_start_min = sprintf("%02d", (int)$pickup_time_starts%60);
										$pickup_start_hour = sprintf("%02d", ((int)$pickup_time_starts-$pickup_start_min)/60);
										$pickup_start_format= "am";
										
                    				} else {
										$pickup_start_min = sprintf("%02d", (int)$pickup_time_starts%60);
										$pickup_start_hour = sprintf("%02d", ((int)$pickup_time_starts-$pickup_start_min)/60);
										if($pickup_start_hour>12) {
											$pickup_start_hour = sprintf("%02d", $pickup_start_hour-12);
										}
										$pickup_start_format= "pm";
                    				}

                    			}
                    		?>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_pickup_time_slot_starts"><?php _e('Pickup Time Slot Starts From', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Pickup Time starts from the time that is specified here. Only numerical value is accepted."><span class="dashicons dashicons-editor-help"></span></p>
	                    		<div id="coderockz_pickup_time_slot_starts" class="coderockz_pickup_time_slot_starts">
	                    			
	                        	<input name="coderockz_pickup_time_slot_starts_hour" type="number" class="coderockz-woo-delivery-number-field" max="12" min="1" onkeyup="if(!Number.isInteger(Number(this.value)) || this.value > 12 || this.value < 1) this.value = null;" value="<?php echo $pickup_start_hour; ?>" placeholder="Hour" autocomplete="off"/>
	                        	<input name="coderockz_pickup_time_slot_starts_min" type="number" class="coderockz-woo-delivery-number-field" max="59" min="0" onkeyup="if(!Number.isInteger(Number(this.value)) || this.value > 59 || this.value < 0) this.value = null;" value="<?php echo $pickup_start_min; ?>" placeholder="Minute" autocomplete="off"/>
	                        	<select class="coderockz-woo-delivery-select-field" name="coderockz_pickup_time_slot_starts_format">
									<option value="am" <?php selected($pickup_start_format,"am",true); ?>>AM</option>
									<option value="pm" <?php selected($pickup_start_format,"pm",true); ?>>PM</option>
								</select>
	                        	</div>
	                    	</div>
	                    	<?php 
                    			$pickup_end_hour = "";
            					$pickup_end_min = "";
            					$pickup_end_format= "am";
                    			
                    			if(isset($pickup_time_settings['pickup_time_ends']) && $pickup_time_settings['pickup_time_ends'] !='') {
                    				$pickup_time_ends = (int)$pickup_time_settings['pickup_time_ends'];
                    				if($pickup_time_ends == 0) {
		            					$pickup_end_hour = "12";
		            					$pickup_end_min = "00";
		            					$pickup_end_format= "am";
		            				} elseif($pickup_time_ends > 0 && $pickup_time_ends <= 59) {
                    					$pickup_end_hour = "12";
                    					$pickup_end_min = sprintf("%02d", $pickup_time_ends);
                    					$pickup_end_format= "am";
                    				} elseif($pickup_time_ends > 59 && $pickup_time_ends <= 719) {
										$pickup_end_min = sprintf("%02d", (int)$pickup_time_ends%60);
										$pickup_end_hour = sprintf("%02d", ((int)$pickup_time_ends-$pickup_end_min)/60);
										$pickup_end_format= "am";
										
                    				} elseif($pickup_time_ends > 719 && $pickup_time_ends <= 1439) {
										$pickup_end_min = sprintf("%02d", (int)$pickup_time_ends%60);
										$pickup_end_hour = sprintf("%02d", ((int)$pickup_time_ends-$pickup_end_min)/60);
										if($pickup_end_hour>12) {
											$pickup_end_hour = sprintf("%02d", $pickup_end_hour-12);
										}
										$pickup_end_format= "pm";
                    				} elseif($pickup_time_ends == 1440) {
										$pickup_end_min = "00";
										$pickup_end_hour = "12";
										$pickup_end_format= "am";
                    				}


                    			}
                    		?>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_delivery_time_slot_ends"><?php _e('Pickup Time Slot Ends At', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Pickup Time ends at the time that is specified here. Only numerical value is accepted."><span class="dashicons dashicons-editor-help"></span></p>
	                    		<div id="coderockz_pickup_time_slot_ends" class="coderockz_pickup_time_slot_ends">
	                        	<input name="coderockz_pickup_time_slot_ends_hour" type="number" class="coderockz-woo-delivery-number-field" max="12" min="1" onkeyup="if(!Number.isInteger(Number(this.value)) || this.value > 12 || this.value < 1) this.value = null;" value="<?php echo $pickup_end_hour; ?>" placeholder="Hour" autocomplete="off"/>
	                        	<input name="coderockz_pickup_time_slot_ends_min" type="number" class="coderockz-woo-delivery-number-field" max="59" min="0" onkeyup="if(!Number.isInteger(Number(this.value)) || this.value > 59 || this.value < 0) this.value = null;" value="<?php echo $pickup_end_min; ?>" placeholder="Minute" autocomplete="off"/>
	                        	<select class="coderockz-woo-delivery-select-field" name="coderockz_pickup_time_slot_ends_format">
									<option value="am" <?php selected($pickup_end_format,"am",true); ?>>AM</option>
									<option value="pm" <?php selected($pickup_end_format,"pm",true); ?>>PM</option>
								</select>
	                        	</div>
	                        	<p class="coderockz_pickup_end_time_greater_notice">End Time Must after Start Time</p>
	                    	</div>
	                    	<?php
	                    		$pickup_duration = ""; 
	                    		$pickup_identity = "min";
                    			if(isset($pickup_time_settings['each_time_slot']) && !empty($pickup_time_settings['each_time_slot'])) {
                    				$pickup_time_slot_duration = (int)$pickup_time_settings['each_time_slot'];
                    				if($pickup_time_slot_duration <= 59) {
                    					$pickup_duration = $pickup_time_slot_duration;
                    				} else {
                    					$pickup_time_slot_duration = $pickup_time_slot_duration/60;
                    					$helper = new Coderockz_Woo_Delivery_Helper();
                    					if($helper->containsDecimal($pickup_time_slot_duration)){
                    						$pickup_duration = $pickup_time_slot_duration*60;
                    						$pickup_identity = "min";
                    					} else {
                    						$pickup_duration = $pickup_time_slot_duration;
                    						$pickup_identity = "hour";
                    					}
                    				}
                    			}
	                    	?>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_pickup_time_slot_duration"><?php _e('Each Pickup Time Slot Duration', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Each pickup time slot duration that is specified here. Only numerical value is accepted."><span class="dashicons dashicons-editor-help"></span></p>
	                    		<div id="coderockz_pickup_time_slot_duration" class="coderockz_pickup_time_slot_duration">
	                        	<input name="coderockz_pickup_time_slot_duration_time" type="number" min="1" onkeyup="if(!Number.isInteger(Number(this.value)) || this.value < 1) this.value = null;" class="coderockz-woo-delivery-number-field" value="<?php echo $pickup_duration; ?>" placeholder="" autocomplete="off"/>
	                        	<select class="coderockz-woo-delivery-select-field" name="coderockz_pickup_time_slot_duration_format">
									<option value="min" <?php selected($pickup_identity,"min",true); ?>>Minutes</option>
									<option value="hour" <?php selected($pickup_identity,"hour",true); ?>>Hour</option>
								</select>
	                        	</div>
	                    	</div>

	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_pickup_time_maximum_order"><?php _e('Maximum Pickup Per Time Slot', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Each time slot take maximum number of pickups that is specified here. After reaching the maximum pickup, the time slot is disabled automaticaly. Only numerical value is accepted. Blank this field means each time slot takes unlimited pickup."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_pickup_time_maximum_order" name="coderockz_pickup_time_maximum_order" type="number" class="coderockz-woo-delivery-number-field" min="1" onkeyup="if(!Number.isInteger(Number(this.value)) || this.value < 1) this.value = null;" value="<?php echo (isset(get_option('coderockz_woo_delivery_pickup_settings')['max_pickup_per_slot']) && !empty(get_option('coderockz_woo_delivery_pickup_settings')['max_pickup_per_slot'])) ? stripslashes(esc_attr(get_option('coderockz_woo_delivery_pickup_settings')['max_pickup_per_slot'])) : "" ?>" placeholder="" autocomplete="off"/>
	                    	</div>

	                    	<div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label"><?php _e('Individual Location (If Pickup Location Enabled) Wise Max Pickup Per Time Slot ' , 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="if you want Maximum pickup per slot working for every individual Location then enable the option. Default is disable"><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_woo_delivery_max_pickup_consider_location">
							       <input type="checkbox" name="coderockz_woo_delivery_max_pickup_consider_location" id="coderockz_woo_delivery_max_pickup_consider_location" <?php echo (isset($pickup_time_settings['max_pickup_consider_location']) && !empty($pickup_time_settings['max_pickup_consider_location'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>

	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_pickup_time_format"><?php _e('Pickup Time format', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Time format that is used in everywhere which is available by this plugin. Default is 12 Hours."><span class="dashicons dashicons-editor-help"></span></p>
	                    		<select class="coderockz-woo-delivery-select-field" name="coderockz_pickup_time_format">

	                    			<option value="" <?php if(isset($pickup_time_settings['time_format']) && $pickup_time_settings['time_format'] == ""){ echo "selected"; } ?>><?php _e('Select Time Format', 'coderockz-woo-delivery'); ?></option>
									<option value="12" <?php if(isset($pickup_time_settings['time_format']) && $pickup_time_settings['time_format'] == "12"){ echo "selected"; } ?>>12 Hours</option>
									<option value="24" <?php if(isset($pickup_time_settings['time_format']) && $pickup_time_settings['time_format'] == "24"){ echo "selected"; } ?>>24 Hours</option>
								</select>
	                    	</div>

	                    	<div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label"><?php _e('Don\'t Consider Order for Maximum Limit if Pickup Status Completed ' , 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="if you want to free up a slot by making the pickup status completed that is already reached for maximum limit then enable the option."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_delivery_free_up_slot_for_pickup_completed">
							       <input type="checkbox" name="coderockz_delivery_free_up_slot_for_pickup_completed" id="coderockz_delivery_free_up_slot_for_pickup_completed" <?php echo (isset($pickup_time_settings['free_up_slot_for_pickup_completed']) && !empty($pickup_time_settings['free_up_slot_for_pickup_completed'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>

	                    	<div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label"><?php _e('Disable Current Time Slot', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="Make the time slot disabled that has the current time. In default, the time slot isn't disabled that has the current time."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_pickup_time_disable_current_time_slot">
							       <input type="checkbox" name="coderockz_pickup_time_disable_current_time_slot" id="coderockz_pickup_time_disable_current_time_slot" <?php echo (isset($pickup_time_settings['disabled_current_pickup_time_slot']) && !empty($pickup_time_settings['disabled_current_pickup_time_slot'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label"><?php _e('Auto Select 1st Available Time', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="Enable the option if you want to select the first available time based on date automatically and shown in the pickup time field. Default is disable."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_auto_select_first_pickup_time">
							       <input type="checkbox" name="coderockz_auto_select_first_pickup_time" id="coderockz_auto_select_first_pickup_time" <?php echo (isset(get_option('coderockz_woo_delivery_pickup_settings')['auto_select_first_time']) && !empty(get_option('coderockz_woo_delivery_pickup_settings')['auto_select_first_time'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label"><?php _e('Hide Searchbox From Time Field Dropdown', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="If you want to hide the searchbox from pickup time field dropdown, enable it. Default is Showing searhbox."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_hide_searchbox_pickup_field_dropdown">
							       <input type="checkbox" name="coderockz_hide_searchbox_pickup_field_dropdown" id="coderockz_hide_searchbox_pickup_field_dropdown" <?php echo (isset(get_option('coderockz_woo_delivery_pickup_settings')['hide_searchbox']) && !empty(get_option('coderockz_woo_delivery_pickup_settings')['hide_searchbox'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>
	                        <input class="coderockz-woo-delivery-submit-btn" type="submit" name="coderockz_pickup_time_form_submit" value="<?php _e('Save Changes', 'coderockz-woo-delivery'); ?>" />

	                    </form>
                	</div>

                </div>
			</div>
			<div data-tab="tab12" class="coderockz-woo-delivery-tabcontent">
				<div class="coderockz-woo-delivery-card">
					<p class="coderockz-woo-delivery-card-header"><?php _e('Custom Pickup Time Slot Settings', 'coderockz-woo-delivery'); ?></p>
					<div class="coderockz-woo-delivery-card-body">
						<p class="coderockz-woo-delivery-pickup-slot-notice"><span class="dashicons dashicons-yes"></span><?php _e(' Settings Changed Successfully', 'coderockz-woo-delivery'); ?></p>
						<p class="coderockz-woo-delivery-timezone-tab-warning"><span class="dashicons dashicons-megaphone"></span><?php _e(' Enable custom pickup slot for pickup makes the Time Slot Starts From, Time Slot Ends At, Each Time Slot Duration, Maximum Order Per Time Slot field unworkable from Pickup Time Tab', 'coderockz-woo-delivery'); ?></p>
						
						<form action="" method="post" id ="coderockz_woo_delivery_custom_pickup_slot_settings_submit">
							<?php wp_nonce_field('coderockz_woo_delivery_nonce'); ?>
		                    <div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label"><?php _e('Enable Custom Pickup Slot For Pickup', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="If you want to make pickup time slot as you want, enable this option. Default is disable."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_woo_delivery_enable_custom_pickup_slot">
							       <input type="checkbox" name="coderockz_woo_delivery_enable_custom_pickup_slot" id="coderockz_woo_delivery_enable_custom_pickup_slot" <?php echo (isset($pickup_slot_settings['enable_custom_pickup_slot']) && !empty($pickup_slot_settings['enable_custom_pickup_slot'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>
	                    	<input class="coderockz-woo-delivery-submit-btn" type="submit" name="coderockz_woo_delivery_custom_pickup_slot_settings_submit" value="<?php _e('Save Changes', 'coderockz-woo-delivery'); ?>" />
	                    </form>
                	</div>

                </div>
				<div class="coderockz-woo-delivery-card">
					<p class="coderockz-woo-delivery-card-header"><?php _e('Add Custom Pickup Slot', 'coderockz-woo-delivery'); ?></p>
					<div class="coderockz-woo-delivery-card-body">
						<p class="coderockz-woo-delivery-timezone-tab-warning"><span class="dashicons dashicons-megaphone"></span><?php _e(' To know how to make your custom timeslots perfectly ', 'coderockz-woo-delivery'); ?><a href="https://coderockz.com/documentations/make-custom-timeslots-perfectly" target="_blank"><?php _e('click here', 'coderockz-woo-delivery'); ?></a></p>
                    	<input class="coderockz-woo-delivery-add-pickup-slot-btn" type="button" value="<?php _e('Add New Pickup Slot', 'coderockz-woo-delivery'); ?>">
	                    <div id="coderockz-woo-delivery-pickup-slot-accordion">
                    	  <div class="coderockz-woo-delivery-pickup-slot-accordion-header" style="display:none;">
                    	  	<p style="font-size:16px;margin:0;font-weight:700;color:#fff!important;"><span class="coderockz-woo-delivery-pickup-slot-start-hour">Start Time</span><span class="coderockz-woo-delivery-pickup-slot-start-min"></span><span class="coderockz-woo-delivery-pickup-slot-start-format"></span> - <span class="coderockz-woo-delivery-pickup-slot-end-hour">End Time</span><span class="coderockz-woo-delivery-pickup-slot-end-min"></span><span class="coderockz-woo-delivery-pickup-slot-end-format"></span></p>
                    	  </div>
						  <div data-plugin-url="<?php echo CODEROCKZ_WOO_DELIVERY_URL; ?>" class="coderockz-woo-delivery-pickup-slot-accordion-content" style="display:none;">
						  	<div class="coderockz-woo-delivery-form-group" style="margin: 0;position: absolute;right: 45px;top: -31px;">
							    <label class="coderockz-woo-delivery-toogle-switch">
							       <input type="checkbox" class="coderockz-woo-delivery-enable-custom-pickup-slot"/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
                    		</div>
                    		<span class="dashicons dashicons-trash coderockz-woo-delivery-pickup-slot-accordion-delete" style="margin: 0;position: absolute;right: 5px;top: -30px;color:#fff;cursor: pointer;"></span>
                    		<p class="coderockz-woo-delivery-custom-pickup-slot-notice"><span class="dashicons dashicons-yes"></span><?php _e(' Settings Changed Successfully', 'coderockz-woo-delivery'); ?></p>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_delivery_pickup_slot_starts"><?php _e('Pickup Slot Starts From', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Pickup Time Slot starts from the time that is specified here. Only numerical value is accepted."><span class="dashicons dashicons-editor-help"></span></p>
	                    		<div class="coderockz_delivery_pickup_slot_starts coderockz_woo_delivery_custom_pickup_slot_starts">
	                    			
	                        	<input type="number" class="coderockz_woo_delivery_custom_pickup_slot_starts_hour coderockz-woo-delivery-number-field" max="12" min="1" onkeyup="if(!Number.isInteger(Number(this.value)) || this.value > 12 || this.value < 1) this.value = null;" value="" placeholder="Hour" autocomplete="off"/>
	                        	<input type="number" class="coderockz_woo_delivery_custom_pickup_slot_starts_min coderockz-woo-delivery-number-field" max="59" min="0" onkeyup="if(!Number.isInteger(Number(this.value)) || this.value > 59 || this.value < 0) this.value = null;" value="" placeholder="Minute" autocomplete="off"/>
	                        	<select class="coderockz_woo_delivery_custom_pickup_slot_starts_format coderockz-woo-delivery-select-field">
									<option value="am">AM</option>
									<option value="pm">PM</option>
								</select>
	                        	</div>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Pickup Slot Ends At', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Pickup Time Slot ends at the time that is specified here. Only numerical value is accepted."><span class="dashicons dashicons-editor-help"></span></p>
	                    		<div class="coderockz_delivery_pickup_slot_ends coderockz_woo_delivery_custom_pickup_slot_ends">
	                        	<input type="number" class="coderockz_woo_delivery_custom_pickup_slot_ends_hour coderockz-woo-delivery-number-field" max="12" min="1" onkeyup="if(!Number.isInteger(Number(this.value)) || this.value > 12 || this.value < 1) this.value = null;" value="" placeholder="Hour" autocomplete="off"/>
	                        	<input type="number" class="coderockz_woo_delivery_custom_pickup_slot_ends_min coderockz-woo-delivery-number-field" max="59" min="0" onkeyup="if(!Number.isInteger(Number(this.value)) || this.value > 59 || this.value < 0) this.value = null;" value="" placeholder="Minute" autocomplete="off"/>
	                        	<select class="coderockz_woo_delivery_custom_pickup_slot_ends_format coderockz-woo-delivery-select-field" name="coderockz_delivery_pickup_slot_ends_format">
									<option value="am">AM</option>
									<option value="pm">PM</option>
								</select>
	                        	</div>
	                        	<p class="coderockz_custom_end_pickup_greater_notice">End Time Must after Start Time</p>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label"><?php _e('Want to split the timeslot?', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="Enable it if you want to split the timeslot in several timeslot according a fixed interval. Default is disable."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch">
							       <input type="checkbox" class="coderockz_woo_delivery_enable_custom_pickup_slot_split"/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-split-pickup-duration-section">
		                    	<div class="coderockz-woo-delivery-form-group">
		                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Each splited Pickup Slot Duration', 'coderockz-woo-delivery'); ?></label>
		                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Each splited Pickup Slot Duration that is specified here. Only numerical value is accepted."><span class="dashicons dashicons-editor-help"></span></p>
		                    		<div class="coderockz_split_pickup_slot_duration">
			                        	<input type="number" min="1" onkeyup="if(!Number.isInteger(Number(this.value)) || this.value < 1) this.value = null;" class="coderockz-woo-delivery-number-field coderockz_split_pickup_slot_duration_time" value="" placeholder="" autocomplete="off"/>
			                        	<select class="coderockz-woo-delivery-select-field coderockz_split_pickup_slot_duration_format">
											<option value="min">Minutes</option>
											<option value="hour">Hour</option>
										</select>
		                        	</div>
		                        	<p class="coderockz_split_pickup_slot_duration_notice">Time slot duration is required</p>
		                    	</div>

		                    	<div class="coderockz-woo-delivery-form-group">
		                        	<span class="coderockz-woo-delivery-form-label"><?php _e('Make Each Pickup Slot Single', 'coderockz-woo-delivery'); ?></span>
		                        	<p class="coderockz-woo-delivery-tooltip" tooltip="Enable it if you want to make every pickup slot single as the pickup slot value is the starting time of the time range. Default is disable."><span class="dashicons dashicons-editor-help"></span></p>
								    <label class="coderockz-woo-delivery-toogle-switch">
								       <input type="checkbox" class="coderockz_woo_delivery_enable_custom_splited_pickup_slot_single"/>
								       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
								    </label>
		                    	</div>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-single-pickup-section">
	                    		<div class="coderockz-woo-delivery-form-group">
		                        	<span class="coderockz-woo-delivery-form-label"><?php _e('Make Each Pickup Slot Single', 'coderockz-woo-delivery'); ?></span>
		                        	<p class="coderockz-woo-delivery-tooltip" tooltip="Enable it if you want to make every pickup slot single as the pickup slot value is the starting time of the time range. Default is disable."><span class="dashicons dashicons-editor-help"></span></p>
								    <label class="coderockz-woo-delivery-toogle-switch">
								       <input type="checkbox" class="coderockz_woo_delivery_enable_custom_pickup_slot_single"/>
								       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
								    </label>
		                    	</div>
		                    </div>
		                    <div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label"><?php _e('Hide This TimeSlot for Current Date', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="Enable it if you want to hide this timeslot current date. Default is disable."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch">
							       <input type="checkbox" class="coderockz_woo_delivery_hide_custom_pickup_slot_current_date" />
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_pickupslot_closing_time"><?php _e('Hide this timeslot at', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="If you don't want to show the timeslot after a certain time if the current date is selected as delivery date, put the time here. Only numerical value is accepted."><span class="dashicons dashicons-editor-help"></span></p>
	                    		<div id="coderockz_woo_delivery_pickupslot_closing_time" class="coderockz_woo_delivery_pickupslot_closing_time">
	                    			
	                        	<input name="coderockz_woo_delivery_pickupslot_closing_time_hour" type="number" class="coderockz-woo-delivery-number-field coderockz_woo_delivery_pickupslot_closing_time_hour" max="12" min="1" onkeyup="if(!Number.isInteger(Number(this.value)) || this.value > 12 || this.value < 1) this.value = null;" value="" placeholder="Hour" autocomplete="off"/>
	                        	<input name="coderockz_woo_delivery_pickupslot_closing_time_min" type="number" class="coderockz-woo-delivery-number-field coderockz_woo_delivery_pickupslot_closing_time_min" max="59" min="0" onkeyup="if(!Number.isInteger(Number(this.value)) || this.value > 59 || this.value < 0) this.value = null;" value="" placeholder="Minute" autocomplete="off"/>
	                        	<select class="coderockz-woo-delivery-select-field coderockz_woo_delivery_pickupslot_closing_time_format" name="coderockz_woo_delivery_pickupslot_closing_time_format">
									<option value="am">AM</option>
									<option value="pm">PM</option>
								</select>
	                        	</div>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Show This Timeslot Only At', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="If you want to show the timeslot only at specific date then specify here the dates"><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input style="width:495px" type="text" class="coderockz-woo-delivery-custom-pickup-slot-specific-date coderockz-woo-delivery-input-field" value="" placeholder="<?php _e('Comma(,) Separated Date, format yyyy-mm-dd(Ex. 2020-12-24)', 'coderockz-woo-delivery'); ?>" autocomplete="off"/>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Hide This Timeslot At', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="If you want to hide the timeslot only at specific date then specify here the dates"><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input style="width:495px" type="text" class="coderockz-woo-delivery-custom-pickup-slot-specific-date-close coderockz-woo-delivery-input-field" value="" placeholder="<?php _e('Comma(,) Separated Date, format yyyy-mm-dd(Ex. 2020-12-24)', 'coderockz-woo-delivery'); ?>" autocomplete="off"/>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Maximum Pickup For This Slot', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Each time slot take maximum number of pickups that is specified here. After reaching the maximum pickup, the time slot is disabled automaticaly. Only numerical value is accepted. Blank this field or 0 value means each time slot takes unlimited pickup."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input type="number" class="coderockz-woo-delivery-custom-pickup-slot-order coderockz-woo-delivery-number-field" min="1" onkeyup="if(!Number.isInteger(Number(this.value)) || this.value < 1) this.value = null;" value="" placeholder="" autocomplete="off"/>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Pickup Slot Fee', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="If anyone select this pickup slot, fee specified here is applied with the total."><span class="dashicons dashicons-editor-help"></span></p>
	                        	
	                        	<input type="text" class="coderockz-woo-delivery-custom-pickup-slot-fee coderockz-woo-delivery-input-field" onkeyup="if(isNaN(parseFloat(Number(this.value))) || isNaN(parseInt(Number(this.value), 10))) this.value = null;" value="" style="width:245px;border-radius: 3px 0 0 3px;" autocomplete="off" placeholder="Fee"/><span class="coderockz-woo-delivery-currency-code" style="width:40px;"><?php echo $currency_code; ?></span>
	                    	</div>

	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label coderockz-woo-delivery-checkbox-label"><?php _e('Hide For', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip coderockz-woo-delivery-checkbox-tooltip" tooltip="This pickup slot will be hidden for the selected days."><span class="dashicons dashicons-editor-help"></span></p>
	                    		<div class="coderockz-woo-delivery-pickup-slot-enable-for" style="display:inline-block">
	                    		<input type="checkbox" name="coderockz_woo_delivery_custom_pickup_slot_disable" value="6"><label class="coderockz-woo-delivery-checkbox-field-text">Saturday</label><br/>
								<input type="checkbox" name="coderockz_woo_delivery_custom_pickup_slot_disable" value="0"><label class="coderockz-woo-delivery-checkbox-field-text">Sunday</label><br/>
								<input type="checkbox" name="coderockz_woo_delivery_custom_pickup_slot_disable" value="1"><label class="coderockz-woo-delivery-checkbox-field-text">Monday</label><br/>
								<input type="checkbox" name="coderockz_woo_delivery_custom_pickup_slot_disable" value="2"><label class="coderockz-woo-delivery-checkbox-field-text">Tuesday</label><br/>
								<input type="checkbox" name="coderockz_woo_delivery_custom_pickup_slot_disable" value="3"><label class="coderockz-woo-delivery-checkbox-field-text">Wednesday</label><br/>
								<input type="checkbox" name="coderockz_woo_delivery_custom_pickup_slot_disable" value="4"><label class="coderockz-woo-delivery-checkbox-field-text">Thursday</label><br/>
								<input type="checkbox" name="coderockz_woo_delivery_custom_pickup_slot_disable" value="5"><label class="coderockz-woo-delivery-checkbox-field-text">Friday</label><br/>
								</div>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Hide This Timeslot for Pickup Location', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="If you want to hide this timeslot for a specific pickup location then select the pickup location here."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<select id="coderockz_woo_delivery_pickup_hide_for_pickup_location" name="coderockz_woo_delivery_pickup_hide_for_pickup_location[]" class="coderockz_woo_delivery_pickup_hide_for_pickup_location" multiple>
                                
                                <?php
                                if(isset($pickup_location_settings['pickup_location']) && !empty($pickup_location_settings['pickup_location'])) {
									foreach($pickup_location_settings['pickup_location'] as $location => $location_details) {
	                                    echo '<option value="'.stripslashes($location).'">'.stripslashes($location).'</option>';
	                                }
                            	}
                                ?>
                                </select>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Hide This Timeslot For Categories', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="If you want to disable the timeslot for a specific category then select the category from here."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<select id="coderockz_woo_delivery_pickup_timeslot_hide_categories" name="coderockz_woo_delivery_pickup_timeslot_hide_categories[]" class="coderockz_woo_delivery_pickup_timeslot_hide_categories" multiple>
                                
                                <?php
                                foreach ($all_categories as $cat) {

                                    echo '<option value="'.$cat->name.'">'.$cat->name.'</option>';
                                }
                                ?>
                                </select>
	                    	</div>
	                    	<?php if(get_option('coderockz_woo_delivery_large_product_list') == false) { ?>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Hide This Timeslot For Products', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="If you want to hide the timeslot for a specific product then select the product from here."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<select id="coderockz_woo_delivery_pickup_timeslot_hide_products" name="coderockz_woo_delivery_pickup_timeslot_hide_products[]" class="coderockz_woo_delivery_pickup_timeslot_hide_products" multiple>
                                
                                <?php
                                foreach ($store_products as $key=>$value) {
                                    echo '<option value="'.$key.'">'.$value.'</option>';
                                }
                                ?>
                                </select>
	                    	</div>
	                    	<?php } else { ?>

                    		<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Hide This Timeslot For Products', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="If you want to hide the timeslot for a specific product then select the product from here."><span class="dashicons dashicons-editor-help"></span></p>
	                    		<input name="coderockz_woo_delivery_pickup_timeslot_hide_products_input" type="text" class="coderockz_woo_delivery_pickup_timeslot_hide_products_input coderockz-woo-delivery-input-field" value="" placeholder="Comma separated Product/Variation ID" autocomplete="off"/>
	                    	</div>

	                    	<?php } ?>
	                    	<div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label"><?php _e('Show If Cart Has Other Categories Or Products', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="If there is hidden category's products or hidden products in the cart then whatever there are other category's products or other products, the timeslot is hidden. If you want to reverse this enable it."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch">
							       <input type="checkbox" class="coderockz_woo_delivery_pickup_time_slot_shown_other_categories_products"/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_custom_pickup_slot_more_settings"><?php _e('Want More Settings Based On', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="If you select 'Shipping State' then you can get more settings based on shipping state and if you select 'Shipping Postcode/ZIP' then you can get more settings based on shipping postcode"><span class="dashicons dashicons-editor-help"></span></p>
	                    		<select class="coderockz-woo-delivery-select-field coderockz_woo_delivery_custom_pickup_slot_more_settings" name="coderockz_woo_delivery_custom_pickup_slot_more_settings">
									<option value="">Select your choice</option>
									<option value="zone">Shipping Zone</option>
									<option value="state">Shipping State</option>
									<option value="postcode">Shipping Postcode/ZIP</option>
								</select>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-custom_pickupslot-more-zone">
		                    	<div class="coderockz-woo-delivery-form-group">
		                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Hide For Shipping Zone', 'coderockz-woo-delivery'); ?></label>
		                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Pickupslot is hidden for the selected shipping zone."><span class="dashicons dashicons-editor-help"></span></p>
		                        	<select id="coderockz_woo_delivery_custom_pickup_disable_zone" name="coderockz_woo_delivery_custom_pickup_disable_zone[]" class="coderockz_woo_delivery_custom_pickup_disable_zone" multiple>
	                                <?php
	                                foreach ($zone_name as $key => $value) {
	                                    echo '<option value="'.$key.'">'.$value.'</option>';
	                                }
	                                ?>
	                                </select>
		                    	</div>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-custom_pickupslot-more-state">
		                    	<div class="coderockz-woo-delivery-form-group">
		                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Hide For Shipping Zone Regions', 'coderockz-woo-delivery'); ?></label>
		                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Pickup slot is hidden for the selected shipping zone regions."><span class="dashicons dashicons-editor-help"></span></p>
		                        	<select id="coderockz_woo_delivery_custom_pickup_disable_regions" name="coderockz_woo_delivery_custom_pickup_disable_regions[]" class="coderockz_woo_delivery_custom_pickup_disable_regions" multiple>
	                                <?php
	                                foreach ($zone_regions as $key => $value) {
	                                    echo '<option value="'.$key.'">'.$value.'</option>';
	                                }
	                                ?>
	                                </select>
		                    	</div>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-custom_pickupslot-more-postcode">           	<div class="coderockz-woo-delivery-form-group">
		                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Hide For Shipping Zone PostCode/Zip', 'coderockz-woo-delivery'); ?></label>
		                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Pickup slot is hidden for the selected shipping zone postcode/zip."><span class="dashicons dashicons-editor-help"></span></p>
		                        	<select id="coderockz_woo_delivery_custom_pickup_disable_postcode" name="coderockz_woo_delivery_custom_pickup_disable_postcode[]" class="coderockz_woo_delivery_custom_pickup_disable_postcode" multiple>
	                                <?php
	                                foreach ($zone_post_code as $key => $value) {
	                                    echo '<option value="'.$value.'">'.$value.'</option>';		                                }
	                                ?>
	                                </select>
		                    	</div>
	                    	</div>
						    <button class="coderockz-woo-delivery-custom-pickup-slot-saving coderockz-woo-delivery-submit-btn"><?php _e('Save Changes', 'coderockz-woo-delivery'); ?></button>
						  </div>
						  <?php
						  	
						  	if(isset($pickup_slot_settings['time_slot']) && count($pickup_slot_settings['time_slot'])>0){
						  		$helper = new Coderockz_Woo_Delivery_Helper();
								$sorted_custom_pickup_slot = $helper->array_sort_by_column($pickup_slot_settings['time_slot'],'start');

						  		foreach($sorted_custom_pickup_slot as $individual_time_slot_pickup) {
						  			$pickup_slot_start_hour = "";
			    					$pickup_slot_start_min = "";
			    					$pickup_slot_start_format= "am";
			            			
			            			if(isset($individual_time_slot_pickup['start']) && $individual_time_slot_pickup['start'] !='') {
			            				$pickup_time_starts = (int)$individual_time_slot_pickup['start'];

			            				if($pickup_time_starts == 0) {
			            					$pickup_slot_start_hour = "12";
			            					$pickup_slot_start_min = "00";
			            					$pickup_slot_start_format= "am";
			            				} elseif($pickup_time_starts > 0 && $pickup_time_starts <= 59) {
			            					$pickup_slot_start_hour = "12";
			            					$pickup_slot_start_min = sprintf("%02d", $pickup_time_starts);
			            					$pickup_slot_start_format= "am";
			            				} elseif($pickup_time_starts > 59 && $pickup_time_starts <= 719) {
											$pickup_slot_start_min = sprintf("%02d", (int)$pickup_time_starts%60);
											$pickup_slot_start_hour = sprintf("%02d", ((int)$pickup_time_starts-$pickup_slot_start_min)/60);
											$pickup_slot_start_format= "am";
											
			            				} elseif ($pickup_time_starts > 719 && $pickup_time_starts <= 1439) {
											$pickup_slot_start_min = sprintf("%02d", (int)$pickup_time_starts%60);
											$pickup_slot_start_hour = sprintf("%02d", ((int)$pickup_time_starts-$pickup_slot_start_min)/60);
											if($pickup_slot_start_hour>12) {
												$pickup_slot_start_hour = sprintf("%02d", $pickup_slot_start_hour-12);
											}
											$pickup_slot_start_format= "pm";
			            				} elseif ($pickup_time_starts == 1440) {
			    							$pickup_slot_start_hour = "12";
			    							$pickup_slot_start_min = "00";
			    							$pickup_slot_start_format= "am";
			            				}

			            			}

			            			$pickup_slot_end_hour = "";
	            					$pickup_slot_end_min = "";
	            					$pickup_slot_end_format= "am";
	                    			
	                    			if(isset($individual_time_slot_pickup['end']) && $individual_time_slot_pickup['end'] !='') {
	                    				$pickup_time_ends = (int)$individual_time_slot_pickup['end'];
	                    				if($pickup_time_ends == 0) {
			            					$pickup_slot_end_hour = "12";
			            					$pickup_slot_end_min = "00";
			            					$pickup_slot_end_format= "am";
			            				} elseif($pickup_time_ends > 0 && $pickup_time_ends <= 59) {
	                    					$pickup_slot_end_hour = "12";
	                    					$pickup_slot_end_min = sprintf("%02d", $pickup_time_ends);
	                    					$pickup_slot_end_format= "am";
	                    				} elseif($pickup_time_ends > 59 && $pickup_time_ends <= 719) {
											$pickup_slot_end_min = sprintf("%02d", (int)$pickup_time_ends%60);
											$pickup_slot_end_hour = sprintf("%02d", ((int)$pickup_time_ends-$pickup_slot_end_min)/60);
											$pickup_slot_end_format= "am";
											
	                    				} elseif ($pickup_time_ends > 719 && $pickup_time_ends <= 1439) {
											$pickup_slot_end_min = sprintf("%02d", (int)$pickup_time_ends%60);
											$pickup_slot_end_hour = sprintf("%02d", ((int)$pickup_time_ends-$pickup_slot_end_min)/60);
											if($pickup_slot_end_hour>12) {
												$pickup_slot_end_hour = sprintf("%02d", $pickup_slot_end_hour-12);
											}
											$pickup_slot_end_format= "pm";

	                    				} elseif($pickup_time_ends == 1440) {
											$pickup_slot_end_min = "00";
											$pickup_slot_end_hour = "12";
											$pickup_slot_end_format= "am";
											
	                    				} 

	                    			}

	                    			?>

	                    			<div class="coderockz-woo-delivery-pickup-slot-accordion-header">
		                    	  	<p style="font-size:16px;margin:0;font-weight:700;color:#fff!important;"><span class="coderockz-woo-delivery-pickup-slot-start-hour"><?php echo $pickup_slot_start_hour; ?></span><span class="coderockz-woo-delivery-pickup-slot-start-min">:<?php echo $pickup_slot_start_min; ?></span><span class="coderockz-woo-delivery-pickup-slot-start-format"> <?php echo strtoupper($pickup_slot_start_format); ?></span> - <span class="coderockz-woo-delivery-pickup-slot-end-hour"><?php echo $pickup_slot_end_hour; ?></span><span class="coderockz-woo-delivery-pickup-slot-end-min">:<?php echo $pickup_slot_end_min; ?></span><span class="coderockz-woo-delivery-pickup-slot-end-format"> <?php echo strtoupper($pickup_slot_end_format); ?></span></p>
		                    	  </div>
								  <div class="coderockz-woo-delivery-pickup-slot-accordion-content">
								  	<div class="coderockz-woo-delivery-form-group" style="margin: 0;position: absolute;right: 45px;top: -31px;">
									    <label class="coderockz-woo-delivery-toogle-switch">
									       <input type="checkbox" class="coderockz-woo-delivery-enable-custom-pickup-slot" <?php echo isset($individual_time_slot_pickup['enable']) && !empty($individual_time_slot_pickup['enable'])  ? "checked" : "";?>/>
									       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
									    </label>
		                    		</div>
		                    		<span class="dashicons dashicons-trash coderockz-woo-delivery-pickup-slot-accordion-delete" style="margin: 0;position: absolute;right: 5px;top: -30px;color:#fff;cursor: pointer;"></span>
		                    		<p class="coderockz-woo-delivery-custom-pickup-slot-notice"><span class="dashicons dashicons-yes"></span><?php _e(' Settings Changed Successfully', 'coderockz-woo-delivery'); ?></p>
			                    	<div class="coderockz-woo-delivery-form-group">
			                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_delivery_pickup_slot_starts"><?php _e('Pickup Slot Starts From', 'coderockz-woo-delivery'); ?></label>
			                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Pickup slot starts from the time that is specified here. Only numerical value is accepted."><span class="dashicons dashicons-editor-help"></span></p>
			                    		<div class="coderockz_delivery_pickup_slot_starts coderockz_woo_delivery_custom_pickup_slot_starts">
			                    			
			                        	<input type="number" class="coderockz_woo_delivery_custom_pickup_slot_starts_hour coderockz-woo-delivery-number-field" max="12" min="1" onkeyup="if(!Number.isInteger(Number(this.value)) || this.value > 12 || this.value < 1) this.value = null;" value="<?php echo $pickup_slot_start_hour; ?>" placeholder="Hour" autocomplete="off"/>
			                        	<input type="number" class="coderockz_woo_delivery_custom_pickup_slot_starts_min coderockz-woo-delivery-number-field" max="59" min="0" onkeyup="if(!Number.isInteger(Number(this.value)) || this.value > 59 || this.value < 0) this.value = null;" value="<?php echo $pickup_slot_start_min; ?>" placeholder="Minute" autocomplete="off"/>
			                        	<select class="coderockz_woo_delivery_custom_pickup_slot_starts_format coderockz-woo-delivery-select-field">
											<option value="am" <?php echo $pickup_slot_start_format == "am"? " selected" : ""; ?>>AM</option>
											<option value="pm" <?php echo $pickup_slot_start_format == "pm"? " selected" : ""; ?>>PM</option>
										</select>
			                        	</div>
			                    	</div>
			                    	<div class="coderockz-woo-delivery-form-group">
			                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Pickup Slot Ends At', 'coderockz-woo-delivery'); ?></label>
			                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Pickup slot ends at the time that is specified here. Only numerical value is accepted."><span class="dashicons dashicons-editor-help"></span></p>
			                    		<div class="coderockz_delivery_pickup_slot_ends coderockz_woo_delivery_custom_pickup_slot_ends">
			                        	<input type="number" class="coderockz_woo_delivery_custom_pickup_slot_ends_hour coderockz-woo-delivery-number-field" max="12" min="1" onkeyup="if(!Number.isInteger(Number(this.value)) || this.value > 12 || this.value < 1) this.value = null;" value="<?php echo $pickup_slot_end_hour; ?>" placeholder="Hour" autocomplete="off"/>
			                        	<input type="number" class="coderockz_woo_delivery_custom_pickup_slot_ends_min coderockz-woo-delivery-number-field" max="59" min="0" onkeyup="if(!Number.isInteger(Number(this.value)) || this.value > 59 || this.value < 0) this.value = null;" value="<?php echo $pickup_slot_end_min; ?>" placeholder="Minute" autocomplete="off"/>
			                        	<select class="coderockz_woo_delivery_custom_pickup_slot_ends_format coderockz-woo-delivery-select-field" name="coderockz_delivery_pickup_slot_ends_format">
											<option value="am" <?php echo $pickup_slot_end_format == "am"? " selected" : ""; ?>>AM</option>
											<option value="pm" <?php echo $pickup_slot_end_format == "pm"? " selected" : ""; ?>>PM</option>
										</select>
			                        	</div>
			                        	<p class="coderockz_custom_end_pickup_greater_notice">End Time Must after Start Time</p>
			                    	</div>
			                    	<div class="coderockz-woo-delivery-form-group">
			                        	<span class="coderockz-woo-delivery-form-label"><?php _e('Want to split the timeslot?', 'coderockz-woo-delivery'); ?></span>
			                        	<p class="coderockz-woo-delivery-tooltip" tooltip="Enable it if you want to split the pickup slot in several pickup slot according a fixed interval. Default is disable."><span class="dashicons dashicons-editor-help"></span></p>
									    <label class="coderockz-woo-delivery-toogle-switch">
									       <input type="checkbox" class="coderockz_woo_delivery_enable_custom_pickup_slot_split" <?php echo isset($individual_time_slot_pickup['enable_split']) && !empty($individual_time_slot_pickup['enable_split'])  ? "checked" : "";?>/>
									       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
									    </label>
			                    	</div>

			                    	<?php
		                    		$pickup_split_slot_duration = ""; 
		                    		$pickup_split_slot_identity = "min";
	                    			if(isset($individual_time_slot_pickup['split_slot_duration']) && !empty($individual_time_slot_pickup['split_slot_duration'])) {
	                    				$pickup_split_time_slot_duration = (int)$individual_time_slot_pickup['split_slot_duration'];
	                    				if($pickup_split_time_slot_duration <= 59) {
	                    					$pickup_split_slot_duration = $pickup_split_time_slot_duration;
	                    				} else {
	                    					$pickup_split_time_slot_duration = $pickup_split_time_slot_duration/60;
	                    					$helper = new Coderockz_Woo_Delivery_Helper();
	                    					if($helper->containsDecimal($pickup_split_time_slot_duration)){
	                    						$pickup_split_slot_duration = $pickup_split_time_slot_duration*60;
	                    						$pickup_split_slot_identity = "min";
	                    					} else {
	                    						$pickup_split_slot_duration = $pickup_split_time_slot_duration;
	                    						$pickup_split_slot_identity = "hour";
	                    					}
	                    				}
	                    			}
			                    	?>
			                    	<div class="coderockz-woo-delivery-split-pickup-duration-section">
				                    	<div class="coderockz-woo-delivery-form-group">
				                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_split_pickup_slot_duration"><?php _e('Each splited Pickup Slot Duration', 'coderockz-woo-delivery'); ?></label>
				                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Each splited pickup slot duration that is specified here. Only numerical value is accepted."><span class="dashicons dashicons-editor-help"></span></p>
				                    		<div id="coderockz_split_pickup_slot_duration" class="coderockz_split_pickup_slot_duration">
					                        	<input class="coderockz_split_pickup_slot_duration_time coderockz-woo-delivery-number-field" type="number" min="1" onkeyup="if(!Number.isInteger(Number(this.value)) || this.value < 1) this.value = null;" value="<?php echo $pickup_split_slot_duration; ?>" placeholder="" autocomplete="off"/>
					                        	<select class="coderockz-woo-delivery-select-field coderockz_split_pickup_slot_duration_format" name="coderockz_split_pickup_slot_duration_format">
													<option value="min" <?php selected($pickup_split_slot_identity,"min",true); ?>>Minutes</option>
													<option value="hour" <?php selected($pickup_split_slot_identity,"hour",true); ?>>Hour</option>
												</select>
				                        	</div>
				                        	<p class="coderockz_split_pickup_slot_duration_notice">Time slot duration is required</p>
				                    	</div>


				                    	<div class="coderockz-woo-delivery-form-group">
				                        	<span class="coderockz-woo-delivery-form-label"><?php _e('Make each splited pickupslot single', 'coderockz-woo-delivery'); ?></span>
				                        	<p class="coderockz-woo-delivery-tooltip" tooltip="Enable it if you want to make every pickupslot single as the timeslot value is the starting time of the time range. Default is disable."><span class="dashicons dashicons-editor-help"></span></p>
										    <label class="coderockz-woo-delivery-toogle-switch">
										       <input type="checkbox" class="coderockz_woo_delivery_enable_custom_splited_pickup_slot_single" <?php echo isset($individual_time_slot_pickup['enable_single_splited_slot']) && !empty($individual_time_slot_pickup['enable_single_splited_slot'])  ? "checked" : "";?>/>
										       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
										    </label>
				                    	</div>
			                    	</div>
			                    	<div class="coderockz-woo-delivery-single-pickup-section">
			                    		<div class="coderockz-woo-delivery-form-group">
				                        	<span class="coderockz-woo-delivery-form-label"><?php _e('Make This Pickup Slot Single', 'coderockz-woo-delivery'); ?></span>
				                        	<p class="coderockz-woo-delivery-tooltip" tooltip="Enable it if you want to make this pickup slot single as the timeslot value is the starting time of the time range. Default is disable."><span class="dashicons dashicons-editor-help"></span></p>
										    <label class="coderockz-woo-delivery-toogle-switch">
										       <input type="checkbox" class="coderockz_woo_delivery_enable_custom_pickup_slot_single" <?php echo isset($individual_time_slot_pickup['enable_single_slot']) && !empty($individual_time_slot_pickup['enable_single_slot'])  ? "checked" : "";?>/>
										       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
										    </label>
				                    	</div>
			                    	</div>
			                    	<div class="coderockz-woo-delivery-form-group">
			                        	<span class="coderockz-woo-delivery-form-label"><?php _e('Hide This TimeSlot for Current Date', 'coderockz-woo-delivery'); ?></span>
			                        	<p class="coderockz-woo-delivery-tooltip" tooltip="Enable it if you want to hide this timeslot current date. Default is disable."><span class="dashicons dashicons-editor-help"></span></p>
									    <label class="coderockz-woo-delivery-toogle-switch">
									       <input type="checkbox" class="coderockz_woo_delivery_hide_custom_pickup_slot_current_date" <?php echo isset($individual_time_slot_pickup['hide_time_slot_current_date']) && !empty($individual_time_slot_pickup['hide_time_slot_current_date'])  ? "checked" : "";?>/>
									       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
									    </label>
			                    	</div>
			                    	<?php 
	                    			$pickupslot_closing_hour = "";
	            					$pickupslot_closing_min = "";
	            					$pickupslot_closing_format= "am";
	                    			
	                    			if(isset($individual_time_slot_pickup['timeslot_closing_time']) && $individual_time_slot_pickup['timeslot_closing_time'] !='') {
	                    				$pickupslot_closing_time = (int)$individual_time_slot_pickup['timeslot_closing_time'];

	                    				if($pickupslot_closing_time > 0 && $pickupslot_closing_time <= 59) {

	                    					$pickupslot_closing_hour = "12";
	                    					$pickupslot_closing_min = sprintf("%02d", $pickupslot_closing_time);
	                    					$pickupslot_closing_format= "am";
	                    				} elseif($pickupslot_closing_time > 59 && $pickupslot_closing_time <= 719) {
											$pickupslot_closing_min = sprintf("%02d", (int)$pickupslot_closing_time%60);
											$pickupslot_closing_hour = sprintf("%02d", ((int)$pickupslot_closing_time-$pickupslot_closing_min)/60);
											$pickupslot_closing_format= "am";
											
	                    				} elseif($pickupslot_closing_time > 719 && $pickupslot_closing_time <= 1439) {
											$pickupslot_closing_min = sprintf("%02d", (int)$pickupslot_closing_time%60);
											$pickupslot_closing_hour = sprintf("%02d", ((int)$pickupslot_closing_time-$pickupslot_closing_min)/60);
											if($pickupslot_closing_hour>12) {
												$pickupslot_closing_hour = sprintf("%02d", $pickupslot_closing_hour-12);
											}
											$pickupslot_closing_format= "pm";
	                    				} elseif($pickupslot_closing_time === 0) {
											$pickupslot_closing_min = "00";
											$pickupslot_closing_hour = "12";
											$pickupslot_closing_format= "am";
	                    				}

	                    			}
		                    		?>
			                    	<div class="coderockz-woo-delivery-form-group">
			                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_pickupslot_closing_time"><?php _e('Hide this timeslot at', 'coderockz-woo-delivery'); ?></label>
			                    		<p class="coderockz-woo-delivery-tooltip" tooltip="If you don't want to show the timeslot after a certain time if the current date is selected as delivery date, put the time here. Only numerical value is accepted."><span class="dashicons dashicons-editor-help"></span></p>
			                    		<div id="coderockz_woo_delivery_pickupslot_closing_time" class="coderockz_woo_delivery_pickupslot_closing_time">
			                    			
			                        	<input name="coderockz_woo_delivery_pickupslot_closing_time_hour" type="number" class="coderockz-woo-delivery-number-field coderockz_woo_delivery_pickupslot_closing_time_hour" max="12" min="1" onkeyup="if(!Number.isInteger(Number(this.value)) || this.value > 12 || this.value < 1) this.value = null;" value="<?php echo $pickupslot_closing_hour; ?>" placeholder="Hour" autocomplete="off"/>
			                        	<input name="coderockz_woo_delivery_pickupslot_closing_time_min" type="number" class="coderockz-woo-delivery-number-field coderockz_woo_delivery_pickupslot_closing_time_min" max="59" min="0" onkeyup="if(!Number.isInteger(Number(this.value)) || this.value > 59 || this.value < 0) this.value = null;" value="<?php echo $pickupslot_closing_min; ?>" placeholder="Minute" autocomplete="off"/>
			                        	<select class="coderockz-woo-delivery-select-field coderockz_woo_delivery_pickupslot_closing_time_format" name="coderockz_woo_delivery_pickupslot_closing_time_format">
											<option value="am" <?php selected($pickupslot_closing_format,"am",true); ?>>AM</option>
											<option value="pm" <?php selected($pickupslot_closing_format,"pm",true); ?>>PM</option>
										</select>
			                        	</div>
			                    	</div>
			                    	<div class="coderockz-woo-delivery-form-group">
			                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Show This Timeslot Only At', 'coderockz-woo-delivery'); ?></label>
			                    		<p class="coderockz-woo-delivery-tooltip" tooltip="If you want to show the timeslot only at specific date then specify here the dates"><span class="dashicons dashicons-editor-help"></span></p>
			                        	<input style="width:495px" type="text" class="coderockz-woo-delivery-custom-pickup-slot-specific-date coderockz-woo-delivery-input-field" value="<?php echo isset($individual_time_slot_pickup['only_specific_date']) && $individual_time_slot_pickup['only_specific_date'] !='' ? $individual_time_slot_pickup['only_specific_date'] : ""; ?>" placeholder="<?php _e('Comma(,) Separated Date, format yyyy-mm-dd(Ex. 2020-12-24)', 'coderockz-woo-delivery'); ?>" autocomplete="off"/>
			                    	</div>
			                    	<div class="coderockz-woo-delivery-form-group">
			                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Hide This Timeslot At', 'coderockz-woo-delivery'); ?></label>
			                    		<p class="coderockz-woo-delivery-tooltip" tooltip="If you want to hide the timeslot only at specific date then specify here the dates"><span class="dashicons dashicons-editor-help"></span></p>
			                        	<input style="width:495px" type="text" class="coderockz-woo-delivery-custom-pickup-slot-specific-date-close coderockz-woo-delivery-input-field" value="<?php echo isset($individual_time_slot_pickup['only_specific_date_close']) && $individual_time_slot_pickup['only_specific_date_close'] !='' ? $individual_time_slot_pickup['only_specific_date_close'] : ""; ?>" placeholder="<?php _e('Comma(,) Separated Date, format yyyy-mm-dd(Ex. 2020-12-24)', 'coderockz-woo-delivery'); ?>" autocomplete="off"/>
			                    	</div>
			                    	<div class="coderockz-woo-delivery-form-group">
			                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Maximum Pickup For This Slot', 'coderockz-woo-delivery'); ?></label>
			                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Each time slot take maximum number of pickups that is specified here. After reaching the maximum pickup, the time slot is disabled automatically. Only numerical value is accepted."><span class="dashicons dashicons-editor-help"></span></p>
			                        	<input type="number" class="coderockz-woo-delivery-custom-pickup-slot-order coderockz-woo-delivery-number-field" min="1" onkeyup="if(!Number.isInteger(Number(this.value)) || this.value < 1) this.value = null;" value="<?php echo isset($individual_time_slot_pickup['max_order']) && $individual_time_slot_pickup['max_order'] !='' ? $individual_time_slot_pickup['max_order'] : ""; ?>" placeholder="" autocomplete="off"/>
			                    	</div>
			                    	<div class="coderockz-woo-delivery-form-group">
			                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Pickup Slot Fee', 'coderockz-woo-delivery'); ?></label>
			                    		<p class="coderockz-woo-delivery-tooltip" tooltip="If anyone select this pickup slot, fee specified here is applied with the total."><span class="dashicons dashicons-editor-help"></span></p>
			                        	
			                        	<input type="text" class="coderockz-woo-delivery-custom-pickup-slot-fee coderockz-woo-delivery-input-field" onkeyup="if(isNaN(parseFloat(Number(this.value))) || isNaN(parseInt(Number(this.value), 10))) this.value = null;" value="<?php echo isset($individual_time_slot_pickup['fee']) && $individual_time_slot_pickup['fee'] !='' ? $individual_time_slot_pickup['fee'] : ""; ?>" style="width:245px;border-radius: 3px 0 0 3px;" autocomplete="off" placeholder="Fee"/><span class="coderockz-woo-delivery-currency-code" style="width:40px;"><?php echo $currency_code; ?></span>
			                    	</div>

			                    	<div class="coderockz-woo-delivery-form-group">
			                    		<label class="coderockz-woo-delivery-form-label coderockz-woo-delivery-checkbox-label"><?php _e('Hide For', 'coderockz-woo-delivery'); ?></label>
			                    		<p class="coderockz-woo-delivery-tooltip coderockz-woo-delivery-checkbox-tooltip" tooltip="This pickup slot will be hidden for the selected days."><span class="dashicons dashicons-editor-help"></span></p>
			                    		<div class="coderockz-woo-delivery-pickup-slot-enable-for" style="display:inline-block">
			                    		<input type="checkbox" name="coderockz_woo_delivery_custom_pickup_slot_disable" value="6" <?php echo isset($individual_time_slot_pickup['disable_for']) && !empty($individual_time_slot_pickup['disable_for']) && in_array("6",$individual_time_slot_pickup['disable_for']) ? "checked" : "";?>><label class="coderockz-woo-delivery-checkbox-field-text">Saturday</label><br/>
										<input type="checkbox" name="coderockz_woo_delivery_custom_pickup_slot_disable" value="0" <?php echo isset($individual_time_slot_pickup['disable_for']) && !empty($individual_time_slot_pickup['disable_for']) && in_array("0",$individual_time_slot_pickup['disable_for']) ? "checked" : "";?>><label class="coderockz-woo-delivery-checkbox-field-text">Sunday</label><br/>
										<input type="checkbox" name="coderockz_woo_delivery_custom_pickup_slot_disable" value="1" <?php echo isset($individual_time_slot_pickup['disable_for']) && !empty($individual_time_slot_pickup['disable_for']) && in_array("1",$individual_time_slot_pickup['disable_for']) ? "checked" : "";?>><label class="coderockz-woo-delivery-checkbox-field-text">Monday</label><br/>
										<input type="checkbox" name="coderockz_woo_delivery_custom_pickup_slot_disable" value="2" <?php echo isset($individual_time_slot_pickup['disable_for']) && !empty($individual_time_slot_pickup['disable_for']) && in_array("2",$individual_time_slot_pickup['disable_for']) ? "checked" : "";?>><label class="coderockz-woo-delivery-checkbox-field-text">Tuesday</label><br/>
										<input type="checkbox" name="coderockz_woo_delivery_custom_pickup_slot_disable" value="3" <?php echo isset($individual_time_slot_pickup['disable_for']) && !empty($individual_time_slot_pickup['disable_for']) && in_array("3",$individual_time_slot_pickup['disable_for']) ? "checked" : "";?>><label class="coderockz-woo-delivery-checkbox-field-text">Wednesday</label><br/>
										<input type="checkbox" name="coderockz_woo_delivery_custom_pickup_slot_disable" value="4" <?php echo isset($individual_time_slot_pickup['disable_for']) && !empty($individual_time_slot_pickup['disable_for']) && in_array("4",$individual_time_slot_pickup['disable_for']) ? "checked" : "";?>><label class="coderockz-woo-delivery-checkbox-field-text">Thursday</label><br/>
										<input type="checkbox" name="coderockz_woo_delivery_custom_pickup_slot_disable" value="5" <?php echo isset($individual_time_slot_pickup['disable_for']) && !empty($individual_time_slot_pickup['disable_for']) && in_array("5",$individual_time_slot_pickup['disable_for']) ? "checked" : "";?>><label class="coderockz-woo-delivery-checkbox-field-text">Friday</label><br/>
										</div>
			                    	</div>
			                    	<div class="coderockz-woo-delivery-form-group">
			                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Hide This Timeslot for Pickup Location', 'coderockz-woo-delivery'); ?></label>
			                    		<p class="coderockz-woo-delivery-tooltip" tooltip="If you want to hide this timeslot for a specific pickup location then select the pickup location here."><span class="dashicons dashicons-editor-help"></span></p>
			                        	<select id="coderockz_woo_delivery_pickup_hide_for_pickup_location2" name="coderockz_woo_delivery_pickup_hide_for_pickup_location[]" class="coderockz_woo_delivery_pickup_hide_for_pickup_location2" multiple>
		                                
		                                <?php
		                                if(isset($pickup_location_settings['pickup_location']) && !empty($pickup_location_settings['pickup_location'])) {


		                                	$pickupslot_hide_location = [];

											foreach ($individual_time_slot_pickup['hide_for_pickup_location'] as $hide_location) {
												$pickupslot_hide_location[] = stripslashes($hide_location);
											}




											foreach($pickup_location_settings['pickup_location'] as $location => $location_details) {


			                                	$selected = isset($individual_time_slot_pickup['hide_for_pickup_location']) && !empty($individual_time_slot_pickup['hide_for_pickup_location']) && in_array(stripslashes($location),$pickupslot_hide_location) ? "selected" : "";
			                                    echo '<option value="'.stripslashes($location).'" '.$selected.'>'.stripslashes($location).'</option>';

			                                }
		                            	}
		                                ?>
		                                </select>
			                    	</div>
			                    	<div class="coderockz-woo-delivery-form-group">
			                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Hide This Timeslot For Categories', 'coderockz-woo-delivery'); ?></label>
			                    		<p class="coderockz-woo-delivery-tooltip" tooltip="If you want to hide the timeslot for a specific category then select the category from here."><span class="dashicons dashicons-editor-help"></span></p>
			                        	<select id="coderockz_woo_delivery_pickup_timeslot_hide_categories2" name="coderockz_woo_delivery_pickup_timeslot_hide_categories[]" class="coderockz_woo_delivery_pickup_timeslot_hide_categories2" multiple>
		                                
		                                <?php
		                                $pickupslot_hide_categories = [];
										if(isset($individual_time_slot_pickup['hide_categories']) && !empty($individual_time_slot_pickup['hide_categories'])) {
											foreach ($individual_time_slot_pickup['hide_categories'] as $hide_cat) {
												$pickupslot_hide_categories[] = stripslashes($hide_cat);
											}
										}
		                                foreach ($all_categories as $cat) {

		                                	$selected = isset($individual_time_slot_pickup['hide_categories']) && !empty($individual_time_slot_pickup['hide_categories']) && in_array(htmlspecialchars_decode($cat->name),$pickupslot_hide_categories) ? "selected" : "";
		                                    echo '<option value="'.$cat->name.'" '.$selected.'>'.$cat->name.'</option>';
		                                }
		                                ?>
		                                </select>
			                    	</div>
			                    	<?php if(get_option('coderockz_woo_delivery_large_product_list') == false) { ?>
			                    	<div class="coderockz-woo-delivery-form-group">
			                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Hide This Timeslot For Products', 'coderockz-woo-delivery'); ?></label>
			                    		<p class="coderockz-woo-delivery-tooltip" tooltip="If you want to hide the timeslot for a specific product then select the product from here."><span class="dashicons dashicons-editor-help"></span></p>
			                        	<select id="coderockz_woo_delivery_pickup_timeslot_hide_products2" name="coderockz_woo_delivery_pickup_timeslot_hide_products[]" class="coderockz_woo_delivery_pickup_timeslot_hide_products2" multiple>
		                                
		                                <?php
		                                foreach ($store_products as $key=>$value) {

		                                	$selected = isset($individual_time_slot_pickup['hide_products']) && !empty($individual_time_slot_pickup['hide_products']) && in_array($key,$individual_time_slot_pickup['hide_products']) ? "selected" : "";
		                                    echo '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
		                                }
		                                ?>
		                                </select>
			                    	</div>
			                    	<?php } else { ?>

		                    		<div class="coderockz-woo-delivery-form-group">
			                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Hide This Timeslot For Products', 'coderockz-woo-delivery'); ?></label>
			                    		<p class="coderockz-woo-delivery-tooltip" tooltip="If you want to hide the timeslot for a specific product then select the product from here."><span class="dashicons dashicons-editor-help"></span></p>
			                        	<?php 
			                        	$pickup_hide_products_input = isset($individual_time_slot_pickup['hide_products']) && !empty($individual_time_slot_pickup['hide_products']) ? $individual_time_slot_pickup['hide_products'] : array();
			                        	$pickup_hide_products_input = implode(",",$pickup_hide_products_input);
			                        	?>
			                    		<input name="coderockz_woo_delivery_pickup_timeslot_hide_products2_input" type="text" class="coderockz_woo_delivery_pickup_timeslot_hide_products2_input coderockz-woo-delivery-input-field" value="<?php echo $pickup_hide_products_input; ?>" placeholder="Comma separated Product/Variation ID" autocomplete="off"/>
			                    	</div>

			                    	<?php } ?>
			                    	<div class="coderockz-woo-delivery-form-group">
			                        	<span class="coderockz-woo-delivery-form-label"><?php _e('Show If Cart Has Other Categories Or Products', 'coderockz-woo-delivery'); ?></span>
			                        	<p class="coderockz-woo-delivery-tooltip" tooltip="If there is hidden category's products or hidden products in the cart then whatever there are other category's products or other products, th timeslot is hidden. If you want to reverse this enable it."><span class="dashicons dashicons-editor-help"></span></p>
									    <label class="coderockz-woo-delivery-toogle-switch">
									       <input type="checkbox" class="coderockz_woo_delivery_pickup_time_slot_shown_other_categories_products" <?php echo isset($individual_time_slot_pickup['time_slot_shown_other_categories_products']) && !empty($individual_time_slot_pickup['time_slot_shown_other_categories_products'])  ? "checked" : "";?>/>
									       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
									    </label>
			                    	</div>
			                    	<div class="coderockz-woo-delivery-form-group">
			                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_custom_pickup_slot_more_settings"><?php _e('Want More Settings Based On', 'coderockz-woo-delivery'); ?></label>
			                    		<p class="coderockz-woo-delivery-tooltip" tooltip="If you select 'Shipping State' then you can get more settings based on shipping state and if you select 'Shipping Postcode/ZIP' then you can get more settings based on shipping postcode"><span class="dashicons dashicons-editor-help"></span></p>
			                    		<select class="coderockz-woo-delivery-select-field coderockz_woo_delivery_custom_pickup_slot_more_settings" name="coderockz_woo_delivery_custom_pickup_slot_more_settings">
											<option value="" <?php if(isset($individual_time_slot_pickup['more_settings']) && $individual_time_slot_pickup['more_settings'] == ""){ echo "selected"; } ?>>Select your choice</option>
											<option value="zone" <?php if(isset($individual_time_slot_pickup['more_settings']) && $individual_time_slot_pickup['more_settings'] == "zone"){ echo "selected"; } ?>>Shipping Zone</option>
											<option value="state" <?php if(isset($individual_time_slot_pickup['more_settings']) && $individual_time_slot_pickup['more_settings'] == "state"){ echo "selected"; } ?>>Shipping State</option>
											<option value="postcode" <?php if(isset($individual_time_slot_pickup['more_settings']) && $individual_time_slot_pickup['more_settings'] == "postcode"){ echo "selected"; } ?>>Shipping Postcode/ZIP</option>
										</select>
			                    	</div>
			                    	<div class="coderockz-woo-delivery-custom_pickupslot-more-zone">
				                    	<div class="coderockz-woo-delivery-form-group">
				                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Hide For Shipping Zone', 'coderockz-woo-delivery'); ?></label>
				                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Pickup slot is hidden for the selected shipping zone."><span class="dashicons dashicons-editor-help"></span></p>
				                        	<select id="coderockz_woo_delivery_custom_pickup_disable_zone2" name="coderockz_woo_delivery_custom_pickup_disable_zone[]" class="coderockz_woo_delivery_custom_pickup_disable_zone2" multiple>
			                                <?php
			                                foreach ($zone_name as $key => $value) {
			                                	$selected = isset($individual_time_slot_pickup['disable_zone']) && !empty($individual_time_slot_pickup['disable_zone']) && in_array($key,$individual_time_slot_pickup['disable_zone']) ? "selected" : "";
			                                    echo '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
			                                }
			                                ?>
			                                </select>
				                    	</div>
			                    	</div>
			                    	<div class="coderockz-woo-delivery-custom_pickupslot-more-state">
				                    	<div class="coderockz-woo-delivery-form-group">
				                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Hide For Shipping Zone Regions', 'coderockz-woo-delivery'); ?></label>
				                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Pickup slot is hidden for the selected shipping zone regions."><span class="dashicons dashicons-editor-help"></span></p>
				                        	<select id="coderockz_woo_delivery_custom_pickup_disable_regions2" name="coderockz_woo_delivery_custom_pickup_disable_regions[]" class="coderockz_woo_delivery_custom_pickup_disable_regions2" multiple>
			                                <?php
			                                foreach ($zone_regions as $key => $value) {
			                                	$selected = isset($individual_time_slot_pickup['disable_state']) && !empty($individual_time_slot_pickup['disable_state']) && in_array($key,$individual_time_slot_pickup['disable_state']) ? "selected" : "";
			                                    echo '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
			                                }
			                                ?>
			                                </select>
				                    	</div>
			                    	</div>
			                    	<div class="coderockz-woo-delivery-custom_pickupslot-more-postcode">	
			                    		<div class="coderockz-woo-delivery-form-group">
				                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Hide For Shipping Zone PostCode/Zip', 'coderockz-woo-delivery'); ?></label>
				                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Pickup slot is hidden for the selected shipping zone postcode/zip."><span class="dashicons dashicons-editor-help"></span></p>
				                        	<select id="coderockz_woo_delivery_custom_pickup_disable_postcode2" name="coderockz_woo_delivery_custom_pickup_disable_postcode[]" class="coderockz_woo_delivery_custom_pickup_disable_postcode2" multiple>
			                                <?php
			                                foreach ($zone_post_code as $key => $value) {
			                                	$selected = isset($individual_time_slot_pickup['disable_postcode']) && !empty($individual_time_slot_pickup['disable_postcode']) && in_array($value,$individual_time_slot_pickup['disable_postcode']) ? "selected" : "";
			                                    echo '<option value="'.$value.'" '.$selected.'>'.$value.'</option>';		                                }
			                                ?>
			                                </select>
				                    	</div>
			                    	</div>
								    <button class="coderockz-woo-delivery-custom-pickup-slot-saving coderockz-woo-delivery-submit-btn"><?php _e('Save Changes', 'coderockz-woo-delivery'); ?></button>
								  </div>

	                    		<?php
						  		}
						  		
						  	}
						  	
						  ?>
	                	</div>
                	</div>

                </div>
			</div>
			<div data-tab="tab13" class="coderockz-woo-delivery-tabcontent">
				<div class="coderockz-woo-delivery-card">
					<p class="coderockz-woo-delivery-card-header"><?php _e('Pickup Location Settings', 'coderockz-woo-delivery'); ?></p>
					<div class="coderockz-woo-delivery-card-body">
						<p class="coderockz-woo-pickup-location-tab-notice"><span class="dashicons dashicons-yes"></span><?php _e(' Settings Changed Successfully', 'coderockz-woo-delivery'); ?></p>
	                    <form action="" method="post" id ="coderockz_pickup_location_form_submit">
	                        <?php wp_nonce_field('coderockz_woo_delivery_nonce'); ?>

	                    	<div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label"><?php _e('Enable Pickup Location', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="Enable Pickup Location select field in woocommerce order checkout page."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_enable_pickup_location">
							       <input type="checkbox" name="coderockz_enable_pickup_location" id="coderockz_enable_pickup_location" <?php echo (isset($pickup_location_settings['enable_pickup_location']) && !empty($pickup_location_settings['enable_pickup_location'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>

	                    	<div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label"><?php _e('Make Pickup Location Field Mandatory', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="Make Pickup Location select field mandatory in woocommerce order checkout page. Default is optional."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_pickup_location_mandatory">
							       <input type="checkbox" name="coderockz_pickup_location_mandatory" id="coderockz_pickup_location_mandatory" <?php echo (isset($pickup_location_settings['pickup_location_mandatory']) && !empty($pickup_location_settings['pickup_location_mandatory'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_pickup_location_field_label"><?php _e('Pickup Location Field Heading', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Pickup Location input field heading. Default is Pickup Location."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_pickup_location_field_label" name="coderockz_pickup_location_field_label" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($pickup_location_settings['field_label']) && !empty($pickup_location_settings['field_label'])) ? stripslashes(esc_attr($pickup_location_settings['field_label'])) : "" ?>" placeholder="" autocomplete="off"/>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_pickup_location_field_placeholder"><?php _e('Pickup Location Field Placeholder', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Pickup Location input field placeholder. Default is Pickup Location."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_pickup_location_field_placeholder" name="coderockz_pickup_location_field_placeholder" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($pickup_location_settings['field_placeholder']) && !empty($pickup_location_settings['field_placeholder'])) ? stripslashes(esc_attr($pickup_location_settings['field_placeholder'])) : "" ?>" placeholder="" autocomplete="off"/>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label"><?php _e('Disable Location Wise Pickup Days Popup', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="When a customer select the pickup as order type, a popup is appeared with the location wise available pickup days. Default is enable."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_woo_delivery_pickup_location_popup">
							       <input type="checkbox" name="coderockz_woo_delivery_pickup_location_popup" id="coderockz_woo_delivery_pickup_location_popup" <?php echo (isset($pickup_location_settings['pickup_location_popup']) && !empty($pickup_location_settings['pickup_location_popup'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_pickup_location_popup_heading"><?php _e('Location Wise Pickup Days Popup Heading', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Location Wise Pickup Days Popup Heading. Default is Location Wise Pickup Days."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_woo_delivery_pickup_location_popup_heading" name="coderockz_woo_delivery_pickup_location_popup_heading" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($pickup_location_settings['pickup_location_popup_heading']) && !empty($pickup_location_settings['pickup_location_popup_heading'])) ? stripslashes(esc_attr($pickup_location_settings['pickup_location_popup_heading'])) : "" ?>" placeholder="" autocomplete="off"/>
	                    	</div>
	                        <input class="coderockz-woo-delivery-submit-btn" type="submit" name="coderockz_pickup_location_form_submit" value="<?php _e('Save Changes', 'coderockz-woo-delivery'); ?>" />

	                    </form>
                	</div>

                </div>

                <div class="coderockz-woo-delivery-card">
					<p class="coderockz-woo-delivery-card-header"><?php _e('Add Pickup Location', 'coderockz-woo-delivery'); ?></p>
					<div class="coderockz-woo-delivery-card-body">
						<p class="coderockz-woo-delivery-timezone-tab-warning"><span class="dashicons dashicons-megaphone"></span><?php _e(' When you CHANGE a pickup location name and the location name is in the option named "Hide This Timeslot for Pickup Location" from custom pickup slots, please indicate it again in "Hide This Timeslot for Pickup Location" option from those custom pickup slots.', 'coderockz-woo-delivery'); ?></p>
                    	<input class="coderockz-woo-delivery-add-pickup-location-btn" type="button" value="<?php _e('Add New Pickup Location', 'coderockz-woo-delivery'); ?>">
	                    <div id="coderockz-woo-delivery-pickup-location-accordion">
                    	  <div class="coderockz-woo-delivery-pickup-location-accordion-header" style="display:none;">
                    	  	<p style="font-size:16px;margin:0;font-weight:700;color:#fff!important;"><?php _e('Pickup Location Name', 'coderockz-woo-delivery'); ?></p>
                    	  </div>
						  <div data-plugin-url="<?php echo CODEROCKZ_WOO_DELIVERY_URL; ?>" class="coderockz-woo-delivery-pickup-location-accordion-content" style="display:none;">
						  	<div class="coderockz-woo-delivery-form-group" style="margin: 0;position: absolute;right: 45px;top: -31px;">
							    <label class="coderockz-woo-delivery-toogle-switch">
							       <input type="checkbox" class="coderockz-woo-delivery-enable-pickup-location"/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
                    		</div>
                    		<span class="dashicons dashicons-trash coderockz-woo-delivery-pickup-location-accordion-delete" style="margin: 0;position: absolute;right: 5px;top: -30px;color:#fff;cursor: pointer;"></span>
                    		<p class="coderockz-woo-delivery-add-pickup-location-notice"><span class="dashicons dashicons-yes"></span><?php _e(' Settings Changed Successfully', 'coderockz-woo-delivery'); ?></p>
                    		<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Pickup Location Name', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip=""><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input type="text" class="coderockz-woo-delivery-pickup-location-name coderockz-woo-delivery-input-field" value="" placeholder="" autocomplete="off"/>
	                    	</div>

	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Pickup Location Google Map URL', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="If you want to give customer a direction after selecting the location under the pickup location field, input the google map url here."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input type="text" class="coderockz-woo-delivery-pickup-location-url coderockz-woo-delivery-input-field" value="" placeholder="" autocomplete="off"/>
	                    	</div>

	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Maximum Pickup For This Location', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Each pickup location take maximum number of pickups that is specified here. After reaching the maximum pickup, the pickup location is disabled automaticaly. Only numerical value is accepted. Keep blank to takes unlimited pickup."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input type="number" class="coderockz-woo-delivery-pickup-location-order coderockz-woo-delivery-number-field" min="1" onkeyup="if(!Number.isInteger(Number(this.value)) || this.value < 1) this.value = null;" value="" placeholder="" autocomplete="off"/>
	                    	</div>


	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label coderockz-woo-delivery-checkbox-label"><?php _e('Hide For', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip coderockz-woo-delivery-checkbox-tooltip" tooltip="This pickup location will be hidden for the selected days."><span class="dashicons dashicons-editor-help"></span></p>
	                    		<div class="coderockz-woo-delivery-pickup-location-enable-for" style="display:inline-block">
	                    		<input type="checkbox" name="coderockz_woo_delivery_custom_pickup_location_disable" value="6"><label class="coderockz-woo-delivery-checkbox-field-text">Saturday</label><br/>
								<input type="checkbox" name="coderockz_woo_delivery_custom_pickup_location_disable" value="0"><label class="coderockz-woo-delivery-checkbox-field-text">Sunday</label><br/>
								<input type="checkbox" name="coderockz_woo_delivery_custom_pickup_location_disable" value="1"><label class="coderockz-woo-delivery-checkbox-field-text">Monday</label><br/>
								<input type="checkbox" name="coderockz_woo_delivery_custom_pickup_location_disable" value="2"><label class="coderockz-woo-delivery-checkbox-field-text">Tuesday</label><br/>
								<input type="checkbox" name="coderockz_woo_delivery_custom_pickup_location_disable" value="3"><label class="coderockz-woo-delivery-checkbox-field-text">Wednesday</label><br/>
								<input type="checkbox" name="coderockz_woo_delivery_custom_pickup_location_disable" value="4"><label class="coderockz-woo-delivery-checkbox-field-text">Thursday</label><br/>
								<input type="checkbox" name="coderockz_woo_delivery_custom_pickup_location_disable" value="5"><label class="coderockz-woo-delivery-checkbox-field-text">Friday</label><br/>
								</div>
	                    	</div>

	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Hide This Location For Categories', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="If you want to disable the location for a specific category then select the category from here."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<select id="coderockz_woo_delivery_pickup_location_hide_categories" name="coderockz_woo_delivery_pickup_location_hide_categories[]" class="coderockz_woo_delivery_pickup_location_hide_categories" multiple>
                                
                                <?php
                                foreach ($all_categories as $cat) {

                                    echo '<option value="'.$cat->name.'">'.$cat->name.'</option>';
                                }
                                ?>
                                </select>
	                    	</div>
	                    	<?php if(get_option('coderockz_woo_delivery_large_product_list') == false) { ?>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Hide This Location For Products', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="If you want to hide the location for a specific product then select the product from here."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<select id="coderockz_woo_delivery_pickup_location_hide_products" name="coderockz_woo_delivery_pickup_location_hide_products[]" class="coderockz_woo_delivery_pickup_location_hide_products" multiple>
                                
                                <?php
                                foreach ($store_products as $key=>$value) {
                                    echo '<option value="'.$key.'">'.$value.'</option>';
                                }
                                ?>
                                </select>
	                    	</div>
	                    	<?php } else { ?>

                    		<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Hide This Location For Products', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="If you want to hide the location for a specific product then select the product from here."><span class="dashicons dashicons-editor-help"></span></p>
	                    		<input name="coderockz_woo_delivery_pickup_location_hide_products_input" type="text" class="coderockz_woo_delivery_pickup_location_hide_products_input coderockz-woo-delivery-input-field" value="" placeholder="Comma separated Product/Variation ID" autocomplete="off"/>
	                    	</div>

	                    	<?php } ?>
	                    	<div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label"><?php _e('Show If Cart Has Other Categories Or Products', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="If there is hidden category's products or hidden products in the cart then whatever there are other category's products or other products, the location is hidden. If you want to reverse this enable it."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch">
							       <input type="checkbox" class="coderockz_woo_delivery_pickup_location_shown_other_categories_products"/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>




						    <button class="coderockz-woo-delivery-custom-pickup-location-saving coderockz-woo-delivery-submit-btn"><?php _e('Save Changes', 'coderockz-woo-delivery'); ?></button>
						  </div>
						  <?php
						  	
						  	if(isset($pickup_location_settings['pickup_location']) && count($pickup_location_settings['pickup_location'])>0){


						  		foreach($pickup_location_settings['pickup_location'] as $individual_pickup_location) {
						  			

	                    			?>

	                    			<div class="coderockz-woo-delivery-pickup-location-accordion-header">
		                    	  	<p style="font-size:16px;margin:0;font-weight:700;color:#fff!important;"><?php echo stripslashes($individual_pickup_location['location_name']); ?></p>
		                    	  </div>
								  <div class="coderockz-woo-delivery-pickup-location-accordion-content">
								  	<div class="coderockz-woo-delivery-form-group" style="margin: 0;position: absolute;right: 45px;top: -31px;">
									    <label class="coderockz-woo-delivery-toogle-switch">
									       <input type="checkbox" class="coderockz-woo-delivery-enable-pickup-location" <?php echo isset($individual_pickup_location['enable']) && !empty($individual_pickup_location['enable'])  ? "checked" : "";?>/>
									       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
									    </label>
		                    		</div>
		                    		<span class="dashicons dashicons-trash coderockz-woo-delivery-pickup-location-accordion-delete" style="margin: 0;position: absolute;right: 5px;top: -30px;color:#fff;cursor: pointer;"></span>
		                    		<p class="coderockz-woo-delivery-add-pickup-location-notice"><span class="dashicons dashicons-yes"></span><?php _e(' Settings Changed Successfully', 'coderockz-woo-delivery'); ?></p>


			                    	<div class="coderockz-woo-delivery-form-group">
			                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Pickup Location', 'coderockz-woo-delivery'); ?></label>
			                    		<p class="coderockz-woo-delivery-tooltip" tooltip=""><span class="dashicons dashicons-editor-help"></span></p>
			                        	<input type="text" class="coderockz-woo-delivery-pickup-location-name coderockz-woo-delivery-input-field" value="<?php echo isset($individual_pickup_location['location_name']) && $individual_pickup_location['location_name'] !='' ? stripslashes($individual_pickup_location['location_name']) : ""; ?>" placeholder="" autocomplete="off"/>
			                    	</div>

			                    	<div class="coderockz-woo-delivery-form-group">
			                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Pickup Location Google Map URL', 'coderockz-woo-delivery'); ?></label>
			                    		<p class="coderockz-woo-delivery-tooltip" tooltip="If you want to give customer a direction after selecting the location under the pickup location field, input the google map url here."><span class="dashicons dashicons-editor-help"></span></p>
			                        	<input type="text" class="coderockz-woo-delivery-pickup-location-url coderockz-woo-delivery-input-field" value="<?php echo isset($individual_pickup_location['map_url']) && $individual_pickup_location['map_url'] !='' ? stripslashes($individual_pickup_location['map_url']) : ""; ?>" placeholder="" autocomplete="off"/>
			                    	</div>


			                    	<div class="coderockz-woo-delivery-form-group">
			                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Maximum Pickup For This Location', 'coderockz-woo-delivery'); ?></label>
			                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Each pickup location take maximum number of pickups that is specified here. After reaching the maximum pickup, the pickup location is disabled automatically. Only numerical value is accepted."><span class="dashicons dashicons-editor-help"></span></p>
			                        	<input type="number" class="coderockz-woo-delivery-pickup-location-order coderockz-woo-delivery-number-field" min="1" onkeyup="if(!Number.isInteger(Number(this.value)) || this.value < 1) this.value = null;" value="<?php echo isset($individual_pickup_location['max_order']) && $individual_pickup_location['max_order'] !='' ? $individual_pickup_location['max_order'] : ""; ?>" placeholder="" autocomplete="off"/>
			                    	</div>


			                    	<div class="coderockz-woo-delivery-form-group">
			                    		<label class="coderockz-woo-delivery-form-label coderockz-woo-delivery-checkbox-label"><?php _e('Hide For', 'coderockz-woo-delivery'); ?></label>
			                    		<p class="coderockz-woo-delivery-tooltip coderockz-woo-delivery-checkbox-tooltip" tooltip="This pickup location will be hidden for the selected days."><span class="dashicons dashicons-editor-help"></span></p>
			                    		<div class="coderockz-woo-delivery-pickup-location-enable-for" style="display:inline-block">
			                    		<input type="checkbox" name="coderockz_woo_delivery_custom_pickup_location_disable" value="6" <?php echo isset($individual_pickup_location['disable_for']) && !empty($individual_pickup_location['disable_for']) && in_array("6",$individual_pickup_location['disable_for']) ? "checked" : "";?>><label class="coderockz-woo-delivery-checkbox-field-text">Saturday</label><br/>
										<input type="checkbox" name="coderockz_woo_delivery_custom_pickup_location_disable" value="0" <?php echo isset($individual_pickup_location['disable_for']) && !empty($individual_pickup_location['disable_for']) && in_array("0",$individual_pickup_location['disable_for']) ? "checked" : "";?>><label class="coderockz-woo-delivery-checkbox-field-text">Sunday</label><br/>
										<input type="checkbox" name="coderockz_woo_delivery_custom_pickup_location_disable" value="1" <?php echo isset($individual_pickup_location['disable_for']) && !empty($individual_pickup_location['disable_for']) && in_array("1",$individual_pickup_location['disable_for']) ? "checked" : "";?>><label class="coderockz-woo-delivery-checkbox-field-text">Monday</label><br/>
										<input type="checkbox" name="coderockz_woo_delivery_custom_pickup_location_disable" value="2" <?php echo isset($individual_pickup_location['disable_for']) && !empty($individual_pickup_location['disable_for']) && in_array("2",$individual_pickup_location['disable_for']) ? "checked" : "";?>><label class="coderockz-woo-delivery-checkbox-field-text">Tuesday</label><br/>
										<input type="checkbox" name="coderockz_woo_delivery_custom_pickup_location_disable" value="3" <?php echo isset($individual_pickup_location['disable_for']) && !empty($individual_pickup_location['disable_for']) && in_array("3",$individual_pickup_location['disable_for']) ? "checked" : "";?>><label class="coderockz-woo-delivery-checkbox-field-text">Wednesday</label><br/>
										<input type="checkbox" name="coderockz_woo_delivery_custom_pickup_location_disable" value="4" <?php echo isset($individual_pickup_location['disable_for']) && !empty($individual_pickup_location['disable_for']) && in_array("4",$individual_pickup_location['disable_for']) ? "checked" : "";?>><label class="coderockz-woo-delivery-checkbox-field-text">Thursday</label><br/>
										<input type="checkbox" name="coderockz_woo_delivery_custom_pickup_location_disable" value="5" <?php echo isset($individual_pickup_location['disable_for']) && !empty($individual_pickup_location['disable_for']) && in_array("5",$individual_pickup_location['disable_for']) ? "checked" : "";?>><label class="coderockz-woo-delivery-checkbox-field-text">Friday</label><br/>
										</div>
			                    	</div>

			                    	<div class="coderockz-woo-delivery-form-group">
			                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Hide This Location For Categories', 'coderockz-woo-delivery'); ?></label>
			                    		<p class="coderockz-woo-delivery-tooltip" tooltip="If you want to hide the timeslot for a specific category then select the category from here."><span class="dashicons dashicons-editor-help"></span></p>
			                        	<select id="coderockz_woo_delivery_pickup_location_hide_categories2" name="coderockz_woo_delivery_pickup_location_hide_categories[]" class="coderockz_woo_delivery_pickup_location_hide_categories2" multiple>
		                                
		                                <?php

		                                $pickuplocation_hide_categories = [];
										if(isset($individual_pickup_location['hide_categories']) && !empty($individual_pickup_location['hide_categories'])) {
											foreach ($individual_pickup_location['hide_categories'] as $hide_cat) {
												$pickuplocation_hide_categories[] = stripslashes($hide_cat);
											}
										}


		                                foreach ($all_categories as $cat) {

		                                	$selected = isset($individual_pickup_location['hide_categories']) && !empty($individual_pickup_location['hide_categories']) && in_array(htmlspecialchars_decode($cat->name),$pickuplocation_hide_categories) ? "selected" : "";
		                                    echo '<option value="'.$cat->name.'" '.$selected.'>'.$cat->name.'</option>';
		                                }
		                                ?>
		                                </select>
			                    	</div>
			                    	<?php if(get_option('coderockz_woo_delivery_large_product_list') == false) { ?>
			                    	<div class="coderockz-woo-delivery-form-group">
			                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Hide This Location For Products', 'coderockz-woo-delivery'); ?></label>
			                    		<p class="coderockz-woo-delivery-tooltip" tooltip="If you want to hide the ocation for a specific product then select the product from here."><span class="dashicons dashicons-editor-help"></span></p>
			                        	<select id="coderockz_woo_delivery_pickup_location_hide_products2" name="coderockz_woo_delivery_pickup_location_hide_products[]" class="coderockz_woo_delivery_pickup_location_hide_products2" multiple>
		                                
		                                <?php
		                                foreach ($store_products as $key=>$value) {

		                                	$selected = isset($individual_pickup_location['hide_products']) && !empty($individual_pickup_location['hide_products']) && in_array($key,$individual_pickup_location['hide_products']) ? "selected" : "";
		                                    echo '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
		                                }
		                                ?>
		                                </select>
			                    	</div>
			                    	<?php } else { ?>

		                    		<div class="coderockz-woo-delivery-form-group">
			                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Hide This Location For Products', 'coderockz-woo-delivery'); ?></label>
			                    		<p class="coderockz-woo-delivery-tooltip" tooltip="If you want to hide the ocation for a specific product then select the product from here."><span class="dashicons dashicons-editor-help"></span></p>
			                        	<?php 
			                        	$location_hide_products_input = isset($individual_pickup_location['hide_products']) && !empty($individual_pickup_location['hide_products']) ? $individual_pickup_location['hide_products'] : array();
			                        	$location_hide_products_input = implode(",",$location_hide_products_input);
			                        	?>
			                    		<input name="coderockz_woo_delivery_pickup_location_hide_products2_input" type="text" class="coderockz_woo_delivery_pickup_location_hide_products2_input coderockz-woo-delivery-input-field" value="<?php echo $location_hide_products_input; ?>" placeholder="Comma separated Product/Variation ID" autocomplete="off"/>
			                    	</div>

			                    	<?php } ?>
			                    	<div class="coderockz-woo-delivery-form-group">
			                        	<span class="coderockz-woo-delivery-form-label"><?php _e('Show If Cart Has Other Categories Or Products', 'coderockz-woo-delivery'); ?></span>
			                        	<p class="coderockz-woo-delivery-tooltip" tooltip="If there is hidden category's products or hidden products in the cart then whatever there are other category's products or other products, the location is hidden. If you want to reverse this enable it."><span class="dashicons dashicons-editor-help"></span></p>
									    <label class="coderockz-woo-delivery-toogle-switch">
									       <input type="checkbox" class="coderockz_woo_delivery_pickup_location_shown_other_categories_products" <?php echo isset($individual_pickup_location['location_shown_other_categories_products']) && !empty($individual_pickup_location['location_shown_other_categories_products'])  ? "checked" : "";?>/>
									       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
									    </label>
			                    	</div>

								    <button class="coderockz-woo-delivery-custom-pickup-location-saving coderockz-woo-delivery-submit-btn"><?php _e('Save Changes', 'coderockz-woo-delivery'); ?></button>
								  </div>

	                    		<?php
						  		}
						  		
						  	}
						  	
						  ?>
	                	</div>
                	</div>

                </div>

			</div>
			<div data-tab="tab14" class="coderockz-woo-delivery-tabcontent">
				<div class="coderockz-woo-delivery-card">
					<p class="coderockz-woo-delivery-card-header"><?php _e('Store Cutoff/Closing Time For Delivery', 'coderockz-woo-delivery'); ?></p>
					<div class="coderockz-woo-delivery-card-body">
						<p class="coderockz-woo-delivery-store-closing-notice"><span class="dashicons dashicons-yes"></span><?php _e(' Settings Changed Successfully', 'coderockz-woo-delivery'); ?></p>

						<p class="coderockz-woo-delivery-timezone-tab-warning"><span class="dashicons dashicons-megaphone"></span><?php _e('This feature is helpful, if you don\'t want to take an delivery/pickup order after a certain time of the current day.', 'coderockz-woo-delivery'); ?></p>						
						<form action="" method="post" id ="coderockz_woo_delivery_store_closing_settings_submit">
							<?php wp_nonce_field('coderockz_woo_delivery_nonce'); ?>
		                    <div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label"><?php _e('Enable Store Cutoff/Closing Time for Delivery', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="Enable it if you have an offline shop and you can't take an order after a specific time. Default is disable."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_woo_delivery_enable_closing_time">
							       <input type="checkbox" name="coderockz_woo_delivery_enable_closing_time" id="coderockz_woo_delivery_enable_closing_time" <?php echo (isset($time_settings['enable_closing_time']) && !empty($time_settings['enable_closing_time'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>
	                    	<?php 
                    			$store_closing_hour = "";
            					$store_closing_min = "";
            					$store_closing_format= "am";
            					$enable_closing_time = (isset($time_settings['enable_closing_time']) && !empty($time_settings['enable_closing_time'])) ? $time_settings['enable_closing_time'] : false;
                    			
                    			if(isset($time_settings['store_closing_time']) && $time_settings['store_closing_time'] !='') {
                    				$store_closing_time = (int)$time_settings['store_closing_time'];

                    				if($store_closing_time > 0 && $store_closing_time <= 59) {

                    					$store_closing_hour = "12";
                    					$store_closing_min = sprintf("%02d", $store_closing_time);
                    					$store_closing_format= "am";
                    				} elseif($store_closing_time > 59 && $store_closing_time <= 719) {
										$store_closing_min = sprintf("%02d", (int)$store_closing_time%60);
										$store_closing_hour = sprintf("%02d", ((int)$store_closing_time-$store_closing_min)/60);
										$store_closing_format= "am";
										
                    				} elseif($store_closing_time > 719 && $store_closing_time <= 1439) {
										$store_closing_min = sprintf("%02d", (int)$store_closing_time%60);
										$store_closing_hour = sprintf("%02d", ((int)$store_closing_time-$store_closing_min)/60);
										if($store_closing_hour>12) {
											$store_closing_hour = sprintf("%02d", $store_closing_hour-12);
										}
										$store_closing_format= "pm";
                    				} elseif($store_closing_time === 0) {
										$store_closing_min = "00";
										$store_closing_hour = "12";
										$store_closing_format= "am";
                    				}

                    			}
                    		?>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_closing_time"><?php _e('Store Cutoff/Closing Time for Delivery', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="If you don't want to get an order after a specific time in the same day, put the time here. Only numerical value is accepted."><span class="dashicons dashicons-editor-help"></span></p>
	                    		<div id="coderockz_woo_delivery_closing_time" class="coderockz_woo_delivery_closing_time">
	                    			
	                        	<input name="coderockz_woo_delivery_closing_time_hour" type="number" class="coderockz-woo-delivery-number-field" max="12" min="1" onkeyup="if(!Number.isInteger(Number(this.value)) || this.value > 12 || this.value < 1) this.value = null;" value="<?php echo $store_closing_hour; ?>" placeholder="Hour" autocomplete="off"/>
	                        	<input name="coderockz_woo_delivery_closing_time_min" type="number" class="coderockz-woo-delivery-number-field" max="59" min="0" onkeyup="if(!Number.isInteger(Number(this.value)) || this.value > 59 || this.value < 0) this.value = null;" value="<?php echo $store_closing_min; ?>" placeholder="Minute" autocomplete="off"/>
	                        	<select class="coderockz-woo-delivery-select-field" name="coderockz_woo_delivery_closing_time_format">
									<option value="am" <?php selected($store_closing_format,"am",true); ?>>AM</option>
									<option value="pm" <?php selected($store_closing_format,"pm",true); ?>>PM</option>
								</select>
	                        	</div>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_extend_closing_time"><?php _e('Additional Days to close After Store Closing', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Need to extend the closing time of your store? If you don't want to take order for certain days after store closed, give the number of days here."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input onkeyup="if(!Number.isInteger(Number(this.value)) || this.value < 1) this.value = null;" id="coderockz_woo_delivery_extend_closing_time" name="coderockz_woo_delivery_extend_closing_time" type="number" class="coderockz-woo-delivery-number-field" value="<?php echo (isset($time_settings['extended_closing_days']) && !empty($time_settings['extended_closing_days'])) ? $time_settings['extended_closing_days'] : ""; ?>" placeholder="" autocomplete="off"/>
	                    	</div>
	                    	<input class="coderockz-woo-delivery-submit-btn" type="submit" name="coderockz_woo_delivery_store_closing_settings_submit" value="<?php _e('Save Changes', 'coderockz-woo-delivery'); ?>" />
	                    </form>
                	</div>

                </div>
                <div class="coderockz-woo-delivery-card">
					<p class="coderockz-woo-delivery-card-header"><?php _e('Store Cutoff/Closing Time For Pickup', 'coderockz-woo-delivery'); ?></p>
					<div class="coderockz-woo-delivery-card-body">
						<p class="coderockz-woo-delivery-store-closing-notice-pickup"><span class="dashicons dashicons-yes"></span><?php _e(' Settings Changed Successfully', 'coderockz-woo-delivery'); ?></p>

						<p class="coderockz-woo-delivery-timezone-tab-warning"><span class="dashicons dashicons-megaphone"></span><?php _e('This feature is helpful, if you don\'t want to take an pickup order after a certain time of the current day.', 'coderockz-woo-delivery'); ?></p>						
						<form action="" method="post" id ="coderockz_woo_delivery_store_closing_pickup_settings_submit">
							<?php wp_nonce_field('coderockz_woo_delivery_nonce'); ?>
		                    <div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label"><?php _e('Enable Store Cutoff/Closing Time for Pickup', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="Enable it if you have an offline shop and you can't take an order as pickup after a specific time. Default is disable."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_woo_delivery_enable_closing_time_pickup">
							       <input type="checkbox" name="coderockz_woo_delivery_enable_closing_time_pickup" id="coderockz_woo_delivery_enable_closing_time_pickup" <?php echo (isset($pickup_time_settings['enable_closing_time']) && !empty($pickup_time_settings['enable_closing_time'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>
	                    	<?php 
                    			$store_closing_hour_pickup = "";
            					$store_closing_min_pickup = "";
            					$store_closing_format_pickup= "am";
            					$enable_closing_time = (isset($pickup_time_settings['enable_closing_time']) && !empty($pickup_time_settings['enable_closing_time'])) ? $pickup_time_settings['enable_closing_time'] : false;
                    			
                    			if(isset($pickup_time_settings['store_closing_time']) && $pickup_time_settings['store_closing_time'] !='') {
                    				$store_closing_time_pickup = (int)$pickup_time_settings['store_closing_time'];

                    				if($store_closing_time_pickup > 0 && $store_closing_time_pickup <= 59) {

                    					$store_closing_hour_pickup = "12";
                    					$store_closing_min_pickup = sprintf("%02d", $store_closing_time_pickup);
                    					$store_closing_format_pickup= "am";
                    				} elseif($store_closing_time_pickup > 59 && $store_closing_time_pickup <= 719) {
										$store_closing_min_pickup = sprintf("%02d", (int)$store_closing_time_pickup%60);
										$store_closing_hour_pickup = sprintf("%02d", ((int)$store_closing_time_pickup-$store_closing_min_pickup)/60);
										$store_closing_format_pickup= "am";
										
                    				} elseif($store_closing_time_pickup > 719 && $store_closing_time_pickup <= 1439) {
										$store_closing_min_pickup = sprintf("%02d", (int)$store_closing_time_pickup%60);
										$store_closing_hour_pickup = sprintf("%02d", ((int)$store_closing_time_pickup-$store_closing_min_pickup)/60);
										if($store_closing_hour_pickup>12) {
											$store_closing_hour_pickup = sprintf("%02d", $store_closing_hour_pickup-12);
										}
										$store_closing_format_pickup= "pm";
                    				} elseif($store_closing_time_pickup === 0) {
										$store_closing_min_pickup = "00";
										$store_closing_hour_pickup = "12";
										$store_closing_format_pickup= "am";
                    				}

                    			}
                    		?>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_closing_time"><?php _e('Store Cutoff/Closing Time for Pickup', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="If you don't want to get an order after a specific time in the same day, put the time here. Only numerical value is accepted."><span class="dashicons dashicons-editor-help"></span></p>
	                    		<div id="coderockz_woo_delivery_closing_time_pickup" class="coderockz_woo_delivery_closing_time_pickup">
	                    			
	                        	<input name="coderockz_woo_delivery_closing_time_hour_pickup" type="number" class="coderockz-woo-delivery-number-field" max="12" min="1" onkeyup="if(!Number.isInteger(Number(this.value)) || this.value > 12 || this.value < 1) this.value = null;" value="<?php echo $store_closing_hour_pickup; ?>" placeholder="Hour" autocomplete="off"/>
	                        	<input name="coderockz_woo_delivery_closing_time_min_pickup" type="number" class="coderockz-woo-delivery-number-field" max="59" min="0" onkeyup="if(!Number.isInteger(Number(this.value)) || this.value > 59 || this.value < 0) this.value = null;" value="<?php echo $store_closing_min_pickup; ?>" placeholder="Minute" autocomplete="off"/>
	                        	<select class="coderockz-woo-delivery-select-field" name="coderockz_woo_delivery_closing_time_format_pickup">
									<option value="am" <?php selected($store_closing_format_pickup,"am",true); ?>>AM</option>
									<option value="pm" <?php selected($store_closing_format_pickup,"pm",true); ?>>PM</option>
								</select>
	                        	</div>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_extend_closing_time_pickup"><?php _e('Additional Days to close After Store Closing', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Need to extend the closing time of your store? If you don't want to take order for certain days after store closed, give the number of days here."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input onkeyup="if(!Number.isInteger(Number(this.value)) || this.value < 1) this.value = null;" id="coderockz_woo_delivery_extend_closing_time_pickup" name="coderockz_woo_delivery_extend_closing_time_pickup" type="number" class="coderockz-woo-delivery-number-field" value="<?php echo (isset($pickup_time_settings['extended_closing_days']) && !empty($pickup_time_settings['extended_closing_days'])) ? $pickup_time_settings['extended_closing_days'] : ""; ?>" placeholder="" autocomplete="off"/>
	                    	</div>
	                    	<input class="coderockz-woo-delivery-submit-btn" type="submit" name="coderockz_woo_delivery_store_closing_pickup_settings_submit" value="<?php _e('Save Changes', 'coderockz-woo-delivery'); ?>" />
	                    </form>
                	</div>

                </div>
				<div class="coderockz-woo-delivery-card">
					<p class="coderockz-woo-delivery-card-header"><?php _e('Weekday Wise Cutoff/Closing Time Settings', 'coderockz-woo-delivery'); ?></p>
					<div class="coderockz-woo-delivery-card-body">
						<p class="coderockz-woo-delivery-different-store-closing-notice"><span class="dashicons dashicons-yes"></span><?php _e(' Settings Changed Successfully', 'coderockz-woo-delivery'); ?></p>
						<p class="coderockz-woo-delivery-timezone-tab-warning"><span class="dashicons dashicons-megaphone"></span><?php _e(' Enable Different Closing Time For Different Day, makes the overall Store Cutoff/Closing Time Functionality disabled'); ?></p>
						<form action="" method="post" id ="coderockz_woo_delivery_different_closing_settings_submit">
							<?php wp_nonce_field('coderockz_woo_delivery_nonce'); ?>
		                    
	                    	<div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label" style="width:365px!important"><?php _e('Enable Different Closing Time For Different Day', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="Enable it if you want different closing or cutoff time for different week day. Default is disable."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_woo_delivery_enable_different_closing_time">
							       <input type="checkbox" class="coderockz_woo_delivery_enable_different_closing_time" name="coderockz_woo_delivery_enable_different_closing_time" id="coderockz_woo_delivery_enable_different_closing_time" <?php echo (isset($time_settings['enable_different_closing_time']) && !empty($time_settings['enable_different_closing_time'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-different-closing-time-section">
	                    	<?php
	                    	$weekday = array("0"=>"Sunday", "1"=>"Monday", "2"=>"Tuesday", "3"=>"Wednesday", "4"=>"Thursday", "5"=>"Friday", "6"=>"Saturday");
	                        foreach ($weekday as $key => $value) {

	                        	if(isset($time_settings['different_extended_closing_day'][$key]) && $time_settings['different_extended_closing_day'][$key] != "") {
                    					$different_extended_closing_day_[$key] = $time_settings['different_extended_closing_day'][$key];
                    			}

                    			$store_closing_hour_[$key] = "";
	            				$store_closing_min_[$key] = "";
	            				$store_closing_format_[$key]= "am";

                    			if(isset($time_settings['different_store_closing_time'][$key])) {
                    				
	                				$store_closing_time_[$key] = (int)$time_settings['different_store_closing_time'][$key];

	                				if($store_closing_time_[$key] > 0 && $store_closing_time_[$key] <= 59) {

	                					$store_closing_hour_[$key] = "12";
	                					$store_closing_min_[$key] = sprintf("%02d", $store_closing_time_[$key]);
	                					$store_closing_format_[$key]= "am";
	                				} elseif($store_closing_time_[$key] > 59 && $store_closing_time_[$key] <= 719) {
										$store_closing_min_[$key] = sprintf("%02d", (int)$store_closing_time_[$key]%60);
										$store_closing_hour_[$key] = sprintf("%02d", ((int)$store_closing_time_[$key]-$store_closing_min_[$key])/60);
										$store_closing_format_[$key]= "am";
										
	                				} elseif($store_closing_time_[$key] > 719 && $store_closing_time_[$key] <= 1439) {
										$store_closing_min_[$key] = sprintf("%02d", (int)$store_closing_time_[$key]%60);
										$store_closing_hour_[$key] = sprintf("%02d", ((int)$store_closing_time_[$key]-$store_closing_min_[$key])/60);
										if($store_closing_hour_[$key]>12) {
											$store_closing_hour_[$key] = sprintf("%02d", $store_closing_hour_[$key]-12);
										}
										$store_closing_format_[$key]= "pm";
	                				} elseif($store_closing_time_[$key] === 0) {
										$store_closing_min_[$key] = "00";
										$store_closing_hour_[$key] = "12";
										$store_closing_format_[$key]= "am";
	                				}

                				}

                				

                				?>
                				<div class="coderockz-woo-delivery-different-closing">
	                			<div class="coderockz-woo-delivery-form-group">
		                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_closing_time_<?php echo $key; ?>"><?php echo $value; ?></label>
		                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Only numerical value is accepted."><span class="dashicons dashicons-editor-help"></span></p>
		                    		<div id="coderockz_woo_delivery_closing_time_<?php echo $key; ?>" class="coderockz_woo_delivery_closing_time_<?php echo $key; ?>" style="width: 300px!important;display: inline-flex!important;">
		                    			
		                        	<input style="width: 33.33%!important;" name="coderockz_woo_delivery_closing_time_hour_<?php echo $key; ?>" type="number" class="coderockz-woo-delivery-number-field" max="12" min="1" onkeyup="if(!Number.isInteger(Number(this.value)) || this.value > 12 || this.value < 1) this.value = null;" value="<?php echo $store_closing_hour_[$key]; ?>" placeholder="Hour" autocomplete="off"/>
		                        	<input style="width: 33.33%!important;" name="coderockz_woo_delivery_closing_time_min_<?php echo $key; ?>" type="number" class="coderockz-woo-delivery-number-field" max="59" min="0" onkeyup="if(!Number.isInteger(Number(this.value)) || this.value > 59 || this.value < 0) this.value = null;" value="<?php echo $store_closing_min_[$key]; ?>" placeholder="Minute" autocomplete="off"/>
		                        	<select style="width: 33.33%!important;" class="coderockz-woo-delivery-select-field" name="coderockz_woo_delivery_closing_time_format_<?php echo $key; ?>">
										<option value="am" <?php selected($store_closing_format_[$key],"am",true); ?>>AM</option>
										<option value="pm" <?php selected($store_closing_format_[$key],"pm",true); ?>>PM</option>
									</select>
		                        	</div>
		                    	</div>


		                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_extend_closing_time_<?php echo $key; ?>"><?php _e(' Additional Days to close After Store Closing For ', 'coderockz-woo-delivery'); ?><?php echo $value?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Need to extend the closing time of your store? If you don't want to take order for certain days after store closed, give the number of days here."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input onkeyup="if(!Number.isInteger(Number(this.value)) || this.value < 1) this.value = null;" id="coderockz_woo_delivery_extend_closing_time_<?php echo $key; ?>" name="coderockz_woo_delivery_extend_closing_time_<?php echo $key; ?>" type="number" class="coderockz-woo-delivery-number-field" value="<?php echo (isset($different_extended_closing_day_[$key]) && !empty($different_extended_closing_day_[$key])) ? $different_extended_closing_day_[$key] : ""; ?>" placeholder="" autocomplete="off"/>
	                    		</div>


		                    	</div>

                				<?php
	                        }
	                        ?>
	                    	</div>
	                    	
	                    	<input class="coderockz-woo-delivery-submit-btn" type="submit" name="coderockz_woo_delivery_different_closing_settings_submit" value="<?php _e('Save Changes', 'coderockz-woo-delivery'); ?>" />
	                    </form>
                	</div>

                </div>
            </div>
			<div data-tab="tab15" class="coderockz-woo-delivery-tabcontent">
				<div class="coderockz-woo-delivery-card">
					<p class="coderockz-woo-delivery-card-header"><?php _e('Overall Processing Days', 'coderockz-woo-delivery'); ?></p>
					<div class="coderockz-woo-delivery-card-body">
						<p class="coderockz-woo-delivery-overall-processing-days-notice"></p>
						<?php if(get_option('coderockz_woo_delivery_large_product_list') !== false) { ?>
							<p class="coderockz-woo-delivery-timezone-tab-warning"><span class="dashicons dashicons-megaphone"></span><?php _e('In Exclude Individual Product option, if product is simple input the product ID and if product is variable input the variation ID', 'coderockz-woo-delivery'); ?></p>
						<?php } ?>
	                    <form action="" method="post" id ="coderockz_overall_processing_days_settings_form_submit">
	                        <?php wp_nonce_field('coderockz_woo_delivery_nonce'); ?>
	                        <div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_delivery_overall_processing_days"><?php _e('Overall Processing Days for All Products', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="If you need processing days for all of your products, put the number of days here. Default is 0 days."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input style="width:150px!important" onkeyup="if(!Number.isInteger(Number(this.value)) || this.value < 1) this.value = null;" id="coderockz_delivery_overall_processing_days" name="coderockz_delivery_overall_processing_days" type="number" class="coderockz-woo-delivery-number-field" value="<?php echo (isset($processing_days_settings['overall_processing_days']) && !empty($processing_days_settings['overall_processing_days'])) ? $processing_days_settings['overall_processing_days'] : ""; ?>" placeholder="" autocomplete="off"/><span class="coderockz-woo-delivery-processing-days-placeholder">Days</span>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_delivery_backorder_processing_days"><?php _e('Processing Days If Cart Has Backorder Products', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="If you need processing days when cart contains a backorder product, put the number of days here. Default is 0 days."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input style="width:150px!important" onkeyup="if(!Number.isInteger(Number(this.value)) || this.value < 1) this.value = null;" id="coderockz_delivery_backorder_processing_days" name="coderockz_delivery_backorder_processing_days" type="number" class="coderockz-woo-delivery-number-field" value="<?php echo (isset($processing_days_settings['backorder_processing_days']) && !empty($processing_days_settings['backorder_processing_days'])) ? $processing_days_settings['backorder_processing_days'] : ""; ?>" placeholder="" autocomplete="off"/><span class="coderockz-woo-delivery-processing-days-placeholder">Days</span>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Exclude Categories from Processing Days', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Select the product categories for which you don't want the processing days."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<select id="coderockz_woo_delivery_exclude_categories_processing_days" name="coderockz_woo_delivery_exclude_categories_processing_days[]" class="coderockz_woo_delivery_exclude_categories_processing_days" multiple>
                                
                                <?php
                                $exclude_categories_processing_days = [];
								if(isset($processing_days_settings['exclude_categories']) && !empty($processing_days_settings['exclude_categories'])) {
									foreach ($processing_days_settings['exclude_categories'] as $hide_cat) {
										$exclude_categories_processing_days[] = stripslashes($hide_cat);
									}
								}
                                foreach ($all_categories as $cat) {

                                	$selected = isset($processing_days_settings['exclude_categories']) && !empty($processing_days_settings['exclude_categories']) && in_array(htmlspecialchars_decode($cat->name),$exclude_categories_processing_days) ? "selected" : "";
                                    echo '<option value="'.$cat->name.'" '.$selected.'>'.$cat->name.'</option>';
                                }
                                ?>
                                </select>
	                    	</div>
	                    	<?php if(get_option('coderockz_woo_delivery_large_product_list') == false) { ?>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Exclude Product from Processing Days', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Select the product for which you don't want the processing days."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<select id="coderockz_woo_delivery_exclude_product_processing_days" name="coderockz_woo_delivery_exclude_product_processing_days[]" class="coderockz_woo_delivery_exclude_product_processing_days" multiple>
                                
                                <?php
                                
                                foreach ($store_products as $key=>$value) {

                                	$selected = isset($processing_days_settings['exclude_products']) && !empty($processing_days_settings['exclude_products']) && in_array($key,$processing_days_settings['exclude_products']) ? "selected" : "";
                                    echo '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
                                }
                                ?>
                                </select>
	                    	</div>
	                    	<?php } else { ?>

                    		<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Exclude Product from Processing Days', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Select the product for which you don't want the processing days."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<?php 
	                        	$exclude_products_input_processing_days = isset($processing_days_settings['exclude_products']) && !empty($processing_days_settings['exclude_products']) ? $processing_days_settings['exclude_products'] : array();
	                        	$exclude_products_input_processing_days = implode(",",$exclude_products_input_processing_days);
	                        	?>
	                    		<input id="coderockz_woo_delivery_exclude_product_input_processing_days" name="coderockz_woo_delivery_exclude_product_input_processing_days" type="text" class="coderockz_woo_delivery_exclude_product_input_processing_days coderockz-woo-delivery-input-field" value="<?php echo $exclude_products_input_processing_days; ?>" placeholder="<?php _e('Comma(,) separated Product/Variation ID', 'coderockz-woo-delivery'); ?>" autocomplete="off"/>
	                    	</div>

	                    	<?php } ?>
	                        <input class="coderockz-woo-delivery-submit-btn" type="submit" name="coderockz_overall_processing_days_settings_form_submit" value="<?php _e('Save Changes', 'coderockz-woo-delivery'); ?>" />
	                    </form>
                	</div>
                </div>
				<div class="coderockz-woo-delivery-card">
					<p class="coderockz-woo-delivery-card-header"><?php _e('Processing Days Settings', 'coderockz-woo-delivery'); ?></p>
					<div class="coderockz-woo-delivery-card-body">
						<p class="coderockz-woo-delivery-processing-days-notice"></p>
						
	                    <form action="" method="post" id ="coderockz_processing_days_settings_form_submit">
	                        <?php wp_nonce_field('coderockz_woo_delivery_nonce'); ?>
	                        <div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label" style="width:398px!important"><?php _e('Processing Days calculate including with Off days', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="If any offday is in processing days, they are not counting as processing days by default. By enabling this option, offdays are counting as procesing days."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_delivery_date_processing_days_off_days">
							       <input type="checkbox" name="coderockz_delivery_date_processing_days_off_days" id="coderockz_delivery_date_processing_days_off_days" <?php echo (isset($processing_days_settings['processing_days_consider_off_days']) && !empty($processing_days_settings['processing_days_consider_off_days'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label" style="width:398px!important"><?php _e('Processing Days calculate including with weekends', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="If any weekend is in processing days, they are not counting as processing days by default. By enabling this option, weekends are counting as procesing days."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_delivery_date_processing_days_weekend_days">
							       <input type="checkbox" name="coderockz_delivery_date_processing_days_weekend_days" id="coderockz_delivery_date_processing_days_weekend_days" <?php echo (isset($processing_days_settings['processing_days_consider_weekends']) && !empty($processing_days_settings['processing_days_consider_weekends'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label" style="width:398px!important"><?php _e('Processing Days calculate including with Current Day', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="If you still want to consider current day as processing day then enable it. Default is disable."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_delivery_date_processing_days_current_day">
							       <input type="checkbox" name="coderockz_delivery_date_processing_days_current_day" id="coderockz_delivery_date_processing_days_current_day" <?php echo (isset($processing_days_settings['processing_days_consider_current_day']) && !empty($processing_days_settings['processing_days_consider_current_day'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>
	                        <input class="coderockz-woo-delivery-submit-btn" type="submit" name="coderockz_processing_days_settings_form_submit" value="<?php _e('Save Changes', 'coderockz-woo-delivery'); ?>" />
	                    </form>
                	</div>

                </div>
                <div class="coderockz-woo-delivery-card">
					<p class="coderockz-woo-delivery-card-header"><?php _e('Category Wise Processing Days', 'coderockz-woo-delivery'); ?></p>
					<div class="coderockz-woo-delivery-card-body">
						<p class="coderockz-woo-delivery-category-processing-days-notice"></p>
						
	                    <form action="" method="post" id ="coderockz_category_processing_days_form_submit">
	                        <?php wp_nonce_field('coderockz_woo_delivery_nonce'); ?>
	                        <div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label" style="width:295px!important"><?php _e('Enable Category Wise Processing Days', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="Enable category wise processing days. Default is disabled."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_woo_delivery_category_wise_processing_days">
							       <input type="checkbox" name="coderockz_woo_delivery_category_wise_processing_days" id="coderockz_woo_delivery_category_wise_processing_days" <?php echo (isset($processing_days_settings['enable_category_wise_processing_days']) && !empty($processing_days_settings['enable_category_wise_processing_days'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>
	                        <div class="coderockz-woo-delivery-processing-days">

	                        <?php
	                        $category_processing_days_html = "";
							if(isset($processing_days_settings['category_processing_days']) && !empty($processing_days_settings['category_processing_days'])) {
								foreach($processing_days_settings['category_processing_days'] as $category => $days) {
									$category_processing_days_html .= '<div class="coderockz-woo-delivery-form-group">';
							        $category_processing_days_html .= '<img class="coderockz-arrow" src="'.CODEROCKZ_WOO_DELIVERY_URL.'/admin/images/arrow.png" alt="" style="width: 20px;vertical-align: top;margin-top: 12px;margin-right: 15px;">';
									$category_processing_days_html .= '<select class="coderockz-woo-delivery-select-field" name="coderockz_delivery_processing_days_categories[]">
								    <option value="">'.__('Select Category', 'coderockz-woo-delivery').'</option>';
									foreach ($all_categories as $cat) {
										$selected = (htmlspecialchars_decode($cat->name) == stripslashes($category)) ? "selected" : "";
										$category_processing_days_html .= '<option value="'.str_replace(" ","--",$cat->name).'"'.$selected.'>'.$cat->name.'</option>';
									}
									$category_processing_days_html .= '</select>';
									$category_processing_days_html .= '<input type="number" class="coderockz-woo-delivery-number-field" onkeyup="if(!Number.isInteger(Number(this.value))) this.value = null;" name="coderockz-woo-delivery-processing-days-'.str_replace(" ","--",$category).'" value="'.$days.'" style="vertical-align:top;width: 150px!important;" autocomplete="off" placeholder=""/><span class="coderockz-woo-delivery-processing-days-placeholder">Days</span>';
									if(array_keys($processing_days_settings['category_processing_days'])[0] != $category){
										$category_processing_days_html .= '<button class="coderockz-woo-delivery-processing-days-remove"><span class="dashicons dashicons-trash"></span></button>';
									}
									
							        $category_processing_days_html .= '</div>';
								}
								echo $category_processing_days_html;
							} else {

	                        ?>
		                        <div class="coderockz-woo-delivery-form-group">
		                        	<img class="coderockz-arrow" src="<?php echo CODEROCKZ_WOO_DELIVERY_URL ?>/admin/images/arrow.png" alt="" style="width: 20px;vertical-align: top;margin-top: 12px;margin-right: 15px;">
								    <select class="coderockz-woo-delivery-select-field" name="coderockz_delivery_processing_days_categories[]">
								    <option value=""><?php _e('Select Category', 'coderockz-woo-delivery'); ?></option>
									<?php
									foreach ($all_categories as $cat) {
										echo '<option value="'.str_replace(" ","--",$cat->name).'">'.$cat->name.'</option>';
									}
									?>
									</select>
								    <input type="number" class="coderockz-woo-delivery-number-field" value="" onkeyup="if(!Number.isInteger(Number(this.value))) this.value = null;" style="vertical-align:top;width: 100px!important;" autocomplete="off" placeholder="" disabled="disabled"/><span class="coderockz-woo-delivery-processing-days-placeholder">Days</span>
		                    	</div>
		                    <?php } ?>
	                    	</div>
	                    	<button class="coderockz-woo-delivery-add-processing-category-btn"><span class="dashicons dashicons-plus"></span></button>
	                        <input class="coderockz-woo-delivery-submit-btn" type="submit" name="coderockz_category_processing_days_form_submit" value="<?php _e('Save Changes', 'coderockz-woo-delivery'); ?>" />
	                    </form>
                	</div>

                </div>

                <div class="coderockz-woo-delivery-card">
					<p class="coderockz-woo-delivery-card-header"><?php _e('Product Wise Processing Days', 'coderockz-woo-delivery'); ?></p>
					<div class="coderockz-woo-delivery-card-body">
						<?php if(get_option('coderockz_woo_delivery_large_product_list') !== false) { ?>
							<p class="coderockz-woo-delivery-timezone-tab-warning"><span class="dashicons dashicons-megaphone"></span><?php _e('One ID per line. If product is simple input the product ID and if product is variable input the variation ID', 'coderockz-woo-delivery'); ?></p>
						<?php } ?>
						<p class="coderockz-woo-delivery-product-processing-days-notice"></p>
						
	                    <form action="" method="post" id ="coderockz_product_processing_days_form_submit">
	                        <?php wp_nonce_field('coderockz_woo_delivery_nonce'); ?>
	                        <div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label"><?php _e('Enable Product Wise Processing Days', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="Enable product wise processing days. Default is disabled."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_woo_delivery_product_wise_processing_days">
							       <input type="checkbox" name="coderockz_woo_delivery_product_wise_processing_days" id="coderockz_woo_delivery_product_wise_processing_days" <?php echo (isset($processing_days_settings['enable_product_wise_processing_days']) && !empty($processing_days_settings['enable_product_wise_processing_days'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label"><?php _e('Multiply Processing Days By Quantity', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="If you need to calculate processing day based on product quantity then enable it. For example, if you need 1 day of a burger processing day and if you need 2 days for 2 burgers processing day then enable it. Default is disabled."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_woo_delivery_product_processing_day_quantity">
							       <input type="checkbox" name="coderockz_woo_delivery_product_processing_day_quantity" id="coderockz_woo_delivery_product_processing_day_quantity" <?php echo (isset($processing_days_settings['enable_product_processing_day_quantity']) && !empty($processing_days_settings['enable_product_processing_day_quantity'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>
	                    	<?php if(get_option('coderockz_woo_delivery_large_product_list') == false) { ?>
	                        <div class="coderockz-woo-delivery-product-processing-days">

	                        <?php
	                        $product_processing_days_html = "";
							if(isset($processing_days_settings['product_processing_days']) && !empty($processing_days_settings['product_processing_days'])) {
								foreach($processing_days_settings['product_processing_days'] as $product => $days) {
									$product_processing_days_html .= '<div class="coderockz-woo-delivery-form-group">';
							        $product_processing_days_html .= '<img class="coderockz-arrow" src="'.CODEROCKZ_WOO_DELIVERY_URL.'/admin/images/arrow.png" alt="" style="width: 20px;vertical-align: top;margin-top: 12px;margin-right: 15px;">';
									$product_processing_days_html .= '<select class="coderockz-woo-delivery-select-field" name="coderockz_delivery_processing_days_products[]">
								    <option value="">'.__('Select Product', 'coderockz-woo-delivery').'</option>';
									foreach ($store_products as $key=>$value) {
										$selected = ($key == $product) ? "selected" : "";
										$product_processing_days_html .= '<option value="'.$key.'"'.$selected.'>'.$value.'</option>';
									}
									$product_processing_days_html .= '</select>';
									$product_processing_days_html .= '<input type="number" class="coderockz-woo-delivery-number-field" onkeyup="if(!Number.isInteger(Number(this.value))) this.value = null;" name="coderockz-woo-delivery-product-processing-days-'.$product.'" value="'.$days.'" style="vertical-align:top;width: 150px!important;" autocomplete="off" placeholder=""/><span class="coderockz-woo-delivery-processing-days-placeholder">Days</span>';
									if(array_keys($processing_days_settings['product_processing_days'])[0] != $product){
										$product_processing_days_html .= '<button class="coderockz-woo-delivery-product-processing-days-remove"><span class="dashicons dashicons-trash"></span></button>';
									}
									
							        $product_processing_days_html .= '</div>';
								}
								echo $product_processing_days_html;
							} else {

	                        ?>
		                        <div class="coderockz-woo-delivery-form-group">
		                        	<img class="coderockz-arrow" src="<?php echo CODEROCKZ_WOO_DELIVERY_URL ?>/admin/images/arrow.png" alt="" style="width: 20px;vertical-align: top;margin-top: 12px;margin-right: 15px;">
								    <select class="coderockz-woo-delivery-select-field" name="coderockz_delivery_processing_days_products[]">
								    <option value=""><?php _e('Select Product', 'coderockz-woo-delivery'); ?></option>
									<?php
									foreach ($store_products as $key=>$value) {
										echo '<option value="'.$key.'">'.$value.'</option>';
									}
									?>
									</select>
								    <input type="number" class="coderockz-woo-delivery-number-field" value="" onkeyup="if(!Number.isInteger(Number(this.value))) this.value = null;" style="vertical-align:top;width: 100px!important;" autocomplete="off" placeholder="" disabled="disabled"/><span class="coderockz-woo-delivery-processing-days-placeholder">Days</span>
		                    	</div>
		                    <?php } ?>
	                    	</div>
	                    	<?php } else { ?>

	                    	<div class="coderockz-woo-delivery-product-processing-days">

	                        <?php
	                        $product_processing_days_html = "";
							if(isset($processing_days_settings['product_processing_days']) && !empty($processing_days_settings['product_processing_days'])) {
								foreach($processing_days_settings['product_processing_days'] as $product => $days) {
									$product_processing_days_html .= '<div class="coderockz-woo-delivery-form-group">';
							        $product_processing_days_html .= '<img class="coderockz-arrow" src="'.CODEROCKZ_WOO_DELIVERY_URL.'/admin/images/arrow.png" alt="" style="width: 20px;vertical-align: top;margin-top: 12px;margin-right: 15px;">';
									$product_processing_days_html .= '<input name="coderockz_delivery_processing_days_products_input" type="text" class="coderockz-woo-delivery-input-field" value="'.$product.'" placeholder="Product/Variation ID" autocomplete="off"/>';
									$product_processing_days_html .= '<input type="number" class="coderockz-woo-delivery-number-field" onkeyup="if(!Number.isInteger(Number(this.value))) this.value = null;" name="coderockz-woo-delivery-product-processing-days-'.$product.'" value="'.$days.'" style="vertical-align:top;width: 150px!important;" autocomplete="off" placeholder=""/><span class="coderockz-woo-delivery-processing-days-placeholder">Days</span>';
									if(array_keys($processing_days_settings['product_processing_days'])[0] != $product){
										$product_processing_days_html .= '<button class="coderockz-woo-delivery-product-processing-days-remove"><span class="dashicons dashicons-trash"></span></button>';
									}
									
							        $product_processing_days_html .= '</div>';
								}
								echo $product_processing_days_html;
							} else {

	                        ?>
		                        <div class="coderockz-woo-delivery-form-group">
		                        	<img class="coderockz-arrow" src="<?php echo CODEROCKZ_WOO_DELIVERY_URL ?>/admin/images/arrow.png" alt="" style="width: 20px;vertical-align: top;margin-top: 12px;margin-right: 15px;">
									<input name="coderockz_delivery_processing_days_products_input" type="text" class="coderockz-woo-delivery-input-field" placeholder="Product/Variation ID" autocomplete="off"/>
								    <input type="number" class="coderockz-woo-delivery-number-field" value="" onkeyup="if(!Number.isInteger(Number(this.value))) this.value = null;" style="vertical-align:top;width: 100px!important;" autocomplete="off" placeholder="" disabled="disabled"/><span class="coderockz-woo-delivery-processing-days-placeholder">Days</span>
		                    	</div>
		                    <?php } ?>
	                    	</div>
	                    	<?php } ?>
	                    	<button class="coderockz-woo-delivery-add-processing-product-btn"><span class="dashicons dashicons-plus"></span></button>
	                        <input class="coderockz-woo-delivery-submit-btn" type="submit" name="coderockz_product_processing_days_form_submit" value="<?php _e('Save Changes', 'coderockz-woo-delivery'); ?>" />
	                    </form>
                	</div>

                </div>
			</div>

			<div data-tab="tab16" class="coderockz-woo-delivery-tabcontent">
				<div class="coderockz-woo-delivery-card">
					<p class="coderockz-woo-delivery-card-header"><?php _e('Processing Time Settings', 'coderockz-woo-delivery'); ?></p>
					<div class="coderockz-woo-delivery-card-body">
						<p class="coderockz-woo-delivery-processing-time-notice"></p>
						<?php if(get_option('coderockz_woo_delivery_large_product_list') !== false) { ?>
							<p class="coderockz-woo-delivery-timezone-tab-warning"><span class="dashicons dashicons-megaphone"></span><?php _e('In Exclude Individual Product option, if product is simple input the product ID and if product is variable input the variation ID', 'coderockz-woo-delivery'); ?></p>
						<?php } ?>
	                    <form action="" method="post" id ="coderockz_processing_time_settings_form_submit">
	                        <?php wp_nonce_field('coderockz_woo_delivery_nonce');?>
	                        <div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label" style="width:44%!important;text-align:right!important"><?php _e('Disable timeslot range in which the sum of current time & processing time exist', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="If you want to disable the timeslot that has the sum of current time & processing time, Disable it. This feature is available when the timeslot is a time range. Default is Enable."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_woo_delivery_processing_time_disable_timeslot_with_processing_time">
							       <input type="checkbox" name="coderockz_woo_delivery_processing_time_disable_timeslot_with_processing_time" id="coderockz_woo_delivery_processing_time_disable_timeslot_with_processing_time" <?php echo (isset($processing_time_settings['disable_timeslot_with_processing_time']) && !empty($processing_time_settings['disable_timeslot_with_processing_time'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>
	                        <?php
	                    		$duration = ""; 
	                    		$identity = "min";
                    			if(isset($processing_time_settings['overall_processing_time']) && !empty($processing_time_settings['overall_processing_time'])) {
                    				$overall_processing_time = (int)$processing_time_settings['overall_processing_time'];
                    				if($overall_processing_time <= 59) {
                    					$duration = $overall_processing_time;
                    				} else {
                    					$overall_processing_time = $overall_processing_time/60;
                    					$helper = new Coderockz_Woo_Delivery_Helper();
                    					if($helper->containsDecimal($overall_processing_time)){
                    						$duration = $overall_processing_time*60;
                    						$identity = "min";
                    					} else {
                    						$duration = $overall_processing_time;
                    						$identity = "hour";
                    					}
                    				}
                    			}
	                    	?>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_overall_processing_time_duration"><?php _e('Overall Processing Time for All Products', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="add the time with current time that is specified here. Only numerical value is accepted. If you save overall processing time, then category wise and product wise processing time is not working. Leave blank for no processing time."><span class="dashicons dashicons-editor-help"></span></p>
	                    		<div id="coderockz_woo_delivery_overall_processing_time_duration" class="coderockz_woo_delivery_overall_processing_time_duration">
	                        	<input name="coderockz_woo_delivery_overall_processing_time" type="number" min="1" onkeyup="if(!Number.isInteger(Number(this.value)) || this.value < 1) this.value = null;" class="coderockz-woo-delivery-number-field" value="<?php echo $duration; ?>" placeholder="" autocomplete="off"/>
	                        	<select class="coderockz-woo-delivery-select-field" name="coderockz_woo_delivery_overall_processing_time_format">
									<option value="min" <?php selected($identity,"min",true); ?>>Minutes</option>
									<option value="hour" <?php selected($identity,"hour",true); ?>>Hour</option>
								</select>
	                        	</div>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Exclude Categories from Processing Time', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Select the product categories for which you don't want the processing time."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<select id="coderockz_woo_delivery_exclude_categories_processing_time" name="coderockz_woo_delivery_exclude_categories_processing_time[]" class="coderockz_woo_delivery_exclude_categories_processing_time" multiple>
                                
                                <?php
                                $exclude_categories_processing_time = [];
								if(isset($processing_time_settings['exclude_categories']) && !empty($processing_time_settings['exclude_categories'])) {
									foreach ($processing_time_settings['exclude_categories'] as $hide_cat) {
										$exclude_categories_processing_time[] = stripslashes($hide_cat);
									}
								}
                                foreach ($all_categories as $cat) {

                                	$selected = isset($processing_time_settings['exclude_categories']) && !empty($processing_time_settings['exclude_categories']) && in_array(htmlspecialchars_decode($cat->name),$exclude_categories_processing_time) ? "selected" : "";
                                    echo '<option value="'.$cat->name.'" '.$selected.'>'.$cat->name.'</option>';
                                }
                                ?>
                                </select>
	                    	</div>
	                    	<?php if(get_option('coderockz_woo_delivery_large_product_list') == false) { ?>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Exclude Product from Processing Time', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Select the product for which you don't want the processing time."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<select id="coderockz_woo_delivery_exclude_product_processing_time" name="coderockz_woo_delivery_exclude_product_processing_time[]" class="coderockz_woo_delivery_exclude_product_processing_time" multiple>
                                
                                <?php
                                
                                foreach ($store_products as $key=>$value) {

                                	$selected = isset($processing_time_settings['exclude_products']) && !empty($processing_time_settings['exclude_products']) && in_array($key,$processing_time_settings['exclude_products']) ? "selected" : "";
                                    echo '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
                                }
                                ?>
                                </select>
	                    	</div>
	                    	<?php } else { ?>

                    		<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Exclude Product from Processing Time', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Select the product for which you don't want the processing time."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<?php 
	                        	$exclude_products_input_processing_time = isset($processing_time_settings['exclude_products']) && !empty($processing_time_settings['exclude_products']) ? $processing_time_settings['exclude_products'] : array();
	                        	$exclude_products_input_processing_time = implode(",",$exclude_products_input_processing_time);
	                        	?>
	                    		<input id="coderockz_woo_delivery_exclude_product_input_processing_time" name="coderockz_woo_delivery_exclude_product_input_processing_time" type="text" class="coderockz_woo_delivery_exclude_product_input_processing_time coderockz-woo-delivery-input-field" value="<?php echo $exclude_products_input_processing_time; ?>" placeholder="<?php _e('Comma(,) separated Product/Variation ID', 'coderockz-woo-delivery'); ?>" autocomplete="off"/>
	                    	</div>

	                    	<?php } ?>
	                        <input class="coderockz-woo-delivery-submit-btn" type="submit" name="coderockz_processing_time_settings_form_submit" value="<?php _e('Save Changes', 'coderockz-woo-delivery'); ?>" />
	                    </form>
                	</div>

                </div>

                <div class="coderockz-woo-delivery-card">
					<p class="coderockz-woo-delivery-card-header"><?php _e('Category Wise Processing Time', 'coderockz-woo-delivery'); ?></p>
					<div class="coderockz-woo-delivery-card-body">
						<p class="coderockz-woo-delivery-category-processing-time-notice"></p>
						
	                    <form action="" method="post" id ="coderockz_category_processing_time_form_submit">
	                        <?php wp_nonce_field('coderockz_woo_delivery_nonce'); ?>
	                        <div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label" style="width:295px!important"><?php _e('Enable Category Wise Processing time', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="Enable category wise processing time. Default is disabled."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_woo_delivery_category_wise_processing_time">
							       <input type="checkbox" name="coderockz_woo_delivery_category_wise_processing_time" id="coderockz_woo_delivery_category_wise_processing_time" <?php echo (isset($processing_time_settings['enable_category_wise_processing_time']) && !empty($processing_time_settings['enable_category_wise_processing_time'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>
	                        <div class="coderockz-woo-delivery-processing-time">

	                        <?php
	                        $category_processing_time_html = "";
							if(isset($processing_time_settings['category_processing_time']) && !empty($processing_time_settings['category_processing_time'])) {
								foreach($processing_time_settings['category_processing_time'] as $category => $time) {


									$duration = ""; 
		                    		$identity = "min";
		                			if(isset($time) && !empty($time)) {
		                				$time = (int)$time;
		                				if($time <= 59) {
		                					$duration = $time;
		                				} else {
		                					$time = $time/60;
		                					$helper = new Coderockz_Woo_Delivery_Helper();
		                					if($helper->containsDecimal($time)){
		                						$duration = $time*60;
		                						$identity = "min";
		                					} else {
		                						$duration = $time;
		                						$identity = "hour";
		                					}
		                				}
		                			}

									$category_processing_time_html .= '<div class="coderockz-woo-delivery-form-group">';
							        $category_processing_time_html .= '<img class="coderockz-arrow" src="'.CODEROCKZ_WOO_DELIVERY_URL.'/admin/images/arrow.png" alt="" style="width: 20px;vertical-align: top;margin-top: 12px;margin-right: 15px;">';
									$category_processing_time_html .= '<select class="coderockz-woo-delivery-select-field" name="coderockz_delivery_processing_time_categories[]">
								    <option value="">'.__('Select Category', 'coderockz-woo-delivery').'</option>';
									foreach ($all_categories as $cat) {
										$selected = (htmlspecialchars_decode($cat->name) == stripslashes($category)) ? "selected" : "";
										$category_processing_time_html .= '<option value="'.str_replace(" ","--",$cat->name).'"'.$selected.'>'.$cat->name.'</option>';
									}
									$category_processing_time_html .= '</select>';
									$selected = ($identity == "min") ? "selected" : "";
									$selected2 = ($identity == "hour") ? "selected" : "";
									$category_processing_time_html .= '<div id="coderockz_woo_delivery_category_processing_time_duration" class="coderockz_woo_delivery_category_processing_time_duration">
			                        	<input name="coderockz_woo_delivery_category_processing_time-'.str_replace(" ","--",$category).'" type="number" min="1" onkeyup="if(!Number.isInteger(Number(this.value)) || this.value < 1) this.value = null;" class="coderockz-woo-delivery-number-field" value="'. $duration.'" placeholder="" autocomplete="off"/>
			                        	<select class="coderockz-woo-delivery-select-field" name="coderockz_woo_delivery_category_processing_time_format-'.str_replace(" ","--",$category).'">
											<option value="min" '.$selected.'>Minutes</option>
											<option value="hour" '.$selected2.'>Hour</option>
										</select>
		                        	</div>';
		                        	if(array_keys($processing_time_settings['category_processing_time'])[0] != $category){
										$category_processing_time_html .= '<button class="coderockz-woo-delivery-category-processing-time-remove"><span class="dashicons dashicons-trash"></span></button>';
									}



									
							        $category_processing_time_html .= '</div>';
								}
								echo $category_processing_time_html;
							} else {

	                        ?>
		                        <div class="coderockz-woo-delivery-form-group">
		                        	<img class="coderockz-arrow" src="<?php echo CODEROCKZ_WOO_DELIVERY_URL ?>/admin/images/arrow.png" alt="" style="width: 20px;vertical-align: top;margin-top: 12px;margin-right: 15px;">
								    <select class="coderockz-woo-delivery-select-field" name="coderockz_delivery_processing_time_categories[]">
								    <option value=""><?php _e('Select Category', 'coderockz-woo-delivery'); ?></option>
									<?php
									foreach ($all_categories as $cat) {
										echo '<option value="'.str_replace(" ","--",$cat->name).'">'.$cat->name.'</option>';
									}
									?>
									</select>
								    <div id="coderockz_woo_delivery_category_processing_time_duration" class="coderockz_woo_delivery_category_processing_time_duration">
			                        	<input name="coderockz_woo_delivery_category_processing_time" type="number" min="1" onkeyup="if(!Number.isInteger(Number(this.value)) || this.value < 1) this.value = null;" class="coderockz-woo-delivery-number-field" value="" placeholder="" autocomplete="off" disabled="disabled"/>
			                        	<select class="coderockz-woo-delivery-select-field" name="coderockz_woo_delivery_category_processing_time_format" disabled="disabled">
											<option value="min">Minutes</option>
											<option value="hour">Hour</option>
										</select>
		                        	</div>

		                    	</div>
		                    <?php } ?>
	                    	</div>
	                    	<button class="coderockz-woo-delivery-add-processing-time-category-btn"><span class="dashicons dashicons-plus"></span></button>
	                        <input class="coderockz-woo-delivery-submit-btn" type="submit" name="coderockz_category_processing_time_form_submit" value="<?php _e('Save Changes', 'coderockz-woo-delivery'); ?>" />
	                    </form>
                	</div>

                </div>

                <div class="coderockz-woo-delivery-card">
					<p class="coderockz-woo-delivery-card-header"><?php _e('Product Wise Processing Time', 'coderockz-woo-delivery'); ?></p>
					<div class="coderockz-woo-delivery-card-body">
						<?php if(get_option('coderockz_woo_delivery_large_product_list') !== false) { ?>
							<p class="coderockz-woo-delivery-timezone-tab-warning"><span class="dashicons dashicons-megaphone"></span><?php _e('One ID per line. If product is simple input the product ID and if product is variable input the variation ID', 'coderockz-woo-delivery'); ?></p>
						<?php } ?>
						<p class="coderockz-woo-delivery-product-processing-time-notice"></p>
						
	                    <form action="" method="post" id ="coderockz_product_processing_time_form_submit">
	                        <?php wp_nonce_field('coderockz_woo_delivery_nonce'); ?>
	                        <div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label" style="width:295px!important"><?php _e('Enable Product Wise Processing time', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="Enable product wise processing time. Default is disabled."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_woo_delivery_product_wise_processing_time">
							       <input type="checkbox" name="coderockz_woo_delivery_product_wise_processing_time" id="coderockz_woo_delivery_product_wise_processing_time" <?php echo (isset($processing_time_settings['enable_product_wise_processing_time']) && !empty($processing_time_settings['enable_product_wise_processing_time'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label" style="width:295px!important"><?php _e('Multiply Processing Time By Quantity', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="If you need to calculate processing time based on product quantity then enable it. For example, if you need 10 min of a burger processing time and if you need 20 mintues for 2 burgers processing time then enable it. Default is disabled."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_woo_delivery_product_processing_time_quantity">
							       <input type="checkbox" name="coderockz_woo_delivery_product_processing_time_quantity" id="coderockz_woo_delivery_product_processing_time_quantity" <?php echo (isset($processing_time_settings['enable_product_processing_time_quantity']) && !empty($processing_time_settings['enable_product_processing_time_quantity'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>
	                    	<?php if(get_option('coderockz_woo_delivery_large_product_list') == false) { ?>
	                        <div class="coderockz-woo-delivery-product-processing-time">

	                        <?php
	                        $product_processing_time_html = "";
							if(isset($processing_time_settings['product_processing_time']) && !empty($processing_time_settings['product_processing_time'])) {
								foreach($processing_time_settings['product_processing_time'] as $product => $time) {


									$duration = ""; 
		                    		$identity = "min";
		                			if(isset($time) && !empty($time)) {
		                				$time = (int)$time;
		                				if($time <= 59) {
		                					$duration = $time;
		                				} else {
		                					$time = $time/60;
		                					$helper = new Coderockz_Woo_Delivery_Helper();
		                					if($helper->containsDecimal($time)){
		                						$duration = $time*60;
		                						$identity = "min";
		                					} else {
		                						$duration = $time;
		                						$identity = "hour";
		                					}
		                				}
		                			}

									$product_processing_time_html .= '<div class="coderockz-woo-delivery-form-group">';
							        $product_processing_time_html .= '<img class="coderockz-arrow" src="'.CODEROCKZ_WOO_DELIVERY_URL.'/admin/images/arrow.png" alt="" style="width: 20px;vertical-align: top;margin-top: 12px;margin-right: 15px;">';
									$product_processing_time_html .= '<select class="coderockz-woo-delivery-select-field" name="coderockz_delivery_processing_time_products[]">
								    <option value="">'.__('Select Product', 'coderockz-woo-delivery').'</option>';
									foreach ($store_products as $key=>$value) {
										$selected = ($key == $product) ? "selected" : "";
										$product_processing_time_html .= '<option value="'.$key.'"'.$selected.'>'.$value.'</option>';
									}
									$product_processing_time_html .= '</select>';
									$selected = ($identity == "min") ? "selected" : "";
									$selected2 = ($identity == "hour") ? "selected" : "";
									$product_processing_time_html .= '<div id="coderockz_woo_delivery_product_processing_time_duration" class="coderockz_woo_delivery_product_processing_time_duration">
			                        	<input name="coderockz_woo_delivery_product_processing_time-'.$product.'" type="number" min="1" onkeyup="if(!Number.isInteger(Number(this.value)) || this.value < 1) this.value = null;" class="coderockz-woo-delivery-number-field" value="'. $duration.'" placeholder="" autocomplete="off"/>
			                        	<select class="coderockz-woo-delivery-select-field" name="coderockz_woo_delivery_product_processing_time_format-'.$product.'">
											<option value="min" '.$selected.'>Minutes</option>
											<option value="hour" '.$selected2.'>Hour</option>
										</select>
		                        	</div>';
		                        	if(array_keys($processing_time_settings['product_processing_time'])[0] != $product){
										$product_processing_time_html .= '<button class="coderockz-woo-delivery-product-processing-time-remove"><span class="dashicons dashicons-trash"></span></button>';
									}



									
							        $product_processing_time_html .= '</div>';
								}
								echo $product_processing_time_html;
							} else {

	                        ?>
		                        <div class="coderockz-woo-delivery-form-group">
		                        	<img class="coderockz-arrow" src="<?php echo CODEROCKZ_WOO_DELIVERY_URL ?>/admin/images/arrow.png" alt="" style="width: 20px;vertical-align: top;margin-top: 12px;margin-right: 15px;">
								    <select class="coderockz-woo-delivery-select-field" name="coderockz_delivery_processing_time_products[]">
								    <option value=""><?php _e('Select Product', 'coderockz-woo-delivery'); ?></option>
									
									<?php
									foreach ($store_products as $key=>$value) {
										echo '<option value="'.$key.'">'.$value.'</option>';
									}
									?>
									</select>
								    <div id="coderockz_woo_delivery_product_processing_time_duration" class="coderockz_woo_delivery_product_processing_time_duration">
			                        	<input name="coderockz_woo_delivery_product_processing_time" type="number" min="1" onkeyup="if(!Number.isInteger(Number(this.value)) || this.value < 1) this.value = null;" class="coderockz-woo-delivery-number-field" value="" placeholder="" autocomplete="off" disabled="disabled"/>
			                        	<select class="coderockz-woo-delivery-select-field" name="coderockz_woo_delivery_product_processing_time_format" disabled="disabled">
											<option value="min">Minutes</option>
											<option value="hour">Hour</option>
										</select>
		                        	</div>

		                    	</div>
		                    <?php } ?>
	                    	</div>

	                    	<?php } else { ?>

	                    	<div class="coderockz-woo-delivery-product-processing-time">

	                        <?php
	                        $product_processing_time_html = "";
							if(isset($processing_time_settings['product_processing_time']) && !empty($processing_time_settings['product_processing_time'])) {
								foreach($processing_time_settings['product_processing_time'] as $product => $time) {


									$duration = ""; 
		                    		$identity = "min";
		                			if(isset($time) && !empty($time)) {
		                				$time = (int)$time;
		                				if($time <= 59) {
		                					$duration = $time;
		                				} else {
		                					$time = $time/60;
		                					$helper = new Coderockz_Woo_Delivery_Helper();
		                					if($helper->containsDecimal($time)){
		                						$duration = $time*60;
		                						$identity = "min";
		                					} else {
		                						$duration = $time;
		                						$identity = "hour";
		                					}
		                				}
		                			}

									$product_processing_time_html .= '<div class="coderockz-woo-delivery-form-group">';
							        $product_processing_time_html .= '<img class="coderockz-arrow" src="'.CODEROCKZ_WOO_DELIVERY_URL.'/admin/images/arrow.png" alt="" style="width: 20px;vertical-align: top;margin-top: 12px;margin-right: 15px;">';
									$product_processing_time_html .= '<input name="coderockz_delivery_processing_time_products_input" type="text" class="coderockz-woo-delivery-input-field" value="'.$product.'" placeholder="Product/Variation ID" autocomplete="off"/>';
									$selected = ($identity == "min") ? "selected" : "";
									$selected2 = ($identity == "hour") ? "selected" : "";
									$product_processing_time_html .= '<div id="coderockz_woo_delivery_product_processing_time_duration" class="coderockz_woo_delivery_product_processing_time_duration">
			                        	<input name="coderockz_woo_delivery_product_processing_time-'.$product.'" type="number" min="1" onkeyup="if(!Number.isInteger(Number(this.value)) || this.value < 1) this.value = null;" class="coderockz-woo-delivery-number-field" value="'. $duration.'" placeholder="" autocomplete="off"/>
			                        	<select class="coderockz-woo-delivery-select-field" name="coderockz_woo_delivery_product_processing_time_format-'.$product.'">
											<option value="min" '.$selected.'>Minutes</option>
											<option value="hour" '.$selected2.'>Hour</option>
										</select>
		                        	</div>';
		                        	if(array_keys($processing_time_settings['product_processing_time'])[0] != $product){
										$product_processing_time_html .= '<button class="coderockz-woo-delivery-product-processing-time-remove"><span class="dashicons dashicons-trash"></span></button>';
									}



									
							        $product_processing_time_html .= '</div>';
								}
								echo $product_processing_time_html;
							} else {

	                        ?>
		                        <div class="coderockz-woo-delivery-form-group">
		                        	<img class="coderockz-arrow" src="<?php echo CODEROCKZ_WOO_DELIVERY_URL ?>/admin/images/arrow.png" alt="" style="width: 20px;vertical-align: top;margin-top: 12px;margin-right: 15px;">
									<input name="coderockz_delivery_processing_time_products_input" type="text" class="coderockz-woo-delivery-input-field" placeholder="Product/Variation ID" autocomplete="off"/>
								    <div id="coderockz_woo_delivery_product_processing_time_duration" class="coderockz_woo_delivery_product_processing_time_duration">
			                        	<input name="coderockz_woo_delivery_product_processing_time" type="number" min="1" onkeyup="if(!Number.isInteger(Number(this.value)) || this.value < 1) this.value = null;" class="coderockz-woo-delivery-number-field" value="" placeholder="" autocomplete="off" disabled="disabled"/>
			                        	<select class="coderockz-woo-delivery-select-field" name="coderockz_woo_delivery_product_processing_time_format" disabled="disabled">
											<option value="min">Minutes</option>
											<option value="hour">Hour</option>
										</select>
		                        	</div>

		                    	</div>
		                    <?php } ?>
	                    	</div>

	                    	<?php } ?>
	                    	
	                    	<button class="coderockz-woo-delivery-add-processing-time-product-btn"><span class="dashicons dashicons-plus"></span></button>
	                        <input class="coderockz-woo-delivery-submit-btn" type="submit" name="coderockz_product_processing_time_form_submit" value="<?php _e('Save Changes', 'coderockz-woo-delivery'); ?>" />
	                    </form>
                	</div>

                </div>
               
			</div>

			<div data-tab="tab17" class="coderockz-woo-delivery-tabcontent">
				<div class="coderockz-woo-delivery-card">
					<p class="coderockz-woo-delivery-card-header"><?php _e('Delivery Time Slot Fee', 'coderockz-woo-delivery'); ?></p>
					<div class="coderockz-woo-delivery-card-body">
						<p class="coderockz-woo-delivery-time-slot-fee-warning"><span class="dashicons dashicons-megaphone"></span><?php _e(' Before This Settings, Please Set Time Slot Starts From & Time Slot Ends At From Delivery Time Tab and ', 'coderockz-woo-delivery'); ?><span class="coderockz-woo-delivery-refresh-btn"><?php _e('refresh the page', 'coderockz-woo-delivery'); ?></span></p>
						<p class="coderockz-woo-delivery-time-slot-fee-notice"><span class="dashicons dashicons-yes"></span><?php _e(' Settings Changed Successfully', 'coderockz-woo-delivery'); ?></p>
	                    <form action="" method="post" id ="coderockz_woo_delivery_time_slot_fee_form_submit">
	                        <?php wp_nonce_field('coderockz_woo_delivery_nonce'); ?>
	                        <div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label" style="width: 160px!important;text-align:unset!important"><?php _e('Enable Time Slot Fee', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="By enabling this option, any fee for a specific time slot can be added."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_delivery_date_enable_time_slot_fee">
							       <input type="checkbox" name="coderockz_delivery_date_enable_time_slot_fee" id="coderockz_delivery_date_enable_time_slot_fee" <?php echo (isset($delivery_fee_settings['enable_time_slot_fee']) && !empty($delivery_fee_settings['enable_time_slot_fee'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>
	                        <div class="coderockz-woo-delivery-time-slot-fees">
	                        <?php
	                        $delivery_time = get_option("coderockz_woo_delivery_time_settings");
							$time_format = (isset($delivery_time['time_format']) && !empty($delivery_time['time_format']))?$delivery_time['time_format']:"12";
							if($time_format == 12) {
								$time_format = "h:i A";
							} elseif ($time_format == 24) {
								$time_format = "H:i";
							}
	                        $time_slot_fee_html = "";
	                        
							if(isset($delivery_fee_settings['time_slot_fee']) && !empty($delivery_fee_settings['time_slot_fee'])) {
								$time_slot_array = explode("-",array_keys($delivery_fee_settings['time_slot_fee'])[0]);
								$temp_time_slot_duration = (int)$time_slot_array[1]-(int)$time_slot_array[0];
								if($temp_time_slot_duration != $delivery_time['each_time_slot']) {
									$delivery_time_slot_fee_settings['time_slot_fee'] = [];
									if(get_option('coderockz_woo_delivery_fee_settings') == false) {
										update_option('coderockz_woo_delivery_fee_settings', $delivery_time_slot_fee_settings);
									} else {
										$delivery_time_slot_fee_settings = array_merge(get_option('coderockz_woo_delivery_fee_settings'),$delivery_time_slot_fee_settings);
										update_option('coderockz_woo_delivery_fee_settings', $delivery_time_slot_fee_settings);
									}
								}
								if(count($delivery_fee_settings['time_slot_fee'])>0) {
								foreach($delivery_fee_settings['time_slot_fee'] as $time_slot => $fee) {
									$time_slot_fee_html .= '<div class="coderockz-woo-delivery-form-group">';
							        $time_slot_fee_html .= '<img class="coderockz-arrow" src="'.CODEROCKZ_WOO_DELIVERY_URL.'/admin/images/arrow.png" alt="" style="width: 20px;vertical-align: top;margin-top: 12px;margin-right: 15px;">';
									$time_slot_fee_html .= '<select class="coderockz-woo-delivery-select-field" name="coderockz_delivery_time_slot[]">
								    <option value="">'.__('Select Time Slot', 'coderockz-woo-delivery').'</option>';
									if(isset($delivery_time['delivery_time_starts']) && $delivery_time['delivery_time_starts'] != "" && isset($delivery_time['each_time_slot']) && $delivery_time['each_time_slot'] != ""){
										if(isset($delivery_time['delivery_time_ends']) && $delivery_time['delivery_time_ends'] != "") {
											if($delivery_time['delivery_time_starts'] == $delivery_time['delivery_time_ends']) {
												$delivery_time['delivery_time_ends'] = 1440+$delivery_time['delivery_time_ends'];
											}
											for($i = $delivery_time['delivery_time_starts']; $i<$delivery_time['delivery_time_ends']; $i = $i+(int)$delivery_time['each_time_slot']) {
												$option_start = date($time_format, mktime(0, $i));
												if($i+(int)$delivery_time['each_time_slot']>$delivery_time['delivery_time_ends']){
													$option_end = date($time_format, mktime(0, (int)$delivery_time['delivery_time_ends']));
													$option_value_start = $i;
													$option_value_end = $delivery_time['delivery_time_ends'];
																
												} else {
													$option_end = date($time_format, mktime(0, $i+(int)$delivery_time['each_time_slot']));
													$option_value_start = $i;
													$option_value_end = $i+(int)$delivery_time['each_time_slot'];
												}
												$selected = ($option_value_start."-".$option_value_end == $time_slot)? " selected" : "";
												$time_slot_fee_html .= '<option value="'.$option_value_start.'-'.$option_value_end.'"'.$selected.'>'.$option_start.' - '.$option_end.'</option>';
											}
										} else {
											for($i = $delivery_time['delivery_time_starts']; $i<1440; $i = $i+(int)$delivery_time['each_time_slot']) {
												$option_start = date($time_format, mktime(0, $i));
												$option_end = date($time_format, mktime(0, $i+(int)$delivery_time['each_time_slot']));
												$option_value_start = $i;
												$option_value_end = $i+(int)$delivery_time['each_time_slot'];
												$selected = ($option_value_start."-".$option_value_end == $time_slot)? " selected" : "";
												$time_slot_fee_html .= '<option value="'.$option_value_start.'-'.$option_value_end.'"'.$selected.'>'.$option_start.' - '.$option_end.'</option>';
											}
										}
										
									}
									$time_slot_fee_html .= '</select>';
									$time_slot_fee_html .= '<input type="text" class="coderockz-woo-delivery-input-field" onkeyup="if(isNaN(parseFloat(Number(this.value))) || isNaN(parseInt(Number(this.value), 10))) this.value = null;" name="coderockz-woo-delivery-time-slot-fee-'.$time_slot.'" value="'.$fee.'" style="vertical-align:top;width: 100px!important;" autocomplete="off" placeholder="Fee"/><span class="coderockz-woo-delivery-currency-code">'.$currency_code.'</span>';
									if(array_keys($delivery_fee_settings['time_slot_fee'])[0] != $time_slot){
										$time_slot_fee_html .= '<button class="coderockz-woo-delivery-time-slot-fee-remove"><span class="dashicons dashicons-trash"></span></button>';
									}
									
							        $time_slot_fee_html .= '</div>';
								}
								}
								echo $time_slot_fee_html;
							} else {

	                        ?>
		                        <div class="coderockz-woo-delivery-form-group">
		                        	<img class="coderockz-arrow" src="<?php echo CODEROCKZ_WOO_DELIVERY_URL ?>/admin/images/arrow.png" alt="" style="width: 20px;vertical-align: top;margin-top: 12px;margin-right: 15px;">
								    <select class="coderockz-woo-delivery-select-field" name="coderockz_delivery_time_slot[]">
								    <option value=""><?php _e('Select Time Slot', 'coderockz-woo-delivery'); ?></option>
									<?php
									if(isset($delivery_time['delivery_time_starts']) && $delivery_time['delivery_time_starts'] != "" && isset($delivery_time['each_time_slot']) && $delivery_time['each_time_slot'] !=""){
										if(isset($delivery_time['delivery_time_ends']) && $delivery_time['delivery_time_ends'] != "") {
											if($delivery_time['delivery_time_starts'] == $delivery_time['delivery_time_ends']) {
												$delivery_time['delivery_time_ends'] = 1440+$delivery_time['delivery_time_ends'];
											}
											for($i = $delivery_time['delivery_time_starts']; $i<$delivery_time['delivery_time_ends']; $i = $i+(int)$delivery_time['each_time_slot']) {

												$option_start = date($time_format, mktime(0, $i));
												if($i+(int)$delivery_time['each_time_slot']>$delivery_time['delivery_time_ends']){
													$option_end = date($time_format, mktime(0, (int)$delivery_time['delivery_time_ends']));
													$option_value_start = $i;
													$option_value_end = $delivery_time['delivery_time_ends'];
																
												} else {
													$option_end = date($time_format, mktime(0, $i+(int)$delivery_time['each_time_slot']));
													$option_value_start = $i;
													$option_value_end = $i+(int)$delivery_time['each_time_slot'];
												}
												
												
												echo '<option value="'.$option_value_start.'-'.$option_value_end.'">'.$option_start.' - '.$option_end.'</option>';
											
											}
						
										} else {
											for($i = $delivery_time['delivery_time_starts']; $i<1440; $i = $i+(int)$delivery_time['each_time_slot']) {
												$option_start = date($time_format, mktime(0, $i));
												$option_end = date($time_format, mktime(0, $i+(int)$delivery_time['each_time_slot']));
												$option_value_start = $i;
												$option_value_end = $i+(int)$delivery_time['each_time_slot'];
												echo '<option value="'.$option_value_start.'-'.$option_value_end.'">'.$option_start.' - '.$option_end.'</option>';
											}
										}
										
									} elseif((!isset($delivery_time['each_time_slot']) || $delivery_time['each_time_slot'] == "") && (isset($delivery_time['delivery_time_starts']) && $delivery_time['delivery_time_starts']!="") && (isset($delivery_time['delivery_time_ends']) && $delivery_time['delivery_time_ends']!="")) {
										$option_start = date($time_format, mktime(0, (int)$delivery_time['delivery_time_starts']));
										$option_end = date($time_format, mktime(0, (int)$delivery_time['delivery_time_ends']));
										$option_value_start = $delivery_time['delivery_time_starts'];
										$option_value_end = $delivery_time['delivery_time_ends'];
									 	echo '<option value="'.$option_value_start.'-'.$option_value_end.'">'.$option_start.' - '.$option_end.'</option>';
										
										
									}
									?>
									</select>
								    <input type="text" class="coderockz-woo-delivery-input-field" value="" onkeyup="if(isNaN(parseFloat(Number(this.value))) || isNaN(parseInt(Number(this.value), 10))) this.value = null;" style="vertical-align:top;width: 100px!important;" autocomplete="off" placeholder="Fee" disabled="disabled"/><span class="coderockz-woo-delivery-currency-code"><?php echo $currency_code; ?></span>
		                    	</div>
		                    <?php } ?>
	                    	</div>
	                    	<button class="coderockz-woo-delivery-time-slot-fee-btn"><span class="dashicons dashicons-plus"></span></button>
	                        <input class="coderockz-woo-delivery-submit-btn" type="submit" name="coderockz_woo_delivery_time_slot_fee_form_submit" value="<?php _e('Save Changes', 'coderockz-woo-delivery'); ?>" />
	                    </form>
                	</div>

                </div>

                <div class="coderockz-woo-delivery-card">
					<p class="coderockz-woo-delivery-card-header"><?php _e('Pickup Slot Fee', 'coderockz-woo-delivery'); ?></p>
					<div class="coderockz-woo-delivery-card-body">
						<p class="coderockz-woo-delivery-time-slot-fee-warning"><span class="dashicons dashicons-megaphone"></span><?php _e(' Before This Settings, Please Set Pickup Slot Starts From & Pickup Slot Ends At From Delivery Pickup Tab and ', 'coderockz-woo-delivery'); ?><span class="coderockz-woo-delivery-refresh-btn"><?php _e('refresh the page', 'coderockz-woo-delivery'); ?></span></p>
						<p class="coderockz-woo-delivery-pickup-slot-fee-notice"><span class="dashicons dashicons-yes"></span><?php _e(' Settings Changed Successfully', 'coderockz-woo-delivery'); ?></p>
	                    <form action="" method="post" id ="coderockz_woo_delivery_pickup_slot_fee_form_submit">
	                        <?php wp_nonce_field('coderockz_woo_delivery_nonce'); ?>
	                        <div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label" style="width: 175px!important;text-align:unset!important"><?php _e('Enable Pickup Slot Fee', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="By enabling this option, any fee for a specific pickup slot can be added."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_delivery_date_enable_pickup_slot_fee">
							       <input type="checkbox" name="coderockz_delivery_date_enable_pickup_slot_fee" id="coderockz_delivery_date_enable_pickup_slot_fee" <?php echo (isset($delivery_fee_settings['enable_pickup_slot_fee']) && !empty($delivery_fee_settings['enable_pickup_slot_fee'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>
	                        <div class="coderockz-woo-delivery-pickup-slot-fees">
	                        <?php
							$time_format = (isset($pickup_time_settings['time_format']) && !empty($pickup_time_settings['time_format']))?$pickup_time_settings['time_format']:"12";
							if($time_format == 12) {
								$time_format = "h:i A";
							} elseif ($time_format == 24) {
								$time_format = "H:i";
							}
	                        $pickup_slot_fee_html = "";
	                        
							if(isset($delivery_fee_settings['pickup_slot_fee']) && !empty($delivery_fee_settings['pickup_slot_fee'])) {
								$pickup_slot_array = explode("-",array_keys($delivery_fee_settings['pickup_slot_fee'])[0]);
								$temp_pickup_slot_duration = $pickup_slot_array[1]-$pickup_slot_array[0];
								if($temp_pickup_slot_duration != $pickup_time_settings['each_time_slot']) {
									$delivery_pickup_slot_fee_settings['pickup_slot_fee'] = [];
									if(get_option('coderockz_woo_delivery_fee_settings') == false) {
										update_option('coderockz_woo_delivery_fee_settings', $delivery_pickup_slot_fee_settings);
									} else {
										$delivery_pickup_slot_fee_settings = array_merge(get_option('coderockz_woo_delivery_fee_settings'),$delivery_pickup_slot_fee_settings);
										update_option('coderockz_woo_delivery_fee_settings', $delivery_pickup_slot_fee_settings);
									}
								}
								if(count($delivery_fee_settings['pickup_slot_fee'])>0) {
								foreach($delivery_fee_settings['pickup_slot_fee'] as $pickup_slot => $fee) {
									$pickup_slot_fee_html .= '<div class="coderockz-woo-delivery-form-group">';
							        $pickup_slot_fee_html .= '<img class="coderockz-arrow" src="'.CODEROCKZ_WOO_DELIVERY_URL.'/admin/images/arrow.png" alt="" style="width: 20px;vertical-align: top;margin-top: 12px;margin-right: 15px;">';
									$pickup_slot_fee_html .= '<select class="coderockz-woo-delivery-select-field" name="coderockz_delivery_pickup_slot[]">
								    <option value="">'.__('Select Pickup Slot', 'coderockz-woo-delivery').'</option>';
									if(isset($pickup_time_settings['pickup_time_starts']) && $pickup_time_settings['pickup_time_starts'] != "" && isset($pickup_time_settings['each_time_slot']) && $pickup_time_settings['each_time_slot'] != ""){
										if(isset($pickup_time_settings['pickup_time_ends']) && $pickup_time_settings['pickup_time_ends'] != "") {
											if($pickup_time_settings['pickup_time_starts'] == $pickup_time_settings['pickup_time_ends']) {
												$pickup_time_settings['pickup_time_ends'] = 1440+$pickup_time_settings['pickup_time_ends'];
											}
											for($i = $pickup_time_settings['pickup_time_starts']; $i<$pickup_time_settings['pickup_time_ends']; $i = $i+(int)$pickup_time_settings['each_time_slot']) {
												$option_start = date($time_format, mktime(0, $i));
												if($i+(int)$pickup_time_settings['each_time_slot']>$pickup_time_settings['pickup_time_ends']){
													$option_end = date($time_format, mktime(0, (int)$pickup_time_settings['pickup_time_ends']));
													$option_value_start = $i;
													$option_value_end = $pickup_time_settings['pickup_time_ends'];
																
												} else {
													$option_end = date($time_format, mktime(0, $i+(int)$pickup_time_settings['each_time_slot']));
													$option_value_start = $i;
													$option_value_end = $i+(int)$pickup_time_settings['each_time_slot'];
												}
												$selected = ($option_value_start."-".$option_value_end == $pickup_slot)? " selected" : "";
												$pickup_slot_fee_html .= '<option value="'.$option_value_start.'-'.$option_value_end.'"'.$selected.'>'.$option_start.' - '.$option_end.'</option>';
											}
										} else {
											for($i = $pickup_time_settings['pickup_time_starts']; $i<1440; $i = $i+(int)$pickup_time_settings['each_time_slot']) {
												$option_start = date($time_format, mktime(0, $i));
												$option_end = date($time_format, mktime(0, $i+(int)$pickup_time_settings['each_time_slot']));
												$option_value_start = $i;
												$option_value_end = $i+(int)$pickup_time_settings['each_time_slot'];
												$selected = ($option_value_start."-".$option_value_end == $pickup_slot)? " selected" : "";
												$pickup_slot_fee_html .= '<option value="'.$option_value_start.'-'.$option_value_end.'"'.$selected.'>'.$option_start.' - '.$option_end.'</option>';
											}
										}
										
									}
									$pickup_slot_fee_html .= '</select>';
									$pickup_slot_fee_html .= '<input type="text" class="coderockz-woo-delivery-input-field" onkeyup="if(isNaN(parseFloat(Number(this.value))) || isNaN(parseInt(Number(this.value), 10))) this.value = null;" name="coderockz-woo-delivery-pickup-slot-fee-'.$pickup_slot.'" value="'.$fee.'" style="vertical-align:top;width: 100px!important;" autocomplete="off" placeholder="Fee"/><span class="coderockz-woo-delivery-currency-code">'.$currency_code.'</span>';
									if(array_keys($delivery_fee_settings['pickup_slot_fee'])[0] != $pickup_slot){
										$pickup_slot_fee_html .= '<button class="coderockz-woo-delivery-pickup-slot-fee-remove"><span class="dashicons dashicons-trash"></span></button>';
									}
									
							        $pickup_slot_fee_html .= '</div>';
								}
								}
								echo $pickup_slot_fee_html;
							} else {

	                        ?>
		                        <div class="coderockz-woo-delivery-form-group">
		                        	<img class="coderockz-arrow" src="<?php echo CODEROCKZ_WOO_DELIVERY_URL ?>/admin/images/arrow.png" alt="" style="width: 20px;vertical-align: top;margin-top: 12px;margin-right: 15px;">
								    <select class="coderockz-woo-delivery-select-field" name="coderockz_delivery_pickup_slot[]">
								    <option value=""><?php _e('Select Pickup Slot', 'coderockz-woo-delivery'); ?></option>
									<?php
									if(isset($pickup_time_settings['pickup_time_starts']) && $pickup_time_settings['pickup_time_starts'] != "" && isset($pickup_time_settings['each_time_slot']) && $pickup_time_settings['each_time_slot'] !=""){
										if(isset($pickup_time_settings['pickup_time_ends']) && $pickup_time_settings['pickup_time_ends'] != "") {
											if($pickup_time_settings['pickup_time_starts'] == $pickup_time_settings['pickup_time_ends']) {
												$pickup_time_settings['pickup_time_ends'] = 1440+$pickup_time_settings['pickup_time_ends'];
											}
											for($i = $pickup_time_settings['pickup_time_starts']; $i<$pickup_time_settings['pickup_time_ends']; $i = $i+(int)$pickup_time_settings['each_time_slot']) {

												$option_start = date($time_format, mktime(0, $i));
												if($i+(int)$pickup_time_settings['each_time_slot']>$pickup_time_settings['pickup_time_ends']){
													$option_end = date($time_format, mktime(0, (int)$pickup_time_settings['pickup_time_ends']));
													$option_value_start = $i;
													$option_value_end = $pickup_time_settings['pickup_time_ends'];
																
												} else {
													$option_end = date($time_format, mktime(0, $i+(int)$pickup_time_settings['each_time_slot']));
													$option_value_start = $i;
													$option_value_end = $i+(int)$pickup_time_settings['each_time_slot'];
												}
												
												
												echo '<option value="'.$option_value_start.'-'.$option_value_end.'">'.$option_start.' - '.$option_end.'</option>';
											
											}
						
										} else {
											for($i = $pickup_time_settings['pickup_time_starts']; $i<1440; $i = $i+(int)$pickup_time_settings['each_time_slot']) {
												$option_start = date($time_format, mktime(0, $i));
												$option_end = date($time_format, mktime(0, $i+(int)$pickup_time_settings['each_time_slot']));
												$option_value_start = $i;
												$option_value_end = $i+(int)$pickup_time_settings['each_time_slot'];
												echo '<option value="'.$option_value_start.'-'.$option_value_end.'">'.$option_start.' - '.$option_end.'</option>';
											}
										}
										
									} elseif((!isset($pickup_time_settings['each_time_slot']) || $pickup_time_settings['each_time_slot'] == "") && (isset($pickup_time_settings['pickup_time_starts']) && $pickup_time_settings['pickup_time_starts']!="") && (isset($pickup_time_settings['pickup_time_ends']) && $pickup_time_settings['pickup_time_ends']!="")) {

										$option_start = date($time_format, mktime(0, (int)$pickup_time_settings['pickup_time_starts']));
										$option_end = date($time_format, mktime(0, (int)$pickup_time_settings['pickup_time_ends']));
										$option_value_start = $pickup_time_settings['pickup_time_starts'];
										$option_value_end = $pickup_time_settings['pickup_time_ends'];
									 	echo '<option value="'.$option_value_start.'-'.$option_value_end.'">'.$option_start.' - '.$option_end.'</option>';
										
										
									}
									?>
									</select>
								    <input type="text" class="coderockz-woo-delivery-input-field" value="" onkeyup="if(isNaN(parseFloat(Number(this.value))) || isNaN(parseInt(Number(this.value), 10))) this.value = null;" style="vertical-align:top;width: 100px!important;" autocomplete="off" placeholder="Fee" disabled="disabled"/><span class="coderockz-woo-delivery-currency-code"><?php echo $currency_code; ?></span>
		                    	</div>
		                    <?php } ?>
	                    	</div>
	                    	<button class="coderockz-woo-delivery-pickup-slot-fee-btn"><span class="dashicons dashicons-plus"></span></button>
	                        <input class="coderockz-woo-delivery-submit-btn" type="submit" name="coderockz_woo_delivery_pickup_slot_fee_form_submit" value="<?php _e('Save Changes', 'coderockz-woo-delivery'); ?>" />
	                    </form>
                	</div>

                </div>

                <div class="coderockz-woo-delivery-card">
					<p class="coderockz-woo-delivery-card-header"><?php _e('Urgent Delivery Fee', 'coderockz-woo-delivery'); ?></p>
					<div class="coderockz-woo-delivery-card-body">
						<p class="coderockz-woo-delivery-time-slot-fee-warning"><span class="dashicons dashicons-megaphone"></span><?php _e(' The timeslots between this mentioned min/hour from current time is hidden from timeslot list.', 'coderockz-woo-delivery'); ?></p>
						<p class="coderockz-woo-delivery-conditional-delivery-fee-notice"><span class="dashicons dashicons-yes"></span><?php _e(' Settings Changed Successfully', 'coderockz-woo-delivery'); ?></p>
	                    <form action="" method="post" id ="coderockz_woo_delivery_conditional_delivery_fee_form_submit">
	                        <?php wp_nonce_field('coderockz_woo_delivery_nonce'); ?>
	                        <div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label" style="width: 250px!important;text-align:unset!important"><?php _e('Enable Conditional Delivery Fee', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="By enabling this option, you can add a conditional base delivery fee."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_delivery_enable_conditional_delivery_fee">
							       <input type="checkbox" name="coderockz_delivery_enable_conditional_delivery_fee" id="coderockz_delivery_enable_conditional_delivery_fee" <?php echo (isset($delivery_fee_settings['enable_conditional_delivery_fee']) && !empty($delivery_fee_settings['enable_conditional_delivery_fee'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>


	                    	<?php 
                    			$conditional_delivery_start_hour = "";
            					$conditional_delivery_start_min = "";
            					$conditional_delivery_start_format= "am";
                    			
                    			if(isset($delivery_fee_settings['conditional_delivery_time_starts']) && (string)$delivery_fee_settings['conditional_delivery_time_starts'] !='') {
                    				$conditional_delivery_time_starts = (int)$delivery_fee_settings['conditional_delivery_time_starts'];

                    				if($conditional_delivery_time_starts == 0) {
		            					$conditional_delivery_start_hour = "12";
		            					$conditional_delivery_start_min = "00";
		            					$conditional_delivery_start_format= "am";
		            				} elseif($conditional_delivery_time_starts > 0 && $conditional_delivery_time_starts <= 59) {

                    					$conditional_delivery_start_hour = "12";
                    					$conditional_delivery_start_min = sprintf("%02d", $conditional_delivery_time_starts);
                    					$conditional_delivery_start_format= "am";
                    				} elseif($conditional_delivery_time_starts > 59 && $conditional_delivery_time_starts <= 719) {
										$conditional_delivery_start_min = sprintf("%02d", (int)$conditional_delivery_time_starts%60);
										$conditional_delivery_start_hour = sprintf("%02d", ((int)$conditional_delivery_time_starts-$conditional_delivery_start_min)/60);
										$conditional_delivery_start_format= "am";
										
                    				} else {
										$conditional_delivery_start_min = sprintf("%02d", (int)$conditional_delivery_time_starts%60);
										$conditional_delivery_start_hour = sprintf("%02d", ((int)$conditional_delivery_time_starts-$conditional_delivery_start_min)/60);
										if($conditional_delivery_start_hour>12) {
											$conditional_delivery_start_hour = sprintf("%02d", $conditional_delivery_start_hour-12);
										}
										$conditional_delivery_start_format= "pm";
                    				}

                    			}
                    		?>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_conditional_delivery_time_slot_starts"><?php _e('Conditional Delivery Time Starts From', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Conditional Delivery Time starts from the time that is specified here. Only numerical value is accepted."><span class="dashicons dashicons-editor-help"></span></p>
	                    		<div id="coderockz_conditional_delivery_time_slot_starts" class="coderockz_conditional_delivery_time_slot_starts">
	                    			
	                        	<input name="coderockz_conditional_delivery_time_slot_starts_hour" type="number" class="coderockz-woo-delivery-number-field" max="12" min="1" onkeyup="if(!Number.isInteger(Number(this.value)) || this.value > 12 || this.value < 1) this.value = null;" value="<?php echo $conditional_delivery_start_hour; ?>" placeholder="Hour" autocomplete="off"/>
	                        	<input name="coderockz_conditional_delivery_time_slot_starts_min" type="number" class="coderockz-woo-delivery-number-field" max="59" min="0" onkeyup="if(!Number.isInteger(Number(this.value)) || this.value > 59 || this.value < 0) this.value = null;" value="<?php echo $conditional_delivery_start_min; ?>" placeholder="Minute" autocomplete="off"/>
	                        	<select class="coderockz-woo-delivery-select-field" name="coderockz_conditional_delivery_time_slot_starts_format">
									<option value="am" <?php selected($conditional_delivery_start_format,"am",true); ?>>AM</option>
									<option value="pm" <?php selected($conditional_delivery_start_format,"pm",true); ?>>PM</option>
								</select>
	                        	</div>
	                    	</div>
	                    	<?php 
                    			$conditional_delivery_end_hour = "";
            					$conditional_delivery_end_min = "";
            					$conditional_delivery_end_format= "am";
                    			
                    			if(isset($delivery_fee_settings['conditional_delivery_time_ends']) && (string)$delivery_fee_settings['conditional_delivery_time_ends'] !='') {
                    				$conditional_delivery_time_ends = (int)$delivery_fee_settings['conditional_delivery_time_ends'];
                    				if($conditional_delivery_time_ends == 0) {
		            					$conditional_delivery_end_hour = "12";
		            					$conditional_delivery_end_min = "00";
		            					$conditional_delivery_end_format= "am";
		            				} elseif($conditional_delivery_time_ends > 0 && $conditional_delivery_time_ends <= 59) {
                    					$conditional_delivery_end_hour = "12";
                    					$conditional_delivery_end_min = sprintf("%02d", $conditional_delivery_time_ends);
                    					$conditional_delivery_end_format= "am";
                    				} elseif($conditional_delivery_time_ends > 59 && $conditional_delivery_time_ends <= 719) {
										$conditional_delivery_end_min = sprintf("%02d", (int)$conditional_delivery_time_ends%60);
										$conditional_delivery_end_hour = sprintf("%02d", ((int)$conditional_delivery_time_ends-$conditional_delivery_end_min)/60);
										$conditional_delivery_end_format= "am";
										
                    				} elseif($conditional_delivery_time_ends > 719 && $conditional_delivery_time_ends <= 1439) {
										$conditional_delivery_end_min = sprintf("%02d", (int)$conditional_delivery_time_ends%60);
										$conditional_delivery_end_hour = sprintf("%02d", ((int)$conditional_delivery_time_ends-$conditional_delivery_end_min)/60);
										if($conditional_delivery_end_hour>12) {
											$conditional_delivery_end_hour = sprintf("%02d", $conditional_delivery_end_hour-12);
										}
										$conditional_delivery_end_format= "pm";
                    				} elseif($conditional_delivery_time_ends == 1440) {
										$conditional_delivery_end_min = "00";
										$conditional_delivery_end_hour = "12";
										$conditional_delivery_end_format= "am";
                    				}


                    			}
                    		?>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_delivery_time_slot_ends"><?php _e('Conditional Delivery Time Ends At', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Conditional Delivery Time ends at the time that is specified here. Only numerical value is accepted."><span class="dashicons dashicons-editor-help"></span></p>
	                    		<div id="coderockz_conditional_delivery_time_slot_ends" class="coderockz_conditional_delivery_time_slot_ends">
	                        	<input name="coderockz_conditional_delivery_time_slot_ends_hour" type="number" class="coderockz-woo-delivery-number-field" max="12" min="1" onkeyup="if(!Number.isInteger(Number(this.value)) || this.value > 12 || this.value < 1) this.value = null;" value="<?php echo $conditional_delivery_end_hour; ?>" placeholder="Hour" autocomplete="off"/>
	                        	<input name="coderockz_conditional_delivery_time_slot_ends_min" type="number" class="coderockz-woo-delivery-number-field" max="59" min="0" onkeyup="if(!Number.isInteger(Number(this.value)) || this.value > 59 || this.value < 0) this.value = null;" value="<?php echo $conditional_delivery_end_min; ?>" placeholder="Minute" autocomplete="off"/>
	                        	<select class="coderockz-woo-delivery-select-field" name="coderockz_conditional_delivery_time_slot_ends_format">
									<option value="am" <?php selected($conditional_delivery_end_format,"am",true); ?>>AM</option>
									<option value="pm" <?php selected($conditional_delivery_end_format,"pm",true); ?>>PM</option>
								</select>
	                        	</div>
	                        	<p class="coderockz_conditional_delivery_time_greater_notice">End Time Must after Start Time</p>
	                    	</div>

	                        <div class="coderockz-woo-delivery-form-group" style="display:inline-block!important;">
	                    		<label style="width:unset!important;" class="coderockz-woo-delivery-form-label" for="coderockz_delivery_conditional_delivery_fee"><?php _e('Delivery Fee', 'coderockz-woo-delivery'); ?></label>
	                        	<input style="width:60px!important;" id="coderockz_delivery_conditional_delivery_fee" name="coderockz_delivery_conditional_delivery_fee" type="text" onkeyup="if(isNaN(parseFloat(Number(this.value))) || isNaN(parseInt(Number(this.value), 10))) this.value = null;" class="coderockz-woo-delivery-input-field" value="<?php echo isset($delivery_fee_settings['conditional_delivery_fee']) && !empty($delivery_fee_settings['conditional_delivery_fee']) ? esc_attr($delivery_fee_settings['conditional_delivery_fee']) : ""; ?>" placeholder="" autocomplete="off"/><span class="coderockz-woo-delivery-currency-code"><?php echo $currency_code; ?></span>
	                    	</div>

	                    	<div class="coderockz-woo-delivery-form-group" style="display:inline!important;">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_conditional_delivery_shipping_method" style="width:unset!important;text-align:left!important"><?php _e('OR Use shipping Method ', 'coderockz-woo-delivery'); ?></label>
	                        	<select class="coderockz-woo-delivery-select-field" id="coderockz_woo_delivery_conditional_delivery_shipping_method" name="coderockz_woo_delivery_conditional_delivery_shipping_method" style="width:200px!important;">
	                        		<option value=""><?php _e('Select Shipping Method', 'coderockz-woo-delivery'); ?></option>
									<?php
			                        foreach ($shipping_methods as $method) { 
		                			?>
									<option value="<?php echo $method; ?>"  <?php if(isset($delivery_fee_settings['conditional_delivery_shipping_method']) && $delivery_fee_settings['conditional_delivery_shipping_method'] == $method){ echo "selected"; } ?>><?php echo $method; ?></option>
									<?php
			                        } 
		                			?>
								</select>
	                    	</div>

	                    	<?php
	                    		$duration = ""; 
	                    		$identity = "min";
                    			if(isset($delivery_fee_settings['conditional_delivery_fee_duration']) && !empty($delivery_fee_settings['conditional_delivery_fee_duration'])) {
                    				$conditional_delivery_fee_duration = (int)$delivery_fee_settings['conditional_delivery_fee_duration'];
                    				if($conditional_delivery_fee_duration <= 59) {
                    					$duration = $conditional_delivery_fee_duration;
                    				} else {
                    					$conditional_delivery_fee_duration = $conditional_delivery_fee_duration/60;
                    					$helper = new Coderockz_Woo_Delivery_Helper();
                    					if($helper->containsDecimal($conditional_delivery_fee_duration)){
                    						$duration = $conditional_delivery_fee_duration*60;
                    						$identity = "min";
                    					} else {
                    						$duration = $conditional_delivery_fee_duration;
                    						$identity = "hour";
                    					}
                    				}
                    			}
	                    	?>
	                    	<div class="coderockz-woo-delivery-form-group" style="display:inline!important;">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_conditional_delivery_fee_duration" style="width:unset!important;text-align:left!important"><?php _e('For the next available', 'coderockz-woo-delivery'); ?></label>
	                    		<div id="coderockz_woo_delivery_conditional_delivery_fee_duration" class="coderockz_woo_delivery_conditional_delivery_fee_duration" style="width:unset!important;">
	                        	<input name="coderockz_woo_delivery_conditional_delivery_fee_time" type="number" min="1" onkeyup="if(!Number.isInteger(Number(this.value)) || this.value < 1) this.value = null;" class="coderockz-woo-delivery-number-field" value="<?php echo $duration; ?>" placeholder="" autocomplete="off" style="width:70px!important;"/>
	                        	<select class="coderockz-woo-delivery-select-field" name="coderockz_woo_delivery_conditional_delivery_fee_format" style="width:90px!important;">
									<option value="min" <?php selected($identity,"min",true); ?>>Minutes</option>
									<option value="hour" <?php selected($identity,"hour",true); ?>>Hour</option>
								</select>
	                        	</div>
	                    	</div>

	                    	<p class="coderockz_conditional_delivery_method_choose_notice">You have to choose either delivery fee or shipping method</p>

	                    	<div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label" style="width: 420px!important;text-align:unset!important"><?php _e('Diasble the timeslot Range Where The Next X Hour End', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="Suppose you have a timeslot of 3:00PM - 5:00PM and you set conditional delivery for next 3 hours. So when a customer comes to order at 1:00PM, the conditional delivery time ends 4:00PM. So if you want to disable the timeslot 3:00PM - 5:00PM, enable the option."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_delivery_disable_inter_timeslot_conditional">
							       <input type="checkbox" name="coderockz_delivery_disable_inter_timeslot_conditional" id="coderockz_delivery_disable_inter_timeslot_conditional" <?php echo (isset($delivery_fee_settings['disable_inter_timeslot_conditional']) && !empty($delivery_fee_settings['disable_inter_timeslot_conditional'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>

	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_conditional_delivery_text"><?php _e('Conditional Delivery Text in Timeslot Dropdown', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="The text you want to show in timeslot dropdown. Default is Delivery within X hour/min for Y amount."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_woo_delivery_conditional_delivery_text" name="coderockz_woo_delivery_conditional_delivery_text" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($delivery_fee_settings['conditional_delivery_dropdown_text']) && !empty($delivery_fee_settings['conditional_delivery_dropdown_text'])) ? stripslashes(esc_attr($delivery_fee_settings['conditional_delivery_dropdown_text'])) : "" ?>" placeholder="" autocomplete="off"/>
	                    	</div>

	                        <input class="coderockz-woo-delivery-submit-btn" type="submit" name="coderockz_woo_delivery_conditional_delivery_fee_form_submit" value="<?php _e('Save Changes', 'coderockz-woo-delivery'); ?>" />
	                    </form>
                	</div>

                </div>

                <div class="coderockz-woo-delivery-card">
					<p class="coderockz-woo-delivery-card-header"><?php _e('Delivery Date Fee', 'coderockz-woo-delivery'); ?></p>
					<div class="coderockz-woo-delivery-card-body">
						<p class="coderockz-woo-delivery-date-fee-notice"><span class="dashicons dashicons-yes"></span><?php _e(' Settings Changed Successfully', 'coderockz-woo-delivery'); ?></p>
	                    <form action="" method="post" id ="coderockz_woo_delivery_date_fee_form_submit">
	                        <?php wp_nonce_field('coderockz_woo_delivery_nonce'); ?>
	                        <div style="border:1px solid #ddd;border-radius: 4px;">
	                        <div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label"><?php _e('Enable Deliver Date Fee', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="By enabling this option, any fee for ordering in same day, next day and other days can be added."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_delivery_date_enable_delivery_date_fee">
							       <input type="checkbox" name="coderockz_delivery_date_enable_delivery_date_fee" id="coderockz_delivery_date_enable_delivery_date_fee" <?php echo (isset($delivery_fee_settings['enable_delivery_date_fee']) && !empty($delivery_fee_settings['enable_delivery_date_fee'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_delivery_date_same_day_fee"><?php _e('Same Day Delivery Fee', 'coderockz-woo-delivery'); ?></label>
	                        	<input style="width:100px!important;" id="coderockz_delivery_date_same_day_fee" name="coderockz_delivery_date_same_day_fee" type="text" onkeyup="if(isNaN(parseFloat(Number(this.value))) || isNaN(parseInt(Number(this.value), 10))) this.value = null;" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($delivery_fee_settings['same_day_fee']) && !empty($delivery_fee_settings['same_day_fee'])) ? esc_attr($delivery_fee_settings['same_day_fee']) : ""; ?>" placeholder="" autocomplete="off"/><span class="coderockz-woo-delivery-currency-code"><?php echo $currency_code; ?></span>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_delivery_date_next_day_fee"><?php _e('Next Day Delivery Fee', 'coderockz-woo-delivery'); ?></label>
	                        	<input style="width:100px!important;" id="coderockz_delivery_date_next_day_fee" name="coderockz_delivery_date_next_day_fee" type="text" onkeyup="if(isNaN(parseFloat(Number(this.value))) || isNaN(parseInt(Number(this.value), 10))) this.value = null;" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($delivery_fee_settings['next_day_fee']) && !empty($delivery_fee_settings['next_day_fee'])) ? esc_attr($delivery_fee_settings['next_day_fee']) : ""; ?>" placeholder="" autocomplete="off"/><span class="coderockz-woo-delivery-currency-code"><?php echo $currency_code; ?></span>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_delivery_date_day_after_tomorrow_fee"><?php _e('Day After Tomorrow Delivery Fee', 'coderockz-woo-delivery'); ?></label>
	                        	<input style="width:100px!important;" id="coderockz_delivery_date_day_after_tomorrow_fee" name="coderockz_delivery_date_day_after_tomorrow_fee" type="text" onkeyup="if(isNaN(parseFloat(Number(this.value))) || isNaN(parseInt(Number(this.value), 10))) this.value = null;" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($delivery_fee_settings['day_after_tomorrow_fee']) && !empty($delivery_fee_settings['day_after_tomorrow_fee'])) ? esc_attr($delivery_fee_settings['day_after_tomorrow_fee']) : ""; ?>" placeholder="" autocomplete="off"/><span class="coderockz-woo-delivery-currency-code"><?php echo $currency_code; ?></span>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_delivery_date_other_days_fee"><?php _e('Other Days Delivery Fee', 'coderockz-woo-delivery'); ?></label>
	                        	<input style="width:100px!important;" id="coderockz_delivery_date_other_days_fee" name="coderockz_delivery_date_other_days_fee" type="text" onkeyup="if(isNaN(parseFloat(Number(this.value))) || isNaN(parseInt(Number(this.value), 10))) this.value = null;" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($delivery_fee_settings['other_days_fee']) && !empty($delivery_fee_settings['other_days_fee'])) ? esc_attr($delivery_fee_settings['other_days_fee']) : ""; ?>" placeholder="" autocomplete="off"/><span class="coderockz-woo-delivery-currency-code"><?php echo $currency_code; ?></span>
	                    	</div>
	                    	</div>
	                    	<div style="border:1px solid #ddd;border-radius: 4px;margin: 10px 0;padding: 20px;">
	                    	<div class="coderockz-woo-delivery-form-group" style="display:inline!important;">
								<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_conditional_delivery_day_shipping_method" style="width:unset!important;text-align:left!important"><?php _e('Use Shipping Method ', 'coderockz-woo-delivery'); ?></label>
								<select class="coderockz-woo-delivery-select-field" id="coderockz_woo_delivery_conditional_delivery_day_shipping_method" name="coderockz_woo_delivery_conditional_delivery_day_shipping_method" style="width:200px!important;">
									<option value=""><?php _e('Select Shipping Method', 'coderockz-woo-delivery'); ?></option>
									<?php
							        foreach ($shipping_methods as $method) { 
									?>
									<option value="<?php echo $method; ?>"  <?php if(isset($delivery_fee_settings['conditional_delivery_day_shipping_method']) && $delivery_fee_settings['conditional_delivery_day_shipping_method'] == $method){ echo "selected"; } ?>><?php echo $method; ?></option>
									<?php
							        } 
									?>
								</select>
							</div>
							<div class="coderockz-woo-delivery-form-group" style="display:inline!important;">
								<label class="coderockz-woo-delivery-form-label" style="width:unset!important;text-align:left!important"><?php _e('For the First ', 'coderockz-woo-delivery'); ?></label>

								<input type="number" name="coderockz_woo_delivery_conditional_delivery_day_shipping_method_total_day" class="coderockz-woo-delivery-number-field" value="<?php echo (isset($delivery_fee_settings['conditional_delivery_day_shipping_method_total_day']) && $delivery_fee_settings['conditional_delivery_day_shipping_method_total_day'] != "") ? esc_attr($delivery_fee_settings['conditional_delivery_day_shipping_method_total_day']) : ""; ?>" onkeyup="if(!Number.isInteger(Number(this.value))) this.value = null;" style="vertical-align:top;width: 100px!important;" autocomplete="off" placeholder=""/><span class="coderockz-woo-delivery-processing-days-placeholder">Days</span>

							</div>
							</div>

	                        <input class="coderockz-woo-delivery-submit-btn" type="submit" name="coderockz_woo_delivery_date_fee_form_submit" value="<?php _e('Save Changes', 'coderockz-woo-delivery'); ?>" />
	                    </form>
                	</div>

                </div>
                <div class="coderockz-woo-delivery-card">
					<p class="coderockz-woo-delivery-card-header"><?php _e(' Weekday Wise Delivery Date Fee', 'coderockz-woo-delivery'); ?></p>
					<div class="coderockz-woo-delivery-card-body">
						<p class="coderockz-woo-delivery-weekday-wise-fee-notice"><span class="dashicons dashicons-yes"></span><?php _e(' Settings Changed Successfully', 'coderockz-woo-delivery'); ?></p>
	                    <form action="" method="post" id ="coderockz_woo_delivery_weekday_wise_fee_form_submit">
	                        <?php wp_nonce_field('coderockz_woo_delivery_nonce'); ?>
	                        <div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label" style="width:365px!important"><?php _e('Enable Weekday Wise Delivery Date Fee', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="Enable it if you want different delivery fee for different week day. Default is disable."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_woo_delivery_enable_weekday_wise_delivery_fee">
							       <input type="checkbox" class="coderockz_woo_delivery_enable_weekday_wise_delivery_fee" name="coderockz_woo_delivery_enable_weekday_wise_delivery_fee" id="coderockz_woo_delivery_enable_weekday_wise_delivery_fee" <?php echo (isset($delivery_fee_settings['enable_weekday_wise_delivery_fee']) && !empty($delivery_fee_settings['enable_weekday_wise_delivery_fee'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>
	                    	<?php
	                    	$weekday = array("0"=>"Sunday", "1"=>"Monday", "2"=>"Tuesday", "3"=>"Wednesday", "4"=>"Thursday", "5"=>"Friday", "6"=>"Saturday");
	                        foreach ($weekday as $key => $value) { 

	                        	$fee = isset($delivery_fee_settings['weekday_wise_delivery_fee'][$key]) && !empty($delivery_fee_settings['weekday_wise_delivery_fee'][$key]) ? $delivery_fee_settings['weekday_wise_delivery_fee'][$key] : "";
                				?>

                				<div class="coderockz-woo-delivery-form-group">
		                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_weekday_wise_fee_<?php echo $key; ?>"><?php _e($value.' Delivery Fee', 'coderockz-woo-delivery'); ?></label>
		                        	<input style="width:100px!important;" id="coderockz_woo_delivery_weekday_wise_fee_<?php echo $key; ?>" name="coderockz_woo_delivery_weekday_wise_fee_<?php echo $key; ?>" type="text" onkeyup="if(isNaN(parseFloat(Number(this.value))) || isNaN(parseInt(Number(this.value), 10))) this.value = null;" class="coderockz-woo-delivery-input-field" value="<?php echo $fee; ?>" placeholder="" autocomplete="off"/><span class="coderockz-woo-delivery-currency-code"><?php echo $currency_code; ?></span>
		                    	</div>

                				<?php
	                        }
	                        ?>

	                        <input class="coderockz-woo-delivery-submit-btn" type="submit" name="coderockz_woo_delivery_weekday_wise_fee_form_submit" value="<?php _e('Save Changes', 'coderockz-woo-delivery'); ?>" />
	                    </form>
                	</div>

                </div>
			</div>

			<div data-tab="tab18" class="coderockz-woo-delivery-tabcontent">
				<div class="coderockz-woo-delivery-card">
					<p class="coderockz-woo-delivery-card-header"><?php _e('Notify Email Settings', 'coderockz-woo-delivery'); ?></p>
					<div class="coderockz-woo-delivery-card-body">
						<p class="coderockz-woo-notify-email-tab-notice"><span class="dashicons dashicons-yes"></span><?php _e(' Settings Changed Successfully', 'coderockz-woo-delivery'); ?></p>
	                    <form action="" method="post" id ="coderockz_woo_delivery_notify_email_form_submit">
	                        <?php wp_nonce_field('coderockz_woo_delivery_nonce'); ?>
	                        <div class="coderockz-woo-delivery-form-group">
								
								<span style="vertical-align:bottom" class="coderockz-woo-delivery-form-label"><?php _e('Header Logo', 'coderockz-woo-delivery'); ?></span>
								<p style="vertical-align:bottom" class="coderockz-woo-delivery-tooltip" tooltip="Set a logo at the top of email."><span class="dashicons dashicons-editor-help"></span></p>
								<div style="display:inline-block;vertical-align: middle;">
									<div class='coderockz-woo-delivery-notify-email-logo-preview-wrapper'>
									<?php
									if ( isset($notify_email_settings['notify-email-logo-id']) && !empty( $notify_email_settings['notify-email-logo-id'] ) ) {
										$coderockz_woo_delivery_notify_email_logo = wp_get_attachment_url( $notify_email_settings['notify-email-logo-id'] );
										?>
										<img style="max-width:90px;display: block;margin:0 auto 10px;padding-right: 15px;" id='coderockz-woo-delivery-notify-email-logo-preview' src='<?php echo esc_url( $coderockz_woo_delivery_notify_email_logo ); ?>'>
										<?php
									} else {
										?>
										<img style="max-width:90px;display: block;margin:0 auto 10px;padding-right: 15px;" id='coderockz-woo-delivery-notify-email-logo-preview' src=''>
										<?php
									}

									?>
								</div>
									<?php wp_enqueue_media(); ?>
									<input id="coderockz-woo-delivery-notify-email-logo-upload-btn" type="button" class="button" value="<?php _e( 'Upload Logo', 'coderockz-woo-delivery' ); ?>"/>
									<input type='hidden' name='coderockz-woo-delivery-notify-email-logo-upload-id' id='coderockz-woo-delivery-notify-email-logo-upload-id' value="<?php echo (isset($notify_email_settings['notify-email-logo-id']) && !empty($notify_email_settings['notify-email-logo-id'])) ? esc_attr($notify_email_settings['notify-email-logo-id']) : "" ?>">
								</div>
							</div>
							<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_notify_email_heading_color"><?php _e('Notify Email Heading Color', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Notify Email Heading Color. Default is #96588a."><span class="dashicons dashicons-editor-help"></span></p>
	                    		<input type="text" style="width: 130px!important;line-height:unset!important;border: 1px solid #ccc!important;max-height: 30px!important;border-top-right-radius: 0!important;border-bottom-right-radius: 0!important;" class="coderockz-woo-delivery-input-field" id="coderockz_woo_delivery_notify_email_heading_color" name="coderockz_woo_delivery_notify_email_heading_color" value="<?php echo (isset($notify_email_settings['notify_email_heading_color']) && !empty($notify_email_settings['notify_email_heading_color'])) ? esc_attr($notify_email_settings['notify_email_heading_color']) : "" ?>" placeholder="" autocomplete="off"/>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_notify_delivery_email_subject"><?php _e('Notify Delivery Email Subject', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Subject of notify delivery email that is sent from the order page metabox. Default is Your Order Information Is Changed."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_woo_delivery_notify_delivery_email_subject" name="coderockz_woo_delivery_notify_delivery_email_subject" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo isset($notify_email_settings['notify_delivery_email_subject']) && $notify_email_settings['notify_delivery_email_subject'] != ""?stripslashes(esc_attr($notify_email_settings['notify_delivery_email_subject'])): ""; ?>" placeholder="" autocomplete="off"/>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_notify_pickup_email_subject"><?php _e('Notify Pickup Email Subject', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Subject of notify pickup email that is sent from the order page metabox. Default is Your Pickup Information Is Changed."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_woo_delivery_notify_pickup_email_subject" name="coderockz_woo_delivery_notify_pickup_email_subject" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo isset($notify_email_settings['notify_pickup_email_subject']) && $notify_email_settings['notify_pickup_email_subject'] != ""?stripslashes(esc_attr($notify_email_settings['notify_pickup_email_subject'])): ""; ?>" placeholder="" autocomplete="off"/>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label"><?php _e('Send Notify Email from Different Email & Name', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="Enable it if you want to send the notify email from different email and name. Default is sending notify email from default admin email and website name."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_woo_delivery_notify_email_different_name_email">
							       <input type="checkbox" name="coderockz_woo_delivery_notify_email_different_name_email" id="coderockz_woo_delivery_notify_email_different_name_email" <?php echo (isset($notify_email_settings['notify_email_different_name_email']) && !empty($notify_email_settings['notify_email_different_name_email'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>
	                    	<div class="coderockz_woo_delivery_notify_email_different_name_email_section" style="display:none;">
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_send_email_from_email"><?php _e('Send Notify Emails From Email', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Send email from this email. Default is going from Administration Email Address."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_woo_delivery_send_email_from_email" name="coderockz_woo_delivery_send_email_from_email" type="email" class="coderockz-woo-delivery-input-field" value="<?php echo isset($notify_email_settings['send_email_from_email']) && $notify_email_settings['send_email_from_email'] != ""?esc_attr($notify_email_settings['send_email_from_email']): ""; ?>" placeholder="" autocomplete="off"/>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_send_email_from_name"><?php _e('Send Notify Emails From Name', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Send email from this name. Default name is Website title."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_woo_delivery_send_email_from_name" name="coderockz_woo_delivery_send_email_from_name" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo isset($notify_email_settings['send_email_from_name']) && $notify_email_settings['send_email_from_name'] != ""?stripslashes(esc_attr($notify_email_settings['send_email_from_name'])): ""; ?>" placeholder="" autocomplete="off"/>
	                    	</div>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_delivery_notify_email_heading"><?php _e('Notify Delivery Email Heading', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Notify Delivery Email Heading. Default is Your Delivery Information is Changed."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_woo_delivery_delivery_notify_email_heading" name="coderockz_woo_delivery_delivery_notify_email_heading" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo isset($notify_email_settings['delivery_notify_email_heading']) && $notify_email_settings['delivery_notify_email_heading'] != ""?stripslashes(esc_attr($notify_email_settings['delivery_notify_email_heading'])): ""; ?>" placeholder="" autocomplete="off"/>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_pickup_notify_email_heading"><?php _e('Notify Pickup Email Heading', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Notify Pickup Email Heading. Default is Your Pickup Information is Changed."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_woo_delivery_pickup_notify_email_heading" name="coderockz_woo_delivery_pickup_notify_email_heading" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo isset($notify_email_settings['pickup_notify_email_heading']) && $notify_email_settings['pickup_notify_email_heading'] != ""?stripslashes(esc_attr($notify_email_settings['pickup_notify_email_heading'])): ""; ?>" placeholder="" autocomplete="off"/>
	                    	</div>

	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_notify_email_product_text"><?php _e('Product Text', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Default is Product."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_woo_delivery_notify_email_product_text" name="coderockz_woo_delivery_notify_email_product_text" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo isset($notify_email_settings['notify_email_product_text']) && $notify_email_settings['notify_email_product_text'] != ""?stripslashes(esc_attr($notify_email_settings['notify_email_product_text'])): ""; ?>" placeholder="" autocomplete="off"/>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_notify_email_quantity_text"><?php _e('Quantity Text', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Default is Quantity."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_woo_delivery_notify_email_quantity_text" name="coderockz_woo_delivery_notify_email_quantity_text" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo isset($notify_email_settings['notify_email_quantity_text']) && $notify_email_settings['notify_email_quantity_text'] != ""?stripslashes(esc_attr($notify_email_settings['notify_email_quantity_text'])): ""; ?>" placeholder="" autocomplete="off"/>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_notify_email_price_text"><?php _e('Price Text', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Default is Price."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_woo_delivery_notify_email_price_text" name="coderockz_woo_delivery_notify_email_price_text" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo isset($notify_email_settings['notify_email_price_text']) && $notify_email_settings['notify_email_price_text'] != ""?stripslashes(esc_attr($notify_email_settings['notify_email_price_text'])): ""; ?>" placeholder="" autocomplete="off"/>
	                    	</div>


	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_notify_email_shipping_text"><?php _e('Shipping Method Text', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Default is Shipping Method."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_woo_delivery_notify_email_shipping_text" name="coderockz_woo_delivery_notify_email_shipping_text" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo isset($notify_email_settings['notify_email_shipping_text']) && $notify_email_settings['notify_email_shipping_text'] != ""?stripslashes(esc_attr($notify_email_settings['notify_email_shipping_text'])): ""; ?>" placeholder="" autocomplete="off"/>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_notify_email_payment_text"><?php _e('Payment Method Text', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Default is Payment Method."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_woo_delivery_notify_email_payment_text" name="coderockz_woo_delivery_notify_email_payment_text" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo isset($notify_email_settings['notify_email_payment_text']) && $notify_email_settings['notify_email_payment_text'] != ""?stripslashes(esc_attr($notify_email_settings['notify_email_payment_text'])): ""; ?>" placeholder="" autocomplete="off"/>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_notify_email_total_text"><?php _e('Total Text', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Default is Total."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_woo_delivery_notify_email_total_text" name="coderockz_woo_delivery_notify_email_total_text" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo isset($notify_email_settings['notify_email_total_text']) && $notify_email_settings['notify_email_total_text'] != ""?stripslashes(esc_attr($notify_email_settings['notify_email_total_text'])): ""; ?>" placeholder="" autocomplete="off"/>
	                    	</div>



	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_notify_email_billing_address_heading"><?php _e('Billing Address Heading Text', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Default is Billing Address."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_woo_delivery_notify_email_billing_address_heading" name="coderockz_woo_delivery_notify_email_billing_address_heading" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo isset($notify_email_settings['notify_email_billing_address_heading']) && $notify_email_settings['notify_email_billing_address_heading'] != ""?stripslashes(esc_attr($notify_email_settings['notify_email_billing_address_heading'])): ""; ?>" placeholder="" autocomplete="off"/>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_notify_email_shipping_address_heading"><?php _e('Shipping Address Heading Text', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Default is Shipping Address."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_woo_delivery_notify_email_shipping_address_heading" name="coderockz_woo_delivery_notify_email_shipping_address_heading" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo isset($notify_email_settings['notify_email_shipping_address_heading']) && $notify_email_settings['notify_email_shipping_address_heading'] != ""?stripslashes(esc_attr($notify_email_settings['notify_email_shipping_address_heading'])): ""; ?>" placeholder="" autocomplete="off"/>
	                    	</div>




	                        <input class="coderockz-woo-delivery-submit-btn" type="submit" name="coderockz_woo_delivery_notify_email_form_submit" value="<?php _e('Save Changes', 'coderockz-woo-delivery'); ?>" />

	                    </form>
                	</div>

                </div>
                <div class="coderockz-woo-delivery-card">
					<p class="coderockz-woo-delivery-card-header"><?php _e('Notify Email Preview', 'coderockz-woo-delivery'); ?></p>
					<div class="coderockz-woo-delivery-card-body">
						<img src="<?php echo CODEROCKZ_WOO_DELIVERY_URL ?>/admin/images/notify-email-preview.png" alt="" style="display: block;margin: 0 auto;">
                	</div>

                </div>
			</div>
			<div data-tab="tab19" class="coderockz-woo-delivery-tabcontent">
				<div class="coderockz-woo-delivery-card">
					<p class="coderockz-woo-delivery-card-header"><?php _e('Additional Field Settings', 'coderockz-woo-delivery'); ?></p>
					<div class="coderockz-woo-delivery-card-body">
						<p class="coderockz-woo-delivery-additional-field-notice"><span class="dashicons dashicons-yes"></span><?php _e(' Settings Changed Successfully', 'coderockz-woo-delivery'); ?></p>
	                    <form action="" method="post" id ="coderockz_delivery_additional_field_form_submit">
	                        <?php wp_nonce_field('coderockz_woo_delivery_nonce'); ?>

	                    	<div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label"><?php _e('Enable Additional field', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="Enable Additional textarea field in woocommerce order checkout page. This field is helpful when customer want to give special instructions to seller about the order"><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_enable_additional_field">
							       <input type="checkbox" name="coderockz_enable_additional_field" id="coderockz_enable_additional_field" <?php echo (isset($additional_field_settings['enable_additional_field']) && !empty($additional_field_settings['enable_additional_field'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label"><?php _e('Make Additional Field Mandatory', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="Make Additional textarea field mandatory in woocommerce order checkout page. Default is optional."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_additional_field_mandatory">
							       <input type="checkbox" name="coderockz_additional_field_mandatory" id="coderockz_additional_field_mandatory" <?php echo (isset($additional_field_settings['additional_field_mandatory']) && !empty($additional_field_settings['additional_field_mandatory'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_additional_field_label"><?php _e('Additional Field Label', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Additional textarea field label. Default is Special Note About Delivery."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_additional_field_label" name="coderockz_additional_field_label" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($additional_field_settings['field_label']) && !empty($additional_field_settings['field_label'])) ? stripslashes(esc_attr($additional_field_settings['field_label'])) : "" ?>" placeholder="" autocomplete="off"/>
	                    	</div>

	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_additional_field_placeholder"><?php _e('Additional Field Placeholder', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Additional textarea field placeholder. Default is nothing."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_additional_field_placeholder" name="coderockz_additional_field_placeholder" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($additional_field_settings['field_placeholder']) && !empty($additional_field_settings['field_placeholder'])) ? stripslashes(esc_attr($additional_field_settings['field_placeholder'])) : "" ?>" placeholder="" autocomplete="off"/>
	                    	</div>

	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_additional_field_ch_limit"><?php _e('Additional Field Character Limit', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Restrict the character of Additional textarea field that customer is written. Only numerical value is excepted. Default is unlimited characters."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input onkeyup="if(!Number.isInteger(Number(this.value)) || this.value < 1) this.value = null;" id="coderockz_additional_field_ch_limit" name="coderockz_additional_field_ch_limit" type="number" class="coderockz-woo-delivery-number-field" value="<?php echo (isset($additional_field_settings['character_limit']) && !empty($additional_field_settings['character_limit'])) ? stripslashes(esc_attr($additional_field_settings['character_limit'])) : "" ?>" placeholder="" autocomplete="off"/>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_additional_field_character_remaining_text"><?php _e('Character Remaining Text', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Default is X characters remaining."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_woo_delivery_additional_field_character_remaining_text" name="coderockz_woo_delivery_additional_field_character_remaining_text" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($additional_field_settings['character_remaining_text']) && !empty($additional_field_settings['character_remaining_text'])) ? stripslashes(esc_attr($additional_field_settings['character_remaining_text'])) : "" ?>" placeholder="" autocomplete="off"/>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Hide Additional Field For', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="If you don't want to show your customers the additional field for delivery or pickup, specify it here."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<select id="coderockz_woo_delivery_hide_additional_field_for" name="coderockz_woo_delivery_hide_additional_field_for[]" class="coderockz_woo_delivery_hide_additional_field_for" multiple>
                                <?php
                                $option = array("delivery"=>"Delivery", "pickup"=>"Pickup");
	                                foreach ($option as $key => $value) {
	                                	$selected = isset($additional_field_settings['hide_additional_field_for']) && !empty($additional_field_settings['hide_additional_field_for']) && in_array($key,$additional_field_settings['hide_additional_field_for']) ? "selected" : "";
	                                    echo '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';		
	                                }
                                ?>
                                </select>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label"><?php _e('Disable WooCommerce Order Notes', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="After enabling our additional fields, there are two textarea which does exactly same work. One is our additional field and another is WooCommerce default order notes. If you don't need the two then you can disable the WooComerce default order notes from here"><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" 2for="coderockz_woo_delivery_disable_order_notes">
							       <input type="checkbox" name="coderockz_woo_delivery_disable_order_notes" id="coderockz_woo_delivery_disable_order_notes" <?php echo (isset($additional_field_settings['disable_order_notes']) && !empty($additional_field_settings['disable_order_notes'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>

	                        <input class="coderockz-woo-delivery-submit-btn" type="submit" name="coderockz_delivery_additional_field_form_submit" value="<?php _e('Save Changes', 'coderockz-woo-delivery'); ?>" />

	                    </form>
                	</div>

                </div>
			</div>
			<div data-tab="tab20" class="coderockz-woo-delivery-tabcontent">
				<div class="coderockz-woo-delivery-card">
					<p class="coderockz-woo-delivery-card-header"><?php _e('Localization', 'coderockz-woo-delivery'); ?></p>
					<div class="coderockz-woo-delivery-card-body">
						<p class="coderockz-woo-delivery-localization-settings-notice"><span class="dashicons dashicons-yes"></span><?php _e(' Settings Changed Successfully', 'coderockz-woo-delivery'); ?></p>
	                    <form action="" method="post" id ="coderockz_delivery_localization_settings_form_submit">
	                        <?php wp_nonce_field('coderockz_woo_delivery_nonce'); ?>
	                      
	                        <div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_order_limit_notice"><?php _e('Maximum Delivery Limit Exceed', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Maximum Delivery Limit Notice. Default is Maximum Delivery Limit Exceed."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_woo_delivery_order_limit_notice" name="coderockz_woo_delivery_order_limit_notice" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($localization_settings['order_limit_notice']) && !empty($localization_settings['order_limit_notice'])) ? stripslashes(esc_attr($localization_settings['order_limit_notice'])) : "" ?>" placeholder="" autocomplete="off"/>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_pickup_limit_notice"><?php _e('Maximum Pickup Limit Exceed', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Maximum Pickup Limit Notice. Default is Maximum Pickup Limit Exceed."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_woo_delivery_pickup_limit_notice" name="coderockz_woo_delivery_pickup_limit_notice" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($localization_settings['pickup_limit_notice']) && !empty($localization_settings['pickup_limit_notice'])) ? stripslashes(esc_attr($localization_settings['pickup_limit_notice'])) : "" ?>" placeholder="" autocomplete="off"/>
	                    	</div>

	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_pickup_location_limit_notice"><?php _e('Maximum Pickup Limit Exceed For This Location', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Maximum Pickup for This Location Limit Notice. Default is Maximum Pickup Limit Exceed."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_woo_delivery_pickup_location_limit_notice" name="coderockz_woo_delivery_pickup_location_limit_notice" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($localization_settings['pickup_location_limit_notice']) && !empty($localization_settings['pickup_location_limit_notice'])) ? stripslashes(esc_attr($localization_settings['pickup_location_limit_notice'])) : "" ?>" placeholder="" autocomplete="off"/>
	                    	</div>

	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_delivery_heading_checkout"><?php _e('Delivery Information', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Checkout heading text of delivery section. Default is Delivery Information."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_woo_delivery_delivery_heading_checkout" name="coderockz_woo_delivery_delivery_heading_checkout" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($localization_settings['delivery_heading_checkout']) && !empty($localization_settings['delivery_heading_checkout'])) ? stripslashes(esc_attr($localization_settings['delivery_heading_checkout'])) : "" ?>" placeholder="" autocomplete="off"/>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_no_timeslot_available_notice"><?php _e('No Timeslot Available To Select', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="When no timeslot is available to select, this text will appear in delivery time field. Default is No Timeslot Available To Select."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_woo_delivery_no_timeslot_available_notice" name="coderockz_woo_delivery_no_timeslot_available_notice" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($localization_settings['no_timeslot_available']) && !empty($localization_settings['no_timeslot_available'])) ? stripslashes(esc_attr($localization_settings['no_timeslot_available'])) : "" ?>" placeholder="" autocomplete="off"/>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_select_delivery_date_notice"><?php _e('Select Delivery Date First', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="When no date is selected, this text will appear in delivery time field. Default is Select Delivery Date First."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_woo_delivery_select_delivery_date_notice" name="coderockz_woo_delivery_select_delivery_date_notice" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($localization_settings['select_delivery_date_notice']) && !empty($localization_settings['select_delivery_date_notice'])) ? stripslashes(esc_attr($localization_settings['select_delivery_date_notice'])) : "" ?>" placeholder="" autocomplete="off"/>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_select_pickup_date_notice"><?php _e('Select Pickup Date First', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="When no date is selected, this text will appear in pickup time field. Default is Select Pickup Date First."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_woo_delivery_select_pickup_date_notice" name="coderockz_woo_delivery_select_pickup_date_notice" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($localization_settings['select_pickup_date_notice']) && !empty($localization_settings['select_pickup_date_notice'])) ? stripslashes(esc_attr($localization_settings['select_pickup_date_notice'])) : "" ?>" placeholder="" autocomplete="off"/>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_select_pickup_date_location_notice"><?php _e('Select Pickup Date & Location First', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="When no date & location is selected, this text will appear in pickup time field. Default is Select Pickup Date & Location First."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_woo_delivery_select_pickup_date_location_notice" name="coderockz_woo_delivery_select_pickup_date_location_notice" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($localization_settings['select_pickup_date_location_notice']) && !empty($localization_settings['select_pickup_date_location_notice'])) ? stripslashes(esc_attr($localization_settings['select_pickup_date_location_notice'])) : "" ?>" placeholder="" autocomplete="off"/>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_select_pickup_location_notice"><?php _e('Select Pickup Location First', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="When no location is selected, this text will appear in pickup time field. Default is Select Pickup Location First."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_woo_delivery_select_pickup_location_notice" name="coderockz_woo_delivery_select_pickup_location_notice" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($localization_settings['select_pickup_location_notice']) && !empty($localization_settings['select_pickup_location_notice'])) ? stripslashes(esc_attr($localization_settings['select_pickup_location_notice'])) : "" ?>" placeholder="" autocomplete="off"/>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_delivery_details_text"><?php _e('Delivery Details', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Delivery Details text in order page, single order page, customer account page. Default is Delivery Details."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_woo_delivery_delivery_details_text" name="coderockz_woo_delivery_delivery_details_text" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($localization_settings['delivery_details_text']) && !empty($localization_settings['delivery_details_text'])) ? stripslashes(esc_attr($localization_settings['delivery_details_text'])) : "" ?>" placeholder="" autocomplete="off"/>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_delivery_status_text"><?php _e('Delivery Status', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Delivery Status text in order page, single order page, customer account page. Default is Delivery Status."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_woo_delivery_delivery_status_text" name="coderockz_woo_delivery_delivery_status_text" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($localization_settings['delivery_status_text']) && !empty($localization_settings['delivery_status_text'])) ? stripslashes(esc_attr($localization_settings['delivery_status_text'])) : "" ?>" placeholder="" autocomplete="off"/>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_delivery_status_not_delivered_text"><?php _e('Not Delivered', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Delivery Not Completed Status text in order page, single order page, customer account page. Default is Not Delivered."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_woo_delivery_delivery_status_not_delivered_text" name="coderockz_woo_delivery_delivery_status_not_delivered_text" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($localization_settings['delivery_status_not_delivered_text']) && !empty($localization_settings['delivery_status_not_delivered_text'])) ? stripslashes(esc_attr($localization_settings['delivery_status_not_delivered_text'])) : "" ?>" placeholder="" autocomplete="off"/>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_delivery_status_delivered_text"><?php _e('Delivery Completed', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Delivery Completed Status text in order page, single order page, customer account page. Default is Delivery Completed."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_woo_delivery_delivery_status_delivered_text" name="coderockz_woo_delivery_delivery_status_delivered_text" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($localization_settings['delivery_status_delivered_text']) && !empty($localization_settings['delivery_status_delivered_text'])) ? stripslashes(esc_attr($localization_settings['delivery_status_delivered_text'])) : "" ?>" placeholder="" autocomplete="off"/>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_pickup_status_not_picked_text"><?php _e('Not Picked', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Not Picked Status text in order page, single order page, customer account page. Default is Not Picked."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_woo_delivery_pickup_status_not_picked_text" name="coderockz_woo_delivery_pickup_status_not_picked_text" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($localization_settings['pickup_status_not_picked_text']) && !empty($localization_settings['pickup_status_not_picked_text'])) ? stripslashes(esc_attr($localization_settings['pickup_status_not_picked_text'])) : "" ?>" placeholder="" autocomplete="off"/>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_pickup_status_picked_text"><?php _e('Pickup Completed', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Pickup Completed Status text in order page, single order page, customer account page. Default is Pickup Completed."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_woo_delivery_pickup_status_picked_text" name="coderockz_woo_delivery_pickup_status_picked_text" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($localization_settings['pickup_status_picked_text']) && !empty($localization_settings['pickup_status_picked_text'])) ? stripslashes(esc_attr($localization_settings['pickup_status_picked_text'])) : "" ?>" placeholder="" autocomplete="off"/>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_order_metabox_heading"><?php _e('Delivery/Pickup Date & Time', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Single order page metabox heading text. Default is Delivery Date & Time."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_woo_delivery_order_metabox_heading" name="coderockz_woo_delivery_order_metabox_heading" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($localization_settings['order_metabox_heading']) && !empty($localization_settings['order_metabox_heading'])) ? stripslashes(esc_attr($localization_settings['order_metabox_heading'])) : "" ?>" placeholder="" autocomplete="off"/>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_checkout_delivery_option_notice"><?php _e('Please Select Your Order Type', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Notice if you make the order type field required but not given any value to the field. Default is Please Select Your Order Type."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_woo_delivery_checkout_delivery_option_notice" name="coderockz_woo_delivery_checkout_delivery_option_notice" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($localization_settings['checkout_delivery_option_notice']) && !empty($localization_settings['checkout_delivery_option_notice'])) ? stripslashes(esc_attr($localization_settings['checkout_delivery_option_notice'])) : "" ?>" placeholder="" autocomplete="off"/>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_checkout_date_notice"><?php _e('Please Enter Delivery Date', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Notice if you make the delivery date field required but not given any value to the field. Default is Please Enter Delivery Date."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_woo_delivery_checkout_date_notice" name="coderockz_woo_delivery_checkout_date_notice" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($localization_settings['checkout_date_notice']) && !empty($localization_settings['checkout_date_notice'])) ? stripslashes(esc_attr($localization_settings['checkout_date_notice'])) : "" ?>" placeholder="" autocomplete="off"/>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_checkout_pickup_date_notice"><?php _e('Please Enter Pickup Date', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Notice if you make the pickup date field required but not given any value to the field. Default is Please Enter Pickup Date."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_woo_delivery_checkout_pickup_date_notice" name="coderockz_woo_delivery_checkout_pickup_date_notice" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($localization_settings['checkout_pickup_date_notice']) && !empty($localization_settings['checkout_pickup_date_notice'])) ? stripslashes(esc_attr($localization_settings['checkout_pickup_date_notice'])) : "" ?>" placeholder="" autocomplete="off"/>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_checkout_time_notice"><?php _e('Please Enter Delivery Time', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Notice if you make the delivery time field required but not given any value to the field. Default is Please Enter Delivery Time."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_woo_delivery_checkout_time_notice" name="coderockz_woo_delivery_checkout_time_notice" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($localization_settings['checkout_time_notice']) && !empty($localization_settings['checkout_time_notice'])) ? stripslashes(esc_attr($localization_settings['checkout_time_notice'])) : "" ?>" placeholder="" autocomplete="off"/>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_checkout_pickup_time_notice"><?php _e('Please Enter Pickup Time', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Notice if you make the pickup time field required but not given any value to the field. Default is Please Enter Pickup Time."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_woo_delivery_checkout_pickup_time_notice" name="coderockz_woo_delivery_checkout_pickup_time_notice" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($localization_settings['checkout_pickup_time_notice']) && !empty($localization_settings['checkout_pickup_time_notice'])) ? stripslashes(esc_attr($localization_settings['checkout_pickup_time_notice'])) : "" ?>" placeholder="" autocomplete="off"/>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_checkout_pickup_notice"><?php _e('Please Enter Pickup Location', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Notice if you make the pickup location field required but not given any value to the field. Default is Please Enter Pickup Location."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_woo_delivery_checkout_pickup_notice" name="coderockz_woo_delivery_checkout_pickup_notice" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($localization_settings['checkout_pickup_notice']) && !empty($localization_settings['checkout_pickup_notice'])) ? stripslashes(esc_attr($localization_settings['checkout_pickup_notice'])) : "" ?>" placeholder="" autocomplete="off"/>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_pickup_location_map_notice"><?php _e('Pickup Location Map Notice(Under Pickup Location Field)', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Notice if you make the pickup location field required but not given any value to the field. Default is Please Enter Pickup Location."><span class="dashicons dashicons-editor-help"></span></p>
	                    		
	                    		<div id="coderockz_woo_delivery_pickup_location_map_notice" class="coderockz_woo_delivery_pickup_location_map_notice">
	                        	<input id="coderockz_woo_delivery_location_map_click_here" name="coderockz_woo_delivery_location_map_click_here" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($localization_settings['location_map_click_here']) && !empty($localization_settings['location_map_click_here'])) ? stripslashes(esc_attr($localization_settings['location_map_click_here'])) : "" ?>" placeholder="<?php _e('Click here', 'coderockz-woo-delivery'); ?>" autocomplete="off"/>
	                        	<input id="coderockz_woo_delivery_to_see_map_location" name="coderockz_woo_delivery_to_see_map_location" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($localization_settings['to_see_map_location']) && !empty($localization_settings['to_see_map_location'])) ? stripslashes(esc_attr($localization_settings['to_see_map_location'])) : "" ?>" placeholder="<?php _e('to see map location', 'coderockz-woo-delivery'); ?>" autocomplete="off"/>
	                        	</div>


	                        		
	                        	
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_checkout_additional_field_notice"><?php _e('Please Enter Special Note for Delivery', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Notice if you make the additional field required but not given any value to the field. Default is Please Enter Special Note for Delivery."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_woo_delivery_checkout_additional_field_notice" name="coderockz_woo_delivery_checkout_additional_field_notice" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($localization_settings['checkout_additional_field_notice']) && !empty($localization_settings['checkout_additional_field_notice'])) ? stripslashes(esc_attr($localization_settings['checkout_additional_field_notice'])) : "" ?>" placeholder="" autocomplete="off"/>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_checkout_delivery_fee_text"><?php _e('Delivery Time Slot Fee', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Delivery Time Slot Fee Text in the order summary box in checkout page. Default is Delivery Time Slot Fee."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_woo_delivery_checkout_delivery_fee_text" name="coderockz_woo_delivery_checkout_delivery_fee_text" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($localization_settings['delivery_fee_text']) && !empty($localization_settings['delivery_fee_text'])) ? stripslashes(esc_attr($localization_settings['delivery_fee_text'])) : "" ?>" placeholder="" autocomplete="off"/>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_checkout_pickup_fee_text"><?php _e('Pickup Slot Fee', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Pickup Slot Fee Text in the order summary box in checkout page. Default is Pickup Slot Fee."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_woo_delivery_checkout_pickup_fee_text" name="coderockz_woo_delivery_checkout_pickup_fee_text" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($localization_settings['pickup_fee_text']) && !empty($localization_settings['pickup_fee_text'])) ? stripslashes(esc_attr($localization_settings['pickup_fee_text'])) : "" ?>" placeholder="" autocomplete="off"/>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_conditional_fee_text"><?php _e('Conditional Delivery Fee', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Conditional Delivery Fee Text in the order summary box in checkout page. Default is Conditional Delivery Fee."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_woo_delivery_conditional_fee_text" name="coderockz_woo_delivery_conditional_fee_text" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($localization_settings['conditional_fee_text']) && !empty($localization_settings['conditional_fee_text'])) ? stripslashes(esc_attr($localization_settings['conditional_fee_text'])) : "" ?>" placeholder="" autocomplete="off"/>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_same_day_delivery_fee_text"><?php _e('Same Day Delivery Fee', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Same Day Delivery Fee Text in the order summary box in checkout page. Default is Same Day Delivery Fee."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_woo_delivery_same_day_delivery_fee_text" name="coderockz_woo_delivery_same_day_delivery_fee_text" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($localization_settings['sameday_fee_text']) && !empty($localization_settings['sameday_fee_text'])) ? stripslashes(esc_attr($localization_settings['sameday_fee_text'])) : "" ?>" placeholder="" autocomplete="off"/>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_next_day_delivery_fee_text"><?php _e('Next Day Delivery Fee', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Next Day Delivery Fee Text in the order summary box in checkout page. Default is Next Day Delivery Fee."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_woo_delivery_next_day_delivery_fee_text" name="coderockz_woo_delivery_next_day_delivery_fee_text" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($localization_settings['nextday_fee_text']) && !empty($localization_settings['nextday_fee_text'])) ? stripslashes(esc_attr($localization_settings['nextday_fee_text'])) : "" ?>" placeholder="" autocomplete="off"/>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_day_after_tomorrow_delivery_fee_text"><?php _e('Day After Tomorrow Delivery Fee', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Day After Tomorrow Delivery Fee Text in the order summary box in checkout page. Default is Day After Tomorrow Delivery Fee."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_woo_delivery_day_after_tomorrow_delivery_fee_text" name="coderockz_woo_delivery_day_after_tomorrow_delivery_fee_text" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($localization_settings['day_after_tomorrow_fee_text']) && !empty($localization_settings['day_after_tomorrow_fee_text'])) ? stripslashes(esc_attr($localization_settings['day_after_tomorrow_fee_text'])) : "" ?>" placeholder="" autocomplete="off"/>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_other_day_delivery_fee_text"><?php _e('Other Day Delivery Fee', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Other Day Delivery Fee Text in the order summary box in checkout page. Default is Other Day Delivery Fee."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_woo_delivery_other_day_delivery_fee_text" name="coderockz_woo_delivery_other_day_delivery_fee_text" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($localization_settings['other_fee_text']) && !empty($localization_settings['other_fee_text'])) ? stripslashes(esc_attr($localization_settings['other_fee_text'])) : "" ?>" placeholder="" autocomplete="off"/>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_weekday_fee_text"><?php _e('Week Day Fee', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Weekday Delivery Fee Text in the order summary box in checkout page. Default is Weekday Delivery Fee."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_woo_delivery_weekday_fee_text" name="coderockz_woo_delivery_weekday_fee_text" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($localization_settings['weekday_fee_text']) && !empty($localization_settings['weekday_fee_text'])) ? stripslashes(esc_attr($localization_settings['weekday_fee_text'])) : "" ?>" placeholder="" autocomplete="off"/>
	                    	</div>

	                    	

	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_only_available_for_today_text"><?php _e('only available for today', 'coderockz-woo-delivery', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Text replacement of only available for today"><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_woo_delivery_only_available_for_today_text" name="coderockz_woo_delivery_only_available_for_today_text" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($localization_settings['only_available_for_today_text']) && !empty($localization_settings['only_available_for_today_text'])) ? stripslashes(esc_attr($localization_settings['only_available_for_today_text'])) : "" ?>" placeholder="" autocomplete="off"/>
	                    	</div>

	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_free_shipping_other_day_text"><?php _e('is not available for today', 'coderockz-woo-delivery', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Text replacement of is not only available for today"><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_woo_delivery_free_shipping_other_day_text" name="coderockz_woo_delivery_free_shipping_other_day_text" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($localization_settings['free_shipping_other_day_text']) && !empty($localization_settings['free_shipping_other_day_text'])) ? stripslashes(esc_attr($localization_settings['free_shipping_other_day_text'])) : "" ?>" placeholder="" autocomplete="off"/>
	                    	</div>

	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_only_available_for_text"><?php _e('only available for', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Text replacement of only available for"><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_woo_delivery_only_available_for_text" name="coderockz_woo_delivery_only_available_for_text" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($localization_settings['only_available_for_text']) && !empty($localization_settings['only_available_for_text'])) ? stripslashes(esc_attr($localization_settings['only_available_for_text'])) : "" ?>" placeholder="" autocomplete="off"/>
	                    	</div>

	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_urgent_delivery_fee_text"><?php _e('Delivery only possible today. Shipping Method will change accordingly', 'coderockz-woo-delivery', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Notice of Urgent Delivery fee from Additional fees. Default is Delivery only possible today. Shipping Method will change accordingly."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_woo_delivery_urgent_delivery_fee_text" name="coderockz_woo_delivery_urgent_delivery_fee_text" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($localization_settings['urgent_delivery_fee_text']) && !empty($localization_settings['urgent_delivery_fee_text'])) ? stripslashes(esc_attr($localization_settings['urgent_delivery_fee_text'])) : "" ?>" placeholder="" autocomplete="off"/>
	                    	</div>

	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_need_to_select_text"><?php _e('and need to select delivery time', 'coderockz-woo-delivery', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Text replacement of and need to select delivery time"><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_woo_delivery_need_to_select_text" name="coderockz_woo_delivery_need_to_select_text" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($localization_settings['need_to_select_text']) && !empty($localization_settings['need_to_select_text'])) ? stripslashes(esc_attr($localization_settings['need_to_select_text'])) : "" ?>" placeholder="" autocomplete="off"/>
	                    	</div>

	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_if_available_text"><?php _e('if available', 'coderockz-woo-delivery', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Text replacement of if available"><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_woo_delivery_if_available_text" name="coderockz_woo_delivery_if_available_text" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($localization_settings['if_available_text']) && !empty($localization_settings['if_available_text'])) ? stripslashes(esc_attr($localization_settings['if_available_text'])) : "" ?>" placeholder="" autocomplete="off"/>
	                    	</div>

	                        <input class="coderockz-woo-delivery-submit-btn" type="submit" name="coderockz_delivery_localization_settings_form_submit" value="<?php _e('Save Changes', 'coderockz-woo-delivery'); ?>" />

	                    </form>
                	</div>

                </div>
			</div>
			<div data-tab="tab21" class="coderockz-woo-delivery-tabcontent">
                <div class="coderockz-woo-delivery-card">
					<p class="coderockz-woo-delivery-card-header"><?php _e('Exclusion Settings', 'coderockz-woo-delivery'); ?></p>
					<div class="coderockz-woo-delivery-card-body">
						<p class="coderockz-woo-delivery-time-slot-fee-warning"><span class="dashicons dashicons-megaphone"></span><?php _e('Please follow the documentation ', 'coderockz-woo-delivery'); ?><a href="https://coderockz.com/documentations/accommodate-shipping-with-delivery-or-pickup-or-both/" target="_blank"><?php _e('from here.', 'coderockz-woo-delivery'); ?></a><?php _e(' for using the Hide for Shipping Method feature correctly', 'coderockz-woo-delivery'); ?></p>
						<?php if(get_option('coderockz_woo_delivery_large_product_list') !== false) { ?>
							<p class="coderockz-woo-delivery-timezone-tab-warning"><span class="dashicons dashicons-megaphone"></span><?php _e('In Hide for Individual Product option, if product is simple input the product ID and if product is variable input the variation ID', 'coderockz-woo-delivery'); ?></p>
						<?php } ?>
						<p class="coderockz-woo-delivery-exclusion-settings-notice"></p>
	                    <form action="" method="post" id ="coderockz_woo_delivery_exclusion_settings_form_submit">
	                        <?php wp_nonce_field('coderockz_woo_delivery_nonce'); ?>
	                        
	                        <div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Hide Plugin Module for Categories', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Select the product categories for which you don't want to show the plugin module."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<select id="coderockz_woo_delivery_exclude_product_categories" name="coderockz_woo_delivery_exclude_product_categories[]" class="coderockz_woo_delivery_exclude_product_categories" multiple>
                                
                                <?php
                                $exclude_categories = [];
                                $get_store_product_meta= $this->helper->get_store_product_meta();
								if(isset($exclude_settings['exclude_categories']) && !empty($exclude_settings['exclude_categories'])) {
									foreach ($exclude_settings['exclude_categories'] as $hide_cat) {
										$exclude_categories[] = stripslashes($hide_cat);
									}
								}
                                foreach ($all_categories as $cat) {

                                	$selected = isset($exclude_settings['exclude_categories']) && !empty($exclude_settings['exclude_categories']) && in_array(htmlspecialchars_decode($cat->name),$exclude_categories) ? "selected" : "";
                                    echo '<option value="'.$cat->name.'" '.$selected.'>'.$cat->name.'</option>';
                                }
                                ?>
                                </select>
	                    	</div>
	                    	<?php if(get_option('coderockz_woo_delivery_large_product_list') == false) { ?>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Hide Plugin Module for Individual Product', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Select the product for which you don't want to show the plugin module."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<select id="coderockz_woo_delivery_exclude_individual_product" name="coderockz_woo_delivery_exclude_individual_product[]" class="coderockz_woo_delivery_exclude_individual_product" multiple>
                                
                                <?php
                                
                                foreach ($store_products as $key=>$value) {

                                	$selected = isset($exclude_settings['exclude_products']) && !empty($exclude_settings['exclude_products']) && in_array($key,$exclude_settings['exclude_products']) ? "selected" : "";
                                    echo '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
                                }
                                ?>
                                </select>
	                    	</div>
	                    	<?php } else { ?>

                    		<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Hide Plugin Module for Individual Product', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Select the product for which you don't want to show the plugin module."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<?php 
	                        	$exclude_products_input = isset($exclude_settings['exclude_products']) && !empty($exclude_settings['exclude_products']) ? $exclude_settings['exclude_products'] : array();
	                        	$exclude_products_input = implode(",",$exclude_products_input);
	                        	?>
	                    		<input id="coderockz_woo_delivery_exclude_individual_product_input" name="coderockz_woo_delivery_exclude_individual_product_input" type="text" class="coderockz_woo_delivery_exclude_individual_product_input coderockz-woo-delivery-input-field" value="<?php echo $exclude_products_input; ?>" placeholder="<?php _e('Comma(,) separated Product/Variation ID', 'coderockz-woo-delivery'); ?>" autocomplete="off"/>
	                    	</div>

	                    	<?php } ?>

	                        <div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label"><?php _e('Show Also If Cart Has Other Categories Or Products'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="If there is exclusion category's products or exclusion products in the cart then whatever there are other category's products or other products, the Delivery Date and Time section is hidden. Enable it if you want to reverse it."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_delivery_exclusion_reverse_current_condition">
							       <input data-get_store_product_meta="<?php echo $get_store_product_meta; ?>" type="checkbox" name="coderockz_delivery_exclusion_reverse_current_condition" id="coderockz_delivery_exclusion_reverse_current_condition" <?php echo (isset($exclude_settings['reverse_current_condition']) && !empty($exclude_settings['reverse_current_condition'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>

	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Hide for Shipping Method', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Select the shipping methods for which you don't want to show the plugin module."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<select id="coderockz_woo_delivery_exclude_shipping_methods" name="coderockz_woo_delivery_exclude_shipping_methods[]" class="coderockz_woo_delivery_exclude_shipping_methods" multiple>
                                
                                <?php
                                $exclude_shipping_methods = [];
                                $get_store_product_meta= $this->helper->get_store_product_meta();
								if(isset($exclude_settings['exclude_shipping_methods']) && !empty($exclude_settings['exclude_shipping_methods'])) {
									foreach ($exclude_settings['exclude_shipping_methods'] as $hide_method) {
										$exclude_shipping_methods[] = stripslashes($hide_method);
									}
								}
								
                                foreach ($shipping_methods_with_pickup as $shipping_method) {

                                	$selected = isset($exclude_settings['exclude_shipping_methods']) && !empty($exclude_settings['exclude_shipping_methods']) && in_array(htmlspecialchars_decode($shipping_method),$exclude_shipping_methods) ? "selected" : "";
                                    echo '<option value="'.$shipping_method.'" '.$selected.'>'.$shipping_method.'</option>';
                                }
                                ?>
                                </select>
	                    	</div>

	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Hide for Shipping Method Title', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Input the comma separated shipping method title for which you don't want to show the plugin module."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<?php 
	                        	$exclude_shipping_method_title = isset($exclude_settings['exclude_shipping_method_title']) && !empty($exclude_settings['exclude_shipping_method_title']) ? $exclude_settings['exclude_shipping_method_title'] : array();
	                        	$exclude_shipping_method_title = implode(",",$exclude_shipping_method_title);
	                        	?>
	                    		<input id="coderockz_woo_delivery_exclude_shipping_method_title" name="coderockz_woo_delivery_exclude_shipping_method_title" type="text" class="coderockz_woo_delivery_exclude_shipping_method_title coderockz-woo-delivery-input-field" value="<?php echo $exclude_shipping_method_title; ?>" placeholder="<?php _e('Comma(,) separated shipping method title', 'coderockz-woo-delivery'); ?>" autocomplete="off"/>
	                    	</div>

	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Hide for User Role', 'coderockz-woo-delivery'); ?><br/><span style="font-size: 11px;font-style: italic;color: lightseagreen;"><?php _e('(User must be Logged in)', 'coderockz-woo-delivery'); ?></span></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Select the user role for which you don't want to show the plugin module."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<select id="coderockz_woo_delivery_exclude_user_roles" name="coderockz_woo_delivery_exclude_user_roles[]" class="coderockz_woo_delivery_exclude_user_roles" multiple>
                                
                                <?php
                                $exclude_user_roles = [];
								if(isset($exclude_settings['exclude_user_roles']) && !empty($exclude_settings['exclude_user_roles'])) {
									foreach ($exclude_settings['exclude_user_roles'] as $hide_role) {
										$exclude_user_roles[] = stripslashes($hide_role);
									}
								}
								
                                foreach ($user_roles as $key => $value) {

                                	$selected = isset($exclude_settings['exclude_user_roles']) && !empty($exclude_settings['exclude_user_roles']) && in_array($key,$exclude_user_roles) ? "selected" : "";
                                    echo '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
                                }
                                ?>
                                </select>
	                    	</div>


	                        <input class="coderockz-woo-delivery-submit-btn" type="submit" name="coderockz_woo_delivery_exclusion_settings_form_submit" value="<?php _e('Save Changes', 'coderockz-woo-delivery'); ?>" />
	                    </form>
                	</div>

                </div>
			</div>
			<div style="display:none" data-tab="tab22" class="coderockz-woo-delivery-tabcontent">
				<div class="coderockz-woo-delivery-card">
					<p class="coderockz-woo-delivery-card-header"><?php _e('Google Calendar Integration Settings', 'coderockz-woo-delivery'); ?></p>
					<div class="coderockz-woo-delivery-card-body">
						<p class="coderockz-woo-delivery-time-slot-fee-warning"><span class="dashicons dashicons-megaphone"></span><?php _e(' To configure Google calendar integration with the plugin, Please follow the instructions ', 'coderockz-woo-delivery'); ?><a href="https://coderockz.com/documentations/how-to-integrate-plugin-google-calendar" target="_blank"><?php _e('from here.', 'coderockz-woo-delivery'); ?></a></p>
						<p class="coderockz-woo-delivery-google_calendar-settings-notice"><span class="dashicons dashicons-yes"></span><?php _e(' Settings Changed Successfully', 'coderockz-woo-delivery'); ?></p>
	                    <form action="" method="post" id ="coderockz_delivery_google_calendar_settings_form_submit">
	                        <?php wp_nonce_field('coderockz_woo_delivery_nonce'); ?>
	                    	<div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label"><?php _e('Automatically Sync to Google Calendar', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="If you want to add orders information as an event of Google calendar then enable the option. Default is disable."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_woo_delivery_enable_google_calendar_sync">
							       <input type="checkbox" name="coderockz_woo_delivery_enable_google_calendar_sync" id="coderockz_woo_delivery_enable_google_calendar_sync" <?php echo (isset($google_calendar_settings['google_calendar_sync']) && !empty($google_calendar_settings['google_calendar_sync'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>

	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_google_calendar_id"><?php _e('Calendar ID', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="If you want to add the order details as an event in a custom calendar. put the calendar ID here. Otherwise event will be created in default calendar."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_woo_delivery_google_calendar_id" name="coderockz_woo_delivery_google_calendar_id" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($google_calendar_settings['google_calendar_id']) && !empty($google_calendar_settings['google_calendar_id'])) ? stripslashes(esc_attr($google_calendar_settings['google_calendar_id'])) : "" ?>" placeholder="" autocomplete="off"/>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_google_calendar_client_id"><?php _e('Client ID', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip=""><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_woo_delivery_google_calendar_client_id" name="coderockz_woo_delivery_google_calendar_client_id" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($google_calendar_settings['google_calendar_client_id']) && !empty($google_calendar_settings['google_calendar_client_id'])) ? stripslashes(esc_attr($google_calendar_settings['google_calendar_client_id'])) : "" ?>" placeholder="" autocomplete="off" required/>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_google_calendar_client_secret"><?php _e('Client Secret', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip=""><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_woo_delivery_google_calendar_client_secret" name="coderockz_woo_delivery_google_calendar_client_secret" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($google_calendar_settings['google_calendar_client_secret']) && !empty($google_calendar_settings['google_calendar_client_secret'])) ? stripslashes(esc_attr($google_calendar_settings['google_calendar_client_secret'])) : "" ?>" placeholder="" autocomplete="off" required/>
	                    	</div>

	                    	<div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label"><?php _e('Google Calendar Sync Option for Customers', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="If you want to give the opportunity to your customer to sync in their google calendar then enable the option. Default is disable."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_woo_delivery_google_calendar_customer_sync">
							       <input type="checkbox" name="coderockz_woo_delivery_google_calendar_customer_sync" id="coderockz_woo_delivery_google_calendar_customer_sync" <?php echo (isset($google_calendar_settings['google_calendar_customer_sync']) && !empty($google_calendar_settings['google_calendar_customer_sync'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_order_received_page_btn_txt"><?php _e('Add to Google Calendar Button Text', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="The button text from the order confirmation page. Default is Add to Google Calendar."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_woo_delivery_order_received_page_btn_txt" name="coderockz_woo_delivery_order_received_page_btn_txt" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($google_calendar_settings['google_calendar_order_received_page_btn_txt']) && !empty($google_calendar_settings['google_calendar_order_received_page_btn_txt'])) ? stripslashes(esc_attr($google_calendar_settings['google_calendar_order_received_page_btn_txt'])) : "" ?>" placeholder="" autocomplete="off"/>
	                    	</div>

	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_order_added_page_btn_txt"><?php _e('Button text After Order Added To Google Calendar', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="The button text from the order confirmation page. Default is Add to Google Calendar."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_woo_delivery_order_added_page_btn_txt" name="coderockz_woo_delivery_order_added_page_btn_txt" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($google_calendar_settings['google_calendar_order_added_page_btn_txt']) && !empty($google_calendar_settings['google_calendar_order_added_page_btn_txt'])) ? stripslashes(esc_attr($google_calendar_settings['google_calendar_order_added_page_btn_txt'])) : "" ?>" placeholder="" autocomplete="off"/>
	                    	</div>

	                        <input class="coderockz-woo-delivery-submit-btn" type="submit" name="coderockz_delivery_google_calendar_settings_form_submit" value="<?php _e('Save Changes', 'coderockz-woo-delivery'); ?>" />

	                    </form>
                	</div>

                </div>
                <?php
                if((isset($google_calendar_settings['google_calendar_client_id']) && $google_calendar_settings['google_calendar_client_id']!="") && (isset($google_calendar_settings['google_calendar_client_secret']) && $google_calendar_settings['google_calendar_client_secret']!="")) {
                    

		            $calendar_sync_customer_client_id = isset($google_calendar_settings['google_calendar_client_id']) && !empty($google_calendar_settings['google_calendar_client_id']) ? $google_calendar_settings['google_calendar_client_id'] : "";
		
		            $calendar_sync_customer_client_secret = isset($google_calendar_settings['google_calendar_client_secret']) && !empty($google_calendar_settings['google_calendar_client_secret']) ? $google_calendar_settings['google_calendar_client_secret'] : "";
		
		            $calendar_sync_customer_redirect_url = get_site_url().'/wp-admin/admin.php?page=coderockz-woo-delivery-settings';
                    
                    
                   	$client = new Google_Client();
                    $client->setClientId($calendar_sync_customer_client_id);
                    $client->setClientSecret($calendar_sync_customer_client_secret);
                    $client->setRedirectUri($calendar_sync_customer_redirect_url);
                    $client->addScope("https://www.googleapis.com/auth/calendar.events");
                    $client->setAccessType('offline');
                    $client->setApprovalPrompt("force");
                    
                    $auth_url = $client->createAuthUrl(); 
                            
                    if(isset($_GET['code'])) {
                        $client->authenticate($_GET['code']);
                        $access_token = $client->getAccessToken();
                        update_option('coderockz_woo_delivery_google_calendar_access_token',$access_token);
                        wp_redirect($calendar_sync_customer_redirect_url);
                        exit();
                    
                    }
                    
                ?>
                <div class="coderockz-woo-delivery-card">
					<p class="coderockz-woo-delivery-card-header"><?php _e('Google Calendar Authentication', 'coderockz-woo-delivery'); ?></p>
					<div class="coderockz-woo-delivery-card-body">
						<p class="coderockz-woo-delivery-time-slot-fee-warning" style="margin-bottom:20px;"><span class="dashicons dashicons-megaphone"></span><?php _e(' You have to authenticate with Google to get the new order information in your Google Calendar.', 'coderockz-woo-delivery'); ?></p>

	                    
	                    <?php
	                    
	                    if(get_option('coderockz_woo_delivery_google_calendar_access_token')) {
	                        
	                        
	                         $client->setAccessToken(get_option('coderockz_woo_delivery_google_calendar_access_token'));
	                    
    	                    if($client->isAccessTokenExpired()) {
    	                        $client->fetchAccessTokenWithRefreshToken(get_option('coderockz_woo_delivery_google_calendar_access_token')['refresh_token']);
    	                        $access_token = $client->getAccessToken();
                                update_option('coderockz_woo_delivery_google_calendar_access_token',$access_token);
                                wp_redirect($calendar_sync_customer_redirect_url);
                                exit();
    	                        
    	                    }
	                        
                            $auth_text = '<a href="#" class="coderockz-woo-delivery-submit-btn coderockz-woo-delivery-google-unauth-btn">'. __('Unauthenticate', 'coderockz-woo-delivery').'</a><p class="coderockz-woo-delivery-no-auth-text" style="color: #1DA160;"><i class="dashicons dashicons-yes" style="margin-bottom: 3px;"></i>'.__('Authenticated', 'coderockz-woo-delivery').'</p>';
                        
                        } else {
                        
                            $auth_text = '<a href="'.$auth_url.'" class="coderockz-woo-delivery-submit-btn coderockz-woo-delivery-google-auth-btn">'. __('Authenticate', 'coderockz-woo-delivery').'</a><p class="coderockz-woo-delivery-no-auth-text" style="color: #DD5246;"><i class="dashicons dashicons-no-alt" style="margin-bottom: 3px;"></i>'.__('No Authentication', 'coderockz-woo-delivery').'</p>';
                        }
                        
                        echo $auth_text;
	                    ?>
                	</div>

                </div>
            	<?php } ?>
			</div>
			<div data-tab="tab23" class="coderockz-woo-delivery-tabcontent">
				<div class="coderockz-woo-delivery-card">
					<p class="coderockz-woo-delivery-card-header"><?php _e('Other Settings', 'coderockz-woo-delivery'); ?></p>
					<div class="coderockz-woo-delivery-card-body">
						<p class="coderockz-woo-delivery-other-settings-notice"><span class="dashicons dashicons-yes"></span><?php _e(' Settings Changed Successfully', 'coderockz-woo-delivery'); ?></p>
	                    <form action="" method="post" id ="coderockz_delivery_other_settings_form_submit">
	                        <?php wp_nonce_field('coderockz_woo_delivery_nonce'); ?>
	                        <div class="coderockz-woo-delivery-form-group">
								
								<span style="vertical-align:bottom" class="coderockz-woo-delivery-form-label"><?php _e('Spinner Animation in Checkout', 'coderockz-woo-delivery'); ?></span>
								<p style="vertical-align:bottom" class="coderockz-woo-delivery-tooltip" tooltip="Change the spinner animation while loading from here."><span class="dashicons dashicons-editor-help"></span></p>
								<div style="display:inline-block;vertical-align: middle;">
									<div class='coderockz-woo-delivery-animation-preview-wrapper'>
									<?php
									if ( isset($other_settings['spinner-animation-id']) && !empty( $other_settings['spinner-animation-id'] ) ) {
										$coderockz_woo_delivery_spinner_animation = wp_get_attachment_url( $other_settings['spinner-animation-id'] );
										?>
										<img style="max-width:90px;display: block;margin:0 auto 10px;padding-right: 15px;" id='coderockz-woo-delivery-animation-preview' src='<?php echo esc_url( $coderockz_woo_delivery_spinner_animation ); ?>'>
										<?php
									} else {
										?>
										<img style="max-width:90px;display: block;margin:0 auto 10px;padding-right: 15px;" id='coderockz-woo-delivery-animation-preview' src=''>
										<?php
									}

									?>
								</div>
									<?php wp_enqueue_media(); ?>
									<input id="coderockz-woo-delivery-spinner-upload-btn" type="button" class="button" value="<?php _e( 'Upload Animation', 'coderockz-woo-delivery' ); ?>"/>
									<input type='hidden' name='coderockz-woo-delivery-spinner-upload-id' id='coderockz-woo-delivery-spinner-upload-id' value="<?php echo (isset($other_settings['spinner-animation-id']) && !empty($other_settings['spinner-animation-id'])) ? esc_attr($other_settings['spinner-animation-id']) : "" ?>">
								</div>
							</div>
							<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_spinner_animation_background"><?php _e('Spinner Animation Background', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Spinner animation background color. Default is Green."><span class="dashicons dashicons-editor-help"></span></p>
	                    		<input type="text" style="width: 130px!important;line-height:unset!important;border: 1px solid #ccc!important;max-height: 30px!important;border-top-right-radius: 0!important;border-bottom-right-radius: 0!important;" class="coderockz-woo-delivery-input-field" id="coderockz_woo_delivery_spinner_animation_background" name="coderockz_woo_delivery_spinner_animation_background" value="<?php echo (isset($other_settings['spinner_animation_background']) && !empty($other_settings['spinner_animation_background'])) ? esc_attr($other_settings['spinner_animation_background']) : "" ?>" placeholder="" autocomplete="off"/>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label"><?php _e('Add Tax to Any Delivery/Pickup Fee', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="Enable this option if you want to add tax in any delivery/pickup fee. Default is disable."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_woo_delivery_add_tax_delivery_pickup_fee">
							       <input type="checkbox" name="coderockz_woo_delivery_add_tax_delivery_pickup_fee" id="coderockz_woo_delivery_add_tax_delivery_pickup_fee" <?php echo (isset($other_settings['add_tax_delivery_pickup_fee']) && !empty($other_settings['add_tax_delivery_pickup_fee'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label"><?php _e('Hide Heading of Delivery Section From Checkout Page', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="Enable the delivery fields if there is any virtual or downloadable products in the cart. Default is disable."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_woo_delivery_hide_heading_delivery_section">
							       <input type="checkbox" name="coderockz_woo_delivery_hide_heading_delivery_section" id="coderockz_woo_delivery_hide_heading_delivery_section" <?php echo (isset($other_settings['hide_heading_delivery_section']) && !empty($other_settings['hide_heading_delivery_section'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>
	                        <div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label"><?php _e('Enable Delivery Field For Virtual Or Downloadable Products', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="Enable the delivery fields if there is any virtual or downloadable products in the cart. Default is disable."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_disable_fields_for_downloadable_products">
							       <input type="checkbox" name="coderockz_disable_fields_for_downloadable_products" id="coderockz_disable_fields_for_downloadable_products" <?php echo (isset($other_settings['disable_fields_for_downloadable_products']) && !empty($other_settings['disable_fields_for_downloadable_products'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label"><?php _e('Give Plugin Settings Access Shop Manager', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="If you want that the shop manager can do everything with the plugin settings, enable the option. Default is disable."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_woo_delivery_access_shop_manager">
							       <input type="checkbox" name="coderockz_woo_delivery_access_shop_manager" id="coderockz_woo_delivery_access_shop_manager" <?php echo (isset($other_settings['access_shop_manager']) && !empty($other_settings['access_shop_manager'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label"><?php _e('Give Delivery Calendar Access Shop Manager', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="If you want that the shop manager can do everything with the delivery calendar, enable the option. Default is disable."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_woo_delivery_calendar_access_shop_manager">
							       <input type="checkbox" name="coderockz_woo_delivery_calendar_access_shop_manager" id="coderockz_woo_delivery_calendar_access_shop_manager" <?php echo (isset($other_settings['calendar_access_shop_manager']) && !empty($other_settings['calendar_access_shop_manager'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label"><?php _e('Add Delivery Info in Order Note', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="If you want to add delivery information in order note then enable it. The feature is super handy becuase WooCommerce app doesn't allow any custom information. Default is disable."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_woo_delivery_add_delivery_info_order_note">
							       <input type="checkbox" name="coderockz_woo_delivery_add_delivery_info_order_note" id="coderockz_woo_delivery_add_delivery_info_order_note" <?php echo (isset($other_settings['add_delivery_info_order_note']) && !empty($other_settings['add_delivery_info_order_note'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label"><?php _e('Disable Changing Shipping Methods Based on Delivery/Pickup', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="The plugin is hidden Local pickup if Delivery option is selected in the Checkout and hidden all shipping methods except Local pickup if Pickup option is selected in the checkout. If you want to disable it, enable it. Default is enable."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_woo_delivery_disable_dynamic_shipping_methods">
							       <input type="checkbox" name="coderockz_woo_delivery_disable_dynamic_shipping_methods" id="coderockz_woo_delivery_disable_dynamic_shipping_methods" <?php echo (isset($other_settings['disable_dynamic_shipping']) && !empty($other_settings['disable_dynamic_shipping'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label"><?php _e('Hide Shipping Address for Pickup', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="If you want to hide the shipping address section if a customer selects the pickup then enable it. Default is disable."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_woo_delivery_hide_shipping_address">
							       <input type="checkbox" name="coderockz_woo_delivery_hide_shipping_address" id="coderockz_woo_delivery_hide_shipping_address" <?php echo (isset($other_settings['hide_shipping_address']) && !empty($other_settings['hide_shipping_address'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label"><?php _e('Mark Delivery/Pickup Completed if Order status Completed', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="If you want to mark the delivery/pickup status completed when the order marks as completed then enable the option. Default is disable."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_woo_delivery_mark_delivery_completed_with_order_completed">
							       <input type="checkbox" name="coderockz_woo_delivery_mark_delivery_completed_with_order_completed" id="coderockz_woo_delivery_mark_delivery_completed_with_order_completed" <?php echo (isset($other_settings['mark_delivery_completed_with_order_completed']) && !empty($other_settings['mark_delivery_completed_with_order_completed'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                        	<span class="coderockz-woo-delivery-form-label"><?php _e('Hide Plugin Module if Cart Total Zero', 'coderockz-woo-delivery'); ?></span>
	                        	<p class="coderockz-woo-delivery-tooltip" tooltip="If you want to hide the pluign module from the checkout page when cart total is zero, enable the option. Default is disable."><span class="dashicons dashicons-editor-help"></span></p>
							    <label class="coderockz-woo-delivery-toogle-switch" for="coderockz_woo_delivery_hide_module_cart_total_zero">
							       <input type="checkbox" name="coderockz_woo_delivery_hide_module_cart_total_zero" id="coderockz_woo_delivery_hide_module_cart_total_zero" <?php echo (isset($other_settings['hide_module_cart_total_zero']) && !empty($other_settings['hide_module_cart_total_zero'])) ? "checked" : "" ?>/>
							       <div class="coderockz-woo-delivery-toogle-slider coderockz-woo-delivery-toogle-round"></div>
							    </label>
	                    	</div>
	                        <div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_delivery_time_format"><?php _e('Field Position', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="Position of all the fields that are enabled by this plugin. Default is after order notes."><span class="dashicons dashicons-editor-help"></span></p>
	                    		<select class="coderockz-woo-delivery-select-field" name="coderockz_woo_delivery_field_position">
	                    			<option value="" <?php if(isset($other_settings['field_position']) && $other_settings['field_position'] == ""){ echo "selected"; } ?>><?php _e('Select Position', 'coderockz-woo-delivery'); ?></option>
									<option value="before_billing" <?php if(isset($other_settings['field_position']) && $other_settings['field_position'] == "before_billing"){ echo "selected"; } ?>>Before Billing Address</option>
									<option value="after_billing" <?php if(isset($other_settings['field_position']) && $other_settings['field_position'] == "after_billing"){ echo "selected"; } ?>>After Billing Address</option>
									<option value="before_shipping" <?php if(isset($other_settings['field_position']) && $other_settings['field_position'] == "before_shipping"){ echo "selected"; } ?>>Before Shipping Address</option>
									<option value="after_shipping" <?php if(isset($other_settings['field_position']) && $other_settings['field_position'] == "after_shipping"){ echo "selected"; } ?>>After Shipping Address</option>
									<option value="before_notes" <?php if(isset($other_settings['field_position']) && $other_settings['field_position'] == "before_notes"){ echo "selected"; } ?>>Before Order Notes</option>
									<option value="after_notes" <?php if(isset($other_settings['field_position']) && $other_settings['field_position'] == "after_notes"){ echo "selected"; } ?>>After Order Notes</option>
									<option value="before_payment" <?php if(isset($other_settings['field_position']) && $other_settings['field_position'] == "before_payment"){ echo "selected"; } ?>>Between Your Order And Payment Section</option>
									<option value="before_your_order" <?php if(isset($other_settings['field_position']) && $other_settings['field_position'] == "before_your_order"){ echo "selected"; } ?>>Before Your Order Section</option>
								</select>
	                    	</div>

	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label" for="coderockz_woo_delivery_additional_message"><?php _e('Additional Message Before Delivery Section', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="If you want to show any additional message to your customer then put it here. It will show before the delivery section. For Ex. We will try our best to deliver in time."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<input id="coderockz_woo_delivery_additional_message" name="coderockz_woo_delivery_additional_message" type="text" class="coderockz-woo-delivery-input-field" value="<?php echo (isset($other_settings['additional_message']) && !empty($other_settings['additional_message'])) ? stripslashes(esc_attr($other_settings['additional_message'])) : "" ?>" placeholder="" autocomplete="off"/>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Hide Additional Message For', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="If you don't want to show your customers the Additional Message for delivery or pickup, specify it here."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<select id="coderockz_woo_delivery_hide_additional_message_for" name="coderockz_woo_delivery_hide_additional_message_for[]" class="coderockz_woo_delivery_hide_additional_message_for" multiple>
                                <?php
                                $option = array("delivery"=>"Delivery", "pickup"=>"Pickup");
	                                foreach ($option as $key => $value) {
	                                	$selected = isset($other_settings['hide_additional_message_for']) && !empty($other_settings['hide_additional_message_for']) && in_array($key,$other_settings['hide_additional_message_for']) ? "selected" : "";
	                                    echo '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';		
	                                }
                                ?>
                                </select>
	                    	</div>
	                    	<div class="coderockz-woo-delivery-form-group">
	                    		<label class="coderockz-woo-delivery-form-label"><?php _e('Custom CSS', 'coderockz-woo-delivery'); ?></label>
	                    		<p class="coderockz-woo-delivery-tooltip" tooltip="If you want some custom css to avoid the plugin/theme conflict, put the css code here."><span class="dashicons dashicons-editor-help"></span></p>
	                        	<textarea id="coderockz_woo_delivery_code_editor_css" name="coderockz_woo_delivery_code_editor_css" class="coderockz-woo-delivery-textarea-field" placeholder="" autocomplete="off"><?php echo (isset($other_settings['custom_css']) && !empty($other_settings['custom_css'])) ? stripslashes(esc_attr($other_settings['custom_css'])) : "" ?>
                                </textarea>
	                    	</div>
	                        <input class="coderockz-woo-delivery-submit-btn" type="submit" name="coderockz_delivery_other_settings_form_submit" value="<?php _e('Save Changes', 'coderockz-woo-delivery'); ?>" />

	                    </form>
                	</div>

                </div>
                <div class="coderockz-woo-delivery-card">
					<p class="coderockz-woo-delivery-card-header"><?php _e('Export Plugin Settings', 'coderockz-woo-delivery'); ?></p>
					<div class="coderockz-woo-delivery-card-body">

	                    <button class="coderockz-woo-delivery-export-settings-btn"><a style="color: #fff;text-decoration: none;" target="_blank">Export Settings</a></button>

                	</div>

                </div>
                <div class="coderockz-woo-delivery-card">
					<p class="coderockz-woo-delivery-card-header"><?php _e('Import Plugin Settings', 'coderockz-woo-delivery'); ?></p>
					<div class="coderockz-woo-delivery-card-body">
						<p class="coderockz-woo-delivery-time-slot-fee-warning"><span class="dashicons dashicons-megaphone"></span><?php _e(' Before importing settings file, please reset the plugin settings first from bellow.', 'coderockz-woo-delivery'); ?></p>
						<p class="coderockz-woo-delivery-time-slot-fee-warning"><span class="dashicons dashicons-megaphone"></span><?php _e(' After importing the file, please ', 'coderockz-woo-delivery'); ?><span class="coderockz-woo-delivery-refresh-btn"><?php _e('refresh the page', 'coderockz-woo-delivery'); ?></span><?php _e(' to see the settings', 'coderockz-woo-delivery'); ?></p>
						<?php


						global $wpdb;

						// Table name
						$tablename = $wpdb->prefix."options";


						if(isset($_POST['coderockz_woo_delivery_import_settings_form_submit'])){
							// File extension
							$extension = pathinfo($_FILES['coderockz_woo_delivery_import_plugin_settings_file']['name'], PATHINFO_EXTENSION);

							// If file extension is 'csv'
							if(!empty($_FILES['coderockz_woo_delivery_import_plugin_settings_file']['name']) && $extension == 'csv'){

								// Open file in read mode
								$csvFile = fopen($_FILES['coderockz_woo_delivery_import_plugin_settings_file']['tmp_name'], 'r');

								/*fgetcsv($csvFile);*/ // Skipping header row

								// Read file
								while(($csvData = fgetcsv($csvFile)) !== FALSE) {

									/*$csvData = array_map("utf8_encode", $csvData);*/

									// Row column length
									$dataLen = count($csvData);

									// Skip row if length != 2
									if( !($dataLen == 2) ) continue;

									// Assign value to variables
									$option_name = trim($csvData[0]);
									$option_value = trim($csvData[1]);


									$option_value = str_replace('c-w-d',',',$option_value);
									
									// Check record already exists or not
									$cntSQL = "SELECT count(*) as count FROM {$tablename} where option_name='".$option_name."'";
									$record = $wpdb->get_results($cntSQL, OBJECT);

									if($record[0]->count==0){

										// Check if variable is empty or not
										if( !empty($option_name) && !empty($option_value) ) {
											// Insert Record
											$wpdb->insert($tablename, array(
											'option_name' =>$option_name,
											'option_value' =>$option_value
											));

										}

									}

								}

							} else {

							   $invalid_extension = "Please upload a CSV file";

							}

						}

						?>



						<p style="color:#ca4a1f;font-size: 20px;font-style: italic;font-weight:bold;"><?php echo (isset($invalid_extension) && !empty($invalid_extension)) ? stripslashes(esc_attr($invalid_extension)) : ""; ?></p>
	                    <form action="<?= $_SERVER['REQUEST_URI']; ?>" method="post" enctype='multipart/form-data'>
	                        <?php wp_nonce_field('coderockz_woo_delivery_nonce'); ?>
	                        <input style="margin-bottom: 20px;" type="file" name="coderockz_woo_delivery_import_plugin_settings_file" >
	                        <input class="coderockz-woo-delivery-submit-btn" type="submit" name="coderockz_woo_delivery_import_settings_form_submit" value="<?php _e('Import Settings', 'coderockz-woo-delivery'); ?>" />
	                    </form>

                	</div>

                </div>
                <div class="coderockz-woo-delivery-card">
					<p class="coderockz-woo-delivery-card-header coderockz-woo-delivery-reset-header"><?php _e('Reset Plugin Settings', 'coderockz-woo-delivery'); ?></p>
					<div class="coderockz-woo-delivery-card-body">
	                    <form action="" method="post" id ="coderockz_woo_delivery_reset_settings_form_submit">
	                        <?php wp_nonce_field('coderockz_woo_delivery_nonce'); ?>

	                        <input class="coderockz-woo-delivery-submit-btn coderockz-woo-delivery-reset-btn" type="submit" name="coderockz_woo_delivery_reset_settings_form_submit" value="<?php _e('Reset Settings', 'coderockz-woo-delivery'); ?>" />
	                    </form>
                	</div>

                </div>
			</div>
		</div>
	</div>

</div>

</div>




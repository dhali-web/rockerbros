<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://coderockz.com
 * @since      1.0.0
 *
 * @package    Coderockz_Woo_Delivery
 * @subpackage Coderockz_Woo_Delivery/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Coderockz_Woo_Delivery
 * @subpackage Coderockz_Woo_Delivery/public
 * @author     CodeRockz <admin@coderockz.com>
 */
class Coderockz_Woo_Delivery_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	public $helper;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->helper = new Coderockz_Woo_Delivery_Helper();
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Coderockz_Woo_Delivery_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Coderockz_Woo_Delivery_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		if( is_checkout() && ! ( is_wc_endpoint_url( 'order-pay' ) || is_wc_endpoint_url( 'order-received' )) ){
			$delivery_date_settings = get_option('coderockz_woo_delivery_date_settings');
			$delivery_date_calendar_theme = (isset($delivery_date_settings['calendar_theme']) && $delivery_date_settings['calendar_theme'] != "") ? $delivery_date_settings['calendar_theme'] : "";
			wp_enqueue_style( "flatpickr_css", plugin_dir_url( __FILE__ ) . 'css/flatpickr.min.css', array(), $this->version, 'all' );
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/coderockz-woo-delivery-public.css', array(), $this->version, 'all' );
			if($delivery_date_calendar_theme != "") {
				wp_enqueue_style( "flatpickr_calendar_theme_css", plugin_dir_url( __FILE__ ) .'css/calendar-themes/' . $delivery_date_calendar_theme.'.css', array(), $this->version, 'all' );
			}
		}

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Coderockz_Woo_Delivery_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Coderockz_Woo_Delivery_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		
		if( is_checkout() && ! ( is_wc_endpoint_url( 'order-pay' ) || is_wc_endpoint_url( 'order-received' )) ){
			$delivery_date_settings = get_option('coderockz_woo_delivery_date_settings');
			$delivery_date_calendar_locale = (isset($delivery_date_settings['calendar_locale']) && !empty($delivery_date_settings['calendar_locale'])) ? $delivery_date_settings['calendar_locale'] : "default";

			$pickup_date_settings = get_option('coderockz_woo_delivery_pickup_date_settings');
			$pickup_date_calendar_locale = (isset($pickup_date_settings['calendar_locale']) && !empty($pickup_date_settings['calendar_locale'])) ? $pickup_date_settings['calendar_locale'] : "default";

			wp_enqueue_script( "flatpickr_js", plugin_dir_url( __FILE__ ) . 'js/flatpickr.min.js', [], $this->version, true );

			$enable_delivery_date = (isset($delivery_date_settings['enable_delivery_date']) && !empty($delivery_date_settings['enable_delivery_date'])) ? $delivery_date_settings['enable_delivery_date'] : false;

			if($enable_delivery_date) {
				wp_enqueue_script( "flatpickr_locale_js", plugin_dir_url( __FILE__ ) . 'js/calendar_locale/'.$delivery_date_calendar_locale.'.js', ["flatpickr_js"], $this->version, true );
			}
			
			$enable_pickup_date = (isset($pickup_date_settings['enable_pickup_date']) && !empty($pickup_date_settings['enable_pickup_date'])) ? $pickup_date_settings['enable_pickup_date'] : false;

			if($enable_pickup_date) {
				wp_enqueue_script( "flatpickr_pickup_locale_js", plugin_dir_url( __FILE__ ) . 'js/calendar_locale/'.$pickup_date_calendar_locale.'.js', ["flatpickr_js"], $this->version, true );
			}

			$theme_name = esc_html( wp_get_theme()->get( 'Name' ) );

			if(strpos($theme_name,"Flatsome") !== false) {

				wp_enqueue_script( "select2_js", plugin_dir_url( __FILE__ ) . 'js/select2.coderockz.delivery.min.js', array('jquery'), $this->version, true );

				if(wp_script_is('flatpickr_locale_js') && !wp_script_is('flatpickr_pickup_locale_js')) {
					wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/coderockz-woo-delivery-public-flatsome.js', array( 'jquery','select2_js', 'flatpickr_js', 'flatpickr_locale_js' ), $this->version, true );
				} elseif(!wp_script_is('flatpickr_locale_js') && wp_script_is('flatpickr_pickup_locale_js')) {
					wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/coderockz-woo-delivery-public-flatsome.js', array( 'jquery','select2_js', 'flatpickr_js','flatpickr_pickup_locale_js' ), $this->version, true );
				} elseif(wp_script_is('flatpickr_locale_js') && wp_script_is('flatpickr_pickup_locale_js')) {
					wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/coderockz-woo-delivery-public-flatsome.js', array( 'jquery','select2_js', 'flatpickr_js', 'flatpickr_locale_js','flatpickr_pickup_locale_js'), $this->version, true );
				} else {
					wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/coderockz-woo-delivery-public-flatsome.js', array( 'jquery','select2_js', 'flatpickr_js'), $this->version, true );
				}

			} else {

				wp_enqueue_script( "selectWoo_js", plugin_dir_url( __FILE__ ) . 'js/selectWoo.coderockz.delivery.min.js', array('jquery'), $this->version, true );

				if(wp_script_is('flatpickr_locale_js') && !wp_script_is('flatpickr_pickup_locale_js')) {
					wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/coderockz-woo-delivery-public.js', array( 'jquery','selectWoo_js', 'flatpickr_js', 'flatpickr_locale_js' ), $this->version, true );
				} elseif(!wp_script_is('flatpickr_locale_js') && wp_script_is('flatpickr_pickup_locale_js')) {
					wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/coderockz-woo-delivery-public.js', array( 'jquery','selectWoo_js', 'flatpickr_js','flatpickr_pickup_locale_js' ), $this->version, true );
				} elseif(wp_script_is('flatpickr_locale_js') && wp_script_is('flatpickr_pickup_locale_js')) {
					wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/coderockz-woo-delivery-public.js', array( 'jquery','selectWoo_js', 'flatpickr_js', 'flatpickr_locale_js','flatpickr_pickup_locale_js'), $this->version, true );
				} else {
					wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/coderockz-woo-delivery-public.js', array( 'jquery','selectWoo_js', 'flatpickr_js'), $this->version, true );
				}

				
			}

			
		}

		/*if( is_checkout() && ( is_wc_endpoint_url( 'order-received' )) ){
			wp_enqueue_script( 'google_calenar_integration.js', plugin_dir_url( __FILE__ ) . 'js/google_calenar_integration.js', array( 'jquery'), $this->version, true );
		}*/
		$coderockz_woo_delivery_nonce = wp_create_nonce('coderockz_woo_delivery_nonce');
        wp_localize_script($this->plugin_name, 'coderockz_woo_delivery_ajax_obj', array(
            'coderockz_woo_delivery_ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => $coderockz_woo_delivery_nonce,
        ));

	}

	// This function adds the delivery time and delivery date fields and it's functionalities
	public function coderockz_woo_delivery_add_custom_field() {

		/* Start of Extra Code to adjust pickup date */

		if(get_option('coderockz_woo_delivery_cutoff_time_extension') == false) {

	    	$delivery_time_settings = get_option('coderockz_woo_delivery_time_settings');

	    	$store_closing_time = isset($delivery_time_settings['store_closing_time']) ? (int)$delivery_time_settings['store_closing_time'] : "";
	    	$extended_closing_days = (isset($delivery_time_settings['extended_closing_days']) && !empty($delivery_time_settings['extended_closing_days'])) ? (int)$delivery_time_settings['extended_closing_days'] : '';
	    	$enable_closing_time = (isset($delivery_time_settings['enable_closing_time']) && !empty($delivery_time_settings['enable_closing_time'])) ? $delivery_time_settings['enable_closing_time'] : false;

	    	$temporary_pickup_cutoff = [];
	    	$temporary_pickup_cutoff['enable_closing_time'] = $enable_closing_time;
	    	$temporary_pickup_cutoff['store_closing_time'] = $store_closing_time;
	    	$temporary_pickup_cutoff['extended_closing_days'] = $extended_closing_days;

			if(get_option('coderockz_woo_delivery_pickup_settings') == false) {
				update_option('coderockz_woo_delivery_pickup_settings', $temporary_pickup_cutoff);
			} else {
				$temporary_pickup_cutoff = array_merge(get_option('coderockz_woo_delivery_pickup_settings'),$temporary_pickup_cutoff);
				update_option('coderockz_woo_delivery_pickup_settings', $temporary_pickup_cutoff);
			}

			update_option('coderockz_woo_delivery_cutoff_time_extension','completed');
		}

		if(get_option('coderockz_woo_delivery_completed_zone_offdays_extension') == false) {

	    	$offdays_settings = get_option('coderockz_woo_delivery_off_days_settings');

			if($offdays_settings && isset($offdays_settings['zone_wise_offdays']) && !empty($offdays_settings['zone_wise_offdays'])) {
				$custom_settings['zone_wise_offdays'] = [];
				$temporary_zone_wise_offdays = $offdays_settings['zone_wise_offdays'];
				unset($offdays_settings['zone_wise_offdays']);
				$custom_settings['zone_wise_offdays']['both'] = $temporary_zone_wise_offdays;

				$custom_settings = array_merge($offdays_settings,$custom_settings);
				update_option('coderockz_woo_delivery_off_days_settings', $custom_settings);
			}

			update_option('coderockz_woo_delivery_completed_zone_offdays_extension','completed');
		}

		if(get_option('coderockz_woo_delivery_completed_category_offdays_extension') == false) {

			$offdays_settings = get_option('coderockz_woo_delivery_off_days_settings');

			if($offdays_settings && isset($offdays_settings['category_wise_offdays']) && !empty($offdays_settings['category_wise_offdays'])) {
				$custom_settings['category_wise_offdays'] = [];
				$temporary_category_wise_offdays = $offdays_settings['category_wise_offdays'];
				unset($offdays_settings['category_wise_offdays']);
				foreach($temporary_category_wise_offdays as $category => $temporary) {
					$custom_settings['category_wise_offdays'][$category]['weekday_offdays'] = $temporary;
				}
				
				$custom_settings = array_merge($offdays_settings,$custom_settings);
				update_option('coderockz_woo_delivery_off_days_settings', $custom_settings);
			}

			update_option('coderockz_woo_delivery_completed_category_offdays_extension','completed');
		}

		if(get_option('coderockz_woo_delivery_date_time_change')) {
			delete_option('coderockz_woo_delivery_date_time_change');
		}

		if(get_option('coderockz_woo_delivery_different_pickup_off_days')) {
			delete_option('coderockz_woo_delivery_different_pickup_off_days');
		}

		if(get_option('coderockz_woo_delivery_different_closing_time')) {
			delete_option('coderockz_woo_delivery_different_closing_time');
		}

		if(get_option('coderockz_woo_delivery_recoding_pickup_location')) {
			delete_option('coderockz_woo_delivery_recoding_pickup_location');
		}

		if(get_option('coderockz_woo_delivery_different_access_shop_manager')) {
			delete_option('coderockz_woo_delivery_different_access_shop_manager');
		}

		/*error_log(WC()->cart->get_cart_discount_total());
		error_log(WC()->cart->get_cart_discount_tax_total());*/

		
		/* End of Extra Code to adjust pickup date */

		//unset the plugin session & cookie first

		if(!is_null(WC()->session)) {		  
			WC()->session->__unset( 'selected_delivery_time_conditional' );  
		}

		if(isset($_COOKIE['coderockz_woo_delivery_option_time_pickup'])) {
		    unset($_COOKIE['coderockz_woo_delivery_option_time_pickup']);
			setcookie("coderockz_woo_delivery_option_time_pickup", null, -1, '/');
		} elseif(!is_null(WC()->session)) {		  
			WC()->session->__unset( 'coderockz_woo_delivery_option_time_pickup' );  
		}

		if(isset($_COOKIE['coderockz_woo_delivery_find_conditional_shipping_method'])) {
		    unset($_COOKIE["coderockz_woo_delivery_find_conditional_shipping_method"]);
			setcookie("coderockz_woo_delivery_find_conditional_shipping_method", null, -1, '/');
		} elseif(!is_null(WC()->session)) {		  
			WC()->session->__unset( 'coderockz_woo_delivery_find_conditional_shipping_method' );  
		}

		if(isset($_COOKIE['coderockz_woo_delivery_find_only_conditional_shipping_method'])) {
		    unset($_COOKIE["coderockz_woo_delivery_find_only_conditional_shipping_method"]);
			setcookie("coderockz_woo_delivery_find_only_conditional_shipping_method", null, -1, '/');
		} elseif(!is_null(WC()->session)) {		  
			WC()->session->__unset( 'coderockz_woo_delivery_find_only_conditional_shipping_method' );  
		}

		if(isset($_COOKIE['coderockz_woo_delivery_find_only_conditional_shipping_method_state'])) {
		    unset($_COOKIE["coderockz_woo_delivery_find_only_conditional_shipping_method_state"]);
			setcookie("coderockz_woo_delivery_find_only_conditional_shipping_method_state", null, -1, '/');
		} elseif(!is_null(WC()->session)) {		  
			WC()->session->__unset( 'coderockz_woo_delivery_find_only_conditional_shipping_method_state' );  
		}

		if(isset($_COOKIE['coderockz_woo_delivery_available_shipping_methods'])) {
		    unset($_COOKIE["coderockz_woo_delivery_available_shipping_methods"]);
			setcookie("coderockz_woo_delivery_available_shipping_methods", null, -1, '/');
		} elseif(!is_null(WC()->session)) {		  
			WC()->session->__unset( 'coderockz_woo_delivery_available_shipping_methods' );  
		}

		if(!is_null(WC()->session)) {
			WC()->session->__unset( 'selected_delivery_date' );
			WC()->session->__unset( 'selected_delivery_time' );
			WC()->session->__unset( 'selected_pickup_date' );
			WC()->session->__unset( 'selected_pickup_time' );
			WC()->session->__unset( 'selected_order_type' );
		}

		// retrieving the data for delivery time
		$delivery_time_settings = get_option('coderockz_woo_delivery_time_settings');
		$pickup_time_settings = get_option('coderockz_woo_delivery_pickup_settings');
		$other_settings = get_option('coderockz_woo_delivery_other_settings');
		$delivery_option_settings = get_option('coderockz_woo_delivery_option_delivery_settings');
		$localization_settings = get_option('coderockz_woo_delivery_localization_settings');

		// if any timezone data is saved, set default timezone with the data
		$timezone = $this->helper->get_the_timezone();
		date_default_timezone_set($timezone);
		$today = date('Y-m-d', time());

		$disable_fields_for_downloadable_products = (isset($other_settings['disable_fields_for_downloadable_products']) && !empty($other_settings['disable_fields_for_downloadable_products'])) ? $other_settings['disable_fields_for_downloadable_products'] : false;

		$has_virtual_downloadable_products = $this->helper->check_virtual_downloadable_products();

		$exclude_condition = $this->helper->detect_exclude_condition();

		$cart_total = WC()->cart->get_cart_contents_total();

		$hide_module_cart_total_zero = (isset($other_settings['hide_module_cart_total_zero']) && !empty($other_settings['hide_module_cart_total_zero'])) ? $other_settings['hide_module_cart_total_zero'] : false;

		if($hide_module_cart_total_zero && $cart_total == 0) {
			$cart_total_zero = true;
		} else {
			$cart_total_zero = false;
		}


		$exclude_user_roles_condition = $this->helper->detect_exclude_user_roles_condition();

		
		if( !$exclude_user_roles_condition && !$cart_total_zero && !$exclude_condition && (!$has_virtual_downloadable_products || $disable_fields_for_downloadable_products)) {
		
		$exclude_settings = get_option('coderockz_woo_delivery_exclude_settings');

		$exclude_shipping_methods = (isset($exclude_settings['exclude_shipping_methods']) && !empty($exclude_settings['exclude_shipping_methods'])) ? $exclude_settings['exclude_shipping_methods'] : array();

		$exclude_shipping_method_title = (isset($exclude_settings['exclude_shipping_method_title']) && !empty($exclude_settings['exclude_shipping_method_title'])) ? $exclude_settings['exclude_shipping_method_title'] : array();

		$exclude_shipping_methods = array_filter(array_merge($exclude_shipping_methods, $exclude_shipping_method_title), 'strlen');

		/*$exclude_shipping_methods = array_map('stripslashes', $exclude_shipping_methods);*/

		$spinner_animation_id = (isset($other_settings['spinner-animation-id']) && !empty($other_settings['spinner-animation-id'])) ? $other_settings['spinner-animation-id'] : "";

		if($spinner_animation_id != "") {

			$spinner_url = wp_get_attachment_image_src($spinner_animation_id,'full', true);
			$full_size_spinner_animation_path = $spinner_url[0];
		} else {
			$full_size_spinner_animation_path = CODEROCKZ_WOO_DELIVERY_URL.'public/images/loading.gif';
		}

		$spinner_animation_background = (isset($other_settings['spinner_animation_background']) && !empty($other_settings['spinner_animation_background'])) ? $this->helper->hex2rgb($other_settings['spinner_animation_background']) : array('red' => 220, 'green' => 220, 'blue' => 220);


		$remove_shipping_address = (isset($other_settings['hide_shipping_address']) && $other_settings['hide_shipping_address'] != "") ? $other_settings['hide_shipping_address'] : false;

		$shipping_state_zip_wise_offdays = false;
		$offdays_settings = get_option('coderockz_woo_delivery_off_days_settings');
		if((isset($offdays_settings['state_wise_offdays']) && !empty($offdays_settings['state_wise_offdays'])) || (isset($offdays_settings['postcode_wise_offdays']) && !empty($offdays_settings['postcode_wise_offdays'])) || ((isset($offdays_settings['zone_wise_offdays']['both']) && !empty($offdays_settings['zone_wise_offdays']['both'])) || (isset($offdays_settings['zone_wise_offdays']['delivery']) && !empty($offdays_settings['zone_wise_offdays']['delivery'])) || (isset($offdays_settings['zone_wise_offdays']['pickup']) && !empty($offdays_settings['zone_wise_offdays']['pickup'])))) {
			$shipping_state_zip_wise_offdays = true;
		}

		$additional_field_settings = get_option('coderockz_woo_delivery_additional_field_settings');

		$enable_additional_field = (isset($additional_field_settings['enable_additional_field']) && !empty($additional_field_settings['enable_additional_field'])) ? $additional_field_settings['enable_additional_field'] : false;

		$hide_additional_field_for = (isset($additional_field_settings['hide_additional_field_for']) && !empty($additional_field_settings['hide_additional_field_for'])) ? $additional_field_settings['hide_additional_field_for'] : array();

		if($enable_additional_field && count($hide_additional_field_for) > 0) {
			$hide_additional_field_for = $hide_additional_field_for;
		} else {
			$hide_additional_field_for = array();
		}

		$enable_delivery_restriction = (isset($delivery_option_settings['enable_delivery_restriction']) && !empty($delivery_option_settings['enable_delivery_restriction'])) ? $delivery_option_settings['enable_delivery_restriction'] : false;

		$delivery_restriction_amount = (isset($delivery_option_settings['minimum_amount_cart_restriction']) && $delivery_option_settings['minimum_amount_cart_restriction'] != "") ? (float)$delivery_option_settings['minimum_amount_cart_restriction'] : "";

		$enable_pickup_restriction = (isset($delivery_option_settings['enable_pickup_restriction']) && !empty($delivery_option_settings['enable_pickup_restriction'])) ? $delivery_option_settings['enable_pickup_restriction'] : false;

		$pickup_restriction_amount = (isset($delivery_option_settings['minimum_amount_cart_restriction_pickup']) && $delivery_option_settings['minimum_amount_cart_restriction_pickup'] != "") ? (float)$delivery_option_settings['minimum_amount_cart_restriction_pickup'] : "";

		$hide_additional_message_for = (isset($other_settings['hide_additional_message_for']) && !empty($other_settings['hide_additional_message_for'])) ? $other_settings['hide_additional_message_for'] : array();
		//NEW NEW
		$delivery_fee_settings = get_option('coderockz_woo_delivery_fee_settings');

		$enable_conditional_delivery_fee = isset($delivery_fee_settings['enable_conditional_delivery_fee']) && !empty($delivery_fee_settings['enable_conditional_delivery_fee']) ? $delivery_fee_settings['enable_conditional_delivery_fee'] : false;

		$conditional_delivery_shipping_method = false;
		if($enable_conditional_delivery_fee && (isset($delivery_fee_settings['conditional_delivery_time_starts']) && ($delivery_fee_settings['conditional_delivery_time_starts'] !='' || $delivery_fee_settings['conditional_delivery_time_starts'] == 0)) && (isset($delivery_fee_settings['conditional_delivery_time_ends']) && $delivery_fee_settings['conditional_delivery_time_ends'] !='') && (isset($delivery_fee_settings['conditional_delivery_fee_duration']) && !empty($delivery_fee_settings['conditional_delivery_fee_duration'])) && (isset($delivery_fee_settings['conditional_delivery_shipping_method']) && !empty($delivery_fee_settings['conditional_delivery_shipping_method'])) ) { 
			$conditional_delivery_shipping_method = true;
		}



		//NEW NEW

		$disable_dynamic_shipping = (isset($other_settings['disable_dynamic_shipping']) && !empty($other_settings['disable_dynamic_shipping'])) ? $other_settings['disable_dynamic_shipping'] : false;
		
		echo "<div data-today_date='".$today."' data-disable_dynamic_shipping='".$disable_dynamic_shipping."' data-conditional_delivery_shipping_method='".$conditional_delivery_shipping_method."' data-delivery_restriction_amount='".$delivery_restriction_amount."' data-enable_delivery_restriction='".$enable_delivery_restriction."' data-pickup_restriction_amount='".$pickup_restriction_amount."' data-enable_pickup_restriction='".$enable_pickup_restriction."' data-shipping_state_zip_wise_offdays='".$shipping_state_zip_wise_offdays."' data-remove_shipping_address='".$remove_shipping_address."' data-animation_background='".json_encode($spinner_animation_background)."' data-animation_path='".$full_size_spinner_animation_path."' data-hide_additional_field_for='".json_encode($hide_additional_field_for)."' data-hide_additional_message_for='".json_encode($hide_additional_message_for)."' data-exclude_shipping_methods='".json_encode($exclude_shipping_methods)."' id='coderockz_woo_delivery_setting_wrapper'>";

		echo "<div id='coderockz_woo_delivery_setting_wrapper_internal'>";	

		$hide_heading_delivery_section = (isset($other_settings['hide_heading_delivery_section']) && $other_settings['hide_heading_delivery_section'] != "") ? $other_settings['hide_heading_delivery_section'] : false;

		
		if(!$hide_heading_delivery_section) {
			
			$delivery_heading_checkout = (isset($localization_settings['delivery_heading_checkout']) && !empty($localization_settings['delivery_heading_checkout'])) ? stripslashes($localization_settings['delivery_heading_checkout']) : __( "Delivery Information", 'coderockz-woo-delivery' );

			echo "<div style='display:none;' id='coderockz-woo-delivery-public-delivery-details'>";
			echo "<h3 style='margin-bottom:0;padding: 20px 0;'>".__($delivery_heading_checkout, 'coderockz-woo-delivery')."</h3>";
			echo "</div>";
		}

		$additional_message = isset($other_settings['additional_message']) && $other_settings['additional_message'] != "" ? stripslashes(html_entity_decode($other_settings['additional_message'])) : "";

		if($additional_message != "") {
			echo '<p style="margin:10px 0;display:none;" class="coderockz_woo_delivery_additional_message"><small>'.__($additional_message, 'coderockz-woo-delivery').'</small></p>';
		}
		
		$enable_delivery_option = (isset($delivery_option_settings['enable_option_time_pickup']) && !empty($delivery_option_settings['enable_option_time_pickup'])) ? $delivery_option_settings['enable_option_time_pickup'] : false;
		$delivery_option_field_label = (isset($delivery_option_settings['delivery_option_label']) && !empty($delivery_option_settings['delivery_option_label'])) ? stripslashes($delivery_option_settings['delivery_option_label']) : __( "Order Type", 'coderockz-woo-delivery' );
		$delivery_field_label = (isset($delivery_option_settings['delivery_label']) && !empty($delivery_option_settings['delivery_label'])) ? stripslashes($delivery_option_settings['delivery_label']) : __( "Delivery", 'coderockz-woo-delivery' );
		$pickup_field_label = (isset($delivery_option_settings['pickup_label']) && !empty($delivery_option_settings['pickup_label'])) ? stripslashes($delivery_option_settings['pickup_label']) : __( "Pickup", 'coderockz-woo-delivery' );
		$no_result_notice = (isset($delivery_option_settings['no_result_notice']) && !empty($delivery_option_settings['no_result_notice'])) ? stripslashes($delivery_option_settings['no_result_notice']) : __( "No Delivery or Pickup", 'coderockz-woo-delivery' );

		$chosen_methods = WC()->session->get( 'chosen_shipping_methods' );
 		
		if(isset($chosen_methods)){
			$chosen_shipping = $chosen_methods[0];
			if($chosen_shipping != "") {
				if(strpos($chosen_shipping, "local_pickup")!==false) {
		 			$default_selection = "pickup";	
		 		} else {
					$default_selection = "delivery";
		 		}
			} else {
				$default_selection = "";
			}
	 		
		} else {
			$default_selection = (isset($delivery_option_settings['pre_selected_order_type']) && !empty($delivery_option_settings['pre_selected_order_type'])) ? stripslashes($delivery_option_settings['pre_selected_order_type']) : "";
		}
 		

		if($enable_delivery_option) {
			echo '<div id="coderockz_woo_delivery_delivery_selection_field" style="display:none;">';
				woocommerce_form_field('coderockz_woo_delivery_delivery_selection_box',
				[
					'type' => 'select',
					'class' => [
						'coderockz_woo_delivery_delivery_selection_box form-row-wide'
					],
					'label' => __($delivery_option_field_label, 'coderockz-woo-delivery'),
					'placeholder' => __($delivery_option_field_label, 'coderockz-woo-delivery'),
				    'options' => Coderockz_Woo_Delivery_Delivery_Option::delivery_option($delivery_option_settings),
					'required' => true,
					'default' => $default_selection,
					'custom_attributes' => [
						'data-no_result_notice' => __($no_result_notice, 'coderockz-woo-delivery'),
					],
				], WC()->checkout->get_value('coderockz_woo_delivery_delivery_selection_box'));
			echo '</div>';
		}

		$delivery_date_settings = get_option('coderockz_woo_delivery_date_settings');
		$pickup_date_settings = get_option('coderockz_woo_delivery_pickup_date_settings');
		$processing_days_settings = get_option('coderockz_woo_delivery_processing_days_settings');
		$processing_time_settings = get_option('coderockz_woo_delivery_processing_time_settings');
		$offdays_settings = get_option('coderockz_woo_delivery_off_days_settings');
		$opendays_settings = get_option('coderockz_woo_delivery_open_days_settings');

		$enable_delivery_date = (isset($delivery_date_settings['enable_delivery_date']) && !empty($delivery_date_settings['enable_delivery_date'])) ? $delivery_date_settings['enable_delivery_date'] : false;
		$enable_pickup_date = (isset($pickup_date_settings['enable_pickup_date']) && !empty($pickup_date_settings['enable_pickup_date'])) ? $pickup_date_settings['enable_pickup_date'] : false;

		
		/*Bring $max_processing_time calculation here because need in both day and time */
		$max_processing_time_array = [];
		
		$enable_category_processing_time = (isset($processing_time_settings['enable_category_wise_processing_time']) && !empty($processing_time_settings['enable_category_wise_processing_time'])) ? $processing_time_settings['enable_category_wise_processing_time'] : false;
		$category_processing_time = (isset($processing_time_settings['category_processing_time']) && !empty($processing_time_settings['category_processing_time'])) ? $processing_time_settings['category_processing_time'] : array();

		$max_processing_days_array = [];

		$enable_category_processing_days = (isset($processing_days_settings['enable_category_wise_processing_days']) && !empty($processing_days_settings['enable_category_wise_processing_days'])) ? $processing_days_settings['enable_category_wise_processing_days'] : false;

		$category_processing_days = (isset($processing_days_settings['category_processing_days']) && !empty($processing_days_settings['category_processing_days'])) ? $processing_days_settings['category_processing_days'] : array();
		
		$category_wise_offdays = (isset($offdays_settings['category_wise_offdays']) && !empty($offdays_settings['category_wise_offdays'])) ? $offdays_settings['category_wise_offdays'] : array();

		$opendays_categories = (isset($opendays_settings['category_open_days']) && !empty($opendays_settings['category_open_days'])) ? $opendays_settings['category_open_days'] : array();

		$next_month_off_categories = (isset($offdays_settings['next_month_off_categories']) && !empty($offdays_settings['next_month_off_categories'])) ? $offdays_settings['next_month_off_categories'] : array();
		$next_week_off_categories = (isset($offdays_settings['next_week_off_categories']) && !empty($offdays_settings['next_week_off_categories'])) ? $offdays_settings['next_week_off_categories'] : array();

		$exclude_category_processing_time = (isset($processing_time_settings['exclude_categories']) && !empty($processing_time_settings['exclude_categories'])) ? $processing_time_settings['exclude_categories'] : array();
		$exclude_product_processing_time = (isset($processing_time_settings['exclude_products']) && !empty($processing_time_settings['exclude_products'])) ? $processing_time_settings['exclude_products'] : array();

		$exclude_category_processing_days = (isset($processing_days_settings['exclude_categories']) && !empty($processing_days_settings['exclude_categories'])) ? $processing_days_settings['exclude_categories'] : array();
		$exclude_product_processing_days = (isset($processing_days_settings['exclude_products']) && !empty($processing_days_settings['exclude_products'])) ? $processing_days_settings['exclude_products'] : array();

		if(($enable_category_processing_days && !empty($category_processing_days)) || ($enable_category_processing_time && !empty($category_processing_time)) || (!empty($category_wise_offdays)) || (!empty($opendays_categories)) || (!empty($next_month_off_categories)) || (!empty($next_week_off_categories)) || (!empty($exclude_product_processing_time) || !empty($exclude_category_processing_time)) || (!empty($exclude_product_processing_days) || !empty($exclude_category_processing_days))) {
			$checkout_product_categories = $this->helper->checkout_product_categories();
		}

		$enable_product_processing_days = (isset($processing_days_settings['enable_product_wise_processing_days']) && !empty($processing_days_settings['enable_product_wise_processing_days'])) ? $processing_days_settings['enable_product_wise_processing_days'] : false;

		$product_processing_days = (isset($processing_days_settings['product_processing_days']) && !empty($processing_days_settings['product_processing_days'])) ? $processing_days_settings['product_processing_days'] : array();

		$enable_product_processing_time = (isset($processing_time_settings['enable_product_wise_processing_time']) && !empty($processing_time_settings['enable_product_wise_processing_time'])) ? $processing_time_settings['enable_product_wise_processing_time'] : false;

		$product_processing_time = (isset($processing_time_settings['product_processing_time']) && !empty($processing_time_settings['product_processing_time'])) ? $processing_time_settings['product_processing_time'] : array();

		$product_wise_offdays = (isset($offdays_settings['product_wise_offdays']) && !empty($offdays_settings['product_wise_offdays'])) ? $offdays_settings['product_wise_offdays'] : array();


		if(($enable_product_processing_days && !empty($product_processing_days)) || ($enable_product_processing_time && !empty($product_processing_time)) || (!empty($product_wise_offdays)) || (!empty($exclude_product_processing_time) || !empty($exclude_category_processing_time)) || (!empty($exclude_product_processing_days) || !empty($exclude_category_processing_days))) {
			$product_id = $this->helper->checkout_product_id();
		}

		if($enable_category_processing_time && !empty($category_processing_time)) {

			foreach ($category_processing_time as $key => $value)
			{
				if(in_array(stripslashes(strtolower($key)), $checkout_product_categories))
				{
					array_push($max_processing_time_array,(int)$value);
				}
			}
		}
		
		$enable_product_processing_time_quantity = (isset($processing_time_settings['enable_product_processing_time_quantity']) && !empty($processing_time_settings['enable_product_processing_time_quantity'])) ? $processing_time_settings['enable_product_processing_time_quantity'] : false;	

		if($enable_product_processing_time && !empty($product_processing_time)) {
			
			foreach ($product_processing_time as $key => $value)
			{
				if(in_array($key, $product_id))
				{
					
					if($enable_product_processing_time_quantity) {
						foreach ( WC()->cart->get_cart() as $cart_item ) { 
						    if($cart_item['product_id'] == $key || (isset($cart_item['variation_id']) && $cart_item['variation_id'] == $key)){
						        $qty =  $cart_item['quantity'];
						        break;
						    }
						}
						array_push($max_processing_time_array,(int)$value * $qty * count(array_keys($product_id, $key)));
					} else {
						array_push($max_processing_time_array,(int)$value);
					}
					
				}
			}
		} 

		if (isset($processing_time_settings['overall_processing_time']) && !empty($processing_time_settings['overall_processing_time']) && empty($max_processing_time_array)) {

			if(empty($exclude_category_processing_time) && empty($exclude_product_processing_time)) {
				array_push($max_processing_time_array,(int)$processing_time_settings['overall_processing_time']);
			} else {
				$exclude_category_processing_time_array = [];
				foreach ($exclude_category_processing_time as $key => $value) {
					$exclude_category_processing_time_array [] = stripslashes(strtolower($value));	
				}

				$exclude_category_processing_time_condition = (count(array_intersect($checkout_product_categories, $exclude_category_processing_time_array)) <= count($checkout_product_categories)) && count(array_intersect($checkout_product_categories, $exclude_category_processing_time_array))>0;

				$exclude_product_processing_time_condition = (count(array_intersect($product_id, $exclude_product_processing_time)) <= count($product_id)) && count(array_intersect($product_id, $exclude_product_processing_time))>0;

				if($exclude_category_processing_time_condition && !$exclude_product_processing_time_condition) {
					$exclude_condition_processing_time = true;
					if(count($checkout_product_categories) > 1 && count($product_id) > 1 && count(array_diff($checkout_product_categories, $exclude_category_processing_time_array))>0) {
		  				$exclude_condition_processing_time = !$exclude_condition_processing_time;
		  			}
				} elseif($exclude_product_processing_time_condition && !$exclude_category_processing_time_condition) {
					$exclude_condition_processing_time = true;
					if(count($product_id) > 1 && count(array_diff($product_id, $exclude_product_processing_time))>0) {
		  				$exclude_condition_processing_time = !$exclude_condition_processing_time;
		  			}
				} elseif($exclude_product_processing_time_condition && $exclude_category_processing_time_condition) {
					$exclude_condition_processing_time = true;					
					foreach($product_id as $product) {
						
						if(!in_array($product, $exclude_product_processing_time)) {
							
							$term_lists = wp_get_post_terms($product,'product_cat',array('fields'=>'ids'));
							foreach($term_lists as $cat_id) {
								$term = get_term_by( 'id', $cat_id, 'product_cat' );
								if(!in_array(stripslashes(strtolower(htmlspecialchars_decode($term->name))), $exclude_category_processing_time_array)) {
									$exclude_condition_processing_time = !$exclude_condition_processing_time;
									break;
								}
							}
						}
					}
				} else {
					$exclude_condition_processing_time = false;
				}

				if(!$exclude_condition_processing_time) {
					array_push($max_processing_time_array,(int)$processing_time_settings['overall_processing_time']);
				}
			}
		}

		$max_processing_time = count($max_processing_time_array) > 0 ? max($max_processing_time_array) : 0;

		$disable_dates = [];
		$pickup_disable_dates = [];

		$current_time = (date("G")*60)+date("i");

		$last_processing_time_date = "";

		if($max_processing_time>0){
			$max_processing_time_with_current = $current_time+$max_processing_time;
			if($max_processing_time_with_current>=1440) {
				$x = 1440;
				$date = $today;
				$days_from_processing_time =0;
				while($max_processing_time_with_current>=$x) {
					$second_time = $max_processing_time_with_current - $x;
					$formated = date('Y-m-d H:i:s', strtotime($date));
					$formated_obj = new DateTime($formated);
					$processing_time_date = $formated_obj->modify("+".$days_from_processing_time." day")->format("Y-m-d");
					$last_processing_time_date = $processing_time_date;
					$disable_dates[] = $processing_time_date;
					$pickup_disable_dates[] = $processing_time_date;
					$max_processing_time_with_current = $second_time;
					$max_processing_time = $second_time;
					$days_from_processing_time = $days_from_processing_time+1;
				}

				$formated_last_processing = date('Y-m-d H:i:s', strtotime($last_processing_time_date));
				$formated_obj_last_processing = new DateTime($formated_last_processing);
				$last_processing_time_date = $formated_obj_last_processing->modify("+1 day")->format("Y-m-d");
			} else {
				$last_processing_time_date = $today;
			}
		}

		$consider_off_days = (isset($processing_days_settings['processing_days_consider_off_days']) && !empty($processing_days_settings['processing_days_consider_off_days'])) ? $processing_days_settings['processing_days_consider_off_days'] : false;
		$consider_weekends = (isset($processing_days_settings['processing_days_consider_weekends']) && !empty($processing_days_settings['processing_days_consider_weekends'])) ? $processing_days_settings['processing_days_consider_weekends'] : false;
		$consider_current_day = (isset($processing_days_settings['processing_days_consider_current_day']) && !empty($processing_days_settings['processing_days_consider_current_day'])) ? $processing_days_settings['processing_days_consider_current_day'] : false;

		$overall_processing_days = (isset($processing_days_settings['overall_processing_days']) && $processing_days_settings['overall_processing_days'] != "") ? $processing_days_settings['overall_processing_days'] : "0";

		if($enable_category_processing_days && !empty($category_processing_days)) {

			foreach ($category_processing_days as $key => $value)
			{
				if(in_array(stripslashes(strtolower($key)), $checkout_product_categories))
				{
					array_push($max_processing_days_array,(int)$value);
				}
			}
		}
		
		$enable_product_processing_day_quantity = (isset($processing_days_settings['enable_product_processing_day_quantity']) && !empty($processing_days_settings['enable_product_processing_day_quantity'])) ? $processing_days_settings['enable_product_processing_day_quantity'] : false;

		
		if($enable_product_processing_days && !empty($product_processing_days)) {

			foreach ($product_processing_days as $key => $value)
			{
				if(in_array($key, $product_id))
				{

					if($enable_product_processing_day_quantity) {
						foreach ( WC()->cart->get_cart() as $cart_item ) { 
						    if($cart_item['product_id'] == $key || (isset($cart_item['variation_id']) && $cart_item['variation_id'] == $key)){
						        $qty =  $cart_item['quantity'];
						        break;
						    }
						}

						array_push($max_processing_days_array,(int)$value * $qty * count(array_keys($product_id, $key)));
					} else {
						array_push($max_processing_days_array,(int)$value);
					}

				}
			}
		}
		

		/*$max_processing_days_array[] = (int)$overall_processing_days;*/

		$backorder_item = false;

	    foreach( WC()->cart->get_cart() as $cart_item ) {
	        if( $cart_item['data']->is_on_backorder( $cart_item['quantity'] ) ) {
	            $backorder_item = true;
	            break;
	        }
	    }

	    if($backorder_item) {
	    	$backorder_processing_days = (isset($processing_days_settings['backorder_processing_days']) && $processing_days_settings['backorder_processing_days'] != "") ? $processing_days_settings['backorder_processing_days'] : "0";
	    	/*$max_processing_days_array[] = (int)$backorder_processing_days;*/
	    }

	    /*if ( && empty($max_processing_days_array)) {*/

	    	if($overall_processing_days !== "0" && empty($exclude_category_processing_days) && empty($exclude_product_processing_days)) {
				array_push($max_processing_days_array,(int)$overall_processing_days);
			} elseif(isset($backorder_processing_days) && empty($exclude_category_processing_days) && empty($exclude_product_processing_days)) {
				array_push($max_processing_days_array,(int)$backorder_processing_days);
			} elseif(!empty($exclude_category_processing_days) || !empty($exclude_product_processing_days)) {
				$exclude_category_processing_days_array = [];
				foreach ($exclude_category_processing_days as $key => $value) {
					$exclude_category_processing_days_array [] = stripslashes(strtolower($value));	
				}

				$exclude_category_processing_days_condition = (count(array_intersect($checkout_product_categories, $exclude_category_processing_days_array)) <= count($checkout_product_categories)) && count(array_intersect($checkout_product_categories, $exclude_category_processing_days_array))>0;

				$exclude_product_processing_days_condition = (count(array_intersect($product_id, $exclude_product_processing_days)) <= count($product_id)) && count(array_intersect($product_id, $exclude_product_processing_days))>0;

				if($exclude_category_processing_days_condition && !$exclude_product_processing_days_condition) {
					$exclude_condition_processing_days = true;
					if(count($checkout_product_categories) > 1 && count($product_id) > 1 && count(array_diff($checkout_product_categories, $exclude_category_processing_days_array))>0) {
		  				$exclude_condition_processing_days = !$exclude_condition_processing_days;
		  			}
		  			
				} elseif($exclude_product_processing_days_condition && !$exclude_category_processing_days_condition) {
					$exclude_condition_processing_days = true;
					if(count($product_id) > 1 && count(array_diff($product_id, $exclude_product_processing_days))>0) {
		  				$exclude_condition_processing_days = !$exclude_condition_processing_days;
		  			}
				} elseif($exclude_product_processing_days_condition && $exclude_category_processing_days_condition) {
					$exclude_condition_processing_days = true;					
					foreach($product_id as $product) {
						
						if(!in_array($product, $exclude_product_processing_days)) {
							
							$term_lists = wp_get_post_terms($product,'product_cat',array('fields'=>'ids'));
							foreach($term_lists as $cat_id) {
								$term = get_term_by( 'id', $cat_id, 'product_cat' );
								if(!in_array(stripslashes(strtolower(htmlspecialchars_decode($term->name))), $exclude_category_processing_days_array)) {
									$exclude_condition_processing_days = !$exclude_condition_processing_days;
									break;
								}
							}
						}
					}
				} else {
					$exclude_condition_processing_days = false;
				}

				if(!$exclude_condition_processing_days) {
					array_push($max_processing_days_array,(int)$overall_processing_days);
					if(isset($backorder_processing_days)) {
						array_push($max_processing_days_array,(int)$backorder_processing_days);
					}
				}
			}

	    /*}*/

		$max_processing_days = count($max_processing_days_array) > 0 ? max($max_processing_days_array) : 0;

		$temp_max_processing_days = $max_processing_days;
		$disable_week_days_category = [];		
		if(!empty($category_wise_offdays)) {

			foreach ($category_wise_offdays as $key => $value)
			{

				if(in_array(stripslashes(strtolower($key)), $checkout_product_categories))
				{
					if(isset($value['weekday_offdays']) && !empty($value['weekday_offdays'])) {
						foreach($value['weekday_offdays'] as $off_day) {
							$disable_week_days_category[] = $off_day;
						}
					}
					

					if(isset($value['specific_date_offdays']) && !empty($value['specific_date_offdays'])) {
						foreach($value['specific_date_offdays'] as $date_off_day) {
							$disable_dates[] = trim($date_off_day);
							$pickup_disable_dates[] = trim($date_off_day);
						}
					}
				}
			}
		}

		$disable_week_days_category = array_unique($disable_week_days_category, false);
		$disable_week_days_category = array_values($disable_week_days_category);


		$disable_week_days_product = [];
		
		if(!empty($product_wise_offdays)) {

			foreach ($product_wise_offdays as $key => $value)
			{
				if(in_array($key, $product_id))
				{
					foreach($value as $off_day) {
						$disable_week_days_product[] = $off_day;
					}
				}
			}
		}

		$disable_week_days_product = array_unique($disable_week_days_product, false);
		$disable_week_days_product = array_values($disable_week_days_product);

		
		$special_open_days_categories = [];

		if(!empty($opendays_categories)) {
			foreach($opendays_categories as $category_name => $open_dates) {
				if(in_array(stripslashes(strtolower($category_name)), $checkout_product_categories)) {
					$open_dates = explode(',', $open_dates);
					$open_dates = array_filter($open_dates,function($date){
					    return strtotime($date) >= strtotime('today');
					});
					$special_open_days_categories = array_values(array_unique(array_merge($special_open_days_categories,$open_dates)));
				}
			}
		}

		$enable_closing_time = (isset($delivery_time_settings['enable_closing_time']) && !empty($delivery_time_settings['enable_closing_time'])) ? $delivery_time_settings['enable_closing_time'] : false;	
		$enable_closing_time_pickup = (isset($pickup_time_settings['enable_closing_time']) && !empty($pickup_time_settings['enable_closing_time'])) ? $pickup_time_settings['enable_closing_time'] : false;	

		$enable_different_closing_time = (isset($delivery_time_settings['enable_different_closing_time']) && !empty($delivery_time_settings['enable_different_closing_time'])) ? $delivery_time_settings['enable_different_closing_time'] : false;

		if($enable_different_closing_time) {
			$current_week_day = date("w"); 				
			$store_closing_time = isset($delivery_time_settings['different_store_closing_time'][$current_week_day]) ? (int)$delivery_time_settings['different_store_closing_time'][$current_week_day] : "";

			$current_time = (date("G")*60)+date("i");

			$extended_closing_days = (isset($delivery_time_settings['different_extended_closing_day'][$current_week_day]) && $delivery_time_settings['different_extended_closing_day'][$current_week_day] != "") ? (int)$delivery_time_settings['different_extended_closing_day'][$current_week_day] : 0;

			if(($store_closing_time != "" || $store_closing_time === 0) && ($current_time >= $store_closing_time)) {
				$disable_dates[] = $today;
				$pickup_disable_dates[] = $today;
				$date = $today;
				for($i=1;$i<=$extended_closing_days;$i++) {
					$formated = date('Y-m-d H:i:s', strtotime($date));
					$formated_obj = new DateTime($formated);
					$extended_closing_date = $formated_obj->modify("+1 day")->format("Y-m-d");
					$disable_dates[] = $extended_closing_date;
					$pickup_disable_dates[] = $extended_closing_date;
					$date = $extended_closing_date;

				}
			}

		}

		$detect_next_month_off_category = false;
		$current_month_remaining_date = [];		
		if(!empty($next_month_off_categories)) {

			$month_last_date_obj = new DateTime($today);
			$month_last_date_obj->modify('last day of this month');
			$month_last_date = $month_last_date_obj->format('Y-m-d');

			$period = new DatePeriod(new DateTime($today), new DateInterval('P1D'), new DateTime($month_last_date.' +1 day'));
			
			foreach ($period as $date) {
				$current_month_remaining_date[] = date("Y-m-d", strtotime($date->format("Y-m-d")));
			}

			foreach ($next_month_off_categories as $key => $value)
			{
				if(in_array(stripslashes(strtolower($value)), $checkout_product_categories))
				{
					$detect_next_month_off_category = true;
					break;
				}
			}
		}

		$detect_next_week_off_category = false;
		$current_week_remaining_date = [];
		$detect_next_week_off_category_pickup = false;
		$current_week_remaining_date_pickup = [];
		$week_starts_from = (isset($delivery_date_settings['week_starts_from']) && !empty($delivery_date_settings['week_starts_from'])) ? $delivery_date_settings['week_starts_from']:"0";
		$pickup_week_starts_from = (isset($pickup_date_settings['week_starts_from']) && !empty($pickup_date_settings['week_starts_from'])) ? $pickup_date_settings['week_starts_from']:"0";		
		if(!empty($next_week_off_categories)) {

			$week_last_date_obj = new DateTime($today);
			$week_last_date_obj->modify('Next '. $this->helper->week_last_date($week_starts_from));
			$week_last_date = $week_last_date_obj->format('Y-m-d');

			$week_last_date_obj_pickup = new DateTime($today);
			$week_last_date_obj_pickup->modify('Next '. $this->helper->week_last_date($pickup_week_starts_from));
			$week_last_date_pickup = $week_last_date_obj_pickup->format('Y-m-d');

			$period = new DatePeriod(new DateTime($today), new DateInterval('P1D'), new DateTime($week_last_date.' +1 day'));
			
			foreach ($period as $date) {
				$current_week_remaining_date[] = date("Y-m-d", strtotime($date->format("Y-m-d")));
			}

			foreach ($next_week_off_categories as $key => $value)
			{
				if(in_array(stripslashes(strtolower($value)), $checkout_product_categories))
				{
					$detect_next_week_off_category = true;
					break;
				}
			}

			$period_pickup = new DatePeriod(new DateTime($today), new DateInterval('P1D'), new DateTime($week_last_date_pickup.' +1 day'));
			
			foreach ($period_pickup as $date) {
				$current_week_remaining_date_pickup[] = date("Y-m-d", strtotime($date->format("Y-m-d")));
			}

			foreach ($next_week_off_categories as $key => $value)
			{
				if(in_array(stripslashes(strtolower($value)), $checkout_product_categories))
				{
					$detect_next_week_off_category_pickup = true;
					break;
				}
			}
		}
	
		// Delivery Date --------------------------------------------------------------
		
		if($enable_delivery_date) {

			$auto_select_first_date = (isset($delivery_date_settings['auto_select_first_date']) && !empty($delivery_date_settings['auto_select_first_date'])) ? $delivery_date_settings['auto_select_first_date'] : false;			

			$delivery_days = isset($delivery_date_settings['delivery_days']) && $delivery_date_settings['delivery_days'] != "" ? $delivery_date_settings['delivery_days'] : "6,0,1,2,3,4,5";			
			$delivery_date_mandatory = (isset($delivery_date_settings['delivery_date_mandatory']) && !empty($delivery_date_settings['delivery_date_mandatory'])) ? $delivery_date_settings['delivery_date_mandatory'] : false;
			$delivery_date_format = (isset($delivery_date_settings['date_format']) && !empty($delivery_date_settings['date_format'])) ? $delivery_date_settings['date_format'] : "F j, Y";

			$add_weekday_name = (isset($delivery_date_settings['add_weekday_name']) && !empty($delivery_date_settings['add_weekday_name'])) ? $delivery_date_settings['add_weekday_name'] : false;

			if($add_weekday_name) {
				$delivery_date_format = "l ".$delivery_date_format;
			}

			$delivery_date_calendar_locale = (isset($delivery_date_settings['calendar_locale']) && !empty($delivery_date_settings['calendar_locale'])) ? $delivery_date_settings['calendar_locale'] : "default";
			$week_starts_from = (isset($delivery_date_settings['week_starts_from']) && !empty($delivery_date_settings['week_starts_from'])) ? $delivery_date_settings['week_starts_from']:"0";
			
			$selectable_date = (isset($delivery_date_settings['selectable_date']) && !empty($delivery_date_settings['selectable_date']))?$delivery_date_settings['selectable_date']:"365";
			$selectable_date_until = (isset($delivery_date_settings['selectable_date_until']) && !empty($delivery_date_settings['selectable_date_until'])) ? $delivery_date_settings['selectable_date_until'] : "no_date";

			$same_day_delivery = (isset($delivery_date_settings['disable_same_day_delivery']) && !empty($delivery_date_settings['disable_same_day_delivery'])) ? $delivery_date_settings['disable_same_day_delivery'] : false;

			$delivery_days = explode(',', $delivery_days);

			$week_days = ['0', '1', '2', '3', '4', '5', '6'];
			$disable_week_days = array_values(array_diff($week_days, $delivery_days));

			$off_days = (isset($delivery_date_settings['off_days']) && !empty($delivery_date_settings['off_days'])) ? $delivery_date_settings['off_days'] : array();
			$off_day_dates = [];
			$selectable_start_date = date('Y-m-d H:i:s', time());
			$start_date = new DateTime($selectable_start_date);
			if(count($off_days)) {
				$date = $start_date;
				foreach ($off_days as $year => $months) {
					foreach($months as $month => $days){
						$month_num = date_parse($month)['month'];
						if(strlen($month_num) == 1) {
							$month_num_final = "0".$month_num;
						} else {
							$month_num_final = $month_num;
						}
						$days = explode(',', $days);
						foreach($days as $day){						
							$disable_dates[] = $year . "-" . $month_num_final . "-" .$day;
							$off_day_dates[] = $year . "-" . $month_num_final . "-" .$day;
						}
					}
				}
			}

					
			$selectable_start_date = date('Y-m-d H:i:s', time());
			$start_date = new DateTime($selectable_start_date);
			
			if($max_processing_days > 0) {

				if($consider_current_day && $max_processing_days > 0) {
					$disable_dates[] = $start_date->format("Y-m-d");
					$max_processing_days = $max_processing_days - 1;
					$start_date = $start_date->modify("+1 day");
				} else {
					$disable_dates[] = $start_date->format("Y-m-d");
					$start_date = $start_date->modify("+1 day");
				}

				while($max_processing_days > 0) {
					$date = $start_date;
					if($consider_weekends) {

						$disable_dates[] = $date->format("Y-m-d");
						$max_processing_days = $max_processing_days - 1;
						$start_date = $start_date->modify("+1 day");
					} else {
						if (!in_array($date->format("w"), $disable_week_days)) {
							$disable_dates[] = $date->format("Y-m-d");
							$max_processing_days = $max_processing_days - 1;
							$start_date = $start_date->modify("+1 day");
						} else {
							$disable_dates[] = $date->format("Y-m-d");
							$start_date = $start_date->modify("+1 day");

						}

					}

				}

			}

			$selectable_start_date_sec = date('Y-m-d H:i:s', time());
			$start_date_sec = new DateTime($selectable_start_date_sec);
			$max_processing_days_sec = $temp_max_processing_days;

			if($max_processing_days_sec > 0) {

				if($consider_current_day && $max_processing_days_sec > 0) {
					$disable_dates[] = $start_date_sec->format("Y-m-d");
					$max_processing_days_sec = $max_processing_days_sec - 1;
					$start_date_sec = $start_date_sec->modify("+1 day");
				} else {
					$disable_dates[] = $start_date_sec->format("Y-m-d");
					$start_date_sec = $start_date_sec->modify("+1 day");
				}

				while($max_processing_days_sec > 0) {
					$date = $start_date_sec;
					if($consider_off_days) {
						
						$disable_dates[] = $date->format("Y-m-d");
						$max_processing_days_sec = $max_processing_days_sec - 1;
						$start_date_sec = $start_date_sec->modify("+1 day");

					} else {
						if (!in_array($date->format("Y-m-d"), $off_day_dates)) {
							$disable_dates[] = $date->format("Y-m-d");
							$max_processing_days_sec = $max_processing_days_sec - 1;
							$start_date_sec = $start_date_sec->modify("+1 day");
						} else {

							$disable_dates[] = $date->format("Y-m-d");
							$start_date_sec = $start_date_sec->modify("+1 day");

						}

					}

				}
	

			}
			
			if(!$enable_different_closing_time && $enable_closing_time) {
				$store_closing_time = isset($delivery_time_settings['store_closing_time']) ? (int)$delivery_time_settings['store_closing_time'] : "";

				$current_time = (date("G")*60)+date("i");

				$extended_closing_days = (isset($delivery_time_settings['extended_closing_days']) && !empty($delivery_time_settings['extended_closing_days'])) ? (int)$delivery_time_settings['extended_closing_days'] : 0;



				if(($store_closing_time != "" || $store_closing_time === 0) && ($current_time >= $store_closing_time)) {
					$disable_dates[] = $today;
					$date = $today;
					for($i=1;$i<=$extended_closing_days;$i++) {
						$formated = date('Y-m-d H:i:s', strtotime($date));
						$formated_obj = new DateTime($formated);
						$extended_closing_date = $formated_obj->modify("+1 day")->format("Y-m-d");
						$disable_dates[] = $extended_closing_date;
						$date = $extended_closing_date;

					}
				}
			}

			if($same_day_delivery) {
				$disable_dates[] = $today;
			}	

			$disable_dates = array_unique($disable_dates, false);
			$disable_dates = array_values($disable_dates);

			$delivery_date_field_heading = (isset($delivery_date_settings['field_label']) && !empty($delivery_date_settings['field_label'])) ? stripslashes($delivery_date_settings['field_label']) : __( "Delivery Date", 'coderockz-woo-delivery' );
			$delivery_date_field_placeholder = (isset($delivery_date_settings['field_placeholder']) && !empty($delivery_date_settings['field_placeholder'])) ? stripslashes($delivery_date_settings['field_placeholder']) : __( "Delivery Date", 'coderockz-woo-delivery' );


			$delivery_open_days = (isset($delivery_date_settings['open_days']) && !empty($delivery_date_settings['open_days'])) ? $delivery_date_settings['open_days'] : array();
			$special_open_days_dates = [];
			$selectable_start_date = date('Y-m-d H:i:s', time());
			$start_date = new DateTime($selectable_start_date);
			if(count($delivery_open_days)) {
				$date = $start_date;
				foreach ($delivery_open_days as $year => $months) {
					foreach($months as $month => $days){
						$month_num = date_parse($month)['month'];
						if(strlen($month_num) == 1) {
							$month_num_final = "0".$month_num;
						} else {
							$month_num_final = $month_num;
						}
						$days = explode(',', $days);
						foreach($days as $day){
							if(strtotime($year . "-" . $month_num_final . "-" .$day) + 86400 >= time())				
								$special_open_days_dates[] = $year . "-" . $month_num_final . "-" .$day;
						}
					}
				}
			}

			$enable_free_shipping_current_day = (isset($delivery_option_settings['enable_free_shipping_current_day']) && !empty($delivery_option_settings['enable_free_shipping_current_day'])) ? $delivery_option_settings['enable_free_shipping_current_day'] : false;

			$disable_free_shipping_current_day = (isset($delivery_option_settings['disable_free_shipping_current_day']) && !empty($delivery_option_settings['disable_free_shipping_current_day'])) ? $delivery_option_settings['disable_free_shipping_current_day'] : false;

	    	$hide_free_shipping_weekday = (isset($delivery_option_settings['hide_free_shipping_weekday']) && !empty($delivery_option_settings['hide_free_shipping_weekday'])) ? $delivery_option_settings['hide_free_shipping_weekday'] : array();

	    	$delivery_fee_settings = get_option('coderockz_woo_delivery_fee_settings');
			$conditional_delivery_day_shipping_method = isset($delivery_fee_settings['conditional_delivery_day_shipping_method']) && $delivery_fee_settings['conditional_delivery_day_shipping_method'] != "" ? $delivery_fee_settings['conditional_delivery_day_shipping_method'] : "";
		   	$conditional_delivery_day_shipping_method_total_day = isset($delivery_fee_settings['conditional_delivery_day_shipping_method_total_day']) && $delivery_fee_settings['conditional_delivery_day_shipping_method_total_day'] != "" ? (int)$delivery_fee_settings['conditional_delivery_day_shipping_method_total_day'] : 0;
		   	$conditional_delivery_day_date = [];
		   	if($conditional_delivery_day_shipping_method_total_day != 0) {
		   		$conditional_delivery_day_range_first_date = date('Y-m-d', strtotime($today));
				$conditional_delivery_day_formated_obj = new DateTime($conditional_delivery_day_range_first_date);
				$conditional_delivery_day_range_last_date = $conditional_delivery_day_formated_obj->modify("+".($conditional_delivery_day_shipping_method_total_day-1)." day")->format("Y-m-d");
				$conditional_delivery_day_filtered_date = $conditional_delivery_day_range_first_date . ',' . $conditional_delivery_day_range_last_date;
				$conditional_delivery_day_filtered_dates = explode(',', $conditional_delivery_day_filtered_date);
				$conditional_delivery_day_period = new DatePeriod(new DateTime($conditional_delivery_day_filtered_dates[0]), new DateInterval('P1D'), new DateTime($conditional_delivery_day_filtered_dates[1].' +1 day'));
				foreach($conditional_delivery_day_period as $conditional_delivery_day) {
					$conditional_delivery_day_date[] = date("Y-m-d", strtotime($conditional_delivery_day->format("Y-m-d")));
				}
		   	}


		   	$only_available_for_today_text = isset($localization_settings['only_available_for_today_text']) && $localization_settings['only_available_for_today_text'] != "" ? stripslashes($localization_settings['only_available_for_today_text']) : __("only available for today","coderockz-woo-delivery");
			$free_shipping_other_day_text = isset($localization_settings['free_shipping_other_day_text']) && $localization_settings['free_shipping_other_day_text'] != "" ? stripslashes($localization_settings['free_shipping_other_day_text']) : __("is not available for today","coderockz-woo-delivery");
			$only_available_for_text = isset($localization_settings['only_available_for_text']) && $localization_settings['only_available_for_text'] != "" ? stripslashes($localization_settings['only_available_for_text']) : __("only available for","coderockz-woo-delivery");

			echo '<div id="coderockz_woo_delivery_delivery_date_section" style="display:none;">';

			woocommerce_form_field('coderockz_woo_delivery_date_field',
			[
				'type' => 'text',
				'class' => array(
				  'coderockz_woo_delivery_date_field form-row-wide'
				) ,
				'id' => "coderockz_woo_delivery_date_datepicker",
				'label' => __($delivery_date_field_heading, 'coderockz-woo-delivery'),
				'placeholder' => __($delivery_date_field_placeholder, 'coderockz-woo-delivery'),
				'required' => $delivery_date_mandatory, 
				'custom_attributes' => [
					'data-selectable_dates' => $selectable_date,
					'data-selectable_dates_until' => $selectable_date_until,
					'data-disable_week_days' => json_encode($disable_week_days),
					'data-disable_week_days_category' => json_encode($disable_week_days_category),
					'data-disable_week_days_product' => json_encode($disable_week_days_product),
					'data-date_format' => $delivery_date_format,
					'data-disable_dates' => json_encode($disable_dates),
					'data-calendar_locale' => $delivery_date_calendar_locale,
					'data-week_starts_from' => $week_starts_from,
					'data-default_date' => $auto_select_first_date,
					'data-same_day_delivery' => $same_day_delivery,
					'data-special_open_days_dates' => json_encode($special_open_days_dates),
					'data-delivery_date_mandatory' => $delivery_date_mandatory,
					'data-special_open_days_categories' => json_encode($special_open_days_categories),
					'data-current_month_remaining_date' => json_encode($current_month_remaining_date),
					'data-detect_next_month_off_category' => $detect_next_month_off_category,
					'data-current_week_remaining_date' => json_encode($current_week_remaining_date),
					'data-detect_next_week_off_category' => $detect_next_week_off_category,

					'data-enable_free_shipping_current_day' => $enable_free_shipping_current_day,
					'data-disable_free_shipping_current_day' => $disable_free_shipping_current_day,
					'data-hide_free_shipping_weekday' => json_encode($hide_free_shipping_weekday),
					'data-conditional_delivery_day_shipping_method' => $conditional_delivery_day_shipping_method,
					'data-conditional_delivery_day_date' => json_encode($conditional_delivery_day_date),
					'data-only_available_for_today_text' => $only_available_for_today_text,
					'data-free_shipping_other_day_text' => $free_shipping_other_day_text,
					'data-only_available_for_text' => $only_available_for_text,

				],
			] , WC()->checkout->get_value('coderockz_woo_delivery_date_field'));
			echo '</div>';

			echo '<div class="coderockz_woo_delivery_notice-modal" id="coderockz_woo_delivery_notice-modal">
                    <div class="coderockz_woo_delivery_notice-modal-wrap">
                        <div class="coderockz_woo_delivery_notice-modal-body" style="overflow:hidden;padding: 5px 20px 5px 20px;"><div style="width:95%;float:left;"></div><button class="coderockz-woo-delivery-cancel-button-notice-modal" style="width:4%;float:right;margin: 10px 0 0 0">X</button>';

            echo '</div></div></div>';

		}

		// End Delivery Date

		// Delivery Time --------------------------------------------------------------
		
		$enable_delivery_time = (isset($delivery_time_settings['enable_delivery_time']) && !empty($delivery_time_settings['enable_delivery_time'])) ? $delivery_time_settings['enable_delivery_time'] : false;

		$delivery_time_field_label = (isset($delivery_time_settings['field_label']) && !empty($delivery_time_settings['field_label'])) ? stripslashes($delivery_time_settings['field_label']) : __( "Delivery Time", 'coderockz-woo-delivery' );
		$delivery_time_field_placeholder = (isset($delivery_time_settings['field_placeholder']) && !empty($delivery_time_settings['field_placeholder'])) ? stripslashes($delivery_time_settings['field_placeholder']) : __( "Delivery Time", 'coderockz-woo-delivery' );

		$delivery_time_mandatory = (isset($delivery_time_settings['delivery_time_mandatory']) && !empty($delivery_time_settings['delivery_time_mandatory'])) ? $delivery_time_settings['delivery_time_mandatory'] : false;
		$auto_select_first_time = (isset($delivery_time_settings['auto_select_first_time']) && !empty($delivery_time_settings['auto_select_first_time'])) ? $delivery_time_settings['auto_select_first_time'] : false;
		$search_box_hidden = (isset($delivery_time_settings['hide_searchbox']) && !empty($delivery_time_settings['hide_searchbox'])) ? $delivery_time_settings['hide_searchbox'] : false;

		if($enable_delivery_time) {


			$disable_timeslot_with_processing_time = (isset($processing_time_settings['disable_timeslot_with_processing_time']) && !empty($processing_time_settings['disable_timeslot_with_processing_time'])) ? $processing_time_settings['disable_timeslot_with_processing_time'] : false;

			$order_limit_notice = (isset($localization_settings['order_limit_notice']) && !empty($localization_settings['order_limit_notice'])) ? "(".stripslashes($localization_settings['order_limit_notice']).")" : __( "(Maximum Delivery Limit Exceed)", 'coderockz-woo-delivery' );
			$select_delivery_date_notice = (isset($localization_settings['select_delivery_date_notice']) && !empty($localization_settings['select_delivery_date_notice'])) ? stripslashes($localization_settings['select_delivery_date_notice']) : __( "Select Delivery Date First", 'coderockz-woo-delivery' );
			$no_timeslot_available = (isset($localization_settings['no_timeslot_available']) && !empty($localization_settings['no_timeslot_available'])) ? stripslashes($localization_settings['no_timeslot_available']) : __( "No Timeslot Available To Select", 'coderockz-woo-delivery' );


			$custom_time_slot_settings = get_option('coderockz_woo_delivery_time_slot_settings');
			$enable_custom_time_slot = (isset($custom_time_slot_settings['enable_custom_time_slot']) && !empty($custom_time_slot_settings['enable_custom_time_slot'])) ? $custom_time_slot_settings['enable_custom_time_slot'] : false;
			$timeslot_zone_check = false;
			if($enable_custom_time_slot) {

				if(isset($custom_time_slot_settings['time_slot']) && count($custom_time_slot_settings['time_slot'])>0){
					foreach($custom_time_slot_settings['time_slot'] as $individual_time_slot) {
			  			 
			  			if( $individual_time_slot['enable'] && (!empty($individual_time_slot['disable_postcode']) || !empty($individual_time_slot['disable_state'])) ) {
			  				$timeslot_zone_check = true;
			  				break;
			  			}
			  		}
				}
			}

			$delivery_fee_settings = get_option('coderockz_woo_delivery_fee_settings');
			$enable_conditional_delivery_fee = isset($delivery_fee_settings['enable_conditional_delivery_fee']) && !empty($delivery_fee_settings['enable_conditional_delivery_fee']) ? $delivery_fee_settings['enable_conditional_delivery_fee'] : false;
			$conditional_delivery_shipping_method_name = (isset($delivery_fee_settings['conditional_delivery_shipping_method']) && $delivery_fee_settings['conditional_delivery_shipping_method'] != "") ? $delivery_fee_settings['conditional_delivery_shipping_method'] : "";

			$current_time = (date("G")*60)+date("i");
			$have_conditional_timeslot = false;
			/*$conditional_delivery_dropdown_text = "";*/
			$duration = "X"; 
        	$identity = __("Minutes", 'coderockz-woo-delivery');
			if($enable_conditional_delivery_fee && $conditional_delivery_shipping_method_name != "" && (isset($delivery_fee_settings['conditional_delivery_time_starts']) && ($delivery_fee_settings['conditional_delivery_time_starts'] !='' || $delivery_fee_settings['conditional_delivery_time_starts'] == 0)) && (isset($delivery_fee_settings['conditional_delivery_time_ends']) && $delivery_fee_settings['conditional_delivery_time_ends'] !='') && (isset($delivery_fee_settings['conditional_delivery_fee_duration']) && !empty($delivery_fee_settings['conditional_delivery_fee_duration']))) {

				$have_conditional_timeslot = true;


				
    			if(isset($delivery_fee_settings['conditional_delivery_fee_duration']) && !empty($delivery_fee_settings['conditional_delivery_fee_duration'])) {
    				$conditional_delivery_fee_duration = (int)$delivery_fee_settings['conditional_delivery_fee_duration'];
    				if($conditional_delivery_fee_duration <= 59) {
    					$duration = $conditional_delivery_fee_duration;
    				} else {
    					$conditional_delivery_fee_duration = $conditional_delivery_fee_duration/60;
    					if($this->helper->containsDecimal($conditional_delivery_fee_duration)){
    						$duration = $conditional_delivery_fee_duration*60;
    						$identity = __("Minutes", 'coderockz-woo-delivery');
    					} else {
    						$duration = $conditional_delivery_fee_duration;
    						$identity = __("Hour", 'coderockz-woo-delivery');
    					}
    				}
    			}
			
			}

			$conditional_delivery_dropdown_text = isset($delivery_fee_settings['conditional_delivery_dropdown_text']) && !empty($delivery_fee_settings['conditional_delivery_dropdown_text']) ? __(stripslashes(esc_attr($delivery_fee_settings['conditional_delivery_dropdown_text'])), 'coderockz-woo-delivery') : __("Delivery within ", 'coderockz-woo-delivery').$duration." ".$identity;

			$need_to_select_text = (isset($localization_settings['need_to_select_text']) && !empty($localization_settings['need_to_select_text'])) ? stripslashes($localization_settings['need_to_select_text']) : __( "and need to select delivery time", 'coderockz-woo-delivery' );

			$if_available_text = (isset($localization_settings['if_available_text']) && !empty($localization_settings['if_available_text'])) ? stripslashes($localization_settings['if_available_text']) : __( "if available", 'coderockz-woo-delivery' );

			echo '<div id="coderockz_woo_delivery_delivery_time_section" style="display:none;">';

			woocommerce_form_field('coderockz_woo_delivery_time_field',
			[
				'type' => 'select',
				'class' => [
					'coderockz_woo_delivery_time_field form-row-wide'
				],
				'label' => __($delivery_time_field_label, 'coderockz-woo-delivery'),
				'placeholder' => __($delivery_time_field_placeholder, 'coderockz-woo-delivery'),
				'options' => Coderockz_Woo_Delivery_Time_Option::delivery_time_option($delivery_time_settings),
				'required' => $delivery_time_mandatory,
				'custom_attributes' => [
					'data-default_time' => $auto_select_first_time,
					'data-max_processing_time' => $max_processing_time,
					'data-last_processing_time_date' => $last_processing_time_date,
					'data-disable_timeslot_with_processing_time' => $disable_timeslot_with_processing_time,
					'data-hide_searchbox' => $search_box_hidden,
					'data-order_limit_notice' => __($order_limit_notice, 'coderockz-woo-delivery'),
					'data-timeslot_zone_check' => $timeslot_zone_check,
					'data-select_delivery_date_notice' => __($select_delivery_date_notice, 'coderockz-woo-delivery'),
					'data-no_timeslot_available' => __($no_timeslot_available, 'coderockz-woo-delivery'),
					'data-conditional_delivery_shipping_method_name' => $conditional_delivery_shipping_method_name,
					'data-have_conditional_timeslot' => $have_conditional_timeslot,
					'data-conditional_delivery_dropdown_text' => $conditional_delivery_dropdown_text,
					'data-need_to_select_text' => $need_to_select_text,
					'data-if_available_text' => $if_available_text,
				],
			], WC()->checkout->get_value('coderockz_woo_delivery_time_field'));
			echo '</div>';

		}
		// End Delivery Time

		// Pickup Date --------------------------------------------------------------

		if($enable_pickup_date) {

			$auto_select_first_pickup_date = (isset($pickup_date_settings['auto_select_first_pickup_date']) && !empty($pickup_date_settings['auto_select_first_pickup_date'])) ? $pickup_date_settings['auto_select_first_pickup_date'] : false;			

			$pickup_days = isset($pickup_date_settings['pickup_days']) && $pickup_date_settings['pickup_days'] != "" ? $pickup_date_settings['pickup_days'] : "6,0,1,2,3,4,5";			

			$pickup_date_mandatory = (isset($pickup_date_settings['pickup_date_mandatory']) && !empty($pickup_date_settings['pickup_date_mandatory'])) ? $pickup_date_settings['pickup_date_mandatory'] : false;
			$pickup_date_format = (isset($pickup_date_settings['date_format']) && !empty($pickup_date_settings['date_format'])) ? $pickup_date_settings['date_format'] : "F j, Y";
			$pickup_add_weekday_name = (isset($pickup_date_settings['add_weekday_name']) && !empty($pickup_date_settings['add_weekday_name'])) ? $pickup_date_settings['add_weekday_name'] : false;

			if($pickup_add_weekday_name) {
				$pickup_date_format = "l ".$pickup_date_format;
			}		
			$pickup_date_calendar_locale = (isset($pickup_date_settings['calendar_locale']) && !empty($pickup_date_settings['calendar_locale'])) ? $pickup_date_settings['calendar_locale'] : "default";
			$pickup_week_starts_from = (isset($pickup_date_settings['week_starts_from']) && !empty($pickup_date_settings['week_starts_from'])) ? $pickup_date_settings['week_starts_from']:"0";
			
			$pickup_selectable_date = (isset($pickup_date_settings['selectable_date']) && !empty($pickup_date_settings['selectable_date']))?$pickup_date_settings['selectable_date']:"365";
			$pickup_selectable_date_until = (isset($pickup_date_settings['selectable_date_until']) && !empty($pickup_date_settings['selectable_date_until']))?$pickup_date_settings['selectable_date_until']:"no_date";

			$same_day_pickup = (isset($pickup_date_settings['disable_same_day_pickup']) && !empty($pickup_date_settings['disable_same_day_pickup'])) ? $pickup_date_settings['disable_same_day_pickup'] : false;

			$pickup_days = explode(',', $pickup_days);

			$week_days = ['0', '1', '2', '3', '4', '5', '6'];
			$pickup_disable_week_days = array_values(array_diff($week_days, $pickup_days));

			

			$off_days = (isset($pickup_date_settings['pickup_off_days']) && !empty($pickup_date_settings['pickup_off_days'])) ? $pickup_date_settings['pickup_off_days'] : array();
			$off_day_dates = [];
			$selectable_start_date = date('Y-m-d H:i:s', time());
			$start_date = new DateTime($selectable_start_date);
			if(count($off_days)) {
				$date = $start_date;
				foreach ($off_days as $year => $months) {
					foreach($months as $month => $days){
						$month_num = date_parse($month)['month'];
						if(strlen($month_num) == 1) {
							$month_num_final = "0".$month_num;
						} else {
							$month_num_final = $month_num;
						}
						$days = explode(',', $days);
						foreach($days as $day){						
							$pickup_disable_dates[] = $year . "-" . $month_num_final . "-" .$day;
							$off_day_dates[] = $year . "-" . $month_num_final . "-" .$day;
						}
					}
				}
			}


			$selectable_start_date = date('Y-m-d H:i:s', time());
			$start_date = new DateTime($selectable_start_date);
			$max_processing_days = $temp_max_processing_days;
			
			if($max_processing_days > 0) {

				if($consider_current_day && $max_processing_days > 0) {
					$pickup_disable_dates[] = $start_date->format("Y-m-d");
					$max_processing_days = $max_processing_days - 1;
					$start_date = $start_date->modify("+1 day");
				} else {
					$pickup_disable_dates[] = $start_date->format("Y-m-d");
					$start_date = $start_date->modify("+1 day");
				}

				while($max_processing_days > 0) {
					$date = $start_date;
					if($consider_weekends) {

						$pickup_disable_dates[] = $date->format("Y-m-d");
						$max_processing_days = $max_processing_days - 1;
						$start_date = $start_date->modify("+1 day");
					} else {
						if (!in_array($date->format("w"), $pickup_disable_week_days)) {
							$pickup_disable_dates[] = $date->format("Y-m-d");
							$max_processing_days = $max_processing_days - 1;
							$start_date = $start_date->modify("+1 day");
						} else {

							$pickup_disable_dates[] = $date->format("Y-m-d");
							$start_date = $start_date->modify("+1 day");

						}

					}

				}

			}

			$selectable_start_date_sec = date('Y-m-d H:i:s', time());
			$start_date_sec = new DateTime($selectable_start_date_sec);
			$max_processing_days_sec = $temp_max_processing_days;

			
			if($max_processing_days_sec > 0) {

				if($consider_current_day && $max_processing_days_sec > 0) {
					$pickup_disable_dates[] = $start_date_sec->format("Y-m-d");
					$max_processing_days_sec = $max_processing_days_sec - 1;
					$start_date_sec = $start_date_sec->modify("+1 day");
				} else {
					$pickup_disable_dates[] = $start_date_sec->format("Y-m-d");
					$start_date_sec = $start_date_sec->modify("+1 day");
				}

				while($max_processing_days_sec > 0) {
					$date = $start_date_sec;
					if($consider_off_days) {
						
						$pickup_disable_dates[] = $date->format("Y-m-d");
						$max_processing_days_sec = $max_processing_days_sec - 1;
						$start_date_sec = $start_date_sec->modify("+1 day");

					} else {
						if (!in_array($date->format("Y-m-d"), $off_day_dates)) {
							$pickup_disable_dates[] = $date->format("Y-m-d");
							$max_processing_days_sec = $max_processing_days_sec - 1;
							$start_date_sec = $start_date_sec->modify("+1 day");
						} else {

							$pickup_disable_dates[] = $date->format("Y-m-d");
							$start_date_sec = $start_date_sec->modify("+1 day");

						}

					}

				}
	

			}

			if(!$enable_different_closing_time && $enable_closing_time_pickup) {
				$store_closing_time = isset($pickup_time_settings['store_closing_time']) ? (int)$pickup_time_settings['store_closing_time'] : "";

				$current_time = (date("G")*60)+date("i");

				$extended_closing_days = (isset($pickup_time_settings['extended_closing_days']) && !empty($pickup_time_settings['extended_closing_days'])) ? (int)$pickup_time_settings['extended_closing_days'] : 0;

				if(($store_closing_time != "" || $store_closing_time === 0) && ($current_time >= $store_closing_time)) {
					$pickup_disable_dates[] = $today;
					$date = $today;
					for($i=1;$i<=$extended_closing_days;$i++) {
						$formated = date('Y-m-d H:i:s', strtotime($date));
						$formated_obj = new DateTime($formated);
						$extended_closing_date = $formated_obj->modify("+1 day")->format("Y-m-d");
						$pickup_disable_dates[] = $extended_closing_date;
						$date = $extended_closing_date;

					}
				}
			}

			if($same_day_pickup) {
				$pickup_disable_dates[] = $today;
			}

			$pickup_disable_dates = array_unique($pickup_disable_dates, false);
			$pickup_disable_dates = array_values($pickup_disable_dates);

			$pickup_date_field_heading = (isset($pickup_date_settings['pickup_field_label']) && !empty($pickup_date_settings['pickup_field_label'])) ? stripslashes($pickup_date_settings['pickup_field_label']) : __( "Pickup Date", 'coderockz-woo-delivery' );
			$pickup_date_field_placeholder = (isset($pickup_date_settings['pickup_field_placeholder']) && !empty($pickup_date_settings['pickup_field_placeholder'])) ? stripslashes($pickup_date_settings['pickup_field_placeholder']) : __( "Pickup Date", 'coderockz-woo-delivery' );

			$pickup_open_days = (isset($pickup_date_settings['open_days']) && !empty($pickup_date_settings['open_days'])) ? $pickup_date_settings['open_days'] : array();
			$special_open_days_dates_pickup = [];
			$selectable_start_date = date('Y-m-d H:i:s', time());
			$start_date = new DateTime($selectable_start_date);
			if(count($pickup_open_days)) {
				$date = $start_date;
				foreach ($pickup_open_days as $year => $months) {
					foreach($months as $month => $days){
						$month_num = date_parse($month)['month'];
						if(strlen($month_num) == 1) {
							$month_num_final = "0".$month_num;
						} else {
							$month_num_final = $month_num;
						}
						$days = explode(',', $days);
						foreach($days as $day){	
							if(strtotime($year . "-" . $month_num_final . "-" .$day) + 86400 >= time())	
								$special_open_days_dates_pickup[] = $year . "-" . $month_num_final . "-" .$day;
						}
					}
				}
			}

			echo '<div id="coderockz_woo_delivery_pickup_date_section" style="display:none;">';

			woocommerce_form_field('coderockz_woo_delivery_pickup_date_field',
			[
				'type' => 'text',
				'class' => array(
				  'coderockz_woo_delivery_pickup_date_field form-row-wide'
				) ,
				'id' => "coderockz_woo_delivery_pickup_date_datepicker",
				'label' => __($pickup_date_field_heading, 'coderockz-woo-delivery'),
				'placeholder' => __($pickup_date_field_placeholder, 'coderockz-woo-delivery'),
				'required' => $pickup_date_mandatory, 
				'custom_attributes' => [
					'data-pickup_selectable_dates' => $pickup_selectable_date,
					'data-pickup_selectable_dates_until' => $pickup_selectable_date_until,
					'data-pickup_disable_week_days' => json_encode($pickup_disable_week_days),
					'data-disable_week_days_category' => json_encode($disable_week_days_category),
					'data-disable_week_days_product' => json_encode($disable_week_days_product),
					'data-pickup_date_format' => $pickup_date_format,
					'data-pickup_disable_dates' => json_encode($pickup_disable_dates),
					'data-pickup_calendar_locale' => $pickup_date_calendar_locale,
					'data-pickup_week_starts_from' => $pickup_week_starts_from,
					'data-pickup_default_date' => $auto_select_first_pickup_date,
					'data-same_day_pickup' => $same_day_pickup,
					'data-special_open_days_dates_pickup' => json_encode($special_open_days_dates_pickup),
					'data-pickup_date_mandatory' => $pickup_date_mandatory,
					'data-special_open_days_categories' => json_encode($special_open_days_categories),
					'data-current_month_remaining_date' => json_encode($current_month_remaining_date),
					'data-pickup_detect_next_month_off_category' => $detect_next_month_off_category,
					'data-current_week_remaining_date_pickup' => json_encode($current_week_remaining_date_pickup),
					'data-detect_next_week_off_category_pickup' => $detect_next_week_off_category_pickup,
				],
			] , WC()->checkout->get_value('coderockz_woo_delivery_pickup_date_field'));
			echo '</div>';

		}

		// End Pickup Date

		// Pickup Time --------------------------------------------------------------

		$enable_pickup_time = (isset($pickup_time_settings['enable_pickup_time']) && !empty($pickup_time_settings['enable_pickup_time'])) ? $pickup_time_settings['enable_pickup_time'] : false;
		$pickup_time_field_label = (isset($pickup_time_settings['field_label']) && !empty($pickup_time_settings['field_label'])) ? stripslashes($pickup_time_settings['field_label']) : __( "Pickup Time", 'coderockz-woo-delivery' );
		$pickup_time_field_placeholder = (isset($pickup_time_settings['field_placeholder']) && !empty($pickup_time_settings['field_placeholder'])) ? stripslashes($pickup_time_settings['field_placeholder']) : __( "Pickup Time", 'coderockz-woo-delivery' );

		$pickup_time_mandatory = (isset($pickup_time_settings['pickup_time_mandatory']) && !empty($pickup_time_settings['pickup_time_mandatory'])) ? $pickup_time_settings['pickup_time_mandatory'] : false;
		$pickup_auto_select_first_time = (isset($pickup_time_settings['auto_select_first_time']) && !empty($pickup_time_settings['auto_select_first_time'])) ? $pickup_time_settings['auto_select_first_time'] : false;
		$pickup_search_box_hidden = (isset($pickup_time_settings['hide_searchbox']) && !empty($pickup_time_settings['hide_searchbox'])) ? $pickup_time_settings['hide_searchbox'] : false;

		if($enable_pickup_time) {


			$disable_timeslot_with_processing_time = (isset($processing_time_settings['disable_timeslot_with_processing_time']) && !empty($processing_time_settings['disable_timeslot_with_processing_time'])) ? $processing_time_settings['disable_timeslot_with_processing_time'] : false;

			$pickup_limit_notice = (isset($localization_settings['pickup_limit_notice']) && !empty($localization_settings['pickup_limit_notice'])) ? "(".stripslashes($localization_settings['pickup_limit_notice']).")" : __( "(Maximum Pickup Limit Exceed)", 'coderockz-woo-delivery' );

			$select_pickup_date_notice = (isset($localization_settings['select_pickup_date_notice']) && !empty($localization_settings['select_pickup_date_notice'])) ? stripslashes($localization_settings['select_pickup_date_notice']) : __( "Select Pickup Date First", 'coderockz-woo-delivery' );

			$select_pickup_date_location_notice = (isset($localization_settings['select_pickup_date_location_notice']) && !empty($localization_settings['select_pickup_date_location_notice'])) ? stripslashes($localization_settings['select_pickup_date_location_notice']) : __( "Select Pickup Date & Location First", 'coderockz-woo-delivery' );

			$select_pickup_location_notice = (isset($localization_settings['select_pickup_location_notice']) && !empty($localization_settings['select_pickup_location_notice'])) ? stripslashes($localization_settings['select_pickup_location_notice']) : __( "Select Pickup Location First", 'coderockz-woo-delivery' );

			$no_timeslot_available_pickup = (isset($localization_settings['no_timeslot_available']) && !empty($localization_settings['no_timeslot_available'])) ? stripslashes($localization_settings['no_timeslot_available']) : __( "No Timeslot Available To Select", 'coderockz-woo-delivery' );

			$custom_pickup_slot_settings = get_option('coderockz_woo_delivery_pickup_slot_settings');
			$enable_custom_pickup_slot = (isset($custom_pickup_slot_settings['enable_custom_pickup_slot']) && !empty($custom_pickup_slot_settings['enable_custom_pickup_slot'])) ? $custom_pickup_slot_settings['enable_custom_pickup_slot'] : false;
			$pickupslot_zone_check = false;
			if($enable_custom_pickup_slot) {

				if(isset($custom_pickup_slot_settings['time_slot']) && count($custom_pickup_slot_settings['time_slot'])>0){
					foreach($custom_pickup_slot_settings['time_slot'] as $individual_time_slot) {
			  			 
			  			if( $individual_time_slot['enable'] && (!empty($individual_time_slot['disable_postcode']) || !empty($individual_time_slot['disable_state'])) ) {
			  				$pickupslot_zone_check = true;
			  				break;
			  			}
			  		}
				}
			}

			echo '<div id="coderockz_woo_delivery_pickup_time_section" style="display:none;">';

			woocommerce_form_field('coderockz_woo_delivery_pickup_time_field',
			[
				'type' => 'select',
				'class' => [
					'coderockz_woo_delivery_pickup_time_field form-row-wide'
				],
				'label' => __($pickup_time_field_label, 'coderockz-woo-delivery'),
				'placeholder' => __($pickup_time_field_placeholder, 'coderockz-woo-delivery'),
				'options' => Coderockz_Woo_Delivery_Pickup_Option::pickup_time_option($pickup_time_settings),
				'required' => $pickup_time_mandatory,
				'custom_attributes' => [
					'data-default_time' => $pickup_auto_select_first_time,
					'data-max_processing_time' => $max_processing_time,
					'data-last_processing_time_date' => $last_processing_time_date,
					'data-disable_timeslot_with_processing_time' => $disable_timeslot_with_processing_time,
					'data-hide_searchbox' => $pickup_search_box_hidden,
					'data-pickup_limit_notice' => __($pickup_limit_notice, 'coderockz-woo-delivery'),
					'data-pickupslot_zone_check' => $pickupslot_zone_check,
					'data-select_pickup_date_notice' => __($select_pickup_date_notice, 'coderockz-woo-delivery'),
					'data-select_pickup_date_location_notice' => __($select_pickup_date_location_notice, 'coderockz-woo-delivery'),
					'data-no_timeslot_available_pickup' => __($no_timeslot_available_pickup, 'coderockz-woo-delivery'),
					'data-select_pickup_location_notice' => __($select_pickup_location_notice, 'coderockz-woo-delivery'),
				],
			], WC()->checkout->get_value('coderockz_woo_delivery_pickup_time_field'));
			echo '</div>';

		}
		// End Pickup Time

		$pickup_location_settings = get_option('coderockz_woo_delivery_pickup_location_settings');

		$enable_pickup_location = (isset($pickup_location_settings['enable_pickup_location']) && !empty($pickup_location_settings['enable_pickup_location'])) ? $pickup_location_settings['enable_pickup_location'] : false;

		if($enable_pickup_location)
		{
			$pickup_location_field_label = (isset($pickup_location_settings['field_label']) && $pickup_location_settings['field_label'] != "") ? stripslashes($pickup_location_settings['field_label']) : __( "Pickup Location", 'coderockz-woo-delivery' );
			
			$pickup_location_field_placeholder = (isset($pickup_location_settings['field_placeholder']) && $pickup_location_settings['field_placeholder'] != "") ? stripslashes($pickup_location_settings['field_placeholder']) : __( "Pickup Location", 'coderockz-woo-delivery' );

			$pickup_location_mandatory = (isset($pickup_location_settings['pickup_location_mandatory']) && $pickup_location_settings['pickup_location_mandatory'] != "") ? $pickup_location_settings['pickup_location_mandatory'] : false;
			
			$pickup_location_popup = (isset($pickup_location_settings['pickup_location_popup']) && $pickup_location_settings['pickup_location_popup'] != "") ? $pickup_location_settings['pickup_location_popup'] : false;

			$pickup_location_limit_notice = (isset($localization_settings['pickup_location_limit_notice']) && $localization_settings['pickup_location_limit_notice'] != "") ? "(".stripslashes($localization_settings['pickup_location_limit_notice']).")" : __( "(Maximum Pickup Limit Exceed For This Location)", 'coderockz-woo-delivery' );

			$select_pickup_date_notice = (isset($localization_settings['select_pickup_date_notice']) && $localization_settings['select_pickup_date_notice'] != "") ? stripslashes($localization_settings['select_pickup_date_notice']) : __( "Select Pickup Date First", 'coderockz-woo-delivery' );

			$location_map_click_here = (isset($localization_settings['location_map_click_here']) && $localization_settings['location_map_click_here'] != "") ? stripslashes($localization_settings['location_map_click_here']) : __( "Click here", 'coderockz-woo-delivery' );
			$to_see_map_location = (isset($localization_settings['to_see_map_location']) && $localization_settings['to_see_map_location'] != "") ? stripslashes($localization_settings['to_see_map_location']) : __( "to see map location", 'coderockz-woo-delivery' );

			$pickup_location_map_url = [];

			$pickup_locations = (isset($pickup_location_settings['pickup_location']) && !empty($pickup_location_settings['pickup_location'])) ? $pickup_location_settings['pickup_location'] : array();

			foreach ($pickup_locations as $name => $location_settings) {
				$pickup_location_map_url[stripslashes($name)] = (isset($location_settings['map_url']) && $location_settings['map_url'] != "") ? $location_settings['map_url'] : "";
			}

			

			$fetch_pickup_locations = Coderockz_Woo_Delivery_Pickup_Location_Option::pickup_location_option($pickup_location_settings);

			if(!$pickup_location_popup && count($fetch_pickup_locations) > 1) {

				$pickup_location_popup_heading = (isset($pickup_location_settings['pickup_location_popup_heading']) && $pickup_location_settings['pickup_location_popup_heading'] != "") ? stripslashes($pickup_location_settings['pickup_location_popup_heading']) : __( "Location Wise Pickup Days", 'coderockz-woo-delivery' );

				echo '<div class="coderockz_woo_delivery_location_pickup-modal" id="coderockz_woo_delivery_location_pickup-modal">
					<div class="coderockz_woo_delivery_location_pickup-modal-wrap">
						<div class="coderockz_woo_delivery_location_pickup-modal-header">
							<h4 style="margin:0;float:left">'.__($pickup_location_popup_heading, 'coderockz-woo-delivery').'</h4>
							<button class="coderockz-woo-delivery-cancel-button-location-pickup-modal">X</button>
						</div>

						<div class="coderockz_woo_delivery_location_pickup-modal-body">';

						foreach($fetch_pickup_locations as $pickup_location) {
							if($pickup_location != "") {

								$pickup_days = isset($pickup_date_settings['pickup_days']) && $pickup_date_settings['pickup_days'] != "" ? $pickup_date_settings['pickup_days'] : "6,0,1,2,3,4,5";

								$week_days = explode(",",$pickup_days);
								$pickup_location_week_days = array_values(array_diff($week_days, isset($pickup_locations[$pickup_location]['disable_for']) && !empty($pickup_locations[$pickup_location]['disable_for']) ? $pickup_locations[$pickup_location]['disable_for'] : []));

								$pickup_location_week_days_name=[];
								foreach($pickup_location_week_days as $weekday) {
									switch ($weekday) {
									  case "0":
									    $pickup_location_week_days_name[] = $this->helper->weekday_conversion_to_locale("Sunday","pickup");
									    break;
									  case "1":
									    $pickup_location_week_days_name[] = $this->helper->weekday_conversion_to_locale("Monday","pickup");
									    break;
									  case "2":
									    $pickup_location_week_days_name[] = $this->helper->weekday_conversion_to_locale("Tuesday","pickup");
									    break;
									  case "3":
									    $pickup_location_week_days_name[] = $this->helper->weekday_conversion_to_locale("Wednesday","pickup");
									    break;
									  case "4":
									    $pickup_location_week_days_name[] = $this->helper->weekday_conversion_to_locale("Thursday","pickup");
									    break;
									  case "5":
									    $pickup_location_week_days_name[] = $this->helper->weekday_conversion_to_locale("Friday","pickup");
									    break;
									  case "6":
									    $pickup_location_week_days_name[] = $this->helper->weekday_conversion_to_locale("Saturday","pickup");
									    break;
									}
								}

								$pickup_location_week_days_name = implode(", ",$pickup_location_week_days_name);

								echo '<p><b>'.$pickup_location.': </b>'.$pickup_location_week_days_name.'</p>';
							}
							
						}

						echo '</div>
					</div>
				</div>';
			}

			echo '<div id="coderockz_woo_delivery_delivery_pickup_section" style="display:none;">';
			
			woocommerce_form_field('coderockz_woo_delivery_pickup_location_field',
			[
				'type' => 'select',
				'class' =>
				[
					'coderockz_woo_delivery_pickup_location_field form-row-wide',
				],
				'label' => __($pickup_location_field_label, 'coderockz-woo-delivery'),
				'placeholder' => __($pickup_location_field_placeholder, 'coderockz-woo-delivery'),
				'options' => $fetch_pickup_locations,
				'required' => $pickup_location_mandatory,
				'custom_attributes' => [
					'data-location_url' => json_encode($pickup_location_map_url),
					'data-pickup_location_limit_notice' => __($pickup_location_limit_notice, 'coderockz-woo-delivery'),
					'data-select_pickup_date_notice' => __($select_pickup_date_notice, 'coderockz-woo-delivery'),
					'data-location_map_click_here' => __($location_map_click_here, 'coderockz-woo-delivery'),
					'data-to_see_map_location' => __($to_see_map_location, 'coderockz-woo-delivery')

				],
			], WC()->checkout->get_value('coderockz_woo_delivery_pickup_location_field'));
			
			echo '<div class="coderockz_woo_delivery_pickup_location_url"></div>';
			echo '</div>';
		}

		$additional_field_settings = get_option('coderockz_woo_delivery_additional_field_settings');

		$enable_additional_field = (isset($additional_field_settings['enable_additional_field']) && !empty($additional_field_settings['enable_additional_field'])) ? $additional_field_settings['enable_additional_field'] : false;

		$additional_field_field_label = (isset($additional_field_settings['field_label']) && !empty($additional_field_settings['field_label'])) ? stripslashes($additional_field_settings['field_label']) : __( "Special Note About Delivery", 'coderockz-woo-delivery' );
		$additional_field_field_placeholder = (isset($additional_field_settings['field_placeholder']) && !empty($additional_field_settings['field_placeholder'])) ? stripslashes($additional_field_settings['field_placeholder']) : "";

		$additional_field_mandatory = (isset($additional_field_settings['additional_field_mandatory']) && !empty($additional_field_settings['additional_field_mandatory'])) ? $additional_field_settings['additional_field_mandatory'] : false;

		$additional_field_character_limit = (isset($additional_field_settings['character_limit']) && !empty($additional_field_settings['character_limit'])) ? $additional_field_settings['character_limit'] : "";
		$character_remaining_text = (isset($additional_field_settings['character_remaining_text']) && !empty($additional_field_settings['character_remaining_text'])) ? $additional_field_settings['character_remaining_text'] : __( "characters remaining", 'coderockz-woo-delivery' );

		if($enable_additional_field)
		{

			echo "<div id='coderockz_woo_delivery_additional_field_section' style='display:none'>";
			woocommerce_form_field('coderockz_woo_delivery_additional_field_field',
			[
				'type' => 'textarea',
				'class' =>
				[
					'coderockz_woo_delivery_additional_field_field form-row-wide',
				],
				'label' => __($additional_field_field_label, 'coderockz-woo-delivery'),
				'placeholder' => __($additional_field_field_placeholder, 'coderockz-woo-delivery'),
				'required' => $additional_field_mandatory,
				'maxlength' => $additional_field_character_limit,
				'custom_attributes' => [
					'data-character_limit' => $additional_field_character_limit,
					'data-character_remaining_text' => $character_remaining_text,
				],
			], WC()->checkout->get_value('coderockz_woo_delivery_additional_field_field'));
			echo "</div>";
		}

		echo "</div>";

		echo "</div>";
	
		}

	}

	/**
	 * Checkout Process
	*/	
	public function coderockz_woo_delivery_customise_checkout_field_process() {
		
		$exclude_settings = get_option('coderockz_woo_delivery_exclude_settings');

		$exclude_shipping_methods = (isset($exclude_settings['exclude_shipping_methods']) && !empty($exclude_settings['exclude_shipping_methods'])) ? $exclude_settings['exclude_shipping_methods'] : array();

		$exclude_shipping_method_title = (isset($exclude_settings['exclude_shipping_method_title']) && !empty($exclude_settings['exclude_shipping_method_title'])) ? $exclude_settings['exclude_shipping_method_title'] : array();

		$exclude_shipping_methods = array_merge($exclude_shipping_methods, $exclude_shipping_method_title);

		$exclude_shipping_methods = array_filter(array_merge($exclude_shipping_methods, $exclude_shipping_method_title), 'strlen');

		if(isset(WC()->session->get('shipping_for_package_0')['rates'])) {
			foreach( WC()->session->get('shipping_for_package_0')['rates'] as $method_id => $rate ){
				if( WC()->session->get('chosen_shipping_methods')[0] == $method_id ){
					$shipping_method_title = $rate->label; // The shipping method label name
					break;
				}
			}
		} else {
			$shipping_method_title = "";
		}
		
		if(!in_array($shipping_method_title, $exclude_shipping_methods)) {

			$timezone = $this->helper->get_the_timezone();
			date_default_timezone_set($timezone);
			$today = date('Y-m-d', time());

			$delivery_option_settings = get_option('coderockz_woo_delivery_option_delivery_settings');
			$delivery_date_settings = get_option('coderockz_woo_delivery_date_settings');
			$pickup_date_settings = get_option('coderockz_woo_delivery_pickup_date_settings');
			$delivery_time_settings = get_option('coderockz_woo_delivery_time_settings');
			$pickup_time_settings = get_option('coderockz_woo_delivery_pickup_settings');
			$enable_delivery_option = (isset($delivery_option_settings['enable_option_time_pickup']) && !empty($delivery_option_settings['enable_option_time_pickup'])) ? $delivery_option_settings['enable_option_time_pickup'] : false;

			$enable_delivery_date = (isset($delivery_date_settings['enable_delivery_date']) && !empty($delivery_date_settings['enable_delivery_date'])) ? $delivery_date_settings['enable_delivery_date'] : false;
			$delivery_date_mandatory = (isset($delivery_date_settings['delivery_date_mandatory']) && !empty($delivery_date_settings['delivery_date_mandatory'])) ? $delivery_date_settings['delivery_date_mandatory'] : false;

			$enable_pickup_date = (isset($pickup_date_settings['enable_pickup_date']) && !empty($pickup_date_settings['enable_pickup_date'])) ? $pickup_date_settings['enable_pickup_date'] : false;
			$pickup_date_mandatory = (isset($pickup_date_settings['pickup_date_mandatory']) && !empty($pickup_date_settings['pickup_date_mandatory'])) ? $pickup_date_settings['pickup_date_mandatory'] : false;


			$enable_delivery_time = (isset($delivery_time_settings['enable_delivery_time']) && !empty($delivery_time_settings['enable_delivery_time'])) ? $delivery_time_settings['enable_delivery_time'] : false;
			$delivery_time_mandatory = (isset($delivery_time_settings['delivery_time_mandatory']) && !empty($delivery_time_settings['delivery_time_mandatory'])) ? $delivery_time_settings['delivery_time_mandatory'] : false;


			$enable_pickup_time = (isset($pickup_time_settings['enable_pickup_time']) && !empty($pickup_time_settings['enable_pickup_time'])) ? $pickup_time_settings['enable_pickup_time'] : false;
			$pickup_time_mandatory = (isset($pickup_time_settings['pickup_time_mandatory']) && !empty($pickup_time_settings['pickup_time_mandatory'])) ? $pickup_time_settings['pickup_time_mandatory'] : false;

			$max_pickup_consider_location = (isset($pickup_time_settings['max_pickup_consider_location']) && !empty($pickup_time_settings['max_pickup_consider_location'])) ? $pickup_time_settings['max_pickup_consider_location'] : false;

			$disable_fields_for_downloadable_products = (isset(get_option('coderockz_woo_delivery_other_settings')['disable_fields_for_downloadable_products']) && !empty(get_option('coderockz_woo_delivery_other_settings')['disable_fields_for_downloadable_products'])) ? get_option('coderockz_woo_delivery_other_settings')['disable_fields_for_downloadable_products'] : false;

			$checkout_notice = get_option('coderockz_woo_delivery_localization_settings');
			$checkout_delivery_option_notice = (isset($checkout_notice['checkout_delivery_option_notice']) && !empty($checkout_notice['checkout_delivery_option_notice'])) ? stripslashes($checkout_notice['checkout_delivery_option_notice']) : __( "Please Select Your Order Type.", 'coderockz-woo-delivery' );
			$checkout_date_notice = (isset($checkout_notice['checkout_date_notice']) && !empty($checkout_notice['checkout_date_notice'])) ? stripslashes($checkout_notice['checkout_date_notice']) : __( "Please Enter Delivery Date.", 'coderockz-woo-delivery' );
			$checkout_pickup_date_notice = (isset($checkout_notice['checkout_pickup_date_notice']) && !empty($checkout_notice['checkout_pickup_date_notice'])) ? stripslashes($checkout_notice['checkout_pickup_date_notice']) : __( "Please Enter Pickup Date.", 'coderockz-woo-delivery' );
			$checkout_time_notice = (isset($checkout_notice['checkout_time_notice']) && !empty($checkout_notice['checkout_time_notice'])) ? stripslashes($checkout_notice['checkout_time_notice']) : __( "Please Enter Delivery Time.", 'coderockz-woo-delivery' );	
			$checkout_pickup_time_notice = (isset($checkout_notice['checkout_pickup_time_notice']) && !empty($checkout_notice['checkout_pickup_time_notice'])) ? stripslashes($checkout_notice['checkout_pickup_time_notice']) : __( "Please Enter Pickup Time.", 'coderockz-woo-delivery' );	
			$checkout_pickup_notice = (isset($checkout_notice['checkout_pickup_notice']) && !empty($checkout_notice['checkout_pickup_notice'])) ? stripslashes($checkout_notice['checkout_pickup_notice']) : __( "Please Enter Pickup Location.", 'coderockz-woo-delivery' );
			$checkout_additional_field_notice = (isset($checkout_notice['checkout_additional_field_notice']) && !empty($checkout_notice['checkout_additional_field_notice'])) ? stripslashes($checkout_notice['checkout_additional_field_notice']) : __( "Please Enter Special Note for Delivery.", 'coderockz-woo-delivery' );
			

			$has_virtual_downloadable_products = $this->helper->check_virtual_downloadable_products();

			$exclude_condition = $this->helper->detect_exclude_condition();

			$other_settings = get_option('coderockz_woo_delivery_other_settings');

			$cart_total = WC()->cart->get_cart_contents_total();

			$hide_module_cart_total_zero = (isset($other_settings['hide_module_cart_total_zero']) && !empty($other_settings['hide_module_cart_total_zero'])) ? $other_settings['hide_module_cart_total_zero'] : false;

			if($hide_module_cart_total_zero && $cart_total == 0) {
				$cart_total_zero = true;
			} else {
				$cart_total_zero = false;
			}

			$exclude_user_roles_condition = $this->helper->detect_exclude_user_roles_condition();


			if(isset($_COOKIE['coderockz_woo_delivery_option_time_pickup'])) {
			  $delivery_option_session = $_COOKIE['coderockz_woo_delivery_option_time_pickup'];
			} elseif(!is_null(WC()->session)) {
			  $delivery_option_session = WC()->session->get( 'coderockz_woo_delivery_option_time_pickup' );
			}

			if ($enable_delivery_option && (!$has_virtual_downloadable_products || $disable_fields_for_downloadable_products) && !$exclude_condition && !$cart_total_zero && !$exclude_user_roles_condition) {
				if (!$_POST['coderockz_woo_delivery_delivery_selection_box']) wc_add_notice(__($checkout_delivery_option_notice, 'coderockz-woo-delivery') , 'error');
			}

			// if the field is set, if not then show an error message.

			if(($enable_delivery_option && isset($delivery_option_session) && $delivery_option_session == "delivery") && $enable_delivery_date && $delivery_date_mandatory && (!$has_virtual_downloadable_products || $disable_fields_for_downloadable_products) && !$exclude_condition && !$cart_total_zero && !$exclude_user_roles_condition) {
				if (!$_POST['coderockz_woo_delivery_date_field']) wc_add_notice(__($checkout_date_notice, 'coderockz-woo-delivery') , 'error');
			} elseif (!$enable_delivery_option && $enable_delivery_date && $delivery_date_mandatory && (!$has_virtual_downloadable_products || $disable_fields_for_downloadable_products) && !$exclude_condition && !$cart_total_zero && !$exclude_user_roles_condition) {
				if (!$_POST['coderockz_woo_delivery_date_field']) wc_add_notice(__($checkout_date_notice, 'coderockz-woo-delivery') , 'error');
			}


			if(($enable_delivery_option && isset($delivery_option_session) && $delivery_option_session == "pickup") && $enable_pickup_date && $pickup_date_mandatory && (!$has_virtual_downloadable_products || $disable_fields_for_downloadable_products) && !$exclude_condition && !$cart_total_zero && !$exclude_user_roles_condition) {
				if (!$_POST['coderockz_woo_delivery_pickup_date_field']) wc_add_notice(__($checkout_pickup_date_notice, 'coderockz-woo-delivery') , 'error');
			} elseif (!$enable_delivery_option && $enable_pickup_date && $pickup_date_mandatory && (!$has_virtual_downloadable_products || $disable_fields_for_downloadable_products) && !$exclude_condition && !$cart_total_zero && !$exclude_user_roles_condition) {
				if (!$_POST['coderockz_woo_delivery_pickup_date_field']) wc_add_notice(__($checkout_pickup_date_notice, 'coderockz-woo-delivery') , 'error');
			}


			// if the field is set, if not then show an error message.
			if(($enable_delivery_option && isset($delivery_option_session) && $delivery_option_session == "delivery") && $enable_delivery_time && $delivery_time_mandatory && (!$has_virtual_downloadable_products || $disable_fields_for_downloadable_products) && !$exclude_condition && !$cart_total_zero && !$exclude_user_roles_condition) {
				if (!$_POST['coderockz_woo_delivery_time_field']) wc_add_notice(__($checkout_time_notice, 'coderockz-woo-delivery') , 'error');
				if(($enable_delivery_date && $_POST['coderockz_woo_delivery_date_field'] && !empty($_POST['coderockz_woo_delivery_date_field'])) && ($enable_delivery_time && $_POST['coderockz_woo_delivery_time_field'] && $_POST['coderockz_woo_delivery_time_field'] != "" && $_POST['coderockz_woo_delivery_time_field'] != "as-soon-as-possible" && $_POST['coderockz_woo_delivery_time_field'] != "conditional-delivery")) {
					$this->check_delivery_quantity_before_placed($_POST['coderockz_woo_delivery_date_field'],$_POST['coderockz_woo_delivery_time_field']);
				} elseif((!$enable_delivery_date) && ($enable_delivery_time && $_POST['coderockz_woo_delivery_time_field'] && $_POST['coderockz_woo_delivery_time_field'] != "" && $_POST['coderockz_woo_delivery_time_field'] != "as-soon-as-possible" && $_POST['coderockz_woo_delivery_time_field'] != "conditional-delivery")) {

					$this->check_delivery_quantity_before_placed($today,$_POST['coderockz_woo_delivery_time_field'],true);

				}
				
			} elseif (!$enable_delivery_option && $enable_delivery_time && $delivery_time_mandatory && (!$has_virtual_downloadable_products || $disable_fields_for_downloadable_products) && !$exclude_condition && !$cart_total_zero && !$exclude_user_roles_condition) {
				if (!$_POST['coderockz_woo_delivery_time_field']) wc_add_notice(__($checkout_time_notice, 'coderockz-woo-delivery') , 'error');
				if(($enable_delivery_date && $_POST['coderockz_woo_delivery_date_field'] && !empty($_POST['coderockz_woo_delivery_date_field'])) && ($enable_delivery_time && $_POST['coderockz_woo_delivery_time_field'] && !empty($_POST['coderockz_woo_delivery_time_field']) && $_POST['coderockz_woo_delivery_time_field'] != "as-soon-as-possible" && $_POST['coderockz_woo_delivery_time_field'] != "conditional-delivery")) {
					$this->check_delivery_quantity_before_placed($_POST['coderockz_woo_delivery_date_field'],$_POST['coderockz_woo_delivery_time_field']);
				} elseif((!$enable_delivery_date) && ($enable_delivery_time && $_POST['coderockz_woo_delivery_time_field'] && !empty($_POST['coderockz_woo_delivery_time_field']) && $_POST['coderockz_woo_delivery_time_field'] != "as-soon-as-possible" && $_POST['coderockz_woo_delivery_time_field'] != "conditional-delivery")) {

					$this->check_delivery_quantity_before_placed($today,$_POST['coderockz_woo_delivery_time_field'],true);

				}
			}
			
			// if the field is set, if not then show an error message.
			if(($enable_delivery_option && isset($delivery_option_session) && $delivery_option_session == "pickup") && $enable_pickup_time && $pickup_time_mandatory && (!$has_virtual_downloadable_products || $disable_fields_for_downloadable_products) && !$exclude_condition && !$cart_total_zero && !$exclude_user_roles_condition) {
				if (!$_POST['coderockz_woo_delivery_pickup_time_field']) wc_add_notice(__($checkout_pickup_time_notice, 'coderockz-woo-delivery') , 'error');

				if(($enable_pickup_date && $_POST['coderockz_woo_delivery_pickup_date_field'] && !empty($_POST['coderockz_woo_delivery_pickup_date_field'])) && ($enable_pickup_time && $_POST['coderockz_woo_delivery_pickup_time_field'] && !empty($_POST['coderockz_woo_delivery_pickup_time_field']))) {
					if($max_pickup_consider_location && $_POST['coderockz_woo_delivery_pickup_location_field']) {
						$pickup_location = $_POST['coderockz_woo_delivery_pickup_location_field'];
					} else {
						$pickup_location = "";
					}
					$this->check_pickup_quantity_before_placed($_POST['coderockz_woo_delivery_pickup_date_field'],$_POST['coderockz_woo_delivery_pickup_time_field'],$pickup_location,false);
				} elseif((!$enable_pickup_date) && ($enable_pickup_time && $_POST['coderockz_woo_delivery_pickup_time_field'] && !empty($_POST['coderockz_woo_delivery_pickup_time_field']))) {
					if($max_pickup_consider_location && $_POST['coderockz_woo_delivery_pickup_location_field']) {
						$pickup_location = $_POST['coderockz_woo_delivery_pickup_location_field'];
					} else {
						$pickup_location = "";
					}
					$this->check_pickup_quantity_before_placed($today,$_POST['coderockz_woo_delivery_pickup_time_field'],$pickup_location,true);

				}



			} elseif(!$enable_delivery_option && $enable_pickup_time && $pickup_time_mandatory && (!$has_virtual_downloadable_products || $disable_fields_for_downloadable_products) && !$exclude_condition && !$cart_total_zero && !$exclude_user_roles_condition) {
				if (!$_POST['coderockz_woo_delivery_pickup_time_field']) wc_add_notice(__($checkout_pickup_time_notice, 'coderockz-woo-delivery') , 'error');

				if(($enable_pickup_date && $_POST['coderockz_woo_delivery_pickup_date_field'] && !empty($_POST['coderockz_woo_delivery_pickup_date_field'])) && ($enable_pickup_time && $_POST['coderockz_woo_delivery_pickup_time_field'] && !empty($_POST['coderockz_woo_delivery_pickup_time_field']))) {
					if($max_pickup_consider_location && $_POST['coderockz_woo_delivery_pickup_location_field']) {
						$pickup_location = $_POST['coderockz_woo_delivery_pickup_location_field'];
					} else {
						$pickup_location = "";
					}
					$this->check_pickup_quantity_before_placed($_POST['coderockz_woo_delivery_pickup_date_field'],$_POST['coderockz_woo_delivery_pickup_time_field'],$pickup_location,false);
				} elseif((!$enable_pickup_date) && ($enable_pickup_time && $_POST['coderockz_woo_delivery_pickup_time_field'] && !empty($_POST['coderockz_woo_delivery_pickup_time_field']))) {
					if($max_pickup_consider_location && $_POST['coderockz_woo_delivery_pickup_location_field']) {
						$pickup_location = $_POST['coderockz_woo_delivery_pickup_location_field'];
					} else {
						$pickup_location = "";
					}
					$this->check_pickup_quantity_before_placed($today,$_POST['coderockz_woo_delivery_pickup_time_field'],$pickup_location,true);

				}
			}


			$pickup_location_settings = get_option('coderockz_woo_delivery_pickup_location_settings');
			$enable_pickup_location = (isset($pickup_location_settings['enable_pickup_location']) && !empty($pickup_location_settings['enable_pickup_location'])) ? $pickup_location_settings['enable_pickup_location'] : false;
			$pickup_location_mandatory = (isset($pickup_location_settings['pickup_location_mandatory']) && !empty($pickup_location_settings['pickup_location_mandatory'])) ? $pickup_location_settings['pickup_location_mandatory'] : false;
			// if the field is set, if not then show an error message.
			if(($enable_delivery_option && isset($delivery_option_session) && $delivery_option_session == "pickup") && $enable_pickup_location && $pickup_location_mandatory && (!$has_virtual_downloadable_products || $disable_fields_for_downloadable_products) && !$exclude_condition && !$cart_total_zero && !$exclude_user_roles_condition) {
				if (!$_POST['coderockz_woo_delivery_pickup_location_field']) wc_add_notice(__($checkout_pickup_notice, 'coderockz-woo-delivery') , 'error');
			} elseif(!$enable_delivery_option && $enable_pickup_location && $pickup_location_mandatory && (!$has_virtual_downloadable_products || $disable_fields_for_downloadable_products) && !$exclude_condition && !$cart_total_zero && !$exclude_user_roles_condition) {
				if (!$_POST['coderockz_woo_delivery_pickup_location_field']) wc_add_notice(__($checkout_pickup_notice, 'coderockz-woo-delivery') , 'error');
			}

			$additional_field_settings = get_option('coderockz_woo_delivery_additional_field_settings');
			$enable_additional_field = (isset($additional_field_settings['enable_additional_field']) && !empty($additional_field_settings['enable_additional_field'])) ? $additional_field_settings['enable_additional_field'] : false;
			$additional_field_mandatory = (isset($additional_field_settings['additional_field_mandatory']) && !empty($additional_field_settings['additional_field_mandatory'])) ? $additional_field_settings['additional_field_mandatory'] : false;
			$hide_additional_field_for = (isset($additional_field_settings['hide_additional_field_for']) && !empty($additional_field_settings['hide_additional_field_for'])) ? $additional_field_settings['hide_additional_field_for'] : array();
			// if the field is set, if not then show an error message.
			if($enable_additional_field && $additional_field_mandatory && !in_array($delivery_option_session,$hide_additional_field_for) && (!$has_virtual_downloadable_products || $disable_fields_for_downloadable_products) && !$exclude_condition && !$cart_total_zero && !$exclude_user_roles_condition) {
				if (!$_POST['coderockz_woo_delivery_additional_field_field']) wc_add_notice(__($checkout_additional_field_notice, 'coderockz-woo-delivery') , 'error');
			}

		}
		
	}


	public function check_delivery_quantity_before_placed($delivery_date,$delivery_time,$no_delivery_date = false) {

		$delivery_time_settings = get_option('coderockz_woo_delivery_time_settings');
		$timezone = $this->helper->get_the_timezone();
		date_default_timezone_set($timezone);
		$delivery_time = sanitize_text_field($delivery_time);
		$order_status_keys = array_keys(wc_get_order_statuses());
		$order_status = ['partially-paid'];
		foreach($order_status_keys as $order_status_key) {
			$order_status[] = substr($order_status_key,3);
		}
		$order_status = array_diff($order_status,['cancelled','failed','refunded']);
	    

		$free_up_slot_for_delivery_completed = (isset($delivery_time_settings['free_up_slot_for_delivery_completed']) && !empty($delivery_time_settings['free_up_slot_for_delivery_completed'])) ? $delivery_time_settings['free_up_slot_for_delivery_completed'] : false;

	    if($no_delivery_date) {
			$order_date = date("Y-m-d", (int)sanitize_text_field(strtotime($delivery_date)));
			$selected_date = $order_date; 
			$args = array(
		        'limit' => -1,
		        'date_created' => $order_date,
		        'delivery_time' => $delivery_time,
		        'delivery_type' => "delivery",
		        'status' => $order_status,
		        'return' => 'ids'
		    );



		} else {
			$en_date = $this->helper->weekday_conversion($this->helper->date_conversion(sanitize_text_field($delivery_date),"delivery"),"delivery");
			$selected_date = date("Y-m-d", strtotime($en_date));
			$args = array(
		        'limit' => -1,
		        'delivery_date' => date("Y-m-d", strtotime($en_date)),
		        'delivery_time' => $delivery_time,
		        'delivery_type' => "delivery",
		        'status' => $order_status,
		        'return' => 'ids'
		    );		    
		}

		$order_ids = wc_get_orders( $args );
		$order_count = [];
		foreach($order_ids as $order_id) {
			if(!$free_up_slot_for_delivery_completed) {
				$order_count[] = $order_id;
			} else {
				if(!metadata_exists('post', $order_id, 'delivery_status')) {
					$order_count[] = $order_id;
				}
			}
		}

	    if($delivery_time != "") {
        	if(strpos($delivery_time, ' - ') !== false) {
        		$delivery_times = explode(' - ', $delivery_time);
				$slot_key_one = explode(':', $delivery_times[0]);
				$slot_key_two = explode(':', $delivery_times[1]);
				$delivery_time = ((int)$slot_key_one[0]*60+(int)$slot_key_one[1]).' - '.((int)$slot_key_two[0]*60+(int)$slot_key_two[1]);
				$delivery_times = explode(" - ",$delivery_time);
				$delivery_time_last_time = ((int)$slot_key_two[0]*60+(int)$slot_key_two[1]);
        	} else {
        		$delivery_times = [];
        		$slot_key_one = explode(':', $delivery_time);
        		$delivery_time = ((int)$slot_key_one[0]*60+(int)$slot_key_one[1]);
        		$delivery_times[] = $delivery_time;
        		$delivery_time_last_time = ((int)$slot_key_one[0]*60+(int)$slot_key_one[1]);
        	}
    		
		}

		$today = date('Y-m-d', time());
		$current_time = (date("G")*60)+date("i");

		if($today == $selected_date && $current_time > $delivery_time_last_time) wc_add_notice(__('Selected delivery time already passed. Please Reload The Page') , 'error');

		$custom_time_slot_settings = get_option('coderockz_woo_delivery_time_slot_settings');
		$enable_custom_time_slot = (isset($custom_time_slot_settings['enable_custom_time_slot']) && !empty($custom_time_slot_settings['enable_custom_time_slot'])) ? $custom_time_slot_settings['enable_custom_time_slot'] : false;
		if($enable_custom_time_slot) {
			if(isset($custom_time_slot_settings['time_slot']) && count($custom_time_slot_settings['time_slot'])>0){

				foreach($custom_time_slot_settings['time_slot'] as $key => $individual_time_slot) {

		  			if($individual_time_slot['enable']) {
			  			$key = preg_replace('/-/', ' - ', $key);

			  			$key_array = explode(" - ",$key);

					    $max_order = (isset($individual_time_slot['max_order']) && $individual_time_slot['max_order'] != "") ? $individual_time_slot['max_order'] : 10000000000000;

					    if(!empty($delivery_time) && isset($delivery_times[1]) && $delivery_times[0]>= $key_array[0] && $delivery_times[1] <= $key_array[1]) {

				    		$time_max_order = (int)$max_order;

					    } elseif(!empty($delivery_time) && !isset($delivery_times[1]) && $delivery_times[0]>= $key_array[0] && $delivery_times[0] < $key_array[1]) {

					    	$time_max_order = (int)$max_order;
					    }

					}
				}
				
				if (count($order_count)>=$time_max_order) wc_add_notice(__('Maximum Order Limit Exceed For This Time Slot. Please Reload The Page') , 'error');

			}
		} else {

		    $time_settings = get_option('coderockz_woo_delivery_time_settings');
	  		$x = (int)$time_settings['delivery_time_starts'];
	  		$each_time_slot = (isset($time_settings['each_time_slot']) && !empty($time_settings['each_time_slot'])) ? (int)$time_settings['each_time_slot'] : (int)$time_settings['delivery_time_ends']-(int)$time_settings['delivery_time_starts'];
	  		$max_order = (isset($time_settings['max_order_per_slot']) && $time_settings['max_order_per_slot'] != "") ? $time_settings['max_order_per_slot'] : 10000000000000;

			while((int)$time_settings['delivery_time_ends']>$x) {
				$second_time = $x+$each_time_slot;
				if($second_time > (int)$time_settings['delivery_time_ends']) {
					$second_time = (int)$time_settings['delivery_time_ends'];
				}
				$key = $x . ' - ' . $second_time; 
				if(!empty($delivery_time) && ($delivery_time == $key) ) {	
					$time_max_order = (int)$max_order;
					if (count($order_count)>=$time_max_order) {
						wc_add_notice(__('Maximum Order Limit Exceed For This Time Slot. Please Reload The Page') , 'error');
					}

					break; 
			    }
				$x = $second_time;
			}
		}

	}


	public function check_pickup_quantity_before_placed($pickup_date,$pickup_time,$pickup_location,$no_pickup_date = false) {
		
		$pickup_settings = get_option('coderockz_woo_delivery_pickup_settings');
		$timezone = $this->helper->get_the_timezone();
		date_default_timezone_set($timezone);
		$pickup_time = sanitize_text_field($pickup_time);
		$order_status_keys = array_keys(wc_get_order_statuses());
		$order_status = ['partially-paid'];
		foreach($order_status_keys as $order_status_key) {
			$order_status[] = substr($order_status_key,3);
		}
		$order_status = array_diff($order_status,['cancelled','failed','refunded']);

		$free_up_slot_for_pickup_completed = (isset($pickup_settings['free_up_slot_for_pickup_completed']) && !empty($pickup_settings['free_up_slot_for_pickup_completed'])) ? $pickup_settings['free_up_slot_for_pickup_completed'] : false;

	    if($no_pickup_date) {
			$order_date = date("Y-m-d", (int)sanitize_text_field(strtotime($pickup_date)));
			$selected_date = $order_date;
			if($pickup_location != "") {
				$args = array(
			        'limit' => -1,
			        'date_created' => $order_date,
			        'pickup_time' => $pickup_time,
			        'delivery_pickup' => $pickup_location,
			        'delivery_type' => "pickup",
			        'status' => $order_status,
			        'return' => 'ids'
			    );
			} else {
				$args = array(
			        'limit' => -1,
			        'date_created' => $order_date,
			        'pickup_time' => $pickup_time,
			        'delivery_type' => "pickup",
			        'status' => $order_status,
			        'return' => 'ids'
			    );
			}
			

		} else {
			$en_date = $this->helper->weekday_conversion($this->helper->date_conversion(sanitize_text_field($pickup_date),"pickup"),"pickup");
			$selected_date = date("Y-m-d", strtotime($en_date));
			if($pickup_location != "") {
				$args = array(
			        'limit' => -1,
			        'pickup_date' => date("Y-m-d", strtotime($en_date)),
			        'pickup_time' => $pickup_time,
			        'delivery_pickup' => $pickup_location,
			        'delivery_type' => "pickup",
			        'status' => $order_status,
			        'return' => 'ids'
			    ); 
			} else {
				$args = array(
			        'limit' => -1,
			        'pickup_date' => date("Y-m-d", strtotime($en_date)),
			        'pickup_time' => $pickup_time,
			        'delivery_type' => "pickup",
			        'status' => $order_status,
			        'return' => 'ids'
			    );
			}
					    
		}

	    $order_ids = wc_get_orders( $args );
		$order_count = [];
		foreach($order_ids as $order_id) {
			if(!$free_up_slot_for_pickup_completed) {
				$order_count[] = $order_id;
			} else {
				if(!metadata_exists('post', $order_id, 'delivery_status')) {
					$order_count[] = $order_id;
				}
			}
		}

	    if($pickup_time != "") {
        	if(strpos($pickup_time, ' - ') !== false) {
        		$pickup_times = explode(' - ', $pickup_time);
				$slot_key_one = explode(':', $pickup_times[0]);
				$slot_key_two = explode(':', $pickup_times[1]);
				$pickup_time = ((int)$slot_key_one[0]*60+(int)$slot_key_one[1]).' - '.((int)$slot_key_two[0]*60+(int)$slot_key_two[1]);
				$pickup_times = explode(" - ",$pickup_time);
				$pickup_time_last_time = ((int)$slot_key_two[0]*60+(int)$slot_key_two[1]);
        	} else {
        		$pickup_times = [];
        		$slot_key_one = explode(':', $pickup_time);
        		$pickup_time = ((int)$slot_key_one[0]*60+(int)$slot_key_one[1]);
        		$pickup_times[] = $pickup_time;
        		$pickup_time_last_time = ((int)$slot_key_one[0]*60+(int)$slot_key_one[1]);
        	}
    		
		}

		$today = date('Y-m-d', time());
		$current_time = (date("G")*60)+date("i");

		if($today == $selected_date && $current_time > $pickup_time_last_time) wc_add_notice(__('Selected pickup time already passed. Please Reload The Page') , 'error');

		$custom_pickup_slot_settings = get_option('coderockz_woo_delivery_pickup_slot_settings');
		$enable_custom_pickup_slot = (isset($custom_pickup_slot_settings['enable_custom_pickup_slot']) && !empty($custom_pickup_slot_settings['enable_custom_pickup_slot'])) ? $custom_pickup_slot_settings['enable_custom_pickup_slot'] : false;
		if($enable_custom_pickup_slot) {
			if(isset($custom_pickup_slot_settings['time_slot']) && count($custom_pickup_slot_settings['time_slot'])>0){

				foreach($custom_pickup_slot_settings['time_slot'] as $key => $individual_pickup_slot) {

		  			if($individual_pickup_slot['enable']) {
			  			$key = preg_replace('/-/', ' - ', $key);

			  			$key_array = explode(" - ",$key);

					    $max_order = (isset($individual_pickup_slot['max_order']) && $individual_pickup_slot['max_order'] != "") ? $individual_pickup_slot['max_order'] : 10000000000000;

					    if(!empty($pickup_time) && isset($pickup_times[1]) && $pickup_times[0]>= $key_array[0] && $pickup_times[1] <= $key_array[1]) {

				    		$pickup_max_order = (int)$max_order;

					    } elseif(!empty($pickup_time) && !isset($pickup_times[1]) && $pickup_times[0]>= $key_array[0] && $pickup_times[0] < $key_array[1]) {

					    	$pickup_max_order = (int)$max_order;
					    }

					}
				}
				if (count($order_count)>=$pickup_max_order) wc_add_notice(__('Maximum Order Limit Exceed For This Pickup Slot. Please Reload The Page') , 'error');
			}
		} else {

		    $pickup_settings = get_option('coderockz_woo_delivery_pickup_settings');
	  		$x = (int)$pickup_settings['pickup_time_starts'];
	  		$each_time_slot = (isset($pickup_settings['each_time_slot']) && !empty($pickup_settings['each_time_slot'])) ? (int)$pickup_settings['each_time_slot'] : (int)$pickup_settings['pickup_time_ends']-(int)$pickup_settings['pickup_time_starts'];
	  		$max_order = (isset($pickup_settings['max_pickup_per_slot']) && $pickup_settings['max_pickup_per_slot'] != "") ? $pickup_settings['max_pickup_per_slot'] : 10000000000000;

			while((int)$pickup_settings['pickup_time_ends']>$x) {
				$second_time = $x+$each_time_slot;
				if($second_time > (int)$pickup_settings['pickup_time_ends']) {
					$second_time = (int)$pickup_settings['pickup_time_ends'];
				}
				$key = $x . ' - ' . $second_time; 
				if(!empty($pickup_time) && ($pickup_time == $key) ) {	
					$pickup_max_order = (int)$max_order;
					if (count($order_count)>=$pickup_max_order) {
						wc_add_notice(__('Maximum Order Limit Exceed For This Pickup Slot. Please Reload The Page') , 'error');
					}

					break; 
			    }
				$x = $second_time;
			}

		}

	}

	/**
	 * Update value of field
	*/
	public function coderockz_woo_delivery_customise_checkout_field_update_order_meta($order_id) {
		
		$delivery_time_settings = get_option('coderockz_woo_delivery_time_settings');
		$timezone = $this->helper->get_the_timezone();
		date_default_timezone_set($timezone);

		$date_timestamp = null;
		if(isset($_POST['coderockz_woo_delivery_date_field']) && $_POST['coderockz_woo_delivery_date_field'] != "") {
			$en_delivery_date = $this->helper->weekday_conversion($this->helper->date_conversion(sanitize_text_field($_POST['coderockz_woo_delivery_date_field']),"delivery"),"delivery");
			$date_timestamp = strtotime($en_delivery_date);
		}		
		
		if(isset($_POST['coderockz_woo_delivery_pickup_date_field']) && $_POST['coderockz_woo_delivery_pickup_date_field'] != "") {
			$en_pickup_date = $this->helper->weekday_conversion($this->helper->date_conversion(sanitize_text_field($_POST['coderockz_woo_delivery_pickup_date_field']),"pickup"),"pickup");
			$date_timestamp = strtotime($en_pickup_date);
		}
		
		$delivery_option_settings = get_option('coderockz_woo_delivery_option_delivery_settings');
		$delivery_date_settings = get_option('coderockz_woo_delivery_date_settings');
		$pickup_date_settings = get_option('coderockz_woo_delivery_pickup_date_settings');
		$pickup_time_settings = get_option('coderockz_woo_delivery_pickup_settings');
		$enable_delivery_option = (isset($delivery_option_settings['enable_option_time_pickup']) && !empty($delivery_option_settings['enable_option_time_pickup'])) ? $delivery_option_settings['enable_option_time_pickup'] : false;

		$enable_delivery_date = (isset($delivery_date_settings['enable_delivery_date']) && !empty($delivery_date_settings['enable_delivery_date'])) ? $delivery_date_settings['enable_delivery_date'] : false;

		$enable_pickup_date = (isset($pickup_date_settings['enable_pickup_date']) && !empty($pickup_date_settings['enable_pickup_date'])) ? $pickup_date_settings['enable_pickup_date'] : false;

		$enable_delivery_time = (isset($delivery_time_settings['enable_delivery_time']) && !empty($delivery_time_settings['enable_delivery_time'])) ? $delivery_time_settings['enable_delivery_time'] : false;
	  	
		$enable_pickup_time = (isset($pickup_time_settings['enable_pickup_time']) && !empty($pickup_time_settings['enable_pickup_time'])) ? $pickup_time_settings['enable_pickup_time'] : false;

		$disable_fields_for_downloadable_products = (isset(get_option('coderockz_woo_delivery_other_settings')['disable_fields_for_downloadable_products']) && !empty(get_option('coderockz_woo_delivery_other_settings')['disable_fields_for_downloadable_products'])) ? get_option('coderockz_woo_delivery_other_settings')['disable_fields_for_downloadable_products'] : false;

		$has_virtual_downloadable_products = $this->helper->check_virtual_downloadable_products();

		$exclude_condition = $this->helper->detect_exclude_condition();

		$other_settings = get_option('coderockz_woo_delivery_other_settings');

		$cart_total = WC()->cart->get_cart_contents_total();

		$hide_module_cart_total_zero = (isset($other_settings['hide_module_cart_total_zero']) && !empty($other_settings['hide_module_cart_total_zero'])) ? $other_settings['hide_module_cart_total_zero'] : false;

		if($hide_module_cart_total_zero && $cart_total == 0) {
			$cart_total_zero = true;
		} else {
			$cart_total_zero = false;
		}

		$exclude_user_roles_condition = $this->helper->detect_exclude_user_roles_condition();
	  	
		if ($enable_delivery_option && isset($_POST['coderockz_woo_delivery_delivery_selection_box']) && $_POST['coderockz_woo_delivery_delivery_selection_box'] != "" && !$exclude_condition && !$cart_total_zero && !$exclude_user_roles_condition) {
			update_post_meta($order_id, 'delivery_type', $_POST['coderockz_woo_delivery_delivery_selection_box']);
		} elseif(!$enable_delivery_option && (($enable_delivery_time && !$enable_pickup_time) || ($enable_delivery_date && !$enable_pickup_date)) && (!$has_virtual_downloadable_products || $disable_fields_for_downloadable_products) && !$exclude_condition && !$cart_total_zero && !$exclude_user_roles_condition) {
			update_post_meta($order_id, 'delivery_type', 'delivery');
		} elseif(!$enable_delivery_option && ((!$enable_delivery_time && $enable_pickup_time) || (!$enable_delivery_date && $enable_pickup_date)) && (!$has_virtual_downloadable_products || $disable_fields_for_downloadable_products) && !$exclude_condition && !$cart_total_zero && !$exclude_user_roles_condition) {
			update_post_meta($order_id, 'delivery_type', 'pickup');
		}


		if(isset($_COOKIE['coderockz_woo_delivery_option_time_pickup'])) {
		  $delivery_option_session = $_COOKIE['coderockz_woo_delivery_option_time_pickup'];
		} elseif(!is_null(WC()->session)) {
		  $delivery_option_session = WC()->session->get( 'coderockz_woo_delivery_option_time_pickup' );
		}

	  	if(($enable_delivery_option && isset($delivery_option_session) && $delivery_option_session == "delivery") && $enable_delivery_date && isset($_POST['coderockz_woo_delivery_date_field']) && $_POST['coderockz_woo_delivery_date_field'] != "" && (!$has_virtual_downloadable_products || $disable_fields_for_downloadable_products) && !$exclude_condition && !$cart_total_zero && !$exclude_user_roles_condition) {
			update_post_meta($order_id, 'delivery_date', date("Y-m-d", strtotime($en_delivery_date)));
		} elseif (!$enable_delivery_option && $enable_delivery_date && isset($_POST['coderockz_woo_delivery_date_field']) && $_POST['coderockz_woo_delivery_date_field'] != "" && (!$has_virtual_downloadable_products || $disable_fields_for_downloadable_products) && !$exclude_condition && !$cart_total_zero && !$exclude_user_roles_condition) {
			update_post_meta($order_id, 'delivery_date', date("Y-m-d", strtotime($en_delivery_date)));
		}

		if(($enable_delivery_option && isset($delivery_option_session) && $delivery_option_session == "pickup") && $enable_pickup_date && isset($_POST['coderockz_woo_delivery_pickup_date_field']) && $_POST['coderockz_woo_delivery_pickup_date_field'] != "" && (!$has_virtual_downloadable_products || $disable_fields_for_downloadable_products) && !$exclude_condition && !$cart_total_zero && !$exclude_user_roles_condition) {
			update_post_meta($order_id, 'pickup_date', date("Y-m-d", strtotime($en_pickup_date)));
		} elseif (!$enable_delivery_option && $enable_pickup_date && isset($_POST['coderockz_woo_delivery_pickup_date_field']) && $_POST['coderockz_woo_delivery_pickup_date_field'] != "" && (!$has_virtual_downloadable_products || $disable_fields_for_downloadable_products) && !$exclude_condition && !$cart_total_zero && !$exclude_user_roles_condition) {
			update_post_meta($order_id, 'pickup_date', date("Y-m-d", strtotime($en_pickup_date)));
		}

		$time_timestamp_start = null;
		$time_timestamp_end = null;
		$for_time_timestamp = null;
		if(($enable_delivery_option && isset($delivery_option_session) && $delivery_option_session == "delivery") && $enable_delivery_time && isset($_POST['coderockz_woo_delivery_time_field']) && $_POST['coderockz_woo_delivery_time_field'] != "" && (!$has_virtual_downloadable_products || $disable_fields_for_downloadable_products) && !$exclude_condition  && !$cart_total_zero && !$exclude_user_roles_condition) {
			if(strpos($_POST['coderockz_woo_delivery_time_field'],"conditional-delivery") !== false) {
				$delivery_fee_settings = get_option('coderockz_woo_delivery_fee_settings');
				$conditional_time = date("H:i", mktime(0, (int)((date("G")*60)+date("i")))) . " - ".date("H:i", mktime(0, (int)((date("G")*60)+date("i") + $delivery_fee_settings['conditional_delivery_fee_duration']))); 
				update_post_meta($order_id, 'delivery_time', $conditional_time);
				$for_time_timestamp = $conditional_time; 
			} else {
				update_post_meta($order_id, 'delivery_time', sanitize_text_field($_POST['coderockz_woo_delivery_time_field']));
				$for_time_timestamp = $_POST['coderockz_woo_delivery_time_field']; 
			}
			
		} elseif (!$enable_delivery_option && $enable_delivery_time && isset($_POST['coderockz_woo_delivery_time_field']) && $_POST['coderockz_woo_delivery_time_field'] != "" && (!$has_virtual_downloadable_products || $disable_fields_for_downloadable_products) && !$exclude_condition && !$cart_total_zero && !$exclude_user_roles_condition) {
			if(strpos($_POST['coderockz_woo_delivery_time_field'],"conditional-delivery") !== false) {
				$delivery_fee_settings = get_option('coderockz_woo_delivery_fee_settings');
				$conditional_time = date("H:i", mktime(0, (int)((date("G")*60)+date("i")))) . " - ".date("H:i", mktime(0, (int)((date("G")*60)+date("i") + $delivery_fee_settings['conditional_delivery_fee_duration']))); 
				update_post_meta($order_id, 'delivery_time', $conditional_time);
				$for_time_timestamp = $conditional_time;
			} else {
				update_post_meta($order_id, 'delivery_time', sanitize_text_field($_POST['coderockz_woo_delivery_time_field']));
				$for_time_timestamp = $_POST['coderockz_woo_delivery_time_field']; 
			}
		}

		if(isset($_POST['coderockz_woo_delivery_time_field']) && $_POST['coderockz_woo_delivery_time_field'] !="as-soon-as-possible" && $_POST['coderockz_woo_delivery_time_field'] != "" && !is_null($for_time_timestamp)) {
			$minutes = $for_time_timestamp;

	    	$slot_key = explode(' - ', $minutes);
			$slot_key_one = explode(':', $slot_key[0]);
			$time_timestamp_start = ((int)$slot_key_one[0]*60+(int)$slot_key_one[1]);

	    	if(!isset($slot_key[1])) {
	    		$time_timestamp_end = 0;
	    	} else {
	    		$slot_key_two = explode(':', $slot_key[1]);
	    		$time_timestamp_end = ((int)$slot_key_two[0]*60+(int)$slot_key_two[1]);
	    	}
		}

		if(($enable_delivery_option && isset($delivery_option_session) && $delivery_option_session == "pickup") && $enable_pickup_time && isset($_POST['coderockz_woo_delivery_pickup_time_field']) && $_POST['coderockz_woo_delivery_pickup_time_field'] != "" && (!$has_virtual_downloadable_products || $disable_fields_for_downloadable_products) && !$exclude_condition && !$cart_total_zero && !$exclude_user_roles_condition) {
			update_post_meta($order_id, 'pickup_time', sanitize_text_field($_POST['coderockz_woo_delivery_pickup_time_field']));
			$for_time_timestamp = $_POST['coderockz_woo_delivery_pickup_time_field'];
		} elseif(!$enable_delivery_option && $enable_pickup_time && isset($_POST['coderockz_woo_delivery_pickup_time_field']) && $_POST['coderockz_woo_delivery_pickup_time_field'] != "" && (!$has_virtual_downloadable_products || $disable_fields_for_downloadable_products) && !$exclude_condition && !$cart_total_zero && !$exclude_user_roles_condition) {
			update_post_meta($order_id, 'pickup_time', sanitize_text_field($_POST['coderockz_woo_delivery_pickup_time_field']));
			$for_time_timestamp = $_POST['coderockz_woo_delivery_pickup_time_field'];
		}

		if(isset($_POST['coderockz_woo_delivery_pickup_time_field']) && $_POST['coderockz_woo_delivery_pickup_time_field'] != "" && !is_null($for_time_timestamp)) {
			$minutes = $for_time_timestamp;

	    	$slot_key = explode(' - ', $minutes);
			$slot_key_one = explode(':', $slot_key[0]);
			$time_timestamp_start = ((int)$slot_key_one[0]*60+(int)$slot_key_one[1]);

	    	if(!isset($slot_key[1])) {
	    		$time_timestamp_end = 0;
	    	} else {
	    		$slot_key_two = explode(':', $slot_key[1]);
	    		$time_timestamp_end = ((int)$slot_key_two[0]*60+(int)$slot_key_two[1]);
	    	}
		}
		$delivery_details_in_timestamp = 0;
		if(!is_null($date_timestamp)) {
			$delivery_details_in_timestamp = $delivery_details_in_timestamp+(int)$date_timestamp;
		}
		if(!is_null($time_timestamp_start)) {
			$delivery_details_in_timestamp = $delivery_details_in_timestamp+(int)$time_timestamp_start;
		}

		if(!is_null($time_timestamp_end)) {
			$delivery_details_in_timestamp = $delivery_details_in_timestamp+(int)$time_timestamp_end;
		}

		if($delivery_details_in_timestamp != 0) {
			update_post_meta($order_id, 'delivery_details_timestamp', $delivery_details_in_timestamp);
		}

	  	$pickup_location_settings = get_option('coderockz_woo_delivery_pickup_location_settings');
		$enable_pickup_location = (isset($pickup_location_settings['enable_pickup_location']) && !empty($pickup_location_settings['enable_pickup_location'])) ? $pickup_location_settings['enable_pickup_location'] : false;
	  	if(($enable_delivery_option && isset($delivery_option_session) && $delivery_option_session == "pickup") && $enable_pickup_location && isset($_POST['coderockz_woo_delivery_pickup_location_field']) && $_POST['coderockz_woo_delivery_pickup_location_field'] != "" && (!$has_virtual_downloadable_products || $disable_fields_for_downloadable_products) && !$exclude_condition && !$cart_total_zero && !$exclude_user_roles_condition) {
			update_post_meta($order_id, 'delivery_pickup', sanitize_text_field($_POST['coderockz_woo_delivery_pickup_location_field']));
		} elseif(!$enable_delivery_option && $enable_pickup_location && isset($_POST['coderockz_woo_delivery_pickup_location_field']) && $_POST['coderockz_woo_delivery_pickup_location_field'] != "" && (!$has_virtual_downloadable_products || $disable_fields_for_downloadable_products) && !$exclude_condition && !$cart_total_zero && !$exclude_user_roles_condition) {
			update_post_meta($order_id, 'delivery_pickup', sanitize_text_field($_POST['coderockz_woo_delivery_pickup_location_field']));
		}

	  	$additional_field_settings = get_option('coderockz_woo_delivery_additional_field_settings');
		$enable_additional_field = (isset($additional_field_settings['enable_additional_field']) && !empty($additional_field_settings['enable_additional_field'])) ? $additional_field_settings['enable_additional_field'] : false;
	  	if ($enable_additional_field && $_POST['coderockz_woo_delivery_additional_field_field'] != "" && (!$has_virtual_downloadable_products || $disable_fields_for_downloadable_products) && !$exclude_condition && !$cart_total_zero && !$exclude_user_roles_condition) {
			update_post_meta($order_id, 'additional_note', sanitize_textarea_field($_POST['coderockz_woo_delivery_additional_field_field']));
	  	}

	}

	public function coderockz_woo_delivery_option_delivery_time_pickup() {
		check_ajax_referer('coderockz_woo_delivery_nonce');
		//WC()->session->__unset( 'coderockz_woo_delivery_option_time_pickup' );
		/*if(isset($_COOKIE['coderockz_woo_delivery_option_time_pickup'])) {
		    unset($_COOKIE["coderockz_woo_delivery_option_time_pickup"]);
			setcookie("coderockz_woo_delivery_option_time_pickup", null, -1, '/');
		} 

		if(!is_null(WC()->session)) {		  
			WC()->session->__unset( 'coderockz_woo_delivery_option_time_pickup' );  
		}*/

		$delivery_option = (isset($_POST['deliveryOption']) && $_POST['deliveryOption'] !="") ? sanitize_text_field($_POST['deliveryOption']) : "";
		setcookie('coderockz_woo_delivery_option_time_pickup', $delivery_option, time() + 60 * 60, '/');
		WC()->session->set( 'coderockz_woo_delivery_option_time_pickup', $delivery_option );

		$delivery_date_settings = get_option('coderockz_woo_delivery_date_settings');
		$pickup_date_settings = get_option('coderockz_woo_delivery_pickup_date_settings');

		$delivery_option_settings = get_option('coderockz_woo_delivery_option_delivery_settings');
		$enable_delivery_option = (isset($delivery_option_settings['enable_option_time_pickup']) && !empty($delivery_option_settings['enable_option_time_pickup'])) ? $delivery_option_settings['enable_option_time_pickup'] : false;

		$timezone = $this->helper->get_the_timezone();
		date_default_timezone_set($timezone);

		$order_status_keys = array_keys(wc_get_order_statuses());
		$order_status = ['partially-paid'];
		foreach($order_status_keys as $order_status_key) {
			$order_status[] = substr($order_status_key,3);
		}
		$order_status = array_diff($order_status,['cancelled','failed','refunded']);

		$disable_for_max_delivery_dates = [];
		$disable_for_max_pickup_dates = [];
		$delivery_days_delivery = [];
		$need_checking_max_order = true;
		$pickup_days_delivery = [];
		$pickup_need_checking_max_order = true;
		$today = date('Y-m-d', time());
		$range_first_date = date('Y-m-d', strtotime($today));
		$formated_obj = new DateTime($range_first_date);


		$second_range_first_date = date('Y-m-d', strtotime($today));
		$second_formated_obj = new DateTime($second_range_first_date);
		$second_range_last_date = $second_formated_obj->modify("+10 day")->format("Y-m-d");
		$second_filtered_date = $second_range_first_date . ',' . $second_range_last_date;
		$second_filtered_dates = explode(',', $second_filtered_date);
		$second_period = new DatePeriod(new DateTime($second_filtered_dates[0]), new DateInterval('P1D'), new DateTime($second_filtered_dates[1].' +1 day'));
	    
		if($delivery_option == "delivery") {

			if(isset($delivery_date_settings['maximum_order_per_day']) && $delivery_date_settings['maximum_order_per_day'] != "") {

				$range_last_date = $formated_obj->modify("+40 day")->format("Y-m-d");
				$filtered_date = $range_first_date . ',' . $range_last_date;
				$filtered_dates = explode(',', $filtered_date);
				$period = new DatePeriod(new DateTime($filtered_dates[0]), new DateInterval('P1D'), new DateTime($filtered_dates[1].' +1 day'));


				$max_order_per_day = (isset($delivery_date_settings['maximum_order_per_day']) && $delivery_date_settings['maximum_order_per_day'] != "") ? (int)$delivery_date_settings['maximum_order_per_day'] : 10000000000000;
				foreach ($period as $date) { 
					$args = array(
				        'limit' => -1,
				        'delivery_date' => date("Y-m-d", strtotime($date->format("Y-m-d"))),
				        'delivery_type' => "delivery",
				        'status' => $order_status,
				        'return' => 'ids'
				    );
				    $orders_array = wc_get_orders( $args );

				    if(count($orders_array) >= $max_order_per_day) {
						$disable_for_max_delivery_dates[] = date('Y-m-d', strtotime($date->format("Y-m-d")));
				    }
				}

			}

			
			$custom_time_slot_settings = get_option('coderockz_woo_delivery_time_slot_settings');
			$enable_custom_time_slot = (isset($custom_time_slot_settings['enable_custom_time_slot']) && !empty($custom_time_slot_settings['enable_custom_time_slot'])) ? $custom_time_slot_settings['enable_custom_time_slot'] : false;
			if($enable_custom_time_slot) {
				if(isset($custom_time_slot_settings['time_slot']) && count($custom_time_slot_settings['time_slot'])>0 && count($custom_time_slot_settings['time_slot'])<=2){
					$max_order = true;
					foreach($custom_time_slot_settings['time_slot'] as $key => $individual_time_slot) {
						if((isset($individual_time_slot['max_order']) && $individual_time_slot['max_order'] !="")) {

						} else {
							$max_order = false;
							break;
						}
					}

					if($max_order) {

						foreach($custom_time_slot_settings['time_slot'] as $key => $individual_time_slot) {

				  			if($individual_time_slot['enable']) {
				  				
				  				if((isset($individual_time_slot['max_order']) && $individual_time_slot['max_order'] !="")) {

					  				$hide_date_timeslot = isset($individual_time_slot['only_specific_date_close']) && $individual_time_slot['only_specific_date_close'] !="" ? sanitize_text_field($individual_time_slot['only_specific_date_close']) : "";
					  				$hide_date_timeslot = explode(",",$hide_date_timeslot);
					  				$available_date_timeslot = isset($individual_time_slot['only_specific_date']) && $individual_time_slot['only_specific_date'] !="" ? sanitize_text_field($individual_time_slot['only_specific_date']) : "";
					  				$available_date_timeslot = explode(",",$available_date_timeslot);
					  				foreach($second_period as $delivery_day) {
					  					$delivery_day = date("Y-m-d", strtotime($delivery_day->format("Y-m-d")));
					  					if(isset($individual_time_slot['only_specific_date']) && $individual_time_slot['only_specific_date'] !="" && !in_array($delivery_day,$available_date_timeslot)) {
					  						continue;
					  					}
					  					if((!empty($available_date_timeslot) && in_array($delivery_day,$available_date_timeslot)) || (!in_array($delivery_day,$hide_date_timeslot) && !in_array(date('w', strtotime($delivery_day)),$individual_time_slot['disable_for']))) {
					  						
					  						$max_order = sanitize_text_field($individual_time_slot['max_order']);

								  			if($individual_time_slot['enable_split']) {
												$times = explode('-', $key);
												$x = $times[0];
												while($times[1]>$x) {
													$second_time = $x+$individual_time_slot['split_slot_duration'];
													if($second_time > $times[1]) {
														$second_time = $times[1];
													}
													if(isset($delivery_days_delivery[$delivery_day])) {
														$delivery_days_delivery[$delivery_day] = (int)$delivery_days_delivery[$delivery_day] + (int)$max_order;
													} else {
														$delivery_days_delivery[$delivery_day] = (int)$max_order;
													}
													
													
													$x = $second_time;
												}
											} else {
												if(isset($delivery_days_delivery[$delivery_day])) {
													$delivery_days_delivery[$delivery_day] = (int)$delivery_days_delivery[$delivery_day] + (int)$max_order;
												} else {
													$delivery_days_delivery[$delivery_day] = (int)$max_order;
												}
												
											}

					  					}
					  				}
				  				}
				  			}
				  		}
			  		} else {
  						$need_checking_max_order = false;
  					}
			  	}
			} else {

				$time_settings = get_option('coderockz_woo_delivery_time_settings');
			    $enable_delivery_time = (isset($time_settings['enable_delivery_time']) && !empty($time_settings['enable_delivery_time'])) ? $time_settings['enable_delivery_time'] : false;
			    $max_order = (isset($time_settings['max_order_per_slot']) && $time_settings['max_order_per_slot'] !="") ? sanitize_text_field($time_settings['max_order_per_slot']) : "";
			    if($enable_delivery_time && $max_order != "" && (int)$max_order <= 15) {
				    
			  		$x = (int)$time_settings['delivery_time_starts'];
			  		$each_delivery_slot = (isset($time_settings['each_time_slot']) && !empty($time_settings['each_time_slot'])) ? (int)$time_settings['each_time_slot'] : (int)$time_settings['delivery_time_ends']-(int)$time_settings['delivery_time_starts'];
					while((int)$time_settings['delivery_time_ends']>$x) {
						$second_time = $x+$each_delivery_slot;
						if($second_time > (int)$time_settings['delivery_time_ends']) {
							$second_time = (int)$time_settings['delivery_time_ends'];
						}

						foreach($second_period as $delivery_day) {
				  			$delivery_day = date("Y-m-d", strtotime($delivery_day->format("Y-m-d")));
				  			if(isset($delivery_days_delivery[$delivery_day])) {
								$delivery_days_delivery[$delivery_day] = (int)$delivery_days_delivery[$delivery_day] + (int)$max_order;
							} else {
								$delivery_days_delivery[$delivery_day] = (int)$max_order;
							}
				  		}	
						$x = $second_time;
					}
				} else {
					$need_checking_max_order = false;
				}
 		
		  	}

			if($need_checking_max_order) {
				foreach($second_period as $delivery_day) {
					if(isset($delivery_days_delivery[date("Y-m-d", strtotime($delivery_day->format("Y-m-d")))])) {
						$checking_max_order_args = array(
					        'limit' => -1,
					        'delivery_date' => date("Y-m-d", strtotime($delivery_day->format("Y-m-d"))),
					        'delivery_type' => "delivery",
					        'status' => $order_status,
					        'return' => 'ids'
					    );
					    $orders_array = wc_get_orders( $checking_max_order_args );
					    if(count($orders_array) >= (int)$delivery_days_delivery[date("Y-m-d", strtotime($delivery_day->format("Y-m-d")))]) {
							$disable_for_max_delivery_dates[] = date('Y-m-d', strtotime($delivery_day->format("Y-m-d")));
					    }
					}
				}
			}

			$disable_for_max_delivery_dates = array_unique($disable_for_max_delivery_dates, false);
			$disable_for_max_delivery_dates = array_values($disable_for_max_delivery_dates);

		} elseif($delivery_option == "pickup") {

			if(isset($pickup_date_settings['maximum_pickup_per_day']) && $pickup_date_settings['maximum_pickup_per_day'] != "") {

				$range_last_date = $formated_obj->modify("+40 day")->format("Y-m-d");
				$filtered_date = $range_first_date . ',' . $range_last_date;
				$filtered_dates = explode(',', $filtered_date);
				$period = new DatePeriod(new DateTime($filtered_dates[0]), new DateInterval('P1D'), new DateTime($filtered_dates[1].' +1 day'));

				$max_pickup_per_day = (isset($pickup_date_settings['maximum_pickup_per_day']) && $pickup_date_settings['maximum_pickup_per_day'] != "") ? (int)$pickup_date_settings['maximum_pickup_per_day'] : 10000000000000;
				foreach ($period as $date) {
					$args = array(
				        'limit' => -1,
				        'pickup_date' => date("Y-m-d", strtotime($date->format("Y-m-d"))),
				        'delivery_type' => "pickup",
				        'status' => $order_status,
				        'return' => 'ids'
				    );
				    $orders_array = wc_get_orders( $args );
				    if(count($orders_array) >= $max_pickup_per_day) {
						$disable_for_max_pickup_dates[] = date('Y-m-d', strtotime($date->format("Y-m-d")));
				    }
				}

			}

			$custom_pickup_slot_settings = get_option('coderockz_woo_delivery_pickup_slot_settings');
			$enable_custom_pickup_slot = (isset($custom_pickup_slot_settings['enable_custom_pickup_slot']) && !empty($custom_pickup_slot_settings['enable_custom_pickup_slot'])) ? $custom_pickup_slot_settings['enable_custom_pickup_slot'] : false;
			if($enable_custom_pickup_slot) {
				if(isset($custom_pickup_slot_settings['time_slot']) && count($custom_pickup_slot_settings['time_slot'])>0 && count($custom_pickup_slot_settings['time_slot'])<=2){
					$pickup_max_order = true;
					foreach($custom_pickup_slot_settings['time_slot'] as $key => $individual_pickup_slot) {
						if((isset($individual_pickup_slot['max_order']) && $individual_pickup_slot['max_order'] !="")) {

						} else {
							$pickup_max_order = false;
							break;
						}
					}

					if($pickup_max_order) {

						foreach($custom_pickup_slot_settings['time_slot'] as $key => $individual_pickup_slot) {

				  			if($individual_pickup_slot['enable']) {
				  				
				  				if((isset($individual_pickup_slot['max_order']) && $individual_pickup_slot['max_order'] !="")) {

					  				$pickup_hide_date_timeslot = isset($individual_pickup_slot['only_specific_date_close']) && $individual_pickup_slot['only_specific_date_close'] !="" ? sanitize_text_field($individual_pickup_slot['only_specific_date_close']) : "";
					  				$pickup_hide_date_timeslot = explode(",",$pickup_hide_date_timeslot);
					  				$pickup_available_date_timeslot = isset($individual_pickup_slot['only_specific_date']) && $individual_pickup_slot['only_specific_date'] !="" ? sanitize_text_field($individual_pickup_slot['only_specific_date']) : "";
					  				$pickup_available_date_timeslot = explode(",",$pickup_available_date_timeslot);
					  				foreach($second_period as $pickup_day) {
					  					$pickup_day = date("Y-m-d", strtotime($pickup_day->format("Y-m-d")));
					  					if(isset($individual_pickup_slot['only_specific_date']) && $individual_pickup_slot['only_specific_date'] !="" && !in_array($pickup_day,$pickup_available_date_timeslot)) {
					  						continue;
					  					}
					  					if((!empty($pickup_available_date_timeslot) && in_array($pickup_day,$pickup_available_date_timeslot)) || (!in_array($pickup_day,$pickup_hide_date_timeslot) && !in_array(date('w', strtotime($pickup_day)),$individual_pickup_slot['disable_for']))) {
					  						
					  						$max_order = sanitize_text_field($individual_pickup_slot['max_order']);

								  			if($individual_pickup_slot['enable_split']) {
												$times = explode('-', $key);
												$x = $times[0];
												while($times[1]>$x) {
													$second_time = $x+$individual_pickup_slot['split_slot_duration'];
													if($second_time > $times[1]) {
														$second_time = $times[1];
													}
													if(isset($pickup_days_delivery[$pickup_day])) {
														$pickup_days_delivery[$pickup_day] = (int)$pickup_days_delivery[$pickup_day] + (int)$max_order;
													} else {
														$pickup_days_delivery[$pickup_day] = (int)$max_order;
													}
													
													
													$x = $second_time;
												}
											} else {
												if(isset($pickup_days_delivery[$pickup_day])) {
													$pickup_days_delivery[$pickup_day] = (int)$pickup_days_delivery[$pickup_day] + (int)$max_order;
												} else {
													$pickup_days_delivery[$pickup_day] = (int)$max_order;
												}
												
											}

					  					}
					  				}
				  				}
				  			}
				  		}
			  		} else {
  						$pickup_need_checking_max_order = false;
  					}
			  	}
			} else {

				$pickup_settings = get_option('coderockz_woo_delivery_pickup_settings');
			    $enable_pickup_time = (isset($pickup_settings['enable_pickup_time']) && !empty($pickup_settings['enable_pickup_time'])) ? $pickup_settings['enable_pickup_time'] : false;
			    $max_order = (isset($pickup_settings['max_pickup_per_slot']) && $pickup_settings['max_pickup_per_slot'] !="") ? sanitize_text_field($pickup_settings['max_pickup_per_slot']) : "";
			    if($enable_pickup_time && $max_order != "" && (int)$max_order <= 15) {
				    
			  		$x = (int)$pickup_settings['pickup_time_starts'];
			  		$each_pickup_slot = (isset($pickup_settings['each_time_slot']) && !empty($pickup_settings['each_time_slot'])) ? (int)$pickup_settings['each_time_slot'] : (int)$pickup_settings['pickup_time_ends']-(int)$pickup_settings['pickup_time_starts'];
					while((int)$pickup_settings['pickup_time_ends']>$x) {
						$second_time = $x+$each_pickup_slot;
						if($second_time > (int)$pickup_settings['pickup_time_ends']) {
							$second_time = (int)$pickup_settings['pickup_time_ends'];
						}

						foreach($second_period as $pickup_day) {
				  			$pickup_day = date("Y-m-d", strtotime($pickup_day->format("Y-m-d")));
				  			if(isset($pickup_days_delivery[$pickup_day])) {
								$pickup_days_delivery[$pickup_day] = (int)$pickup_days_delivery[$pickup_day] + (int)$max_order;
							} else {
								$pickup_days_delivery[$pickup_day] = (int)$max_order;
							}
				  		}	
						$x = $second_time;
					}
				} else {
					$pickup_need_checking_max_order = false;
				}
 		
		  	}

			if($pickup_need_checking_max_order) {
				foreach($second_period as $pickup_day) {
					if(isset($pickup_days_delivery[date("Y-m-d", strtotime($pickup_day->format("Y-m-d")))])) {
						$pickup_checking_max_order_args = array(
					        'limit' => -1,
					        'pickup_date' => date("Y-m-d", strtotime($pickup_day->format("Y-m-d"))),
					        'pickup_type' => "pickup",
					        'status' => $order_status,
					        'return' => 'ids'
					    );
					    $orders_array = wc_get_orders( $pickup_checking_max_order_args );
					    if(count($orders_array) >= (int)$pickup_days_delivery[date("Y-m-d", strtotime($pickup_day->format("Y-m-d")))]) {
							$disable_for_max_pickup_dates[] = date('Y-m-d', strtotime($pickup_day->format("Y-m-d")));
					    }
					}
				}
			}

			$disable_for_max_pickup_dates = array_unique($disable_for_max_pickup_dates, false);
			$disable_for_max_pickup_dates = array_values($disable_for_max_pickup_dates);
		
		} 


		if ($enable_delivery_option && (isset($delivery_option_settings['maximum_delivery_pickup_per_day']) && $delivery_option_settings['maximum_delivery_pickup_per_day'] != "") ) {


			$range_last_date = $formated_obj->modify("+40 day")->format("Y-m-d");
			$filtered_date = $range_first_date . ',' . $range_last_date;
			$filtered_dates = explode(',', $filtered_date);
			$period = new DatePeriod(new DateTime($filtered_dates[0]), new DateInterval('P1D'), new DateTime($filtered_dates[1].' +1 day'));


			$maximum_delivery_pickup_per_day = (isset($delivery_option_settings['maximum_delivery_pickup_per_day']) && $delivery_option_settings['maximum_delivery_pickup_per_day'] != "") ? (int)$delivery_option_settings['maximum_delivery_pickup_per_day'] : 10000000000000;
			foreach ($period as $date) { 
				$args = array(
			        'limit' => -1,
			        'delivery_date' => date("Y-m-d", strtotime($date->format("Y-m-d"))),
			        'delivery_type' => "delivery",
			        'status' => $order_status,
			        'return' => 'ids'
			    );
			    $delivery_array = wc_get_orders( $args );

			    $pickup_args = array(
			        'limit' => -1,
			        'pickup_date' => date("Y-m-d", strtotime($date->format("Y-m-d"))),
			        'delivery_type' => "pickup",
			        'status' => $order_status,
			        'return' => 'ids'
			    );
			    $pickup_array = wc_get_orders( $pickup_args );

			    $total_orders = count($delivery_array) + count($pickup_array);

			    if($total_orders >= $maximum_delivery_pickup_per_day) {
					$disable_for_max_delivery_dates[] = date('Y-m-d', strtotime($date->format("Y-m-d")));
					$disable_for_max_pickup_dates[] = date('Y-m-d', strtotime($date->format("Y-m-d")));
			    }
			}

			$disable_for_max_delivery_dates = array_unique($disable_for_max_delivery_dates, false);
			$disable_for_max_delivery_dates = array_values($disable_for_max_delivery_dates);
			$disable_for_max_pickup_dates = array_unique($disable_for_max_pickup_dates, false);
			$disable_for_max_pickup_dates = array_values($disable_for_max_pickup_dates);

		}

		$disable_delivery_date_passed_time = [];
		$disable_pickup_date_passed_time = [];

		$delivery_time_settings = get_option('coderockz_woo_delivery_time_settings');
		$pickup_time_settings = get_option('coderockz_woo_delivery_pickup_settings');

		$enable_delivery_time = (isset($delivery_time_settings['enable_delivery_time']) && !empty($delivery_time_settings['enable_delivery_time'])) ? $delivery_time_settings['enable_delivery_time'] : false;
	  	
		$enable_pickup_time = (isset($pickup_time_settings['enable_pickup_time']) && !empty($pickup_time_settings['enable_pickup_time'])) ? $pickup_time_settings['enable_pickup_time'] : false;
		
		
		if($enable_delivery_time) {
			$time_slot_end = [0];
			$custom_time_slot_settings = get_option('coderockz_woo_delivery_time_slot_settings');
			$enable_custom_time_slot = (isset($custom_time_slot_settings['enable_custom_time_slot']) && !empty($custom_time_slot_settings['enable_custom_time_slot'])) ? $custom_time_slot_settings['enable_custom_time_slot'] : false;
			if($enable_custom_time_slot) {
				if(isset($custom_time_slot_settings['time_slot']) && count($custom_time_slot_settings['time_slot'])>0){				
					foreach($custom_time_slot_settings['time_slot'] as $key => $individual_time_slot) {

			  			if($individual_time_slot['enable']) {
			  				
			  				if(!in_array(date("w"),$individual_time_slot['disable_for'])) {
					  			$key = preg_replace('/-/', ',', $key);

					  			$key_array = explode(",",$key);

							    if($individual_time_slot['enable_split']) {
							    	$x = $key_array[0];
									while($key_array[1]>$x) {
										$second_time = $x+$individual_time_slot['split_slot_duration'];
										if($second_time > $key_array[1]) {
											$second_time = $key_array[1];
										}
										if($individual_time_slot['enable_single_splited_slot']) {
											$time_slot_end[] = (int)$x;
										} else {


											$time_slot_end[] = (int)$second_time;
										}
																		
										$x = $second_time;
									}
							    } else {
							    	if($individual_time_slot['enable_single_slot']) {
										$time_slot_end[] = (int)$individual_time_slot['start'];
									} else {
										$time_slot_end[] = (int)$individual_time_slot['end'];
									}
							    }

							}

						}
					}
				}
			} else {

				$time_settings = get_option('coderockz_woo_delivery_time_settings');
				$time_slot_end[] = (int)$time_settings['delivery_time_ends'];

				/*foreach($delivery_days as $delivery_day) {
  					$delivery_days_delivery[$delivery_day][$key] = (isset($individual_time_slot['max_order']) && $individual_time_slot['max_order'] !="") ? sanitize_text_field($individual_time_slot['max_order']) : "";
  				}*/												
			}


			$delivery_fee_settings = get_option('coderockz_woo_delivery_fee_settings');

			$enable_conditional_delivery_fee = isset($delivery_fee_settings['enable_conditional_delivery_fee']) && !empty($delivery_fee_settings['enable_conditional_delivery_fee']) ? $delivery_fee_settings['enable_conditional_delivery_fee'] : false;

			if($enable_conditional_delivery_fee && isset($delivery_fee_settings['conditional_delivery_time_ends']) && $delivery_fee_settings['conditional_delivery_time_ends'] !='') {
				$time_slot_end[] = (int)$delivery_fee_settings['conditional_delivery_time_ends'];
			}

			$highest_timeslot_end = max($time_slot_end);

			$current_time = (date("G")*60)+date("i");

			if($current_time>$highest_timeslot_end) {
				$disable_delivery_date_passed_time[] = date('Y-m-d', time());
			}

		}

		if($enable_pickup_time) {

			$pickup_slot_end = [0];
			$custom_pickup_slot_settings = get_option('coderockz_woo_delivery_pickup_slot_settings');
			$enable_custom_pickup_slot = (isset($custom_pickup_slot_settings['enable_custom_pickup_slot']) && !empty($custom_pickup_slot_settings['enable_custom_pickup_slot'])) ? $custom_pickup_slot_settings['enable_custom_pickup_slot'] : false;
			if($enable_custom_pickup_slot) {
				if(isset($custom_pickup_slot_settings['time_slot']) && count($custom_pickup_slot_settings['time_slot'])>0){

					foreach($custom_pickup_slot_settings['time_slot'] as $pickup_key => $individual_pickup_slot) {

			  			if($individual_pickup_slot['enable'] && !in_array(date("w"),$individual_pickup_slot['disable_for'])) {
				  			$pickup_key = preg_replace('/-/', ',', $pickup_key);

				  			$pickup_key_array = explode(",",$pickup_key);
						
						    if($individual_pickup_slot['enable_split']) {
						    	$pickup_x = $pickup_key_array[0];
								while($pickup_key_array[1]>$pickup_x) {
									$pickup_second_time = $pickup_x+$individual_pickup_slot['split_slot_duration'];
									if($pickup_second_time > $pickup_key_array[1]) {
										$pickup_second_time = $pickup_key_array[1];
									}
									if($individual_pickup_slot['enable_single_splited_slot']) {
										$pickup_slot_end[] = (int)$pickup_x;
									} else {
										$pickup_slot_end[] = (int)$pickup_second_time;
									}

									$pickup_x = $pickup_second_time;
								}
						    } else {
						    	if($individual_pickup_slot['enable_single_slot']) {
									$pickup_slot_end[] = (int)$individual_pickup_slot['start'];
								} else {
									$pickup_slot_end[] = (int)$individual_pickup_slot['end'];
								}
						    }

						}
					}
				}
			} else {

		    	$pickup_settings = get_option('coderockz_woo_delivery_pickup_settings');
				$pickup_slot_end[] = (int)$pickup_settings['pickup_time_ends'];
			}

			$highest_pickupslot_end = max($pickup_slot_end);

			$current_time = (date("G")*60)+date("i");
			if($current_time>$highest_pickupslot_end) {
				$disable_pickup_date_passed_time[] = date('Y-m-d', time());
			}
		}

		$response=[
			"disable_for_max_delivery_dates" => $disable_for_max_delivery_dates,
			"disable_for_max_pickup_dates" => $disable_for_max_pickup_dates,
			"disable_delivery_date_passed_time" => $disable_delivery_date_passed_time,
			"disable_pickup_date_passed_time" => $disable_pickup_date_passed_time,
		];
		$response = json_encode($response);
		wp_send_json_success($response);
	}

	//Without this function of filter "woocommerce_order_data_store_cpt_get_orders_query" query with post_meta "delivery_date" is not possible
	public function coderockz_woo_delivery_handle_custom_query_var( $query, $query_vars ) {
		if ( ! empty( $query_vars['delivery_date'] ) ) {
			$query['meta_query'][] = array(
				'key' => 'delivery_date',
				'value' => esc_attr( $query_vars['delivery_date'] ),
			);
		}

		if ( ! empty( $query_vars['pickup_date'] ) ) {
			$query['meta_query'][] = array(
				'key' => 'pickup_date',
				'value' => esc_attr( $query_vars['pickup_date'] ),
			);
		}

		if ( ! empty( $query_vars['delivery_type'] ) ) {
			$query['meta_query'][] = array(
				'key' => 'delivery_type',
				'value' => esc_attr( $query_vars['delivery_type'] ),
			);
		}

		if ( ! empty( $query_vars['delivery_time'] ) ) {
			$query['meta_query'][] = array(
				'key' => 'delivery_time',
				'value' => esc_attr( $query_vars['delivery_time'] ),
			);
		}

		if ( ! empty( $query_vars['pickup_time'] ) ) {
			$query['meta_query'][] = array(
				'key' => 'pickup_time',
				'value' => esc_attr( $query_vars['pickup_time'] ),
			);
		}

		if ( ! empty( $query_vars['delivery_pickup'] ) ) {
			$query['meta_query'][] = array(
				'key' => 'delivery_pickup',
				'value' => esc_attr( $query_vars['delivery_pickup'] ),
			);
		}

		return $query;
	}

	public function coderockz_woo_delivery_get_orders() {

		check_ajax_referer('coderockz_woo_delivery_nonce');
		
		$delivery_time_settings = get_option('coderockz_woo_delivery_time_settings');

		// if any timezone data is saved, set default timezone with the data
		$timezone = $this->helper->get_the_timezone();
		date_default_timezone_set($timezone);
		$order_status_keys = array_keys(wc_get_order_statuses());
		$order_status = ['partially-paid'];
		foreach($order_status_keys as $order_status_key) {
			$order_status[] = substr($order_status_key,3);
		}
		$order_status = array_diff($order_status,['cancelled','failed','refunded']);
		
		$disabled_current_time_slot = (isset($delivery_time_settings['disabled_current_time_slot']) && !empty($delivery_time_settings['disabled_current_time_slot'])) ? $delivery_time_settings['disabled_current_time_slot'] : false;

		$custom_time_slot_settings = get_option('coderockz_woo_delivery_time_slot_settings');
		$enable_custom_time_slot = (isset($custom_time_slot_settings['enable_custom_time_slot']) && !empty($custom_time_slot_settings['enable_custom_time_slot'])) ? $custom_time_slot_settings['enable_custom_time_slot'] : false;

		$formated_selected_date = date("Y-m-d", strtotime($this->helper->weekday_conversion($this->helper->date_conversion(sanitize_text_field($_POST['date']),"delivery"),"delivery")));

		if(isset($_POST['onlyDeliveryTime']) && $_POST['onlyDeliveryTime']) {
			$order_date = date("Y-m-d", strtotime(sanitize_text_field($formated_selected_date))); 
			$args = array(
		        'limit' => -1,
		        'date_created' => $order_date,
		        'delivery_type' => 'delivery',
		        'status' => $order_status,
		        'return' => 'ids'
		    );

		} else {
			$delivery_date = date("Y-m-d", strtotime(sanitize_text_field($formated_selected_date)));
			$args = array(
		        'limit' => -1,
		        'delivery_date' => $delivery_date,
		        'status' => $order_status,
		        'return' => 'ids'
		    );		    
		}

		$order_ids = wc_get_orders( $args );

		$given_state = (isset($_POST['givenState']) && $_POST['givenState'] !="") ? sanitize_text_field($_POST['givenState']) : "";
		$given_zip = (isset($_POST['givenZip']) && $_POST['givenZip'] !="") ? sanitize_text_field($_POST['givenZip']) : "";
		$response_delivery = [];
		$delivery_times = [];
		$max_order_per_slot = [];
		$slot_disable_for = [];
		$slot_disable_for_sameday = [];
		$slot_disable_for_excceed = [];
		$slot_open_specific_date = [];
		$slot_close_specific_date = [];
		$disable_timeslot = [];
		$state_zip_disable_timeslot_all = [];
		$no_state_zip_disable_timeslot_all = [];
		if($enable_custom_time_slot && isset($custom_time_slot_settings['time_slot']) && count($custom_time_slot_settings['time_slot'])>0){
	  		foreach($custom_time_slot_settings['time_slot'] as $key => $individual_time_slot) {

	  			if($individual_time_slot['enable']) {
	  				$times = explode('-', $key);
		  			if($individual_time_slot['enable_split']) {
						$times = explode('-', $key);
						$x = $times[0];
						while($times[1]>$x) {
							$second_time = $x+$individual_time_slot['split_slot_duration'];
							if($second_time > $times[1]) {
								$second_time = $times[1];
							}
							$disable = $individual_time_slot['disable_for'];
							if($individual_time_slot['enable_single_splited_slot']) {
								$slot_disable_for[date("H:i", mktime(0, (int)$x))] = $disable;
							} else {
								$slot_disable_for[date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time))] = $disable;
							}
							
							$x = $second_time;
						}
					} else {
						$times = explode('-', $key);
						$disable = $individual_time_slot['disable_for'];
						if($individual_time_slot['enable_single_slot']) {
							$slot_disable_for[date("H:i", mktime(0, (int)$times[0]))] = $disable;
						} else {
							$slot_disable_for[date("H:i", mktime(0, (int)$times[0])) . ' - ' . date("H:i", mktime(0, (int)$times[1]))] = $disable;
						}
						
					}

					if(isset($individual_time_slot['hide_time_slot_current_date']) && $individual_time_slot['hide_time_slot_current_date'] != "") {
						
		  				$times = explode('-', $key);
			  			if($individual_time_slot['enable_split']) {
							$times = explode('-', $key);
							$x = $times[0];
							while($times[1]>$x) {
								$second_time = $x+$individual_time_slot['split_slot_duration'];
								if($second_time > $times[1]) {
									$second_time = $times[1];
								}
								if($individual_time_slot['enable_single_splited_slot']) {
									$slot_disable_for_sameday[] = date("H:i", mktime(0, (int)$x));
								} else {
									$slot_disable_for_sameday[] = date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time));
								}
								
								$x = $second_time;
							}
						} else {
							$times = explode('-', $key);
							if($individual_time_slot['enable_single_slot']) {
								$slot_disable_for_sameday[] = date("H:i", mktime(0, (int)$times[0]));
							} else {
								$slot_disable_for_sameday[] = date("H:i", mktime(0, (int)$times[0])) . ' - ' . date("H:i", mktime(0, (int)$times[1]));
							}
							
						}

					}

					$current_time = (date("G")*60)+date("i");

					if(isset($individual_time_slot['timeslot_closing_time']) && $individual_time_slot['timeslot_closing_time'] != "") {

						if($current_time >= $individual_time_slot['timeslot_closing_time']) {
						
			  				$times = explode('-', $key);
				  			if($individual_time_slot['enable_split']) {
								$times = explode('-', $key);
								$x = $times[0];
								while($times[1]>$x) {
									$second_time = $x+$individual_time_slot['split_slot_duration'];
									if($second_time > $times[1]) {
										$second_time = $times[1];
									}
									if($individual_time_slot['enable_single_splited_slot']) {
										$slot_disable_for_excceed[] = date("H:i", mktime(0, (int)$x));
									} else {
										$slot_disable_for_excceed[] = date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time));
									}
									
									$x = $second_time;
								}
							} else {
								$times = explode('-', $key);
								if($individual_time_slot['enable_single_slot']) {
									$slot_disable_for_excceed[] = date("H:i", mktime(0, (int)$times[0]));
								} else {
									$slot_disable_for_excceed[] = date("H:i", mktime(0, (int)$times[0])) . ' - ' . date("H:i", mktime(0, (int)$times[1]));
								}
								
							}

						}

					}


					if(isset($individual_time_slot['only_specific_date']) && $individual_time_slot['only_specific_date'] != "") {
						
		  				$times = explode('-', $key);
			  			if($individual_time_slot['enable_split']) {
							$times = explode('-', $key);
							$x = $times[0];
							while($times[1]>$x) {
								$second_time = $x+$individual_time_slot['split_slot_duration'];
								if($second_time > $times[1]) {
									$second_time = $times[1];
								}
								if($individual_time_slot['enable_single_splited_slot']) {
									$slot_open_specific_date[date("H:i", mktime(0, (int)$x))][] = explode(",",$individual_time_slot['only_specific_date']);
								} else {
									$slot_open_specific_date[date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time))][] = explode(",",$individual_time_slot['only_specific_date']);
								}
								
								$x = $second_time;
							}
						} else {
							$times = explode('-', $key);
							if($individual_time_slot['enable_single_slot']) {
								$slot_open_specific_date[date("H:i", mktime(0, (int)$times[0]))][] = explode(",",$individual_time_slot['only_specific_date']);
							} else {
								$slot_open_specific_date[date("H:i", mktime(0, (int)$times[0])) . ' - ' . date("H:i", mktime(0, (int)$times[1]))][] = explode(",",$individual_time_slot['only_specific_date']);
							}
							
						}

					}

					if(isset($individual_time_slot['only_specific_date_close']) && $individual_time_slot['only_specific_date_close'] != "") {
						
		  				$times = explode('-', $key);
			  			if($individual_time_slot['enable_split']) {
							$times = explode('-', $key);
							$x = $times[0];
							while($times[1]>$x) {
								$second_time = $x+$individual_time_slot['split_slot_duration'];
								if($second_time > $times[1]) {
									$second_time = $times[1];
								}
								if($individual_time_slot['enable_single_splited_slot']) {
									$slot_close_specific_date[date("H:i", mktime(0, (int)$x))][] = explode(",",$individual_time_slot['only_specific_date_close']);
								} else {
									$slot_close_specific_date[date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time))][] = explode(",",$individual_time_slot['only_specific_date_close']);
								}
								
								$x = $second_time;
							}
						} else {
							$times = explode('-', $key);
							if($individual_time_slot['enable_single_slot']) {
								$slot_close_specific_date[date("H:i", mktime(0, (int)$times[0]))][] = explode(",",$individual_time_slot['only_specific_date_close']);
							} else {
								$slot_close_specific_date[date("H:i", mktime(0, (int)$times[0])) . ' - ' . date("H:i", mktime(0, (int)$times[1]))][] = explode(",",$individual_time_slot['only_specific_date_close']);
							}
							
						}

					}

					if(isset($individual_time_slot['more_settings']) && !empty($individual_time_slot['more_settings']) && $individual_time_slot['more_settings'] == 'zone') {

						if(isset($individual_time_slot['disable_postcode']) && !empty($individual_time_slot['disable_postcode'])) {
							
			  				foreach($individual_time_slot['disable_postcode'] as $postcode_value) {
			  					$multistep_postal_code = false;
			  					$between_postal_code = false;
								/*$postcode_range = [];*/
							    if (stripos($postcode_value,'...') !== false) {
							    	$range = explode('...', $postcode_value);
							    	if(stripos($given_zip,'-') !== false && stripos($range[0],'-') !== false && stripos($range[1],'-') !== false) {
						
										$sub_range_one = (int)str_replace("-", "", $range[0]);
										$sub_range_two = (int)str_replace("-", "", $range[1]);

										$given_zip_range = (int)str_replace("-", "", $given_zip);
										
										if($this->helper->number_between($given_zip_range, $sub_range_two, $sub_range_one)) {
											$multistep_postal_code = true;
										}
										
									} elseif(stripos($given_zip,'-') === false && stripos($range[0],'-') === false && stripos($range[1],'-') === false) {
										$alphabet_code = preg_replace("/[^a-zA-Z]+/", "", $range[0]);
										$range[0] = preg_replace("/[^0-9]+/", "", $range[0]);
										$range[1] = preg_replace("/[^0-9]+/", "", $range[1]);
										if($alphabet_code != "" && $this->helper->starts_with(strtolower($given_zip), strtolower($alphabet_code)) && $this->helper->number_between(preg_replace("/[^0-9]/", "", $given_zip ), $range[1], $range[0])) {
											$between_postal_code = true;
										} elseif($alphabet_code == "" && is_numeric($given_zip) && $this->helper->number_between($given_zip, $range[1], $range[0])) {
											$between_postal_code = true;
										}
									}
							    }
							    if (substr($postcode_value, -1) == '*') {
							    	if($this->helper->starts_with($given_zip,substr($postcode_value, 0, -1)) || $this->helper->starts_with(strtolower($given_zip),substr(strtolower($postcode_value), 0, -1)) || $this->helper->starts_with(strtoupper($given_zip),substr(strtoupper($postcode_value), 0, -1))) {
							    		$times = explode('-', $key);
										if($individual_time_slot['enable_split']) {
											$x = $times[0];
											while($times[1]>$x) {
												$second_time = $x+$individual_time_slot['split_slot_duration'];
												if($second_time > $times[1]) {
													$second_time = $times[1];
												}
												$disable = $individual_time_slot['disable_for'];
												if($individual_time_slot['enable_single_splited_slot']) {
													$disable_timeslot['postcode'][] = date("H:i", mktime(0, (int)$x));								
												} else {
													$disable_timeslot['postcode'][] = date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time));							
												}
												
												$x = $second_time;
											}
										} else {
											if($individual_time_slot['enable_single_slot']) {
												$disable_timeslot['postcode'][] = date("H:i", mktime(0, (int)$times[0]));								
											} else {
												$disable_timeslot['postcode'][] = date("H:i", mktime(0, (int)$times[0])) . ' - ' . date("H:i", mktime(0, (int)$times[1]));						
											}						
										}
							    	}
							    } elseif($multistep_postal_code || $between_postal_code || ($postcode_value == $given_zip || str_replace(" ","",$postcode_value) == $given_zip || strtolower($postcode_value) == strtolower($given_zip) || str_replace(" ","",strtolower($postcode_value)) == strtolower($given_zip) )) {
							    	$times = explode('-', $key);
									if($individual_time_slot['enable_split']) {
										$x = $times[0];
										while($times[1]>$x) {
											$second_time = $x+$individual_time_slot['split_slot_duration'];
											if($second_time > $times[1]) {
												$second_time = $times[1];
											}
											$disable = $individual_time_slot['disable_for'];
											if($individual_time_slot['enable_single_splited_slot']) {
												$disable_timeslot['postcode'][] = date("H:i", mktime(0, (int)$x));								
											} else {
												$disable_timeslot['postcode'][] = date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time));							
											}
											
											$x = $second_time;
										}
									} else {
										if($individual_time_slot['enable_single_slot']) {
											$disable_timeslot['postcode'][] = date("H:i", mktime(0, (int)$times[0]));								
										} else {
											$disable_timeslot['postcode'][] = date("H:i", mktime(0, (int)$times[0])) . ' - ' . date("H:i", mktime(0, (int)$times[1]));						
										}						
									}
							    }
							}		  				 
			  			} else {
			  				if((isset($individual_time_slot['disable_state']) && !empty($individual_time_slot['disable_state']) && in_array($given_state,$individual_time_slot['disable_state']))){
				  				$times = explode('-', $key);

								if($individual_time_slot['enable_split']) {
									
									$x = $times[0];
									while($times[1]>$x) {
										$second_time = $x+$individual_time_slot['split_slot_duration'];
										if($second_time > $times[1]) {
											$second_time = $times[1];
										}
										$disable = $individual_time_slot['disable_for'];
										if($individual_time_slot['enable_single_splited_slot']) {
											$disable_timeslot['state'][] = date("H:i", mktime(0, (int)$x));								
										} else {
											$disable_timeslot['state'][] = date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time));							
										}
										
										$x = $second_time;
									}
								} else {

									if($individual_time_slot['enable_single_slot']) {
										$disable_timeslot['state'][] = date("H:i", mktime(0, (int)$times[0]));								
									} else {
										$disable_timeslot['state'][] = date("H:i", mktime(0, (int)$times[0])) . ' - ' . date("H:i", mktime(0, (int)$times[1]));							
									}
									
								}		  				 
				  			}
			  			}


					} else {

			  			if((isset($individual_time_slot['disable_state']) && !empty($individual_time_slot['disable_state']) && in_array($given_state,$individual_time_slot['disable_state']))){
			  				$times = explode('-', $key);

							if($individual_time_slot['enable_split']) {
								
								$x = $times[0];
								while($times[1]>$x) {
									$second_time = $x+$individual_time_slot['split_slot_duration'];
									if($second_time > $times[1]) {
										$second_time = $times[1];
									}
									$disable = $individual_time_slot['disable_for'];
									if($individual_time_slot['enable_single_splited_slot']) {
										$disable_timeslot['state'][] = date("H:i", mktime(0, (int)$x));								
									} else {
										$disable_timeslot['state'][] = date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time));							
									}
									
									$x = $second_time;
								}
							} else {

								if($individual_time_slot['enable_single_slot']) {
									$disable_timeslot['state'][] = date("H:i", mktime(0, (int)$times[0]));								
								} else {
									$disable_timeslot['state'][] = date("H:i", mktime(0, (int)$times[0])) . ' - ' . date("H:i", mktime(0, (int)$times[1]));							
								}
								
							}		  				 
			  			} 

			  			if(isset($individual_time_slot['disable_postcode']) && !empty($individual_time_slot['disable_postcode'])) {
			  				
			  				foreach($individual_time_slot['disable_postcode'] as $postcode_value) {
								$multistep_postal_code = false;
								$between_postal_code = false;
								/*$postcode_range = [];*/
							    if (stripos($postcode_value,'...') !== false) {
							    	$range = explode('...', $postcode_value);
							    	if(stripos($given_zip,'-') !== false && stripos($range[0],'-') !== false && stripos($range[1],'-') !== false) {
						
										$sub_range_one = (int)str_replace("-", "", $range[0]);
										$sub_range_two = (int)str_replace("-", "", $range[1]);

										$given_zip_range = (int)str_replace("-", "", $given_zip);
										
										if($this->helper->number_between($given_zip_range, $sub_range_two, $sub_range_one)) {
											$multistep_postal_code = true;
										}
										
									} elseif(stripos($given_zip,'-') === false && stripos($range[0],'-') === false && stripos($range[1],'-') === false) {
										$alphabet_code = preg_replace("/[^a-zA-Z]+/", "", $range[0]);
										$range[0] = preg_replace("/[^0-9]+/", "", $range[0]);
										$range[1] = preg_replace("/[^0-9]+/", "", $range[1]);
										if($alphabet_code != "" && $this->helper->starts_with(strtolower($given_zip), strtolower($alphabet_code)) && $this->helper->number_between(preg_replace("/[^0-9]/", "", $given_zip ), $range[1], $range[0])) {
											$between_postal_code = true;
										} elseif($alphabet_code == "" && is_numeric($given_zip) && $this->helper->number_between($given_zip, $range[1], $range[0])) {
											$between_postal_code = true;
										}
									}
							    }
							    if (substr($postcode_value, -1) == '*') {
							    	if($this->helper->starts_with($given_zip,substr($postcode_value, 0, -1)) || $this->helper->starts_with(strtolower($given_zip),substr(strtolower($postcode_value), 0, -1)) || $this->helper->starts_with(strtoupper($given_zip),substr(strtoupper($postcode_value), 0, -1))) {
							    		$times = explode('-', $key);
										if($individual_time_slot['enable_split']) {
											$x = $times[0];
											while($times[1]>$x) {
												$second_time = $x+$individual_time_slot['split_slot_duration'];
												if($second_time > $times[1]) {
													$second_time = $times[1];
												}
												$disable = $individual_time_slot['disable_for'];
												if($individual_time_slot['enable_single_splited_slot']) {
													$disable_timeslot['postcode'][] = date("H:i", mktime(0, (int)$x));								
												} else {
													$disable_timeslot['postcode'][] = date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time));							
												}
												
												$x = $second_time;
											}
										} else {
											if($individual_time_slot['enable_single_slot']) {
												$disable_timeslot['postcode'][] = date("H:i", mktime(0, (int)$times[0]));								
											} else {
												$disable_timeslot['postcode'][] = date("H:i", mktime(0, (int)$times[0])) . ' - ' . date("H:i", mktime(0, (int)$times[1]));						
											}						
										}
							    	}
							    } elseif($multistep_postal_code || $between_postal_code || ($postcode_value == $given_zip || str_replace(" ","",$postcode_value) == $given_zip || strtolower($postcode_value) == strtolower($given_zip) || str_replace(" ","",strtolower($postcode_value)) == strtolower($given_zip) )) {
							    	$times = explode('-', $key);
									if($individual_time_slot['enable_split']) {
										$x = $times[0];
										while($times[1]>$x) {
											$second_time = $x+$individual_time_slot['split_slot_duration'];
											if($second_time > $times[1]) {
												$second_time = $times[1];
											}
											$disable = $individual_time_slot['disable_for'];
											if($individual_time_slot['enable_single_splited_slot']) {
												$disable_timeslot['postcode'][] = date("H:i", mktime(0, (int)$x));								
											} else {
												$disable_timeslot['postcode'][] = date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time));							
											}
											
											$x = $second_time;
										}
									} else {
										if($individual_time_slot['enable_single_slot']) {
											$disable_timeslot['postcode'][] = date("H:i", mktime(0, (int)$times[0]));								
										} else {
											$disable_timeslot['postcode'][] = date("H:i", mktime(0, (int)$times[0])) . ' - ' . date("H:i", mktime(0, (int)$times[1]));						
										}						
									}
							    }
							}		  				 
			  			}

		  			}


		  			if((isset($individual_time_slot['disable_state']) && !empty($individual_time_slot['disable_state']))){
		  				$times = explode('-', $key);
						if($individual_time_slot['enable_split']) {
							$x = $times[0];
							while($times[1]>$x) {
								$second_time = $x+$individual_time_slot['split_slot_duration'];
								if($second_time > $times[1]) {
									$second_time = $times[1];
								}
								$disable = $individual_time_slot['disable_for'];
								if($individual_time_slot['enable_single_splited_slot']) {
									$state_zip_disable_timeslot_all['state'][] = date("H:i", mktime(0, (int)$x));								
								} else {
									$state_zip_disable_timeslot_all['state'][] = date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time));							
								}
								
								$x = $second_time;
							}
						} else {
							if($individual_time_slot['enable_single_slot']) {
								$state_zip_disable_timeslot_all['state'][] = date("H:i", mktime(0, (int)$times[0]));								
							} else {
								$state_zip_disable_timeslot_all['state'][] = date("H:i", mktime(0, (int)$times[0])) . ' - ' . date("H:i", mktime(0, (int)$times[1]));							
							}
							
						}		  				 
		  			}

		  			if((isset($individual_time_slot['disable_postcode']) && !empty($individual_time_slot['disable_postcode']))){
		  				$times = explode('-', $key);
						if($individual_time_slot['enable_split']) {
							$x = $times[0];
							while($times[1]>$x) {
								$second_time = $x+$individual_time_slot['split_slot_duration'];
								if($second_time > $times[1]) {
									$second_time = $times[1];
								}
								$disable = $individual_time_slot['disable_for'];
								if($individual_time_slot['enable_single_splited_slot']) {
									$state_zip_disable_timeslot_all['postcode'][] = date("H:i", mktime(0, (int)$x));								
								} else {
									$state_zip_disable_timeslot_all['postcode'][] = date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time));							
								}
								
								$x = $second_time;
							}
						} else {
							if($individual_time_slot['enable_single_slot']) {
								$state_zip_disable_timeslot_all['postcode'][] = date("H:i", mktime(0, (int)$times[0]));								
							} else {
								$state_zip_disable_timeslot_all['postcode'][] = date("H:i", mktime(0, (int)$times[0])) . ' - ' . date("H:i", mktime(0, (int)$times[1]));							
							}						
						}		  				 
		  			} else {
		  				$times = explode('-', $key);
						if($individual_time_slot['enable_split']) {
							$x = $times[0];
							while($times[1]>$x) {
								$second_time = $x+$individual_time_slot['split_slot_duration'];
								if($second_time > $times[1]) {
									$second_time = $times[1];
								}
								$disable = $individual_time_slot['disable_for'];
								if($individual_time_slot['enable_single_splited_slot']) {
									$no_state_zip_disable_timeslot_all['nostatezip'][] = date("H:i", mktime(0, (int)$x));								
								} else {
									$no_state_zip_disable_timeslot_all['nostatezip'][] = date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time));							
								}
								
								$x = $second_time;
							}

						} else {
							if($individual_time_slot['enable_single_slot']) {
								$no_state_zip_disable_timeslot_all['nostatezip'][] = date("H:i", mktime(0, (int)$times[0]));								
							} else {
								$no_state_zip_disable_timeslot_all['nostatezip'][] = date("H:i", mktime(0, (int)$times[0])) . ' - ' . date("H:i", mktime(0, (int)$times[1]));							
							}

						}		  				 
		  			}
	  			}
	  		}
	  	} else {
	  		$time_settings = get_option('coderockz_woo_delivery_time_settings');
	  		$enable_delivery_time = (isset($time_settings['enable_delivery_time']) && !empty($time_settings['enable_delivery_time'])) ? $time_settings['enable_delivery_time'] : false;
			if($enable_delivery_time) {
		  		$x = (int)$time_settings['delivery_time_starts'];
		  		$each_time_slot = (isset($time_settings['each_time_slot']) && !empty($time_settings['each_time_slot'])) ? (int)$time_settings['each_time_slot'] : (int)$time_settings['delivery_time_ends']-(int)$time_settings['delivery_time_starts'];
				while((int)$time_settings['delivery_time_ends']>$x) {
					$second_time = $x+$each_time_slot;
					if($second_time > (int)$time_settings['delivery_time_ends']) {
						$second_time = (int)$time_settings['delivery_time_ends'];
					}
					$no_state_zip_disable_timeslot_all['nostatezip'][] = date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time));		
					$x = $second_time;
				}
			}  		
	  	}

	  	$free_up_slot_for_delivery_completed = (isset($delivery_time_settings['free_up_slot_for_delivery_completed']) && !empty($delivery_time_settings['free_up_slot_for_delivery_completed'])) ? $delivery_time_settings['free_up_slot_for_delivery_completed'] : false;

	  	
	  	foreach ($order_ids as $order) {
			$date = get_post_meta($order,"delivery_date",true);
			$time = get_post_meta($order,"delivery_time",true);
			
			if((isset($date) && isset($time)) || isset($time)) {
				if($time != "as-soon-as-possible") {
					
					if(!$free_up_slot_for_delivery_completed) {
						$delivery_times[] = $time;
					} else {
						if(!metadata_exists('post', $order, 'delivery_status')) {
							$delivery_times[] = $time;
						}
					}
					
				}
				
			}

		}

		$unique_delivery_times = array_unique($delivery_times, false);
		$unique_delivery_times = array_values($unique_delivery_times);

		if($enable_custom_time_slot) {
			if(isset($custom_time_slot_settings['time_slot']) && count($custom_time_slot_settings['time_slot'])>0){

				foreach($custom_time_slot_settings['time_slot'] as $key => $individual_time_slot) {

		  			if($individual_time_slot['enable']) {
			  			$key = preg_replace('/-/', ',', $key);

			  			$key_array = explode(",",$key);

					    $max_order = (isset($individual_time_slot['max_order']) && $individual_time_slot['max_order'] !="") ? sanitize_text_field($individual_time_slot['max_order']) : "";
					
					    if($individual_time_slot['enable_split']) {
							$x = $key_array[0];
							while($key_array[1]>$x) {
								$second_time = $x+$individual_time_slot['split_slot_duration'];
								if($second_time > $key_array[1]) {
									$second_time = $key_array[1];
								}
								if($individual_time_slot['enable_single_splited_slot']) {
									if(in_array(date("H:i", mktime(0, (int)$x)),$unique_delivery_times)) {
										$max_order_per_slot[date("H:i", mktime(0, (int)$x))] = (int)$max_order;
									}
									
								} else {
									if(in_array(date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time)),$unique_delivery_times)) {
									$max_order_per_slot[date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time))] = (int)$max_order;
									}						
								}
								
								$x = $second_time;
							}

						} else {
							if($individual_time_slot['enable_single_slot']) {
					
								if(in_array(date("H:i", mktime(0, (int)$key_array[0])),$unique_delivery_times)) {
									$max_order_per_slot[date("H:i", mktime(0, (int)$key_array[0]))] = (int)$max_order;
								}
							} else {
								if(in_array(date("H:i", mktime(0, (int)$key_array[0])) . ' - ' . date("H:i", mktime(0, (int)$key_array[1])),$unique_delivery_times)) {
									$max_order_per_slot[date("H:i", mktime(0, (int)$key_array[0])) . ' - ' . date("H:i", mktime(0, (int)$key_array[1]))] = (int)$max_order;	
								}						
							}

						}

					}
				}
			}
		} else {

		    $time_settings = get_option('coderockz_woo_delivery_time_settings');
		    $enable_delivery_time = (isset($time_settings['enable_delivery_time']) && !empty($time_settings['enable_delivery_time'])) ? $time_settings['enable_delivery_time'] : false;
		    if($enable_delivery_time) {
			    $max_order = (isset($time_settings['max_order_per_slot']) && $time_settings['max_order_per_slot'] !="") ? sanitize_text_field($time_settings['max_order_per_slot']) : "";
		  		$x = (int)$time_settings['delivery_time_starts'];
		  		$each_delivery_slot = (isset($time_settings['each_time_slot']) && !empty($time_settings['each_time_slot'])) ? (int)$time_settings['each_time_slot'] : (int)$time_settings['delivery_time_ends']-(int)$time_settings['delivery_time_starts'];
				while((int)$time_settings['delivery_time_ends']>$x) {
					$second_time = $x+$each_delivery_slot;
					if($second_time > (int)$time_settings['delivery_time_ends']) {
						$second_time = (int)$time_settings['delivery_time_ends'];
					}
					if(in_array(date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time)),$unique_delivery_times)) {
						$max_order_per_slot[date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time))] = (int)$max_order;
					}		
					$x = $second_time;
				}
			}
		}
	  	
		$response_delivery = [
			"delivery_times" => $delivery_times,
			"max_order_per_slot" => $max_order_per_slot,
			"slot_disable_for" => $slot_disable_for,
			'disabled_current_time_slot' => $disabled_current_time_slot,
			'disable_timeslot' => $disable_timeslot,
			'state_zip_disable_timeslot_all' => $state_zip_disable_timeslot_all,
			'no_state_zip_disable_timeslot_all' => $no_state_zip_disable_timeslot_all,
			'slot_disable_for_sameday' => $slot_disable_for_sameday,
			'slot_disable_for_excceed' => $slot_disable_for_excceed,
			'slot_open_specific_date' => $slot_open_specific_date,
			'slot_close_specific_date' => $slot_close_specific_date
		];

		$formated_date = date('Y-m-d H:i:s', strtotime(sanitize_text_field($formated_selected_date)));
		$formated_date = new DateTime($formated_date);
		$formated_date = $formated_date->format("w");

		$current_time = (date("G")*60)+date("i");

		$delivery_fee_settings = get_option('coderockz_woo_delivery_fee_settings');

		$enable_conditional_delivery_fee = isset($delivery_fee_settings['enable_conditional_delivery_fee']) && !empty($delivery_fee_settings['enable_conditional_delivery_fee']) ? $delivery_fee_settings['enable_conditional_delivery_fee'] : false;
		$have_conditional_delivery = false;
		$only_conditional_delivery_for_fee = false;
		$conditional_delivery_fee_duration= "";
		$disable_inter_timeslot_conditional = false;
		$have_conditional_delivery_with_shipping_method = false;
		if($enable_conditional_delivery_fee && (isset($delivery_fee_settings['conditional_delivery_time_starts']) && ($delivery_fee_settings['conditional_delivery_time_starts'] !='' || $delivery_fee_settings['conditional_delivery_time_starts'] == 0)) && (isset($delivery_fee_settings['conditional_delivery_time_ends']) && $delivery_fee_settings['conditional_delivery_time_ends'] !='') && ($current_time >= (int)$delivery_fee_settings['conditional_delivery_time_starts'] && (int)$delivery_fee_settings['conditional_delivery_time_ends'] >= $current_time) && (isset($delivery_fee_settings['conditional_delivery_fee_duration']) && !empty($delivery_fee_settings['conditional_delivery_fee_duration'])) && ((isset($delivery_fee_settings['conditional_delivery_fee']) && !empty($delivery_fee_settings['conditional_delivery_fee'])) || (isset($delivery_fee_settings['conditional_delivery_shipping_method']) && !empty($delivery_fee_settings['conditional_delivery_shipping_method'])))) {
			$have_conditional_delivery = true;
			$conditional_delivery_fee_duration = (int)$delivery_fee_settings['conditional_delivery_fee_duration'];
			$disable_inter_timeslot_conditional = (isset($delivery_fee_settings['disable_inter_timeslot_conditional']) && !empty($delivery_fee_settings['disable_inter_timeslot_conditional'])) ? $delivery_fee_settings['disable_inter_timeslot_conditional'] : false;

		}

		if($enable_conditional_delivery_fee && (isset($delivery_fee_settings['conditional_delivery_time_starts']) && ($delivery_fee_settings['conditional_delivery_time_starts'] !='' || $delivery_fee_settings['conditional_delivery_time_starts'] == 0)) && (isset($delivery_fee_settings['conditional_delivery_time_ends']) && $delivery_fee_settings['conditional_delivery_time_ends'] !='') && ($current_time >= (int)$delivery_fee_settings['conditional_delivery_time_starts'] && (int)$delivery_fee_settings['conditional_delivery_time_ends'] >= $current_time) && (isset($delivery_fee_settings['conditional_delivery_fee_duration']) && !empty($delivery_fee_settings['conditional_delivery_fee_duration'])) && (isset($delivery_fee_settings['conditional_delivery_fee']) && !empty($delivery_fee_settings['conditional_delivery_fee'])) && date('Y-m-d', strtotime(sanitize_text_field($formated_selected_date))) == date('Y-m-d', time()) ) {
			$only_conditional_delivery_for_fee = true;

		}

		if($enable_conditional_delivery_fee && (isset($delivery_fee_settings['conditional_delivery_time_starts']) && ($delivery_fee_settings['conditional_delivery_time_starts'] !='' || $delivery_fee_settings['conditional_delivery_time_starts'] == 0)) && (isset($delivery_fee_settings['conditional_delivery_time_ends']) && $delivery_fee_settings['conditional_delivery_time_ends'] !='') && ($current_time >= (int)$delivery_fee_settings['conditional_delivery_time_starts'] && (int)$delivery_fee_settings['conditional_delivery_time_ends'] >= $current_time) && (isset($delivery_fee_settings['conditional_delivery_fee_duration']) && !empty($delivery_fee_settings['conditional_delivery_fee_duration'])) && isset($delivery_fee_settings['conditional_delivery_shipping_method']) && !empty($delivery_fee_settings['conditional_delivery_shipping_method'])) {
			$have_conditional_delivery_with_shipping_method = true;
		}



		$response_for_all = [
			"formated_selected_date" => $formated_selected_date,
			"formated_date" => $formated_date,
			"current_time" => $current_time,
			"have_conditional_delivery" => $have_conditional_delivery,
			"conditional_delivery_fee_duration" => $conditional_delivery_fee_duration,
			"disable_inter_timeslot_conditional" => $disable_inter_timeslot_conditional,
			'only_conditional_delivery_for_fee' => $only_conditional_delivery_for_fee,
			'have_conditional_delivery_with_shipping_method' => $have_conditional_delivery_with_shipping_method
		];

		$response = array_merge($response_delivery, $response_for_all);

		$response = json_encode($response);
		wp_send_json_success($response);
	}

	public function coderockz_woo_delivery_get_orders_pickup() {

		check_ajax_referer('coderockz_woo_delivery_nonce');
		
		$delivery_pickup_settings = get_option('coderockz_woo_delivery_pickup_settings');
		// if any timezone data is saved, set default timezone with the data
		$timezone = $this->helper->get_the_timezone();
		date_default_timezone_set($timezone);

		$order_status_keys = array_keys(wc_get_order_statuses());
		$order_status = ['partially-paid'];
		foreach($order_status_keys as $order_status_key) {
			$order_status[] = substr($order_status_key,3);
		}
		$order_status = array_diff($order_status,['cancelled','failed','refunded']);

		$given_state = (isset($_POST['givenState']) && $_POST['givenState'] !="") ? sanitize_text_field($_POST['givenState']) : "";
		$given_zip = (isset($_POST['givenZip']) && $_POST['givenZip'] !="") ? sanitize_text_field($_POST['givenZip']) : "";
		$given_location = (isset($_POST['givenLocation']) && $_POST['givenLocation'] !="") ? sanitize_text_field($_POST['givenLocation']) : "";
		
		$pickup_disabled_current_time_slot = (isset($delivery_pickup_settings['disabled_current_pickup_time_slot']) && !empty($delivery_pickup_settings['disabled_current_pickup_time_slot'])) ? $delivery_pickup_settings['disabled_current_pickup_time_slot'] : false;
		
		$max_pickup_consider_location = (isset($delivery_pickup_settings['max_pickup_consider_location']) && !empty($delivery_pickup_settings['max_pickup_consider_location'])) ? $delivery_pickup_settings['max_pickup_consider_location'] : false;


		$custom_pickup_slot_settings = get_option('coderockz_woo_delivery_pickup_slot_settings');
		$enable_custom_pickup_slot = (isset($custom_pickup_slot_settings['enable_custom_pickup_slot']) && !empty($custom_pickup_slot_settings['enable_custom_pickup_slot'])) ? $custom_pickup_slot_settings['enable_custom_pickup_slot'] : false;

		$pickup_location_settings = get_option('coderockz_woo_delivery_pickup_location_settings');
		$enable_pickup_location = (isset($pickup_location_settings['enable_pickup_location']) && !empty($pickup_location_settings['enable_pickup_location'])) ? $pickup_location_settings['enable_pickup_location'] : false;

		$formated_selected_date = date("Y-m-d", strtotime($this->helper->weekday_conversion($this->helper->date_conversion(sanitize_text_field($_POST['date']),"pickup"),"pickup")));
		
		$max_pickup_slot_individual_location = false;
		if($enable_pickup_location && $max_pickup_consider_location) {

			$max_pickup_slot_individual_location = true;
			if(isset($_POST['onlyPickupTime']) && $_POST['onlyPickupTime']) {
				$order_date = date("Y-m-d", strtotime(sanitize_text_field($formated_selected_date))); 
				$args = array(
			        'limit' => -1,
			        'date_created' => $order_date,
			        'delivery_type' => 'pickup',
			        'delivery_pickup' => $given_location,
			        'status' => $order_status,
			        'return' => 'ids'
			    );

			} else {
				$pickup_date = date("Y-m-d", strtotime(sanitize_text_field($formated_selected_date)));
				$args = array(
			        'limit' => -1,
			        'pickup_date' => $pickup_date,
			        'delivery_pickup' => $given_location,
			        'status' => $order_status,
			        'return' => 'ids'
			    );		    
			}

		} else {
			if(isset($_POST['onlyPickupTime']) && $_POST['onlyPickupTime']) {
				$order_date = date("Y-m-d", strtotime(sanitize_text_field($formated_selected_date))); 
				$args = array(
			        'limit' => -1,
			        'date_created' => $order_date,
			        'delivery_type' => 'pickup',
			        'status' => $order_status,
			        'return' => 'ids'
			    );

			} else {
				$pickup_date = date("Y-m-d", strtotime(sanitize_text_field($formated_selected_date)));
				$args = array(
			        'limit' => -1,
			        'pickup_date' => $pickup_date,
			        'status' => $order_status,
			        'return' => 'ids'
			    );		    
			}
		}

		$order_ids = wc_get_orders( $args );

		$pickup_location_disable_for = [];
		$pickup_delivery_locations = [];
		$pickup_max_order_per_location = [];
		$response_pickup = [];
		$pickup_delivery_times = [];
		$pickup_slot_disable_for_sameday = [];
		$pickup_slot_disable_for_excceed = [];
		$pickup_slot_open_specific_date = [];
		$pickup_slot_close_specific_date = [];
		$pickup_max_order_per_slot = [];
		$pickup_slot_disable_for = [];
		$pickup_disable_timeslot = [];
		$pickup_state_zip_disable_timeslot_all = [];
		$pickup_no_state_zip_disable_timeslot_all = [];
		$pickup_disable_timeslot_location[] = [];
		$pickup_location_disable_timeslot_all = [];
		$pickup_no_location_disable_timeslot_all[] = [];
		$detect_pickup_location_hide = false;

		if($enable_custom_pickup_slot && isset($custom_pickup_slot_settings['time_slot']) && count($custom_pickup_slot_settings['time_slot'])>0){
	  		foreach($custom_pickup_slot_settings['time_slot'] as $key => $individual_pickup_slot) {

	  			if($individual_pickup_slot['enable']) {
		  			if($individual_pickup_slot['enable_split']) {
						$times = explode('-', $key);
						$x = $times[0];
						while($times[1]>$x) {
							$second_time = $x+$individual_pickup_slot['split_slot_duration'];
							if($second_time > $times[1]) {
								$second_time = $times[1];
							}
							$disable = $individual_pickup_slot['disable_for'];
							if($individual_pickup_slot['enable_single_splited_slot']) {
								$pickup_slot_disable_for[date("H:i", mktime(0, (int)$x))] = $disable;
							} else {
								$pickup_slot_disable_for[date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time))] = $disable;
							}
							
							$x = $second_time;
						}
					} else {
						$times = explode('-', $key);
						$disable = $individual_pickup_slot['disable_for'];
						if($individual_pickup_slot['enable_single_slot']) {
							$pickup_slot_disable_for[date("H:i", mktime(0, (int)$times[0]))] = $disable;
						} else {
							$pickup_slot_disable_for[date("H:i", mktime(0, (int)$times[0])) . ' - ' . date("H:i", mktime(0, (int)$times[1]))] = $disable;
						}
					}

					if(isset($individual_pickup_slot['hide_time_slot_current_date']) && $individual_pickup_slot['hide_time_slot_current_date']) {
						
		  				$times = explode('-', $key);
			  			if($individual_pickup_slot['enable_split']) {
							$times = explode('-', $key);
							$x = $times[0];
							while($times[1]>$x) {
								$second_time = $x+$individual_pickup_slot['split_slot_duration'];
								if($second_time > $times[1]) {
									$second_time = $times[1];
								}
								if($individual_pickup_slot['enable_single_splited_slot']) {
									$pickup_slot_disable_for_sameday[] = date("H:i", mktime(0, (int)$x));
								} else {
									$pickup_slot_disable_for_sameday[] = date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time));
								}
								
								$x = $second_time;
							}
						} else {
							$times = explode('-', $key);
							if($individual_pickup_slot['enable_single_slot']) {
								$pickup_slot_disable_for_sameday[] = date("H:i", mktime(0, (int)$times[0]));
							} else {
								$pickup_slot_disable_for_sameday[] = date("H:i", mktime(0, (int)$times[0])) . ' - ' . date("H:i", mktime(0, (int)$times[1]));
							}
							
						}

					}

					$current_time = (date("G")*60)+date("i");

					if(isset($individual_pickup_slot['timeslot_closing_time']) && $individual_pickup_slot['timeslot_closing_time'] != "") {

						if($current_time >= $individual_pickup_slot['timeslot_closing_time']) {
						
			  				$times = explode('-', $key);
				  			if($individual_pickup_slot['enable_split']) {
								$times = explode('-', $key);
								$x = $times[0];
								while($times[1]>$x) {
									$second_time = $x+$individual_pickup_slot['split_slot_duration'];
									if($second_time > $times[1]) {
										$second_time = $times[1];
									}
									if($individual_pickup_slot['enable_single_splited_slot']) {
										$pickup_slot_disable_for_excceed[] = date("H:i", mktime(0, (int)$x));
									} else {
										$pickup_slot_disable_for_excceed[] = date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time));
									}
									
									$x = $second_time;
								}
							} else {
								$times = explode('-', $key);
								if($individual_pickup_slot['enable_single_slot']) {
									$pickup_slot_disable_for_excceed[] = date("H:i", mktime(0, (int)$times[0]));
								} else {
									$pickup_slot_disable_for_excceed[] = date("H:i", mktime(0, (int)$times[0])) . ' - ' . date("H:i", mktime(0, (int)$times[1]));
								}
								
							}

						}

					}

					if(isset($individual_pickup_slot['only_specific_date']) && $individual_pickup_slot['only_specific_date'] != "") {
						
		  				$times = explode('-', $key);
			  			if($individual_pickup_slot['enable_split']) {
							$times = explode('-', $key);
							$x = $times[0];
							while($times[1]>$x) {
								$second_time = $x+$individual_pickup_slot['split_slot_duration'];
								if($second_time > $times[1]) {
									$second_time = $times[1];
								}
								if($individual_pickup_slot['enable_single_splited_slot']) {
									$pickup_slot_open_specific_date[date("H:i", mktime(0, (int)$x))][] = explode(",",$individual_pickup_slot['only_specific_date']);
								} else {
									$pickup_slot_open_specific_date[date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time))][] = explode(",",$individual_pickup_slot['only_specific_date']);
								}
								
								$x = $second_time;
							}
						} else {
							$times = explode('-', $key);
							if($individual_pickup_slot['enable_single_slot']) {
								$pickup_slot_open_specific_date[date("H:i", mktime(0, (int)$times[0]))][] = explode(",",$individual_pickup_slot['only_specific_date']);
							} else {
								$pickup_slot_open_specific_date[date("H:i", mktime(0, (int)$times[0])) . ' - ' . date("H:i", mktime(0, (int)$times[1]))][] = explode(",",$individual_pickup_slot['only_specific_date']);
							}
							
						}

					}

					if(isset($individual_pickup_slot['only_specific_date_close']) && $individual_pickup_slot['only_specific_date_close'] != "") {
						
		  				$times = explode('-', $key);
			  			if($individual_pickup_slot['enable_split']) {
							$times = explode('-', $key);
							$x = $times[0];
							while($times[1]>$x) {
								$second_time = $x+$individual_pickup_slot['split_slot_duration'];
								if($second_time > $times[1]) {
									$second_time = $times[1];
								}
								if($individual_pickup_slot['enable_single_splited_slot']) {
									$pickup_slot_close_specific_date[date("H:i", mktime(0, (int)$x))][] = explode(",",$individual_pickup_slot['only_specific_date_close']);
								} else {
									$pickup_slot_close_specific_date[date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time))][] = explode(",",$individual_pickup_slot['only_specific_date_close']);
								}
								
								$x = $second_time;
							}
						} else {
							$times = explode('-', $key);
							if($individual_pickup_slot['enable_single_slot']) {
								$pickup_slot_close_specific_date[date("H:i", mktime(0, (int)$times[0]))][] = explode(",",$individual_pickup_slot['only_specific_date_close']);
							} else {
								$pickup_slot_close_specific_date[date("H:i", mktime(0, (int)$times[0])) . ' - ' . date("H:i", mktime(0, (int)$times[1]))][] = explode(",",$individual_pickup_slot['only_specific_date_close']);
							}
							
						}

					}

					if(isset($individual_pickup_slot['more_settings']) && !empty($individual_pickup_slot['more_settings']) && $individual_pickup_slot['more_settings'] == 'zone') {

						if(isset($individual_pickup_slot['disable_postcode']) && !empty($individual_pickup_slot['disable_postcode'])){
							
			  				foreach($individual_pickup_slot['disable_postcode'] as $postcode_value) {
								$multistep_postal_code = false;
								$between_postal_code = false;
								/*$postcode_range = [];*/
							    if (stripos($postcode_value,'...') !== false) {
							    	$range = explode('...', $postcode_value);
							    	if(stripos($given_zip,'-') !== false && stripos($range[0],'-') !== false && stripos($range[1],'-') !== false) {
						
										$sub_range_one = (int)str_replace("-", "", $range[0]);
										$sub_range_two = (int)str_replace("-", "", $range[1]);

										$given_zip_range = (int)str_replace("-", "", $given_zip);
										
										if($this->helper->number_between($given_zip_range, $sub_range_two, $sub_range_one)) {
											$multistep_postal_code = true;
										}
										
									} elseif(stripos($given_zip,'-') === false && stripos($range[0],'-') === false && stripos($range[1],'-') === false) {
										$alphabet_code = preg_replace("/[^a-zA-Z]+/", "", $range[0]);
										$range[0] = preg_replace("/[^0-9]+/", "", $range[0]);
										$range[1] = preg_replace("/[^0-9]+/", "", $range[1]);
										if($alphabet_code != "" && $this->helper->starts_with(strtolower($given_zip), strtolower($alphabet_code)) && $this->helper->number_between(preg_replace("/[^0-9]/", "", $given_zip ), $range[1], $range[0])) {
											$between_postal_code = true;
										} elseif($alphabet_code == "" && is_numeric($given_zip) && $this->helper->number_between($given_zip, $range[1], $range[0])) {
											$between_postal_code = true;
										}
									}
							    }
							    if (substr($postcode_value, -1) == '*') {
							    	if($this->helper->starts_with($given_zip,substr($postcode_value, 0, -1)) || $this->helper->starts_with(strtolower($given_zip),substr(strtolower($postcode_value), 0, -1)) || $this->helper->starts_with(strtoupper($given_zip),substr(strtoupper($postcode_value), 0, -1))) {
							    		$times = explode('-', $key);
										if($individual_pickup_slot['enable_split']) {
											$x = $times[0];
											while($times[1]>$x) {
												$second_time = $x+$individual_pickup_slot['split_slot_duration'];
												if($second_time > $times[1]) {
													$second_time = $times[1];
												}
												$disable = $individual_pickup_slot['disable_for'];
												if($individual_pickup_slot['enable_single_splited_slot']) {
													$pickup_disable_timeslot['postcode'][] = date("H:i", mktime(0, (int)$x));								
												} else {
													$pickup_disable_timeslot['postcode'][] = date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time));							
												}
												
												$x = $second_time;
											}
										} else {
											if($individual_pickup_slot['enable_single_slot']) {
												$pickup_disable_timeslot['postcode'][] = date("H:i", mktime(0, (int)$times[0]));								
											} else {
												$pickup_disable_timeslot['postcode'][] = date("H:i", mktime(0, (int)$times[0])) . ' - ' . date("H:i", mktime(0, (int)$times[1]));						
											}						
										}
							    	}
							    } elseif($multistep_postal_code || $between_postal_code || ($postcode_value == $given_zip || str_replace(" ","",$postcode_value) == $given_zip || strtolower($postcode_value) == strtolower($given_zip) || str_replace(" ","",strtolower($postcode_value)) == strtolower($given_zip) )) {
							    	$times = explode('-', $key);
									if($individual_pickup_slot['enable_split']) {
										$x = $times[0];
										while($times[1]>$x) {
											$second_time = $x+$individual_pickup_slot['split_slot_duration'];
											if($second_time > $times[1]) {
												$second_time = $times[1];
											}
											$disable = $individual_pickup_slot['disable_for'];
											if($individual_pickup_slot['enable_single_splited_slot']) {
												$pickup_disable_timeslot['postcode'][] = date("H:i", mktime(0, (int)$x));								
											} else {
												$pickup_disable_timeslot['postcode'][] = date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time));							
											}
											
											$x = $second_time;
										}
									} else {
										if($individual_pickup_slot['enable_single_slot']) {
											$pickup_disable_timeslot['postcode'][] = date("H:i", mktime(0, (int)$times[0]));								
										} else {
											$pickup_disable_timeslot['postcode'][] = date("H:i", mktime(0, (int)$times[0])) . ' - ' . date("H:i", mktime(0, (int)$times[1]));						
										}						
									}
							    }
							}		  				 
			  			} else {
			  				
			  				if((isset($individual_pickup_slot['disable_state']) && !empty($individual_pickup_slot['disable_state']) && in_array($given_state,$individual_pickup_slot['disable_state']))){
				  				$times = explode('-', $key);

								if($individual_pickup_slot['enable_split']) {
									
									$x = $times[0];
									while($times[1]>$x) {
										$second_time = $x+$individual_pickup_slot['split_slot_duration'];
										if($second_time > $times[1]) {
											$second_time = $times[1];
										}
										$disable = $individual_pickup_slot['disable_for'];
										if($individual_pickup_slot['enable_single_splited_slot']) {
											$pickup_disable_timeslot['state'][] = date("H:i", mktime(0, (int)$x));								
										} else {
											$pickup_disable_timeslot['state'][] = date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time));							
										}
										
										$x = $second_time;
									}
								} else {

									if($individual_pickup_slot['enable_single_slot']) {
										$pickup_disable_timeslot['state'][] = date("H:i", mktime(0, (int)$times[0]));								
									} else {
										$pickup_disable_timeslot['state'][] = date("H:i", mktime(0, (int)$times[0])) . ' - ' . date("H:i", mktime(0, (int)$times[1]));							
									}
									
								}		  				 
				  			} 
			  			}


					} else {


			  			if((isset($individual_pickup_slot['disable_state']) && !empty($individual_pickup_slot['disable_state']) && in_array($given_state,$individual_pickup_slot['disable_state']))){
			  				$times = explode('-', $key);

							if($individual_pickup_slot['enable_split']) {
								
								$x = $times[0];
								while($times[1]>$x) {
									$second_time = $x+$individual_pickup_slot['split_slot_duration'];
									if($second_time > $times[1]) {
										$second_time = $times[1];
									}
									$disable = $individual_pickup_slot['disable_for'];
									if($individual_pickup_slot['enable_single_splited_slot']) {
										$pickup_disable_timeslot['state'][] = date("H:i", mktime(0, (int)$x));								
									} else {
										$pickup_disable_timeslot['state'][] = date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time));							
									}
									
									$x = $second_time;
								}
							} else {

								if($individual_pickup_slot['enable_single_slot']) {
									$pickup_disable_timeslot['state'][] = date("H:i", mktime(0, (int)$times[0]));								
								} else {
									$pickup_disable_timeslot['state'][] = date("H:i", mktime(0, (int)$times[0])) . ' - ' . date("H:i", mktime(0, (int)$times[1]));							
								}
								
							}		  				 
			  			} 

			  			if(isset($individual_pickup_slot['disable_postcode']) && !empty($individual_pickup_slot['disable_postcode'])){
			  				
			  				foreach($individual_pickup_slot['disable_postcode'] as $postcode_value) {
								$multistep_postal_code = false;
								$between_postal_code = false;
								/*$postcode_range = [];*/
							    if (stripos($postcode_value,'...') !== false) {
							    	$range = explode('...', $postcode_value);
							    	if(stripos($given_zip,'-') !== false && stripos($range[0],'-') !== false && stripos($range[1],'-') !== false) {
						
										$sub_range_one = (int)str_replace("-", "", $range[0]);
										$sub_range_two = (int)str_replace("-", "", $range[1]);

										$given_zip_range = (int)str_replace("-", "", $given_zip);
										
										if($this->helper->number_between($given_zip_range, $sub_range_two, $sub_range_one)) {
											$multistep_postal_code = true;
										}
										
									} elseif(stripos($given_zip,'-') === false && stripos($range[0],'-') === false && stripos($range[1],'-') === false) {
										$alphabet_code = preg_replace("/[^a-zA-Z]+/", "", $range[0]);
										$range[0] = preg_replace("/[^0-9]+/", "", $range[0]);
										$range[1] = preg_replace("/[^0-9]+/", "", $range[1]);
										if($alphabet_code != "" && $this->helper->starts_with(strtolower($given_zip), strtolower($alphabet_code)) && $this->helper->number_between(preg_replace("/[^0-9]/", "", $given_zip ), $range[1], $range[0])) {
											$between_postal_code = true;
										} elseif($alphabet_code == "" && is_numeric($given_zip) && $this->helper->number_between($given_zip, $range[1], $range[0])) {
											$between_postal_code = true;
										}
									}
							    }
							    if (substr($postcode_value, -1) == '*') {
							    	if($this->helper->starts_with($given_zip,substr($postcode_value, 0, -1)) || $this->helper->starts_with(strtolower($given_zip),substr(strtolower($postcode_value), 0, -1)) || $this->helper->starts_with(strtoupper($given_zip),substr(strtoupper($postcode_value), 0, -1))) {
							    		$times = explode('-', $key);
										if($individual_pickup_slot['enable_split']) {
											$x = $times[0];
											while($times[1]>$x) {
												$second_time = $x+$individual_pickup_slot['split_slot_duration'];
												if($second_time > $times[1]) {
													$second_time = $times[1];
												}
												$disable = $individual_pickup_slot['disable_for'];
												if($individual_pickup_slot['enable_single_splited_slot']) {
													$pickup_disable_timeslot['postcode'][] = date("H:i", mktime(0, (int)$x));								
												} else {
													$pickup_disable_timeslot['postcode'][] = date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time));							
												}
												
												$x = $second_time;
											}
										} else {
											if($individual_pickup_slot['enable_single_slot']) {
												$pickup_disable_timeslot['postcode'][] = date("H:i", mktime(0, (int)$times[0]));								
											} else {
												$pickup_disable_timeslot['postcode'][] = date("H:i", mktime(0, (int)$times[0])) . ' - ' . date("H:i", mktime(0, (int)$times[1]));						
											}						
										}
							    	}
							    } elseif($multistep_postal_code || $between_postal_code || ($postcode_value == $given_zip || str_replace(" ","",$postcode_value) == $given_zip || strtolower($postcode_value) == strtolower($given_zip) || str_replace(" ","",strtolower($postcode_value)) == strtolower($given_zip) )) {
							    	$times = explode('-', $key);
									if($individual_pickup_slot['enable_split']) {
										$x = $times[0];
										while($times[1]>$x) {
											$second_time = $x+$individual_pickup_slot['split_slot_duration'];
											if($second_time > $times[1]) {
												$second_time = $times[1];
											}
											$disable = $individual_pickup_slot['disable_for'];
											if($individual_pickup_slot['enable_single_splited_slot']) {
												$pickup_disable_timeslot['postcode'][] = date("H:i", mktime(0, (int)$x));								
											} else {
												$pickup_disable_timeslot['postcode'][] = date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time));							
											}
											
											$x = $second_time;
										}
									} else {
										if($individual_pickup_slot['enable_single_slot']) {
											$pickup_disable_timeslot['postcode'][] = date("H:i", mktime(0, (int)$times[0]));								
										} else {
											$pickup_disable_timeslot['postcode'][] = date("H:i", mktime(0, (int)$times[0])) . ' - ' . date("H:i", mktime(0, (int)$times[1]));						
										}						
									}
							    }
							}		  				 
			  			}

		  			}


		  			if((isset($individual_pickup_slot['disable_state']) && !empty($individual_pickup_slot['disable_state']))){
		  				$times = explode('-', $key);
						if($individual_pickup_slot['enable_split']) {
							$x = $times[0];
							while($times[1]>$x) {
								$second_time = $x+$individual_pickup_slot['split_slot_duration'];
								if($second_time > $times[1]) {
									$second_time = $times[1];
								}
								$disable = $individual_pickup_slot['disable_for'];
								if($individual_pickup_slot['enable_single_splited_slot']) {
									$pickup_state_zip_disable_timeslot_all['state'][] = date("H:i", mktime(0, (int)$x));								
								} else {
									$pickup_state_zip_disable_timeslot_all['state'][] = date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time));							
								}
								
								$x = $second_time;
							}
						} else {
							if($individual_pickup_slot['enable_single_slot']) {
								$pickup_state_zip_disable_timeslot_all['state'][] = date("H:i", mktime(0, (int)$times[0]));								
							} else {
								$pickup_state_zip_disable_timeslot_all['state'][] = date("H:i", mktime(0, (int)$times[0])) . ' - ' . date("H:i", mktime(0, (int)$times[1]));							
							}
							
						}		  				 
		  			}

		  			if((isset($individual_pickup_slot['disable_postcode']) && !empty($individual_pickup_slot['disable_postcode']))) {
		  				$times = explode('-', $key);
						if($individual_pickup_slot['enable_split']) {
							$x = $times[0];
							while($times[1]>$x) {
								$second_time = $x+$individual_pickup_slot['split_slot_duration'];
								if($second_time > $times[1]) {
									$second_time = $times[1];
								}
								$disable = $individual_pickup_slot['disable_for'];
								if($individual_pickup_slot['enable_single_splited_slot']) {
									$pickup_state_zip_disable_timeslot_all['postcode'][] = date("H:i", mktime(0, (int)$x));								
								} else {
									$pickup_state_zip_disable_timeslot_all['postcode'][] = date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time));							
								}
								
								$x = $second_time;
							}
						} else {
							if($individual_pickup_slot['enable_single_slot']) {
								$pickup_state_zip_disable_timeslot_all['postcode'][] = date("H:i", mktime(0, (int)$times[0]));								
							} else {
								$pickup_state_zip_disable_timeslot_all['postcode'][] = date("H:i", mktime(0, (int)$times[0])) . ' - ' . date("H:i", mktime(0, (int)$times[1]));							
							}						
						}		  				 
		  			} else {
		  				$times = explode('-', $key);
						if($individual_pickup_slot['enable_split']) {
							$x = $times[0];
							while($times[1]>$x) {
								$second_time = $x+$individual_pickup_slot['split_slot_duration'];
								if($second_time > $times[1]) {
									$second_time = $times[1];
								}
								$disable = $individual_pickup_slot['disable_for'];
								if($individual_pickup_slot['enable_single_splited_slot']) {
									$pickup_no_state_zip_disable_timeslot_all['nostatezip'][] = date("H:i", mktime(0, (int)$x));								
								} else {
									$pickup_no_state_zip_disable_timeslot_all['nostatezip'][] = date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time));							
								}
								
								$x = $second_time;
							}

						} else {
							if($individual_pickup_slot['enable_single_slot']) {
								$pickup_no_state_zip_disable_timeslot_all['nostatezip'][] = date("H:i", mktime(0, (int)$times[0]));								
							} else {
								$pickup_no_state_zip_disable_timeslot_all['nostatezip'][] = date("H:i", mktime(0, (int)$times[0])) . ' - ' . date("H:i", mktime(0, (int)$times[1]));							
							}

						}		  				 
		  			}

		  			if(isset($individual_pickup_slot['hide_for_pickup_location']) && !empty($individual_pickup_slot['hide_for_pickup_location']) && $enable_pickup_location) {
		  				$detect_pickup_location_hide = true;
		  				if((isset($individual_pickup_slot['hide_for_pickup_location']) && !empty($individual_pickup_slot['hide_for_pickup_location']) && in_array($given_location,$individual_pickup_slot['hide_for_pickup_location']))){
			  				$times = explode('-', $key);

							if($individual_pickup_slot['enable_split']) {
								
								$x = $times[0];
								while($times[1]>$x) {
									$second_time = $x+$individual_pickup_slot['split_slot_duration'];
									if($second_time > $times[1]) {
										$second_time = $times[1];
									}
									if($individual_pickup_slot['enable_single_splited_slot']) {
										$pickup_disable_timeslot_location['location'][] = date("H:i", mktime(0, (int)$x));								
									} else {
										$pickup_disable_timeslot_location['location'][] = date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time));							
									}
									
									$x = $second_time;
								}
							} else {

								if($individual_pickup_slot['enable_single_slot']) {
									$pickup_disable_timeslot_location['location'][] = date("H:i", mktime(0, (int)$times[0]));								
								} else {
									$pickup_disable_timeslot_location['location'][] = date("H:i", mktime(0, (int)$times[0])) . ' - ' . date("H:i", mktime(0, (int)$times[1]));							
								}
								
							}		  				 
			  			}



			  			if((isset($individual_pickup_slot['hide_for_pickup_location']) && !empty($individual_pickup_slot['hide_for_pickup_location']))){
		  					$times = explode('-', $key);
							if($individual_pickup_slot['enable_split']) {
								$x = $times[0];
								while($times[1]>$x) {
									$second_time = $x+$individual_pickup_slot['split_slot_duration'];
									if($second_time > $times[1]) {
										$second_time = $times[1];
									}
									if($individual_pickup_slot['enable_single_splited_slot']) {
										$pickup_location_disable_timeslot_all['location'][] = date("H:i", mktime(0, (int)$x));								
									} else {
										$pickup_location_disable_timeslot_all['location'][] = date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time));							
									}
									
									$x = $second_time;
								}
							} else {
								if($individual_pickup_slot['enable_single_slot']) {
									$pickup_location_disable_timeslot_all['location'][] = date("H:i", mktime(0, (int)$times[0]));								
								} else {
									$pickup_location_disable_timeslot_all['location'][] = date("H:i", mktime(0, (int)$times[0])) . ' - ' . date("H:i", mktime(0, (int)$times[1]));							
								}
								
							}		  				 
		  				} else {
			  				$times = explode('-', $key);
							if($individual_pickup_slot['enable_split']) {
								$x = $times[0];
								while($times[1]>$x) {
									$second_time = $x+$individual_pickup_slot['split_slot_duration'];
									if($second_time > $times[1]) {
										$second_time = $times[1];
									}
									if($individual_pickup_slot['enable_single_splited_slot']) {
										$pickup_no_location_disable_timeslot_all['nolocation'][] = date("H:i", mktime(0, (int)$x));								
									} else {
										$pickup_no_location_disable_timeslot_all['nolocation'][] = date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time));							
									}
									
									$x = $second_time;
								}

							} else {
								if($individual_pickup_slot['enable_single_slot']) {
									$pickup_no_location_disable_timeslot_all['nolocation'][] = date("H:i", mktime(0, (int)$times[0]));								
								} else {
									$pickup_no_location_disable_timeslot_all['nolocation'][] = date("H:i", mktime(0, (int)$times[0])) . ' - ' . date("H:i", mktime(0, (int)$times[1]));							
								}

							}		  				 
			  			}
		  			}


	  			}
	  		}
	  	} else {
	  		
	  		$pickup_settings = get_option('coderockz_woo_delivery_pickup_settings');
	  		$enable_pickup_time = (isset($pickup_settings['enable_pickup_time']) && !empty($pickup_settings['enable_pickup_time'])) ? $pickup_settings['enable_pickup_time'] : false;
		    if($enable_pickup_time) {
		  		$x = (int)$pickup_settings['pickup_time_starts'];
		  		$each_pickup_slot = (isset($pickup_settings['each_time_slot']) && !empty($pickup_settings['each_time_slot'])) ? (int)$pickup_settings['each_time_slot'] : (int)$pickup_settings['pickup_time_ends']-(int)$pickup_settings['pickup_time_starts'];
				while((int)$pickup_settings['pickup_time_ends']>$x) {
					$second_time = $x+$each_pickup_slot;
					if($second_time > (int)$pickup_settings['pickup_time_ends']) {
						$second_time = (int)$pickup_settings['pickup_time_ends'];
					}
					$pickup_no_state_zip_disable_timeslot_all['nostatezip'][] = date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time));		
					$x = $second_time;
				}

			}	  		
	  	}

	  	$free_up_slot_for_pickup_completed = (isset($delivery_pickup_settings['free_up_slot_for_pickup_completed']) && !empty($delivery_pickup_settings['free_up_slot_for_pickup_completed'])) ? $delivery_pickup_settings['free_up_slot_for_pickup_completed'] : false;

	  	foreach ($order_ids as $order) {
			$date = get_post_meta($order,"pickup_date",true);
			$time = get_post_meta($order,"pickup_time",true);
			$location = get_post_meta($order,"delivery_pickup",true);
			
			if((isset($date) && isset($time)) || isset($time)) {
				if(!$free_up_slot_for_pickup_completed) {
					$pickup_delivery_times[] = $time;
				} else {
					if(!metadata_exists('post', $order, 'delivery_status')) {
						$pickup_delivery_times[] = $time;
					}
				}
			}

			if((isset($date) && isset($location)) || isset($location)) {
				$pickup_delivery_locations[] = $location;
			}

		}

		$unique_pickup_times = array_unique($pickup_delivery_times, false);
		$unique_pickup_times = array_values($unique_pickup_times);

		$unique_pickup_locations = array_unique($pickup_delivery_locations, false);
		$unique_pickup_locations = array_values($unique_pickup_locations);


		if($enable_pickup_location && isset($pickup_location_settings['pickup_location']) && count($pickup_location_settings['pickup_location'])>0){
	  		foreach($pickup_location_settings['pickup_location'] as $name => $settings) {

	  			if($settings['enable']) {
		  			
					$disable = $settings['disable_for'];

					$pickup_location_disable_for[stripslashes($name)] = $disable;

					if(in_array(stripslashes($name),$unique_pickup_locations)) {
						$pickup_max_order_per_location[stripslashes($name)] = (isset($settings['max_order']) && $settings['max_order'] !="") ? (int)sanitize_text_field($settings['max_order']) : "";
					}

				}

			}
		}

		if($enable_custom_pickup_slot) {
			if(isset($custom_pickup_slot_settings['time_slot']) && count($custom_pickup_slot_settings['time_slot'])>0){

				foreach($custom_pickup_slot_settings['time_slot'] as $key => $individual_pickup_slot) {

		  			if($individual_pickup_slot['enable']) {
			  			$key = preg_replace('/-/', ',', $key);

			  			$key_array = explode(",",$key);

					    $max_order = (isset($individual_pickup_slot['max_order']) && $individual_pickup_slot['max_order'] !="") ? sanitize_text_field($individual_pickup_slot['max_order']) : "";
					
					    if($individual_pickup_slot['enable_split']) {
							$x = $key_array[0];
							while($key_array[1]>$x) {
								$second_time = $x+$individual_pickup_slot['split_slot_duration'];
								if($second_time > $key_array[1]) {
									$second_time = $key_array[1];
								}
								if($individual_pickup_slot['enable_single_splited_slot']) {
									if(in_array(date("H:i", mktime(0, (int)$x)),$unique_pickup_times)) {
										$pickup_max_order_per_slot[date("H:i", mktime(0, (int)$x))] = (int)$max_order;
									}
									
								} else {
									if(in_array(date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time)),$unique_pickup_times)) {
									$pickup_max_order_per_slot[date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time))] = (int)$max_order;
									}						
								}
								
								$x = $second_time;
							}

						} else {
							if($individual_pickup_slot['enable_single_slot']) {
					
								if(in_array(date("H:i", mktime(0, (int)$key_array[0])),$unique_pickup_times)) {
									$pickup_max_order_per_slot[date("H:i", mktime(0, (int)$key_array[0]))] = (int)$max_order;
								}
							} else {
								if(in_array(date("H:i", mktime(0, (int)$key_array[0])) . ' - ' . date("H:i", mktime(0, (int)$key_array[1])),$unique_pickup_times)) {
									$pickup_max_order_per_slot[date("H:i", mktime(0, (int)$key_array[0])) . ' - ' . date("H:i", mktime(0, (int)$key_array[1]))] = (int)$max_order;	
								}						
							}

						}

					}
				}
			}
		} else {

		    $pickup_settings = get_option('coderockz_woo_delivery_pickup_settings');
		    $enable_pickup_time = (isset($pickup_settings['enable_pickup_time']) && !empty($pickup_settings['enable_pickup_time'])) ? $pickup_settings['enable_pickup_time'] : false;
		    if($enable_pickup_time) {
			    $max_order = (isset($pickup_settings['max_pickup_per_slot']) && $pickup_settings['max_pickup_per_slot'] !="") ? sanitize_text_field($pickup_settings['max_pickup_per_slot']) : "";
		  		$x = (int)$pickup_settings['pickup_time_starts'];
		  		$each_pickup_slot = (isset($pickup_settings['each_time_slot']) && !empty($pickup_settings['each_time_slot'])) ? (int)$pickup_settings['each_time_slot'] : (int)$pickup_settings['pickup_time_ends']-(int)$pickup_settings['pickup_time_starts'];
				while((int)$pickup_settings['pickup_time_ends']>$x) {
					$second_time = $x+$each_pickup_slot;
					if($second_time > (int)$pickup_settings['pickup_time_ends']) {
						$second_time = (int)$pickup_settings['pickup_time_ends'];
					}
					if(in_array(date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time)),$unique_pickup_times)) {
						$pickup_max_order_per_slot[date("H:i", mktime(0, (int)$x)) . ' - ' . date("H:i", mktime(0, (int)$second_time))] = (int)$max_order;
					}		
					$x = $second_time;
				}

			}
		}


		$response_pickup = [
			"pickup_delivery_times" => $pickup_delivery_times,
			"pickup_max_order_per_slot" => $pickup_max_order_per_slot,
			"pickup_slot_disable_for" => $pickup_slot_disable_for,
			'pickup_disabled_current_time_slot' => $pickup_disabled_current_time_slot,
			'pickup_disable_timeslot' => $pickup_disable_timeslot,
			'pickup_state_zip_disable_timeslot_all' => $pickup_state_zip_disable_timeslot_all,
			'pickup_no_state_zip_disable_timeslot_all' => $pickup_no_state_zip_disable_timeslot_all,
			'pickup_disable_timeslot_location' => $pickup_disable_timeslot_location,
			'pickup_location_disable_timeslot_all' => $pickup_location_disable_timeslot_all,
			'pickup_no_location_disable_timeslot_all' => $pickup_no_location_disable_timeslot_all,
			'detect_pickup_location_hide' => $detect_pickup_location_hide,
			'pickup_location_disable_for' => $pickup_location_disable_for,
			'pickup_delivery_locations' => $pickup_delivery_locations,
			'pickup_max_order_per_location' => $pickup_max_order_per_location,
			'max_pickup_slot_individual_location' => $max_pickup_slot_individual_location,
			'pickup_slot_disable_for_sameday' => $pickup_slot_disable_for_sameday,
			'pickup_slot_disable_for_excceed' => $pickup_slot_disable_for_excceed,
			'pickup_slot_open_specific_date' => $pickup_slot_open_specific_date,
			'pickup_slot_close_specific_date' => $pickup_slot_close_specific_date
		];

		$formated_date = date('Y-m-d H:i:s', strtotime(sanitize_text_field($formated_selected_date)));
		$formated_date_obj = new DateTime($formated_date);
		$formated_date = $formated_date_obj->format("w");
		$formated_pickup_date_selected = $formated_date_obj->format("Y-m-d");

		$current_time = (date("G")*60)+date("i");

		$response_for_all = [
			"formated_selected_date" => $formated_selected_date,
			"formated_date" => $formated_date,
			"current_time" => $current_time,
			"formated_pickup_date_selected" => $formated_pickup_date_selected,
		];

		$response = array_merge($response_pickup, $response_for_all);

		$response = json_encode($response);
		wp_send_json_success($response);
	}

	public function coderockz_woo_delivery_get_correct_formated_date() {
		check_ajax_referer('coderockz_woo_delivery_nonce');

		$timezone = $this->helper->get_the_timezone();
		date_default_timezone_set($timezone);
		if(isset($_POST['formatedDateSelected']) && $_POST['formatedDateSelected'] != "") {
			$formated_date_selected = $this->helper->weekday_conversion($this->helper->date_conversion(sanitize_text_field($_POST['formatedDateSelected']),"delivery"),"delivery");
			$formated_date_selected = date('Y-m-d', strtotime($formated_date_selected));
		} else {
			$formated_date_selected = "";
		}
		if(isset($_POST['formatedPickupDateSelected']) && $_POST['formatedPickupDateSelected'] != "") {
			$formated_pickup_date_selected = $this->helper->weekday_conversion($this->helper->date_conversion(sanitize_text_field($_POST['formatedPickupDateSelected']),"pickup"),"pickup");
			$formated_pickup_date_selected = date('Y-m-d', strtotime($formated_pickup_date_selected));
		} else {
			$formated_pickup_date_selected = "";
		}

		$response = [
			"formated_date_selected" => $formated_date_selected,
			"formated_pickup_date_selected" => $formated_pickup_date_selected
		];
		$response = json_encode($response);
		wp_send_json_success($response);


	}


	public function coderockz_woo_delivery_get_state_zip_disable_weekday_checkout() {
		check_ajax_referer('coderockz_woo_delivery_nonce');
		$offdays_settings = get_option('coderockz_woo_delivery_off_days_settings');
		$given_state_offdays_delivery = [];
		$given_zip_offdays_delivery = [];
		$given_state_offdays_pickup = [];
		$given_zip_offdays_pickup = [];
		$given_state = (isset($_POST['givenState']) && $_POST['givenState'] !="") ? sanitize_text_field($_POST['givenState']) : "";
		if($given_state != "") {

			if(isset($offdays_settings['state_wise_offdays']) && !empty($offdays_settings['state_wise_offdays']) && isset($offdays_settings['state_wise_offdays'][$given_state]) && !empty($offdays_settings['state_wise_offdays'][$given_state])) { 
				$given_state_offdays_delivery = $offdays_settings['state_wise_offdays'][$given_state];
				$given_state_offdays_pickup = $offdays_settings['state_wise_offdays'][$given_state];
			}

		}

		$given_zip = (isset($_POST['givenZip']) && $_POST['givenZip'] !="") ? sanitize_text_field($_POST['givenZip']) : "";
		if(isset($offdays_settings['postcode_wise_offdays']) && !empty($offdays_settings['postcode_wise_offdays'])) {
			
			foreach($offdays_settings['postcode_wise_offdays'] as $key => $off_days) {
				$multistep_postal_code = false;
				$between_postal_code = false;
				/*$individual_postcode_range = [];*/
			    if (stripos($key,'...') !== false) {
			    	$range = explode('...', $key);
			    	if(stripos($given_zip,'-') !== false && stripos($range[0],'-') !== false && stripos($range[1],'-') !== false) {
						
			    		$sub_range_one = (int)str_replace("-", "", $range[0]);
			    		$sub_range_two = (int)str_replace("-", "", $range[1]);

						$given_zip_range = (int)str_replace("-", "", $given_zip);
						
						if($this->helper->number_between($given_zip_range, $sub_range_two, $sub_range_one)) {
							$multistep_postal_code = true;
						}
						
					} elseif(stripos($given_zip,'-') === false && stripos($range[0],'-') === false && stripos($range[1],'-') === false) {
						$alphabet_code = preg_replace("/[^a-zA-Z]+/", "", $range[0]);
						$range[0] = preg_replace("/[^0-9]+/", "", $range[0]);
						$range[1] = preg_replace("/[^0-9]+/", "", $range[1]);
						if($alphabet_code != "" && $this->helper->starts_with(strtolower($given_zip), strtolower($alphabet_code)) && $this->helper->number_between(preg_replace("/[^0-9]/", "", $given_zip ), $range[1], $range[0])) {
							$between_postal_code = true;
						} elseif($alphabet_code == "" && is_numeric($given_zip) && $this->helper->number_between($given_zip, $range[1], $range[0])) {
							$between_postal_code = true;
						}
					}
			    }
			    if (substr($key, -1) == '*') {
			    	if($this->helper->starts_with($given_zip,substr($key, 0, -1)) || $this->helper->starts_with(strtolower($given_zip),substr(strtolower($key), 0, -1)) || $this->helper->starts_with(strtoupper($given_zip),substr(strtoupper($key), 0, -1))) {
			    		$given_zip_offdays = [];
						foreach($off_days as $off_day) {
							$given_zip_offdays_delivery[] = $off_day;
							$given_zip_offdays_pickup[] = $off_day;
						}
			    	}
			    } elseif($multistep_postal_code || $between_postal_code || ($key == $given_zip || str_replace(" ","",$key) == $given_zip || strtolower($key) == strtolower($given_zip) || str_replace(" ","",strtolower($key)) == strtolower($given_zip) )) {
					foreach($off_days as $off_day) {
						$given_zip_offdays_delivery[] = $off_day;
						$given_zip_offdays_pickup[] = $off_day;
					}
			    }
			}			
		}

		if(isset($offdays_settings['zone_wise_offdays']) && !empty($offdays_settings['zone_wise_offdays'])) {
			
			if(isset($offdays_settings['zone_wise_offdays']['both']) && !empty($offdays_settings['zone_wise_offdays']['both'])) {
				
				foreach($offdays_settings['zone_wise_offdays']['both'] as $zone) {

					if(isset($zone['postcode']) && !empty($zone['postcode'])) {
						foreach($zone['postcode'] as $postcode_value) {
							$multistep_postal_code = false;
							$between_postal_code = false;
							$postcode_range = [];
						    if (stripos($postcode_value,'...') !== false) {
						    	$range = explode('...', $postcode_value);

						    	if(stripos($given_zip,'-') !== false && stripos($range[0],'-') !== false && stripos($range[1],'-') !== false) {
						
									$sub_range_one = (int)str_replace("-", "", $range[0]);
									$sub_range_two = (int)str_replace("-", "", $range[1]);

									$given_zip_range = (int)str_replace("-", "", $given_zip);
									
									if($this->helper->number_between($given_zip_range, $sub_range_two, $sub_range_one)) {
										$multistep_postal_code = true;
									}
									
								} elseif(stripos($given_zip,'-') === false && stripos($range[0],'-') === false && stripos($range[1],'-') === false) {
									$alphabet_code = preg_replace("/[^a-zA-Z]+/", "", $range[0]);
									$range[0] = preg_replace("/[^0-9]+/", "", $range[0]);
									$range[1] = preg_replace("/[^0-9]+/", "", $range[1]);
									if($alphabet_code != "" && $this->helper->starts_with(strtolower($given_zip), strtolower($alphabet_code)) && $this->helper->number_between(preg_replace("/[^0-9]/", "", $given_zip ), $range[1], $range[0])) {
										$between_postal_code = true;
									} elseif($alphabet_code == "" && is_numeric($given_zip) && $this->helper->number_between($given_zip, $range[1], $range[0])) {
										$between_postal_code = true;
									}
								}
						    }
						    if (substr($postcode_value, -1) == '*') {
						    	if($this->helper->starts_with($given_zip,substr($postcode_value, 0, -1)) || $this->helper->starts_with(strtolower($given_zip),substr(strtolower($postcode_value), 0, -1)) || $this->helper->starts_with(strtoupper($given_zip),substr(strtoupper($postcode_value), 0, -1))) {
						    		$off_days = explode(",",$zone['off_days']);
						    		$given_zip_offdays_delivery = [];
						    		$given_zip_offdays_pickup = [];
									foreach($off_days as $off_day) {

										$given_zip_offdays_delivery[] = $off_day;
										$given_zip_offdays_pickup[] = $off_day;
										
									}
						    	}
						    } elseif($multistep_postal_code || $between_postal_code || ($postcode_value == $given_zip || str_replace(" ","",$postcode_value) == $given_zip || strtolower($postcode_value) == strtolower($given_zip) || str_replace(" ","",strtolower($postcode_value)) == strtolower($given_zip) )) {
						    	$off_days = explode(",",$zone['off_days']);
								foreach($off_days as $off_day) {

									$given_zip_offdays_delivery[] = $off_day;
									$given_zip_offdays_pickup[] = $off_day;
								}
						    }
						}
											
					} elseif(isset($zone['state']) && !empty($zone['state']) && isset($zone['state'][$given_state])) {
						$given_state_offdays_delivery = [];
						$given_state_offdays_pickup = [];
						$off_days = explode(",",$zone['off_days']);
						foreach($off_days as $off_day) {
							$given_state_offdays_delivery[] = $off_day;
							$given_state_offdays_pickup[] = $off_day;
						}					
					}
					
				}

			}

			if(isset($offdays_settings['zone_wise_offdays']['delivery']) && !empty($offdays_settings['zone_wise_offdays']['delivery'])) {
				
				foreach($offdays_settings['zone_wise_offdays']['delivery'] as $zone) {

					if(isset($zone['postcode']) && !empty($zone['postcode'])) {
						foreach($zone['postcode'] as $postcode_value) {
							$multistep_postal_code = false;
							$between_postal_code = false;
							/*$postcode_range = [];*/
						    if (stripos($postcode_value,'...') !== false) {
						    	$range = explode('...', $postcode_value);

						    	if(stripos($given_zip,'-') !== false && stripos($range[0],'-') !== false && stripos($range[1],'-') !== false) {
						
									$sub_range_one = (int)str_replace("-", "", $range[0]);
									$sub_range_two = (int)str_replace("-", "", $range[1]);

									$given_zip_range = (int)str_replace("-", "", $given_zip);
									
									if($this->helper->number_between($given_zip_range, $sub_range_two, $sub_range_one)) {
										$multistep_postal_code = true;
									}
									
								} elseif(stripos($given_zip,'-') === false && stripos($range[0],'-') === false && stripos($range[1],'-') === false) {
							    	$alphabet_code = preg_replace("/[^a-zA-Z]+/", "", $range[0]);
									$range[0] = preg_replace("/[^0-9]+/", "", $range[0]);
									$range[1] = preg_replace("/[^0-9]+/", "", $range[1]);
									if($alphabet_code != "" && $this->helper->starts_with(strtolower($given_zip), strtolower($alphabet_code)) && $this->helper->number_between(preg_replace("/[^0-9]/", "", $given_zip ), $range[1], $range[0])) {
										$between_postal_code = true;
									} elseif($alphabet_code == "" && is_numeric($given_zip) && $this->helper->number_between($given_zip, $range[1], $range[0])) {
										$between_postal_code = true;
									}
						    	}
						    }
						    if (substr($postcode_value, -1) == '*') {
						    	if($this->helper->starts_with($given_zip,substr($postcode_value, 0, -1)) || $this->helper->starts_with(strtolower($given_zip),substr(strtolower($postcode_value), 0, -1)) || $this->helper->starts_with(strtoupper($given_zip),substr(strtoupper($postcode_value), 0, -1))) {
						    		$off_days = explode(",",$zone['off_days']);
						    		$given_zip_offdays_delivery = [];
									foreach($off_days as $off_day) {

										$given_zip_offdays_delivery[] = $off_day;
										
									}
						    	}
						    } elseif($multistep_postal_code || $between_postal_code || ($postcode_value == $given_zip || str_replace(" ","",$postcode_value) == $given_zip || strtolower($postcode_value) == strtolower($given_zip) || str_replace(" ","",strtolower($postcode_value)) == strtolower($given_zip) )) {
						    	$off_days = explode(",",$zone['off_days']);
								foreach($off_days as $off_day) {
									$given_zip_offdays_delivery[] = $off_day;
								}
						    }
						}
											
					} elseif(isset($zone['state']) && !empty($zone['state']) && isset($zone['state'][$given_state])) {
						$given_state_offdays_delivery = [];
						$off_days = explode(",",$zone['off_days']);
						foreach($off_days as $off_day) {
							$given_state_offdays_delivery[] = $off_day;
						}					
					}
					
				}

			}

			if(isset($offdays_settings['zone_wise_offdays']['pickup']) && !empty($offdays_settings['zone_wise_offdays']['pickup'])) {

				foreach($offdays_settings['zone_wise_offdays']['pickup'] as $zone) {

					if(isset($zone['postcode']) && !empty($zone['postcode'])) {
						foreach($zone['postcode'] as $postcode_value) {
							$multistep_postal_code = false;
							$between_postal_code = false;
							/*$postcode_range = [];*/
						    if (stripos($postcode_value,'...') !== false) {
						    	$range = explode('...', $postcode_value);
						    	if(stripos($given_zip,'-') !== false && stripos($range[0],'-') !== false && stripos($range[1],'-') !== false) {
						
									$sub_range_one = (int)str_replace("-", "", $range[0]);
									$sub_range_two = (int)str_replace("-", "", $range[1]);

									$given_zip_range = (int)str_replace("-", "", $given_zip);
									
									if($this->helper->number_between($given_zip_range, $sub_range_two, $sub_range_one)) {
										$multistep_postal_code = true;
									}
									
								} elseif(stripos($given_zip,'-') === false && stripos($range[0],'-') === false && stripos($range[1],'-') === false) {
									$alphabet_code = preg_replace("/[^a-zA-Z]+/", "", $range[0]);
									$range[0] = preg_replace("/[^0-9]+/", "", $range[0]);
									$range[1] = preg_replace("/[^0-9]+/", "", $range[1]);
									if($alphabet_code != "" && $this->helper->starts_with(strtolower($given_zip), strtolower($alphabet_code)) && $this->helper->number_between(preg_replace("/[^0-9]/", "", $given_zip ), $range[1], $range[0])) {
										$between_postal_code = true;
									} elseif($alphabet_code == "" && is_numeric($given_zip) && $this->helper->number_between($given_zip, $range[1], $range[0])) {
										$between_postal_code = true;
									}
								}
						    }
						    if (substr($postcode_value, -1) == '*') {
						    	if($this->helper->starts_with($given_zip,substr($postcode_value, 0, -1)) || $this->helper->starts_with(strtolower($given_zip),substr(strtolower($postcode_value), 0, -1)) || $this->helper->starts_with(strtoupper($given_zip),substr(strtoupper($postcode_value), 0, -1))) {
						    		$off_days = explode(",",$zone['off_days']);
						    		$given_zip_offdays_pickup = [];
									foreach($off_days as $off_day) {
										$given_zip_offdays_pickup[] = $off_day;
										
									}
						    	}
						    } elseif($multistep_postal_code || $between_postal_code || ($postcode_value == $given_zip || str_replace(" ","",$postcode_value) == $given_zip || strtolower($postcode_value) == strtolower($given_zip) || str_replace(" ","",strtolower($postcode_value)) == strtolower($given_zip) )) {
						    	$off_days = explode(",",$zone['off_days']);
								foreach($off_days as $off_day) {
									$given_zip_offdays_pickup[] = $off_day;
								}


						    }
						}
											
					} elseif(isset($zone['state']) && !empty($zone['state']) && isset($zone['state'][$given_state])) {
						$given_state_offdays_pickup = [];
						$off_days = explode(",",$zone['off_days']);
						foreach($off_days as $off_day) {
							$given_state_offdays_pickup[] = $off_day;
						}					
					}
					
				}

			}

		}

		$given_zip_offdays_delivery = array_values(array_unique($given_zip_offdays_delivery, false));
		$given_state_offdays_delivery = array_values(array_unique($given_state_offdays_delivery, false));
		$given_zip_offdays_pickup = array_values(array_unique($given_zip_offdays_pickup, false));
		$given_state_offdays_pickup = array_values(array_unique($given_state_offdays_pickup, false));

		$timezone = $this->helper->get_the_timezone();
		date_default_timezone_set($timezone);
		if(isset($_POST['formatedDateSelected']) && $_POST['formatedDateSelected'] != "") {
			$formated_date_selected = $this->helper->weekday_conversion($this->helper->date_conversion(sanitize_text_field($_POST['formatedDateSelected']),"delivery"),"delivery");
			$formated_date_selected = date('Y-m-d', strtotime($formated_date_selected));
		} else {
			$formated_date_selected = "";
		}
		if(isset($_POST['formatedPickupDateSelected']) && $_POST['formatedPickupDateSelected'] != "") {
			$formated_pickup_date_selected = $this->helper->weekday_conversion($this->helper->date_conversion(sanitize_text_field($_POST['formatedPickupDateSelected']),"pickup"),"pickup");
			$formated_pickup_date_selected = date('Y-m-d', strtotime($formated_pickup_date_selected));
		} else {
			$formated_pickup_date_selected = "";
		}

		$response = [
			"given_state_offdays_delivery" => $given_state_offdays_delivery,
			"given_zip_offdays_delivery" => $given_zip_offdays_delivery,
			"given_state_offdays_pickup" => $given_state_offdays_pickup,
			"given_zip_offdays_pickup" => $given_zip_offdays_pickup,
			"formated_date_selected" => $formated_date_selected,
			"formated_pickup_date_selected" => $formated_pickup_date_selected
		];
		$response = json_encode($response);
		wp_send_json_success($response);


	}

	public function coderockz_woo_delivery_disable_max_delivery_pickup_date() {
		check_ajax_referer('coderockz_woo_delivery_nonce');
		$delivery_date_settings = get_option('coderockz_woo_delivery_date_settings');
		$pickup_date_settings = get_option('coderockz_woo_delivery_pickup_date_settings');
		$enable_delivery_date = (isset($delivery_date_settings['enable_delivery_date']) && !empty($delivery_date_settings['enable_delivery_date'])) ? $delivery_date_settings['enable_delivery_date'] : false;
		$enable_pickup_date = (isset($pickup_date_settings['enable_pickup_date']) && !empty($pickup_date_settings['enable_pickup_date'])) ? $pickup_date_settings['enable_pickup_date'] : false;
		// if any timezone data is saved, set default timezone with the data
		$timezone = $this->helper->get_the_timezone();
		date_default_timezone_set($timezone);

		$order_status_keys = array_keys(wc_get_order_statuses());
		$order_status = ['partially-paid'];
		foreach($order_status_keys as $order_status_key) {
			$order_status[] = substr($order_status_key,3);
		}
		$order_status = array_diff($order_status,['cancelled','failed','refunded']);

		$disable_for_max_delivery_dates = [];
		$disable_for_max_pickup_dates = [];
		$delivery_days_delivery = [];
		$need_checking_max_order = true;
		$pickup_days_delivery = [];
		$pickup_need_checking_max_order = true;
		$today = date('Y-m-d', time());
		$range_first_date = date('Y-m-d', strtotime($today));
		$formated_obj = new DateTime($range_first_date);

		$second_range_first_date = date('Y-m-d', strtotime($today));
		$second_formated_obj = new DateTime($second_range_first_date);
		$second_range_last_date = $second_formated_obj->modify("+10 day")->format("Y-m-d");
		$second_filtered_date = $second_range_first_date . ',' . $second_range_last_date;
		$second_filtered_dates = explode(',', $second_filtered_date);
		$second_period = new DatePeriod(new DateTime($second_filtered_dates[0]), new DateInterval('P1D'), new DateTime($second_filtered_dates[1].' +1 day'));
	    
		if($enable_delivery_date) {

			if((isset($delivery_date_settings['maximum_order_per_day']) && $delivery_date_settings['maximum_order_per_day'] != "")) {

				$range_last_date = $formated_obj->modify("+40 day")->format("Y-m-d");
				$filtered_date = $range_first_date . ',' . $range_last_date;
				$filtered_dates = explode(',', $filtered_date);
				$period = new DatePeriod(new DateTime($filtered_dates[0]), new DateInterval('P1D'), new DateTime($filtered_dates[1].' +1 day'));


				$max_order_per_day = (isset($delivery_date_settings['maximum_order_per_day']) && $delivery_date_settings['maximum_order_per_day'] != "") ? (int)$delivery_date_settings['maximum_order_per_day'] : 10000000000000;
				foreach ($period as $date) { 
					$args = array(
				        'limit' => -1,
				        'delivery_date' => date("Y-m-d", strtotime($date->format("Y-m-d"))),
				        /*'delivery_type' => "delivery",*/
				        'status' => $order_status,
				        'return' => 'ids'
				    );
				    $orders_array = wc_get_orders( $args );
				    if(count($orders_array) >= $max_order_per_day) {
						$disable_for_max_delivery_dates[] = date('Y-m-d', strtotime($date->format("Y-m-d")));
				    }
				}
			}


			$custom_time_slot_settings = get_option('coderockz_woo_delivery_time_slot_settings');
			$enable_custom_time_slot = (isset($custom_time_slot_settings['enable_custom_time_slot']) && !empty($custom_time_slot_settings['enable_custom_time_slot'])) ? $custom_time_slot_settings['enable_custom_time_slot'] : false;
			if($enable_custom_time_slot) {
				if(isset($custom_time_slot_settings['time_slot']) && count($custom_time_slot_settings['time_slot'])>0 && count($custom_time_slot_settings['time_slot'])<=2){
					$max_order = true;
					foreach($custom_time_slot_settings['time_slot'] as $key => $individual_time_slot) {
						if((isset($individual_time_slot['max_order']) && $individual_time_slot['max_order'] !="")) {

						} else {
							$max_order = false;
							break;
						}
					}

					if($max_order) {

						foreach($custom_time_slot_settings['time_slot'] as $key => $individual_time_slot) {

				  			if($individual_time_slot['enable']) {
				  				
				  				if((isset($individual_time_slot['max_order']) && $individual_time_slot['max_order'] !="")) {

					  				$hide_date_timeslot = isset($individual_time_slot['only_specific_date_close']) && $individual_time_slot['only_specific_date_close'] !="" ? sanitize_text_field($individual_time_slot['only_specific_date_close']) : "";
					  				$hide_date_timeslot = explode(",",$hide_date_timeslot);
					  				$available_date_timeslot = isset($individual_time_slot['only_specific_date']) && $individual_time_slot['only_specific_date'] !="" ? sanitize_text_field($individual_time_slot['only_specific_date']) : "";
					  				$available_date_timeslot = explode(",",$available_date_timeslot);
					  				foreach($second_period as $delivery_day) {
					  					$delivery_day = date("Y-m-d", strtotime($delivery_day->format("Y-m-d")));
					  					if(isset($individual_time_slot['only_specific_date']) && $individual_time_slot['only_specific_date'] !="" && !in_array($delivery_day,$available_date_timeslot)) {
					  						continue;
					  					}
					  					if((!empty($available_date_timeslot) && in_array($delivery_day,$available_date_timeslot)) || (!in_array($delivery_day,$hide_date_timeslot) && !in_array(date('w', strtotime($delivery_day)),$individual_time_slot['disable_for']))) {
					  						
					  						$max_order = sanitize_text_field($individual_time_slot['max_order']);

								  			if($individual_time_slot['enable_split']) {
												$times = explode('-', $key);
												$x = $times[0];
												while($times[1]>$x) {
													$second_time = $x+$individual_time_slot['split_slot_duration'];
													if($second_time > $times[1]) {
														$second_time = $times[1];
													}
													if(isset($delivery_days_delivery[$delivery_day])) {
														$delivery_days_delivery[$delivery_day] = (int)$delivery_days_delivery[$delivery_day] + (int)$max_order;
													} else {
														$delivery_days_delivery[$delivery_day] = (int)$max_order;
													}
													
													
													$x = $second_time;
												}
											} else {
												if(isset($delivery_days_delivery[$delivery_day])) {
													$delivery_days_delivery[$delivery_day] = (int)$delivery_days_delivery[$delivery_day] + (int)$max_order;
												} else {
													$delivery_days_delivery[$delivery_day] = (int)$max_order;
												}
												
											}

					  					}
					  				}
				  				}
				  			}
				  		}
			  		} else {
  						$need_checking_max_order = false;
  					}
			  	}
			} else {

				$time_settings = get_option('coderockz_woo_delivery_time_settings');
			    $enable_delivery_time = (isset($time_settings['enable_delivery_time']) && !empty($time_settings['enable_delivery_time'])) ? $time_settings['enable_delivery_time'] : false;
			    $max_order = (isset($time_settings['max_order_per_slot']) && $time_settings['max_order_per_slot'] !="") ? sanitize_text_field($time_settings['max_order_per_slot']) : "";
			    if($enable_delivery_time && $max_order != "" && (int)$max_order <= 15) {
				    
			  		$x = (int)$time_settings['delivery_time_starts'];
			  		$each_delivery_slot = (isset($time_settings['each_time_slot']) && !empty($time_settings['each_time_slot'])) ? (int)$time_settings['each_time_slot'] : (int)$time_settings['delivery_time_ends']-(int)$time_settings['delivery_time_starts'];
					while((int)$time_settings['delivery_time_ends']>$x) {
						$second_time = $x+$each_delivery_slot;
						if($second_time > (int)$time_settings['delivery_time_ends']) {
							$second_time = (int)$time_settings['delivery_time_ends'];
						}

						foreach($second_period as $delivery_day) {
				  			$delivery_day = date("Y-m-d", strtotime($delivery_day->format("Y-m-d")));
				  			if(isset($delivery_days_delivery[$delivery_day])) {
								$delivery_days_delivery[$delivery_day] = (int)$delivery_days_delivery[$delivery_day] + (int)$max_order;
							} else {
								$delivery_days_delivery[$delivery_day] = (int)$max_order;
							}
				  		}	
						$x = $second_time;
					}
				} else {
					$need_checking_max_order = false;
				}
 		
		  	}

			if($need_checking_max_order) {
				foreach($second_period as $delivery_day) {
					if(isset($delivery_days_delivery[date("Y-m-d", strtotime($delivery_day->format("Y-m-d")))])) {
						$checking_max_order_args = array(
					        'limit' => -1,
					        'delivery_date' => date("Y-m-d", strtotime($delivery_day->format("Y-m-d"))),
					        'delivery_type' => "delivery",
					        'status' => $order_status,
					        'return' => 'ids'
					    );
					    $orders_array = wc_get_orders( $checking_max_order_args );
					    if(count($orders_array) >= (int)$delivery_days_delivery[date("Y-m-d", strtotime($delivery_day->format("Y-m-d")))]) {
							$disable_for_max_delivery_dates[] = date('Y-m-d', strtotime($delivery_day->format("Y-m-d")));
					    }
					}
				}
			}

			$disable_for_max_delivery_dates = array_unique($disable_for_max_delivery_dates, false);
			$disable_for_max_delivery_dates = array_values($disable_for_max_delivery_dates);

		} 

		if($enable_pickup_date) {
			
			if(isset($pickup_date_settings['maximum_pickup_per_day']) && $pickup_date_settings['maximum_pickup_per_day'] != "") {

			$range_last_date = $formated_obj->modify("+40 day")->format("Y-m-d");
			$filtered_date = $range_first_date . ',' . $range_last_date;
			$filtered_dates = explode(',', $filtered_date);
			$period = new DatePeriod(new DateTime($filtered_dates[0]), new DateInterval('P1D'), new DateTime($filtered_dates[1].' +1 day'));

			$max_pickup_per_day = (isset($pickup_date_settings['maximum_pickup_per_day']) && $pickup_date_settings['maximum_pickup_per_day'] != "") ? (int)$pickup_date_settings['maximum_pickup_per_day'] : 10000000000000;
			foreach ($period as $date) {
				$args = array(
			        'limit' => -1,
			        'pickup_date' => date("Y-m-d", strtotime($date->format("Y-m-d"))),
			        /*'delivery_type' => "pickup",*/
			        'status' => $order_status,
			        'return' => 'ids'
			    );
			    $orders_array = wc_get_orders( $args );
			    if(count($orders_array) >= $max_pickup_per_day) {
					$disable_for_max_pickup_dates[] = date('Y-m-d', strtotime($date->format("Y-m-d")));
			    }
			}

			}

			$custom_pickup_slot_settings = get_option('coderockz_woo_delivery_pickup_slot_settings');
			$enable_custom_pickup_slot = (isset($custom_pickup_slot_settings['enable_custom_pickup_slot']) && !empty($custom_pickup_slot_settings['enable_custom_pickup_slot'])) ? $custom_pickup_slot_settings['enable_custom_pickup_slot'] : false;
			if($enable_custom_pickup_slot) {
				if(isset($custom_pickup_slot_settings['time_slot']) && count($custom_pickup_slot_settings['time_slot'])>0 && count($custom_pickup_slot_settings['time_slot'])<=2){
					$pickup_max_order = true;
					foreach($custom_pickup_slot_settings['time_slot'] as $key => $individual_pickup_slot) {
						if((isset($individual_pickup_slot['max_order']) && $individual_pickup_slot['max_order'] !="")) {

						} else {
							$pickup_max_order = false;
							break;
						}
					}

					if($pickup_max_order) {

						foreach($custom_pickup_slot_settings['time_slot'] as $key => $individual_pickup_slot) {

				  			if($individual_pickup_slot['enable']) {
				  				
				  				if((isset($individual_pickup_slot['max_order']) && $individual_pickup_slot['max_order'] !="")) {

					  				$pickup_hide_date_timeslot = isset($individual_pickup_slot['only_specific_date_close']) && $individual_pickup_slot['only_specific_date_close'] !="" ? sanitize_text_field($individual_pickup_slot['only_specific_date_close']) : "";
					  				$pickup_hide_date_timeslot = explode(",",$pickup_hide_date_timeslot);
					  				$pickup_available_date_timeslot = isset($individual_pickup_slot['only_specific_date']) && $individual_pickup_slot['only_specific_date'] !="" ? sanitize_text_field($individual_pickup_slot['only_specific_date']) : "";
					  				$pickup_available_date_timeslot = explode(",",$pickup_available_date_timeslot);
					  				foreach($second_period as $pickup_day) {
					  					$pickup_day = date("Y-m-d", strtotime($pickup_day->format("Y-m-d")));
					  					if(isset($individual_pickup_slot['only_specific_date']) && $individual_pickup_slot['only_specific_date'] !="" && !in_array($pickup_day,$pickup_available_date_timeslot)) {
					  						continue;
					  					}
					  					if((!empty($pickup_available_date_timeslot) && in_array($pickup_day,$pickup_available_date_timeslot)) || (!in_array($pickup_day,$pickup_hide_date_timeslot) && !in_array(date('w', strtotime($pickup_day)),$individual_pickup_slot['disable_for']))) {
					  						
					  						$max_order = sanitize_text_field($individual_pickup_slot['max_order']);

								  			if($individual_pickup_slot['enable_split']) {
												$times = explode('-', $key);
												$x = $times[0];
												while($times[1]>$x) {
													$second_time = $x+$individual_pickup_slot['split_slot_duration'];
													if($second_time > $times[1]) {
														$second_time = $times[1];
													}
													if(isset($pickup_days_delivery[$pickup_day])) {
														$pickup_days_delivery[$pickup_day] = (int)$pickup_days_delivery[$pickup_day] + (int)$max_order;
													} else {
														$pickup_days_delivery[$pickup_day] = (int)$max_order;
													}
													
													
													$x = $second_time;
												}
											} else {
												if(isset($pickup_days_delivery[$pickup_day])) {
													$pickup_days_delivery[$pickup_day] = (int)$pickup_days_delivery[$pickup_day] + (int)$max_order;
												} else {
													$pickup_days_delivery[$pickup_day] = (int)$max_order;
												}
												
											}

					  					}
					  				}
				  				}
				  			}
				  		}
			  		} else {
  						$pickup_need_checking_max_order = false;
  					}
			  	}
			} else {

				$pickup_settings = get_option('coderockz_woo_delivery_pickup_settings');
			    $enable_pickup_time = (isset($pickup_settings['enable_pickup_time']) && !empty($pickup_settings['enable_pickup_time'])) ? $pickup_settings['enable_pickup_time'] : false;
			    $max_order = (isset($pickup_settings['max_pickup_per_slot']) && $pickup_settings['max_pickup_per_slot'] !="") ? sanitize_text_field($pickup_settings['max_pickup_per_slot']) : "";
			    if($enable_pickup_time && $max_order != "" && (int)$max_order <= 15) {
				    
			  		$x = (int)$pickup_settings['pickup_time_starts'];
			  		$each_pickup_slot = (isset($pickup_settings['each_time_slot']) && !empty($pickup_settings['each_time_slot'])) ? (int)$pickup_settings['each_time_slot'] : (int)$pickup_settings['pickup_time_ends']-(int)$pickup_settings['pickup_time_starts'];
					while((int)$pickup_settings['pickup_time_ends']>$x) {
						$second_time = $x+$each_pickup_slot;
						if($second_time > (int)$pickup_settings['pickup_time_ends']) {
							$second_time = (int)$pickup_settings['pickup_time_ends'];
						}

						foreach($second_period as $pickup_day) {
				  			$pickup_day = date("Y-m-d", strtotime($pickup_day->format("Y-m-d")));
				  			if(isset($pickup_days_delivery[$pickup_day])) {
								$pickup_days_delivery[$pickup_day] = (int)$pickup_days_delivery[$pickup_day] + (int)$max_order;
							} else {
								$pickup_days_delivery[$pickup_day] = (int)$max_order;
							}
				  		}	
						$x = $second_time;
					}
				} else {
					$pickup_need_checking_max_order = false;
				}
 		
		  	}

			if($pickup_need_checking_max_order) {
				foreach($second_period as $pickup_day) {
					if(isset($pickup_days_delivery[date("Y-m-d", strtotime($pickup_day->format("Y-m-d")))])) {
						$pickup_checking_max_order_args = array(
					        'limit' => -1,
					        'pickup_date' => date("Y-m-d", strtotime($pickup_day->format("Y-m-d"))),
					        'pickup_type' => "pickup",
					        'status' => $order_status,
					        'return' => 'ids'
					    );
					    $orders_array = wc_get_orders( $pickup_checking_max_order_args );
					    if(count($orders_array) >= (int)$pickup_days_delivery[date("Y-m-d", strtotime($pickup_day->format("Y-m-d")))]) {
							$disable_for_max_pickup_dates[] = date('Y-m-d', strtotime($pickup_day->format("Y-m-d")));
					    }
					}
				}
			}

			$disable_for_max_pickup_dates = array_unique($disable_for_max_pickup_dates, false);
			$disable_for_max_pickup_dates = array_values($disable_for_max_pickup_dates);
		}

		$disable_delivery_date_passed_time = [];
		$disable_pickup_date_passed_time = [];

		$delivery_time_settings = get_option('coderockz_woo_delivery_time_settings');
		$pickup_time_settings = get_option('coderockz_woo_delivery_pickup_settings');

		$enable_delivery_time = (isset($delivery_time_settings['enable_delivery_time']) && !empty($delivery_time_settings['enable_delivery_time'])) ? $delivery_time_settings['enable_delivery_time'] : false;
	  	
		$enable_pickup_time = (isset($pickup_time_settings['enable_pickup_time']) && !empty($pickup_time_settings['enable_pickup_time'])) ? $pickup_time_settings['enable_pickup_time'] : false;
		
		
		if($enable_delivery_time) {

			$time_slot_end = [0];
			$custom_time_slot_settings = get_option('coderockz_woo_delivery_time_slot_settings');
			$enable_custom_time_slot = (isset($custom_time_slot_settings['enable_custom_time_slot']) && !empty($custom_time_slot_settings['enable_custom_time_slot'])) ? $custom_time_slot_settings['enable_custom_time_slot'] : false;
			if($enable_custom_time_slot) {
				if(isset($custom_time_slot_settings['time_slot']) && count($custom_time_slot_settings['time_slot'])>0){				
					foreach($custom_time_slot_settings['time_slot'] as $key => $individual_time_slot) {

			  			if($individual_time_slot['enable'] && !in_array(date("w"),$individual_time_slot['disable_for'])) {
				  			$key = preg_replace('/-/', ',', $key);

				  			$key_array = explode(",",$key);

						
						    if($individual_time_slot['enable_split']) {
						    	$x = $key_array[0];
								while($key_array[1]>$x) {
									$second_time = $x+$individual_time_slot['split_slot_duration'];
									if($second_time > $key_array[1]) {
										$second_time = $key_array[1];
									}
									if($individual_time_slot['enable_single_splited_slot']) {
										$time_slot_end[] = (int)$x;
									} else {
										$time_slot_end[] = (int)$second_time;
									}
																	
									$x = $second_time;
								}
						    } else {
						    	if($individual_time_slot['enable_single_slot']) {
									$time_slot_end[] = (int)$individual_time_slot['start'];
								} else {
									$time_slot_end[] = (int)$individual_time_slot['end'];
								}
						    }

						}

					}
				}
			} else {

				$time_settings = get_option('coderockz_woo_delivery_time_settings');
				$time_slot_end[] = (int)$time_settings['delivery_time_ends'];												
			}

			$delivery_fee_settings = get_option('coderockz_woo_delivery_fee_settings');

			$enable_conditional_delivery_fee = isset($delivery_fee_settings['enable_conditional_delivery_fee']) && !empty($delivery_fee_settings['enable_conditional_delivery_fee']) ? $delivery_fee_settings['enable_conditional_delivery_fee'] : false;

			if($enable_conditional_delivery_fee && isset($delivery_fee_settings['conditional_delivery_time_ends']) && $delivery_fee_settings['conditional_delivery_time_ends'] !='') {
				$time_slot_end[] = (int)$delivery_fee_settings['conditional_delivery_time_ends'];
			}

			$highest_timeslot_end = max($time_slot_end);
			$current_time = (date("G")*60)+date("i");

			if($current_time>$highest_timeslot_end) {
				$disable_delivery_date_passed_time[] = date('Y-m-d', time());
			}
		}

		if($enable_pickup_time) {

			$pickup_slot_end = [0];
			$custom_pickup_slot_settings = get_option('coderockz_woo_delivery_pickup_slot_settings');
			$enable_custom_pickup_slot = (isset($custom_pickup_slot_settings['enable_custom_pickup_slot']) && !empty($custom_pickup_slot_settings['enable_custom_pickup_slot'])) ? $custom_pickup_slot_settings['enable_custom_pickup_slot'] : false;
			if($enable_custom_pickup_slot) {
				if(isset($custom_pickup_slot_settings['time_slot']) && count($custom_pickup_slot_settings['time_slot'])>0){

					foreach($custom_pickup_slot_settings['time_slot'] as $pickup_key => $individual_pickup_slot) {

			  			if($individual_pickup_slot['enable'] && !in_array(date("w"),$individual_pickup_slot['disable_for'])) {
				  			$pickup_key = preg_replace('/-/', ',', $pickup_key);

				  			$pickup_key_array = explode(",",$pickup_key);
						
						    if($individual_pickup_slot['enable_split']) {
						    	$pickup_x = $pickup_key_array[0];
								while($pickup_key_array[1]>$pickup_x) {
									$pickup_second_time = $pickup_x+$individual_pickup_slot['split_slot_duration'];
									if($pickup_second_time > $pickup_key_array[1]) {
										$pickup_second_time = $pickup_key_array[1];
									}
									if($individual_pickup_slot['enable_single_splited_slot']) {
										$pickup_slot_end[] = (int)$pickup_x;
									} else {
										$pickup_slot_end[] = (int)$pickup_second_time;
									}

									$pickup_x = $pickup_second_time;
								}
						    } else {
						    	if($individual_pickup_slot['enable_single_slot']) {
									$pickup_slot_end[] = (int)$individual_pickup_slot['start'];
								} else {
									$pickup_slot_end[] = (int)$individual_pickup_slot['end'];
								}
						    }

						}
					}
				}
			} else {

		    	$pickup_settings = get_option('coderockz_woo_delivery_pickup_settings');
				$pickup_slot_end[] = (int)$pickup_settings['pickup_time_ends'];
			}

			$highest_pickupslot_end = max($pickup_slot_end);

			$current_time = (date("G")*60)+date("i");

			if($current_time>$highest_pickupslot_end) {
				$disable_pickup_date_passed_time[] = date('Y-m-d', time());
			}

		}

		$response=[
			"disable_for_max_delivery_dates" => $disable_for_max_delivery_dates,
			"disable_for_max_pickup_dates" => $disable_for_max_pickup_dates,
			"disable_delivery_date_passed_time" => $disable_delivery_date_passed_time,
			"disable_pickup_date_passed_time" => $disable_pickup_date_passed_time,
		];
		$response = json_encode($response);
		wp_send_json_success($response);
		
	}

	
	public function coderockz_woo_delivery_add_account_orders_column( $columns ) {
		$delivery_details_text = (isset(get_option('coderockz_woo_delivery_localization_settings')['delivery_details_text']) && !empty(get_option('coderockz_woo_delivery_localization_settings')['delivery_details_text'])) ? stripslashes(get_option('coderockz_woo_delivery_localization_settings')['delivery_details_text']) : __( "Delivery Details", 'coderockz-woo-delivery' ); 

		if(class_exists('CodeRockz_Woocommerce_Delivery_Date_Time')) {
			$columns  = array_splice($columns, 0, 3, true) +
				['order_delivery_details' => $delivery_details_text] +
				array_splice($columns, 1, count($columns) - 1, true);
		}
		
	    return $columns;
	}

	public function coderockz_woo_delivery_show_delivery_details_my_account_tab($order) {
		if(class_exists('CodeRockz_Woocommerce_Delivery_Date_Time')) {
			$delivery_date_settings = get_option('coderockz_woo_delivery_date_settings');			
			$pickup_date_settings = get_option('coderockz_woo_delivery_pickup_date_settings');			
			$delivery_time_settings = get_option('coderockz_woo_delivery_time_settings');
			$pickup_time_settings = get_option('coderockz_woo_delivery_pickup_settings');
			$delivery_pickup_settings = get_option('coderockz_woo_delivery_pickup_location_settings');
			$additional_field_settings = get_option('coderockz_woo_delivery_additional_field_settings');

			$delivery_date_field_label = (isset($delivery_date_settings['field_label']) && !empty($delivery_date_settings['field_label'])) ? stripslashes($delivery_date_settings['field_label']) : __( "Delivery Date", 'coderockz-woo-delivery' );
			$pickup_date_field_label = (isset($pickup_date_settings['pickup_field_label']) && !empty($pickup_date_settings['pickup_field_label'])) ? stripslashes($pickup_date_settings['pickup_field_label']) : __( "Pickup Date", 'coderockz-woo-delivery' );
			$delivery_time_field_label = (isset($delivery_time_settings['field_label']) && !empty($delivery_time_settings['field_label'])) ? stripslashes($delivery_time_settings['field_label']) : __( "Delivery Time", 'coderockz-woo-delivery' );
			$pickup_time_field_label = (isset($pickup_time_settings['field_label']) && !empty($pickup_time_settings['field_label'])) ? stripslashes($pickup_time_settings['field_label']) : __( "Pickup Time", 'coderockz-woo-delivery' );
			$pickup_location_field_label = (isset($delivery_pickup_settings['field_label']) && !empty($delivery_pickup_settings['field_label'])) ? stripslashes($delivery_pickup_settings['field_label']) : __( "Pickup Location", 'coderockz-woo-delivery' );
			$additional_field_field_label = (isset($additional_field_settings['field_label']) && !empty($additional_field_settings['field_label'])) ? stripslashes($additional_field_settings['field_label']) : __( "Special Note About Delivery", 'coderockz-woo-delivery' );


			// if any timezone data is saved, set default timezone with the data
			$timezone = $this->helper->get_the_timezone();
			date_default_timezone_set($timezone);

			$delivery_date_format = (isset($delivery_date_settings['date_format']) && !empty($delivery_date_settings['date_format'])) ? $delivery_date_settings['date_format'] : "F j, Y";
			$add_weekday_name = (isset($delivery_date_settings['add_weekday_name']) && !empty($delivery_date_settings['add_weekday_name'])) ? $delivery_date_settings['add_weekday_name'] : false;

			if($add_weekday_name) {
				$delivery_date_format = "l ".$delivery_date_format;
			}

			$pickup_date_format = (isset($pickup_date_settings['date_format']) && !empty($pickup_date_settings['date_format'])) ? $pickup_date_settings['date_format'] : "F j, Y";

			$pickup_add_weekday_name = (isset($pickup_date_settings['add_weekday_name']) && !empty($pickup_date_settings['add_weekday_name'])) ? $pickup_date_settings['add_weekday_name'] : false;

			if($pickup_add_weekday_name) {
				$pickup_date_format = "l ".$pickup_date_format;
			}

			$time_format = (isset($delivery_time_settings['time_format']) && !empty($delivery_time_settings['time_format']))?$delivery_time_settings['time_format']:"12";
			if($time_format == 12) {
				$time_format = "h:i A";
			} elseif ($time_format == 24) {
				$time_format = "H:i";
			}
			
			$my_account_column = "";
			if(metadata_exists('post', $order->get_id(), 'delivery_date') && get_post_meta($order->get_id(), 'delivery_date', true) !="") {

				$delivery_date = $this->helper->weekday_conversion_to_locale($this->helper->date_conversion_to_locale(date($delivery_date_format, strtotime(get_post_meta( $order->get_id(), 'delivery_date', true ))),"delivery"),"delivery");

				$my_account_column .= $delivery_date_field_label.": " . $delivery_date;
				$my_account_column .= "<br>";
			}

			if(metadata_exists('post', $order->get_id(), 'delivery_time') && get_post_meta($order->get_id(), 'delivery_time', true) !="") {

				if(get_post_meta($order->get_id(),"delivery_time",true) == "as-soon-as-possible") {
					$as_soon_as_possible_text = (isset($delivery_time_settings['as_soon_as_possible_text']) && !empty($delivery_time_settings['as_soon_as_possible_text'])) ? stripslashes($delivery_time_settings['as_soon_as_possible_text']) : "As Soon As Possible";
					$my_account_column .= $delivery_time_field_label.": " . $as_soon_as_possible_text;
					$my_account_column .= "<br>";
				} else {
					$minutes = get_post_meta($order->get_id(),"delivery_time",true);

					$minutes = explode(' - ', $minutes);
		    		if(!isset($minutes[1])) {
		    			$time_value = date($time_format, strtotime($minutes[0]));
		    		} else {
		    			$time_value = date($time_format, strtotime($minutes[0])) . ' - ' . date($time_format, strtotime($minutes[1]));
		    		}

					$my_account_column .= $delivery_time_field_label.": " . $time_value;
					$my_account_column .= "<br>";
				}
			}

			if(metadata_exists('post', $order->get_id(), 'pickup_date') && get_post_meta($order->get_id(), 'pickup_date', true) !="") {
				$pickup_date = $this->helper->weekday_conversion_to_locale($this->helper->date_conversion_to_locale(date($pickup_date_format, strtotime(get_post_meta( $order->get_id(), 'pickup_date', true ))),"pickup"),"pickup");
				$my_account_column .= $pickup_date_field_label.": " . $pickup_date;
				$my_account_column .= "<br>";
			}

			if(metadata_exists('post', $order->get_id(), 'pickup_time') && get_post_meta($order->get_id(), 'pickup_time', true) !="") {
				$pickup_minutes = get_post_meta($order->get_id(),"pickup_time",true);
				$pickup_time_format = (isset($pickup_time_settings['time_format']) && !empty($pickup_time_settings['time_format']))?$pickup_time_settings['time_format']:"12";
				if($pickup_time_format == 12) {
					$pickup_time_format = "h:i A";
				} elseif ($pickup_time_format == 24) {
					$pickup_time_format = "H:i";
				}
				$pickup_minutes = explode(' - ', $pickup_minutes);
	    		if(!isset($pickup_minutes[1])) {
	    			$pickup_time_value = date($pickup_time_format, strtotime($pickup_minutes[0]));
	    		} else {
	    			$pickup_time_value = date($pickup_time_format, strtotime($pickup_minutes[0])) . ' - ' . date($pickup_time_format, strtotime($pickup_minutes[1]));
	    		}

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

			echo $my_account_column;
		}
	}

	public function coderockz_woo_delivery_add_delivery_information_row( $total_rows, $order ) {
 
		$delivery_date_settings = get_option('coderockz_woo_delivery_date_settings');			
		$pickup_date_settings = get_option('coderockz_woo_delivery_pickup_date_settings');			
		$delivery_time_settings = get_option('coderockz_woo_delivery_time_settings');
		$pickup_time_settings = get_option('coderockz_woo_delivery_pickup_settings');
		$delivery_pickup_settings = get_option('coderockz_woo_delivery_pickup_location_settings');
		$additional_field_settings = get_option('coderockz_woo_delivery_additional_field_settings');

		$delivery_date_field_label = (isset($delivery_date_settings['field_label']) && !empty($delivery_date_settings['field_label'])) ? stripslashes($delivery_date_settings['field_label']) : __( "Delivery Date", 'coderockz-woo-delivery' );
		$pickup_date_field_label = (isset($pickup_date_settings['pickup_field_label']) && !empty($pickup_date_settings['pickup_field_label'])) ? stripslashes($pickup_date_settings['pickup_field_label']) : __( "Pickup Date", 'coderockz-woo-delivery' );
		$delivery_time_field_label = (isset($delivery_time_settings['field_label']) && !empty($delivery_time_settings['field_label'])) ? stripslashes($delivery_time_settings['field_label']) : __( "Delivery Time", 'coderockz-woo-delivery' );
		$pickup_time_field_label = (isset($pickup_time_settings['field_label']) && !empty($pickup_time_settings['field_label'])) ? stripslashes($pickup_time_settings['field_label']) : __( "Pickup Time", 'coderockz-woo-delivery' );
		$pickup_location_field_label = (isset($delivery_pickup_settings['field_label']) && !empty($delivery_pickup_settings['field_label'])) ? stripslashes($delivery_pickup_settings['field_label']) : __( "Pickup Location", 'coderockz-woo-delivery' );
		$additional_field_field_label = (isset($additional_field_settings['field_label']) && !empty($additional_field_settings['field_label'])) ? stripslashes($additional_field_settings['field_label']) : __( "Special Note About Delivery", 'coderockz-woo-delivery' );

		// if any timezone data is saved, set default timezone with the data
		$timezone = $this->helper->get_the_timezone();
		date_default_timezone_set($timezone);

		$delivery_date_format = (isset($delivery_date_settings['date_format']) && !empty($delivery_date_settings['date_format'])) ? $delivery_date_settings['date_format'] : "F j, Y";

		$add_weekday_name = (isset($delivery_date_settings['add_weekday_name']) && !empty($delivery_date_settings['add_weekday_name'])) ? $delivery_date_settings['add_weekday_name'] : false;

		if($add_weekday_name) {
			$delivery_date_format = "l ".$delivery_date_format;
		}

		$pickup_date_format = (isset($pickup_date_settings['date_format']) && !empty($pickup_date_settings['date_format'])) ? $pickup_date_settings['date_format'] : "F j, Y";

		$pickup_add_weekday_name = (isset($pickup_date_settings['add_weekday_name']) && !empty($pickup_date_settings['add_weekday_name'])) ? $pickup_date_settings['add_weekday_name'] : false;

		if($pickup_add_weekday_name) {
			$pickup_date_format = "l ".$pickup_date_format;
		}

		$time_format = (isset($delivery_time_settings['time_format']) && !empty($delivery_time_settings['time_format']))?$delivery_time_settings['time_format']:"12";
		if($time_format == 12) {
			$time_format = "h:i A";
		} elseif ($time_format == 24) {
			$time_format = "H:i";
		}

		$pickup_time_format = (isset($pickup_time_settings['time_format']) && !empty($pickup_time_settings['time_format']))?$pickup_time_settings['time_format']:"12";
		if($pickup_time_format == 12) {
			$pickup_time_format = "h:i A";
		} elseif ($pickup_time_format == 24) {
			$pickup_time_format = "H:i";
		}

		if( version_compare( get_option( 'woocommerce_version' ), '3.0.0', ">=" ) ) {            
	        $order_id = $order->get_id();
	    } else {
	        $order_id = $order->id;
	    }

	    $delivery_option_settings = get_option('coderockz_woo_delivery_option_delivery_settings');
	    /*$order_type_field_label = (isset($delivery_option_settings['delivery_option_label']) && !empty($delivery_option_settings['delivery_option_label'])) ? stripslashes($delivery_option_settings['delivery_option_label']) : __( "Order Type", 'coderockz-woo-delivery' );
	    $delivery_field_label = (isset($delivery_option_settings['delivery_label']) && !empty($delivery_option_settings['delivery_label'])) ? stripslashes($delivery_option_settings['delivery_label']) : __( "Delivery", 'coderockz-woo-delivery' );
		$pickup_field_label = (isset($delivery_option_settings['pickup_label']) && !empty($delivery_option_settings['pickup_label'])) ? stripslashes($delivery_option_settings['pickup_label']) : __( "Pickup", 'coderockz-woo-delivery' );
	    if(metadata_exists('post', $order_id, 'delivery_type') && get_post_meta($order_id, 'delivery_type', true) !="") {

			if(get_post_meta($order_id, 'delivery_type', true) == "delivery") {
				$total_rows['delivery_type'] = array(
				   'label' => __($order_type_field_label, 'coderockz-woo-delivery'),
				   'value'   => __($delivery_field_label, 'coderockz-woo-delivery')
				);
			} elseif(get_post_meta($order_id, 'delivery_type', true) == "pickup") {
				$total_rows['delivery_type'] = array(
				   'label' => __($order_type_field_label, 'coderockz-woo-delivery'),
				   'value'   => __($pickup_field_label, 'coderockz-woo-delivery')
				);
			}

	    }*/
	    
	    if(metadata_exists('post', $order_id, 'delivery_date') && get_post_meta( $order_id, 'delivery_date', true ) != "") {

	    	$delivery_date = $this->helper->weekday_conversion_to_locale($this->helper->date_conversion_to_locale(date($delivery_date_format, strtotime(get_post_meta( $order_id, 'delivery_date', true ))),"delivery"),"delivery");

	    	$total_rows['delivery_date'] = array(
			   'label' => __($delivery_date_field_label, 'coderockz-woo-delivery'),
			   'value'   => $delivery_date
			);
	    }
		
	    if(metadata_exists('post', $order_id, 'delivery_time') && get_post_meta($order_id,"delivery_time",true) != "") {
	    	if(get_post_meta($order_id,"delivery_time",true) == "as-soon-as-possible") {
	    		$as_soon_as_possible_text = (isset($delivery_time_settings['as_soon_as_possible_text']) && !empty($delivery_time_settings['as_soon_as_possible_text'])) ? stripslashes($delivery_time_settings['as_soon_as_possible_text']) : __( "As Soon As Possible", 'coderockz-woo-delivery' );
	    		$time_value = $as_soon_as_possible_text;
	    	} else {
				$minutes = get_post_meta($order_id,"delivery_time",true);
				$minutes = explode(' - ', $minutes);

	    		if(!isset($minutes[1])) {
	    			$time_value = date($time_format, strtotime($minutes[0]));
	    		} else {

	    			$time_value = date($time_format, strtotime($minutes[0])) . ' - ' . date($time_format, strtotime($minutes[1]));

	    		}

    		}

			$total_rows['delivery_time'] = array(
			   'label' => __($delivery_time_field_label, 'coderockz-woo-delivery'),
			   'value'   => $time_value
			);
		}

		if(metadata_exists('post', $order_id, 'pickup_date') && get_post_meta( $order_id, 'pickup_date', true ) != "") {

			$pickup_date = $this->helper->weekday_conversion_to_locale($this->helper->date_conversion_to_locale(date($pickup_date_format, strtotime(get_post_meta( $order_id, 'pickup_date', true ))),"pickup"),"pickup");

	    	$total_rows['pickup_date'] = array(
			   'label' => __($pickup_date_field_label, 'coderockz-woo-delivery'),
			   'value'   => $pickup_date
			);
	    }

		if(metadata_exists('post', $order_id, 'pickup_time') && get_post_meta($order_id,"pickup_time",true) != "") {
			$pickup_minutes = get_post_meta($order_id,"pickup_time",true);
			$pickup_minutes = explode(' - ', $pickup_minutes);

    		if(!isset($pickup_minutes[1])) {
    			$pickup_time_value = date($pickup_time_format, strtotime($pickup_minutes[0]));
    		} else {
    			$pickup_time_value = date($pickup_time_format, strtotime($pickup_minutes[0])) . ' - ' . date($pickup_time_format, strtotime($pickup_minutes[1]));
    		}

			$total_rows['pickup_time'] = array(
			   'label' => __($pickup_time_field_label, 'coderockz-woo-delivery'),
			   'value'   => $pickup_time_value
			);
		}

		if(metadata_exists('post', $order_id, 'delivery_pickup') && get_post_meta($order_id, 'delivery_pickup', true) !="") {
			$total_rows['delivery_pickup'] = array(
			   'label' => __($pickup_location_field_label, 'coderockz-woo-delivery'),
			   'value'   => get_post_meta($order_id, 'delivery_pickup', true)
			);
		}

		if(metadata_exists('post', $order_id, 'additional_note') && get_post_meta($order_id, 'additional_note', true) !="") {
			$total_rows['additional_note'] = array(
			   'label' => __($additional_field_field_label, 'coderockz-woo-delivery'),
			   'value'   => get_post_meta($order_id, 'additional_note', true)
			);
		}

		/*if(isset($_COOKIE['coderockz_woo_delivery_option_time_pickup'])) {
		  $delivery_option_session = $_COOKIE['coderockz_woo_delivery_option_time_pickup'];
		} elseif(!is_null(WC()->session)) {
		  $delivery_option_session = WC()->session->get( 'coderockz_woo_delivery_option_time_pickup' );
		}
	    

		$delivery_option_settings = get_option('coderockz_woo_delivery_option_delivery_settings');
		$enable_delivery_option = (isset($delivery_option_settings['enable_option_time_pickup']) && !empty($delivery_option_settings['enable_option_time_pickup'])) ? $delivery_option_settings['enable_option_time_pickup'] : false;

		$enable_pickup_time = (isset($pickup_time_settings['enable_pickup_time']) && !empty($pickup_time_settings['enable_pickup_time'])) ? $pickup_time_settings['enable_pickup_time'] : false;

		$enable_delivery_time = (isset($delivery_time_settings['enable_delivery_time']) && !empty($delivery_time_settings['enable_delivery_time'])) ? $delivery_time_settings['enable_delivery_time'] : false;

		if(!is_null(WC()->session)) {
			$chosen_shipping_method = WC()->session->get('chosen_shipping_methods');
		} else {
			$chosen_shipping_method = [];
			$chosen_shipping_method[] = '';
		}

	    if(((isset($delivery_option_session) && $delivery_option_session == "pickup" && (strpos($chosen_shipping_method[0], 'local_pickup') === false && $order->get_shipping_total() == 0)) || (!isset($delivery_option_session) && !$enable_delivery_option && !$enable_delivery_time && (strpos($chosen_shipping_method[0], 'local_pickup') === false && $order->get_shipping_total() == 0)) || ($enable_pickup_time && !$enable_delivery_time && (strpos($chosen_shipping_method[0], 'local_pickup') === false && $order->get_shipping_total() == 0)))) {
	    	unset($total_rows['shipping']);
	    }*/
		 
		return $total_rows;
	}

	public function coderockz_woo_delivery_add_custom_fee ( $cart ) {
		if ( ! $_POST || ( is_admin() && ! is_ajax() ) ) {
	        return;
	    }

	    if(isset($_COOKIE['coderockz_woo_delivery_option_time_pickup'])) {
		  $delivery_option_session = $_COOKIE['coderockz_woo_delivery_option_time_pickup'];
		} elseif(!is_null(WC()->session)) {
		  $delivery_option_session = WC()->session->get( 'coderockz_woo_delivery_option_time_pickup' );
		}

        $selected_delivery_date = $this->helper->weekday_conversion($this->helper->date_conversion(sanitize_text_field(WC()->session->get( 'selected_delivery_date' )),"delivery"),"delivery");
        $selected_delivery_time = WC()->session->get( 'selected_delivery_time' );
               
        $selected_pickup_time = WC()->session->get( 'selected_pickup_time' );

        $selected_order_type = WC()->session->get( 'selected_order_type' );
	    $delivery_time_settings = get_option('coderockz_woo_delivery_time_settings');

		// if any timezone data is saved, set default timezone with the data
		$timezone = $this->helper->get_the_timezone();
		date_default_timezone_set($timezone);

		$other_settings = get_option('coderockz_woo_delivery_other_settings');
		$add_tax_delivery_pickup_fee = (isset($other_settings['add_tax_delivery_pickup_fee']) && !empty($other_settings['add_tax_delivery_pickup_fee'])) ? $other_settings['add_tax_delivery_pickup_fee'] : false;

	    $fees_settings = get_option('coderockz_woo_delivery_fee_settings');

		$custom_time_slot_settings = get_option('coderockz_woo_delivery_time_slot_settings');
		$enable_custom_time_slot = (isset($custom_time_slot_settings['enable_custom_time_slot']) && !empty($custom_time_slot_settings['enable_custom_time_slot'])) ? $custom_time_slot_settings['enable_custom_time_slot'] : false;

		$delivery_option_settings = get_option('coderockz_woo_delivery_option_delivery_settings');
		$enable_delivery_option = (isset($delivery_option_settings['enable_option_time_pickup']) && !empty($delivery_option_settings['enable_option_time_pickup'])) ? $delivery_option_settings['enable_option_time_pickup'] : false;

		$pickup_time_settings = get_option('coderockz_woo_delivery_pickup_settings');
		$enable_pickup_time = (isset($pickup_time_settings['enable_pickup_time']) && !empty($pickup_time_settings['enable_pickup_time'])) ? $pickup_time_settings['enable_pickup_time'] : false;

		$delivery_date_settings = get_option('coderockz_woo_delivery_date_settings');
		$enable_delivery_date = (isset($delivery_date_settings['enable_delivery_date']) && !empty($delivery_date_settings['enable_delivery_date'])) ? $delivery_date_settings['enable_delivery_date'] : false;

		$delivery_time_settings = get_option('coderockz_woo_delivery_time_settings');
		$enable_delivery_time = (isset($delivery_time_settings['enable_delivery_time']) && !empty($delivery_time_settings['enable_delivery_time'])) ? $delivery_time_settings['enable_delivery_time'] : false;



		$localization_settings = get_option('coderockz_woo_delivery_localization_settings');
		$delivery_fee_text = (isset($localization_settings['delivery_fee_text']) && !empty($localization_settings['delivery_fee_text'])) ? stripslashes($localization_settings['delivery_fee_text']) : __( "Delivery Time Slot Fee", 'coderockz-woo-delivery' );
		$pickup_fee_text = (isset($localization_settings['pickup_fee_text']) && !empty($localization_settings['pickup_fee_text'])) ? stripslashes($localization_settings['pickup_fee_text']) : __( "Pickup Slot Fee", 'coderockz-woo-delivery' );
		$sameday_fee_text = (isset($localization_settings['sameday_fee_text']) && !empty($localization_settings['sameday_fee_text'])) ? stripslashes($localization_settings['sameday_fee_text']) : __( "Same Day Delivery Fee", 'coderockz-woo-delivery' );
		$nextday_fee_text = (isset($localization_settings['nextday_fee_text']) && !empty($localization_settings['nextday_fee_text'])) ? stripslashes($localization_settings['nextday_fee_text']) : __( "Next Day Delivery Fee", 'coderockz-woo-delivery' );
		$day_after_tomorrow_fee_text = (isset($localization_settings['day_after_tomorrow_fee_text']) && !empty($localization_settings['day_after_tomorrow_fee_text'])) ? stripslashes($localization_settings['day_after_tomorrow_fee_text']) : __( "Day After Tomorrow Delivery Fee", 'coderockz-woo-delivery' );
		$other_fee_text = (isset($localization_settings['other_fee_text']) && !empty($localization_settings['other_fee_text'])) ? stripslashes($localization_settings['other_fee_text']) : __( "Other Day Delivery Fee", 'coderockz-woo-delivery' );
		$weekday_fee_text = (isset($localization_settings['weekday_fee_text']) && !empty($localization_settings['weekday_fee_text'])) ? stripslashes($localization_settings['weekday_fee_text']) : __( "Weekday Delivery Fee", 'coderockz-woo-delivery' );


		if(((isset($delivery_option_session) && $delivery_option_session == "delivery" && $enable_delivery_option && $enable_delivery_time && $selected_order_type != "") || (!$enable_delivery_option && $enable_delivery_time/* && !$enable_pickup_time*/)) && is_checkout()) {
			if($selected_delivery_time != "" && $selected_delivery_time != "conditional-delivery"  && $selected_delivery_time != "as-soon-as-possible") {
	        	if(strpos($selected_delivery_time, ' - ') !== false) {
	        		$selected_delivery_time = explode(' - ', $selected_delivery_time);
					$inserted_data_key_array_one = explode(':', $selected_delivery_time[0]);
					$inserted_data_key_array_two = explode(':', $selected_delivery_time[1]);
					$selected_delivery_time = ((int)$inserted_data_key_array_one[0]*60+(int)$inserted_data_key_array_one[1]).' - '.((int)$inserted_data_key_array_two[0]*60+(int)$inserted_data_key_array_two[1]);
					$inserted_data_key_array = explode(" - ",$selected_delivery_time);
	        	} else {
	        		$inserted_data_key_array = [];
	        		$inserted_data_key_array_one = explode(':', $selected_delivery_time);
	        		$selected_delivery_time = ((int)$inserted_data_key_array_one[0]*60+(int)$inserted_data_key_array_one[1]);
	        		$inserted_data_key_array[] = $selected_delivery_time;
	        	}
	    		
			}

			if ( $selected_delivery_time != "conditional-delivery" ) {
				if($enable_custom_time_slot) {
					if(isset($custom_time_slot_settings['time_slot']) && count($custom_time_slot_settings['time_slot'])>0){


				  		foreach($custom_time_slot_settings['time_slot'] as $key => $individual_time_slot) {
				  			if($individual_time_slot['enable']) {
					  			$key = preg_replace('/-/', ' - ', $key);

					  			$key_array = explode(" - ",$key);
					    		
						    	if(!empty($selected_delivery_time) && ((isset($inserted_data_key_array[1]) && $selected_delivery_time == $key) || ($individual_time_slot['enable_split'] && $individual_time_slot['split_slot_duration']!="" && isset($inserted_data_key_array[1]) && $inserted_data_key_array[0]>= $key_array[0] && $inserted_data_key_array[1] <= $key_array[1]))) {

						    		if($individual_time_slot["fee"] !="") {
						    			if(class_exists('WOOCS_STARTER')){
		
											$individual_fee = apply_filters('woocs_exchange_value', $individual_time_slot["fee"]);
										} else {
											$individual_fee = $individual_time_slot["fee"];
										}
						    			$cart->add_fee( __( $delivery_fee_text, 'coderockz-woo-delivery' ) , $individual_fee, $add_tax_delivery_pickup_fee );
						    		}
							    } elseif(!empty($selected_delivery_time) && ((!isset($inserted_data_key_array[1]) && $selected_delivery_time == $key_array[0] && $inserted_data_key_array[0] < $key_array[1]) || ($individual_time_slot['enable_split'] && $individual_time_slot['split_slot_duration']!="" && !isset($inserted_data_key_array[1]) && $inserted_data_key_array[0]>= $key_array[0] && $inserted_data_key_array[0] < $key_array[1]))) {

							    	if($individual_time_slot["fee"] !="") {
							    		if(class_exists('WOOCS_STARTER')){
		
											$individual_fee = apply_filters('woocs_exchange_value', $individual_time_slot["fee"]);
										} else {
											$individual_fee = $individual_time_slot["fee"];
										}
						    			$cart->add_fee( __( $delivery_fee_text, 'coderockz-woo-delivery' ) , $individual_fee, $add_tax_delivery_pickup_fee );
						    		}
							    }
							}
				  		}
				  	}
				} else {
					if(isset($fees_settings['enable_time_slot_fee']) && $fees_settings['enable_time_slot_fee'] && isset($selected_delivery_time))
					{
				    	foreach($fees_settings['time_slot_fee'] as $key => $slot_fee)
				    	{
				    		
				    		$key = preg_replace('/-/', ' - ', $key);

				    		if(!empty($selected_delivery_time) && $selected_delivery_time == $key) {
				    			if(class_exists('WOOCS_STARTER')){	
									$individual_slot_fee = apply_filters('woocs_exchange_value', $slot_fee);
								} else {
									$individual_slot_fee = $slot_fee;
								}
						    	$cart->add_fee( __( $delivery_fee_text, 'coderockz-woo-delivery' ) , $individual_slot_fee, $add_tax_delivery_pickup_fee );
						    }

				    	}
					}
				}
			} elseif ( $selected_delivery_time == "conditional-delivery") {
				$today = date('Y-m-d', time());
				
				if(date("Y-m-d", strtotime($selected_delivery_date)) == $today || (!$enable_delivery_date && $enable_delivery_time)) {
					$conditional_delivery_fee = isset($fees_settings['conditional_delivery_fee']) && !empty($fees_settings['conditional_delivery_fee']) ? esc_attr($fees_settings['conditional_delivery_fee']) : 0;
					$conditional_fee_text = (isset($localization_settings['conditional_fee_text']) && !empty($localization_settings['conditional_fee_text'])) ? stripslashes($localization_settings['conditional_fee_text']) : __( "Conditional Delivery Fee", 'coderockz-woo-delivery' );
					if(isset($conditional_delivery_fee) && $conditional_delivery_fee != 0) {
				    	$cart->add_fee( __( $conditional_fee_text, 'coderockz-woo-delivery' ) , $conditional_delivery_fee, $add_tax_delivery_pickup_fee ); 
					}
				}
			}

		}

		$custom_pickup_slot_settings = get_option('coderockz_woo_delivery_pickup_slot_settings');
		$enable_custom_pickup_slot = (isset($custom_pickup_slot_settings['enable_custom_pickup_slot']) && !empty($custom_pickup_slot_settings['enable_custom_pickup_slot'])) ? $custom_pickup_slot_settings['enable_custom_pickup_slot'] : false;


		if(((isset($delivery_option_session) && $delivery_option_session == "pickup" && $enable_delivery_option && $enable_pickup_time && $selected_order_type != "") || (!$enable_delivery_option && /*!$enable_delivery_time && */$enable_pickup_time)) && is_checkout()) {
			if($selected_pickup_time != "") {
	        	if(strpos($selected_pickup_time, ' - ') !== false) {
	        		$selected_pickup_time = explode(' - ', $selected_pickup_time);
					$inserted_data_key_array_one = explode(':', $selected_pickup_time[0]);
					$inserted_data_key_array_two = explode(':', $selected_pickup_time[1]);
					$selected_pickup_time = ((int)$inserted_data_key_array_one[0]*60+(int)$inserted_data_key_array_one[1]).' - '.((int)$inserted_data_key_array_two[0]*60+(int)$inserted_data_key_array_two[1]);
					$inserted_data_key_array = explode(" - ",$selected_pickup_time);
	        	} else {
	        		$inserted_data_key_array = [];
	        		$inserted_data_key_array_one = explode(':', $selected_pickup_time);
	        		$selected_pickup_time = ((int)$inserted_data_key_array_one[0]*60+(int)$inserted_data_key_array_one[1]);
	        		$inserted_data_key_array[] = $selected_pickup_time;
	        	}
	    		
			}
			if($enable_custom_pickup_slot) {
				if(isset($custom_pickup_slot_settings['time_slot']) && count($custom_pickup_slot_settings['time_slot'])>0){


			  		foreach($custom_pickup_slot_settings['time_slot'] as $key => $individual_time_slot) {
			  			if($individual_time_slot['enable']) {
				  			$key = preg_replace('/-/', ' - ', $key);

				  			$key_array = explode(" - ",$key);
				    		
						    if(!empty($selected_pickup_time) && ((isset($inserted_data_key_array[1]) && $selected_pickup_time == $key) || ($individual_time_slot['enable_split'] && $individual_time_slot['split_slot_duration']!="" && isset($inserted_data_key_array[1]) && $inserted_data_key_array[0]>= $key_array[0] && $inserted_data_key_array[1] <= $key_array[1]))) {

					    		if($individual_time_slot["fee"] !="") {
					    			if(class_exists('WOOCS_STARTER')){
	
										$individual_fee = apply_filters('woocs_exchange_value', $individual_time_slot["fee"]);
									} else {
										$individual_fee = $individual_time_slot["fee"];
									}
					    			$cart->add_fee( __( $pickup_fee_text, 'coderockz-woo-delivery' ) , $individual_fee, $add_tax_delivery_pickup_fee );
					    		}
						    } elseif(!empty($selected_pickup_time) && ((!isset($inserted_data_key_array[1]) && $selected_pickup_time == $key_array[0] && $inserted_data_key_array[0] < $key_array[1]) || ($individual_time_slot['enable_split'] && $individual_time_slot['split_slot_duration']!="" && !isset($inserted_data_key_array[1]) && $inserted_data_key_array[0]>= $key_array[0] && $inserted_data_key_array[0] < $key_array[1]))) {
						    	
						    	if($individual_time_slot["fee"] !="") {
						    		if(class_exists('WOOCS_STARTER')){
	
										$individual_fee = apply_filters('woocs_exchange_value', $individual_time_slot["fee"]);
									} else {
										$individual_fee = $individual_time_slot["fee"];
									}
					    			$cart->add_fee( __( $pickup_fee_text, 'coderockz-woo-delivery' ) , $individual_fee, $add_tax_delivery_pickup_fee );
					    		}
						    }


						}
			  		}
			  	}
			} else {
				if(isset($fees_settings['enable_pickup_slot_fee']) && $fees_settings['enable_pickup_slot_fee'] && isset($selected_pickup_time))
				{
			    	foreach($fees_settings['pickup_slot_fee'] as $key => $slot_fee)
			    	{
			    		$key = preg_replace('/-/', ' - ', $key);
				    	if(!empty($selected_pickup_time) && $selected_pickup_time == $key) {
				    		if(class_exists('WOOCS_STARTER')){	
								$individual_slot_fee = apply_filters('woocs_exchange_value', $slot_fee);
							} else {
								$individual_slot_fee = $slot_fee;
							}
					    	$cart->add_fee( __( $pickup_fee_text, 'coderockz-woo-delivery' ) , $individual_slot_fee, $add_tax_delivery_pickup_fee );
					    }
			    	}
				}
			}

		}
		
		if(((isset($delivery_option_session) && $delivery_option_session == "delivery" && $selected_order_type != "") || (!$enable_delivery_option && $enable_delivery_date)) && is_checkout()) {

			if (isset($fees_settings['enable_delivery_date_fee']) && $fees_settings['enable_delivery_date_fee'] && isset($selected_delivery_date) && !empty($selected_delivery_date))
			{
				$today = date('Y-m-d', time());
				$today_dt = new DateTime($today);
				$tomorrow = $today_dt->modify('+1 day')->format("Y-m-d");
				$today_dt = new DateTime($today);
				$day_after_tomorrow = $today_dt->modify('+2 day')->format("Y-m-d");

				if(date("Y-m-d", strtotime($selected_delivery_date)) == $today)
				{
					if(isset($fees_settings['same_day_fee']))
					{
		    			if(class_exists('WOOCS_STARTER')){	
							$fee = apply_filters('woocs_exchange_value', $fees_settings['same_day_fee']);
						} else {
							$fee = $fees_settings['same_day_fee'];
						}

		    			$day = $sameday_fee_text;
					}
				}
				elseif(date("Y-m-d", strtotime($selected_delivery_date)) == $tomorrow)
				{
					if(isset($fees_settings['next_day_fee']))
					{
		    			if(class_exists('WOOCS_STARTER')){	
							$fee = apply_filters('woocs_exchange_value', $fees_settings['next_day_fee']);
						} else {
							$fee = $fees_settings['next_day_fee'];
						}

		    			$day = $nextday_fee_text;
					}
				}
				elseif(date("Y-m-d", strtotime($selected_delivery_date)) == $day_after_tomorrow)
				{
					if(isset($fees_settings['day_after_tomorrow_fee']))
					{
		    			if(class_exists('WOOCS_STARTER')){	
							$fee = apply_filters('woocs_exchange_value', $fees_settings['day_after_tomorrow_fee']);
						} else {
							$fee = $fees_settings['day_after_tomorrow_fee'];
						}

		    			$day = $day_after_tomorrow_fee_text;
					}
				}
				else
				{
					if(isset($fees_settings['other_days_fee']))
					{
		    			if(class_exists('WOOCS_STARTER')){	
							$fee = apply_filters('woocs_exchange_value', $fees_settings['other_days_fee']);
						} else {
							$fee = $fees_settings['other_days_fee'];
						}

		    			$day = $other_fee_text;
					}
				}
				if(isset($fee) && $fee != 0)
				{
			    	$cart->add_fee( __( $day, 'coderockz-woo-delivery' ) , $fee, $add_tax_delivery_pickup_fee ); 
				}
			}

			if (isset($fees_settings['enable_weekday_wise_delivery_fee']) && $fees_settings['enable_weekday_wise_delivery_fee'] && isset($selected_delivery_date) && !empty($selected_delivery_date))
			{	
				$current_week_day = date("w",strtotime($selected_delivery_date));			
				
				if(class_exists('WOOCS_STARTER')){
					$week_day_fee = (isset($fees_settings['weekday_wise_delivery_fee'][$current_week_day]) && $fees_settings['weekday_wise_delivery_fee'][$current_week_day] != "") ? apply_filters('woocs_exchange_value', $fees_settings['weekday_wise_delivery_fee'][$current_week_day]) : "";	
				} else {
					$week_day_fee = (isset($fees_settings['weekday_wise_delivery_fee'][$current_week_day]) && $fees_settings['weekday_wise_delivery_fee'][$current_week_day] != "") ? $fees_settings['weekday_wise_delivery_fee'][$current_week_day] : "";
				}

				if( $week_day_fee != "" && $week_day_fee != 0 )
				{
			    	$cart->add_fee( __( $weekday_fee_text, 'coderockz-woo-delivery' ) , $week_day_fee, $add_tax_delivery_pickup_fee ); 
				}
			}

		}

	}

	public function coderockz_checkout_delivery_date_time_set_session( $posted_data ) {
	    parse_str( $posted_data, $output );
	    if ( isset( $output['coderockz_woo_delivery_date_field'] ) ){
	        WC()->session->set( 'selected_delivery_date', $output['coderockz_woo_delivery_date_field'] );
	    }

	    if ( isset( $output['coderockz_woo_delivery_pickup_date_field'] ) ){
	        WC()->session->set( 'selected_pickup_date', $output['coderockz_woo_delivery_pickup_date_field'] );
	    }

	    if ( isset( $output['coderockz_woo_delivery_time_field'] ) && ($output['coderockz_woo_delivery_time_field'] != "as-soon-as-possible" || $output['coderockz_woo_delivery_time_field'] != "conditional-delivery")) {
	        WC()->session->set( 'selected_delivery_time', $output['coderockz_woo_delivery_time_field'] );
	    } else {
	    	WC()->session->set( 'selected_delivery_time', "" );
	    }

	    if ( isset( $output['coderockz_woo_delivery_time_field'] ) && $output['coderockz_woo_delivery_time_field'] == "conditional-delivery") {
	        WC()->session->set( 'selected_delivery_time_conditional', $output['coderockz_woo_delivery_time_field'] );
	    } else {
	    	WC()->session->set( 'selected_delivery_time_conditional', "" );
	    } 

	    if ( isset( $output['coderockz_woo_delivery_pickup_time_field'] ) ){
	        WC()->session->set( 'selected_pickup_time', $output['coderockz_woo_delivery_pickup_time_field'] );
	    }

	    if ( isset( $output['coderockz_woo_delivery_delivery_selection_box'] ) ){
	        WC()->session->set( 'selected_order_type', $output['coderockz_woo_delivery_delivery_selection_box'] );
	    }
	}

	public function coderockz_woo_delivery_remove_order_note() {
		return false;
	}

	public function hide_show_shipping_methods_based_on_selection( $available_shipping_methods, $package ) {

		//NEW NEW
		$timezone = $this->helper->get_the_timezone();
		date_default_timezone_set($timezone);
		$delivery_selected_date = date("Y-m-d", strtotime($this->helper->weekday_conversion($this->helper->date_conversion(sanitize_text_field(WC()->session->get( 'selected_delivery_date')),"delivery"),"delivery")));
		$today = date('Y-m-d', time());
		if(!is_null(WC()->session)) {
		  $delivery_time_conditional = WC()->session->get( 'selected_delivery_time_conditional' );
		}
		//NEW NEW
			
		if(isset($_COOKIE['coderockz_woo_delivery_option_time_pickup'])) {
		  $delivery_option_session = $_COOKIE['coderockz_woo_delivery_option_time_pickup'];
		} elseif(!is_null(WC()->session)) {
		  $delivery_option_session = WC()->session->get( 'coderockz_woo_delivery_option_time_pickup' );
		}

	    
		$localization_settings = get_option('coderockz_woo_delivery_localization_settings');
		$delivery_option_settings = get_option('coderockz_woo_delivery_option_delivery_settings');
		$enable_delivery_option = (isset($delivery_option_settings['enable_option_time_pickup']) && !empty($delivery_option_settings['enable_option_time_pickup'])) ? $delivery_option_settings['enable_option_time_pickup'] : false;

		$delivery_date_settings = get_option('coderockz_woo_delivery_date_settings');
		$pickup_date_settings = get_option('coderockz_woo_delivery_pickup_date_settings');
		$enable_delivery_date = (isset($delivery_date_settings['enable_delivery_date']) && !empty($delivery_date_settings['enable_delivery_date'])) ? $delivery_date_settings['enable_delivery_date'] : false;
		$enable_pickup_date = (isset($pickup_date_settings['enable_pickup_date']) && !empty($pickup_date_settings['enable_pickup_date'])) ? $pickup_date_settings['enable_pickup_date'] : false;

		$pickup_time_settings = get_option('coderockz_woo_delivery_pickup_settings');
		$enable_pickup_time = (isset($pickup_time_settings['enable_pickup_time']) && !empty($pickup_time_settings['enable_pickup_time'])) ? $pickup_time_settings['enable_pickup_time'] : false;

		$delivery_time_settings = get_option('coderockz_woo_delivery_time_settings');
		$enable_delivery_time = (isset($delivery_time_settings['enable_delivery_time']) && !empty($delivery_time_settings['enable_delivery_time'])) ? $delivery_time_settings['enable_delivery_time'] : false;

		$exclude_settings = get_option('coderockz_woo_delivery_exclude_settings');

		$exclude_shipping_methods = (isset($exclude_settings['exclude_shipping_methods']) && !empty($exclude_settings['exclude_shipping_methods'])) ? $exclude_settings['exclude_shipping_methods'] : array();

		$other_settings = get_option('coderockz_woo_delivery_other_settings');
		$disable_dynamic_shipping = (isset($other_settings['disable_dynamic_shipping']) && !empty($other_settings['disable_dynamic_shipping'])) ? $other_settings['disable_dynamic_shipping'] : false;


		$except_local_pickup = [];
		$local_pickup = [];

		$cart_total = $this ->helper->cart_total();
		$enable_free_shipping_restriction = (isset($delivery_option_settings['enable_free_shipping_restriction']) && !empty($delivery_option_settings['enable_free_shipping_restriction'])) ? $delivery_option_settings['enable_free_shipping_restriction'] : false;
		$enable_hide_other_shipping_method = (isset($delivery_option_settings['enable_hide_other_shipping_method']) && !empty($delivery_option_settings['enable_hide_other_shipping_method'])) ? $delivery_option_settings['enable_hide_other_shipping_method'] : false;
		$minimum_amount = (isset($delivery_option_settings['minimum_amount_shipping_restriction']) && $delivery_option_settings['minimum_amount_shipping_restriction'] != "") ? (float)$delivery_option_settings['minimum_amount_shipping_restriction'] : "";


	    if( ($enable_free_shipping_restriction && $minimum_amount != "" && $cart_total['delivery_free_shipping'] < $minimum_amount)){
	    	$hide_free_shipping = true;
	    } else {
	    	$hide_free_shipping = false;
	    }

	    if($hide_free_shipping) {
		    foreach ( $available_shipping_methods as $shipping_method_id => $shipping_method ) {
		    	if($shipping_method->method_id == "free_shipping") {
		    		unset($available_shipping_methods[$shipping_method_id]);
		    		break;
		    	}
		    }
		}

		$free_shipping_available = false;
		if(($enable_free_shipping_restriction && $enable_hide_other_shipping_method && $minimum_amount != "" && $cart_total['delivery_free_shipping'] >= $minimum_amount) ) {
			foreach ( $available_shipping_methods as $shipping_method_id => $shipping_method ) {
		    	if($shipping_method->method_id == "free_shipping") {
		    		$free_shipping_available = true;
		    	}
		    }

		    if($free_shipping_available) {
		    	foreach ( $available_shipping_methods as $shipping_method_id => $shipping_method ) {
			    	if($shipping_method->method_id == "flat_rate" && !in_array($shipping_method->label,$exclude_shipping_methods)) {
			    		unset($available_shipping_methods[$shipping_method_id]);
			    	}
			    }
		    }
		}


		if( is_checkout() ) {

			foreach ( $available_shipping_methods as $shipping_method_id => $shipping_method ) {
		    	if($shipping_method->method_id == "free_shipping" && !$disable_dynamic_shipping) {
		    		$free_shipping_label = $shipping_method->label;
		    		break;
		    	}
		    }

			$enable_free_shipping_current_day = (isset($delivery_option_settings['enable_free_shipping_current_day']) && !empty($delivery_option_settings['enable_free_shipping_current_day'])) ? $delivery_option_settings['enable_free_shipping_current_day'] : false;

			$disable_free_shipping_current_day = (isset($delivery_option_settings['disable_free_shipping_current_day']) && !empty($delivery_option_settings['disable_free_shipping_current_day'])) ? $delivery_option_settings['disable_free_shipping_current_day'] : false;

	    	$hide_free_shipping_weekday = (isset($delivery_option_settings['hide_free_shipping_weekday']) && !empty($delivery_option_settings['hide_free_shipping_weekday'])) ? $delivery_option_settings['hide_free_shipping_weekday'] : array();

		    $formated_date = date('Y-m-d H:i:s', strtotime($delivery_selected_date));
			$formated_date_obj = new DateTime($formated_date);
			$delivery_selected_date_weekday = $formated_date_obj->format("w");

			if( ($enable_free_shipping_restriction && $minimum_amount != "" && $cart_total['delivery_free_shipping'] < $minimum_amount) || ($enable_free_shipping_current_day && $delivery_selected_date != $today && $delivery_selected_date != '1970-01-01') || ($disable_free_shipping_current_day && $delivery_selected_date == $today && $delivery_selected_date != '1970-01-01') || (in_array($delivery_selected_date_weekday, $hide_free_shipping_weekday) && $delivery_selected_date != '1970-01-01')){
		    	$hide_free_shipping = true;
		    } else {
		    	$hide_free_shipping = false;
		    }

		    if($hide_free_shipping && (($enable_delivery_option && isset($delivery_option_session) && $delivery_option_session == "delivery") || (!$enable_delivery_option && (($enable_delivery_time && !$enable_pickup_time)||($enable_delivery_date && !$enable_pickup_date))))) {
			    foreach ( $available_shipping_methods as $shipping_method_id => $shipping_method ) {
			    	if($shipping_method->method_id == "free_shipping" && !$disable_dynamic_shipping) {
			    		unset($available_shipping_methods[$shipping_method_id]);
			    		break;
			    	}
			    }
			}

			$free_shipping_available = false;
			if(($enable_free_shipping_restriction && $enable_hide_other_shipping_method && $minimum_amount != "" && $cart_total['delivery_free_shipping'] >= $minimum_amount) || ($enable_hide_other_shipping_method && $enable_free_shipping_current_day && $delivery_selected_date == $today && $delivery_selected_date != '1970-01-01') || ($enable_hide_other_shipping_method && $disable_free_shipping_current_day && $delivery_selected_date != $today && $delivery_selected_date != '1970-01-01') || ($enable_hide_other_shipping_method && !in_array($delivery_selected_date_weekday, $hide_free_shipping_weekday) && $delivery_selected_date != '1970-01-01') ) {
				foreach ( $available_shipping_methods as $shipping_method_id => $shipping_method ) {
			    	if($shipping_method->method_id == "free_shipping") {
			    		$free_shipping_available = true;
			    	}
			    }

			    if($free_shipping_available && (($enable_delivery_option && isset($delivery_option_session) && $delivery_option_session == "delivery") || (!$enable_delivery_option && (($enable_delivery_time && !$enable_pickup_time)||($enable_delivery_date && !$enable_pickup_date))))) {
			    	foreach ( $available_shipping_methods as $shipping_method_id => $shipping_method ) {
				    	if($shipping_method->method_id == "flat_rate" && !in_array($shipping_method->label,$exclude_shipping_methods) && !$disable_dynamic_shipping) {

				    		unset($available_shipping_methods[$shipping_method_id]);
				    	}

				    	/*if($shipping_method->method_id == "flat_rate" && !in_array($shipping_method->label,$exclude_shipping_methods) && $disable_dynamic_shipping && $shipping_method->method_id != "local_pickup" ) {
				    		unset($available_shipping_methods[$shipping_method_id]);
				    	}*/
				    }
			    }
			}

			$shipping_id = [];
			$find_conditional_shipping_method = false;
			$find_only_conditional_shipping_method = false;
			$find_only_conditional_shipping_method_state = false;

			unset($_COOKIE["coderockz_woo_delivery_available_shipping_methods"]);
			setcookie("coderockz_woo_delivery_available_shipping_methods", null, -1, '/');
			WC()->session->__unset( 'coderockz_woo_delivery_available_shipping_methods' );


		    unset($_COOKIE['coderockz_woo_delivery_find_conditional_shipping_method']);
			setcookie("coderockz_woo_delivery_find_conditional_shipping_method", null, -1, '/');
			WC()->session->__unset( 'coderockz_woo_delivery_find_conditional_shipping_method' );

			unset($_COOKIE['coderockz_woo_delivery_find_only_conditional_shipping_method']);
			setcookie("coderockz_woo_delivery_find_only_conditional_shipping_method", null, -1, '/');
			WC()->session->__unset( 'coderockz_woo_delivery_find_only_conditional_shipping_method' );

			unset($_COOKIE['coderockz_woo_delivery_find_only_conditional_shipping_method_state']);
			setcookie("coderockz_woo_delivery_find_only_conditional_shipping_method_state", null, -1, '/');
			WC()->session->__unset( 'coderockz_woo_delivery_find_only_conditional_shipping_method_state' );

			$delivery_fee_settings = get_option('coderockz_woo_delivery_fee_settings');
			$conditional_delivery_shipping_method_name = (isset($delivery_fee_settings['conditional_delivery_shipping_method']) && $delivery_fee_settings['conditional_delivery_shipping_method'] != "") ? $delivery_fee_settings['conditional_delivery_shipping_method'] : "";
			$enable_conditional_delivery_fee = (isset($delivery_fee_settings['enable_conditional_delivery_fee']) && !empty($delivery_fee_settings['enable_conditional_delivery_fee'])) ? $delivery_fee_settings['enable_conditional_delivery_fee'] : false;

			$conditional_delivery_day_shipping_method = isset($delivery_fee_settings['conditional_delivery_day_shipping_method']) && $delivery_fee_settings['conditional_delivery_day_shipping_method'] != "" ? $delivery_fee_settings['conditional_delivery_day_shipping_method'] : "";
		   	$conditional_delivery_day_shipping_method_total_day = isset($delivery_fee_settings['conditional_delivery_day_shipping_method_total_day']) && $delivery_fee_settings['conditional_delivery_day_shipping_method_total_day'] != "" ? (int)$delivery_fee_settings['conditional_delivery_day_shipping_method_total_day'] : 0;

		    foreach ( $available_shipping_methods as $shipping_method_id => $shipping_method ) {
		    	if($shipping_method->label == $conditional_delivery_shipping_method_name && (($today == $delivery_selected_date) || (!$enable_delivery_date && $enable_delivery_time)) && $enable_conditional_delivery_fee) {
	        		
	        		$find_conditional_shipping_method = 'true';
	        		setcookie("coderockz_woo_delivery_find_conditional_shipping_method", $find_conditional_shipping_method);
    				WC()->session->set( 'coderockz_woo_delivery_find_conditional_shipping_method', $find_conditional_shipping_method );

    				break;
	        	
	        	} else {

	        		$find_conditional_shipping_method = 'false';
	        		setcookie("coderockz_woo_delivery_find_conditional_shipping_method", $find_conditional_shipping_method);
    				WC()->session->set( 'coderockz_woo_delivery_find_conditional_shipping_method', $find_conditional_shipping_method );
	        	}

	        }


	        $only_conditional_delivery_method_array = [];

        	foreach ( $available_shipping_methods as $shipping_method_id => $shipping_method ) {

		    	if($shipping_method->method_id != "local_pickup") {
		    		$only_conditional_delivery_method_array [] = $shipping_method->label;
		    	}

	        }

	        
			$detect_conditional_delivery_day_shipping_method = false;
	        foreach ( $available_shipping_methods as $shipping_method_id => $shipping_method ) {
		    	if($shipping_method->label == $conditional_delivery_day_shipping_method) {
	        		$detect_conditional_delivery_day_shipping_method = true;
    				break;
	        	}

	        }

	        if(count($only_conditional_delivery_method_array) == 1 && in_array($conditional_delivery_shipping_method_name,$only_conditional_delivery_method_array) && $enable_conditional_delivery_fee && (($today == $delivery_selected_date) || (!$enable_delivery_date && $enable_delivery_time))) {

        		$find_only_conditional_shipping_method = 'true';
        		setcookie("coderockz_woo_delivery_find_only_conditional_shipping_method", $find_only_conditional_shipping_method);
				WC()->session->set( 'coderockz_woo_delivery_find_only_conditional_shipping_method', $find_only_conditional_shipping_method );
        	
        	} else {

        		$find_only_conditional_shipping_method = 'false';
        		setcookie("coderockz_woo_delivery_find_only_conditional_shipping_method", $find_only_conditional_shipping_method);
				WC()->session->set( 'coderockz_woo_delivery_find_only_conditional_shipping_method', $find_only_conditional_shipping_method );
        	}

        	if(count($only_conditional_delivery_method_array) == 1 && in_array($conditional_delivery_shipping_method_name,$only_conditional_delivery_method_array) && $enable_conditional_delivery_fee) {

        		$find_only_conditional_shipping_method_state = 'true';
        		setcookie("coderockz_woo_delivery_find_only_conditional_shipping_method_state", $find_only_conditional_shipping_method_state);
				WC()->session->set( 'coderockz_woo_delivery_find_only_conditional_shipping_method_state', $find_only_conditional_shipping_method_state );
        	
        	} else {

        		$find_only_conditional_shipping_method_state = 'false';
        		setcookie("coderockz_woo_delivery_find_only_conditional_shipping_method_state", $find_only_conditional_shipping_method_state);
				WC()->session->set( 'coderockz_woo_delivery_find_only_conditional_shipping_method_state', $find_only_conditional_shipping_method_state );
        	}


        	if(count($only_conditional_delivery_method_array) == 1 && in_array($conditional_delivery_day_shipping_method,$only_conditional_delivery_method_array)) {

        		$find_only_conditional_day_shipping_method = 'true';
        	
        	} else {

        		$find_only_conditional_day_shipping_method = 'false';

        	}

        	

		    foreach ( $available_shipping_methods as $shipping_method_id => $shipping_method ) {

		    	$shipping_id [] = $shipping_method->method_id;

		    	if($shipping_method->method_id == 'free_shipping') {
		    		$free_shipping_label = $shipping_method->label;
		    	}

		    	if ( ! in_array( $shipping_method->method_id, ['local_pickup'] ) ) {
		            $local_pickup[] = $shipping_method_id; 
		        }

		        if ( in_array( $shipping_method->method_id, ['local_pickup'] ) ) {
		            $except_local_pickup[] = $shipping_method_id; 
		        }
		        
		    }


		    //NEW NEW
	        if((($enable_delivery_option && isset($delivery_option_session) && $delivery_option_session == "delivery") || (!$enable_delivery_option && (($enable_delivery_time && !$enable_pickup_time)||($enable_delivery_date && !$enable_pickup_date)))) && !empty($local_pickup) && is_checkout()) {

				$enable_conditional_delivery_fee = isset($delivery_fee_settings['enable_conditional_delivery_fee']) && !empty($delivery_fee_settings['enable_conditional_delivery_fee']) ? $delivery_fee_settings['enable_conditional_delivery_fee'] : false;


		        foreach ( $available_shipping_methods as $shipping_method_id => $shipping_method ) {

			        
		        	
			        $period = new DatePeriod(new DateTime(date('Y-m-d', time())), new DateInterval('P1D'), new DateTime(date('Y-m-d', time()).' +'.$conditional_delivery_day_shipping_method_total_day.' day'));
					$conditional_dates = [];
					foreach ($period as $date) {
					    $conditional_dates[] = $date->format("Y-m-d");
					}

					if($conditional_delivery_day_shipping_method != "" && $conditional_delivery_day_shipping_method_total_day != 0 && in_array($delivery_selected_date, $conditional_dates) && $detect_conditional_delivery_day_shipping_method && ($shipping_method->label != $conditional_delivery_day_shipping_method && $shipping_method->label != $conditional_delivery_shipping_method_name) && !$disable_dynamic_shipping) {

			        	unset($available_shipping_methods[$shipping_method_id]);

					} elseif($conditional_delivery_day_shipping_method != "" && $conditional_delivery_day_shipping_method_total_day != 0 && !in_array($delivery_selected_date, $conditional_dates) && $detect_conditional_delivery_day_shipping_method && $shipping_method->label == $conditional_delivery_day_shipping_method && !$disable_dynamic_shipping) {
			        	unset($available_shipping_methods[$shipping_method_id]);
					}


			        if(isset($delivery_time_conditional) && $delivery_time_conditional == "conditional-delivery" && $enable_conditional_delivery_fee && (isset($delivery_fee_settings['conditional_delivery_shipping_method']) && !empty($delivery_fee_settings['conditional_delivery_shipping_method'])) && (($delivery_selected_date == $today) || (!$enable_delivery_date && $enable_delivery_time)) && $find_conditional_shipping_method && !$disable_dynamic_shipping) {

				        	
			        	if($shipping_method->label != $delivery_fee_settings['conditional_delivery_shipping_method']) {
			        		unset($available_shipping_methods[$shipping_method_id]);
			        	}

					} elseif(isset($delivery_time_conditional) && $delivery_time_conditional == "conditional-delivery" && $enable_conditional_delivery_fee && (isset($delivery_fee_settings['conditional_delivery_shipping_method']) && !empty($delivery_fee_settings['conditional_delivery_shipping_method'])) && $today != $delivery_selected_date && !$disable_dynamic_shipping) {
						if($shipping_method->label == $delivery_fee_settings['conditional_delivery_shipping_method']) {
			        		unset($available_shipping_methods[$shipping_method_id]);
			        	}
					}

					if(isset($delivery_time_conditional) && $delivery_time_conditional != "conditional-delivery" && $enable_conditional_delivery_fee && (isset($delivery_fee_settings['conditional_delivery_shipping_method']) && !empty($delivery_fee_settings['conditional_delivery_shipping_method'])) && !$disable_dynamic_shipping ) {
						if($shipping_method->label == $delivery_fee_settings['conditional_delivery_shipping_method']) {
			        		unset($available_shipping_methods[$shipping_method_id]);
			        	}
					}

				}
			}

			//NEW NEW

		    setcookie("coderockz_woo_delivery_available_shipping_methods", json_encode($shipping_id));
		    WC()->session->set( 'coderockz_woo_delivery_available_shipping_methods', $shipping_id );

			if(!$disable_dynamic_shipping) {

			    if((($enable_delivery_option && isset($delivery_option_session) && $delivery_option_session == "delivery") || (!$enable_delivery_option && (($enable_delivery_time && !$enable_pickup_time)||($enable_delivery_date && !$enable_pickup_date)))) /*&& !empty($local_pickup)*/ && is_checkout()) {
			    	foreach ( $except_local_pickup as $rate_id ) {
			            unset($available_shipping_methods[$rate_id]);
			        }

			        if(!empty($available_shipping_methods)) {		        	
						return $available_shipping_methods;
					} else {
						/*$remove_shipping = add_filter( 'woocommerce_cart_ready_to_calc_shipping', array($this,'coderockz_woo_delivery_disable_shipping_calc_on_cart'), 99 );*/
						
						if($enable_free_shipping_current_day && $delivery_selected_date != $today && $delivery_selected_date != '1970-01-01') {
							$only_available_for_today_text = isset($localization_settings['only_available_for_today_text']) && $localization_settings['only_available_for_today_text'] != "" ? stripslashes($localization_settings['only_available_for_today_text']) : __("only available for today","coderockz-woo-delivery");
							wc_add_notice( $free_shipping_label.' '.$only_available_for_today_text, 'notice');
						}

						if($disable_free_shipping_current_day && $delivery_selected_date == $today && $delivery_selected_date != '1970-01-01') {
							$free_shipping_other_day_text = isset($localization_settings['free_shipping_other_day_text']) && $localization_settings['free_shipping_other_day_text'] != "" ? stripslashes($localization_settings['free_shipping_other_day_text']) : __("is not available for today","coderockz-woo-delivery");
							wc_add_notice( $free_shipping_label.' '.$free_shipping_other_day_text, 'notice');
						}

						if(!empty($hide_free_shipping_weekday) && in_array($delivery_selected_date_weekday, $hide_free_shipping_weekday) && $delivery_selected_date != '1970-01-01') {
							$week_days = "6,0,1,2,3,4,5";

							$week_days = explode(",",$week_days);
							$available_free_weekday= array_values(array_diff($week_days, $hide_free_shipping_weekday));

							$available_free_weekday_name=[];
							foreach($available_free_weekday as $weekday) {
								switch ($weekday) {
								  case "0":
								    $available_free_weekday_name[] = $this->helper->weekday_conversion_to_locale("Sunday","pickup");
								    break;
								  case "1":
								    $available_free_weekday_name[] = $this->helper->weekday_conversion_to_locale("Monday","pickup");
								    break;
								  case "2":
								    $available_free_weekday_name[] = $this->helper->weekday_conversion_to_locale("Tuesday","pickup");
								    break;
								  case "3":
								    $available_free_weekday_name[] = $this->helper->weekday_conversion_to_locale("Wednesday","pickup");
								    break;
								  case "4":
								    $available_free_weekday_name[] = $this->helper->weekday_conversion_to_locale("Thursday","pickup");
								    break;
								  case "5":
								    $available_free_weekday_name[] = $this->helper->weekday_conversion_to_locale("Friday","pickup");
								    break;
								  case "6":
								    $available_free_weekday_name[] = $this->helper->weekday_conversion_to_locale("Saturday","pickup");
								    break;
								}
							}

							$available_free_weekday_name = implode(", ",$available_free_weekday_name);
							$only_available_for_text = isset($localization_settings['only_available_for_text']) && $localization_settings['only_available_for_text'] != "" ? stripslashes($localization_settings['only_available_for_text']) : __("only available for","coderockz-woo-delivery");
							wc_add_notice( $free_shipping_label.' '.$only_available_for_text.' '. $available_free_weekday_name, 'notice');
						}

						if($enable_conditional_delivery_fee && $find_only_conditional_shipping_method && $find_only_conditional_shipping_method_state && $conditional_delivery_shipping_method_name != "" && (($delivery_selected_date != $today) || (!$enable_delivery_date && $enable_delivery_time))) {
							$urgent_delivery_fee_text = isset($localization_settings['urgent_delivery_fee_text']) && $localization_settings['urgent_delivery_fee_text'] != "" ? stripslashes($localization_settings['urgent_delivery_fee_text']) : __("Delivery only possible today. Shipping Method will change accordingly","coderockz-woo-delivery");
							wc_add_notice( __($urgent_delivery_fee_text, 'coderockz-woo-delivery'), 'notice');
						}

						
						if($detect_conditional_delivery_day_shipping_method && $find_only_conditional_day_shipping_method) {

				        	if($conditional_delivery_day_shipping_method != "" && $conditional_delivery_day_shipping_method_total_day != 0 && !in_array($delivery_selected_date, $conditional_dates) && ($enable_delivery_option && isset($delivery_option_session) && $delivery_option_session == "delivery") || (!$enable_delivery_option && $enable_delivery_date && !$enable_pickup_date)) {
				        		$only_available_for_text = isset($localization_settings['only_available_for_text']) && $localization_settings['only_available_for_text'] != "" ? stripslashes($localization_settings['only_available_for_text']) : __("only available for","coderockz-woo-delivery");
								wc_add_notice( $conditional_delivery_day_shipping_method.' '.$only_available_for_text.' '.implode(", ",$conditional_dates), 'notice');
							}

						}

						return array();
					}
			    } elseif((($enable_delivery_option && isset($delivery_option_session) && $delivery_option_session == "pickup") || (!$enable_delivery_option && ((!$enable_delivery_time && $enable_pickup_time)||(!$enable_delivery_date && $enable_pickup_date)))) && !empty($except_local_pickup) && is_checkout()) {
			    	

			    	foreach ( $local_pickup as $rate_id ) {
			            unset($available_shipping_methods[$rate_id]);
			        }

			        if(!empty($available_shipping_methods)) {
						return $available_shipping_methods;
					} else {
						$remove_shipping = add_filter( 'woocommerce_cart_ready_to_calc_shipping', array($this,'coderockz_woo_delivery_disable_shipping_calc_on_cart'), 99 );
						return array();
					}
			    }

			    if((($enable_delivery_option && isset($delivery_option_session) && $delivery_option_session == "pickup") || (!$enable_delivery_option && ((!$enable_delivery_time && $enable_pickup_time)||(!$enable_delivery_date && $enable_pickup_date)))) && !empty($except_local_pickup) && is_checkout()) { 
					$remove_shipping = add_filter( 'woocommerce_cart_ready_to_calc_shipping', array($this,'coderockz_woo_delivery_disable_shipping_calc_on_cart'), 99 );

					foreach ( $available_shipping_methods as $rate_key => $rate_values ) {
			            // Not for "Free Shipping method" (all others only)
			            if ( 'free_shipping' !== $rate_values->method_id ) {

			                // Set the rate cost
			                $available_shipping_methods[$rate_key]->cost = 0;

			                // Set taxes rate cost (if enabled)
			                $taxes = array();
			                foreach ($available_shipping_methods[$rate_key]->taxes as $key => $tax)
			                    if( $available_shipping_methods[$rate_key]->taxes[$key] > 0 ) // set the new tax cost
			                        $taxes[$key] = 0;
			                $available_shipping_methods[$rate_key]->taxes = $taxes;
			            }
			        }

					return $available_shipping_methods;
				}


			}

		}
	
		return $available_shipping_methods;

	}

	public function coderockz_woo_delivery_disable_shipping_calc_on_cart( $show_shipping ) {
	    if( is_checkout() ) {
	        return false;
	    }
	    return $show_shipping;
	}

	public function coderockz_woo_delivery_refresh_shipping_methods( $post_data ){

	    $bool = true;
	    if(isset($_COOKIE['coderockz_woo_delivery_option_time_pickup'])) {
		  $delivery_option_session = $_COOKIE['coderockz_woo_delivery_option_time_pickup'];
		} elseif (!is_null(WC()->session)) {
		  $delivery_option_session = WC()->session->get( 'coderockz_woo_delivery_option_time_pickup' );
		}
	    
		$delivery_option_settings = get_option('coderockz_woo_delivery_option_delivery_settings');
		$enable_delivery_option = (isset($delivery_option_settings['enable_option_time_pickup']) && !empty($delivery_option_settings['enable_option_time_pickup'])) ? $delivery_option_settings['enable_option_time_pickup'] : false;

		$delivery_date_settings = get_option('coderockz_woo_delivery_date_settings');
		$pickup_date_settings = get_option('coderockz_woo_delivery_pickup_date_settings');
		$enable_delivery_date = (isset($delivery_date_settings['enable_delivery_date']) && !empty($delivery_date_settings['enable_delivery_date'])) ? $delivery_date_settings['enable_delivery_date'] : false;
		$enable_pickup_date = (isset($pickup_date_settings['enable_pickup_date']) && !empty($pickup_date_settings['enable_pickup_date'])) ? $pickup_date_settings['enable_pickup_date'] : false;

		$pickup_time_settings = get_option('coderockz_woo_delivery_pickup_settings');
		$enable_pickup_time = (isset($pickup_time_settings['enable_pickup_time']) && !empty($pickup_time_settings['enable_pickup_time'])) ? $pickup_time_settings['enable_pickup_time'] : false;

		$delivery_time_settings = get_option('coderockz_woo_delivery_time_settings');
		$enable_delivery_time = (isset($delivery_time_settings['enable_delivery_time']) && !empty($delivery_time_settings['enable_delivery_time'])) ? $delivery_time_settings['enable_delivery_time'] : false;


	    if((($enable_delivery_option && isset($delivery_option_session) && $delivery_option_session == "pickup") || (!$enable_delivery_option && ((!$enable_delivery_time && $enable_pickup_time)||(!$enable_delivery_date && $enable_pickup_date)))) && is_checkout()) {
	    	$bool = false;
	    }
	    // Mandatory to make it work with shipping methods
	    if(is_checkout()){
	    	foreach ( WC()->cart->get_shipping_packages() as $package_key => $package ){
		        WC()->session->set( 'shipping_for_package_' . $package_key, $bool );
		    }
	    }
	    
	    WC()->cart->calculate_shipping();
	}

	public function coderockz_woo_delivery_add_delivery_info_order_note( $order_id ) {

		$order = new WC_Order( $order_id );
	    
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
		
		$delivery_date_format = (isset($delivery_date_settings['date_format']) && !empty($delivery_date_settings['date_format'])) ? $delivery_date_settings['date_format'] : "F j, Y";
		
		$add_weekday_name = (isset($delivery_date_settings['add_weekday_name']) && !empty($delivery_date_settings['add_weekday_name'])) ? $delivery_date_settings['add_weekday_name'] : false;

		if($add_weekday_name) {
			$delivery_date_format = "l ".$delivery_date_format;
		}

		$pickup_date_format = (isset($pickup_date_settings['date_format']) && !empty($pickup_date_settings['date_format'])) ? $pickup_date_settings['date_format'] : "F j, Y";

		$pickup_add_weekday_name = (isset($pickup_date_settings['add_weekday_name']) && !empty($pickup_date_settings['add_weekday_name'])) ? $pickup_date_settings['add_weekday_name'] : false;

		if($pickup_add_weekday_name) {
			$pickup_date_format = "l ".$pickup_date_format;
		}
		
		$time_format = (isset($delivery_time_settings['time_format']) && !empty($delivery_time_settings['time_format']))?$delivery_time_settings['time_format']:"12";
		if($time_format == 12) {
			$time_format = "h:i A";
		} elseif ($time_format == 24) {
			$time_format = "H:i";
		}

		$pickup_time_format = (isset($pickup_time_settings['time_format']) && !empty($pickup_time_settings['time_format']))?$pickup_time_settings['time_format']:"12";
		if($pickup_time_format == 12) {
			$pickup_time_format = "h:i A";
		} elseif ($pickup_time_format == 24) {
			$pickup_time_format = "H:i";
		}


	    $order_type_field_label = (isset($delivery_option_settings['delivery_option_label']) && !empty($delivery_option_settings['delivery_option_label'])) ? stripslashes($delivery_option_settings['delivery_option_label']) : "Order Type";
	    $delivery_field_label = (isset($delivery_option_settings['delivery_label']) && !empty($delivery_option_settings['delivery_label'])) ? stripslashes($delivery_option_settings['delivery_label']) : "Delivery";
		$pickup_field_label = (isset($delivery_option_settings['pickup_label']) && !empty($delivery_option_settings['pickup_label'])) ? stripslashes($delivery_option_settings['pickup_label']) : "Pickup";
	    
	    $other_settings = get_option('coderockz_woo_delivery_other_settings');
	    
	    $add_delivery_info_order_note = (isset($other_settings['add_delivery_info_order_note']) && !empty($other_settings['add_delivery_info_order_note'])) ? $other_settings['add_delivery_info_order_note'] : false;
	    
	    if($add_delivery_info_order_note) {
	    
		    $order_note = "";

		    if(isset($_POST['coderockz_woo_delivery_delivery_selection_box']) && !empty($_POST['coderockz_woo_delivery_delivery_selection_box'])) {

				if($_POST['coderockz_woo_delivery_delivery_selection_box'] == "delivery") {
					//$order_note .= $order_type_field_label .': '. $delivery_field_label;
					if(isset($_POST['coderockz_woo_delivery_date_field']) && !empty($_POST['coderockz_woo_delivery_date_field'])) {
						$order_note .= "<br>".$delivery_date_field_label .': '.sanitize_text_field($_POST['coderockz_woo_delivery_date_field']);
				    }
					

				    if(isset($_POST['coderockz_woo_delivery_time_field']) && !empty($_POST['coderockz_woo_delivery_time_field'])) {
						if($_POST['coderockz_woo_delivery_time_field'] == "as-soon-as-possible") {
							$as_soon_as_possible_text = (isset($delivery_time_settings['as_soon_as_possible_text']) && !empty($delivery_time_settings['as_soon_as_possible_text'])) ? stripslashes($delivery_time_settings['as_soon_as_possible_text']) : "As Soon As Possible";
							$time_value = $as_soon_as_possible_text;
						} elseif( $_POST['coderockz_woo_delivery_time_field'] == "conditional-delivery" ) {
							$delivery_fee_settings = get_option('coderockz_woo_delivery_fee_settings');
							$conditional_time = date($time_format, mktime(0, (int)((date("G")*60)+date("i")))) . " - ".date($time_format, mktime(0, (int)((date("G")*60)+date("i") + $delivery_fee_settings['conditional_delivery_fee_duration'])));  
							$time_value = $conditional_time;
						} else {

							$minutes = sanitize_text_field($_POST['coderockz_woo_delivery_time_field']);
							$minutes = explode(' - ', $minutes);

				    		if(!isset($minutes[1])) {
				    			$time_value = date($time_format, strtotime($minutes[0]));
				    		} else {

				    			$time_value = date($time_format, strtotime($minutes[0])) . ' - ' . date($time_format, strtotime($minutes[1]));

				    		}

			    		}

						$order_note .= "<br>".$delivery_time_field_label .': '.$time_value;
					}
				} elseif($_POST['coderockz_woo_delivery_delivery_selection_box'] == "pickup") {
					//$order_note .= $order_type_field_label .': '. $pickup_field_label;
					if(isset($_POST['coderockz_woo_delivery_pickup_date_field']) && !empty($_POST['coderockz_woo_delivery_pickup_date_field'])) {

						$order_note .= "<br>".$pickup_date_field_label .': '.sanitize_text_field($_POST['coderockz_woo_delivery_pickup_date_field']);
				    }


					if(isset($_POST['coderockz_woo_delivery_pickup_time_field']) && !empty($_POST['coderockz_woo_delivery_pickup_time_field'])) {
						$pickup_minutes = sanitize_text_field($_POST['coderockz_woo_delivery_pickup_time_field']);
						$pickup_minutes = explode(' - ', $pickup_minutes);

			    		if(!isset($pickup_minutes[1])) {
			    			$pickup_time_value = date($pickup_time_format, strtotime($pickup_minutes[0]));
			    		} else {

			    			$pickup_time_value = date($pickup_time_format, strtotime($pickup_minutes[0])) . ' - ' . date($pickup_time_format, strtotime($pickup_minutes[1]));
			    		}

						$order_note .= "<br>".$pickup_time_field_label .': '.$pickup_time_value;
					}


					if(isset($_POST['coderockz_woo_delivery_pickup_location_field']) && !empty($_POST['coderockz_woo_delivery_pickup_location_field'])) {

						$order_note .= "<br>".$pickup_location_field_label .': '.sanitize_text_field($_POST['coderockz_woo_delivery_pickup_location_field']);
					}
				}
		    } else {
		    	if(isset($_POST['coderockz_woo_delivery_date_field']) && !empty($_POST['coderockz_woo_delivery_date_field'])) {

					$order_note .= "<br>".$delivery_date_field_label .': '.sanitize_text_field($_POST['coderockz_woo_delivery_date_field']);
			    }
				

			    if(isset($_POST['coderockz_woo_delivery_time_field']) && !empty($_POST['coderockz_woo_delivery_time_field'])) {
					$minutes = sanitize_text_field($_POST['coderockz_woo_delivery_time_field']);
					if($_POST['coderockz_woo_delivery_time_field'] == "as-soon-as-possible") {
						$as_soon_as_possible_text = (isset($delivery_time_settings['as_soon_as_possible_text']) && !empty($delivery_time_settings['as_soon_as_possible_text'])) ? stripslashes($delivery_time_settings['as_soon_as_possible_text']) : "As Soon As Possible";
						$time_value = $as_soon_as_possible_text;
					} elseif( $_POST['coderockz_woo_delivery_time_field'] == "conditional-delivery" ) {
						$delivery_fee_settings = get_option('coderockz_woo_delivery_fee_settings');
						$conditional_time = date($time_format, mktime(0, (int)((date("G")*60)+date("i")))) . " - ".date($time_format, mktime(0, (int)((date("G")*60)+date("i") + $delivery_fee_settings['conditional_delivery_fee_duration']))); 
						$time_value = $conditional_time;
					
					} else {

						$minutes = sanitize_text_field($_POST['coderockz_woo_delivery_time_field']);
						$minutes = explode(' - ', $minutes);

			    		if(!isset($minutes[1])) {
			    			$time_value = date($time_format, strtotime($minutes[0]));
			    		} else {

			    			$time_value = date($time_format, strtotime($minutes[0])) . ' - ' . date($time_format, strtotime($minutes[1]));

			    		}

		    		}

					$order_note .= "<br>".$delivery_time_field_label .': '.$time_value;
				}

				if(isset($_POST['coderockz_woo_delivery_pickup_date_field']) && !empty($_POST['coderockz_woo_delivery_pickup_date_field'])) {

					$order_note .= "<br>".$pickup_date_field_label .': '.sanitize_text_field($_POST['coderockz_woo_delivery_pickup_date_field']);
			    }


				if(isset($_POST['coderockz_woo_delivery_pickup_time_field']) && !empty($_POST['coderockz_woo_delivery_pickup_time_field'])) {
					$pickup_minutes = sanitize_text_field($_POST['coderockz_woo_delivery_pickup_time_field']);
					$pickup_minutes = explode(' - ', $pickup_minutes);

		    		if(!isset($pickup_minutes[1])) {
		    			$pickup_time_value = date($pickup_time_format, strtotime($pickup_minutes[0]));
		    		} else {

		    			$pickup_time_value = date($pickup_time_format, strtotime($pickup_minutes[0])) . ' - ' . date($pickup_time_format, strtotime($pickup_minutes[1]));
		    		}

					$order_note .= "<br>".$pickup_time_field_label .': '.$pickup_time_value;
				}


				if(isset($_POST['coderockz_woo_delivery_pickup_location_field']) && !empty($_POST['coderockz_woo_delivery_pickup_location_field'])) {

					$order_note .= "<br>".$pickup_location_field_label .': '.sanitize_text_field($_POST['coderockz_woo_delivery_pickup_location_field']);
				}
		    }


			if(isset($_POST['coderockz_woo_delivery_additional_field_field']) && !empty($_POST['coderockz_woo_delivery_additional_field_field'])) {

				$order_note .= "<br>".$additional_field_field_label .': '.sanitize_text_field($_POST['coderockz_woo_delivery_additional_field_field']);
			}


			// Add the note
			if($order_note != "") {
				$order->add_order_note( $order_note );
			}
		
	    }
		
		/*$google_calendar_settings = get_option('coderockz_woo_delivery_google_calendar_settings');
		
		$enable_calendar_sync_client = isset($google_calendar_settings['google_calendar_sync']) && !empty($google_calendar_settings['google_calendar_sync']) ? $google_calendar_settings['google_calendar_sync'] : false;
		
		$calendar_sync_customer_client_id = isset($google_calendar_settings['google_calendar_client_id']) && !empty($google_calendar_settings['google_calendar_client_id']) ? $google_calendar_settings['google_calendar_client_id'] : "";
		
		$calendar_sync_customer_client_secret = isset($google_calendar_settings['google_calendar_client_secret']) && !empty($google_calendar_settings['google_calendar_client_secret']) ? $google_calendar_settings['google_calendar_client_secret'] : "";
		
		$calendar_sync_customer_redirect_url = wc_get_checkout_url().'order-received/';
		
		if(get_option('coderockz_woo_delivery_google_calendar_access_token') && $enable_calendar_sync_client && $google_calendar_settings['google_calendar_client_id'] != "" && $google_calendar_settings['google_calendar_client_secret'] != "" ) {
		    
		    $client = new Google_Client();
            $client->setClientId($calendar_sync_customer_client_id);
            $client->setClientSecret($calendar_sync_customer_client_secret);
            $client->setRedirectUri($calendar_sync_customer_redirect_url);
            $client->addScope("https://www.googleapis.com/auth/calendar.events");
            $client->setAccessType('offline');
            
            $client->setAccessToken(get_option('coderockz_woo_delivery_google_calendar_access_token'));
	                    
            if($client->isAccessTokenExpired()) {
                $client->fetchAccessTokenWithRefreshToken(get_option('coderockz_woo_delivery_google_calendar_access_token')['refresh_token']);
                $access_token = $client->getAccessToken();
                update_option('coderockz_woo_delivery_google_calendar_access_token',$access_token);
                
            }
            
    	    
    	    if (isset($_POST['coderockz_woo_delivery_delivery_selection_box']) && !empty($_POST['coderockz_woo_delivery_delivery_selection_box'])) {
    			if($_POST['coderockz_woo_delivery_delivery_selection_box'] == "delivery") {

    	    		 $delivery_type = $delivery_field_label;
    
    			} elseif($_POST['coderockz_woo_delivery_delivery_selection_box'] == "pickup") {
    				
    				$delivery_type = $pickup_field_label;
    			}
    		} elseif(!$enable_delivery_option && (($enable_delivery_time && !$enable_pickup_time) || ($enable_delivery_date && !$enable_pickup_date))) {
    			$delivery_type = $delivery_field_label;
    		} elseif(!$enable_delivery_option && ((!$enable_delivery_time && $enable_pickup_time) || (!$enable_delivery_date && $enable_pickup_date))) {
    			$delivery_type = $pickup_field_label;
    		} else {
    			$delivery_type = "";
    		}
    	    
			
			if(metadata_exists('post', $order_id, '_wcj_order_number') && get_post_meta($order_id, '_wcj_order_number', true) !="") {
				$order_id_with_custom = '#'.get_post_meta($order_id, '_wcj_order_number', true);
			} elseif(class_exists('WC_Seq_Order_Number') || class_exists('WC_Seq_Order_Number_Pro')) {
				$order_id_with_custom = '#'.$order->get_order_number();
			} else {
				$order_id_with_custom = '#'.$order_id;
			}
            
            if(isset($_POST['coderockz_woo_delivery_time_field']) && !empty($_POST['coderockz_woo_delivery_time_field']) && $_POST['coderockz_woo_delivery_time_field'] == "as-soon-as-possible") {
                
				$as_soon_as_possible_text = (isset($delivery_time_settings['as_soon_as_possible_text']) && !empty($delivery_time_settings['as_soon_as_possible_text'])) ? stripslashes($delivery_time_settings['as_soon_as_possible_text']) : __("As Soon As Possible", 'coderockz-woo-delivery');
                $summary = $delivery_type.$order_id_with_custom."(".$as_soon_as_possible_text.")";
                
            } else {
                $summary = $delivery_type.$order_id_with_custom;
            }
            
            if(isset($_POST['coderockz_woo_delivery_date_field']) && !empty($_POST['coderockz_woo_delivery_date_field'])) {
		    	$date = date('Y-m-d', strtotime($en_delivery_date));
		    }

		    if(isset($_POST['coderockz_woo_delivery_pickup_date_field']) && !empty($_POST['coderockz_woo_delivery_pickup_date_field'])) {
		    	$date = date('Y-m-d', strtotime($en_pickup_date)); 
		    }

		    if(isset($_POST['coderockz_woo_delivery_time_field']) && !empty($_POST['coderockz_woo_delivery_time_field'])) {

		    	if($_POST['coderockz_woo_delivery_time_field'] != "as-soon-as-possible" && $_POST['coderockz_woo_delivery_time_field'] != "conditional-delivery") {
			    	$minutes = sanitize_text_field($_POST['coderockz_woo_delivery_time_field']);
			    	$minutes = explode(' - ', $minutes);

		    		if(!isset($minutes[1])) {
		    			$time_start = "T".date("H:i", strtotime($minutes[0])).':00';
		    			$time_end = "T".date("H:i", strtotime($minutes[0])).':00';
		    		} else {

		    			$time_start = "T".date("H:i", strtotime($minutes[0])).':00';
		    			$time_end = "T".date("H:i", strtotime($minutes[1])).':00'; 			
		    		}
	    		} elseif($_POST['coderockz_woo_delivery_time_field'] == "conditional-delivery" ) {
	    		    $delivery_fee_settings = get_option('coderockz_woo_delivery_fee_settings');
				    $minutes = date($time_format, mktime(0, (int)((date("G")*60)+date("i")))) . " - ".date($time_format, mktime(0, (int)((date("G")*60)+date("i") + $delivery_fee_settings['conditional_delivery_fee_duration']))); 
				    $minutes = explode(' - ', $minutes);

		    		if(!isset($minutes[1])) {
		    			$time_start = "T".date("H:i", strtotime($minutes[0])).':00';
		    			$time_end = "T".date("H:i", strtotime($minutes[0])).':00';
		    		} else {

		    			$time_start = "T".date("H:i", strtotime($minutes[0])).':00';
		    			$time_end = "T".date("H:i", strtotime($minutes[1])).':00'; 			
		    		}
	    		}
		    	
		    }

		    if(isset($_POST['coderockz_woo_delivery_pickup_time_field']) && !empty($_POST['coderockz_woo_delivery_pickup_time_field'])) {
		    	$pickup_minutes = sanitize_text_field($_POST['coderockz_woo_delivery_pickup_time_field']);
		    	$pickup_minutes = explode(' - ', $pickup_minutes);

		    	if(!isset($pickup_minutes[1])) {
	    			$time_start = "T".date("H:i", strtotime($pickup_minutes[0])).':00';
	    			$time_end = "T".date("H:i", strtotime($pickup_minutes[0])).':00';
	    		} else {

	    			$time_start = "T".date("H:i", strtotime($pickup_minutes[0])).':00';
	    			$time_end = "T".date("H:i", strtotime($pickup_minutes[1])).':00'; 			
	    		}
		    	
		    }
		    
		    if(isset($_POST['coderockz_woo_delivery_pickup_location_field']) && !empty($_POST['coderockz_woo_delivery_pickup_location_field'])) {
				$location = sanitize_text_field($_POST['coderockz_woo_delivery_pickup_location_field']);
			} else {
			    $location = "";
			}
			
			
			$start = "";
			$end = "";
			if(isset($date)) {
			    $start .= $date;
			    $end .= $date;
			} else {
			    $order_created_obj= new DateTime($order->get_date_created());
			    $start .= $order_created_obj->format("Y-m-d");
			    $end .= $order_created_obj->format("Y-m-d");
			}
			
			if(isset($time_start)) {
			    $start .= $time_start;
			    $dateCriteria = 'dateTime';
			} else {
			   $dateCriteria = 'date'; 
			}
			
			if(isset($time_end)) {
			    $end .= $time_end;
			    $dateCriteria = 'dateTime';
			} else {
			   $dateCriteria = 'date'; 
			}
			    			
			$delivery_details = "<b>"."Delivery Details:"."</b><br/>";
			//if($delivery_type != "") {
			    //$delivery_details .= $order_type_field_label.': ' . $delivery_type . "<br/>";
			//}
		    if(isset($_POST['coderockz_woo_delivery_date_field']) && !empty($_POST['coderockz_woo_delivery_date_field'])) {

		    	$delivery_details .= $delivery_date_field_label.': ' . sanitize_text_field($_POST['coderockz_woo_delivery_date_field']) . "<br/>";

		    }

		    if(isset($_POST['coderockz_woo_delivery_pickup_date_field']) && !empty($_POST['coderockz_woo_delivery_pickup_date_field'])) {

		    	$delivery_details .= $pickup_date_field_label.': ' . sanitize_text_field($_POST['coderockz_woo_delivery_pickup_date_field']) . "<br/>"; 

		    }

		    if(isset($_POST['coderockz_woo_delivery_time_field']) && !empty($_POST['coderockz_woo_delivery_time_field'])) {

		    	if($_POST['coderockz_woo_delivery_time_field'] !="as-soon-as-possible" && $_POST['coderockz_woo_delivery_time_field'] != "conditional-delivery") {
			    	$minutes = sanitize_text_field($_POST['coderockz_woo_delivery_time_field']);
			    	$minutes = explode(' - ', $minutes);

		    		if(!isset($minutes[1])) {
		    			$delivery_details .= $delivery_time_field_label.': ' . date($time_format, strtotime($minutes[0])) . "<br/>";
		    		} else {

		    			$delivery_details .= $delivery_time_field_label.': ' . date($time_format, strtotime($minutes[0])) . ' - ' . date($time_format, strtotime($minutes[1])) . "<br/>";  			
		    		}
	    		} elseif($_POST['coderockz_woo_delivery_time_field'] == "conditional-delivery") {
	    		    $delivery_fee_settings = get_option('coderockz_woo_delivery_fee_settings');
				    $minutes = date($time_format, mktime(0, (int)((date("G")*60)+date("i")))) . " - ".date($time_format, mktime(0, (int)((date("G")*60)+date("i") + $delivery_fee_settings['conditional_delivery_fee_duration'])));
				    $minutes = explode(' - ', $minutes);

		    		if(!isset($minutes[1])) {
		    			$delivery_details .= $delivery_time_field_label.': ' . date($time_format, strtotime($minutes[0])) . "<br/>";
		    		} else {

		    			$delivery_details .= $delivery_time_field_label.': ' . date($time_format, strtotime($minutes[0])) . ' - ' . date($time_format, strtotime($minutes[1])) . "<br/>";  			
		    		}
	    		} else {
	    			$as_soon_as_possible_text = (isset($delivery_time_settings['as_soon_as_possible_text']) && !empty($delivery_time_settings['as_soon_as_possible_text'])) ? stripslashes($delivery_time_settings['as_soon_as_possible_text']) : "As Soon As Possible";
	    			$delivery_details .= $delivery_time_field_label.': ' . $as_soon_as_possible_text . "<br/>";
	    		}
		    	
		    }

		    if(isset($_POST['coderockz_woo_delivery_pickup_time_field']) && !empty($_POST['coderockz_woo_delivery_pickup_time_field'])) {
		    	$pickup_minutes = sanitize_text_field($_POST['coderockz_woo_delivery_pickup_time_field']);
		    	$pickup_minutes = explode(' - ', $pickup_minutes);

	    		if(!isset($pickup_minutes[1])) {
	    			$delivery_details .= $pickup_time_field_label.': ' . date($pickup_time_format, strtotime($pickup_minutes[0])) . "<br/>";
	    		} else {

	    			$delivery_details .= $pickup_time_field_label.': ' . date($pickup_time_format, strtotime($pickup_minutes[0])) . ' - ' . date($pickup_time_format, strtotime($pickup_minutes[1])) . "<br/>";  			
	    		}
		    	
		    }

		    if(isset($_POST['coderockz_woo_delivery_pickup_location_field']) && !empty($_POST['coderockz_woo_delivery_pickup_location_field'])) {
				$delivery_details .= $pickup_location_field_label.': ' . sanitize_text_field($_POST['coderockz_woo_delivery_pickup_location_field']) . "<br/>";
			}

			if(isset($_POST['coderockz_woo_delivery_additional_field_field']) && !empty($_POST['coderockz_woo_delivery_additional_field_field'])) {
				$delivery_details .= $additional_field_label.': ' . sanitize_text_field($_POST['coderockz_woo_delivery_additional_field_field']);
			}
			
			$i=1;
			$product_details = "<br/><b>"."Products:"."</b><br/>";
			foreach ($order->get_items() as $item) {
				$product_details .= $i.'. ';
				$product_details .= $item->get_name();
				$product_details .= '   '.$this->helper->format_price($order->get_item_total( $item ),$order_id).'x';
				$product_details .= $item->get_quantity().'=';
				$product_details .= $this->helper->format_price($item->get_total(),$order_id);
				$product_details .= "<br/>";
				$i = $i+1;
			}
			
			$total = "<br/><b>"."Total: "."</b>".$order->get_currency() . $order->get_total()."<br/>";
			
			$order_billing_address = "<br/><b>"."Billing Address:"."</b><br/>".$order->get_formatted_billing_address();
		    $order_billing_address .= "<br/>".'Mobile: '.$order->get_billing_phone();
		    $order_billing_address .= "<br/>".'Email: '.$order->get_billing_email();
		    $order_billing_address .="<br/>";
		    $order_shipping_address = "<br/><b>"."Shipping Address:"."</b><br/>".$order->get_formatted_shipping_address()."<br/>";
		    
		    $payment_method = "<br/><b>"."Payment Method: "."</b>".$order->get_payment_method_title()."<br/>";
		    
		    $customer_note = "<br/><b>"."Customer Note: "."</b>".$order->get_customer_note();
		    
		    $description = $delivery_details.$product_details.$total.$order_billing_address.$order_shipping_address.$payment_method.$customer_note;
		    
		    $service = new Google_Service_Calendar($client);
    	    
    	    $event = new Google_Service_Calendar_Event(array(
              'id' => 'order'.$order_id,
              'summary' => $summary,
              'location' => $location,
              'description' => $description,
              'start' => array(
                $dateCriteria => $start,
                'timeZone' => $timezone,
              ),
              'end' => array(
                $dateCriteria => $end,
                'timeZone' => $timezone,
              ),
              'reminders' => array(
                'useDefault' => FALSE,
                'overrides' => array(
                  array('method' => 'email', 'minutes' => 12 * 60),
                  array('method' => 'popup', 'minutes' => 60),
                ),
              ),
            ));
            
            $calendarId = isset($google_calendar_settings['google_calendar_id']) && !empty($google_calendar_settings['google_calendar_id']) ? $google_calendar_settings['google_calendar_id'] : 'primary';
            $event = $service->events->insert($calendarId, $event);
		}*/
		
	}

	public function add_custom_notice_minimum_amount( ){

	    $cart_total = $this->helper->cart_total();

	    $currency_symbol = get_woocommerce_currency_symbol();

	    $delivery_option_settings = get_option('coderockz_woo_delivery_option_delivery_settings'); 

		$enable_delivery_restriction = (isset($delivery_option_settings['enable_delivery_restriction']) && !empty($delivery_option_settings['enable_delivery_restriction'])) ? $delivery_option_settings['enable_delivery_restriction'] : false;
		$minimum_amount = (isset($delivery_option_settings['minimum_amount_cart_restriction']) && $delivery_option_settings['minimum_amount_cart_restriction'] != "") ? (float)$delivery_option_settings['minimum_amount_cart_restriction'] : "";

		$minimum_cart_amount_notice = (isset($delivery_option_settings['delivery_restriction_notice']) && $delivery_option_settings['delivery_restriction_notice'] != "") ? $delivery_option_settings['delivery_restriction_notice'] : __( "Your cart amount must be", 'coderockz-woo-delivery' )." ".$this->helper->postion_currency_symbol($currency_symbol,$minimum_amount)." ".__( "to get the Delivery Option", 'coderockz-woo-delivery' );

	    if( $enable_delivery_restriction && $minimum_amount != "" && $cart_total['delivery'] < $minimum_amount){
	        /*wc_clear_notices();*/
	        wc_add_notice( __($minimum_cart_amount_notice, 'coderockz-woo-delivery'), 'notice');
	    }


	    $enable_pickup_restriction = (isset($delivery_option_settings['enable_pickup_restriction']) && !empty($delivery_option_settings['enable_pickup_restriction'])) ? $delivery_option_settings['enable_pickup_restriction'] : false;
		$minimum_amount_pickup = (isset($delivery_option_settings['minimum_amount_cart_restriction_pickup']) && $delivery_option_settings['minimum_amount_cart_restriction_pickup'] != "") ? (float)$delivery_option_settings['minimum_amount_cart_restriction_pickup'] : "";

		$minimum_cart_amount_notice_pickup = (isset($delivery_option_settings['pickup_restriction_notice']) && $delivery_option_settings['pickup_restriction_notice'] != "") ? $delivery_option_settings['pickup_restriction_notice'] : __( "Your cart amount must be", 'coderockz-woo-delivery' )." ".$this->helper->postion_currency_symbol($currency_symbol,$minimum_amount_pickup)." ".__( "to get the Pickup Option", 'coderockz-woo-delivery' );

	    if( $enable_pickup_restriction && $minimum_amount_pickup != "" && $cart_total['pickup'] < $minimum_amount_pickup){
	        /*wc_clear_notices();*/
	        wc_add_notice( __($minimum_cart_amount_notice_pickup, 'coderockz-woo-delivery'), 'notice');
	    }


		$enable_free_shipping_restriction = (isset($delivery_option_settings['enable_free_shipping_restriction']) && !empty($delivery_option_settings['enable_free_shipping_restriction'])) ? $delivery_option_settings['enable_free_shipping_restriction'] : false;
		$minimum_amount_free_shipping = (isset($delivery_option_settings['minimum_amount_shipping_restriction']) && $delivery_option_settings['minimum_amount_shipping_restriction'] != "") ? (float)$delivery_option_settings['minimum_amount_shipping_restriction'] : "";
		$minimum_cart_amount_notice_free_shipping = (isset($delivery_option_settings['free_shipping_restriction_notice']) && $delivery_option_settings['free_shipping_restriction_notice'] != "") ? $delivery_option_settings['free_shipping_restriction_notice'] : __( "Your cart amount must be", 'coderockz-woo-delivery' )." ".$this->helper->postion_currency_symbol($currency_symbol,$minimum_amount_free_shipping)." ".__( "to get the Free Shipping", 'coderockz-woo-delivery' );

	    if( $enable_free_shipping_restriction && $minimum_amount_free_shipping != "" && $cart_total['delivery_free_shipping'] < $minimum_amount_free_shipping){
	    	/*wc_clear_notices();*/
	        wc_add_notice( __($minimum_cart_amount_notice_free_shipping, 'coderockz-woo-delivery'), 'notice');
	    }

	}

	public function coderockz_woo_delivery_get_available_shipping_methods() {

		$only_local_pickup = null;
		$no_local_pickup_with_other = null;
		$has_local_pickup_with_other = null;
		$shipping_methods = null;
		$dynamic_delivery_pickup_notice = "";

		$localization_settings = get_option('coderockz_woo_delivery_localization_settings');

		$delivery_option_settings = get_option('coderockz_woo_delivery_option_delivery_settings'); 

		$enable_dynamic_order_type = (isset($delivery_option_settings['enable_dynamic_order_type']) && !empty($delivery_option_settings['enable_dynamic_order_type'])) ? $delivery_option_settings['enable_dynamic_order_type'] : false;

		$dynamic_order_type_no_delivery = isset($delivery_option_settings['dynamic_order_type_no_delivery']) && $delivery_option_settings['dynamic_order_type_no_delivery'] != "" ? stripslashes($delivery_option_settings['dynamic_order_type_no_delivery']) : "";
		$dynamic_order_type_no_pickup = isset($delivery_option_settings['dynamic_order_type_no_pickup']) && $delivery_option_settings['dynamic_order_type_no_pickup'] != "" ? stripslashes($delivery_option_settings['dynamic_order_type_no_pickup']) : "";
		$dynamic_order_type_no_delivery_pickup = isset($delivery_option_settings['dynamic_order_type_no_delivery_pickup']) && $delivery_option_settings['dynamic_order_type_no_delivery_pickup'] != "" ? stripslashes($delivery_option_settings['dynamic_order_type_no_delivery_pickup']) : "";

		if($enable_dynamic_order_type) {

			if(isset($_COOKIE['coderockz_woo_delivery_available_shipping_methods'])) {
			    $shipping_methods = json_decode(stripslashes($_COOKIE['coderockz_woo_delivery_available_shipping_methods']),true);
			} elseif(!is_null(WC()->session)) {		  
				$shipping_methods = WC()->session->get( 'coderockz_woo_delivery_available_shipping_methods' );  
			}

			if(!is_null($shipping_methods)) {
				$shipping_methods = array_unique($shipping_methods, false);
				$shipping_methods = array_values($shipping_methods);
			}
			

			if(is_array($shipping_methods)){
				if((in_array('local_pickup',$shipping_methods) && count($shipping_methods) >= 1)) {
					$has_local_pickup_with_other = true;
				}

				if((in_array('local_pickup',$shipping_methods) && count($shipping_methods) == 1)) {
					$only_local_pickup = true;
					$dynamic_delivery_pickup_notice = $dynamic_order_type_no_delivery;
				}

				
				if(!in_array('local_pickup',$shipping_methods) && count($shipping_methods) >= 1) {
					$no_local_pickup_with_other = true;
					$dynamic_delivery_pickup_notice = $dynamic_order_type_no_pickup;
				}

				if(count($shipping_methods) == 0) {
					$dynamic_delivery_pickup_notice = $dynamic_order_type_no_delivery_pickup;
				}
			}

		}

		if(isset($_COOKIE['coderockz_woo_delivery_available_shipping_methods'])) {
		    unset($_COOKIE["coderockz_woo_delivery_available_shipping_methods"]);
			setcookie("coderockz_woo_delivery_available_shipping_methods", null, -1, '/');
		} elseif(!is_null(WC()->session)) {		  
			WC()->session->__unset( 'coderockz_woo_delivery_available_shipping_methods' );  
		}


		$cart_total = $this->helper->cart_total();

		$response = [
			"shipping_methods" => $shipping_methods,
			"has_local_pickup_with_other" => $has_local_pickup_with_other,
			"only_local_pickup" => $only_local_pickup,
			"no_local_pickup_with_other" => $no_local_pickup_with_other,
			"dynamic_delivery_pickup_notice" => __($dynamic_delivery_pickup_notice, 'coderockz-woo-delivery'),
			"cart_total" => $cart_total
		];
		$response = json_encode($response);
		wp_send_json_success($response);
	}

	public function coderockz_woo_delivery_disable_conditional_delivery_for_no_conditional_shipping_method() {

		$find_conditional_shipping_method = false;
		if(isset($_COOKIE['coderockz_woo_delivery_find_conditional_shipping_method'])) {

		    $find_conditional_shipping_method = $_COOKIE['coderockz_woo_delivery_find_conditional_shipping_method'];

		    if($find_conditional_shipping_method === "true") {
		    	$find_conditional_shipping_method = true;
		    } else {
		    	$find_conditional_shipping_method =false;
		    }
		    
		} elseif(!is_null(WC()->session)) {		  
			$find_conditional_shipping_method = WC()->session->get( 'coderockz_woo_delivery_find_conditional_shipping_method' ); 
			if($find_conditional_shipping_method === "true") {
		    	$find_conditional_shipping_method = true;
		    } else {
		    	$find_conditional_shipping_method = false;
		    } 
		}



		$find_only_conditional_shipping_method = false;
		if(isset($_COOKIE['coderockz_woo_delivery_find_only_conditional_shipping_method'])) {
		    $find_only_conditional_shipping_method = $_COOKIE['coderockz_woo_delivery_find_only_conditional_shipping_method'];
		    if($find_only_conditional_shipping_method === "true") {
		    	$find_only_conditional_shipping_method = true;
		    } else {
		    	$find_only_conditional_shipping_method = false;
		    }
		} elseif(!is_null(WC()->session)) {		  
			$find_only_conditional_shipping_method = WC()->session->get( 'coderockz_woo_delivery_find_only_conditional_shipping_method' );
			if($find_only_conditional_shipping_method === "true") {
		    	$find_only_conditional_shipping_method = true;
		    } else {
		    	$find_only_conditional_shipping_method = false;
		    }  
		}

		$find_only_conditional_shipping_method_state = false;
		if(isset($_COOKIE['coderockz_woo_delivery_find_only_conditional_shipping_method_state'])) {
		    $find_only_conditional_shipping_method_state = $_COOKIE['coderockz_woo_delivery_find_only_conditional_shipping_method_state'];
		    if($find_only_conditional_shipping_method_state === "true") {
		    	$find_only_conditional_shipping_method_state = true;
		    } else {
		    	$find_only_conditional_shipping_method_state = false;
		    }
		} elseif(!is_null(WC()->session)) {		  
			$find_only_conditional_shipping_method_state = WC()->session->get( 'coderockz_woo_delivery_find_only_conditional_shipping_method_state' );
			if($find_only_conditional_shipping_method_state === "true") {
		    	$find_only_conditional_shipping_method_state = true;
		    } else {
		    	$find_only_conditional_shipping_method_state = false;
		    }  
		}


		if(isset($_COOKIE['coderockz_woo_delivery_find_conditional_shipping_method'])) {
		    unset($_COOKIE["coderockz_woo_delivery_find_conditional_shipping_method"]);
			setcookie("coderockz_woo_delivery_find_conditional_shipping_method", null, -1, '/');
		} elseif(!is_null(WC()->session)) {		  
			WC()->session->__unset( 'coderockz_woo_delivery_find_conditional_shipping_method' );  
		}

		if(isset($_COOKIE['coderockz_woo_delivery_find_only_conditional_shipping_method'])) {
		    unset($_COOKIE["coderockz_woo_delivery_find_only_conditional_shipping_method"]);
			setcookie("coderockz_woo_delivery_find_only_conditional_shipping_method", null, -1, '/');
		} elseif(!is_null(WC()->session)) {		  
			WC()->session->__unset( 'coderockz_woo_delivery_find_only_conditional_shipping_method' );  
		}

		if(isset($_COOKIE['coderockz_woo_delivery_find_only_conditional_shipping_method_state'])) {
		    unset($_COOKIE["coderockz_woo_delivery_find_only_conditional_shipping_method_state"]);
			setcookie("coderockz_woo_delivery_find_only_conditional_shipping_method_state", null, -1, '/');
		} elseif(!is_null(WC()->session)) {		  
			WC()->session->__unset( 'coderockz_woo_delivery_find_only_conditional_shipping_method_state' );  
		}

		$response = [
			"find_conditional_shipping_method" => $find_conditional_shipping_method,
			"find_only_conditional_shipping_method" => $find_only_conditional_shipping_method,
			"find_only_conditional_shipping_method_state" => $find_only_conditional_shipping_method_state,
		];
		$response = json_encode($response);
		wp_send_json_success($response);
	}

	public function coderockz_woo_delivery_load_custom_css() {
		if( is_checkout() && ! ( is_wc_endpoint_url( 'order-pay' ) || is_wc_endpoint_url( 'order-received' )) ){
			$other_settings = get_option('coderockz_woo_delivery_other_settings');
			$custom_css = isset($other_settings['custom_css']) && $other_settings['custom_css'] != "" ? stripslashes($other_settings['custom_css']) : "";
			$custom_css = wp_unslash($custom_css);
			echo '<style>' . $custom_css . '</style>';
		}
		
	}

	public function coderockz_woo_delivery_dynamic_update_order_type() {
		
		$delivery_option_settings = get_option('coderockz_woo_delivery_option_delivery_settings');
		
		$cart_total = $this ->helper->cart_total();
		$checkout_product_categories = $this ->helper->checkout_product_categories(true);
		$checkout_products = $this ->helper->checkout_product_id();
		$show_delivery = false;
		$show_pickup = false;
		$hide_delivery = false;
		$hide_pickup = false;
		$hide_for_product_categories = false;
		$hide_for_pickup_product_categories = false;

		if(isset($_COOKIE['coderockz_woo_delivery_current_cart_total'])) {
		    $current_cart_total = json_decode(stripslashes($_COOKIE['coderockz_woo_delivery_current_cart_total']),true);
		} elseif(!is_null(WC()->session)) {		  
			$current_cart_total = WC()->session->get( 'coderockz_woo_delivery_current_cart_total' );  
		}

		if(isset($_COOKIE['coderockz_woo_delivery_current_cart_category'])) {
		    $current_cart_category = json_decode(stripslashes($_COOKIE['coderockz_woo_delivery_current_cart_category']),true);
		} elseif(!is_null(WC()->session)) {		  
			$current_cart_category = WC()->session->get( 'coderockz_woo_delivery_current_cart_category' );  
		}

		if(isset($_COOKIE['coderockz_woo_delivery_current_cart_product'])) {
		    $current_cart_product = json_decode(stripslashes($_COOKIE['coderockz_woo_delivery_current_cart_product']),true);
		} elseif(!is_null(WC()->session)) {		  
			$current_cart_product = WC()->session->get( 'coderockz_woo_delivery_current_cart_product' );  
		}
		
		
		if(isset($current_cart_total)) {
			
			if($cart_total['delivery'] != $current_cart_total['delivery']) {
			
				$enable_delivery_restriction = (isset($delivery_option_settings['enable_delivery_restriction']) && !empty($delivery_option_settings['enable_delivery_restriction'])) ? $delivery_option_settings['enable_delivery_restriction'] : false;
			
				$minimum_amount = (isset($delivery_option_settings['minimum_amount_cart_restriction']) && $delivery_option_settings['minimum_amount_cart_restriction'] != "") ? (float)$delivery_option_settings['minimum_amount_cart_restriction'] : "";

				if( $enable_delivery_restriction && $minimum_amount != "" && $cart_total['delivery'] < $minimum_amount){
			    	$hide_delivery = true;
			    }

			}

			if($cart_total['pickup'] != $current_cart_total['pickup']) {

			    $enable_pickup_restriction = (isset($delivery_option_settings['enable_pickup_restriction']) && !empty($delivery_option_settings['enable_pickup_restriction'])) ? $delivery_option_settings['enable_pickup_restriction'] : false;
				$minimum_amount_pickup = (isset($delivery_option_settings['minimum_amount_cart_restriction_pickup']) && $delivery_option_settings['minimum_amount_cart_restriction_pickup'] != "") ? (float)$delivery_option_settings['minimum_amount_cart_restriction_pickup'] : "";

				if( $enable_pickup_restriction && $minimum_amount_pickup != "" && $cart_total['pickup'] < $minimum_amount_pickup){
			    	$hide_pickup = true;
			    }

			}

		}

		if(isset($current_cart_category)) {

			if(array_diff($checkout_product_categories,$current_cart_category) != array_diff($current_cart_category,$checkout_product_categories)) {

				$restrict_delivery_categories_array = (isset($delivery_option_settings['restrict_delivery_categories']) && !empty($delivery_option_settings['restrict_delivery_categories'])) ? $delivery_option_settings['restrict_delivery_categories'] : array();

				$restrict_delivery_products = (isset($delivery_option_settings['restrict_delivery_products']) && !empty($delivery_option_settings['restrict_delivery_products'])) ? $delivery_option_settings['restrict_delivery_products'] : array();

				$restrict_delivery_categories = [];

				foreach($restrict_delivery_categories_array as $restrict_delivery_category) {
					$restrict_delivery_categories [] = stripslashes($restrict_delivery_category);
				}		

				$hide_categories_condition = (count(array_intersect($checkout_product_categories, $restrict_delivery_categories)) <= count($checkout_product_categories)) && count(array_intersect($checkout_product_categories, $restrict_delivery_categories))>0;

				$hide_products_condition = (count(array_intersect($checkout_products, $restrict_delivery_products)) <= count($checkout_products)) && count(array_intersect($checkout_products, $restrict_delivery_products))>0;

				if($hide_categories_condition) {
					$hide_for_product_categories = true;
					if($delivery_option_settings['restrict_delivery_reverse_current_condition'] && count($checkout_product_categories) > 1 && count($checkout_products) > 1 && count(array_diff($checkout_product_categories, $restrict_delivery_categories))>0) {
		  				$hide_for_product_categories = !$hide_for_product_categories;
		  			}
				} elseif($hide_products_condition) {
					$hide_for_product_categories = true;
					if($delivery_option_settings['restrict_delivery_reverse_current_condition'] && count($checkout_products) > 1 && count(array_diff($checkout_products, $restrict_delivery_products))>0) {
		  				$hide_for_product_categories = !$hide_for_product_categories;
		  			}
				}
			
			}
		}

		if(!$hide_delivery && !$hide_for_product_categories) {
			$show_delivery = true;
		}
		
		if(isset($current_cart_product)) {

			if(array_diff($checkout_products,$current_cart_product) != array_diff($current_cart_product,$checkout_products)) {
			
				$restrict_pickup_categories = [];

				$restrict_pickup_categories_array = (isset($delivery_option_settings['restrict_pickup_categories']) && !empty($delivery_option_settings['restrict_pickup_categories'])) ? $delivery_option_settings['restrict_pickup_categories'] : array();

				foreach($restrict_pickup_categories_array as $restrict_pickup_category) {
					$restrict_pickup_categories [] = stripslashes($restrict_pickup_category);
				}

				$restrict_pickup_products = (isset($delivery_option_settings['restrict_pickup_products']) && !empty($delivery_option_settings['restrict_pickup_products'])) ? $delivery_option_settings['restrict_pickup_products'] : array();

				$hide_pickup_categories_condition = (count(array_intersect($checkout_product_categories, $restrict_pickup_categories)) <= count($checkout_product_categories)) && count(array_intersect($checkout_product_categories, $restrict_pickup_categories))>0;

				$hide_pickup_products_condition = (count(array_intersect($checkout_products, $restrict_pickup_products)) <= count($checkout_products)) && count(array_intersect($checkout_products, $restrict_pickup_products))>0;

				if($hide_pickup_categories_condition) {
					$hide_for_pickup_product_categories = true;
					if($delivery_option_settings['restrict_pickup_reverse_current_condition'] && count($checkout_product_categories) > 1 && count($checkout_products) > 1) {
		  				$hide_for_pickup_product_categories = !$hide_for_pickup_product_categories;
		  			}
				} elseif($hide_pickup_products_condition) {
					$hide_for_pickup_product_categories = true;
					if($delivery_option_settings['restrict_pickup_reverse_current_condition'] && count($checkout_products) > 1) {
		  				$hide_for_pickup_product_categories = !$hide_for_pickup_product_categories;
		  			}
				}

			}

		}


		if(!$hide_pickup && !$hide_for_pickup_product_categories) {
			$show_pickup = true;
		}

		setcookie("coderockz_woo_delivery_current_cart_total", json_encode($cart_total));
		WC()->session->set( 'coderockz_woo_delivery_current_cart_total', $cart_total );
		setcookie("coderockz_woo_delivery_current_cart_category", json_encode($checkout_product_categories));
		WC()->session->set( 'coderockz_woo_delivery_current_cart_category', $checkout_product_categories );
		setcookie("coderockz_woo_delivery_current_cart_product", json_encode($checkout_products));
		WC()->session->set( 'coderockz_woo_delivery_current_cart_product', $checkout_products );

		$response = [
			"show_delivery" => $show_delivery,
			"show_pickup" => $show_pickup,
		];
		$response = json_encode($response);
		wp_send_json_success($response);

	}

	public function dokan_checkout_update_order_meta($order_id,$seller_id) {
		$sub_order = wc_get_order($order_id);
		$parent_order_id = $sub_order->get_parent_id();
		if(metadata_exists('post', $parent_order_id, 'delivery_type') && get_post_meta($parent_order_id, 'delivery_type', true) !="") {
			update_post_meta($order_id, 'delivery_type', get_post_meta($parent_order_id, 'delivery_type', true));
		}
		if(metadata_exists('post', $parent_order_id, 'delivery_date') && get_post_meta($parent_order_id, 'delivery_date', true) !="") {
			update_post_meta($order_id, 'delivery_date', get_post_meta($parent_order_id, 'delivery_date', true));
		}
		if(metadata_exists('post', $parent_order_id, 'delivery_time') && get_post_meta($parent_order_id, 'delivery_time', true) !="") {
			update_post_meta($order_id, 'delivery_time', get_post_meta($parent_order_id, 'delivery_time', true));
		}
		if(metadata_exists('post', $parent_order_id, 'pickup_date') && get_post_meta($parent_order_id, 'pickup_date', true) !="") {
			update_post_meta($order_id, 'pickup_date', get_post_meta($parent_order_id, 'pickup_date', true));
		}
		if(metadata_exists('post', $parent_order_id, 'pickup_time') && get_post_meta($parent_order_id, 'pickup_time', true) !="") {
			update_post_meta($order_id, 'pickup_time', get_post_meta($parent_order_id, 'pickup_time', true));
		}
		if(metadata_exists('post', $parent_order_id, 'delivery_pickup') && get_post_meta($parent_order_id, 'delivery_pickup', true) !="") {
			update_post_meta($order_id, 'delivery_pickup', get_post_meta($parent_order_id, 'delivery_pickup', true));
		}
		if(metadata_exists('post', $parent_order_id, 'additional_note') && get_post_meta($parent_order_id, 'additional_note', true) !="") {
			update_post_meta($order_id, 'additional_note', get_post_meta($parent_order_id, 'additional_note', true));
		}
			
	}

	public function coderockz_woo_delivery_add_google_calendar_btn( $text, $order ) {
	    
		$google_calendar_settings = get_option('coderockz_woo_delivery_google_calendar_settings');

		$calendar_sync_customer = isset($google_calendar_settings['google_calendar_customer_sync']) && !empty($google_calendar_settings['google_calendar_customer_sync']) ? $google_calendar_settings['google_calendar_customer_sync'] : false;

		$calendar_sync_customer_client_id = isset($google_calendar_settings['google_calendar_client_id']) && !empty($google_calendar_settings['google_calendar_client_id']) ? $google_calendar_settings['google_calendar_client_id'] : "";
		
		$calendar_sync_customer_client_secret = isset($google_calendar_settings['google_calendar_client_secret']) && !empty($google_calendar_settings['google_calendar_client_secret']) ? $google_calendar_settings['google_calendar_client_secret'] : "";

		$google_calendar_order_received_page_btn_txt = isset($google_calendar_settings['google_calendar_order_received_page_btn_txt']) && !empty($google_calendar_settings['google_calendar_order_received_page_btn_txt']) ? $google_calendar_settings['google_calendar_order_received_page_btn_txt'] : __("Add to Google Calendar","coderockz-woo-delivery");

		$google_calendar_order_added_page_btn_txt = isset($google_calendar_settings['google_calendar_order_added_page_btn_txt']) && !empty($google_calendar_settings['google_calendar_order_added_page_btn_txt']) ? $google_calendar_settings['google_calendar_order_added_page_btn_txt'] : __("Successfully Added","coderockz-woo-delivery");
		
		$calendar_sync_customer_redirect_url = wc_get_checkout_url().'order-received/';

		if($calendar_sync_customer && $calendar_sync_customer_client_id != "" && $calendar_sync_customer_client_secret != "") {
		    
		    $client = new Google_Client();
            $client->setClientId($calendar_sync_customer_client_id);
            $client->setClientSecret($calendar_sync_customer_client_secret);
            $client->setRedirectUri($calendar_sync_customer_redirect_url);
            $client->addScope("https://www.googleapis.com/auth/calendar.events");
            $client->setAccessType('online');
            //$client->setApprovalPrompt("force");
            
            $auth_url = $client->createAuthUrl();
	    	
	    	if(isset($_GET['code'])) {
	    	    
	    	    $client->setAccessToken($client->fetchAccessTokenWithAuthCode($_GET['code']));
	    	    
	    	    $service = new Google_Service_Calendar($client);
	    	    
	    	    if(isset($_COOKIE['coderockz_woo_delivery_google_calendar_order_id'])) {
    			    $google_calendar_order_id = $_COOKIE['coderockz_woo_delivery_google_calendar_order_id'];
    			} elseif(!is_null(WC()->session)) {		  
    				$google_calendar_order_id = WC()->session->get( 'coderockz_woo_delivery_google_calendar_order_id' );  
    			}
    			
    			$order = wc_get_order($google_calendar_order_id);
    			
    			// if any timezone data is saved, set default timezone with the data
		        $timezone = $this->helper->get_the_timezone();
		        date_default_timezone_set($timezone);
    			
    			$delivery_option_settings = get_option('coderockz_woo_delivery_option_delivery_settings');
    			$delivery_date_settings = get_option('coderockz_woo_delivery_date_settings');			
            	$pickup_date_settings = get_option('coderockz_woo_delivery_pickup_date_settings');			
        		$delivery_time_settings = get_option('coderockz_woo_delivery_time_settings');
        		$pickup_time_settings = get_option('coderockz_woo_delivery_pickup_settings');
        		$delivery_pickup_settings = get_option('coderockz_woo_delivery_pickup_location_settings');
        		$additional_field_settings = get_option('coderockz_woo_delivery_additional_field_settings');
    			
    			$order_type_field_label = (isset($delivery_option_settings['delivery_option_label']) && !empty($delivery_option_settings['delivery_option_label'])) ? stripslashes($delivery_option_settings['delivery_option_label']) : "Order Type";
    			$delivery_field_label = (isset($delivery_option_settings['delivery_label']) && !empty($delivery_option_settings['delivery_label'])) ? stripslashes($delivery_option_settings['delivery_label']) : __("Delivery","coderockz-woo-delivery");
		        $pickup_field_label = (isset($delivery_option_settings['pickup_label']) && !empty($delivery_option_settings['pickup_label'])) ? stripslashes($delivery_option_settings['pickup_label']) : __("Pickup","coderockz-woo-delivery");
		        
		        $delivery_date_field_label = (isset($delivery_date_settings['field_label']) && !empty($delivery_date_settings['field_label'])) ? stripslashes($delivery_date_settings['field_label']) : __("Delivery Date","coderockz-woo-delivery");
        		$pickup_date_field_label = (isset($pickup_date_settings['pickup_field_label']) && !empty($pickup_date_settings['pickup_field_label'])) ? stripslashes($pickup_date_settings['pickup_field_label']) : __("Pickup Date","coderockz-woo-delivery");
        		$delivery_time_field_label = (isset($delivery_time_settings['field_label']) && !empty($delivery_time_settings['field_label'])) ? stripslashes($delivery_time_settings['field_label']) : __("Delivery Time","coderockz-woo-delivery");
        		$pickup_time_field_label = (isset($pickup_time_settings['field_label']) && !empty($pickup_time_settings['field_label'])) ? stripslashes($pickup_time_settings['field_label']) : __("Pickup Time","coderockz-woo-delivery");
        		$pickup_location_field_label = (isset($pickup_location_settings['field_label']) && !empty($pickup_location_settings['field_label'])) ? stripslashes($pickup_location_settings['field_label']) : __("Pickup Location","coderockz-woo-delivery");
        		$additional_field_label = (isset($additional_field_settings['field_label']) && !empty($additional_field_settings['field_label'])) ? $additional_field_settings['field_label'] : __("Special Note for Delivery","coderockz-woo-delivery");
    			
    			$delivery_date_format = (isset($delivery_date_settings['date_format']) && !empty($delivery_date_settings['date_format'])) ? $delivery_date_settings['date_format'] : "F j, Y";
		
        		$add_weekday_name = (isset($delivery_date_settings['add_weekday_name']) && !empty($delivery_date_settings['add_weekday_name'])) ? $delivery_date_settings['add_weekday_name'] : false;
        
        		if($add_weekday_name) {
        			$delivery_date_format = "l ".$delivery_date_format;
        		}
        
        		$pickup_date_format = (isset($pickup_date_settings['date_format']) && !empty($pickup_date_settings['date_format'])) ? $pickup_date_settings['date_format'] : "F j, Y";
        
        		$pickup_add_weekday_name = (isset($pickup_date_settings['add_weekday_name']) && !empty($pickup_date_settings['add_weekday_name'])) ? $pickup_date_settings['add_weekday_name'] : false;
        
        		if($pickup_add_weekday_name) {
        			$pickup_date_format = "l ".$pickup_date_format;
        		}
        
        		$time_format = (isset($delivery_time_settings['time_format']) && !empty($delivery_time_settings['time_format']))?$delivery_time_settings['time_format']:"12";
        		if($time_format == 12) {
        			$time_format = "h:i A";
        		} elseif ($time_format == 24) {
        			$time_format = "H:i";
        		}
        
        		$pickup_time_format = (isset($pickup_time_settings['time_format']) && !empty($pickup_time_settings['time_format']))?$pickup_time_settings['time_format']:"12";
        		if($pickup_time_format == 12) {
        			$pickup_time_format = "h:i A";
        		} elseif ($pickup_time_format == 24) {
        			$pickup_time_format = "H:i";
        		}
    			  			
    			if(metadata_exists('post', $google_calendar_order_id, 'delivery_type') && get_post_meta($google_calendar_order_id, 'delivery_type', true) !="") {
	    	

        	    	if(get_post_meta($google_calendar_order_id, 'delivery_type', true) == "delivery") {
        
        	    		 $delivery_type = $delivery_field_label;
        
        			} elseif(get_post_meta($google_calendar_order_id, 'delivery_type', true) == "pickup") {
        				
        				$delivery_type = $pickup_field_label;
        			} else {
        			    $delivery_type = "";
        			}
        
        	    }
    			
    			if(metadata_exists('post', $google_calendar_order_id, '_wcj_order_number') && get_post_meta($google_calendar_order_id, '_wcj_order_number', true) !="") {
    				$order_id_with_custom = '#'.get_post_meta($google_calendar_order_id, '_wcj_order_number', true);
    			} elseif(class_exists('WC_SeqOrder_Order_Number') || class_exists('WC_Seq_Order_Number_Pro')) {
    				$order_id_with_custom = '#'.$order->get_order_number();
    			} else {
    				$order_id_with_custom = '#'.$order->get_id();
    			}
                
                if(metadata_exists('post', $order->get_id(), 'delivery_time') && get_post_meta($order->get_id(), 'delivery_time', true) !="" && get_post_meta($order->get_id(), 'delivery_time', true) =="as-soon-as-possible") {
                    $as_soon_as_possible_text = (isset($delivery_time_settings['as_soon_as_possible_text']) && !empty($delivery_time_settings['as_soon_as_possible_text'])) ? stripslashes($delivery_time_settings['as_soon_as_possible_text']) : __("As Soon As Possible", 'coderockz-woo-delivery');
                    $summary = $delivery_type.$order_id_with_custom."(".$as_soon_as_possible_text.")";
                } else {
                    $summary = $delivery_type.$order_id_with_custom;
                }
                               
                if(metadata_exists('post', $order->get_id(), 'delivery_date') && get_post_meta($order->get_id(), 'delivery_date', true) !="") {

			    	$date = get_post_meta( $order->get_id(), 'delivery_date', true );

			    }

			    if(metadata_exists('post', $order->get_id(), 'pickup_date') && get_post_meta($order->get_id(), 'pickup_date', true) !="") {

			    	$date = get_post_meta( $order->get_id(), 'pickup_date', true ); 

			    }

			    if(metadata_exists('post', $order->get_id(), 'delivery_time') && get_post_meta($order->get_id(), 'delivery_time', true) !="") {

			    	if(get_post_meta($order->get_id(), 'delivery_time', true) != "as-soon-as-possible" && get_post_meta($order->get_id(), 'delivery_time', true) != "conditional-delivery") {
				    	$minutes = get_post_meta($order->get_id(),"delivery_time",true);
				    	$minutes = explode(' - ', $minutes);

			    		if(!isset($minutes[1])) {
			    			$time_start = "T".date("H:i", strtotime($minutes[0])).':00';
			    			$time_end = "T".date("H:i", strtotime($minutes[0])).':00';
			    		} else {

			    			$time_start = "T".date("H:i", strtotime($minutes[0])).':00';
			    			$time_end = "T".date("H:i", strtotime($minutes[1])).':00'; 			
			    		}
		    		} elseif(get_post_meta($order->get_id(), 'delivery_time', true) == "conditional-delivery" ) {
		    		    $delivery_fee_settings = get_option('coderockz_woo_delivery_fee_settings');
					    $minutes = date($time_format, mktime(0, (int)((date("G")*60)+date("i")))) . " - ".date($time_format, mktime(0, (int)((date("G")*60)+date("i") + $delivery_fee_settings['conditional_delivery_fee_duration']))); 
					    $minutes = explode(' - ', $minutes);

			    		if(!isset($minutes[1])) {
			    			$time_start = "T".date("H:i", strtotime($minutes[0])).':00';
			    			$time_end = "T".date("H:i", strtotime($minutes[0])).':00';
			    		} else {

			    			$time_start = "T".date("H:i", strtotime($minutes[0])).':00';
			    			$time_end = "T".date("H:i", strtotime($minutes[1])).':00'; 			
			    		}
		    		}
			    	
			    }

			    if(metadata_exists('post', $order->get_id(), 'pickup_time') && get_post_meta($order->get_id(), 'pickup_time', true) !="") {
			    	$pickup_minutes = get_post_meta($order->get_id(),"pickup_time",true);
			    	$pickup_minutes = explode(' - ', $pickup_minutes);

			    	if(!isset($pickup_minutes[1])) {
		    			$time_start = "T".date("H:i", strtotime($pickup_minutes[0])).':00';
		    			$time_end = "T".date("H:i", strtotime($pickup_minutes[0])).':00';
		    		} else {

		    			$time_start = "T".date("H:i", strtotime($pickup_minutes[0])).':00';
		    			$time_end = "T".date("H:i", strtotime($pickup_minutes[1])).':00'; 			
		    		}
			    	
			    }
			    
			    if(metadata_exists('post', $order->get_id(), 'delivery_pickup') && get_post_meta($order->get_id(), 'delivery_pickup', true) !="") {
    				$location = get_post_meta($order->get_id(), 'delivery_pickup', true);
    			} else {
    			    $location = "";
    			}
    			
    			
    			$start = "";
    			$end = "";
    			if(isset($date)) {
    			    $start .= $date;
    			    $end .= $date;
    			} else {
    			    $order_created_obj= new DateTime($order->get_date_created());
    			    $start .= $order_created_obj->format("Y-m-d");
    			    $end .= $order_created_obj->format("Y-m-d");
    			}
    			
    			if(isset($time_start)) {
    			    $start .= $time_start;
    			    $dateCriteria = 'dateTime';
    			} else {
    			   $dateCriteria = 'date'; 
    			}
    			
    			if(isset($time_end)) {
    			    $end .= $time_end;
    			    $dateCriteria = 'dateTime';
    			} else {
    			   $dateCriteria = 'date'; 
    			}
    			    			
    			$delivery_details = "<b>"."Delivery Details:"."</b><br/>";
    			if($delivery_type != "") {
				    $delivery_details .= $order_type_field_label.': ' . $delivery_type . "<br/>";
				}
    		    if(metadata_exists('post', $order->get_id(), 'delivery_date') && get_post_meta($order->get_id(), 'delivery_date', true) !="") {
    
    		    	$delivery_details .= $delivery_date_field_label.': ' . date($delivery_date_format, strtotime(get_post_meta( $order->get_id(), 'delivery_date', true ))) . "<br/>";
    
    		    }
    
    		    if(metadata_exists('post', $order->get_id(), 'pickup_date') && get_post_meta($order->get_id(), 'pickup_date', true) !="") {
    
    		    	$delivery_details .= $pickup_date_field_label.': ' . date($pickup_date_format, strtotime(get_post_meta( $order->get_id(), 'pickup_date', true ))) . "<br/>"; 
    
    		    }
    
    		    if(metadata_exists('post', $order->get_id(), 'delivery_time') && get_post_meta($order->get_id(), 'delivery_time', true) !="") {
    
    		    	if(get_post_meta($order->get_id(), 'delivery_time', true) !="as-soon-as-possible" && get_post_meta($order->get_id(), 'delivery_time', true) !="conditional-delivery") {
    			    	$minutes = get_post_meta($order->get_id(),"delivery_time",true);
    			    	$minutes = explode(' - ', $minutes);
    
    		    		if(!isset($minutes[1])) {
    		    			$delivery_details .= $delivery_time_field_label.': ' . date($time_format, strtotime($minutes[0])) . "<br/>";
    		    		} else {
    
    		    			$delivery_details .= $delivery_time_field_label.': ' . date($time_format, strtotime($minutes[0])) . ' - ' . date($time_format, strtotime($minutes[1])) . "<br/>";  			
    		    		}
    	    		} elseif (get_post_meta($order->get_id(), 'delivery_time', true) == "conditional-delivery") {
    	    		    $delivery_fee_settings = get_option('coderockz_woo_delivery_fee_settings');
					    $minutes = date($time_format, mktime(0, (int)((date("G")*60)+date("i")))) . " - ".date($time_format, mktime(0, (int)((date("G")*60)+date("i") + $delivery_fee_settings['conditional_delivery_fee_duration'])));
					    $minutes = explode(' - ', $minutes);
    
    		    		if(!isset($minutes[1])) {
    		    			$delivery_details .= $delivery_time_field_label.': ' . date($time_format, strtotime($minutes[0])) . "<br/>";
    		    		} else {
    
    		    			$delivery_details .= $delivery_time_field_label.': ' . date($time_format, strtotime($minutes[0])) . ' - ' . date($time_format, strtotime($minutes[1])) . "<br/>";  			
    		    		}
    	    		} else {
    	    			$as_soon_as_possible_text = (isset($delivery_time_settings['as_soon_as_possible_text']) && !empty($delivery_time_settings['as_soon_as_possible_text'])) ? stripslashes($delivery_time_settings['as_soon_as_possible_text']) : "As Soon As Possible";
    	    			$delivery_details .= $delivery_time_field_label.': ' . $as_soon_as_possible_text . "<br/>";
    	    		}
    		    	
    		    }
    
    		    if(metadata_exists('post', $order->get_id(), 'pickup_time') && get_post_meta($order->get_id(), 'pickup_time', true) !="") {
    		    	$pickup_minutes = get_post_meta($order->get_id(),"pickup_time",true);
    		    	$pickup_minutes = explode(' - ', $pickup_minutes);
    
    	    		if(!isset($pickup_minutes[1])) {
    	    			$delivery_details .= $pickup_time_field_label.': ' . date($pickup_time_format, strtotime($pickup_minutes[0])) . "<br/>";
    	    		} else {
    
    	    			$delivery_details .= $pickup_time_field_label.': ' . date($pickup_time_format, strtotime($pickup_minutes[0])) . ' - ' . date($pickup_time_format, strtotime($pickup_minutes[1])) . "<br/>";  			
    	    		}
    		    	
    		    }
    
    		    if(metadata_exists('post', $order->get_id(), 'delivery_pickup') && get_post_meta($order->get_id(), 'delivery_pickup', true) !="") {
    				$delivery_details .= $pickup_location_field_label.': ' . get_post_meta($order->get_id(), 'delivery_pickup', true) . "<br/>";
    			}
    
    			if(metadata_exists('post', $order->get_id(), 'additional_note') && get_post_meta($order->get_id(), 'additional_note', true) !="") {
    				$delivery_details .= $additional_field_label.': ' . get_post_meta($order->get_id(), 'additional_note', true);
    			}
    			
    			$i=1;
    			$product_details = "<br/><b>"."Products:"."</b><br/>";
    			foreach ($order->get_items() as $item) {
    				$product_details .= $i.'. ';
    				$product_details .= $item->get_name();
    				$product_details .= '   '.$this->helper->format_price($order->get_item_total( $item ),$order->get_id()).'x';
    				$product_details .= $item->get_quantity().'=';
    				$product_details .= $this->helper->format_price($item->get_total(),$order->get_id());
    				$product_details .= "<br/>";
    				$i = $i+1;
    			}
    			
    			$total = "<br/><b>"."Total: "."</b>".$order->get_currency() . $order->get_total()."<br/>";
    			
    			$order_billing_address = "<br/><b>"."Billing Address:"."</b><br/>".$order->get_formatted_billing_address();
			    $order_billing_address .= "<br/>".'Mobile: '.$order->get_billing_phone();
			    $order_billing_address .= "<br/>".'Email: '.$order->get_billing_email();
			    $order_billing_address .="<br/>";
			    $order_shipping_address = "<br/><b>"."Shipping Address:"."</b><br/>".$order->get_formatted_shipping_address()."<br/>";
			    
			    $payment_method = "<br/><b>"."Payment Method: "."</b>".$order->get_payment_method_title()."<br/>";
			    
			    $customer_note = "<br/><b>"."Customer Note: "."</b>".$order->get_customer_note();
			    
			    $description = $delivery_details.$product_details.$total.$order_billing_address.$order_shipping_address.$payment_method.$customer_note;
	    	    
	    	    $event = new Google_Service_Calendar_Event(array(
	    	      'id' => 'order'.$order_id,
                  'summary' => $summary,
                  'location' => $location,
                  'description' => $description,
                  'start' => array(
                    $dateCriteria => $start,
                    'timeZone' => $timezone,
                  ),
                  'end' => array(
                    $dateCriteria => $end,
                    'timeZone' => $timezone,
                  ),
                  'reminders' => array(
                    'useDefault' => FALSE,
                    'overrides' => array(
                      array('method' => 'email', 'minutes' => 12 * 60),
                      array('method' => 'popup', 'minutes' => 60),
                    ),
                  ),
                ));
                
                $calendarId = 'primary';
                $event = $service->events->insert($calendarId, $event);
		        
		        if(isset($_COOKIE['coderockz_woo_delivery_google_calendar_current_page_url'])) {
    			    $redirect_url = $_COOKIE['coderockz_woo_delivery_google_calendar_current_page_url'];
    			} elseif(!is_null(WC()->session)) {		  
    				$redirect_url = WC()->session->get( 'coderockz_woo_delivery_google_calendar_current_page_url' );  
    			}
                
                wp_redirect( $redirect_url );
                exit;
	    	    
	    	} else {
	 		    
			    $current_page_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] .  $_SERVER['REQUEST_URI'];
			    
			    if(isset($_COOKIE['coderockz_woo_delivery_google_calendar_current_page_url'])) {
    			    $redirect_url = $_COOKIE['coderockz_woo_delivery_google_calendar_current_page_url'];
    			} elseif(!is_null(WC()->session)) {		  
    				$redirect_url = WC()->session->get( 'coderockz_woo_delivery_google_calendar_current_page_url' );  
    			}
    			
    			if($current_page_url == $redirect_url) {
    			    $out = '<div style="overflow:hidden"><button style="font-weight: 700 !important;background: #249D60;background: -moz-linear-gradient(left, #249D60 0%, #247552 100%, #C59237 100%);background: -webkit-linear-gradient(left, #249D60 0%, #247552 100%, #C59237 100%);background: linear-gradient(to right, #249D60 0%, #247552 100%, #C59237 100%);border: 0;outline: 0;padding: 12px 10px;color: #fff;border-radius: 4px;display: block;text-shadow: -1px -1px 1px rgba(255,255,255,.1), 1px 1px 1px rgba(0,0,0,.5);cursor: pointer;text-align: center;float:left;margin-bottom: 20px;text-transform:capitalize;">'.__($google_calendar_order_added_page_btn_txt, "coderockz-woo-delivery").'</button></div>';
    			    
    			    unset($_COOKIE["coderockz_woo_delivery_google_calendar_order_id"]);
			        setcookie("coderockz_woo_delivery_google_calendar_order_id", null, -1, '/');
			        WC()->session->__unset( 'coderockz_woo_delivery_google_calendar_order_id' );
    			    
    			    unset($_COOKIE["coderockz_woo_delivery_google_calendar_current_page_url"]);
			        setcookie("coderockz_woo_delivery_google_calendar_current_page_url", null, -1, '/');
			        WC()->session->__unset( 'coderockz_woo_delivery_google_calendar_current_page_url' );
    			    
    			} else {
    			       			   
    			   $current_page_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] .  $_SERVER['REQUEST_URI'];
    			   
    			   setcookie("coderockz_woo_delivery_google_calendar_current_page_url", $current_page_url);
		            WC()->session->set( 'coderockz_woo_delivery_google_calendar_current_page_url', $current_page_url );
    			   
    			   
    			    if( version_compare( get_option( 'woocommerce_version' ), '3.0.0', ">=" ) ) {            
            	        $order_id = $order->get_id();
            	    } else {
            	        $order_id = $order->id;
            	    }
		
    			    setcookie("coderockz_woo_delivery_google_calendar_order_id", $order_id);
		            WC()->session->set( 'coderockz_woo_delivery_google_calendar_order_id', $order_id );
		        
    			    $out = '<div style="overflow:hidden"><a href="'.$auth_url.'" style="font-weight: 700 !important;background: #249D60;background: -moz-linear-gradient(left, #249D60 0%, #247552 100%, #C59237 100%);background: -webkit-linear-gradient(left, #249D60 0%, #247552 100%, #C59237 100%);background: linear-gradient(to right, #249D60 0%, #247552 100%, #C59237 100%);border: 0;outline: 0;padding: 5px 10px;color: #fff;border-radius: 4px;display: block;text-shadow: -1px -1px 1px rgba(255,255,255,.1), 1px 1px 1px rgba(0,0,0,.5);cursor: pointer;text-align: center;float:left;margin-bottom: 20px;">'.__($google_calendar_order_received_page_btn_txt, "coderockz-woo-delivery").'</a></div>';
    			}
	    	    
	    	}

		} else {
			$out = "";
		}
		
	    return $out . ' ' . $text;
	}


}

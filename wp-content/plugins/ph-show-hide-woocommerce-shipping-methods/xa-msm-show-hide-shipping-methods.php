<?php
/*
Plugin Name: Hide Shipping Methods based on Shipping Class and Zone
Plugin URI: https://www.xadapter.com/shop/
Description: Display or hide various shipping options provided by various shipping plugins. It also manages free shipping and flat rate offered by woocommerce.
Version: 1.0.8
Author: PluginHive
Author URI: https://www.pluginhive.com/
WC requires at least: 3.0.0
WC tested up to: 5.5.1

*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if( ! defined('XA_MSM_DEBUG') ){
	define("XA_MSM_DEBUG", "off"); // Turn 'on' to allow advanced debug mode.
}

//check if woocommerce exists
if ( ! class_exists( 'woocommerce' ) ) {
	add_action( 'admin_init', 'xa_msm_shipping_addon_plugin_deactivate' );
	if ( ! function_exists( 'xa_msm_shipping_addon_plugin_deactivate' ) ) {
		function xa_msm_shipping_addon_plugin_deactivate() {
			if ( !class_exists( 'woocommerce' ) ){
				deactivate_plugins( plugin_basename( __FILE__ ) );
				wp_safe_redirect( admin_url('plugins.php') );
			}
		}
	}
}


//Class - To setup the plugin
if( ! class_exists('xa_msm_shipping_addon_Setup') )
{
	class xa_msm_shipping_addon_Setup {

		//constructor
		public function __construct() {
			add_action( 'woocommerce_get_settings_pages',array($this, 'xa_msm_show_hide_initialize') );
			$this->xa_msm_shipping_addon_init();
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'xa_msm_shipping_addon_plugin_action_links' ) );
		}


		public function xa_msm_get_settings_url(){
			return version_compare(WC()->version, '1.0', '>=') ? "wc-settings" : "woocommerce_settings";
		}

		//to add settings url near plugin under installed plugin
		public function xa_msm_shipping_addon_plugin_action_links( $links ) {
			$plugin_links = array(
				'<a href="' . admin_url( 'admin.php?page=' . $this->xa_msm_get_settings_url() . '&tab=xa_manage_shipping_method' ) . '">' . __( 'Settings', 'xa_msm_hide_shipping_method' ) . '</a>',

			);
			return array_merge( $plugin_links, $links );
		}
		
		public function xa_msm_show_hide_initialize( $settings = array() ){
			include_once( 'includes/class-xa-msm-shipping-addon-settings.php' );

			return $settings;
		}

		//to include the necessary files for plugin
		public function xa_msm_shipping_addon_init() {
			include_once( 'includes/class-xa-msm-shipping-rates-processor.php' );
		}

	}
}
new xa_msm_shipping_addon_Setup();

// Plugin updater
require 'plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
	'https://bitbucket.org/pluginhive/ph-show-hide-woocommerce-shipping-methods/',
	__FILE__,
	'ph-show-hide-woocommerce-shipping-methods'
);

$myUpdateChecker->setAuthentication(array(
	'consumer_key' => 'nXGsrkFGPS86v9eHnK',
	'consumer_secret' => 'G5SHXQtbg8Yp9dpkuMhSmvLYQWkhG97x',
));
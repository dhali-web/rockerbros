<?php
/**
 * Common class.
 *
 * @since 1.0.0
 *
 * @package Envira_Defaults
 * @author  Envira Team
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Common class.
 *
 * @since 1.0.0
 *
 * @package Envira_Defaults
 * @author  Envira Team
 */
class Envira_Defaults_Common {

	/**
	 * Holds the class object.
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	public static $instance;

	/**
	 * Path to the file.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $file = __FILE__;

	/**
	 * Holds the Envira Gallery Default ID.
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	public $gallery_default_id;

	/**
	 * Holds the Envira Album Default ID.
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	public $album_default_id;

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Get Envira Gallery and Album Default IDs.
		$this->gallery_default_id = get_option( 'envira_default_gallery' );
		$this->album_default_id   = get_option( 'envira_default_album' );

		// Filters.
		add_filter( 'envira_gallery_defaults', array( $this, 'get_gallery_config_defaults' ), 99, 2 );
		add_filter( 'envira_albums_defaults', array( $this, 'get_album_config_defaults' ), 99, 2 );

	}

	/**
	 * Retrieves the defaults gallery ID for holding default settings.
	 *
	 * @since 1.0.0
	 *
	 * @return int The post ID for the default settings.
	 */
	public function get_gallery_default_id() {

		return get_option( 'envira_default_gallery' );

	}

	/**
	 * Retrieves the default album ID for holding default settings.
	 *
	 * @since 1.0.0
	 *
	 * @return int The post ID for the default settings.
	 */
	public function get_album_default_id() {

		return get_option( 'envira_default_album' );

	}

	/**
	 * Overrides Envira Gallery config defaults with those stored in the Envira Gallery Default Post
	 *
	 * @since 1.0.0
	 *
	 * @param array $defaults Defaults.
	 * @param int   $post_id Post ID.
	 * @return array Defaults
	 */
	public function get_gallery_config_defaults( $defaults, $post_id ) {
		// Check if we're adding a new Gallery, and if a default gallery ID has been defined
		// If so, use that Gallery ID as the default.
		if ( is_admin() && function_exists( 'get_current_screen' ) ) {
			// Get screen.
			$screen = get_current_screen();

			if ( ! empty( $screen ) ) {
				if ( 'add' === $screen->action && 'envira' === $screen->post_type && isset( $_REQUEST['envira_defaults_config_id'] ) ) { // @codingStandardsIgnoreLine - nonce in querystring
					$this->gallery_default_id = absint( $_REQUEST['envira_defaults_config_id'] ); // @codingStandardsIgnoreLine - nonce in querystring
				}
			}
		}

		// Check Envira Gallery Defaults Post exists.
		if ( ! $this->gallery_default_id ) {
			return $defaults;
		}

		// Check we are not editing the Envira Gallery Defaults Post
		// If we are, we don't want to do anything right now.
		if ( $this->gallery_default_id === $post_id ) {
			return $defaults;
		}

		// Get the gallery chosen to inherit the configuration from.
		$default_gallery = Envira_Gallery::get_instance()->get_gallery( $this->gallery_default_id );
		if ( ! $default_gallery ) {
			return $defaults;
		}

		// Default Gallery exists - map its settings onto our defaults.
		$new_defaults = $default_gallery['config'];

		// Map the type back, so we don't end up creating another 'defaults' Gallery type.
		$new_defaults['type'] = $defaults['type'];

		// Unset some defaults that we don't want to copy to the new gallery, as these will break things.
		unset( $new_defaults['title'] );
		unset( $new_defaults['slug'] );

		// Return.
		return $new_defaults;

	}

	/**
	 * Overrides Envira Album config defaults with those stored in the Envira Album Default Post
	 *
	 * @since 1.0.0
	 *
	 * @param array $defaults Defaults.
	 * @param int   $post_id Post ID.
	 * @return array Defaults
	 */
	public function get_album_config_defaults( $defaults, $post_id ) {

		// Check if we're adding a new Album, and if a default album ID has been defined
		// If so, use that Album ID as the default.
		if ( is_admin() && function_exists( 'get_current_screen' ) ) {
			// Get screen.
			$screen = get_current_screen();

			if ( ! empty( $screen ) ) {
				if ( 'add' === $screen->action && 'envira_album' === $screen->post_type && isset( $_REQUEST['envira_defaults_config_id'] ) ) { // @codingStandardsIgnoreLine - nonce in querystring
					$this->album_default_id = absint( $_REQUEST['envira_defaults_config_id'] ); // @codingStandardsIgnoreLine - nonce in querystring

				}
			}
		}

		// Check Envira Album Defaults Post exists.
		if ( ! $this->album_default_id ) {
			return $defaults;
		}

		// Check we are not editing the Envira Album Defaults Post
		// If we are, we don't want to do anything right now.
		if ( $this->album_default_id === $post_id ) {
			return $defaults;
		}

		if ( function_exists( 'envira_get_album' ) ) {
			$default_album = envira_get_album( $this->album_default_id );
			if ( ! $default_album ) {
				return $defaults;
			}
		}

		// Default Album exists - map its settings onto our defaults.
		$new_defaults = $default_album['config'];

		// Map the type back, so we don't end up creating another 'defaults' Album type.
		$new_defaults['type'] = isset( $defaults['type'] ) ? $defaults['type'] : false;

		// Unset some defaults that we don't want to copy to the new gallery, as these will break things.
		unset( $new_defaults['title'] );
		unset( $new_defaults['slug'] );

		// Return.
		return $new_defaults;

	}

	/**
	 * Helper function to retrieve a Setting
	 *
	 * @since 1.1.2
	 *
	 * @param string $key Setting.
	 * @return array Settings
	 */
	public function get_setting( $key ) {

		// Get settings.
		$settings = $this->get_settings();

		// Check setting exists.
		if ( ! is_array( $settings ) ) {
			return false;
		}
		if ( ! array_key_exists( $key, $settings ) ) {
			return false;
		}

		$setting = apply_filters( 'envira_defaults_setting', $settings[ $key ] );
		return $setting;

	}

	/**
	 * Helper function to retrieve Settings
	 *
	 * @since 1.1.2
	 *
	 * @return array Settings
	 */
	public function get_settings() {

		$settings = get_option( 'envira-defaults' );
		$settings = apply_filters( 'envira_defaults_settings', $settings );
		return $settings;

	}

	/**
	 * Returns the singleton instance of the class.
	 *
	 * @since 1.0.0
	 *
	 * @return object The Envira_Defaults_Common object.
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Envira_Defaults_Common ) ) {
			self::$instance = new Envira_Defaults_Common();
		}

		return self::$instance;

	}

}

// Load the common class.
$envira_defaults_common = Envira_Defaults_Common::get_instance();

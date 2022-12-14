<?php
/**
 * Plugin Name: Envira Gallery - Defaults Addon
 * Plugin URI:  https://enviragallery.com
 * Description: Enables defaults to be set and inherited by new Envira galleries and albums.
 * Author:      Envira Gallery Team
 * Author URI:  https://enviragallery.com
 * Version:     1.5.0
 * Text Domain: envira-defaults
 * Domain Path: languages
 *
 * @package Envira Gallery
 * @subpackage Envira Defaults.
 *
 * Envira Gallery is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * Envira Gallery is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Envira Gallery. If not, see <http://www.gnu.org/licenses/>.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Envira\Utils\Updater as Updater;

if ( function_exists( 'envira_license_checker' ) && false === envira_license_checker() ) {
	return false;
}

/**
 * Main plugin class.
 *
 * @since 1.0.0
 *
 * @package Envira_Defaults
 * @author  Envira Gallery Team <support@enviragallery.com>
 */
class Envira_Defaults {

	/**
	 * Holds the class object.
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	public static $instance;

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $version = '1.5.0';

	/**
	 * The name of the plugin.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $plugin_name = 'Envira Defaults';

	/**
	 * Unique plugin slug identifier.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $plugin_slug = 'envira-defaults';

	/**
	 * Plugin file.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $file = __FILE__;

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Load the plugin textdomain.
		add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );

		// Load the plugin.
		add_action( 'envira_gallery_init', array( $this, 'init' ), 99 );

		add_action( 'activated_plugin', array( $this, 'detect_plugin_activation' ), 10, 2 );

		// Load the updater.
		add_action( 'envira_gallery_updater', array( $this, 'updater' ), 10, 1 );
	}

	/**
	 * Detect Plugin Deactivation function.
	 *
	 * @access public
	 * @param mixed $plugin Plugin name.
	 * @param mixed $network_activation Network Activation.
	 * @return void
	 */
	public function detect_plugin_activation( $plugin, $network_activation ) {

		$default_id = get_option( 'envira_default_gallery' );
		$album_id   = get_option( 'envira_default_album' );

		do_action( 'envira_defaults_detect_activation', $plugin, $network_activation, $default_id, $album_id );

	}

	/**
	 * Loads the plugin textdomain for translation.
	 *
	 * @since 1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain( $this->plugin_slug, false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since 1.0.0
	 *
	 * @global object $wpdb         The WordPress database object.
	 * @param boolean $network_wide True if WPMU superadmin uses "Network Activate" action, false otherwise.
	 */
	public function activate( $network_wide = false ) {

		// Bail if the main class does not exist.
		if ( ! class_exists( 'Envira_Gallery' ) ) {
			return;
		}

		// Check if we are on a multisite install, activating network wide, or a single install.
		if ( is_multisite() && $network_wide ) {
			// Multisite network wide activation
			// Iterate through each blog in multisite, creating a default gallery and album if needed.
			$sites = get_sites(
				array(
					'limit' => 0,
				)
			);
			if ( is_array( $sites ) && count( $sites ) > 0 ) {
				foreach ( $sites as $site ) {
					switch_to_blog( $site->blog_id );

					// is there already a stored option? if so, check and see if the post was 'hidden'.
					$default_id = get_option( 'envira_default_gallery' );
					if ( $default_id && false !== get_post_status( $default_id ) ) {
						// this post exists, so move the post type back.
						set_post_type( $default_id, 'envira' );
					} else {
						// this post apparently doesn't exist.
						$this->generate_default_gallery();
					}
					$default_id = get_option( 'envira_default_album' );
					if ( $default_id && false !== get_post_status( $default_id ) ) {
						// this post exists, so move the post type back.
						set_post_type( $default_id, 'envira_album' );
					} else {
						// this post apparently doesn't exist.
						$this->generate_default_album();
					}
					restore_current_blog();
				}
			}
		} else {
			// Single Site - create a default gallery and album if needed.
			$default_id = get_option( 'envira_default_gallery' );
			if ( $default_id && false !== get_post_status( $default_id ) ) {
				// this post exists, so move the post type back.
				set_post_type( $default_id, 'envira' );
			} else {
				// this post apparently doesn't exist.
				$this->generate_default_gallery();
			}
			$default_id = get_option( 'envira_default_album' );
			if ( $default_id && false !== get_post_status( $default_id ) ) {
				// this post exists, so move the post type back.
				set_post_type( $default_id, 'envira_album' );
			} else {
				// this post apparently doesn't exist.
				$this->generate_default_album();
			}
		}

	}

	/**
	 * Checks if a Default Gallery already exists. If not, a default gallery is created.
	 *
	 * @since 1.0.0.
	 */
	public function generate_default_gallery() {

		global $wpdb;

		// Get Envira Gallery Instance.
		$instance = Envira_Gallery_Common::get_instance();

		// Generate the custom gallery options holder for default galleries if it does not exist.
		$exists = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE 'post_name' = %s AND 'post_type' = %s LIMIT 1", 'envira-default-gallery', 'envira' )  ); // @codingStandardsIgnoreLine

		if ( ! is_null( $exists ) ) {
			// check and make sure this gallery is set to 'draft' and not 'publish'.
			$existing_post = array(
				'ID'          => $exists,
				'post_status' => 'draft',
			);
			wp_update_post( $existing_post );
			return;
		}

		// Default gallery does not exist - create it.
		$args       = array(
			'post_type'   => 'envira',
			'post_name'   => 'envira-default-gallery',
			'post_title'  => apply_filters( 'envira_whitelabel', false ) ? apply_filters( 'envira_album_whitelabel_name', false ) . ' Default Settings' : __( 'Envira Default Gallery Settings', 'envira-default' ),
			'post_status' => 'draft',
		);
		$default_id = wp_insert_post( $args );

		// If successful, update our option so that we can know which gallery is default.
		if ( $default_id ) {
			update_option( 'envira_default_gallery', $default_id );

			// Loop through the defaults and prepare them to be stored.
			$defaults = $instance->get_config_defaults( $default_id );
			foreach ( $defaults as $key => $default ) {
				$fields['config'][ $key ] = $default;
			}

			// Update Fields.
			$fields['id']                = $default_id;
			$fields['config']['title']   = apply_filters( 'envira_whitelabel', false ) ? apply_filters( 'envira_album_whitelabel_name', false ) . ' Default Settings' : __( 'Envira Default Gallery Settings', 'envira-default' );
			$fields['config']['slug']    = 'envira-default-gallery';
			$fields['config']['classes'] = array( 'envira-default-gallery' );
			$fields['config']['type']    = 'defaults';
			$fields['gallery']           = array();

			// Update the meta field.
			update_post_meta( $default_id, '_eg_gallery_data', $fields );
		}

	}

	/**
	 * Checks if a Default Album already exists. If not, a default album is created.
	 *
	 * @since 1.0.0.
	 */
	public function generate_default_album() {

		global $wpdb;

		// Check if the Albums Addon is activated.
		// If not, don't attempt to generate a dynamic album.
		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		if ( ! is_plugin_active( 'envira-albums/envira-albums.php' ) ) {
			return;
		}
		if ( ! class_exists( 'Envira_Albums_Common' ) ) {
			return;
		}

		// Get Envira Album Instance.
		$instance = Envira_Albums_Common::get_instance();

		// Generate the custom album options holder for default default albums if it does not exist.
		$exists = $wpdb->get_var( $wpdb->prepare( 'SELECT ID FROM ' . $wpdb->posts . " WHERE 'post_name' = %s AND 'post_type' = %s LIMIT 1", 'envira-default-album', 'envira_album' ) ); // @codingStandardsIgnoreLine
		if ( ! is_null( $exists ) ) {
			// check and make sure this gallery is set to 'draft' and not 'publish'.
			$existing_post = array(
				'ID'          => $exists,
				'post_status' => 'draft',
			);
			wp_update_post( $existing_post );
			return;
		}

		// Default album does not exist - create it.
		$args       = array(
			'post_type'   => 'envira_album',
			'post_name'   => 'envira-default-album',
			'post_title'  => apply_filters( 'envira_whitelabel', false ) ? apply_filters( 'envira_album_whitelabel_name', false ) . ' Default Settings' : __( 'Envira Default Album Settings', 'envira-default' ),
			'post_status' => 'draft',
		);
		$default_id = wp_insert_post( $args );

		// If successful, update our option so that we can know which album is default.
		if ( $default_id ) {

			update_option( 'envira_default_album', $default_id );

			// Loop through the defaults and prepare them to be stored.
			$defaults = $instance->get_config_defaults( $default_id );

			foreach ( $defaults as $key => $default ) {

				$fields['config'][ $key ] = $default;

			}

			// Update Fields.
			$fields['id']                = $default_id;
			$fields['config']['title']   = ( apply_filters( 'envira_whitelabel', false ) ? apply_filters( 'envira_album_whitelabel_name', false ) . ' Default Settings' : __( 'Envira Default Album Settings', 'envira-default' ) );
			$fields['config']['slug']    = 'envira-default-album';
			$fields['config']['classes'] = array( 'envira-default-album' );
			$fields['config']['type']    = 'defaults';
			$fields['gallery']           = array();

			// Update the meta field.
			update_post_meta( $default_id, '_eg_album_data', $fields );
		}

	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since 1.0.0
	 *
	 * @global object $wpdb The WordPress database object.
	 */
	public function deactivate() {

		// Bail if the main class does not exist.
		if ( ! class_exists( 'Envira_Gallery' ) ) {
			return;
		}

		// Check if we are on a multisite install, activating network wide, or a single install.
		if ( is_multisite() ) {

			// Multisite network wide activation.
			if ( is_plugin_active_for_network( 'envira-defaults/envira-defaults.php' ) ) {

				// Iterate through each blog in multisite, removing the default gallery and album if needed.
				$sites = get_sites(
					array(
						'limit' => 0,
					)
				);

				if ( is_array( $sites ) && count( $sites ) > 0 ) {

					foreach ( $sites as $site ) {

						switch_to_blog( $site->blog_id );
						$this->deactivate_default_gallery();
						$this->deactivate_default_album();
						restore_current_blog();
					}
				}
			} else {

				$current_blog = get_current_blog_id();
				switch_to_blog( $current_blog );
				$this->deactivate_default_gallery();
				$this->deactivate_default_album();
				restore_current_blog();

			}
		} else {

			// Single Site - remove default gallery and album if needed.
			$this->deactivate_default_gallery();
			$this->deactivate_default_album();
		}

	}

	/**
	 * Fired when the plugin is uninstalled.
	 *
	 * @since 1.0.0
	 *
	 * @global object $wpdb The WordPress database object.
	 */
	public function uninstalled() {

		// Bail if the main class does not exist.
		if ( ! class_exists( 'Envira_Gallery' ) ) {
			return;
		}

		// Check if we are on a multisite install, activating network wide, or a single install.
		if ( is_multisite() ) {

			// Multisite network wide activation.
			if ( is_plugin_active_for_network( 'envira-defaults/envira-defaults.php' ) ) {

				// Iterate through each blog in multisite, removing the default gallery and album if needed.
				$sites = get_sites(
					array(
						'limit' => 0,
					)
				);

				if ( is_array( $sites ) && count( $sites ) > 0 ) {

					foreach ( $sites as $site ) {

						switch_to_blog( $site->blog_id );
						$this->remove_default_gallery();
						$this->remove_default_album();
						restore_current_blog();
					}
				}
			} else {

				$current_blog = get_current_blog_id();
				switch_to_blog( $current_blog );
				$this->remove_default_gallery();
				$this->remove_default_album();
				restore_current_blog();

			}
		} else {

			// Single Site - remove default gallery and album if needed.
			$this->remove_default_gallery();
			$this->remove_default_album();
		}

	}

	/**
	 * Removes the default gallery
	 *
	 * @since 1.0.0
	 */
	public function remove_default_gallery() {

		// Grab the default gallery ID and use that to delete the gallery.
		$default_id = get_option( 'envira_default_gallery' );
		if ( $default_id ) {
			wp_delete_post( $default_id, true );
		}

		// Delete the option.
		delete_option( 'envira_default_gallery' );

	}

	/**
	 * Removes the default album
	 *
	 * @since 1.0.0
	 */
	public function remove_default_album() {

		// Grab the default album ID and use that to delete the album.
		$default_id = get_option( 'envira_default_album' );
		if ( $default_id ) {
			wp_delete_post( $default_id, true );
		}

		// Delete the option.
		delete_option( 'envira_default_album' );

	}

	/**
	 * Removes the default gallery
	 *
	 * @since 1.0.0
	 */
	public function deactivate_default_gallery() {

		// Grab the default gallery ID and use that to delete the gallery.
		$default_id = get_option( 'envira_default_gallery' );
		if ( $default_id ) {
			set_post_type( $default_id, 'envira_deactivated' );
		}

	}

	/**
	 * Removes the default album
	 *
	 * @since 1.0.0
	 */
	public function deactivate_default_album() {

		global $wpdb;

		// Grab the default album ID and use that to delete the album.
		$default_id = get_option( 'envira_default_album' );

		if ( $default_id ) {
			$return = $wpdb->update( $wpdb->posts, array( 'post_type' => 'envira_default_album' ), array( 'ID' => $default_id ) ); // @codingStandardsIgnoreLine
			if ( ! $return && defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				// Only used when debuggin is enabled.
				// @codingStandardsIgnoreStart
				error_log( 'Envira Debug: Default Not Deactivated For Albums', 0 );
				error_log( $return );
				error_log( get_post_type( $default_id ) );
				// @codingStandardsIgnoreEnd
			}
		}

	}

	/**
	 * Loads the plugin into WordPress.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		// Load global components.
		$this->require_global();

		// Load admin only components.
		if ( is_admin() ) {
			$this->require_admin();
		}

	}

	/**
	 * Loads all admin related files into scope.
	 *
	 * @since 1.0.0
	 */
	public function require_admin() {

		require plugin_dir_path( __FILE__ ) . 'includes/admin/ajax.php';
		require plugin_dir_path( __FILE__ ) . 'includes/admin/media-view.php';
		require plugin_dir_path( __FILE__ ) . 'includes/admin/metaboxes.php';
		require plugin_dir_path( __FILE__ ) . 'includes/admin/settings.php';
		require plugin_dir_path( __FILE__ ) . 'includes/admin/table.php';

	}

	/**
	 * Initializes the addon updater.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key The user license key.
	 */
	public function updater( $key ) {

		$args = array(
			'plugin_name' => $this->plugin_name,
			'plugin_slug' => $this->plugin_slug,
			'plugin_path' => plugin_basename( __FILE__ ),
			'plugin_url'  => trailingslashit( WP_PLUGIN_URL ) . $this->plugin_slug,
			'remote_url'  => 'https://enviragallery.com/',
			'version'     => $this->version,
			'key'         => $key,
		);

		$updater = new Updater( $args );

	}

	/**
	 * Loads all global files into scope.
	 *
	 * @since 1.0.0
	 */
	public function require_global() {

		require plugin_dir_path( __FILE__ ) . 'includes/global/common.php';

	}

	/**
	 * Returns the singleton instance of the class.
	 *
	 * @since 1.0.0
	 *
	 * @return object The Envira_Defaults object.
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Envira_Defaults ) ) {
			self::$instance = new Envira_Defaults();
		}

		return self::$instance;

	}

}

// Load the main plugin class.
$envira_defaults = Envira_Defaults::get_instance();

// Register activation and deactivation hooks.
register_activation_hook( __FILE__, array( &$envira_defaults, 'activate' ) );
register_deactivation_hook( __FILE__, array( &$envira_defaults, 'deactivate' ) );
add_action( 'activate_wpmu_site', array( &$envira_defaults, 'activate' ) );

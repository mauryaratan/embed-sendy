<?php
/**
 * Plugin Name: Embed Sendy
 * Plugin URI: https://github.com/mauryaratan/embed-sendy/
 * Description: Embed Sendy subscription form, through a widget, shortcode, or as a Gutenberg block.
 * Author: Ram Ratan Maurya
 * Author URI: https://mauryaratan.me
 * Version: 1.0.0
 * Text Domain: esd
 * License: GPL2+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * @package ESD
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main Embed_Sendy class.
 *
 * @since 1.0.0.
 */
final class Embed_Sendy {
	/**
	 * Class instance.
	 *
	 * @var Embed_Sendy The one true Embed_Sendy
	 * @since 1.0.0
	 */
	private static $instance;

	/**
	 * Main Embed_Sendy Instance.
	 *
	 * Insures that only one instance of Easy_Digital_Downloads exists in memory at any one
	 * time. Also prevents needing to define globals all over the place.
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Embed_Sendy ) ) {
			self::$instance = new Embed_Sendy();
			self::$instance->setup_constants();
			self::$instance->includes();

			add_shortcode( 'embed_sendy', array( self::$instance, 'embed_sendy_shortcode' ) );
		}

		return self::$instance;
	}

	/**
	 * Setup plugin constants.
	 *
	 * @access private
	 * @since 1.0.0
	 * @return void
	 */
	private function setup_constants() {
		// Plugin version.
		if ( ! defined( 'ESD_VERSION' ) ) {
			define( 'ESD_VERSION', '1.0.0' );
		}

		// Plugin Folder Path.
		if ( ! defined( 'ESD_PLUGIN_DIR' ) ) {
			define( 'ESD_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		}

		// Plugin Folder URL.
		if ( ! defined( 'ESD_PLUGIN_URL' ) ) {
			define( 'ESD_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		}

		// Plugin Root File.
		if ( ! defined( 'ESD_PLUGIN_FILE' ) ) {
			define( 'ESD_PLUGIN_FILE', __FILE__ );
		}
	}

	/**
	 * Include plugins files.
	 *
	 * @access private
	 * @since 1.0.0
	 * @return void
	 */
	private function includes() {
		require_once ESD_PLUGIN_DIR . 'src/init.php';
		require_once ESD_PLUGIN_DIR . 'includes/class-wp-osa.php';
		require_once ESD_PLUGIN_DIR . 'includes/admin-settings.php';
	}

	/**
	 * Add [embed_sendy] shortcode.
	 *
	 * @param string|mixed $atts Shortcode attributes.
	 * @return mixed
	 */
	public function embed_sendy_shortcode( $atts ) {
		$atts = shortcode_atts( array(
			'list' => '',
		), $atts, 'embed_sendy' );

		ob_start();



		return ob_get_clean();
	}

	/**
	 * Format all Sendy lists as an associative array.
	 *
	 * @return array|void Returns array of List Name -> List ID.
	 */
	public function get_lists() {
		$settings = get_option( 'esd_settings' );

		if ( array_key_exists( 'esd_lists', $settings ) ) {
			$new_lists = [];

			foreach ( $settings['esd_lists'] as $lists ) {
				$new_lists[ $lists[1] ] = $lists[0];
			}

			return $new_lists;
		}

		return false;
	}
}

/**
 * The main function for that returns Embed_Sendy.
 *
 * @return object|Embed_Sendy
 */
function ESD() { //@codingStandardsIgnoreLine
	return Embed_Sendy::instance();
}

ESD();

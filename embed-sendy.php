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
			add_action( 'wp_enqueue_scripts', array( self::$instance, 'frontend_scripts' ) );

			add_action( 'embed_sendy_form_start', array( self::$instance, 'display_before_form' ), 20, 1 );
			add_action( 'embed_sendy_form_end', array( self::$instance, 'display_after_form' ), 20, 1 );
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
		require_once ESD_PLUGIN_DIR . 'includes/class-embed-sendy-widget.php';
	}

	/**
	 * Frontend scripts & styles.
	 *
	 * @return void
	 */
	public function frontend_scripts() {
		wp_enqueue_style( 'embed-sendy', ESD_PLUGIN_URL . 'assets/embed-sendy.css', array(), ESD_VERSION, 'screen' );
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

		if ( '' === $atts['list'] ) {
			$atts['list'] = self::get_option( 'esd_default_list' );
		}

		ob_start();

		self::get_template( 'form-embed-sendy', array( 'list' => $atts['list'] ) );

		return ob_get_clean();
	}

	/**
	 * Get plugin settings.
	 *
	 * @param string $key Settings ID.
	 * @return array
	 */
	public function get_option( $key, $section = 'esd_settings' ) {
		$settings = get_option( $section );

		if ( array_key_exists( $key, $settings ) && '' !== $key ) {
			return $settings[ $key ];
		}

		return false;
	}

	/**
	 * Format all Sendy lists as an associative array.
	 *
	 * @return array|void Returns array of List Name -> List ID.
	 */
	public function get_lists() {
		$lists = self::get_option( 'esd_lists' );

		if ( is_array( $lists ) ) {
			$new_list = [];

			foreach ( $lists as $list ) {
				$new_list[ $list[1] ] = $list[0];
			}

			return $new_list;
		}

		return false;
	}

	/**
	 * Include a plugin template.
	 *
	 * @param string $template Template file name to include.
	 * @param array  $data An array to pass through template.
	 */
	public function get_template( $template, $data = array() ) {
		extract( $data ); // @codingStandardsIgnoreLine
		include ESD_PLUGIN_DIR . 'templates/' . $template . '.php';
	}

	/**
	 * Show before form content.
	 *
	 * @param string $list Sendy list ID.
	 * @return string|void
	 */
	public function display_before_form( $list ) {
		$before_text = self::get_option( 'esd_form_header', 'esd_form_settings' );

		if ( '' !== $before_text ) {
			echo '<div class="esd-form__row esd-form__header">' . self::filter_form_content( $before_text, $list ) . '</div>'; // WPCS: XSS ok.
		}
	}

	/**
	 * Show after form content.
	 *
	 * @param string $list Sendy list ID.
	 * @return string|void
	 */
	public function display_after_form( $list ) {
		$after_text = self::get_option( 'esd_form_footer', 'esd_form_settings' );

		if ( '' !== $after_text ) {
			echo '<div class="esd-form__row esd-form__footer">' . self::filter_form_content( $after_text, $list ) . '</div>'; // WPCS: XSS ok.
		}
	}

	/**
	 * Filter and replace tags in form header/footer.
	 *
	 * @param mixed  $text Text to filter.
	 * @param string $list Sendy list ID.
	 * @return mixed
	 */
	public function filter_form_content( $text, $list ) {
		$filtered_text = $text;

		$subscribers = self::get_subscribers( $list );
		if ( $subscribers ) {
			$filtered_text = str_replace( '{count}', $subscribers, $text );
		}

		return wpautop( $filtered_text );
	}

	/**
	 * Get subscribers count for an specific list.
	 *
	 * @param string $list List ID.
	 * @return int Count of subscribers.
	 */
	public function get_subscribers( $list = '' ) {
		// Bail early if no API is provided.
		if ( '' === self::get_option( 'esd_sendy_api' ) ) {
			return false;
		}

		// Use default mailing list if none is provided.
		if ( '' === $list ) {
			$list = self::get_option( 'esd_default_list' );
		}

		$subscribers = get_transient( 'esd_subscribers_' . $list );

		if ( false === $subscribers ) {
			$endpoint = self::get_option( 'esd_url' ) . '/api/subscribers/active-subscriber-count.php';

			$response = wp_remote_post( $endpoint, array(
				'body' => array(
					'api_key' => self::get_option( 'esd_sendy_api' ),
					'list_id' => $list,
				),
			) );

			if ( ! is_wp_error( $response ) ) {
				$subscribers = $response['body'];

				set_transient( 'esd_subscribers_' . $list, $subscribers, DAY_IN_SECONDS );
			}
		}

		return number_format_i18n( $subscribers );
	}

	/**
	 * Get User IP.
	 *
	 * Returns the IP address of the current visitor.
	 *
	 * @return string $ip User's IP address
	 */
	public function ip_address() {
		$ip = '127.0.0.1';

		// @codingStandardsIgnoreStart

		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			// Check ip from share internet.
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			// To check ip is pass from proxy.
			// Can include more than 1 ip, first is the public one.
			$ip = explode( ',',$_SERVER['HTTP_X_FORWARDED_FOR'] );
			$ip = trim( $ip[0] );
		} elseif ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		// @codingStandardsIgnoreEnd

		// Fix potential CSV returned from $_SERVER variables.
		$ip_array = explode( ',', $ip );
		$ip_array = array_map( 'trim', $ip_array );

		return apply_filters( 'esd_get_ip', $ip_array[0] );
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

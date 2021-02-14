<?php
/**
 * Plugin Name: Embed Sendy
 * Plugin URI: https://github.com/mauryaratan/embed-sendy/
 * Description: Embed Sendy subscription form, through a widget, shortcode, or as a Gutenberg block.
 * Author: Ram Ratan Maurya
 * Author URI: https://twitter.com/mauryaratan
 * Version: 1.3.3
 * Text Domain: embed-sendy
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

	protected static $sendy_api;

	/**
	 * Main Embed_Sendy Instance.
	 *
	 * Insures that only one instance of Easy_Digital_Downloads exists in memory at any one
	 * time. Also prevents needing to define globals all over the place.
	 *
	 * @throws Exception
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof self ) ) {
			self::$instance = new self();
			self::$instance->setup_constants();
			self::$instance->includes();

			$config = array(
				'sendyUrl' => self::get_option( 'esd_url' ),
				'listId'   => self::get_option( 'esd_default_list' ),
				'apiKey'   => self::get_option( 'esd_sendy_api' ),
			);

			try {
				self::$sendy_api = new \SENDY\API( $config );
			} catch ( Exception $e ) {
				add_action(
					'admin_notices',
					function() {
						?>
						<div class="notice notice-error is-dismissible">
							<p>
							<?php
								echo sprintf(
									/* translators: %s: settings link. */
									esc_html__( 'Embed Sendy is not working yet, please enter plugin %s.', 'embed-sendy' ),
									'<a href="' . esc_url( admin_url( 'options-general.php?page=embed_sendy' ) ) . '">' . esc_html__( 'settings', 'embed-sendy' ) . '</a>'
								);
							?>
							</p>
						</div>
						<?php
					}
				);
				return;
			}

			add_shortcode( 'embed_sendy', array( self::$instance, 'embed_sendy_shortcode' ) );
			add_action( 'wp_enqueue_scripts', array( self::$instance, 'frontend_scripts' ) );

			add_action( 'embed_sendy_form_start', array( self::$instance, 'display_before_form' ), 20, 1 );
			add_action( 'embed_sendy_form_end', array( self::$instance, 'display_after_form' ), 20, 1 );

			add_filter( 'the_content', array( self::$instance, 'display_form' ), 99 );

			add_action( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( self::$instance, 'plugin_action_links' ) );
			add_action( 'plugins_loaded', array( self::$instance, 'load_textdomain' ) );

			add_action( 'wp_ajax_process_sendy', array( self::$instance, 'process_sendy' ) );
			add_action( 'wp_ajax_nopriv_process_sendy', array( self::$instance, 'process_sendy' ) );
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
			define( 'ESD_VERSION', '1.3.3' );
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
		require_once ESD_PLUGIN_DIR . 'includes/class-api.php';
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
		$disable_styles = self::get_option( 'esd_disable_styles' );
		if ( 'off' === $disable_styles ) {
			wp_enqueue_style( 'embed-sendy', ESD_PLUGIN_URL . 'assets/embed-sendy.css', array(), ESD_VERSION, 'screen' );
		}

		wp_register_script( 'google-recaptcha-v2', 'https://www.google.com/recaptcha/api.js', array(), ESD_VERSION, true );

		$deps = array( 'jquery' );

		$recaptcha = self::get_option( 'esd_recaptcha_key' );
		if ( $recaptcha && '' !== $recaptcha ) {
			$deps[] = 'google-recaptcha-v2';
		}

		wp_enqueue_script( 'embed-sendy', ESD_PLUGIN_URL . 'assets/embed-sendy.js', $deps, ESD_VERSION, true );

		wp_localize_script(
			'embed-sendy',
			'esdSettings',
			array(
				'ajaxurl'           => admin_url( 'admin-ajax.php' ),
				'successMessage'    => self::get_option( 'esd_success', 'esd_form_settings' ),
				'alreadySubscribed' => self::get_option( 'esd_already_subscribed', 'esd_form_settings' ),
				'recaptchaFailed'   => __( 'Incorrect Captcha', 'embed-sendy' ),
			)
		);
	}

	/**
	 * Add [embed_sendy] shortcode.
	 *
	 * @param string|mixed $atts Shortcode attributes.
	 * @return mixed
	 */
	public function embed_sendy_shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'list'      => '',
				'recaptcha' => '',
			),
			$atts,
			'embed_sendy'
		);

		if ( '' === $atts['list'] ) {
			$atts['list'] = self::get_option( 'esd_default_list' );
		}

		ob_start();

		$this->get_template(
			'form-embed-sendy',
			array(
				'list'      => $atts['list'],
				'recaptcha' => $atts['recaptcha'],
			)
		);

		return ob_get_clean();
	}

	/**
	 * Get plugin settings.
	 *
	 * @param string $key Settings ID.
	 * @param string $section Section ID.
	 * @return mixed Returns value if found or false.
	 */
	public static function get_option( $key, $section = 'esd_settings' ) {
		$settings = get_option( $section );

		if ( is_array( $settings ) && array_key_exists( $key, $settings ) ) {
			return $settings[ $key ];
		}

		return ESD()->get_default( $key );
	}

	/**
	 * Format all Sendy lists as an associative array.
	 *
	 * @return array|bool Returns array of List Name -> List ID.
	 */
	public function get_lists() {
		$lists = self::get_option( 'esd_lists' );

		if ( $lists && is_array( $lists ) ) {
			$new_list = array();

			foreach ( $lists as $list ) {
				$new_list[ $list[1] ] = $list[0];
			}

			return $new_list;
		}

		return false;
	}

	/**
	 * Format all Sendy lists as an object for Gutenberg.
	 *
	 * @return array|bool Returns array of List Name -> List ID.
	 */
	public function get_lists_object() {
		$lists = self::get_option( 'esd_lists' );

		if ( is_array( $lists ) ) {
			$new_list = array();
			$index    = 0;

			foreach ( $lists as $list ) {
				$new_list[ $index ]['label'] = $list[0];
				$new_list[ $index ]['value'] = $list[1];

				$index++;
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

		if ( '' === $before_text ) {
			ESD()->get_default( 'esd_form_header' );
		}

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

		if ( '' === $after_text ) {
			ESD()->get_default( 'esd_form_footer' );
		}

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

		$subscribers = $this->get_subscribers( $list );
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

			$response = wp_remote_post(
				$endpoint,
				array(
					'body' => array(
						'api_key' => self::get_option( 'esd_sendy_api' ),
						'list_id' => $list,
					),
				)
			);

			if ( ! is_wp_error( $response ) ) {
				$subscribers = $response['body'];

				set_transient( 'esd_subscribers_' . $list, $subscribers, DAY_IN_SECONDS );
			}
		}
		return is_numeric( $subscribers ) ? number_format_i18n( $subscribers ) : 0;
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

		$final_ip = $ip_array[0];
		if ( '::1' === $final_ip ) {
			$final_ip = '127.0.0.1';
		}

		return apply_filters( 'esd_get_ip', $final_ip );
	}

	/**
	 * Output subscription form based on conditions.
	 *
	 * @param string|mixed $content Main content.
	 * @return string|mixed $content Content, with subscription form added.
	 */
	public function display_form( $content ) {
		$conditions = self::get_option( 'esd_display', 'esd_form_settings' );

		if ( ! is_array( $conditions ) ) {
			return $content;
		}

		$default_list = self::get_option( 'esd_default_list' );

		ob_start();
		$this->get_template( 'form-embed-sendy', array( 'list' => $default_list ) );
		$template = ob_get_clean();

		if ( array_key_exists( 'before_post', $conditions ) && is_singular( 'post' ) ) {
			$content = $template . $content;
		}

		if ( array_key_exists( 'after_post', $conditions ) && is_singular( 'post' ) ) {
			$content .= $template;
		}

		if ( array_key_exists( 'before_page', $conditions ) && is_singular( 'page' ) ) {
			$content = $template . $content;
		}

		if ( array_key_exists( 'after_page', $conditions ) && is_singular( 'page' ) ) {
			$content .= $template;
		}

		return $content;
	}

	/**
	 * Add plugin action links.
	 *
	 * Add a link to the settings page on the plugins.php page.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $links List of existing plugin action links.
	 * @return array        List of modified plugin action links.
	 */
	public function plugin_action_links( $links ) {
		$links = array_merge(
			array(
				'<a href="' . esc_url( admin_url( 'options-general.php?page=embed_sendy' ) ) . '">' . __( 'Settings', 'embed-sendy' ) . '</a>',
			),
			$links
		);

		return $links;
	}

	/**
	 * Load the plugin text domain.
	 *
	 * @return void
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'embed-sendy', false, basename( dirname( __FILE__ ) ) . '/languages' );
	}

	public function get_default( $key ) {
		$messages = array(
			'esd_gdpr_text'          => 'I consent to having this website store my submitted information so they can add me to an email subscription list.',
			'esd_success'            => 'Thanks for subscribing!',
			'esd_already_subscribed' => 'You are already subscribed to this list.',
			'esd_form_header'        => '<h3>Join our newsletter</h3>',
			'esd_form_footer'        => '<p>No spam. Ever!</p><p>You can unsubscribe any time — obviously.</p>',
			'esd_label_name'         => 'Name',
			'esd_label_email'        => 'Email',
			'esd_label_submit'       => 'Subscribe',
		);

		return isset( $messages[ $key ] ) ? $messages[ $key ] : false;
	}

	public function process_sendy() {
		check_ajax_referer( 'process_sendy' );

		if ( isset( $_POST['hp'] ) && '' !== $_POST['hp'] ) {
			wp_send_json_error(
				array(
					'success' => false,
					'message' => __( 'Invalid data received.', 'embed-sendy' ),
				)
			);
		}

		if ( ! isset( $_POST['email'], $_POST['list'] ) ) {
			wp_send_json_error(
				array(
					'success' => false,
					'message' => __( 'Invalid parameters.', 'embed-sendy' ),
				)
			);
		}

		if ( ! filter_var( wp_unslash( $_POST['email'] ), FILTER_VALIDATE_EMAIL ) ) {
			wp_send_json_error(
				array(
					'success' => false,
					'message' => __( 'Invalid email provided', 'embed-sendy' ),
				)
			);
		}

		$data = array(
			'name'      => isset( $_POST['name'] ) ? filter_var( wp_unslash( $_POST['name'] ) ) : '',
			'email'     => filter_var( wp_unslash( $_POST['email'] ), FILTER_VALIDATE_EMAIL ),
			'ipaddress' => filter_var( wp_unslash( $_POST['ipaddress'] ), FILTER_VALIDATE_IP ),
			'referrer'  => filter_var( wp_unslash( $_POST['referrer'] ), FILTER_VALIDATE_URL ),
			'gdpr'      => isset( $_POST['gdpr'] ) ? wp_unslash( $_POST['gdpr'] ) : false,
			'hp'        => wp_unslash( $_POST['hp'] ), // @codingStandardsIgnoreLine Passing this as it is intentionally
		);

		$recaptcha = ESD()::get_option( 'esd_recaptcha_key' );
		$recaptcha_v3 = ESD()::get_option( 'esd_recaptcha_key_v3' );

		if ( ! $recaptcha_v3 && $recaptcha && '' !== $recaptcha ) {
			$data += array(
				'subform'              => wp_unslash( $_POST['subform'] ),
				'g-recaptcha-response' => wp_unslash( $_POST['g-recaptcha-response'] ),
			);
		}

		self::$sendy_api->setListId( filter_var( wp_unslash( $_POST['list'] ) ) );
		$response = self::$sendy_api->subscribe( $data );

		wp_send_json_success( $response );

		wp_die();
	}
}

/**
 * The main function for that returns Embed_Sendy.
 *
 * @return object|Embed_Sendy
 * @throws Exception
 */
function ESD() { //@codingStandardsIgnoreLine
	return Embed_Sendy::instance();
}

ESD();

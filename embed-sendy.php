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

final class Embed_Sendy {
	public function __construct() {

	}
}

/**
 * Block Initializer.
 */
require_once plugin_dir_path( __FILE__ ) . 'src/init.php';

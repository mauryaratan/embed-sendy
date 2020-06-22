<?php
/**
 * Uninstall Embed Sendy.
 *
 * @package ESD
 */

// Exit if accessed directly.
defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

delete_option( 'esd_settings' );
delete_option( 'esd_form_settings' );

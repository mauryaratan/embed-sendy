<?php
/**
 * Define admin settings.
 *
 * @package ESD
 */

if ( class_exists( 'WP_OSA' ) ) {
	/**
	 * Object Instantiation.
	 *
	 * Object for the class `WP_OSA`.
	 */
	$wposa_obj = new WP_OSA();

	// Section: Basic Settings.
	$wposa_obj->add_section(
		array(
			'id'    => 'esd_settings',
			'title' => __( 'Basic Settings', 'esd' ),
		)
	);

	$wposa_obj->add_field(
		'esd_settings',
		array(
			'id'      => 'esd_url',
			'type'    => 'text',
			'name'    => __( 'Sendy URL', 'esd' ),
			'desc'    => __( 'Enter your sendy installation URL.', 'esd' ),
			'default' => false,
		)
	);

	$wposa_obj->add_field(
		'esd_settings',
		array(
			'id'      => 'esd_lists',
			'type'    => 'dynamic_text',
			'name'    => __( 'Lists', 'esd' ),
			'default' => false,
		)
	);

	$wposa_obj->add_field(
		'esd_settings',
		array(
			'id'      => 'esd_success',
			'type'    => 'text',
			'name'    => __( 'Success message', 'esd' ),
			'desc'    => __( 'Displayed when a user successfully subscribes to your mailing list.', 'esd' ),
			'default' => false,
		)
	);

	$wposa_obj->add_field(
		'esd_settings',
		array(
			'id'      => 'esd_already_subscribed',
			'type'    => 'text',
			'name'    => __( 'Already subscribed', 'esd' ),
			'desc'    => __( 'Displayed when a user is already subscribed to your mailing list.', 'esd' ),
			'default' => false,
		)
	);
}
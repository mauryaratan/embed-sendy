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
			'title' => __( 'Basic Settings', 'embed-sendy' ),
		)
	);
	// Section: Form Settings.
	$wposa_obj->add_section(
		array(
			'id'    => 'esd_form_settings',
			'title' => __( 'Form Settings', 'embed-sendy' ),
		)
	);

	$wposa_obj->add_field(
		'esd_settings',
		array(
			'id'      => 'esd_url',
			'type'    => 'text',
			'name'    => __( 'Sendy URL', 'embed-sendy' ),
			'desc'    => __( 'Enter your sendy installation URL.', 'embed-sendy' ),
			'default' => false,
		)
	);

	$wposa_obj->add_field(
		'esd_settings',
		array(
			'id'   => 'esd_sendy_api',
			'type' => 'text',
			'name' => __( 'Sendy API key', 'embed-sendy' ),
			'desc' => __( 'Enter your sendy API key. Optional.<br>Needed only if you plan to show subscribers count for a mailing list.', 'embed-sendy' ),
		)
	);

	$wposa_obj->add_field(
		'esd_settings',
		array(
			'id'      => 'esd_lists',
			'type'    => 'dynamic_text',
			'name'    => __( 'Lists', 'embed-sendy' ),
			'default' => false,
		)
	);

	if ( ESD()->get_option( 'esd_lists' ) ) {
		$wposa_obj->add_field(
			'esd_settings',
			array(
				'id'      => 'esd_default_list',
				'type'    => 'select',
				'name'    => __( 'Default List', 'embed-sendy' ),
				'desc'    => __( 'Select the default mailing list. Used in shortcode, and widget.', 'embed-sendy' ),
				'options' => ESD()->get_lists(),
			)
		);
	}

	$wposa_obj->add_field(
		'esd_settings',
		array(
			'id'      => 'esd_show_name',
			'type'    => 'checkbox',
			'name'    => __( 'Show name field', 'embed-sendy' ),
			'desc'    => __( 'Show Name field in forms along with Email.', 'embed-sendy' ),
			'default' => false,
		)
	);

	$wposa_obj->add_field(
		'esd_settings',
		array(
			'id' => 'esd_recaptcha_key',
			'type' => 'text',
			'name' => __( 'Google Recaptcha Site Key', 'embed-sendy' ),
			'desc' => __( 'If you\'re using Google Recaptcha for this list, please enter it here to enable Recaptcha.<br>You can also use different key for each list with <strong>recaptcha</strong> attribute on shortcode.', 'embed-sendy' ),
		)
	);

	$wposa_obj->add_field(
		'esd_settings',
		array(
			'id' => 'esd_recaptcha_key_v3',
			'type' => 'text',
			'name' => __( 'Google Recaptcha v3 Site Key', 'embed-sendy' ),
			'desc' => __( 'Enter v3 Recaptcha key. With v3 key in place, v2 recaptcha would be disabled automatically.', 'embed-sendy' ),
		)
	);

	$wposa_obj->add_field(
		'esd_settings',
		array(
			'id'      => 'esd_show_gdpr',
			'type'    => 'checkbox',
			'name'    => __( 'GDPR Enhancement', 'embed-sendy' ),
			'desc'    => __( 'Check this to turn on GDPR related features.', 'embed-sendy' ),
			'default' => false,
		)
	);

	$wposa_obj->add_field(
		'esd_settings',
		array(
			'id'      => 'esd_gdpr_text',
			'type'    => 'text',
			'name'    => __( 'GDPR Agreement Text', 'embed-sendy' ),
			'default' => ESD()->get_default( 'esd_gdpr_text' ),
			'size'    => 'large',
		)
	);

	$wposa_obj->add_field(
		'esd_settings',
		array(
			'id'      => 'esd_disable_styles',
			'type'    => 'checkbox',
			'name'    => __( 'Disable styles', 'embed-sendy' ),
			'desc'    => __( 'Embed sendy forms comes with some default styles, check this option to disable.', 'embed-sendy' ),
			'default' => false,
		)
	);

	$wposa_obj->add_field(
		'esd_form_settings',
		array(
			'id'      => 'esd_success',
			'type'    => 'text',
			'name'    => __( 'Success message', 'embed-sendy' ),
			'desc'    => __( 'Displayed when a user successfully subscribes to your mailing list.', 'embed-sendy' ),
			'default' => ESD()->get_default( 'esd_success' ),
		)
	);

	$wposa_obj->add_field(
		'esd_form_settings',
		array(
			'id'      => 'esd_already_subscribed',
			'type'    => 'text',
			'name'    => __( 'Already subscribed', 'embed-sendy' ),
			'desc'    => __( 'Displayed when a user is already subscribed to your mailing list.', 'embed-sendy' ),
			'default' => ESD()->get_default( 'esd_already_subscribed' ),
		)
	);

	$wposa_obj->add_field(
		'esd_form_settings',
		array(
			'id'      => 'esd_display',
			'type'    => 'multicheck',
			'name'    => __( 'Display Options', 'embed-sendy' ),
			'desc'    => __( 'Automatically display subscription form based on above conditions.', 'embed-sendy' ),
			'options' => array(
				'before_post' => __( 'Before each post', 'embed-sendy' ),
				'after_post'  => __( 'After each post', 'embed-sendy' ),
				'before_page' => __( 'Before each page', 'embed-sendy' ),
				'after_page'  => __( 'After each page', 'embed-sendy' ),
			),
		)
	);

	$template_tags_text = __( ' HTML is accepted. Available template tags: <br>{count} - Returns the count of active subscribers.', 'embed-sendy' );

	$wposa_obj->add_field(
		'esd_form_settings',
		array(
			'id'      => 'esd_form_header',
			'type'    => 'wysiwyg',
			'name'    => __( 'Form Header', 'embed-sendy' ),
			'desc'    => sprintf( __( 'Displayed right before form fields. %s', 'embed-sendy' ), $template_tags_text ),
			'default' => ESD()->get_default( 'esd_form_header' ),
		)
	);

	$wposa_obj->add_field(
		'esd_form_settings',
		array(
			'id'      => 'esd_form_footer',
			'type'    => 'wysiwyg',
			'name'    => __( 'Form Footer', 'embed-sendy' ),
			'desc'    => sprintf( __( 'Displayed right after form fields. %s', 'embed-sendy' ), $template_tags_text ),
			'default' => ESD()->get_default( 'esd_form_footer' ),
		)
	);

	$wposa_obj->add_field(
		'esd_form_settings',
		array(
			'id'   => 'esd_form_labels_desc',
			'type' => 'title',
			'name' => __( 'Form Labels', 'embed-sendy' ),
		)
	);

	$wposa_obj->add_field(
		'esd_form_settings',
		array(
			'id'      => 'esd_label_name',
			'type'    => 'text',
			'name'    => __( 'Name', 'embed-sendy' ),
			'default' => ESD()->get_default( 'esd_label_name' ),
		)
	);

	$wposa_obj->add_field(
		'esd_form_settings',
		array(
			'id'      => 'esd_label_email',
			'type'    => 'text',
			'name'    => __( 'Email', 'embed-sendy' ),
			'default' => ESD()->get_default( 'esd_label_email' ),
		)
	);

	$wposa_obj->add_field(
		'esd_form_settings',
		array(
			'id'      => 'esd_label_submit',
			'type'    => 'text',
			'name'    => __( 'Submit', 'embed-sendy' ),
			'default' => ESD()->get_default( 'esd_label_submit' ),
		)
	);
}

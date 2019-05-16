<?php
/**
 * Blocks Initializer
 *
 * Enqueue CSS/JS of all the blocks.
 *
 * @since   1.0.0
 * @package ESD
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue Gutenberg block assets for both frontend + backend.
 *
 * `wp-blocks`: includes block type registration and related functions.
 *
 * @since 1.0.0
 */
function embed_sendy_cgb_block_assets() {
	// Styles.
	wp_enqueue_style(
		'embed_sendy-cgb-style-css', // Handle.
		plugins_url( 'dist/blocks.style.build.css', dirname( __FILE__ ) ), // Block style CSS.
		array( 'wp-edit-blocks' ) // Dependency to include the CSS after it.
		// filemtime( plugin_dir_path( __FILE__ ) . 'editor.css' ) // Version: filemtime — Gets file modification time.
	);
} // End function embed_sendy_cgb_block_assets().

// Hook: Frontend assets.
add_action( 'enqueue_block_assets', 'embed_sendy_cgb_block_assets' );

/**
 * Enqueue Gutenberg block assets for backend editor.
 *
 * `wp-blocks`: includes block type registration and related functions.
 * `wp-element`: includes the WordPress Element abstraction for describing the structure of your blocks.
 * `wp-i18n`: To internationalize the block's text.
 *
 * @since 1.0.0
 */
function embed_sendy_cgb_editor_assets() {
	// Scripts.
	wp_enqueue_script(
		'embed_sendy-cgb-block-js', // Handle.
		plugins_url( '/dist/blocks.build.js', dirname( __FILE__ ) ), // Block.build.js: We register the block here. Built with Webpack.
		array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor' ) // Dependencies, defined above.
		// filemtime( plugin_dir_path( __FILE__ ) . 'block.js' ) // Version: filemtime — Gets file modification time.
	);

	// Gutenberg related settings.
	wp_localize_script(
		'embed_sendy-cgb-block-js',
		'esdBlockSettings',
		array(
			'lists'        => wp_json_encode( ESD()->get_lists_object() ),
			'default_list' => ESD()->get_option( 'esd_default_list' ),
			'form_header'  => ESD()->get_option( 'esd_form_header', 'esd_form_settings' ),
			'form_footer'  => ESD()->get_option( 'esd_form_footer', 'esd_form_settings' ),
			'gdpr_text'    => ESD()->get_option( 'esd_gdpr_text' ),
		)
	);

	// Styles.
	wp_enqueue_style(
		'embed_sendy-cgb-block-editor-css', // Handle.
		plugins_url( 'dist/blocks.editor.build.css', dirname( __FILE__ ) ), // Block editor CSS.
		array( 'wp-edit-blocks' ) // Dependency to include the CSS after it.
		// filemtime( plugin_dir_path( __FILE__ ) . 'editor.css' ) // Version: filemtime — Gets file modification time.
	);
} // End function embed_sendy_cgb_editor_assets().

// Hook: Editor assets.
add_action( 'enqueue_block_editor_assets', 'embed_sendy_cgb_editor_assets' );

/**
 * Register block types.
 *
 * @return void
 */
function esd_register_block_types() {
	if ( ! function_exists( 'register_block_type' ) ) return;

	register_block_type(
		'embed-sendy/block-embed-sendy',
		array(
			'attributes'      => array(
				'name'                => array(
					'type'    => 'checkbox',
					'default' => false,
				),
				'gdpr'                => array(
					'type'    => 'checkbox',
					'default' => false,
				),
				'list'                => array(
					'type'    => 'string',
					'default' => ESD()->get_option( 'esd_default_list' ),
				),
				'formBackgroundColor' => array(
					'type'    => 'string',
					'default' => '#f5f5f5',
				),
				'formTextColor'       => array(
					'type'    => 'string',
					'default' => '#000000',
				),
			),
			'render_callback' => 'esd_render_block_form',
		)
		);
}
add_action( 'init', 'esd_register_block_types' );

/**
 * Render Gutenberg block for Embed Sendy block.
 *
 * @param array $attributes An array of block settings.
 * @return string|mixed
 */
function esd_render_block_form( $attributes ) {

	ob_start();

	ESD()->get_template(
		'form-embed-sendy',
		array(
			'list'             => $attributes['list'],
			'name'             => $attributes['name'],
			'gdpr'             => $attributes['gdpr'],
			'background_color' => $attributes['formBackgroundColor'],
			'text_color'       => $attributes['formTextColor'],
			'is_block'         => true,
		)
	);

	$block_content = ob_get_clean();

	return $block_content;
}

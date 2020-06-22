<?php
/**
 * Embed Sendy widget.
 *
 * @package ESD
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Embed Sendy widget.
 *
 * Displays the newsletter subscription form based on plugin settings.
 *
 * @since 1.0.0
 */
class Embed_Sendy_Widget extends WP_Widget {
	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct( 'embed_sendy_widget', __( 'Embed Sendy', 'embed-sendy' ), array( 'description' => __( 'Displays a subscription form for Embed Sendy.', 'embed-sendy' ) ) );
	}

	/**
	 * Register widget.
	 *
	 * @see WP_Widget::widget
	 *
	 * @param array $args Widget arguments.
	 * @param array $instance Widget instance.
	 * @return mixed
	 */
	public function widget( $args, $instance ) {
		$instance['list'] = ( isset( $instance['list'] ) ) ? $instance['list'] : ESD()->get_option( 'esd_default_list' );

		echo $args['before_widget']; // WPCS: XSS ok.

		do_action( 'esd_before_widget' );

		ESD()->get_template( 'form-embed-sendy', array( 'list' => $instance['list'] ) );

		do_action( 'esd_after_widget' );

		echo $args['after_widget']; // WPCS: XSS ok.
	}

	/**
	 * Widget update function.
	 *
	 * @see WP_Widget::update
	 *
	 * @param array $new_instance Old instance.
	 * @param array $old_instance New instance.
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['list'] = $new_instance['list'];

		return $instance;
	}

	/**
	 * Widget form
	 *
	 * WP_Widget::form
	 *
	 * @param array $instance Widget instance.
	 * @return void
	 */
	public function form( $instance ) {
		$defaults = array(
			'list' => '',
		);

		$instance = wp_parse_args( (array) $instance, $defaults );

		?>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'list' ) ); ?>"><?php esc_html_e( 'Mailing List:', 'embed-sendy' ); ?></label>
			<select name="<?php echo esc_attr( $this->get_field_name( 'list' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'list' ) ); ?>">
			<?php
			if ( false !== ESD()->get_lists() ) :
				?>
				<?php foreach ( ESD()->get_lists() as $list_id => $list_name ) : ?>
				<option value="<?php echo esc_attr( $list_id ); ?>" <?php selected( $instance['list'], $list_id ); ?>><?php echo esc_html( $list_name ); ?></option>
				<?php endforeach; ?>
			<?php endif; ?>
			</select>
		</p>

		<?php /* translators: %s: refers to link to plugin settings. */ ?>
		<p><?php echo sprintf( __( 'See <a href="%s">Embed Sendy settings</a> to customize form display settings.', 'embed-sendy' ), admin_url( 'options-general.php?page=embed_sendy' ) ); // WPCS: XSS ok. ?></p>

		<?php
	}
}

/**
 * Register Widgets.
 *
 * Registers the ESD Widgets.
 *
 * @since 1.0.0
 * @return void
 */
function esd_register_widgets() {
	register_widget( 'embed_sendy_widget' );
}
add_action( 'widgets_init', 'esd_register_widgets' );

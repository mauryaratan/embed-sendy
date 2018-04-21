<?php
/**
 * Embed Sendy form.
 *
 * @package ESD
 */

$esd_settings = get_option( 'esd_settings' );

// Bail early if no lists found.
if ( ! array_key_exists( 'esd_lists', $esd_settings ) || ! array_key_exists( 'esd_url', $esd_settings ) ) return;

global $wp;
$user = false;

if ( is_user_logged_in() ) {
	$user = get_userdata( get_current_user_id() );
	$user = $user->data;
}

?>

<?php do_action( 'embed_sendy_form_before', $list ); ?>

<form id="js-esd-form" class="esd-form" action="<?php echo esc_url( $esd_settings['esd_url'] ); ?>/subscribe" method="post" target="_blank">
	<?php do_action( 'embed_sendy_form_start', $list ); ?>

	<div class="esd-form__row esd-form__fields">
		<input type="email" name="email" placeholder="<?php esc_attr_e( 'Enter your email', 'esd' ); ?>" value="<?php echo ( $user ) ? esc_attr( $user->user_email ) : ''; ?>" required>
		<input type="submit" value="<?php esc_attr_e( 'Subscribe', 'esd' ); ?>">

		<?php if ( $user ) : ?>
		<input type="hidden" name="name" value="<?php echo esc_attr( $user->display_name ); ?>">
		<?php endif; ?>

		<input type="hidden" name="list" value="<?php echo esc_attr( $list ); ?>">
		<input type="hidden" name="hp">
		<input type="hidden" name="ipaddress" value="<?php echo esc_attr( ESD()->ip_address() ); ?>">
		<input type="hidden" name="referrer" value="<?php echo esc_url( home_url( $wp->request ) ); ?>">

		<?php do_action( 'embed_sendy_form_fields', $list ); ?>
	</div>

	<?php do_action( 'embed_sendy_form_end', $list ); ?>

	<style>
		<?php if ( isset( $background_color ) ) : ?>
		.esd-form { background-color: <?php echo esc_html( $background_color ); ?> }
		<?php endif; ?>
		<?php if ( isset( $text_color ) ) : ?>
		.esd-form { color: <?php echo esc_html( $text_color ); ?> }
		<?php endif; ?>
	</style>
</form><!-- #js-embed-sendy.embed-sendy -->

<?php do_action( 'embed_sendy_form_after', $list ); ?>

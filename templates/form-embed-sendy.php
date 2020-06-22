<?php
/**
 * Embed Sendy form.
 *
 * @package ESD
 */

$esd_settings = get_option( 'esd_settings' );

if ( ! is_array( $esd_settings ) ) return;

// Bail early if no lists found.
if ( ! array_key_exists( 'esd_lists', $esd_settings ) || ! array_key_exists( 'esd_url', $esd_settings ) ) return;

$show_name = isset( $esd_settings['esd_show_name'] ) ? $esd_settings['esd_show_name'] : false;
$show_gdpr = isset( $esd_settings['esd_show_gdpr'] ) ? $esd_settings['esd_show_gdpr'] : false;
$gdpr_text = isset( $esd_settings['esd_gdpr_text'] ) ? $esd_settings['esd_gdpr_text'] : ESD()->get_default( 'esd_gdpr_text' );

global $wp;
$user = false;

if ( is_user_logged_in() ) {
	$user = get_userdata( get_current_user_id() );
	$user = $user->data;
}

$class = 'esd-form';

$in_block = false;
if ( isset( $is_block ) ) {
	$class   .= ' esd-form--block';
	$in_block = true;
}

if ( 'on' === $show_name || 'on' === $show_gdpr || $name || $gdpr ) {
	$class .= ' esd-form--show-name';
}

if ( ! isset( $recaptcha ) || '' === $recaptcha ) {
	$recaptcha = $esd_settings['esd_recaptcha_key'];
}

?>

<?php do_action( 'embed_sendy_form_before', $list ); ?>

<form id="js-esd-form" class="<?php echo esc_attr( $class ); ?>" method="post">
	<?php do_action( 'embed_sendy_form_start', $list ); ?>

	<div class="esd-form__row esd-form__fields">
		<?php if ( ( 'on' === $show_name && ! $in_block ) || ( $in_block && $name ) ) : ?>
		<input type="text" name="name" placeholder="<?php esc_attr_e( 'Name', 'embed-sendy' ); ?>" value="<?php echo esc_attr( $user->display_name ); ?>">
		<?php endif; ?>

		<input type="email" name="email" placeholder="<?php esc_attr_e( 'Email', 'embed-sendy' ); ?>" value="<?php echo ( $user ) ? esc_attr( $user->user_email ) : ''; ?>" required>

		<?php if ( ( 'on' === $show_gdpr && ! $in_block ) || ( $in_block && $gdpr ) ) : ?>
		<div class="gdpr-row">
			<input type="checkbox" id="gdpr" name="gdpr" required>
			<label for="gdpr"><?php echo esc_html( $gdpr_text ); ?></label>
		</div>
		<?php endif; ?>

		<?php if ( $recaptcha && '' !== $recaptcha ) : ?>
			<p class="g-recaptcha" data-sitekey="<?php echo esc_attr( $recaptcha ); ?>"></p>
			<input type="hidden" name="subform" value="yes" />
		<?php endif; ?>

		<div style="display:none;">
			<label for="hp">HP</label><br/>
			<input type="text" name="hp" id="hp"/>
		</div>

		<input id="submit" type="submit" value="<?php esc_attr_e( 'Subscribe', 'embed-sendy' ); ?>">
		<input type="hidden" name="list" value="<?php echo esc_attr( $list ); ?>">
		<input type="hidden" name="ipaddress" value="<?php echo esc_attr( ESD()->ip_address() ); ?>">
		<input type="hidden" name="referrer" value="<?php echo esc_url( home_url( $wp->request ) ); ?>">
		<?php wp_nonce_field( 'process_sendy' ); ?>

		<?php do_action( 'embed_sendy_form_fields', $list ); ?>
	</div>

	<?php do_action( 'embed_sendy_form_end', $list ); ?>

	<?php if ( isset( $is_block ) ) : ?>
	<style>
		<?php if ( isset( $background_color ) ) : ?>
		.esd-form--block { background-color: <?php echo esc_html( $background_color ); ?> }
		<?php endif; ?>
		<?php if ( isset( $text_color ) ) : ?>
		.esd-form--block { color: <?php echo esc_html( $text_color ); ?> }
		<?php endif; ?>
	</style>
	<?php endif; ?>
</form><!-- #js-embed-sendy.embed-sendy -->

<?php do_action( 'embed_sendy_form_after', $list ); ?>

/* global esdSettings, jQuery */
( function( $ ) {
	$( document ).ready( function() {
		$( '#js-esd-form' ).on( 'submit', function( e ) {
			e.preventDefault();
			e.stopPropagation();
			const self = $( this );

			if ( self.find( '#gdpr' ).is( ':checked' ) ) {
				self.find( '#gdpr' ).val( 'true' );
			}

			self.find( 'input[type=submit]' ).attr( 'disabled', 'disabled' );

			self.find( '.esd-form__response' ).remove();

			let formData = $( this ).serialize();
			formData += '&boolean=true&action=process_sendy';

			$.ajax( {
				method: 'POST',
				url: esdSettings.ajaxurl,
				data: formData,
				dataType: 'json',
			} )
				.done( function( res ) {
					if ( res.data && res.data.status === false ) {
						const message = res.data.message || 'Some error occurred.';
						$( '<p class="esd-form__row esd-form__response esd-form__response--error">Error: ' + message + '</p>' ).insertBefore( self.find( '#submit' ) );
						return;
					}

					if ( res.success === false ) {
						$( '<p class="esd-form__row esd-form__response esd-form__response--error">Error: ' + res.data.message + '</p>' ).insertBefore( self.find( '#submit' ) );
						return;
					}

					if ( res.success && res.data.status ) {
						const message = ( res.data.message === 'Already subscribed!' ) ? esdSettings.alreadySubscribed : esdSettings.successMessage;

						$( '<p class="esd-form__row esd-form__response esd-form__response--success">' + message + '</p>' ).insertBefore( self.find( '#submit' ) );
					}
				} )
				.fail( function( data ) {
					const response = data.responseText;
					let message = '';

					if ( 'Already subscribed.' === response ) {
						message = esdSettings.alreadySubscribed;
					} else {
						message = response;
					}

					$( '<p class="esd-form__row esd-form__response esd-form__response--error">Error: ' + message + '</p>' ).insertBefore( self.find( '#submit' ) );
				} )
				.always( function() {
					self.find( 'input[type=submit]' ).removeAttr( 'disabled' );
				} );
		} );
	} );
}( jQuery ) );

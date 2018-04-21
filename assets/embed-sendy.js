/* global esdSettings, jQuery */
( function( $ ) {
	$( document ).ready( function() {
		$( '.esd-form' ).on( 'submit', function( e ) {
			e.preventDefault();
			e.stopPropagation();
			const self = $( this );

			self.find( 'input[type=submit]' ).attr( 'disabled', 'disabled' );

			self.find( '.esd-form__response' ).remove();

			let formData = $( this ).serialize();
			formData += '&boolean=true';

			$.ajax( {
				method: 'POST',
				url: esdSettings.url,
				data: formData,
				dataType: 'json',
			} )
				.done( function() {
					$( '<p class="esd-form__response esd-form__response--success">' + esdSettings.successMessage + '</p>' ).insertAfter( self.find( '.esd-form__fields' ) );
				} )
				.fail( function( data ) {
					const response = data.responseText;
					let message = '';

					if ( 'Already subscribed.' === response ) {
						message = esdSettings.alreadySubscribed;
					} else {
						message = response;
					}

					$( '<p class="esd-form__response esd-form__response--error">' + message + '</p>' ).insertAfter( self.find( '.esd-form__fields' ) );
				} )
				.always( function() {
					self.find( 'input[type=submit]' ).removeAttr( 'disabled' );
				} );
		} );
	} );
}( jQuery ) );

( function () {
	const api = new mw.Api();
	// eslint-disable-next-line no-jquery/no-global-selector
	const $form = $( "[name='userlogin']" );

	function submitForm() {
		$form.trigger( 'submit' );
	}

	function pendingVerification() {
		api.get( {
			action: 'passwordlesslogin-verify-login'
		} ).then( ( data ) => {
			if ( data[ 'login-verification' ].result === 'Success' ) {
				$( '<div>' )
					.css( 'z-index', '1' )
					.css( 'height', '100%' )
					.css( 'width', '100%' )
					.css( 'position', 'absolute' )
					.insertBefore( $form );
				$form.css( 'opacity', '0.5' );
				submitForm();
			} else {
				pendingVerification();
			}
		} );
	}

	if ( mw.config.get( 'PLEnableApiVerification' ) ) {
		pendingVerification();
	} else {
		setTimeout( () => {
			submitForm();
		}, 3000 );
	}
}() );

jQuery(document).ready(function($){

	if ( !$( 'input[name="enable_b2b_credit_payment"]' ).is( ":checked" )) {
		$( '#ccr_credit_value' ).hide();
		$( '#ccr_credit_payment_heading' ).hide();
	}

	$( 'input[name="enable_b2b_credit_payment"]' ).on(
			'click',
			function () {
				if ($( this ).is( ":checked" )) {
					$( '#ccr_credit_value' ).show();
					$( '#ccr_credit_payment_heading' ).show();
				} else {
					$( '#ccr_credit_value' ).hide();
					$( '#ccr_credit_payment_heading' ).hide();
				}
			}
		);



});
(function($){
	$(
		function(){

			var $tbdsTable = $( '.codup-ecommerce-payment-method-mode' ).eq( 0 ).closest( 'table.form-table' );
			// add another tier button
			var $addMethodButton = $( '.b2be-add-method-button' );
			$addMethodButton.on( 'click', addMethodRow );

			// bind delete methods buttons
			$tbdsTable.on( 'click', '.remove-method', deleteMethodRow );

			/**
			 * Add another input row for adding tiers.
			 *
			 * @returns {undefined}
			 */
			function addMethodRow() {
				if ( 6 == parseInt( $( '.b2be_payment_method' ).length ) ) {
					alert( "You can only add a maximum of five payment method" );
					return;
				}

				var $lastRow = $addMethodButton.closest( 'table' ).find( ".b2be_payment_method" ).last().prev( "tr" );
				var $row     = $( '.b2be-payment-template-row' ).clone();

				var oldNumber = $lastRow.attr( 'data-method-number' );
				var newNumber = 'b2be_payment_' + Math.floor( (Math.random() * 1000000000) + 1 );

				$row.attr( 'data-method-number', newNumber );
				// clear values and update indexes
				$row.find( 'input' ).each(
					function(){
						var $this = $( this );
						$this.attr( 'name', 'codup_ecommerce_payment_method_settings[' + newNumber + ']' );
						$this.prop( 'required','required' );
						$this.val( '' );
					}
				);
				$row.show();
				$row.attr( 'class','b2be_payment_method' );
				$lastRow.after( $row );

			}

			var table = $( ".form-table" ).eq(0);
			if ( $( "input[id='codup-rfq_enable_has_terms']" ).prop( 'checked' ) == true ) {
				// table.find( "tr:last" ).show( 'slow' );
				table.find( ".b2be_payment_method" ).show( 'slow' );
				table.find( ".codup-ecommerce-payment-method-mode" ).attr( "required", true );
				table.find( '.b2be-payment-template-row' ).hide();
				table.find( '.b2be-method-row-footer' ).show();
				table.find( '.b2be-payment-template-row .codup-ecommerce-payment-method-mode' ).attr( "required", false );
			} else {
				// table.find( "tr:last" ).hide( 'slow' );
				table.find( ".b2be_payment_method" ).hide( 'slow' );
				table.find( ".codup-ecommerce-payment-method-mode" ).attr( "required", false );
				table.find( '.b2be-method-row-footer' ).hide();
				table.find( '.b2be-payment-template-row' ).hide();
				table.find( '.b2be-payment-template-row .codup-ecommerce-payment-method-mode' ).attr( "required", false );
			}

			$( "input[id='codup-rfq_enable_has_terms']" ).on(
				"change",
				function(){
					var table = $( ".form-table" ).eq(0);
					if ( $( this ).prop( 'checked' ) == true ) {
						// table.find( "tr:last" ).show( 'slow' );
						table.find( ".b2be_payment_method" ).show( 'slow' );
						table.find( ".codup-ecommerce-payment-method-mode" ).attr( "required", true );
						table.find( '.b2be-payment-template-row' ).hide();
						table.find( '.b2be-method-row-footer' ).show();
						table.find( '.b2be-payment-template-row .codup-ecommerce-payment-method-mode' ).attr( "required", false );
					} else {
						// table.find( "tr:last" ).hide( 'slow' );
						table.find( ".b2be_payment_method" ).hide( 'slow' );
						table.find( ".codup-ecommerce-payment-method-mode" ).attr( "required", false );
						table.find( '.b2be-method-row-footer' ).hide();
						table.find( '.b2be-payment-template-row' ).hide();
						table.find( '.b2be-payment-template-row .codup-ecommerce-payment-method-mode' ).attr( "required", false );
					}
				}
			)
		}
	);

})( jQuery );

/**
 * Deletes the tier row that delete button was clicked on.
 *
 * @returns {undefined}
 */
function deleteMethodRow() {
	var current_row      = jQuery( this ).closest( 'tr' );
	var method_to_delete = current_row.attr( 'data-method-number' );
	current_row.remove();
}

jQuery( document ).ready( function($) {

	$("#b2be_balance_test_account_id").closest("tr").hide()
    $("#b2be_balance_testmode").on( "change", function() {
        let checked = $(this).is(":checked");
        if ( checked ) {
            $("#b2be_balance_test_account_id").closest("tr").show()
            $("#b2be_balance_live_account_id").closest("tr").hide()
        }
        else{
            $("#b2be_balance_live_account_id").closest("tr").show()
            $("#b2be_balance_test_account_id").closest("tr").hide()
        }
    })

	if ( $( "input[id='b2be_integrate_balance']" ).is(":checked") ) {
		$("#get_balance_account_keys-description").show();
		$("#get_balance_account_keys-description").siblings("h2").eq(1).show();
		$("input[id='b2be_enable_balance_gateway']").closest("table").find("tr").show();
		$( "input[id='b2be_integrate_balance']" ).closest("table").find("hr").show();
		if ( $("#b2be_balance_testmode").is(":checked") ) {
			$("#b2be_balance_test_account_id").closest("tr").show()
			$("#b2be_balance_live_account_id").closest("tr").hide()
		}
		else{
			$("#b2be_balance_live_account_id").closest("tr").show()
			$("#b2be_balance_test_account_id").closest("tr").hide()
		}
	} else {
		$("#get_balance_account_keys-description").hide();
		$("#get_balance_account_keys-description").siblings("h2").eq(1).hide();
		$("input[id='b2be_enable_balance_gateway']").closest("table").find("tr").hide();
		$( "input[id='b2be_integrate_balance']" ).closest("table").find("hr").hide();
	}
	$( "input[id='b2be_integrate_balance']" ).on(
		"click",
		function() {
			let checked = $(this).is(":checked");
			if ( checked ) {
				$("#get_balance_account_keys-description").show("slow");
				$("#get_balance_account_keys-description").siblings("h2").eq(1).show("slow");
				$("input[id='b2be_enable_balance_gateway']").closest("table").find("tr").show("slow");
				$( "input[id='b2be_integrate_balance']" ).closest("table").find("hr").show();
				if ( $("#b2be_balance_testmode").is(":checked") ) {
					$("#b2be_balance_test_account_id").closest("tr").show()
					$("#b2be_balance_live_account_id").closest("tr").hide()
				}
				else{
					$("#b2be_balance_live_account_id").closest("tr").show()
					$("#b2be_balance_test_account_id").closest("tr").hide()
				}
			} else {
				$("#get_balance_account_keys-description").hide("slow");
				$("#get_balance_account_keys-description").siblings("h2").eq(1).hide("slow");
				$( "input[id='b2be_integrate_balance']" ).closest("table").find("hr").hide();
				$("input[id='b2be_enable_balance_gateway']").closest("table").find("tr").hide("slow");
			}
		}
	)

	if ( $(".b2be_gateway").is(":checked") ) {
		$(".balance_term_methods").closest("tr").show();
		$( "h3.balance_term_methods").show();
	}
	else{
		$(".balance_term_methods").closest("tr").hide();
		$( "h3.balance_term_methods").hide();
	}
	$(".b2be_gateway").on(
		"click", 
		function() {
			let checked = $(this).is(":checked");
			if ( checked ) {
				$(".balance_term_methods").closest("tr").show("slow");
				$( "h3.balance_term_methods").show("slow");
			}
			else{
				$(".balance_term_methods").closest("tr").hide("slow");
				$("h3.balance_term_methods").hide("slow");
			}
		}
	)
})
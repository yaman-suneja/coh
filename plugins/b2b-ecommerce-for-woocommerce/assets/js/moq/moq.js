jQuery( document ).ready(
	function($){

		// variation range.
		$( document ).on(
			"show_variation",
			function(){
				var qtyField     = $( 'input[name="quantity"]' );
				var variation_id = $( '.variation_id' ).val();
				var moq_rules    = moq_limit.b2be_moq_rules;
				var arrr         = Object.values( moq_rules );
				for ( let i = 0; i < Object.keys( moq_rules ).length; i++) {
					if ( 0 < arrr[i].variation_ids.filter( (id) => parseInt( variation_id ) == parseInt( id ) ).length ) {
						if ( '' !== arrr[i].minQuantity ) {
							qtyField.attr( 'min', arrr[i].minQuantity );
						}
						if ( '' !== arrr[i].maxQuantity ) {
							qtyField.attr( 'max', arrr[i].maxQuantity );
						}
						qtyField.val( arrr[i].minQuantity );
					}
				}
			}
		);

		// variation multiplier.
		$( document ).on(
			"show_variation",
			function(){

				var qtyField     = $( 'input[name="quantity"]' );
				qtyField.attr( 'step', 1 );
				qtyField.attr( 'min',  1 );
				var variation_id = $( '.variation_id' ).val();
				var moq_rules    = moq_limit.b2be_moq_rules;
				var arrr         = Object.values( moq_rules );

				for ( let i = 0; i < Object.keys( moq_rules ).length; i++) {
					if ( 0 < arrr[i].variation_ids.filter( (id) => parseInt( variation_id ) == parseInt( id ) ).length ) {
						if ( '' !== arrr[i].multiplier ) {
							qtyField.attr( 'step', arrr[i].multiplier );
							qtyField.attr( 'min',  arrr[i].multiplier );
							qtyField.attr( 'value', arrr[i].multiplier );
							qtyField.attr( 'placeholder', arrr[i].multiplier );
							qtyField.val(arrr[i].multiplier).change();
						}
					}
				}


		});
	}
)

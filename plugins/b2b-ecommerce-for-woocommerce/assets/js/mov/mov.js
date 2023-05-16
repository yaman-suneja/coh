jQuery( document ).ready(
	function($){
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
		)
	}
)

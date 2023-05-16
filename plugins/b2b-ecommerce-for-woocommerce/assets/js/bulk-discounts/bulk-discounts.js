jQuery( document ).ready(
	function($){
		$( document ).on(
			"show_variation",
			function(){
				$is_ok            = false;
				var variation_ids = [];
				var variation_id  = parseInt( $( '.variation_id' ).val() );
				var b2be_rules    = bulk_rule.b2be_bulk_rules;
				var arrr = Object.values( b2be_rules );
				var temp = [];

					var discount_mode = b2be_rules.mode;
					var product_price = bulk_rule.b2be_variations_price[variation_id];
					var newprice;

				if ( 'd' == discount_mode ) {
					if ( $.inArray( variation_id, b2be_rules.variation_ids ) !== -1 ) {
						var discount_type = b2be_rules.type;
						var discount      = b2be_rules.discount;

						if ( 'fixed' == discount_type ) {
							newprice = product_price - discount;
						} else {
							newprice = product_price - ( product_price * ( discount / 100 ) );
						}

						if ( newprice <= 0 ) {
							newprice = 0.00;
						}
						if ( newprice >= 0 ) {
							$( '.woocommerce-variation-price .price' ).html( '<del>' + bulk_rule.price_symbol + product_price.toFixed( 2 ) + '</del> ' + bulk_rule.price_symbol + newprice.toFixed( 2 ) );
						}
					}
				}
			}
		)
	}
)

jQuery(document).ready(function($){

	bulk_fields_toggle( ".bulk-role-based-enable", ".role-discount" );
	bulk_fields_toggle( ".bulk-category-based-enable", ".category-discount" );
	bulk_fields_toggle( ".bulk-customer-based-enable", ".customer-discount" );
	bulk_fields_toggle( ".bulk-quantity-based-enable", ".quantity-discount" );
	bulk_fields_toggle( ".bulk-product-based-enable", ".product-discount" );

	b2be_select2( '#bulk-template .bulk-b2be-role-selection', 'Select Role' );
	b2be_select2( '#bulk-template .bulk-b2be-category-selection', 'Select Category' );
	b2be_select2( '#bulk-template .bulk-b2be-customer-selection', 'Select Customer' );
	b2be_select2( '#bulk-template .bulk-b2be-product-selection', 'Select Products' );

	$( document.body ).on( 'click', '#add_bulk_quantity_range', function() {
		var prev_row = $( '#template' ).find( '#bulk-inner-rows' ).find('tr');
		
		var new_row = prev_row.clone();
		var new_id = $(this).closest( '.bulk-inner-template' ).attr( 'data-template-id' );
		
		new_row.find( '#add_bulk_quantity_range' ).replaceWith(
			'<input type="button" name="remove_bulk_quantity_range" id="remove_bulk_quantity_range" class="button-secondary" value="-" >'
		);

		if( true == $(this).closest( '.bulk-inner-template' ).find( '.bulk-quantity-based-enable' ).prop( 'checked' ) ) {
			new_row.find('.quantity-discount').show();
			new_row.find('#remove_bulk_quantity_range').show();
		}
		else {
			new_row.find('.quantity-discount' ).hide();
			new_row.find( '#remove_bulk_quantity_range' ).hide();
		}

		$(this).closest( '.bulk-inner-template' ).find("#bulk-inner-rows").append( new_row );

	});

	$( document.body ).on( 'change', '#bulk-type', function() {

		if( 'percentage' == $(this).val() ) {
			var discount_field = $(this).closest( 'tr' ).find( '#bulk-quantity-based-discount-amount' );
			discount_field.attr( 'max', '100' );
			if ( discount_field.val() > 100 ) {
				discount_field.val(100);
			}

		}
		else {
			var discount_field = $(this).closest( 'tr' ).find( '#bulk-quantity-based-discount-amount' );
			discount_field.removeAttr( 'max' );
		}

	});

	$( document.body ).on( 'click', '#add_discount_rule', function() {

		var new_div  = $('#template div.bulk-inner-template').clone();
		var new_id = $('#template div.bulk-inner-template').attr('data-template-id');
		
		new_div.attr( "data-template-id", ( parseInt( new_id )+1 ) );
		new_div.attr( "id", 'bulk-inner-template-' + ( parseInt( new_id )+1 ) );
		
		$( '#template div.bulk-inner-template' ).attr( "data-template-id", ( parseInt( new_id )+1 ) );
		$( '#template div.bulk-inner-template' ).find( '#bulk-title-discounts' ).find( 'span' ).text( 'Rule ' + ( parseInt( new_id )+1 ) );
		
		new_div.find( '.bulk-b2be-role-selection' ).attr( 'id', 'bulk-b2be-role-selection-' + ( parseInt( new_id )+1 ) );
		new_div.find( '.bulk-b2be-category-selection' ).attr( 'id', 'bulk-b2be-category-selection-' + ( parseInt( new_id )+1 ) );
		new_div.find( '.bulk-b2be-customer-selection' ).attr( 'id', 'bulk-b2be-customer-selection-' + ( parseInt( new_id )+1 ) );
		new_div.find( '.bulk-b2be-product-selection' ).attr( 'id', 'bulk-b2be-product-selection-' + ( parseInt( new_id )+1 ) );
		new_div.find( '.bulk-discount-format' ).attr( 'name', 'bulk-discount-format-' + ( parseInt( new_id )+1 ) );
		new_div.find( '#bulk-discount-format-default' ).prop( 'checked', true );

		$("#bulk-template").append( new_div );
		b2be_select2( '#bulk-b2be-role-selection-' + ( parseInt( new_id )+1 ), 'Select Role' );
		b2be_select2( '#bulk-b2be-category-selection-' + ( parseInt( new_id )+1 ), 'Select Category' );
		b2be_select2( '#bulk-b2be-customer-selection-' + ( parseInt( new_id )+1 ), 'Select Customer' );
		b2be_select2( '#bulk-b2be-product-selection-' + ( parseInt( new_id )+1 ), 'Select Product' );

		if( true == new_div.find( '.bulk-quantity-based-enable' ).prop( 'checked' ) ) {
			new_div.find('.quantity-discount').show();
			new_div.find('#remove_bulk_quantity_range').show();
		}
		else {
			new_div.find('.quantity-discount' ).hide();
			new_div.find( '#remove_bulk_quantity_range' ).hide();
		}
		
		new_div.find( '#bulk-title-discounts' ).find( 'span' ).text( 'Rule ' + ( parseInt( new_id )+1 ));
	});

	$( document.body ).on( 'click', '#remove_bulk_quantity_range', function() {
		$(this).closest('tr').remove();
	});

	$( document.body ).on( 'click', '.remove-rule', function() {
		
		$(this).closest('.bulk-inner-template').remove();

		var next_div_id = 0;
		$( '.bulk-template' ).find( 'div.bulk-inner-template' ).each(function(index, value ) {
			$( value ).find('div#bulk-title-discounts span').text( 'Rule ' + ( parseInt( index )+1 ) );
			next_div_id = index+1;
		});
		$( '#template .bulk-inner-template' ).attr('data-template-id', parseInt( next_div_id ) );
	});

    $( '.enable-bulk-discount' ).on('click', function() {

        if( true == $(this).is(':checked') ) {
			$( '.enable-bulk-discount' ).attr( "checked", true );
			$( '.bulk-inner-template' ).show();
			$( '#add_discount_rule' ).show();
        }
        else {
			$( '.enable-bulk-discount' ).attr( "checked", false );
			$( '.bulk-inner-template' ).hide();
			$( '#add_discount_rule' ).hide();
        }
        
    });

	$( document.body ).on( 'click', '#save_discount_rule', function(e) {

		$('span').removeClass( 'empty-field-error' );
		var rule = $( '.bulk-inner-template' );
		var innerRuleRows = '';
		var discountRules = [];
		var error = 0;
		var isEnable = $( '#enable-bulk-discount' ).is(':checked');

        if( isEnable ) {
    		$('.select2-selection').removeClass( 'empty-field-error' );
    		for( var i=0; i<rule.length-1; i++ ) {
    			
    			var discountInnerRules = [];
    			var obj = {};
    			var b2be_vari = [];
    			var b2be_simple = [];
    
    			$( rule[i] ).find( '.bulk-b2be-product-selection option:selected' ).each(function (indexInArray, valueOfElement) { 
    				if( 'b2be-vari' == $( valueOfElement ).attr('class') ) {
    					b2be_vari = [ ...b2be_vari, $( valueOfElement ).val() ];
    				}
    				else{
    					b2be_simple = [ ...b2be_simple, $( valueOfElement ).val() ];
    				}
    			});
    
    			obj['ruleId'] = $( rule[i] ).attr( 'data-template-id' );
    			obj['priority'] = $( rule[i] ).find( '#bulk-rule-priority' ).val();
    			
    			obj['is_role_based']     = $( rule[i] ).find('input[name="bulk-role-based-enable"]').prop( 'checked' );
    			obj['is_category_based'] = $( rule[i] ).find('input[name="bulk-category-based-enable"]').prop( 'checked' );
    			obj['is_product_based']  = $( rule[i] ).find('input[name="bulk-product-based-enable"]').prop( 'checked' );
    			obj['is_customer_based'] = $( rule[i] ).find('input[name="bulk-customer-based-enable"]').prop( 'checked' );
    			obj['is_quantity_based'] = $( rule[i] ).find('input[name="bulk-quantity-based-enable"]').prop( 'checked' );
    			obj['discount_format']  = 'default';
    			$( rule[i] ).find('input[class="bulk-discount-format"]').each( function( index, value ) {
					if ( $(value).is(':checked') ) {
						obj['discount_format'] = $(value).val();
					}
				})
				if( $( rule[i] ).find('input[name="bulk-role-based-enable"]').prop( 'checked' ) ) {
    				if( 0 == $( rule[i] ).find( '.bulk-b2be-role-selection' ).val().length ) {
    					$( rule[i] ).find( '.bulk-b2be-role-selection' ).siblings('.select2').find('.select2-selection').addClass( 'empty-field-error' );
    					error++;
    				}
    				obj['roles'] = $( rule[i] ).find( '.bulk-b2be-role-selection' ).val();
    			}
    			if( $( rule[i] ).find('input[name="bulk-category-based-enable"]').prop( 'checked' ) ) {
    				if( 0 == $( rule[i] ).find( '.bulk-b2be-category-selection' ).val().length ) {
    					$( rule[i] ).find( '.bulk-b2be-category-selection' ).siblings('.select2').find('.select2-selection').addClass( 'empty-field-error' );
    					error++;
    				}
    				obj['categories'] = $( rule[i] ).find( '.bulk-b2be-category-selection' ).val();
    			}
    			if( $( rule[i] ).find('input[name="bulk-product-based-enable"]').prop( 'checked' ) && b2be_simple ) {
    				if( 0 == $( rule[i] ).find( '.bulk-b2be-product-selection' ).val().length ) {
    					$( rule[i] ).find( '.bulk-b2be-product-selection' ).siblings('.select2').find('.select2-selection').addClass( 'empty-field-error' );
    					error++;
    				}
    				obj['products'] = b2be_simple;
    			}
    			if( $( rule[i] ).find('input[name="bulk-customer-based-enable"]').prop( 'checked' ) ) {
    				if( 0 == $( rule[i] ).find( '.bulk-b2be-customer-selection' ).val().length ) {
    					$( rule[i] ).find( '.bulk-b2be-customer-selection' ).siblings('.select2').find('.select2-selection').addClass( 'empty-field-error' );
    					error++;
    				}
    				obj['customer'] = $( rule[i] ).find( '.bulk-b2be-customer-selection' ).val();
    			}
			
    			innerRuleRows = $( rule[i] ).find( '#bulk-inner-rows' ).find( 'tr' );
    			for( var j=0; j<innerRuleRows.length; j++ ) {
    				var innerRule = {};
    
    				if( $( rule[i] ).find('input[name="bulk-quantity-based-enable"]').prop( 'checked' ) ) {
    					if( '' == $( innerRuleRows[j] ).find( 'input[name="bulk-quantity-based-discount-min"]' ).val() ) {
    						$( innerRuleRows[j] ).find( 'input[name="bulk-quantity-based-discount-min"]' ).addClass( 'empty-field-error' );
    						error++;
    					}
    					if( '' == $( innerRuleRows[j] ).find( 'input[name="bulk-quantity-based-discount-max"]' ).val() ) {
    						$( innerRuleRows[j] ).find( 'input[name="bulk-quantity-based-discount-max"]' ).addClass( 'empty-field-error' );
    						error++;
    					}
    					innerRule['minQuantity'] = $( innerRuleRows[j] ).find( 'input[name="bulk-quantity-based-discount-min"]' ).val();
    					innerRule['maxQuantity'] = $( innerRuleRows[j] ).find( 'input[name="bulk-quantity-based-discount-max"]' ).val();
    				}
    				if ( 'percentage' == $( innerRuleRows[j] ).find( '#bulk-type' ).val() ) {
    					if ( 100 < $( innerRuleRows[j] ).find( 'input[name="bulk-quantity-based-discount-amount"]' ).val() ) {
    						$( innerRuleRows[j] ).find( 'input[name="bulk-quantity-based-discount-amount"]' ).addClass( 'empty-field-error' );						
    						return;
    					}
    				}
    				if( '' == $( innerRuleRows[j] ).find( 'input[name="bulk-quantity-based-discount-amount"]' ).val() ) {
    					$( innerRuleRows[j] ).find( 'input[name="bulk-quantity-based-discount-amount"]' ).addClass( 'empty-field-error' );
    					error++;
    				}
    				innerRule['discount']     = $( innerRuleRows[j] ).find( 'input[name="bulk-quantity-based-discount-amount"]' ).val();
    				innerRule['type']         = $( innerRuleRows[j] ).find( '#bulk-type' ).val();
    				if( b2be_vari ) {
    					innerRule['variation_ids'] = b2be_vari;
    				}
    				discountInnerRules = [...discountInnerRules, innerRule];
    
    			}
    			obj['innerRule'] = discountInnerRules;
    
    			discountRules = [...discountRules, obj ];
    		}
    		if( error > 0 ) {
				$([document.documentElement, document.body]).animate({
					scrollTop: $( '.empty-field-error:first' ).closest('.bulk-inner-template').offset().top
				}, 1000);
				return;
			}
        }

		$.ajax({
			type: "POST",
			url:  bulkAjax.ajax_url,
			data: {
				action: 'save_discount_rules',
				'discountRules' : discountRules,
				'isEnable' : isEnable,
			},
			success: function (response) {
				$( '#bulk-rule-priority' ).removeClass("priorityError");
				if( response != 'true' ) {
					var resp = JSON.parse( response );
					$.each( resp, function( key, value ) {
						$( '#bulk-inner-template-'+ value ).find( '#bulk-rule-priority' ).addClass("priorityError");
					});
					
					$([document.documentElement, document.body]).animate({
						scrollTop: $( '#bulk-inner-template-'+ resp[0] ).offset().top
					}, 2000);
					
				}
				else{   
					window.onbeforeunload = null;
					location.reload();
				}
			}
		});

	});

})

function b2be_select2( element, placeholder )
{
	jQuery( element ).select2(
		{
			closeOnSelect: false,
			placeholder: placeholder,
			allowHtml: true,
			allowClear: true,
			tags: false
		}
	);
}

function bulk_fields_toggle( child, parent ) {

	for( var i=0; i < jQuery( child ).length-1; i++ ) {		
		var parentId = jQuery( child )[i].closest(".bulk-inner-template").id; 
		if( true == jQuery( child )[i].checked ) {
			jQuery( '#' + parentId ).find( jQuery( parent) ).show('slow');
			if( '.bulk-quantity-based-enable' == child ) {
				var row_to_hide = jQuery( child ).closest(".bulk-inner-template").find( jQuery( '.hide-by-default' ) );
				for (let index = 0; index < row_to_hide.length; index++) {
					if ( 0 != index ) {
						// jQuery( row_to_hide[index] ).show();
					}
					
				}
			}
		}
		else {
			
			jQuery( '#' + parentId ).find( jQuery(parent) ).hide('slow');
			if( '.bulk-quantity-based-enable' == child ) {
				var row_to_hide = jQuery( child ).closest(".bulk-inner-template").find( jQuery( '.hide-by-default' ) )
				for (let index = 0; index < row_to_hide.length; index++) {
					if ( 0 != index ) {
						// jQuery( row_to_hide[index] ).hide();
					}
					
				}
			}
		}

	}

	jQuery( document.body ).on('click', child, function() {

		if( true == jQuery( this ).prop("checked") ) {
			jQuery( this ).closest(".bulk-inner-template").find(jQuery(parent)).show('slow');
			if( '.bulk-quantity-based-enable' == child ) {
				var row_to_hide = jQuery( this ).closest(".bulk-inner-template").find( jQuery( '.hide-by-default' ) );
			}
		}
		else {
			jQuery( this ).closest(".bulk-inner-template").find(jQuery(parent)).hide('slow');
			if( '.bulk-quantity-based-enable' == child ) {
				var row_to_hide = jQuery( this ).closest(".bulk-inner-template").find( jQuery( '.hide-by-default' ) )
				for (let index = 0; index < row_to_hide.length; index++) {
					if ( 0 != index ) {
						jQuery( row_to_hide[index] ).remove();
					}
					
				}
			}
		}
	})

}
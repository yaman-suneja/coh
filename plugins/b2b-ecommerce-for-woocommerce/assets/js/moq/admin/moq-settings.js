jQuery(document).ready(function($) {

	moq_fields_toggle( ".moq-role-based-enable", ".role-discount" );
	moq_fields_toggle( ".moq-category-based-enable", ".category-discount" );
	moq_fields_toggle( ".moq-customer-based-enable", ".customer-discount" );
	moq_fields_toggle( ".moq-product-based-enable",  ".product-discount" );
	quantity_format_toggle();

	b2be_select2( '#moq-template .moq-b2be-role-selection', 'Select Role' );
	b2be_select2( '#moq-template .moq-b2be-category-selection', 'Select Category' );
	b2be_select2( '#moq-template .moq-b2be-customer-selection', 'Select Customer' );
	b2be_select2( '#moq-template .moq-b2be-product-selection', 'Select Product' );

	$( document.body ).on( 'click', '#add_discount_rule', function() {

		var new_div  = $('#template div.moq-inner-template').clone();
		var new_id = $('div.moq-inner-template').last().attr('data-template-id');
		
		new_div.attr( "data-template-id", ( parseInt( new_id )+1 ) );
		new_div.attr( "id", 'moq-inner-template-' + ( parseInt( new_id )+1 ) );
		
		$( '#template div.moq-inner-template' ).attr( "data-template-id", ( parseInt( new_id )+1 ) );
		$( '#template div.moq-inner-template' ).find( '#moq-title-discounts' ).find( 'span' ).text( 'Rule ' + ( parseInt( new_id )+1 ) );
		
		new_div.find( '.moq-b2be-role-selection' ).attr( 'id', 'moq-b2be-role-selection-' + ( parseInt( new_id )+1 ) )
		new_div.find( '.moq-b2be-category-selection' ).attr( 'id', 'moq-b2be-category-selection-' + ( parseInt( new_id )+1 ) )
		new_div.find( '.moq-b2be-customer-selection' ).attr( 'id', 'moq-b2be-customer-selection-' + ( parseInt( new_id )+1 ) )
		new_div.find( '.moq-b2be-product-selection' ).attr( 'id', 'moq-b2be-product-selection-' + ( parseInt( new_id )+1 ) )
		new_div.find( '#moq-quanity-format-range' ).attr( 'name', 'moq-quanity-format-' + ( parseInt( new_id )+1 ) )
		new_div.find( '#moq-quanity-format-multi' ).attr( 'name', 'moq-quanity-format-' + ( parseInt( new_id )+1 ) )

		$("#moq-template").append( new_div );
		b2be_select2( '#moq-b2be-role-selection-' + ( parseInt( new_id )+1 ) , 'Select Role' );
		b2be_select2( '#moq-b2be-category-selection-' + ( parseInt( new_id )+1 ), 'Select Category' );
		b2be_select2( '#moq-b2be-customer-selection-' + ( parseInt( new_id )+1 ), 'Select Customer' );
		b2be_select2( '#moq-b2be-product-selection-' + ( parseInt( new_id )+1 ), 'Select Product' );
		
		new_div.find( '#moq-title-discounts' ).find( 'span' ).text( 'Rule ' + ( parseInt( new_id )+1 ));
	});

    $( '.enable-moq-discount' ).on('click', function() {
        if( true == $(this).is(':checked') ) {
            $( '.enable-moq-discount' ).attr( "checked", true );
			$( '.moq-inner-template' ).show();
			$( '#add_discount_rule' ).show();
        }
        else {
            $( '.enable-moq-discount' ).attr( "checked", false );
			$( '.moq-inner-template' ).hide();
			$( '#add_discount_rule' ).hide();
        }
        
    });

	$( document.body ).on( 'click', '#remove_moq_quantity_range', function() {
		$(this).closest('tr').remove();
	});

	$( document.body ).on( 'click', '.remove-rule', function() {
		
		$(this).closest('.moq-inner-template').remove();

		var next_div_id = 0;
		$( '.moq-template' ).find( 'div.moq-inner-template' ).each(function(index, value ) {
			$( value ).find('div#moq-title-discounts span').text( 'Rule ' + ( parseInt( index )+1 ) );
			next_div_id = index+1;
		});
		$( '#template .moq-inner-template' ).attr('data-template-id', parseInt( next_div_id ) );
		
		$('.moq-template').find( '.moq-inner-template' ).first().find( '.remove-rule' ).hide();
	});

	$( document.body ).on( 'click', '#save_discount_rule', function() {

		$('span').removeClass( 'empty-field-error' );
		var rule = $( '.moq-inner-template' );
		var innerRuleRows = '';
		var moqRules = [];
		var error = 0;
		var isEnable = $( '#enable-moq-discount' ).is(':checked');

        if( isEnable ) {
			for( var i=0; i<rule.length-1; i++ ) {
								
				var moqInnerRules = [];
				var obj = {};
				var b2be_vari = [];
				var b2be_simple = [];
				let quantity_format = $( rule[i] ).find('input[class="moq-quanity-format"]:checked').val() || 'range';

				$( rule[i] ).find( '.moq-b2be-product-selection option:selected' ).each(function (indexInArray, valueOfElement) { 
					if( 'b2be-vari' == $( valueOfElement ).attr('class') ) {
						b2be_vari = [ ...b2be_vari, $( valueOfElement ).val() ];
					}
					else{
						b2be_simple = [ ...b2be_simple, $( valueOfElement ).val() ];
					}
				});

				obj['ruleId'] = $( rule[i] ).attr( 'data-template-id' );
				obj['priority'] = $( rule[i] ).find( '#moq-rule-priority' ).val();
				
				obj['is_role_based']     = $( rule[i] ).find('input[name="moq-role-based-enable"]').prop( 'checked' );
				obj['is_category_based'] = $( rule[i] ).find('input[name="moq-category-based-enable"]').prop( 'checked' );
				obj['is_customer_based'] = $( rule[i] ).find('input[name="moq-customer-based-enable"]').prop( 'checked' );
				obj['is_product_based']  = $( rule[i] ).find('input[name="moq-product-based-enable"]').prop( 'checked' );
				obj['quantity_format']   = quantity_format;
				
				if( $( rule[i] ).find('input[name="moq-role-based-enable"]').prop( 'checked' ) ) {
					if( 0 == $( rule[i] ).find( '.moq-b2be-role-selection' ).val().length ) {
						$( rule[i] ).find( '.moq-b2be-role-selection' ).siblings('.select2').find('.select2-selection').addClass( 'empty-field-error' );
						error++;
					}
					obj['roles'] = $( rule[i] ).find( '.moq-b2be-role-selection' ).val();
				}
				if( $( rule[i] ).find('input[name="moq-category-based-enable"]').prop( 'checked' ) ) {
					if( 0 == $( rule[i] ).find( '.moq-b2be-category-selection' ).val().length ) {
						$( rule[i] ).find( '.moq-b2be-category-selection' ).siblings('.select2').find('.select2-selection').addClass( 'empty-field-error' );
						error++;
					}
					obj['categories'] = $( rule[i] ).find( '.moq-b2be-category-selection' ).val();
				}
				if( $( rule[i] ).find('input[name="moq-product-based-enable"]').prop( 'checked' ) && b2be_simple ) {
					if( 0 == $( rule[i] ).find( '.moq-b2be-product-selection' ).val().length ) {
						$( rule[i] ).find( '.moq-b2be-product-selection' ).siblings('.select2').find('.select2-selection').addClass( 'empty-field-error' );
						error++;
					}
					obj['products'] = b2be_simple;
				}
				if( $( rule[i] ).find('input[name="moq-customer-based-enable"]').prop( 'checked' ) ) {
					if( 0 == $( rule[i] ).find( '.moq-b2be-customer-selection' ).val().length ) {
						$( rule[i] ).find( '.moq-b2be-customer-selection' ).siblings('.select2').find('.select2-selection').addClass( 'empty-field-error' );
						error++;
					}	
					obj['customer'] = $( rule[i] ).find( '.moq-b2be-customer-selection' ).val();
				}
				innerRuleRows = $( rule[i] ).find( '#moq-inner-rows' ).find( 'tr' );
				
				for( var j=0; j<innerRuleRows.length; j++ ) {
					var innerRule = {};
					if( 'range' == quantity_format ) {
						if( '' == $( innerRuleRows[j] ).find( 'input[name="moq-quantity-based-discount-min"]' ).val() ) {
							$( innerRuleRows[j] ).find( 'input[name="moq-quantity-based-discount-min"]' ).addClass( 'empty-field-error' );
							error++;
						}
						if( '' == $( innerRuleRows[j] ).find( 'input[name="moq-quantity-based-discount-max"]' ).val() ) {
							$( innerRuleRows[j] ).find( 'input[name="moq-quantity-based-discount-max"]' ).addClass( 'empty-field-error' );
							error++;
						}
						innerRule['minQuantity'] = parseInt( $( innerRuleRows[j] ).find( 'input[name="moq-quantity-based-discount-min"]' ).val() );
						innerRule['maxQuantity'] = parseInt( $( innerRuleRows[j] ).find( 'input[name="moq-quantity-based-discount-max"]' ).val() );
					}
					else if ( 'multiplier' == quantity_format ) {
						if( '' == $( innerRuleRows[j] ).find( 'input[name="moq-quantity-based-discount-multiplier"]' ).val() ) {
							$( innerRuleRows[j] ).find( 'input[name="moq-quantity-based-discount-multiplier"]' ).addClass( 'empty-field-error' );
							error++;
						}
						innerRule['multiplier'] = parseInt( $( innerRuleRows[j] ).find( 'input[name="moq-quantity-based-discount-multiplier"]' ).val() );
					}

					if( b2be_vari ) {
						innerRule['variation_ids'] = b2be_vari;
					}
					moqInnerRules = [...moqInnerRules, innerRule];
				}
				obj['innerRule'] = moqInnerRules;

				moqRules = [...moqRules, obj];

			}
			if( error > 0 ) {
				$([document.documentElement, document.body]).animate({
					scrollTop: $( '.empty-field-error:first' ).closest('.moq-inner-template').offset().top
				}, 1000);
				return;
			}
			$('span').removeClass( 'empty-field-error' );

		}

		$.ajax({
			type: "POST",
			url:  moqAjax.ajax_url,
			data: {
				action: 'save_moq_rules',
				'moqRules' : moqRules,
				'isEnable' : isEnable,
			},
			success: function (response) {
				$( '#moq-rule-priority' ).removeClass("priorityError");
				if( response != 'true' ) {
					var resp = JSON.parse( response );
					$.each( resp, function( key, value ) {
						$( '#moq-inner-template-'+ value ).find( '#moq-rule-priority' ).addClass("priorityError");
					});

					$([document.documentElement, document.body]).animate({
						scrollTop: $( '#moq-inner-template-'+ resp[0] ).offset().top
					}, 1000);
			
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

function moq_fields_toggle( child, parent ) {
	for( var i=0; i < jQuery( child ).length-1; i++ ) {
		
		var parentId = jQuery( child )[i].closest(".moq-inner-template").id; 
		if( true == jQuery( child )[i].checked ) {
			jQuery( '#' + parentId ).find( jQuery(parent) ).show('slow');
		}
		else {
			jQuery( '#' + parentId ).find( jQuery(parent) ).hide('slow');
		}

	}

	jQuery( document.body ).on('click', child, function() {

		if( true == jQuery( this ).prop("checked") ) {
			jQuery( this ).closest(".moq-inner-template").find(jQuery(parent)).show('slow');
		}
		else {
			jQuery( this ).closest(".moq-inner-template").find(jQuery(parent)).hide('slow');
		}
	})

}

function quantity_format_toggle() {
	
	jQuery( '.moq-quanity-format' ).each(function (index, value) {
		var parentId = jQuery(value).closest(".moq-inner-template").attr('id'); 
		if( true == jQuery(value).is(':checked') && 'range' == jQuery(value).val() ) {
			jQuery( '#' + parentId ).find( jQuery('.quantity-discount') ).show();
			jQuery( '#' + parentId ).find( jQuery('.quantity-discount-multiplier') ).hide();
		}
		else if( true == jQuery(value).is(':checked') && 'multiplier' == jQuery(value).val() ) {
			jQuery( '#' + parentId ).find( jQuery('.quantity-discount-multiplier') ).show();
			jQuery( '#' + parentId ).find( jQuery('.quantity-discount') ).hide();
		}
	});
	jQuery( document.body ).on('click', '.moq-quanity-format', function() {

		if( true == jQuery( this ).is(':checked') && 'range' == jQuery( this ).val() ) {
			jQuery( this ).closest(".moq-inner-template").find(jQuery('.quantity-discount')).show();
			jQuery( this ).closest(".moq-inner-template").find(jQuery('.quantity-discount-multiplier')).hide();
		}
		else if( true == jQuery( this ).is(':checked') && 'multiplier' == jQuery( this ).val() ) {
			jQuery( this ).closest(".moq-inner-template").find(jQuery('.quantity-discount-multiplier')).show();
			jQuery( this ).closest(".moq-inner-template").find(jQuery('.quantity-discount')).hide();
		}
	})
}
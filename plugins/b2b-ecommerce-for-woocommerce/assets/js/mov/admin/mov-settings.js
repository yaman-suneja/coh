jQuery(document).ready(function($){

	mov_fields_toggle( ".mov-role-based-enable", ".role-discount" );
	mov_fields_toggle( ".mov-category-based-enable", ".category-discount" );
	mov_fields_toggle( ".mov-customer-based-enable", ".customer-discount" );
	mov_fields_toggle( ".mov-product-based-enable",  ".product-discount" );

	b2be_select2( '#mov-template .mov-b2be-role-selection', 'Select Role' );
	b2be_select2( '#mov-template .mov-b2be-category-selection', 'Select Category' );
	b2be_select2( '#mov-template .mov-b2be-customer-selection', 'Select Customer' );
	b2be_select2( '#mov-template .mov-b2be-product-selection', 'Select Product' );

	$( document.body ).on( 'click', '#add_discount_rule', function() {

		var new_div  = $('#template div.mov-inner-template').clone();
		var new_id = $('div.mov-inner-template').last().attr('data-template-id');
		
		new_div.attr( "data-template-id", ( parseInt( new_id )+1 ) );
		new_div.attr( "id", 'mov-inner-template-' + ( parseInt( new_id )+1 ) );
		
		$( '#template div.mov-inner-template' ).attr( "data-template-id", ( parseInt( new_id )+1 ) );
		$( '#template div.mov-inner-template' ).find( '#mov-title-discounts' ).find( 'span' ).text( 'Rule ' + ( parseInt( new_id )+1 ) );
		
		new_div.find( '.mov-b2be-role-selection' ).attr( 'id', 'mov-b2be-role-selection-' + ( parseInt( new_id )+1 ) )
		new_div.find( '.mov-b2be-category-selection' ).attr( 'id', 'mov-b2be-category-selection-' + ( parseInt( new_id )+1 ) )
		new_div.find( '.mov-b2be-customer-selection' ).attr( 'id', 'mov-b2be-customer-selection-' + ( parseInt( new_id )+1 ) )
		new_div.find( '.mov-b2be-product-selection' ).attr( 'id', 'mov-b2be-product-selection-' + ( parseInt( new_id )+1 ) )
		
		$("#mov-template").append( new_div );
		b2be_select2( '#mov-b2be-role-selection-' + ( parseInt( new_id )+1 ) , 'Select Role' );
		b2be_select2( '#mov-b2be-category-selection-' + ( parseInt( new_id )+1 ), 'Select Category' );
		b2be_select2( '#mov-b2be-customer-selection-' + ( parseInt( new_id )+1 ), 'Select Customer' );
		b2be_select2( '#mov-b2be-product-selection-' + ( parseInt( new_id )+1 ), 'Select Product' );
		
		new_div.find( '#mov-title-discounts' ).find( 'span' ).text( 'Rule ' + ( parseInt( new_id )+1 ));
	});

    $( '.enable-mov-discount' ).on('click', function() {
        if( true == $(this).is(':checked') ) {
            $( '.enable-mov-discount' ).attr( "checked", true );
			$( '.mov-inner-template' ).show();
			$( '#add_discount_rule' ).show();
        }
        else {
            $( '.enable-mov-discount' ).attr( "checked", false );
			$( '.mov-inner-template' ).hide();
			$( '#add_discount_rule' ).hide();
        }
        
    });

	$( document.body ).on( 'click', '#remove_mov_quantity_range', function() {
		$(this).closest('tr').remove();
	});

	$( document.body ).on( 'click', '.remove-rule', function() {
		
		$(this).closest('.mov-inner-template').remove();

		var next_div_id = 0;
		$( '.mov-template' ).find( 'div.mov-inner-template' ).each(function(index, value ) {
			$( value ).find('div#mov-title-discounts span').text( 'Rule ' + ( parseInt( index )+1 ) );
			next_div_id = index+1;
		});
		$( '#template .mov-inner-template' ).attr('data-template-id', parseInt( next_div_id ) );
		
		$('.mov-template').find( '.mov-inner-template' ).first().find( '.remove-rule' ).hide();
	});

	$( document.body ).on( 'click', '#save_discount_rule', function() {

		$('span').removeClass( 'empty-field-error' );
		var rule = $( '.mov-inner-template' );
		var innerRuleRows = '';
		var movRules = [];
		var error = 0;
		var isEnable = $( '#enable-mov-discount' ).is(':checked');

        if( isEnable ) {
			for( var i=0; i<rule.length-1; i++ ) {
				
				var movInnerRules = [];
				var obj = {};
				var b2be_vari = [];
				var b2be_simple = [];

				$( rule[i] ).find( '.mov-b2be-product-selection option:selected' ).each(function (indexInArray, valueOfElement) { 
					if( 'b2be-vari' == $( valueOfElement ).attr('class') ) {
						b2be_vari = [ ...b2be_vari, $( valueOfElement ).val() ];
					}
					else{
						b2be_simple = [ ...b2be_simple, $( valueOfElement ).val() ];
					}
				});

				obj['ruleId'] = $( rule[i] ).attr( 'data-template-id' );
				obj['priority'] = $( rule[i] ).find( '#mov-rule-priority' ).val();
				
				obj['is_role_based']     = $( rule[i] ).find('input[name="mov-role-based-enable"]').prop( 'checked' );
				obj['is_category_based'] = $( rule[i] ).find('input[name="mov-category-based-enable"]').prop( 'checked' );
				obj['is_customer_based'] = $( rule[i] ).find('input[name="mov-customer-based-enable"]').prop( 'checked' );
				obj['is_product_based'] = $( rule[i] ).find('input[name="mov-product-based-enable"]').prop( 'checked' );
				
				if( $( rule[i] ).find('input[name="mov-role-based-enable"]').prop( 'checked' ) ) {
					if( 0 == $( rule[i] ).find( '.mov-b2be-role-selection' ).val().length ) {
						$( rule[i] ).find( '.mov-b2be-role-selection' ).siblings('.select2').find('.select2-selection').addClass( 'empty-field-error' );
						error++;
					}
					obj['roles'] = $( rule[i] ).find( '.mov-b2be-role-selection' ).val();
				}
				if( $( rule[i] ).find('input[name="mov-category-based-enable"]').prop( 'checked' ) ) {
					if( 0 == $( rule[i] ).find( '.mov-b2be-category-selection' ).val().length ) {
						$( rule[i] ).find( '.mov-b2be-category-selection' ).siblings('.select2').find('.select2-selection').addClass( 'empty-field-error' );
						error++;
					}
					obj['categories'] = $( rule[i] ).find( '.mov-b2be-category-selection' ).val();
				}
				if( $( rule[i] ).find('input[name="mov-product-based-enable"]').prop( 'checked' ) && b2be_simple ) {
					if( 0 == $( rule[i] ).find( '.mov-b2be-product-selection' ).val().length ) {
						$( rule[i] ).find( '.mov-b2be-product-selection' ).siblings('.select2').find('.select2-selection').addClass( 'empty-field-error' );
						error++;
					}
					obj['products'] = b2be_simple;
				}
				if( $( rule[i] ).find('input[name="mov-customer-based-enable"]').prop( 'checked' ) ) {
					if( 0 == $( rule[i] ).find( '.mov-b2be-customer-selection' ).val().length ) {
						$( rule[i] ).find( '.mov-b2be-customer-selection' ).siblings('.select2').find('.select2-selection').addClass( 'empty-field-error' );
						error++;
					}	
					obj['customer'] = $( rule[i] ).find( '.mov-b2be-customer-selection' ).val();
				}
				
				innerRuleRows = $( rule[i] ).find( '#mov-inner-rows' ).find( 'tr' );
				
				for( var j=0; j<innerRuleRows.length; j++ ) {
					var innerRule = {};
					if( '' == $( innerRuleRows[j] ).find( 'input[name="mov-value-based-limit-min"]' ).val() && '' == $( innerRuleRows[j] ).find( 'input[name="mov-value-based-limit-max"]' ).val() ) {
						$( innerRuleRows[j] ).find( 'input[name="mov-value-based-limit-min"]' ).addClass( 'empty-field-error' );
						$( innerRuleRows[j] ).find( 'input[name="mov-value-based-limit-max"]' ).addClass( 'empty-field-error' );
						error++;
					}
					innerRule['minValue'] = $( innerRuleRows[j] ).find( 'input[name="mov-value-based-limit-min"]' ).val();
					innerRule['maxValue'] = $( innerRuleRows[j] ).find( 'input[name="mov-value-based-limit-max"]' ).val();
					if( b2be_vari ) {
						innerRule['variation_ids'] = b2be_vari;
					}
					movInnerRules = [...movInnerRules, innerRule];
				}
				obj['innerRule'] = movInnerRules;

				movRules = [...movRules, obj];

			}
			if( error > 0 ) {
				$([document.documentElement, document.body]).animate({
					scrollTop: $( '.empty-field-error:first' ).closest('.mov-inner-template').offset().top
				}, 1000);
				return;
			}
		}

		$.ajax({
			type: "POST",
			url:  movAjax.ajax_url,
			data: {
				action: 'save_mov_rules',
				'movRules' : movRules,
				'isEnable' : isEnable,
			},
			success: function (response) {
				$( '#mov-rule-priority' ).removeClass("priorityError");
				if( response != 'true' ) {
					var resp = JSON.parse( response );
					$.each( resp, function( key, value ) {
						$( '#mov-inner-template-'+ value ).find( '#mov-rule-priority' ).addClass("priorityError");
					});

					$([document.documentElement, document.body]).animate({
						scrollTop: $( '#mov-inner-template-'+ resp[0] ).offset().top
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

function mov_fields_toggle( child, parent ) {
	for( var i=0; i < jQuery( child ).length-1; i++ ) {
		
		var parentId = jQuery( child )[i].closest(".mov-inner-template").id; 
		if( true == jQuery( child )[i].checked ) {
			jQuery( '#' + parentId ).find( jQuery(parent) ).show('slow');
		}
		else {
			jQuery( '#' + parentId ).find( jQuery(parent) ).hide('slow');
		}

	}

	jQuery( document.body ).on('click', child, function() {

		

		if( true == jQuery( this ).prop("checked") ) {
			jQuery( this ).closest(".mov-inner-template").find(jQuery(parent)).show('slow');
			if( '.mov-quantity-based-enable' == child ) {
				var row_to_hide = jQuery( this ).closest(".mov-inner-template").find( jQuery( '.hide-by-default' ) );
				jQuery( row_to_hide[0] ).find('#add_mov_quantity_range').show();
				for (let index = 0; index < row_to_hide.length; index++) {
					if ( 0 != index ) {
						jQuery( row_to_hide[index] ).show();
					}
					
				}
			}
		}
		else {
			jQuery( this ).closest(".mov-inner-template").find(jQuery(parent)).hide('slow');
			if( '.mov-quantity-based-enable' == child ) {
				var row_to_hide = jQuery( this ).closest(".mov-inner-template").find( jQuery( '.hide-by-default' ) )
				jQuery( row_to_hide[0] ).find('#add_mov_quantity_range').hide();
				for (let index = 0; index < row_to_hide.length; index++) {
					if ( 0 != index ) {
						jQuery( row_to_hide[index] ).hide();
					}
					
				}
			}
		}
	})

}
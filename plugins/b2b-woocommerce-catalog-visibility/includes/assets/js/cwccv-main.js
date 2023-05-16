jQuery( document ).ready( function($) {
	
	$(".role_setting").show();
	$(".group_setting").show();

	// buttons classes list
	let buttons_class= [
		'.cwccv_cancel_role_button',
		'.cwccv_save_role_button',
		'.cwccv_cancel_group_button',
		'.cwccv_save_group_button',
		'.cwccv_edit_role_button',
		'.cwccv_delete_role_button',
		'.cwccv_edit_group_button',
		'.cwccv_delete_group_button',
	];
	
	// Initializing multi-select dropdowns
	function cwccv_select2( element, placeholder='' )
	{
		$( element ).select2(
			{
				closeOnSelect: false,
				placeholder: placeholder,
				allowHtml: true,
				allowClear: true,
				tags: false,
				width: '100%'
			}
		);
	}

	// Hide price for non login user toggle click event
	$('.cwccv_hide_price_for_non_login_toggle').click(function(){
		if( $(this).is(':checked') ){
			$('.cwccv_hide_price_option').show();
			if($('.cwccv_hide_whole_catalog_price_toggle').is(':checked')){
				$('.cwccv_hide_catalog_category_select').attr('disabled', true);
				$('.cwccv_hide_catalog_product_select').attr('disabled', true);
			}
			else{
				$('.cwccv_hide_catalog_category_select').removeAttr('disabled');
				$('.cwccv_hide_catalog_product_select').removeAttr('disabled');
			}
		}
		else{
			$('.cwccv_hide_price_option').hide();
			$('.cwccv_hide_catalog_category_select').attr('disabled', true);
			$('.cwccv_hide_catalog_product_select').attr('disabled', true);
		}
	});

	// Hide price for non login user toggle click event
	$('.cwccv_hide_product_for_non_login_toggle').click(function(){
		if( $(this).is(':checked') ){
			$('.cwccv_hide_product_option').show();
			if($('.cwccv_hide_whole_catalog_product_toggle').is(':checked')){
				$('.cwccv_hide_catalog_category_select_by_product').attr('disabled', true);
				$('.cwccv_hide_catalog_product_select_by_product').attr('disabled', true);
			}
			else{
				$('.cwccv_hide_catalog_category_select_by_product').removeAttr('disabled');
				$('.cwccv_hide_catalog_product_select_by_product').removeAttr('disabled');
			}
		}
		else{
			$('.cwccv_hide_product_option').hide();
			$('.cwccv_hide_catalog_category_select_by_product').attr('disabled', true);
			$('.cwccv_hide_catalog_product_select_by_product').attr('disabled', true);
		}
	});

	// Add class to all buttons
	$(buttons_class.join(',')).addClass('button-primary');
	
	// Individual customer enable disable toggle event
	$('.cwccv_individual_customer_settings_enable_toggle').click(function(){
		if( $(this).is(':checked') ){
			$('.cwccv_individual_customer_titles').show();
			$('.cwccv_individual_user_row').show();
		}
		else{
			$('.cwccv_individual_customer_titles').hide();
			$('.cwccv_individual_user_row').hide();
		}
	});
	// User roles enable disable toggle event
	$('.cwccv_user_roles_settings_enable_toggle').click(function(){
		if( $(this).is(':checked') ){
			$('.cwccv_user_roles_titles').show();
			$('.cwccv_user_roles_row').show();
		}
		else{
			$('.cwccv_user_roles_titles').hide();
			$('.cwccv_user_roles_row').hide();
		}
	});
	// User Group enable disable toggle event
	$('.cwccv_user_groups_settings_enable_toggle').click(function(){
		if( $(this).is(':checked') ){
			$('.cwccv_user_groups_titles').show();
			$('.cwccv_user_groups_row').show();
		}
		else{
			$('.cwccv_user_groups_titles').hide();
			$('.cwccv_user_groups_row').hide();
		}
	});
	// Price Tier enable disable toggle event
	$('.cwccv_price_tier_settings_enable_toggle').click(function(){
		if( $(this).is(':checked') ){
			$('.cwccv_price_tier_titles').show();
			$('.cwccv_price_tier_row').show();
		}
		else{
			$('.cwccv_price_tier_titles').hide();
			$('.cwccv_price_tier_row').hide();
		}
	});
	// Geo Location enable disable toggle event
	$('.cwccv_geo_location_settings_enable_toggle').click(function(){
		if( $(this).is(':checked') ){
			$('.cwccv_geo_location_titles').show();
			$('.cwccv_geo_location_row').show();
		}
		else{
			$('.cwccv_geo_location_titles').hide();
			$('.cwccv_geo_location_row').hide();
		}
	});
	// Hide save and cancel buton for role and group
	$('.cwccv_cancel_role_button').hide();
	$('.cwccv_cancel_group_button').hide();
	$('.cwccv_save_role_button').hide();
	$('.cwccv_save_group_button').hide();

	// Hide whole catalog price toggle click event
	$('.cwccv_hide_whole_catalog_price_toggle').click(function(){
		if( $(this).is(':checked') ){
			$('.cwccv_hide_catalog_category_select').attr('disabled', true);
			$('.cwccv_hide_catalog_product_select').attr('disabled', true);
		}
		else{
			$('.cwccv_hide_catalog_category_select').removeAttr('disabled');
			$('.cwccv_hide_catalog_product_select').removeAttr('disabled');
		}
	});

	$('.cwccv_hide_whole_catalog_product_toggle').click(function(){
		if( $(this).is(':checked') ){
			$('.cwccv_hide_catalog_category_select_by_product').attr('disabled', true);
			$('.cwccv_hide_catalog_product_select_by_product').attr('disabled', true);
		}
		else{
			$('.cwccv_hide_catalog_category_select_by_product').removeAttr('disabled');
			$('.cwccv_hide_catalog_product_select_by_product').removeAttr('disabled');
		}
	});

	/**
	 * Removes the attribute "selected" fomr dropdown.
	 * @param array options
	 * @since 1.1.2.1
	 * @return array options
	 */
	function remove_attribute_selected( options ){
		$( options ).each(function(){
			$(this).removeAttr('selected');
		});
		return options;
	}

	cwccv_select2( '.cwccv_selectpicker' );

	// Click event of repeater button of individual user section
	$('.cwccv_individual_customer_repeater_field_button').click(function(){
		
		let template_row = $('.cwccv_individual_user_row_template');
		let row = $('.cwccv_individual_user_row').last();

		let last_row_number = $(row).data('id');
		let row_copy = $(template_row).clone();
		
		//increamenting the name array index
		let new_row_number = parseInt(last_row_number)+1;
		$(row_copy).find('.cwccv_individual_customer_customer_name_select').attr('name', 'cwccv_individual_customer_customer_name_select' + '['+ new_row_number.toString() +'][]');
		$(row_copy).find('.cwccv_individual_customer_category_select').attr('name', 'cwccv_individual_customer_category_select' + '[' + new_row_number.toString() +'][]');
		$(row_copy).find('.cwccv_individual_customer_product_select').attr('name', 'cwccv_individual_customer_product_select' + '[' + new_row_number.toString() +'][]');
		$(row_copy).find('input[type=radio]').attr('name', 'cwccv_individual_customer_products_show_hide_radio['+ new_row_number.toString() +']');
		$(row_copy).attr('data-id', new_row_number.toString());
		$(row_copy).attr('class', 'cwccv_individual_user_row');

		$(row_copy).find('.cwccv_individual_customer_customer_name_select').attr('id', 'cwccv_individual_customer_customer_name_select_' + new_row_number.toString());
		$(row_copy).find('.cwccv_individual_customer_category_select').attr('id', 'cwccv_individual_customer_category_select_' + new_row_number.toString());
		$(row_copy).find('.cwccv_individual_customer_product_select').attr('id', 'cwccv_individual_customer_product_select_' + new_row_number.toString());
		
		$(row_copy).find('option:selected').removeAttr('selected');
		$(row_copy).find('input[type=radio]:checked').attr('checked', false);
		$(row_copy).find('.cwccv_individual_customer_products_show_hide_radio').first().attr('checked', true);
		$(row_copy).insertAfter(row);
		
		cwccv_select2( '#cwccv_individual_customer_customer_name_select_' + new_row_number.toString() );
		cwccv_select2( '#cwccv_individual_customer_category_select_' + new_row_number.toString() );
		cwccv_select2( '#cwccv_individual_customer_product_select_' + new_row_number.toString() );

	});
	// End click event of repeater button of individual user
	
	// Click event of user Roles repeater button
	$('.cwccv_user_roles_repeater_field_button').click(function(){
		
		let template_row = $('.cwccv_user_roles_row_template');
		let row = $('.cwccv_user_roles_row').last();

		let last_row_number = $(row).data('id');
		let row_copy = $(template_row).clone();

		//increamenting the name array index
		let new_row_number = parseInt(last_row_number)+1;
		$(row_copy).find('.cwccv_user_roles_roles_name_select').attr('name', 'cwccv_user_roles_roles_name_select' + '['+ new_row_number.toString() +'][]');
		$(row_copy).find('.cwccv_user_roles_category_select').attr('name', 'cwccv_user_roles_category_select' + '[' + new_row_number.toString() +'][]');
		$(row_copy).find('.cwccv_user_roles_product_select').attr('name', 'cwccv_user_roles_product_select' + '[' + new_row_number.toString() +'][]');
		$(row_copy).find('input[type=radio]').attr('name', 'cwccv_user_roles_show_hide_radio['+ new_row_number.toString() +']');
		$(row_copy).attr('data-id', new_row_number.toString());
		$(row_copy).attr('class', 'cwccv_user_roles_row');

		$(row_copy).find('.cwccv_user_roles_roles_name_select').attr('id', 'cwccv_user_roles_roles_name_select_' + new_row_number.toString());
		$(row_copy).find('.cwccv_user_roles_category_select').attr('id', 'cwccv_user_roles_category_select_' + new_row_number.toString());
		$(row_copy).find('.cwccv_user_roles_product_select').attr('id', 'cwccv_user_roles_product_select_' + new_row_number.toString());

		$(row_copy).find('option:selected').removeAttr('selected');
		$(row_copy).find('input[type=radio]:checked').attr('checked', false);
		$(row_copy).find('.cwccv_user_roles_show_hide_radio').first().attr('checked', true);
		
		$(row_copy).insertAfter(row);
		
		cwccv_select2( '#cwccv_user_roles_roles_name_select_' + new_row_number.toString() );
		cwccv_select2( '#cwccv_user_roles_category_select_' + new_row_number.toString() );
		cwccv_select2( '#cwccv_user_roles_product_select_' + new_row_number.toString() );

	});

	// Click event of user groups repeater button
	$('.cwccv_user_groups_repeater_field_button').click(function(){
		
		let template_row = $('.cwccv_user_groups_row_template');
		let row = $('.cwccv_user_groups_row').last();

		let last_row_number = $(row).data('id');
		let row_copy = $(template_row).clone();
		
		//increamenting the name array index
		let new_row_number = parseInt(last_row_number)+1;
		$(row_copy).find('.cwccv_user_groups_groups_name_select').attr('name', 'cwccv_user_groups_groups_name_select' + '['+ new_row_number.toString() +'][]');
		$(row_copy).find('.cwccv_user_groups_category_select').attr('name', 'cwccv_user_groups_category_select' + '[' + new_row_number.toString() +'][]');
		$(row_copy).find('.cwccv_user_groups_product_select').attr('name', 'cwccv_user_groups_product_select' + '[' + new_row_number.toString() +'][]');
		$(row_copy).find('input[type=radio]').attr('name', 'cwccv_user_groups_show_hide_radio['+ new_row_number.toString() +']');
		$(row_copy).attr('data-id', new_row_number.toString());
		$(row_copy).attr('class', 'cwccv_user_groups_row');

		$(row_copy).find('.cwccv_user_groups_groups_name_select').attr('id', 'cwccv_user_groups_groups_name_select_' + new_row_number.toString());
		$(row_copy).find('.cwccv_user_groups_category_select').attr('id', 'cwccv_user_groups_category_select_' + new_row_number.toString());
		$(row_copy).find('.cwccv_user_groups_product_select').attr('id', 'cwccv_user_groups_product_select_' + new_row_number.toString());

		$(row_copy).find('option:selected').removeAttr('selected');
		$(row_copy).find('input[type=radio]:checked').attr('checked', false);
		$(row_copy).find('.cwccv_user_groups_show_hide_radio').first().attr('checked', true);

		$(row_copy).insertAfter(row);
		
		cwccv_select2( '#cwccv_user_groups_groups_name_select_' + new_row_number.toString() );
		cwccv_select2( '#cwccv_user_groups_category_select_' + new_row_number.toString() );
		cwccv_select2( '#cwccv_user_groups_product_select_' + new_row_number.toString() );

	});

	// Click event of price tier repeater button
	$('.cwccv_price_tier_repeater_field_button').click(function(){
		
		let template_row = $('.cwccv_price_tier_row_template');
		let row = $('.cwccv_price_tier_row').last();

		let last_row_number = $(row).data('id');
		let row_copy = $(template_row).clone();
		
		//increamenting the name array index
		let new_row_number = parseInt(last_row_number)+1;
		$(row_copy).find('.cwccv_price_tier_from_text_field').attr('name', 'cwccv_price_tier_from_text_field' + '[' + new_row_number.toString() +']');
		$(row_copy).find('.cwccv_price_tier_to_text_field').attr('name', 'cwccv_price_tier_to_text_field' + '[' + new_row_number.toString() +']');
		$(row_copy).find('.cwccv_price_tier_category_select').attr('name', 'cwccv_price_tier_category_select' + '[' + new_row_number.toString() +'][]');
		$(row_copy).find('.cwccv_price_tier_product_select').attr('name', 'cwccv_price_tier_product_select' + '[' + new_row_number.toString() +'][]');
		$(row_copy).find('input[type=radio]').attr('name', 'cwccv_price_tier_show_hide_radio['+ new_row_number.toString() +']');
		$(row_copy).attr('data-id', new_row_number.toString());
		$(row_copy).attr('class', 'cwccv_price_tier_row');

		$(row_copy).find('.cwccv_price_tier_category_select').attr('id', 'cwccv_price_tier_category_select_' + new_row_number.toString());
		$(row_copy).find('.cwccv_price_tier_product_select').attr('id', 'cwccv_price_tier_product_select_' + new_row_number.toString());

		$(row_copy).find('input[type=text]').val('');
		$(row_copy).find('input[type=radio]:checked').attr('checked', false);
		$(row_copy).find('option:selected').removeAttr('selected');
		$(row_copy).find('.cwccv_price_tier_show_hide_radio').first().attr('checked', true);

		$(row_copy).insertAfter(row);
		
		cwccv_select2( '#cwccv_price_tier_category_select_' + new_row_number.toString() );
		cwccv_select2( '#cwccv_price_tier_product_select_' + new_row_number.toString() );

	});

	// Click event of geo location repeater button
	$('.cwccv_geo_location_repeater_field_button').click(function(){
		
		let template_row = $('.cwccv_geo_location_row_template');
		let row = $('.cwccv_geo_location_row').last();

		let last_row_number = $(row).data('id');
		let row_copy = $(template_row).clone();
		
		//increamenting the name array index
		let new_row_number = parseInt(last_row_number)+1;
		$(row_copy).find('.cwccv_geo_location_location_name_select').attr('name', 'cwccv_geo_location_location_name_select' + '['+ new_row_number.toString() +'][]');
		$(row_copy).find('.cwccv_geo_location_category_select').attr('name', 'cwccv_geo_location_category_select' + '[' + new_row_number.toString() +'][]');
		$(row_copy).find('.cwccv_geo_location_product_select').attr('name', 'cwccv_geo_location_product_select' + '[' + new_row_number.toString() +'][]');
		$(row_copy).find('input[type=radio]').attr('name', 'cwccv_geo_location_show_hide_radio['+ new_row_number.toString() +']');
		$(row_copy).attr('data-id', new_row_number.toString());
		$(row_copy).attr('class', 'cwccv_geo_location_row');

		$(row_copy).find('.cwccv_geo_location_location_name_select').attr('id', 'cwccv_geo_location_location_name_select_' + new_row_number.toString());
		$(row_copy).find('.cwccv_geo_location_category_select').attr('id', 'cwccv_geo_location_category_select_' + new_row_number.toString());
		$(row_copy).find('.cwccv_geo_location_product_select').attr('id', 'cwccv_geo_location_product_select_' + new_row_number.toString());

		$(row_copy).find('option:selected').removeAttr('selected');
		$(row_copy).find('input[type=radio]:checked').attr('checked', false);
		$(row_copy).find('.cwccv_geo_location_show_hide_radio').first().attr('checked', true);
		
		$(row_copy).insertAfter(row);
		
		cwccv_select2( '#cwccv_geo_location_location_name_select_' + new_row_number.toString() );
		cwccv_select2( '#cwccv_geo_location_category_select_' + new_row_number.toString() );
		cwccv_select2( '#cwccv_geo_location_product_select_' + new_row_number.toString() );

	});

	// Cross button click event for indivisual user
	$(document).on('click', '.cwccv_ind_user_remove_row_btn', function(){
		if( $('.cwccv_individual_user_row').length > 1  ){
			// cwccv_destruct_dropdowns();
			var current_removing_row = $(this).closest('.cwccv_individual_user_row');
			
			$(current_removing_row).remove();
			
			// cwccv_initialize_dropdowns();
		}
	});
	// Cross button click event for user roles
	$(document).on('click', '.cwccv_user_roles_remove_row_btn', function(){
		if( $('.cwccv_user_roles_row').length > 1  ){
			// cwccv_destruct_dropdowns();
			var current_removing_row = $(this).closest('.cwccv_user_roles_row');
			
			$(current_removing_row).remove();
			
			// cwccv_initialize_dropdowns();
		}
	});
	// Cross button click event for user groups
	$(document).on('click', '.cwccv_user_groups_remove_row_btn', function(){
		if( $('.cwccv_user_groups_row').length > 1  ){
			// cwccv_destruct_dropdowns();
			var current_removing_row = $(this).closest('.cwccv_user_groups_row');
			
			$(current_removing_row).remove();
			
			// cwccv_initialize_dropdowns();
		}
	});
	// Cross button click event for price tier
	$(document).on('click', '.cwccv_price_tier_remove_row_btn', function(){
		if( $('.cwccv_price_tier_row').length > 1  ){
			// cwccv_destruct_dropdowns();
			var current_removing_row = $(this).closest('.cwccv_price_tier_row');
			
			$(current_removing_row).remove();
			
			// cwccv_initialize_dropdowns();
		}
	});
	// Cross button click event for geo_location
	$(document).on('click', '.cwccv_geo_location_remove_row_btn', function(){
		if( $('.cwccv_geo_location_row').length > 1  ){
			// cwccv_destruct_dropdowns();
			var current_removing_row = $(this).closest('.cwccv_geo_location_row');
			
			$(current_removing_row).remove();
			
			// cwccv_initialize_dropdowns();
		}
	});

	$('.cwccv_edit_role_button').click( function(){

		var button  = $(this);
		var role_name = button.closest('tr').find('.user_role_name').html();
		cwccv_old_role_name = role_name;
		button.closest('tr').find('.user_role_name').html('<input type="text" value="' + role_name + '">');
		// Hide edit and delete button
		$('.cwccv_edit_role_button').attr('disabled', 'disabled');
		$('.cwccv_delete_role_button').attr('disabled', 'disabled');
		// Hide edit and delete button for perticular record row.
		$(button).closest('tr').find('.cwccv_edit_role_button').hide();
		$(button).closest('tr').find('.cwccv_delete_role_button').hide();
		// Show save and cancel button for perticualar record row.
		$(button).closest('tr').find('.cwccv_save_role_button').show();
		$(button).closest('tr').find('.cwccv_cancel_role_button').show();
	});

	// Click event of cancel button on user role section.
	$('.cwccv_cancel_role_button').click(function(){
		//var role_name = $(this).closest('tr').find('.user_role_name>input').val();
		var role_name = $(this).data("name");
		// Hide save and cancel button
		$(this).closest('tr').find('.user_role_name').html(role_name);
		$(this).closest('tr').find('.cwccv_save_role_button').hide();
		$(this).hide();
		// Enable edit and delete button
		$('.cwccv_edit_role_button').removeAttr('disabled', 'disabled');
		$('.cwccv_delete_role_button').removeAttr('disabled', 'disabled');
		// Show edit and delete butotn
		$('.cwccv_edit_role_button').show();
		$('.cwccv_delete_role_button').show();
	});

	// Click event of save role button
	$('.cwccv_save_role_button').click(function(){
		var save_role_button = $(this);
		var role_name = $(save_role_button).closest('tr').find('.user_role_name>input').val();
		console.table(role_name,cwccv_old_role_name)
		$.ajax({
			type: 'POST',
			url: main_ajax_var.ajaxurl,
			dataType: 'json',
			data:{
				action: 'update_user_role',
				'role_name': role_name,
				'old_role_name': cwccv_old_role_name
			},
			success: function(data){

				if( data.status == 'fail' ){                    
					$(save_role_button).closest('tr').find('.user_role_name').html(cwccv_old_role_name);
				}
				else{
					$(save_role_button).closest('tr').find('.user_role_name').html( role_name );
				}
				// Hide save and cancel button
				$('.cwccv_save_role_button').hide();
				$('.cwccv_cancel_role_button').hide();
				
				// Show edit and delete button
				$('.cwccv_edit_role_button').show();
				$('.cwccv_delete_role_button').show();

				// Remove hide attr from edit and delete button
				$('.cwccv_edit_role_button').removeAttr('disabled', 'disabled');
				$('.cwccv_delete_role_button').removeAttr('disabled', 'disabled');

				// Show message then made it fade out and remove after 2 seconds.
				$('.status').append(data.message).fadeIn(1000);
				setTimeout(function() { $('#cwccv_message').fadeOut(2000, function(){
					$('#cwccv_message').remove();
				}); }, 2000);

			},
			error: function(){
				console.log('Something went wrong while saving the role.');
			}

		});
	});

	//Group settings
	$('.cwccv_edit_group_button').click( function(){
		var button  = $(this);
		var group_name = button.closest('tr').find('.user_group_name').html();
		cwccv_old_group_name = group_name;
		button.closest('tr').find('.user_group_name').html('<input type="text" value="' + group_name + '">');
		// Disable edit and delete button
		$('.cwccv_edit_group_button').attr('disabled', 'disabled');
		$('.cwccv_delete_group_button').attr('disabled', 'disabled');
		// Hide edit delelete button for perticular row.
		$(button).closest('tr').find('.cwccv_edit_group_button').hide();
		$(button).closest('tr').find('.cwccv_delete_group_button').hide();
		// Show save and delete button
		$(button).closest('tr').find('.cwccv_save_group_button').show();
		$(button).closest('tr').find('.cwccv_cancel_group_button').show();
	});

	$('.cwccv_cancel_group_button').click(function(){
		//var group_name = $(this).closest('tr').find('.user_group_name>input').val();
		var group_name = $(this).data("name");
		// Hide save button and cancel button
		$(this).closest('tr').find('.user_group_name').html(group_name);
		$(this).closest('tr').find('.cwccv_save_group_button').hide();
		$(this).hide();
		// Enable edit and delete button
		$('.cwccv_edit_group_button').removeAttr('disabled', 'disabled');
		$('.cwccv_delete_group_button').removeAttr('disabled', 'disabled');
		// Show edit and delete button
		$('.cwccv_edit_group_button').show();
		$('.cwccv_delete_group_button').show();
	});

	$('.cwccv_save_group_button').click(function(){
		var save_group_button = $(this);
		var group_name = $(save_group_button).closest('tr').find('.user_group_name>input').val();
		$.ajax({
			type: 'POST',
			url: main_ajax_var.ajaxurl,
			dataType: 'json',
			data:{
				action: 'update_user_group',
				'group_name': group_name,
				'old_group_name': cwccv_old_group_name
			},
			success: function(data){

				if( data.status == 'fail' ){
					$(save_group_button).closest('tr').find('.user_group_name').html( cwccv_old_group_name );
				}
				else{
					$(save_group_button).closest('tr').find('.user_group_name').html( group_name );
				}

				// Hide save and cancel button
				$('.cwccv_save_group_button').hide();
				$('.cwccv_cancel_group_button').hide();
				
				// Show edit and delete button
				$('.cwccv_edit_group_button').show();
				$('.cwccv_delete_group_button').show();

				// Enable edit and delete button
				$('.cwccv_edit_group_button').removeAttr('disabled', 'disabled');
				$('.cwccv_delete_group_button').removeAttr('disabled', 'disabled');
				console.log(data);
				// Show message then made it fade out and remove after 2 seconds.
				$('.status').html(data.message).fadeIn(1000);
				//$('#wpbody-content').append(data.message).fadeIn(1000);
				setTimeout(function() { $('#cwccv_message').fadeOut(2000, function(){
					$('#cwccv_message').remove();
				}); }, 2000);

			},
			error: function(){
				console.log('Something went wrong while saving the group.');
			}

		});
	});

	$('.cwccv_delete_role_button').click(function(){
		var delete_role_button = $(this);
		var role_name = $(delete_role_button).closest('tr').find('.user_role_name').html();

		if( confirm('Are you sure you want to delete this role?') ){
			$.ajax({
				url: main_ajax_var.ajaxurl,
				type: 'POST',
				dataType: 'json',
				data: {
					action: 'delete_user_role',
					'role_name': role_name
				},
				success: function(data){
					// Delete the row
					$(delete_role_button).closest('tr').remove();
					// Show message then made it fade out and remove after 2 seconds.
					$('.status').html(data.message).fadeIn(1000);
					setTimeout(function() { $('#cwccv_message').fadeOut(2000, function(){
						$('#cwccv_message').remove();
					}); }, 2000);
				},
				error: function(){
					console.log('Something went wrong. Please try again.');
				}

			});
		}
	});

	$('.cwccv_delete_group_button').click(function(){
		var delete_group_button = $(this);
		var group_name = $(delete_group_button).closest('tr').find('.user_group_name').html();

		if( confirm('Are you sure you want to delete this group?') ){
			$.ajax({
				url: main_ajax_var.ajaxurl,
				type: 'POST',
				dataType: 'json',
				data: {
					action: 'delete_user_group',
					'group_name': group_name
				},
				success: function(data){
					// Delete the row
					$(delete_group_button).closest('tr').remove();
					// Show message then made it fade out and remove after 2 seconds.
					$('.status').html(data.message).fadeIn(1000);
					setTimeout(function() { $('#cwccv_message').fadeOut(2000, function(){
						$('#cwccv_message').remove();
					}); }, 2000);
				},
				error: function(){
					console.log('Something went wrong. Please try again.');
				}

			});
		}
	});
	
	//  Show/Hide the Add Role field in Roles Tab
	//$("input.cwccv_add_new_role_text_field, input.cwccv_add_new_group_text_field").hide();

	/*$(".button-add-role").click(function() {
		$("input.cwccv_add_new_role_text_field").toggle();
	});

	$(".button-add-group").click(function() {
		$("input.cwccv_add_new_group_text_field").toggle();
		if($("input.cwccv_add_new_group_text_field").is(":visible")){
			$('.woocommerce-save-button').attr('disabled', false);
		}
		else {
			$('.woocommerce-save-button').attr('disabled', true);
		}

	});*/

	// Check repeatition
	$('.cwccv_priority_select').change(function(){
		var dropdown_values = $('.cwccv_priority_select').val();
		var current_value = $(this).val();
		var value_count = 0;
		$('.cwccv_priority_select').each(function(){

			if( $(this).val() == current_value ){
				value_count+=1;
			}
		});
		if( value_count > 1 ){
			$('.cwccv_priority_select').removeClass("cwccv_priority_dropdown_error");
			$(this).addClass('cwccv_priority_dropdown_error');
			$('.woocommerce-save-button').attr('disabled', true);
			$('.cwccv_custom_error_message').empty();
			$('.cwccv_custom_error_message').append('<label style="color:red;">Priorities are conflicting. Need to select distinct priority</label>')
			return false;
		}
		else{
			$('.cwccv_priority_select').removeClass("cwccv_priority_dropdown_error");
			$('.woocommerce-save-button').attr('disabled', false);
			$('.cwccv_custom_error_message').empty();
			return false;
		}
	});

	function cwccv_save_validation( unbind_event,toggle, listClass, hideshowClass, catClass,proClass){

		if( $(toggle).is(':checked') ){

	 	$( listClass ).each(function() {
				

 		 if( $( this ).val() != "" ){
 		 	if( unbind_event == true  ){

 		 		if(  $( this ).parent().parent().find( hideshowClass ).is(':checked') == false){
 		 			$( this ).focus();
 		 			alert(main_ajax_var.hide_show_alert);

 		 			unbind_event = false;
 		 			return unbind_event;

 		 		}
 		 		
 		 		if( ( $( this ).parent().parent().find( catClass ).val() == ""  ) && ( $( this ).parent().parent().find( proClass ).val() == ''  ) ){
 		 			$( this ).focus();
 		 			alert(main_ajax_var.cat_product_alert);

 		 			unbind_event = false;
 		 			return unbind_event;
 		 		}

 		 	}
 		 }

		});

	 }

	 return unbind_event;

	}

	 $('.woocommerce-save-button').click(function(event){
	 	event.preventDefault();

	 	var unbind_event = true;

	 	unbind_event = cwccv_save_validation(unbind_event,
	 		'.cwccv_individual_customer_settings_enable_toggle',
	 		'.cwccv_individual_customer_customer_name_select',
	 		'.cwccv_individual_customer_products_show_hide_radio',
	 		'.cwccv_individual_customer_category_select',
	 		'.cwccv_individual_customer_product_select'
	 		);

	 	unbind_event = cwccv_save_validation(unbind_event,
	 		'.cwccv_user_roles_settings_enable_toggle',
	 		'.cwccv_user_roles_roles_name_select',
	 		'.cwccv_user_roles_show_hide_radio',
	 		'.cwccv_user_roles_category_select',
	 		'.cwccv_user_roles_product_select'
	 		);

	 	unbind_event = cwccv_save_validation(unbind_event,
	 		'.cwccv_user_groups_settings_enable_toggle',
	 		'.cwccv_user_groups_groups_name_select',
	 		'.cwccv_user_groups_show_hide_radio',
	 		'.cwccv_user_groups_category_select',
	 		'.cwccv_user_groups_product_select'
	 		);


		if( $('.cwccv_price_tier_settings_enable_toggle').is(':checked') ){

		$( ".cwccv_price_tier_from_text_field" ).each(function() {

 		 if( $( this ).val() != "" ){
 		 	
 		 	if( $( this ).siblings('.cwccv_price_tier_to_text_field').val() == ""){
 		 		$( this ).siblings('.cwccv_price_tier_to_text_field').focus();
 		 		alert(main_ajax_var.price_alert);

 		 		unbind_event = false;
 		 		return false;

 		 	}

 		 	if( unbind_event == true  ){
 		 		
 		 		if( !  $( this ).parent().parent().find('.cwccv_price_tier_show_hide_radio').is(':checked') ){
 		 			$( this ).focus();
 		 			alert(main_ajax_var.hide_show_alert);

 		 			unbind_event = false;
 		 			return false;

 		 		}
 		 		
 		 		if( ( $( this ).parent().parent().find('.cwccv_price_tier_category_select').val() == ""  ) && ( $( this ).parent().parent().find('.cwccv_price_tier_product_select').val() == ''  ) ){
 		 			$( this ).focus();
 		 			alert(main_ajax_var.cat_product_alert);

 		 			unbind_event = false;
 		 			return false;
 		 		}

 		 	}
 		 }

		});

		}

		unbind_event = cwccv_save_validation(unbind_event,
	 		'.cwccv_geo_location_settings_enable_toggle',
	 		'.cwccv_geo_location_location_name_select',
	 		'.cwccv_geo_location_show_hide_radio',
	 		'.cwccv_geo_location_category_select',
	 		'.cwccv_geo_location_product_select'
	 		);

	 	if( true == unbind_event ){
	 		$(this).unbind('click').click();
	 	}

	 });

} );


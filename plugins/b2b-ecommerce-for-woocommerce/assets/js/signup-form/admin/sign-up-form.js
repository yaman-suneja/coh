jQuery(document).ready( function($) {
	
	$('#b2be-signup-form-entry-table').DataTable({
		"lengthMenu": [[5, 10,50,100, -1], [5, 10,50,100, "All"]],
		"ordering": false,
		"searching": true,
	});

	$( "#sortable" ).sortable({
		start:function(event, ui){
			pre = ui.item.index();
		},
		stop: function(event, ui) {
			post = ui.item.index();
			other = 'b2be-signup-form-preview-wrapper';
			if (post > pre) {
				$('.'+other+ ' div.preview-fields-wrapper:eq(' +pre+ ')').insertAfter('.'+other+ ' div.preview-fields-wrapper:eq(' +post+ ')');
			}else{
				$('.'+other+ ' div.preview-fields-wrapper:eq(' +pre+ ')').insertBefore('.'+other+ ' div.preview-fields-wrapper:eq(' +post+ ')');
			}
		}
	});
	$( "#sortable" ).disableSelection();

	$(document.body).on( "click", ".b2be-signup-fomr-user-info", function() {
		if ( "none" == $(this).siblings(".b2be-signup-form-user-entries-wrapper").css("display") ) {
			$(this).siblings(".b2be-signup-form-user-entries-wrapper").slideDown("slow")
			$(this).find(".dashicons-arrow-down").removeClass("dashicons-arrow-down").addClass("dashicons-arrow-up");
		}
		else {
			$(this).siblings(".b2be-signup-form-user-entries-wrapper").slideUp("slow")
			$(this).find(".dashicons-arrow-up").removeClass("dashicons-arrow-up").addClass("dashicons-arrow-down");
		}
	});

	$(document.body).on( "click", ".b2be-signup-form-field__header", function() {
		if ( "none" == $(this).siblings(".b2be-signup-form-field__body").css("display") ) {
			$(this).siblings(".b2be-signup-form-field__body").slideDown("slow")
			$(this).find(".dashicons-arrow-down").removeClass("dashicons-arrow-down").addClass("dashicons-arrow-up");
		}
		else {
			$(this).siblings(".b2be-signup-form-field__body").slideUp("slow")
			$(this).find(".dashicons-arrow-up").removeClass("dashicons-arrow-up").addClass("dashicons-arrow-down");
		}
	});
	$(document.body).on( "change", "#signup_form_field_type", function() {
		if( "role" == $(this).val() ) {
			$(this).closest( ".b2be-signup-form-field" ).find(".b2be-signup-form-field-roles-wrapper").slideDown('slow');
		}
		else {
			$(".b2be-signup-form-field-roles-wrapper").slideUp("hide");
		}
	});
	$(document.body).on( "change", "#signup_form_field_size", function() {
		let count = $(this).closest(".b2be-signup-form-field").find(".field-id").html();
		if ( "small" == $(this).val() ) {
			$("#preview_fields_wrapper_"+count).css("width", "33.33%");
		}
		else if( "medium" == $(this).val() ) {
			$("#preview_fields_wrapper_"+count).css("width", "50%");
		}
		else if( "large" == $(this).val() ) {
			$("#preview_fields_wrapper_"+count).css("width", "100%");
		}
	});
	$(document.body).on( "click", "#expand_all_fields", function() {
		$(".b2be-signup-form-field__body").slideDown("slow");
		$(".dashicons-arrow-down").removeClass("dashicons-arrow-down").addClass("dashicons-arrow-up");
	}); 
	$(document.body).on( "click", "#close_all_fields", function() {
		$(".b2be-signup-form-field__body").slideUp("slow")
		$(".dashicons-arrow-up").removeClass("dashicons-arrow-up").addClass("dashicons-arrow-down");
	});
	$(document.body).on( "click", "#signup_form_field_visibility", function() {
		let count = $(this).closest(".b2be-signup-form-field").find(".field-id").html();
		if( $(this).is(":checked") ) {
			$("#preview_fields_wrapper_"+count).show();
		}
		else{
			$("#preview_fields_wrapper_"+count).hide();
		}
	});
	$(document.body).on( "click", "#signup_form_field_required", function() {
		let count = $(this).closest(".b2be-signup-form-field").find(".field-id").html();
		if( $(this).is(":checked") ) {
			$("#preview_fields_wrapper_"+count).find(".is_required").show();
		}
		else{
			$("#preview_fields_wrapper_"+count).find(".is_required").hide();
		}
	});

	$(document.body).on( "keyup", "#signup_form_field_name", function(e) {
		let current_field_number = $(this).attr("data-field-number");
		$("#preview_field_label_"+current_field_number).find("label").text( $(this).val() )
		$(this).closest(".b2be-signup-form-field").find(".field-name").text( $(this).val() )
	}); 

	$(document.body).on( "click", ".select-all-signup-form-field", function() {
		$( ".select-signup-form-field" ).attr( "checked", false );
		if ( $(this).is(":checked") ) {
			$( ".select-signup-form-field" ).attr( "checked", true );
		}
		else {
			$( ".select-signup-form-field" ).attr( "checked", false );
		}
	});
	$(document.body).on( "click", ".select-signup-form-field", function() {
		$(".select-all-signup-form-field").attr("checked", false );
		if ( $(this).is(":checked") ) {
			$(".select-all-signup-form-field").attr("checked", true );
		}
	});
	
	$(document.body).on( "click", "#delete_selected_fields", function() {

		if( 0 != $( ".select-signup-form-field:checked" ).length ) {
			if( confirm('Are you sure you want to delete the selected fields?') ) {
				$( ".select-signup-form-field" ).each( function( index, element ) {
					if( $(element).is(":checked") ) {
						$(element).closest(".b2be-signup-form-field").remove();
					} 
				});
				$('.b2be-save-signup-fields').trigger("click");
			}
		}
	})

	$(document.body).on( "click", ".add-new-fields", function() {
		
		let template = $( "#template .b2be-signup-form-field" ).clone();
		let count    = $( ".b2be-signup-form-field" ).length-1;

		template.find(".field-id").html( count );
		
		template.find("#signup_form_field_name").attr( "name" ).replace("[1]","["+count+"]");
		template.find("#signup_form_field_name").attr( "data-field-number", count );
		template.find("#signup_form_field_type").attr( "name" ).replace("[1]","["+count+"]");
		template.find("#signup_form_field_visibility").attr( "name" ).replace("[1]","["+count+"]");
		template.find("#signup_form_field_required").attr( "name" ).replace("[1]","["+count+"]");
		template.find("#select_signup_form_field").attr( "name" ).replace("[1]","["+count+"]");
		template.find("#b2be_signup_custom_classes").attr( "name" ).replace("[1]","["+count+"]");
		template.find("#signup_form_field_size").attr( "name" ).replace("[1]","["+count+"]");
		
		$( ".b2be-signup-form-fields-inner-wrapper" ).append(template);

		let preview_fields = $('<div class="preview-fields-wrapper" id="preview_fields_wrapper_'+count+'"><p id="preview_field_label_'+count+'"><label></label><span class="is_required" style="color:red;"> *</span></p><p id="preview_field_'+count+'"><input type="text"></p></div>');

		$(preview_fields).insertAfter($(".b2be-signup-form-preview-wrapper").find("div.preview-fields-wrapper").last());

	});


	$('.b2be-save-signup-fields').click(function(event){
		
		let signup_form_fields = [];
		let required_approval  = $( ".b2be-sign-up-approval" ).is(":checked");
		$( ".b2be-signup-form-fields-inner-wrapper .b2be-signup-form-field" ).each( function ( index, element ) {
			
			var field_obj           = {};
			var signup_form_role    = [];
			
			field_obj["name"]       = $( element ).find("#signup_form_field_name").val();
			field_obj["type"]       = ( '' == $( element ).find("#signup_form_field_type").val() ) ? "text" : $( element ).find("#signup_form_field_type").val();
			field_obj["visibility"] = $( element ).find("#signup_form_field_visibility").is(":checked");
			field_obj["required"]   = $( element ).find("#signup_form_field_required").is(":checked");

			$( element ).find(".b2be_signup_role").each( function( index, child ) {
				if( $( child ).is(":checked")  ) {
					var role_id = $( child ).attr("data-role-id");
					signup_form_role = [ ...signup_form_role, role_id ];
				}
			});

			field_obj["roles"]      = signup_form_role;
			field_obj["size"]       = $( element ).find("#signup_form_field_size").val();
			field_obj["classes"]    = $( element ).find("#b2be_signup_custom_classes").val().split("\n");
			signup_form_fields = [...signup_form_fields, field_obj ];
		})

		$.ajax({
			type: "POST",
			url:  sign_up_settings.ajaxurl,
			data: {
				action               : 'save_signup_form',
				'signup_form_fields' : signup_form_fields,
				'required_approval'  : required_approval,
			},
			success: function (response) {
				window.onbeforeunload = null;
				location.reload();
			}
		});

	})

	$( ".sfg_request" ).on(
		"click",
		function() {
	
			var sfg_request = $( this ).attr( "id" );
			if ( $( this ).attr( "disabled" ) != "disabled" ) {
	
				var result = confirm( "Are you sure you want to perform this action? An email will be sent to users regarding their status.." );
				if ( result ) {
					var user_id = $( "#user_id" ).val();
	
					$.ajax(
						{
							url: sign_up_settings.ajaxurl,
							method: "POST",
							data: {
								action: "sfg_request_action",
								"sfg_request": sfg_request,
								"user_id": user_id,
							},
							success: function(response) {
	
								$( "#signup_success_message" ).text( response ).css( "color", "green" );
								location.reload();
							}
	
							}
					);
	
				}
			}
		}
	);

});

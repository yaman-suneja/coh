jQuery(document).ready(function($){

	// $('#codup-rfq_enable_rfq').parent().addClass('switch');
	// $('#codup-rfq_enable_rfq').parent().append('<span class="slider round"></span>');
	$("#codup-rfq_enable_rfq").on("change",function(){
			
		if($(this).prop('checked')==true){
			var contents = $(this).parent().contents();
			contents[contents.length -1].nodeValue= 'RFQ is Enabled';
			$(this).parent().siblings(".description").text('Adds a Request for Quote button on all products in the store.')

		}
		else{
			var contents = $(this).parent().contents();
			contents[contents.length -1].nodeValue= 'RFQ is Disabled';
			$(this).parent().siblings(".description").text('Hide a Request for Quote button on all products in the store.')

		}
	})

	$("#codup-rfq_disable_add_to_cart").on("change",function(){
		
		if($(this).prop('checked')==true){
			var contents = $(this).parent().contents();
			contents[contents.length -1].nodeValue= 'Add To Cart Is Disabled';
			$(this).parent().siblings(".description").text('Show Add to cart button on all products')
		}
		else{
			var contents = $(this).parent().contents();
			contents[contents.length -1].nodeValue= 'Add To Cart Is Enabled';
			$(this).parent().siblings(".description").text('Hide Add to cart button from all products')
		}
	})
	
	// ------------------------- Add Category Page --------------------------------------------
	
	$(".codup-rfq_enable_rfq").on("change",function(){
		
		if($(this).prop('checked')==true){
			var contents = $(this).parent().contents();
			contents[contents.length -1].nodeValue= 'RFQ is Enabled';
			$(".add_cat_rfq_description").text('Adds a Request for Quote button on all products in the store.');
		}
		else{
			var contents = $(this).contents();
			contents[contents.length -1].nodeValue= 'RFQ is Disabled';
			$(".add_cat_rfq_description").text('Hide a Request for Quote button on all products in the store.');
		}
	})

	$(".codup-rfq_disable_add_to_cart").on("change",function(){
		
		if($(this).prop('checked')==true){
			var contents = $(this).parent().contents();
			contents[contents.length -1].nodeValue= 'Add To Cart Is Disabled';
			$(".add_cat_cart_description").text('Hide a Request for Quote button on all products in the store.');
		}
		else{
			var contents = $(this).parent().contents();
			contents[contents.length -1].nodeValue= 'Add To Cart Is Enabled';
			$(".add_cat_cart_description").text('Adds a Request for Quote button on all products in the store.');
		}
	})

	// ------------------------- Edit Category Page --------------------------------------------

	$(".codup-rfq_enable_rfq-edit").on("change",function(){
		
		if($(this).prop('checked')===true){
		   
		   $("#add_to_rfq_span").text('RFQ is Enabled');
		   $(".edit_cat_rfq_description").text('Adds a Request for Quote button on all products in the store.');

		}
		else{
			$("#add_to_rfq_span").text('RFQ is Disabled');
			$(".edit_cat_rfq_description").text('Hide a Request for Quote button on all products in the store.');

		}
	})

	$(".codup-rfq_disable_add_to_cart-edit").on("change",function(){
		
		if($(this).prop('checked')===true){
		   
		   $("#add_to_cart_span").text('Add To Cart Is Disabled');
		   $(".edit_cat_cart_description").text('This will disable Add to cart on products belong to this cateogory.');

		}
		else{
			$("#add_to_cart_span").text('Add To Cart Is Enabled');
			$(".edit_cat_cart_description").text('This will enable Add to cart on products belong to this cateogory.');

		}
	})

		
	// --------------------------- Role Page -------------------------------------------

	$("#b2be-custom-role-options #enable_rfq").on("change",function() {
			
		if($(this).prop('checked')===true) {
			//    $("#add_to_cart_span").text('Add To Cart Is Disabled');
			$(this).siblings(".description").text('Adds a RFQ button for this role.')
		}
		else{
			// $("#add_to_cart_span").text('Add To Cart Is Enabled');
			$(this).siblings(".description").text('Hide a RFQ button for this role.')
		}
	})

	$("#b2be-custom-role-options #disable_add_to_cart").on("change",function() {
		
		if($(this).prop('checked')===true) {
			//    $("#add_to_cart_span").text('Add To Cart Is Disabled');
			$(this).siblings(".description").text('Hide Add to cart on this role.')
		}
		else{
			// $("#add_to_cart_span").text('Add To Cart Is Enabled');
			$(this).siblings(".description").text('Show Add to cart on this role.')
		}
	})

	//  --------------------------- Product Page ---------------------------

	$("#codup-rfq-options #enable_rfq").on("change",function() {
			
		if($(this).prop('checked')===true) {
			//    $("#add_to_cart_span").text('Add To Cart Is Disabled');
			$(this).siblings(".description").text('Adds a RFQ button for this product.')
		}
		else{
			// $("#add_to_cart_span").text('Add To Cart Is Enabled');
			$(this).siblings(".description").text('Hide a RFQ button for this product.')
		}
	})

	$("#codup-rfq-options #disable_add_to_cart").on("change",function() {
		
		if($(this).prop('checked')===true) {
			//    $("#add_to_cart_span").text('Add To Cart Is Disabled');
			$(this).siblings(".description").text('Hide Add to cart on this product.')
		}
		else{
			// $("#add_to_cart_span").text('Add To Cart Is Enabled');
			$(this).siblings(".description").text('Show Add to cart on this product.')
		}
	})

})
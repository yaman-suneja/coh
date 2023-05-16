/**
 * Scripts bulk shop
 */
var tbl      = '#wbs-table';
var aDefault = 'black';
var $        = jQuery;
var aColor   = $( 'a' ).css( 'color' );

function checkAll() {

	$( tbl + ' tbody form' ).each(
		function(){
			$( this['id'] ).prop( 'checked', $( '#checkall' ).prop( 'checked' ) );
			clearRow( this['id'] );
		}
	);
	calcChanged( false );

}

function clearRow( obj ) {
	
	var total_hidden = $('#wbs-total').hasClass('wbs-hide-column');
	var total_count  = 0;

	if ( ! obj.checked ) {
		obj.form['qty'].value = 0;
	}

	if ( total_hidden ) {
		$( tbl + ' tbody form' ).each(
			function() {
				var v             = this['qty'].value;
				this['qty'].value = eval( parseInt( v ) );
				total_count      += parseInt( this['qty'].value );
			}
		)
		$( '#tbl-total-q' ).text( total_count );
	}
	
}

function sort( column, type ){

	$( tbl ).tableCalc( 'sortColumn', column, type );

	if ( $( '#s' + column ).hasClass( 'fa-sort' ) ) {
		$( '#s' + column ).removeClass( 'fa-sort' ).addClass( 'fa-sort-up' );
		resetSortClass( column );
		return;
	}
	if ( $( '#s' + column ).hasClass( 'fa-sort-up' ) ) {
		$( '#s' + column ).removeClass( 'fa-sort-up' ).addClass( 'fa-sort-down' );
		resetSortClass( column );
		return;
	}
	if ( $( '#s' + column ).hasClass( 'fa-sort-down' ) ) {
		$( '#s' + column ).removeClass( 'fa-sort-down' ).addClass( 'fa-sort-up' );
		resetSortClass( column );
		return;
	}

	function resetSortClass( column ){
		for ( var i = 1; i < 6; i++ ) {
			if ( i != column ) {
				$( '#s' + i ).removeClass( 'fa-sort-down' ).removeClass( 'fa-sort-up' ).addClass( 'fa-sort' );
			}
		}
	}

}

function addToCart( obj ) {

	var form = $('#' + obj)[0];
	var qty  = parseInt( form['qty'].value ) || 0;
	if ( qty === 0 ) {
		form['qty'].value = 1;
	}
	$(form['id']).prop( 'checked', true ); 
	saveAll();

}

function changeQty( type, obj ) {
	
	var form = $('#' + obj)[0];

	if ( type === 'x' ) {
		if (form['qty'].value > 0) {
			$(form['id']).prop( 'checked', true );
		} else {
			$(form['id']).prop( 'checked', false );
		}
		return;
	}

	if ( type === 'p' ) {
		form['qty'].value = eval( parseInt(form['qty'].value || 0 ) + 1 );
	} else {
		if (form['qty'].value > 0) {
			form['qty'].value = eval( parseInt(form['qty'].value || 0 ) - 1 );
		}
	}

	if (form['qty'].value > 0) {
		$(form['id']).prop( 'checked', true );
	} else {
		$(form['id']).prop( 'checked', false );
	}

	$(form['qty']).trigger('change');
}

function saveAll() {

	var count     = 0;
	var nonce_val = $( '#_wpnonce' ).val();
	var json_data = { action: 'wbs_add_products_to_cart', nonce: nonce_val, rows: [] };
	
	$( tbl + ' tbody form' ).each(
		function() {
			if ( $( this['id'] ).prop( 'checked' ) ) {
				var suggested_price = '';
				if ( this['_suggested_price'] !== undefined ) {
					suggested_price = this['price'].value;
				}
				var frm_data = { id: this['id'].value, qty: this['qty'].value, 
								price: this['price'].value, suggested: suggested_price };
				json_data.rows.push( frm_data );
				count++;
			}
		}
	);
	
	if ( count > 0 ) {
		progressBar('start');
		$.ajax(
			{
				type: 'POST',
				url: wbs_woo_params.ajax_url,
				datatype: 'json',
				data: json_data,
				success: function( response ) {
					console.log( response );
					reloadPage();
				},
				error: function( response ){
					console.log( response );
					progressBar('stop');
				}
			}
		);
	}

	event.preventDefault();

};


function reloadPage() {

	setTimeout(
		function() {
			location.reload();
		},
		100
	);
}

function progressBar( option ) {
	
	if (option === 'start'){
		$('#wbs-saving').removeClass('wbs-saving').addClass('wbs-saving-show');
		var v = 0;
		
		setInterval(
			function() {
				if ( v > 100) {
					v = 0;
				}
				$('#cart-saving').val(v);
				v += 5;
			},
			50
		);

	} else {
		$('#wbs-saving').removeClass('wbs-saving-show').addClass('wbs-saving');
	}

}


function calcChanged( is_onload ) {

	var total_hidden = checkIfHidden('wbs-total');
	var tot_qty      = 0;
	var tot_sum      = 0;

	$( tbl + ' tbody form' ).each(
		function() {
			var qtyVal = parseInt(this['qty'].value || 0);
			if ( ! total_hidden ){
				this['row-total'].value = eval( qtyVal * parseFloat( this['price'].value || 0 ) ).toFixed(2);
			}
			if ( $( this['id'] ).prop( 'checked' ) ) {
				tot_qty += parseInt( this['qty'].value || 0 );
				if ( ! total_hidden ) {
					tot_sum += parseFloat( this['row-total'].value || 0 );
				}
			}
		}
	)
	
	$( '#tbl-total-q' ).text( tot_qty );
	
	if ( ! total_hidden ) {
		$( '#tbl-total-f' ).text( tot_sum.toFixed(2) );
	}

}

function addQty(){
	var quantity = parseInt( $('#qty-add').val() || 0 );
	if (quantity > 0) {
		$( tbl + ' tbody form' ).each(
			function() {
				this['qty'].value = eval( parseInt(this['qty'].value || 0 ) + quantity );
				if (this['qty'].value > 0) {
					$(this['id']).prop( 'checked', true );
				} else {
					$(this['id']).prop( 'checked', false );
				}
				$(this['qty']).trigger('change');
			}
		);
		$('#qty-add').val('');

	}
}

function checkIfHidden( name ){
	var cols   = $( tbl + ' thead th');
	var retVal = false;
	cols.each( function(){
		var item_hidden = $(this).hasClass('wbs-hide-column');
		if ( item_hidden && this.id === name ){
			retVal = true;
		}
	})
	return retVal;
}

function getHiddenColumns(){
	var cols  = $( tbl + ' thead th');
	var count = 0;
	cols.each( function(){
		var item_hidden = $(this).hasClass('wbs-hide-column');
		if ( item_hidden && this.id !== 'wbs-th-cart' ){
			count++;
		}
	})
	return count;
}

function nypValidate( obj, min ) {

	if ( obj !== undefined ) {
		if ( parseFloat( obj.value ) < parseFloat( min ) ) {
			obj.value = min;
		}
	}

}

function selectedChanged() {

	var nonce_val = $( '#_wpnonce' ).val();
	var json_data = { action: 'wbs_get_variable_data', nonce: nonce_val, rows: [], atts: $('#wbs_atts').val() };
	var this2     = this;

	var optionValues = [];
	var count        = 0;
	var frm_id       = $(this).closest('td').find('input[name="form_id"]').val();
	var the_form     = $('#'+frm_id);

	$(the_form[0].elements).each( function() {
		
		if (this.nodeName === 'SELECT'){
			var selVal = $(this).find(':selected').val();
			if (selVal.length > 0){
				optionValues.push( { key: 'attribute_'+this.id, value: this.value } );
			}
			count++;
		}
	});

	if ( count !== optionValues.length ) {
		return 0;
	}
	event.preventDefault();

	var frm_data = { id: this.form['product_id'].value, selectedOptions: optionValues };
	json_data.rows.push( frm_data );

	$.ajax(
		{
			type: 'POST',
			url: wbs_woo_params.ajax_url,
			datatype: 'json',
			data: json_data,
			success: function( response ) {
				//console.log( response );
				successHandler(this2, response);
			},
			error: function( response ){
				console.log( response );
			}
		}
	);
	
}

function successHandler( obj, response ) {

	if(response.length === 0){
		return 0;
	}

	var jasonObj = JSON.parse(response);
	var oldId    = obj.form['product_id'].value;

	obj.form['id'].value    = jasonObj.id;
	obj.form['price'].value = jasonObj.price; 
	jQuery('#'+oldId+'-var-price').html(jasonObj.price_formated);
	jQuery('#'+ oldId +'-sku').text(jasonObj.sku.toString());
	
	obj.form['row-total'].value = eval( jasonObj.price * obj.form['qty'].value ).toFixed(2);

	if ( jasonObj.is_on_sale === 'true' ) {
		jQuery('#'+oldId+'-sale-badge').attr('style','display:inline-block;');
	} else {
		jQuery('#'+oldId+'-sale-badge').attr('style','display:none;');
	}

	if ( jasonObj.in_stock === 'true' ) {
		jQuery('#'+oldId+'-stock-status').html('In stock');
	} else {
		jQuery('#'+oldId+'-stock-status').html('Out of stock');
	}

}


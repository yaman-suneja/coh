<?php
/**
 * Settings page for Bulk Shop
 *
 * @package bulkshop/wbs-settings
 */

require_once __DIR__ . '/../includes/class-wbsfunctions.php';

$functions = new WbsFunctions();

$allowed_html = array(
	'select' => array(
		'id'     => array(),
		'name'   => array(),
		'class'  => array(),
		'option' => array(
			'value' => array(),
			'class' => array(),
		),
	),
);
?>
<style>
.cas-logo{
	width: 20px;
	opacity: 0.7;
	padding-left: 5px;
}
.cas-logo:hover{
	opacity: 1;
}
.alignleft{
	display: flex;
	flex-direction: row;
	flex-wrap: wrap;
	justify-content: center;
	align-items: center;	
}

</style>
<div class="wrap">
<h1><?php esc_html_e( 'Bulk Shop', 'woo-bulk-shop' ); ?></h1>
<div>
<span>
<i class="fas fa-info-circle"> </i>
<?php esc_html_e( 'The shortcode: [bulkshop] shows all features default, generate shortcode to specify or hide features and columns. ', 'woo-bulk-shop' ); ?>
<?php esc_html_e( 'After creating a new shortcode, copy and use it in new or existing posts or pages. ', 'woo-bulk-shop' ); ?>
<?php /* translators: %s: url for documentation */ ?>
<?php printf( esc_html__( 'Read the %1$s extension documentation %2$s for more information.', 'woo-bulk-shop' ), '<a href="https://docs.woocommerce.com/document/bulk-shop" target="_blank">', '</a>' ); ?>	
</span>
</div>


<table class="form-table">
	<tbody>
	<tr valign="top">
		<th scope="row">
			<label for="select categories">
				<?php esc_html_e( 'Select categories', 'woo-bulk-shop' ); ?>
			</label>
			<span class="woocommerce-help-tip"></span>
		</th>
		<td>
			<?php echo wp_kses( $functions->wbs_get_categories_settings_select(), $allowed_html ); ?>
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Products per page', 'woo-bulk-shop' ); ?></th>
		<td>
			<input type="number" name="rows" id="rows" value="" />
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Default product quantity', 'woo-bulk-shop' ); ?></th>
		<td>
			<input type="number" name="product_qty" id="product_qty" value="" />
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Related products (in single product page)', 'woo-bulk-shop' ); ?></th>
		<td>
			<input name="related_products" id="related_products" type="checkbox"> 
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Related products quantity', 'woo-bulk-shop' ); ?></th>
		<td>
			<input type="number" name="related_count" id="related_count" value="" />
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Sort products Z - A (desc) ', 'woo-bulk-shop' ); ?></th>
		<td>
			<input name="product_order" id="product_order" type="checkbox"> 
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Hide expanded variations (show select option for variations)', 'woo-bulk-shop' ); ?></th>
		<td>
			<input name="variation" id="variations" type="checkbox"> 
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Hide stock', 'woo-bulk-shop' ); ?></th>
		<td>
			<input name="stock" id="stock" type="checkbox"> 
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Hide sale badge', 'woo-bulk-shop' ); ?></th>
		<td>
			<input name="salebadge" id="salebadge" type="checkbox"> 
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Hide category selector', 'woo-bulk-shop' ); ?></th>
		<td>
			<input name="categoryselector" id="categoryselector" type="checkbox"> 
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Hide SKU', 'woo-bulk-shop' ); ?></th>
		<td>
			<input name="sku" id="sku" type="checkbox"> 
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Hide thumbnail (column)', 'woo-bulk-shop' ); ?></th>
		<td>
			<input name="thumbnail" id="thumbnail" type="checkbox"> 
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Hide short description (column)', 'woo-bulk-shop' ); ?></th>
		<td>
			<input name="desc" id="desc" type="checkbox"> 
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Hide price (column)', 'woo-bulk-shop' ); ?></th>
		<td>
			<input name="price" id="price" type="checkbox"> 
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Hide total (column)', 'woo-bulk-shop' ); ?></th>
		<td>
			<input name="total" id="total" type="checkbox"> 
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Hide add to cart icon (per row)', 'woo-bulk-shop' ); ?></th>
		<td>
			<input name="carticon" id="carticon" type="checkbox"> 
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Hide bulk add (input and button)', 'woo-bulk-shop' ); ?></th>
		<td>
			<input name="bulkadd" id="bulkadd" type="checkbox"> 
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Hide search (search box and buttons)', 'woo-bulk-shop' ); ?></th>
		<td>
			<input name="hide_search" id="hide_search" type="checkbox"> 
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Hide checkboxes (first column)', 'woo-bulk-shop' ); ?></th>
		<td>
			<input name="hide_checkboxes" id="hide_checkboxes" type="checkbox"> 
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Hide price (for not logged in users)', 'woo-bulk-shop' ); ?></th>
		<td>
			<input name="hide_price_non_user" id="hide_price_non_user" type="checkbox"> 
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Custom price', 'woo-bulk-shop' ); ?></th>
		<td>
			<input name="integration-price" id="integration-price" type="text" style="width: 300px;"
			placeholder="<?php esc_html_e( 'i.e _wholesaler_price', 'woo-bulk-shop' ); ?>"> 
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Custom price roles', 'woo-bulk-shop' ); ?></th>
		<td>
			<select id="integration-roles" name="integration-roles" style="width: 352px;">
				<option value="" selected="selected"><?php esc_html_e( 'Select roles', 'woo-bulk-shop' ); ?></option>
				<?php wp_dropdown_roles( '' ); ?> 
			</select>
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"></th>
		<td>
			<button class="button" onclick="makepricerole();"><?php esc_html_e( 'Add price and role(s)', 'woo-bulk-shop' ); ?></button> 
		</td>
	</tr>
	
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Price and role(s) - max 10', 'woo-bulk-shop' ); ?></th>
		<td>
			<textarea name="price-role" id="price-role" rows="5" cols="40"></textarea> 
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"></th>
		<td>
			<button class="button" onclick="makecode();"><?php esc_html_e( 'Create shortcode', 'woo-bulk-shop' ); ?></button> 
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Shortcode', 'woo-bulk-shop' ); ?></th>
		<td>
			<textarea name="shortcode" id="shortcode" rows="4" cols="40"></textarea> 
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"></th>
		<td>
			<button class="button" onclick="copyshortcode();"><?php esc_html_e( 'Copy shortcode', 'woo-bulk-shop' ); ?></button>
		</td>
	</tr>
	</tbody>
</table>

</div>
<script>
	var $ = jQuery;
	var i = 0;

	jQuery( document ).ready( function ( $ ) {
		$('#product_cat').attr('multiple', 'multiple').attr('size', '15').attr('style','width:250px;');
		$('#integration-roles').attr('multiple', 'multiple').attr('size', '8').attr('style','width:250px;');
	});

	function makepricerole(){
		
		var user_roles = $( '#integration-roles' ).val();
		var price      = $( '#integration-price' ).val();
		var shortcode  = ''; 

		if ( price.length > 0 ) {
			if ( i === 0 ) {
				shortcode += " price_field='" + price + "'";
			} else {
				shortcode += " price_field" + i + "='" + price + "'";
			}
		}

		if ( user_roles.length > 0 && user_roles[0].length > 0 ){
			if ( i === 0 ) {
				shortcode += " price_field_roles='" + user_roles + "'";
			} else {
				shortcode += " price_field_roles" + i + "='" + user_roles + "'";
			}
		}

		if ( shortcode.length > 0 ) {
			$('#price-role').append( shortcode + ' ');
			i++;
		}

	}

	function makecode(){

		$('#shortcode').text('');
		var categories  = $( '#product_cat' ).val();
		var user_roles  = $( '#integration-roles' ).val();
		var rows        = $( '#rows' ).val();
		var prod_qty    = $( '#product_qty' ).val();
		var related_qty = $( '#related_count' ).val();
		var price       = $( '#integration-price' ).val();
		var shortcode   = '';

		if ( categories.length > 0 && categories[0] !== '' ){
			shortcode += " categories='" + categories + "'";
		}

		if ( rows.length > 0 ) {
			shortcode += " maxrows='" + rows + "'";
		}

		if ( prod_qty.length > 0 ) {
			shortcode += " product_qty='" + prod_qty + "'";
		}

		if ( $('#related_products').is(':checked') ) {
			shortcode += " related_products='true'";
		}

		if ( related_qty.length > 0 ) {
			shortcode += " related_count='" + related_qty + "'";
		}

		if ( $('#product_order').is(':checked') ) {
			shortcode += " product_order='desc'";
		}

		if ( $('#variations').is(':checked') ) {
			shortcode += " hidevariations='true'";
		}

		if ( $('#stock').is(':checked') ) {
			shortcode += " hidestock='true'";
		}

		if ( $('#salebadge').is(':checked') ) {
			shortcode += " hidesalebadge='true'";
		}

		if ( $('#categoryselector').is(':checked') ) {
			shortcode += " hidecategoryselector='true'";
		}

		if ( $('#sku').is(':checked') ) {
			shortcode += " hidesku='true'";
		}

		if ( $('#thumbnail').is(':checked') ) {
			shortcode += " hidethumbnail='true'";
		}

		if ( $('#desc').is(':checked') ) {
			shortcode += " hidedescription='true'";
		}

		if ( $('#price').is(':checked') ) {
			shortcode += " hideprice='true'";
		}
	
		if ( $('#hide_price_non_user').is(':checked') ) {
			shortcode += " hide_price_non_user='true'";
		}
		
		if ( $('#total').is(':checked') ) {
			shortcode += " hidetotal='true'";
		}

		if ( $('#carticon').is(':checked') ) {
			shortcode += " hidecarticon='true'";
		}
		
		if ( $('#bulkadd').is(':checked') ) {
			shortcode += " hidebulkadd='true'";
		}

		if ( $('#hide_search').is(':checked') ) {
			shortcode += " hide_search='true'";
		}

		if ( $('#hide_checkboxes').is(':checked') ) {
			shortcode += " hide_checkboxes='true'";
		}

		if ( $('#price-role').val().length > 0 ) {
			shortcode += $('#price-role').val();
		}

		if ( shortcode.length > 0 ) {
			$('#shortcode').text('[bulkshop ' + shortcode + ']');
		}
		else {
			$('#shortcode').text('[bulkshop]');
		}

	}

	function copyshortcode(){

		$('#shortcode').focus();
		$('#shortcode').select();
		document.execCommand('copy');

	}

</script>

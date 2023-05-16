<?php
/**
 * Template for the method entry. Based on the WooCommerce file class-wc-admin-settings.php.
 *
 * @global string CWL_SLUG
 *
 * @var $this CodupWooLoyaltymethodsFields
 * @var $field_config string[]
 * @package B2B_E-commerce_For_WooCommerce/templates
 */

// prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<tr valign="top" class="b2be-method-row-footer">
	<th class="input-column" colspan="5">
		<a href="javascript: void(0);" class="b2be-add-method-button">
			<?php echo esc_html__( 'Add Another Method', 'b2b-ecommerce' ); ?>
		</a>
	</th>
</tr>
<tr>
	<th colspan="5">
		<hr>
	</th>
</tr>
<?php

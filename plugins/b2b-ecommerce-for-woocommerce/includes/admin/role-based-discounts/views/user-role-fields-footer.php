<?php
/**
 * Template for the tier entry. Based on the WooCommerce file class-wc-admin-settings.php.
 *
 * @global string CWL_SLUG
 *
 * @var $this CodupWooLoyaltyTiersFields
 * @var $field_config string[]
 * @package B2B_E-commerce_For_WooCommerce/templates
 */

// prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<tr valign="top" class="cwl-tier-row-footer">
	<th class="input-column" colspan="5">
		<a href="javascript: void(0);" class="cwl-add-tier-button">
			<?php esc_html_e( 'Add Another Role', 'codup-wcrfq' ); ?>
		</a>
	</th>
</tr>

<?php

<?php
/**
 * File cwccv-admin-settings-custom-tab-settings.php
 *
 * @package templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<ul class="subsubsub">
<?php
$array_keys = array_keys( $sections );
foreach ( $sections as $key => $label ) {
	?>
	<li>
		<a href="admin.php?page=wc-settings&tab=<?php echo wp_kses_post( $tab_name ); ?>&section=<?php echo wp_kses_post( sanitize_title( $key ) ); ?>" class="<?php echo ( $current_section == $key ) ? 'current' : ''; ?>" > <?php echo wp_kses_post( $label ); ?> </a>
		<?php echo end( $array_keys ) === $key ? '' : '|'; ?>
		</li>
<?php } ?>
</ul><br class="clear" />

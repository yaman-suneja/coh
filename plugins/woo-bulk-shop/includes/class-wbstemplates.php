<?php
/**
 * Templates for Bulk Shop
 *
 * @package /includes/class-wbstemplates
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/class-wbsfunctions.php';

/**
 * Class for WbsFunctions
 */
class WbsTemplates {

	/**
	 * WbsFunctions
	 *
	 * @var var $wbsfunctions.
	 */
	public $wbsfunctions;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->wbsfunctions = new WbsFunctions();
	}

	/**
	 * Get table
	 *
	 * @param array $atts array.
	 */
	public function wbs_get_table( $atts ) {
		
		$prod_cat         = get_query_var( 'product_cat' );
		$search_type      = filter_input( 1, 'stype', FILTER_SANITIZE_STRING );
		$products         = $this->wbsfunctions->wbs_get_products( $atts );
		$hide_cat         = $atts['hidecategoryselector'] ? $atts['hidecategoryselector'] : 'false';
		$has_cat          = $atts['categories'];
		$hide_thumbnail   = $atts['hidethumbnail'] ? $atts['hidethumbnail'] : 'false';
		$hide_description = $atts['hidedescription'] ? $atts['hidedescription'] : 'false';
		$hide_carticon    = $atts['hidecarticon'] ? $atts['hidecarticon'] : 'false';
		$hide_bulkadd     = $atts['hidebulkadd'] ? $atts['hidebulkadd'] : 'false'; 
		$hide_price       = $atts['hideprice'] ? $atts['hideprice'] : 'false'; 
		$hide_total       = $atts['hidetotal'] ? $atts['hidetotal'] : 'false';
		$hide_checkboxes  = $atts['hide_checkboxes'] ? $atts['hide_checkboxes'] : 'false'; 
		$hide_search      = $atts['hide_search'] ? $atts['hide_search'] : 'false'; 
		$cat_css          = 'wbs-col-50';
		$thumb_css        = '';
		$desc_css         = '';
		$carticon_css     = '';
		$price_css        = '';
		$total_css        = '';
		$hide_css         = 'wbs-hide-column';

		if ( 'true' === $hide_search ) {
			$cat_css = 'wbs-col-70';
		}

		if ( 'true' === $hide_thumbnail ) {
			$thumb_css = $hide_css;
		}

		if ( 'true' === $hide_description ) {
			$desc_css = $hide_css;
		}

		if ( 'true' === $hide_price ) {
			$price_css = $hide_css;
		}

		if ( 'true' === $hide_total ) {
			$total_css = $hide_css;
		}

		if ( 'true' === $hide_carticon ) {
			$carticon_css = $hide_css;
		}
		
		?>

		<?php $this->wbsfunctions->wbs_get_table_paging( $products ); ?>
		
		<div id="wbs" class="wbs-head">
		
			<div class="wbs-col-25">
			<?php
			if ( 'false' === $hide_cat ) {
				echo esc_html( $this->wbsfunctions->wbs_get_categories_select( $prod_cat, $atts ) );
			}
			?>
			</div>
			<div class="<?php echo esc_attr( $cat_css ); ?>">
			<div id="bulk-qty-box" class="wbs-qty-box">
			<?php
			if ( 'false' === $hide_bulkadd ) {
				?>
				<input id="qty-add" type="number" min="0" step="1" placeholder="<?php echo esc_attr( $this->wbsfunctions->wbs_get_translation( 'Quantity' ) ); ?>">
				<button id="btn-add" class="wbs-btn-qty-add" onclick="addQty();"><?php echo esc_attr( $this->wbsfunctions->wbs_get_translation( 'Add' ) ); ?></button>
				<?php
			}
			?>
			</div>
			</div>
			<?php
			if ( 'false' === $hide_search ) {
				?>
				<div class="wbs-col-25-r">
				<form method="get" id="search" name="search">
					<?php
					if ( ! get_option( 'permalink_structure' ) ) {
						?>
						<input type="hidden" name="page_id" value="<?php echo esc_attr( get_query_var( 'page_id' ) ); ?>">
						<?php
					}
					?>
					<input type="search" name="product_search" class="search-field wbs-search" 
					placeholder="<?php echo esc_attr( $this->wbsfunctions->wbs_get_translation( 'Search' ) ); ?>">
					<div class="wbs-radio-div">
					<input type="radio" name="stype" class="wbs-radio" value="s" <?php echo ( 's' === $search_type || ! isset( $search_type ) ) ? 'checked="checked"' : ''; ?>> <?php echo esc_attr( $this->wbsfunctions->wbs_get_translation( 's-radio-text' ) ); ?>
					<input type="radio" name="stype" class="wbs-radio" value="sku" <?php echo ( 'sku' === $search_type ) ? 'checked="checked"' : ''; ?>> <?php echo esc_attr( $this->wbsfunctions->wbs_get_translation( 's-radio-sku' ) ); ?>
					<input type="radio" name="stype" class="wbs-radio" value="tag" <?php echo ( 'tag' === $search_type ) ? 'checked="checked"' : ''; ?>> <?php echo esc_attr( $this->wbsfunctions->wbs_get_translation( 's-radio-tag' ) ); ?>
					<button type="submit" class="wbs-btn-search-radio" onclick="submit();"><?php echo esc_attr_e( 'Search', 'woo-bulk-shop' ); ?></button>
					</div>
					<input type="hidden" onchange="submit();">
				</form>
				</div>
				<?php
			}
			?>
		</div>

		<div class="div-table">
		<?php $this->wbsfunctions->wbs_get_thumbnail_css(); ?>
		<input type="hidden" id="wbs_atts" value="<?php esc_attr_e( wp_json_encode( $atts ) ); ?>">
		<table id="wbs-table" class="<?php echo esc_attr( $this->wbsfunctions->wbs_get_table_css() ); ?> striped posts wbs-table-set">
			<thead>
				<tr>
					<th scope="col" class="wbs-th-check" <?php echo ( 'true' === $hide_checkboxes ) ? 'style="width:3px;"' : ''; ?>><input type="<?php echo ( 'true' === $hide_checkboxes ) ? 'hidden' : 'checkbox'; ?>" id="checkall" class="input-txt" onclick="checkAll();"></th>
					<th scope="col" class="wbs-th-image <?php echo esc_attr( $thumb_css ); ?>"></th>
					<th scope="col" class="wbs-th-name">
						<a href="#0" onclick="sort(2,'text');"><?php echo esc_attr( $this->wbsfunctions->wbs_get_translation( 'Name' ) ); ?></a> <i id="s2" class="fas fa-sort"></i></th>
					<th scope="col" class="wbs-th-desc <?php echo esc_attr( $desc_css ); ?>">
					<?php
					if ( strlen( $desc_css ) === 0 ) {
						?>
						<a href="#0" onclick="sort(3,'text');"><?php echo esc_attr( $this->wbsfunctions->wbs_get_translation( 'Description' ) ); ?></a> <i id="s3" class="fas fa-sort"></i>
						<?php
					}
					?>
					</th>
					<th scope="col" id="wbs-th-price" class="wbs-th-price <?php echo esc_attr( $price_css ); ?>">
					<?php
					if ( strlen( $price_css ) === 0 ) {
						?>
						<a href="#0" onclick="sort(4,'number');"><?php echo esc_attr( $this->wbsfunctions->wbs_get_translation( 'Price' ) ); ?></a> <i id="s4" class="fas fa-sort"></i></th>
						<?php
					}
					?>
					<th scope="col" class="wbs-th-qty">
						<a href="#0" onclick="sort(5,'number');"><?php echo esc_attr( $this->wbsfunctions->wbs_get_translation( 'QtyHead' ) ); ?></a> <i id="s5" class="fas fa-sort"></i></th>
					<th scope="col" id="wbs-total" class="wbs-th-total <?php echo esc_attr( $total_css ); ?>">
						<a href="#0" onclick="sort(6,'number');"><?php echo esc_attr( $this->wbsfunctions->wbs_get_translation( 'Total' ) ); ?></a> <i id="s6" class="fas fa-sort"></i></th>
					<th scope="col" id="wbs-th-cart" class="wbs-th-cart <?php echo esc_attr( $carticon_css ); ?>"></th>
				</tr>
			</thead>
			
				<?php $this->wbsfunctions->wbs_loop_products( $products, $atts ); ?>
			
				<?php $this->wbs_get_table_footer(); ?>
			
		</table>
		<div class="wbs-add-cart">
			<?php
			if ( $this->wbsfunctions->wbs_show_price_to_user( $atts ) ) {
				?>
				<button type="button" id="save" class="button action bs-button" onclick="saveAll();" style="width:200px;">
					<?php echo esc_attr( $this->wbsfunctions->wbs_get_translation( 'Addtocart' ) ); ?>
				</button>
				<?php
			}
			?>
		</div>
		<div class="wbs-saving" id="wbs-saving">
			<progress id="cart-saving" class="wbs-cart-saving" value="0" max="100"></progress>
		</div>
		<?php $this->wbsfunctions->wbs_get_table_paging( $products ); ?>

		</div>

		<?php
		wp_reset_postdata();
	}

	/**
	 * Get table footer
	 */
	public function wbs_get_table_footer() {
		?>
		<tfoot>
			<tr>
				<td><?php wp_nonce_field( 'wbs_id' ); ?></td>
				<td class="wbs-foot"></td>
				<td class="wbs-foot"></td>
				<td class="wbs-foot"></td>
				<td class="wbs-foot"></td>
				<td data-label="<?php esc_attr_e( 'Total quantity', 'woo-bulk-shop' ); ?>" class="wbs-center" style="padding-right:1px;"><span id="tbl-total-q" class="span-txt"></span></td>
				<td data-label="<?php esc_attr_e( 'Totals', 'woo-bulk-shop' ); ?>" class="wbs-right" style="padding-right:1px;"><span id="tbl-total-f" class="span-txt"></span></td>
				<td class="wbs-foot"></td>
			</tr>
		</tfoot>
		<?php
	}

}

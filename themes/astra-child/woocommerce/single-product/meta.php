<?php
/**
 * Single Product Meta
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/meta.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;
?>
<div class="product_meta">

	<?php do_action( 'woocommerce_product_meta_start' ); ?>

	<?php if ( wc_product_sku_enabled() && ( $product->get_sku() || $product->is_type( 'variable' ) ) ) : ?>

		<!-- <span class="sku_wrapper"><?php esc_html_e( 'SKU:', 'woocommerce' ); ?> <span class="sku"><?php echo ( $sku = $product->get_sku() ) ? $sku : esc_html__( 'N/A', 'woocommerce' ); ?></span></span> -->

	<?php endif; ?>

	<?php //echo wc_get_product_category_list( $product->get_id(), ', ', '<span class="posted_in">' . _n( 'Category:', 'Categories:', count( $product->get_category_ids() ), 'woocommerce' ) . ' ', '</span>' ); ?>
	
	<?php
	if( have_rows('product_detail') ):
        $i = 1;
        // Loop through rows.
        while( have_rows('product_detail') ) : the_row();
    
            // Load sub field value.
            $title_accor = get_sub_field('title');
            $des_accor = get_sub_field('description');
            $button_text = get_sub_field('button_text');
            
            ?>
            
            <button class="accordion"><?php echo $title_accor; ?></button>
            <div class="panel">
                <?php if($button_text != ''){ ?>
              <p><?php echo substr($des_accor, 0, 200); ?></p>
              <?php }else{ ?>
                <p><?php echo $des_accor; ?></p>
              <?php } ?>
              
              <?php if($button_text){ ?>
              <button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#myModal-<?php echo $i; ?>"><?php echo $button_text; ?></button>
              <?php } ?> 
            

              <!-- Modal -->
              <div class="modal fade" id="myModal-<?php echo $i; ?>" role="dialog">
                <div class="modal-dialog">
                
                  <!-- Modal content-->
                  <div class="modal-content">
                    <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal">&times;</button>
                      <h4 class="modal-title"><?php echo $title_accor; ?></h4>
                    </div>
                    <div class="modal-body">
                      <p><?php echo $des_accor; ?></p>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                  </div>
                  
                </div>
              </div>
            </div>
        <?php
        $i++;
        endwhile;
    
    // No value.
    else :
        // Do something...
    endif;

    if( get_field('ingredients_details') ){ ?>
      <div class="modal fade" id="myModal-ingredients" role="dialog">
        <div class="modal-dialog">
          <!-- Modal content-->
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title"><?php echo get_field('ingredients_details_title'); ?></h4>
            </div>
            <div class="modal-body">
              <p><?php echo get_field('ingredients_details'); ?></p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
      </div>
    <?php }
	?>

	<?php do_action( 'woocommerce_product_meta_end' ); ?>

</div>

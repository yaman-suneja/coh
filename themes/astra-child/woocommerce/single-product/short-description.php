<?php
/**
 * Single product short description
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/short-description.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $post;

$short_description = apply_filters( 'woocommerce_short_description', $post->post_excerpt );

// if ( ! $short_description ) {
// 	return;
// }

?>
<div class="woocommerce-product-details__short-description">
	<?php echo $short_description; // WPCS: XSS ok. ?>
	
	<?php
	$title_1 = get_field('title_1');
	$description_1 = get_field('description_1');
	$title_2 = get_field('title_2');
	//$description_2 = get_field('description_2');
    if( $title_1 ) {
        echo '<strong>'.$title_1.'</strong>';
        echo $description_1;
    }
    
    // if( $title_2 ) {
        // echo '<strong>'.$title_2.'</strong>';
        if( have_rows('description_2') ):
            echo '<ul class="good-points">';
                while( have_rows('description_2') ) : the_row();
                    // echo '<li><span><img src='.get_sub_field('gtk_image').'></span>'.get_sub_field('gtk_title').'</li>';
                    echo '<li class="gtk-img"><span><img src='.get_sub_field('gtk_image').'></span></li>';
                endwhile;
            echo '</ul>';
        endif;
    // }
	?>
</div>

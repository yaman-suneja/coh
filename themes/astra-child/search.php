<?php
/**
 * The template for displaying search results pages.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package Astra
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

get_header(); ?>

<?php astra_archive_header(); ?>

<div class="woocommerce content-container mobile-columns-1">
	<?php if ( have_posts() ): ?>
		<ul class="products columns-4">
			<?php while( have_posts() ): ?>
				<?php the_post(); ?>
				<li class="ast-col-sm-12 ast-article-post astra-woo-hover-swap product type-product post-36731 status-publish first instock product_cat-cbd-body-products product_tag-acid-trip product_tag-acne-prone product_tag-ahas product_tag-antiaging product_tag-antioxidants product_tag-brightening product_tag-cbd-exfoliant product_tag-exfoliation product_tag-face-scrub has-post-thumbnail taxable shipping-taxable purchasable product-type-variable berocket_lmp_first_on_page">
					<div class="astra-shop-thumbnail-wrap">
						<a href="<?php echo get_the_permalink(); ?>" class="woocommerce-LoopProduct-link woocommerce-loop-product__link">
							<img src="<?php echo get_the_post_thumbnail_url(); ?>" class="attachment-woocommerce_thumbnail size-woocommerce_thumbnail" alt="<?php echo get_the_title(); ?>" loading="lazy">
						</a>
						<div class="astra-shop-summary-wrap">
							<a href="<?php echo get_the_permalink(); ?>" class="ast-loop-product__link">
								<h2 class="woocommerce-loop-product__title"><?php echo get_the_title(); ?></h2>
							</a>
						</div>
					</div>
				</li>
			<?php endwhile; ?>
		</ul>
	<?php endif; ?>
</div>
<?php get_footer(); ?>


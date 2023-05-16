<?php
/**
 * Functions for Bulk Shop
 *
 * @package /includes/class-wbsfunctions
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class for WbsFunctions
 */
class WbsFunctions {

	/**
	 * Get product categories - select box
	 *
	 * @param var $prodcat object.
	 */
	public function wbs_get_categories_select( $prodcat, $atts ) {
		
		$args = array(
			'hide_empty'        => 1,
			'taxonomy'          => 'product_cat',
			'hierarchical'      => 1,
			'show_count'        => 1,
			'name'              => 'product_cat',
			'orderby'           => 'product_cat',
			'order'             => 'asc',
			'show_option_none'  => __( 'Select category', 'woo-bulk-shop' ),
			'option_none_value' => '',
			'selected'          => $prodcat,
			'include'			=> $atts['categories'] ? $atts['categories'] : '',	
		);
		?>
		<form method="post" onchange="submit();" action="<?php echo esc_attr( get_permalink() ); ?>" class="wbs-frm-category">
		<input type="hidden" name="page_id" value="<?php echo esc_attr( get_post()->ID ); ?>">
		<?php
			wp_dropdown_categories( apply_filters( 'wbs_categories_select_query', $args ) );
		?>
		</form>
		<?php
	}

	/**
	 * Get product categories - select box
	 */
	public function wbs_get_categories_settings_select() {

		$args = array(
			'hide_empty'        => 0,
			'taxonomy'          => 'product_cat',
			'hierarchical'      => 1,
			'show_count'        => 1,
			'name'              => 'product_cat',
			'orderby'           => 'product_cat',
			'order'             => 'asc',
			'show_option_none'  => __( 'All categories', 'woo-bulk-shop' ),
			'option_none_value' => '',
			'selected'          => 0,
		);
		?>
		<?php
			wp_dropdown_categories( apply_filters( 'wbs_categories_select_settings_query', $args ) );
		?>
		<?php
	}

	/**
	 * Get related products, used on single product page
	 */
	public function wbs_get_related_products( $atts ) {

		global $product;

		if ( ! $product ) {
			return;
		}

		$post_per_page = $atts['maxrows'] ? $atts['maxrows'] : 50;
		$related_count = $atts['related_count'] ? $atts['related_count'] : 5;
		$related       = wc_get_related_products( $product->get_id(), $related_count );
		$upsell        = $product->get_upsell_ids();
		$arr           = array_merge( $upsell, $related ); 
		$list          = implode( ',', $arr );
		$items         = array_map( 'intval', explode( ',', $list ) );
		$page_num      = 1;

		if ( get_query_var( 'paged' ) ) { 
			$page_num = get_query_var( 'paged' ); 
		} elseif ( get_query_var( 'page' ) ) { 
			$page_num = get_query_var( 'page' ); 
		} else { 
			$page_num = 1; 
		}

		$args = array(
			'include'  => $items,
			'paginate' => true,
			'page'     => $page_num,
			'limit'    => $post_per_page,
			'orderby'  => 'include',
		);

		return wc_get_products( $args );

	}


	/**
	 * Get products
	 *
	 * @param var $atts object.
	 */
	public function wbs_get_products( $atts ) {
		
		$post_per_page = $atts['maxrows'] ? $atts['maxrows'] : 50;
		$product_order = $atts['product_order'] ? $atts['product_order'] : 'asc';
		$get_related   = $atts['related_products'] ? $atts['related_products'] : 'false';
		$prod_cat      = get_query_var( 'product_cat' );
		$prod_search   = filter_input( 1, 'product_search', FILTER_SANITIZE_STRING );
		$prod_search_t = filter_input( 1, 'stype', FILTER_SANITIZE_STRING );
		$categories    = array();
		$page_num      = 1;

		if ( 'true' === $get_related ) {
			return $this->wbs_get_related_products( $atts );
		}

		if ( get_query_var( 'paged' ) ) { 
			$page_num = get_query_var( 'paged' ); 
		} elseif ( get_query_var( 'page' ) ) { 
			$page_num = get_query_var( 'page' ); 
		} else { 
			$page_num = 1; 
		}

		if ( strlen( $prod_search ) > 0 ) {
			$prod_cat = '';
		}

		$slug = $this->wbs_get_category_slug( $prod_cat );

		if ( ! $slug && strlen( $atts['categories'] ) > 0 ) {
			$cat_ids = explode( ',', $atts['categories'] );
			foreach ( $cat_ids as $category_id ) {
				$the_slug = $this->wbs_get_category_slug( $category_id );
				array_push( $categories, $the_slug );
			}
		} else {
			array_push( $categories, $slug );
		}

		$args = array(
			'limit'   => -1,
			'orderby' => 'name',
			'status'  => 'publish',
			'order'   => $product_order,
		);

		$query = new WC_Product_Query( $args );
		
		if ( isset( $categories[0] ) && count( $categories ) > 0 ) {
			$query->set( 'category', $categories );
		}

		if ( $prod_search ) {
			$query->set( $prod_search_t, $prod_search );
			if ( 'tag' === $prod_search_t ) {
				$query->set( $prod_search_t, array( $prod_search ) );
			}
			$page_num = 1;
		}

		if ( $post_per_page ) {
			$query->set( 'page', $page_num );
			$query->set( 'paginate', true );
			$query->set( 'limit', $post_per_page );
			$query->set( 'product_type', '' );
		}

		apply_filters( 'wbs_get_products_query', $query );
		return $query->get_products();
	}


	/**
	 * Get product category slug
	 *
	 * @param var $id int.
	 */
	public function wbs_get_category_slug( $id ) {
		$term = get_term_by( 'id', $id, 'product_cat', 'ARRAY_A' );
		return $term['slug'];
	}

	/**
	 * Custom price
	 *
	 * @param var $price price.
	 * @param var $product product.
	 * @param var $atts attributes.
	 */
	public function wbs_get_custom_price( $price, $product, $atts ) {

		$user        = wp_get_current_user();
		$price_roles = $atts['price_field_roles'] ? $atts['price_field_roles'] : '';
		$price_field = $atts['price_field'] ? $atts['price_field'] : '';

		for ( $i = 0; $i < 10; $i++ ) {
			
			if ( $i > 0 ) {
				$price_roles = $atts['price_field_roles' . strval($i) ] ? $atts['price_field_roles' . strval($i) ] : '';
				$price_field = $atts['price_field' . strval($i) ] ? $atts['price_field' . strval($i) ] : '';
			}
			
			if ( strlen( $price_field ) > 0 && strlen( $price_roles ) > 0 ) {
				
				$change_price = false;
				$roles        = explode( ',', $price_roles );

				foreach ( $roles as $role ) {
					if ( ( 0 !== $user->ID ) && ( in_array( $role, (array) $user->roles, true ) ) ) {
						$change_price = true;
					}
				}

				if ( $change_price ) {
					if ( get_post_meta( $product->get_id(), $price_field, true ) > 0 ) {
						// Use the price field
						$price = get_post_meta( $product->get_id(), $price_field, true );
					}
				}

			}
		}
		return apply_filters( 'wbs_custom_product_price', $price );

	}


	/**
	 * Name your price integration
	 *
	 * @param var $product product.
	 */
	public function wbs_get_nyp_price( $product ) {

		$min_price   = '_min_price';
		$max_price   = '_maximum_price';
		$suggested   = '_suggested_price';
		$hide_min    = '_hide_nyp_minimum';
		$product_id  = $product->get_id();
		$return_html = '';

		if ( get_post_meta( $product_id, $suggested, true ) > 0 ) {
			
			$price          = get_post_meta( $product_id, $suggested, true );
			$price_min      = get_post_meta( $product_id, $min_price, true );
			$price_max      = get_post_meta( $product_id, $max_price, true );
			$price_min_hide = get_post_meta( $product_id, $hide_min, true );
			$title_nyp      = $this->wbs_get_translation( 'NYP' );
			$title_min      = $this->wbs_get_translation( 'NYP-min' );
			$html_input     = '<input name="price" type="number" onchange="nypValidate(this,' . $price_min . ');" class="wbs-nyp-price-input" min="' . $price_min . '" value="' . $price . '"><span class="wbs-nyp">' . $title_nyp . '</span>';
			$html_input    .= '<input name="_suggested_price" type="hidden" value="' . $price . '">';
			
			if ( 'no' === $price_min_hide ) {
				$html_input .= '<span class="wbs-nyp-min">' . $title_min . ': ' . wc_price( $price_min ) . '</span>';
			}

			$return_html = $html_input;
		}

		return $return_html;
	}
	
	/**
	 * Function to get current url
	 */
	public function wbs_get_current_url() {
		
		$product_cat      = get_query_var( 'product_cat' );
		$permalink_struct = get_option( 'permalink_structure' );
		$prod_str         = '';

		if ( strlen( $product_cat ) > 0 ) {
			$prod_str = '?product_cat=' . $product_cat;
			if ( strlen( $permalink_struct ) === 0 ) {
				$prod_str = '&product_cat=' . $product_cat;
			}
		}

		$current_url = get_permalink() . 'page/%1$s/' . $prod_str;

		if ( strlen( $permalink_struct ) === 0 ) {
			$current_url = get_permalink() . '&paged=%1$s' . $prod_str;
		}
		
		if ( is_home() || is_front_page() ) {
			$current_url = get_permalink() . '?page_id=' . get_the_ID() . '&paged=%1$s' . $prod_str;
		}

		return apply_filters( 'wbs_get_current_url', $current_url );
	}

	/**
	 * Get paging function
	 *
	 * @param var $products array.
	 */
	public function wbs_get_table_paging( $products ) {

		if ( $products->max_num_pages <= 1 ) {
			return;
		}

		$current_page = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
		$next_page    = ( $current_page ) ? $current_page + 1 : 1;
		$next_shown   = ( $next_page < $products->max_num_pages + 1 ) ? true : false;
		$prev_page    = ( $current_page > 1 ) ? $current_page - 1 : 1;
		$prev_shown   = ( $prev_page < $products->max_num_pages && $current_page > $prev_page ) ? true : false;
		
		$current_url = $this->wbs_get_current_url();
	
		?>
		<div class="div-paging display-desktop"> <!-- Paging -->
			<div class="tablenav-pages">
			<span class="displaying-num"></span>
			<span class="pagination-links">
			<?php
			$prev_shown_class_disabled = '';
			$prev_shown_url            = '';
			$prev_shown_url_prev       = '';

			if ( ! $prev_shown ) {
				$prev_shown_class_disabled = 'disabled';
			}

			if ( $prev_shown ) {
				$prev_shown_url      = sprintf( esc_url( $current_url ), 1 );
				$prev_shown_url_prev = sprintf( esc_url( $current_url ), esc_attr( $prev_page ) );
			}
			?>
			<a class="prev-page button <?php echo esc_attr( $prev_shown_class_disabled ); ?>" href="<?php echo esc_url( $prev_shown_url ); ?>">
			<span class="screen-reader-text"><?php esc_html_e( 'First page', 'woo-bulk-shop' ); ?></span><span aria-hidden="true">«</span></a>
			<a class="prev-page button <?php echo esc_attr( $prev_shown_class_disabled ); ?>" href="<?php echo esc_url( $prev_shown_url_prev ); ?>">
			<span class="screen-reader-text"><?php esc_html_e( 'Previous page', 'woo-bulk-shop' ); ?></span>
			<span aria-hidden="true">‹</span>
			</a>
			<span class="prev-page button" aria-hidden="true"><?php echo esc_attr( $current_page ? $current_page : 1 ); ?></span>
			<span class="tablenav-paging-text"> <?php esc_html_e( 'of', 'woo-bulk-shop' ); ?> 
			<span class="total-pages"><?php echo esc_html( $products->max_num_pages ); ?></span>
			</span>
			<?php
			$next_shown_class_disabled = '';
			$next_shown_url            = '';
			$next_shown_url_max        = '';

			if ( ! $next_shown ) {
				$next_shown_class_disabled = 'disabled';
			}

			if ( $next_shown ) {
				$next_shown_url     = sprintf( esc_url( $current_url ), esc_attr( $next_page ) );
				$next_shown_url_max = sprintf( esc_url( $current_url ), esc_attr( $products->max_num_pages ) );
			}
			?>
			<a class="next-page button <?php echo esc_attr( $next_shown_class_disabled ); ?>" href="<?php echo esc_url( $next_shown_url ); ?>">
			<span class="screen-reader-text"><?php esc_html_e( 'Next page', 'woo-bulk-shop' ); ?></span><span aria-hidden="true">›</span></a>
			<a class="last-page button <?php echo esc_attr( $next_shown_class_disabled ); ?>" href="<?php echo esc_url( $next_shown_url_max ); ?>">
			<span class="screen-reader-text"><?php esc_html_e( 'Last page', 'woo-bulk-shop' ); ?></span>
			<span aria-hidden="true">»</span>
			</a>
			</span>
			</div>
		</div>
		<?php
	}


	/**
	 * Loop products
	 *
	 * @param array $products array.
	 * @param array $atts array.
	 */
	public function wbs_loop_products( $products, $atts ) {

		$hide_variations = $atts['hidevariations'] ? $atts['hidevariations'] : 'false';

		if ( $products ) {
			foreach ( $products as $products => $product_arr ) {

				if ( is_array( $product_arr ) ) {
					foreach ( $product_arr as $product ) {

						$has_children = false;
						if ( count( $product->get_children() ) > 0 ) {
							$has_children = true;
						}

						if ( $has_children ) {
							if ( 'true' === $hide_variations ) {
								$this->wbs_get_product_row( $product, $atts, false, $has_children );
							} else {
								$children = $product->get_children();
								foreach ( $children as $value ) {
									$child = wc_get_product( $value );
									$this->wbs_get_product_row( $child, $atts, true, $has_children );
								}
							}
						} else {
							$this->wbs_get_product_row( $product, $atts, false, $has_children );
						}

					}
				}
			}
			wp_reset_postdata();
		}
	}

	/**
	 * Get product variation name
	 *
	 * @param var $product object.
	 * @param var $has_attributes bool.
	 */
	public function wbs_get_product_variation_name( $product, $has_attributes ) {
		
		$product_name = $product->get_title();
		
		if ( $has_attributes ) {

			$attributes = $product->get_attributes();
			
			if ( isset( $attributes ) && is_array( $attributes ) ) {
				
				foreach ( $attributes as $key => $val ) {
					if ( isset( $val ) && is_string( $val ) && strlen( $val ) > 0 ) {
						$key_name      = preg_replace( '/pa\_/', '', $key );
						$product_name .= ', ' . ucfirst( $key_name ) . ': ' . ucfirst( $val );
					}
				}
				
			}
		}

		return apply_filters( 'wbs_product_variation_name', $product_name );
	}

	/**
	 * Get stock status text
	 *  
	 * @param var $product object.
	 */
	public function wbs_get_stock_status_txt( $product ) {
		
		$stock_status = '';

		switch ( $product->get_stock_status() ) {
			case 'instock':
				$stock_status = __( 'In stock', 'woo-bulk-shop' );
				break;
			case 'outofstock':
				$stock_status = __( 'Out of stock', 'woo-bulk-shop' );
				break;
			case 'onbackorder':
				$stock_status = __( 'On backorder', 'woo-bulk-shop' );
				break;
		}

		return $stock_status;
	}

	/**
	 * Show product options
	 *
	 * @param var $product object.
	 */
	public function wbs_get_product_variable_dropdown( $product, $frm_id ) {
		
		$attributes = $product->get_variation_attributes();
		$select     = __( 'Select', 'woo-bulk-shop' );

		?>
		<input type="hidden" name="form_id" value="<?php esc_attr_e( $frm_id ); ?>">
		<?php
		
		foreach ( $attributes as $attribute_name => $options ) :
			?>
			<div class="wbs-select">
				<?php
					wc_dropdown_variation_attribute_options( array( 
						'options'          => $options, 
						'attribute'        => $attribute_name, 
						'product'          => $product,
						'class'            => 'wbs-variation-select',
						'show_option_none' => $select . ' ' . wc_attribute_label( $attribute_name ),
						) 
					);
				?>
			</div>
			<?php
		endforeach;

	}

	/**
	 * Get min and max price on variable product
	 *
	 * @param var $product object.
	 */
	public function wbs_get_variable_price_min_max( $product ) {
		
		$prices   = array();
		$children = $product->get_children();
								
		if ( count ( $children ) > 0 ) {
			
			foreach ( $children as $cid ) {
				$child = wc_get_product( $cid );
				array_push( $prices, $child->get_price() );
			}
			sort( $prices );

			//Check if prices not equals
			if ( end( $prices ) !== $prices[0] ) {
				echo wp_kses( wc_price( $prices[0] ), $this->wbs_get_allowed_html() ) . ' - ' . wp_kses( wc_price( end( $prices ) ), $this->wbs_get_allowed_html() );
			} else {
				echo wp_kses( wc_price( $prices[0] ), $this->wbs_get_allowed_html() );
			}

		} else {

			$prices     = array( $product->get_variation_price( 'min', true ), $product->get_variation_price( 'max', true ) );
			$prices_reg = array( $product->get_variation_regular_price( 'min', true ), $product->get_variation_regular_price( 'max', true ) );
			
			array_push( $prices, array( $prices_reg[0], $prices_reg[1] ) );
			sort( $prices );

			if ( $prices[0] !== $prices[1] ) {
				echo wp_kses( wc_price( $prices[0] ), $this->wbs_get_allowed_html() ) . ' - ' . wp_kses( wc_price( $prices[1] ), $this->wbs_get_allowed_html() );
			} else {
				echo wp_kses( wc_price( $prices[0] ), $this->wbs_get_allowed_html() );
			}
		}

	}

	/**
	 * Get allowed html
	 */
	public function wbs_get_allowed_html() {
		
		$html = array(
			'img'    => array(
				'width'  => array(),
				'height' => array(),
				'src'    => array(),
				'class'  => array(),
				'srcset' => array(),
				'alt'    => array(),
			),
			'span'   => array(
				'class' => array(),
			),
			'br'     => array(),
			'em'     => array(),
			'strong' => array(),
			'ul'     => array(
				'class' => array(),
				'style' => array(),
			),
			'ol'     => array(
				'class' => array(),
				'style' => array(),
				'type'  => array(),
			),
			'li'     => array(),
			'input'  => array(
				'class'    => array(),
				'style'    => array(),
				'type'     => array(),
				'name'     => array(),
				'value'    => array(),
				'min'      => array(),
				'max'      => array(),
				'onchange' => array(),
			),
			'a'     => array(
				'class' => array(),
				'style' => array(),
				'href'  => array(),
			),
		);

		return apply_filters( 'wbs_allowed_html', $html );
	}

	/**
	 * Get sales badge
	 *
	 * @param bool $hide_salebadge bool.
	 * @param var $product object.
	 * @param var $reduction number.
	 * @param bool $need_role bool.
	 */
	public function wbs_get_sales_badge( $hide_salebadge, $product, $reduction, $need_role ) {
		
		if ( 'false' === $hide_salebadge && 
				strlen( $product->get_sale_price() ) > 0 && 
				! $need_role ) {
			?>
			<span id="<?php echo esc_attr( $product->get_id() ); ?>-sale-badge" class="wbs-sale-badge">
			<?php echo esc_attr( $this->wbs_get_translation( 'Sale' ) ); ?>
			<?php
			if ( strlen( $reduction ) > 0 && ! empty( $product->get_sale_price() ) ) {
				echo esc_html( $reduction . '%' );
			} 
			?>
			</span>
			<?php 
		};
	}
	
	/**
	 * Get product row
	 *
	 * @param object $p product object.
	 * @param array $atts array.
	 * @param bool $has_attributes bool.
	 * @param bool $has_children bool.
	 */
	public function wbs_get_product_row( $p, $atts, $has_attributes, $has_children ) {
		
		global $product;
		
		$product = wc_get_product( $p->get_id() );

		/* If no price, is hidden, grouped product or not in stock */
		if ( $product->get_price() === '' || 
			$product->is_type( 'grouped' ) || 
			'hidden' === $product->get_catalog_visibility() || 
			! $product->is_in_stock() ) {
			return 0;
		}

		$product_name     = $this->wbs_get_product_variation_name( $product, $has_attributes );
		$hide_stock       = $atts['hidestock'] ? $atts['hidestock'] : 'false'; 
		$hide_salebadge   = $atts['hidesalebadge'] ? $atts['hidesalebadge'] : 'false';
		$hide_variations  = $atts['hidevariations'] ? $atts['hidevariations'] : 'false';
		$hide_sku         = $atts['hidesku'] ? $atts['hidesku'] : 'false';
		$hide_thumbnail   = $atts['hidethumbnail'] ? $atts['hidethumbnail'] : 'false';
		$hide_description = $atts['hidedescription'] ? $atts['hidedescription'] : 'false';
		$hide_carticon    = $atts['hidecarticon'] ? $atts['hidecarticon'] : 'false';
		$hide_price       = $atts['hideprice'] ? $atts['hideprice'] : 'false'; 
		$hide_total       = $atts['hidetotal'] ? $atts['hidetotal'] : 'false'; 
		$price_field      = $atts['price_field'] ? $atts['price_field'] : '';
		$price_roles      = $atts['price_field_roles'] ? $atts['price_field_roles'] : '';
		$product_qty      = $atts['product_qty'] ? $atts['product_qty'] : 0;
		$hide_checkboxes  = $atts['hide_checkboxes'] ? $atts['hide_checkboxes'] : 'false'; 
		$cart_message     = __( 'In cart', 'woo-bulk-shop' );
		$in_cart          = null;
		$cart_item        = null;
		$thumb_css        = '';
		$desc_css         = '';
		$carticon_css     = '';
		$price_css        = '';
		$total_css        = '';
		$stock_status     = $this->wbs_get_stock_status_txt( $product );
		$show_price       = $this->wbs_show_price_to_user( $atts );

		if ( 'true' === $hide_thumbnail ) {
			$thumb_css = 'wbs-collapse';
		}
		if ( 'true' === $hide_description ) {
			$desc_css = 'wbs-collapse';
		}
		if ( 'true' === $hide_price ) {
			$price_css = 'wbs-collapse';
		}
		if ( 'true' === $hide_total ) {
			$total_css = 'wbs-collapse';
		}
		if ( 'true' === $hide_carticon ) {
			$carticon_css = 'wbs-hide-column';
		}

		if ( WC()->cart ) {
			
			$product_cart_id = WC()->cart->generate_cart_id( $product->get_id() );

			if ( $has_attributes ) {
				$product_cart_id = WC()->cart->generate_cart_id( $product->get_parent_id(), $product->get_id() );
			}

			$in_cart   = WC()->cart->find_product_in_cart( $product_cart_id );
			$cart_item = WC()->cart->get_cart_item( $in_cart );

		}

		$reduction = '';
		$need_role = false;
		$price     = $product->get_price();
		$price_nyp = $this->wbs_get_nyp_price( $product );

		// Custom prices and roles
		if ( strlen( $price_field ) > 0 && strlen( $price_roles ) > 0 ) {

			$price = $this->wbs_get_custom_price( $product->get_price(), $product, $atts );
			$user  = wp_get_current_user();
			$roles = explode( ',', $price_roles );

			foreach ( $roles as $role ) {
				if ( ( 0 !== $user->ID ) && ( in_array( $role, (array) $user->roles, true ) ) ) {
					$need_role = true;
				}
			}
		}
		
		if ( strlen( $product->get_sale_price() ) > 0 && floatval( $product->get_regular_price() ) > 0 ) {
			
			$price_reg = $product->get_regular_price() ? floatval( $product->get_regular_price() ) : 0;
			$saleprice = $product->get_sale_price() ? floatval( $product->get_sale_price() ) : 0;
			$reduction = round( ( $price_reg - $saleprice ) * 100 / $price_reg );
			if ( ! $reduction ) {
				$reduction = 0;
			}
		}

		$frm_id = 'frm_' . $product->get_id() . '-' . wp_rand( 200, 20000 );

		?>
		<tr>
			<td scope="row" data-label="" id="<?php echo esc_attr( $product->get_id() ); ?>">
				<form id="<?php echo esc_attr( $frm_id ); ?>" method="post">
				<input name="product_id" value="<?php echo esc_attr( $product->get_id() ); ?>" type="hidden">
				<input name="id" value="<?php echo esc_attr( $product->get_id() ); ?>" type="<?php echo ( 'true' === $hide_checkboxes ) ? 'hidden' : 'checkbox'; ?>" class="input-txt" onclick="clearRow(this);">
			</td>
			<td data-label="<?php echo esc_attr( $this->wbs_get_translation( 'Productimage' ) ); ?>" class="product-thumbnail wbs-center <?php echo esc_attr( $thumb_css ); ?>">
				<a href="<?php echo esc_attr( get_permalink( $product->get_id() ) ); ?>">
					<?php echo wp_kses( $product->get_image('woocommerce_thumbnail'), $this->wbs_get_allowed_html() ); ?>
				</a>
			</td>
			<td data-label="<?php echo esc_attr( $this->wbs_get_translation( 'Name' ) ); ?>">
				<a href="<?php echo esc_attr( get_permalink( $product->get_id() ) ); ?>" style='display:block;'>
					<?php echo esc_html( $product_name ); ?>
				</a>
				<?php
				if ( 'true' === $hide_variations && $has_children ) {
					$this->wbs_get_product_variable_dropdown( $product, $frm_id );
				}

				if ( 'false' === $hide_stock ) {
					if ( $product->get_stock_quantity() > 0 ) {
						/* translators: quantity in stock */
						echo sprintf( esc_html__( '%1$s in stock', 'woo-bulk-shop' ), esc_html( $product->get_stock_quantity() ) );
					} else {
						?>
						<span id="<?php echo esc_attr( $product->get_id() ); ?>-stock-status"><?php echo esc_html( $stock_status ); ?></span>
						<?php
					}
				}
				
				if ( $in_cart ) {
					?>
					<div class="wbs-tooltip"><i class="fas fa-shopping-basket"></i>
						<span class="wbs-tooltiptext"><?php echo sprintf( esc_html( $cart_message . ': %1$s' ), esc_html( ( int ) $cart_item['quantity'] ) ); ?></span>
					</div>
					<?php
				}
				
				if ( 'false' === $hide_sku && strlen( $product->get_sku() ) > 0 ) {
					?>
					<div class="wbs-sku">
						<?php echo esc_html_e( 'SKU', 'woo-bulk-shop' ); ?>: <span id="<?php echo esc_attr( $product->get_id() ); ?>-sku"><?php echo esc_attr( $product->get_sku() ); ?></span>
					</div>
					<?php
				}
				?>
			</td>
			<td data-label="<?php echo esc_attr( $this->wbs_get_translation( 'Description' ) ); ?>" class="<?php echo esc_attr( $desc_css ); ?>">
			<?php
			if ( strlen( $desc_css ) === 0 ) {
				?>
				<?php echo wp_kses( $product->get_short_description(), $this->wbs_get_allowed_html() ); ?>
				<?php
			}
			?>
			</td>
			<td data-label="<?php echo esc_attr( $this->wbs_get_translation( 'Price' ) ); ?>" class="wbs-center <?php echo esc_attr( $price_css ); ?>">
			<?php
			if ( strlen( $price_nyp ) > 0 && strlen( $price_css ) === 0 ) {
				echo wp_kses( $price_nyp, $this->wbs_get_allowed_html() );
			} else {
				?>
				<input name="price" type="hidden" value="<?php echo esc_attr( $price ); ?>">
				<?php
			}
			?>
			<?php
			if ( strlen( $price_css ) === 0 && $show_price ) {
				if ( $product->is_on_sale() && ! $need_role && ! $has_children ) {
					?>
					<s>
					<?php
					echo wp_kses( wc_price( $product->get_regular_price() ), $this->wbs_get_allowed_html() );
					?>
					</s>
					<?php
					/* Check for dynamic pricing */
					$sale_price = $product->get_sale_price();
					if ( empty( $sale_price ) ) {
						$sale_price = $price;
					}
					echo wp_kses( wc_price( $sale_price ), $this->wbs_get_allowed_html() );
					?>
					<?php
				} elseif ( 'true' === $hide_variations && $has_children ) {
					?>
					<span id="<?php echo esc_attr( $product->get_id() ); ?>-var-price">
					<?php
						$this->wbs_get_variable_price_min_max( $product );
					?>
					</span>
					<?php

				} elseif ( $need_role && $product->get_regular_price() > $price ) {
					?>
					<s>
					<?php
					echo wp_kses( wc_price( $product->get_regular_price() ), $this->wbs_get_allowed_html() );
					?>
					</s>
					<?php
					echo wp_kses( wc_price( $price ), $this->wbs_get_allowed_html() );
					?>
					<?php
				} else {
					if ( 'false' === $hide_variations && $product->is_on_sale() ) {
						?>
						<s>
						<?php
						echo wp_kses( wc_price( $product->get_regular_price() ), $this->wbs_get_allowed_html() );
						?>
						</s>
						<?php
					}
					if ( strlen( $price_nyp ) === 0 ) {
						echo wp_kses( wc_price( $price ), $this->wbs_get_allowed_html() );
					}

				}
				
				$this->wbs_get_sales_badge( $hide_salebadge, $product, $reduction, $need_role );

			} else {
				echo esc_attr( $this->wbs_get_translation( 'login-see-price' ) );
			}
			?>
			</td>
			<td data-label="<?php echo esc_attr( $this->wbs_get_translation( 'QtyHead' ) ); ?>" class="wbs-qty-td">
				<a href="#0" name="remove-qty" class="wbs-btn-qty" onclick="changeQty( 'm', '<?php echo esc_js( $frm_id ); ?>');"><i class="fas fa-minus-square"></i></a>
				<input type="number" class="wbs-input-qty" value="<?php echo esc_attr( $product_qty ); ?>" name="qty" onchange="changeQty( 'x', '<?php echo esc_js( $frm_id ); ?>');" step="1" pattern="\d+" min="0" onmouseover="this.focus();">
				<a href="#0" name="add-qty" class="wbs-btn-qty" onclick="changeQty( 'p', '<?php echo esc_js( $frm_id ); ?>');"><i class="fas fa-plus-square"></i></a>
			</td>
			<td data-label="<?php echo esc_attr( $this->wbs_get_translation( 'Total' ) ); ?>" class="td-total <?php echo esc_attr( $total_css ); ?>">
			<?php
			if ( strlen( $total_css) === 0 && $show_price ) {
				?>
				<input type="number" name="row-total" class="wbs-input-total" readonly="readonly" style="direction:rtl;border:none;background-color:transparent;">
				<?php
			} elseif ( strlen( $total_css) === 0 && ! $show_price ) {
				?>
				<input type="number" class="wbs-input-total" readonly="readonly">
				<?php
			}
			?>
			</td>
			<td data-label="<?php echo esc_attr( $this->wbs_get_translation( 'Addtocart' ) ); ?>" class="td-cart <?php echo esc_attr( $carticon_css ); ?>">
			<?php
			if ( strlen( $carticon_css ) === 0 && $show_price ) {
				?>
				<a href="#" onclick="addToCart('<?php echo esc_js( $frm_id ); ?>');" class="wbs-btn-add-cart" title="<?php echo esc_attr( $this->wbs_get_translation( 'Addtocart' ) ); ?>"><i class="fas fa-cart-plus"></i></a>
				<?php
			}
			?>
			</form>
			</td>
		</tr>
		<?php
	}

	/**
	 * Get translation or name from settings
	 *
	 * @param string $value string value.
	 */
	public function wbs_get_translation( $value ) {

		$options = get_option( 'wbs_options' );

		if ( strlen( $value ) > 0 ) {
			switch ( $value ) {
				case 'Quantity':
					$option = $options['wbs-placeholder-qty'];
					$value  = ( strlen( $option ) > 0 ) ? $option : __( 'Quantity', 'woo-bulk-shop' );
					break;
				case 'Add':
					$option = $options['wbs-button-add'];
					$value  = ( strlen( $option ) > 0 ) ? $option : __( 'Add', 'woo-bulk-shop' );
					break;
				case 'Search':
					$option = $options['wbs-placeholder-search'];
					$value  = ( strlen( $option ) > 0 ) ? $option : __( 'Search..', 'woo-bulk-shop' );
					break;
				case 'Name':
					$option = $options['wbs-heading-name'];
					$value  = ( strlen( $option ) > 0 ) ? $option : __( 'Name', 'woo-bulk-shop' );
					break;
				case 'Description':
					$option = $options['wbs-heading-description'];
					$value  = ( strlen( $option ) > 0 ) ? $option : __( 'Description', 'woo-bulk-shop' );
					break;
				case 'Price':
					$option = $options['wbs-heading-price'];
					$value  = ( strlen( $option ) > 0 ) ? $option : __( 'Price', 'woo-bulk-shop' );
					break;
				case 'QtyHead':
					$option = $options['wbs-heading-quantity'];
					$value  = ( strlen( $option ) > 0 ) ? $option : __( 'Quantity', 'woo-bulk-shop' );
					break;
				case 'Total':
					$option = $options['wbs-heading-total'];
					$value  = ( strlen( $option ) > 0 ) ? $option : __( 'Total', 'woo-bulk-shop' );
					break;
				case 'Addtocart':
					$option = $options['wbs-button-add-to-cart'];
					$value  = ( strlen( $option ) > 0 ) ? $option : __( 'Add to cart', 'woo-bulk-shop' );
					break;
				case 'Sale':
					$option = $options['wbs-button-sales'];
					$value  = ( strlen( $option ) > 0 ) ? $option : __( 'SALE', 'woo-bulk-shop' );
					break;
				case 'Productimage':
					$option = $options['wbs-heading-product-image'];
					$value  = ( strlen( $option ) > 0 ) ? $option : __( 'Product image', 'woo-bulk-shop' );
					break;
				case 'NYP':
					$option = $options['wbs-nyp-name'];
					$value  = ( strlen( $option ) > 0 ) ? $option : __( 'Name your price', 'woo-bulk-shop' );
					break;
				case 'NYP-min':
					$option = $options['wbs-nyp-minimum'];
					$value  = ( strlen( $option ) > 0 ) ? $option : __( 'Minimum price', 'woo-bulk-shop' );
					break;
				case 's-radio-text':
					$option = $options['wbs-radio-text'];
					$value  = ( strlen( $option ) > 0 ) ? $option : __( 'Text', 'woo-bulk-shop' );
					break;
				case 's-radio-sku':
					$option = $options['wbs-radio-sku'];
					$value  = ( strlen( $option ) > 0 ) ? $option : __( 'SKU', 'woo-bulk-shop' );
					break;
				case 's-radio-tag':
					$option = $options['wbs-radio-text'];
					$value  = ( strlen( $option ) > 0 ) ? $option : __( 'Tag', 'woo-bulk-shop' );
					break;
				case 'login-see-price':
					$option = $options['wbs-login-see-price'];
					$value  = ( strlen( $option ) > 0 ) ? $option : __( 'Login to see price', 'woo-bulk-shop' );
					break;
				default:
					break;
			}
		}

		return $value;
	}

	/**
	 * Check if shop table css is set from settings
	 *
	 */
	public function wbs_get_table_css() {

		$options = get_option( 'wbs_options' );
		$option  = $options['wbs-css-shop-table'];

		if ( 'yes' !== $option ) {
			return 'shop_table shop_table_responsive';
		}

		return '';
	}


	/**
	 * Get css from settings
	 *
	 */
	public function wbs_get_thumbnail_css() {

		$options       = get_option( 'wbs_options' );
		$option        = ( strlen( $options['wbs-thumbnail-size'] ) > 0 ) ? $options['wbs-thumbnail-size']: '50';
		$option_mobile = ( strlen( $options['wbs-thumbnail-mobile-size'] ) > 0 ) ? $options['wbs-thumbnail-mobile-size']: '150';

		if ( '150' !== $option_mobile || '50' !== $option ) {
			?>
			<style>
				.product-thumbnail img{
					max-width: <?php echo esc_attr( $option ); ?>px !important;
				}
				@media screen and (max-width: 768px) {
					.product-thumbnail img {
						max-width: <?php echo esc_attr( $option_mobile ); ?>px !important;
					}
				}
			</style>
			<?php
		}
	}

	/**
	 * Check if we need to hide price for non logged in users
	 *
	 * @param var $atts object.
	 */
	public function wbs_show_price_to_user( $atts ) {

		$hide_price_none_user = $atts['hide_price_non_user'] ? $atts['hide_price_non_user'] : 'false'; 

		if ( 'true' === $hide_price_none_user ) {
			if ( ! is_user_logged_in() ) {
				return false;
			}
		}

		return true;
	}


}


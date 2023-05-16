<?php
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Wpccl_Frontend' ) ) {
	class Wpccl_Frontend {
		protected static $instance = null;

		public static function instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		public function __construct() {
			add_action( 'wp_enqueue_scripts', [ $this, 'scripts' ], 99 );
			add_action( 'woocommerce_cart_coupon', [ $this, 'show_button' ] );
			add_action( 'wp_footer', [ $this, 'display_popup' ] );
			add_filter( 'woocommerce_checkout_coupon_message', [ $this, 'show_button_checkout' ] );
			add_action( 'wp_ajax_wpccl_apply_coupon', [ $this, 'apply_coupon' ] );
			add_action( 'wp_ajax_nopriv_wpccl_apply_coupon', [ $this, 'apply_coupon' ] );
			add_action( 'wp_ajax_wpccl_load_coupons', [ $this, 'load_coupons' ] );
			add_action( 'wp_ajax_nopriv_wpccl_load_coupons', [ $this, 'load_coupons' ] );
			add_shortcode( 'wpccl_button', [ $this, 'button_shortcode' ] );
		}

		public function scripts() {
			// countdown
			if ( Wpccl_Helper::get_setting( 'countdown', 'no' ) === 'yes' ) {
				// moment
				wp_enqueue_script( 'moment', WPCCL_URI . 'assets/libs/moment/moment.js', [ 'jquery' ], WPCCL_VERSION, true );
				wp_enqueue_script( 'moment-timezone', WPCCL_URI . 'assets/libs/moment-timezone/moment-timezone-with-data.js', [ 'jquery' ], WPCCL_VERSION, true );

				// jquery.countdown
				if ( apply_filters( 'wpccl_zero_is_plural', true ) ) {
					wp_enqueue_script( 'jquery.countdown', WPCCL_URI . 'assets/libs/jquery.countdown/jquery.countdown_zp.js', [ 'jquery' ], WPCCL_VERSION, true );
				} else {
					wp_enqueue_script( 'jquery.countdown', WPCCL_URI . 'assets/libs/jquery.countdown/jquery.countdown.js', [ 'jquery' ], WPCCL_VERSION, true );
				}
			}

			// featherlight
			wp_enqueue_style( 'featherlight', WPCCL_URI . 'assets/libs/featherlight/featherlight.css' );
			wp_enqueue_script( 'featherlight', WPCCL_URI . 'assets/libs/featherlight/featherlight.js', [ 'jquery' ], WPCCL_VERSION, true );

			// frontend
			wp_enqueue_style( 'wpccl-frontend', WPCCL_URI . 'assets/css/frontend.css' );
			wp_enqueue_script( 'wpccl-frontend', WPCCL_URI . 'assets/js/frontend.js', [ 'jquery' ], WPCCL_VERSION, true );
			wp_localize_script( 'wpccl-frontend', 'wpccl_vars', [
				'ajax_url'           => admin_url( 'admin-ajax.php' ),
				'apply_coupon_nonce' => wp_create_nonce( 'wpccl-apply-coupon' ),
				'countdown'          => Wpccl_Helper::get_setting( 'countdown', 'no' ),
				'timezone'           => get_option( 'timezone_string' ),
				'active_in'          => Wpccl_Helper::localization( 'active_in', esc_html__( 'Active in %s', 'wpc-coupon-listing' ) ),
				'day'                => Wpccl_Helper::localization( 'day', esc_html__( 'Day', 'wpc-coupon-listing' ) ),
				'days'               => Wpccl_Helper::localization( 'days', esc_html__( 'Days', 'wpc-coupon-listing' ) ),
				'is_checkout'        => is_checkout()
			] );
		}

		public function apply_coupon() {
			check_ajax_referer( 'wpccl-apply-coupon', 'security' );

			if ( isset( $_POST['coupon_code'] ) ) {
				if ( WC()->cart->apply_coupon( sanitize_text_field( $_POST['coupon_code'] ) ) ) {
					echo 'true';
				} else {
					echo 'false';
				}
			}

			wp_die();
		}

		public function get_button() {
			$button = '<div class="wpccl-btn-wrapper">';
			$button .= '<a href="#" class="wpccl-btn" data-featherlight="#wpccl-popup" data-featherlight-before-open="wpccl_load_coupons()" data-featherlight-variant="wpccl-featherlight">';
			$button .= Wpccl_Helper::localization( 'button', esc_html__( 'View Available Coupons', 'wpc-coupon-listing' ) );
			$button .= '</a>';
			$button .= '</div>';

			return apply_filters( 'wpccl_get_button', $button );
		}

		public function button_shortcode() {
			return self::get_button();
		}

		public function show_button() {
			echo do_shortcode( '[wpccl_button]' );
		}

		public function show_button_checkout( $html ) {
			$html = '<div class="wpccl-input-wrapper">' . $html . '</div>';
			$html .= do_shortcode( '[wpccl_button]' );

			return $html;
		}

		public function load_coupons() {
			echo self::render_coupons();
			wp_die();
		}

		public function render_coupons() {
			$coupons = self::get_coupons();

			if ( empty( $coupons ) ) {
				return '<div class="wpccl-empty">' . Wpccl_Helper::localization( 'empty', esc_html__( 'Have no coupons here!', 'wpc-coupon-listing' ) ) . '</div>';
			}

			ob_start();

			foreach ( $coupons as $coupon_id => $coupon_data ) {
				$coupon     = new WC_Coupon( $coupon_id );
				$type       = $coupon->get_discount_type();
				$individual = $coupon->get_individual_use();

				switch ( $type ) {
					case 'percent' :
						$amount = sprintf( Wpccl_Helper::localization( 'discount', esc_html__( '%s Discount', 'wpc-coupon-listing' ) ), $coupon->get_amount() . '%' );

						break;
					case 'fixed_product' :
						$amount = sprintf( Wpccl_Helper::localization( 'product_discount', esc_html__( '%s Product Discount', 'wpc-coupon-listing' ) ), wc_price( $coupon->get_amount() ) );

						break;
					default:
						$amount = sprintf( Wpccl_Helper::localization( 'discount', esc_html__( '%s Discount', 'wpc-coupon-listing' ) ), wc_price( $coupon->get_amount() ) );

						break;
				}

				if ( $coupon->get_free_shipping() ) {
					$amount = Wpccl_Helper::localization( 'free_shipping', esc_html__( 'Free Shipping', 'wpc-coupon-listing' ) );
				}

				$countdown   = empty( $coupon->get_date_expires() ) ? '' : $coupon->get_date_expires()->date( 'Y-m-d' );
				$expiry_date = empty( $coupon->get_date_expires() ) ? '' : $coupon->get_date_expires()->date( get_option( 'date_format' ) );
				$expiry      = ! empty( $expiry_date ) ? sprintf( Wpccl_Helper::localization( 'expires', esc_html__( 'Expires on: %s', 'wpc-coupon-listing' ) ), $expiry_date ) : Wpccl_Helper::localization( 'never_expire', esc_html__( 'Never expire', 'wpc-coupon-listing' ) );

				$classes = 'wpccl-coupon';

				if ( ! empty( $coupon_data['active'] ) ) {
					$classes .= ' wpccl-coupon-applied';
				}

				if ( empty( $coupon_data['enable'] ) ) {
					$classes .= ' wpccl-coupon-disabled';
				}
				?>
				<div class="<?php echo esc_attr( $classes ); ?>" data-coupon="<?php echo esc_attr( $coupon->get_code() ); ?>">
					<div class="wpccl-coupon-info">
						<?php if ( Wpccl_Helper::get_setting( 'value', 'show' ) === 'show' ) {
							echo '<div class="wpccl-coupon-value">' . apply_filters( 'wpccl_coupon_value', esc_html( strip_tags( $amount ) ), $coupon, $coupon_data ) . '</div>';
						} ?>
						<div class="wpccl-coupon-code-wrap">
							<div class="wpccl-coupon-code"><?php echo apply_filters( 'wpccl_coupon_code', esc_html( $coupon->get_code() ), $coupon, $coupon_data ); ?></div>
							<div class="wpccl-coupon-more">
								<?php
								if ( $individual ) {
									echo '<span class="wpccl-coupon-individual">' . Wpccl_Helper::localization( 'individual', esc_html__( 'Individual use only', 'wpc-coupon-listing' ) ) . '</span><br/>';
								}

								if ( Wpccl_Helper::get_setting( 'expiry', 'show' ) === 'show' ) {
									echo '<span class="wpccl-coupon-expiry" data-date="' . esc_attr( $countdown ) . '">' . esc_html( $expiry ) . '</span>';
								}
								?>
							</div>
						</div>
						<?php if ( Wpccl_Helper::get_setting( 'desc', 'show' ) === 'show' ) {
							echo '<div class="wpccl-coupon-desc">' . apply_filters( 'wpccl_coupon_description', $coupon->get_description(), $coupon, $coupon_data ) . '</div>';
						} ?>
					</div>
					<?php
					if ( ( Wpccl_Helper::get_setting( 'message', 'show' ) === 'show' ) && ! empty( $coupon_data['message'] ) ) {
						echo '<div class="wpccl-coupon-message">' . apply_filters( 'wpccl_coupon_message', $coupon_data['message'], $coupon, $coupon_data ) . '</div>';
					}

					if ( ! empty( $coupon_data['active'] ) ) {
						echo '<div class="wpccl-coupon-applied-txt">' . Wpccl_Helper::localization( 'applied', esc_html__( 'Applied', 'wpc-coupon-listing' ) ) . '</div>';
					}
					?>
				</div>
			<?php }

			return ob_get_clean();
		}

		public function display_popup() {
			?>
			<div id="wpccl-popup" class="wpccl-lightbox">
				<div class="wpccl-heading"><?php echo Wpccl_Helper::localization( 'heading', esc_html__( 'Select an available coupon below', 'wpc-coupon-listing' ) ); ?></div>
				<div class="wpccl-coupons"></div>
			</div>
			<?php
		}

		public function get_coupons() {
			$query_args = [
				'posts_per_page' => 500,
				'orderby'        => Wpccl_Helper::get_setting( 'orderby', 'date' ),
				'order'          => Wpccl_Helper::get_setting( 'order', 'DESC' ),
				'no_found_rows'  => true,
				'post_type'      => 'shop_coupon',
				'post_status'    => 'publish',
			];

			if ( Wpccl_Helper::get_setting( 'listing', 'publish' ) === 'public' ) {
				$query_args['meta_query'] = [
					[
						'key'     => 'wpccl_public',
						'value'   => '1',
						'compare' => '==',
					],
				];
			}

			$coupons           = new WP_Query( apply_filters( 'wpccl_query_args', $query_args ) );
			$coupon_ids        = [];
			$available_coupons = [];

			if ( $coupons->have_posts() ) {
				while ( $coupons->have_posts() ) {
					$coupons->the_post();

					$coupon_ids[] = get_the_ID();
				}

				wp_reset_postdata();
			}

			if ( ! empty( $coupon_ids ) ) {
				$current_user  = wp_get_current_user();
				$billing_email = isset( $_POST['billing_email'] ) ? sanitize_email( $_POST['billing_email'] ) : '';
				$check_emails  = array_unique(
					array_filter(
						array_map(
							'strtolower',
							array_map(
								'sanitize_email',
								[
									$billing_email,
									$current_user->user_email,
								]
							)
						)
					)
				);

				$applied_coupons = WC()->cart->get_applied_coupons() ?: [];
				$cart_subtotal   = WC()->cart->get_subtotal();
				$cart_item       = WC()->cart->get_cart();
				$now             = current_time( 'timestamp' );

				$products = [];

				if ( ! empty( $cart_item ) ) {
					foreach ( $cart_item as $item ) {
						$product_id = $item['variation_id'] ?: $item['product_id'];
						$product    = wc_get_product( $product_id );
						$products[] = $product;
					}
				}

				foreach ( $coupon_ids as $coupon_id ) {
					$coupon         = new WC_Coupon( $coupon_id );
					$date_expire    = ! empty( $coupon->get_date_expires() ) ? strtotime( $coupon->get_date_expires( 'edit' )->date( 'Y-m-d' ) ) : '';
					$coupon_enable  = true;
					$coupon_active  = false;
					$coupon_message = '';

					// Skip coupon if it has expired.
					if ( '' !== $date_expire && $now > $date_expire ) {
						continue;
					}

					// Limit to defined email addresses.
					$restrictions = $coupon->get_email_restrictions();

					if ( is_array( $restrictions ) && 0 < count( $restrictions ) && ! WC()->cart->is_coupon_emails_allowed( $check_emails, $restrictions ) ) {
						continue;
					}

					// Skip coupon if products in cart not fit with usage restriction.
					if ( ! empty( $products ) ) {
						$continue = false;

						if ( ! $coupon->is_type( wc_get_product_coupon_types() ) ) {
							if ( $coupon->get_exclude_sale_items() ) {
								foreach ( $products as $product ) {
									if ( $product->is_on_sale() ) {
										$continue = true;
										break;
									}
								}

								if ( $continue ) {
									continue;
								}
							}

							if ( count( $coupon->get_excluded_product_ids() ) > 0 ) {
								foreach ( $products as $product ) {
									if ( in_array( $product->get_id(), $coupon->get_excluded_product_ids(), true ) || in_array( $product->get_parent_id(), $coupon->get_excluded_product_ids(), true ) ) {
										$continue = true;
										break;
									}
								}

								if ( $continue ) {
									continue;
								}
							}

							if ( count( $coupon->get_excluded_product_categories() ) > 0 ) {
								foreach ( $products as $product ) {
									$product_cats = wc_get_product_cat_ids( $product->is_type( 'variation' ) ? $product->get_parent_id() : $product->get_id() );

									if ( ! count( array_intersect( $product_cats, $coupon->get_excluded_product_categories() ) ) ) {
										$continue = true;
										break;
									}
								}

								if ( $continue ) {
									continue;
								}
							}
						} else {
							foreach ( $products as $product ) {
								$continue = $coupon->is_valid_for_product( $product );

								if ( $continue ) {
									break;
								}
							}

							if ( ! $continue ) {
								continue;
							}
						}
					}

					// Skip coupon if it applied in cart.
					if ( in_array( $coupon->get_code(), $applied_coupons ) ) {
						$coupon_active = true;
					}

					$minimum_amount = $coupon->get_minimum_amount();
					$maximum_amount = $coupon->get_maximum_amount();

					// Disable coupon if cart subtotal spent lest than minimum amount required.
					if ( $minimum_amount > 0 && apply_filters( 'woocommerce_coupon_validate_minimum_amount', $minimum_amount > $cart_subtotal, $coupon, $cart_subtotal ) ) {
						$coupon_enable  = false;
						$coupon_message = sprintf( Wpccl_Helper::localization( 'minimum_spend', esc_html__( 'The minimum spend for this coupon is %s.', 'wpc-coupon-listing' ) ), wc_price( $minimum_amount ) );
					}

					// Disable coupon if cart subtotal spent more than maximum amount required.
					if ( $maximum_amount > 0 && apply_filters( 'woocommerce_coupon_validate_maximum_amount', $maximum_amount < $cart_subtotal, $coupon ) ) {
						$coupon_enable  = false;
						$coupon_message = sprintf( Wpccl_Helper::localization( 'maximum_spend', esc_html__( 'The maximum spend for this coupon is %s.', 'wpc-coupon-listing' ) ), wc_price( $maximum_amount ) );
					}

					$available_coupons[ $coupon_id ] = [
						'enable'  => $coupon_enable,
						'active'  => $coupon_active,
						'message' => $coupon_message,
					];
				}
			}

			return apply_filters( 'wpccl_get_coupons', $available_coupons );
		}
	}
}

return Wpccl_Frontend::instance();

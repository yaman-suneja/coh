<?php
/**
 * WooCommerce Square
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0 or later
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@woocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Square to newer
 * versions in the future. If you wish to customize WooCommerce Square for your
 * needs please refer to https://docs.woocommerce.com/document/woocommerce-square/
 */

namespace WooCommerce\Square\Gateway\API\Requests;

defined( 'ABSPATH' ) || exit;

use WooCommerce\Square\API;
use WooCommerce\Square\Framework\Square_Helper;
use WooCommerce\Square\Handlers\Product;
use WooCommerce\Square\Utilities\Money_Utility;

/**
 * The Orders API request class.
 *
 * @since 2.0.0
 */
class Orders extends API\Request {


	/**
	 * Initializes a new Catalog request.
	 *
	 * @since 2.0.0
	 *
	 * @param \Square\SquareClient $api_client the API client
	 */
	public function __construct( $api_client ) {
		$this->square_api = $api_client->getOrdersApi();
	}


	/**
	 * Sets the data for creating an order.
	 *
	 * @since 2.0.0
	 *
	 * @param string $location_id location ID
	 * @param \WC_Order $order order object
	 */
	public function set_create_order_data( $location_id, \WC_Order $order ) {

		$this->square_api_method = 'createOrder';
		$this->square_request    = new \Square\Models\CreateOrderRequest();

		$order_model = new \Square\Models\Order( $location_id );
		$order_model->setReferenceId( $order->get_order_number() );

		if ( ! empty( $order->square_customer_id ) ) {
			$order_model->setCustomerId( $order->square_customer_id );
		}

		$taxes          = $this->get_order_taxes( $order );
		$all_line_items = $this->get_api_line_items(
			$order,
			array_merge( $this->get_product_line_items( $order ), $this->get_fee_line_items( $order ), $this->get_shipping_line_items( $order ) ),
			$taxes
		);

		$square_order_line_items = array_values(
			array_filter(
				$all_line_items,
				function( $line_item ) {
					return $line_item instanceof \Square\Models\OrderLineItem;
				}
			)
		);

		$square_discount_line_items = array_values(
			array_filter(
				$all_line_items,
				function( $line_item ) {
					return $line_item instanceof \Square\Models\OrderLineItemDiscount;
				}
			)
		);

		$order_model->setLineItems( $square_order_line_items );

		if ( ! empty( $square_discount_line_items ) ) {
			$order_model->setDiscounts( $square_discount_line_items );
		}

		$order_model->setTaxes( array_values( $taxes ) );

		$this->square_request->setIdempotencyKey( wc_square()->get_idempotency_key( $order->unique_transaction_ref ) );
		$this->square_request->setOrder( $order_model );

		$this->square_api_args = array( $this->square_request );
	}

	/**
	 * Gets Square line item objects for an order's product items.
	 *
	 * @since 2.0.0
	 *
	 * @param \WC_Order $order order object
	 * @return \WC_Order_Item_Product[]
	 */
	protected function get_product_line_items( \WC_Order $order ) {

		$line_items = array();

		foreach ( $order->get_items() as $item ) {

			if ( ! $item instanceof \WC_Order_Item_Product ) {
				continue;
			}

			$line_items[] = $item;
		}

		return $line_items;
	}


	/**
	 * Gets Square line item objects for an order's fee items.
	 *
	 * @since 2.0.0
	 *
	 * @param \WC_Order $order order object
	 * @return \WC_Order_Item_Fee[]
	 */
	protected function get_fee_line_items( \WC_Order $order ) {

		$line_items = array();

		foreach ( $order->get_fees() as $item ) {

			if ( ! $item instanceof \WC_Order_Item_Fee ) {
				continue;
			}

			$line_items[] = $item;
		}

		return $line_items;
	}


	/**
	 * Gets Square line item objects for an order's shipping items.
	 *
	 * @since 2.0.0
	 *
	 * @param \WC_Order $order order object
	 * @return \WC_Order_Item_Shipping[]
	 */
	protected function get_shipping_line_items( \WC_Order $order ) {

		$line_items = array();

		foreach ( $order->get_shipping_methods() as $item ) {

			if ( ! $item instanceof \WC_Order_Item_Shipping ) {
				continue;
			}

			$line_items[] = $item;
		}

		return $line_items;
	}


	/**
	 * Gets Square API line item objects.
	 *
	 * @since 2.2.6
	 *
	 * @param \WC_Order $order
	 * @param \WC_Order_Item[] $line_items
	 * @param \Square\Models\OrderLineItemTax[] $taxes
	 * @return mixed[]
	 */
	protected function get_api_line_items( \WC_Order $order, $line_items, $taxes ) {

		$api_line_items = array();

		foreach ( $line_items as $item ) {
			$is_product = $item instanceof \WC_Order_Item_Product;
			$line_item  = new \Square\Models\OrderLineItem( $is_product ? (string) $item->get_quantity() : (string) 1 );

			$line_item->setQuantity( $is_product ? (string) $item->get_quantity() : (string) 1 );
			$line_item->setBasePriceMoney( Money_Utility::amount_to_money( $is_product ? $order->get_item_subtotal( $item ) : $item->get_total(), $order->get_currency() ) );

			if ( $is_product && $item->get_meta( Product::SQUARE_VARIATION_ID_META_KEY ) ) {
				$line_item->setCatalogObjectId( $item->get_meta( Product::SQUARE_VARIATION_ID_META_KEY ) );
			} else {
				$line_item->setName( $item->get_name() );
			}

			$applied_taxes = $this->apply_taxes( $taxes, $item );

			$line_item->setAppliedTaxes( $applied_taxes );

			if ( $item instanceof \WC_Order_Item_Product ) {

				$discount = $item->get_subtotal() - $item->get_total();

				$discount_uid = wc_square()->get_idempotency_key( '', false );

				if ( $discount > 0 ) {
					$line_item->setAppliedDiscounts(
						array( new \Square\Models\OrderLineItemAppliedDiscount( $discount_uid ) )
					);

					$order_line_item_discount = new \Square\Models\OrderLineItemDiscount();
					$order_line_item_discount->setUid( $discount_uid );
					$order_line_item_discount->setName( __( 'Discount', 'woocommerce-square' ) );
					$order_line_item_discount->setType( 'FIXED_AMOUNT' );
					$order_line_item_discount->setScope( 'LINE_ITEM' );
					$order_line_item_discount->setAmountMoney(
						Money_Utility::amount_to_money(
							$discount,
							$order->get_currency()
						)
					);

					$api_line_items[] = $order_line_item_discount;
				}
			}

			$api_line_items[] = $line_item;
		}

		return $api_line_items;
	}


	/**
	 * Gets the tax line items for an order.
	 *
	 * @since 2.0.0
	 *
	 * @param \WC_Order $order
	 * @return \Square\Models\OrderLineItemTax[]
	 */
	protected function get_order_taxes( \WC_Order $order ) {

		$taxes = array();

		foreach ( $order->get_taxes() as $tax ) {
			$tax_item = new \Square\Models\OrderLineItemTax();
			$tax_item->setUid( uniqid() );
			$tax_item->setName( $tax->get_name() );
			$tax_item->setType( 'ADDITIVE' );
			$tax_item->setScope( 'LINE_ITEM' );

			$tax_item->setPercentage( Square_Helper::number_format( (float) $tax->get_rate_percent() ) );

			$taxes[ $tax->get_rate_id() ] = $tax_item;
		}

		return $taxes;
	}


	/**
	 * Creates applied taxes array for each Square line item.
	 *
	 * @since 2.0.4
	 *
	 * @param \Square\Models\OrderLineItemTax[] $taxes
	 * @param WC_Order_Item $line_item
	 * @return \Square\Models\OrderLineItemAppliedTax[] $taxes
	 */
	protected function apply_taxes( $taxes, $line_item ) {

		$tax_ids = array();

		foreach ( $line_item->get_taxes()['total'] as $key => $value ) {
			$tax_ids[] = $key;
		}

		$applied_taxes = array();

		foreach ( $tax_ids as $tax_id ) {
			if ( empty( $tax_id ) ) {
				continue;
			}

			$applied_taxes[] = new \Square\Models\OrderLineItemAppliedTax( $taxes[ $tax_id ]->getUid() );
		};

		return empty( $applied_taxes ) ? null : $applied_taxes;
	}


	/**
	 * Sets the data for updating an order with a line item adjustment.
	 *
	 * @since 2.0.4
	 *
	 * @param string $location_id location ID
	 * @param \WC_Order $order order object
	 * @param int $version Current 'version' value of Square order
	 * @param int $amount Amount of line item in smallest unit
	 */
	public function add_line_item_order_data( $location_id, \WC_Order $order, $version, $amount ) {

		$this->square_api_method = 'updateOrder';
		$this->square_request    = new \Square\Models\UpdateOrderRequest();

		$order_model = new \Square\Models\Order( $location_id );
		$order_model->setVersion( $version );

		$line_item = new \Square\Models\OrderLineItem( (string) 1 );
		$line_item->setName( __( 'Adjustment', 'woocommerce-square' ) );
		$line_item->setQuantity( (string) 1 );

		$money_object = new \Square\Models\Money();
		$money_object->setAmount( $amount );
		$money_object->setCurrency( $order->get_currency() );

		$line_item->setBasePriceMoney( $money_object );
		$order_model->setLineItems( array( $line_item ) );

		$this->square_request->setIdempotencyKey( wc_square()->get_idempotency_key( $order->unique_transaction_ref ) . $version );
		$this->square_request->setOrder( $order_model );

		$this->square_api_args = array(
			$order->square_order_id,
			$this->square_request,
		);
	}


	/**
	 * Sets the data for updating an order with a discount adjustment.
	 *
	 * @since 2.0.4
	 *
	 * @param string $location_id location ID
	 * @param \WC_Order $order order object
	 * @param int $version Current 'version' value of Square order
	 * @param int $amount Amount of discount in smallest unit
	 */
	public function add_discount_order_data( $location_id, \WC_Order $order, $version, $amount ) {

		$this->square_api_method = 'updateOrder';
		$this->square_request    = new \Square\Models\UpdateOrderRequest();

		$order_model = new \Square\Models\Order( $location_id );
		$order_model->setVersion( $version );

		$order_line_item_discount = new \Square\Models\OrderLineItemDiscount();
		$order_line_item_discount->setName( __( 'Adjustment', 'woocommerce-square' ) );
		$order_line_item_discount->setType( 'FIXED_AMOUNT' );

		$money_object = new \Square\Models\Money();
		$money_object->setAmount( $amount );
		$money_object->setCurrency( $order->get_currency() );

		$order_line_item_discount->setAmountMoney( $money_object );
		$order_line_item_discount->setScope( 'ORDER' );

		$order_model->setDiscounts( array( $order_line_item_discount ) );

		$this->square_request->setIdempotencyKey( wc_square()->get_idempotency_key( $order->unique_transaction_ref ) . $version );
		$this->square_request->setOrder( $order_model );

		$this->square_api_args = array(
			$order->square_order_id,
			$this->square_request,
		);
	}

}

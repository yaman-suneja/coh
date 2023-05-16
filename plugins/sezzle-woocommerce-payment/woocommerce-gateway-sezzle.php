<?php
/*
Plugin Name: Sezzle WooCommerce Payment
Description: Buy Now Pay Later with Sezzle
Version: 4.0.8
Author: Sezzle
Author URI: https://www.sezzle.com/
Tested up to: 6.0.3
Copyright: © 2022 Sezzle
WC requires at least: 3.0.0
WC tested up to: 7.0.0
Domain Path: /i18n/languages/

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/

if (!function_exists('is_plugin_active_for_network')) {
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))) || is_plugin_active_for_network('woocommerce/woocommerce.php')) {

    function load_plugin_textdomain_files()
    {
        load_plugin_textdomain('woo_sezzlepay', false, dirname(plugin_basename(__FILE__)) . '/i18n/languages/');
    }

    add_action('plugins_loaded', 'load_plugin_textdomain_files');
    add_action('plugins_loaded', 'woocommerce_sezzlepay_init');


    function woocommerce_sezzlepay_init()
    {
        if (!class_exists('WC_Payment_Gateway')) {
            return;
        }

        class WC_Gateway_Sezzlepay extends WC_Payment_Gateway
        {

            public static $log = false;
            private static $_instance = null;

            public $supported_regions;
            public $supported_countries;

            const TRANSACTION_MODE_LIVE = "live";
            const TRANSACTION_MODE_SANDBOX = "sandbox";

            const GATEWAY_URL = "https://%sgateway.%s/%s";
            const SEZZLE_DOMAIN = "%ssezzle.com";

            const WIDGET_URL = "https://widget.%s/%s";

            public function __construct()
            {
                $this->id = 'sezzlepay';
                $this->method_title = __('Sezzle', 'woo_sezzlepay');
                $this->description = __('Buy Now and Pay Later with Sezzle.', 'woo_sezzlepay');
                $this->method_description = $this->description;
                $this->icon = 'https://d34uoa9py2cgca.cloudfront.net/branding/sezzle-logos/png/sezzle-logo-sm-100w.png';
                $this->supports = array('products', 'refunds');
                $this->init_form_fields();
                $this->init_settings();
                $this->title = $this->get_option('title');
                $this->supported_regions = ['US', 'EU'];
                $this->supported_countries = ['US', 'CA', 'DE'];

                add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
                add_action('woocommerce_api_' . strtolower(get_class($this)), array($this, 'sezzle_payment_callback'));
                add_action('admin_notices', function () {
                    $message = get_transient('sezzle_api_error');
                    if (!empty($message)) {
                        echo '<div class="notice error is-dismissible"> <p><strong>' . $message . '</strong></p> </div>';
                    }
                });
            }

            /**
             * Instance of WC_Gateway_Sezzlepay
             *
             * @return WC_Gateway_Sezzlepay|null
             */
            public static function instance()
            {
                if (is_null(self::$_instance)) {
                    self::$_instance = new self();
                }
                return self::$_instance;
            }

            /**
             * Get gateway region
             *
             * @return string
             */
            public function get_gateway_region()
            {
                $region = $this->get_option('gateway-region') ?: $this->default_region();
                return $region === 'US/CA' ? $this->default_region() : $region;
            }

            /**
             * Get default gateway region
             *
             * @return string
             */
            public function default_region()
            {
                return $this->supported_regions[0];
            }

            /**
             * Get Sezzle Domain
             *
             * @param string $gateway_region
             * @return string
             */
            public function get_sezzle_domain($gateway_region = '')
            {
                $region = $gateway_region === $this->default_region() ? '' : "$gateway_region.";
                return sprintf(self::SEZZLE_DOMAIN, strtolower($region));
            }

            /**
             * Get Gateway URL
             *
             * @param string $gateway_mode
             * @param string $api_version
             * @param string $gateway_region
             * @return string
             */
            private function get_gateway_url($gateway_mode, $api_version, $gateway_region = '')
            {
                $sezzle_domain = $this->get_sezzle_domain($gateway_region);
                $env = $gateway_mode === self::TRANSACTION_MODE_SANDBOX ? 'sandbox.' : '';
                return sprintf(self::GATEWAY_URL, $env, $sezzle_domain, $api_version);
            }

            /**
             * Get widget url
             *
             * @param $api_version
             * @return string
             */
            public function get_widget_url($api_version)
            {
                $sezzle_domain = $this->get_sezzle_domain($this->get_gateway_region());
                return sprintf(self::WIDGET_URL, $sezzle_domain, $api_version);
            }

            /**
             * Process Sezzle Settings
             *
             * @return bool|void
             */
            public function process_admin_options()
            {
                if (!$region = $this->obtain_gateway_region()) {
                    $this->log("Unable to validate keys. Gateway region not found");
                    $this->add_error("Unable to validate keys.");
                    $this->display_errors();
                    return;
                }
                $this->update_option('gateway-region', $region);
                $this->log(sprintf("Keys validated. Gateway Region : %s", $region));
                $this->send_admin_configuration();
                return parent::process_admin_options();
            }

            /**
             * Obtain Gateway Region
             *
             * @return string
             */
            private function obtain_gateway_region()
            {
                // stored data
                $stored_public_key = $this->get_option('public-key');
                $stored_private_key = $this->get_option('private-key');
                $stored_transaction_mode = $this->get_option('transaction-mode');

                // input data
                $form_fields = $this->get_form_fields();
                $public_key = $this->get_field_value(
                    'public-key',
                    $form_fields['public-key'],
                    $this->get_post_data()
                );
                $private_key = $this->get_field_value(
                    'private-key',
                    $form_fields['private-key'],
                    $this->get_post_data()
                );
                $transaction_mode = $this->get_field_value(
                    'transaction-mode',
                    $form_fields['transaction-mode'],
                    $this->get_post_data()
                );

                // return stored region if the key elements match
                if (
                    $stored_public_key == $public_key
                    && $stored_private_key == $private_key
                    && $stored_transaction_mode == $transaction_mode
                    && $this->get_option('gateway-region')
                ) {
                    return $this->get_option('gateway-region');
                }
                $body = array(
                    'public_key' => $public_key,
                    'private_key' => $private_key
                );
                $args = array(
                    'headers' => array(
                        'Content-Type' => 'application/json',
                    ),
                    'body' => json_encode($body),
                    'timeout' => 80,
                    'redirection' => 35
                );

                // trying out us and eu gateway to find out exact region
                foreach ($this->supported_regions as $region) {
                    $authTokenURL = $this->get_gateway_url($transaction_mode, 'v1', $region) . '/authentication';
                    $response = wp_remote_post($authTokenURL, $args);
                    $encodeResponseBody = wp_remote_retrieve_body($response);
                    $body = json_decode($encodeResponseBody);
                    if ($body->token) {
                        return $region;
                    }
                }
                return "";
            }


            public function log($message)
            {
                if ($this->get_option('logging') == 'no') {
                    return;
                }
                if (empty(self::$log)) {
                    self::$log = new WC_Logger();
                }
                self::$log->add('Sezzlepay', $message);
            }

            function getAuthTokenUrl()
            {
                return $this->get_base_url() . '/authentication';
            }

            function getOrderIdUrl($reference)
            {
                return $this->get_base_url() . '/orders' . '/' . $reference . '/save_order_id';
            }

            function checkoutRefundUrl($reference)
            {
                return $this->get_base_url() . '/orders' . '/' . $reference . '/refund';
            }

            function checkoutCompleteUrl($reference)
            {
                return $this->get_base_url() . '/checkouts' . '/' . $reference . '/complete';
            }

            function getSubmitCheckoutDetailsAndGetRedirectUrl()
            {
                return $this->get_base_url() . '/checkouts';
            }

            function ordersSubmitUrl()
            {
                return $this->get_base_url() . '/merchant_data' . '/woocommerce/merchant_orders';
            }

            function heartbeatUrl()
            {
                return $this->get_base_url() . '/merchant_data' . '/woocommerce/heartbeat';
            }

            function getOrderUrl($reference)
            {
                return $this->get_base_url() . '/orders/' . $reference;
            }

            function get_v2_configuration_url()
            {
                return $this->get_base_url('v2') . '/configuration';
            }

            function get_base_url($api_version = 'v1')
            {
                $transaction_mode = $this->get_option('transaction-mode');
                $gateway_region = $this->get_gateway_region();
                return $this->get_gateway_url($transaction_mode, $api_version, $gateway_region);
            }

            function init_form_fields()
            {
                $this->form_fields = array(
                    'enabled' => array(
                        'title' => __('Enable/Disable', 'woo_sezzlepay'),
                        'type' => 'checkbox',
                        'label' => __('Enable Sezzle', 'woo_sezzlepay'),
                        'default' => 'yes'
                    ),
                    'payment-option-availability' => array(
                        'title' => __('Payment option availability in other countries', 'woo_sezzlepay'),
                        'type' => 'checkbox',
                        'label' => __('Enable', 'woo_sezzlepay'),
                        'description' => __(
                            'Enable Sezzle gateway in countries other than the US, Canada and Germany.',
                            'woo_sezzlepay'
                        ),
                        'default' => 'yes'
                    ),
                    'title' => array(
                        'title' => __('Title', 'woo_sezzlepay'),
                        'type' => 'text',
                        'description' => __(
                            'This controls the payment method title which the user sees during checkout.',
                            'woo_sezzlepay'
                        ),
                        'default' => __('Sezzle', 'woo_sezzlepay')
                    ),
                    'merchant-id' => array(
                        'title' => __('Merchant ID', 'woo_sezzlepay'),
                        'type' => 'text',
                        'description' => __(
                            'Look for your Sezzle merchant ID in your Sezzle Dashboard.',
                            'woo_sezzlepay'
                        ),
                        'default' => ''
                    ),
                    'private-key' => array(
                        'title' => __('Private Key', 'woo_sezzlepay'),
                        'type' => 'text',
                        'default' => ''
                    ),
                    'public-key' => array(
                        'title' => __('Public Key', 'woo_sezzlepay'),
                        'type' => 'text',
                        'default' => ''
                    ),
                    'min-checkout-amount' => array(
                        'title' => __('Minimum Checkout Amount', 'woo_sezzlepay'),
                        'type' => 'number',
                        'default' => ''
                    ),
                    'transaction-mode' => array(
                        'title' => __('Transaction Mode', 'woo_sezzlepay'),
                        'type' => 'select',
                        'default' => 'live',
                        'desc_tip' => true,
                        'options' => array(
                            self::TRANSACTION_MODE_SANDBOX => __('Sandbox', 'woocommerce'),
                            self::TRANSACTION_MODE_LIVE => __('Live', 'woocommerce'),
                        ),
                    ),
                    'show-product-page-widget' => array(
                        'title' => __('Show Sezzle widget in product pages', 'woo_sezzlepay'),
                        'type' => 'checkbox',
                        'label' => __('Show the sezzle widget under price label in product pages', 'woo_sezzlepay'),
                        'default' => 'yes'
                    ),
                    'enable-installment-widget' => array(
                        'title' => __('Installment Plan Widget Configuration', 'woo_sezzlepay'),
                        'type' => 'checkbox',
                        'label' => __(
                            'Enable Installment Widget Plan in Checkout page',
                            'woo_sezzlepay'
                        ),
                        'default' => 'yes'
                    ),
                    'order-total-container-class-name' => array(
                        'type' => 'text',
                        'description' => __(
                            'Order Total Container Class Name(e.g. ' . $this->get_order_total_container_class_desc() . ')',
                            'woo_sezzlepay'
                        ),
                        'default' => 'woocommerce-Price-amount'
                    ),
                    'order-total-container-parent-class-name' => array(
                        'type' => 'text',
                        'description' => __(
                            'Order Total Container Parent Class Name(e.g. ' . $this->get_order_total_container_parent_class_desc() . ')',
                            'woo_sezzlepay'
                        ),
                        'default' => 'order-total'
                    ),
                    'sync-all-orders' => array(
                        'title' => __('Analytical Data Sync', 'woo_sezzlepay'),
                        'type' => 'checkbox',
                        'label' => __('Sync Orders', 'woo_sezzlepay'),
                        'description' => __('Data includes all orders from your store', 'woo_sezzlepay'),
                        'default' => 'yes'
                    ),
                    'logging' => array(
                        'title' => __('Enable Logging', 'woo_sezzlepay'),
                        'type' => 'checkbox',
                        'label' => __('Enable Logging', 'woo_sezzlepay'),
                        'default' => 'yes',
                    ),
                );
            }

            function get_order_total_container_class_desc()
            {
                return htmlspecialchars('<span class="woocommerce-Price-amount amount"></span>', ENT_QUOTES);
            }

            function get_order_total_container_parent_class_desc()
            {
                return htmlspecialchars('<tr class="order-total"></tr>', ENT_QUOTES);
            }

            function process_payment($order_id)
            {
                global $woocommerce;
                if (function_exists("wc_get_order")) {
                    $order = wc_get_order($order_id);
                } else {
                    $order = new WC_Order($order_id);
                }

                return $this->get_redirect_url($order);
            }

            function get_redirect_url($order)
            {
                $uniqOrderId = uniqid() . "-" . $order->get_id();
                $order->set_transaction_id($uniqOrderId);
                $order->save();
                $complete_url = add_query_arg(
                    array(
                        'key' => $order->get_order_key(),
                    ),
                    WC()->api_request_url(get_class($this))
                );

                $body = array(
                    'amount_in_cents' => (int)((round($order->get_total(), 3) * 1000) / 10),
                    'currency_code' => $order->get_currency(),
                    'order_description' => $uniqOrderId,
                    'order_reference_id' => $uniqOrderId,
                    'display_order_reference_id' => (string)$order->get_id(),
                    'checkout_complete_url' => $complete_url,
                );

                $body['checkout_cancel_url'] = wc_get_checkout_url();

                $body['customer_details'] = array(
                    'first_name' => $order->get_billing_first_name(),
                    'last_name' => $order->get_billing_last_name(),
                    'email' => $order->get_billing_email(),
                    'phone' => $order->get_billing_phone(),
                );

                $body['billing_address'] = array(
                    'street' => $order->get_billing_address_1(),
                    'street2' => $order->get_billing_address_2(),
                    'city' => $order->get_billing_city(),
                    'state' => $order->get_billing_state(),
                    'postal_code' => $order->get_billing_postcode(),
                    'country_code' => $order->get_billing_country(),
                    'phone' => $order->get_billing_phone(),
                );

                $body['shipping_address'] = array(
                    'street' => $order->get_shipping_address_1(),
                    'street2' => $order->get_shipping_address_2(),
                    'city' => $order->get_shipping_city(),
                    'state' => $order->get_shipping_state(),
                    'postal_code' => $order->get_shipping_postcode(),
                    'country_code' => $order->get_shipping_country(),
                );

                $body["items"] = array();
                if (count($order->get_items())) {
                    foreach ($order->get_items() as $item) {
                        if ($item['variation_id']) {
                            if (function_exists("wc_get_product")) {
                                $product = wc_get_product($item['variation_id']);
                            } else {
                                $product = new WC_Product($item['variation_id']);
                            }
                        } else {
                            if (function_exists("wc_get_product")) {
                                $product = wc_get_product($item['product_id']);
                            } else {
                                $product = new WC_Product($item['product_id']);
                            }
                        }
                        $itemData = array(
                            "name" => $item['name'],
                            "sku" => $product->get_sku(),
                            "quantity" => $item['qty'],
                            "price" => array(
                                "amount_in_cents" => (int)((round(($item['line_subtotal'] / $item['qty']), 3) * 1000) / 10),
                                "currency" => $order->get_currency()
                            )
                        );
                        array_push($body["items"], $itemData);
                    }
                }
                $body['merchant_completes'] = true;
                $this->log("Sezzle redirecting");
                $args = array(
                    'headers' => $this->get_headers(),
                    'body' => json_encode($body),
                    'timeout' => 80,
                    'redirection' => 35
                );

                $submitCheckoutDetailsAndGetRedirectUrl = $this->getSubmitCheckoutDetailsAndGetRedirectUrl();
                $response = wp_remote_post($submitCheckoutDetailsAndGetRedirectUrl, $args);
                $encodeResponseBody = wp_remote_retrieve_body($response);
                $this->dump_api_actions($submitCheckoutDetailsAndGetRedirectUrl, $args, $encodeResponseBody);
                $body = json_decode($encodeResponseBody);
                if (isset($body->checkout_url)) {
                    // save url to use later
                    update_post_meta($order->get_id(), 'sezzle_redirect_url', $body->checkout_url);
                    return array(
                        'result' => 'success',
                        'redirect' => $body->checkout_url,
                    );
                }
                $order->add_order_note(__(
                    'Unable to generate the transaction ID. Payment couldn\'t proceed.',
                    'woo_sezzlepay'
                ));
                wc_add_notice(__('Sorry, there was a problem preparing your payment.', 'woo_sezzlepay'), 'error');
                return array(
                    'result' => 'failure',
                    'redirect' => $order->get_checkout_payment_url(true)
                );
            }

            public function dump_api_actions($url, $request = null, $response = null, $status_code = null)
            {
                $this->log($url);
                $this->log("Request Body");
                $this->log(json_encode($request));
                $this->log("Response Body");
                $this->log($response);
                $this->log($status_code);
            }

            function get_sezzle_version()
            {
                // If get_plugins() isn't available, require it
                if (!function_exists('get_plugins')) {
                    require_once(ABSPATH . 'wp-admin/includes/plugin.php');
                }

                $plugin_folder = get_plugins('/' . 'sezzle-woocommerce-payment');
                $plugin_file = 'woocommerce-gateway-sezzle.php';

                return isset($plugin_folder[$plugin_file]['Version']) ? $plugin_folder[$plugin_file]['Version'] : '';
            }

            function get_encoded_platform_details()
            {
                try {
                    $encoded_details = "";
                    global $wp_version, $woocommerce;
                    $platform_details = [
                        'id' => 'WooCommerce',
                        'version' => 'WP ' . $wp_version . ' | WC ' . $woocommerce->version,
                        'plugin_version' => $this->get_sezzle_version()
                    ];

                    $encoded_details = base64_encode(json_encode($platform_details));
                } catch (Exception $exception) {
                    $this->log("Error getting platform details: " . $exception->getMessage());
                }

                return $encoded_details;
            }

            function get_sezzlepay_authorization_code()
            {
                $body = array(
                    'public_key' => $this->get_option('public-key'),
                    'private_key' => $this->get_option('private-key')
                );

                $headers = ['Content-Type' => 'application/json'];
                if ($platform_details = $this->get_encoded_platform_details()) {
                    $headers['Sezzle-Platform'] = $platform_details;
                }

                $args = array(
                    'headers' => $headers,
                    'body' => json_encode($body),
                    'timeout' => 80,
                    'redirection' => 35
                );

                $authTokenURL = $this->getAuthTokenUrl();
                $response = wp_remote_post($authTokenURL, $args);
                $encodeResponseBody = wp_remote_retrieve_body($response);
                $this->dump_api_actions($authTokenURL, $args, $encodeResponseBody);
                $body = json_decode($encodeResponseBody);
                if (!is_object($body) || !isset($body->token)) {
                    set_transient(
                        'sezzle_api_error',
                        'Sezzle: Unable to authenticate. Try resetting your API keys or contact support.',
                        2 * HOUR_IN_SECONDS
                    );
                    throw new Exception('Problem communicating with Sezzle');
                }
                delete_transient('sezzle_api_error');
                return "Bearer $body->token";
            }

            function get_headers()
            {
                return array(
                    'Authorization' => $this->get_sezzlepay_authorization_code(),
                    'Content-Type' => 'application/json',
                );
            }

            function is_payment_captured($reference)
            {
                $url = $this->getOrderUrl($reference);
                $args = array(
                    'headers' => $this->get_headers(),
                    'body' => null,
                    'timeout' => 80,
                    'redirection' => 35
                );

                $response = wp_remote_get($url, $args);
                $encodeResponseBody = wp_remote_retrieve_body($response);
                $this->dump_api_actions($url, $args, $encodeResponseBody);
                $response = json_decode($encodeResponseBody, true);
                if (isset($response["captured_at"]) && $response["captured_at"]) {
                    return true;
                }
                return false;
            }

            function sezzle_payment_callback()
            {
                global $woocommerce;
                $_REQUEST = stripslashes_deep($_REQUEST);
                $order_key = $_REQUEST['key'];
                $order_id = wc_get_order_id_by_order_key($order_key);
                if (function_exists("wc_get_order")) {
                    $order = wc_get_order($order_id);
                } else {
                    $order = new WC_Order($order_id);
                }
                $sezzle_reference_id = $order->get_transaction_id();
                $redirect_url = $this->get_return_url($order);
                if (!$this->is_payment_captured($sezzle_reference_id)) {


                    $args = array(
                        'headers' => $this->get_headers(),
                        'body' => null,
                        'timeout' => 80,
                        'redirection' => 35
                    );

                    $checkoutCompleteURL = $this->checkoutCompleteUrl($sezzle_reference_id);
                    $response = wp_remote_post($checkoutCompleteURL, $args);
                    $encodeResponseBody = wp_remote_retrieve_body($response);
                    $response_code = wp_remote_retrieve_response_code($response);
                    $this->dump_api_actions($checkoutCompleteURL, $args, $encodeResponseBody, $response_code);
                    if ($response_code == 200) {
                        $order->add_order_note(__('Payment approved by Sezzle successfully.', 'woo_sezzlepay'));
                        $order->payment_complete($sezzle_reference_id);
                        WC()->cart->empty_cart();
                        $redirect_url = $this->get_return_url($order);
                    } else {
                        $orderFailed = true;
                        // get the json body string
                        $body_string = wp_remote_retrieve_body($response);

                        // convert it into a json
                        $body = json_decode($body_string);

                        // if it is not a json
                        if (is_null($body)) {
                            // return a generic error
                            $order->add_order_note(__(
                                'The payment failed because of an unknown error. Please contact Sezzle from the Sezzle merchant dashboard.',
                                'woo_sezzlepay'
                            ));
                        } else {
                            // if the body is not valid json
                            if (!isset($body->id)) {
                                // return a generic error
                                $order->add_order_note(__(
                                    "The payment failed because of an unknown error. Please contact Sezzle from the Sezzle merchant dashboard.",
                                    'woo_sezzlepay'
                                ));
                            } else {
                                if (strtolower($body->id) == "checkout_expired") {
                                    // show the message received from sezzle
                                    $order->add_order_note(__(ucfirst("$body->id : $body->message"), 'woo_sezzlepay'));
                                } else {
                                    if (strtolower($body->id) == "checkout_captured") {
                                        $orderFailed = false;
                                    }
                                }
                            }
                        }

                        if ($orderFailed) {
                            $order->update_status('failed');
                        }
                        $redirect_url = wc_get_checkout_url();
                    }
                } else {
                    if (!$order->is_paid()) {
                        $order->payment_complete($sezzle_reference_id);
                        WC()->cart->empty_cart();
                        $redirect_url = $this->get_return_url($order);
                    }
                }
                wp_redirect($redirect_url);
            }

            public function process_refund($order_id, $amount = null, $reason = '')
            {
                if (function_exists("wc_get_order")) {
                    $order = wc_get_order($order_id);
                } else {
                    $order = new WC_Order($order_id);
                }
                $sezzle_reference_id = $order->get_transaction_id();
                $body = array(
                    'amount' => array(
                        'amount_in_cents' => (int)((round($amount, 3) * 1000) / 10),
                        'currency' => $order->get_currency(),
                    ),
                );
                $args = array(
                    'headers' => $this->get_headers(),
                    'body' => json_encode($body),
                    'timeout' => 80,
                    'redirection' => 35
                );
                $checkoutRefundUrl = $this->checkoutRefundUrl($sezzle_reference_id);
                $response = wp_remote_post($checkoutRefundUrl, $args);
                $encodeResponseBody = wp_remote_retrieve_body($response);
                $response_code = wp_remote_retrieve_response_code($response);
                $this->dump_api_actions($checkoutRefundUrl, $args, $encodeResponseBody, $response_code);

                if ($response_code == 200 || $response_code == 201) {
                    $order->add_order_note(sprintf(
                        __('Refund of %s successfully sent to Sezzle.', 'woo_sezzlepay'),
                        $amount
                    ));
                    return true;
                } else {
                    $order->add_order_note(sprintf(__(
                        'There was an error submitting the refund to Sezzle.',
                        'woo_sezzlepay'
                    )));
                    return false;
                }
            }

            function get_last_day_orders()
            {
                $yesterday = date('Y-m-d H:i:s', strtotime('-24 hours'));
                $orders = wc_get_orders(
                    array(
                        'type' => 'shop_order',
                        'status' => array('processing', 'completed'),
                        'limit' => -1,
                        'date_after' => "$yesterday"
                    )
                );
                return $orders;
            }

            function get_order_details_from_order($order)
            {
                $details = array();
                $details["order_number"] = $order->get_order_number();
                $details["payment_method"] = $order->get_payment_method();
                $details["amount"] = (int)((round($order->calculate_totals(), 3) * 1000) / 10);
                $details["currency"] = $order->get_currency();

                // Send the gateway reference too. This may be empty.
                $details["sezzle_reference"] = $order->get_transaction_id();

                // Send customer information
                $details["customer_email"] = $order->get_billing_email();
                $details["customer_phone"] = $order->get_billing_phone();
                $details["billing_address1"] = $order->get_billing_address_1();
                $details["billing_address2"] = $order->get_billing_address_2();
                $details["billing_city"] = $order->get_billing_city();
                $details["billing_state"] = $order->get_billing_state();
                $details["billing_postcode"] = $order->get_billing_postcode();
                $details["billing_country"] = $order->get_billing_country();
                $details["merchant_id"] = $this->get_option('merchant-id');
                return $details;
            }

            function get_order_details_from_orders($orders)
            {
                $orders_details = array();
                foreach ($orders as $order) {
                    $order_details = $this->get_order_details_from_order($order);
                    array_push($orders_details, $order_details);
                }
                return $orders_details;
            }

            function send_merchant_last_day_orders()
            {
                $orders = $this->get_last_day_orders();
                $orders_for_sezzle = $this->get_order_details_from_orders($orders);
                $args = array(
                    'headers' => $this->get_headers(),
                    'body' => json_encode($orders_for_sezzle)
                );
                $ordersSubmitUrl = $this->ordersSubmitUrl();
                $response = wp_remote_post($ordersSubmitUrl, $args);
                $encodeResponseBody = wp_remote_retrieve_body($response);
                $response_code = wp_remote_retrieve_response_code($response);
                $this->dump_api_actions($ordersSubmitUrl, $args, $encodeResponseBody, $response_code);

                if ($response_code == 204) {
                    $this->log("Orders sent to Sezzle, Response Code : $response_code");
                } else {
                    $error = print_r($response);
                    $this->log("Could not send orders to Sezzle, Error Response : $error");
                }
            }

            function get_admin_configuration()
            {
                $form_fields = $this->get_form_fields();
                $sezzle_enabled = ($this->get_field_value(
                    'enabled',
                    $form_fields['enabled'],
                    $this->get_post_data()
                ) == 'yes');
                $merchant_uuid = $this->get_field_value(
                    'merchant-id',
                    $form_fields['merchant-id'],
                    $this->get_post_data()
                );
                $pdp_widget_enabled = ($this->get_field_value(
                    'show-product-page-widget',
                    $form_fields['show-product-page-widget'],
                    $this->get_post_data()
                ) == 'yes');
                $installment_widget_enabled = ($this->get_field_value(
                    'enable-installment-widget',
                    $form_fields['enable-installment-widget'],
                    $this->get_post_data()
                ) == 'yes');
                return array(
                    'sezzle_enabled' => $sezzle_enabled,
                    'merchant_uuid' => $merchant_uuid,
                    'pdp_widget_enabled' => $pdp_widget_enabled,
                    'installment_widget_enabled' =>  $installment_widget_enabled
                );
            }

            function send_admin_configuration()
            {
                try {
                    $admin_configuration = $this->get_admin_configuration();
                    $payload = array(
                        'headers' => $this->get_headers(),
                        'body' => json_encode($admin_configuration)
                    );
                    $configuration_url = $this->get_v2_configuration_url();
                    $response = wp_remote_post($configuration_url, $payload);
                    $encoded_response_body = wp_remote_retrieve_body($response);
                    $response_code = wp_remote_retrieve_response_code($response);
                    $this->dump_api_actions($configuration_url, $payload, $encoded_response_body, $response_code);
                } catch (Exception $exception) {
                    $this->log("Error sending admin config details: " . $exception->getMessage());
                }
            }
        }

        function add_sezzlepay_gateway($methods)
        {
            $methods[] = 'WC_Gateway_Sezzlepay';
            return $methods;
        }

        function remove_sezzlepay_gateway_based_on_billing_country($available_gateways)
        {
            // TODO
            global $woocommerce;
            if (is_admin()) {
                return $available_gateways;
            }
            $gateway = WC_Gateway_Sezzlepay::instance();
            $enableSezzlepayOutsideUSA = $gateway->get_option('payment-option-availability') == 'yes';
            if (!$enableSezzlepayOutsideUSA && $woocommerce->customer) {
                $countryCode = $woocommerce->customer->get_billing_country();
                if (!in_array($countryCode, $gateway->supported_countries, true)) {
                    unset($available_gateways[$gateway->id]);
                }
            }
            return $available_gateways;
        }

        /**
         * Remove Sezzle Pay based on checkout total
         *
         * @return array
         */
        function remove_sezzlepay_gateway_based_on_checkout_total($available_gateways)
        {
            global $woocommerce;
            if (is_admin() || !isset($woocommerce->cart)) {
                return $available_gateways;
            }
            $cart_total = $woocommerce->cart->total;
            $gateway = WC_Gateway_Sezzlepay::instance();
            $min_checkout_amount = $gateway->get_option('min-checkout-amount');
            if ($cart_total && $min_checkout_amount && ($cart_total < $min_checkout_amount)) {
                unset($available_gateways[$gateway->id]);
            }
            return $available_gateways;
        }

        add_filter('woocommerce_payment_gateways', 'add_sezzlepay_gateway');
        add_filter('woocommerce_available_payment_gateways', 'remove_sezzlepay_gateway_based_on_checkout_total');
        add_filter('woocommerce_available_payment_gateways', 'remove_sezzlepay_gateway_based_on_billing_country');
        add_action('woocommerce_single_product_summary', 'add_sezzle_product_banner');

        function add_sezzle_product_banner()
        {
            $gateway = WC_Gateway_Sezzlepay::instance();
            $show_widget = $gateway->get_option('show-product-page-widget');
            $merchant_id = $gateway->get_option('merchant-id');
            if ($show_widget == 'no' || !$merchant_id) {
                return;
            }

            $baseUrl = $gateway->get_widget_url('v1');
            $widget_url = sprintf("$baseUrl/javascript/price-widget?uuid=%s", $merchant_id);
            echo "
                <script type='text/javascript'>
					
                    Sezzle = {}
                    Sezzle.render = function () {
                        document.sezzleConfig = {
                            'configGroups': [
                               {
                                   'targetXPath': document.querySelector('.woocs_price_code') ? '.summary/.price/.woocs_price_code' : '.summary/.price',
                                   'renderToPath': '.',
                                   'ignoredFormattedPriceText':['From:'],
                                   'ignoredPriceElements': ['DEL','SMALL']
                               },
                               {
                                   'targetXPath': '.et_pb_module_inner/.price',
                                   'renderToPath': '.',
                                   'ignoredFormattedPriceText':['From:'],
                                   'ignoredPriceElements': ['DEL','SMALL']
                               },
                               {
                                   'targetXPath': '.elementor-widget-container/.price',
                                   'renderToPath': '.',
                                   'ignoredFormattedPriceText':['From:'],
                                   'ignoredPriceElements': ['DEL','SMALL']
                               },
                               {
                                   'targetXPath': '.order-total/TD-0/STRONG-0/.woocommerce-Price-amount',
                                   'renderToPath': '../../../../..',
                                   'urlMatch': 'cart'
                               }
                            ]
                        }                   
                        var script = document.createElement('script');
                        script.type = 'text/javascript';
                        script.src = '$widget_url';
                        document.head.appendChild(script);
                     
                    };
                    Sezzle.render();
                </script>";
        }

        function sezzle_daily_data_send_event()
        {
            $gateway = WC_Gateway_Sezzlepay::instance();
            $gatewayRegion = $gateway->get_option('gateway-region');
            if (
                $gateway->get_option('sync-all-orders') !== 'yes' ||
                !in_array($gatewayRegion, $gateway->supported_regions)
            ) {
                return;
            }
            $gateway->send_merchant_last_day_orders();
        }

        function add_installment_widget_script()
        {
            $gateway = WC_Gateway_Sezzlepay::instance();
            if (
                $gateway->get_option('enabled') == 'no'
                || $gateway->get_option('enable-installment-widget') == 'no'
            ) {
                return;
            }
            $order_total_container_class_name = $gateway->get_option('order-total-container-class-name');
            $order_total_container_parent_class_name = $gateway->get_option('order-total-container-parent-class-name');
            if (!$order_total_container_class_name || !$order_total_container_parent_class_name) {
                return;
            }

            echo "<script type='text/javascript'>
                new SezzleInstallmentWidget({
                    'merchantLocale': 'US',
                    'platform': 'woocommerce'
                });
                
                // create an observer instance
                jQuery(document.body).on( 'updated_checkout', function(){
                    var sezzlePaymentLine = document.querySelector('.payment_method_sezzlepay');
                    if (document.getElementById('sezzle-installment-widget-box')) {
                        document.getElementById('sezzle-installment-widget-box').remove();
                        document.querySelector('.sezzle-modal-overlay').remove();
                    }
                    if (sezzlePaymentLine) {
                         var sezzleCheckoutWidget = document.createElement('div');
                         sezzleCheckoutWidget.id = 'sezzle-installment-widget-box';
                         sezzleCheckoutWidget.style.display = 'none';
                         sezzlePaymentLine.parentElement.insertBefore(sezzleCheckoutWidget, sezzlePaymentLine.nextElementSibling);
                    }
                    
                    var sezzleInstallmentPlanBox = document.getElementById('sezzle-installment-widget-box');
                    if (sezzleInstallmentPlanBox) {
                        jQuery('input[type=radio][name=\"payment_method\"]').change(function() {
                            if (jQuery(this).val() === 'sezzlepay' && sezzleInstallmentPlanBox) {
                                sezzleInstallmentPlanBox.style.display = 'flex';
                            } else {
                                sezzleInstallmentPlanBox.style.display = 'none';
                            }
                        });
                        if (jQuery('#payment_method_sezzlepay').is(':checked')) {
                            sezzleInstallmentPlanBox.style.display = 'flex';
                        }
                    }
                });
            </script>";
        }

        /**
         * frontend_enqueue_scripts - Add scripts to frontend
         *
         * @return void
         */
        function frontend_enqueue_scripts()
        {
            if (is_checkout()) {
                wp_enqueue_script(
                    'installment_widget_js',
                    'https://checkout-sdk.sezzle.com/installment-widget.min.js'
                );
            }
        }

        add_action('sezzle_daily_data_send_event', 'sezzle_daily_data_send_event');
        add_action('woocommerce_after_checkout_form', 'add_installment_widget_script');
        add_action('wp_enqueue_scripts', 'frontend_enqueue_scripts');
    }
}

// Activation hook - called when plugin is activated
register_activation_hook(__FILE__, 'sezzle_activated');
function sezzle_activated($network_wide)
{
    global $wpdb;

    if (!$network_wide) {
        sezzle_activate_single_site();
        return;
    }

    // Retrieve all site IDs from this network (WordPress >= 4.6 provides easy to use functions for that).
    if (function_exists('get_sites') && function_exists('get_current_network_id')) {
        $site_ids = get_sites(array('fields' => 'ids', 'network_id' => get_current_network_id()));
    } else {
        $site_ids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs WHERE site_id = $wpdb->siteid;");
    }

    // Install the plugin for all these sites.
    foreach ($site_ids as $site_id) {
        switch_to_blog($site_id);
        sezzle_activate_single_site();
        restore_current_blog();
    }
}

// Deactivation hook - called when plugin is deactivated
register_deactivation_hook(__FILE__, 'sezzle_deactivated');
function sezzle_deactivated($network_wide)
{
    global $wpdb;

    if (!$network_wide) {
        sezzle_deactivate_single_site();
        return;
    }

    // Retrieve all site IDs from this network (WordPress >= 4.6 provides easy to use functions for that).
    if (function_exists('get_sites') && function_exists('get_current_network_id')) {
        $site_ids = get_sites(array('fields' => 'ids', 'network_id' => get_current_network_id()));
    } else {
        $site_ids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs WHERE site_id = $wpdb->siteid;");
    }
    // Install the plugin for all these sites.
    foreach ($site_ids as $site_id) {
        switch_to_blog($site_id);
        sezzle_deactivate_single_site();
        restore_current_blog();
    }
}

function sezzle_activate_single_site()
{
    // Schedule cron
    if (!wp_next_scheduled('sezzle_daily_data_send_event_cron')) {
        wp_schedule_event(time(), 'daily', 'sezzle_daily_data_send_event_cron');
    }
}

function sezzle_deactivate_single_site()
{
    wp_clear_scheduled_hook('sezzle_daily_data_send_event_cron');
}

function sezzle_on_activate_blog_from_wp_site($blog)
{
    if (is_object($blog) && isset($blog->blog_id)) {
        sezzle_on_activate_blog((int)$blog->blog_id);
    }
}

function sezzle_on_activate_blog($blog_id)
{
    if (!function_exists('is_plugin_active_for_network')) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }

    if (is_plugin_active_for_network('sezzle-woocommerce-payment/woocommerce-gateway-sezzle.php')) {
        switch_to_blog($blog_id);
        sezzle_activate_single_site();
        restore_current_blog();
    }
}

// Wpmu_new_blog has been deprecated in 5.1 and replaced by wp_insert_site.
global $wp_version;
if (version_compare($wp_version, '5.1', '<')) {
    add_action('wpmu_new_blog', 'sezzle_on_activate_blog');
} else {
    add_action('wp_initialize_site', 'sezzle_on_activate_blog_from_wp_site', 99);
}

add_action('activate_blog', 'sezzle_on_activate_blog');

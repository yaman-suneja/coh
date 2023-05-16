<?php

namespace wobef\classes\repositories;

use wobef\classes\helpers\Formula;
use wobef\classes\helpers\Others;
use wobef\classes\helpers\Pagination;
use wobef\classes\helpers\Render;
use WC_Order_Item_Product;
use wobef\classes\providers\column\OrderColumnProvider;
use wobef\classes\providers\order\OrderProvider;
use wobef\classes\services\order\Duplicate_Order_Service;

class Order
{
    public function get_order($order_id)
    {
        return wc_get_order(intval($order_id));
    }

    public function get_order_as_post($order_id)
    {
        return get_post(intval($order_id));
    }

    public function get_wc_orders($orders_id)
    {
        $settings_repository = new Setting();
        $current_settings = $settings_repository->get_current_settings();
        $sort_by = isset($current_settings['sort_by']) ? $current_settings['sort_by'] : '';
        $sort_type = isset($current_settings['sort_type']) ? $current_settings['sort_type'] : '';
        $statuses = $this->get_order_statuses();
        $args = \wobef\classes\helpers\Setting::get_arg_order_by(esc_sql($sort_by), [
            'limit' => -1,
            'order' => $sort_type,
            'post_status' => array_keys($statuses),
            'post__in' => $orders_id,
        ]);
        return wc_get_orders($args);
    }

    public function get_ids_by_custom_query($join, $where, $type = 'shop_order')
    {
        global $wpdb;
        $type = ($type == 'product') ? "'product', 'product_variation'" : "'" . sanitize_text_field(esc_sql($type)) . "'";
        $where = (!empty($where)) ? "AND ({$where})" : '';
        $query = "SELECT posts.ID, posts.post_parent FROM $wpdb->posts AS posts {$join} WHERE posts.post_type IN ({$type}) {$where}";
        $orders = $wpdb->get_results($query, ARRAY_N);
        $orders = array_unique(Others::array_flatten($orders, 'int'));
        if ($key = array_search(0, $orders) !== false) {
            unset($orders[$key]);
        }
        return implode(',', $orders);
    }

    public function get_orders($args)
    {
        $orders = new \WP_Query($args);
        return $orders;
    }

    public function get_except_columns_for_export()
    {
        return [
            'all_billing',
            'all_shipping',
            'order_details',
            '_billing_address_index',
            '_shipping_address_index',
            'coupon_used',
            'order_items',
        ];
    }

    public function get_orders_list($data, $active_page)
    {
        $column_repository = new Column();
        $search_repository = new Search();
        $search_repository->update_current_data([
            'last_filter_data' => $data
        ]);

        $settings_repository = new Setting();
        $settings = $settings_repository->get_settings();
        $current_settings = $settings_repository->get_current_settings();
        $sort_by = isset($current_settings['sort_by']) ? $current_settings['sort_by'] : '';
        $sort_type = isset($current_settings['sort_type']) ? $current_settings['sort_type'] : '';
        $sticky_first_columns = $current_settings['sticky_first_columns'];
        $args = \wobef\classes\helpers\Setting::get_arg_order_by(esc_sql($sort_by), [
            'order' => esc_sql($sort_type),
            'posts_per_page' => $current_settings['count_per_page'],
            'paged' => $active_page,
            'paginate' => true,
            'post_type' => ['shop_order'],
            'post_status' => 'any',
            'fields' => 'ids',
        ]);

        $orders_args = \wobef\classes\helpers\Order_Helper::set_filter_data_items($data, $args);
        $orders = $this->get_orders($orders_args);
        $items = $orders->posts;
        $item_provider = OrderProvider::get_instance();
        $show_id_column = $column_repository::SHOW_ID_COLUMN;
        $columns_title = $column_repository::get_columns_title();
        $columns = $column_repository->get_active_columns()['fields'];
        $display_full_columns_title = $settings['display_full_columns_title'];
        $after_dynamic_columns = [
            [
                'title' => esc_html__('Actions', 'ithemeland-woocommerce-bulk-orders-editing-lite'),
                'field' => 'action',
            ]
        ];
        $orders_list = Render::html(WOBEF_VIEWS_DIR . 'data_table/items.php', compact('item_provider', 'display_full_columns_title', 'items', 'columns', 'sort_type', 'sort_by', 'show_id_column', 'columns_title', 'sticky_first_columns', 'after_dynamic_columns'));
        if (!empty($orders) && !empty($active_page)) {
            $pagination = Pagination::init($active_page, $orders->max_num_pages);
        }

        $order_counts_by_status = $this->get_order_counts_group_by_status();
        $order_statuses = $this->get_order_statuses();
        $status_filters = Render::html(WOBEF_VIEWS_DIR . "bulk_edit/status_filters.php", compact('order_counts_by_status', 'order_statuses'));

        $result = new \stdClass();
        $result->orders_list = $orders_list;
        $result->products = !empty($data['products']['value']) ? $this->get_selected_products($data['products']['value']) : [];
        $result->categories = !empty($data['categories']['value']) ? $this->get_selected_categories($data['categories']['value']) : [];
        $result->tags = !empty($data['tags']['value']) ? $this->get_selected_tags($data['tags']['value']) : [];
        $result->taxonomies = !empty($data['taxonomies']['value']) ? $this->get_selected_taxonomies($data['taxonomies']['value']) : [];
        $result->pagination = $pagination;
        $result->status_filters = $status_filters;
        $result->count = $orders->found_posts;
        return $result;
    }

    public function get_orders_rows($order_ids)
    {
        if (!is_array($order_ids)) {
            return false;
        }

        $column_repository = new Column();
        $settings_repository = new Setting();
        $settings = $settings_repository->get_settings();
        $sticky_first_columns = $settings['sticky_first_columns'];
        $column_provider = OrderColumnProvider::get_instance();
        $show_id_column = $column_repository::SHOW_ID_COLUMN;
        $columns = $column_repository->get_active_columns()['fields'];

        $order_rows = [];
        $includes = [];
        $order_statuses = [];

        if (!empty($order_ids)) {
            foreach ($order_ids as $order_id) {
                $item = $this->get_order(intval($order_id));
                $item_columns = $column_provider->get_item_columns($item, $columns);
                $order_statuses[intval($order_id)] = $item->get_status();
                if (is_array($item_columns) && isset($item_columns['items'])) {
                    $order_rows[intval($order_id)] = $item_columns['items'];
                    $includes[] = $item_columns['includes'];
                } else {
                    $order_rows[intval($order_id)] = $item_columns;
                }
            }
        }

        $result = new \stdClass();
        $result->order_rows = $order_rows;
        $result->order_statuses = $order_statuses;
        $result->includes = $includes;
        $result->status_filters = $this->get_status_filters();
        return $result;
    }

    private function get_status_filters()
    {
        $order_counts_by_status = $this->get_order_counts_group_by_status();
        $order_statuses = $this->get_order_statuses();
        return Render::html(WOBEF_VIEWS_DIR . "bulk_edit/status_filters.php", compact('order_counts_by_status', 'order_statuses'));
    }

    private function get_selected_products($ids)
    {
        $selected_products = [];
        if (!empty($ids) && is_array($ids)) {
            $product_ids = [];
            foreach ($ids as $product_item) {
                $exploded = explode('__', $product_item);
                if (is_array($exploded) && !empty($exploded[1])) {
                    $product_ids[] = intval($exploded[1]);
                }
            }
            if (!empty($product_ids)) {
                $product_repository = new Product();
                $products = $product_repository->get_products([
                    'post__in' => $product_ids,
                    'post_type' => ['product', 'product_variation'],
                    'post_status' => 'any',
                    'posts_per_page' => -1
                ]);

                if (!empty($products->posts)) {
                    foreach ($products->posts as $product) {
                        if ($product instanceof \WP_Post) {
                            $selected_products[$product->post_parent . '__' . $product->ID] = $product->post_title;
                        }
                    }
                }
            }
        }
        return $selected_products;
    }

    private function get_selected_categories($ids)
    {
        $selected_categories = [];
        if (!empty($ids) && is_array($ids)) {
            $product_repository = new Product();
            $selected_categories = $product_repository->get_categories_by_id($ids);
        }

        return $selected_categories;
    }

    private function get_selected_tags($ids)
    {
        $selected_tags = [];
        if (!empty($ids) && is_array($ids)) {
            $product_repository = new Product();
            $selected_tags = $product_repository->get_tags_by_id($ids);
        }

        return $selected_tags;
    }

    private function get_selected_taxonomies($ids)
    {
        $selected_taxonomies = [];
        if (!empty($ids) && is_array($ids)) {
            $taxonomy_ids = [];
            foreach ($ids as $taxonomy_item) {
                $exploded = explode('__', $taxonomy_item);
                if (is_array($exploded) && !empty($exploded[1])) {
                    $taxonomy_ids[] = intval($exploded[1]);
                }
            }
            if (!empty($taxonomy_ids)) {
                $product_repository = new Product();
                $selected_taxonomies = $product_repository->get_taxonomies_by_id($taxonomy_ids);
            }
        }

        return $selected_taxonomies;
    }

    private function set_value_with_operator($old_value, $data)
    {
        if (!empty($data['operator'])) {
            $new_val = (isset($data['round_item']) && !empty($data['round_item'])) ? \wobef\classes\helpers\Order_Helper::round($data['value'], $data['round_item']) : $data['value'];
            switch ($data['operator']) {
                case 'text_append':
                    $value = $old_value . $data['value'];
                    break;
                case 'text_prepend':
                    $value = $data['value'] . $old_value;
                    break;
                case 'text_new':
                    $value = $data['value'];
                    break;
                case 'text_delete':
                    $value = str_replace($data['value'], '', $old_value);
                    break;
                case 'text_replace':
                    if (!empty($data['value'])) {
                        $value = ($data['sensitive'] == 'yes') ? str_replace($data['value'], $data['replace'], $old_value) : str_ireplace($data['value'], $data['replace'], $old_value);
                    } else {
                        $value = $old_value;
                    }
                    break;
                case 'text_remove_duplicate':
                    $value = $old_value;
                    break;
                case 'taxonomy_append':
                    $value = array_unique(array_merge($old_value, $data['value']));
                    break;
                case 'taxonomy_replace':
                    $value = $data['value'];
                    break;
                case 'taxonomy_delete':
                    $value = array_values(array_diff($old_value, $data['value']));
                    break;
                case 'number_new':
                    $value = $new_val;
                    break;
                case 'number_delete':
                    $value = str_replace($data['value'], '', $old_value);
                    break;
                case 'number_clear':
                    $value = '';
                    break;
                case 'number_formula':
                    $formula = new Formula();
                    $value = $formula->calculate(str_replace('X', $old_value, $data['value']));
                    break;
                case 'increase_by_value':
                    $value = floatval($old_value) + floatval($new_val);
                    break;
                case 'decrease_by_value':
                    $value = floatval($old_value) - floatval($new_val);
                    break;
                case 'increase_by_percent':
                    $value = floatval($old_value) + floatval(floatval($old_value) * floatval($new_val) / 100);
                    break;
                case 'decrease_by_percent':
                    $value = floatval($old_value) - floatval(floatval($old_value) * floatval($new_val) / 100);
                    break;
                case 'increase_by_value_from_sale':
                    $value = (isset($data['sale_price'])) ? floatval($data['sale_price']) + floatval($new_val) : $data;
                    break;
                case 'increase_by_percent_from_sale':
                    $value = (isset($data['sale_price'])) ? floatval($data['sale_price']) + floatval(floatval($data['sale_price']) * floatval($new_val) / 100) : $data;
                    break;
                case 'decrease_by_value_from_regular':
                    $value = (isset($data['regular_price'])) ? floatval($data['regular_price']) - floatval($data['value']) : $data;
                    break;
                case 'decrease_by_percent_from_regular':
                    $value = (isset($data['regular_price'])) ? floatval($data['regular_price']) - (floatval($data['regular_price']) * floatval($new_val) / 100) : $data;
                    break;
            }
        } else {
            $value = $data['value'];
        }
        return $value;
    }

    public function get_order_statuses()
    {
        $statuses = wc_get_order_statuses();
        $statuses['trash'] = esc_html__('Trash', 'ithemeland-woocommerce-bulk-orders-editing-lite');

        return $statuses;
    }

    public function get_shipping_countries()
    {
        $countries = new \WC_Countries();
        return $countries->get_shipping_countries();
    }

    public function get_country_name($country_key)
    {
        return (function_exists('WC') && !empty(WC()->countries->countries[$country_key])) ? WC()->countries->countries[$country_key] : '';
    }

    public function get_shipping_states()
    {
        $countries = new \WC_Countries();
        return $countries->get_states();
    }

    public function get_currencies()
    {
        $currencies = get_woocommerce_currencies();
        $symbols = get_woocommerce_currency_symbols();
        if (!empty($currencies) && !empty($symbols)) {
            foreach ($currencies as $currency_key => $currency_label) {
                if (isset($symbols[$currency_key])) {
                    $currencies[$currency_key] = $currency_label . ' (' . $symbols[$currency_key] . ')';
                }
            }
        }

        return $currencies;
    }

    public function get_customers()
    {
        $users = get_users([
            'fields' => ['ID', 'user_login']
        ]);
        $customers = [];
        if (!empty($users)) {
            foreach ($users as $user_item) {
                $customers[$user_item->ID] = $user_item->user_login;
            }
        }
        return $customers;
    }

    public function get_payment_methods()
    {
        $gateways = WC()->payment_gateways->get_available_payment_gateways();
        $payment_methods = [];
        if (!empty($gateways) && is_array($gateways)) {
            foreach ($gateways as $gateway) {
                if (!empty($gateway->id)) {
                    $payment_methods[$gateway->id] = $gateway->title;
                }
            }
        }

        return $payment_methods;
    }

    private function field_update($order_id, $data)
    {
        $field_type = esc_sql($data['field_type']);
        $field = esc_sql($data['field']);
        $value = $data['value'];
        $order_setter_methods = $this->get_order_setter_methods();


        $order = $this->get_order(intval(esc_sql($order_id)));
        if (!$order instanceof \WC_Order) {
            return false;
        }
        $order_array = $this->order_to_array($order);
        switch ($field_type) {
            case 'custom_field':
                $this->custom_field_update($order->get_id(), $data);
                break;
            case 'main_field':
                if ($field == 'order_notes') {
                    $order->add_order_note($value, $data['operator'] == 'customer' ? 1 : 0, true);
                } else {
                    $method = isset($order_setter_methods[$field]) ? $order_setter_methods[$field] : '';
                    if (isset($order_array[$field])) {
                        $value = (!empty($data['operator'])) ? $this->set_value_with_operator($order_array[$field], $data) : $value;
                    }
                    if (in_array($field, ['_paid_date'])) {
                        $value = new \WC_DateTime($value);
                    }
                    if (!empty($method) && method_exists($order, $method)) {
                        $order->{$method}($value);
                    } else {
                        update_post_meta($order->get_id(), $field, $value);
                    }
                }

                break;
        }
        $order->save();
        wp_update_post(['ID' => $order->get_id()]);
        return true;
    }

    public function update($order_ids, $data)
    {
        if (empty($order_ids)) {
            return false;
        }
        if (!empty($data)) {
            foreach ($order_ids as $order_id) {
                $result = $this->field_update($order_id, $data);
                if (!$result) {
                    return false;
                }
            }
        }

        return true;
    }

    private function custom_field_update($order_id, $data)
    {
        $old_value = get_post_meta(intval($order_id), $data['field']);
        $old_value = isset($old_value[0]) ? $old_value[0] : '';
        $value = $this->set_value_with_operator($old_value, $data, $data['operator']);
        return update_post_meta(intval($order_id), esc_sql($data['field']), esc_sql($value));
    }

    public function get_taxonomies()
    {
        $taxonomies_value = [];
        $taxonomies = get_taxonomies([], 'objects');
        foreach ($taxonomies as $taxonomy) {
            if (taxonomy_exists($taxonomy->name)) {
                $taxonomies_value[$taxonomy->name] = [
                    'label' => (strpos($taxonomy->name, 'pa_') !== false) ? wc_attribute_label($taxonomy->name) : $taxonomy->label,
                    'terms' => get_terms([
                        'taxonomy' => $taxonomy->name,
                        'hide_empty' => false,
                    ]),
                ];
            }
        }
        return $taxonomies_value;
    }

    public function order_to_array($order_object)
    {
        if (!($order_object instanceof \WC_Order)) {
            return false;
        }

        $post_meta = get_post_meta($order_object->get_id());
        $customer_name = $order_object->get_user();
        $customer_agent_name = $order_object->get_customer_user_agent();
        $order_items_string = "";
        $order_items_array = [];

        $order_items_object = $order_object->get_items();
        $items_no = 0;
        if (!empty($order_items_object)) {
            foreach ($order_items_object as $order_item) {
                if ($order_item instanceof \WC_Order_Item_Product) {
                    $items_no++;
                    if ($order_item->get_product_id() != 0) {
                        $product_id = $order_item->get_product_id();
                        $product_name = $order_item->get_product()->get_name();
                        $quantity = $order_item->get_quantity();
                        $order_items_array[] = [
                            'product_link' => esc_url(admin_url("post.php?post={$product_id}&action=edit")),
                            'product_name' => $product_name,
                            'quantity' => (is_numeric($quantity)) ? number_format($quantity) : $quantity,
                            'tax' => (is_numeric($order_item->get_total_tax())) ? number_format($order_item->get_total_tax()) : $order_item->get_total_tax(),
                            'total' => (is_numeric($order_item->get_total())) ? number_format($order_item->get_total()) : $order_item->get_total(),
                            'currency' => (function_exists('get_woocommerce_currency_symbol')) ? get_woocommerce_currency_symbol($order_object->get_currency()) : '',
                        ];
                        $order_items_string .= '<strong>' . $product_name . '</strong> x ' . $quantity . "<br>";
                    }
                }
            }
        }
        $order_status = $order_object->get_status();
        $order_status = ($order_status != 'trash') ? 'wc-' . $order_status : $order_status;
        return [
            'id' => $order_object->get_id(),
            'date_created' => (!empty($order_object->get_date_created()) && !empty($order_object->get_date_created()->date('Y/m/d H:i'))) ? $order_object->get_date_created()->format('Y/m/d H:i') : '',
            'date_modified' => (!empty($order_object->get_date_modified()) && !empty($order_object->get_date_modified()->date('Y/m/d H:i'))) ? $order_object->get_date_modified()->format('Y/m/d H:i') : '',
            'customer_note' => $order_object->get_customer_note(),
            'order_status' => $order_status,
            'view_url' => $order_object->get_view_order_url(),
            'order_items' => $order_items_string,
            'order_items_no' => $items_no,
            'order_items_array' => $order_items_array,
            'coupon_used' => (!empty($order_object->get_coupons())) ? 'Yes' : 'No',
            '_order_stock_reduced' => (!empty($post_meta['_order_stock_reduced'][0]) && $post_meta['_order_stock_reduced'][0] == 'yes') ? 'yes' : 'no',
            '_recorded_sales' => (!empty($post_meta['_recorded_sales'][0]) && $post_meta['_recorded_sales'][0] == 'yes') ? 'yes' : 'no',
            'customer_ip_address' => $order_object->get_customer_ip_address(),
            'customer_user' => ($customer_name instanceof \WP_User) ? $customer_name->user_nicename : '',
            'customer_user_id' => $order_object->get_customer_id(),
            'customer_user_agent' => ($customer_agent_name instanceof \WP_User) ? $customer_agent_name->user_nicename : '',
            'date_paid' => $order_object->get_date_paid(),
            'date_completed' => $order_object->get_date_completed(),
            'order_total' => $order_object->get_total(),
            'order_sub_total' => $order_object->get_subtotal(),
            'order_discount' => $order_object->get_total_discount(),
            'order_discount_tax' => $order_object->get_discount_tax(),
            'created_via' => $order_object->get_created_via(),
            'order_currency' => $order_object->get_currency(),
            'shipping_method' => $order_object->get_shipping_method(),
            'payment_method' => $order_object->get_payment_method(),
            'payment_method_title' => $order_object->get_payment_method_title(),
            'order_version' => $order_object->get_version(),
            'prices_include_tax' => $order_object->get_prices_include_tax(),
            '_order_tax' => (isset($post_meta['_order_tax'][0])) ? $post_meta['_order_tax'][0] : '',
            'order_shipping' => $order_object->get_shipping_total(),
            'order_shipping_tax' => $order_object->get_shipping_tax(),
            'billing_address_1' => $order_object->get_billing_address_1(),
            'billing_address_2' => $order_object->get_billing_address_2(),
            'billing_city' => $order_object->get_billing_city(),
            'billing_company' => $order_object->get_billing_company(),
            'billing_country' => $order_object->get_billing_country(),
            'billing_email' => $order_object->get_billing_email(),
            'billing_phone' => $order_object->get_billing_phone(),
            'billing_first_name' => $order_object->get_billing_first_name(),
            'billing_last_name' => $order_object->get_billing_last_name(),
            'billing_address_index' => $order_object->get_formatted_billing_address(),
            'billing_postcode' => $order_object->get_billing_postcode(),
            'billing_state' => $order_object->get_billing_state(),
            'transaction_id' => $order_object->get_transaction_id(),
            'shipping_address_1' => $order_object->get_shipping_address_1(),
            'shipping_address_2' => $order_object->get_shipping_address_2(),
            'shipping_address_index' => $order_object->get_formatted_shipping_address(),
            'shipping_city' => $order_object->get_shipping_city(),
            'shipping_company' => $order_object->get_shipping_company(),
            'shipping_country' => $order_object->get_shipping_country(),
            'shipping_first_name' => $order_object->get_shipping_first_name(),
            'shipping_last_name' => $order_object->get_shipping_last_name(),
            'shipping_postcode' => $order_object->get_shipping_postcode(),
            'shipping_state' => $order_object->get_shipping_state(),
            'custom_field' => $post_meta,
        ];
    }

    public function order_to_array_for_export($order_object)
    {
        if (!($order_object instanceof \WC_Order)) {
            return false;
        }

        $post_meta = get_post_meta($order_object->get_id());
        $customer_agent_name = $order_object->get_customer_user_agent();
        $notes = wc_get_order_notes([
            'order_id' => $order_object->get_id()
        ]);
        $order_notes = !empty($notes) && is_array($notes) ? json_encode($notes) : ' ';

        $order_items = [];
        foreach ($order_object->get_items() as $item_id => $order_item) {
            if ($order_item instanceof \WC_Order_Item_Product) {
                $order_items[] = [
                    'product_name' => $order_item->get_name(),
                    'product_id' => $order_item->get_product_id(),
                    'variation_id' => $order_item->get_variation_id(),
                    'quantity' => $order_item->get_quantity(),
                    'total' => $order_item->get_total(),
                    'total_tax' => $order_item->get_total_tax(),
                    'subtotal' => $order_item->get_subtotal(),
                    'subtotal_tax' => $order_item->get_subtotal_tax(),
                ];
            }
        }

        $order_status = $order_object->get_status();
        $order_status = ($order_status != 'trash') ? 'wc-' . $order_status : $order_status;

        return [
            'id' => $order_object->get_id(),
            'date_created' => $order_object->get_date_created(),
            'customer_note' => $order_object->get_customer_note(),
            'order_status' => $order_status,
            'line_items' => json_encode($order_items),
            '_order_stock_reduced' => (!empty($post_meta['_order_stock_reduced'][0]) && $post_meta['_order_stock_reduced'][0] == 'yes') ? 'yes' : 'no',
            '_recorded_sales' => (!empty($post_meta['_recorded_sales'][0]) && $post_meta['_recorded_sales'][0] == 'yes') ? 'yes' : 'no',
            'customer_ip_address' => $order_object->get_customer_ip_address(),
            'customer_user' => $order_object->get_customer_id(),
            'customer_user_agent' => ($customer_agent_name instanceof \WP_User) ? $customer_agent_name->user_nicename : '',
            'date_paid' => $order_object->get_date_paid(),
            'date_completed' => $order_object->get_date_completed(),
            'order_total' => $order_object->get_total(),
            'order_sub_total' => $order_object->get_subtotal(),
            'order_discount' => $order_object->get_total_discount(),
            'order_discount_tax' => $order_object->get_discount_tax(),
            'created_via' => $order_object->get_created_via(),
            'order_currency' => $order_object->get_currency(),
            'shipping_method' => $order_object->get_shipping_method(),
            'payment_method' => $order_object->get_payment_method(),
            'payment_method_title' => $order_object->get_payment_method_title(),
            'order_version' => $order_object->get_version(),
            'prices_include_tax' => $order_object->get_prices_include_tax(),
            '_order_tax' => (isset($post_meta['_order_tax'][0])) ? $post_meta['_order_tax'][0] : '',
            'order_shipping' => $order_object->get_shipping_total(),
            'order_shipping_tax' => $order_object->get_shipping_tax(),
            'billing_address_1' => $order_object->get_billing_address_1(),
            'billing_address_2' => $order_object->get_billing_address_2(),
            'billing_city' => $order_object->get_billing_city(),
            'billing_company' => $order_object->get_billing_company(),
            'billing_country' => $order_object->get_billing_country(),
            'billing_email' => $order_object->get_billing_email(),
            'billing_phone' => $order_object->get_billing_phone(),
            'billing_first_name' => $order_object->get_billing_first_name(),
            'billing_last_name' => $order_object->get_billing_last_name(),
            'billing_postcode' => $order_object->get_billing_postcode(),
            'billing_state' => $order_object->get_billing_state(),
            'transaction_id' => $order_object->get_transaction_id(),
            'shipping_address_1' => $order_object->get_shipping_address_1(),
            'shipping_address_2' => $order_object->get_shipping_address_2(),
            'shipping_city' => $order_object->get_shipping_city(),
            'shipping_company' => $order_object->get_shipping_company(),
            'shipping_country' => $order_object->get_shipping_country(),
            'shipping_first_name' => $order_object->get_shipping_first_name(),
            'shipping_last_name' => $order_object->get_shipping_last_name(),
            'shipping_postcode' => $order_object->get_shipping_postcode(),
            'shipping_state' => $order_object->get_shipping_state(),
            'order_notes' => $order_notes,
        ];
    }

    public function create($data = [])
    {
        $order = new \WC_Order();
        $order->set_status('wc-pending');
        return $order->save();
    }

    public function duplicate($order_ids, $number = 1)
    {
        $duplicateService = new Duplicate_Order_Service();
        return $duplicateService->duplicate($order_ids, $number);
    }

    public function import_from_csv($csv_path)
    {
        if (!file_exists($csv_path)) {
            return false;
        }
        $order_setter_methods = $this->get_order_setter_methods();

        if (($handle = fopen($csv_path, "r")) !== false) {
            $columns = fgetcsv($handle);
            if (empty($columns)) {
                return false;
            }

            while (($data = fgetcsv($handle)) !== false) {
                $num = count($data);
                $new_order = new \WC_Order();
                for ($c = 0; $c < $num; $c++) {
                    switch ($columns[$c]) {
                        case 'order_notes':
                            $order_notes_index = $c;
                            break;
                        case 'line_items':
                            $items = json_decode($data[$c], true);
                            if (!empty($items) && is_array($items)) {
                                foreach ($items as $item) {
                                    if (is_array($item)) {
                                        $product = wc_get_product(intval($item['product_id']));
                                        if ($product instanceof \WC_Product) {
                                            $order_item = new WC_Order_Item_Product();
                                            $order_item->set_product($product);
                                            $order_item->set_product_id($product->get_id());
                                            $order_item->set_variation_id($item['variation_id']);
                                            $order_item->set_quantity($item['quantity']);
                                            $order_item->set_total($item['total']);
                                            $order_item->set_total_tax($item['total_tax']);
                                            $order_item->set_subtotal($item['subtotal']);
                                            $order_item->set_subtotal_tax($item['subtotal_tax']);
                                            $new_order->add_item($order_item);
                                        }
                                    }
                                }
                            }
                            break;
                        default:
                            $method = isset($order_setter_methods[$columns[$c]]) ? $order_setter_methods[$columns[$c]] : '';
                            $value = $data[$c];
                            if (!empty($method) && method_exists($new_order, $method)) {
                                $new_order->{$method}($value);
                            } else {
                                update_post_meta($new_order->get_id(), $columns[$c], $value);
                            }
                            break;
                    }
                }
                $new_order->save();
                if (isset($order_notes_index)) {
                    $notes = json_decode($data[$order_notes_index], true);
                    if (!empty($notes) && is_array($notes)) {
                        foreach (array_reverse($notes) as $note) {
                            if (is_array($note) && isset($note['content'])) {
                                $comment_id = $new_order->add_order_note($note['content'], ($note['customer_note']) ? 1 : 0, ($note['added_by'] != 'system'));
                                if (!empty($note['date_created']['date'])) {
                                    wp_update_comment([
                                        'comment_ID' => intval($comment_id),
                                        'comment_date' => $note['date_created']['date'],
                                        'comment_date_gmt' => $note['date_created']['date']
                                    ]);
                                }
                            }
                        }
                        $new_order->save();
                    }
                }
            }
            fclose($handle);
            return true;
        }
        return false;
    }

    public function get_order_setter_methods()
    {
        return [
            'customer_note' => 'set_customer_note',
            'post_date' => 'set_date_created',
            'post_status' => 'set_status',
            '_customer_ip_address' => 'set_customer_ip_address',
            '_customer_user' => 'set_customer_id',
            '_customer_user_agent' => 'set_customer_user_agent',
            '_completed_date' => 'set_date_completed',
            '_paid_date' => 'set_date_paid',
            '_order_total' => 'set_total',
            '_cart_discount' => 'set_discount_total',
            '_cart_discount_tax' => 'set_discount_tax',
            '_created_via' => 'set_created_via',
            '_order_currency' => 'set_currency',
            '_payment_method' => 'set_payment_method',
            '_payment_method_title' => 'set_payment_method_title',
            '_order_version' => 'set_version',
            '_prices_include_tax' => 'set_prices_include_tax',
            '_order_shipping' => 'set_shipping_total',
            '_shipping_tax' => 'set_shipping_tax',
            '_billing_address_1' => 'set_billing_address_1',
            '_billing_address_2' => 'set_billing_address_2',
            '_billing_city' => 'set_billing_city',
            '_billing_company' => 'set_billing_company',
            '_billing_country' => 'set_billing_country',
            '_billing_email' => 'set_billing_email',
            '_billing_phone' => 'set_billing_phone',
            '_billing_first_name' => 'set_billing_first_name',
            '_billing_last_name' => 'set_billing_last_name',
            '_billing_postcode' => 'set_billing_postcode',
            '_billing_state' => 'set_billing_state',
            '_shipping_address_1' => 'set_shipping_address_1',
            '_shipping_address_2' => 'set_shipping_address_2',
            '_shipping_city' => 'set_shipping_city',
            '_shipping_company' => 'set_shipping_company',
            '_shipping_country' => 'set_shipping_country',
            '_shipping_first_name' => 'set_shipping_first_name',
            '_shipping_last_name' => 'set_shipping_last_name',
            '_shipping_postcode' => 'set_shipping_postcode',
            '_shipping_state' => 'set_shipping_state',
            '_transaction_id' => 'set_transaction_id',
        ];
    }

    public function get_order_counts_group_by_status()
    {
        global $wpdb;
        $output = [];
        $all = 0;
        $result = $wpdb->get_results("SELECT post_status AS 'status',COUNT(*) AS 'count' FROM {$wpdb->posts} WHERE post_type = 'shop_order' AND post_status NOT IN ('auto-draft') GROUP BY post_status", ARRAY_A);
        if (!empty($result) && is_array($result)) {
            foreach ($result as $item) {
                if (isset($item['status']) && isset($item['count'])) {
                    if ($item['status'] !== 'trash') {
                        $all += $item['count'];
                    }
                    $output[$item['status']] = $item['count'];
                }
            }
        }
        $output['all'] = intval($all);
        return $output;
    }

    public function get_status_color($status)
    {
        $status_colors = $this->get_status_colors();
        return (isset($status_colors[$status])) ? $status_colors[$status] : null;
    }

    private function get_status_colors()
    {
        return [
            'wc-pending' => '#a3b7a3',
            'wc-processing' => '#80e045',
            'wc-on-hold' => '#f9c662',
            'wc-completed' => '#6ca9d6',
            'wc-cancelled' => '#bf8e8e',
            'wc-refunded' => '#ea4848',
            'wc-failed' => '#d07a7a',
            'trash' => '#808080',
        ];
    }
}

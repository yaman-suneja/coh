<?php

namespace wobef\classes\helpers;

class Order_Helper
{
    public static function round($value, $round_item)
    {
        $division = intval('1' . str_repeat('0', wc_get_price_decimals()));
        switch ($round_item) {
            case 5:
            case 10:
                $value += floatval($round_item / $division);
                $decimals = floatval($value - floor($value));
                $value = floor($value) + ($decimals - floatval(intval(($decimals * $division) . '') % $round_item) / $division);
                break;
            case 9:
            case 19:
            case 29:
            case 39:
            case 49:
            case 59:
            case 69:
            case 79:
            case 89:
            case 99:
                $value = intval($value) + floatval($round_item / $division);
                break;
            default:
                break;
        }

        return $value;
    }

    public static function orders_id_parser($ids)
    {
        $output = '';
        $ids_array = explode('|', $ids);
        if (is_array($ids_array) && !empty($ids_array)) {
            foreach ($ids_array as $item) {
                $output .= self::parser($item);
            }
        } else {
            $output .= self::parser($ids_array);
        }

        return rtrim($output, ',');
    }

    private static function parser($ids_string)
    {
        $output = '';
        if (strpos($ids_string, '-') > 0) {
            $from_to = explode('-', $ids_string);
            if (isset($from_to[0]) && isset($from_to[1])) {
                for ($i = intval($from_to[0]); $i <= intval($from_to[1]); $i++) {
                    $output .= $i . ',';
                }
            }
        } else {
            $output = $ids_string . ',';
        }

        return $output;
    }

    public static function get_tax_query($taxonomy, $terms, $operator = null, $field = null)
    {
        $field = !empty($field) ? $field : 'slug';
        $values = (is_array($terms)) ? array_map('urldecode', $terms) : $terms;
        switch ($operator) {
            case null:
                $tax_item = [
                    'taxonomy' => urldecode($taxonomy),
                    'field' => $field,
                    'terms' => $values,
                    'operator' => 'AND'
                ];
                break;
            case 'or':
                $tax_item = [
                    'taxonomy' => urldecode($taxonomy),
                    'field' => $field,
                    'terms' => $values,
                    'operator' => 'IN'
                ];
                break;
            case 'and':
                $tax_item['relation'] = 'AND';
                if (is_array($values) && !empty($values)) {
                    foreach ($values as $value) {
                        $tax_item[] = [
                            'taxonomy' => urldecode($taxonomy),
                            'field' => $field,
                            'terms' => [$value],
                        ];
                    }
                }
                break;
            case 'not_in':
                $tax_item = [
                    'taxonomy' => urldecode($taxonomy),
                    'field' => $field,
                    'terms' => $values,
                    'operator' => 'NOT IN'
                ];
                break;
        }
        return $tax_item;
    }

    public static function set_filter_data_items($data, $others = null)
    {
        $args = [];
        if (!is_null($others)) {
            $args = $others;
        }
        if (isset($data['search_type']) && $data['search_type'] == 'quick_search') {
            if (isset($data['quick_search_text']) && !empty($data['quick_search_text'])) {
                switch ($data['quick_search_field']) {
                    case 'id':
                        $ids = self::orders_id_parser($data['quick_search_text']);
                        $args['wobef_general_column_filter'][] = [
                            'field' => 'ID',
                            'value' => $ids,
                            'operator' => "in"
                        ];
                        break;
                }
            }
        } else {
            if (isset($data['order_ids']) && !empty($data['order_ids']['value'])) {
                $ids = self::orders_id_parser($data['order_ids']['value']);
                $args['wobef_general_column_filter'][] = [
                    'field' => 'ID',
                    'value' => $ids,
                    'operator' => "in"
                ];
            }
            if (isset($data['post_status']) && !empty($data['post_status']['value'])) {
                $args['post_status'] = esc_sql($data['post_status']['value']);
            }
            if (isset($data['post_date']) && (!empty($data['post_date']['from']) || !empty(!empty($data['post_date']['to'])))) {
                $from = (!empty($data['post_date']['from'])) ? date('Y-m-d H:i:s', strtotime($data['post_date']['from'])) : null;
                $to = (!empty($data['post_date']['to'])) ? date('Y-m-d H:i:s', strtotime($data['post_date']['to'])) : null;
                if (!empty($from) & !empty($to)) {
                    $value = [$from, $to];
                    $operator = 'between';
                } else if (!empty($from)) {
                    $value = $from;
                    $operator = '>_with_quotation';
                } else {
                    $value = $to;
                    $operator = '<_with_quotation';
                }
                $args['wobef_general_column_filter'][] = [
                    'field' => 'post_date',
                    'value' => $value,
                    'operator' => $operator,
                ];
            }
            if (isset($data['post_modified']) && (!empty($data['post_modified']['from']) || !empty(!empty($data['post_modified']['to'])))) {
                $from = (!empty($data['post_modified']['from'])) ? date('Y-m-d H:i:s', strtotime($data['post_modified']['from'])) : null;
                $to = (!empty($data['post_modified']['to'])) ? date('Y-m-d H:i:s', strtotime($data['post_modified']['to'])) : null;
                if (!empty($from) & !empty($to)) {
                    $value = [$from, $to];
                    $operator = 'between';
                } else if (!empty($from)) {
                    $value = $from;
                    $operator = '>_with_quotation';
                } else {
                    $value = $to;
                    $operator = '<_with_quotation';
                }
                $args['wobef_general_column_filter'][] = [
                    'field' => 'post_modified',
                    'value' => $value,
                    'operator' => $operator,
                ];
            }
            if (isset($data['_paid_date']) && (!empty($data['_paid_date']['from']) || !empty(!empty($data['_paid_date']['to'])))) {
                $from = (!empty($data['_paid_date']['from'])) ? date('Y-m-d H:i:s', strtotime($data['_paid_date']['from'])) : null;
                $to = (!empty($data['_paid_date']['to'])) ? date('Y-m-d H:i:s', strtotime($data['_paid_date']['to'])) : null;
                if (!empty($from) & !empty($to)) {
                    $value = [$from, $to];
                    $operator = 'between_with_quotation';
                } else if (!empty($from)) {
                    $value = $from;
                    $operator = '>=_with_quotation';
                } else {
                    $value = $to;
                    $operator = '<=_with_quotation';
                }
                $args['wobef_meta_filter'][] = [
                    'key' => '_paid_date',
                    'value' => $value,
                    'operator' => $operator,
                ];
            }
            if (!empty($data['_customer_ip_address']['value'])) {
                $args['wobef_meta_filter'][] = [
                    'key' => '_customer_ip_address',
                    'value' => sanitize_text_field($data['_customer_ip_address']['value']),
                    'operator' => sanitize_text_field($data['_customer_ip_address']['operator']),
                ];
            }
            if (!empty($data['_billing_address_1']['value'])) {
                $args['wobef_meta_filter'][] = [
                    'key' => '_billing_address_1',
                    'value' => sanitize_text_field($data['_billing_address_1']['value']),
                    'operator' => sanitize_text_field($data['_billing_address_1']['operator']),
                ];
            }
            if (!empty($data['_billing_address_2']['value'])) {
                $args['wobef_meta_filter'][] = [
                    'key' => '_billing_address_2',
                    'value' => sanitize_text_field($data['_billing_address_2']['value']),
                    'operator' => sanitize_text_field($data['_billing_address_2']['operator']),
                ];
            }
            if (!empty($data['_billing_city']['value'])) {
                $args['wobef_meta_filter'][] = [
                    'key' => '_billing_city',
                    'value' => sanitize_text_field($data['_billing_city']['value']),
                    'operator' => sanitize_text_field($data['_billing_city']['operator']),
                ];
            }
            if (!empty($data['_billing_company']['value'])) {
                $args['wobef_meta_filter'][] = [
                    'key' => '_billing_company',
                    'value' => sanitize_text_field($data['_billing_company']['value']),
                    'operator' => sanitize_text_field($data['_billing_company']['operator']),
                ];
            }
            if (!empty($data['_billing_country']['value'])) {
                $args['wobef_meta_filter'][] = [
                    'key' => '_billing_country',
                    'value' => sanitize_text_field($data['_billing_country']['value']),
                ];
            }
            if (!empty($data['_billing_state']['value'])) {
                $args['wobef_meta_filter'][] = [
                    'key' => '_billing_state',
                    'value' => sanitize_text_field($data['_billing_state']['value']),
                ];
            }
            if (!empty($data['_billing_email']['value'])) {
                $args['wobef_meta_filter'][] = [
                    'key' => '_billing_email',
                    'value' => sanitize_text_field($data['_billing_email']['value']),
                    'operator' => sanitize_text_field($data['_billing_email']['operator']),
                ];
            }
            if (!empty($data['_billing_phone']['value'])) {
                $args['wobef_meta_filter'][] = [
                    'key' => '_billing_phone',
                    'value' => sanitize_text_field($data['_billing_phone']['value']),
                    'operator' => sanitize_text_field($data['_billing_phone']['operator']),
                ];
            }
            if (!empty($data['_billing_first_name']['value'])) {
                $args['wobef_meta_filter'][] = [
                    'key' => '_billing_first_name',
                    'value' => sanitize_text_field($data['_billing_first_name']['value']),
                    'operator' => sanitize_text_field($data['_billing_first_name']['operator']),
                ];
            }
            if (!empty($data['_billing_last_name']['value'])) {
                $args['wobef_meta_filter'][] = [
                    'key' => '_billing_last_name',
                    'value' => sanitize_text_field($data['_billing_last_name']['value']),
                    'operator' => sanitize_text_field($data['_billing_last_name']['operator']),
                ];
            }
            if (!empty($data['_billing_postcode']['value'])) {
                $args['wobef_meta_filter'][] = [
                    'key' => '_billing_postcode',
                    'value' => sanitize_text_field($data['_billing_postcode']['value']),
                    'operator' => sanitize_text_field($data['_billing_postcode']['operator']),
                ];
            }
            if (!empty($data['_shipping_address_1']['value'])) {
                $args['wobef_meta_filter'][] = [
                    'key' => '_shipping_address_1',
                    'value' => sanitize_text_field($data['_shipping_address_1']['value']),
                    'operator' => sanitize_text_field($data['_shipping_address_1']['operator']),
                ];
            }
            if (!empty($data['_shipping_address_2']['value'])) {
                $args['wobef_meta_filter'][] = [
                    'key' => '_shipping_address_2',
                    'value' => sanitize_text_field($data['_shipping_address_2']['value']),
                    'operator' => sanitize_text_field($data['_shipping_address_2']['operator']),
                ];
            }
            if (!empty($data['_shipping_city']['value'])) {
                $args['wobef_meta_filter'][] = [
                    'key' => '_shipping_city',
                    'value' => sanitize_text_field($data['_shipping_city']['value']),
                    'operator' => sanitize_text_field($data['_shipping_city']['operator']),
                ];
            }
            if (!empty($data['_shipping_company']['value'])) {
                $args['wobef_meta_filter'][] = [
                    'key' => '_shipping_company',
                    'value' => sanitize_text_field($data['_shipping_company']['value']),
                    'operator' => sanitize_text_field($data['_shipping_company']['operator']),
                ];
            }
            if (!empty($data['_shipping_country']['value'])) {
                $args['wobef_meta_filter'][] = [
                    'key' => '_shipping_country',
                    'value' => sanitize_text_field($data['_shipping_country']['value']),
                ];
            }
            if (!empty($data['_shipping_state']['value'])) {
                $args['wobef_meta_filter'][] = [
                    'key' => '_shipping_state',
                    'value' => sanitize_text_field($data['_shipping_state']['value']),
                ];
            }
            if (!empty($data['_shipping_first_name']['value'])) {
                $args['wobef_meta_filter'][] = [
                    'key' => '_shipping_first_name',
                    'value' => sanitize_text_field($data['_shipping_first_name']['value']),
                    'operator' => sanitize_text_field($data['_shipping_first_name']['operator']),
                ];
            }
            if (!empty($data['_shipping_last_name']['value'])) {
                $args['wobef_meta_filter'][] = [
                    'key' => '_shipping_last_name',
                    'value' => sanitize_text_field($data['_shipping_last_name']['value']),
                    'operator' => sanitize_text_field($data['_shipping_last_name']['operator']),
                ];
            }
            if (!empty($data['_shipping_postcode']['value'])) {
                $args['wobef_meta_filter'][] = [
                    'key' => '_shipping_postcode',
                    'value' => sanitize_text_field($data['_shipping_postcode']['value']),
                    'operator' => sanitize_text_field($data['_shipping_postcode']['operator']),
                ];
            }
            if (!empty($data['_order_currency']['value'])) {
                $args['wobef_meta_filter'][] = [
                    'key' => '_order_currency',
                    'value' => sanitize_text_field($data['_order_currency']['value']),
                ];
            }
            if (!empty($data['_order_total']['from']) || !empty($data['_order_total']['to'])) {
                $from = (!empty($data['_order_total']['from'])) ? $data['_order_total']['from'] : null;
                $to = (!empty($data['_order_total']['to'])) ? $data['_order_total']['to'] : null;
                if (!empty($from) & !empty($to)) {
                    $value = [$from, $to];
                    $operator = 'between';
                } else if (!empty($from)) {
                    $value = $from;
                    $operator = '>_with_quotation';
                } else {
                    $value = $to;
                    $operator = '<_with_quotation';
                }
                $args['wobef_meta_filter'][] = [
                    'key' => '_order_total',
                    'value' => $value,
                    'operator' => $operator,
                ];
            }
            if (!empty($data['_order_discount']['from']) || !empty($data['_order_discount']['to'])) {
                $from = (!empty($data['_order_discount']['from'])) ? $data['_order_discount']['from'] : null;
                $to = (!empty($data['_order_discount']['to'])) ? $data['_order_discount']['to'] : null;
                if (!empty($from) & !empty($to)) {
                    $value = [$from, $to];
                    $operator = 'between';
                } else if (!empty($from)) {
                    $value = $from;
                    $operator = '>_with_quotation';
                } else {
                    $value = $to;
                    $operator = '<_with_quotation';
                }
                $args['wobef_meta_filter'][] = [
                    'key' => '_cart_discount',
                    'value' => $value,
                    'operator' => $operator,
                ];
            }
            if (!empty($data['_order_discount_tax']['from']) || !empty($data['_order_discount_tax']['to'])) {
                $from = (!empty($data['_order_discount_tax']['from'])) ? $data['_order_discount_tax']['from'] : null;
                $to = (!empty($data['_order_discount_tax']['to'])) ? $data['_order_discount_tax']['to'] : null;
                if (!empty($from) & !empty($to)) {
                    $value = [$from, $to];
                    $operator = 'between';
                } else if (!empty($from)) {
                    $value = $from;
                    $operator = '>_with_quotation';
                } else {
                    $value = $to;
                    $operator = '<_with_quotation';
                }
                $args['wobef_meta_filter'][] = [
                    'key' => '_cart_discount_tax',
                    'value' => $value,
                    'operator' => $operator,
                ];
            }
            if (!empty($data['_created_via']['value'])) {
                $args['wobef_meta_filter'][] = [
                    'key' => '_created_via',
                    'value' => sanitize_text_field($data['_created_via']['value']),
                ];
            }
            if (!empty($data['_payment_method']['value'])) {
                $args['wobef_meta_filter'][] = [
                    'key' => '_payment_method',
                    'value' => sanitize_text_field($data['_payment_method']['value']),
                ];
            }
            if (!empty($data['_shipping_tax']['value'])) {
                $args['wobef_meta_filter'][] = [
                    'key' => '_order_shipping_tax',
                    'value' => (isset($data['_shipping_tax']['value']) && $data['_shipping_tax']['value'] == 'yes') ? 1 : 0,
                ];
            }
            if (!empty($data['_order_shipping']['value'])) {
                $args['wobef_meta_filter'][] = [
                    'key' => '_order_shipping',
                    'value' => (isset($data['_order_shipping']['value']) && $data['_order_shipping']['value'] == 'yes') ? 1 : 0,
                ];
            }
            if (!empty($data['_recorded_coupon_usage_counts']['value'])) {
                $args['wobef_coupon_used_filter'][] = [
                    'value' => sanitize_text_field($data['_recorded_coupon_usage_counts']['value']),
                ];
            }
            if (!empty($data['_order_stock_reduced']['value'])) {
                $args['wobef_meta_filter'][] = [
                    'key' => '_order_stock_reduced',
                    'value' => sanitize_text_field($data['_order_stock_reduced']['value']),
                ];
            }
            if (!empty($data['_prices_include_tax']['value'])) {
                $args['wobef_meta_filter'][] = [
                    'key' => '_prices_include_tax',
                    'value' => sanitize_text_field($data['_prices_include_tax']['value']),
                ];
            }
            if (!empty($data['_recorded_sales']['value'])) {
                $args['wobef_meta_filter'][] = [
                    'key' => '_recorded_sales',
                    'value' => sanitize_text_field($data['_recorded_sales']['value']),
                ];
            }
            if (!empty($data['products']['value'])) {
                $args['wobef_product_filter'][] = [
                    'operator' => sanitize_text_field($data['products']['operator']),
                    'value' => esc_sql($data['products']['value']),
                ];
            }
            if (!empty($data['categories']['value'])) {
                $args['wobef_product_taxonomy_filter'][] = [
                    'operator' => sanitize_text_field($data['categories']['operator']),
                    'value' => esc_sql($data['categories']['value']),
                    'taxonomy' => 'product_cat'
                ];
            }
            if (!empty($data['tags']['value'])) {
                $args['wobef_product_taxonomy_filter'][] = [
                    'operator' => sanitize_text_field($data['tags']['operator']),
                    'value' => esc_sql($data['tags']['value']),
                    'taxonomy' => 'product_tag'
                ];
            }
            if (!empty($data['taxonomies']['value'])) {
                $args['wobef_product_taxonomy_filter'][] = [
                    'operator' => sanitize_text_field($data['taxonomies']['operator']),
                    'value' => esc_sql($data['taxonomies']['value']),
                ];
            }
            if (isset($data['custom_fields']) && !empty($data['custom_fields'])) {
                foreach ($data['custom_fields'] as $custom_field_item) {
                    switch ($custom_field_item['type']) {
                        case 'from-to-date':
                            $from = (!empty($custom_field_item['value'][0])) ? date('Y-m-d H:i:s', strtotime($custom_field_item['value'][0])) : null;
                            $to = (!empty($custom_field_item['value'][1])) ? date('Y-m-d H:i:s', strtotime($custom_field_item['value'][1])) : null;
                            if (empty($from) && empty($to)) {
                                $value = null;
                                $operator = null;
                                break;
                            }
                            if (!empty($from) & !empty($to)) {
                                $value = [$from, $to];
                                $operator = 'between_with_quotation';
                            } else if (!empty($from)) {
                                $value = $from;
                                $operator = '>=_with_quotation';
                            } else {
                                $value = $to;
                                $operator = '<=_with_quotation';
                            }
                            break;
                        case 'from-to-time':
                            $from = (!empty($custom_field_item['value'][0])) ? date('H:i', strtotime($custom_field_item['value'][0])) : null;
                            $to = (!empty($custom_field_item['value'][1])) ? date('H:i', strtotime($custom_field_item['value'][1])) : null;
                            if (empty($from) && empty($to)) {
                                $value = null;
                                $operator = null;
                                break;
                            }
                            if (!empty($from) & !empty($to)) {
                                $value = [$from, $to];
                                $operator = 'between_with_quotation';
                            } else if (!empty($from)) {
                                $value = $from;
                                $operator = '>=_with_quotation';
                            } else {
                                $value = $to;
                                $operator = '<=_with_quotation';
                            }
                            break;
                        case 'from-to-number':
                            $from = (!empty($custom_field_item['value'][0])) ? floatval($custom_field_item['value'][0]) : null;
                            $to = (!empty($custom_field_item['value'][1])) ? floatval($custom_field_item['value'][1]) : null;
                            if (empty($from) && empty($to)) {
                                $value = null;
                                $operator = null;
                                break;
                            }
                            if (!empty($from) & !empty($to)) {
                                $value = [$from, $to];
                                $operator = 'between';
                            } else if (!empty($from)) {
                                $value = $from;
                                $operator = '>=';
                            } else {
                                $value = $to;
                                $operator = '<=';
                            }
                            break;
                        case 'text':
                            $operator = $custom_field_item['operator'];
                            $value = $custom_field_item['value'];
                            break;
                        case 'select':
                            $operator = "like";
                            $value = $custom_field_item['value'];
                            break;
                    }

                    if (!empty($value)) {
                        if (is_array($value) && $custom_field_item['type'] == 'select') {
                            $values = [];
                            foreach ($value as $value_item) {
                                if (!empty($value_item)) {
                                    $values[] = $value_item;
                                }
                            }
                            if (!empty($values)) {
                                $args['wobef_meta_filter'][] = [
                                    'key' => $custom_field_item['taxonomy'],
                                    'value' => $values,
                                    'operator' => $operator,
                                ];
                            }
                        } else {
                            $args['wobef_meta_filter'][] = [
                                'key' => $custom_field_item['taxonomy'],
                                'value' => $value,
                                'operator' => $operator,
                            ];
                        }
                    }
                }
            }
        }

        return $args;
    }

    public static function apply_operator($old_value, $data)
    {
        if (empty($data['operator'])) {
            return $data['value'];
        }

        $data['value'] = (!empty($data['operator_type'])) ? self::apply_calculator_operator($old_value, $data) : self::apply_default_operator($old_value, $data);
        $data['value'] = (isset($data['round']) && !empty($data['round'])) ? self::round($data['value'], $data['round']) : $data['value'];

        return $data['value'];
    }

    private static function apply_calculator_operator($old_value, $data)
    {
        $old_value = floatval($old_value);
        $data['value'] = floatval($data['value']);
        $data['sale_price'] = (isset($data['sale_price'])) ? floatval($data['sale_price']) : 0;
        $data['regular_price'] = (isset($data['regular_price'])) ? floatval($data['regular_price']) : 0;

        switch ($data['operator_type']) {
            case 'n':
                switch ($data['operator']) {
                    case '+':
                        $data['value'] += $old_value;
                        break;
                    case '-':
                        $data['value'] = $old_value - $data['value'];
                        break;
                    case 'sp+':
                        $data['value'] += $data['sale_price'];
                        break;
                    case 'rp-':
                        $data['value'] = $data['regular_price'] - $data['value'];
                        break;
                }
                break;
            case '%':
                switch ($data['operator']) {
                    case '+':
                        $data['value'] = $old_value + ($old_value * $data['value'] / 100);
                        break;
                    case '-':
                        $data['value'] = $old_value - ($old_value * $data['value'] / 100);
                        break;
                    case 'sp+':
                        $data['value'] = $data['sale_price'] + ($data['sale_price'] * $data['value'] / 100);
                        break;
                    case 'rp-':
                        $data['value'] = $data['regular_price'] - ($data['regular_price'] * $data['value'] / 100);
                        break;
                }
                break;
        }

        return $data['value'];
    }

    private static function apply_default_operator($old_value, $data)
    {
        switch ($data['operator']) {
            case 'text_append':
                $data['value'] = $old_value . $data['value'];
                break;
            case 'text_prepend':
                $data['value'] = $data['value'] . $old_value;
                break;
            case 'text_new':
                $data['value'] = $data['value'];
                break;
            case 'text_delete':
                $data['value'] = str_replace($data['value'], '', $old_value);
                break;
            case 'text_replace':
                if (isset($data['value'])) {
                    $data['value'] = ($data['sensitive'] == 'yes') ? str_replace($data['value'], $data['replace'], $old_value) : str_ireplace($data['value'], $data['replace'], $old_value);
                } else {
                    $data['value'] = $old_value;
                }
                break;
            case 'text_remove_duplicate':
                $data['value'] = $old_value;
                break;
            case 'taxonomy_append':
                $data['value'] = array_unique(array_merge($old_value, $data['value']));
                break;
            case 'taxonomy_replace':
                $data['value'] = $data['value'];
                break;
            case 'taxonomy_delete':
                $data['value'] = array_values(array_diff($old_value, $data['value']));
                break;
            case 'number_new':
                $data['value'] = $data['value'];
                break;
            case 'number_delete':
                $data['value'] = str_replace($data['value'], '', $old_value);
                break;
            case 'number_clear':
                $data['value'] = '';
                break;
            case 'number_formula':
                $formulaCalculator = new Formula();
                $data['value'] = $formulaCalculator->calculate($data['value'], ['X' => $old_value]);
                break;
            case 'increase_by_value':
                $data['value'] = floatval($old_value) + floatval($data['value']);
                break;
            case 'decrease_by_value':
                $data['value'] = floatval($old_value) - floatval($data['value']);
                break;
            case 'increase_by_percent':
                $data['value'] = floatval($old_value) + floatval(floatval($old_value) * floatval($data['value']) / 100);
                break;
            case 'decrease_by_percent':
                $data['value'] = floatval($old_value) - floatval(floatval($old_value) * floatval($data['value']) / 100);
                break;
            case 'increase_by_value_from_sale':
                $data['value'] = (isset($data['sale_price'])) ? floatval($data['sale_price']) + floatval($data['value']) : $data;
                break;
            case 'increase_by_percent_from_sale':
                $data['value'] = (isset($data['sale_price'])) ? floatval($data['sale_price']) + floatval(floatval($data['sale_price']) * floatval($data['value']) / 100) : $data;
                break;
            case 'decrease_by_value_from_regular':
                $data['value'] = (isset($data['regular_price'])) ? floatval($data['regular_price']) - floatval($data['value']) : $data;
                break;
            case 'decrease_by_percent_from_regular':
                $data['value'] = (isset($data['regular_price'])) ? floatval($data['regular_price']) - (floatval($data['regular_price']) * floatval($data['value']) / 100) : $data;
                break;
        }

        return $data['value'];
    }
}

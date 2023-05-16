<?php

namespace wobef\classes\helpers;

class Operator
{
    public static function edit_text()
    {
        return [
            'text_append' => esc_html__('Append', 'ithemeland-woocommerce-bulk-orders-editing-lite'),
            'text_prepend' => esc_html__('Prepend', 'ithemeland-woocommerce-bulk-orders-editing-lite'),
            'text_new' => esc_html__('New', 'ithemeland-woocommerce-bulk-orders-editing-lite'),
            'text_delete' => esc_html__('Delete', 'ithemeland-woocommerce-bulk-orders-editing-lite'),
            'text_replace' => esc_html__('Replace', 'ithemeland-woocommerce-bulk-orders-editing-lite'),
        ];
    }

    public static function edit_taxonomy()
    {
        return [
            'taxonomy_append' => esc_html__('Append', 'ithemeland-woocommerce-bulk-orders-editing-lite'),
            'taxonomy_replace' => esc_html__('Replace', 'ithemeland-woocommerce-bulk-orders-editing-lite'),
            'taxonomy_delete' => esc_html__('Delete', 'ithemeland-woocommerce-bulk-orders-editing-lite'),
        ];
    }

    public static function edit_number()
    {
        return [
            'number_new' => esc_html__('Set New', 'ithemeland-woocommerce-bulk-orders-editing-lite'),
            'number_clear' => esc_html__('Clear Value', 'ithemeland-woocommerce-bulk-orders-editing-lite'),
            'number_formula' => esc_html__('Formula', 'ithemeland-woocommerce-bulk-orders-editing-lite'),
            'increase_by_value' => esc_html__('Increase by value', 'ithemeland-woocommerce-bulk-orders-editing-lite'),
            'decrease_by_value' => esc_html__('Decrease by value', 'ithemeland-woocommerce-bulk-orders-editing-lite'),
            'increase_by_percent' => esc_html__('Increase by %', 'ithemeland-woocommerce-bulk-orders-editing-lite'),
            'decrease_by_percent' => esc_html__('Decrease by %', 'ithemeland-woocommerce-bulk-orders-editing-lite'),
        ];
    }

    public static function edit_regular_price()
    {
        return [
            'increase_by_value_from_sale' => esc_html__('Increase by value (From sale)', 'ithemeland-woocommerce-bulk-orders-editing-lite'),
            'increase_by_percent_from_sale' => esc_html__('Increase by % (From sale)', 'ithemeland-woocommerce-bulk-orders-editing-lite'),
        ];
    }

    public static function edit_sale_price()
    {
        return [
            'decrease_by_value_from_regular' => esc_html__('Decrease by value (From regular)', 'ithemeland-woocommerce-bulk-orders-editing-lite'),
            'decrease_by_percent_from_regular' => esc_html__('Decrease by % (From regular)', 'ithemeland-woocommerce-bulk-orders-editing-lite'),
        ];
    }

    public static function filter_text()
    {
        return [
            'like' => esc_html__('Like', 'ithemeland-woocommerce-bulk-orders-editing-lite'),
            'exact' => esc_html__('Exact', 'ithemeland-woocommerce-bulk-orders-editing-lite'),
            'not' => esc_html__('Not', 'ithemeland-woocommerce-bulk-orders-editing-lite'),
            'begin' => esc_html__('Begin', 'ithemeland-woocommerce-bulk-orders-editing-lite'),
            'end' => esc_html__('End', 'ithemeland-woocommerce-bulk-orders-editing-lite'),
        ];
    }

    public static function filter_multi_select()
    {
        return [
            'or' => 'OR',
            'and' => 'And',
            'not_in' => 'Not IN',
        ];
    }

    public static function round_items()
    {
        return [
            5 => 5,
            10 => 10,
            19 => 19,
            29 => 29,
            39 => 39,
            49 => 49,
            59 => 59,
            69 => 69,
            79 => 79,
            89 => 89,
            99 => 99
        ];
    }
}

<?php

namespace wobef\classes\helpers;

class Generator
{
    public static function license_hash($license_data, $product_id)
    {
        return (empty($license_data) || !isset($license_data['license_key']) || !isset($license_data['email']))
            ? md5(rand(100000, 999999))
            : md5($license_data['license_key'] . sanitize_text_field($product_id) . $license_data['email'] . $_SERVER['SERVER_NAME']);
    }

    public static function div_field_start($attributes = [])
    {
        $output = "<div";
        $output .= self::get_field_attributes($attributes);
        $output .= ">";
        return sprintf('%s', $output);
    }

    public static function div_field_end()
    {
        return "</div>";
    }

    public static function label_field($attributes, $label_text)
    {
        $output = "<label";
        $output .= self::get_field_attributes($attributes);
        $output .= ">";
        if (!empty($label_text)) {
            $output .= sanitize_text_field($label_text);
        }
        $output .= "</label>";
        return sprintf('%s', $output);
    }

    public static function select_field($attributes, $options, $first_select_option = false)
    {
        $output = "<select";
        $output .= self::get_field_attributes($attributes);
        $output .= ">";
        if ($first_select_option) {
            $output .= "<option value=''>" . esc_html__('Select', 'ithemeland-woocommerce-bulk-orders-editing-lite') . "</option>";
        }
        if (!empty($options) && is_array($options)) {
            foreach ($options as $key => $value) {
                $output .= '<option value="' . sanitize_text_field($key) . '">' . sanitize_text_field($value) . '</option>';
            }
        }
        $output .= "</select>";

        return sprintf('%s', $output);
    }

    public static function textarea_field($attributes, $value = "")
    {
        $output = "<textarea";
        $output .= self::get_field_attributes($attributes);
        $output .= ">";
        if (!empty($value)) {
            $output .= $value;
        }
        $output .= "</textarea>";
        return sprintf('%s', $output);
    }

    public static function input_field($attributes)
    {
        $output = "<input";
        $output .= self::get_field_attributes($attributes);
        $output .= ">";
        return sprintf('%s', $output);
    }

    public static function span_field($text, $attributes = [])
    {
        $output = "<span";
        $output .= self::get_field_attributes($attributes);
        $output .= ">";
        $output .= sanitize_text_field($text);
        $output .= "</span>";
        return sprintf('%s', $output);
    }

    private static function get_field_attributes($attributes = [])
    {
        $output = "";
        if (!empty($attributes) && is_array($attributes)) {
            foreach ($attributes as $key => $value) {
                $output .= " " . sanitize_text_field($key) . '="' . sanitize_text_field($value) . '"';
            }
        }
        return $output;
    }
}

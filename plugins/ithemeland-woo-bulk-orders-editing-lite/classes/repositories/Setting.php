<?php

namespace wobef\classes\repositories;

class Setting
{
    private $settings_option_name;
    private $current_settings_option_name;

    public function __construct()
    {
        $this->settings_option_name = "wobef_settings";
        $this->current_settings_option_name = "wobef_current_settings";
    }

    public function update($data = [])
    {
        $settings = [];
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $settings[sanitize_text_field($key)] = sanitize_text_field($value);
            }
        }

        $this->set_current_settings($settings);
        return update_option($this->settings_option_name, $settings);
    }

    public function get_settings()
    {
        return get_option($this->settings_option_name);
    }

    public function get_current_settings()
    {
        return get_option($this->current_settings_option_name);
    }

    public function update_current_settings($current_settings)
    {
        $old_current_settings = $this->get_current_settings();
        if (!empty($current_settings)) {
            foreach ($current_settings as $setting_key => $setting_value) {
                $old_current_settings[$setting_key] = $setting_value;
            }
        }
        update_option($this->current_settings_option_name, $old_current_settings);
        return $this->get_current_settings();
    }

    public function delete_current_settings()
    {
        return delete_option($this->current_settings_option_name);
    }

    public function set_default_settings()
    {
        $this->update([
            'count_per_page' => 10,
            'default_sort_by' => 'id',
            'default_sort' => "DESC",
            'show_quick_search' => 'yes',
            'sticky_search_form' => 'yes',
            'sticky_first_columns' => 'yes',
            'fetch_data_in_bulk' => 'no',
            'show_customer_details' => 'yes',
            'display_full_columns_title' => 'yes',
            'show_order_items_popup' => 'yes',
            'show_billing_shipping_address_popup' => 'yes',
            'colorize_status_column' => 'yes',
        ]);
    }

    public function set_current_settings($settings)
    {
        $this->update_current_settings([
            'count_per_page' => isset($settings['count_per_page']) ? $settings['count_per_page'] : 10,
            'sticky_first_columns' => isset($settings['sticky_first_columns']) ? $settings['sticky_first_columns'] : 'yes',
            'show_customer_details' => isset($settings['show_customer_details']) ? $settings['show_customer_details'] : 'yes',
        ]);
    }
}

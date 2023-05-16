<?php

namespace wobef\classes\providers\column;

use wobef\classes\repositories\Column;
use wobef\classes\repositories\Order;
use wobef\classes\repositories\Setting;

class OrderColumnProvider
{
    private static $instance;
    private $sticky_first_columns;
    private $order_repository;
    private $order;
    private $order_object;
    private $column_key;
    private $decoded_column_key;
    private $column_data;
    private $field_type;
    private $settings;

    public static function get_instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        $this->order_repository = new Order();
        $setting_repository = new Setting();
        $this->settings = $setting_repository->get_settings();
        $this->sticky_first_columns = isset($this->settings['sticky_first_columns']) ? $this->settings['sticky_first_columns'] : 'yes';

        $this->field_type = "";

        $this->fields_method = $this->get_fields_method();
    }

    public function get_item_columns($item, $columns)
    {
        return $this->item_columns($item, $columns);
    }

    private function item_columns($item, $columns)
    {
        if ($item instanceof \WC_Order) {
            $this->order_object = $item;
            $this->order = $this->order_repository->order_to_array($item);
            $output = '<tr data-item-id="' . esc_attr($this->order['id']) . '">';
            $output .= $this->get_static_columns();
            if (!empty($columns) && is_array($columns)) {
                foreach ($columns as $column_key => $column_data) {
                    $this->column_key = $column_key;
                    $this->column_data = $column_data;
                    $this->decoded_column_key = (substr($this->column_key, 0, 3) == 'pa_') ? strtolower(urlencode($this->column_key)) : urlencode($this->column_key);
                    $field_data = $this->get_field();
                    $output .= (!empty($field_data['field'])) ? $field_data['field'] : '';
                    if (isset($field_data['includes']) && is_array($field_data['includes'])) {
                        foreach ($field_data['includes'] as $include) {
                            if (file_exists($include)) {
                                include $include;
                            }
                        }
                    }
                }
            }
            $output .= $this->get_action_column();
            $output .= "</tr>";
            return $output;
        }
    }

    private function get_action_column()
    {
        $output = '<td class="wobef-action-column">';

        ob_start();
        do_action('woocommerce_admin_order_actions_start', $this->order_object);
        $output .= ob_get_clean();

        $actions = array();
        if ($this->order_object->has_status(array('pending', 'on-hold'))) {
            $actions['processing'] = array(
                'url'    => wp_nonce_url(admin_url('admin-ajax.php?action=woocommerce_mark_order_status&status=processing&order_id=' . $this->order_object->get_id()), 'woocommerce-mark-order-status'),
                'name'   => __('Processing', 'woocommerce'),
                'action' => 'processing',
            );
        }
        if ($this->order_object->has_status(array('pending', 'on-hold', 'processing'))) {
            $actions['complete'] = array(
                'url'    => wp_nonce_url(admin_url('admin-ajax.php?action=woocommerce_mark_order_status&status=completed&order_id=' . $this->order_object->get_id()), 'woocommerce-mark-order-status'),
                'name'   => __('Complete', 'woocommerce'),
                'action' => 'complete',
            );
        }
        $actions = apply_filters('woocommerce_admin_order_actions', $actions, $this->order_object);
        $output .=  (!empty($actions)) ? wc_render_action_buttons($actions) : ' ';

        ob_start();
        do_action('woocommerce_admin_order_actions_end', $this->order_object);
        $output .= ob_get_clean();

        $output .= '</td>';
        return $output;
    }

    private function get_field()
    {
        $output['field'] = '';
        $output['includes'] = [];
        $this->field_type = '';

        $this->set_order_field();
        $color = $this->get_column_colors_style();

        if (isset($this->settings['show_billing_shipping_address_popup']) && $this->settings['show_billing_shipping_address_popup'] == 'no' && $this->column_data['content_type'] == 'address') {
            $this->column_data['content_type'] = 'text';
            if (in_array($this->column_key, ['_billing_address_index', '_shipping_address_index'])) {
                $this->column_data['editable'] = false;
            }
        }

        $editable = ($this->column_data['editable']) ? 'yes' : 'no';
        $sub_name = (!empty($this->column_data['sub_name'])) ? $this->column_data['sub_name'] : '';
        $update_type = (!empty($this->column_data['update_type'])) ? $this->column_data['update_type'] : '';
        $output['field'] .= '<td data-item-id="' . esc_attr($this->order['id']) . '" data-editable="' . $editable . '" data-item-title="#' . esc_attr($this->order['id']) . '" data-col-title="' . esc_attr($this->column_data['title']) . '" data-field="' . esc_attr($this->column_key) . '" data-field-type="' . esc_attr($this->field_type) . '" data-name="' . esc_attr($this->column_data['name']) . '" data-sub-name="' . esc_attr($sub_name) . '" data-update-type="' . esc_attr($update_type) . '" style="' . esc_attr($color['background']) . ' ' . esc_attr($color['text']) . '"';
        if ($this->column_data['editable'] === true && !in_array($this->column_data['content_type'], ['multi_select', 'multi_select_attribute'])) {
            $output['field'] .= 'data-content-type="' . esc_attr($this->column_data['content_type']) . '" data-action="inline-editable"';
        }
        $output['field'] .= '>';

        if ($this->column_data['editable'] === true) {
            $generated = $this->generate_field();
            if (is_array($generated) && isset($generated['field']) && isset($generated['includes'])) {
                $output['field'] .= $generated['field'];
                $output['includes'][] = $generated['includes'];
            } else {
                $output['field'] .= $generated;
            }
        } else {
            if (isset($this->order[$this->decoded_column_key])) {
                $output['field'] .= (is_array($this->order[$this->decoded_column_key])) ? sprintf('%s', implode(',', $this->order[$this->decoded_column_key])) : sprintf('%s', $this->order[$this->decoded_column_key]);
            } else {
                $output['field'] .= ' ';
            }
        }

        $output['field'] .= '</td>';
        return $output;
    }

    private function get_id_column()
    {
        $output = '';
        if (Column::SHOW_ID_COLUMN === true) {
            $sticky_class = ($this->sticky_first_columns == 'yes') ? 'wobef-td-sticky wobef-td-sticky-id wobef-gray-bg' : '';
            $output .= '<td data-item-id="' . esc_attr($this->order['id']) . '" data-item-title="#' . esc_attr($this->order['id']) . '" data-col-title="ID" class="' . esc_attr($sticky_class) . '">';
            $output .= '<label class="wobef-td70">';
            $output .= '<input type="checkbox" class="wobef-check-item" value="' . esc_attr($this->order['id']) . '" title="Select Order">';
            $output .= esc_html($this->order['id']);
            $output .= '<a href="' . admin_url("post.php?post=" . esc_attr($this->order['id']) . "&action=edit") . '" target="_blank" class="wobef-ml5 wobef-float-right" title="Edit Order"><span class="lni lni-pencil-alt"></span></a>';
            $output .= "</label>";
            $output .= "</td>";
        }
        return $output;
    }

    private function get_static_columns()
    {
        return $this->get_id_column();
    }

    private function set_order_field()
    {
        if (isset($this->column_data['field_type'])) {
            switch ($this->column_data['field_type']) {
                case 'custom_field':
                    $this->field_type = 'custom_field';
                    $this->order[$this->decoded_column_key] = (isset($this->order['custom_field'][$this->decoded_column_key])) ? $this->order['custom_field'][$this->decoded_column_key][0] : '';
                    break;
                default:
                    break;
            }
        }
    }

    private function get_column_colors_style()
    {
        if ($this->column_key == 'post_status' && isset($this->settings['colorize_status_column']) && $this->settings['colorize_status_column'] == 'yes') {
            $status_color = $this->order_repository->get_status_color($this->order['post_status']);
            $status_color = (!empty($status_color)) ? $status_color : '#fff';
            $color['background'] = "background: {$status_color};";
        } else {
            $color['background'] = (!empty($this->column_data['background_color']) && $this->column_data['background_color'] != '#fff' && $this->column_data['background_color'] != '#ffffff') ? 'background:' . esc_attr($this->column_data['background_color']) . ';' : '';
        }
        $color['text'] = (!empty($this->column_data['text_color'])) ? 'color:' . esc_attr($this->column_data['text_color']) . ';' : '';
        return $color;
    }

    private function generate_field()
    {
        if (isset($this->fields_method[$this->column_data['content_type']]) && method_exists($this, $this->fields_method[$this->column_data['content_type']])) {
            return $this->{$this->fields_method[$this->column_data['content_type']]}();
        } else {
            return (is_array($this->order[$this->decoded_column_key])) ? implode(',', $this->order[$this->decoded_column_key]) : $this->order[$this->decoded_column_key];
        }
    }

    private function get_fields_method()
    {
        return [
            'text' => 'text_field',
            'email' => 'text_field',
            'textarea' => 'textarea_field',
            'image' => 'image_field',
            'numeric' => 'numeric_with_calculator_field',
            'numeric_without_calculator' => 'numeric_field',
            'checkbox_dual_mode' => 'checkbox_dual_model_field',
            'checkbox' => 'checkbox_field',
            'radio' => 'radio_field',
            'file' => 'file_field',
            'select' => 'select_field',
            'date' => 'date_field',
            'date_picker' => 'date_field',
            'date_time_picker' => 'datetime_field',
            'time_picker' => 'time_field',
            'color_picker' => 'color_field',
            'order_details' => 'order_details_field',
            'order_items' => 'order_items_field',
            'all_billing' => 'all_billing_field',
            'all_shipping' => 'all_shipping_field',
            'address' => 'address_field',
            'order_notes' => 'order_notes_field',
            'customer' => 'customer_field',
            'order_status' => 'order_status_field',
        ];
    }

    private function text_field()
    {
        $value = (is_array($this->order[$this->decoded_column_key])) ? implode(',', $this->order[$this->decoded_column_key]) : $this->order[$this->decoded_column_key];
        $output = "<span data-action='inline-editable' class='wobef-td160'>" . sprintf('%s', $value) . "</span>";
        return $output;
    }

    private function textarea_field()
    {
        return "<button type='button' data-toggle='modal' data-target='#wobef-modal-text-editor' class='wobef-button wobef-button-white wobef-load-text-editor wobef-td160' data-item-id='" . esc_attr($this->order['id']) . "' data-item-name='#" . esc_attr($this->order['id']) . "' data-field='" . esc_attr($this->column_key) . "' data-field-type='" . esc_attr($this->field_type) . "'>Content</button>";
    }

    private function image_field()
    {
        if (isset($this->order[$this->decoded_column_key]['small'])) {
            $image_id = intval($this->order[$this->decoded_column_key]['id']);
            $image = sprintf('%s', $this->order[$this->decoded_column_key]['small']);
            $full_size = wp_get_attachment_image_src($image_id, 'full');
        }
        if (isset($this->order[$this->decoded_column_key]) && is_numeric($this->order[$this->decoded_column_key])) {
            $image_id = intval($this->order[$this->decoded_column_key]);
            $image_url = wp_get_attachment_image_src($image_id, [40, 40]);
            $full_size = wp_get_attachment_image_src($image_id, 'full');
            $image = (!empty($image_url[0])) ? "<img src='" . esc_url($image_url[0]) . "' alt='' width='40' height='40' />" : null;
        }
        $image = (!empty($image)) ? $image : esc_html__('No Image', 'ithemeland-woocommerce-bulk-orders-editing-lite');
        $full_size = (!empty($full_size[0])) ? $full_size[0] : esc_url(wp_upload_dir()['baseurl'] . "/woocommerce-placeholder.png");
        $image_id = (!empty($image_id)) ? $image_id : 0;

        return "<span data-toggle='modal' data-target='#wobef-modal-image' data-id='wobef-" . esc_attr($this->column_key) . "-" . esc_attr($this->order['id']) . "' class='wobef-image-inline-edit' data-full-image-src='" . esc_url($full_size) . "' data-image-id='" . esc_attr($image_id) . "'>" . $image . "</span>";
    }

    private function numeric_with_calculator_field()
    {
        return "<span data-action='inline-editable' class='wobef-numeric-content wobef-td120'>" . esc_html($this->order[$this->decoded_column_key]) . "</span><button type='button' data-toggle='modal' class='wobef-calculator' data-field='" . esc_attr($this->column_key) . "' data-item-id='" . esc_attr($this->order['id']) . "' data-item-name='#" . esc_attr($this->order['id']) . "' data-field-type='" . esc_attr($this->field_type) . "' data-target='#wobef-modal-numeric-calculator'></button>";
    }

    private function numeric_field()
    {
        return "<span data-action='inline-editable' class='wobef-numeric-content wobef-td120'>" . esc_html($this->order[$this->decoded_column_key]) . "</span>";
    }

    private function checkbox_dual_model_field()
    {
        $checked = ($this->order[$this->decoded_column_key] == 'yes' || $this->order[$this->decoded_column_key] == 1) ? 'checked="checked"' : "";
        return "<label><input type='checkbox' data-field='" . esc_attr($this->column_key) . "' data-field-type='" . esc_attr($this->field_type) . "' data-item-id='" . esc_attr($this->order['id']) . "' value='yes' class='wobef-dual-mode-checkbox wobef-inline-edit-action' " . esc_attr($checked) . "><span>" . esc_html__('Yes', 'ithemeland-woocommerce-bulk-orders-editing-lite') . "</span></label>";
    }

    private function file_field()
    {
        $file_id = (isset($this->order[$this->decoded_column_key])) ? intval($this->order[$this->decoded_column_key]) : null;
        $file_url = wp_get_attachment_url($file_id);
        $file_url = !empty($file_url) ? esc_url($file_url) : '';
        return "<button type='button' data-toggle='modal' data-target='#wobef-modal-file' class='wobef-button wobef-button-white' data-item-id='" . esc_attr($this->order['id']) . "' data-item-name='#" . esc_attr($this->order['id']) . "' data-field='" . esc_attr($this->column_key) . "' data-field-type='" . esc_attr($this->field_type) . "' data-file-id='" . $file_id . "' data-file-url='" . $file_url . "'>Select File</button>";
    }

    private function order_status_field()
    {
        $statuses = $this->order_repository->get_order_statuses();
        $output = "<select class='wobef-inline-edit-action' data-field='" . esc_attr($this->column_key) . "' data-item-id='" . esc_attr($this->order['id']) . "' title='Select " . esc_attr($this->column_data['label']) . "' data-field-type='" . esc_attr($this->field_type) . "'>";
        foreach ($statuses as $status_key => $status_value) {
            $selected = ($status_key == $this->order[$this->decoded_column_key]) ? 'selected' : '';
            $output .= "<option value='{$status_key}' $selected>{$status_value}</option>";
        }
        $output .= '</select>';
        return $output;
    }

    private function select_field()
    {
        $output = "";
        if (in_array($this->column_key, ['_billing_state', '_shipping_state'])) {
            $states = $this->order_repository->get_shipping_states();
            $country = ($this->column_key == '_billing_state') ? $this->order['_billing_country'] : $this->order['_shipping_country'];
            if (!empty($states) && !empty($states[$country]) && is_array($states[$country])) {
                $output .= "<select class='wobef-inline-edit-action' data-field='" . esc_attr($this->column_key) . "' data-item-id='" . esc_attr($this->order['id']) . "' title='Select " . esc_attr($this->column_data['label']) . "' data-field-type='" . esc_attr($this->field_type) . "'>";
                foreach ($states[$country] as $state_key => $state_label) {
                    $selected = ($state_key == $this->order[$this->decoded_column_key]) ? 'selected' : '';
                    $output .= "<option value='{$state_key}' {$selected}>{$state_label}</option>";
                }
                $output .= '</select>';
            } else {
                $output .= "<span data-action='inline-editable' class='wobef-td160'>" . $this->order[$this->decoded_column_key] . "</span>";
            }
        } else {
            if (!empty($this->column_data['options'])) {
                $output .= "<select class='wobef-inline-edit-action' data-field='" . esc_attr($this->column_key) . "' data-item-id='" . esc_attr($this->order['id']) . "' title='Select " . esc_attr($this->column_data['label']) . "' data-field-type='" . esc_attr($this->field_type) . "'>";
                foreach ($this->column_data['options'] as $option_key => $option_value) {
                    $selected = ($option_key == $this->order[$this->decoded_column_key]) ? 'selected' : '';
                    $output .= "<option value='{$option_key}' $selected>{$option_value}</option>";
                }
                $output .= '</select>';
            }
        }

        return $output;
    }

    private function date_field()
    {
        $date = (!empty($this->order[$this->decoded_column_key])) ? date('Y/m/d', strtotime($this->order[$this->decoded_column_key])) : '';
        $clear_button = ($this->decoded_column_key != 'post_date') ? "<button type='button' class='wobef-clear-date-btn wobef-inline-edit-clear-date' data-field='" . esc_attr($this->column_key) . "' data-field-type='" . esc_attr($this->field_type) . "' data-item-id='" . esc_attr($this->order['id']) . "' value=''><img src='" . esc_url(WOBEF_IMAGES_URL . 'calendar_clear.svg') . "' alt='Clear' title='Clear Date'></button>" : '';
        return "<input type='text' class='wobef-datepicker wobef-inline-edit-action' data-field='" . esc_attr($this->column_key) . "' data-field-type='" . esc_attr($this->field_type) . "' data-item-id='" . esc_attr($this->order['id']) . "' title='Select " . esc_attr($this->column_data['label']) . "' value='" . esc_html($date) . "'>" . sprintf('%s', $clear_button);
    }

    private function datetime_field()
    {
        $date = (!empty($this->order[$this->decoded_column_key])) ? date('Y/m/d H:i', strtotime($this->order[$this->decoded_column_key])) : '';
        $clear_button = "<button type='button' class='wobef-clear-date-btn wobef-inline-edit-clear-date' data-field='" . esc_attr($this->column_key) . "' data-field-type='" . esc_attr($this->field_type) . "' data-item-id='" . esc_attr($this->order['id']) . "' value=''><img src='" . esc_url(WOBEF_IMAGES_URL . 'calendar_clear.svg') . "' alt='Clear' title='Clear Date'></button>";
        return "<input type='text' class='wobef-datetimepicker wobef-inline-edit-action' data-field='" . esc_attr($this->column_key) . "' data-field-type='" . esc_attr($this->field_type) . "' data-item-id='" . esc_attr($this->order['id']) . "' title='Select " . esc_attr($this->column_data['label']) . "' value='" . esc_html($date) . "'>" . sprintf('%s', $clear_button);
    }

    private function time_field()
    {
        $date = (!empty($this->order[$this->decoded_column_key])) ? date('H:i', strtotime($this->order[$this->decoded_column_key])) : '';
        $clear_button = "<button type='button' class='wobef-clear-date-btn wobef-inline-edit-clear-date' data-field='" . esc_attr($this->column_key) . "' data-field-type='" . esc_attr($this->field_type) . "' data-item-id='" . esc_attr($this->order['id']) . "' value=''><img src='" . esc_url(WOBEF_IMAGES_URL . 'calendar_clear.svg') . "' alt='Clear' title='Clear Date'></button>";
        return "<input type='text' class='wobef-timepicker wobef-inline-edit-action' data-field='" . esc_attr($this->column_key) . "' data-field-type='" . esc_attr($this->field_type) . "' data-item-id='" . esc_attr($this->order['id']) . "' title='Select " . esc_attr($this->column_data['label']) . "' value='" . esc_html($date) . "'>" . sprintf('%s', $clear_button);
    }

    private function color_field()
    {
        return "<input type='text' class='wobef-color-picker-field wobef-inline-edit-action' data-field='" . esc_attr($this->column_key) . "' data-field-type='" . esc_attr($this->field_type) . "' data-item-id='" . esc_attr($this->order['id']) . "' title='Select " . esc_attr($this->column_data['label']) . "' value='" . esc_html($this->order[$this->decoded_column_key]) . "'><button type='button' class='wobef-inline-edit-color-action'>" . esc_html__('Apply', 'ithemeland-woocommerce-bulk-orders-editing-lite') . "</button>";
    }

    private function order_details_field()
    {
        return "<button type='button' data-toggle='modal' data-target='#wobef-modal-order-details' class='wobef-button wobef-button-white wobef-td160 wobef-order-details-button' data-item-id='" . esc_attr($this->order['id']) . "' data-item-name='#" . esc_attr($this->order['id']) . "' data-field='" . esc_attr($this->column_key) . "' data-field-type='" . esc_attr($this->field_type) . "'>" . esc_html__("Details", 'ithemeland-woocommerce-bulk-orders-editing-lite') . "</button>";
    }

    private function all_billing_field()
    {
        return "<button type='button' data-toggle='modal' data-target='#wobef-modal-order-billing' class='wobef-button wobef-button-white wobef-td160 wobef-order-billing-button' data-item-id='" . esc_attr($this->order['id']) . "' data-item-name='#" . esc_attr($this->order['id']) . "' data-field='" . esc_attr($this->column_key) . "' data-field-type='" . esc_attr($this->field_type) . "'>" . esc_html__("All Billing", 'ithemeland-woocommerce-bulk-orders-editing-lite') . "</button>";
    }

    private function all_shipping_field()
    {
        return "<button type='button' data-toggle='modal' data-target='#wobef-modal-order-shipping' class='wobef-button wobef-button-white wobef-td160 wobef-order-shipping-button' data-item-id='" . esc_attr($this->order['id']) . "' data-item-name='#" . esc_attr($this->order['id']) . "' data-field='" . esc_attr($this->column_key) . "' data-field-type='" . esc_attr($this->field_type) . "'>" . esc_html__("All Shipping", 'ithemeland-woocommerce-bulk-orders-editing-lite') . "</button>";
    }

    private function order_notes_field()
    {
        return "<button type='button' data-toggle='modal' data-target='#wobef-modal-order-notes' class='wobef-button wobef-button-white wobef-td160 wobef-order-notes-button' data-item-id='" . esc_attr($this->order['id']) . "' data-item-name='#" . esc_attr($this->order['id']) . "' data-field='" . esc_attr($this->column_key) . "' data-field-type='" . esc_attr($this->field_type) . "'>" . esc_html__("Order Notes", 'ithemeland-woocommerce-bulk-orders-editing-lite') . "</button>";
    }

    private function address_field()
    {
        return "<button type='button' data-toggle='modal' data-target='#wobef-modal-order-address' data-field='" . esc_attr($this->column_key) . "' class='wobef-button wobef-button-white wobef-td160 wobef-order-address' data-item-id='" . esc_attr($this->order['id']) . "' data-item-name='#" . esc_attr($this->order['id']) . "' data-field='" . esc_attr($this->column_key) . "' data-field-type='" . esc_attr($this->field_type) . "'>" . esc_html__("Show Address", 'ithemeland-woocommerce-bulk-orders-editing-lite') . "</button>";
    }

    private function order_items_field()
    {
        if (isset($this->settings['show_order_items_popup']) && $this->settings['show_order_items_popup'] == 'yes') {
            return "<button type='button' data-toggle='modal' data-target='#wobef-modal-order-items' class='wobef-button wobef-button-white wobef-td160 wobef-order-items' data-item-id='" . esc_attr($this->order['id']) . "' data-item-name='#" . esc_attr($this->order['id']) . "' data-field='" . esc_attr($this->column_key) . "' data-field-type='" . esc_attr($this->field_type) . "'>" . esc_html__("Order Items", 'ithemeland-woocommerce-bulk-orders-editing-lite') . "</button>";
        } else {
            return $this->order['order_items'];
        }
    }

    private function customer_field()
    {
        $customer_id = $this->order_object->get_customer_id();
        $modal = (isset($this->settings['show_customer_details']) && $this->settings['show_customer_details'] == 'yes') ? "data-toggle='modal' data-target='#wobef-modal-customer-details' data-customer-id='" . $customer_id . "'" : '';
        return "<a href='javascript:;'  class='wobef-td160 wobef-customer-details' {$modal} data-item-id='" . esc_attr($this->order['id']) . "' data-item-name='" . esc_Attr($this->order[$this->column_key]) . "' data-field='" . esc_attr($this->column_key) . "' data-field-type='" . esc_attr($this->field_type) . "'>" . esc_html($this->order[$this->column_key]) . "</a>";
    }
}

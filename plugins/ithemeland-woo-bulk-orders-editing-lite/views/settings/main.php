<div class="wobef-wrap">
    <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
        <input type="hidden" name="action" value="wobef_settings">
        <div class="wobef-tab-middle-content">
            <div class="wobef-alert wobef-alert-default">
                <span><?php esc_html_e('You can set bulk editor settings', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?></span>
            </div>
            <?php if (!empty($flush_message) && is_array($flush_message) && $flush_message['hash'] == 'settings') : ?>
                <?php include WOBEF_VIEWS_DIR . "alerts/flush_message.php"; ?>
            <?php endif; ?>
            <div class="wobef-form-group">
                <label for="wobef-settings-count-per-page"><?php esc_html_e('Count Per Page', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?></label>
                <select name="settings[count_per_page]" id="wobef-quick-per-page" title="The number of orders per page">
                    <?php foreach (\wobef\classes\helpers\Setting::get_count_per_page_items() as $count_per_page_item) : ?>
                        <option value="<?php echo intval(esc_attr($count_per_page_item)); ?>" <?php if (isset($settings['count_per_page']) && $settings['count_per_page'] == intval($count_per_page_item)) : ?> selected <?php endif; ?>>
                            <?php echo esc_html($count_per_page_item); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="wobef-form-group">
                <label for="wobef-settings-default-sort-by"><?php esc_html_e('Default Sort By', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?></label>
                <select id="wobef-settings-default-sort-by" class="wobef-input-md" name="settings[default_sort_by]">
                    <option value="id" <?php echo (isset($settings['default_sort_by']) && $settings['default_sort_by'] == 'id') ? 'selected' : ''; ?>>
                        <?php esc_html_e('ID', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?>
                    </option>
                </select>
            </div>
            <div class="wobef-form-group">
                <label for="wobef-settings-default-sort"><?php esc_html_e('Default Sort', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?></label>
                <select name="settings[default_sort]" id="wobef-settings-default-sort" class="wobef-input-md">
                    <option value="ASC" <?php echo (isset($settings['default_sort']) && $settings['default_sort'] == 'ASC') ? 'selected' : ''; ?>>
                        <?php esc_html_e('ASC', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?>
                    </option>
                    <option value="DESC" <?php echo (isset($settings['default_sort']) && $settings['default_sort'] == 'DESC') ? 'selected' : ''; ?>>
                        <?php esc_html_e('DESC', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?>
                    </option>
                </select>
            </div>
            <div class="wobef-form-group">
                <label for="wobef-settings-show-quick-search"><?php esc_html_e('Show Quick Search', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?></label>
                <select name="settings[show_quick_search]" id="wobef-settings-show-quick-search" class="wobef-input-md">
                    <option value="yes" <?php echo (isset($settings['show_quick_search']) && $settings['show_quick_search'] == 'yes') ? 'selected' : ''; ?>>
                        <?php esc_html_e('Yes', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?>
                    </option>
                    <option value="no" <?php echo (isset($settings['show_quick_search']) && $settings['show_quick_search'] == 'no') ? 'selected' : ''; ?>>
                        <?php esc_html_e('No', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?>
                    </option>
                </select>
            </div>
            <div class="wobef-form-group">
                <label for="wobef-settings-sticky-search-form"><?php esc_html_e('Search Form Mode', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?></label>
                <select name="settings[sticky_search_form]" id="wobef-settings-sticky-search-form" class="wobef-input-md">
                    <option value="yes" <?php echo (isset($settings['sticky_search_form']) && $settings['sticky_search_form'] == 'yes') ? 'selected' : ''; ?>>
                        <?php esc_html_e('Don\'t Push Down the Content', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?>
                    </option>
                    <option value="no" <?php echo (isset($settings['sticky_search_form']) && $settings['sticky_search_form'] == 'no') ? 'selected' : ''; ?>>
                        <?php esc_html_e('Push Down the Content', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?>
                    </option>
                </select>
            </div>
            <div class="wobef-form-group">
                <label for="wobef-settings-sticky-first-columns"><?php esc_html_e("Sticky 'ID' Column", 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?></label>
                <select name="settings[sticky_first_columns]" id="wobef-settings-sticky-first-columns" class="wobef-input-md">
                    <option value="yes" <?php echo (isset($settings['sticky_first_columns']) && $settings['sticky_first_columns'] == 'yes') ? 'selected' : ''; ?>>
                        <?php esc_html_e('Yes', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?>
                    </option>
                    <option value="no" <?php echo (isset($settings['sticky_first_columns']) && $settings['sticky_first_columns'] == 'no') ? 'selected' : ''; ?>>
                        <?php esc_html_e('No', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?>
                    </option>
                </select>
            </div>
            <div class="wobef-form-group">
                <label for="wobef-settings-display-full-columns-title"><?php esc_html_e('Display Columns Label', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?></label>
                <select name="settings[display_full_columns_title]" id="wobef-settings-display-full-columns-title" class="wobef-input-md">
                    <option value="yes" <?php echo (isset($settings['display_full_columns_title']) && $settings['display_full_columns_title'] == 'yes') ? 'selected' : ''; ?>>
                        <?php esc_html_e('Completely', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?>
                    </option>
                    <option value="no" <?php echo (isset($settings['display_full_columns_title']) && $settings['display_full_columns_title'] == 'no') ? 'selected' : ''; ?>>
                        <?php esc_html_e('In short', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?>
                    </option>
                </select>
            </div>
            <div class="wobef-form-group">
                <label for="wobef-settings-show-customer-details"><?php esc_html_e('Customer Details Column', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?></label>
                <select name="settings[show_customer_details]" id="wobef-settings-show-customer-details" class="wobef-input-md">
                    <option value="yes" <?php echo (isset($settings['show_customer_details']) && $settings['show_customer_details'] == 'yes') ? 'selected' : ''; ?>>
                        <?php esc_html_e('Show as popup', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?>
                    </option>
                    <option value="no" <?php echo (isset($settings['show_customer_details']) && $settings['show_customer_details'] == 'no') ? 'selected' : ''; ?>>
                        <?php esc_html_e('Show as text', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?>
                    </option>
                </select>
            </div>
            <div class="wobef-form-group">
                <label for="wobef-settings-show-order-items-popup"><?php esc_html_e('Order Items Column', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?></label>
                <select name="settings[show_order_items_popup]" id="wobef-settings-show-order-items-popup" class="wobef-input-md">
                    <option value="yes" <?php echo (isset($settings['show_order_items_popup']) && $settings['show_order_items_popup'] == 'yes') ? 'selected' : ''; ?>>
                        <?php esc_html_e('Show as popup', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?>
                    </option>
                    <option value="no" <?php echo (isset($settings['show_order_items_popup']) && $settings['show_order_items_popup'] == 'no') ? 'selected' : ''; ?>>
                        <?php esc_html_e('Show as text', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?>
                    </option>
                </select>
            </div>
            <div class="wobef-form-group">
                <label for="wobef-settings-show-billing-shipping-address-popup"><?php esc_html_e('Billing/Shipping Address', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?></label>
                <select name="settings[show_billing_shipping_address_popup]" id="wobef-settings-show-billing-shipping-address-popup" class="wobef-input-md">
                    <option value="yes" <?php echo (isset($settings['show_billing_shipping_address_popup']) && $settings['show_billing_shipping_address_popup'] == 'yes') ? 'selected' : ''; ?>>
                        <?php esc_html_e('Show as popup', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?>
                    </option>
                    <option value="no" <?php echo (isset($settings['show_billing_shipping_address_popup']) && $settings['show_billing_shipping_address_popup'] == 'no') ? 'selected' : ''; ?>>
                        <?php esc_html_e('Show as text', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?>
                    </option>
                </select>
            </div>
            <div class="wobef-form-group">
                <label for="wobef-settings-colorize-status-column"><?php esc_html_e("Colorize 'Status' Column", 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?></label>
                <select name="settings[colorize_status_column]" id="wobef-settings-colorize-status-column" class="wobef-input-md">
                    <option value="yes" <?php echo (isset($settings['colorize_status_column']) && $settings['colorize_status_column'] == 'yes') ? 'selected' : ''; ?>>
                        <?php esc_html_e('Yes', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?>
                    </option>
                    <option value="no" <?php echo (isset($settings['colorize_status_column']) && $settings['colorize_status_column'] == 'no') ? 'selected' : ''; ?>>
                        <?php esc_html_e('No', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?>
                    </option>
                </select>
            </div>
        </div>
        <div class="wobef-tab-footer">
            <div class="wobef-tab-footer-left">
                <button type="submit" class="wobef-button wobef-button-lg wobef-button-blue">
                    <?php $img = WOBEF_IMAGES_URL . 'save.svg'; ?>
                    <img src="<?php echo esc_url($img); ?>" alt="">
                    <span><?php esc_html_e('Save Changes', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?></span>
                </button>
            </div>
            <div class="clear"></div>
        </div>
    </form>
</div>
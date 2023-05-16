<div class="wobef-wrap">
    <div class="wobef-tab-middle-content">
        <div class="wobef-alert wobef-alert-default">
            <span><?php esc_html_e('Import/Export orders as CSV files', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?>.</span>
        </div>
        <?php if (!empty($flush_message) && is_array($flush_message) && $flush_message['hash'] == 'import-export') : ?>
            <?php include WOBEF_VIEWS_DIR . "alerts/flush_message.php"; ?>
        <?php endif; ?>
        <div class="wobef-export">
            <form action="<?php echo esc_url(admin_url("admin-post.php")); ?>" method="post">
                <input type="hidden" name="action" value="wobef_export_orders">
                <div id="wobef-export-items-selected"></div>
                <div class="wobef-export-fields">
                    <div class="wobef-export-field-item">
                        <strong class="label"><?php esc_html_e('Orders', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?></strong>
                        <label class="wobef-export-radio">
                            <input type="radio" name="orders" value="all" checked="checked" id="wobef-export-all-items-in-table">
                            <?php esc_html_e('All Orders In Table', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?>
                        </label>
                        <label class="wobef-export-radio">
                            <input type="radio" name="orders" id="wobef-export-only-selected-items" value="selected" disabled="disabled">
                            <?php esc_html_e('Only Selected orders', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?>
                        </label>
                    </div>
                    <div class="wobef-export-field-item">
                        <strong class="label"><?php esc_html_e('Fields', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?></strong>
                        <label class="wobef-export-radio">
                            <input type="radio" name="fields" value="all" checked="checked">
                            <?php esc_html_e('All Fields', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?>
                        </label>
                        <label class="wobef-export-radio">
                            <input type="radio" name="fields" value="visible">
                            <?php esc_html_e('Only Visible Fields', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?>
                        </label>
                    </div>
                    <div class="wobef-export-field-item">
                        <label class="label" for="wobef-export-delimiter"><?php esc_html_e('Delimiter', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?></label>
                        <select name="wobef-export-delimiter" id="wobef-export-delimiter">
                            <option value=",">,</option>
                        </select>
                    </div>
                </div>
                <div class="wobef-export-buttons">
                    <div class="wobef-export-buttons-left">
                        <button type="submit" class="wobef-button wobef-button-lg wobef-button-blue" id="wobef-export-orders">
                            <i class="lni lni-funnel"></i>
                            <span><?php esc_html_e('Export Now', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?></span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <div class="wobef-import">
            <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post" enctype="multipart/form-data">
                <input type="hidden" name="action" value="wobef_import_orders">
                <div class="wobef-import-content">
                    <p><?php esc_html_e("If you have orders in another system, you can import those into this site. ", 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?></p>
                    <input type="file" name="import_file" required>
                </div>
                <div class="wobef-import-buttons">
                    <div class="wobef-import-buttons-left">
                        <button type="submit" name="import" class="wobef-button wobef-button-lg wobef-button-blue">
                            <i class="lni lni-funnel"></i>
                            <span><?php esc_html_e('Import Now', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?></span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="wobef-wrap">
    <div class="wobef-tab-middle-content">
        <div class="wobef-alert wobef-alert-default">
            <span><?php esc_html_e('List of your changes and possible to roll back to the previous data', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?></span>
        </div>
        <div class="wobef-alert wobef-alert-danger">
            <span class="wobef-lh36">This option is not available in Free Version, Please upgrade to Pro Version</span>
            <a href="<?php echo esc_url(WOBEF_UPGRADE_URL); ?>"><?php echo esc_html(WOBEF_UPGRADE_TEXT); ?></a>
        </div>
        <?php if (!empty($flush_message) && is_array($flush_message) && $flush_message['hash'] == 'history') : ?>
            <?php include WOBEF_VIEWS_DIR . "alerts/flush_message.php"; ?>
        <?php endif; ?>
        <div class="wobef-history-filter">
            <div class="wobef-history-filter-fields">
                <div class="wobef-history-filter-field-item">
                    <label for="wobef-history-filter-operation"><?php esc_html_e('Operation', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?></label>
                    <select id="wobef-history-filter-operation">
                        <option value=""><?php esc_html_e('Select', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?></option>
                        <?php if (!empty($history_types = \wobef\classes\repositories\History::get_operation_types())) : ?>
                            <?php foreach ($history_types as $history_type_key => $history_type_label) : ?>
                                <option value="<?php echo esc_attr($history_type_key); ?>"><?php echo esc_html($history_type_label); ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="wobef-history-filter-field-item">
                    <label for="wobef-history-filter-author"><?php esc_html_e('Author', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?></label>
                    <select id="wobef-history-filter-author">
                        <option value=""><?php esc_html_e('Select', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?></option>
                        <?php if (!empty($users)) : ?>
                            <?php foreach ($users as $user_item) : ?>
                                <option value="<?php echo esc_attr($user_item->ID); ?>"><?php echo esc_html($user_item->user_login); ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="wobef-history-filter-field-item">
                    <label for="wobef-history-filter-fields"><?php esc_html_e('Fields', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?></label>
                    <input type="text" id="wobef-history-filter-fields" placeholder="for example: ID">
                </div>
                <div class="wobef-history-filter-field-item wobef-history-filter-field-date">
                    <label><?php esc_html_e('Date', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?></label>
                    <input type="text" id="wobef-history-filter-date-from" class="wobef-datepicker wobef-date-from" data-to-id="wobef-history-filter-date-to" placeholder="<?php esc_html_e('From ...', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?>">
                    <input type="text" id="wobef-history-filter-date-to" class="wobef-datepicker" placeholder="<?php esc_html_e('To ...', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?>">
                </div>
            </div>
            <div class="wobef-history-filter-buttons">
                <div class="wobef-history-filter-buttons-left">
                    <button type="button" class="wobef-button wobef-button-lg wobef-button-blue" disabled="disabled">
                        <i class="lni lni-funnel"></i>
                        <span><?php esc_html_e('Apply Filters', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?></span>
                    </button>
                    <button type="button" class="wobef-button wobef-button-lg wobef-button-gray" disabled="disabled">
                        <i class="lni lni-spinner-arrow"></i>
                        <span><?php esc_html_e('Reset Filters', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?></span>
                    </button>
                </div>
                <div class="wobef-history-filter-buttons-right">
                    <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post" id="wobef-history-clear-all">
                        <input type="hidden" name="action" value="<?php echo esc_attr($clear_all_history); ?>">
                        <button type="button" name="clear_all" value="1" disabled="disabled" class="wobef-button wobef-button-lg wobef-button-red">
                            <i class="lni lni-trash"></i>
                            <span><?php esc_html_e('Clear History', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?></span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="wobef-history-items">
            <h3><?php esc_html_e('Column(s)', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?></h3>
            <div class="wobef-table-border-radius">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th><?php esc_html_e('History Name', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?></th>
                            <th><?php esc_html_e('Author', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?></th>
                            <th class="wobef-mw125"><?php esc_html_e('Date Modified', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?></th>
                            <th class="wobef-mw250"><?php esc_html_e('Actions', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php include 'history_items.php'; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
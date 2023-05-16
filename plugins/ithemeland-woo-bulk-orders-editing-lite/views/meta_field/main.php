<div class="wobef-wrap">
    <div class="wobef-tab-middle-content">
        <div class="wobef-alert wobef-alert-default">
            <span><?php esc_html_e('You can add new orders meta fields in two ways: 1- Individually 2- Get from other order.', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?></span>
        </div>
        <?php if (!empty($flush_message) && is_array($flush_message) && $flush_message['hash'] == 'meta-fields') : ?>
            <?php include WOBEF_VIEWS_DIR . "alerts/flush_message.php"; ?>
        <?php endif; ?>
        <div class="wobef-meta-fields-left">
            <div class="wobef-meta-fields-manual">
                <label for="wobef-meta-fields-manual_key_name"><?php esc_html_e('Manually', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?></label>
                <div class="wobef-meta-fields-manual-field">
                    <input type="text" id="wobef-meta-fields-manual_key_name" placeholder="<?php esc_html_e('Enter Meta Key ...', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?>">
                    <button type="button" class="wobef-button wobef-button-square wobef-button-blue" id="wobef-add-meta-field-manual">
                        <i class="lni lni-plus wobef-m0"></i>
                    </button>
                </div>
            </div>
            <div class="wobef-meta-fields-automatic">
                <label for="wobef-add-meta-fields-order-id"><?php esc_html_e('Automatically From Order', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?></label>
                <div class="wobef-meta-fields-automatic-field">
                    <input type="text" id="wobef-add-meta-fields-order-id" placeholder="<?php esc_html_e('Enter Order ID ...', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?>">
                    <button type="button" class="wobef-button wobef-button-square wobef-button-blue" id="wobef-get-meta-fields-by-order-id">
                        <i class="lni lni-plus wobef-m0"></i>
                    </button>
                </div>
            </div>
        </div>
        <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
            <input type="hidden" name="action" value="wobef_meta_fields">
            <div class="wobef-meta-fields-right" id="wobef-meta-fields-items">
                <p class="wobef-meta-fields-empty-text" <?php echo (!empty($meta_fields)) ? 'style="display:none";' : ''; ?>><?php esc_html_e("Please add your meta key manually", 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?><br> <?php esc_html_e("OR", 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?><br><?php esc_html_e("From another order", 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?></p>
                <?php if (!empty($meta_fields)) : ?>
                    <?php foreach ($meta_fields as $meta_field) : ?>
                        <?php include WOBEF_VIEWS_DIR . 'meta_field/meta_field_item.php'; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
                <div class="droppable-helper"></div>
            </div>
            <div class="wobef-meta-fields-buttons">
                <div class="wobef-meta-fields-buttons-left">
                    <button type="submit" value="1" name="save_meta_fields" class="wobef-button wobef-button-lg wobef-button-blue">
                        <?php $img = WOBEF_IMAGES_URL . 'save.svg'; ?>
                        <img src="<?php echo esc_url($img); ?>" alt="">
                        <span><?php esc_html_e('Save Fields', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?></span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
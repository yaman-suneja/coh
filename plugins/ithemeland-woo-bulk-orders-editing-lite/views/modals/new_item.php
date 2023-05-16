<div class="wobef-modal" id="wobef-modal-new-item">
    <div class="wobef-modal-container">
        <div class="wobef-modal-box wobef-modal-box-sm">
            <div class="wobef-modal-content">
                <div class="wobef-modal-title">
                    <h2 id="wobef-new-item-title"></h2>
                    <button type="button" class="wobef-modal-close" data-toggle="modal-close">
                        <i class="lni lni-close"></i>
                    </button>
                </div>
                <div class="wobef-modal-body">
                    <div class="wobef-wrap">
                        <div class="wobef-form-group">
                            <label class="wobef-label-big" for="wobef-new-item-count" id="wobef-new-item-description"></label>
                            <input type="number" class="wobef-input-numeric-sm wobef-m0" id="wobef-new-item-count" value="1" placeholder="<?php esc_html_e('Number ...', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?>">
                        </div>
                        <div id="wobef-new-item-extra-fields">
                            <?php if (!empty($new_item_extra_fields)) : ?>
                                <?php foreach ($new_item_extra_fields as $extra_field) : ?>
                                    <div class="wobef-form-group">
                                        <?php echo sprintf("%s", $extra_field['label']); ?>
                                        <?php echo sprintf("%s", $extra_field['field']); ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="wobef-modal-footer">
                    <button type="button" class="wobef-button wobef-button-blue" id="wobef-create-new-item"><?php esc_html_e('Create', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?></button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="wobef-modal" id="wobef-modal-text-editor">
    <div class="wobef-modal-container">
        <div class="wobef-modal-box wobef-modal-box-lg">
            <div class="wobef-modal-content">
                <div class="wobef-modal-title">
                    <h2><?php esc_html_e('Content Edit', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?> - <span id="wobef-modal-text-editor-item-title" class="wobef-modal-item-title"></span></h2>
                    <button type="button" class="wobef-modal-close" data-toggle="modal-close">
                        <i class="lni lni-close"></i>
                    </button>
                </div>
                <div class="wobef-modal-body">
                    <div class="wobef-wrap">
                        <?php wp_editor("", 'wobef-text-editor'); ?>
                    </div>
                </div>
                <div class="wobef-modal-footer">
                    <button type="button" data-field="" data-item-id="" data-content-type="textarea" id="wobef-text-editor-apply" class="wobef-button wobef-button-blue wobef-edit-action-with-button" data-toggle="modal-close">
                        <?php esc_html_e('Apply Changes', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
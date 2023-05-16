<div class="wobef-modal" id="wobef-modal-bulk-edit">
    <div class="wobef-modal-container">
        <div class="wobef-modal-box wobef-modal-box-lg">
            <div class="wobef-modal-content">
                <div class="wobef-modal-title">
                    <h2><?php esc_html_e('Bulk Edit Form', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?></h2>
                    <button type="button" class="wobef-modal-close" data-toggle="modal-close">
                        <i class="lni lni-close"></i>
                    </button>
                </div>
                <div class="wobef-modal-body">
                    <div class="wobef-wrap">
                        <div class="wobef-tabs">
                            <div class="wobef-tabs-navigation">
                                <nav class="wobef-tabs-navbar">
                                    <ul class="wobef-tabs-list" data-content-id="wobef-bulk-edit-tabs">
                                        <?php if (!empty($bulk_edit_form_tabs_title) && is_array($bulk_edit_form_tabs_title)) : ?>
                                            <?php $bulk_edit_tab_title_counter = 1; ?>
                                            <?php foreach ($bulk_edit_form_tabs_title as $tab_key => $tab_label) : ?>
                                                <li><a class="<?php echo ($bulk_edit_tab_title_counter == 1) ? 'selected' : ''; ?>" data-content="<?php echo $tab_key; ?>" href="#"><?php echo $tab_label; ?></a></li>
                                                <?php $bulk_edit_tab_title_counter++; ?>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </ul>
                                </nav>
                            </div>
                            <div class="wobef-tabs-contents wobef-mt30" id="wobef-bulk-edit-tabs">
                                <?php if (!empty($bulk_edit_form_tabs_content)) : ?>
                                    <?php foreach ($bulk_edit_form_tabs_content as $tab_key => $bulk_edit_tab) : ?>
                                        <?php echo (!empty($bulk_edit_tab['wrapper_start'])) ? $bulk_edit_tab['wrapper_start'] : ''; ?>
                                        <?php
                                        if (!empty($bulk_edit_tab['fields_top']) && is_array($bulk_edit_tab['fields_top'])) {
                                            foreach ($bulk_edit_tab['fields_top'] as $top_item) {
                                                echo sprintf('%s', $top_item);
                                            }
                                        }
                                        ?>
                                        <?php if (!empty($bulk_edit_tab['tabs_titles'])) : ?>
                                            <ul class="wobef-sub-tab-titles">
                                                <?php foreach ($bulk_edit_tab['tabs_titles'] as $sub_tab_key => $sub_tab_title) : ?>
                                                    <li><a href="javascript:;" class="wobef-sub-tab-title" data-content="<?php echo sanitize_text_field($sub_tab_key); ?>"><?php echo sanitize_text_field($sub_tab_title); ?></a></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php endif; ?>
                                        <?php if (!empty($bulk_edit_tab['tabs_content'])) : ?>
                                            <div class="wobef-sub-tab-contents">
                                                <?php foreach ($bulk_edit_tab['tabs_content'] as $sub_tab_content_key => $fields_group) : ?>
                                                    <div class="wobef-sub-tab-content" data-content="<?php echo sanitize_text_field($sub_tab_content_key); ?>">
                                                        <?php
                                                        if (!empty($fields_group) && is_array($fields_group)) :
                                                            foreach ($fields_group as $group_key => $fields) :
                                                                if (!empty($fields) && is_array($fields)) :
                                                                    if (!empty($fields['header']) && is_array($fields['header'])) {
                                                                        foreach ($fields['header'] as $header_item) {
                                                                            echo sprintf('%s', $header_item);
                                                                        }
                                                                    }
                                                                    foreach ($fields as $field_items) : ?>
                                                                        <div class="wobef-form-group" <?php echo (!empty($field_items['wrap_attributes'])) ? sanitize_text_field($field_items['wrap_attributes']) : ''; ?>>
                                                                            <?php
                                                                            if (!empty($field_items['html']) && is_array($field_items['html'])) :
                                                                                foreach ($field_items['html'] as $field) {
                                                                                    echo sprintf('%s', $field);
                                                                                }
                                                                            endif;
                                                                            ?>
                                                                        </div>
                                                        <?php
                                                                    endforeach;
                                                                    if (!empty($fields['footer']) && is_array($fields['footer'])) {
                                                                        foreach ($fields['footer'] as $footer_item) {
                                                                            echo sprintf('%s', $footer_item);
                                                                        }
                                                                    }
                                                                endif;
                                                            endforeach;
                                                        endif;
                                                        ?>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (!empty($bulk_edit_tab['fields']) && is_array($bulk_edit_tab['fields'])) : ?>
                                            <?php foreach ($bulk_edit_tab['fields'] as $field_key => $field_items) : ?>
                                                <?php
                                                if (!empty($field_items) && is_array($field_items)) : ?>
                                                    <div class="wobef-form-group" <?php echo (!empty($field_items['wrap_attributes'])) ? sanitize_text_field($field_items['wrap_attributes']) : ''; ?>>
                                                        <div>
                                                            <?php
                                                            if (!empty($field_items['header']) && is_array($field_items['header'])) {
                                                                foreach ($field_items['header'] as $header_item) {
                                                                    echo sprintf('%s', $header_item);
                                                                }
                                                            }

                                                            if (!empty($field_items['html']) && is_array($field_items['html'])) :
                                                                foreach ($field_items['html'] as $field_html) :
                                                                    echo sprintf('%s', $field_html);
                                                                endforeach;
                                                            endif;

                                                            if (!empty($field_items['footer']) && is_array($field_items['footer'])) {
                                                                foreach ($field_items['footer'] as $footer_item) {
                                                                    echo sprintf('%s', $footer_item);
                                                                }
                                                            }
                                                            ?>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                        <?php echo (!empty($bulk_edit_tab['wrapper_end'])) ? $bulk_edit_tab['wrapper_end'] : ''; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="wobef-modal-footer">
                    <button type="button" class="wobef-button wobef-button-lg wobef-button-blue" id="wobef-bulk-edit-form-do-bulk-edit">
                        <?php esc_html_e('Do Bulk Edit', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?>
                    </button>
                    <button type="button" class="wobef-button wobef-button-lg wobef-button-white" id="wobef-bulk-edit-form-reset">
                        <?php esc_html_e('Reset Form', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
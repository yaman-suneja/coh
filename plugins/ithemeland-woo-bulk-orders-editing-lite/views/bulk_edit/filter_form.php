<div id="wobef-filter-form" <?php echo (isset($settings['sticky_search_form']) && $settings['sticky_search_form'] == 'no') ? 'style="position:static"' : '' ?>>
    <div id="wobef-filter-form-content" class="wobef-hide" data-visibility="hidden">
        <input type="hidden" id="filter-form-changed" value="">
        <div class="wobef-wrap">
            <i class="lni lni-close wobef-filter-form-toggle" id="wobef-bulk-edit-filter-form-close-button"></i>
            <ul class="wobef-tabs-list" data-content-id="wobef-bulk-edit-filter-tabs-contents">
                <?php if (!empty($filter_form_tabs_title) && is_array($filter_form_tabs_title)) : ?>
                    <?php $filter_tab_title_counter = 1; ?>
                    <?php foreach ($filter_form_tabs_title as $tab_key => $tab_label) : ?>
                        <li><a class="<?php echo ($filter_tab_title_counter == 1) ? 'selected' : ''; ?>" data-content="<?php echo $tab_key; ?>" href="#"><?php echo $tab_label; ?></a></li>
                        <?php $filter_tab_title_counter++; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
            <div class="wobef-tabs-contents" id="wobef-bulk-edit-filter-tabs-contents">
                <?php if (!empty($filter_form_tabs_content)) : ?>
                    <?php foreach ($filter_form_tabs_content as $tab_key => $filter_tab) : ?>
                        <?php echo (!empty($filter_tab['wrapper_start'])) ? $filter_tab['wrapper_start'] : ''; ?>
                        <?php
                        if (!empty($filter_tab['fields_top']) && is_array($filter_tab['fields_top'])) {
                            foreach ($filter_tab['fields_top'] as $top_item) {
                                echo sprintf('%s', $top_item);
                            }
                        }
                        ?>
                        <?php if (!empty($filter_tab['fields']) && is_array($filter_tab['fields'])) : ?>
                            <?php foreach ($filter_tab['fields'] as $field_key => $field_items) : ?>
                                <?php if (!empty($field_items) && is_array($field_items)) : ?>
                                    <div class="wobef-form-group" data-name="<?php echo sanitize_text_field($field_key); ?>">
                                        <?php foreach ($field_items as $field_html) : ?>
                                            <?php echo sprintf('%s', $field_html); ?>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <?php echo (!empty($filter_tab['wrapper_end'])) ? $filter_tab['wrapper_end'] : ''; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="wobef-tab-footer">
                <div class="wobef-tab-footer-left">
                    <button type="button" id="wobef-filter-form-get-orders" class="wobef-button wobef-button-lg wobef-button-blue wobef-filter-form-action" data-search-action="pro_search">
                        <?php esc_html_e('Get orders', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?>
                    </button>
                    <button type="button" class="wobef-button wobef-button-lg wobef-button-white" id="wobef-filter-form-reset">
                        <?php esc_html_e('Reset Filters', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?>
                    </button>
                </div>
                <div class="wobef-tab-footer-right">
                    <input type="text" name="save_filter" id="wobef-filter-form-save-preset-name" placeholder="Filter Name ..." class="wobef-h50" title="Filter Name">
                    <button type="button" id="wobef-filter-form-save-preset" class="wobef-button wobef-button-lg wobef-button-blue">
                        <?php esc_html_e('Save Profile', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?>
                    </button>
                </div>
                <div class="clear"></div>
            </div>
        </div>
    </div>
    <div class="wobef-filter-form-button">
        <a class="wobef-filter-form-toggle">
            <span class="lni lni-funnel wobef-mr5"></span>
            <?php esc_html_e('Filter Form', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?>
            <span class="lni lni-chevron-down wobef-ml5 wobef-filter-form-icon"></span>
        </a>
    </div>
</div>
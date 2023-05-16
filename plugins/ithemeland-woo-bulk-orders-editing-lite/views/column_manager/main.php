<div class="wobef-wrap">
    <div class="wobef-tab-middle-content">
        <div class="wobef-alert wobef-alert-default">
            <span><?php esc_html_e('Mange columns of table. You can Create your customize presets and use them in column profile section.', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?></span>
        </div>
        <div class="wobef-alert wobef-alert-danger">
            <span class="wobef-lh36">This option is not available in Free Version, Please upgrade to Pro Version</span>
            <a href="<?php echo esc_url(WOBEF_UPGRADE_URL); ?>"><?php echo esc_html(WOBEF_UPGRADE_TEXT); ?></a>
        </div>
        <?php if (!empty($flush_message) && is_array($flush_message) && $flush_message['hash'] == 'column-manager') : ?>
            <?php include WOBEF_VIEWS_DIR . "alerts/flush_message.php"; ?>
        <?php endif; ?>
        <div class="wobef-column-manager-items">
            <h3><?php esc_html_e('Column Profiles', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?></h3>
            <div class="wobef-table-border-radius">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th><?php esc_html_e('Profile Name', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?></th>
                            <th><?php esc_html_e('Date Modified', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?></th>
                            <th><?php esc_html_e('Actions', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($column_manager_presets)) : ?>
                            <?php $i = 1 ?>
                            <?php foreach ($column_manager_presets as $key => $column_manager_preset) : ?>
                                <tr>
                                    <td><?php echo esc_html($i); ?></td>
                                    <td>
                                        <span class="wobef-history-name"><?php echo (isset($column_manager_preset['name'])) ? esc_html($column_manager_preset['name']) : ''; ?></span>
                                    </td>
                                    <td><?php echo (isset($column_manager_preset['date_modified'])) ? esc_html(date('d M Y', strtotime($column_manager_preset['date_modified']))) : ''; ?></td>
                                    <td>
                                        <?php if (!in_array($key, \wobef\classes\repositories\Column::get_default_columns_name())) : ?>
                                            <button type="button" class="wobef-button wobef-button-blue wobef-column-manager-edit-field-btn" value="" disabled="disabled">
                                                <i class="lni lni-pencil"></i>
                                                <?php esc_html_e('Edit', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?>
                                            </button>
                                            <button type="button" class="wobef-button wobef-button-red wobef-column-manager-delete-preset" value="" disabled="disabled">
                                                <i class="lni lni-trash"></i>
                                                <?php esc_html_e('Delete', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?>
                                            </button>
                                        <?php else : ?>
                                            <i class="lni lni-lock"></i>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php $i++; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="wobef-column-manager-new-profile">
            <h3 class="wobef-column-manager-section-title"><?php esc_html_e('Create New Profile', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?></h3>
            <div class="wobef-column-manager-new-profile-left">
                <input type="text" title="<?php esc_html_e('Search Field', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?>" data-action="new" placeholder="<?php esc_html_e('Search Field ...', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?>" class="wobef-column-manager-search-field">
                <div class="wobef-column-manager-available-fields" data-action="new">
                    <label class="wobef-column-manager-check-all-fields-btn" data-action="new">
                        <input type="checkbox" class="wobef-column-manager-check-all-fields">
                        <span><?php esc_html_e('Select All', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?></span>
                    </label>
                    <ul>
                        <?php if (!empty($column_items)) : ?>
                            <?php foreach ($column_items as $column_key => $column_field) : ?>
                                <li data-name="<?php echo esc_attr($column_key); ?>" data-added="false">
                                    <label>
                                        <input type="checkbox" data-type="field" data-name="<?php echo esc_attr($column_key); ?>" value="<?php echo esc_attr($column_field['label']); ?>">
                                        <?php echo esc_html($column_field['label']); ?>
                                    </label>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
            <div class="wobef-column-manager-new-profile-middle">
                <div class="wobef-column-manager-middle-buttons">
                    <div>
                        <button type="button" class="wobef-button wobef-button-lg wobef-button-square-lg wobef-button-blue" disabled="disabled">
                            <i class="lni lni-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="wobef-column-manager-new-profile-right">
                <div class="wobef-column-manager-right-top">
                    <input type="text" title="Profile Name" id="wobef-column-manager-new-preset-name" name="preset_name" placeholder="Profile name ..." required>
                    <button type="button" disabled="disabled" class="wobef-button wobef-button-lg wobef-button-blue">
                        <img src="<?php echo WOBEF_IMAGES_URL . 'save.svg'; ?>" alt="">
                        <?php esc_html_e('Save Preset', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?>
                    </button>
                </div>
                <div class="wobef-column-manager-added-fields-wrapper">
                    <p class="wobef-column-manager-empty-text"><?php esc_html_e('Please add your columns here', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?></p>
                    <div class="wobef-column-manager-added-fields" data-action="new">
                        <div class="items"></div>
                        <img src="<?php echo WOBEF_IMAGES_URL . 'loading.gif'; ?>" alt="" class="wobef-box-loading wobef-hide">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
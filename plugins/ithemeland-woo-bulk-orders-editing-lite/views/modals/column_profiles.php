<div class="wobef-modal" id="wobef-modal-column-profiles">
    <div class="wobef-modal-container">
        <div class="wobef-modal-box wobef-modal-box-lg">
            <div class="wobef-modal-content">
                <div class="wobef-modal-title">
                    <h2><?php esc_html_e('Column Profiles', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?></h2>
                    <button type="button" class="wobef-modal-close" data-toggle="modal-close">
                        <i class="lni lni-close"></i>
                    </button>
                </div>
                <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
                    <input type="hidden" name="action" value="<?php echo (!empty($column_profile_action_form)) ? $column_profile_action_form : ''; ?>">
                    <div class="wobef-modal-body">
                        <div class="wobef-wrap">
                            <div class="wobef-alert wobef-alert-default">
                                <span><?php esc_html_e('You can load saved column profile presets through Column Manager. You can change the columns and save your changes too.', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?></span>
                            </div>
                            <div class="wobef-column-profiles-choose">
                                <label for="wobef-column-profiles-choose"><?php esc_html_e('Choose Preset', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?></label>
                                <select id="wobef-column-profiles-choose" name="preset_key">
                                    <?php if (!empty($column_manager_presets)) : ?>
                                        <?php foreach ($column_manager_presets as $column_manager_preset) : ?>
                                            <?php if ($i == 0) {
                                                $first_key = $column_manager_preset['key'];
                                            } ?>
                                            <option value="<?php echo sanitize_text_field($column_manager_preset['key']); ?>" <?php echo (!empty($active_columns_key) && $active_columns_key == $column_manager_preset['key']) ? 'selected' : ''; ?>><?php echo sanitize_text_field($column_manager_preset['name']); ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <label class="wobef-column-profile-select-all">
                                    <input type="checkbox" id="wobef-column-profile-select-all" data-profile-name="<?php echo (!empty($active_columns_key)) ? sanitize_text_field($active_columns_key) : ''; ?>">
                                    <span><?php esc_html_e('Select All', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?></span>
                                </label>
                            </div>
                            <div class="wobef-column-profile-search">
                                <label for="wobef-column-profile-search"><?php esc_html_e('Search', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?> </label>
                                <input type="text" id="wobef-column-profile-search" placeholder="<?php esc_html_e('Search Column ...', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?>">
                            </div>
                            <div class="wobef-column-profiles-fields">
                                <?php if (!empty($grouped_fields)) :
                                    $compatibles = [];
                                    if (!empty($grouped_fields['compatibles'])) {
                                        $compatibles = $grouped_fields['compatibles'];
                                        unset($grouped_fields['compatibles']);
                                    }
                                ?>
                                    <div class="wobef-column-profile-fields">
                                        <?php foreach ($grouped_fields as $group_name => $column_fields) : ?>
                                            <?php if (!empty($column_fields)) : ?>
                                                <div class="wobef-column-profile-fields-group">
                                                    <div class="group-title">
                                                        <h3><?php echo sanitize_text_field($group_name); ?></h3>
                                                    </div>
                                                    <ul>
                                                        <?php foreach ($column_fields as $name => $column_field) : ?>
                                                            <li>
                                                                <label>
                                                                    <input type="checkbox" name="columns[]" value="<?php echo sanitize_text_field($name); ?>">
                                                                    <?php echo sanitize_text_field($column_field['label']); ?>
                                                                </label>
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                </div>
                                            <?php endif; ?>
                                        <?php
                                        endforeach;
                                        if (!empty($compatibles) && is_array($compatibles)) : ?>
                                            <div class="wobef-column-profile-compatibles-group">
                                                <strong class="wobef-column-profile-compatibles-group-title"><?php esc_html_e('Fields from certain third-party plugins', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?></strong>
                                                <?php foreach ($compatibles as $compatible_name => $compatible_fields) : ?>
                                                    <div class="wobef-column-profile-fields-group">
                                                        <div class="group-title">
                                                            <h3><?php echo sanitize_text_field($compatible_name); ?></h3>
                                                        </div>
                                                        <ul>
                                                            <?php foreach ($compatible_fields as $compatible_field_name => $compatible_field) : ?>
                                                                <li>
                                                                    <label>
                                                                        <input type="checkbox" name="columns[]" value="<?php echo sanitize_text_field($compatible_field_name); ?>">
                                                                        <?php echo sanitize_text_field($compatible_field['label']); ?>
                                                                    </label>
                                                                </li>
                                                            <?php endforeach; ?>
                                                        </ul>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="wobef-modal-footer">
                        <button type="submit" class="wobef-button wobef-button-blue wobef-float-left" id="wobef-column-profiles-apply" data-preset-key="<?php echo (!empty($first_key)) ? $first_key : ''; ?>">
                            <?php esc_html_e('Apply To Table', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?>
                        </button>
                        <div class="wobef-column-profile-save-dropdown" style="display: none">
                            <span>
                                <?php esc_html_e('Save Changes', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?>
                                <i class="lni lni-chevron-down"></i>
                            </span>
                            <div class="wobef-column-profile-save-dropdown-buttons">
                                <ul>
                                    <li id="wobef-column-profiles-update-changes" <?php echo (!empty($active_columns_key) && !empty($default_columns_name) && in_array($active_columns_key, $default_columns_name)) ? 'style="display:none;"' : ''; ?>>
                                        <?php esc_html_e('Update selected preset', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?>
                                    </li>
                                    <li id="wobef-column-profiles-save-as-new-preset">
                                        <?php esc_html_e('Save as new preset', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="wobef-meta-fields-right-item">
    <span class="wobef-meta-fields-name"><?php echo (!empty($meta_field['key'])) ? esc_html($meta_field['key']) : 'No Name!'; ?></span>
    <input type="hidden" name="meta_field_key[]" value="<?php echo (!empty($meta_field['key'])) ? esc_attr($meta_field['key']) : ''; ?>">
    <input type="text" name="meta_field_title[]" class="wobef-meta-fields-title" placeholder="<?php esc_html_e('Enter field title ...', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?>" value="<?php echo (isset($meta_field['title'])) ? esc_html($meta_field['title']) : ''; ?>">
    <?php if (isset($meta_field['type_disabled']) && $meta_field['type_disabled'] === true) : ?>
        <span class="wobef-meta-fields-type wobef-meta-fields-main-type"><?php echo esc_html(ucfirst($meta_field['main_type'])); ?></span>
        <span class="wobef-meta-fields-type wobef-meta-fields-sub-type"><?php echo esc_html(ucfirst($meta_field['sub_type'])); ?></span>
        <input type="hidden" name="meta_field_main_type[]" value="<?php echo esc_attr($meta_field['main_type']); ?>">
        <input type="hidden" name="meta_field_sub_type[]" value="<?php echo esc_attr($meta_field['sub_type']); ?>">
    <?php else : ?>
        <select class="wobef-meta-fields-type wobef-meta-fields-main-type" data-id="<?php echo (!empty($meta_field['key'])) ? esc_html($meta_field['key']) : ''; ?>" name="meta_field_main_type[]" title="<?php esc_html_e('Select Type', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?>">
            <?php if (!empty($meta_fields_main_types)) : ?>
                <?php foreach ($meta_fields_main_types as $main_type_name => $main_type_label) : ?>
                    <option value="<?php echo esc_attr($main_type_name); ?>" <?php echo (isset($meta_field['main_type']) && $meta_field['main_type'] == $main_type_name) ? 'selected' : ''; ?>>
                        <?php echo esc_html($main_type_label); ?>
                    </option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
        <select class="wobef-meta-fields-type wobef-meta-fields-sub-type <?php echo (isset($meta_field['main_type']) && $meta_field['main_type'] != 'textinput') ? 'wobef-hide' : ''; ?>" data-id="<?php echo (!empty($meta_field['key'])) ? esc_html($meta_field['key']) : ''; ?>" name="meta_field_sub_type[]" title="<?php esc_html_e('Select Type', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?>">
            <?php if (!empty($meta_fields_sub_types)) : ?>
                <?php foreach ($meta_fields_sub_types as $sub_type_name => $sub_type_label) : ?>
                    <option value="<?php echo esc_attr($sub_type_name); ?>" <?php echo (isset($meta_field['sub_type']) && $meta_field['sub_type'] == $sub_type_name) ? 'selected' : ''; ?>>
                        <?php echo esc_html($sub_type_label); ?>
                    </option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
    <?php endif; ?>
    <button type="button" class="wobef-button wobef-button-flat wobef-meta-field-item-sortable-btn" title="<?php esc_html_e('Drag', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?>">
        <i class="lni lni-menu"></i>
    </button>
    <button type="button" class="wobef-button wobef-button-flat wobef-meta-field-remove">
        <i class="lni lni-close"></i>
    </button>
</div>
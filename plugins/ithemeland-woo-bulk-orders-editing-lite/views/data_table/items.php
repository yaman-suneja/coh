<table id="wobef-items-list">
    <thead>
        <tr>
            <?php if (isset($show_id_column) && $show_id_column === true) : ?>
                <?php
                if ('id' == $sort_by) {
                    if ($sort_type == 'ASC') {
                        $sortable_icon = "<i class='dashicons dashicons-arrow-up'></i>";
                    } else {
                        $sortable_icon = "<i class='dashicons dashicons-arrow-down'></i>";
                    }
                } else {
                    $img =  WOBEF_IMAGES_URL . "/sortable.png";
                    $sortable_icon = "<img src='" . esc_url($img) . "' alt=''>";
                }
                ?>
                <th class="wobef-td70 <?php echo ($sticky_first_columns == 'yes') ? 'wobef-td-sticky wobef-td-sticky-id' : ''; ?>">
                    <input type="checkbox" class="wobef-check-item-main" title="<?php esc_html_e('Select All', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?>">
                    <label data-column-name="id" class="wobef-sortable-column"><?php esc_html_e('ID', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?><span class="wobef-sortable-column-icon"><?php echo sprintf('%s', $sortable_icon); ?></span></span>
                </th>
            <?php endif; ?>
            <?php if (!empty($next_static_columns)) : ?>
                <?php foreach ($next_static_columns as $static_column) : ?>
                    <?php
                    if ($static_column['field'] == $sort_by) {
                        if ($sort_type == 'ASC') {
                            $sortable_icon = "<i class='dashicons dashicons-arrow-up'></i>";
                        } else {
                            $sortable_icon = "<i class='dashicons dashicons-arrow-down'></i>";
                        }
                    } else {
                        $img =  WOBEF_IMAGES_URL . "/sortable.png";
                        $sortable_icon = "<img src='" . esc_url($img) . "' alt=''>";
                    }
                    ?>
                    <th data-column-name="<?php echo esc_attr($static_column['field']) ?>" class="wobef-sortable-column wobef-td120 <?php echo ($sticky_first_columns == 'yes') ? 'wobef-td-sticky wobef-td-sticky-title' : ''; ?>"><?php echo esc_attr($static_column['title']); ?><span class="wobef-sortable-column-icon"><?php echo sprintf('%s', $sortable_icon); ?></span></th>
                <?php endforeach; ?>
            <?php endif; ?>
            <?php if (!empty($columns)) : ?>
                <?php foreach ($columns as $column_name => $column) : ?>
                    <?php
                    $title = (!empty($columns_title) && isset($columns_title[$column_name])) ? $columns_title[$column_name] : '';
                    $sortable_icon = '';
                    if (isset($column['sortable']) && $column['sortable'] === true) {
                        if ($column_name == $sort_by) {
                            if ($sort_type == 'ASC') {
                                $sortable_icon = "<i class='dashicons dashicons-arrow-up'></i>";
                            } else {
                                $sortable_icon = "<i class='dashicons dashicons-arrow-down'></i>";
                            }
                        } else {
                            $img =  WOBEF_IMAGES_URL . "/sortable.png";
                            $sortable_icon = "<img src='" . esc_url($img) . "' alt=''>";
                        }
                    }

                    if (!empty($display_full_columns_title)) {
                        $column_title = ($display_full_columns_title == 'no') ? mb_substr($column['title'], 0, 12) . '.' : $column['title'];
                    } else {
                        $column_title = (strlen($column['title']) > 12) ? mb_substr($column['title'], 0, 12) . '.' : $column['title'];
                    }
                    ?>
                    <th data-column-name="<?php echo esc_attr($column_name); ?>" <?php echo (!empty($column['sortable'])) ? 'class="wobef-sortable-column"' : ''; ?>><?php echo esc_html($column_title); ?><?php echo (!empty($title)) ? "<span class='wobef-column-title dashicons dashicons-info' title='" . esc_attr($title) . "'></span>" : "" ?> <span class="wobef-sortable-column-icon"><?php echo sprintf('%s', $sortable_icon); ?></span></th>
                <?php endforeach; ?>
            <?php endif; ?>
            <?php if (!empty($after_dynamic_columns)) : ?>
                <?php foreach ($after_dynamic_columns as $last_column_item) : ?>
                    <th data-column-name="<?php echo esc_attr($last_column_item['field']) ?>" class="wobef-td120"><?php echo esc_attr($last_column_item['title']); ?></th>
                <?php endforeach; ?>
            <?php endif; ?>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($items_loading)) : ?>
            <tr>
                <td colspan="8" class="wobef-text-alert"><?php esc_html_e('Loading ...', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?></td>
            </tr>
        <?php elseif (!empty($items) && count($items) > 0) : ?>
            <?php if (!empty($item_provider && is_object($item_provider))) : ?>
                <?php $variations = !empty($variations) ? $variations : []; ?>
                <?php $item_provider->get_items($items, $variations, $columns); ?>
            <?php endif; ?>
        <?php else : ?>
            <tr>
                <td colspan="8" class="wobef-text-alert"><?php esc_html_e('No Data Available!', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?></td>
            </tr>
        <?php endif; ?>
    </tbody>
    <tfoot>
        <tr>
            <?php if (isset($show_id_column) && $show_id_column === true) : ?>
                <th <?php echo ($sticky_first_columns == 'yes') ? 'class="wobef-td-sticky wobef-td-sticky-id"' : ''; ?>>ID</th>
            <?php endif; ?>
            <?php if (!empty($next_static_columns)) : ?>
                <?php foreach ($next_static_columns as $static_column) : ?>
                    <th <?php echo ($sticky_first_columns == 'yes') ? 'class="wobef-td-sticky wobef-td-sticky-title"' : ''; ?>><?php echo esc_html($static_column['title']); ?></th>
                <?php endforeach; ?>
            <?php endif; ?>
            <?php if (!empty($columns)) : ?>
                <?php foreach ($columns as $column) : ?>
                    <th><?php echo (strlen($column['title']) > 12) ? esc_html(mb_substr($column['title'], 0, 12)) . '.' : esc_html($column['title']); ?></th>
                <?php endforeach; ?>
            <?php endif; ?>
            <?php if (!empty($after_dynamic_columns)) : ?>
                <?php foreach ($after_dynamic_columns as $last_column_item) : ?>
                    <th><?php echo esc_html($last_column_item['title']); ?></th>
                <?php endforeach; ?>
            <?php endif; ?>
        </tr>
    </tfoot>
</table>
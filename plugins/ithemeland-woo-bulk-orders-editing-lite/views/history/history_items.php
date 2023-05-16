<?php if (!empty($histories)) : ?>
    <?php $i = 1; ?>
    <?php foreach ($histories as $history) : ?>
        <?php $user_data = get_userdata(intval($history->user_id)); ?>
        <tr>
            <td><?php echo $i; ?></td>
            <td>
                <span class="wobef-history-name wobef-fw600">
                    <?php switch ($history->operation_type) {
                        case 'inline':
                            $item = (new wobef\classes\repositories\History())->get_history_items($history->id);
                            echo (!empty($item[0]->post_title)) ? esc_html($item[0]->post_title) : 'Inline Operation';
                            break;
                        case 'bulk':
                            echo 'Bulk Operation';
                            break;
                    }
                    ?>
                </span>
                <?php
                $fields = '';
                if (is_array(unserialize($history->fields)) && !empty(unserialize($history->fields))) {
                    foreach (unserialize($history->fields) as $field) {
                        if (is_array($field)) {
                            foreach ($field as $field_item) {
                                $fields .= "[" .  esc_html($field_item) . "]";
                            }
                        } else {
                            $fields .= "[" .  esc_html($field) . "]";
                        }
                    }
                }
                ?>
                <span class="wobef-history-text-sm"><?php echo esc_attr($fields); ?></span>
            </td>
            <td class="wobef-fw600"><?php echo (!empty($user_data)) ? esc_html($user_data->user_login) : ''; ?></td>
            <td class="wobef-fw600"><?php echo esc_html(date('Y / m / d', strtotime($history->operation_date))); ?></td>
            <td>
                <button type="button" class="wobef-button wobef-button-blue" disabled="disabled" value="<?php echo esc_attr($history->id); ?>">
                    <i class="lni lni-spinner-arrow"></i>
                    <?php esc_html_e('Revert', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?>
                </button>
                <button type="button" class="wobef-button wobef-button-red" disabled="disabled" value="<?php echo esc_attr($history->id); ?>">
                    <i class="lni lni-trash"></i>
                    <?php esc_html_e('Delete', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?>
                </button>
            </td>
        </tr>
        <?php $i++; ?>
    <?php endforeach; ?>
<?php endif; ?>
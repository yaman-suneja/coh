<div class="wobef-modal" id="wobef-modal-filter-profiles">
    <div class="wobef-modal-container">
        <div class="wobef-modal-box wobef-modal-box-lg">
            <div class="wobef-modal-content">
                <div class="wobef-modal-title">
                    <h2><?php esc_html_e('Filter Profiles', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?></h2>
                    <button type="button" class="wobef-modal-close" data-toggle="modal-close">
                        <i class="lni lni-close"></i>
                    </button>
                </div>
                <div class="wobef-modal-body">
                    <div class="wobef-wrap">
                        <div class="wobef-filter-profiles-items wobef-pb30">
                            <div class="wobef-table-border-radius">
                                <table>
                                    <thead>
                                        <tr>
                                            <th><?php esc_html_e('Profile Name', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?></th>
                                            <th><?php esc_html_e('Date Modified', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?></th>
                                            <th><?php esc_html_e('Use Always', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?></th>
                                            <th><?php esc_html_e('Actions', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($filters_preset)) : ?>
                                            <?php foreach ($filters_preset as $filter_item) : ?>
                                                <?php include "filter_profile_item.php"; ?>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="wobef-main">
    <div id="wobef-loading" class="wobef-loading">
        <?php esc_html_e('Loading ...', 'ithemeland-woocommerce-bulk-orders-editing-lite') ?>
    </div>
    <div id="wobef-header">
        <div class="wobef-plugin-title">
            <div class="wobef-plugin-name">
                <img src="<?php echo WOBEF_IMAGES_URL . 'wobef_icon_original.svg'; ?>" alt="">
                <span><?php esc_html_e($title); ?></span>
                <strong>Lite</strong>
            </div>
            <span class="wobef-plugin-description"><?php esc_html_e("Be professionals with managing data in the reliable and flexible way!", 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?></span>
        </div>
        <div class="wobef-header-left">
            <div class="wobef-plugin-help">
                <span>
                    <a href="<?php echo (!empty($doc_link)) ? esc_attr($doc_link) : '#'; ?>"><strong class="wobef-plugin-help-text"><?php esc_html_e('Need Help', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?></strong> <i class="lni-help"></i></a>
                </span>
            </div>
            <div class="wobef-full-screen" id="wobef-full-screen">
                <span><i class="lni lni-frame-expand"></i></span>
            </div>
            <div class="wobef-upgrade" id="wobef-upgrade">
                <a href="<?php echo esc_url(WOBEF_UPGRADE_URL); ?>"><?php echo esc_html(WOBEF_UPGRADE_TEXT); ?></a>
            </div>
        </div>
    </div>
<?php $industries = wobef\classes\helpers\Industry_Helper::get_industries(); ?>

<div id="wobef-body">
    <div class="wobef-dashboard-body">
        <div id="wobef-activation">
            <?php if (isset($is_active) && $is_active === true && $activation_skipped !== true) : ?>
                <div class="wobef-wrap">
                    <div class="wobef-tab-middle-content">
                        <div id="wobef-activation-info">
                            <strong><?php esc_html_e("Congratulations, Your plugin is activated successfully. Let's Go!", 'ithemeland-woocommerce-bulk-orders-editing-lite') ?></strong>
                        </div>
                    </div>
                </div>
            <?php else : ?>
                <div class="wobef-wrap wobef-activation-form">
                    <div class="wobef-tab-middle-content">
                        <?php if (!empty($flush_message) && is_array($flush_message)) : ?>
                            <div class="wobef-alert <?php echo ($flush_message['message'] == "Success !") ? "wobef-alert-success" : "wobef-alert-danger"; ?>">
                                <span><?php echo sanitize_text_field($flush_message['message']); ?></span>
                            </div>
                        <?php endif; ?>
                        <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post" id="wobef-activation-form">
                            <h3 class="wobef-activation-top-alert">Fill the below form to get the latest updates' news and <strong style="text-decoration: underline;">Special Offers(Discount)</strong>, Otherwise, Skip it!</h3>
                            <input type="hidden" name="action" value="wobef_activation_plugin">
                            <div class="wobef-activation-field">
                                <label for="wobef-activation-email"><?php esc_html_e('Email', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?> </label>
                                <input type="email" name="email" placeholder="Email ..." id="wobef-activation-email">
                            </div>
                            <div class="wobef-activation-field">
                                <label for="wobef-activation-industry"><?php esc_html_e('What is your industry?', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?> </label>
                                <select name="industry" id="wobef-activation-industry">
                                    <option value=""><?php esc_html_e('Select', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?></option>
                                    <?php
                                    if (!empty($industries)) :
                                        foreach ($industries as $industry_key => $industry_label) :
                                    ?>
                                            <option value="<?php echo esc_attr($industry_key); ?>"><?php echo esc_attr($industry_label); ?></option>
                                    <?php
                                        endforeach;
                                    endif
                                    ?>
                                </select>
                            </div>
                            <input type="hidden" name="activation_type" id="wobef-activation-type" value="">
                            <button type="button" id="wobef-activation-activate" class="wobef-button wobef-button-lg wobef-button-blue" value="1"><?php esc_html_e('Activate', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?></button>
                            <button type="button" id="wobef-activation-skip" class="wobef-button wobef-button-lg wobef-button-gray" style="float: left;" value="skip"><?php esc_html_e('Skip', 'ithemeland-woocommerce-bulk-orders-editing-lite'); ?></button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
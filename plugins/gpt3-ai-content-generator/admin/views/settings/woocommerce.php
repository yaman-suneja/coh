<?php
if ( ! defined( 'ABSPATH' ) ) exit;
    ?>
    <div id="tabs-7">
        <h3>Product Writer</h3>
        <div class="wpcgai_form_row">
            <label class="wpcgai_label">Write a SEO friendly product title?:</label>
            <?php $wpaicg_woo_generate_title = get_option('wpaicg_woo_generate_title',false); ?>
            <input<?php echo $wpaicg_woo_generate_title ? ' checked':'';?> type="checkbox" name="wpaicg_woo_generate_title" value="1">
            <a class="wpcgai_help_link" href="https://gptaipower.com/optimize-your-woocommerce-product-listings-with-gpt-3-ai/" target="_blank">?</a>
        </div>
        <div class="wpcgai_form_row">
            <label class="wpcgai_label">Write a SEO Meta Description?:</label>
            <?php $wpaicg_woo_meta_description = get_option('wpaicg_woo_meta_description',false); ?>
            <input<?php echo $wpaicg_woo_meta_description ? ' checked':'';?> type="checkbox" name="wpaicg_woo_meta_description" value="1">
            <a class="wpcgai_help_link" href="https://gptaipower.com/optimize-your-woocommerce-product-listings-with-gpt-3-ai/" target="_blank">?</a>
        </div>
        <div class="wpcgai_form_row">
            <label class="wpcgai_label">Write a product description?:</label>
            <?php $wpaicg_woo_generate_description = get_option('wpaicg_woo_generate_description',false); ?>
            <input<?php echo $wpaicg_woo_generate_description ? ' checked':'';?> type="checkbox" name="wpaicg_woo_generate_description" value="1">
            <a class="wpcgai_help_link" href="https://gptaipower.com/optimize-your-woocommerce-product-listings-with-gpt-3-ai/" target="_blank">?</a>
        </div>
        <div class="wpcgai_form_row">
            <label class="wpcgai_label">Write a short product description?:</label>
            <?php $wpaicg_woo_generate_short = get_option('wpaicg_woo_generate_short',false); ?>
            <input<?php echo $wpaicg_woo_generate_short ? ' checked':'';?> type="checkbox" name="wpaicg_woo_generate_short" value="1">
            <a class="wpcgai_help_link" href="https://gptaipower.com/optimize-your-woocommerce-product-listings-with-gpt-3-ai/" target="_blank">?</a>
        </div>
        <div class="wpcgai_form_row">
            <label class="wpcgai_label">Generate product tags?:</label>
            <?php $wpaicg_woo_generate_tags = get_option('wpaicg_woo_generate_tags',false); ?>
            <input<?php echo $wpaicg_woo_generate_tags ? ' checked':'';?> type="checkbox" name="wpaicg_woo_generate_tags" value="1">
            <a class="wpcgai_help_link" href="https://gptaipower.com/optimize-your-woocommerce-product-listings-with-gpt-3-ai/" target="_blank">?</a>
        </div>
        <?php
        $wpaicg_woo_custom_prompt = get_option('wpaicg_woo_custom_prompt',false);
        $wpaicg_woo_custom_prompt_title = get_option('wpaicg_woo_custom_prompt_title','Write a SEO friendly product title: %s.');
        $wpaicg_woo_custom_prompt_short = get_option('wpaicg_woo_custom_prompt_short','Summarize this product in 2 short sentences: %s.');
        $wpaicg_woo_custom_prompt_description = get_option('wpaicg_woo_custom_prompt_description','Write a detailed product description about: %s.');
        $wpaicg_woo_custom_prompt_keywords = get_option('wpaicg_woo_custom_prompt_keywords','Suggest keywords for this product: %s.');
        $wpaicg_woo_custom_prompt_meta = get_option('wpaicg_woo_custom_prompt_meta','Write a meta description about: %s. Max: 155 characters.');
        ?>
        <div class="wpcgai_form_row">
            <label class="wpcgai_label">Use Custom Prompt:</label>
            <input<?php echo $wpaicg_woo_custom_prompt ? ' checked':'';?> type="checkbox" class="wpaicg_woo_custom_prompt" name="wpaicg_woo_custom_prompt" value="1">
        </div>
        <div<?php echo $wpaicg_woo_custom_prompt ? '':' style="display:none"';?> class="wpaicg_woo_custom_prompts">
            <div class="wpcgai_form_row">
                <label class="wpcgai_label">Title Prompt:</label>
                <textarea style="width: 65%;" type="text" name="wpaicg_woo_custom_prompt_title"><?php echo esc_html($wpaicg_woo_custom_prompt_title);?></textarea>
            </div>
            <div class="wpcgai_form_row">
                <label class="wpcgai_label">Short description prompt:</label>
                <textarea style="width: 65%;" type="text" name="wpaicg_woo_custom_prompt_short"><?php echo esc_html($wpaicg_woo_custom_prompt_short);?></textarea>
            </div>
            <div class="wpcgai_form_row">
                <label class="wpcgai_label">Description prompt:</label>
                <textarea style="width: 65%;" type="text" name="wpaicg_woo_custom_prompt_description"><?php echo esc_html($wpaicg_woo_custom_prompt_description);?></textarea>
            </div>
            <div class="wpcgai_form_row">
                <label class="wpcgai_label">Meta Description prompt:</label>
                <textarea style="width: 65%;" type="text" name="wpaicg_woo_custom_prompt_meta"><?php echo esc_html($wpaicg_woo_custom_prompt_meta);?></textarea>
            </div>
            <div class="wpcgai_form_row">
                <label class="wpcgai_label">Keywords prompt:</label>
                <textarea style="width: 65%;" type="text" name="wpaicg_woo_custom_prompt_keywords"><?php echo esc_html($wpaicg_woo_custom_prompt_keywords);?></textarea>
            </div>
        </div>
        <h3>Token Sale</h3>
        <?php
        $wpaicg_order_status_token = get_option('wpaicg_order_status_token','completed');
        ?>
        <div class="wpcgai_form_row">
            <label class="wpcgai_label">Add tokens to user account if order status is: </label>
            <select name="wpaicg_order_status_token">
                <option<?php echo $wpaicg_order_status_token == 'completed'? ' selected':''?> value="completed">Completed</option>
                <option<?php echo $wpaicg_order_status_token == 'processing'? ' selected':''?> value="processing">Processing</option>
            </select>
        </div>
    </div>
<script>
    jQuery(document).ready(function ($){
        $('.wpaicg_woo_custom_prompt').click(function (){
            if($(this).prop('checked')){
                $('.wpaicg_woo_custom_prompts').show();
            }
            else{
                $('.wpaicg_woo_custom_prompts').hide();
            }
        })
    })
</script>

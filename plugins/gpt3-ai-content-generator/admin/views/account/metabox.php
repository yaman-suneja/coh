<?php
if ( ! defined( 'ABSPATH' ) ) exit;
$wpaicg_product_sale_type = get_post_meta($post->ID,'wpaicg_product_sale_type',true);
$wpaicg_product_sale_tokens = get_post_meta($post->ID,'wpaicg_product_sale_tokens',true);
?>
<p class="wpaicg-form-row">
    <label><strong>Sell Token For?</strong></label>
    <select name="wpaicg_product_sale_type">
        <option value="">None</option>
        <?php
        if($this->chat_sale):
        ?>
        <option<?php echo $wpaicg_product_sale_type == 'chat' ? ' selected':''?> value="chat">ChatGPT</option>
        <?php
        endif;
        ?>
        <?php
        if($this->image_sale):
        ?>
        <option<?php echo $wpaicg_product_sale_type == 'image' ? ' selected':''?> value="image">Image Generator</option>
        <?php
        endif;
        ?>
        <?php
        if($this->forms_sale):
        ?>
        <option<?php echo $wpaicg_product_sale_type == 'forms' ? ' selected':''?> value="forms">AI Forms</option>
        <?php
        endif;
        ?>
        <?php
        if($this->promptbase_sale):
        ?>
        <option<?php echo $wpaicg_product_sale_type == 'promptbase' ? ' selected':''?> value="promptbase">Promptbase</option>
        <?php
        endif;
        ?>
    </select>
</p>
<p class="wpaicg-form-row">
    <label><strong>Token Amount:</strong></label>
    <input type="number" value="<?php echo esc_html($wpaicg_product_sale_tokens)?>" name="wpaicg_product_sale_tokens">
</p>

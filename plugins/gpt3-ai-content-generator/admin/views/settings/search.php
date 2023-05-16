<?php
if ( ! defined( 'ABSPATH' ) ) exit;
//$wpaicg_search_language = get_option('wpaicg_search_language','en');
$wpaicg_search_placeholder = get_option('wpaicg_search_placeholder','Search anything..');
$wpaicg_search_no_result = get_option('wpaicg_search_no_result','5');
$wpaicg_search_font_size = get_option('wpaicg_search_font_size','13');
$wpaicg_search_font_color = get_option('wpaicg_search_font_color','#000');
$wpaicg_search_border_color = get_option('wpaicg_search_border_color','#ccc');
$wpaicg_search_bg_color = get_option('wpaicg_search_bg_color','');
$wpaicg_search_width = get_option('wpaicg_search_width','100%');
$wpaicg_search_height = get_option('wpaicg_search_height','45px');
$wpaicg_pinecone_api = get_option('wpaicg_pinecone_api','');

$wpaicg_search_result_font_size = get_option('wpaicg_search_result_font_size','13');
$wpaicg_search_result_font_color = get_option('wpaicg_search_result_font_color','#000');
$wpaicg_search_result_bg_color = get_option('wpaicg_search_result_bg_color','');
$wpaicg_search_loading_color = get_option('wpaicg_search_loading_color','#ccc');

$wpaicg_pinecone_environment = get_option('wpaicg_pinecone_environment','');
?>
<div id="tabs-8">
    <?php
    if(empty($wpaicg_pinecone_api) || empty($wpaicg_pinecone_environment)):
        ?>
        <p>It appears that you haven't entered your keys for Pinecone, which is why this feature is currently disabled. Please go to <a href="<?php echo admin_url('admin.php?page=wpaicg_embeddings&action=settings')?>">this page</a> and enter your keys.</p>
    <?php
    else:
    ?>
    <p><b>Usage</b></p>
    <p>Copy the following code and paste it in your page or post where you want to show the search box: <code>[wpaicg_search]</code></p>
    <hr>
    <p><b>Search Box</b></p>
    <div class="wpcgai_form_row">
        <label class="wpcgai_label">Placeholder:</label>
        <input type="text" class="regular-text" name="wpaicg_search_placeholder" value="<?php
        echo  esc_html( get_option( 'wpaicg_search_placeholder', 'Search anything..' ) ) ;
        ?>" >
    </div>
    <div class="wpcgai_form_row">
        <label class="wpcgai_label">Font Size:</label>
        <select name="wpaicg_search_font_size">
            <?php
            for($i = 10; $i <= 30; $i++){
                echo '<option'.($wpaicg_search_font_size == $i ? ' selected': '').' value="'.esc_html($i).'">'.esc_html($i).'px</option>';
            }
            ?>
        </select>
    </div>
    <div class="wpcgai_form_row">
        <label class="wpcgai_label">Font Color:</label>
        <input value="<?php echo esc_html($wpaicg_search_font_color)?>" type="text" class="wpaicgchat_color" name="wpaicg_search_font_color">
    </div>
    <div class="wpcgai_form_row">
        <label class="wpcgai_label">Border Color:</label>
        <input value="<?php echo esc_html($wpaicg_search_border_color)?>" type="text" class="wpaicgchat_color" name="wpaicg_search_border_color">
    </div>
    <div class="wpcgai_form_row">
        <label class="wpcgai_label">Background Color:</label>
        <input value="<?php echo esc_html($wpaicg_search_bg_color)?>" type="text" class="wpaicgchat_color" name="wpaicg_search_bg_color">
    </div>
    <div class="wpcgai_form_row">
        <label class="wpcgai_label">Width:</label>
        <input value="<?php echo esc_html($wpaicg_search_width)?>" style="width: 100px;" min="100" type="text" name="wpaicg_search_width"> (You can use percent or pixel)
    </div>
    <div class="wpcgai_form_row">
        <label class="wpcgai_label">Height:</label>
        <input value="<?php echo esc_html($wpaicg_search_height)?>" style="width: 100px;" min="100" type="text" name="wpaicg_search_height"> (You can use percent or pixel)
    </div>
    <hr>
    <p><b>Results</b></p>
    <div class="wpcgai_form_row">
        <label class="wpcgai_label">Number of Results:</label>
        <select name="wpaicg_search_no_result">
            <?php
            for($i = 1; $i <=5;$i++){
                echo '<option'.($wpaicg_search_no_result == $i ? ' selected':'').' value="'.esc_html($i).'">'.esc_html($i).'</option>';
            }
            ?>
        </select>
    </div>
    <div class="wpcgai_form_row">
        <label class="wpcgai_label">Font Size:</label>
        <select name="wpaicg_search_result_font_size">
            <?php
            for($i = 10; $i <= 30; $i++){
                echo '<option'.($wpaicg_search_result_font_size == $i ? ' selected': '').' value="'.esc_html($i).'">'.esc_html($i).'px</option>';
            }
            ?>
        </select>
    </div>
    <div class="wpcgai_form_row">
        <label class="wpcgai_label">Font Color:</label>
        <input value="<?php echo esc_html($wpaicg_search_result_font_color)?>" type="text" class="wpaicgchat_color" name="wpaicg_search_result_font_color">
    </div>
    <div class="wpcgai_form_row">
        <label class="wpcgai_label">Background Color:</label>
        <input value="<?php echo esc_html($wpaicg_search_result_bg_color)?>" type="text" class="wpaicgchat_color" name="wpaicg_search_result_bg_color">
    </div>
    <div class="wpcgai_form_row">
        <label class="wpcgai_label">Progress Background Color:</label>
        <input value="<?php echo esc_html($wpaicg_search_loading_color)?>" type="text" class="wpaicgchat_color" name="wpaicg_search_loading_color">
    </div>
    <?php
    endif;
    ?>
</div>

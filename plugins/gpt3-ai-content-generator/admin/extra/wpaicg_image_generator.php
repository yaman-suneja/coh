<?php
if ( ! defined( 'ABSPATH' ) ) exit;
$wpaicg_action = isset($_GET['action']) && !empty($_GET['action']) ? sanitize_text_field($_GET['action']) : '';
if(is_admin()){
    $checkRole = \WPAICG\wpaicg_roles()->user_can('wpaicg_image_generator',empty($wpaicg_action) ? 'dalle' : $wpaicg_action);
    if($checkRole){
        echo '<script>window.location.href="'.$checkRole.'"</script>';
        exit;
    }
}
?>
<style>
    .wpaicg_notice_text_rw {
    padding: 10px;
    background-color: #F8DC6F;
    text-align: left;
    margin-bottom: 12px;
    color: #000;
    box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px;
    }
    .image-grid {
        grid-template-columns: repeat(3,1fr);
        grid-column-gap: 20px;
        grid-row-gap: 20px;
        display: grid;
        grid-template-rows: auto auto;
    }
    .image-generated{
        min-height: 100px;
        position: relative;
        border-radius: 5px;
    }
    .image-generate-loading{
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: #2271B1;
        border-radius: 5px;
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 99;
    }
    .lds-dual-ring {
        display: inline-block;
        width: 64px;
        height: 64px;
    }
    .lds-dual-ring:after {
        content: " ";
        display: block;
        width: 48px;
        height: 48px;
        margin: 8px;
        border-radius: 50%;
        border: 6px solid #fff;
        border-color: #fff transparent #fff transparent;
        animation: lds-dual-ring 1.2s linear infinite;
    }
    @keyframes lds-dual-ring {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }
    .wpaicg-image-item {
        background-size: cover;
        box-shadow: 0px 0px 10px #ccc;
        position: relative;
        cursor: pointer;
    }
    .wpaicg-image-item img{
        width: 100%;
        height: auto;
    }
    .wpaicg-image-item label{
        position: absolute;
        top: 10px;
        right: 10px;
    }

    .select-element {
        margin: 10px;
    }

    .button-element {
        background-color: #4169E1;
        /* this is the blue color */
        color: white;
        padding: 12px 20px;
        border-radius: 10px;
        border: none;
        cursor: pointer;
        font-size: 14px;
    }

    .button-element:hover {
        background-color: #6495ED;
        /* this is a slightly lighter blue color on hover */
        box-shadow: 0px 0px 10px #B0C4DE;
        /* this is a subtle shadow on hover */
        transform: translateY(-2px);
        /* this is a subtle upward movement on hover */
    }


    #prompt {
        width: 100%;
        padding: 12px 20px;
        box-sizing: border-box;
        border: 2px solid #ccc;
        border-radius: 4px;
        background-color: #f8f8f8;
        resize: none;
    }
    .wpaicg-convert-progress{
        height: 15px;
        background: #727272;
        border-radius: 5px;
        color: #fff;
        padding: 2px 12px;
        position: relative;
        font-size: 12px;
        text-align: center;
        margin-bottom: 10px;
        display: none;
    }
    .wpaicg-convert-progress.wpaicg_error span{
        background: #bb0505;
    }
    .wpaicg-convert-progress span{
        display: block;
        position: absolute;
        height: 100%;
        border-radius: 5px;
        background: #2271b1;
        top: 0;
        left: 0;
        transition: width .6s ease;
    }
    .wpaicg-convert-progress small{
        position: relative;
        font-size: 12px;
    }
    .wpaicg_modal{
        top: 5%;
        height: 90%;
    }
    .wpaicg_modal_content{
        height: calc(100% - 50px);
        overflow-y: auto;
    }
    .wpaicg-collapse-content select,.wpaicg-collapse-content input[type=number]{
        display: inline-block!important;
        width: 48%!important;
    }
    .wpaicg-collapse-content .wpaicg-mb-5{
        display: flex;
        align-items: center;
    }
    .wpaicg-mb-5{
        margin-bottom: 5px;

    }
    .wpaicg-loader{
        width: 15px;
        height: 15px;
        border: 2px solid #5e5e5e;
        border-bottom-color: transparent;
        border-radius: 50%;
        display: inline-block;
        box-sizing: border-box;
        animation: wpaicg_rotation 1s linear infinite;
    }
    .wpaicg-image-shortcode .wpaicg-button .wpaicg-loader{
        border-color:#fff;
        border-bottom-color: transparent;
        width: 20px;
        height: 20px;
        margin-top: 2px;
    }
    .wpaicg-button .wpaicg-loader{
        float: right;
        margin-left: 5px;
        margin-top: 6px;
    }
    @keyframes wpaicg_rotation {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }
</style>
<?php
$wpaicg_art_file = WPAICG_PLUGIN_DIR . 'admin/data/art.json';
$wpaicg_painter_data = file_get_contents($wpaicg_art_file);
$wpaicg_painter_data = json_decode($wpaicg_painter_data, true);

$wpaicg_style_data = file_get_contents($wpaicg_art_file);
$wpaicg_style_data = json_decode($wpaicg_style_data, true);

$wpaicg_photo_file = WPAICG_PLUGIN_DIR . 'admin/data/photo.json';

$wpaicg_photo_data = file_get_contents($wpaicg_photo_file);
$wpaicg_photo_data = json_decode($wpaicg_photo_data, true);
$wpaicg_dalle_url = isset($wpaicg_image_shortcode) && $wpaicg_image_shortcode ? get_permalink() : admin_url('admin.php?page=wpaicg_image_generator');
$wpaicg_sd_url = isset($wpaicg_image_shortcode) && $wpaicg_image_shortcode ? add_query_arg('action','stable-diffusion',get_permalink()) : admin_url('admin.php?page=wpaicg_image_generator&action=stable-diffusion');
$wpaicg_show_setting = true;
$wpaicg_save_media = true;
$wpaicg_show_dalle = true;
$wpaicg_show_sd = true;
$wpaicg_image_surprise_text = get_option('wpaicg_image_surprise_text','Surprise Me');
$wpaicg_image_generate_text = get_option('wpaicg_image_generate_text','Generate');
$wpaicg_image_view_text = get_option('wpaicg_image_view_text','View Image');
$wpaicg_image_save_media_text = get_option('wpaicg_image_save_media_text','Save to Media');
$wpaicg_image_select_all_text = get_option('wpaicg_image_select_all_text','Select All');
if(isset($wpaicg_image_shortcode) && $wpaicg_image_shortcode){
    if(isset($wpaicg_shortcode_settings) && is_array($wpaicg_shortcode_settings)){
        if(
            (isset($wpaicg_shortcode_settings['dalle']) && $wpaicg_shortcode_settings['dalle'] == 'no')
            || !isset($wpaicg_shortcode_settings['dalle'])
        ){
            $wpaicg_show_dalle = false;
        }
        if(!isset($wpaicg_shortcode_settings['dalle']) && isset($wpaicg_shortcode_settings['sd']) && $wpaicg_shortcode_settings['sd'] == 'no'){
            $wpaicg_show_dalle = true;
        }
        if(
            (isset($wpaicg_shortcode_settings['sd']) && $wpaicg_shortcode_settings['sd'] == 'no')
            || !isset($wpaicg_shortcode_settings['sd'])
        ){
            $wpaicg_show_sd = false;
        }
        if(!isset($wpaicg_shortcode_settings['sd']) && isset($wpaicg_shortcode_settings['dalle']) && $wpaicg_shortcode_settings['dalle'] == 'no'){
            $wpaicg_show_sd = true;
        }
        if(!$wpaicg_show_dalle && !$wpaicg_show_sd){
            $wpaicg_show_dalle = true;
            $wpaicg_show_sd = true;
        }
        if(
            (isset($wpaicg_shortcode_settings['settings']) && $wpaicg_shortcode_settings['settings'] == 'no')
            || !isset($wpaicg_shortcode_settings['settings'])
        ){
            $wpaicg_show_setting = false;
        }
    }
    else{
        $wpaicg_show_setting = false;
    }
}
$wpaicg_shortcode_text = 'wpcgai_img';
if($wpaicg_show_dalle){
    $wpaicg_shortcode_text .= ' dalle=yes';
}
if($wpaicg_show_sd){
    $wpaicg_shortcode_text .= ' sd=yes';
}
if($wpaicg_show_setting){
    $wpaicg_shortcode_text .= ' settings=yes';
}
$wpaicg_image_default_prompts = get_option('wpaicg_image_default_prompts','');
$wpaicg_image_prompts = array();
if(!empty($wpaicg_image_default_prompts)){
    $exs = array_map('trim', explode("\n", $wpaicg_image_default_prompts));
    if($exs && is_array($exs) && count($exs)) {
        foreach ($exs as $ex) {
            if(!empty($ex)){
                $wpaicg_image_prompts[] = str_replace("\\",'',$ex);
            }
        }
    }
}
if(count($wpaicg_image_prompts)){
    $wpaicg_painter_data['prompts'] = $wpaicg_image_prompts;
}
?>
<div class="wrap fs-section wpaicg-image-generator<?php echo isset($wpaicg_image_shortcode) && $wpaicg_image_shortcode ? ' wpaicg-image-shortcode':''?>">
    <?php
    if(isset($wpaicg_image_shortcode) && $wpaicg_image_shortcode):
    ?>
    <div class="wpaicg-image-generator-tabs">
        <?php
        if($wpaicg_show_dalle && $wpaicg_show_sd){
        ?>
        <a href="<?php echo esc_url($wpaicg_dalle_url)?>" class="<?php echo empty($wpaicg_action) ? ' wpaicg-tab-active' : ''?>">DALL-E</a>
        <a href="<?php echo esc_url($wpaicg_sd_url)?>" class="<?php echo $wpaicg_action == 'stable-diffusion' ? ' wpaicg-tab-active' : ''?>">Stable Diffusion</a>
        <?php
        }
        ?>
    </div>
    <?php
    else:
    ?>
    <h2 class="nav-tab-wrapper">
        <?php
        if(empty($wpaicg_action)){
            $wpaicg_action = 'dalle';
        };
        \WPAICG\wpaicg_util_core()->wpaicg_tabs('wpaicg_image_generator',array(
            'dalle' => 'DALL-E',
            'stable-diffusion' => 'Stable Diffusion',
            'shortcodes' => 'Shortcodes',
            'logs' => 'Logs',
            'settings' => 'Settings'
        ),$wpaicg_action);
        if(!$wpaicg_action || $wpaicg_action == 'dalle'){
            $wpaicg_action = '';
        }
        ?>
    </h2>
    <?php
    endif;
    ?>
    <div id="poststuff">
        <div id="fs_account">
            <?php
            if($wpaicg_action !== 'shortcodes' && $wpaicg_action != 'logs' && $wpaicg_action != 'settings'):
            ?>
            <form class="wpaicg-single-content-form" id="wpaicg-image-generator-form" method="post">
                <input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( 'wpaicg-image-generator' )?>">
                <?php
                if($wpaicg_show_setting):
                ?>
                <div class="wpaicg_grid_form" id="wpaicg-post-form">
                    <div class="wpaicg_grid_form_2">
                    <?php
                    endif;
                    ?>
                        <div class="wpaicg-mb-5">
                            <label for="prompt">Prompt:</label>
                            <textarea name="prompt" id="prompt" rows="2" cols="50"><?php echo esc_html($wpaicg_painter_data['prompts'][array_rand($wpaicg_painter_data['prompts'])])?></textarea>
                            <button class="button button-primary wpaicg-button" type="button" onclick="getRandomPrompt()"><?php echo esc_html($wpaicg_image_surprise_text)?></button>
                            <button class="button button-primary wpaicg-button wpaicg_button_generate" id="wpaicg_button_generate"><?php echo esc_html($wpaicg_image_generate_text)?></button>
                        </div>
                        <div class="image-generated">
                            <div class="image-generate-loading" id="image-generate-loading"><div class="lds-dual-ring"></div></div>
                            <div class="image-grid wpaicg-mb-5" id="image-grid">
                            </div>
                            <div style="<?php echo is_user_logged_in()? '' : 'display:none'?>">
                            <a href="javascript:void(0)" id="wpaicg_image_select_all" class="wpaicg_image_select_all" style="display: none"><?php echo esc_html($wpaicg_image_select_all_text)?></a><br><br>
                            <div id="wpaicg_message" class="wpaicg_message" style="text-align: center;margin-top: 10px;"></div>
                            <div class="wpaicg-convert-progress wpaicg-convert-bar" id="wpaicg-convert-bar">
                                <span></span>
                                <small>0%</small>
                            </div>
                            <button type="button" id="image-generator-save" class="button button-primary wpaicg-button image-generator-save" style="width: 100%;display: none"><?php echo esc_html($wpaicg_image_save_media_text)?></button>
                            </div>
                        </div>
                        <?php
                        if($wpaicg_show_setting):
                        ?>
                    </div>
                    <div class="wpaicg_grid_form_1">
                        <?php
                        endif;
                        if(empty($wpaicg_action)){
                            $wpaicg_image_settings = get_option('wpaicg_image_setting_dalle',[]);
                        }
                        else{
                            $wpaicg_image_settings = get_option('wpaicg_image_setting_sd',[]);
                        }
                        ?>
                        <div class="wpaicg-collapse wpaicg-collapse-active" style="<?php echo $wpaicg_show_setting ? '' : 'display:none'?>">
                            <div class="wpaicg-collapse-title">Settings</div>
                            <div class="wpaicg-collapse-content">
                                <div class="wpaicg-mb-5">
                                    <?php
                                    echo '<label for="artist" class="wpaicg-form-label">Artist:</label>';
                                    echo '<select class="wpaicg-input" name="artist" id="artist">';
                                    foreach ($wpaicg_painter_data['painters'] as $key => $value) {
                                        echo '<option'.(isset($wpaicg_image_settings['artist']) && $wpaicg_image_settings['artist'] == $value ? ' selected':'').' value="' . esc_html($value) . '">' . esc_html($value) . '</option>';
                                    }
                                    echo '</select>';
                                    ?>
                                </div>
                                <div class="wpaicg-mb-5">
                                    <?php
                                    echo '<label for="art_style" class="wpaicg-form-label">Style:</label>';
                                    echo '<select class="wpaicg-input" name="art_style" id="art_style">';
                                    foreach ($wpaicg_painter_data['styles'] as $key => $value) {
                                        echo '<option'.(isset($wpaicg_image_settings['art_style']) && $wpaicg_image_settings['art_style'] == $value ? ' selected':'').' value="' . esc_html($value) . '">' . esc_html($value) . '</option>';
                                    }
                                    echo '</select>';
                                    ?>
                                </div>
                                <div class="wpaicg-mb-5">
                                    <?php
                                    echo '<label for="photography_style" class="wpaicg-form-label">Photography:</label>';
                                    echo '<select class="wpaicg-input" name="photography_style" id="photography_style">';
                                    foreach ($wpaicg_photo_data['photography_style'] as $key => $value) {
                                        echo '<option'.(isset($wpaicg_image_settings['photography_style']) && $wpaicg_image_settings['photography_style'] == $value ? ' selected':'').' value="' . esc_html($value) . '">' . esc_html($value) . '</option>';
                                    }
                                    echo '</select>';
                                    ?>
                                </div>
                                <div class="wpaicg-mb-5">
                                    <?php
                                    echo '<label for="lighting" class="wpaicg-form-label">Lighting:</label>';
                                    echo '<select class="wpaicg-input" name="lighting" id="lighting">';
                                    foreach ($wpaicg_photo_data['lighting'] as $key => $value) {
                                        echo '<option'.(isset($wpaicg_image_settings['lighting']) && $wpaicg_image_settings['lighting'] == $value ? ' selected':'').' value="' . esc_html($value) . '">' . esc_html($value) . '</option>';
                                    }
                                    echo '</select>';
                                    ?>
                                </div>
                                <div class="wpaicg-mb-5">
                                    <?php
                                    echo '<label for="subject" class="wpaicg-form-label">Subject:</label>';
                                    echo '<select class="wpaicg-input" name="subject" id="subject">';
                                    foreach ($wpaicg_photo_data['subject'] as $key => $value) {
                                        echo '<option'.(isset($wpaicg_image_settings['subject']) && $wpaicg_image_settings['subject'] == $value ? ' selected':'').' value="' . esc_html($value) . '">' . esc_html($value) . '</option>';
                                    }
                                    echo '</select>';
                                    ?>
                                </div>
                                <div class="wpaicg-mb-5">
                                    <?php
                                    echo '<label for="camera_settings" class="wpaicg-form-label">Camera:</label>';
                                    echo '<select class="wpaicg-input" name="camera_settings" id="camera_settings">';
                                    foreach ($wpaicg_photo_data['camera_settings'] as $key => $value) {
                                        echo '<option'.(isset($wpaicg_image_settings['camera_settings']) && $wpaicg_image_settings['camera_settings'] == $value ? ' selected':'').' value="' . esc_html($value) . '">' . esc_html($value) . '</option>';
                                    }
                                    echo '</select>';
                                    ?>
                                </div>
                                <div class="wpaicg-mb-5">
                                    <?php
                                    echo '<label for="composition" class="wpaicg-form-label">Composition:</label>';
                                    echo '<select class="wpaicg-input" name="composition" id="composition">';
                                    foreach ($wpaicg_photo_data['composition'] as $key => $value) {
                                        echo '<option'.(isset($wpaicg_image_settings['composition']) && $wpaicg_image_settings['composition'] == $value ? ' selected':'').' value="' . esc_html($value) . '">' . esc_html($value) . '</option>';
                                    }
                                    echo '</select>';
                                    ?>
                                </div>
                                <div class="wpaicg-mb-5">
                                    <?php
                                    echo '<label for="resolution" class="wpaicg-form-label">Resolution:</label>';
                                    echo '<select class="wpaicg-input" name="resolution" id="resolution">';
                                    foreach ($wpaicg_photo_data['resolution'] as $key => $value) {
                                        echo '<option'.(isset($wpaicg_image_settings['resolution']) && $wpaicg_image_settings['resolution'] == $value ? ' selected':'').' value="' . esc_html($value) . '">' . esc_html($value) . '</option>';
                                    }
                                    echo '</select>';
                                    ?>
                                </div>
                                <div class="wpaicg-mb-5">
                                    <?php
                                    echo '<label for="color" class="wpaicg-form-label">Color:</label>';
                                    echo '<select class="wpaicg-input" name="color" id="color">';
                                    foreach ($wpaicg_photo_data['color'] as $key => $value) {
                                        echo '<option'.(isset($wpaicg_image_settings['color']) && $wpaicg_image_settings['color'] == $value ? ' selected':'').' value="' . esc_html($value) . '">' . esc_html($value) . '</option>';
                                    }
                                    echo '</select>';
                                    ?>
                                </div>
                                <div class="wpaicg-mb-5">
                                    <?php
                                    echo '<label for="special_effects" class="wpaicg-form-label">Special Effects:</label>';
                                    echo '<select class="wpaicg-input" name="special_effects" id="special_effects">';
                                    foreach ($wpaicg_photo_data['special_effects'] as $key => $value) {
                                        echo '<option'.(isset($wpaicg_image_settings['special_effects']) && $wpaicg_image_settings['special_effects'] == $value ? ' selected':'').' value="' . esc_html($value) . '">' . esc_html($value) . '</option>';
                                    }
                                    echo '</select>';
                                    ?>
                                </div>
                                <?php
                                if(empty($wpaicg_action) && $wpaicg_show_dalle):
                                    ?>
                                    <input type="hidden" name="action" value="wpaicg_image_generator">
                                <div class="wpaicg-mb-5">
                                <?php
                                echo '<label for="img_size" class="wpaicg-form-label">Size:</label>';
                                echo '<select class="wpaicg-input" name="img_size" id="img_size">';
                                if(isset($_POST['img_size'])) {
                                    echo '<option value="256x256" ' . (sanitize_text_field($_POST['img_size']) == "256x256" ? 'selected' : '') . '>256x256</option>';
                                    echo '<option value="512x512" ' . (sanitize_text_field($_POST['img_size']) == "512x512" ? 'selected' : '') . '>512x512</option>';
                                    echo '<option value="1024x1024" ' . (sanitize_text_field($_POST['img_size']) == "1024x1024" ? 'selected' : '') . '>1024x1024</option>';
                                } else {
                                    echo '<option'.(isset($wpaicg_image_settings['img_size']) && $wpaicg_image_settings['img_size'] == '256x256' ? ' selected':'').' value="256x256">256x256</option>';
                                    echo '<option'.(!isset($wpaicg_image_settings['img_size']) || $wpaicg_image_settings['img_size'] == '' || $wpaicg_image_settings['img_size'] == '512x512' ? ' selected':'').' value="512x512">512x512</option>';
                                    echo '<option'.(isset($wpaicg_image_settings['img_size']) && $wpaicg_image_settings['img_size'] == '1024x1024' ? ' selected':'').' value="1024x1024">1024x1024</option>';
                                }
                                echo '</select>';
                                ?>
                                </div>
                                <div class="wpaicg-mb-5">
                                    <?php
                                    echo '<label for="num_images" class="wpaicg-form-label"># of:</label>';
                                    echo '<select name="num_images" id="num_images" class="wpaicg-input">';
                                    for($i=1;$i<=10;$i++){
                                        echo '<option'.((isset($wpaicg_image_settings['num_images']) && $wpaicg_image_settings['num_images'] == $i) || (!isset($wpaicg_image_settings['num_images']) && $i==6) ? ' selected':'').' value="'.esc_html($i).'">'.esc_html($i).'</option>';
                                    }
                                    echo '</select>';
                                    ?>
                                </div>
                                <?php
                                elseif($wpaicg_action == 'stable-diffusion' || $wpaicg_show_sd):
                                    $wpaicg_sizes = [128, 256, 384, 448, 512, 576, 640, 704, 768, 832, 896, 960, 1024];
                                ?>
                                    <input type="hidden" name="action" value="wpaicg_image_stable_diffusion">
                                    <div class="wpaicg-mb-5">
                                        <label class="wpaicg-form-label">Negative Prompt<br><small style="font-weight: normal">Separate by commas</small></label>
                                        <input class="wpaicg_input" type="text" name="negative_prompt" value="<?php echo isset($wpaicg_image_settings['negative_prompt']) ? esc_html($wpaicg_image_settings['negative_prompt']) : ''?>">
                                    </div>
                                    <div class="wpaicg-mb-5">
                                        <label class="wpaicg-form-label">Width</label>
                                        <select name="width">
                                        <?php
                                        foreach($wpaicg_sizes as $wpaicg_size){
                                            echo '<option'.((isset($wpaicg_image_settings['width']) && $wpaicg_image_settings['width'] == $wpaicg_size ) ||(!isset($wpaicg_image_settings['width']) && $wpaicg_size == 768) ? ' selected':'').' value="'.esc_html($wpaicg_size).'">'.esc_html($wpaicg_size).'px</option>';
                                        }
                                        ?>
                                        </select>
                                    </div>
                                    <div class="wpaicg-mb-5">
                                        <label class="wpaicg-form-label">Height</label>
                                        <select name="height">
                                        <?php
                                        foreach($wpaicg_sizes as $wpaicg_size){
                                            echo '<option'.((isset($wpaicg_image_settings['height']) && $wpaicg_image_settings['height'] == $wpaicg_size ) ||(!isset($wpaicg_image_settings['height']) && $wpaicg_size == 768) ? ' selected':'').' value="'.esc_html($wpaicg_size).'">'.esc_html($wpaicg_size).'px</option>';
                                        }
                                        ?>
                                        </select>
                                    </div>
                                    <div class="wpaicg-mb-5">
                                        <label class="wpaicg-form-label">Prompt Strength</label>
                                        <input class="wpaicg_input prompt_strength" type="text" name="prompt_strength" id="prompt_strength" value="<?php echo isset($wpaicg_image_settings['prompt_strength']) ? esc_html($wpaicg_image_settings['prompt_strength']) : '0.8'?>">
                                    </div>
                                    <div class="wpaicg-mb-5">
                                        <label class="wpaicg-form-label">Number of Images</label>
                                        <select name="num_outputs" id="num_images">
                                            <?php
                                            for($i = 1; $i<=2;$i++){
                                                echo '<option'.(isset($wpaicg_image_settings['num_outputs']) && $wpaicg_image_settings['num_outputs'] == $i ? ' selected':'').' value="'.esc_html($i).'">'.esc_html($i).'</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="wpaicg-mb-5">
                                        <label class="wpaicg-form-label">Number of Inference Steps</label>
                                        <input class="wpaicg_input num_inference_steps" type="number" name="num_inference_steps" id="num_inference_steps" value="<?php echo isset($wpaicg_image_settings['num_inference_steps']) ? esc_html($wpaicg_image_settings['num_inference_steps']) : '50'?>">
                                    </div>
                                    <div class="wpaicg-mb-5">
                                        <label class="wpaicg-form-label">Guidance Scale</label>
                                        <input class="wpaicg_input guidance_scale" type="text" name="guidance_scale" id="guidance_scale" value="<?php echo isset($wpaicg_image_settings['guidance_scale']) ? esc_html($wpaicg_image_settings['guidance_scale']) : '7.5'?>">
                                    </div>
                                    <div class="wpaicg-mb-5">
                                        <label class="wpaicg-form-label">Scheduler</label>
                                        <select name="scheduler">
                                            <?php
                                            foreach(array('DDIM', 'K_EULER', 'DPMSolverMultistep', 'K_EULER_ANCESTRAL', 'PNDM', 'KLMS') as $scheduler){
                                                echo '<option'.((isset($wpaicg_image_settings['scheduler']) && $wpaicg_image_settings['scheduler'] == $scheduler ) ||(!isset($wpaicg_image_settings['scheduler']) && $scheduler == 'DPMSolverMultistep') ? ' selected':'').' value="'.esc_html($scheduler).'">'.esc_html($scheduler).'</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                <?php
                                endif;
                                ?>
                            </div>
                        </div>
                    <?php
                    if($wpaicg_show_setting):
                    ?>
                        <?php
                        if(is_admin()){
                            echo '<button type="button" style="margin-top: 10px;" id="wpaicg-set-default-setting" class="button button-primary" data-type="'.($wpaicg_action == 'stable-diffusion' ? 'sd' : 'dalle').'">Set as Default</button>';
                        }
                        ?>
                    </div>
                </div>
                    <?php
                    endif;
                    ?>
            </form>
            <?php
            elseif($wpaicg_action == 'logs'):
                include __DIR__.'/wpaicg_image_generator_log.php';
            elseif($wpaicg_action == 'settings'):
                include __DIR__.'/wpaicg_image_generator_settings.php';
            elseif($wpaicg_action == 'shortcodes'):
                /*Show shortcode HTML*/
            ?>
            <p>Copy and paste the following shortcode into your post or page to display the image generator.</p>
            <p>If you want to display both DALL-E and Stable Diffusion, use: <code>[wpcgai_img]</code></p>
            <p>If you want to display only DALL-E, use: <code>[wpcgai_img dalle=yes]</code></p>
            <p>If you want to display only Stable Diffusion, use: <code>[wpcgai_img sd=yes]</code></p>
            <p>If you want to display the settings, use: <code>[wpcgai_img settings=yes]</code> or <code>[wpcgai_img dalle=yes settings=yes]</code> or <code>[wpcgai_img sd=yes settings=yes]</code></p>
            <?php
            endif;
            ?>
        </div>
    </div>
</div>
<?php
if($wpaicg_action !== 'shortcodes' && $wpaicg_action != 'logs'):
?>
<script>
    function getRandomPrompt() {
        <?php
        $wpaicg_art_file = WPAICG_PLUGIN_DIR . 'admin/data/art.json';
        $wpaicg_prompt_data = file_get_contents($wpaicg_art_file);
        $wpaicg_prompt_data = json_decode($wpaicg_prompt_data, true);
        if(count($wpaicg_image_prompts)){
            $wpaicg_prompt_data['prompts'] = $wpaicg_image_prompts;
        }
        ?>
        var randomIndex = Math.floor(Math.random() * <?php echo esc_html(count($wpaicg_prompt_data['prompts'])); ?>);
        document.getElementById("prompt").value = <?php echo json_encode($wpaicg_prompt_data['prompts']); ?> [randomIndex];
    }
    var wpaicgImageForm = document.getElementById('wpaicg-image-generator-form');
    var wpaicg_image_ajax_url = '<?php echo admin_url('admin-ajax.php')?>';
    var wpaicgImageGenerated = document.getElementsByClassName('image-generated')[0];
    var wpaicgImageGrid = document.getElementById('image-grid');
    var wpaicgImageLoading = document.getElementById('image-generate-loading');
    var wpaicgImageSaveBtn = document.getElementById('image-generator-save');
    var wpaicgImageMessage = document.getElementById('wpaicg_message');
    var wpaicgImageConvertBar = document.getElementById('wpaicg-convert-bar');
    var wpaicg_image_modal_close = document.getElementsByClassName('wpaicg_image_modal_close');
    var wpaicgNumberImages = document.getElementById('num_images');
    var wpaicgImageGenerateBtn = document.getElementById('wpaicg_button_generate');
    var wpaicgImageSelectAll = document.getElementById('wpaicg_image_select_all');
    let wpaicgImageSourceID = <?php echo !empty(get_the_ID()) ? get_the_ID() : 0?>;
    let wpaicgImageShortcode = '<?php echo esc_html($wpaicg_shortcode_text)?>';
    let wpaicgImageNonce = '<?php echo esc_html(wp_create_nonce( 'wpaicg-imagelog' ))?>';
    let wpaicgStartTime;
    <?php
        if(is_admin()):
    ?>
    let wpaicgSetDefault = document.getElementById('wpaicg-set-default-setting');
    if(wpaicgSetDefault) {
        wpaicgSetDefault.addEventListener('click', function () {
            let type = wpaicgSetDefault.getAttribute('data-type');
            wpaicgImageLoadingEffect(wpaicgSetDefault);
            let queryString = new URLSearchParams(new FormData(wpaicgImageForm)).toString();
            queryString += '&action=wpaicg_image_default';
            queryString += '&type_default='+type;
            const xhttp = new XMLHttpRequest();
            xhttp.open('POST', wpaicg_image_ajax_url);
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhttp.send(queryString);
            xhttp.onreadystatechange = function (oEvent) {
                if (xhttp.readyState === 4) {
                    if (xhttp.status === 200) {
                        let wpaicgParentCol = wpaicgSetDefault.parentElement;
                        let successMessage = document.createElement('div');
                        successMessage.style.color = '#009512';
                        successMessage.style.fontWeight = 'bold';
                        successMessage.innerHTML = 'Record updated successfully';
                        wpaicgParentCol.appendChild(successMessage);
                        setTimeout(function (){
                            successMessage.remove();
                        },2000);
                        wpaicgImageRmLoading(wpaicgSetDefault);
                    }
                }
            }
        });
    }
    <?php
        endif;
    ?>
    function wpaicgImageLoadingEffect(btn){
        btn.setAttribute('disabled','disabled');
        btn.innerHTML += '<span class="wpaicg-loader"></span>';
    }
    function wpaicgImageRmLoading(btn){
        btn.removeAttribute('disabled');
        btn.removeChild(btn.getElementsByTagName('span')[0]);
    }
    function wpaicgImageCloseModal() {
        document.querySelectorAll('.wpaicg_modal_close')[0].addEventListener('click', event => {
            document.querySelectorAll('.wpaicg_modal_content')[0].innerHTML = '';
            document.querySelectorAll('.wpaicg-overlay')[0].style.display = 'none';
            document.querySelectorAll('.wpaicg_modal')[0].style.display = 'none';
        })
    }
    function wpaicgSaveImageData(id){
        var item = document.getElementById('wpaicg-image-item-'+id);
        item.querySelectorAll('.wpaicg-image-item-alt')[0].value = document.querySelectorAll('.wpaicg_edit_item_alt')[0].value;
        item.querySelectorAll('.wpaicg-image-item-title')[0].value = document.querySelectorAll('.wpaicg_edit_item_title')[0].value;
        item.querySelectorAll('.wpaicg-image-item-caption')[0].value = document.querySelectorAll('.wpaicg_edit_item_caption')[0].value;
        item.querySelectorAll('.wpaicg-image-item-description')[0].value = document.querySelectorAll('.wpaicg_edit_item_description')[0].value;
        document.querySelectorAll('.wpaicg_modal_content')[0].innerHTML = '';
        document.querySelectorAll('.wpaicg-overlay')[0].style.display = 'none';
        document.querySelectorAll('.wpaicg_modal')[0].style.display = 'none';
    }
    function wpaicgModalImage(id){
        var item = document.getElementById('wpaicg-image-item-'+id);
        var alt = item.querySelectorAll('.wpaicg-image-item-alt')[0].value;
        var title = item.querySelectorAll('.wpaicg-image-item-title')[0].value;
        var caption = item.querySelectorAll('.wpaicg-image-item-caption')[0].value;
        var description = item.querySelectorAll('.wpaicg-image-item-description')[0].value;
        var url = item.querySelectorAll('input[type=checkbox]')[0].value;
        document.querySelectorAll('.wpaicg_modal_content')[0].innerHTML = '';
        document.querySelectorAll('.wpaicg-overlay')[0].style.display = 'block';
        document.querySelectorAll('.wpaicg_modal')[0].style.display = 'block';
        document.querySelectorAll('.wpaicg_modal_title')[0].innerHTML = 'Edit Image';
        var html = '<div class="wpaicg_grid_form">';
        html += '<div class="wpaicg_grid_form_2"><img src="'+url+'" style="width: 100%"></div>';
        html += '<div class="wpaicg_grid_form_1">';
        html += '<p><label>Alternative Text</label><input type="text" class="wpaicg_edit_item_alt" style="width: 100%" value="'+alt+'"></p>';
        html += '<p><label>Title</label><input type="text" class="wpaicg_edit_item_title" style="width: 100%" value="'+title+'"></p>';
        html += '<p><label>Caption</label><input type="text" class="wpaicg_edit_item_caption" style="width: 100%" value="'+caption+'"></p>';
        html += '<p><label>Description</label><textarea class="wpaicg_edit_item_description" style="width: 100%">'+description+'</textarea></p>';
        html += '<button onclick="wpaicgSaveImageData('+id+')" data-id="'+id+'" class="button button-primary wpaicg_edit_image_save" type="button">Save</button>';
        html += '</div>';
        html += '</div>';
        document.querySelectorAll('.wpaicg_modal_content')[0].innerHTML = html;
        wpaicgImageCloseModal();
    }
    function wpaicgViewModalImage(element){
        var url = element.getAttribute('src');
        document.querySelectorAll('.wpaicg_modal_content')[0].innerHTML = '';
        document.querySelectorAll('.wpaicg-overlay')[0].style.display = 'block';
        document.querySelectorAll('.wpaicg_modal')[0].style.display = 'block';
        document.querySelectorAll('.wpaicg_modal_title')[0].innerHTML = '<?php echo esc_html($wpaicg_image_view_text)?>';
        var html = '';
        html += '<img src="'+url+'" style="width: 100%">';
        document.querySelectorAll('.wpaicg_modal_content')[0].innerHTML = html;
        wpaicgImageCloseModal();
    }
    function wpaicgImageGenerator(data, start, max, multi_steps,form_action){
        const xhttp = new XMLHttpRequest();
        xhttp.open('POST', wpaicg_image_ajax_url);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send(data);
        xhttp.onreadystatechange = function(oEvent) {
            if (xhttp.readyState === 4) {
                if (xhttp.status === 200) {
                    var wpaicg_response = this.responseText;
                    res = JSON.parse(wpaicg_response);
                    if(res.status === 'success'){
                        for(var idx = 0; idx < res.imgs.length; idx++){
                            let idImageBox = idx;
                            if(multi_steps){
                                idImageBox = start -1;
                            }
                            var img = res.imgs[idx];
                            var html = '<div id="wpaicg-image-item-'+idImageBox+'" class="wpaicg-image-item wpaicg-image-item-'+idx+'" data-id="'+idImageBox+'">';
                            <?php
                            if(is_user_logged_in()):
                            ?>
                            html += '<label><input data-id="'+idImageBox+'" class="wpaicg-image-item-select" type="checkbox" name="image_url" value="'+img+'"></label>';
                            <?php
                            endif;
                            ?>
                            html += '<input value="'+res.title+'" class="wpaicg-image-item-alt" type="hidden" name="image_alt">';
                            html += '<input value="'+res.title+'" class="wpaicg-image-item-title" type="hidden" name="image_title">';
                            html += '<input value="'+res.title+'" class="wpaicg-image-item-caption" type="hidden" name="image_caption">';
                            html += '<input value="'+res.title+'" class="wpaicg-image-item-description" type="hidden" name="image_description">';
                            <?php
                            if(is_user_logged_in()):
                            ?>
                            html += '<img onclick="wpaicgModalImage(' + idImageBox + ')" src="' + img + '">';
                            <?php
                            else:
                            ?>
                            html += '<img onclick="wpaicgViewModalImage(this)" src="'+img+'">';
                            <?php
                            endif;
                            ?>
                            html += '</div>';
                            wpaicgImageGrid.innerHTML += html;
                        }
                        if(multi_steps){
                            if(start === max){
                                wpaicgImageRmLoading(wpaicgImageGenerateBtn);
                                wpaicgImageSelectAll.classList.remove('selectall')
                                wpaicgImageSelectAll.innerHTML = '<?php echo esc_html($wpaicg_image_select_all_text)?>';
                                wpaicgImageSelectAll.style.display = 'block';
                                wpaicgImageLoading.style.display = 'none';
                                wpaicgImageSaveBtn.style.display = 'block';
                            }
                            else{
                                wpaicgImageGenerator(data, start+1, max, multi_steps,form_action)
                            }
                        }
                        else{
                            if(form_action === 'wpaicg_image_generator'){
                                let endTime = new Date();
                                let timeDiff = endTime - wpaicgStartTime;
                                timeDiff = timeDiff/1000;
                                data += '&action=wpaicg_image_log&duration='+timeDiff+'&_wpnonce_image_log='+wpaicgImageNonce+'&shortcode=['+wpaicgImageShortcode+']&source_id='+wpaicgImageSourceID;
                                const xhttp = new XMLHttpRequest();
                                xhttp.open('POST', '<?php echo admin_url('admin-ajax.php')?>');
                                xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                                xhttp.send(data);
                                xhttp.onreadystatechange = function (oEvent) {
                                    if (xhttp.readyState === 4) {

                                    }
                                }
                            }
                            wpaicgImageRmLoading(wpaicgImageGenerateBtn);
                            wpaicgImageSelectAll.classList.remove('selectall')
                            wpaicgImageSelectAll.innerHTML = '<?php echo esc_html($wpaicg_image_select_all_text)?>';
                            wpaicgImageSelectAll.style.display = 'block';
                            wpaicgImageLoading.style.display = 'none';
                            wpaicgImageSaveBtn.style.display = 'block';
                        }
                    }
                    else{
                        wpaicgImageRmLoading(wpaicgImageGenerateBtn);
                        wpaicgImageLoading.style.display = 'none';
                        let errorMessage = document.createElement('div');
                        errorMessage.style.color = '#f00';
                        errorMessage.classList.add('wpaicg-image-error');
                        errorMessage.innerHTML = res.msg;
                        wpaicgImageGenerated.prepend(errorMessage);
                        setTimeout(function (){
                            errorMessage.remove();
                        },3000);
                    }
                }
                else{
                    wpaicgImageLoading.style.display = 'none';
                    wpaicgImageRmLoading(wpaicgImageGenerateBtn);
                    alert('Something went wrong');
                }
            }
        }

    }
    wpaicgImageForm.addEventListener('submit', function (e){
        e.preventDefault();
        var form_action = wpaicgImageForm.querySelectorAll('input[name=action]')[0].value;
        var num_images = parseInt(wpaicgNumberImages.value);
        if(num_images > 0){
            var wpaicg_error = false;
            if(form_action === 'wpaicg_image_stable_diffusion'){
                var prompt_strength = parseFloat(document.getElementById('prompt_strength').value);
                var num_inference_steps = parseFloat(document.getElementById('num_inference_steps').value);
                var guidance_scale = parseFloat(document.getElementById('guidance_scale').value);
                if(prompt_strength < 0 || prompt_strength > 1){
                    wpaicg_error = 'Please enter a valid prompt strength value between 0 and 1.'
                }
                else if(num_inference_steps < 1 || num_inference_steps > 500){
                    wpaicg_error = 'Please enter a valid number of inference steps value between 1 and 500.'
                }
                else if(guidance_scale < 1 || guidance_scale > 20){
                    wpaicg_error = 'Please enter a valid guidance scale value between 1 and 20.'
                }
            }
            if(wpaicg_error){
                alert(wpaicg_error);
            }
            else{
                const queryString = new URLSearchParams(new FormData(wpaicgImageForm)).toString();
                wpaicgImageSaveBtn.style.display = 'none';
                wpaicgImageLoadingEffect(wpaicgImageGenerateBtn);
                wpaicgImageConvertBar.style.display = 'none';
                wpaicgImageLoading.style.display = 'flex';
                wpaicgImageGrid.innerHTML = '';
                wpaicgImageSelectAll.style.display = 'none';
                let wpaicgImageError = document.getElementsByClassName('wpaicg-image-error');
                if(wpaicgImageError.length){
                    wpaicgImageError[0].remove();
                }
                if (form_action === 'wpaicg_image_stable_diffusion') {
                    wpaicgImageGenerator(queryString, 1, num_images, true,form_action);
                } else {
                    wpaicgStartTime = new Date();
                    wpaicgImageGenerator(queryString, 1, num_images, false,form_action);
                }
            }
        }
        else{
            alert('Please select least one image for generate')
        }
        return false;
    });
    <?php
    if(is_user_logged_in()):
    ?>
    function wpaicgSaveImage(items,start){
        if(start >= items.length){
            wpaicgImageConvertBar.getElementsByTagName('small')[0].innerHTML = items.length+'/'+items.length;
            wpaicgImageConvertBar.getElementsByTagName('span')[0].style.width = '100%';
            wpaicgImageMessage.innerHTML = 'Save images to media successfully';
            wpaicgImageRmLoading(wpaicgImageSaveBtn);
            setTimeout(function (){
                wpaicgImageMessage.innerHTML = '';
            },2000)
        }
        else{
            var id = items[start];
            var item = document.getElementById('wpaicg-image-item-'+id);
            var data = 'action=wpaicg_save_image_media';
            data += '&image_alt='+item.querySelectorAll('.wpaicg-image-item-alt')[0].value;
            data += '&image_title='+item.querySelectorAll('.wpaicg-image-item-title')[0].value;
            data += '&image_caption='+item.querySelectorAll('.wpaicg-image-item-caption')[0].value;
            data += '&image_description='+item.querySelectorAll('.wpaicg-image-item-description')[0].value;
            data += '&image_url='+encodeURIComponent(item.querySelectorAll('.wpaicg-image-item-select')[0].value);
            data +='&nonce=<?php echo wp_create_nonce('wpaicg-ajax-nonce')?>';
            const xhttp = new XMLHttpRequest();
            xhttp.open('POST', wpaicg_image_ajax_url);
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhttp.send(data);
            xhttp.onreadystatechange = function(oEvent) {
                if (xhttp.readyState === 4) {
                    if (xhttp.status === 200) {
                        var wpaicg_response = this.responseText;
                        res = JSON.parse(wpaicg_response);
                        if(res.status === 'success'){
                            var currentPos = start+1;
                            var percent = Math.ceil(currentPos*100/items.length);
                            wpaicgImageConvertBar.getElementsByTagName('small')[0].innerHTML = currentPos+'/'+items.length;
                            wpaicgImageConvertBar.getElementsByTagName('span')[0].style.width = percent+'%';
                            wpaicgSaveImage(items, start+1);
                        }
                        else{
                            wpaicgImageConvertBar.classList.add('wpaicg_error');
                            wpaicgImageRmLoading(wpaicgImageSaveBtn);
                            alert(res.msg);
                        }
                    } else {
                        alert('Something went wrong');
                        wpaicgImageConvertBar.classList.add('wpaicg_error');
                        wpaicgImageRmLoading(wpaicgImageSaveBtn);
                    }
                }
            }
        }
    }
    wpaicgImageSelectAll.addEventListener('click', function (e){
        if(wpaicgImageSelectAll.classList.contains('selectall')){
            wpaicgImageSelectAll.classList.remove('selectall');
            wpaicgImageSelectAll.innerHTML = '<?php echo esc_html($wpaicg_image_select_all_text)?>';
            document.querySelectorAll('.wpaicg-image-item input[type=checkbox]').forEach(function(item){
                item.checked = false;
            })
        }
        else{
            wpaicgImageSelectAll.classList.add('selectall');
            wpaicgImageSelectAll.innerHTML = 'Unselect';
            document.querySelectorAll('.wpaicg-image-item input[type=checkbox]').forEach(function(item){
                item.checked = true;
            })
        }
    });
    wpaicgImageSaveBtn.addEventListener('click', function (e){
        var items = [];
        document.querySelectorAll('.wpaicg-image-item input[type=checkbox]').forEach(function(item){
            if(item.checked){
                items.push(item.getAttribute('data-id'));
            }
        });
        if(items.length){
            wpaicgImageConvertBar.style.display = 'block';
            wpaicgImageConvertBar.classList.remove('wpaicg_error');
            wpaicgImageConvertBar.getElementsByTagName('small')[0].innerHTML = '0/'+items.length;
            wpaicgImageConvertBar.getElementsByTagName('span')[0].style.width = 0;
            wpaicgImageMessage.innerHTML = '';
            wpaicgImageLoadingEffect(wpaicgImageSaveBtn);
            wpaicgSaveImage(items,0);
        }
        else{
            alert('Please select least one image to save');
        }
    })
    <?php
    endif;
    ?>
</script>
<?php
endif;
?>

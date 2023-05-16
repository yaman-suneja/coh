<?php
if ( ! defined( 'ABSPATH' ) ) exit;
$wpaicg_image_source = get_option('wpaicg_image_source','');
$wpaicg_featured_image_source = get_option('wpaicg_featured_image_source','');
?>
<div id="tabs-5">
    <div class="wpcgai_form_row">
        <label class="wpcgai_label">Image Source:</label>
        <select class="regular-text" id="label_img_size" name="wpaicg_image_source" >
            <option value="">None</option>
            <option<?php echo $wpaicg_image_source == 'dalle' ? ' selected':''?> value="dalle">DALL-E</option>
            <option<?php echo $wpaicg_image_source == 'pexels' ? ' selected':''?> value="pexels">Pexels</option>
        </select>
    </div>
    <div class="wpcgai_form_row">
        <label class="wpcgai_label">Featured Image Source:</label>
        <select class="regular-text" id="label_img_size" name="wpaicg_featured_image_source" >
            <option value="">None</option>
            <option<?php echo $wpaicg_featured_image_source == 'dalle' ? ' selected':''?> value="dalle">DALL-E</option>
            <option<?php echo $wpaicg_featured_image_source == 'pexels' ? ' selected':''?> value="pexels">Pexels</option>
        </select>
    </div>
    <hr>
    <div class="wpcgai_form_row">
        <p><b>DALL-E</b></p>
        <label class="wpcgai_label">Image Size:</label>
        <select class="regular-text" id="label_img_size" name="wpaicg_settings[img_size]" >
            <option value="256x256" <?php
            echo  ( esc_html($existingValue['img_size']) == '256x256' ? 'selected' : '' ) ;
            ?>>Small (256x256)</option>
            <option value="512x512" <?php
            echo  ( esc_html( $existingValue['img_size'] ) == '512x512' ? 'selected' : '' ) ;
            ?>>Medium (512x512)</option>
            <option value="1024x1024" <?php
            echo  ( esc_html( $existingValue['img_size'] ) == '1024x1024' ? 'selected' : '' ) ;
            ?>>Big (1024x1024)</option>
        </select>
        <a class="wpcgai_help_link" href="https://gptaipower.com/dall-e-image-size/" target="_blank">?</a>
    </div>
    <div class="wpcgai_form_row">
        <label class="wpcgai_label">Style:</label>
        <?php
        $_wpaicg_image_style = get_option( '_wpaicg_image_style', '' );
        ?>
        <select class="regular-text" id="label_img_style" name="_wpaicg_image_style" >
            <option value="">None</option>
            <option<?php
            echo  ( esc_html( $_wpaicg_image_style ) == 'abstract' ? ' selected' : '' ) ;
            ?> value="abstract">Abstract</option>
            <option<?php
            echo  ( esc_html( $_wpaicg_image_style ) == 'modern' ? ' selected' : '' ) ;
            ?> value="modern">Modern</option>
            <option<?php
            echo  ( esc_html( $_wpaicg_image_style ) == 'impressionist' ? ' selected' : '' ) ;
            ?> value="impressionist">Impressionist</option>
            <option<?php
            echo  ( esc_html( $_wpaicg_image_style ) == 'popart' ? ' selected' : '' ) ;
            ?> value="popart">Pop Art</option>
            <option<?php
            echo  ( esc_html( $_wpaicg_image_style ) == 'cubism' ? ' selected' : '' ) ;
            ?> value="cubism">Cubism</option>
            <option<?php
            echo  ( esc_html( $_wpaicg_image_style ) == 'surrealism' ? ' selected' : '' ) ;
            ?> value="surrealism">Surrealism</option>
            <option<?php
            echo  ( esc_html( $_wpaicg_image_style ) == 'contemporary' ? ' selected' : '' ) ;
            ?> value="contemporary">Contemporary</option>
            <option<?php
            echo  ( esc_html( $_wpaicg_image_style ) == 'cantasy' ? ' selected' : '' ) ;
            ?> value="cantasy">Fantasy</option>
            <option<?php
            echo  ( esc_html( $_wpaicg_image_style ) == 'graffiti' ? ' selected' : '' ) ;
            ?> value="graffiti">Graffiti</option>
        </select>
        <a class="wpcgai_help_link" href="https://gptaipower.com/customizing-dall-e-generated-images-with-the-art-style-feature/" target="_blank">?</a>
    </div>
    <?php
    $wpaicg_art_file = WPAICG_PLUGIN_DIR . 'admin/data/art.json';

    $wpaicg_painter_data = file_get_contents($wpaicg_art_file);
    $wpaicg_painter_data = json_decode($wpaicg_painter_data, true);

    $wpaicg_style_data = file_get_contents($wpaicg_art_file);
    $wpaicg_style_data = json_decode($wpaicg_style_data, true);

    $wpaicg_photo_file = WPAICG_PLUGIN_DIR . 'admin/data/photo.json';

    $wpaicg_photo_data = file_get_contents($wpaicg_photo_file);
    $wpaicg_photo_data = json_decode($wpaicg_photo_data, true);
    $wpaicg_custom_image_settings = get_option('wpaicg_custom_image_settings',[]);
    ?>
    <div class="wpaicg_more_image_settings" style="display: none">
        <div class="wpcgai_form_row">
            <label for="artist" class="wpcgai_label">Artist:</label>
            <select class="regular-text" name="wpaicg_custom_image_settings[artist]" id="artist">
                <?php
                foreach ($wpaicg_painter_data['painters'] as $key => $value) {
                    echo '<option'.((isset($wpaicg_custom_image_settings['artist']) && $wpaicg_custom_image_settings['artist'] == $value) || (!isset($wpaicg_custom_image_settings['artist']) && $value) == 'None' ? ' selected':'').' value="' . esc_html($value) . '">' . esc_html($value) . '</option>';
                }
                ?>
            </select>
        </div>
        <div class="wpcgai_form_row">
            <label for="photography_style" class="wpcgai_label">Photography:</label>
            <select class="regular-text" name="wpaicg_custom_image_settings[photography_style]" id="photography_style">
            <?php
            foreach ($wpaicg_photo_data['photography_style'] as $key => $value) {
                echo '<option'.((isset($wpaicg_custom_image_settings['photography_style']) && $wpaicg_custom_image_settings['photography_style'] == $value) || (!isset($wpaicg_custom_image_settings['photography_style']) && $value == 'Landscape') ? ' selected':'').' value="' . esc_html($value) . '">' . esc_html($value) . '</option>';
            }
            ?>
            </select>
        </div>
        <div class="wpcgai_form_row">
            <?php
            echo '<label for="lighting" class="wpcgai_label">Lighting:</label>'."\n";
            echo '<select class="regular-text" name="wpaicg_custom_image_settings[lighting]" id="lighting">'."\n";
            foreach ($wpaicg_photo_data['lighting'] as $key => $value) {
                echo '<option'.(isset($wpaicg_custom_image_settings['lighting']) && $wpaicg_custom_image_settings['lighting'] == $value ? ' selected':'').' value="' . esc_html($value) . '">' . esc_html($value) . '</option>';
            }
            echo '</select>';
            ?>
        </div>
        <div class="wpcgai_form_row">
            <?php
            echo '<label for="subject" class="wpcgai_label">Subject:</label>'."\n";
            echo '<select class="regular-text" name="wpaicg_custom_image_settings[subject]" id="subject">'."\n";
            foreach ($wpaicg_photo_data['subject'] as $key => $value) {
                echo '<option'.((isset($wpaicg_custom_image_settings['subject']) && $wpaicg_custom_image_settings['subject'] == $value) || (!isset($wpaicg_custom_image_settings['subject']) && $value == 'None') ? ' selected':'').' value="' . esc_html($value) . '">' . esc_html($value) . '</option>';
            }
            echo '</select>';
            ?>
        </div>
        <div class="wpcgai_form_row">
            <?php
            echo '<label for="camera_settings" class="wpcgai_label">Camera:</label>'."\n";
            echo '<select class="regular-text" name="wpaicg_custom_image_settings[camera_settings]" id="camera_settings">'."\n";
            foreach ($wpaicg_photo_data['camera_settings'] as $key => $value) {
                echo '<option'.(isset($wpaicg_custom_image_settings['camera_settings']) && $wpaicg_custom_image_settings['camera_settings'] == $value ? ' selected':'').' value="' . esc_html($value) . '">' . esc_html($value) . '</option>';
            }
            echo '</select>';
            ?>
        </div>
        <div class="wpcgai_form_row">
            <?php
            echo '<label for="composition" class="wpcgai_label">Composition:</label>'."\n";
            echo '<select class="regular-text" name="wpaicg_custom_image_settings[composition]" id="composition">'."\n";
            foreach ($wpaicg_photo_data['composition'] as $key => $value) {
                echo '<option'.(isset($wpaicg_custom_image_settings['composition']) && $wpaicg_custom_image_settings['composition'] == $value ? ' selected':'').' value="' . esc_html($value) . '">' . esc_html($value) . '</option>';
            }
            echo '</select>';
            ?>
        </div>
        <div class="wpcgai_form_row">
            <?php
            echo '<label for="resolution" class="wpcgai_label">Resolution:</label>'."\n";
            echo '<select class="regular-text" name="wpaicg_custom_image_settings[resolution]" id="resolution">'."\n";
            foreach ($wpaicg_photo_data['resolution'] as $key => $value) {
                echo '<option'.(isset($wpaicg_custom_image_settings['resolution']) && $wpaicg_custom_image_settings['resolution'] == $value ? ' selected':'').' value="' . esc_html($value) . '">' . esc_html($value) . '</option>';
            }
            echo '</select>';
            ?>
        </div>
        <div class="wpcgai_form_row">
            <?php
            echo '<label for="color" class="wpcgai_label">Color:</label>'."\n";
            echo '<select class="regular-text" name="wpaicg_custom_image_settings[color]" id="color">'."\n";
            foreach ($wpaicg_photo_data['color'] as $key => $value) {
                echo '<option'.(isset($wpaicg_custom_image_settings['color']) && $wpaicg_custom_image_settings['color'] == $value ? ' selected':'').' value="' . esc_html($value) . '">' . esc_html($value) . '</option>';
            }
            echo '</select>';
            ?>
        </div>
        <div class="wpcgai_form_row">
            <?php
            echo '<label for="special_effects" class="wpcgai_label">Special Effects:</label>'."\n";
            echo '<select class="regular-text" name="wpaicg_custom_image_settings[special_effects]" id="special_effects">."\n"';
            foreach ($wpaicg_photo_data['special_effects'] as $key => $value) {
                echo '<option'.(isset($wpaicg_custom_image_settings['special_effects']) && $wpaicg_custom_image_settings['special_effects'] == $value ? ' selected':'').' value="' . esc_html($value) . '">' . esc_html($value) . '</option>';
            }
            echo '</select>';
            ?>
        </div>
    </div>
    <div class="mb-5">
        <a href="javascript:void(0)" class="wpaicg_show_image_settings">[+ More Settings]</a>
    </div>
    <script>
        jQuery(document).ready(function ($){
            $('.wpaicg_show_image_settings').click(function (){
                $(this).toggleClass('wpaig_opened');
                $('.wpaicg_more_image_settings').slideToggle();
                if($(this).hasClass('wpaig_opened')){
                    $(this).html('[- Hide Settings]');
                }
                else{
                    $(this).html('[+ More Settings]');
                }
            })
        })
    </script>
    <?php
    $wpaicg_sd_api_key = get_option('wpaicg_sd_api_key','');
    ?>
    <hr>
    <p><b>Stable Diffusion 🚀🚀🚀</b></p>
    <div class="wpcgai_form_row">
        <label class="wpcgai_label">API Key:</label>
        <input value="<?php echo esc_html($wpaicg_sd_api_key)?>" type="text" class="regular-text" name="wpaicg_sd_api_key">
        <a class="wpcgai_help_link" href="https://replicate.com/account" target="_blank">Get API Key</a>
    </div>
    <?php
    $wpaicg_sd_api_version = get_option('wpaicg_sd_api_version','');
    ?>
    <div class="wpcgai_form_row">
        <label class="wpcgai_label">Version:</label>
        <input value="<?php echo esc_html($wpaicg_sd_api_version)?>" type="text" class="regular-text" name="wpaicg_sd_api_version" placeholder="Leave blank for default">
    </div>
    <hr>
    <p><b>Pexels</b></p>
    <?php
    $wpaicg_pexels_api = get_option('wpaicg_pexels_api','');
    $wpaicg_pexels_orientation = get_option('wpaicg_pexels_orientation','');
    $wpaicg_pexels_size = get_option('wpaicg_pexels_size','');
    ?>
    <div class="wpcgai_form_row">
        <label class="wpcgai_label">API Key:</label>
        <input value="<?php echo esc_html($wpaicg_pexels_api)?>" type="text" class="regular-text" name="wpaicg_pexels_api">
        <a class="wpcgai_help_link" href="https://www.pexels.com/api/new/" target="_blank">Get API Key</a>
    </div>
    <div class="wpcgai_form_row">
        <label class="wpcgai_label">Orientation:</label>
        <select class="regular-text" id="label_img_size" name="wpaicg_pexels_orientation" >
            <option value="">None</option>
            <option<?php echo $wpaicg_pexels_orientation == 'landscape' ? ' selected':''?> value="landscape">Landscape</option>
            <option<?php echo $wpaicg_pexels_orientation == 'portrait' ? ' selected':''?> value="portrait">Portrait</option>
            <option<?php echo $wpaicg_pexels_orientation == 'square' ? ' selected':''?> value="square">Square</option>
        </select>
    </div>
    <div class="wpcgai_form_row">
        <label class="wpcgai_label">Size:</label>
        <select class="regular-text" id="label_img_size" name="wpaicg_pexels_size" >
            <option value="">None</option>
            <option<?php echo $wpaicg_pexels_size == 'large' ? ' selected':''?> value="large">Large</option>
            <option<?php echo $wpaicg_pexels_size == 'medium' ? ' selected':''?> value="medium">Medium</option>
            <option<?php echo $wpaicg_pexels_size == 'small' ? ' selected':''?> value="small">Small</option>
        </select>
    </div>
    <div class="wpcgai_form_row">
        <hr>
        <p><b>Shortcodes</b></p>
        <p>Copy and paste the following shortcode into your post or page to display the image generator.</p>
        <p>If you want to display both DALL-E and Stable Diffusion, use: <code>[wpcgai_img]</code></p>
        <p>If you want to display DALL-E only, use: <code>[wpcgai_img dalle=yes]</code></p>
        <p>If you want to display Stable Diffusion only, use: <code>[wpcgai_img sd=yes]</code></p>
        <p>If you want to display the settings, use: <code>[wpcgai_img settings=yes]</code> or <code>[wpcgai_img dalle=yes settings=yes]</code> or <code>[wpcgai_img sd=yes settings=yes]</code></p>
    </div>
</div>

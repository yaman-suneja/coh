<?php
if ( ! defined( 'ABSPATH' ) ) exit;
global $wpdb;
$wpaicg_categories = array();
$wpaicg_items = array();
$wpaicg_icons = array();
$wpaicg_models = array();
$wpaicg_authors = array('default' => array('name' => 'GPT AI Power','count' => 0));
if(file_exists(WPAICG_PLUGIN_DIR.'admin/data/gptcategories.json')){
    $wpaicg_file_content = file_get_contents(WPAICG_PLUGIN_DIR.'admin/data/gptcategories.json');
    $wpaicg_file_content = json_decode($wpaicg_file_content, true);
    if($wpaicg_file_content && is_array($wpaicg_file_content) && count($wpaicg_file_content)){
        foreach($wpaicg_file_content as $key=>$item){
            $wpaicg_categories[$key] = trim($item);
        }
    }
}
if(file_exists(WPAICG_PLUGIN_DIR.'admin/data/icons.json')){
    $wpaicg_file_content = file_get_contents(WPAICG_PLUGIN_DIR.'admin/data/icons.json');
    $wpaicg_file_content = json_decode($wpaicg_file_content, true);
    if($wpaicg_file_content && is_array($wpaicg_file_content) && count($wpaicg_file_content)){
        foreach($wpaicg_file_content as $key=>$item){
            $wpaicg_icons[$key] = trim($item);
        }
    }
}
if(file_exists(WPAICG_PLUGIN_DIR.'admin/data/gptforms.json')){
    $wpaicg_file_content = file_get_contents(WPAICG_PLUGIN_DIR.'admin/data/gptforms.json');
    $wpaicg_file_content = json_decode($wpaicg_file_content, true);
    if($wpaicg_file_content && is_array($wpaicg_file_content) && count($wpaicg_file_content)){
        foreach($wpaicg_file_content as $item){
            $item['type'] = 'json';
            $item['author'] = 'default';
            $wpaicg_authors['default']['count'] += 1;
            $wpaicg_items[] = $item;
        }
    }
}
if(file_exists(WPAICG_PLUGIN_DIR.'admin/data/models.json')){
    $wpaicg_file_content = file_get_contents(WPAICG_PLUGIN_DIR.'admin/data/models.json');
    $wpaicg_file_content = json_decode($wpaicg_file_content, true);
    if($wpaicg_file_content && is_array($wpaicg_file_content) && isset($wpaicg_file_content['models']) && is_array($wpaicg_file_content['models']) && count($wpaicg_file_content['models'])){
        foreach($wpaicg_file_content['models'] as $item){
            $wpaicg_models[] = $item['name'];
        }
    }
}
$sql = "SELECT p.ID as id,p.post_title as title,p.post_author as author, p.post_content as description";
$wpaicg_meta_keys = array('fields','editor','prompt','response','category','engine','max_tokens','temperature','top_p','best_of','frequency_penalty','presence_penalty','stop','color','icon','bgcolor','header','dans','ddraft','dclear','dnotice','generate_text','noanswer_text','draft_text','clear_text','stop_text','cnotice_text');

foreach ($wpaicg_meta_keys as $wpaicg_meta_key) {
    $sql .= $wpdb->prepare(
        ", (SELECT {$wpaicg_meta_key}.meta_value FROM {$wpdb->postmeta} {$wpaicg_meta_key} WHERE {$wpaicg_meta_key}.meta_key = %s AND p.ID = {$wpaicg_meta_key}.post_id LIMIT 1) as {$wpaicg_meta_key}",
        "wpaicg_form_{$wpaicg_meta_key}"
    );
}

$sql .= $wpdb->prepare(" FROM {$wpdb->posts} p WHERE p.post_type = %s AND p.post_status = 'publish' ORDER BY p.post_date DESC", 'wpaicg_form');
$wpaicg_custom_templates = $wpdb->get_results($sql,ARRAY_A);
if($wpaicg_custom_templates && is_array($wpaicg_custom_templates) && count($wpaicg_custom_templates)){
    foreach ($wpaicg_custom_templates as $wpaicg_custom_template){
        $wpaicg_custom_template['type'] = 'custom';
        $wpaicg_items[] = $wpaicg_custom_template;
        if(!isset($wpaicg_authors[$wpaicg_custom_template['author']])){
            $prompt_author = get_user_by('ID', $wpaicg_custom_template['author']);
            $wpaicg_authors[$wpaicg_custom_template['author']] = array('name' => $prompt_author->display_name, 'count' => 1);
        }
        else{
            $wpaicg_authors[$wpaicg_custom_template['author']]['count'] += 1;
        }
    }
}
$wpaicg_per_page = 36;
wp_enqueue_editor();
wp_enqueue_script('wp-color-picker');
wp_enqueue_style('wp-color-picker');
$kses_defaults = wp_kses_allowed_html( 'post' );
$svg_args = array(
    'svg'   => array(
        'class'           => true,
        'aria-hidden'     => true,
        'aria-labelledby' => true,
        'role'            => true,
        'xmlns'           => true,
        'width'           => true,
        'height'          => true,
        'viewbox'         => true // <= Must be lower case!
    ),
    'g'     => array( 'fill' => true ),
    'title' => array( 'title' => true ),
    'path'  => array(
        'd'               => true,
        'fill'            => true
    )
);
$allowed_tags = array_merge( $kses_defaults, $svg_args );
?>
<style>
    .wpaicg-template-icon{
        width: 70px;
        height: 70px;
        border-radius: 3px;
        display: flex;
        justify-content: center;
        align-items: center;
        color: #fff;
    }
    .wpaicg-template-icon svg{
        fill: currentColor;
        width: 50px;
        height: 50px;
    }
    .wpaicg-template-item{
        cursor: pointer;
        height: 100px;
        position: relative;
    }
    .wpaicg-template-content{
        margin-left: 10px;
        flex: 1;
    }
    .wpaicg-template-content p{
        margin: 5px 0;
        font-size: 12px;
        height: 36px;
        overflow: hidden;
    }
    .wpaicg_modal{
        position: relative;
        top: 5%;
        height: 90%;
    }
    .disappear-item{
        position: absolute;
        top: -10000px;
    }
    .wpaicg-template-items{
        position: relative;
        overflow-y: hidden;
    }
    .wpaicg-paginate .page-numbers{
        background: #e5e5e5;
        margin-right: 5px;
        cursor: pointer;
    }
    .wpaicg-paginate .page-numbers.current{
        background: #fff;
    }
    .wpaicg-template-settings > div{
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .wpaicg-template-settings > div > strong{
        display: inline-block;
        width: 50%;
    }
    .wpaicg-template-settings > div > strong > small{
        font-weight: normal;
        display: block;
    }
    .wpaicg-template-settings > div > input,.wpaicg-template-settings > div > select{
        width: 48%;
        margin: 0;
    }
    .wpaicg-template-settings .wpaicg-template-sample{
        display: block;
        position: relative;
    }
    .wpaicg-template-settings .wpaicg-template-sample:hover .wpaicg-template-response{
        display: block;
    }
    .wpaicg-template-settings .wpaicg-template-response{
        background: #333;
        border: 1px solid #444;
        position: absolute;
        border-radius: 3px;
        color: #fff;
        padding: 5px;
        width: 100%;
        bottom: calc(100% + 5px);
        right: calc(50% - 55px);
        z-index: 99;
        display: none;
    }
    .wpaicg-template-settings .wpaicg-template-response:after,.wpaicg-template-settings .wpaicg-template-response:before{
        top: 100%;
        left: 50%;
        border: solid transparent;
        content: "";
        height: 0;
        width: 0;
        position: absolute;
        pointer-events: none;
    }
    .wpaicg-template-settings .wpaicg-template-response:before{
        border-color: rgba(68, 68, 68, 0);
        border-top-color: #444;
        border-width: 7px;
        margin-left: -7px;
    }
    .wpaicg-template-settings .wpaicg-template-response:after{
        border-color: rgba(51, 51, 51, 0);
        border-top-color: #333;
        border-width: 6px;
        margin-left: -6px;
    }
    .wpaicg_modal_content{
        max-height: calc(100% - 103px);
        overflow-y: auto;
    }
    .wpaicg_notice_text {
        padding: 10px;
        text-align: left;
        margin-bottom: 12px;
    }
    .wpaicg-create-template{
        width: 100%;
        display: block!important;
        margin-bottom: 10px!important;
    }
    .wpaicg-template-icons{}
    .wpaicg-template-icons span{
        padding: 10px;
        border-radius: 4px;
        border: 1px solid #ccc;
        display: inline-flex;
        justify-content: center;
        align-items: center;
        margin-right: 5px;
        margin-bottom: 5px;
        cursor: pointer;
        color: #333;
    }
    .wpaicg-template-icons span svg{
        fill: currentColor;
        width: 30px;
        height: 30px;
    }
    .wpaicg-template-icons span.icon_selected{
        background: #343434;
        color: #fff;
    }
    .wp-picker-holder{
        position: absolute;
        z-index: 99;
    }
    .wp-picker-container{
        position: relative;
    }
    .wpaicg-template-action{
        position: absolute;
        right: 0;
        top: 37px;
        display: none;
    }
    .wpaicg-template-item:hover .wpaicg-template-action{
        display: block;
    }
    .wpaicg-template-action-edit{}
    .wpaicg-template-action-delete{
        background: #9d0000!important;
        border-color: #9b0000!important;
        color: #fff!important;
    }
    .wpaicg-template-form-field{
        display: block;
        padding: 10px;
        background: #d7d7d7;
        position: relative;
        border-radius: 4px;
        margin-bottom: 5px;
    }
    .wpaicg-template-form-field .wpaicg-grid{
        grid-row-gap: 0;
    }
    .wpaicg-template-form-field input{
        display: block;
        width: 100%;
    }
    .wpaicg-field-delete{
        position: absolute;
        top: 4px;
        right: 5px;
        width: 20px;
        height: 20px;
        display: flex;
        justify-content: center;
        align-items: center;
        background: #b00;
        border-radius: 2px;
        color: #fff;
        font-size: 20px;
        font-family: Arial, serif;
        cursor: pointer;
    }
    .wpaicg-form-fields input[type=text],.wpaicg-form-fields select,.wpaicg-form-fields input[type=url],.wpaicg-form-fields input[type=email],.wpaicg-form-fields input[type=number]{
        width: 50%;
        margin-bottom: 10px;
    }
    .wpaicg-modal-tabs{
        margin: 0;
        display: flex;
    }
    .wpaicg-modal-tabs li{
        padding: 12px 15px;
        border-top-left-radius: 3px;
        border-top-right-radius: 3px;
        background: #2271b1;
        margin-bottom: 0;
        margin-right: 5px;
        border-top: 1px solid #2271b1;
        border-left: 1px solid #2271b1;
        border-right: 1px solid #2271b1;
        cursor: pointer;
        position: relative;
        top: 1px;
        color: #fff;
    }
    .wpaicg-modal-tabs li.wpaicg-active{
        background: #fff;
        color: #333;
    }
    .wpaicg-modal-tab-content{
        border: 1px solid #ccc;
    }
    .wpaicg-modal-tab{
        padding: 10px;
    }
</style>
<div class="wpaicg-template-form-field-default" style="display: none">
    <div class="wpaicg-template-form-field">
        <div class="wpaicg-grid">
            <div class="">
                <strong class="wpaicg-d-block mb-5">Label</strong>
                <input type="text" name="fields[0][label]" required class="wpaicg-create-template-field-label">
            </div>
            <div class="wpaicg-grid-2">
                <strong class="wpaicg-d-block mb-5">ID</strong>
                <input placeholder="example_field" type="text" name="fields[0][id]" required class="wpaicg-create-template-field-id">
                <small>Add this field in your prompt with following format: {example_field}</small>
            </div>
            <div class="">
                <strong class="wpaicg-d-block mb-5">Type</strong>
                <select name="fields[0][type]" class="wpaicg-create-template-field-type">
                    <option value="text">Text</option>
                    <option value="select">Drop-Down</option>
                    <option value="number">Number</option>
                    <option value="email">Email</option>
                    <option value="url">URL</option>
                    <option value="textarea">Textarea</option>
                    <option value="checkbox">Checkbox</option>
                    <option value="radio">Radio</option>
                </select>
            </div>
            <div class="wpaicg-create-template-field-min-main">
                <strong class="wpaicg-d-block mb-5">Min</strong>
                <input placeholder="Optional" type="number" name="fields[0][min]" class="wpaicg-create-template-field-min">
            </div>
            <div class="wpaicg-create-template-field-max-main">
                <strong class="wpaicg-d-block mb-5">Max</strong>
                <input placeholder="Optional" type="number" name="fields[0][min]" class="wpaicg-create-template-field-max">
            </div>
            <div class="wpaicg-create-template-field-rows-main" style="display: none">
                <strong class="wpaicg-d-block mb-5">Rows</strong>
                <input placeholder="Optional" type="number" name="fields[0][rows]" class="wpaicg-create-template-field-rows">
            </div>
            <div class="wpaicg-create-template-field-cols-main" style="display: none">
                <strong class="wpaicg-d-block mb-5">Cols</strong>
                <input placeholder="Optional" type="number" name="fields[0][cols]" class="wpaicg-create-template-field-cols">
            </div>
        </div>
        <div class="wpaicg-create-template-field-options-main" style="display: none">
            <strong class="wpaicg-d-block mb-5">Options</strong>
            <textarea name="fields[0][options]" class="wpaicg-create-template-field-options wpaicg-w-100"></textarea>
            <small>Separate by "|"</small>
        </div>
        <span class="wpaicg-field-delete">&times;</span>
    </div>
</div>
<div class="wpaicg-create-template-content" style="display: none">
    <?php
    wp_nonce_field('wpaicg_formai_save');
    ?>
    <input type="hidden" name="action" value="wpaicg_update_template">
    <input type="hidden" name="id" value="" class="wpaicg-create-template-id">
    <ul class="wpaicg-modal-tabs">
        <li class="wpaicg-active" data-target="properties">Properties</li>
        <li data-target="ai-engine">AI Engine</li>
        <li data-target="fields">Fields</li>
        <li data-target="style">Style</li>
        <li data-target="frontend">Frontend</li>
    </ul>
    <div class="wpaicg-modal-tab-content wpaicg-mb-10">
        <div class="wpaicg-modal-tab wpaicg-modal-tab-properties">
            <div class="wpaicg-grid wpaicg-mb-10">
                <div class="wpaicg-grid-3">
                    <strong class="wpaicg-d-block mb-5">Title</strong>
                    <input type="text" name="title" required class="regular-text wpaicg-w-100 wpaicg-create-template-title">
                </div>
                <div class="wpaicg-grid-3">
                    <strong class="wpaicg-d-block mb-5">Category</strong>
                    <select name="category" class="wpaicg-w-100 wpaicg-create-template-category">
                        <?php
                        foreach($wpaicg_categories as $key=>$wpaicg_category){
                            echo '<option value="'.esc_html($key).'">'.esc_html($wpaicg_category).'</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="wpaicg-mb-10">
                <strong class="wpaicg-d-block mb-5">Description</strong>
                <input type="text" name="description" required class="regular-text wpaicg-w-100 wpaicg-create-template-description">
            </div>
            <div class="wpaicg-mb-10">
                <strong class="wpaicg-d-block mb-5">Prompt</strong>
                <textarea name="prompt" required class="regular-text wpaicg-w-100 wpaicg-create-template-prompt"></textarea>
            </div>
            <div class="wpaicg-mb-10">
                <strong class="wpaicg-d-block mb-5">Sample Response</strong>
                <textarea name="response" class="regular-text wpaicg-w-100 wpaicg-create-template-response"></textarea>
            </div>
        </div>
        <div class="wpaicg-modal-tab wpaicg-modal-tab-ai-engine" style="display: none">
            <div class="wpaicg-grid wpaicg-mb-10">
                <div class="wpaicg-grid-1">
                    <strong class="wpaicg-d-block mb-5">Engine</strong>
                    <select name="engine" class="wpaicg-w-100 wpaicg-create-template-engine" required>
                        <option value="gpt-3.5-turbo">gpt-3.5-turbo</option>
                        <?php
                        foreach($wpaicg_models as $wpaicg_model){
                            echo '<option value="' . esc_html($wpaicg_model) . '">' . esc_html($wpaicg_model) . '</option>';
                        }
                        ?>
                        <option value="gpt-4">gpt-4</option>
                        <option value="gpt-4-32k">gpt-4-32k</option>
                    </select>
                </div>
                <div>
                    <strong class="wpaicg-d-block mb-5">Max Tokens</strong>
                    <input type="text" name="max_tokens" class="regular-text wpaicg-w-100 wpaicg-create-template-max_tokens">
                </div>
                <div>
                    <strong class="wpaicg-d-block mb-5">Temperature</strong>
                    <input type="text" name="temperature" class="regular-text wpaicg-w-100 wpaicg-create-template-temperature">
                </div>
                <div>
                    <strong class="wpaicg-d-block mb-5">Top P</strong>
                    <input type="text" name="top_p" class="regular-text wpaicg-w-100 wpaicg-create-template-top_p">
                </div>
                <div>
                    <strong class="wpaicg-d-block mb-5">Best Of</strong>
                    <input type="text" name="best_of" class="regular-text wpaicg-w-100 wpaicg-create-template-best_of">
                </div>
                <div>
                    <strong class="wpaicg-d-block mb-5">Frequency Penalty</strong>
                    <input type="text" name="frequency_penalty" class="regular-text wpaicg-w-100 wpaicg-create-template-frequency_penalty">
                </div>
                <div>
                    <strong class="wpaicg-d-block mb-5">Presence Penalty</strong>
                    <input type="text" name="presence_penalty" class="regular-text wpaicg-w-100 wpaicg-create-template-presence_penalty">
                </div>
                <div>
                    <strong class="wpaicg-d-block mb-5">Stop</strong>
                    <input type="text" name="stop" class="regular-text wpaicg-w-100 wpaicg-create-template-stop">
                </div>
            </div>
        </div>
        <div class="wpaicg-modal-tab wpaicg-modal-tab-fields" style="display: none">
            <div class="wpaicg-mb-10">
                <div class="mb-5 wpaicg-d-flex wpaicg-align-items-center"><strong class="">Fields</strong><button type="button" class="wpaicg-create-form-field button button-primary button-small wpaicg-align-items-center" style="display: inline-flex;margin-left: 5px;"><span class="dashicons dashicons-plus"></span></button></div>
                <div class="wpaicg-template-fields"></div>
            </div>
        </div>
        <div class="wpaicg-modal-tab wpaicg-modal-tab-style" style="display: none">
            <div class="wpaicg-grid wpaicg-mb-10">
                <div class="wpaicg-grid-2">
                    <strong class="wpaicg-d-block mb-5">Icon Color</strong>
                    <input type="text" name="color" class="regular-text wpaicg-w-100 wpaicg-create-template-color">
                </div>
                <div class="wpaicg-grid-2">
                    <strong class="wpaicg-d-block mb-5">Background Color</strong>
                    <input type="text" name="bgcolor" class="regular-text wpaicg-w-100 wpaicg-create-template-bgcolor">
                </div>
            </div>
            <div class="wpaicg-mb-10">
                <strong class="wpaicg-d-block mb-5">Icon</strong>
                <input type="hidden" class="wpaicg-create-template-icon" name="icon" value="robot">
                <div class="wpaicg-template-icons">
                    <?php
                    foreach($wpaicg_icons as $key=>$wpaicg_icon){
                        echo '<span data-key="'.esc_html($key).'">'.wp_kses($wpaicg_icon,$allowed_tags).'</span>';
                    }
                    ?>
                </div>
            </div>
        </div>
        <div class="wpaicg-modal-tab wpaicg-modal-tab-frontend" style="display: none">
            <div class="wpaicg-grid wpaicg-mb-10">
                <div class="wpaicg-grid-1">
                    <strong class="wpaicg-d-block mb-5">Result</strong>
                    <select name="editor" class="wpaicg-w-100 wpaicg-create-template-editor">
                        <option value="textarea">Text Editor</option>
                        <option value="div">Inline</option>
                    </select>
                </div>
                <div class="wpaicg-grid-1">
                    <strong class="wpaicg-d-block mb-5">Header</strong>
                    <select name="header" class="wpaicg-w-100 wpaicg-create-template-header">
                        <option value="">Yes</option>
                        <option value="no">No</option>
                    </select>
                </div>
                <div class="wpaicg-grid-1">
                    <strong class="wpaicg-d-block mb-5">Number of Answers</strong>
                    <select name="dans" class="wpaicg-w-100 wpaicg-create-template-dans">
                        <option value="">Yes</option>
                        <option value="no">No</option>
                    </select>
                </div>
                <div class="wpaicg-grid-1">
                    <strong class="wpaicg-d-block mb-5">Draft Button</strong>
                    <select name="ddraft" class="wpaicg-w-100 wpaicg-create-template-ddraft">
                        <option value="">Yes</option>
                        <option value="no">No</option>
                    </select>
                </div>
                <div class="wpaicg-grid-1">
                    <strong class="wpaicg-d-block mb-5">Clear Button</strong>
                    <select name="dclear" class="wpaicg-w-100 wpaicg-create-template-dclear">
                        <option value="">Yes</option>
                        <option value="no">No</option>
                    </select>
                </div>
                <div class="wpaicg-grid-1">
                    <strong class="wpaicg-d-block mb-5">Notice</strong>
                    <select name="dnotice" class="wpaicg-w-100 wpaicg-create-template-dnotice">
                        <option value="">Yes</option>
                        <option value="no">No</option>
                    </select>
                </div>
                <div class="wpaicg-grid-2">
                    <strong class="wpaicg-d-block mb-5">Generate Button</strong>
                    <input value="Generate" type="text" name="generate_text" class="regular-text wpaicg-w-100 wpaicg-create-template-generate_text">
                </div>
                <div class="wpaicg-grid-2">
                    <strong class="wpaicg-d-block mb-5">No. of Answer Text</strong>
                    <input value="Number of Answers" type="text" name="noanswer_text" class="regular-text wpaicg-w-100 wpaicg-create-template-noanswer_text">
                </div>
                <div class="wpaicg-grid-2">
                    <strong class="wpaicg-d-block mb-5">Draft Text</strong>
                    <input value="Save Draft" type="text" name="draft_text" class="regular-text wpaicg-w-100 wpaicg-create-template-draft_text">
                </div>
                <div class="wpaicg-grid-2">
                    <strong class="wpaicg-d-block mb-5">Clear Text</strong>
                    <input value="Clear" type="text" name="clear_text" class="regular-text wpaicg-w-100 wpaicg-create-template-clear_text">
                </div>
                <div class="wpaicg-grid-2">
                    <strong class="wpaicg-d-block mb-5">Stop Text</strong>
                    <input value="Stop" type="text" name="stop_text" class="regular-text wpaicg-w-100 wpaicg-create-template-stop_text">
                </div>
                <div class="wpaicg-grid-2">
                    <strong class="wpaicg-d-block mb-5">Notice Text</strong>
                    <input value="Please register to save your result" type="text" name="cnotice_text" class="regular-text wpaicg-w-100 wpaicg-create-template-cnotice_text">
                </div>
            </div>
        </div>
    </div>
    <button class="button button-primary wpaicg-create-template-save">Save</button>
</div>
<p class="wpaicg_notice_text">Please note that you can only embed <u><b>one form per page</b></u>.</p>
<?php
if(isset($_GET['update_template']) && !empty($_GET['update_template'])):
    ?>
    <p style="padding: 6px 12px;border: 1px solid green;border-radius: 3px;background: lightgreen;">
        <strong>Success:</strong> Congrats! Your template created! You can add this shortcode to your page: [wpaicg_form id=<?php echo esc_html($_GET['update_template'])?> custom=yes]
    </p>
<?php
endif;
?>
<div class="wpaicg_template">
    <div class="wpaicg-grid">
        <div class="wpaicg-grid-1">
            <button class="button button-primary wpaicg-create-template" type="button">Design Your Form</button>
            <strong>Author</strong>
            <ul class="wpaicg-list wpaicg-mb-10 wpaicg-authors">
                <?php
                if(count($wpaicg_authors)){
                    foreach($wpaicg_authors as $key=>$wpaicg_author){
                        ?>
                        <li><label><input type="checkbox" value="<?php echo esc_attr($key)?>">&nbsp;<?php echo esc_html($wpaicg_author['name'])?> (<?php echo esc_html($wpaicg_author['count'])?>)</label></li>
                        <?php
                    }
                }
                ?>
            </ul>
            <strong>Category</strong>
            <ul class="wpaicg-list wpaicg-categories">
                <?php
                if(count($wpaicg_categories)){
                    foreach($wpaicg_categories as $wpaicg_category){
                        ?>
                        <li><label><input type="checkbox" value="<?php echo sanitize_title($wpaicg_category)?>">&nbsp;<?php echo esc_html($wpaicg_category)?></label></li>
                        <?php
                    }
                }
                ?>
            </ul>
        </div>
        <div class="wpaicg-grid-5">
            <div class="wpaicg-mb-10">
                <input class="wpaicg-w-100 wpaicg-d-block wpaicg-template-search" type="text" placeholder="Search Template">
            </div>
            <div class="wpaicg-grid-three wpaicg-template-items">
                <?php
                if(count($wpaicg_items)):
                    foreach($wpaicg_items as $wpaicg_item):
                        $wpaicg_item_categories = array();
                        $wpaicg_item_categories_name = array();
                        if(isset($wpaicg_item['category']) && !empty($wpaicg_item['category'])){
                            $wpaicg_item_categories = array_map('trim', explode(',', $wpaicg_item['category']));
                        }
                        $wpaicg_icon = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><path d="M320 0c17.7 0 32 14.3 32 32V96H480c35.3 0 64 28.7 64 64V448c0 35.3-28.7 64-64 64H160c-35.3 0-64-28.7-64-64V160c0-35.3 28.7-64 64-64H288V32c0-17.7 14.3-32 32-32zM208 384c-8.8 0-16 7.2-16 16s7.2 16 16 16h32c8.8 0 16-7.2 16-16s-7.2-16-16-16H208zm96 0c-8.8 0-16 7.2-16 16s7.2 16 16 16h32c8.8 0 16-7.2 16-16s-7.2-16-16-16H304zm96 0c-8.8 0-16 7.2-16 16s7.2 16 16 16h32c8.8 0 16-7.2 16-16s-7.2-16-16-16H400zM264 256c0-22.1-17.9-40-40-40s-40 17.9-40 40s17.9 40 40 40s40-17.9 40-40zm152 40c22.1 0 40-17.9 40-40s-17.9-40-40-40s-40 17.9-40 40s17.9 40 40 40zM48 224H64V416H48c-26.5 0-48-21.5-48-48V272c0-26.5 21.5-48 48-48zm544 0c26.5 0 48 21.5 48 48v96c0 26.5-21.5 48-48 48H576V224h16z"/></svg>';
                        if(isset($wpaicg_item['icon']) && !empty($wpaicg_item['icon']) && isset($wpaicg_icons[$wpaicg_item['icon']]) && !empty($wpaicg_icons[$wpaicg_item['icon']])){
                            $wpaicg_icon = $wpaicg_icons[$wpaicg_item['icon']];
                        }
                        $wpaicg_icon_color = isset($wpaicg_item['color']) && !empty($wpaicg_item['color']) ? $wpaicg_item['color'] : '#19c37d';
                        $wpaicg_engine = isset($wpaicg_item['engine']) && !empty($wpaicg_item['engine']) ? $wpaicg_item['engine'] : $this->wpaicg_engine;
                        $wpaicg_max_tokens = isset($wpaicg_item['max_tokens']) && !empty($wpaicg_item['max_tokens']) ? $wpaicg_item['max_tokens'] : $this->wpaicg_max_tokens;
                        $wpaicg_temperature = isset($wpaicg_item['temperature']) && !empty($wpaicg_item['temperature']) ? $wpaicg_item['temperature'] : $this->wpaicg_temperature;
                        $wpaicg_top_p = isset($wpaicg_item['top_p']) && !empty($wpaicg_item['top_p']) ? $wpaicg_item['top_p'] : $this->wpaicg_top_p;
                        $wpaicg_best_of = isset($wpaicg_item['best_of']) && !empty($wpaicg_item['best_of']) ? $wpaicg_item['best_of'] : $this->wpaicg_best_of;
                        $wpaicg_frequency_penalty = isset($wpaicg_item['frequency_penalty']) && !empty($wpaicg_item['frequency_penalty']) ? $wpaicg_item['frequency_penalty'] : $this->wpaicg_frequency_penalty;
                        $wpaicg_presence_penalty = isset($wpaicg_item['presence_penalty']) && !empty($wpaicg_item['presence_penalty']) ? $wpaicg_item['presence_penalty'] : $this->wpaicg_presence_penalty;
                        $wpaicg_stop = isset($wpaicg_item['stop']) && !empty($wpaicg_item['stop']) ? $wpaicg_item['stop'] : $this->wpaicg_stop;
                        $wpaicg_stop_lists = '';
                        if(is_array($wpaicg_stop) && count($wpaicg_stop)){
                            foreach($wpaicg_stop as $item_stop){
                                if($item_stop === "\n"){
                                    $item_stop = '\n';
                                }
                                $wpaicg_stop_lists = empty($wpaicg_stop_lists) ? $item_stop : ','.$item_stop;
                            }
                        }
                        elseif(is_array($wpaicg_stop) && !count($wpaicg_stop)){
                            $wpaicg_stop_lists = '';
                        }
                        else{
                            $wpaicg_stop_lists = $wpaicg_stop;
                        }
                        if(count($wpaicg_item_categories)){
                            foreach($wpaicg_item_categories as $wpaicg_item_category){
                                if(isset($wpaicg_categories[$wpaicg_item_category]) && !empty($wpaicg_categories[$wpaicg_item_category])){
                                    $wpaicg_item_categories_name[] = $wpaicg_categories[$wpaicg_item_category];
                                }
                            }
                        }
                        $wpaicg_fields = isset($wpaicg_item['fields']) && !empty($wpaicg_item['fields']) ? $wpaicg_item['fields'] : '';
                        if(!empty($wpaicg_fields) && !is_array($wpaicg_fields) && strpos($wpaicg_fields,'\"') !== false) {
                            $wpaicg_fields = str_replace('\"', '[QUOTE]', $wpaicg_fields);
                        }
                        if(!empty($wpaicg_fields) && !is_array($wpaicg_fields) && strpos($wpaicg_fields,"\'") !== false) {
                            $wpaicg_fields = str_replace('\\', '', $wpaicg_fields);
                        }
                        ?>
                        <div
                            id="wpaicg-template-item-<?php echo esc_html($wpaicg_item['id'])?>"
                            data-fields="<?php echo esc_html($wpaicg_item['type'] == 'custom' ? $wpaicg_fields : json_encode($wpaicg_fields,JSON_UNESCAPED_UNICODE ))?>"
                            data-title="<?php echo esc_html($wpaicg_item['title'])?>"
                            data-type="<?php echo esc_html($wpaicg_item['type'])?>"
                            data-id="<?php echo esc_html($wpaicg_item['id'])?>"
                            data-post_title="<?php echo esc_html($wpaicg_item['title'])?>"
                            data-desc="<?php echo esc_html(@$wpaicg_item['description'])?>"
                            data-description="<?php echo esc_html(@$wpaicg_item['description'])?>"
                            data-icon="<?php echo esc_html(@$wpaicg_item['icon'])?>"
                            data-color="<?php echo esc_html($wpaicg_icon_color)?>"
                            data-engine="<?php echo esc_html($wpaicg_engine)?>"
                            data-max_tokens="<?php echo esc_html($wpaicg_max_tokens)?>"
                            data-temperature="<?php echo esc_html($wpaicg_temperature)?>"
                            data-top_p="<?php echo esc_html($wpaicg_top_p)?>"
                            data-best_of="<?php echo esc_html($wpaicg_best_of)?>"
                            data-frequency_penalty="<?php echo esc_html($wpaicg_frequency_penalty)?>"
                            data-presence_penalty="<?php echo esc_html($wpaicg_presence_penalty)?>"
                            data-stop="<?php echo esc_html($wpaicg_stop_lists)?>"
                            data-categories="<?php echo esc_html(implode(', ',$wpaicg_item_categories_name))?>"
                            data-category="<?php echo esc_html($wpaicg_item['category'])?>"
                            data-prompt="<?php echo esc_html(@$wpaicg_item['prompt']);?>"
                            data-estimated="<?php echo isset($wpaicg_item['estimated']) ? esc_html($wpaicg_item['estimated']): '';?>"
                            data-response="<?php echo esc_html(@$wpaicg_item['response']);?>"
                            data-editor="<?php echo isset($wpaicg_item['editor']) && $wpaicg_item['editor'] == 'div' ? 'div' : 'textarea'?>"
                            data-header="<?php echo isset($wpaicg_item['header']) ? esc_html($wpaicg_item['header']) : '';?>"
                            data-bgcolor="<?php echo isset($wpaicg_item['bgcolor']) ? esc_html($wpaicg_item['bgcolor']) : '';?>"
                            data-dans="<?php echo isset($wpaicg_item['dans']) ? esc_html($wpaicg_item['dans']) : '';?>"
                            data-ddraft="<?php echo isset($wpaicg_item['ddraft']) ? esc_html($wpaicg_item['ddraft']) : '';?>"
                            data-dclear="<?php echo isset($wpaicg_item['dclear']) ? esc_html($wpaicg_item['dclear']) : '';?>"
                            data-dnotice="<?php echo isset($wpaicg_item['dnotice']) ? esc_html($wpaicg_item['dnotice']) : '';?>"
                            data-generate_text="<?php echo isset($wpaicg_item['generate_text']) && !empty($wpaicg_item['generate_text']) ? esc_html($wpaicg_item['generate_text']) : 'Generate';?>"
                            data-noanswer_text="<?php echo isset($wpaicg_item['noanswer_text']) && !empty($wpaicg_item['noanswer_text']) ? esc_html($wpaicg_item['noanswer_text']) : 'Number of Answers';?>"
                            data-draft_text="<?php echo isset($wpaicg_item['draft_text']) && !empty($wpaicg_item['draft_text']) ? esc_html($wpaicg_item['draft_text']) : 'Save Draft';?>"
                            data-clear_text="<?php echo isset($wpaicg_item['clear_text']) && !empty($wpaicg_item['clear_text']) ? esc_html($wpaicg_item['clear_text']) : 'Clear';?>"
                            data-stop_text="<?php echo isset($wpaicg_item['stop_text']) && !empty($wpaicg_item['stop_text']) ? esc_html($wpaicg_item['stop_text']) : 'Stop';?>"
                            data-cnotice_text="<?php echo isset($wpaicg_item['cnotice_text']) && !empty($wpaicg_item['cnotice_text']) ? esc_html($wpaicg_item['cnotice_text']) : 'Please register to save your result';?>"
                            class="wpaicg-template-item wpaicg-d-flex wpaicg-align-items-center <?php echo implode(' ',$wpaicg_item_categories)?><?php echo ' user-'.esc_html($wpaicg_item['author'])?><?php echo ' wpaicg-template-item-'.$wpaicg_item['type'].'-'.esc_html($wpaicg_item['id']);?>">
                            <div class="wpaicg-template-icon" style="background: <?php echo esc_html($wpaicg_icon_color)?>"><?php echo wp_kses($wpaicg_icon,$allowed_tags)?></div>
                            <div class="wpaicg-template-content">
                                <strong><?php echo isset($wpaicg_item['title']) && !empty($wpaicg_item['title']) ? esc_html($wpaicg_item['title']) : ''?></strong>
                                <?php
                                if(isset($wpaicg_item['description']) && !empty($wpaicg_item['description'])){
                                    echo '<p>'.esc_html($wpaicg_item['description']).'</p>';
                                }
                                ?>
                            </div>
                            <?php
                            if($wpaicg_item['type'] == 'custom'):
                                ?>
                                <div class="wpaicg-template-action">
                                    <button class="button button-small wpaicg-template-action-edit" data-id="<?php echo esc_html($wpaicg_item['id'])?>">Edit</button>
                                    <button class="button button-small wpaicg-template-action-delete" data-id="<?php echo esc_html($wpaicg_item['id'])?>">Delete</button>
                                </div>
                            <?php
                            endif;
                            ?>
                        </div>
                    <?php
                    endforeach;
                endif;
                ?>
            </div>
            <div class="wpaicg-paginate"></div>
        </div>
    </div>
</div>
<div class="wpaicg-template-modal-content" style="display: none">
    <form method="post" action="">
        <div class="wpaicg-grid-three">
            <div class="wpaicg-grid-2">
                <input type="hidden" class="wpaicg-template-response-type" value="textarea">
                <textarea style="display: none" class="wpaicg-template-title" rows="8"></textarea>
                <textarea style="display: none" name="title" class="wpaicg-template-title-filled" rows="8"></textarea>
                <div class="wpaicg-form-fields"></div>
                <div class="wpaicg-mb-10">
                    <strong class="wpaicg-template-text-noanswer_text">Number of Answers</strong>
                    <select class="wpaicg-template-max-lines">
                        <?php
                        for($i=1;$i<=10;$i++){
                            echo '<option value="'.esc_html($i).'">'.esc_html($i).'</option>';
                        }
                        ?>
                    </select>
                    <button class="button button-primary wpaicg-generate-button wpaicg-template-text-generate_text">Generate</button>
                    &nbsp;<button type="button" class="button button-primary wpaicg-template-stop-generate wpaicg-template-text-stop_text" style="display: none">Stop</button>
                </div>
                <div class="mb-5">
                    <div class="wpaicg-template-response-editor">
                        <textarea class="wpaicg-template-result" rows="12"></textarea>
                    </div>
                    <div class="wpaicg-template-response-element"></div>
                </div>
                <div class="wpaicg-template-save-result" style="display: none">
                    <button type="button" class="button button-primary wpaicg-template-save-draft wpaicg-template-text-draft_text">Save Draft</button>
                    <button type="button" class="button wpaicg-template-clear wpaicg-template-text-clear_text">Clear</button>
                </div>
            </div>
            <div class="wpaicg-grid-1">
                <div class="wpaicg-mb-10 wpaicg-template-settings">
                    <button type="button" style="width: 100%" class="button button-primary wpaicg-template-action-customize" data-id="">Customize</button>
                    <h3>Settings</h3>
                    <div class="mb-5 wpaicg-template-engine">
                        <strong>Engine: </strong>
                        <select name="engine">
                            <option value="gpt-3.5-turbo">gpt-3.5-turbo</option>
                            <?php
                            foreach($wpaicg_models as $wpaicg_model){
                                echo '<option value="' . esc_html($wpaicg_model) . '">' . esc_html($wpaicg_model) . '</option>';
                            }
                            ?>
                            <option value="gpt-4">gpt-4</option>
                            <option value="gpt-4-32k">gpt-4-32k</option>
                        </select>
                    </div>
                    <div class="mb-5 wpaicg-template-max_tokens"><strong>Max Tokens: </strong><input name="max_tokens" type="text" min="1" max="2048"></div>
                    <div class="mb-5 wpaicg-template-temperature"><strong>Temperature: </strong><input name="temperature" type="text" min="0" max="1" step="any"></div>
                    <div class="mb-5 wpaicg-template-top_p"><strong>Top P: </strong><input name="top_p" type="text" min="0" max="1"></div>
                    <div class="mb-5 wpaicg-template-best_of"><strong>Best Of: </strong><input name="best_of" type="text" min="1" max="20"></div>
                    <div class="mb-5 wpaicg-template-frequency_penalty"><strong>Frequency Penalty: </strong><input name="frequency_penalty" type="text" min="0" max="2" step="any"></div>
                    <div class="mb-5 wpaicg-template-presence_penalty"><strong>Presence Penalty: </strong><input name="presence_penalty" type="text" min="0" max="2" step="any"></div>
                    <div class="mb-5 wpaicg-template-stop"><strong>Stop:<small>separate by commas</small></strong><input name="stop" type="text"></div>
                    <div class="mb-5 wpaicg-template-estimated"><strong>Estimated: </strong><span></span></div>
                    <div class="mb-5 wpaicg-template-post_title"><input type="hidden" name="post_title"></div>
                    <div class="mb-5 wpaicg-template-sample">Sample Response?<div class="wpaicg-template-response"></div></div>
                    <div style="padding: 5px;background: #ffc74a;border-radius: 4px;color: #000;" class="wpaicg-template-shortcode"></div>
                </div>
            </div>
        </div>
    </form>
</div>


<script>
    jQuery(document).ready(function ($){
        let prompt_id;
        let prompt_name;
        let prompt_response = '';
        let wpaicg_limited_token = false;
        let wp_nonce = '<?php echo esc_html(wp_create_nonce( 'wpaicg-formlog' ))?>'
        /*Modal tab*/
        $(document).on('click','.wpaicg-modal-tabs li', function (e){
            var tab = $(e.currentTarget);
            var target =  tab.attr('data-target');
            var modal = tab.closest('.wpaicg_modal_content');
            modal.find('.wpaicg-modal-tabs li').removeClass('wpaicg-active');
            tab.addClass('wpaicg-active');
            modal.find('.wpaicg-modal-tab').hide();
            modal.find('.wpaicg-modal-tab-'+target).show();
        })
        /*Create Template*/
        var wpaicgTemplateContent = $('.wpaicg-create-template-content');
        var wpaicgTemplageFieldDefault = $('.wpaicg-template-form-field-default');
        var wpaicgCreateField = $('.wpaicg-template-form-field-default');
        var wpaicgFieldInputs = ['label','id','type','min','max','options','rows','cols'];
        $(document).on('click','.wpaicg-template-icons span', function (e){
            var icon = $(e.currentTarget);
            icon.parent().find('span').removeClass('icon_selected');
            icon.addClass('icon_selected');
            icon.parent().parent().find('.wpaicg-create-template-icon').val(icon.attr('data-key'));
        });
        $(document).on('click','.wpaicg-field-delete', function(e){
            $(e.currentTarget).parent().remove();
            wpaicgSortField();
        });
        function wpaicgSortField(){
            $('.wpaicg-create-template-form .wpaicg-template-form-field').each(function(idx, item){
                $.each(wpaicgFieldInputs, function(idxy, field){
                    $(item).find('.wpaicg-create-template-field-'+field).attr('name','fields['+idx+']['+field+']');
                });
            })
        }
        $(document).on('click','.wpaicg-create-form-field', function(e){
            $('.wpaicg-create-template-form .wpaicg-template-fields').append(wpaicgCreateField.html());
            wpaicgSortField();
        });
        $(document).on('change','.wpaicg-create-template-field-type', function(e){
            var type = $(e.currentTarget).val();
            var parentEl = $(e.currentTarget).closest('.wpaicg-template-form-field');
            if(type === 'select' || type === 'checkbox' || type === 'radio'){
                parentEl.find('.wpaicg-create-template-field-options-main').show();
                parentEl.find('.wpaicg-create-template-field-min-main').hide();
                parentEl.find('.wpaicg-create-template-field-max-main').hide();
                parentEl.find('.wpaicg-create-template-field-rows-main').hide();
                parentEl.find('.wpaicg-create-template-field-cols-main').hide();
            }
            else if(type === 'textarea'){
                parentEl.find('.wpaicg-create-template-field-rows-main').show();
                parentEl.find('.wpaicg-create-template-field-cols-main').show();
                parentEl.find('.wpaicg-create-template-field-options-main').hide();
                parentEl.find('.wpaicg-create-template-field-min-main').show();
                parentEl.find('.wpaicg-create-template-field-max-main').show();
            }
            else{
                parentEl.find('.wpaicg-create-template-field-rows-main').hide();
                parentEl.find('.wpaicg-create-template-field-cols-main').hide();
                parentEl.find('.wpaicg-create-template-field-options-main').hide();
                parentEl.find('.wpaicg-create-template-field-min-main').show();
                parentEl.find('.wpaicg-create-template-field-max-main').show();
            }
        })
        $('.wpaicg-create-template').click(function (){
            $('.wpaicg_modal_content').empty();
            $('.wpaicg_modal_title').html('Design Your Form');
            $('.wpaicg_modal_content').html('<form action="" method="post" class="wpaicg-create-template-form">'+wpaicgTemplateContent.html()+'</form>');
            $('.wpaicg-create-template-form .wpaicg-create-template-color').wpColorPicker();
            $('.wpaicg-create-template-form .wpaicg-create-template-bgcolor').wpColorPicker();
            $('.wpaicg-create-template-form .wpaicg-create-template-category').val('generation');
            $('.wpaicg-create-template-form .wpaicg-template-icons span[data-key=robot]').addClass('icon_selected');
            $('.wpaicg-overlay').show();
            //$('.wpaicg_modal').css('height','60%');
            $('.wpaicg_modal').show();
        })
        $(document).on('click','.wpaicg-template-item .wpaicg-template-action-delete',function (e){
            var id = $(e.currentTarget).attr('data-id');
            var conf = confirm('Are you sure?');
            if(conf){
                $('.wpaicg-template-item-custom-'+id).remove();
                $.post('<?php echo admin_url('admin-ajax.php')?>', {action: 'wpaicg_template_delete', id: id,'nonce': '<?php echo wp_create_nonce('wpaicg-ajax-nonce')?>'});
            }
        });
        $(document).on('click','.wpaicg-template-item .wpaicg-template-action-edit',function (e){
            var id = $(e.currentTarget).attr('data-id');
            var item = $('.wpaicg-template-item-custom-'+id);
            $('.wpaicg_modal_content').empty();
            $('.wpaicg_modal_title').html('Edit your Template');
            $('.wpaicg_modal_content').html('<form action="" method="post" class="wpaicg-create-template-form">'+wpaicgTemplateContent.html()+'</form>');
            var form = $('.wpaicg-create-template-form');
            var wpaicg_template_keys = ['engine','editor','title','description','max_tokens','temperature','top_p','best_of','frequency_penalty','presence_penalty','stop','prompt','response','category','icon','color','bgcolor','header','dans','ddraft','dclear','dnotice','generate_text','noanswer_text','draft_text','clear_text','stop_text','cnotice_text'];
            for(var i = 0; i < wpaicg_template_keys.length;i++){
                var wpaicg_template_key = wpaicg_template_keys[i];
                var wpaicg_template_key_value = item.attr('data-'+wpaicg_template_key);
                form.find('.wpaicg-create-template-'+wpaicg_template_key).val(wpaicg_template_key_value);
                if(wpaicg_template_key === 'icon'){
                    if(wpaicg_template_key_value === ''){
                        wpaicg_template_key_value = 'robot';
                    }
                    $('.wpaicg-create-template-form .wpaicg-template-icons span[data-key='+wpaicg_template_key_value+']').addClass('icon_selected');
                }
            }
            var wpaicg_form_fields = item.attr('data-fields');
            if(wpaicg_form_fields !== '') {
                wpaicg_form_fields = wpaicg_form_fields.replace(/\\/g,'');
                wpaicg_form_fields = JSON.parse(wpaicg_form_fields);
                $.each(wpaicg_form_fields, function(idx, field){
                    $('.wpaicg-create-template-form .wpaicg-template-fields').append('<div id="wpaicg-template-form-field-'+idx+'">'+wpaicgCreateField.html()+'</div>');
                    var field_item = $('#wpaicg-template-form-field-'+idx);
                    $.each(wpaicgFieldInputs, function(idxy, field_key){
                        if(field[field_key] !== undefined) {
                            var field_value = field[field_key];
                            field_value = field_value.replace('[QUOTE]','"');
                            field_item.find('.wpaicg-create-template-field-' + field_key).val(field_value);
                            if(field_key === 'type' && (field_value === 'select' || field_value === 'checkbox' || field_value === 'radio')){
                                field_item.find('.wpaicg-create-template-field-options-main').show();
                                field_item.find('.wpaicg-create-template-field-min-main').hide();
                                field_item.find('.wpaicg-create-template-field-max-main').hide();
                            }
                            else if(field_key === 'type' && field_value === 'textarea'){
                                field_item.find('.wpaicg-create-template-field-rows-main').show();
                                field_item.find('.wpaicg-create-template-field-cols-main').show();
                            }
                        }
                    });
                    wpaicgSortField();
                })
            }
            form.find('.wpaicg-create-template-id').val(id);
            $('.wpaicg-create-template-form .wpaicg-create-template-color').wpColorPicker();
            $('.wpaicg-create-template-form .wpaicg-create-template-bgcolor').wpColorPicker();
            //$('.wpaicg_modal').css('height','60%');
            $('.wpaicg-overlay').show();
            $('.wpaicg_modal').show();
        });

        $(document).on('click','.wpaicg-template-action-customize',function (e){
            var id = $(e.currentTarget).attr('data-id');
            var item = $('.wpaicg-template-item-json-'+id);
            $('.wpaicg_modal_content').empty();
            $('.wpaicg_modal_title').html('Customize your Template');
            $('.wpaicg_modal_content').html('<form action="" method="post" class="wpaicg-create-template-form">'+wpaicgTemplateContent.html()+'</form>');
            var form = $('.wpaicg-create-template-form');
            var wpaicg_template_keys = ['engine','editor','title','description','max_tokens','temperature','top_p','best_of','frequency_penalty','presence_penalty','stop','prompt','response','category','icon','color','bgcolor','header','dans','ddraft','dclear','dnotice','generate_text','noanswer_text','draft_text','clear_text','stop_text','cnotice_text'];
            for(var i = 0; i < wpaicg_template_keys.length;i++){
                var wpaicg_template_key = wpaicg_template_keys[i];
                var wpaicg_template_key_value = item.attr('data-'+wpaicg_template_key);
                form.find('.wpaicg-create-template-'+wpaicg_template_key).val(wpaicg_template_key_value);
                if(wpaicg_template_key === 'icon'){
                    if(wpaicg_template_key_value === ''){
                        wpaicg_template_key_value = 'robot';
                    }
                    $('.wpaicg-create-template-form .wpaicg-template-icons span[data-key='+wpaicg_template_key_value+']').addClass('icon_selected');
                }
            }
            var wpaicg_form_fields = item.attr('data-fields');
            if(wpaicg_form_fields !== '') {
                wpaicg_form_fields = wpaicg_form_fields.replace(/\\/g,'');
                wpaicg_form_fields = JSON.parse(wpaicg_form_fields);
                $.each(wpaicg_form_fields, function(idx, field){
                    $('.wpaicg-create-template-form .wpaicg-template-fields').append('<div id="wpaicg-template-form-field-'+idx+'">'+wpaicgCreateField.html()+'</div>');
                    var field_item = $('#wpaicg-template-form-field-'+idx);
                    $.each(wpaicgFieldInputs, function(idxy, field_key){
                        if(field[field_key] !== undefined) {
                            var field_value = field[field_key];
                            if(field_key === 'options'){
                                if(typeof field_value === 'object'){
                                    field_value = field_value.join('|');
                                }
                                field_item.find('.wpaicg-create-template-field-' + field_key).val(field_value);
                            }
                            else {
                                field_item.find('.wpaicg-create-template-field-' + field_key).val(field_value);
                            }
                            if(field_key === 'type' && (field_value === 'select' || field_value === 'checkbox' || field_value === 'radio')){
                                field_item.find('.wpaicg-create-template-field-options-main').show();
                                field_item.find('.wpaicg-create-template-field-min-main').hide();
                                field_item.find('.wpaicg-create-template-field-max-main').hide();
                            }
                            else if(field_key === 'type' && field_value === 'textarea'){
                                field_item.find('.wpaicg-create-template-field-rows-main').show();
                                field_item.find('.wpaicg-create-template-field-cols-main').show();
                            }
                        }
                    });
                    wpaicgSortField();
                })
            }
            $('.wpaicg-create-template-form .wpaicg-create-template-color').wpColorPicker();
            $('.wpaicg-create-template-form .wpaicg-create-template-bgcolor').wpColorPicker();
            //$('.wpaicg_modal').css('height','60%');
            $('.wpaicg-overlay').show();
            $('.wpaicg_modal').show();
        });
        $(document).on('submit','.wpaicg-create-template-form', function(e){
            var form = $(e.currentTarget);
            var btn = form.find('.wpaicg-create-template-save');
            var max_tokens = form.find('.wpaicg-create-template-make-tokens').val();
            var temperature = form.find('.wpaicg-create-template-temperature').val();
            var top_p = form.find('.wpaicg-create-template-top_p').val();
            var best_of = form.find('.wpaicg-create-template-best_of').val();
            var frequency_penalty = form.find('.wpaicg-create-template-frequency_penalty').val();
            var presence_penalty = form.find('.wpaicg-create-template-presence_penalty').val();
            var error_message = false;
            var data = form.serialize();
            if(max_tokens !== '' && (parseFloat(max_tokens) < 1 || parseFloat(max_tokens) > 8000)){
                error_message = 'Please enter a valid max tokens value between 1 and 8000';
            }
            else if(temperature !== '' && (parseFloat(temperature) < 0 || parseFloat(temperature) > 1)){
                error_message = 'Please enter a valid temperature value between 0 and 1';
            }
            else if(top_p !== '' && (parseFloat(top_p) < 0 || parseFloat(top_p) > 1)){
                error_message = 'Please enter a valid Top P value between 0 and 1';
            }
            else if(best_of !== '' && (parseFloat(best_of) < 1 || parseFloat(best_of) > 20)){
                error_message = 'Please enter a valid best of value between 0 and 1';
            }
            else if(frequency_penalty !== '' && (parseFloat(frequency_penalty) < 0 || parseFloat(frequency_penalty) > 2)){
                error_message = 'Please enter a valid frequency penalty value between 0 and 2';
            }
            else if(presence_penalty !== '' && (parseFloat(presence_penalty) < 0 || parseFloat(presence_penalty) > 2)){
                error_message = 'Please enter a valid presence penalty value between 0 and 2';
            }
            if(error_message){
                alert(error_message);
            }
            else{
                if(form.find('.wpaicg-template-form-field').length){
                    var wpaicgFieldID = [];
                    form.find('.wpaicg-template-form-field').each(function (idx, item){
                        var field_id = $(item).find('.wpaicg-create-template-field-id').val();
                        if(field_id !== ''){
                            if($.inArray(field_id,wpaicgFieldID) > -1){
                                error_message = 'Please insert unique ID';
                            }
                            else{
                                wpaicgFieldID.push(field_id)
                            }
                        }
                    })
                }
                if(error_message){
                    alert(error_message)
                }
                else {
                    $.ajax({
                        url: '<?php echo admin_url('admin-ajax.php')?>',
                        data: data,
                        dataType: 'JSON',
                        type: 'POST',
                        beforeSend: function () {
                            wpaicgLoading(btn)
                        },
                        success: function (res) {
                            wpaicgRmLoading(btn);
                            if (res.status === 'success') {
                                window.location.href = '<?php echo admin_url('admin.php?page=wpaicg_forms&update_template=')?>' + res.id;
                            } else {
                                alert(res.msg)
                            }
                        },
                        error: function () {
                            wpaicgRmLoading(btn);
                            alert('Something went wrong');
                        }
                    })
                }
            }
            return false;
        })
        /*End create*/
        var wpaicgNumberParse = 3;
        if($(window).width() < 900){
            wpaicgNumberParse = 2;
        }
        if($(window).width() < 480){
            wpaicgNumberParse = 1;
        }
        var wpaicg_per_page = <?php echo esc_html($wpaicg_per_page);?>;
        var wpaicg_count_templates = <?php echo esc_html(count($wpaicg_items))?>;
        $('.wpaicg-list input').on('change',function (){
            wpaicgTemplatesFilter();
        });
        var wpaicgTemplateItem = $('.wpaicg-template-item');
        var wpaicgTemplateSearch = $('.wpaicg-template-search');
        var wpaicgTemplateItems = $('.wpaicg-template-items');
        var wpaicgTemplateSettings = ['engine','max_tokens','temperature','top_p','best_of','frequency_penalty','presence_penalty','stop','post_title','generate_text','noanswer_text','draft_text','clear_text','stop_text','cnotice_text'];
        var wpaicgTemplateDefaultContent = $('.wpaicg-template-modal-content');
        var wpaicgTemplateEditor = false;
        var eventGenerator = false;
        wpaicgTemplateSearch.on('input', function (){
            wpaicgTemplatesFilter();
        });
        function wpaicgTemplatesFilter(){
            var categories = [];
            var users = [];
            var filterClasses = [];
            $('.wpaicg-categories input').each(function (idx, item){
                if($(item).prop('checked')){
                    categories.push($(item).val());
                    filterClasses.push($(item).val());
                }
            });
            $('.wpaicg-authors input').each(function (idx, item){
                if($(item).prop('checked')){
                    users.push('user-'+$(item).val());
                    filterClasses.push('user-'+$(item).val());
                }
            });
            var search = wpaicgTemplateSearch.val();
            wpaicgTemplateItem.each(function (idx, item){
                var show = false;
                var item_title = $(item).attr('data-title');
                var item_desc = $(item).attr('data-desc');
                if(categories.length){
                    for(var i=0;i<categories.length;i++){
                        if($(item).hasClass(categories[i])){
                            show = true;
                            break;
                        }
                        else{
                            show = false;
                        }
                    }
                    if(show && users.length){
                        for(var i=0;i<users.length;i++){
                            if($(item).hasClass(users[i])){
                                show = true;
                                break;
                            }
                            else{
                                show = false;
                            }
                        }
                    }
                }
                if(users.length){
                    for(var i=0;i<users.length;i++){
                        if($(item).hasClass(users[i])){
                            show = true;
                            break;
                        }
                        else{
                            show = false;
                        }
                    }
                    if(show && categories.length){
                        for(var i=0;i<categories.length;i++){
                            if($(item).hasClass(categories[i])){
                                show = true;
                                break;
                            }
                            else{
                                show = false;
                            }
                        }
                    }
                }
                if(!users.length && !categories.length){
                    show = true;
                }
                if(search !== '' && show){
                    search = search.toLowerCase();
                    item_title = item_title.toLowerCase();
                    item_desc = item_desc.toLowerCase();
                    if(item_title.indexOf(search) === -1 && item_desc.indexOf(search) === -1){
                        show = false;
                    }
                }
                if(show){
                    $(item).show();
                }
                else{
                    $(item).hide();
                }
            });
            wpaicgTemplatePagination();
        }
        wpaicgTemplatePagination();
        function wpaicgTemplatePagination(){
            wpaicgTemplateItem.removeClass('disappear-item');
            var number_rows = 0 ;
            wpaicgTemplateItem.each(function (idx, item){
                if($(item).is(':visible')){
                    number_rows++;
                }
            });
            $('.wpaicg-paginate').empty();
            if(number_rows > wpaicg_per_page){
                var  totalPage = Math.ceil(number_rows/wpaicg_per_page);
                for(var i=1;i <=totalPage;i++){
                    var classSpan = 'page-numbers';
                    if(i === 1){
                        classSpan = 'page-numbers current';
                    }
                    $('.wpaicg-paginate').append('<span class="'+classSpan+'" data-page="'+i+'">'+i+'</span>');
                }
            }
            var rowDisplay = 0;
            wpaicgTemplateItem.each(function (idx, item){
                if($(item).is(':visible')){
                    rowDisplay += 1;
                }
            });
            if(rowDisplay > wpaicg_per_page) {
                wpaicgTemplateItems.css('height', ((Math.ceil(wpaicg_per_page/wpaicgNumberParse) * 120) - 20) + 'px');
            }
            else{
                wpaicgTemplateItems.css('height', ((Math.ceil(rowDisplay/wpaicgNumberParse) * 120) - 20) + 'px');
            }
        }

        $(document).on('click','.wpaicg-paginate span:not(.current)', function (e){
            var btn = $(e.currentTarget);
            var page = parseInt(btn.attr('data-page'));
            $('.wpaicg-paginate span').removeClass('current');
            btn.addClass('current');
            var prevpage = page-1;
            var startRow = prevpage*wpaicg_per_page;
            var endRow = startRow+wpaicg_per_page;
            var keyRow = 0;
            var rowDisplay = 0;
            wpaicgTemplateItem.each(function (idx, item){
                if($(item).is(':visible')){
                    keyRow += 1;
                    if(keyRow > startRow && keyRow <= endRow){
                        rowDisplay += 1;
                        $(item).removeClass('disappear-item');
                    }
                    else{
                        $(item).addClass('disappear-item');
                    }
                }
            });
            wpaicgTemplateItems.css('height',((Math.ceil(rowDisplay/wpaicgNumberParse)*120)- 20)+'px');
        });
        $('.wpaicg_modal_close').click(function (){
            $('.wpaicg_modal_close').closest('.wpaicg_modal').hide();
            $('.wpaicg_modal_close').closest('.wpaicg_modal').removeClass('wpaicg-small-modal');
            $('.wpaicg-overlay').hide();
            if(eventGenerator){
                eventGenerator.close();
            }
        });
        var wpaicgEditorNumber;
        $(document).on('click','.wpaicg-template-form .wpaicg-template-save-draft', function(e){
            var response_type = $('.wpaicg-template-form .wpaicg-template-response-type').val();
            var post_content = '';
            if(response_type === 'textarea') {
                var basicEditor = true;
                var btn = $(e.currentTarget);
                var editor = tinyMCE.get('editor-' + wpaicgEditorNumber);
                if ($('#wp-editor-' + wpaicgEditorNumber + '-wrap').hasClass('tmce-active') && editor) {
                    basicEditor = false;
                }
                if (basicEditor) {
                    post_content = $('#editor-' + wpaicgEditorNumber).val();
                } else {
                    post_content = editor.getContent();
                }
            }
            else{
                post_content = $('.wpaicg-template-response-element').html();
            }
            var post_title = $('.wpaicg-template-form .wpaicg-template-post_title input').val();
            if(post_content !== ''){
                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php')?>',
                    data: {title: post_title, content: post_content, action: 'wpaicg_save_draft_post_extra',save_source: 'promptbase','nonce': '<?php echo wp_create_nonce('wpaicg-ajax-nonce')?>'},
                    dataType: 'json',
                    type: 'POST',
                    beforeSend: function (){
                        wpaicgLoading(btn);
                    },
                    success: function (res){
                        wpaicgRmLoading(btn);
                        if(res.status === 'success'){
                            window.location.href = '<?php echo admin_url('post.php')?>?post='+res.id+'&action=edit';
                        }
                        else{
                            alert(res.msg);
                        }
                    },
                    error: function (){
                        wpaicgRmLoading(btn);
                        alert('Something went wrong');
                    }
                });
            }
            else{
                alert('Please wait content generated');
            }

        });
        $(document).on('click','.wpaicg-template-form .wpaicg-template-clear', function(e){
            var response_type = $('.wpaicg-template-form .wpaicg-template-response-type').val();
            if(response_type === 'textarea') {
                var basicEditor = true;
                var editor = tinyMCE.get('editor-' + wpaicgEditorNumber);
                if ($('#wp-editor-' + wpaicgEditorNumber + '-wrap').hasClass('tmce-active') && editor) {
                    basicEditor = false;
                }
                if (basicEditor) {
                    $('#editor-' + wpaicgEditorNumber).val('');
                } else {
                    editor.setContent('');
                }
            }
            else{
                $('.wpaicg-template-response-element').empty();
            }
            $('.wpaicg-template-form .wpaicg-template-save-result').hide();
        });
        $(document).on('input','.wpaicg-template-form .wpaicg-template-max_tokens input', function(e){
            var maxtokens = $(e.currentTarget).val();
            var wpaicg_estimated_cost = maxtokens !== '' ? parseFloat(maxtokens)*0.02/1000 : 0;
            wpaicg_estimated_cost = '$'+parseFloat(wpaicg_estimated_cost.toFixed(5));
            $('.wpaicg-template-form .wpaicg-template-estimated span').html(wpaicg_estimated_cost);
        });
        $(document).on('click','.wpaicg-template-item .wpaicg-template-content,.wpaicg-template-item .wpaicg-template-icon',function (e){
            var item = $(e.currentTarget).parent();
            var title = item.attr('data-title');
            var id = item.attr('data-id');
            var type = item.attr('data-type');
            var categories = item.attr('data-categories');
            prompt_name = title;
            prompt_id = id;
            $('.wpaicg_modal_content').empty();
            if(type === 'json') {
                $('.wpaicg-template-action-customize').attr('data-id', id);
            }
            else{
                $('.wpaicg-template-action-customize').hide();
            }
            var modal_head = '<div class="wpaicg-d-flex wpaicg-align-items-center wpaicg-modal-template-head"><div style="margin-left: 10px;">';
            modal_head += '<strong>'+title+'</strong>';
            if(categories !== ''){
                modal_head += '<div><small>'+categories+'</small></div>';
            }
            modal_head += '</div></div>';
            $('.wpaicg_modal_title').html(modal_head);
            $('.wpaicg-modal-template-head').prepend(item.find('.wpaicg-template-icon').clone());
            var prompt = item.attr('data-prompt');
            var response = item.attr('data-response');
            var response_type = item.attr('data-editor');
            $('.wpaicg-template-response-type').val(response_type);
            wpaicgEditorNumber = Math.ceil(Math.random()*1000000);
            $('.wpaicg_modal_content').html('<div class="wpaicg-template-form">'+wpaicgTemplateDefaultContent.html()+'</div>');
            $('.wpaicg-template-form').find('.wpaicg-template-title').val(prompt);
            var wpaicgFieldsForm = item.attr('data-fields');
            if(wpaicgFieldsForm !== ''){
                var wpaicgFormFieldsElement = $('.wpaicg-template-form .wpaicg-form-fields');
                wpaicgFieldsForm = wpaicgFieldsForm.replace(/\\/g,'');
                wpaicgFieldsForm = JSON.parse(wpaicgFieldsForm);
                $.each(wpaicgFieldsForm, function(idx, form_field){
                    var form_field_html = '<div class="mb-5"><lable>'+form_field['label']+'</label><br>';
                    var form_field_type = 'text';
                    if(form_field['type'] !== undefined){
                        form_field_type = form_field['type'];
                    }
                    if(form_field_type === 'select'){
                        form_field_html += '<select name="'+form_field['id']+'" data-min="'+form_field['min']+'" data-max="'+form_field['max']+'" data-type="'+form_field_type+'" data-label="'+form_field['label']+'" class="wpaicg-form-field-template">';
                        if(form_field['options'] !== undefined && form_field['options'].length){
                            var form_field_options = form_field['options'];
                            if(typeof form_field_options === 'string'){
                                form_field_options = form_field_options.split("|");
                            }
                            $.each(form_field_options, function (idy, form_field_option){
                                form_field_html += '<option value="'+form_field_option+'">'+form_field_option+'</option>';
                            })
                        }
                        form_field_html += '</select>';
                    }
                    else if(form_field_type === 'checkbox' || form_field_type === 'radio'){
                        form_field_html += '<div>';
                        if(form_field['options'] !== undefined && form_field['options'].length){
                            var form_field_options = form_field['options'];
                            if(typeof form_field_options === 'string'){
                                form_field_options = form_field_options.split("|");
                            }
                            $.each(form_field_options, function (idy, form_field_option){
                                form_field_html += '<input name="'+form_field['id']+(form_field_type === 'checkbox' ? '[]':'')+'" type="'+form_field_type+'" value="'+form_field_option+'">&nbsp;'+form_field_option+'&nbsp;&nbsp;&nbsp;';
                            })
                        }
                        form_field_html += '</div>';
                    }
                    else if(form_field_type === 'textarea'){
                        var textarea_rows = form_field['rows'] !== undefined ? ' rows="'+form_field['rows']+'"' : '';
                        var textarea_cols = form_field['cols'] !== undefined ? ' cols="'+form_field['cols']+'"' : '';
                        form_field_html += '<textarea'+textarea_rows+textarea_cols+' name="'+form_field['id']+'" data-type="'+form_field_type+'" data-label="'+form_field['label']+'" type="'+form_field_type+'" required class="wpaicg-form-field-template" data-min="'+form_field['min']+'" data-max="'+form_field['max']+'"></textarea>'
                    }
                    else{
                        form_field_html += '<input name="'+form_field['id']+'" data-type="'+form_field_type+'" data-label="'+form_field['label']+'" type="'+form_field_type+'" required class="wpaicg-form-field-template" data-min="'+form_field['min']+'" data-max="'+form_field['max']+'">'
                    }
                    form_field_html += '</div>';
                    wpaicgFormFieldsElement.append(form_field_html);
                })
            }
            wpaicgTemplateEditor = $('.wpaicg-template-form').find('.wpaicg-template-result');
            if(id !== undefined){
                var embed_message = 'Embed this form to your website: [wpaicg_form id='+id+' settings=no';
                if(type === 'custom'){
                    embed_message += ' custom=yes';
                }
                embed_message += ']';
                $('.wpaicg-template-form .wpaicg-template-shortcode').html(embed_message);
            }
            for(var i = 0; i < wpaicgTemplateSettings.length; i++){
                var item_name = wpaicgTemplateSettings[i];
                var item_value = item.attr('data-'+item_name);
                if(item_name === 'max_tokens'){
                    var wpaicg_estimated_cost = item_value !== undefined ? parseFloat(item_value)*0.02/1000 : 0;
                    wpaicg_estimated_cost = '$'+parseFloat(wpaicg_estimated_cost.toFixed(5));
                    $('.wpaicg-template-form .wpaicg-template-estimated span').html(wpaicg_estimated_cost);
                }
                if(item_value !== undefined){
                    if(
                        item_name === 'generate_text'
                        || item_name === 'draft_text'
                        || item_name === 'noanswer_text'
                        || item_name === 'clear_text'
                        || item_name === 'stop_text'
                    ){
                        $('.wpaicg-template-text-'+item_name).html(item_value);
                    }
                    else {
                        if (item_name !== 'engine' && item_name !== 'stop' && item_name !== 'post_title') {
                            item_value = parseFloat(item_value);
                            item_value = item_value.toString().replace(/,/g, '.');
                        }
                        $('.wpaicg-template-form .wpaicg-template-' + item_name).find('[name=' + item_name + ']').val(item_value);
                        $('.wpaicg-template-form .wpaicg-template-' + item_name).show();
                    }
                }
                else{
                    $('.wpaicg-template-form .wpaicg-template-'+item_name).hide();
                }
            }
            $('.wpaicg-template-form .wpaicg-template-response').html(response);
            wpaicgTemplateEditor.attr('id','editor-'+wpaicgEditorNumber);
            if(response_type === 'textarea') {
                wp.editor.initialize('editor-' + wpaicgEditorNumber, {
                    tinymce: {
                        wpautop: true,
                        plugins: 'charmap colorpicker hr lists paste tabfocus textcolor fullscreen wordpress wpautoresize wpeditimage wpemoji wpgallery wplink wptextpattern',
                        toolbar1: 'formatselect,bold,italic,bullist,numlist,blockquote,alignleft,aligncenter,alignright,link,wp_more,spellchecker,fullscreen,wp_adv,listbuttons',
                        toolbar2: 'styleselect,strikethrough,hr,forecolor,pastetext,removeformat,charmap,outdent,indent,undo,redo,wp_help',
                        height: 300
                    },
                    quicktags: {buttons: 'strong,em,link,block,del,ins,img,ul,ol,li,code,more,close'},
                    mediaButtons: true
                });
            }
            else{
                $('.wpaicg-template-form .wpaicg-template-response-editor').hide();
            }
            $('.wpaicg_modal').css('top','');
            $('.wpaicg_modal').css('height','');
            $('.wpaicg-overlay').show();
            $('.wpaicg_modal').show();
        });
        function wpaicgLoading(btn){
            btn.attr('disabled','disabled');
            if(!btn.find('spinner').length){
                btn.append('<span class="spinner"></span>');
            }
            btn.find('.spinner').css('visibility','unset');
        }
        function wpaicgRmLoading(btn){
            btn.removeAttr('disabled');
            btn.find('.spinner').remove();
        }
        function stopOpenAIGenerator(){
            $('.wpaicg-template-form .wpaicg-template-stop-generate').hide();
            if(!wpaicg_limited_token) {
                $('.wpaicg-template-form .wpaicg-template-save-result').show();
            }
            wpaicgRmLoading($('.wpaicg-template-form .wpaicg-generate-button'));
            eventGenerator.close();
        }
        $(document).on('click','.wpaicg-template-form .wpaicg-template-stop-generate', function (e){
            stopOpenAIGenerator();
        });
        function wpaicgValidEmail(email){
            return String(email)
                .toLowerCase()
                .match(
                    /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
                );
        }
        function wpaicgValidUrl(string) {
            try {
                new URL(string);
                return true;
            } catch (err) {
                return false;
            }
        }
        $(document).on('submit','.wpaicg-template-form form', function (e){
            var form = $(e.currentTarget);
            var btn = form.find('.wpaicg-generate-button');
            var template_title = form.find('.wpaicg-template-title').val();
            var response_type = form.find('.wpaicg-template-response-type').val();
            if(template_title !== '') {
                var max_tokens = form.find('.wpaicg-template-max_tokens input').val();
                var temperature = form.find('.wpaicg-template-temperature input').val();
                var top_p = form.find('.wpaicg-template-top_p input').val();
                var best_of = form.find('.wpaicg-template-best_of input').val();
                var frequency_penalty = form.find('.wpaicg-template-frequency_penalty input').val();
                var presence_penalty = form.find('.wpaicg-template-presence_penalty input').val();
                var error_message = false;
                if(max_tokens === ''){
                    error_message = 'Please enter max tokens';
                }
                else if(parseFloat(max_tokens) < 1 || parseFloat(max_tokens) > 4000){
                    error_message = 'Please enter a valid max tokens value between 1 and 4000';
                }
                else if(temperature === ''){
                    error_message = 'Please enter temperature';
                }
                else if(parseFloat(temperature) < 0 || parseFloat(temperature) > 1){
                    error_message = 'Please enter a valid temperature value between 0 and 1';
                }
                else if(top_p === ''){
                    error_message = 'Please enter Top P';
                }
                else if(parseFloat(top_p) < 0 || parseFloat(top_p) > 1){
                    error_message = 'Please enter a valid Top P value between 0 and 1';
                }
                else if(best_of === ''){
                    error_message = 'Please enter best of';
                }
                else if(parseFloat(best_of) < 1 || parseFloat(best_of) > 20){
                    error_message = 'Please enter a valid best of value between 0 and 1';
                }
                else if(frequency_penalty === ''){
                    error_message = 'Please enter frequency penalty';
                }
                else if(parseFloat(frequency_penalty) < 0 || parseFloat(frequency_penalty) > 2){
                    error_message = 'Please enter a valid frequency penalty value between 0 and 2';
                }
                else if(presence_penalty === ''){
                    error_message = 'Please enter presence penalty';
                }
                else if(parseFloat(presence_penalty) < 0 || parseFloat(presence_penalty) > 2){
                    error_message = 'Please enter a valid presence penalty value between 0 and 2';
                }
                if(error_message){
                    alert(error_message);
                }
                else {
                    if($('.wpaicg-template-form .wpaicg-form-field-template').length) {
                        $('.wpaicg-template-form .wpaicg-form-field-template').each(function(idf, item){
                            var field_type = $(item).attr('data-type');
                            var field_name = $(item).attr('name');
                            var field_label = $(item).attr('data-label');
                            var field_value = $(item).val();
                            var field_min = $(item).attr('data-min');
                            var field_max = $(item).attr('data-max');
                            if(field_type === 'text' ||field_type === 'textarea' || field_type === 'email' || field_type === 'url'){
                                if(field_min !== '' && field_value.length < parseInt(field_min)){
                                    error_message = field_label+' minimum '+field_min+' characters';
                                }
                                else if(field_max !== '' && field_value.length > parseInt(field_max)){
                                    error_message = field_label+' maximum '+field_max+' characters';
                                }
                                else if(field_type === 'email' && !wpaicgValidEmail(field_value)){
                                    error_message = field_label+' must be email address';
                                }
                                else if(field_type === 'url' && !wpaicgValidUrl(field_value)){
                                    error_message = field_label+' must be url';
                                }
                            }
                            else if(field_type === 'number'){
                                if(field_min !== '' && parseFloat(field_value) < parseInt(field_min)){
                                    error_message = field_label+' minimum '+field_min;
                                }
                                else if(field_max !== '' && parseFloat(field_value) > parseInt(field_max)){
                                    error_message = field_label+' maximum '+field_max;
                                }
                            }
                        })
                    }
                    if(error_message){
                        alert(error_message);
                    }
                    else {
                        prompt_response = '';
                        let startTime = new Date();
                        if($('.wpaicg-template-form .wpaicg-form-field-template').length) {
                            $('.wpaicg-template-form .wpaicg-form-field-template').each(function (idf, item) {
                                var field_name = $(item).attr('name');
                                var field_value = $(item).val();
                                var sRegExInput = new RegExp('{' + field_name + '}', 'g');
                                template_title = template_title.replace(sRegExInput, field_value);
                            })
                        }
                        $('.wpaicg-template-title-filled').val(template_title+".\n\n");
                        var data = form.serialize();
                        var basicEditor = true;
                        if(response_type === 'textarea') {
                            var editor = tinyMCE.get('editor-' + wpaicgEditorNumber);
                            if ($('#wp-editor-' + wpaicgEditorNumber + '-wrap').hasClass('tmce-active') && editor) {
                                basicEditor = false;
                            }
                            if (basicEditor) {
                                $('#editor-' + wpaicgEditorNumber).val('');
                            } else {
                                editor.setContent('');
                            }
                        }
                        wpaicgLoading(btn);
                        form.find('.wpaicg-template-stop-generate').show();
                        form.find('.wpaicg-template-save-result').hide();
                        var wpaicg_limitLines = parseInt(form.find('.wpaicg-template-max-lines').val());
                        var count_line = 0;
                        var currentContent = '';
                        data += '&source_stream=form&nonce=<?php echo wp_create_nonce('wpaicg-ajax-nonce')?>';
                        eventGenerator = new EventSource('<?php echo esc_html(add_query_arg('wpaicg_stream','yes',site_url().'/index.php'));?>&' + data);
                        var wpaicg_response_events = 0;
                        var wpaicg_newline_before = false;
                        wpaicg_limited_token = false;
                        eventGenerator.onmessage = function (e) {
                            if(response_type === 'textarea') {
                                if (basicEditor) {
                                    currentContent = $('#editor-' + wpaicgEditorNumber).val();
                                } else {
                                    currentContent = editor.getContent();
                                    currentContent = currentContent.replace(/<\/?p(>|$)/g, "");
                                }
                            }
                            else{
                                currentContent = $('.wpaicg-template-response-element').html();
                            }
                            if (e.data === "[DONE]") {
                                count_line += 1;
                                if(response_type === 'textarea') {
                                    if (basicEditor) {
                                        $('#editor-' + wpaicgEditorNumber).val(currentContent + '<br /><br />');
                                    } else {
                                        editor.setContent(currentContent + '<br /><br />');
                                    }
                                }
                                else{
                                    $('.wpaicg-template-response-element').append('<br />');
                                }
                                wpaicg_response_events = 0;
                            }
                            else if (e.data === "[LIMITED]") {
                                wpaicg_limited_token = true;
                                count_line += 1;
                                if(response_type === 'textarea') {
                                    if (basicEditor) {
                                        $('#editor-' + wpaicgEditorNumber).val(currentContent + '<br /><br />');
                                    } else {
                                        editor.setContent(currentContent + '<br /><br />');
                                    }
                                }
                                else{
                                    $('.wpaicg-template-response-element').append('<br />');
                                }
                                wpaicg_response_events = 0;
                            } else {
                                var result = JSON.parse(e.data);
                                if (result.error !== undefined) {
                                    var content_generated = result.error.message;
                                } else {
                                    var content_generated = result.choices[0].delta !== undefined ? (result.choices[0].delta.content !== undefined ? result.choices[0].delta.content : '') : result.choices[0].text;
                                }
                                prompt_response += content_generated;
                                if((content_generated === '\n' || content_generated === ' \n' || content_generated === '.\n' || content_generated === '\n\n' || content_generated === '.\n\n') && wpaicg_response_events > 0 && currentContent !== ''){
                                    if(!wpaicg_newline_before) {
                                        wpaicg_newline_before = true;
                                        if (response_type === 'textarea') {
                                            if (basicEditor) {
                                                $('#editor-' + wpaicgEditorNumber).val(currentContent + '<br /><br />');
                                            } else {
                                                editor.setContent(currentContent + '<br /><br />');
                                            }
                                        } else {
                                            $('.wpaicg-template-response-element').append('<br />');
                                        }
                                    }
                                }
                                else if(content_generated === '\n' && wpaicg_response_events === 0 && currentContent === '' ){

                                }
                                else {
                                    wpaicg_newline_before = false;
                                    wpaicg_response_events += 1;
                                    if(response_type === 'textarea') {
                                        if (basicEditor) {
                                            $('#editor-' + wpaicgEditorNumber).val(currentContent + content_generated);
                                        } else {
                                            editor.setContent(currentContent + content_generated);
                                        }
                                    }
                                    else{
                                        $('.wpaicg-template-response-element').append(content_generated);
                                    }
                                }
                            }
                            if (count_line === wpaicg_limitLines) {
                                if(!wpaicg_limited_token) {
                                    let endTime = new Date();
                                    let timeDiff = endTime - startTime;
                                    timeDiff = timeDiff / 1000;
                                    data += '&action=wpaicg_form_log&prompt_id=' + prompt_id + '&prompt_name=' + prompt_name + '&prompt_response=' + prompt_response + '&duration=' + timeDiff + '&_wpnonce=' + wp_nonce;
                                    $.ajax({
                                        url: '<?php echo admin_url('admin-ajax.php')?>',
                                        data: data,
                                        dataType: 'JSON',
                                        type: 'POST',
                                        success: function (res) {

                                        }
                                    })
                                }
                                $('.wpaicg-template-form .wpaicg-template-stop-generate').hide();
                                stopOpenAIGenerator();
                                wpaicgRmLoading(btn);
                            }
                        }
                    }
                }
            }
            else{
                alert('Please enter prompt');
            }
            return false;
        })
    })
</script>


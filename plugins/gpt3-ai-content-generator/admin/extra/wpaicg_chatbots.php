<?php
if ( ! defined( 'ABSPATH' ) ) exit;
global $wpdb,$wp;
if(isset($_GET['wpaicg_bot_delete']) && !empty($_GET['wpaicg_bot_delete'])){
    if(!wp_verify_nonce($_GET['_wpnonce'], 'wpaicg_delete_'.sanitize_text_field($_GET['wpaicg_bot_delete']))){
        die(WPAICG_NONCE_ERROR);
    }
    wp_delete_post(sanitize_text_field($_GET['wpaicg_bot_delete']));
    echo '<script>window.location.href = "'.admin_url('admin.php?page=wpaicg_chatgpt&action=bots').'"</script>';
    exit;
}
wp_enqueue_script('wp-color-picker');
wp_enqueue_style('wp-color-picker');
$wpaicg_custom_models = get_option('wpaicg_custom_models',array());
$wpaicg_custom_models = array_merge(array('text-davinci-003','text-curie-001','text-babbage-001','text-ada-001'),$wpaicg_custom_models);
$table = $wpdb->prefix . 'wpaicg';
$existingValue = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE name = %s", 'wpaicg_settings' ), ARRAY_A );
$wpaicg_chat_temperature = get_option('wpaicg_chat_temperature',$existingValue['temperature']);
$wpaicg_chat_max_tokens = get_option('wpaicg_chat_max_tokens',$existingValue['max_tokens']);
$wpaicg_chat_top_p = get_option('wpaicg_chat_top_p',$existingValue['top_p']);
$wpaicg_chat_best_of = get_option('wpaicg_chat_best_of',$existingValue['best_of']);
$wpaicg_chat_frequency_penalty = get_option('wpaicg_chat_frequency_penalty',$existingValue['frequency_penalty']);
$wpaicg_chat_presence_penalty = get_option('wpaicg_chat_presence_penalty',$existingValue['presence_penalty']);
$wpaicg_chat_icon = 'default';
$wpaicg_chat_fontsize = '13';
$wpaicg_chat_fontcolor = '#fff';
$wpaicg_chat_bgcolor = '#222222';
$wpaicg_bg_text_field = '#fff';
$wpaicg_send_color = '#fff';
$wpaicg_border_text_field = '#ccc';
$wpaicg_footer_text = '';
$wpaicg_user_bg_color = '#444654';
$wpaicg_ai_bg_color = '#343541';
$wpaicg_use_avatar = false;
$wpaicg_ai_avatar = 'default';
$wpaicg_ai_avatar_id = '';
$wpaicg_chat_width = '350';
$wpaicg_chat_height = '400';
$wpaicg_chat_position = 'left';
$wpaicg_chat_tone = 'friendly';
$wpaicg_user_aware = 'no';
$wpaicg_chat_proffesion = 'none';
$wpaicg_chat_icon_url = '';
$wpaicg_chat_remember_conversation = 'yes';
$wpaicg_chat_content_aware = 'yes';
$wpaicg_pinecone_api = get_option('wpaicg_pinecone_api','');
$wpaicg_pinecone_environment = get_option('wpaicg_pinecone_environment','');
$wpaicg_save_logs = false;
$wpaicg_log_notice = false;
$wpaicg_log_notice_message = 'Please note that your conversations will be recorded.';
$wpaicg_conversation_cut = 10;
$wpaicg_embedding_field_disabled = empty($wpaicg_pinecone_api) || empty($wpaicg_pinecone_environment) ? true : false;
$wpaicg_chat_embedding = false;
$wpaicg_chat_addition = false;
$wpaicg_chat_addition_text = false;
$wpaicg_chat_embedding_type = false;
$wpaicg_chat_embedding_top = false;
$wpaicg_audio_enable = false;
$wpaicg_mic_color = '#222';
$wpaicg_stop_color = '#f00';
$wpaicg_user_limited = false;
$wpaicg_guest_limited = false;
$wpaicg_user_tokens = 0;
$wpaicg_guest_tokens = 0;
$wpaicg_reset_limit = 0;
$wpaicg_limited_message = 'You have reached your token limit.';
$wpaicg_include_footer = 0;
$wpaicg_roles = wp_roles()->get_names();
?>
<style>
    .wpaicg-bot-wizard{}
    .wpaicg-bot-wizard .wpaicg-mb-10{}
    .wpaicg-bot-wizard .wpaicg-form-label{
        width: 40%;
        display: inline-block;
    }
    .wpaicg-bot-wizard input[type=text],.wpaicg-bot-wizard input[type=number],.wpaicg-bot-wizard select{
        width: 55%;
        display: inline-block;
    }
    .wpaicg-bot-wizard textarea{
        width: 65%;
        display: inline-block;
    }
    .wpaicg_modal{
        top: 5%;
        height: 90%;
        position: relative;
    }
    .wpaicg_modal_content{
        max-height: calc(100% - 103px);
        overflow-y: auto;
    }
    .wp-picker-holder{
        position: absolute;
    }
    .wpaicg-chat-shortcode-send{
        display: flex;
        align-items: center;
        padding: 2px 3px;
        cursor: pointer;
    }
    .wpaicg-bot-thinking {
        bottom: 0;
        font-size: 11px;
        padding: 2px 6px;
        display: none;
    }
    .wpaicg-chat-shortcode-send svg{
        width: 30px;
        height: 30px;
        fill: currentColor;
        stroke: currentColor;
    }
    .wpaicg-chat-shortcode-type {
        display: flex;
        align-items: center;
        padding: 5px;
    }
    input.wpaicg-chat-shortcode-typing {
        flex: 1;
        border: 1px solid #ccc;
        border-radius: 3px;
        padding: 0 8px;
        min-height: 30px;
        line-height: 2;
        box-shadow: 0 0 0 transparent;
        margin: 0;
    }
    .wpaicg-chat-shortcode-content ul {
        overflow-y: auto;
        margin: 0;
        padding: 0;
    }
    .wpaicg-chat-shortcode-content ul li {
        display: flex;
        margin-bottom: 0;
        padding: 10px;
    }
    .wpaicg-chat-shortcode-content ul li p {
        margin: 0;
        padding: 0;
    }
    .wpaicg-chat-shortcode-content ul li p,.wpaicg-chat-shortcode-content ul li span{
        font-size: inherit;
    }
    .wpaicg-chat-shortcode-content ul li strong {
        font-weight: bold;
        margin-right: 5px;
        float: left;
    }
    .wpaicg-mic-icon {
        display: flex;
        cursor: pointer;
        position: absolute;
        right: 47px;
    }
    .wpaicg-mic-icon svg {
        width: 16px;
        height: 16px;
        fill: currentColor;
    }
    .wpaicg-chat-shortcode{
        border-radius: 4px;
        overflow: hidden;
    }
    .wpaicg-chat-shortcode-footer{
        height: 18px;
        font-size: 11px;
        padding: 0 5px;
        color: #424242;
        margin-bottom: 2px;
    }
    .wpaicg_chatbox_avatar,.wpaicg_chatbox_icon{
        cursor: pointer;
    }
    .asdisabled{
        background: #ebebeb!important;
    }
    .wpaicg-bot-footer{
        width: calc(100% - 31px);
        display: flex;
        bottom: 0px;
        position: absolute;
        margin-left: -21px;
        padding: 10px;
        border-top: 1px solid #d9d9d9;
        background: #fff;
        border-bottom-left-radius: 5px;
        border-bottom-right-radius: 5px;
    }
    .wpaicg-bot-footer > div{
        flex: 1;
    }
    .wpaicg_modal_content{
    }
    .wpaicg-grid-3{
        border: 1px solid #d9d9d9;
        border-radius: 5px;
        padding: 10px;
    }
    .wpaicg-jumping-dots span {
        position: relative;
        bottom: 0;
        -webkit-animation: wpaicg-jump 1500ms infinite;
        animation: wpaicg-jump 2s infinite;
    }
    .wpaicg-jumping-dots .wpaicg-dot-1{
        -webkit-animation-delay: 200ms;
        animation-delay: 200ms;
    }
    .wpaicg-jumping-dots .wpaicg-dot-2{
        -webkit-animation-delay: 400ms;
        animation-delay: 400ms;
    }
    .wpaicg-jumping-dots .wpaicg-dot-3{
        -webkit-animation-delay: 600ms;
        animation-delay: 600ms;
    }
    @-webkit-keyframes wpaicg-jump {
        0%   {bottom: 0px;}
        20%  {bottom: 5px;}
        40%  {bottom: 0px;}
    }

    @keyframes wpaicg-jump {
        0%   {bottom: 0px;}
        20%  {bottom: 5px;}
        40%  {bottom: 0px;}
    }
</style>
<div class="wpaicg-create-bot-default" style="display: none">
    <div class="wpaicg-grid">
        <div class="wpaicg-grid-3">
            <form action="" method="post" class="wpaicg-bot-form">
                <?php
                wp_nonce_field('wpaicg_chatbot_save');
                ?>
                <input value="<?php echo esc_html($wpaicg_chat_icon_url)?>" type="hidden" name="bot[icon_url]" class="wpaicg_chatbot_icon_url">
                <input value="<?php echo esc_html($wpaicg_ai_avatar_id)?>" type="hidden" name="bot[ai_avatar_id]" class="wpaicg_chatbot_ai_avatar_id">
                <input value="" type="hidden" name="bot[id]" class="wpaicg_chatbot_id">
                <input value="wpaicg_update_chatbot" type="hidden" name="action">
                <!--Type-->
                <div class="wpaicg-bot-type wpaicg-bot-wizard">
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label">Name:</label>
                        <input type="text" name="bot[name]" class="regular-text wpaicg_chatbot_name">
                    </div>
                    <div class="wpaicg-mb-10">
                        <label><strong>What would you like to create?</strong></label>
                    </div>
                    <div class="wpaicg-mb-10"><label><input type="radio" name="bot[type]" value="shortcode" class="wpaicg_chatbot_type_shortcode">&nbsp;Shortcode</label></div>
                    <div class="wpaicg-mb-10"><label><input type="radio" name="bot[type]" value="widget" class="wpaicg_chatbot_type_widget">&nbsp;Widget</label></div>
                    <div class="wpaicg-mb-10 wpaicg-widget-pages" style="display: none">
                    <div class="wpaicg-mb-10">
                        <label><strong>Where would you like to display it?</strong></label>
                    </div>
                        <label class="wpaicg-form-label">Page / Post ID:</label>
                        <input type="text" class="regular-text wpaicg_chatbot_pages" name="bot[pages]" placeholder="Example: 1,2,3">
                    </div>
                    <div class="wpaicg-mb-10 wpaicg_chatbot_position" style="display: none">
                        <label class="wpaicg-form-label">Position:</label>
                        <input<?php echo $wpaicg_chat_position == 'left' ? ' checked': ''?> type="radio" value="left" name="bot[position]" class="wpaicg_chatbot_position_left"> Bottom Left
                        <input<?php echo $wpaicg_chat_position == 'right' ? ' checked': ''?> type="radio" value="right" name="bot[position]" class="wpaicg_chatbot_position_right"> Bottom Right
                    </div>
                    <div class="wpaicg-bot-footer">
                        <div>
                            <button type="button" class="button button-primary wpaicg-bot-step" data-type="language">Next</button>
                        </div>
                        <button class="button button-primary wpaicg-chatbot-submit">Save</button>
                    </div>
                </div>
                <!--Language-->
                <div class="wpaicg-bot-language wpaicg-bot-wizard" style="display: none">
                    <h3>Language, Tone and Profession?</h3>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label">Language:</label>
                        <select class="regular-text wpaicg_chatbot_language"  name="bot[language]">
                            <option value="en">English</option>
                            <option value="af">Afrikaans</option>
                            <option value="ar">Arabic</option>
                            <option value="bg">Bulgarian</option>
                            <option value="zh">Chinese</option>
                            <option value="hr">Croatian</option>
                            <option value="cs">Czech</option>
                            <option value="da">Danish</option>
                            <option value="nl">Dutch</option>
                            <option value="et">Estonian</option>
                            <option value="fil">Filipino</option>
                            <option value="fi">Finnish</option>
                            <option value="fr">French</option>
                            <option value="de">German</option>
                            <option value="el">Greek</option>
                            <option value="he">Hebrew</option>
                            <option value="hi">Hindi</option>
                            <option value="hu">Hungarian</option>
                            <option value="id">Indonesian</option>
                            <option value="it">Italian</option>
                            <option value="ja">Japanese</option>
                            <option value="ko">Korean</option>
                            <option value="lv">Latvian</option>
                            <option value="lt">Lithuanian</option>
                            <option value="ms">Malay</option>
                            <option value="no">Norwegian</option>
                            <option value="pl">Polish</option>
                            <option value="pt">Portuguese</option>
                            <option value="ro">Romanian</option>
                            <option value="ru">Russian</option>
                            <option value="sr">Serbian</option>
                            <option value="sk">Slovak</option>
                            <option value="sl">Slovenian</option>
                            <option value="sv">Swedish</option>
                            <option value="es">Spanish</option>
                            <option value="th">Thai</option>
                            <option value="tr">Turkish</option>
                            <option value="uk">Ukrainian</option>
                            <option value="vi">Vietnamese</option>
                        </select>
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label">Tone:</label>
                        <select class="regular-text wpaicg_chatbot_tone" name="bot[tone]">
                            <option value="friendly">Friendly</option>
                            <option value="professional">Professional</option>
                            <option value="sarcastic">Sarcastic</option>
                            <option value="humorous">Humorous</option>
                            <option value="cheerful">Cheerful</option>
                            <option value="anecdotal">Anecdotal</option>
                        </select>
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label">Act As:</label>
                        <select name="bot[proffesion]" class="regular-text wpaicg_chatbot_proffesion">
                            <option value="none">None</option>
                            <option value="accountant">Accountant</option>
                            <option value="advertisingspecialist">Advertising Specialist</option>
                            <option value="architect">Architect</option>
                            <option value="artist">Artist</option>
                            <option value="blogger">Blogger</option>
                            <option value="businessanalyst">Business Analyst</option>
                            <option value="businessowner">Business Owner</option>
                            <option value="carexpert">Car Expert</option>
                            <option value="consultant">Consultant</option>
                            <option value="counselor">Counselor</option>
                            <option value="cryptocurrencytrader">Cryptocurrency Trader</option>
                            <option value="cryptocurrencyexpert">Cryptocurrency Expert</option>
                            <option value="customersupport">Customer Support</option>
                            <option value="designer">Designer</option>
                            <option value="digitalmarketinagency">Digital Marketing Agency</option>
                            <option value="editor">Editor</option>
                            <option value="engineer">Engineer</option>
                            <option value="eventplanner">Event Planner</option>
                            <option value="freelancer">Freelancer</option>
                            <option value="insuranceagent">Insurance Agent</option>
                            <option value="insurancebroker">Insurance Broker</option>
                            <option value="interiordesigner">Interior Designer</option>
                            <option value="journalist">Journalist</option>
                            <option value="marketingagency">Marketing Agency</option>
                            <option value="marketingexpert">Marketing Expert</option>
                            <option value="marketingspecialist">Marketing Specialist</option>
                            <option value="photographer">Photographer</option>
                            <option value="programmer">Programmer</option>
                            <option value="publicrelationsagency">Public Relations Agency</option>
                            <option value="publisher">Publisher</option>
                            <option value="realestateagent">Real Estate Agent</option>
                            <option value="recruiter">Recruiter</option>
                            <option value="reporter">Reporter</option>
                            <option value="salesperson">Sales Person</option>
                            <option value="salerep">Sales Representative</option>
                            <option value="seoagency">SEO Agency</option>
                            <option value="seoexpert">SEO Expert</option>
                            <option value="socialmediaagency">Social Media Agency</option>
                            <option value="student">Student</option>
                            <option value="teacher">Teacher</option>
                            <option value="technicalsupport">Technical Support</option>
                            <option value="trainer">Trainer</option>
                            <option value="travelagency">Travel Agency</option>
                            <option value="videographer">Videographer</option>
                            <option value="webdesignagency">Web Design Agency</option>
                            <option value="webdesignexpert">Web Design Expert</option>
                            <option value="webdevelopmentagency">Web Development Agency</option>
                            <option value="webdevelopmentexpert">Web Development Expert</option>
                            <option value="webdesigner">Web Designer</option>
                            <option value="webdeveloper">Web Developer</option>
                            <option value="writer">Writer</option>
                        </select>
                    </div>
                    <div class="wpaicg-bot-footer">
                        <div>
                            <button type="button" class="button wpaicg-bot-step" data-type="type">Previous</button>
                            <button type="button" class="button button-primary wpaicg-bot-step" data-type="style">Next</button>
                        </div>
                        <button class="button button-primary wpaicg-chatbot-submit">Save</button>
                    </div>
                </div>
                <!--Style-->
                <div class="wpaicg-bot-style wpaicg-bot-wizard" style="display: none">
                    <h3>Style</h3>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label">Font Size:</label>
                        <select name="bot[fontsize]" class="wpaicg_chatbot_fontsize">
                            <?php
                            for($i = 10; $i <= 30; $i++){
                                echo '<option'.($wpaicg_chat_fontsize == $i ? ' selected' :'').' value="'.esc_html($i).'">'.esc_html($i).'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label">Font Color:</label>
                        <input value="<?php echo esc_html($wpaicg_chat_fontcolor)?>" type="text" class="wpaicgchat_color wpaicg_chatbot_fontcolor" name="bot[fontcolor]">
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label">Background Color:</label>
                        <input value="<?php echo esc_html($wpaicg_chat_bgcolor)?>" type="text" class="wpaicgchat_color wpaicg_chatbot_bgcolor" name="bot[bgcolor]">
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label">Text Field Background:</label>
                        <input value="<?php echo esc_html($wpaicg_bg_text_field)?>" type="text" class="wpaicgchat_color wpaicg_chatbot_bg_text_field" name="bot[bg_text_field]">
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label">Text Field Border:</label>
                        <input value="<?php echo esc_html($wpaicg_border_text_field)?>" type="text" class="wpaicgchat_color wpaicg_chatbot_border_text_field" name="bot[border_text_field]">
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label">Button Color:</label>
                        <input value="<?php echo esc_html($wpaicg_send_color)?>" type="text" class="wpaicgchat_color wpaicg_chatbot_send_color" name="bot[send_color]">
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label">User Background Color:</label>
                        <input value="<?php echo esc_html($wpaicg_user_bg_color)?>" type="text" class="wpaicgchat_color wpaicg_chatbot_user_bg_color" name="bot[user_bg_color]">
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label">AI Background Color:</label>
                        <input value="<?php echo esc_html($wpaicg_ai_bg_color)?>" type="text" class="wpaicgchat_color wpaicg_chatbot_ai_bg_color" name="bot[ai_bg_color]">
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label">Width:</label>
                        <input value="<?php echo esc_html($wpaicg_chat_width)?>" style="width: 100px;" class="wpaicg_chatbot_width" min="100" type="number" name="bot[width]">&nbsp;px
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label">Height:</label>
                        <input value="<?php echo esc_html($wpaicg_chat_height)?>" style="width: 100px;" class="wpaicg_chatbot_height" min="100" type="number" name="bot[height]">&nbsp;px
                    </div>
                    <div class="wpaicg-widget-icon" style="display: none">
                        <div class="wpaicg-mb-10">
                            <label class="wpaicg-form-label">Icon (75x75):</label>
                            <div style="display: inline-flex; align-items: center">
                                <input checked class="wpaicg_chatbox_icon_default wpaicg_chatbot_icon_default" type="radio" value="default" name="bot[icon]">
                                <div style="text-align: center">
                                    <img style="display: block;width: 40px; height: 40px" src="<?php echo esc_html(WPAICG_PLUGIN_URL).'admin/images/chatbot.png'?>"<br>
                                    <strong>Default</strong>
                                </div>
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="radio" class="wpaicg_chatbox_icon_custom wpaicg_chatbot_icon_custom" value="custom" name="bot[icon]">
                                <div style="text-align: center">
                                    <div class="wpaicg_chatbox_icon">
                                        <svg width="40px" height="40px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M246.6 9.4c-12.5-12.5-32.8-12.5-45.3 0l-128 128c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 109.3V320c0 17.7 14.3 32 32 32s32-14.3 32-32V109.3l73.4 73.4c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3l-128-128zM64 352c0-17.7-14.3-32-32-32s-32 14.3-32 32v64c0 53 43 96 96 96H352c53 0 96-43 96-96V352c0-17.7-14.3-32-32-32s-32 14.3-32 32v64c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V352z"/></svg><br>
                                    </div>
                                    <strong>Custom</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label">Use Avatars:</label>
                        <input<?php echo $wpaicg_use_avatar ? ' checked':''?> value="1" type="checkbox" class="wpaicg_chatbot_use_avatar" name="bot[use_avatar]">
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label">AI Avatar (40x40):</label>
                        <div style="display: inline-flex; align-items: center">
                            <input checked class="wpaicg_chatbox_avatar_default wpaicg_chatbot_ai_avatar_default" type="radio" value="default" name="bot[ai_avatar]">
                            <div style="text-align: center">
                                <img style="display: block;width: 40px; height: 40px" src="<?php echo esc_html(WPAICG_PLUGIN_URL).'admin/images/chatbot.png'?>"<br>
                                <strong>Default</strong>
                            </div>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type="radio" class="wpaicg_chatbox_avatar_custom wpaicg_chatbot_ai_avatar_custom" value="custom" name="bot[ai_avatar]">
                            <div style="text-align: center">
                                <div class="wpaicg_chatbox_avatar">
                                    <svg width="40px" height="40px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M246.6 9.4c-12.5-12.5-32.8-12.5-45.3 0l-128 128c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 109.3V320c0 17.7 14.3 32 32 32s32-14.3 32-32V109.3l73.4 73.4c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3l-128-128zM64 352c0-17.7-14.3-32-32-32s-32 14.3-32 32v64c0 53 43 96 96 96H352c53 0 96-43 96-96V352c0-17.7-14.3-32-32-32s-32 14.3-32 32v64c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V352z"/></svg><br>
                                </div>
                                <strong>Custom</strong>
                            </div>
                        </div>
                    </div>
                    <div class="wpaicg-bot-footer">
                        <div>
                        <button type="button" class="button wpaicg-bot-step" data-type="language">Previous</button>
                        <button type="button" class="button button-primary wpaicg-bot-step" data-type="parameters">Next</button>
                        </div>
                        <button class="button button-primary wpaicg-chatbot-submit">Save</button>
                    </div>
                </div>
                <!--Parameters-->
                <div class="wpaicg-bot-parameters wpaicg-bot-wizard" style="display: none">
                    <h3>Parameters</h3>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label" for="wpaicg_chat_model">Model:</label>
                        <select class="regular-text wpaicg_chatbot_model" id="wpaicg_chat_model"  name="bot[model]" >
                            <?php
                            if(!in_array('gpt-3.5-turbo',$wpaicg_custom_models)) {
                                array_unshift($wpaicg_custom_models, 'gpt-3.5-turbo');
                            }
                            if(!in_array('gpt-4',$wpaicg_custom_models)) {
                                $wpaicg_custom_models[] = 'gpt-4';
                            }
                            if(!in_array('gpt-4-32k',$wpaicg_custom_models)) {
                                $wpaicg_custom_models[] = 'gpt-4-32k';
                            }
                            foreach($wpaicg_custom_models as $wpaicg_custom_model){
                                echo '<option value="'.esc_html($wpaicg_custom_model).'">'.esc_html($wpaicg_custom_model).'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label">Temperature:</label>
                        <input type="text" class="regular-text wpaicg_chatbot_temperature" id="label_temperature" name="bot[temperature]" value="<?php
                        echo  esc_html( $wpaicg_chat_temperature ) ;
                        ?>">
                    </div>

                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label">Max Tokens:</label>
                        <input type="text" class="regular-text wpaicg_chatbot_max_tokens" id="label_max_tokens" name="bot[max_tokens]" value="<?php
                        echo  esc_html( $wpaicg_chat_max_tokens ) ;
                        ?>" >
                    </div>

                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label">Top P:</label>
                        <input type="text" class="regular-text wpaicg_chatbot_top_p" id="label_top_p" name="bot[top_p]" value="<?php
                        echo  esc_html( $wpaicg_chat_top_p ) ;
                        ?>" >
                    </div>

                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label">Best Of:</label>
                        <input type="text" class="regular-text wpaicg_chatbot_best_of" id="label_best_of" name="bot[best_of]" value="<?php
                        echo  esc_html( $wpaicg_chat_best_of ) ;
                        ?>" >
                    </div>

                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label">Frequency Penalty:</label>
                        <input type="text" class="regular-text wpaicg_chatbot_frequency_penalty" id="label_frequency_penalty" name="bot[frequency_penalty]" value="<?php
                        echo  esc_html( $wpaicg_chat_frequency_penalty ) ;
                        ?>" >
                    </div>

                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label">Presence Penalty:</label>
                        <input type="text" class="regular-text wpaicg_chatbot_presence_penalty" id="label_presence_penalty" name="bot[presence_penalty]" value="<?php
                        echo  esc_html( $wpaicg_chat_presence_penalty ) ;
                        ?>" >
                    </div>
                    <div class="wpaicg-bot-footer">
                        <div>
                            <button type="button" class="button wpaicg-bot-step" data-type="style">Previous</button>
                            <button type="button" class="button button-primary wpaicg-bot-step" data-type="<?php echo \WPAICG\wpaicg_util_core()->wpaicg_is_pro() ? 'moderation' : 'audio'?>">Next</button>
                        </div>
                        <button class="button button-primary wpaicg-chatbot-submit">Save</button>
                    </div>
                </div>
                <?php
                if(\WPAICG\wpaicg_util_core()->wpaicg_is_pro()):
                    ?>
                    <div class="wpaicg-bot-moderation wpaicg-bot-wizard" style="display: none">
                        <h3>Moderation</h3>
                        <div class="wpaicg-mb-10">
                            <label class="wpaicg-form-label">Enable:</label>
                            <input name="bot[moderation]" value="1" type="checkbox" class="wpaicg_chatbot_moderation">
                        </div>
                        <div class="wpaicg-mb-10">
                            <label class="wpaicg-form-label">Model:</label>
                            <select class="regular-text wpaicg_chatbot_moderation_model"  name="bot[moderation_model]" >
                                <option value="text-moderation-latest">text-moderation-latest</option>
                                <option value="text-moderation-stable">text-moderation-stable</option>
                            </select>
                        </div>
                        <div class="wpaicg-mb-10">
                            <label class="wpaicg-form-label">Notice:</label>
                            <textarea class="wpaicg_chatbot_moderation_notice" rows="8" name="bot[moderation_notice]">Your message has been flagged as potentially harmful or inappropriate. Please ensure that your messages are respectful and do not contain language or content that could be offensive or harmful to others. Thank you for your cooperation.</textarea>
                        </div>
                        <div class="wpaicg-bot-footer">
                            <div>
                            <button type="button" class="button wpaicg-bot-step" data-type="parameters">Previous</button>
                            <button type="button" class="button button-primary wpaicg-bot-step" data-type="audio">Next</button>
                            </div>
                            <button class="button button-primary wpaicg-chatbot-submit">Save</button>
                        </div>
                    </div>
                <?php
                endif;
                ?>
                <div class="wpaicg-bot-audio wpaicg-bot-wizard" style="display: none">
                    <h3>Voice Input</h3>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label">Enable:</label>
                        <input value="1" type="checkbox" class="wpaicg_chatbot_audio_enable" name="bot[audio_enable]">
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label">Mic Color:</label>
                        <input value="<?php echo esc_html($wpaicg_mic_color)?>" type="text" class="wpaicgchat_color wpaicg_chatbot_mic_color" name="bot[mic_color]">
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label">Stop Color:</label>
                        <input value="<?php echo esc_html($wpaicg_stop_color)?>" type="text" class="wpaicgchat_color wpaicg_chatbot_stop_color" name="bot[stop_color]">
                    </div>
                    <div class="wpaicg-bot-footer">
                        <div>
                        <button type="button" class="button wpaicg-bot-step" data-type="parameters">Previous</button>
                        <button type="button" class="button button-primary wpaicg-bot-step" data-type="custom">Next</button>
                        </div>
                        <button class="button button-primary wpaicg-chatbot-submit">Save</button>
                    </div>
                </div>
                <div class="wpaicg-bot-custom wpaicg-bot-wizard" style="display: none">
                    <h3>Custom Text</h3>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label">AI Name:</label>
                        <input type="text" class="regular-text wpaicg_chatbot_ai_name" name="bot[ai_name]" value="AI" >
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label">You:</label>
                        <input type="text" class="regular-text wpaicg_chatbot_you" name="bot[you]" value="You" >
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label">AI Thinking:</label>
                        <input type="text" class="regular-text wpaicg_chatbot_ai_thinking" name="bot[ai_thinking]" value="AI thinking" >
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label">Placeholder:</label>
                        <input type="text" class="regular-text wpaicg_chatbot_placeholder" name="bot[placeholder]" value="Type message.." >
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label">Welcome Message:</label>
                        <input type="text" class="regular-text wpaicg_chatbot_welcome" name="bot[welcome]" value="Hello human, I am a GPT powered AI chat bot. Ask me anything!" >
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label">No Answer Message:</label>
                        <input class="regular-text wpaicg_chatbot_no_answer" type="text" value="" name="bot[no_answer]">
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label">Footer Note:</label>
                        <input class="regular-text wpaicg_chatbot_footer_text" value="" type="text" name="bot[footer_text]" placeholder="Powered by ...">
                    </div>
                    <div class="wpaicg-bot-footer">
                        <div>
                        <button type="button" class="button wpaicg-bot-step" data-type="audio">Previous</button>
                        <button type="button" class="button button-primary wpaicg-bot-step" data-type="context">Next</button>
                        </div>
                        <button class="button button-primary wpaicg-chatbot-submit">Save</button>
                    </div>
                </div>
                <div class="wpaicg-bot-context wpaicg-bot-wizard" style="display: none">
                    <h3>Context</h3>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label">Remember Conversation:</label>
                        <select name="bot[remember_conversation]" class="wpaicg_chatbot_remember_conversation">
                            <option value="yes">Yes</option>
                            <option value="no">No</option>
                        </select>
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label">Remember Conv. Up To:</label>
                        <select name="bot[conversation_cut]" class="wpaicg_chatbot_conversation_cut">
                            <?php
                            for($i=3;$i<=20;$i++){
                                echo '<option'.(10 == $i ? ' selected':'').' value="'.esc_html($i).'">'.esc_html($i).'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label">User Aware:</label>
                        <select name="bot[user_aware]" class="wpaicg_chatbot_user_aware">
                            <option value="no">No</option>
                            <option value="yes">Yes</option>
                        </select>
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label">Content Aware:</label>
                        <select name="bot[content_aware]" class="wpaicg_chatbot_content_aware">
                            <option value="yes">Yes</option>
                            <option value="no">No</option>
                        </select>
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label">Use Excerpt:</label>
                        <input checked type="checkbox" id="wpaicg_chat_excerpt" class="wpaicg_chatbot_chat_excerpt">
                    </div>

                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label">Use Embeddings:</label>
                        <input type="checkbox" value="1" name="bot[embedding]" id="wpaicg_chat_embedding" class="asdisabled wpaicg_chatbot_embedding">
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label">Method:</label>
                        <select disabled name="bot[embedding_type]" id="wpaicg_chat_embedding_type" class="asdisabled wpaicg_chatbot_embedding_type">
                            <option value="openai">Embeddings + Completion</option>
                            <option value="">Embeddings only</option>
                        </select>
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label">Nearest Answers Up To:</label>
                        <select disabled name="bot[embedding_top]" id="wpaicg_chat_embedding_top" class="asdisabled wpaicg_chatbot_embedding_top">
                            <?php
                            for($i = 1; $i <=5;$i++){
                                echo '<option value="'.esc_html($i).'">'.esc_html($i).'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label">Additional Context?:</label>
                        <input name="bot[chat_addition]" class="wpaicg_chatbot_chat_addition" value="1" type="checkbox" id="wpaicg_chat_addition">
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label">Context:</label>
                        <textarea disabled name="bot[chat_addition_text]" id="wpaicg_chat_addition_text" class="regular-text wpaicg_chatbot_chat_addition_text"></textarea>
                    </div>
                    <div class="wpaicg-bot-footer">
                        <div>
                        <button type="button" class="button wpaicg-bot-step" data-type="custom">Previous</button>
                        <button type="button" class="button button-primary wpaicg-bot-step" data-type="logs">Next</button>
                        </div>
                        <button class="button button-primary wpaicg-chatbot-submit">Save</button>
                    </div>
                </div>
                <div class="wpaicg-bot-logs wpaicg-bot-wizard" style="display: none">
                    <h3>Logs</h3>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label">Save Chat Logs:</label>
                        <input<?php echo $wpaicg_save_logs ? ' checked': ''?> class="wpaicg_chatbot_save_logs" value="1" type="checkbox" name="bot[save_logs]">
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label">Save Prompt:</label>
                        <input disabled class="wpaicg_chatbot_log_request" value="1" type="checkbox" name="bot[log_request]">
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label">Display Notice:</label>
                        <input disabled <?php echo $wpaicg_log_notice ? ' checked': ''?> class="wpaicg_chatbot_log_notice" value="1" type="checkbox" name="bot[log_notice]">
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label">Notice Text:</label>
                        <textarea disabled class="wpaicg_chatbot_log_notice_message" name="bot[log_notice_message]"><?php echo esc_html($wpaicg_log_notice_message)?></textarea>
                    </div>
                    <div class="wpaicg-bot-footer">
                        <div>
                        <button type="button" class="button wpaicg-bot-step" data-type="context">Previous</button>
                        <button type="button" class="button button-primary wpaicg-bot-step" data-type="tokens">Next</button>
                        </div>
                        <button class="button button-primary wpaicg-chatbot-submit">Save</button>
                    </div>
                </div>
                <div class="wpaicg-bot-tokens wpaicg-bot-wizard" style="display: none">
                    <h3>Token Handling</h3>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label">Limit Registered User:</label>
                        <input<?php echo $wpaicg_user_limited ? ' checked': ''?> type="checkbox" value="1" class="wpaicg_user_token_limit wpaicg_chatbot_user_limited" name="bot[user_limited]">
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label">Token Limit:</label>
                        <input<?php echo $wpaicg_user_limited ? '' : ' disabled'?> style="width: 80px" class="wpaicg_user_token_limit_text wpaicg_chatbot_user_tokens" type="text" value="<?php echo esc_html($wpaicg_user_tokens)?>" name="bot[user_tokens]">
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label">Role based limit:</label>
                        <?php
                        foreach($wpaicg_roles as $key=>$wpaicg_role){
                            echo '<input class="wpaicg_role_'.esc_html($key).'" type="hidden" name="bot[limited_roles]['.esc_html($key).']">';
                        }
                        ?>
                        <input type="checkbox" value="1" class="wpaicg_role_limited" name="bot[role_limited]">
                        <a href="javascript:void(0)" class="wpaicg_limit_set_role<?php echo $wpaicg_user_limited ? ' ': ' disabled'?>">Set Limit</a>
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label">Limit Non-Registered User:</label>
                        <input<?php echo $wpaicg_guest_limited ? ' checked': ''?> type="checkbox" class="wpaicg_guest_token_limit wpaicg_chatbot_guest_limited" value="1" name="bot[guest_limited]">
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label">Token Limit:</label>
                        <input<?php echo $wpaicg_guest_limited ? '' : ' disabled'?> class="wpaicg_guest_token_limit_text wpaicg_chatbot_guest_tokens" style="width: 80px" type="text" value="<?php echo esc_html($wpaicg_guest_tokens)?>" name="bot[guest_tokens]">
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label">Notice:</label>
                        <input type="text" value="<?php echo esc_html($wpaicg_limited_message)?>" name="bot[limited_message]" class="wpaicg_chatbot_limited_message">
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label">Reset Limit:</label>
                        <select name="bot[reset_limit]" class="wpaicg_chatbot_reset_limit">
                            <option value="0">Never</option>
                            <option value="1">1 Day</option>
                            <option value="3">3 Days</option>
                            <option value="7">1 Week</option>
                            <option value="14">2 Weeks</option>
                            <option value="30">1 Month</option>
                            <option value="60">2 Months</option>
                            <option value="90">3 Months</option>
                            <option value="180">6 Months</option>
                        </select>
                    </div>
                    <div class="wpaicg-bot-footer">
                        <div>
                        <button type="button" class="button wpaicg-bot-step" data-type="logs">Previous</button>
                        </div>
                        <button class="button button-primary wpaicg-chatbot-submit">Save</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="wpaicg-grid-3">
            <div class="wpaicg-bot-preview">
                <div class="wpaicg-chat-shortcode"
                     data-user-bg-color="<?php echo esc_html($wpaicg_user_bg_color)?>"
                     data-color="<?php echo esc_html($wpaicg_chat_fontcolor)?>"
                     data-fontsize="<?php echo esc_html($wpaicg_chat_fontsize)?>"
                     data-use-avatar="<?php echo $wpaicg_use_avatar ? '1' : '0'?>"
                     data-user-avatar="<?php echo get_avatar_url('')?>"
                     data-you="You"
                     data-ai-avatar="<?php echo $wpaicg_use_avatar && !empty($wpaicg_ai_avatar_id) ? wp_get_attachment_url(esc_html($wpaicg_ai_avatar_id)) : WPAICG_PLUGIN_URL.'admin/images/chatbot.png'?>"
                     data-ai-name="AI"
                     data-ai-bg-color="<?php echo esc_html($wpaicg_ai_bg_color)?>"
                     data-nonce="<?php echo esc_html(wp_create_nonce( 'wpaicg-chatbox' ))?>"
                     data-post-id="<?php echo get_the_ID()?>"
                     data-url="<?php echo home_url( $wp->request )?>"
                     style="width: <?php echo esc_html($wpaicg_chat_width)?>px;"
                     >
                    <div class="wpaicg-chat-shortcode-content" style="background-color: <?php echo esc_html($wpaicg_chat_bgcolor)?>;">
                        <ul class="wpaicg-chat-shortcode-messages" style="height: <?php echo esc_html($wpaicg_chat_height) - 44?>px;">
                            <li style="background: rgb(0 0 0 / 32%); padding: 10px;margin-bottom: 0;display:none" class="wpaicg_chatbot_log_preview">
                                <p><span class="wpaicg-chat-message"></span></p>
                            </li>
                            <li class="wpaicg-ai-message" style="color: <?php echo esc_html($wpaicg_chat_fontcolor)?>; font-size: <?php echo esc_html($wpaicg_chat_fontsize)?>px; background-color: <?php echo esc_html($wpaicg_ai_bg_color);?>">
                                <p>
                                    <strong style="float: left" class="wpaicg-chat-avatar">AI: </strong>
                                    <span class="wpaicg-chat-message wpaicg_chatbot_welcome_message">Hello human, I am a GPT powered AI chat bot. Ask me anything!</span>
                                </p>
                            </li>
                        </ul>
                        <span class="wpaicg-bot-thinking" style="display: none;background-color: <?php echo esc_html($wpaicg_chat_bgcolor)?>;color:<?php echo esc_html($wpaicg_chat_fontcolor)?>"><span class="wpaicg_chatbot_ai_thinking_view">AI thinking</span>&nbsp;<span class="wpaicg-jumping-dots"><span class="wpaicg-dot-1">.</span><span class="wpaicg-dot-2">.</span><span class="wpaicg-dot-3">.</span></span></span>
                    </div>
                    <div class="wpaicg-chat-shortcode-type" style="background-color: <?php echo esc_html($wpaicg_chat_bgcolor)?>;">
                        <input style="border-color: <?php echo esc_html($wpaicg_border_text_field)?>;background-color: <?php echo esc_html($wpaicg_bg_text_field)?>" type="text" class="wpaicg-chat-shortcode-typing" placeholder="Type message..">
                        <span class="wpaicg-mic-icon" data-type="shortcode" style="<?php echo $wpaicg_audio_enable ? '' : 'display:none'?>;color: <?php echo esc_html($wpaicg_mic_color)?>">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path d="M176 0C123 0 80 43 80 96V256c0 53 43 96 96 96s96-43 96-96V96c0-53-43-96-96-96zM48 216c0-13.3-10.7-24-24-24s-24 10.7-24 24v40c0 89.1 66.2 162.7 152 174.4V464H104c-13.3 0-24 10.7-24 24s10.7 24 24 24h72 72c13.3 0 24-10.7 24-24s-10.7-24-24-24H200V430.4c85.8-11.7 152-85.3 152-174.4V216c0-13.3-10.7-24-24-24s-24 10.7-24 24v40c0 70.7-57.3 128-128 128s-128-57.3-128-128V216z"/></svg>
                        </span>
                        <span class="wpaicg-chat-shortcode-send" style="color:<?php echo esc_html($wpaicg_send_color)?>">
                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10.5004 11.9998H5.00043M4.91577 12.2913L2.58085 19.266C2.39742 19.8139 2.3057 20.0879 2.37152 20.2566C2.42868 20.4031 2.55144 20.5142 2.70292 20.5565C2.87736 20.6052 3.14083 20.4866 3.66776 20.2495L20.3792 12.7293C20.8936 12.4979 21.1507 12.3822 21.2302 12.2214C21.2993 12.0817 21.2993 11.9179 21.2302 11.7782C21.1507 11.6174 20.8936 11.5017 20.3792 11.2703L3.66193 3.74751C3.13659 3.51111 2.87392 3.39291 2.69966 3.4414C2.54832 3.48351 2.42556 3.59429 2.36821 3.74054C2.30216 3.90893 2.3929 4.18231 2.57437 4.72906L4.91642 11.7853C4.94759 11.8792 4.96317 11.9262 4.96933 11.9742C4.97479 12.0168 4.97473 12.0599 4.96916 12.1025C4.96289 12.1506 4.94718 12.1975 4.91577 12.2913Z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </span>
                    </div>
                    <div style="<?php echo $wpaicg_include_footer ? '' :' display:none'?>;background-color: <?php echo esc_html($wpaicg_chat_bgcolor)?>" class="wpaicg-chat-shortcode-footer"></div>
                </div>
                <div class="wpaicg-chatbot-widget-icon" style="display: none">
                    <img src="<?php echo esc_html(WPAICG_PLUGIN_URL).'admin/images/chatbot.png'?>" height="75" width="75">
                </div>
            </div>
        </div>
    </div>
</div>
<?php
if(isset($_GET['update_success']) && !empty($_GET['update_success'])){
    ?>
    <p style="color: #26a300; font-weight: bold;">Congratulations! Your chatbot has been saved successfully!</p>
    <?php
}
?>
<?php
$wpaicg_bot_page = isset($_GET['wpage']) && !empty($_GET['wpage']) ? sanitize_text_field($_GET['wpage']) : 1;
$args = array(
    'post_type' => 'wpaicg_chatbot',
    'posts_per_page' => 40,
    'paged' => $wpaicg_bot_page
);
if(isset($_GET['search']) && !empty($_GET['search'])){
    $search = sanitize_text_field($_GET['search']);
    $args['s'] = $search;
}
$wpaicg_bots = new WP_Query($args);
?>
<div class="wpaicg-mb-10">
    <form action="" method="GET">
        <input type="hidden" name="page" value="wpaicg_chatgpt">
        <input type="hidden" name="action" value="bots">
        <input value="<?php echo isset($_GET['search']) && !empty($_GET['search']) ? esc_html($_GET['search']) : ''?>" name="search" type="text" placeholder="Search Bot">
        <button class="button button-primary">Search</button>
        <button type="button" class="button button-primary wpaicg-create-bot">Create New Bot</button>
    </form>
</div>
<table class="wp-list-table widefat fixed striped table-view-list posts">
    <thead>
    <tr>
        <th>Name</th>
        <th>Type</th>
        <th>ID / Shortcode</th>
        <th>Created</th>
        <th>Updated</th>
        <th>Model</th>
        <th>Context</th>
        <th>Action</th>
    </tr>
    </thead>
    <tbody>
    <?php
    if($wpaicg_bots->have_posts()){
        foreach($wpaicg_bots->posts as $wpaicg_bot){
            if(strpos($wpaicg_bot->post_content,'\"') !== false) {
                $wpaicg_bot->post_content = str_replace('\"', '&quot;', $wpaicg_bot->post_content);
            }
            if(strpos($wpaicg_bot->post_content,"\'") !== false) {
                $wpaicg_bot->post_content = str_replace('\\', '', $wpaicg_bot->post_content);
            }

            $bot = json_decode($wpaicg_bot->post_content,true);
            if($bot && is_array($bot)){
            $bot['id'] = $wpaicg_bot->ID;
            $bot['ai_avatar_url'] = isset($bot['ai_avatar_id']) && !empty($bot['ai_avatar_id']) ? wp_get_attachment_url($bot['ai_avatar_id']) : '';
            $bot['icon_url_url'] = isset($bot['icon_url']) && !empty($bot['icon_url']) ? wp_get_attachment_url($bot['icon_url']) : '';
            ?>
                <tr>
                    <td><?php echo esc_html($wpaicg_bot->post_title);?></td>
                    <td><?php echo isset($bot['type']) && $bot['type'] == 'shortcode' ? 'Shortcode' : 'Widget';?></td>
                    <td>
                        <code>
                        <?php
                        if(isset($bot['type']) && $bot['type'] === 'shortcode'){
                            echo '[wpaicg_chatgpt id='.esc_html($wpaicg_bot->ID).']';
                        }
                        else{
                            if(isset($bot['pages'])){
                                $pages = array_map('trim', explode(',', $bot['pages']));
                                $key = 0;
                                foreach($pages as $page){
                                    $link = get_permalink($page);
                                    if(!empty($link)){
                                        $key++;
                                        echo ($key == 1 ? '' : ', ').'<a href="'.$link.'" target="_blank">'.$page.'</a>';
                                    }
                                }
                            }
                        }
                        ?>
                        </code>
                    </td>
                    <td><?php echo esc_html(date('d.m.Y H:i',strtotime($wpaicg_bot->post_date)))?></td>
                    <td><?php echo esc_html(date('d.m.Y H:i',strtotime($wpaicg_bot->post_modified)))?></td>
                    <td><?php echo isset($bot['model']) && !empty($bot['model']) ? esc_html($bot['model']) : ''?></td>
                    <td>
                        <?php
                        if(isset($bot['content_aware']) && $bot['content_aware'] == 'yes'){
                            if(isset($bot['embedding']) && $bot['embedding']){
                                echo 'Embeddings';
                            }
                            else{
                                echo 'Excerpt';
                            }
                        }
                        else{
                            echo 'No';
                        }
                        ?>
                    </td>
                    <td>
                        <button class="button button-primary button-small wpaicg-bot-edit" data-content="<?php echo htmlspecialchars(json_encode($bot,JSON_UNESCAPED_UNICODE),ENT_QUOTES, 'UTF-8')?>">Edit</button>
                        <a class="button-small button button-link-delete" onclick="return confirm('Are you sure?')" href="<?php echo wp_nonce_url(admin_url('admin.php?page=wpaicg_chatgpt&action=bots&wpaicg_bot_delete='.$wpaicg_bot->ID),'wpaicg_delete_'.$wpaicg_bot->ID)?>">Delete</a>
                    </td>
                </tr>
            <?php
            }
        }
    }
    ?>
    </tbody>
</table>
<div class="wpaicg-paginate">
    <?php
    echo paginate_links( array(
        'base'         => admin_url('admin.php?page=wpaicg_chatgpt&action=bots&wpage=%#%'),
        'total'        => $wpaicg_bots->max_num_pages,
        'current'      => $wpaicg_bot_page,
        'format'       => '?wpage=%#%',
        'show_all'     => false,
        'prev_next'    => false,
        'add_args'     => false,
    ));
    ?>
</div>
<script>
    jQuery(document).ready(function ($){
        let wpaicg_roles = <?php echo wp_kses_post(json_encode($wpaicg_roles))?>;
        let defaultAIAvatar = '<?php echo esc_html(WPAICG_PLUGIN_URL).'admin/images/chatbot.png'?>';
        let defaultUserAvatar = '<?php echo get_avatar_url(get_current_user_id())?>';
        $(document).on('change','.wpaicg_chatbot_fontsize', function(){
            wpaicgUpdateRealtime();
        });
        $(document).on('click','.wpaicg_chatbot_save_logs,.wpaicg_chatbot_log_notice,.wpaicg_chatbot_audio_enable,.wpaicg_chatbot_use_avatar,.wpaicg_chatbot_icon_default,.wpaicg_chatbot_ai_avatar_default,.wpaicg_chatbot_ai_avatar_custom,.wpaicg_chatbot_icon_custom', function(){
            wpaicgUpdateRealtime();
        })
        $(document).on('input','.wpaicg_chatbot_welcome,.wpaicg_chatbot_log_notice_message,.wpaicg_chatbot_footer_text,.wpaicg_chatbot_ai_name,.wpaicg_chatbot_you,.wpaicg_chatbot_placeholder,.wpaicg_chatbot_height,.wpaicg_chatbot_width', function(){
            wpaicgUpdateRealtime();
        });
        $(document).on('click', '.wpaicg_chatbot_save_logs', function(e){
            let modalContent = $(e.currentTarget).closest('.wpaicg_modal_content');
            if($(e.currentTarget).prop('checked')){
                modalContent.find('.wpaicg_chatbot_log_request').removeAttr('disabled');
                modalContent.find('.wpaicg_chatbot_log_notice').removeAttr('disabled');
                modalContent.find('.wpaicg_chatbot_log_notice_message').removeAttr('disabled');
            }
            else{
                modalContent.find('.wpaicg_chatbot_log_request').attr('disabled','disabled');
                modalContent.find('.wpaicg_chatbot_log_request').prop('checked',false);
                modalContent.find('.wpaicg_chatbot_log_notice').attr('disabled','disabled');
                modalContent.find('.wpaicg_chatbot_log_notice').prop('checked',false);
                modalContent.find('.wpaicg_chatbot_log_notice_message').attr('disabled','disabled');
            }
        });
        function wpaicgUpdateRealtime(){
            let modalContent = $('.wpaicg_modal_content');
            let fontsize = modalContent.find('.wpaicg_chatbot_fontsize').val();
            let fontcolor = modalContent.find('.wpaicg_chatbot_fontcolor').iris('color');
            let bgcolor = modalContent.find('.wpaicg_chatbot_bgcolor').iris('color');
            let inputbg = modalContent.find('.wpaicg_chatbot_bg_text_field').iris('color');
            let inputborder = modalContent.find('.wpaicg_chatbot_border_text_field').iris('color');
            let sendcolor = modalContent.find('.wpaicg_chatbot_send_color').iris('color');
            let userbg = modalContent.find('.wpaicg_chatbot_user_bg_color').iris('color');;
            let aibg = modalContent.find('.wpaicg_chatbot_ai_bg_color').iris('color');
            let useavatar = modalContent.find('.wpaicg_chatbot_use_avatar').prop('checked') ? true : false;
            let chatwidth = modalContent.find('.wpaicg_chatbot_width').val();
            let chatheight = modalContent.find('.wpaicg_chatbot_height').val();
            let enablemic = modalContent.find('.wpaicg_chatbot_audio_enable').prop('checked') ? true :false;
            let save_log = modalContent.find('.wpaicg_chatbot_save_logs').prop('checked') ? true :false;
            let log_notice = modalContent.find('.wpaicg_chatbot_log_notice').prop('checked') ? true :false;
            let log_notice_msg = modalContent.find('.wpaicg_chatbot_log_notice_message').val();
            let miccolor = modalContent.find('.wpaicg_chatbot_mic_color').iris('color');
            let ai_thinking = modalContent.find('.wpaicg_chatbot_ai_thinking').val();
            let ai_name = modalContent.find('.wpaicg_chatbot_ai_name').val();
            let you_name = modalContent.find('.wpaicg_chatbot_you').val();
            let placeholder = modalContent.find('.wpaicg_chatbot_placeholder').val();
            let welcome = modalContent.find('.wpaicg_chatbot_welcome').val();
            let footer = modalContent.find('.wpaicg_chatbot_footer_text').val();
            let previewWidth = modalContent.find('.wpaicg-bot-preview').width();
            if(welcome !== ''){
                modalContent.find('.wpaicg_chatbot_welcome_message').html(welcome);
            }
            if(save_log && log_notice && log_notice_msg !== ''){
                modalContent.find('.wpaicg_chatbot_log_preview span').html(log_notice_msg);
                modalContent.find('.wpaicg_chatbot_log_preview').show();
            }
            else{
                modalContent.find('.wpaicg_chatbot_log_preview').hide();
            }
            if(modalContent.find('.wpaicg_chatbot_icon_custom').prop('checked') && modalContent.find('.wpaicg_chatbox_icon img').length){
                modalContent.find('.wpaicg-chatbot-widget-icon').html('<img src="'+modalContent.find('.wpaicg_chatbox_icon img').attr('src')+'" height="75" width="75">')
            }
            else{
                modalContent.find('.wpaicg-chatbot-widget-icon').html('<img src="'+defaultAIAvatar+'" height="75" width="75">')
            }
            if(chatwidth === ''){
                chatwidth = 350;
            }
            if(chatheight === ''){
                chatheight = 400;
            }
            if(parseInt(chatwidth) > previewWidth){
                chatwidth = previewWidth;
            }
            modalContent.find('.wpaicg-chat-shortcode').css({
                width: chatwidth+'px'
            });
            let content_height = parseInt(chatheight) - 44;
            if(footer !== ''){
                content_height  = parseInt(chatheight) - 44 - 13;
                modalContent.find('.wpaicg-chat-shortcode-type').css({
                    padding: '5px 5px 0 5px'
                });
                $('.wpaicg-chat-shortcode-footer').html(footer);
                $('.wpaicg-chat-shortcode-footer').show();
            }
            else{
                $('.wpaicg-chat-shortcode-footer').hide();
                modalContent.find('.wpaicg-chat-shortcode-type').css({
                    padding: '5px'
                })
            }
            modalContent.find('.wpaicg-chat-shortcode-content ul').css({
                height: content_height+'px'
            })
            if(enablemic){
                modalContent.find('.wpaicg-mic-icon').show();
            }
            else{
                modalContent.find('.wpaicg-mic-icon').hide();
            }
            modalContent.find('.wpaicg-chat-shortcode-messages li').css({
                'font-size': fontsize+'px',
                'color': fontcolor
            });
            modalContent.find('.wpaicg-chat-shortcode-messages li.wpaicg-ai-message').css({
                'background-color': aibg
            });
            modalContent.find('.wpaicg-chat-shortcode-footer').css({
                'background-color': bgcolor
            });
            modalContent.find('.wpaicg-chat-shortcode').attr('data-fontsize',fontsize);
            modalContent.find('.wpaicg-chat-shortcode').attr('data-color',fontcolor);
            modalContent.find('.wpaicg-chat-shortcode').attr('data-use-avatar',useavatar ? 1 : 0);
            modalContent.find('.wpaicg-chat-shortcode').attr('data-you',you_name);
            modalContent.find('.wpaicg-chat-shortcode').attr('data-ai-name',ai_name);
            modalContent.find('.wpaicg-chat-shortcode').attr('data-ai-bg-color',aibg);
            modalContent.find('.wpaicg-chat-shortcode').attr('data-user-bg-color',userbg);
            if(useavatar){
                let messageAIAvatar = defaultAIAvatar;
                if(modalContent.find('.wpaicg_chatbox_avatar img').length && modalContent.find('.wpaicg_chatbot_ai_avatar_custom').prop('checked')){
                    messageAIAvatar = modalContent.find('.wpaicg_chatbox_avatar img').attr('src');
                }
                modalContent.find('.wpaicg-chat-shortcode').attr('data-ai-avatar',messageAIAvatar);
                modalContent.find('.wpaicg-chat-shortcode-messages li.wpaicg-ai-message .wpaicg-chat-avatar').html('<img src="'+messageAIAvatar+'" height="40" width="40">');
                modalContent.find('.wpaicg-chat-shortcode-messages li.wpaicg-user-message .wpaicg-chat-avatar').html('<img src="'+defaultUserAvatar+'" height="40" width="40">');
            }
            else{
                modalContent.find('.wpaicg-chat-shortcode-messages li.wpaicg-ai-message .wpaicg-chat-avatar').html(ai_name+':&nbsp;');
                modalContent.find('.wpaicg-chat-shortcode-messages li.wpaicg-user-message .wpaicg-chat-avatar').html(you_name+':&nbsp;');
            }
            modalContent.find('.wpaicg-chat-shortcode-messages li.wpaicg-user-message').css({
                'background-color': userbg
            });
            modalContent.find('.wpaicg-chat-shortcode-content').css({
                'background-color': bgcolor
            });
            modalContent.find('.wpaicg-chat-shortcode-type').css({
                'background-color': bgcolor
            });
            modalContent.find('input.wpaicg-chat-shortcode-typing').css({
                'background-color': inputbg,
                'border-color':inputborder
            });
            modalContent.find('input.wpaicg-chat-shortcode-typing').attr('placeholder', placeholder);
            modalContent.find('.wpaicg-chat-shortcode-send').css({
                'color': sendcolor
            })
            modalContent.find('.wpaicg-mic-icon').css({
                'color': miccolor
            });
            let contentaware = modalContent.find('.wpaicg_chatbot_content_aware').val();
            if(contentaware === 'no'){
                $('.wpaicg_chatbot_chat_excerpt').prop('checked', false);
                $('.wpaicg_chatbot_chat_excerpt').attr('disabled','disabled');
                $('.wpaicg_chatbot_embedding').prop('checked', false);
                $('.wpaicg_chatbot_embedding').attr('disabled','disabled');
                $('.wpaicg_chatbot_embedding_type').attr('disabled','disabled');
                $('.wpaicg_chatbot_embedding_top').attr('disabled','disabled');
            }
            if(footer !== ''){

            }

        }
        $(document).on('click','.wpaicg-bot-step',function (e){
            let btn = $(e.currentTarget);
            let step = btn.attr('data-type');
            let wpaicgGrid = btn.closest('.wpaicg-grid');
            wpaicgGrid.find('.wpaicg-bot-wizard').hide();
            wpaicgGrid.find('.wpaicg-bot-'+step).show();
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
        $('.wpaicg_modal_close').click(function (){
            $('.wpaicg_modal_close').closest('.wpaicg_modal').hide();
            $('.wpaicg-overlay').hide();
        });
        $(document).on('click','.wpaicg_chatbot_type_widget', function (){
            $('.wpaicg_modal_content .wpaicg_chatbot_position').show();
            $('.wpaicg_modal_content .wpaicg-widget-icon').show();
            $('.wpaicg_modal_content .wpaicg-widget-pages').show();
            $('.wpaicg_modal_content .wpaicg-chatbot-widget-icon').show();
        });
        $(document).on('click','.wpaicg_chatbot_type_shortcode', function (){
            $('.wpaicg_modal_content .wpaicg-chatbot-widget-icon').hide();
            $('.wpaicg_modal_content .wpaicg_chatbot_position').hide();
            $('.wpaicg_modal_content .wpaicg-widget-pages').hide();
            $('.wpaicg_modal_content .wpaicg-widget-icon').hide();
        });
        $('.wpaicg-create-bot').click(function (){
            $('.wpaicg_modal_title').html('Create New Bot');
            $('.wpaicg_modal_content').html($('.wpaicg-create-bot-default').html());
            $('.wpaicg_modal_content .wpaicgchat_color').wpColorPicker({
                change: function (event, ui){
                    wpaicgUpdateRealtime();
                },
                clear: function(event){
                    wpaicgUpdateRealtime();
                }
            });
            $('.wpaicg_modal_content .wpaicg_chatbot_type_shortcode').prop('checked',true);
            $('.wpaicg_modal_content .wpaicg_chatbot_position').hide();
            $('.wpaicg-overlay').show();
            $('.wpaicg_modal').show();
            wpaicgChatInit();
        });
        $(document).on('click', '.wpaicg_chatbox_icon', function (e){
            e.preventDefault();
            $('.wpaicg_modal_content .wpaicg_chatbox_icon_default').prop('checked',false);
            $('.wpaicg_modal_content .wpaicg_chatbox_icon_custom').prop('checked',true);
            let button = $(e.currentTarget),
                custom_uploader = wp.media({
                    title: '<?php echo __('Insert image')?>',
                    library : {
                        type : 'image'
                    },
                    button: {
                        text: '<?php echo __('Use this image')?>'
                    },
                    multiple: false
                }).on('select', function() {
                    var attachment = custom_uploader.state().get('selection').first().toJSON();
                    button.html('<img width="75" height="75" src="'+attachment.url+'">');
                    $('.wpaicg_modal_content .wpaicg_chatbot_icon_url').val(attachment.id);
                    wpaicgUpdateRealtime();
                }).open();
        });
        $(document).on('click', '.wpaicg_chatbox_avatar', function (e){
            e.preventDefault();
            $('.wpaicg_modal_content .wpaicg_chatbot_ai_avatar_default').prop('checked',false);
            $('.wpaicg_modal_content .wpaicg_chatbox_avatar_custom').prop('checked',true);
            let button = $(e.currentTarget),
                custom_uploader = wp.media({
                    title: '<?php echo __('Insert image')?>',
                    library : {
                        type : 'image'
                    },
                    button: {
                        text: '<?php echo __('Use this image')?>'
                    },
                    multiple: false
                }).on('select', function() {
                    var attachment = custom_uploader.state().get('selection').first().toJSON();
                    button.html('<img width="40" height="40" src="'+attachment.url+'">');
                    $('.wpaicg_modal_content .wpaicg_chatbot_ai_avatar_id').val(attachment.id);
                    wpaicgUpdateRealtime();
                }).open();
        });
        $(document).on('submit','.wpaicg_modal_content .wpaicg-bot-form', function (e){
            e.preventDefault();
            let form = $(e.currentTarget);
            let btn = form.find('.wpaicg-chatbot-submit');
            let data = form.serialize();
            let name = form.find('.wpaicg_chatbot_name').val();
            if(name === ''){
                alert('Please enter a name for your awesome chat bot');
            }
            else {
                $.ajax({
                    url: '<?php echo esc_url(admin_url('admin-ajax.php'))?>',
                    data: data,
                    type: 'POST',
                    dataType: 'JSON',
                    beforeSend: function () {
                        wpaicgLoading(btn)
                    },
                    success: function (res) {
                        wpaicgRmLoading(btn);
                        if (res.status === 'success') {
                            window.location.href = '<?php echo admin_url('admin.php?page=wpaicg_chatgpt&action=bots&update_success=true')?>';
                        } else {
                            alert(res.msg);
                        }
                    }
                })
            }
        });
        $('.wpaicg-bot-edit').click(function (){
            let fields = $(this).attr('data-content');
            // fields = fields.replace(/\\/g,'');
            fields = JSON.parse(fields);
            $('.wpaicg_modal_title').html('Edit Bot');
            $('.wpaicg_modal_content').html($('.wpaicg-create-bot-default').html());
            let modalContent = $('.wpaicg_modal_content');
            let wpaicg_save_log = false;
            modalContent.find('.wpaicg_chatbot_log_request').removeAttr('disabled');
            modalContent.find('.wpaicg_chatbot_log_notice').removeAttr('disabled');
            modalContent.find('.wpaicg_chatbot_log_notice_message').removeAttr('disabled');
            $.each(fields, function (key, field){
                if(key === 'chat_addition' && field === '1'){
                    modalContent.find('.wpaicg_chatbot_chat_addition_text').removeAttr('disabled');
                }
                if(typeof field === 'string' && field.indexOf('&quot;') > -1) {
                    field = field.replace(/&quot;/g, '"');
                }
                if(key === 'type'){
                    if(field === 'widget'){
                        modalContent.find('.wpaicg-chatbot-widget-icon').show();
                        modalContent.find('.wpaicg-widget-icon').show();
                        modalContent.find('.wpaicg-widget-pages').show();
                        modalContent.find('.wpaicg_chatbot_position').show();
                    }
                    else{
                        modalContent.find('.wpaicg-chatbot-widget-icon').hide();
                    }
                    modalContent.find('.wpaicg_chatbot_type_'+field).prop('checked',true);
                }
                else if(key === 'icon'){
                    modalContent.find('.wpaicg_chatbot_icon_default').prop('checked',false);
                    modalContent.find('.wpaicg_chatbot_icon_custom').prop('checked',false);
                    modalContent.find('.wpaicg_chatbot_icon_'+field).prop('checked',true);
                    if(field === 'custom' && fields.icon_url_url !== ''){
                        modalContent.find('.wpaicg_chatbox_icon').html('<img src="'+fields.icon_url_url+'" height="75" width="75">');
                        modalContent.find('.wpaicg-chatbot-widget-icon').html('<img src="'+fields.icon_url_url+'" height="75" width="75">');
                    }
                }
                else if(key === 'ai_avatar'){
                    modalContent.find('.wpaicg_chatbot_ai_avatar_default').prop('checked',false);
                    modalContent.find('.wpaicg_chatbot_ai_avatar_custom').prop('checked',false);
                    modalContent.find('.wpaicg_chatbot_ai_avatar_'+field).prop('checked',true);
                    if(field === 'custom' && fields.ai_avatar_url !== ''){
                        modalContent.find('.wpaicg_chatbox_avatar').html('<img src="'+fields.ai_avatar_url+'" height="40" width="40">');
                    }
                }
                else if(key === 'moderation_notice'){
                    if(field === ''){
                        field = 'Your message has been flagged as potentially harmful or inappropriate. Please ensure that your messages are respectful and do not contain language or content that could be offensive or harmful to others. Thank you for your cooperation.';
                    }
                    modalContent.find('.wpaicg_chatbot_'+key).val(field);
                }
                else if(key === 'position'){
                    modalContent.find('.wpaicg_chatbot_position_left').prop('checked',false);
                    modalContent.find('.wpaicg_chatbot_position_right').prop('checked',false);
                    modalContent.find('.wpaicg_chatbot_position_'+field).prop('checked',true);
                }
                else if((key === 'log_request' || key === 'audio_enable' || key === 'moderation' || key === 'use_avatar' || key === 'chat_addition' || key === 'save_logs' || key === 'log_notice') && field === '1'){
                    if(key === 'save_logs'){
                        wpaicg_save_log = true;
                    }
                    if((key === 'log_request' || key === 'log_notice' || key === 'log_request') && wpaicg_save_log){
                        modalContent.find('.wpaicg_chatbot_'+key).prop('checked',true);
                        modalContent.find('.wpaicg_chatbot_'+key).removeAttr('disabled');
                    }
                    else if((key === 'log_request' || key === 'log_notice' || key === 'log_request') && !wpaicg_save_log){
                        modalContent.find('.wpaicg_chatbot_'+key).prop('checked',false);
                        modalContent.find('.wpaicg_chatbot_'+key).attr('disabled','disabled');
                    }
                    else{
                        modalContent.find('.wpaicg_chatbot_'+key).prop('checked',true);
                    }
                }
                else if(key === 'user_limited' && field === '1'){
                    modalContent.find('.wpaicg_chatbot_user_limited').prop('checked',true);
                    modalContent.find('.wpaicg_chatbot_user_tokens').removeAttr('disabled');
                    modalContent.find('.wpaicg_limit_set_role').addClass('disabled');
                    modalContent.find('.wpaicg_role_limited').prop('checked',false);
                }
                else if(key === 'role_limited' && field === '1'){
                    modalContent.find('.wpaicg_role_limited').prop('checked',true);
                    modalContent.find('.wpaicg_chatbot_user_limited').prop('checked',false);
                    modalContent.find('.wpaicg_chatbot_user_tokens').attr('disabled','disabled');
                    modalContent.find('.wpaicg_limit_set_role').removeClass('disabled');
                }
                else if(key === 'guest_limited' && field === '1'){
                    modalContent.find('.wpaicg_chatbot_guest_limited').prop('checked',true);
                    modalContent.find('.wpaicg_chatbot_guest_tokens').removeAttr('disabled');
                }
                else if(key === 'embedding' && field === '1'){
                    modalContent.find('.wpaicg_chatbot_chat_excerpt').prop('checked',false);
                    modalContent.find('.wpaicg_chatbot_chat_excerpt').addClass('asdisabled');
                    modalContent.find('.wpaicg_chatbot_embedding').removeClass('asdisabled');
                    modalContent.find('.wpaicg_chatbot_embedding').prop('checked',true);
                    modalContent.find('.wpaicg_chatbot_embedding_type').removeAttr('disabled');
                    modalContent.find('.wpaicg_chatbot_embedding_top').removeAttr('disabled');
                }
                if(key === 'limited_roles'){
                    if(typeof field === 'object'){
                        $.each(field, function(role,limit_num){
                            modalContent.find('.wpaicg_role_'+role).val(limit_num);
                        })
                    }
                }
                else{
                    if(typeof field === 'string' && field.indexOf('&quot;') > -1) {
                        field = field.replace(/&quot;/g, '"');
                    }
                    if(key === 'limited_message' && field === ''){
                        field = 'You have reached your token limit.';
                    }
                    if(key === 'log_notice_message' && !wpaicg_save_log){
                        modalContent.find('.wpaicg_chatbot_log_notice_message').attr('disabled','disabled');
                    }
                    modalContent.find('.wpaicg_chatbot_'+key).val(field);
                }
            });
            if(!wpaicg_save_log){
                modalContent.find('.wpaicg_chatbot_log_request').prop('checked',false);
                modalContent.find('.wpaicg_chatbot_log_request').attr('disabled','disabled');
                modalContent.find('.wpaicg_chatbot_log_notice').prop('checked',false);
                modalContent.find('.wpaicg_chatbot_log_notice').attr('disabled','disabled');
                modalContent.find('.wpaicg_chatbot_log_notice_message').attr('disabled','disabled');
            }
            $('.wpaicg_modal_content .wpaicgchat_color').wpColorPicker({
                change: function (event, ui){
                    wpaicgUpdateRealtime();
                },
                clear: function(event){
                    wpaicgUpdateRealtime();
                }
            });
            $('.wpaicg-overlay').show();
            $('.wpaicg_modal').show();
            wpaicgUpdateRealtime();
            wpaicgChatInit();
        });
        $('.wpaicg_modal_close_second').click(function (){
            $('.wpaicg_modal_close_second').closest('.wpaicg_modal_second').hide();
            $('.wpaicg-overlay-second').hide();
        });
        $(document).on('keypress','.wpaicg_user_token_limit_text,.wpaicg_update_role_limit,.wpaicg_guest_token_limit_text', function (e){
            var charCode = (e.which) ? e.which : e.keyCode;
            if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode !== 46) {
                return false;
            }
            return true;
        });
        $(document).on('click','.wpaicg_limit_set_role',function (e){
            if(!$(e.currentTarget).hasClass('disabled')) {
                if ($('.wpaicg_modal_content .wpaicg_role_limited').prop('checked')) {
                    let html = '';
                    $.each(wpaicg_roles, function (key, role) {
                        let valueRole = $('.wpaicg_modal_content .wpaicg_role_'+key).val();
                        html += '<div style="padding: 5px;display: flex;justify-content: space-between;align-items: center;"><label><strong>'+role+'</strong></label><input class="wpaicg_update_role_limit" data-target="'+key+'" value="'+valueRole+'" placeholder="Empty for no-limit" type="text"></div>';
                    });
                    html += '<div style="padding: 5px"><button class="button button-primary wpaicg_save_role_limit" style="width: 100%;margin: 5px 0;">Save</button></div>';
                    $('.wpaicg_modal_title_second').html('Role Limit');
                    $('.wpaicg_modal_content_second').html(html);
                    $('.wpaicg-overlay-second').css('display','flex');
                    $('.wpaicg_modal_second').show();

                } else {
                    $.each(wpaicg_roles, function (key, role) {
                        $('.wpaicg_modal_content .wpaicg_role_' + key).val('');
                    })
                }
            }
        });
        $(document).on('click','.wpaicg_save_role_limit', function (e){
            $('.wpaicg_update_role_limit').each(function (idx, item){
                let input = $(item);
                let target = input.attr('data-target');
                $('.wpaicg_role_'+target).val(input.val());
            });
            $('.wpaicg_modal_close_second').closest('.wpaicg_modal_second').hide();
            $('.wpaicg-overlay-second').hide();
        });
        $(document).on('click','.wpaicg_chatbot_embedding', function (e){
            $('.wpaicg_modal_content .wpaicg_chatbot_embedding').prop('checked',true);
            $('.wpaicg_modal_content .wpaicg_chatbot_embedding').removeClass('asdisabled');
            $('.wpaicg_modal_content .wpaicg_chatbot_chat_excerpt').prop('checked',false);
            $('.wpaicg_modal_content .wpaicg_chatbot_chat_excerpt').addClass('asdisabled');
            $('.wpaicg_modal_content .wpaicg_chatbot_embedding_type').removeClass('asdisabled');
            $('.wpaicg_modal_content .wpaicg_chatbot_embedding_type').removeAttr('disabled');
            $('.wpaicg_modal_content .wpaicg_chatbot_embedding_top').removeClass('asdisabled');
            $('.wpaicg_modal_content .wpaicg_chatbot_embedding_top').removeAttr('disabled');
        });
        $(document).on('click','.wpaicg_chatbot_chat_addition', function (e){
            if($(e.currentTarget).prop('checked')){
                $('.wpaicg_modal_content .wpaicg_chatbot_chat_addition_text').removeAttr('disabled');
            }
            else{
                $('.wpaicg_modal_content .wpaicg_chatbot_chat_addition_text').attr('disabled','disabled');
            }
        });
        $(document).on('click','.wpaicg_role_limited', function (e){
            if($(e.currentTarget).prop('checked')){
                $('.wpaicg_modal_content .wpaicg_chatbot_user_tokens').attr('disabled','disabled');
                $('.wpaicg_modal_content .wpaicg_chatbot_user_limited').prop('checked',false);
                $('.wpaicg_modal_content .wpaicg_limit_set_role').removeClass('disabled');
            }
            else{
                $('.wpaicg_modal_content .wpaicg_limit_set_role').addClass('disabled');
            }
        })
        $(document).on('click','.wpaicg_chatbot_user_limited', function (e){
            if($(e.currentTarget).prop('checked')){
                $('.wpaicg_modal_content .wpaicg_chatbot_user_tokens').removeAttr('disabled');
                $('.wpaicg_modal_content .wpaicg_role_limited').prop('checked',false);
                $('.wpaicg_modal_content .wpaicg_limit_set_role').addClass('disabled');
            }
            else{
                $('.wpaicg_modal_content .wpaicg_chatbot_user_tokens').attr('disabled','disabled');
            }
        });
        $(document).on('click','.wpaicg_chatbot_guest_limited', function (e){
            if($(e.currentTarget).prop('checked')){
                $('.wpaicg_modal_content .wpaicg_chatbot_guest_tokens').removeAttr('disabled');
            }
            else{
                $('.wpaicg_modal_content .wpaicg_chatbot_guest_tokens').attr('disabled','disabled');
            }
        });
        $(document).on('click','.wpaicg_chatbot_chat_excerpt', function (e){
            $('.wpaicg_modal_content .wpaicg_chatbot_chat_excerpt').prop('checked',true);
            $('.wpaicg_modal_content .wpaicg_chatbot_chat_excerpt').removeClass('asdisabled');
            $('.wpaicg_modal_content .wpaicg_chatbot_embedding').prop('checked', false);
            $('.wpaicg_modal_content .wpaicg_chatbot_embedding').addClass('asdisabled');
            $('.wpaicg_modal_content .wpaicg_chatbot_embedding_type').addClass('asdisabled');
            $('.wpaicg_modal_content .wpaicg_chatbot_embedding_type').attr('disabled','disabled');
            $('.wpaicg_modal_content .wpaicg_chatbot_embedding_top').addClass('asdisabled');
            $('.wpaicg_modal_content .wpaicg_chatbot_embedding_top').attr('disabled','disabled');
        });
        $(document).on('change', '.wpaicg_chatbot_content_aware', function (e){
            if($(e.currentTarget).val() === 'yes'){
                $('.wpaicg_modal_content .wpaicg_chatbot_chat_excerpt').prop('checked',true);
                $('.wpaicg_modal_content .wpaicg_chatbot_chat_excerpt').removeClass('asdisabled');
                $('.wpaicg_modal_content .wpaicg_chatbot_chat_excerpt').removeAttr('disabled');
                $('.wpaicg_modal_content .wpaicg_chatbot_embedding').prop('checked', false);
                $('.wpaicg_modal_content .wpaicg_chatbot_embedding').addClass('asdisabled');
                $('.wpaicg_modal_content .wpaicg_chatbot_embedding').removeAttr('disabled');
                $('.wpaicg_modal_content .wpaicg_chatbot_embedding_type').addClass('asdisabled');
                $('.wpaicg_modal_content .wpaicg_chatbot_embedding_type').attr('disabled','disabled');
                $('.wpaicg_modal_content .wpaicg_chatbot_embedding_top').addClass('asdisabled');
                $('.wpaicg_modal_content .wpaicg_chatbot_embedding_top').attr('disabled','disabled');
            }
            else{
                $('.wpaicg_modal_content .wpaicg_chatbot_chat_excerpt').prop('checked',false);
                $('.wpaicg_modal_content .wpaicg_chatbot_chat_excerpt').removeClass('asdisabled');
                $('.wpaicg_modal_content .wpaicg_chatbot_chat_excerpt').attr('disabled','disabled');
                $('.wpaicg_modal_content .wpaicg_chatbot_embedding').prop('checked', false);
                $('.wpaicg_modal_content .wpaicg_chatbot_embedding').addClass('asdisabled');
                $('.wpaicg_modal_content .wpaicg_chatbot_embedding').attr('disabled','disabled');
                $('.wpaicg_modal_content .wpaicg_chatbot_embedding_type').addClass('asdisabled');
                $('.wpaicg_modal_content .wpaicg_chatbot_embedding_type').attr('disabled','disabled');
                $('.wpaicg_modal_content .wpaicg_chatbot_embedding_top').addClass('asdisabled');
                $('.wpaicg_modal_content .wpaicg_chatbot_embedding_top').attr('disabled','disabled');
            }
        });
    })
</script>

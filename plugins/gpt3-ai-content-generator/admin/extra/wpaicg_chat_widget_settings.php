<?php
if ( ! defined( 'ABSPATH' ) ) exit;
global $wpdb;
$errors = false;
$message = false;
if ( isset( $_POST['wpaicg_submit'] ) ) {
    check_admin_referer('wpaicg_chat_widget_save');
    if ( isset($_POST['wpaicg_chat_temperature']) && (!is_numeric( $_POST['wpaicg_chat_temperature'] ) || floatval( $_POST['wpaicg_chat_temperature'] ) < 0 || floatval( $_POST['wpaicg_chat_temperature'] ) > 1 )) {
        $errors = 'Please enter a valid temperature value between 0 and 1.';
    }
    if (isset($_POST['wpaicg_chat_max_tokens']) && ( !is_numeric( $_POST['wpaicg_chat_max_tokens'] ) || floatval( $_POST['wpaicg_chat_max_tokens'] ) < 64 || floatval( $_POST['wpaicg_chat_max_tokens'] ) > 8000 )) {
        $errors = 'Please enter a valid max token value between 64 and 8000.';
    }
    if (isset($_POST['wpaicg_chat_top_p']) && (!is_numeric( $_POST['wpaicg_chat_top_p'] ) || floatval( $_POST['wpaicg_chat_top_p'] ) < 0 || floatval( $_POST['wpaicg_chat_top_p'] ) > 1 )){
        $errors = 'Please enter a valid top p value between 0 and 1.';
    }
    if (isset($_POST['wpaicg_chat_best_of']) && ( !is_numeric( $_POST['wpaicg_chat_best_of'] ) || floatval( $_POST['wpaicg_chat_best_of'] ) < 1 || floatval( $_POST['wpaicg_chat_best_of'] ) > 20 )) {
        $errors = 'Please enter a valid best of value between 1 and 20.';
    }
    if (isset($_POST['wpaicg_chat_frequency_penalty']) && ( !is_numeric( $_POST['wpaicg_chat_frequency_penalty'] ) || floatval( $_POST['wpaicg_chat_frequency_penalty'] ) < 0 || floatval( $_POST['wpaicg_chat_frequency_penalty'] ) > 2 )) {
        $errors = 'Please enter a valid frequency penalty value between 0 and 2.';
    }
    if (isset($_POST['wpaicg_chat_presence_penalty']) && ( !is_numeric( $_POST['wpaicg_chat_presence_penalty'] ) || floatval( $_POST['wpaicg_chat_presence_penalty'] ) < 0 || floatval( $_POST['wpaicg_chat_presence_penalty'] ) > 2 ) ){
        $errors = 'Please enter a valid presence penalty value between 0 and 2.';
    }
    if(!$errors){
        $wpaicg_keys = array(
            '_wpaicg_chatbox_you',
            '_wpaicg_ai_thinking',
            '_wpaicg_typing_placeholder',
            '_wpaicg_chatbox_welcome_message',
            '_wpaicg_chatbox_ai_name',
            'wpaicg_chat_model',
            'wpaicg_chat_temperature',
            'wpaicg_chat_max_tokens',
            'wpaicg_chat_top_p',
            'wpaicg_chat_best_of',
            'wpaicg_chat_frequency_penalty',
            'wpaicg_chat_presence_penalty',
            'wpaicg_chat_widget',
            'wpaicg_chat_language',
            'wpaicg_conversation_cut',
            'wpaicg_chat_embedding',
            'wpaicg_chat_addition',
            'wpaicg_chat_addition_text',
            'wpaicg_chat_no_answer',
            'wpaicg_chat_embedding_type',
            'wpaicg_chat_embedding_top'
        );
        foreach($wpaicg_keys as $wpaicg_key){
            if(isset($_POST[$wpaicg_key]) && !empty($_POST[$wpaicg_key])){
                update_option($wpaicg_key, \WPAICG\wpaicg_util_core()->sanitize_text_or_array_field($_POST[$wpaicg_key]));
            }
            else{
                delete_option($wpaicg_key);
            }
        }
        $message = "Setting saved successfully";
    }
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
$wpaicg_chat_widget = get_option('wpaicg_chat_widget',[]);
$wpaicg_chat_icon = isset($wpaicg_chat_widget['icon']) && !empty($wpaicg_chat_widget['icon']) ? $wpaicg_chat_widget['icon'] : 'default';
$wpaicg_chat_status = isset($wpaicg_chat_widget['status']) && !empty($wpaicg_chat_widget['status']) ? $wpaicg_chat_widget['status'] : '';
$wpaicg_chat_fontsize = isset($wpaicg_chat_widget['fontsize']) && !empty($wpaicg_chat_widget['fontsize']) ? $wpaicg_chat_widget['fontsize'] : '13';
$wpaicg_chat_fontcolor = isset($wpaicg_chat_widget['fontcolor']) && !empty($wpaicg_chat_widget['fontcolor']) ? $wpaicg_chat_widget['fontcolor'] : '#fff';
$wpaicg_chat_bgcolor = isset($wpaicg_chat_widget['bgcolor']) && !empty($wpaicg_chat_widget['bgcolor']) ? $wpaicg_chat_widget['bgcolor'] : '#222222';
$wpaicg_bg_text_field = isset($wpaicg_chat_widget['bg_text_field']) && !empty($wpaicg_chat_widget['bg_text_field']) ? $wpaicg_chat_widget['bg_text_field'] : '#fff';
$wpaicg_send_color = isset($wpaicg_chat_widget['send_color']) && !empty($wpaicg_chat_widget['send_color']) ? $wpaicg_chat_widget['send_color'] : '#fff';
$wpaicg_border_text_field = isset($wpaicg_chat_widget['border_text_field']) && !empty($wpaicg_chat_widget['border_text_field']) ? $wpaicg_chat_widget['border_text_field'] : '#ccc';
$wpaicg_footer_text = isset($wpaicg_chat_widget['footer_text']) && !empty($wpaicg_chat_widget['footer_text']) ? $wpaicg_chat_widget['footer_text'] : '';
$wpaicg_user_bg_color = isset($wpaicg_chat_widget['user_bg_color']) && !empty($wpaicg_chat_widget['user_bg_color']) ? $wpaicg_chat_widget['user_bg_color'] : '#444654';
$wpaicg_ai_bg_color = isset($wpaicg_chat_widget['ai_bg_color']) && !empty($wpaicg_chat_widget['ai_bg_color']) ? $wpaicg_chat_widget['ai_bg_color'] : '#343541';
$wpaicg_use_avatar = isset($wpaicg_chat_widget['use_avatar']) && !empty($wpaicg_chat_widget['use_avatar']) ? $wpaicg_chat_widget['use_avatar'] : false;
$wpaicg_ai_avatar = isset($wpaicg_chat_widget['ai_avatar']) && !empty($wpaicg_chat_widget['ai_avatar']) ? $wpaicg_chat_widget['ai_avatar'] : 'default';
$wpaicg_ai_avatar_id = isset($wpaicg_chat_widget['ai_avatar_id']) && !empty($wpaicg_chat_widget['ai_avatar_id']) ? $wpaicg_chat_widget['ai_avatar_id'] : '';
$wpaicg_chat_width = isset($wpaicg_chat_widget['width']) && !empty($wpaicg_chat_widget['width']) ? $wpaicg_chat_widget['width'] : '350';
$wpaicg_chat_height = isset($wpaicg_chat_widget['height']) && !empty($wpaicg_chat_widget['height']) ? $wpaicg_chat_widget['height'] : '400';
$wpaicg_chat_position = isset($wpaicg_chat_widget['position']) && !empty($wpaicg_chat_widget['position']) ? $wpaicg_chat_widget['position'] : 'left';
$wpaicg_chat_tone = isset($wpaicg_chat_widget['tone']) && !empty($wpaicg_chat_widget['tone']) ? $wpaicg_chat_widget['tone'] : 'friendly';
$wpaicg_user_aware = isset($wpaicg_chat_widget['user_aware']) && !empty($wpaicg_chat_widget['user_aware']) ? $wpaicg_chat_widget['user_aware'] : 'no';
$wpaicg_chat_proffesion = isset($wpaicg_chat_widget['proffesion']) && !empty($wpaicg_chat_widget['proffesion']) ? $wpaicg_chat_widget['proffesion'] : 'none';
$wpaicg_chat_remember_conversation = isset($wpaicg_chat_widget['remember_conversation']) && !empty($wpaicg_chat_widget['remember_conversation']) ? $wpaicg_chat_widget['remember_conversation'] : 'yes';
$wpaicg_chat_content_aware = isset($wpaicg_chat_widget['content_aware']) && !empty($wpaicg_chat_widget['content_aware']) ? $wpaicg_chat_widget['content_aware'] : 'yes';
$wpaicg_pinecone_api = get_option('wpaicg_pinecone_api','');
$wpaicg_pinecone_environment = get_option('wpaicg_pinecone_environment','');
$wpaicg_save_logs = isset($wpaicg_chat_widget['save_logs']) && !empty($wpaicg_chat_widget['save_logs']) ? $wpaicg_chat_widget['save_logs'] : false;
$wpaicg_log_notice = isset($wpaicg_chat_widget['log_notice']) && !empty($wpaicg_chat_widget['log_notice']) ? $wpaicg_chat_widget['log_notice'] : false;
$wpaicg_log_notice_message = isset($wpaicg_chat_widget['log_notice_message']) && !empty($wpaicg_chat_widget['log_notice_message']) ? $wpaicg_chat_widget['log_notice_message'] : 'Please note that your conversations will be recorded.';
$wpaicg_conversation_cut = get_option('wpaicg_conversation_cut',10);
$wpaicg_embedding_field_disabled = empty($wpaicg_pinecone_api) || empty($wpaicg_pinecone_environment) ? true : false;
$wpaicg_chat_embedding = get_option('wpaicg_chat_embedding',false);
$wpaicg_chat_addition = get_option('wpaicg_chat_addition',false);
$wpaicg_chat_addition_text = get_option('wpaicg_chat_addition_text',false);
$wpaicg_chat_embedding_type = get_option('wpaicg_chat_embedding_type',false);
$wpaicg_chat_embedding_top = get_option('wpaicg_chat_embedding_top',false);
$wpaicg_audio_enable = isset($wpaicg_chat_widget['audio_enable']) ? $wpaicg_chat_widget['audio_enable'] : false;
$wpaicg_mic_color = isset($wpaicg_chat_widget['mic_color']) ? $wpaicg_chat_widget['mic_color'] : '#222';
$wpaicg_stop_color = isset($wpaicg_chat_widget['stop_color']) ? $wpaicg_chat_widget['stop_color'] : '#f00';
$wpaicg_user_limited = isset($wpaicg_chat_widget['user_limited']) ? $wpaicg_chat_widget['user_limited'] : false;
$wpaicg_guest_limited = isset($wpaicg_chat_widget['guest_limited']) ? $wpaicg_chat_widget['guest_limited'] : false;
$wpaicg_user_tokens = isset($wpaicg_chat_widget['user_tokens']) ? $wpaicg_chat_widget['user_tokens'] : 0;
$wpaicg_guest_tokens = isset($wpaicg_chat_widget['guest_tokens']) ? $wpaicg_chat_widget['guest_tokens'] : 0;
$wpaicg_reset_limit = isset($wpaicg_chat_widget['reset_limit']) ? $wpaicg_chat_widget['reset_limit'] : 0;
$wpaicg_limited_message = isset($wpaicg_chat_widget['limited_message']) && !empty($wpaicg_chat_widget['limited_message']) ? $wpaicg_chat_widget['limited_message'] : 'You have reached your token limit.';
$wpaicg_include_footer = (isset($wpaicg_chat_widget['footer_text']) && !empty($wpaicg_chat_widget['footer_text'])) ? 5 : 0;
$wpaicg_chat_widget['role_limited'] = isset($wpaicg_chat_widget['role_limited']) && !empty($wpaicg_chat_widget['role_limited']) ? $wpaicg_chat_widget['role_limited'] : false;
$wpaicg_chat_widget['limited_roles'] = isset($wpaicg_chat_widget['limited_roles']) && !empty($wpaicg_chat_widget['limited_roles']) ? $wpaicg_chat_widget['limited_roles'] : array();
$wpaicg_roles = wp_roles()->get_names();
?>
<style>
    .asdisabled{
        background: #ebebeb!important;
    }
    .wpaicg_chatbox_avatar{
        cursor: pointer;
    }
    .wp-picker-holder{
        position: absolute;
    }
    .wpaicg_chatbox_icon{
        cursor: pointer;
    }
    .wpaicg_chatbox_icon svg{
    }
    .wpaicg_chat_widget_content .wpaicg-chatbox{
        height: 100%;
        background-color: <?php echo esc_html($wpaicg_chat_bgcolor)?>;
        border-radius: 5px;
    }
    .wpaicg_widget_open .wpaicg_chat_widget_content{
        height: <?php echo esc_html($wpaicg_chat_height)?>px;
    }
    .wpaicg_chat_widget_content{
        position: absolute;
        bottom: calc(100% + 15px);
        width: <?php echo esc_html($wpaicg_chat_width)?>px;
        overflow: hidden;

    }
    .wpaicg-collapse-content textarea{
        display: inline-block!important;
        width: 48%!important;
    }
    .wpaicg_widget_open .wpaicg_chat_widget_content .wpaicg-chatbox{
        top: 0;
    }
    .wpaicg_chat_widget_content .wpaicg-chatbox{
        position: absolute;
        top: 100%;
        left: 0;
        width: <?php echo esc_html($wpaicg_chat_width)?>px;
        height: <?php echo esc_html($wpaicg_chat_height)?>px;
        transition: top 300ms cubic-bezier(0.17, 0.04, 0.03, 0.94);
    }
    .wpaicg_chat_widget_content .wpaicg-chatbox-content{
        height: <?php echo esc_html($wpaicg_chat_height)- ($wpaicg_include_footer ? 58 : 44)?>px;
    }
    .wpaicg_chat_widget_content .wpaicg-chatbox-content ul{
        box-sizing: border-box;
        height: <?php echo esc_html($wpaicg_chat_height)- ($wpaicg_include_footer ? 58 : 44) -24?>px;
        background: <?php echo esc_html($wpaicg_chat_bgcolor)?>;
    }
    .wpaicg_chat_widget_content .wpaicg-chatbox-content ul li{
        color: <?php echo esc_html($wpaicg_chat_fontcolor)?>;
        font-size: <?php echo esc_html($wpaicg_chat_fontsize)?>px;
    }
    .wpaicg_chat_widget_content .wpaicg-bot-thinking{
        color: <?php echo esc_html($wpaicg_chat_fontcolor)?>;
    }
    .wpaicg_chat_widget_content .wpaicg-chatbox-type{
    <?php
    if($wpaicg_include_footer):
    ?>
        padding: 5px 5px 0 5px;
    <?php
    endif;
    ?>
        border-top: 0;
        background: rgb(0 0 0 / 19%);
    }
    .wpaicg_chat_widget_content .wpaicg-chat-message{
        color: <?php echo esc_html($wpaicg_chat_fontcolor)?>;
    }
    .wpaicg_chat_widget_content input.wpaicg-chatbox-typing{
        background-color: <?php echo esc_html($wpaicg_bg_text_field)?>;
        border-color: <?php echo esc_html($wpaicg_border_text_field)?>;
    }
    .wpaicg_chat_widget_content .wpaicg-chatbox-send{
        color: <?php echo esc_html($wpaicg_send_color)?>;
    }
    .wpaicg-chatbox-footer{
        height: 18px;
        font-size: 11px;
        padding: 0 5px;
        color: <?php echo esc_html($wpaicg_send_color)?>;
        background: rgb(0 0 0 / 19%);
        margin-top:2px;
        margin-bottom: 2px;
    }
    .wpaicg_chat_widget_content input.wpaicg-chatbox-typing:focus{
        outline: none;
    }
    .wpaicg_chat_widget .wpaicg_toggle{
        cursor: pointer;
    }
    .wpaicg_chat_widget .wpaicg_toggle img{
        width: 75px;
        height: 75px;
    }
    .wpaicg-chat-shortcode-type,.wpaicg-chatbox-type{
        position: relative;
    }
    .wpaicg-mic-icon{
        display: flex;
        cursor: pointer;
        position: absolute;
        right: 47px;
    }
    .wpaicg-mic-icon svg{
        width: 16px;
        height: 16px;
        fill: currentColor;
    }
    .wpaicg-chatbox-preview{
        position: relative;
    }
    /*.wpaicg_chat_widget_content{*/
    /*    position: relative;*/
    /*    overflow: unset;*/
    /*}*/
    /*.wpaicg_chat_widget_content .wpaicg-chatbox{*/
    /*    position: relative;*/
    /*}*/
    .wpaicg_toggle{}
    .wpaicg_chat_widget{
        position: absolute;
        bottom: 0;
    }
    .wpaicg_chat_widget_content .wpaicg-chat-ai-message *, .wpaicg_chat_widget_content .wpaicg-chat-user-message *, .wpaicg_chat_widget_content .wpaicg-chat-user-message .wpaicg-chat-message, .wpaicg_chat_widget_content .wpaicg-chat-ai-message .wpaicg-chat-message, .wpaicg_chat_widget_content .wpaicg-chat-ai-message a, .wpaicg_chat_widget_content .wpaicg-chat-user-message a{
        color: inherit!important;
        font-size: inherit!important;
    }
</style>
<div class="wpaicg-alert mb-5">
    <p>If you prefer to use shortcode instead of widget, go to <b>Shortcode</b> tab and configure it.</p>
    <p>Learn how you can train the chat bot with your content <u><b><a href="https://youtu.be/NPMLGwFQYrY" target="_blank">here</a></u></b>.</p>
</div>
<?php
$wpaicg_chat_model = get_option('wpaicg_chat_model','');
$wpaicg_chat_language = get_option('wpaicg_chat_language','');
if ( !empty($errors)) {
    echo  "<h4 id='setting_message' style='color: red;'>" . esc_html( $errors ) . "</h4>" ;
} elseif(!empty($message)) {
    echo  "<h4 id='setting_message' style='color: green;'>" . esc_html( $message ) . "</h4>" ;
}
?>
<div class="wpaicg-grid-three">
    <div class="wpaicg-grid-2 wpaicg-chatbox-preview">
        <div class="wpaicg-chatbox-preview-box" style="height: <?php echo esc_html($wpaicg_chat_height)+100?>px;position: relative;">
            <?php
            include __DIR__.'/wpaicg_chat_widget.php';
            ?>
        </div>
    </div>
    <div class="wpaicg-grid-1">
        <form action="" method="post" id="form-chatbox-setting">
            <?php
            wp_nonce_field('wpaicg_chat_widget_save');
            ?>
            <div class="wpaicg-collapse wpaicg-collapse-active">
                <div class="wpaicg-collapse-title"><span>-</span> Enable / Disable</div>
                <div class="wpaicg-collapse-content">
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Enable Widget:</label>
                        <select name="wpaicg_chat_widget[status]">
                            <option value="">No</option>
                            <option<?php echo $wpaicg_chat_status == 'active' ? ' selected': ''?> value="active">Yes</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="wpaicg-collapse">
                <div class="wpaicg-collapse-title"><span>+</span> Language, Tone and Profession</div>
                <div class="wpaicg-collapse-content">
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Language:</label>
                        <select class="wpaicg-input" id="label_wpai_language"  name="wpaicg_chat_language" >
                            <option value="en" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'en' ? 'selected' : '' ) ;
                            ?>>English</option>
                            <option value="af" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'af' ? 'selected' : '' ) ;
                            ?>>Afrikaans</option>
                            <option value="ar" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'ar' ? 'selected' : '' ) ;
                            ?>>Arabic</option>
                            <option value="bg" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'bg' ? 'selected' : '' ) ;
                            ?>>Bulgarian</option>
                            <option value="zh" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'zh' ? 'selected' : '' ) ;
                            ?>>Chinese</option>
                            <option value="hr" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'hr' ? 'selected' : '' ) ;
                            ?>>Croatian</option>
                            <option value="cs" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'cs' ? 'selected' : '' ) ;
                            ?>>Czech</option>
                            <option value="da" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'da' ? 'selected' : '' ) ;
                            ?>>Danish</option>
                            <option value="nl" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'nl' ? 'selected' : '' ) ;
                            ?>>Dutch</option>
                            <option value="et" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'et' ? 'selected' : '' ) ;
                            ?>>Estonian</option>
                            <option value="fil" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'fil' ? 'selected' : '' ) ;
                            ?>>Filipino</option>
                            <option value="fi" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'fi' ? 'selected' : '' ) ;
                            ?>>Finnish</option>
                            <option value="fr" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'fr' ? 'selected' : '' ) ;
                            ?>>French</option>
                            <option value="de" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'de' ? 'selected' : '' ) ;
                            ?>>German</option>
                            <option value="el" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'el' ? 'selected' : '' ) ;
                            ?>>Greek</option>
                            <option value="he" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'he' ? 'selected' : '' ) ;
                            ?>>Hebrew</option>
                            <option value="hi" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'hi' ? 'selected' : '' ) ;
                            ?>>Hindi</option>
                            <option value="hu" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'hu' ? 'selected' : '' ) ;
                            ?>>Hungarian</option>
                            <option value="id" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'id' ? 'selected' : '' ) ;
                            ?>>Indonesian</option>
                            <option value="it" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'it' ? 'selected' : '' ) ;
                            ?>>Italian</option>
                            <option value="ja" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'ja' ? 'selected' : '' ) ;
                            ?>>Japanese</option>
                            <option value="ko" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'ko' ? 'selected' : '' ) ;
                            ?>>Korean</option>
                            <option value="lv" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'lv' ? 'selected' : '' ) ;
                            ?>>Latvian</option>
                            <option value="lt" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'lt' ? 'selected' : '' ) ;
                            ?>>Lithuanian</option>
                            <option value="ms" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'ms' ? 'selected' : '' ) ;
                            ?>>Malay</option>
                            <option value="no" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'no' ? 'selected' : '' ) ;
                            ?>>Norwegian</option>
                            <option value="pl" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'pl' ? 'selected' : '' ) ;
                            ?>>Polish</option>
                            <option value="pt" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'pt' ? 'selected' : '' ) ;
                            ?>>Portuguese</option>
                            <option value="ro" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'ro' ? 'selected' : '' ) ;
                            ?>>Romanian</option>
                            <option value="ru" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'ru' ? 'selected' : '' ) ;
                            ?>>Russian</option>
                            <option value="sr" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'sr' ? 'selected' : '' ) ;
                            ?>>Serbian</option>
                            <option value="sk" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'sk' ? 'selected' : '' ) ;
                            ?>>Slovak</option>
                            <option value="sl" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'sl' ? 'selected' : '' ) ;
                            ?>>Slovenian</option>
                            <option value="sv" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'sv' ? 'selected' : '' ) ;
                            ?>>Swedish</option>
                            <option value="es" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'es' ? 'selected' : '' ) ;
                            ?>>Spanish</option>
                            <option value="th" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'th' ? 'selected' : '' ) ;
                            ?>>Thai</option>
                            <option value="tr" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'tr' ? 'selected' : '' ) ;
                            ?>>Turkish</option>
                            <option value="uk" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'uk' ? 'selected' : '' ) ;
                            ?>>Ukrainian</option>
                            <option value="vi" <?php
                            echo  ( esc_html( $wpaicg_chat_language ) == 'vi' ? 'selected' : '' ) ;
                            ?>>Vietnamese</option>
                        </select>
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Tone:</label>
                        <select name="wpaicg_chat_widget[tone]">
                            <option<?php echo $wpaicg_chat_tone == 'friendly' ? ' selected': ''?> value="friendly">Friendly</option>
                            <option<?php echo $wpaicg_chat_tone == 'professional' ? ' selected': ''?> value="professional">Professional</option>
                            <option<?php echo $wpaicg_chat_tone == 'sarcastic' ? ' selected': ''?> value="sarcastic">Sarcastic</option>
                            <option<?php echo $wpaicg_chat_tone == 'humorous' ? ' selected': ''?> value="humorous">Humorous</option>
                            <option<?php echo $wpaicg_chat_tone == 'cheerful' ? ' selected': ''?> value="cheerful">Cheerful</option>
                            <option<?php echo $wpaicg_chat_tone == 'anecdotal' ? ' selected': ''?> value="anecdotal">Anecdotal</option>
                        </select>
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Act As:</label>
                        <select name="wpaicg_chat_widget[proffesion]">
                            <option<?php echo $wpaicg_chat_proffesion == 'none' ? ' selected': ''?> value="none">None</option>
                            <option<?php echo $wpaicg_chat_proffesion == 'accountant' ? ' selected': ''?> value="accountant">Accountant</option>
                            <option<?php echo $wpaicg_chat_proffesion == 'advertisingspecialist' ? ' selected': ''?> value="advertisingspecialist">Advertising Specialist</option>
                            <option<?php echo $wpaicg_chat_proffesion == 'architect' ? ' selected': ''?> value="architect">Architect</option>
                            <option<?php echo $wpaicg_chat_proffesion == 'artist' ? ' selected': ''?> value="artist">Artist</option>
                            <option<?php echo $wpaicg_chat_proffesion == 'blogger' ? ' selected': ''?> value="blogger">Blogger</option>
                            <option<?php echo $wpaicg_chat_proffesion == 'businessanalyst' ? ' selected': ''?> value="businessanalyst">Business Analyst</option>
                            <option<?php echo $wpaicg_chat_proffesion == 'businessowner' ? ' selected': ''?> value="businessowner">Business Owner</option>
                            <option<?php echo $wpaicg_chat_proffesion == 'carexpert' ? ' selected': ''?> value="carexpert">Car Expert</option>
                            <option<?php echo $wpaicg_chat_proffesion == 'consultant' ? ' selected': ''?> value="consultant">Consultant</option>
                            <option<?php echo $wpaicg_chat_proffesion == 'counselor' ? ' selected': ''?> value="counselor">Counselor</option>
                            <option<?php echo $wpaicg_chat_proffesion == 'cryptocurrencytrader' ? ' selected': ''?> value="cryptocurrencytrader">Cryptocurrency Trader</option>
                            <option<?php echo $wpaicg_chat_proffesion == 'cryptocurrencyexpert' ? ' selected': ''?> value="cryptocurrencyexpert">Cryptocurrency Expert</option>
                            <option<?php echo $wpaicg_chat_proffesion == 'customersupport' ? ' selected': ''?> value="customersupport">Customer Support</option>
                            <option<?php echo $wpaicg_chat_proffesion == 'designer' ? ' selected': ''?> value="designer">Designer</option>
                            <option<?php echo $wpaicg_chat_proffesion == 'digitalmarketinagency' ? ' selected': ''?> value="digitalmarketinagency">Digital Marketing Agency</option>
                            <option<?php echo $wpaicg_chat_proffesion == 'editor' ? ' selected': ''?> value="editor">Editor</option>
                            <option<?php echo $wpaicg_chat_proffesion == 'engineer' ? ' selected': ''?> value="engineer">Engineer</option>
                            <option<?php echo $wpaicg_chat_proffesion == 'eventplanner' ? ' selected': ''?> value="eventplanner">Event Planner</option>
                            <option<?php echo $wpaicg_chat_proffesion == 'freelancer' ? ' selected': ''?> value="freelancer">Freelancer</option>
                            <option<?php echo $wpaicg_chat_proffesion == 'insuranceagent' ? ' selected': ''?> value="insuranceagent">Insurance Agent</option>
                            <option<?php echo $wpaicg_chat_proffesion == 'insurancebroker' ? ' selected': ''?> value="insurancebroker">Insurance Broker</option>
                            <option<?php echo $wpaicg_chat_proffesion == 'interiordesigner' ? ' selected': ''?> value="interiordesigner">Interior Designer</option>
                            <option<?php echo $wpaicg_chat_proffesion == 'journalist' ? ' selected': ''?> value="journalist">Journalist</option>
                            <option<?php echo $wpaicg_chat_proffesion == 'marketingagency' ? ' selected': ''?> value="marketingagency">Marketing Agency</option>
                            <option<?php echo $wpaicg_chat_proffesion == 'marketingexpert' ? ' selected': ''?> value="marketingexpert">Marketing Expert</option>
                            <option<?php echo $wpaicg_chat_proffesion == 'marketingspecialist' ? ' selected': ''?> value="marketingspecialist">Marketing Specialist</option>
                            <option<?php echo $wpaicg_chat_proffesion == 'photographer' ? ' selected': ''?> value="photographer">Photographer</option>
                            <option<?php echo $wpaicg_chat_proffesion == 'programmer' ? ' selected': ''?> value="programmer">Programmer</option>
                            <option<?php echo $wpaicg_chat_proffesion == 'publicrelationsagency' ? ' selected': ''?> value="publicrelationsagency">Public Relations Agency</option>
                            <option<?php echo $wpaicg_chat_proffesion == 'publisher' ? ' selected': ''?> value="publisher">Publisher</option>
                            <option<?php echo $wpaicg_chat_proffesion == 'realestateagent' ? ' selected': ''?> value="realestateagent">Real Estate Agent</option>
                            <option<?php echo $wpaicg_chat_proffesion == 'recruiter' ? ' selected': ''?> value="recruiter">Recruiter</option>
                            <option<?php echo $wpaicg_chat_proffesion == 'reporter' ? ' selected': ''?> value="reporter">Reporter</option>
                            <option<?php echo $wpaicg_chat_proffesion == 'salesperson' ? ' selected': ''?> value="salesperson">Sales Person</option>
                            <option<?php echo $wpaicg_chat_proffesion == 'salerep' ? ' selected': ''?> value="salerep">Sales Representative</option>
                            <option<?php echo $wpaicg_chat_proffesion == 'seoagency' ? ' selected': ''?> value="seoagency">SEO Agency</option>
                            <option<?php echo $wpaicg_chat_proffesion == 'seoexpert' ? ' selected': ''?> value="seoexpert">SEO Expert</option>
                            <option<?php echo $wpaicg_chat_proffesion == 'socialmediaagency' ? ' selected': ''?> value="socialmediaagency">Social Media Agency</option>
                            <option<?php echo $wpaicg_chat_proffesion == 'student' ? ' selected': ''?> value="student">Student</option>
                            <option<?php echo $wpaicg_chat_proffesion == 'teacher' ? ' selected': ''?> value="teacher">Teacher</option>
                            <option<?php echo $wpaicg_chat_proffesion == 'technicalsupport' ? ' selected': ''?> value="technicalsupport">Technical Support</option>
                            <option<?php echo $wpaicg_chat_proffesion == 'trainer' ? ' selected': ''?> value="trainer">Trainer</option>
                            <option<?php echo $wpaicg_chat_proffesion == 'travelagency' ? ' selected': ''?> value="travelagency">Travel Agency</option>
                            <option<?php echo $wpaicg_chat_proffesion == 'videographer' ? ' selected': ''?> value="videographer">Videographer</option>
                            <option<?php echo $wpaicg_chat_proffesion == 'webdesignagency' ? ' selected': ''?> value="webdesignagency">Web Design Agency</option>
                            <option<?php echo $wpaicg_chat_proffesion == 'webdesignexpert' ? ' selected': ''?> value="webdesignexpert">Web Design Expert</option>
                            <option<?php echo $wpaicg_chat_proffesion == 'webdevelopmentagency' ? ' selected': ''?> value="webdevelopmentagency">Web Development Agency</option>
                            <option<?php echo $wpaicg_chat_proffesion == 'webdevelopmentexpert' ? ' selected': ''?> value="webdevelopmentexpert">Web Development Expert</option>
                            <option<?php echo $wpaicg_chat_proffesion == 'webdesigner' ? ' selected': ''?> value="webdesigner">Web Designer</option>
                            <option<?php echo $wpaicg_chat_proffesion == 'webdeveloper' ? ' selected': ''?> value="webdeveloper">Web Developer</option>
                            <option<?php echo $wpaicg_chat_proffesion == 'writer' ? ' selected': ''?> value="writer">Writer</option>
                        </select>
                    </div>
                </div>
            </div>
            <!--Style-->
            <div class="wpaicg-collapse">
                <div class="wpaicg-collapse-title"><span>+</span> Style</div>
                <div class="wpaicg-collapse-content">
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Icon (75x75):</label>
                        <div style="display: inline-flex; align-items: center">
                            <input<?php echo $wpaicg_chat_icon == 'default' ? ' checked': ''?> class="wpaicg_chatbox_icon_default" type="radio" value="default" name="wpaicg_chat_widget[icon]">
                            <div style="text-align: center">
                                <img style="display: block" src="<?php echo esc_html(WPAICG_PLUGIN_URL).'admin/images/chatbot.png'?>"<br>
                                <strong>Default</strong>
                            </div>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input<?php echo $wpaicg_chat_icon == 'custom' ? ' checked': ''?> type="radio" class="wpaicg_chatbox_icon_custom" value="custom" name="wpaicg_chat_widget[icon]">
                            <div style="text-align: center">
                                <div class="wpaicg_chatbox_icon">
                                    <?php
                                    $wpaicg_chat_icon_url = isset($wpaicg_chat_widget['icon_url']) && !empty($wpaicg_chat_widget['icon_url']) ? $wpaicg_chat_widget['icon_url'] : '';
                                    if(!empty($wpaicg_chat_icon_url) && $wpaicg_chat_icon == 'custom'):
                                        $wpaicg_chatbox_icon_url = wp_get_attachment_url($wpaicg_chat_icon_url);
                                        ?>
                                        <img src="<?php echo esc_html($wpaicg_chatbox_icon_url)?>" width="75" height="75">
                                    <?php
                                    else:
                                        ?>
                                        <svg width="60px" height="60px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M246.6 9.4c-12.5-12.5-32.8-12.5-45.3 0l-128 128c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 109.3V320c0 17.7 14.3 32 32 32s32-14.3 32-32V109.3l73.4 73.4c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3l-128-128zM64 352c0-17.7-14.3-32-32-32s-32 14.3-32 32v64c0 53 43 96 96 96H352c53 0 96-43 96-96V352c0-17.7-14.3-32-32-32s-32 14.3-32 32v64c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V352z"/></svg><br>
                                    <?php
                                    endif;
                                    ?>
                                </div>
                                <strong>Custom</strong>
                            </div>
                        </div>
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Font Size:</label>
                        <select name="wpaicg_chat_widget[fontsize]" class="wpaicg_chat_widget_font_size">
                            <?php
                            for($i = 10; $i <= 30; $i++){
                                echo '<option'.($wpaicg_chat_fontsize == $i ? ' selected': '').' value="'.esc_html($i).'">'.esc_html($i).'px</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Font Color:</label>
                        <input value="<?php echo esc_html($wpaicg_chat_fontcolor)?>" type="text" class="wpaicgchat_color wpaicgchat_font_color" name="wpaicg_chat_widget[fontcolor]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Background Color:</label>
                        <input value="<?php echo esc_html($wpaicg_chat_bgcolor)?>" type="text" class="wpaicgchat_color wpaicgchat_bg_color" name="wpaicg_chat_widget[bgcolor]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Text Field Background:</label>
                        <input value="<?php echo esc_html($wpaicg_bg_text_field)?>" type="text" class="wpaicgchat_color wpaicgchat_input_color" name="wpaicg_chat_widget[bg_text_field]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Text Field Border:</label>
                        <input value="<?php echo esc_html($wpaicg_border_text_field)?>" type="text" class="wpaicgchat_color wpaicgchat_input_border" name="wpaicg_chat_widget[border_text_field]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Button Color:</label>
                        <input value="<?php echo esc_html($wpaicg_send_color)?>" type="text" class="wpaicgchat_color wpaicgchat_send_color" name="wpaicg_chat_widget[send_color]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">User Background Color:</label>
                        <input value="<?php echo esc_html($wpaicg_user_bg_color)?>" type="text" class="wpaicgchat_color wpaicgchat_user_color" name="wpaicg_chat_widget[user_bg_color]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">AI Background Color:</label>
                        <input value="<?php echo esc_html($wpaicg_ai_bg_color)?>" type="text" class="wpaicgchat_color wpaicgchat_ai_color" name="wpaicg_chat_widget[ai_bg_color]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Use Avatars:</label>
                        <input<?php echo $wpaicg_use_avatar ? ' checked':''?> value="1" type="checkbox" class="wpaicgchat_use_avatar" name="wpaicg_chat_widget[use_avatar]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">AI Avatar (40x40):</label>
                        <div style="display: inline-flex; align-items: center">
                            <input<?php echo $wpaicg_ai_avatar == 'default' ? ' checked': ''?> class="wpaicg_chatbox_avatar_default" type="radio" value="default" name="wpaicg_chat_widget[ai_avatar]">
                            <div style="text-align: center">
                                <img style="display: block;width: 40px; height: 40px" src="<?php echo esc_html(WPAICG_PLUGIN_URL).'admin/images/chatbot.png'?>"<br>
                                <strong>Default</strong>
                            </div>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input<?php echo $wpaicg_ai_avatar == 'custom' ? ' checked': ''?> type="radio" class="wpaicg_chatbox_avatar_custom" value="custom" name="wpaicg_chat_widget[ai_avatar]">
                            <div style="text-align: center">
                                <div class="wpaicg_chatbox_avatar">
                                    <?php
                                    if(!empty($wpaicg_ai_avatar_id) && $wpaicg_ai_avatar == 'custom'):
                                        $wpaicg_ai_avatar_url = wp_get_attachment_url($wpaicg_ai_avatar_id);
                                        ?>
                                        <img src="<?php echo esc_html($wpaicg_ai_avatar_url)?>" width="40" height="40">
                                    <?php
                                    else:
                                        ?>
                                        <svg width="40px" height="40px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M246.6 9.4c-12.5-12.5-32.8-12.5-45.3 0l-128 128c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 109.3V320c0 17.7 14.3 32 32 32s32-14.3 32-32V109.3l73.4 73.4c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3l-128-128zM64 352c0-17.7-14.3-32-32-32s-32 14.3-32 32v64c0 53 43 96 96 96H352c53 0 96-43 96-96V352c0-17.7-14.3-32-32-32s-32 14.3-32 32v64c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V352z"/></svg><br>
                                    <?php
                                    endif;
                                    ?>
                                </div>
                                <strong>Custom</strong>
                            </div>
                        </div>
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Width:</label>
                        <input value="<?php echo esc_html($wpaicg_chat_width)?>" style="width: 100px;" class="wpaicg_chat_widget_width" min="100" type="number" name="wpaicg_chat_widget[width]">px
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Height:</label>
                        <input value="<?php echo esc_html($wpaicg_chat_height)?>" style="width: 100px;" class="wpaicg_chat_widget_height" min="100" type="number" name="wpaicg_chat_widget[height]">px
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Position:</label>
                        <input<?php echo $wpaicg_chat_position == 'left' ? ' checked': ''?> type="radio" value="left" name="wpaicg_chat_widget[position]"> Left
                        <input<?php echo $wpaicg_chat_position == 'right' ? ' checked': ''?> type="radio" value="right" name="wpaicg_chat_widget[position]"> Right
                    </div>
                </div>
            </div>
            <!--Parameters-->
            <div class="wpaicg-collapse">
                <div class="wpaicg-collapse-title"><span>+</span> Parameters</div>
                <div class="wpaicg-collapse-content">
                    <div class="mb-5">
                        <label class="wpaicg-form-label" for="wpaicg_chat_model">Model:</label>
                        <select class="regular-text" id="wpaicg_chat_model"  name="wpaicg_chat_model" >
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
                                echo '<option'.($wpaicg_chat_model == $wpaicg_custom_model ? ' selected':'').' value="'.esc_html($wpaicg_custom_model).'">'.esc_html($wpaicg_custom_model).'</option>';
                            }
                            ?>
                        </select>
                        <a class="wpaicg_sync_finetune" href="javascript:void(0)">Sync</a>
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Temperature:</label>
                        <input type="text" class="regular-text" id="label_temperature" name="wpaicg_chat_temperature" value="<?php
                        echo  esc_html( $wpaicg_chat_temperature ) ;
                        ?>">
                    </div>

                    <div class="mb-5">
                        <label class="wpaicg-form-label">Max Tokens:</label>
                        <input type="text" class="regular-text" id="label_max_tokens" name="wpaicg_chat_max_tokens" value="<?php
                        echo  esc_html( $wpaicg_chat_max_tokens ) ;
                        ?>" >
                    </div>

                    <div class="mb-5">
                        <label class="wpaicg-form-label">Top P:</label>
                        <input type="text" class="regular-text" id="label_top_p" name="wpaicg_chat_top_p" value="<?php
                        echo  esc_html( $wpaicg_chat_top_p ) ;
                        ?>" >
                    </div>

                    <div class="mb-5">
                        <label class="wpaicg-form-label">Best Of:</label>
                        <input type="text" class="regular-text" id="label_best_of" name="wpaicg_chat_best_of" value="<?php
                        echo  esc_html( $wpaicg_chat_best_of ) ;
                        ?>" >
                    </div>

                    <div class="mb-5">
                        <label class="wpaicg-form-label">Frequency Penalty:</label>
                        <input type="text" class="regular-text" id="label_frequency_penalty" name="wpaicg_chat_frequency_penalty" value="<?php
                        echo  esc_html( $wpaicg_chat_frequency_penalty ) ;
                        ?>" >
                    </div>

                    <div class="mb-5">
                        <label class="wpaicg-form-label">Presence Penalty:</label>
                        <input type="text" class="regular-text" id="label_presence_penalty" name="wpaicg_chat_presence_penalty" value="<?php
                        echo  esc_html( $wpaicg_chat_presence_penalty ) ;
                        ?>" >
                    </div>
                </div>
            </div>
            <!--Moderation-->
            <div class="wpaicg-collapse">
                <div class="wpaicg-collapse-title">
                    <span>+</span>Moderation
                    <?php
                    if(!\WPAICG\wpaicg_util_core()->wpaicg_is_pro()){
                        echo '<small style="background: #f90;padding: 1px 4px;border-radius: 2px;display: inline-block;margin-left: 5px;color: #000;">Pro Feature</small>';
                    }
                    ?>
                </div>
                <div class="wpaicg-collapse-content">
                    <?php
                    if(!\WPAICG\wpaicg_util_core()->wpaicg_is_pro()):
                        ?>
                        <div class="mb-5">
                            <label class="wpaicg-form-label">Enable:</label>
                            <input disabled type="checkbox"> Available in Pro
                        </div>
                        <div class="mb-5">
                            <label class="wpaicg-form-label">Model:</label>
                            <select disabled class="regular-text">
                                <option value="text-moderation-latest">text-moderation-latest</option>
                                <option value="text-moderation-stable">text-moderation-stable</option>
                            </select>
                        </div>
                        <div class="mb-5">
                            <label class="wpaicg-form-label">Notice:</label>
                            <textarea disabled >Your message has been flagged as potentially harmful or inappropriate. Please ensure that your messages are respectful and do not contain language or content that could be offensive or harmful to others. Thank you for your cooperation.</textarea>
                        </div>
                    <?php
                    else:
                        ?>
                        <div class="mb-5">
                            <label class="wpaicg-form-label">Enable:</label>
                            <input<?php echo isset($wpaicg_chat_widget['moderation']) && $wpaicg_chat_widget['moderation'] ? ' checked': ''?>  name="wpaicg_chat_widget[moderation]" value="1" type="checkbox">
                        </div>
                        <div class="mb-5">
                            <label class="wpaicg-form-label">Model:</label>
                            <select class="regular-text"  name="wpaicg_chat_widget[moderation_model]" >
                                <option<?php echo isset($wpaicg_chat_widget['moderation_model']) && $wpaicg_chat_widget['moderation_model'] == 'text-moderation-latest' ? ' selected':'';?> value="text-moderation-latest">text-moderation-latest</option>
                                <option<?php echo isset($wpaicg_chat_widget['moderation_model']) && $wpaicg_chat_widget['moderation_model'] == 'text-moderation-stable' ? ' selected':'';?> value="text-moderation-stable">text-moderation-stable</option>
                            </select>
                        </div>
                        <div class="mb-5">
                            <label class="wpaicg-form-label">Notice:</label>
                            <textarea rows="8" name="wpaicg_chat_widget[moderation_notice]"><?php echo isset($wpaicg_chat_widget['moderation_notice']) ? esc_html($wpaicg_chat_widget['moderation_notice']) : 'Your message has been flagged as potentially harmful or inappropriate. Please ensure that your messages are respectful and do not contain language or content that could be offensive or harmful to others. Thank you for your cooperation.'?></textarea>
                        </div>
                    <?php
                    endif;
                    ?>
                </div>
            </div>
            <!--Voice-->
            <div class="wpaicg-collapse">
                <div class="wpaicg-collapse-title"><span>+</span> Voice Input</div>
                <div class="wpaicg-collapse-content">
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Enable Voice Input:</label>
                        <input<?php echo $wpaicg_audio_enable ? ' checked':''?> value="1" class="wpaicg_chat_widget_audio" type="checkbox" name="wpaicg_chat_widget[audio_enable]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Mic Color:</label>
                        <input value="<?php echo esc_html($wpaicg_mic_color)?>" type="text" class="wpaicgchat_color wpaicg_chat_widget_mic_color" name="wpaicg_chat_widget[mic_color]" class="wpaicgchat_color">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Stop Color:</label>
                        <input value="<?php echo esc_html($wpaicg_stop_color)?>" type="text" name="wpaicg_chat_widget[stop_color]" class="wpaicgchat_color">
                    </div>
                </div>
            </div>
            <!--CustomText-->
            <div class="wpaicg-collapse">
                <div class="wpaicg-collapse-title"><span>+</span> Custom Text</div>
                <div class="wpaicg-collapse-content">
                    <div class="mb-5">
                        <label class="wpaicg-form-label">AI Name:</label>
                        <input type="text" class="regular-text" name="_wpaicg_chatbox_ai_name" value="<?php
                        echo  esc_html( get_option( '_wpaicg_chatbox_ai_name', 'AI' ) ) ;
                        ?>" >
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">You:</label>
                        <input type="text" class="regular-text" name="_wpaicg_chatbox_you" value="<?php
                        echo  esc_html( get_option( '_wpaicg_chatbox_you', 'You' ) ) ;
                        ?>" >
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">AI Thinking:</label>
                        <input type="text" class="regular-text" name="_wpaicg_ai_thinking" value="<?php
                        echo  esc_html( get_option( '_wpaicg_ai_thinking', 'AI thinking' ) ) ;
                        ?>" >
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Placeholder:</label>
                        <input type="text" class="regular-text" name="_wpaicg_typing_placeholder" value="<?php
                        echo  esc_html( get_option( '_wpaicg_typing_placeholder', 'Type a message' ) ) ;
                        ?>" >
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Welcome Message:</label>
                        <input type="text" class="regular-text" name="_wpaicg_chatbox_welcome_message" value="<?php
                        echo  esc_html( get_option( '_wpaicg_chatbox_welcome_message', 'Hello human, I am a GPT powered AI chat bot. Ask me anything!' ) ) ;
                        ?>" >
                    </div>
                    <div class="mb-5">
                        <?php $wpaicg_chat_no_answer = get_option('wpaicg_chat_no_answer','')?>
                        <label class="wpaicg-form-label">No Answer Message:</label>
                        <input class="regular-text" type="text" value="<?php echo esc_html($wpaicg_chat_no_answer)?>" name="wpaicg_chat_no_answer">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Footer Note:</label>
                        <input value="<?php echo esc_html($wpaicg_footer_text)?>" class="wpaicg-footer-note" type="text" name="wpaicg_chat_widget[footer_text]" placeholder="Powered by ...">
                    </div>
                </div>
            </div>
            <!--Context-->
            <div class="wpaicg-collapse">
                <div class="wpaicg-collapse-title"><span>+</span> Context</div>
                <div class="wpaicg-collapse-content">
                    <input value="<?php echo esc_html($wpaicg_chat_icon_url)?>" type="hidden" name="wpaicg_chat_widget[icon_url]" class="wpaicg_chat_icon_url">
                    <input value="<?php echo esc_html($wpaicg_ai_avatar_id)?>" type="hidden" name="wpaicg_chat_widget[ai_avatar_id]" class="wpaicg_ai_avatar_id">
                    <!-- wpaicg_chat_remember_conversation -->
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Remember Conversation:</label>
                        <select name="wpaicg_chat_widget[remember_conversation]">
                            <option<?php echo $wpaicg_chat_remember_conversation == 'yes' ? ' selected': ''?> value="yes">Yes</option>
                            <option<?php echo $wpaicg_chat_remember_conversation == 'no' ? ' selected': ''?> value="no">No</option>
                        </select>
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Remember Conv. Up To:</label>
                        <select name="wpaicg_conversation_cut">
                            <?php
                            for($i=3;$i<=20;$i++){
                                echo '<option'.($wpaicg_conversation_cut == $i ? ' selected':'').' value="'.esc_html($i).'">'.esc_html($i).'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">User Aware:</label>
                        <select name="wpaicg_chat_widget[user_aware]">
                            <option<?php echo $wpaicg_user_aware == 'no' ? ' selected': ''?> value="no">No</option>
                            <option<?php echo $wpaicg_user_aware == 'yes' ? ' selected': ''?> value="yes">Yes</option>
                        </select>
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Content Aware:</label>
                        <select name="wpaicg_chat_widget[content_aware]" id="wpaicg_chat_content_aware">
                            <option<?php echo $wpaicg_chat_content_aware == 'yes' ? ' selected': ''?> value="yes">Yes</option>
                            <option<?php echo $wpaicg_chat_content_aware == 'no' ? ' selected': ''?> value="no">No</option>
                        </select>
                    </div>
                    <?php

                    ?>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Use Excerpt:</label>
                        <input<?php echo !$wpaicg_chat_embedding && $wpaicg_chat_content_aware == 'yes' ? ' checked': ''?><?php echo $wpaicg_chat_content_aware == 'no' ? ' disabled':''?> type="checkbox" id="wpaicg_chat_excerpt" class="<?php echo $wpaicg_chat_embedding && $wpaicg_chat_content_aware == 'yes' ? 'asdisabled' : ''?>">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Use Embeddings:</label>
                        <input<?php echo $wpaicg_chat_embedding && $wpaicg_chat_content_aware == 'yes' ? ' checked': ''?><?php echo $wpaicg_embedding_field_disabled || $wpaicg_chat_content_aware == 'no' ? ' disabled':''?> type="checkbox" value="1" name="wpaicg_chat_embedding" id="wpaicg_chat_embedding" class="<?php echo !$wpaicg_chat_embedding && $wpaicg_chat_content_aware == 'yes' ? 'asdisabled' : ''?>">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Method:</label>
                        <select<?php echo $wpaicg_embedding_field_disabled || empty($wpaicg_chat_embedding) || $wpaicg_chat_content_aware == 'no' ? ' disabled':''?> name="wpaicg_chat_embedding_type" id="wpaicg_chat_embedding_type" class="<?php echo !$wpaicg_chat_embedding && $wpaicg_chat_content_aware == 'yes' ? 'asdisabled' : ''?>">
                            <option<?php echo $wpaicg_chat_embedding_type ? ' selected':'';?> value="openai">Embeddings + Completion</option>
                            <option<?php echo empty($wpaicg_chat_embedding_type) ? ' selected':''?> value="">Embeddings only</option>
                        </select>
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Nearest Answers:</label>
                        <select<?php echo $wpaicg_embedding_field_disabled || empty($wpaicg_chat_embedding) || $wpaicg_chat_content_aware == 'no' ? ' disabled':''?> name="wpaicg_chat_embedding_top" id="wpaicg_chat_embedding_top" class="<?php echo !$wpaicg_chat_embedding && $wpaicg_chat_content_aware == 'yes' ? 'asdisabled' : ''?>">
                            <?php
                            for($i = 1; $i <=5;$i++){
                                echo '<option'.($wpaicg_chat_embedding_top == $i ? ' selected':'').' value="'.esc_html($i).'">'.esc_html($i).'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Additional Context?:</label>
                        <input<?php echo $wpaicg_chat_content_aware == 'no' ? ' disabled':''?><?php echo $wpaicg_chat_addition == '1' ? ' checked': ''?> name="wpaicg_chat_addition" value="1" type="checkbox" id="wpaicg_chat_addition">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Context:</label>
                        <textarea<?php echo $wpaicg_chat_content_aware == 'no' || !$wpaicg_chat_addition ? ' disabled':''?> class="regular-text" id="wpaicg_chat_addition_text" name="wpaicg_chat_addition_text"><?php echo esc_html($wpaicg_chat_addition_text)?></textarea>
                    </div>
                </div>
            </div>
            <!--Logs-->
            <div class="wpaicg-collapse">
                <div class="wpaicg-collapse-title"><span>+</span> Logs</div>
                <div class="wpaicg-collapse-content">
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Save Chat Logs:</label>
                        <input<?php echo $wpaicg_save_logs ? ' checked':''?> class="wpaicg_chatbot_save_logs" value="1" type="checkbox" name="wpaicg_chat_widget[save_logs]">
                    </div>

                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label">Save Prompt:</label>
                        <input<?php echo $wpaicg_save_logs ? '': ' disabled'?><?php echo $wpaicg_save_logs && isset($wpaicg_chat_widget['log_request']) && $wpaicg_chat_widget['log_request'] ? ' checked' : ''?> class="wpaicg_chatbot_log_request" value="1" type="checkbox" name="wpaicg_chat_widget[log_request]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Display Notice:</label>
                        <input<?php echo $wpaicg_save_logs ? '': ' disabled'?><?php echo $wpaicg_log_notice ? ' checked':''?> class="wpaicg_chatbot_log_notice" value="1" type="checkbox" name="wpaicg_chat_widget[log_notice]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Notice Text:</label>
                        <input<?php echo $wpaicg_save_logs ? '': ' disabled'?> class="regular-text wpaicg_chatbot_log_notice_message" value="<?php echo esc_html($wpaicg_log_notice_message)?>" type="text" name="wpaicg_chat_widget[log_notice_message]">
                    </div>
                </div>
            </div>
            <!--Token Handing-->
            <div class="wpaicg-collapse mb-5">
                <div class="wpaicg-collapse-title">
                    <span>+</span>Token Handling
                </div>
                <div class="wpaicg-collapse-content">
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Limit Registered User:</label>
                        <input<?php echo $wpaicg_user_limited ? ' checked': ''?> class="wpaicg_user_token_limit" type="checkbox" value="1" name="wpaicg_chat_widget[user_limited]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Token Limit:</label>
                        <input<?php echo $wpaicg_user_limited ? '' : ' disabled'?> class="wpaicg_user_token_limit_text" style="width: 80px" type="text" value="<?php echo esc_html($wpaicg_user_tokens)?>" name="wpaicg_chat_widget[user_tokens]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Role based limit:</label>
                        <?php
                        foreach($wpaicg_roles as $key=>$wpaicg_role){
                            echo '<input class="wpaicg_role_'.esc_html($key).'" value="'.(isset($wpaicg_chat_widget['limited_roles'][$key]) && !empty($wpaicg_chat_widget['limited_roles'][$key]) ? esc_html($wpaicg_chat_widget['limited_roles'][$key]) : '').'" type="hidden" name="wpaicg_chat_widget[limited_roles]['.esc_html($key).']">';
                        }
                        ?>
                        <input<?php echo $wpaicg_user_limited ? '': (isset($wpaicg_chat_widget['role_limited']) && $wpaicg_chat_widget['role_limited'] ? ' checked':'')?> type="checkbox" value="1" class="wpaicg_role_limited" name="wpaicg_chat_widget[role_limited]">
                        <a href="javascript:void(0)" class="wpaicg_limit_set_role<?php echo $wpaicg_user_limited || !isset($wpaicg_chat_widget['role_limited']) || !$wpaicg_chat_widget['role_limited'] ? ' disabled': ''?>">Set Limit</a>
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Limit Non-Registered User:</label>
                        <input<?php echo $wpaicg_guest_limited ? ' checked': ''?> class="wpaicg_guest_token_limit" type="checkbox" value="1" name="wpaicg_chat_widget[guest_limited]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Token Limit:</label>
                        <input<?php echo $wpaicg_guest_limited ? '' : ' disabled'?> class="wpaicg_guest_token_limit_text" style="width: 80px" type="text" value="<?php echo esc_html($wpaicg_guest_tokens)?>" name="wpaicg_chat_widget[guest_tokens]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Notice:</label>
                        <input type="text" value="<?php echo esc_html($wpaicg_limited_message)?>" name="wpaicg_chat_widget[limited_message]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Reset Limit:</label>
                        <select name="wpaicg_chat_widget[reset_limit]">
                            <option<?php echo $wpaicg_reset_limit == 0 ? ' selected':''?> value="0">Never</option>
                            <option<?php echo $wpaicg_reset_limit == 1 ? ' selected':''?> value="1">1 Day</option>
                            <option<?php echo $wpaicg_reset_limit == 3 ? ' selected':''?> value="3">3 Days</option>
                            <option<?php echo $wpaicg_reset_limit == 7 ? ' selected':''?> value="7">1 Week</option>
                            <option<?php echo $wpaicg_reset_limit == 14 ? ' selected':''?> value="14">2 Weeks</option>
                            <option<?php echo $wpaicg_reset_limit == 30 ? ' selected':''?> value="30">1 Month</option>
                            <option<?php echo $wpaicg_reset_limit == 60 ? ' selected':''?> value="60">2 Months</option>
                            <option<?php echo $wpaicg_reset_limit == 90 ? ' selected':''?> value="90">3 Months</option>
                            <option<?php echo $wpaicg_reset_limit == 180 ? ' selected':''?> value="180">6 Months</option>
                        </select>
                    </div>
                </div>
            </div>
            <button class="button button-primary" name="wpaicg_submit" style="width: 100%">Save</button>
        </form>
    </div>
</div>
<script>
    jQuery(document).ready(function ($){
        let wpaicg_roles = <?php echo wp_kses_post(json_encode($wpaicg_roles))?>;
        $('.wpaicg_modal_close_second').click(function (){
            $('.wpaicg_modal_close_second').closest('.wpaicg_modal_second').hide();
            $('.wpaicg-overlay-second').hide();
        });
        $(document).on('click', '.wpaicg_chatbot_save_logs', function(e){
            if($(e.currentTarget).prop('checked')){
                $('.wpaicg_chatbot_log_request').removeAttr('disabled');
                $('.wpaicg_chatbot_log_notice').removeAttr('disabled');
                $('.wpaicg_chatbot_log_notice_message').removeAttr('disabled');
            }
            else{
                $('.wpaicg_chatbot_log_request').attr('disabled','disabled');
                $('.wpaicg_chatbot_log_request').prop('checked',false);
                $('.wpaicg_chatbot_log_notice').attr('disabled','disabled');
                $('.wpaicg_chatbot_log_notice').prop('checked',false);
                $('.wpaicg_chatbot_log_notice_message').attr('disabled','disabled');
            }
        });
        $(document).on('keypress','.wpaicg_user_token_limit_text,.wpaicg_update_role_limit,.wpaicg_guest_token_limit_text', function (e){
            var charCode = (e.which) ? e.which : e.keyCode;
            if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode !== 46) {
                return false;
            }
            return true;
        });
        $('.wpaicg_limit_set_role').click(function (){
            if(!$(this).hasClass('disabled')) {
                if ($('.wpaicg_role_limited').prop('checked')) {
                    let html = '';
                    $.each(wpaicg_roles, function (key, role) {
                        let valueRole = $('.wpaicg_role_'+key).val();
                        html += '<div style="padding: 5px;display: flex;justify-content: space-between;align-items: center;"><label><strong>'+role+'</strong></label><input class="wpaicg_update_role_limit" data-target="'+key+'" value="'+valueRole+'" placeholder="Empty for no-limit" type="text"></div>';
                    });
                    html += '<div style="padding: 5px"><button class="button button-primary wpaicg_save_role_limit" style="width: 100%;margin: 5px 0;">Save</button></div>';
                    $('.wpaicg_modal_title_second').html('Role Limit');
                    $('.wpaicg_modal_content_second').html(html);
                    $('.wpaicg-overlay-second').css('display','flex');
                    $('.wpaicg_modal_second').show();

                } else {
                    $.each(wpaicg_roles, function (key, role) {
                        $('.wpaicg_role_' + key).val('');
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
        $('.wpaicg_guest_token_limit').click(function (){
            if($(this).prop('checked')){
                $('.wpaicg_guest_token_limit_text').removeAttr('disabled');
            }
            else{
                $('.wpaicg_guest_token_limit_text').val('');
                $('.wpaicg_guest_token_limit_text').attr('disabled','disabled');
            }
        });
        $('.wpaicg_role_limited').click(function (){
            if($(this).prop('checked')){
                $('.wpaicg_user_token_limit').prop('checked',false);
                $('.wpaicg_user_token_limit_text').attr('disabled','disabled');
                $('.wpaicg_limit_set_role').removeClass('disabled');
            }
            else{
                $('.wpaicg_limit_set_role').addClass('disabled');
            }
        });
        $('.wpaicg_user_token_limit').click(function (){
            if($(this).prop('checked')){
                $('.wpaicg_user_token_limit_text').removeAttr('disabled');
                $('.wpaicg_role_limited').prop('checked',false);
                $('.wpaicg_limit_set_role').addClass('disabled');
            }
            else{
                $('.wpaicg_user_token_limit_text').val('');
                $('.wpaicg_user_token_limit_text').attr('disabled','disabled');
            }
        });
        $('.wpaicg-chatbox-preview-box > .wpaicg_chat_widget').addClass('wpaicg_widget_open');
        $('.wpaicg-chatbox-preview-box .wpaicg_toggle').addClass('wpaicg_widget_open');
        function wpaicgChangeAvatarRealtime(){
            var wpaicg_user_avatar_check = $('input[name=_wpaicg_chatbox_you]').val()+':';
            var wpaicg_ai_avatar_check = $('input[name=_wpaicg_chatbox_ai_name]').val()+':';
            if($('.wpaicgchat_use_avatar').prop('checked')){
                wpaicg_user_avatar_check = '<img src="<?php echo get_avatar_url(get_current_user_id())?>" height="40" width="40">';
                wpaicg_ai_avatar_check = '<?php echo esc_html(WPAICG_PLUGIN_URL) . 'admin/images/chatbot.png';?>';
                if($('.wpaicg_chatbox_avatar_custom').prop('checked') && $('.wpaicg_chatbox_avatar img').length){
                    wpaicg_ai_avatar_check = $('.wpaicg_chatbox_avatar img').attr('src');
                }
                wpaicg_ai_avatar_check = '<img src="'+wpaicg_ai_avatar_check+'" height="40" width="40">';
            }

            $('.wpaicg-chat-ai-message').each(function (idx, item){
                $(item).find('strong').html(wpaicg_ai_avatar_check);
            });
            $('.wpaicg-chat-user-message').each(function (idx, item){
                $(item).find('strong').html(wpaicg_user_avatar_check);
            });
        }
        $('input[name=_wpaicg_chatbox_you],input[name=_wpaicg_chatbox_ai_name]').on('input', function (){
            wpaicgChangeAvatarRealtime();
        })
        $('.wpaicgchat_use_avatar,.wpaicg_chatbox_avatar_default,.wpaicg_chatbox_avatar_custom').on('click', function (){
            wpaicgChangeAvatarRealtime();
        })
        function wpaicgUpdateRealtime(){
            let fontsize = $('.wpaicg_chat_widget_font_size').val();
            let fontcolor = $('.wpaicgchat_font_color').iris('color');
            let bgcolor = $('.wpaicgchat_bg_color').iris('color');
            let inputbg = $('.wpaicgchat_input_color').iris('color');
            let inputborder = $('.wpaicgchat_input_border').iris('color');
            let buttoncolor = $('.wpaicgchat_send_color').iris('color');
            let userbg = $('.wpaicgchat_user_color').iris('color');
            let aibg = $('.wpaicgchat_ai_color').iris('color');
            let useavatar = $('.wpaicgchat_use_avatar').val();
            let width = $('.wpaicg_chat_widget_width').val();
            let height = $('.wpaicg_chat_widget_height').val();
            let mic_color = $('.wpaicg_chat_widget_mic_color').iris('color');
            $('.wpaicg-mic-icon').css('color', mic_color);
            let footernote = $('.wpaicg-footer-note').val();
            let footerheight = 0;

            if(footernote === ''){
                footerheight = 18;
                $('.wpaicg-chatbox-footer').hide();
                $('.wpaicg-chatbox-type').css('padding','5px');
            }
            else{
                $('.wpaicg-chatbox-type').css('padding','5px 5px 0 5px');
                $('.wpaicg-chatbox-footer').show();
                $('.wpaicg-chatbox-footer').html(footernote);
            }
            if($('.wpaicg_chat_widget_audio').prop('checked')){
                $('.wpaicg-mic-icon').show();
            }
            else{
                $('.wpaicg-mic-icon').hide();
            }
            $('.wpaicg-chatbox-messages li.wpaicg-chat-ai-message').css({
                'font-size': fontsize+'px',
                'color': fontcolor,
                'background-color': aibg
            })
            $('.wpaicg-chatbox-messages li.wpaicg-chat-user-message').css({
                'font-size': fontsize+'px',
                'color': fontcolor,
                'background-color': userbg
            });
            $('.wpaicg_chat_widget_content .wpaicg-chatbox-content ul,.wpaicg_chat_widget_content .wpaicg-chatbox').css({
                'background-color': bgcolor
            });
            $('.wpaicg-chatbox-typing').css({
                'border-color': inputborder,
                'background-color': inputbg
            });
            $('.wpaicg-chatbox-send').css('color',buttoncolor);
            if(width === '' || parseInt(width) === 0){
                width = 350;
            }
            if(height === '' || parseInt(height) === 0){
                height = 400;
            }
            $('.wpaicg-chatbox-preview-box').height((parseInt(height)+100)+'px');
            $('.wpaicg_chat_widget_content .wpaicg-chatbox,.wpaicg_widget_open .wpaicg_chat_widget_content').css({
                'height': height+'px',
                'width': width+'px',
            });
            $('.wpaicg_chat_widget_content .wpaicg-chatbox-content').css({
                'height': (height - 58 + footerheight)+'px'
            });
            $('.wpaicg_chat_widget_content .wpaicg-chatbox-content ul').css({
                'height': (height - 82 + footerheight)+'px'
            });
        }
        $('.wpaicg_chat_widget_font_size,.wpaicg_chat_widget_width,.wpaicg_chat_widget_height').on('input', function(){
            wpaicgUpdateRealtime();
        });
        $('.wpaicg_chat_widget_audio,.wpaicgchat_use_avatar').click(function(){
            wpaicgUpdateRealtime();
        })
        $('.wpaicgchat_color').wpColorPicker({
            change: function (event, ui){
                wpaicgUpdateRealtime();
            },
            clear: function(event){
                wpaicgUpdateRealtime();
            }
        });
        $('.wpaicg-footer-note').on('input', function(){
            wpaicgUpdateRealtime();
        })
        $('.wpaicg_chatbox_icon').click(function (e){
            e.preventDefault();
            $('.wpaicg_chatbox_icon_default').prop('checked',false);
            $('.wpaicg_chatbox_icon_custom').prop('checked',true);
            var button = $(e.currentTarget),
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
                    $('.wpaicg_chat_icon_url').val(attachment.id);
                }).open();
        });
        $('.wpaicg_chatbox_avatar').click(function (e){
            e.preventDefault();
            $('.wpaicg_chatbox_avatar_default').prop('checked',false);
            $('.wpaicg_chatbox_avatar_custom').prop('checked',true);
            var button = $(e.currentTarget),
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
                    $('.wpaicg_ai_avatar_id').val(attachment.id);
                }).open();
        });
        $('.wpaicg-collapse-title').click(function (){
            if(!$(this).hasClass('wpaicg-collapse-active')){
                $('.wpaicg-collapse').removeClass('wpaicg-collapse-active');
                $('.wpaicg-collapse-title span').html('+');
                $(this).find('span').html('-');
                $(this).parent().addClass('wpaicg-collapse-active');
            }
        });
        $('#wpaicg_chat_excerpt').on('click', function (){
            if($(this).prop('checked')){
                $('#wpaicg_chat_excerpt').removeClass('asdisabled');
                $('#wpaicg_chat_embedding').prop('checked',false);
                $('#wpaicg_chat_embedding').addClass('asdisabled');
                $('#wpaicg_chat_embedding_type').val('openai');
                $('#wpaicg_chat_embedding_type').addClass('asdisabled');
                $('#wpaicg_chat_embedding_type').attr('disabled','disabled');
                $('#wpaicg_chat_embedding_top').attr('disabled','disabled');
                $('#wpaicg_chat_embedding_top').val(1);
            }
            else{
                $(this).prop('checked',true);
            }
        });
        $('#wpaicg_chat_addition').on('click', function (){
            if($(this).prop('checked')){
                $('#wpaicg_chat_addition_text').removeAttr('disabled');
            }
            else{
                $('#wpaicg_chat_addition_text').attr('disabled','disabled');
            }
        });
        $('#wpaicg_chat_embedding').on('click', function (){
            if($(this).prop('checked')){
                $('#wpaicg_chat_excerpt').prop('checked',false);
                $('#wpaicg_chat_excerpt').addClass('asdisabled');
                $('#wpaicg_chat_addition').prop('checked',false);
                $('#wpaicg_chat_embedding').removeClass('asdisabled');
                $('#wpaicg_chat_embedding_type').val('openai');
                $('#wpaicg_chat_embedding_type').removeClass('asdisabled');
                $('#wpaicg_chat_embedding_type').removeAttr('disabled');
                $('#wpaicg_chat_embedding_top').val(1);
                $('#wpaicg_chat_embedding_top').removeClass('asdisabled');
                $('#wpaicg_chat_embedding_top').removeAttr('disabled');
            }
            else{
                $(this).prop('checked',true);
            }
        });
        <?php
        if(!$wpaicg_embedding_field_disabled):
        ?>
        $('#wpaicg_chat_content_aware').on('change', function (){
            if($(this).val() === 'yes'){
                $('#wpaicg_chat_excerpt').removeAttr('disabled');
                $('#wpaicg_chat_excerpt').prop('checked',true);
                $('#wpaicg_chat_embedding').removeAttr('disabled');
                $('#wpaicg_chat_embedding_type').removeAttr('disabled');
                $('#wpaicg_chat_embedding').addClass('asdisabled');
                $('#wpaicg_chat_embedding_type').val('openai');
                $('#wpaicg_chat_embedding_type').addClass('asdisabled');
                $('#wpaicg_chat_embedding_top').val(1);
                $('#wpaicg_chat_embedding_top').addClass('asdisabled');
                $('#wpaicg_chat_addition').removeClass('asdisabled');
                $('#wpaicg_chat_addition').removeAttr('disabled');
                $('#wpaicg_chat_addition_text').attr('disabled','disabled');
            }
            else{
                $('#wpaicg_chat_embedding_type').removeClass('asdisabled');
                $('#wpaicg_chat_excerpt').removeClass('asdisabled');
                $('#wpaicg_chat_embedding').removeClass('asdisabled');
                $('#wpaicg_chat_excerpt').prop('checked',false);
                $('#wpaicg_chat_embedding').prop('checked',false);
                $('#wpaicg_chat_excerpt').attr('disabled','disabled');
                $('#wpaicg_chat_embedding').attr('disabled','disabled');
                $('#wpaicg_chat_embedding_type').attr('disabled','disabled');
                $('#wpaicg_chat_embedding_top').attr('disabled','disabled');
                $('#wpaicg_chat_embedding_top').removeClass('asdisabled');
                $('#wpaicg_chat_addition').attr('disabled','disabled');
                $('#wpaicg_chat_addition_text').attr('disabled','disabled');
            }
        })
        <?php
        else:
        ?>
        $('#wpaicg_chat_content_aware').on('change', function (){
            if($(this).val() === 'yes'){
                $('#wpaicg_chat_excerpt').removeAttr('disabled');
                $('#wpaicg_chat_excerpt').prop('checked',true);
            }
            else{
                $('#wpaicg_chat_excerpt').removeClass('asdisabled');
                $('#wpaicg_chat_excerpt').prop('checked',false);
                $('#wpaicg_chat_excerpt').attr('disabled','disabled');
            }
        })
        <?php
        endif;
        ?>
    })
</script>

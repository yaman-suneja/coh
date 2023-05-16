<?php
if ( ! defined( 'ABSPATH' ) ) exit;
wp_enqueue_script('wp-color-picker');
wp_enqueue_style('wp-color-picker');
global  $wpdb ;
$table = $wpdb->prefix . 'wpaicg';
$wpaicg_save_setting_success = false;
if(isset($_POST['wpaicg_chat_shortcode_options']) && is_array($_POST['wpaicg_chat_shortcode_options'])){
    check_admin_referer('wpaicg_chat_shortcode_save');
    $wpaicg_chat_shortcode_options = \WPAICG\wpaicg_util_core()->sanitize_text_or_array_field($_POST['wpaicg_chat_shortcode_options']);
    update_option('wpaicg_chat_shortcode_options',$wpaicg_chat_shortcode_options);
    $wpaicg_save_setting_success = 'Setting saved successfully';
}
$existingValue = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE name = %s", 'wpaicg_settings' ), ARRAY_A );
$wpaicg_chat_shortcode_options = get_option('wpaicg_chat_shortcode_options',[]);
$default_setting = array(
    'language' => 'en',
    'tone' => 'friendly',
    'profession' => 'none',
    'model' => 'text-davinci-003',
    'temperature' => $existingValue['temperature'],
    'max_tokens' => $existingValue['max_tokens'],
    'top_p' => $existingValue['top_p'],
    'best_of' => $existingValue['best_of'],
    'frequency_penalty' => $existingValue['frequency_penalty'],
    'presence_penalty' => $existingValue['presence_penalty'],
    'ai_name' => 'AI',
    'you' => 'You',
    'ai_thinking' => 'AI Thinking',
    'placeholder' => 'Type a message',
    'welcome' => 'Hello human, I am a GPT powered AI chat bot. Ask me anything!',
    'remember_conversation' => 'yes',
    'conversation_cut' => 10,
    'content_aware' => 'yes',
    'embedding' =>  false,
    'embedding_type' =>  false,
    'embedding_top' =>  false,
    'no_answer' => '',
    'fontsize' => 13,
    'fontcolor' => '#fff',
    'user_bg_color' => '#444654',
    'ai_bg_color' => '#343541',
    'ai_icon_url' => '',
    'ai_icon' => 'default',
    'use_avatar' => false,
    'width' => '100%',
    'height' => '445px',
    'save_logs' => false,
    'log_notice' => false,
    'log_notice_message' => 'Please note that your conversations will be recorded.',
    'bgcolor' => '#222',
    'bg_text_field' => '#fff',
    'send_color' => '#fff',
    'border_text_field' => '#ccc',
    'footer_text' => '',
    'chat_addition' => false,
    'chat_addition_text' => '',
    'audio_enable' => false,
    'mic_color' => '#222',
    'stop_color' => '#f00',
    'user_aware' => 'no',
    'user_limited' => false,
    'guest_limited' => false,
    'user_tokens' => 0,
    'guest_tokens' => 0,
    'reset_limit' => 0,
    'limited_message' => 'You have reached your token limit.',
    'moderation' => false,
    'moderation_model' => 'text-moderation-latest',
    'moderation_notice' => 'Your message has been flagged as potentially harmful or inappropriate. Please ensure that your messages are respectful and do not contain language or content that could be offensive or harmful to others. Thank you for your cooperation.',
    'role_limited' => false,
    'limited_roles' => [],
    'log_request' => false
);
$wpaicg_pinecone_api = get_option('wpaicg_pinecone_api','');
$wpaicg_pinecone_environment = get_option('wpaicg_pinecone_environment','');
$wpaicg_settings = shortcode_atts($default_setting, $wpaicg_chat_shortcode_options);
$wpaicg_custom_models = get_option('wpaicg_custom_models',array());
$wpaicg_custom_models = array_merge(array('text-davinci-003','text-curie-001','text-babbage-001','text-ada-001'),$wpaicg_custom_models);
$wpaicg_embedding_field_disabled = empty($wpaicg_pinecone_api) || empty($wpaicg_pinecone_environment) ? true : false;
$wpaicg_save_logs = isset($wpaicg_settings['save_logs']) && !empty($wpaicg_settings['save_logs']) ? $wpaicg_settings['save_logs'] : false;
$wpaicg_log_notice = isset($wpaicg_settings['log_notice']) && !empty($wpaicg_settings['log_notice']) ? $wpaicg_settings['log_notice'] : false;
$wpaicg_log_notice_message = isset($wpaicg_settings['log_notice_message']) && !empty($wpaicg_settings['log_notice_message']) ? $wpaicg_settings['log_notice_message'] : 'Please note that your conversations will be recorded.';
$wpaicg_user_limited = isset($wpaicg_settings['user_limited']) ? $wpaicg_settings['user_limited'] : false;
$wpaicg_guest_limited = isset($wpaicg_settings['guest_limited']) ? $wpaicg_settings['guest_limited'] : false;
$wpaicg_user_tokens = isset($wpaicg_settings['user_tokens']) ? $wpaicg_settings['user_tokens'] : 0;
$wpaicg_guest_tokens = isset($wpaicg_settings['guest_tokens']) ? $wpaicg_settings['guest_tokens'] : 0;
$wpaicg_reset_limit = isset($wpaicg_settings['reset_limit']) ? $wpaicg_settings['reset_limit'] : 0;
$wpaicg_limited_message = isset($wpaicg_settings['limited_message']) && !empty($wpaicg_settings['limited_message']) ? $wpaicg_settings['limited_message'] : 'You have reached your token limit.';
$wpaicg_roles = wp_roles()->get_names();
?>
<style>
    .asdisabled{
        background: #ebebeb!important;
    }
    .wp-picker-holder {
        position: absolute;
    }
    .wpaicg-collapse-content input.wp-color-picker[type=text]{
        width: 4rem!important;
    }
    .wpaicg-chat-shortcode{
        background-color: <?php echo esc_html($wpaicg_settings['bgcolor'])?>;
    }
    .wpaicg-chat-shortcode-type{
        background: rgb(0 0 0 / 19%);
    }
    .wpaicg-chat-shortcode-typing{
        background-color: <?php echo esc_html($wpaicg_settings['bg_text_field'])?>;
        border-color: <?php echo esc_html($wpaicg_settings['border_text_field'])?>;
    }
    .wpaicg-chat-shortcode-send{
        color: <?php echo esc_html($wpaicg_settings['send_color'])?>;
    }
    .wpaicg-collapse-content textarea{
        display: inline-block!important;
        width: 48%!important;
    }
    .wpaicg-collapse-content .wpaicg-form-label{
        vertical-align: top;
    }
</style>
<?php
if($wpaicg_save_setting_success):
    ?>
    <div class="notice notice-success">
        <p><?php echo esc_html($wpaicg_save_setting_success);?></p>
    </div>
<?php
endif;
?>
<div class="wpaicg-alert mb-5">
    <p>To add the chat bot to your website, please include the shortcode <code>[wpaicg_chatgpt]</code> in the desired location on your site.</p>
    <p>If you prefer to use widget instead of shortcode, go to <b>Widget</b> tab and configure it. Learn how you can train the chat bot with your content <u><b><a href="https://youtu.be/NPMLGwFQYrY" target="_blank">here</a></u></b>.</p>
</div>
<div class="wpaicg-grid-three">
    <div class="wpaicg-grid-2 wpaicg-chat-shortcode-preview">
        <?php
        echo do_shortcode('[wpaicg_chatgpt]');
        ?>
    </div>
    <div class="wpaicg-grid-1">
        <form action="" method="post" id="form-chatbox-setting">
            <?php
            wp_nonce_field('wpaicg_chat_shortcode_save');
            ?>
            <div class="wpaicg-collapse wpaicg-collapse-active">
                <div class="wpaicg-collapse-title"><span>-</span> Language, Tone and Profession</div>
                <div class="wpaicg-collapse-content">
                    <div class="mb-5">
                        <label class="wpaicg-form-label" for="label_title"><?php
                            echo  esc_html( __( "Language", "wp-ai-content-generator" ) ) ;
                            ?>
                        </label>
                        <select class="wpaicg-input" id="label_wpai_language" name="wpaicg_chat_shortcode_options[language]" >
                            <option value="en" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'en' ? 'selected' : '' ) ;
                            ?>>English</option>
                            <option value="af" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'af' ? 'selected' : '' ) ;
                            ?>>Afrikaans</option>
                            <option value="ar" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'ar' ? 'selected' : '' ) ;
                            ?>>Arabic</option>
                            <option value="bg" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'bg' ? 'selected' : '' ) ;
                            ?>>Bulgarian</option>
                            <option value="zh" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'zh' ? 'selected' : '' ) ;
                            ?>>Chinese</option>
                            <option value="hr" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'hr' ? 'selected' : '' ) ;
                            ?>>Croatian</option>
                            <option value="cs" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'cs' ? 'selected' : '' ) ;
                            ?>>Czech</option>
                            <option value="da" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'da' ? 'selected' : '' ) ;
                            ?>>Danish</option>
                            <option value="nl" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'nl' ? 'selected' : '' ) ;
                            ?>>Dutch</option>
                            <option value="et" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'et' ? 'selected' : '' ) ;
                            ?>>Estonian</option>
                            <option value="fil" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'fil' ? 'selected' : '' ) ;
                            ?>>Filipino</option>
                            <option value="fi" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'fi' ? 'selected' : '' ) ;
                            ?>>Finnish</option>
                            <option value="fr" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'fr' ? 'selected' : '' ) ;
                            ?>>French</option>
                            <option value="de" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'de' ? 'selected' : '' ) ;
                            ?>>German</option>
                            <option value="el" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'el' ? 'selected' : '' ) ;
                            ?>>Greek</option>
                            <option value="he" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'he' ? 'selected' : '' ) ;
                            ?>>Hebrew</option>
                            <option value="hi" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'hi' ? 'selected' : '' ) ;
                            ?>>Hindi</option>
                            <option value="hu" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'hu' ? 'selected' : '' ) ;
                            ?>>Hungarian</option>
                            <option value="id" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'id' ? 'selected' : '' ) ;
                            ?>>Indonesian</option>
                            <option value="it" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'it' ? 'selected' : '' ) ;
                            ?>>Italian</option>
                            <option value="ja" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'ja' ? 'selected' : '' ) ;
                            ?>>Japanese</option>
                            <option value="ko" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'ko' ? 'selected' : '' ) ;
                            ?>>Korean</option>
                            <option value="lv" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'lv' ? 'selected' : '' ) ;
                            ?>>Latvian</option>
                            <option value="lt" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'lt' ? 'selected' : '' ) ;
                            ?>>Lithuanian</option>
                            <option value="ms" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'ms' ? 'selected' : '' ) ;
                            ?>>Malay</option>
                            <option value="no" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'no' ? 'selected' : '' ) ;
                            ?>>Norwegian</option>
                            <option value="pl" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'pl' ? 'selected' : '' ) ;
                            ?>>Polish</option>
                            <option value="pt" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'pt' ? 'selected' : '' ) ;
                            ?>>Portuguese</option>
                            <option value="ro" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'ro' ? 'selected' : '' ) ;
                            ?>>Romanian</option>
                            <option value="ru" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'ru' ? 'selected' : '' ) ;
                            ?>>Russian</option>
                            <option value="sr" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'sr' ? 'selected' : '' ) ;
                            ?>>Serbian</option>
                            <option value="sk" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'sk' ? 'selected' : '' ) ;
                            ?>>Slovak</option>
                            <option value="sl" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'sl' ? 'selected' : '' ) ;
                            ?>>Slovenian</option>
                            <option value="sv" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'sv' ? 'selected' : '' ) ;
                            ?>>Swedish</option>
                            <option value="es" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'es' ? 'selected' : '' ) ;
                            ?>>Spanish</option>
                            <option value="th" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'th' ? 'selected' : '' ) ;
                            ?>>Thai</option>
                            <option value="tr" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'tr' ? 'selected' : '' ) ;
                            ?>>Turkish</option>
                            <option value="uk" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'uk' ? 'selected' : '' ) ;
                            ?>>Ukrainian</option>
                            <option value="vi" <?php
                            echo  ( esc_html( $wpaicg_settings['language'] ) == 'vi' ? 'selected' : '' ) ;
                            ?>>Vietnamese</option>
                        </select>
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Tone</label>
                        <select name="wpaicg_chat_shortcode_options[tone]">
                            <option<?php echo $wpaicg_settings['tone'] == 'friendly' ? ' selected': ''?> value="friendly">Friendly</option>
                            <option<?php echo $wpaicg_settings['tone'] == 'professional' ? ' selected': ''?> value="professional">Professional</option>
                            <option<?php echo $wpaicg_settings['tone'] == 'sarcastic' ? ' selected': ''?> value="sarcastic">Sarcastic</option>
                            <option<?php echo $wpaicg_settings['tone'] == 'humorous' ? ' selected': ''?> value="humorous">Humorous</option>
                            <option<?php echo $wpaicg_settings['tone'] == 'cheerful' ? ' selected': ''?> value="cheerful">Cheerful</option>
                            <option<?php echo $wpaicg_settings['tone'] == 'anecdotal' ? ' selected': ''?> value="anecdotal">Anecdotal</option>
                        </select>
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Act As</label>
                        <select name="wpaicg_chat_shortcode_options[profession]">
                            <option<?php echo $wpaicg_settings['profession'] == 'none' ? ' selected': ''?> value="none">None</option>
                            <option<?php echo $wpaicg_settings['profession'] == 'accountant' ? ' selected': ''?> value="accountant">Accountant</option>
                            <option<?php echo $wpaicg_settings['profession'] == 'advertisingspecialist' ? ' selected': ''?> value="advertisingspecialist">Advertising Specialist</option>
                            <option<?php echo $wpaicg_settings['profession'] == 'architect' ? ' selected': ''?> value="architect">Architect</option>
                            <option<?php echo $wpaicg_settings['profession'] == 'artist' ? ' selected': ''?> value="artist">Artist</option>
                            <option<?php echo $wpaicg_settings['profession'] == 'blogger' ? ' selected': ''?> value="blogger">Blogger</option>
                            <option<?php echo $wpaicg_settings['profession'] == 'businessanalyst' ? ' selected': ''?> value="businessanalyst">Business Analyst</option>
                            <option<?php echo $wpaicg_settings['profession'] == 'businessowner' ? ' selected': ''?> value="businessowner">Business Owner</option>
                            <option<?php echo $wpaicg_settings['profession'] == 'carexpert' ? ' selected': ''?> value="carexpert">Car Expert</option>
                            <option<?php echo $wpaicg_settings['profession'] == 'consultant' ? ' selected': ''?> value="consultant">Consultant</option>
                            <option<?php echo $wpaicg_settings['profession'] == 'counselor' ? ' selected': ''?> value="counselor">Counselor</option>
                            <option<?php echo $wpaicg_settings['profession'] == 'cryptocurrencytrader' ? ' selected': ''?> value="cryptocurrencytrader">Cryptocurrency Trader</option>
                            <option<?php echo $wpaicg_settings['profession'] == 'cryptocurrencyexpert' ? ' selected': ''?> value="cryptocurrencyexpert">Cryptocurrency Expert</option>
                            <option<?php echo $wpaicg_settings['profession'] == 'customersupport' ? ' selected': ''?> value="customersupport">Customer Support</option>
                            <option<?php echo $wpaicg_settings['profession'] == 'designer' ? ' selected': ''?> value="designer">Designer</option>
                            <option<?php echo $wpaicg_settings['profession'] == 'digitalmarketinagency' ? ' selected': ''?> value="digitalmarketinagency">Digital Marketing Agency</option>
                            <option<?php echo $wpaicg_settings['profession'] == 'editor' ? ' selected': ''?> value="editor">Editor</option>
                            <option<?php echo $wpaicg_settings['profession'] == 'engineer' ? ' selected': ''?> value="engineer">Engineer</option>
                            <option<?php echo $wpaicg_settings['profession'] == 'eventplanner' ? ' selected': ''?> value="eventplanner">Event Planner</option>
                            <option<?php echo $wpaicg_settings['profession'] == 'freelancer' ? ' selected': ''?> value="freelancer">Freelancer</option>
                            <option<?php echo $wpaicg_settings['profession'] == 'insuranceagent' ? ' selected': ''?> value="insuranceagent">Insurance Agent</option>
                            <option<?php echo $wpaicg_settings['profession'] == 'insurancebroker' ? ' selected': ''?> value="insurancebroker">Insurance Broker</option>
                            <option<?php echo $wpaicg_settings['profession'] == 'interiordesigner' ? ' selected': ''?> value="interiordesigner">Interior Designer</option>
                            <option<?php echo $wpaicg_settings['profession'] == 'journalist' ? ' selected': ''?> value="journalist">Journalist</option>
                            <option<?php echo $wpaicg_settings['profession'] == 'marketingagency' ? ' selected': ''?> value="marketingagency">Marketing Agency</option>
                            <option<?php echo $wpaicg_settings['profession'] == 'marketingexpert' ? ' selected': ''?> value="marketingexpert">Marketing Expert</option>
                            <option<?php echo $wpaicg_settings['profession'] == 'marketingspecialist' ? ' selected': ''?> value="marketingspecialist">Marketing Specialist</option>
                            <option<?php echo $wpaicg_settings['profession'] == 'photographer' ? ' selected': ''?> value="photographer">Photographer</option>
                            <option<?php echo $wpaicg_settings['profession'] == 'programmer' ? ' selected': ''?> value="programmer">Programmer</option>
                            <option<?php echo $wpaicg_settings['profession'] == 'publicrelationsagency' ? ' selected': ''?> value="publicrelationsagency">Public Relations Agency</option>
                            <option<?php echo $wpaicg_settings['profession'] == 'publisher' ? ' selected': ''?> value="publisher">Publisher</option>
                            <option<?php echo $wpaicg_settings['profession'] == 'realestateagent' ? ' selected': ''?> value="realestateagent">Real Estate Agent</option>
                            <option<?php echo $wpaicg_settings['profession'] == 'recruiter' ? ' selected': ''?> value="recruiter">Recruiter</option>
                            <option<?php echo $wpaicg_settings['profession'] == 'reporter' ? ' selected': ''?> value="reporter">Reporter</option>
                            <option<?php echo $wpaicg_settings['profession'] == 'salesperson' ? ' selected': ''?> value="salesperson">Sales Person</option>
                            <option<?php echo $wpaicg_settings['profession'] == 'salerep' ? ' selected': ''?> value="salerep">Sales Representative</option>
                            <option<?php echo $wpaicg_settings['profession'] == 'seoagency' ? ' selected': ''?> value="seoagency">SEO Agency</option>
                            <option<?php echo $wpaicg_settings['profession'] == 'seoexpert' ? ' selected': ''?> value="seoexpert">SEO Expert</option>
                            <option<?php echo $wpaicg_settings['profession'] == 'socialmediaagency' ? ' selected': ''?> value="socialmediaagency">Social Media Agency</option>
                            <option<?php echo $wpaicg_settings['profession'] == 'student' ? ' selected': ''?> value="student">Student</option>
                            <option<?php echo $wpaicg_settings['profession'] == 'teacher' ? ' selected': ''?> value="teacher">Teacher</option>
                            <option<?php echo $wpaicg_settings['profession'] == 'technicalsupport' ? ' selected': ''?> value="technicalsupport">Technical Support</option>
                            <option<?php echo $wpaicg_settings['profession'] == 'trainer' ? ' selected': ''?> value="trainer">Trainer</option>
                            <option<?php echo $wpaicg_settings['profession'] == 'travelagency' ? ' selected': ''?> value="travelagency">Travel Agency</option>
                            <option<?php echo $wpaicg_settings['profession'] == 'videographer' ? ' selected': ''?> value="videographer">Videographer</option>
                            <option<?php echo $wpaicg_settings['profession'] == 'webdesignagency' ? ' selected': ''?> value="webdesignagency">Web Design Agency</option>
                            <option<?php echo $wpaicg_settings['profession'] == 'webdesignexpert' ? ' selected': ''?> value="webdesignexpert">Web Design Expert</option>
                            <option<?php echo $wpaicg_settings['profession'] == 'webdevelopmentagency' ? ' selected': ''?> value="webdevelopmentagency">Web Development Agency</option>
                            <option<?php echo $wpaicg_settings['profession'] == 'webdevelopmentexpert' ? ' selected': ''?> value="webdevelopmentexpert">Web Development Expert</option>
                            <option<?php echo $wpaicg_settings['profession'] == 'webdesigner' ? ' selected': ''?> value="webdesigner">Web Designer</option>
                            <option<?php echo $wpaicg_settings['profession'] == 'webdeveloper' ? ' selected': ''?> value="webdeveloper">Web Developer</option>
                            <option<?php echo $wpaicg_settings['profession'] == 'writer' ? ' selected': ''?> value="writer">Writer</option>
                        </select>
                    </div>
                </div>
            </div>
            <!--Style-->
            <div class="wpaicg-collapse">
                <div class="wpaicg-collapse-title"><span>+</span>Style</div>
                <div class="wpaicg-collapse-content">
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Font Size:</label>
                        <select name="wpaicg_chat_shortcode_options[fontsize]" class="wpaicg_chat_shortcode_font_size">
                            <?php
                            for($i = 10; $i <= 30; $i++){
                                echo '<option'.($wpaicg_settings['fontsize'] == $i ? ' selected': '').' value="'.esc_html($i).'">'.esc_html($i).'px</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Width:</label>
                        <input min="300" type="text" class="wpaicg_chat_shortcode_width" name="wpaicg_chat_shortcode_options[width]" value="<?php
                        echo  esc_html( $wpaicg_settings['width'] ) ;
                        ?>" >
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Height:</label>
                        <input min="300" type="text" class="wpaicg_chat_shortcode_height" name="wpaicg_chat_shortcode_options[height]" value="<?php
                        echo  esc_html( $wpaicg_settings['height'] ) ;
                        ?>" >
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Background Color:</label>
                        <input value="<?php echo esc_html($wpaicg_settings['bgcolor'])?>" type="text" class="wpaicgchat_color wpaicg_bgcolor" name="wpaicg_chat_shortcode_options[bgcolor]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Font Color:</label>
                        <input value="<?php echo esc_html($wpaicg_settings['fontcolor'])?>" type="text" class="wpaicgchat_color wpaicg_font_color" name="wpaicg_chat_shortcode_options[fontcolor]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Text Field Background:</label>
                        <input value="<?php echo esc_html($wpaicg_settings['bg_text_field'])?>" type="text" class="wpaicgchat_color wpaicg_bg_text_field" name="wpaicg_chat_shortcode_options[bg_text_field]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Text Field Border:</label>
                        <input value="<?php echo esc_html($wpaicg_settings['border_text_field'])?>" type="text" class="wpaicgchat_color wpaicg_border_text_field" name="wpaicg_chat_shortcode_options[border_text_field]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Button Color:</label>
                        <input value="<?php echo esc_html($wpaicg_settings['send_color'])?>" type="text" class="wpaicgchat_color wpaicg_send_color" name="wpaicg_chat_shortcode_options[send_color]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">User Background Color:</label>
                        <input value="<?php echo esc_html($wpaicg_settings['user_bg_color'])?>" type="text" class="wpaicgchat_color wpaicg_user_bg_color" name="wpaicg_chat_shortcode_options[user_bg_color]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">AI Background Color:</label>
                        <input value="<?php echo esc_html($wpaicg_settings['ai_bg_color'])?>" type="text" class="wpaicgchat_color wpaicg_ai_bg_color" name="wpaicg_chat_shortcode_options[ai_bg_color]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Use Avatars:</label>
                        <input<?php echo $wpaicg_settings['use_avatar'] ? ' checked': ''?> class="wpaicg_chat_shortcode_use_avatar" value="1" type="checkbox" name="wpaicg_chat_shortcode_options[use_avatar]">
                    </div>
                    <input value="<?php echo esc_html($wpaicg_settings['ai_icon_url'])?>" type="hidden" name="wpaicg_chat_shortcode_options[ai_icon_url]" class="wpaicg_chat_icon_url">
                    <div class="wpcgai_form_row">
                        <label class="wpaicg-form-label">AI Avatar (40x40):</label>
                        <div style="display: inline-flex; align-items: center">
                            <input<?php echo $wpaicg_settings['ai_icon'] == 'default' ? ' checked': ''?> class="wpaicg_chatbox_icon_default" type="radio" value="default" name="wpaicg_chat_shortcode_options[ai_icon]">
                            <div style="text-align: center">
                                <img style="display: block;width: 40px; height: 40px;" src="<?php echo esc_html(WPAICG_PLUGIN_URL).'admin/images/chatbot.png'?>"<br>
                                <strong>Default</strong>
                            </div>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input<?php echo $wpaicg_settings['ai_icon'] == 'custom' ? ' checked': ''?> type="radio" class="wpaicg_chatbox_icon_custom" value="custom" name="wpaicg_chat_shortcode_options[ai_icon]">
                            <div style="text-align: center">
                                <div class="wpaicg_chatbox_icon">
                                    <?php
                                    if(!empty($wpaicg_settings['ai_icon_url']) && $wpaicg_settings['ai_icon'] == 'custom'):
                                        $wpaicg_chatbox_icon_url = wp_get_attachment_url($wpaicg_settings['ai_icon_url']);
                                        ?>
                                        <img src="<?php echo esc_html($wpaicg_chatbox_icon_url)?>" width="40" height="40">
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
                </div>
            </div>
            <!--AI Engine-->
            <div class="wpaicg-collapse">
                <div class="wpaicg-collapse-title"><span>+</span>Parameters</div>
                <div class="wpaicg-collapse-content">
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Model:</label>
                        <select class="regular-text" id="wpaicg_chat_model"  name="wpaicg_chat_shortcode_options[model]" >
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
                                echo '<option'.($wpaicg_settings['model'] == $wpaicg_custom_model ? ' selected':'').' value="'.esc_html($wpaicg_custom_model).'">'.esc_html($wpaicg_custom_model).'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Temperature:</label>
                        <input type="text" class="regular-text" id="label_temperature" name="wpaicg_chat_shortcode_options[temperature]" value="<?php
                        echo  esc_html( $wpaicg_settings['temperature'] ) ;
                        ?>">
                    </div>

                    <div class="mb-5">
                        <label class="wpaicg-form-label">Max Tokens:</label>
                        <input type="text" class="regular-text" id="label_max_tokens" name="wpaicg_chat_shortcode_options[max_tokens]" value="<?php
                        echo  esc_html( $wpaicg_settings['max_tokens'] ) ;
                        ?>" >
                    </div>

                    <div class="mb-5">
                        <label class="wpaicg-form-label">Top P:</label>
                        <input type="text" class="regular-text" id="label_top_p" name="wpaicg_chat_shortcode_options[top_p]" value="<?php
                        echo  esc_html( $wpaicg_settings['top_p'] ) ;
                        ?>" >
                    </div>

                    <div class="mb-5">
                        <label class="wpaicg-form-label">Best Of:</label>
                        <input type="text" class="regular-text" id="label_best_of" name="wpaicg_chat_shortcode_options[best_of]" value="<?php
                        echo  esc_html( $wpaicg_settings['best_of'] ) ;
                        ?>" >
                    </div>

                    <div class="mb-5">
                        <label class="wpaicg-form-label">Frequency Penalty:</label>
                        <input type="text" class="regular-text" id="label_frequency_penalty" name="wpaicg_chat_shortcode_options[frequency_penalty]" value="<?php
                        echo  esc_html( $wpaicg_settings['frequency_penalty'] ) ;
                        ?>" >
                    </div>

                    <div class="mb-5">
                        <label class="wpaicg-form-label">Presence Penalty:</label>
                        <input type="text" class="regular-text" id="label_presence_penalty" name="wpaicg_chat_shortcode_options[presence_penalty]" value="<?php
                        echo  esc_html( $wpaicg_settings['presence_penalty'] ) ;
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
                            <input<?php echo isset($wpaicg_settings['moderation']) && $wpaicg_settings['moderation'] ? ' checked': ''?>  name="wpaicg_chat_shortcode_options[moderation]" value="1" type="checkbox">
                        </div>
                        <div class="mb-5">
                            <label class="wpaicg-form-label">Model:</label>
                            <select class="regular-text"  name="wpaicg_chat_shortcode_options[moderation_model]" >
                                <option<?php echo isset($wpaicg_settings['moderation_model']) && $wpaicg_settings['moderation_model'] == 'text-moderation-latest' ? ' selected':'';?> value="text-moderation-latest">text-moderation-latest</option>
                                <option<?php echo isset($wpaicg_settings['moderation_model']) && $wpaicg_settings['moderation_model'] == 'text-moderation-stable' ? ' selected':'';?> value="text-moderation-stable">text-moderation-stable</option>
                            </select>
                        </div>
                        <div class="mb-5">
                            <label class="wpaicg-form-label">Notice:</label>
                            <textarea rows="8" name="wpaicg_chat_shortcode_options[moderation_notice]"><?php echo isset($wpaicg_settings['moderation_notice']) ? esc_html($wpaicg_settings['moderation_notice']) : ''?></textarea>
                        </div>
                    <?php
                    endif;
                    ?>
                </div>
            </div>
            <!--Audio-->
            <div class="wpaicg-collapse">
                <div class="wpaicg-collapse-title"><span>+</span>Voice Input</div>
                <div class="wpaicg-collapse-content">
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Enable:</label>
                        <input<?php echo isset($wpaicg_settings['audio_enable']) && $wpaicg_settings['audio_enable'] ? ' checked': ''?> value="1" type="checkbox" class="wpaicg_audio_enable" name="wpaicg_chat_shortcode_options[audio_enable]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Mic Color:</label>
                        <input value="<?php echo esc_html($wpaicg_settings['mic_color'])?>" type="text" class="wpaicgchat_color wpaicg_mic_color" name="wpaicg_chat_shortcode_options[mic_color]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Stop Color:</label>
                        <input value="<?php echo esc_html($wpaicg_settings['stop_color'])?>" type="text" class="wpaicgchat_color wpaicg_stop_color" name="wpaicg_chat_shortcode_options[stop_color]">
                    </div>
                </div>
            </div>
            <!--Text message-->
            <div class="wpaicg-collapse">
                <div class="wpaicg-collapse-title"><span>+</span>Custom Text</div>
                <div class="wpaicg-collapse-content">
                    <div class="mb-5">
                        <label class="wpaicg-form-label">AI Name:</label>
                        <input type="text" class="regular-text wpaicg_chat_shortcode_ai_name" name="wpaicg_chat_shortcode_options[ai_name]" value="<?php
                        echo  esc_html( $wpaicg_settings['ai_name'] ) ;
                        ?>" >
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">You:</label>
                        <input type="text" class="regular-text wpaicg_chat_shortcode_you" name="wpaicg_chat_shortcode_options[you]" value="<?php
                        echo  esc_html( $wpaicg_settings['you'] ) ;
                        ?>" >
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">AI Thinking:</label>
                        <input type="text" class="regular-text wpaicg_chat_shortcode_ai_thinking" name="wpaicg_chat_shortcode_options[ai_thinking]" value="<?php
                        echo  esc_html( $wpaicg_settings['ai_thinking'] ) ;
                        ?>" >
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Placeholder:</label>
                        <input type="text" class="regular-text wpaicg_chat_shortcode_placeholder" name="wpaicg_chat_shortcode_options[placeholder]" value="<?php
                        echo  esc_html( $wpaicg_settings['placeholder'] ) ;
                        ?>" >
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Welcome Message:</label>
                        <input type="text" class="regular-text wpaicg_chat_shortcode_welcome" name="wpaicg_chat_shortcode_options[welcome]" value="<?php
                        echo  esc_html( $wpaicg_settings['welcome'] ) ;
                        ?>" >
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">No Answer Message:</label>
                        <input class="regular-text" type="text" value="<?php echo esc_html($wpaicg_settings['no_answer'])?>" name="wpaicg_chat_shortcode_options[no_answer]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Footer Note:</label>
                        <input value="<?php echo esc_html($wpaicg_settings['footer_text'])?>" type="text" name="wpaicg_chat_shortcode_options[footer_text]" placeholder="Powered by ...">
                    </div>
                </div>
            </div>
            <!--Context-->
            <div class="wpaicg-collapse">
                <div class="wpaicg-collapse-title"><span>+</span>Context</div>
                <div class="wpaicg-collapse-content">
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Remember Conversation:</label>
                        <select name="wpaicg_chat_shortcode_options[remember_conversation]">
                            <option<?php echo $wpaicg_settings['remember_conversation'] == 'yes' ? ' selected': ''?> value="yes">Yes</option>
                            <option<?php echo $wpaicg_settings['remember_conversation'] == 'no' ? ' selected': ''?> value="no">No</option>
                        </select>
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Remember Conv. Up To:</label>
                        <select name="wpaicg_chat_shortcode_options[conversation_cut]">
                            <?php
                            for($i=3;$i<=20;$i++){
                                echo '<option'.($wpaicg_settings['conversation_cut'] == $i ? ' selected':'').' value="'.esc_html($i).'">'.esc_html($i).'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">User Aware:</label>
                        <select name="wpaicg_chat_shortcode_options[user_aware]">
                        <option<?php echo $wpaicg_settings['user_aware'] == 'no' ? ' selected': ''?> value="no">No</option>
                            <option<?php echo $wpaicg_settings['user_aware'] == 'yes' ? ' selected': ''?> value="yes">Yes</option>
                        </select>
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Content Aware:</label>
                        <select name="wpaicg_chat_shortcode_options[content_aware]" id="wpaicg_chat_content_aware">
                            <option<?php echo $wpaicg_settings['content_aware'] == 'yes' ? ' selected': ''?> value="yes">Yes</option>
                            <option<?php echo $wpaicg_settings['content_aware'] == 'no' ? ' selected': ''?> value="no">No</option>
                        </select>
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Use Excerpt:</label>
                        <input<?php echo !$wpaicg_settings['embedding'] && $wpaicg_settings['content_aware'] == 'yes' ? ' checked': ''?><?php echo $wpaicg_settings['content_aware'] == 'no' ? ' disabled':''?> type="checkbox" id="wpaicg_chat_excerpt" class="<?php echo $wpaicg_settings['embedding'] && $wpaicg_settings['content_aware'] == 'yes' ? 'asdisabled' : ''?>">
                    </div>

                    <div class="mb-5">
                        <label class="wpaicg-form-label">Use Embeddings:</label>
                        <input<?php echo $wpaicg_settings['embedding'] && $wpaicg_settings['content_aware'] == 'yes' ? ' checked': ''?><?php echo $wpaicg_embedding_field_disabled || $wpaicg_settings['content_aware'] == 'no' ? ' disabled':''?> type="checkbox" value="1" name="wpaicg_chat_shortcode_options[embedding]" id="wpaicg_chat_embedding" class="<?php echo !$wpaicg_settings['embedding'] && $wpaicg_settings['content_aware'] == 'yes' ? 'asdisabled' : ''?>">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Method:</label>
                        <select<?php echo $wpaicg_embedding_field_disabled || empty($wpaicg_settings['embedding']) || $wpaicg_settings['content_aware'] == 'no' ? ' disabled':''?> name="wpaicg_chat_shortcode_options[embedding_type]" id="wpaicg_chat_embedding_type" class="<?php echo !$wpaicg_settings['embedding'] && $wpaicg_settings['content_aware'] == 'yes' ? 'asdisabled' : ''?>">
                            <option<?php echo $wpaicg_settings['embedding_type'] ? ' selected':'';?> value="openai">Embeddings + Completion</option>
                            <option<?php echo empty($wpaicg_settings['embedding_type']) ? ' selected':''?> value="">Embeddings only</option>
                        </select>
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Nearest Answers Up To:</label>
                        <select<?php echo $wpaicg_embedding_field_disabled || empty($wpaicg_settings['embedding']) || $wpaicg_settings['content_aware'] == 'no' ? ' disabled':''?> name="wpaicg_chat_shortcode_options[embedding_top]" id="wpaicg_chat_embedding_top" class="<?php echo !$wpaicg_settings['embedding'] && $wpaicg_settings['content_aware'] == 'yes' ? 'asdisabled' : ''?>">
                            <?php
                            for($i = 1; $i <=5;$i++){
                                echo '<option'.($wpaicg_settings['embedding_top'] == $i ? ' selected':'').' value="'.esc_html($i).'">'.esc_html($i).'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Additional Context?:</label>
                        <input<?php echo $wpaicg_settings['content_aware'] == 'no' ? ' disabled':''?><?php echo $wpaicg_settings['chat_addition'] ? ' checked': ''?> name="wpaicg_chat_shortcode_options[chat_addition]" value="1" type="checkbox" id="wpaicg_chat_addition">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Context:</label>
                        <textarea<?php echo $wpaicg_settings['content_aware'] == 'no' || !$wpaicg_settings['chat_addition'] ? ' disabled':''?> name="wpaicg_chat_shortcode_options[chat_addition_text]" id="wpaicg_chat_addition_text" class="regular-text"><?php echo esc_html($wpaicg_settings['chat_addition_text'])?></textarea>
                    </div>
                </div>
            </div>
            <!--Log-->
            <div class="wpaicg-collapse">
                <div class="wpaicg-collapse-title"><span>+</span>Logs</div>
                <div class="wpaicg-collapse-content">
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Save Chat Logs:</label>
                        <input<?php echo $wpaicg_save_logs ? ' checked': ''?> value="1" type="checkbox" class="wpaicg_chatbot_save_logs" name="wpaicg_chat_shortcode_options[save_logs]">
                    </div>
                    <div class="wpaicg-mb-10">
                        <label class="wpaicg-form-label">Save Prompt:</label>
                        <input<?php echo $wpaicg_save_logs ? '' : ' disabled'?><?php echo $wpaicg_save_logs && isset($wpaicg_settings['log_request']) && $wpaicg_settings['log_request'] ? ' checked' : ''?> class="wpaicg_chatbot_log_request" value="1" type="checkbox" name="wpaicg_chat_shortcode_options[log_request]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Display Notice:</label>
                        <input<?php echo $wpaicg_save_logs ? '': ' disabled'?><?php echo $wpaicg_log_notice ? ' checked': ''?> value="1" class="wpaicg_chatbot_log_notice" type="checkbox" name="wpaicg_chat_shortcode_options[log_notice]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Notice Text:</label>
                        <input<?php echo $wpaicg_save_logs ? '': ' disabled'?> value="<?php echo esc_html($wpaicg_log_notice_message)?>" class="wpaicg_chatbot_log_notice_message" type="text" name="wpaicg_chat_shortcode_options[log_notice_message]">
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
                        <input<?php echo $wpaicg_user_limited ? ' checked': ''?> type="checkbox" value="1" class="wpaicg_user_token_limit" name="wpaicg_chat_shortcode_options[user_limited]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Token Limit:</label>
                        <input<?php echo $wpaicg_user_limited ? '' : ' disabled'?> style="width: 80px" class="wpaicg_user_token_limit_text" type="text" value="<?php echo esc_html($wpaicg_user_tokens)?>" name="wpaicg_chat_shortcode_options[user_tokens]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Role based limit:</label>
                        <?php
                        foreach($wpaicg_roles as $key=>$wpaicg_role){
                            echo '<input class="wpaicg_role_'.esc_html($key).'" value="'.(isset($wpaicg_settings['limited_roles'][$key]) && !empty($wpaicg_settings['limited_roles'][$key]) ? esc_html($wpaicg_settings['limited_roles'][$key]) : '').'" type="hidden" name="wpaicg_chat_shortcode_options[limited_roles]['.esc_html($key).']">';
                        }
                        ?>
                        <input<?php echo $wpaicg_user_limited ? '': ($wpaicg_settings['role_limited'] ? ' checked':'')?> type="checkbox" value="1" class="wpaicg_role_limited" name="wpaicg_chat_shortcode_options[role_limited]">
                        <a href="javascript:void(0)" class="wpaicg_limit_set_role<?php echo $wpaicg_user_limited || !$wpaicg_settings['role_limited'] ? ' disabled': ''?>">Set Limit</a>
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Limit Non-Registered User:</label>
                        <input<?php echo $wpaicg_guest_limited ? ' checked': ''?> type="checkbox" class="wpaicg_guest_token_limit" value="1" name="wpaicg_chat_shortcode_options[guest_limited]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Token Limit:</label>
                        <input<?php echo $wpaicg_guest_limited ? '' : ' disabled'?> class="wpaicg_guest_token_limit_text" style="width: 80px" type="text" value="<?php echo esc_html($wpaicg_guest_tokens)?>" name="wpaicg_chat_shortcode_options[guest_tokens]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Notice:</label>
                        <input type="text" value="<?php echo esc_html($wpaicg_limited_message)?>" name="wpaicg_chat_shortcode_options[limited_message]">
                    </div>
                    <div class="mb-5">
                        <label class="wpaicg-form-label">Reset Limit:</label>
                        <select name="wpaicg_chat_shortcode_options[reset_limit]">
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

            <button class="button button-primary wpaicg-w-100">Save</button>
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
        $(document).on('keypress','.wpaicg_user_token_limit_text,.wpaicg_update_role_limit,.wpaicg_guest_token_limit_text', function (e){
            var charCode = (e.which) ? e.which : e.keyCode;
            if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode !== 46) {
                return false;
            }
            return true;
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
                $('#wpaicg_chat_addition').removeAttr('disabled');
                $('#wpaicg_chat_addition_text').removeAttr('disabled');
                $('#wpaicg_chat_addition').removeClass('asdisabled');
                $('#wpaicg_chat_addition').removeAttr('disabled');
                $('#wpaicg_chat_addition_text').removeAttr('disabled');
            }
            else{
                $('#wpaicg_chat_addition').attr('disabled','disabled');
                $('#wpaicg_chat_addition_text').attr('disabled','disabled');
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
        $('.wpaicg_font_color').wpColorPicker({
            change: function (event, ui){
                var color = ui.color.toString();
                $('.wpaicg-user-message').css('color', color);
                $('.wpaicg-ai-message').css('color', color);
            },
            clear: function(event){
                $('.wpaicg-user-message').css('color', '');
                $('.wpaicg-ai-message').css('color', '');
            }
        });
        $('.wpaicg_audio_enable').click(function (){
            if($(this).prop('checked')){
                $('.wpaicg-mic-icon').show();
            }
            else{
                $('.wpaicg-mic-icon').hide();
            }
        })
        $('.wpaicg_mic_color').wpColorPicker({
            change: function (event, ui){
                var color = ui.color.toString();
                $('.wpaicg-mic-icon').css('color', color);
            },
            clear: function(event){
                $('.wpaicg-mic-icon').css('color', '');
            }
        });
        $('.wpaicg_stop_color').wpColorPicker({
            change: function (event, ui){
                var color = ui.color.toString();
                $('.wpaicg-mic-icon').css('color', color);
            },
            clear: function(event){
                $('.wpaicg-mic-icon').css('color', '');
            }
        });
        $('.wpaicg_user_bg_color').wpColorPicker({
            change: function (event, ui){
                var color = ui.color.toString();
                $('.wpaicg-user-message').css('background-color', color);
            },
            clear: function(event){
                $('.wpaicg-user-message').css('background-color', '');
            }
        });
        $('.wpaicg_bgcolor').wpColorPicker({
            change: function (event, ui){
                var color = ui.color.toString();
                $('.wpaicg-chat-shortcode').css('background-color', color);
            },
            clear: function(event){
                $('.wpaicg-chat-shortcode').css('background-color', '#222');
            }
        });
        $('.wpaicg_bg_text_field').wpColorPicker({
            change: function (event, ui){
                var color = ui.color.toString();
                $('.wpaicg-chat-shortcode-typing').css('background-color', color);
            },
            clear: function(event){
                $('.wpaicg-chat-shortcode-typing').css('background-color', '#fff');
            }
        });
        $('.wpaicg_border_text_field').wpColorPicker({
            change: function (event, ui){
                var color = ui.color.toString();
                $('.wpaicg-chat-shortcode-typing').css('border-color', color);
            },
            clear: function(event){
                $('.wpaicg-chat-shortcode-typing').css('border-color', '#ccc');
            }
        });
        $('.wpaicg_send_color').wpColorPicker({
            change: function (event, ui){
                var color = ui.color.toString();
                $('.wpaicg-chat-shortcode-send').css('color', color);
            },
            clear: function(event){
                $('.wpaicg-chat-shortcode-send').css('color', '#fff');
            }
        });
        $('.wpaicg_ai_bg_color').wpColorPicker({
            change: function (event, ui){
                var color = ui.color.toString();
                $('.wpaicg-ai-message').css('background-color', color);
            },
            clear: function(event){
                $('.wpaicg-ai-message').css('background-color', '');
            }
        });
        $('.wpaicg_chat_shortcode_width').on('input', function (){
            var chatbox_width = $(this).val();
            var preview_width = $('.wpaicg-chat-shortcode-preview').width();
            console.log(preview_width);
            if(chatbox_width.indexOf('%') > -1){
                chatbox_width = chatbox_width.replace('%','');
                chatbox_width = parseFloat(chatbox_width);
                chatbox_width = chatbox_width*preview_width/100;
            }
            else{
                chatbox_width = chatbox_width.replace('px','');
                chatbox_width = parseFloat(chatbox_width);
            }
            if(chatbox_width > preview_width){
                chatbox_width = preview_width;
            }
            $('.wpaicg-chat-shortcode').width(chatbox_width+'px');
        });
        $('.wpaicg_chat_shortcode_height').on('input', function (){
            var chatbox_height = $(this).val();
            var preview_width = $(window).height();
            if(chatbox_height.indexOf('%') > -1){
                chatbox_height = chatbox_height.replace('%','');
                chatbox_height = parseFloat(chatbox_height);
                chatbox_height = chatbox_height*preview_width/100;
            }
            else{
                chatbox_height = chatbox_height.replace('px','');
                chatbox_height = parseFloat(chatbox_height);
            }
            if(chatbox_height > preview_width){
                chatbox_height = preview_width;
            }
            $('.wpaicg-chat-shortcode-content ul').height((chatbox_height - 44)+'px');
        });
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
                    button.html('<img width="40" height="40" src="'+attachment.url+'">');
                    $('.wpaicg_chat_icon_url').val(attachment.id);
                }).open();
        });
        $('.wpaicg_chat_shortcode_font_size').on('change', function (){
            var font_size = $(this).val();
            $('.wpaicg-chat-shortcode-messages li').each(function (idx, item){
                $(item).css('font-size',font_size+'px');
            })
        });
        function wpaicgChangeAvatarRealtime(){
            var wpaicg_user_avatar_check = $('.wpaicg_chat_shortcode_you').val()+':';
            var wpaicg_ai_avatar_check = $('.wpaicg_chat_shortcode_ai_name').val()+':';
            if($('.wpaicg_chat_shortcode_use_avatar').prop('checked')){
                wpaicg_user_avatar_check = '<img src="<?php echo get_avatar_url(get_current_user_id())?>" height="40" width="40">';
                wpaicg_ai_avatar_check = '<?php echo esc_html(WPAICG_PLUGIN_URL) . 'admin/images/chatbot.png';?>';
                if($('.wpaicg_chatbox_icon_custom').prop('checked') && $('.wpaicg_chatbox_icon img').length){
                    wpaicg_ai_avatar_check = $('.wpaicg_chatbox_icon img').attr('src');
                }
                wpaicg_ai_avatar_check = '<img src="'+wpaicg_ai_avatar_check+'" height="40" width="40">';
            }

            $('.wpaicg-chat-shortcode-messages li.wpaicg-ai-message').each(function (idx, item){
                $(item).find('.wpaicg-chat-avatar').html(wpaicg_ai_avatar_check);
            });
            $('.wpaicg-chat-shortcode-messages li.wpaicg-user-message').each(function (idx, item){
                $(item).find('.wpaicg-chat-avatar').html(wpaicg_user_avatar_check);
            });
        }
        $('.wpaicg_chat_shortcode_ai_name,.wpaicg_chat_shortcode_you').on('input', function (){
            wpaicgChangeAvatarRealtime();
        })
        $('.wpaicg_chat_shortcode_use_avatar,.wpaicg_chatbox_icon_default,.wpaicg_chatbox_icon_custom').on('click', function (){
            wpaicgChangeAvatarRealtime();
        })
    })
</script>

<?php
if ( ! defined( 'ABSPATH' ) ) exit;
if(isset($_POST['wpaicg_delete_running'])){
    if(!wp_verify_nonce($_REQUEST['_wpnonce'], 'wpaicg_delete_running')){
        die(WPAICG_NONCE_ERROR);
    }
    update_option('wpaicg_crojob_builder_last_time', time());
    @unlink(WPAICG_PLUGIN_DIR.'wpaicg_builder.txt');
    echo '<script>window.location.reload()</script>';
    exit;
}
$wpaicg_cron_job_last_time = get_option('wpaicg_crojob_builder_last_time','');
$wpaicg_cron_added = get_option('wpaicg_cron_builder_added','');
if(!empty($wpaicg_cron_job_last_time)){
    $wpaicg_timestamp_diff = time() - $wpaicg_cron_job_last_time;
    if($wpaicg_timestamp_diff > 600){
        ?>
        <div class="wpaicg-alert">
            <p style="color: #f00">
                You can use below button to restart your queue if it is stuck.
            </p>
            <form action="" method="post">
                <?php
                wp_nonce_field('wpaicg_delete_running');
                ?>
                <button name="wpaicg_delete_running" class="button button-primary">Force Refresh</button>
            </form>
        </div>
        <?php
    }
}
?>
<div class="wpaicg-alert">
    <?php
    if(empty($wpaicg_cron_added)):
        ?>
        <h4>Important</h4>
        <p>
            You must configure a <a href="https://www.hostgator.com/help/article/what-are-cron-jobs" target="_blank">Cron Job</a> on your hosting/server.
            If this is not done, the Index Builder feature will not be available for use.
        </p>
        <p>You can also index your posts manually by clicking the "Instant Embedding" button on the Posts/Pages/Products page.</p>
    <?php
    endif;
    ?>
    <?php
    if(empty($wpaicg_cron_added)){
        echo '<p style="color: #f00"><strong>It appears that you have not activated Cron Job on your server, which means you will not be able to use the Index Builder feature. If you have already activated Cron Job, please allow a few minutes to pass before refreshing the page.</strong></p>';
    }
    else{
        echo '<p style="color: #10922c"><strong>Great! It looks like your Cron Job is running properly. You should now be able to use the Index Builder.</strong></p>';
    }
    ?>
    <?php
    if(!empty($wpaicg_cron_job_last_time)):
        $wpaicg_current_timestamp = time();

        $wpaicg_time_diff = human_time_diff( $wpaicg_cron_job_last_time, $wpaicg_current_timestamp );

        if ( strpos( $wpaicg_time_diff, 'hour' ) !== false ) {
            $wpaicg_output = str_replace( 'hours', 'hours', $wpaicg_time_diff );
        } elseif ( strpos( $wpaicg_time_diff, 'day' ) !== false ) {
            $wpaicg_output = str_replace( 'days', 'days', $wpaicg_time_diff );
        } elseif ( strpos( $wpaicg_time_diff, 'min' ) !== false ) {
            $wpaicg_output = str_replace( 'minutes', 'minutes', $wpaicg_time_diff );
        } else {
            $wpaicg_output = $wpaicg_time_diff;
        }
        ?>
        <p>The last time, the Cron Job ran on your website <?php echo esc_html(date('Y-m-d H:i:s',$wpaicg_cron_job_last_time))?> (<?php echo esc_html($wpaicg_output)?> ago)</p>
    <?php
    endif;
    ?>
    <hr>
    <p></p>
    <p><strong>Cron Job Configuration</strong></p>
    <p></p>
    <p>If you are using Linux/Unix server, copy below code and paste it into crontab. Read detailed guide <a href="<?php echo esc_url("https://gptaipower.com/how-to-add-cron-job/"); ?>" target="_blank">here</a>.</p>
    <p><code>* * * * * php <?php echo esc_html(ABSPATH)?>index.php -- wpaicg_builder=yes</code></p>
    <p></p>
    <hr>
    <p><strong>Instant Embedding</strong></p>
    <p>You can also index your posts manually by clicking the "Instant Embedding" button on the Posts/Pages/Products page.</p>
</div>

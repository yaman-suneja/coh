<?php
if ( ! defined( 'ABSPATH' ) ) exit;
include __DIR__.'/wpaicg_alert.php';
?>
<h2>Auto Content From Multi Lines</h2>
<div class="p-10">
    <table class="form-table">
        <tbody>
        <tr>
            <th scope="row">
                Titles<br>
                <small style="font-weight: normal">(Enter one title per line.)</small>
            </th>
            <td>
                <textarea<?php echo empty($wpaicg_cron_added) ? ' disabled':''?> rows="15" class="wpaicg-multi-line"></textarea>
            </td>
        </tr>
        <tr>
            <th scope="row">Posts Status</th>
            <td>
                <label>
                    <input<?php echo empty($wpaicg_cron_added) ? ' disabled':''?> checked type="radio" name="post_status" value="draft" class="wpaicg-post-status"> Draft
                </label>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <label>
                    <input<?php echo empty($wpaicg_cron_added) ? ' disabled':''?> type="radio" value="publish" name="post_status" class="wpaicg-post-status"> Publish
                </label>
                <p class="wpaicg-ajax-message"></p>
            </td>
        </tr>
        <tr>
            <th></th>
            <td>
                <button<?php echo empty($wpaicg_cron_added) ? ' disabled':''?> class="button button-primary wpaicg-multi-button">Generate</button>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<script>
    (function ($){
        $('.wpaicg-schedule-post').datetimepicker({
            format: 'Y-m-d H:i',
            startDate: new Date()
        });
        var wpaicg_button = $('.wpaicg-multi-button');
        var wpaicg_multi_line = $('.wpaicg-multi-line');
        wpaicg_button.click(function (){
            var wpaicg_multi_line_value = wpaicg_multi_line.val();
            if(wpaicg_multi_line_value === ''){
                alert('Please enter at least one line');
            }
            else{
                var wpaicg_lines = wpaicg_multi_line_value.split("\n");
                if(wpaicg_lines.length > <?php echo esc_html($wpaicg_number_title)?>){
                    <?php
                    if(\WPAICG\wpaicg_util_core()->wpaicg_is_pro()):
                    ?>
                    $('.wpaicg-ajax-message').html('You added more than <?php echo esc_html($wpaicg_number_title)?> lines so we are only processing first <?php echo esc_html($wpaicg_number_title)?> lines');
                    <?php
                    else:
                    ?>
                    $('.wpaicg-ajax-message').html('Free users can only generate <?php echo esc_html($wpaicg_number_title)?> lines at a time. Please upgrade to the Pro plan to get access to more lines.');
                    <?php
                    endif;
                    ?>
                }
                var wpaicg_titles = wpaicg_lines.slice(0,<?php echo esc_html($wpaicg_number_title)?>);
                var wpaicg_schedules = [];
                var wpaicg_post_status = $('.wpaicg-post-status:checked').val();
                var wpaicg_schedule = $('.wpaicg-schedule-post').val();
                $.each(wpaicg_titles, function (idx,item){
                    wpaicg_schedules.push(wpaicg_schedule);
                });
                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php')?>',
                    data: {wpaicg_titles: wpaicg_titles,wpaicg_schedules: wpaicg_schedules,post_status: wpaicg_post_status, action: 'wpaicg_bulk_generator',source: 'multi','nonce': '<?php echo wp_create_nonce('wpaicg-ajax-nonce')?>'},
                    type: 'POST',
                    dataType: 'JSON',
                    beforeSend: function(){
                        wpaicg_button.attr('disabled','disabled');
                        wpaicg_button.append('<span class="spinner"></span>');
                        wpaicg_button.find('.spinner').css('visibility','unset');
                    },
                    success: function (res){
                        wpaicg_button.removeAttr('disabled');
                        wpaicg_button.find('.spinner').remove();
                        if(res.status === 'success'){
                            window.location.href = '<?php echo admin_url('admin.php?page=wpaicg_bulk_content')?>&wpaicg_track='+res.id
                        }
                        else{
                            alert(res.msg);
                        }
                    },
                    error: function (){
                        wpaicg_button.removeAttr('disabled');
                        wpaicg_button.find('.spinner').remove();
                        alert('Something went wrong');
                    }
                })
            }
        })
    })(jQuery)
</script>

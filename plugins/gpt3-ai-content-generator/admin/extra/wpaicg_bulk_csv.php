<?php
if ( ! defined( 'ABSPATH' ) ) exit;
include __DIR__.'/wpaicg_alert.php';
?>
<h2>Auto Content From CSV</h2>
<div class="p-10">
    <p>Please make sure your CSV delimiter is <strong>comma</strong>.</p>
    <div class="wpaicg-bulk-item">
        <div class="wpaicg-bulk-title"><strong>CSV File</strong></div>
    </div>
    <div class="wpaicg-bulk-item">
        <div class="wpaicg-bulk-title">
            <input accept="text/csv" type="file" class="wpaicg-csv-file">
        </div>
    </div>
    <div class="wpaicg-bulk-item">
        <div class="wpaicg-bulk-title p-10">
            <label>
                <input<?php echo empty($wpaicg_cron_added) ? ' disabled':''?> checked type="radio" value="draft" name="post_status" class="wpaicg-csv-status"> Draft
            </label>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <label>
                <input<?php echo empty($wpaicg_cron_added) ? ' disabled':''?> type="radio" value="publish" name="post_status" class="wpaicg-csv-status"> Publish
            </label>
        </div>
    </div>
    <div class="wpaicg-bulk-item">
        <div class="wpaicg-bulk-title">
            <button<?php echo empty($wpaicg_cron_added) ? ' disabled':''?> class="button button-primary wpaicg-import-csv-button wpaicg-bulk-button">Import</button>
        </div>
    </div>
    <p class="wpaicg-ajax-message"></p>
</div>
<script>
    (function ($){
        var wpaicg_import_btn = $('.wpaicg-import-csv-button');
        var wpaicg_import_file = $('.wpaicg-csv-file');
        $('.wpaicg-schedule-csv').datetimepicker({
            format: 'Y-m-d H:i',
            startDate: new Date()
        });
        wpaicg_import_btn.click(function (){
            var wpaicg_file = wpaicg_import_file[0].files[0];
            if(wpaicg_file === undefined){
                alert('Please select CSV file')
            }
            else{
                if(wpaicg_file.type !== 'text/csv'){
                    alert('Wrong file type. We only accept CSV file')
                }
                else{
                    var data = new FormData();
                    data.append('action', 'wpaicg_read_csv');
                    data.append('file', wpaicg_file);
                    data.append('nonce','<?php echo wp_create_nonce('wpaicg-ajax-nonce')?>');
                    $.ajax({
                        url: '<?php echo admin_url('admin-ajax.php')?>',
                        data: data,
                        cache: false,
                        contentType: false,
                        processData: false,
                        type: 'POST',
                        beforeSend: function (){
                            wpaicg_import_btn.attr('disabled','disabled');
                            wpaicg_import_btn.append('<span class="spinner"></span>');
                            wpaicg_import_btn.find('.spinner').css('visibility','unset');
                        },
                        success: function (res){
                            if(res.status === 'success'){
                                if(res.notice !== undefined){
                                    $('.wpaicg-ajax-message').html(res.notice);
                                }
                                if(res.data !== ''){
                                    var wpaicg_titles = res.data.split('|');
                                    var wpaicg_schedules = [];
                                    var wpaicg_post_status = $('.wpaicg-csv-status:checked').val();
                                    var wpaicg_schedule = $('.wpaicg-schedule-csv').val();
                                    $.each(wpaicg_titles, function (idx,item){
                                        wpaicg_schedules.push(wpaicg_schedule);
                                    });
                                    $.ajax({
                                        url: '<?php echo admin_url('admin-ajax.php')?>',
                                        data: {wpaicg_titles: wpaicg_titles,wpaicg_schedules: wpaicg_schedules,post_status: wpaicg_post_status, action: 'wpaicg_bulk_generator',source: 'csv','nonce': '<?php echo wp_create_nonce('wpaicg-ajax-nonce')?>'},
                                        type: 'POST',
                                        dataType: 'JSON',
                                        success: function (res){
                                            wpaicg_import_btn.removeAttr('disabled');
                                            wpaicg_import_btn.find('.spinner').remove();
                                            if(res.status === 'success'){
                                                window.location.href = '<?php echo admin_url('admin.php?page=wpaicg_bulk_content')?>&wpaicg_track='+res.id
                                            }
                                            else{
                                                alert(res.msg);
                                            }
                                        },
                                        error: function (){
                                            wpaicg_import_btn.removeAttr('disabled');
                                            wpaicg_import_btn.find('.spinner').remove();
                                            alert('Something went wrong');
                                        }
                                    })
                                }
                                else{
                                    alert('No data for import');
                                }
                            }
                            else {
                                wpaicg_import_btn.removeAttr('disabled');
                                wpaicg_import_btn.find('.spinner').remove();
                                alert(res.msg)
                            }
                        },
                        error: function (){
                            wpaicg_import_btn.removeAttr('disabled');
                            wpaicg_import_btn.find('.spinner').remove();
                        }
                    })
                }
            }
        })
    })(jQuery)
</script>


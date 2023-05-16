<?php
if ( ! defined( 'ABSPATH' ) ) exit;
$wpaicg_all_categories = get_terms(array(
    'taxonomy' => 'category',
    'hide_empty' => false
));
?>
<style>
    .wpaicg-form-bulk{
        max-width: 800px;
    }
    .wpaicg-form-bulk table td,.wpaicg-form-bulk table th{
        padding: 2px
    }
    .wpaicg-bulk-addition{
        padding: 10px;
        background: #e3e3e3;
        border-radius: 5px;
        border: 1px solid #ccc;
        margin-left: 16px;
        margin-bottom: 20px;
        display: none;
    }
    .wpaicg-bulk-addition table{}
</style>
<div id="wpaicg-bulk-generator">
    <div class="wpaicg-row">
        <div class="wpaicg-col">
            <?php
            include __DIR__.'/wpaicg_alert.php';
            ?>
        </div>
        <div class="wpaicg-col">
            <h2>Auto Content Writer</h2>
            <form action="" method="post" class="wpaicg-form-bulk">
                <?php
                wp_nonce_field('wpaicg_bulk_save');
                ?>
                <input type="hidden" name="action" value="wpaicg_bulk_save_editor">
                <div class="wpaicg-bulk-item">
                    <label class="wpaicg-label">&nbsp;</label>
                    <div class="wpaicg-bulk-title"><strong>Title</strong></div>
                    <div class="ml-5" style="margin-left: 7px;width: 179px;"><strong>Schedule</strong></div>
                    <div class="ml-5" style="margin-left: 7px;width: 90px;"><strong>Category</strong></div>
                    <div class="ml-5" style="margin-left: 7px;width: 90px;"><strong>Author</strong></div>
                    <div class="ml-5" style="margin-left: 7px;width: 120px;"></div>
                </div>
                <?php
                for ($i = 0;$i < $wpaicg_number_title; $i++){
                    ?>
                    <div class="wpaicg-bulk-item wpaicg-bulk-item-<?php echo esc_html($i);?>">
                        <label class="wpaicg-label"><?php echo esc_html($i+1);?></label>
                        <input<?php echo empty($wpaicg_cron_added) ? ' disabled':''?>  type="text" class="regular-text wpaicg_bulk_title" name="bulk[<?php echo esc_html($i);?>][title]">
                        <div class="wpaicg-bulk-schedule ml-5">
                            <input<?php echo \WPAICG\wpaicg_util_core()->wpaicg_is_pro() && !empty($wpaicg_cron_added) ? '' :' disabled'?><?php echo \WPAICG\wpaicg_util_core()->wpaicg_is_pro() ? '' :' placeholder="Available in Pro"'?> type="text" class="wpaicg-item-schedule" name="bulk[<?php echo esc_html($i);?>][schedule]">
                        </div>
                        <div class="ml-5" style="margin-left: 7px;width: 120px;">
                            <select<?php echo empty($wpaicg_cron_added) ? ' disabled':''?> name="bulk[<?php echo esc_html($i);?>][category]" style="width: 100%">
                                <option value="">Category</option>
                                <?php
                                foreach($wpaicg_all_categories as $wpaicg_all_category){
                                    echo '<option value="'.esc_html($wpaicg_all_category->term_id).'">'.esc_html($wpaicg_all_category->name).'</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="ml-5" style="margin-left: 7px;width: 120px;">
                            <select<?php echo empty($wpaicg_cron_added) ? ' disabled':''?> name="bulk[<?php echo esc_html($i);?>][author]" style="width: 100%">
                                <?php
                                foreach(get_users() as $user){
                                    echo '<option'.($user->ID == get_current_user_id() ? ' selected':'').' value="'.esc_html($user->ID).'">'.esc_html($user->display_name).'</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="ml-5" style="margin-left: 7px;width: 120px;"><a class="wpaicg-bulk-open-bulk" data-id="<?php echo esc_html($i)?>" href="javascript:void(0)">[+] More</a></div>
                    </div>
                    <div class="wpaicg-bulk-addition wpaicg-bulk-addition-<?php echo esc_html($i)?>">
                        <table class="form-table">
                            <tr>
                                <th>Tags</th>
                                <th>Keywords to Include</th>
                                <th>Keywords to Avoid</th>
                            </tr>
                            <tr>
                                <td><input<?php echo empty($wpaicg_cron_added) ? ' disabled':''?>  type="text" name="bulk[<?php echo esc_html($i);?>][tags]"></td>
                                <td><input<?php echo \WPAICG\wpaicg_util_core()->wpaicg_is_pro() && !empty($wpaicg_cron_added) ? '' : ' disabled'?><?php echo \WPAICG\wpaicg_util_core()->wpaicg_is_pro() ? '' : ' placeholder="Available in Pro"'?> type="text" name="bulk[<?php echo esc_html($i);?>][keywords]"></td>
                                <td><input<?php echo \WPAICG\wpaicg_util_core()->wpaicg_is_pro() && !empty($wpaicg_cron_added) ? '' : ' disabled'?><?php echo \WPAICG\wpaicg_util_core()->wpaicg_is_pro() ? '' : ' placeholder="Available in Pro"'?> type="text" name="bulk[<?php echo esc_html($i);?>][avoid]"></td>
                            </tr>
                            <tr>
                                <th>Anchor Text</th>
                                <th>Target URL</th>
                                <th>CTA</th>
                            </tr>
                            <tr>
                                <td><input<?php echo empty($wpaicg_cron_added) ? ' disabled':''?>  type="text" name="bulk[<?php echo esc_html($i);?>][anchor]" placeholder="e.g. battery life"></td>
                                <td><input<?php echo empty($wpaicg_cron_added) ? ' disabled':''?>  type="text" name="bulk[<?php echo esc_html($i);?>][target]" placeholder="https://"></td>
                                <td><input<?php echo empty($wpaicg_cron_added) ? ' disabled':''?>  type="text" name="bulk[<?php echo esc_html($i);?>][cta]" placeholder="https://...."></td>
                            </tr>
                        </table>
                    </div>
                    <?php
                }
                if(!\WPAICG\wpaicg_util_core()->wpaicg_is_pro()){
                ?>
                    <a href="<?php echo esc_url(admin_url('admin.php?page=wpaicg-pricing'))?>"><img src="<?php echo esc_url(WPAICG_PLUGIN_URL)?>admin/images/pro_img.png"></a>
                <?php
                }
                ?>
                <div class="wpaicg-bulk-item">
                    <label class="wpaicg-label">&nbsp;</label>
                    <div class="wpaicg-bulk-title p-10">
                        <label>
                            <input<?php echo empty($wpaicg_cron_added) ? ' disabled':''?> checked type="radio" name="post_status" value="draft"> Draft
                        </label>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <label>
                            <input<?php echo empty($wpaicg_cron_added) ? ' disabled':''?> type="radio" name="post_status" value="publish"> Publish
                        </label>
                    </div>
                </div>
                <div class="wpaicg-bulk-item">
                    <label class="paicg-label">&nbsp;</label>
                    <div class="wpaicg-bulk-title wpaicg-text-center">
                        <button<?php echo empty($wpaicg_cron_added) ? ' disabled':''?> class="button button-primary wpaicg-bulk-button">Generate</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    jQuery(document).ready(function ($){
        if($('.wpaicg-item-schedule').length) {
            $('.wpaicg-item-schedule').datetimepicker({
                format: 'Y-m-d H:i',
                startDate: new Date()
            })
        }
        $('.wpaicg-bulk-open-bulk').click(function (){
            var id = $(this).attr('data-id');
            if($(this).hasClass('active')){
                $(this).removeClass('active');
                $('.wpaicg-bulk-open-bulk').html('[+] More');
                $('.wpaicg-bulk-addition').hide();
            }
            else{
                $('.wpaicg-bulk-open-bulk').html('[+] More');
                $('.wpaicg-bulk-open-bulk').removeClass('active');
                $('.wpaicg-bulk-addition').hide();
                $(this).addClass('active');
                $(this).html('[-] Hide');
                $('.wpaicg-bulk-addition-'+id).slideDown('fast');
            }
        })
        var wpaicg_number_title = <?php echo esc_html($wpaicg_number_title)?>;
        $('.wpaicg-form-bulk').on('submit', function (){
            var wpaicg_button = $('.wpaicg-bulk-button');
            var wpaicg_form = $(this);
            var wpaicg_error_title = 0;
            $('.wpaicg_bulk_title').each(function (idx, wpaicg_input){
                if($(wpaicg_input).val() === ''){
                    wpaicg_error_title += 1;
                }
            });
            if(wpaicg_error_title >= wpaicg_number_title){
                alert('Please enter at least one title');
            }
            else{
                var wpaicg_data = wpaicg_form.serialize();
                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php')?>',
                    data: wpaicg_data,
                    type: 'POST',
                    dataType: 'JSON',
                    beforeSend: function (){
                        wpaicg_button.attr('disabled','disabled');
                    },
                    success: function (res){
                        wpaicg_button.removeAttr('disabled');
                        if(res.status === 'success'){
                            window.location.href = '<?php echo esc_url(admin_url('admin.php?page=wpaicg_bulk_content'))?>&wpaicg_track='+res.id
                        }
                        else{
                            alert('Something went wrong');
                        }
                    },
                    error: function (){
                        wpaicg_button.removeAttr('disabled');
                        alert('Something went wrong');
                    }
                })
            }
            return false;
        })
    })
</script>

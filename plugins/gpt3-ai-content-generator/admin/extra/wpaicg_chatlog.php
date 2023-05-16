<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// Verify nonce
if (isset($_GET['wpaicg_nonce']) && !wp_verify_nonce($_GET['wpaicg_nonce'], 'wpaicg_chatlogs_search_nonce')) {
    die(WPAICG_NONCE_ERROR);
}

global $wpdb;
$wpaicg_log_page = isset($_GET['wpage']) && !empty($_GET['wpage']) ? sanitize_text_field($_GET['wpage']) : 1;
$search = isset($_GET['wsearch']) && !empty($_GET['wsearch']) ? sanitize_text_field($_GET['wsearch']) : '';
$where = '';
if(!empty($search)) {
    $where .= $wpdb->prepare(" AND `data` LIKE %s", '%' . $wpdb->esc_like($search) . '%');
}
$query = "SELECT * FROM ".$wpdb->prefix."wpaicg_chatlogs WHERE 1=1".$where;
$total_query = "SELECT COUNT(1) FROM (${query}) AS combined_table";
$total = $wpdb->get_var( $total_query );
$items_per_page = 10;
$offset = ( $wpaicg_log_page * $items_per_page ) - $items_per_page;
$wpaicg_logs = $wpdb->get_results( $wpdb->prepare( $query . " ORDER BY created_at DESC LIMIT %d, %d", $offset, $items_per_page ) );
$totalPage = ceil($total / $items_per_page);
?>
<style>
    .wpaicg_modal{
        top: 5%;
        height: 90%;
        position: relative;
    }
    .wpaicg_modal_content{
        max-height: calc(100% - 103px);
        overflow-y: auto;
    }
    .wpaicg_message code{
        padding: 3px 5px 2px;
        background: rgb(0 0 0 / 20%);
        font-size: 13px;
        font-family: Consolas,Monaco,monospace;
        direction: ltr;
        unicode-bidi: embed;
        display: block;
        margin: 5px 0px;
        border-radius: 4px;
        white-space: pre-wrap;
    }
</style>
<form action="" method="get">
    <input type="hidden" name="page" value="wpaicg_chatgpt">
    <input type="hidden" name="action" value="logs">
    <?php wp_nonce_field('wpaicg_chatlogs_search_nonce', 'wpaicg_nonce'); ?>
    <div class="wpaicg-d-flex mb-5">
        <input style="width: 100%" value="<?php echo esc_html($search)?>" class="regular-text" name="wsearch" type="text" placeholder="Type for search">
        <button class="button button-primary">Search</button>
    </div>
</form>
<table class="wp-list-table widefat fixed striped table-view-list posts">
    <thead>
    <tr>
        <th>SessionID</th>
        <th>Date</th>
        <th>User Message</th>
        <th>AI Response</th>
        <th>Page</th>
        <th>Source</th>
        <th>Token</th>
        <th>Estimated</th>
        <th>IP</th>
        <?php
        if(\WPAICG\wpaicg_util_core()->wpaicg_is_pro()):
        ?>
        <th>Moderation</th>
        <?php
        endif;
        ?>
        <th>Action</th>
    </tr>
    </thead>
    <tbody class="wpaicg-builder-list">
    <?php
    if($wpaicg_logs && is_array($wpaicg_logs) && count($wpaicg_logs)){
        foreach ($wpaicg_logs as $wpaicg_log){
            $wpaicg_flagged = false;
            $last_user_message = '';
            $ip_address = '';
            $last_ai_message = '';
            $all_messages = json_decode($wpaicg_log->data,true);
            $all_messages = $all_messages && is_array($all_messages) ? $all_messages : array();
            $tokens = 0;
            foreach(array_reverse($all_messages) as $item){
                if(isset($item['flag']) && !empty($item['flag'])){
                    $wpaicg_flagged = $item['flag'];
                }
            }
            foreach(array_reverse($all_messages) as $item){
                if(
                    isset($item['type'])
                    && $item['type'] == 'user'
                    && empty($last_user_message)
                ){
                    $last_user_message = $item['message'];
                    $ip_address = isset($item['ip']) ? $item['ip'] : '';
                }

                if(
                    isset($item['type'])
                    && $item['type'] == 'ai'
                    && empty($last_ai_message)
                ){
                    $last_ai_message = $item['message'];
                }
                if(!empty($last_ai_message) && !empty($last_user_message)){
                    break;
                }
                if(isset($item['token']) && !empty($item['token'])){
                    $tokens += $item['token'];
                }

            }
            $estimated = $tokens * 0.000002;
            ?>
            <tr>
                <td><?php echo esc_html($wpaicg_log->id)?></td>
                <td><?php echo esc_html(date('d.m.Y H:i',$wpaicg_log->created_at))?></td>
                <td><?php echo esc_html(substr($last_user_message,0,255))?></td>
                <td><?php echo esc_html(substr($last_ai_message,0,255))?></td>
                <td><?php echo esc_html($wpaicg_log->page_title)?></td>
                <td><?php echo $wpaicg_log->source == 'widget' ? 'Chat Widget' : ($wpaicg_log->source == 'shortcode' ? 'Chat Shortcode' : esc_html($wpaicg_log->source))?></td>
                <td><?php echo $tokens > 0 ? esc_html($tokens) : '--'?></td>
                <td><?php echo $estimated > 0 ? esc_html($estimated).'$' : '--'?></td>
                <td><?php echo esc_html($ip_address)?></td>
                <?php
                if(\WPAICG\wpaicg_util_core()->wpaicg_is_pro()):
                ?>
                <td><?php echo $wpaicg_flagged ? '<span style="font-weight: bold;color: #f00;">Flagged</span>':'<span style="font-weight: bold;color: #47a700;">Passed</span>'?></td>
                <?php
                endif;
                ?>
                <td>
                    <button class="button button-primary button-small wpaicg-log-messages" data-messages="<?php echo esc_html(htmlspecialchars(json_encode($all_messages),ENT_QUOTES, 'UTF-8'))?>">View</button>
                </td>
            </tr>
            <?php
        }
    }
    ?>
    </tbody>
</table>
<div class="wpaicg-paginate">
<?php
if($totalPage > 1){
    echo paginate_links( array(
        'base'         => admin_url('admin.php?page=wpaicg_chatgpt&action=logs&wpage=%#%'),
        'total'        => $totalPage,
        'current'      => $wpaicg_log_page,
        'format'       => '?wpage=%#%',
        'show_all'     => false,
        'prev_next'    => false,
        'add_args'     => false,
    ));
}
?>
</div>
<script>
    jQuery(document).ready(function ($){
        function htmlEntities(str) {
            return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }
        $('.wpaicg_modal_close').click(function (){
            $('.wpaicg_modal_close').closest('.wpaicg_modal').hide();
            $('.wpaicg-overlay').hide();
        });
        $('.wpaicg-log-messages').click(function (){
            var wpaicg_messages = $(this).attr('data-messages');
            if(wpaicg_messages !== ''){
                wpaicg_messages = JSON.parse(wpaicg_messages);
                var html = '';
                $('.wpaicg_modal_title').html('View Chat Log');
                $.each(wpaicg_messages, function (idx, item){
                    html += '<div class="wpaicg_message" style="margin-bottom: 10px;">';
                    if(item.type === 'ai'){
                        html += '<strong>AI:</strong>&nbsp;';
                    }
                    else{
                        html += '<strong>User:</strong>&nbsp;';
                    }
                    let html_Entities = htmlEntities(item.message);
                    html += html_Entities.replace(/```([\s\S]*?)```/g,'<code>$1</code>');
                    if(typeof item.flag !== "undefined" && item.flag !== '' && item.flag !== false){
                        html += '<span style="display: inline-block;font-size: 12px;font-weight: bold;background: #b71a1a;padding: 1px 5px;border-radius: 3px;color: #fff;margin-left: 5px;">Flagged as '+item.flag+'<span>';
                    }
                    if(typeof item.request !== "undefined" && typeof item.request === 'object'){
                        html += '<a href="javascript:void(0)" class="show_message_request">[details]</a>';
                        html += '<div class="wpaicg_request" style="display: none;padding: 10px;background: #e9e9e9;border-radius: 4px;"><pre style="white-space: pre-wrap">'+JSON.stringify(item.request,undefined, 4)+'</pre></div>';
                    }
                    html += '</div>';
                })
                $('.wpaicg_modal_content').html(html);
                $('.wpaicg-overlay').show();
                $('.wpaicg_modal').show();
            }
        });
        $(document).on('click','.show_message_request', function (e){
            let el = $(e.currentTarget);
            if(el.hasClass('activeated')){
                el.removeClass('activeated');
                el.html('[details]');
                el.closest('.wpaicg_message').find('.wpaicg_request').slideUp();
            }
            else{
                el.addClass('activeated');
                el.html('[hide]');
                el.closest('.wpaicg_message').find('.wpaicg_request').slideDown();
            }
        })
    })
</script>

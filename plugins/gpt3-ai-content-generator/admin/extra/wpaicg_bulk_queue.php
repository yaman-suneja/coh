<?php
if ( ! defined( 'ABSPATH' ) ) exit;
global $wpdb;

if(isset($_GET['sub_action']) && sanitize_text_field($_GET['sub_action']) == 'delete' && isset($_GET['id']) && !empty($_GET['id'])){
    $wpaicg_delete_id = sanitize_text_field($_GET['id']);
    if(!wp_verify_nonce($_GET['_wpnonce'], 'wpaicg_delete_'.$wpaicg_delete_id)){
        die('Nonce verification failed');
    }
    $wpdb->delete($wpdb->posts,array('post_type' => 'wpaicg_tracking', 'ID' => $wpaicg_delete_id));
    $wpdb->delete($wpdb->posts,array('post_type' => 'wpaicg_bulk', 'post_parent' => $wpaicg_delete_id));
    echo '<script>window.location.href = "'.admin_url('admin.php?page=wpaicg_bulk_content&wpaicg_action=tracking').'";</script>';
    exit;
}
$wpaicg_tracking_page = isset($_GET['wpage']) && !empty($_GET['wpage']) ? sanitize_text_field($_GET['wpage']) : 1;
$wpaicg_tracking_per_page = 10;
$wpaicg_tracking_offset = ( $wpaicg_tracking_page * $wpaicg_tracking_per_page ) - $wpaicg_tracking_per_page;
$wpaicg_sum_length = $wpdb->prepare("SELECT SUM(meta_value) FROM ".$wpdb->postmeta." l LEFT JOIN ".$wpdb->posts." lp ON lp.ID=l.post_id WHERE l.meta_key='_wpaicg_generator_length' AND lp.post_parent=p.ID");
$wpaicg_sum_time = $wpdb->prepare("SELECT SUM(meta_value) FROM ".$wpdb->postmeta." l LEFT JOIN ".$wpdb->posts." lp ON lp.ID=l.post_id WHERE l.meta_key='_wpaicg_generator_run' AND lp.post_parent=p.ID");
$wpaicg_sum_tokens = $wpdb->prepare("SELECT SUM(meta_value) FROM ".$wpdb->postmeta." l LEFT JOIN ".$wpdb->posts." lp ON lp.ID=l.post_id WHERE l.meta_key='_wpaicg_generator_token' AND lp.post_parent=p.ID");
$wpaicg_trackings_sql = $wpdb->prepare("SELECT p.*,(".$wpaicg_sum_length.") as word_count,(".$wpaicg_sum_time.") as time_run,(".$wpaicg_sum_tokens.") as total_tokens FROM ".$wpdb->posts." p WHERE p.post_type='wpaicg_tracking' AND p.post_status IN ('publish','pending','draft','trash') ORDER BY p.post_date DESC LIMIT %d, %d", $wpaicg_tracking_offset, $wpaicg_tracking_per_page);
$wpaicg_trackings_total_sql = $wpdb->prepare("SELECT COUNT(*) FROM ".$wpdb->posts." p WHERE p.post_type='wpaicg_tracking' AND p.post_status IN ('publish','pending','draft','trash')");
$wpaicg_trackings = $wpdb->get_results($wpaicg_trackings_sql);
$wpaicg_trackings_total = $wpdb->get_var( $wpaicg_trackings_total_sql );
?>
<h2>Bulk Tracking</h2>
<table class="wp-list-table widefat fixed striped table-view-list comments">
    <thead>
    <tr>
        <th>Batch</th>
        <th>Status</th>
        <th>Source</th>
        <th>Duration</th>
        <th>Token</th>
        <th>Words Count</th>
        <th>Action</th>
    </tr>
    </thead>
    <tbody>
    <?php
    if($wpaicg_trackings && is_array($wpaicg_trackings) && count($wpaicg_trackings)):
        foreach($wpaicg_trackings as $wpaicg_tracking):
            ?>
            <tr>
                <td>
                    <a href="<?php echo admin_url('admin.php?page=wpaicg_bulk_content&wpaicg_track='.$wpaicg_tracking->ID)?>"><?php echo esc_html($wpaicg_tracking->post_title)?></a>
                </td>
                <td>
                    <?php
                    if($wpaicg_tracking->post_status == 'pending'){
                        echo 'Pending';
                    }
                    if($wpaicg_tracking->post_status == 'publish'){
                        echo '<span style="color: #10922c">Completed</span>';
                    }
                    if($wpaicg_tracking->post_status == 'draft'){
                        echo '<span style="color: #bb0505">Error</span>';
                    }
                    if($wpaicg_tracking->post_status == 'trash'){
                        echo '<span style="color: #bb0505">Cancelled</span>';
                    }
                    ?>
                </td>
                <td>
                    <?php
                    if(empty($wpaicg_tracking->post_mime_type) || $wpaicg_tracking->post_mime_type == 'editor'){
                        echo 'Bulk Editor';
                    }
                    if($wpaicg_tracking->post_mime_type == 'csv'){
                        echo 'CSV';
                    }
                    if($wpaicg_tracking->post_mime_type == 'rss'){
                        echo 'RSS';
                    }
                    if($wpaicg_tracking->post_mime_type == 'sheets'){
                        echo 'Google Sheets';
                    }
                    if($wpaicg_tracking->post_mime_type == 'multi'){
                        echo 'Copy-Paste';
                    }
                    ?>
                </td>
                <td><?php echo !empty($wpaicg_tracking->time_run) ? esc_html($this->wpaicg_seconds_to_time((int)$wpaicg_tracking->time_run)): ''?></td>
                <td><?php echo esc_html($wpaicg_tracking->total_tokens)?></td>
                <td><?php echo esc_html($wpaicg_tracking->word_count)?></td>
                <td><a onclick="return confirm('Are you sure?')" class="button button-link-delete button-small" href="<?php echo wp_nonce_url(admin_url('admin.php?page=wpaicg_bulk_content&wpaicg_action=tracking&sub_action=delete&id='.$wpaicg_tracking->ID), 'wpaicg_delete_'.$wpaicg_tracking->ID)?>">Delete</a></td>
            </tr>
        <?php
        endforeach;
    endif;
    ?>
    </tbody>
</table>
<div class="wpaicg-paginate">
    <?php
    echo paginate_links( array(
        'base'         => admin_url('admin.php?page=wpaicg_bulk_content&wpaicg_action=tracking&wpage=%#%'),
        'total'        => ceil($wpaicg_trackings_total / $wpaicg_tracking_per_page),
        'current'      => $wpaicg_tracking_page,
        'format'       => '?wpaged=%#%',
        'show_all'     => false,
        'prev_next'    => false,
        'add_args'     => false,
    ));
    ?>
</div>

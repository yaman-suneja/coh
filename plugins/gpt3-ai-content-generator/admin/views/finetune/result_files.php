<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<div class="wpaicg-modal-content">
<?php
if(isset($wpaicg_data) && is_array($wpaicg_data) && count($wpaicg_data)):
?>
<table class="wp-list-table widefat fixed striped table-view-list comments">
    <thead>
    <tr>
        <th>ID</th>
        <th>Purpose</th>
        <th>Created At</th>
        <th>Filename</th>
        <th>Status</th>
        <th>Download</th>
    </tr>
    </thead>
    <tbody>
    <?php
    foreach($wpaicg_data as $item){
        ?>
        <tr>
            <td><?php echo esc_html($item->id)?></td>
            <td><?php echo esc_html($item->purpose)?></td>
            <td><?php echo esc_html(date('Y-m-d H:i:s',$item->created_at))?></td>
            <td><?php echo esc_html($item->filename)?></td>
            <td><?php echo esc_html($item->status)?></td>
            <td><a download="download" href="<?php echo admin_url('admin-ajax.php?action=wpaicg_download&id='.$item->id)?>">Download</a></td>
        </tr>
        <?php
    }
    ?>
    </tbody>
</table>
<?php
else:
?>
Fine-tuning has not yet been completed.
<?php
endif;
?>
</div>

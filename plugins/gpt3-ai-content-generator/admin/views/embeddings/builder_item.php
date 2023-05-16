<?php
if ( ! defined( 'ABSPATH' ) ) exit;
$token = get_post_meta($wpaicg_embedding->ID,'wpaicg_embedding_token',true);
$wpaicg_source = get_post_meta($wpaicg_embedding->ID,'wpaicg_source',true);
$wpaicg_indexed = get_post_meta($wpaicg_embedding->ID,'wpaicg_indexed',true);
$wpaicg_completed = get_post_meta($wpaicg_embedding->ID,'wpaicg_completed',true);
$wpaicg_start = get_post_meta($wpaicg_embedding->ID,'wpaicg_start',true);
$wpaicg_error_msg = get_post_meta($wpaicg_embedding->ID,'wpaicg_error_msg',true);
?>
<tr id="wpaicg-builder-<?php echo esc_html($wpaicg_embedding->ID)?>">
    <th scope="row" class="check-column">
        <input class="cb-select-embedding" id="cb-select-<?php echo esc_html($wpaicg_embedding->ID);?>" type="checkbox" name="ids[]" value="<?php echo esc_html($wpaicg_embedding->ID);?>">
    </th>
    <td><a data-content="<?php echo esc_html($wpaicg_embedding->post_content)?>" href="javascript:void(0)" class="wpaicg-embedding-content"><?php echo esc_html($wpaicg_embedding->post_title)?></a></td>
    <td><?php echo esc_html($token)?></td>
    <td><?php echo !empty($token) ? (number_format((int)esc_html($token)*0.0004/1000,5)).'$': '--'?></td>
    <td>
        <?php
        if($wpaicg_source == 'post'){
            echo 'Post';
        }
        if($wpaicg_source == 'page'){
            echo 'Page';
        }
        if($wpaicg_source == 'product'){
            echo 'Product';
        }
        ?>
    </td>
    <td class="builder-status">
        <?php
        if($wpaicg_indexed == '' || $wpaicg_indexed == 'yes'){
            echo '<span style="color: #018b25;font-weight: bold;">'.esc_html(__('Indexed','gpt3-ai-content-generator')).'</span>';
        }
        if($wpaicg_indexed == 'error'){
            echo '<span style="color: #ff0000;font-weight: bold;">'.esc_html(__('Error','gpt3-ai-content-generator')).'</span>';
            if(!empty($wpaicg_error_msg)){
                echo '<p>'.esc_html($wpaicg_error_msg).'</p>';
            }
        }
        if($wpaicg_indexed == 'reindex'){
            echo '<span style="color: #d73e1c;font-weight: bold;">'.esc_html(__('Pending','gpt3-ai-content-generator')).'</span>';
        }
        ?>
    </td>
    <td>
        <?php
        if(!empty($wpaicg_start)){
            echo esc_html(date('d.m.Y H:i',$wpaicg_start));
        }
        ?>
    </td>
    <td>
        <?php
        if(!empty($wpaicg_completed)){
            echo esc_html(date('d.m.Y H:i',$wpaicg_completed));
        }
        ?>
    </td>
    <td>
        <?php
        if($wpaicg_indexed != 'reindex'):
        ?>
        <button data-id="<?php echo esc_html($wpaicg_embedding->ID)?>" class="button button-primary button-small wpaicg_reindex">Re-Index</button>
        <?php
        endif;
        ?>
        <button data-id="<?php echo esc_html($wpaicg_embedding->ID)?>" class="button button-link-delete button-small wpaicg_delete">Delete</button>
    </td>
</tr>

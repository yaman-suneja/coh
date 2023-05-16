<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<p><strong>Epochs: </strong><?php echo esc_html($wpaicg_data->n_epochs);?></p>
<p><strong>Batch size: </strong><?php echo esc_html($wpaicg_data->batch_size);?></p>
<p><strong>Prompt loss weight: </strong><?php echo esc_html($wpaicg_data->prompt_loss_weight);?></p>
<p><strong>Learning rate multiplier: </strong><?php echo esc_html($wpaicg_data->learning_rate_multiplier);?></p>

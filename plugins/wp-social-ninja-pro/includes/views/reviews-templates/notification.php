<?php
use WPSocialReviews\App\Services\Platforms\Reviews\Helper;
use WPSocialReviews\App\Services\Helper as GlobalHelper;
use WPSocialReviews\Framework\Support\Arr;
use WPSocialReviews\App\Services\GlobalSettings;

if(count($reviews)){
 $notification_position = Arr::get($templateMeta, 'notification_settings.notification_position');
 $reviews_on_click = Arr::get($templateMeta, 'notification_settings.display_reviews_on_click');
 $notificationDelay = Arr::get($templateMeta, 'notification_settings.notification_delay', 5000);
 $initialDelay = Arr::get($templateMeta, 'notification_settings.initial_delay', 5000);
 $delayFor     = Arr::get($templateMeta, 'notification_settings.delay_for', 5000);
 $displayDate = Arr::get($templateMeta, 'notification_settings.display_date', 'true');
 $translations =  GlobalSettings::getTranslations();
    ?>
<div id="wpsr-notification-card-<?php echo $templateId; ?>"
     class="wpsr-reviews-notification-card-wrapper <?php echo 'wpsr-'.$notification_position; echo ($reviews_on_click === 'true') ? ' wpsr-enable-cursor' : '' ?>"
     data-notification_id="<?php echo $templateId; ?>"
     data-total="<?php echo count($reviews); ?>"
     data-index="0"
     data-popup="<?php echo esc_attr($reviews_on_click);?>"
     data-notification_delay="<?php echo esc_attr($notificationDelay);?>"
     data-display_date="<?php echo esc_attr($displayDate);?>"
     data-assets_url="<?php echo esc_url(WPSOCIALREVIEWS_URL . 'assets'); ?>"
     data-initial_delay="<?php echo esc_attr($initialDelay); ?>"
     data-delay_for="<?php echo esc_attr($delayFor); ?>"
>
    <div class="wpsr-reviews-notification-card">
        <?php if($templateMeta['reviewer_image'] === 'true') { ?>
            <div class="wpsr-notification-image-wrapper">
                <div class="wpsr-reviewer-image">
                    <img src="<?php echo (!empty($reviews[0]['reviewer_img'])) ? $reviews[0]['reviewer_img'] : WPSOCIALREVIEWS_URL.'assets/images/template/review-template/placeholder-image.png'; ?>" alt="<?php echo !empty($reviews[0]['reviewer_name']) ?  $reviews[0]['reviewer_name'] : '';?>" width="50" height="50" loading="lazy">
                </div>
            </div>
        <?php } ?>
        <div class="wpsr-notification-content-wrapper">
            <div class="wpsr-review-header">
                <span class="reviewer-name"><?php echo !empty($reviews[0]['reviewer_name']) ? $reviews[0]['reviewer_name'] : ''; ?></span>
                <?php
                    $custom_notification_text = $templateMeta['notification_settings']['custom_notification_text'];
                    if($custom_notification_text){
                ?>
                 <p><?php echo str_replace('{review_rating}', "<span class='review-rating'>{$reviews[0]['rating']}</span>",  $custom_notification_text); ?></p>
                <?php } ?>
            </div>
            <div class="wpsr-notification-body">
                <div class="wpsr-rating-wrapper">
                    <div class="wpsr-rating">
                        <?php echo Helper::generateRatingIcon($reviews[0]['rating']); ?>
                    </div>
                </div>
                <?php
                    $platform_name = Arr::get($reviews, '0.platform_name', '');
                    if($platform_name !== 'custom' && $platform_name !== 'fluent_forms'){
                    echo Arr::get($translations, 'on');
                ?>
                <div class="wpsr-review-platform">
                    <img src="<?php echo WPSOCIALREVIEWS_URL. 'assets/images/icon/icon-'.$reviews[0]['platform_name'].'-small.png' ?>" alt="<?php echo $reviews[0]['platform_name']; ?>" width="20" height="20">
                </div>
                <?php } ?>
            </div>

            <?php if($displayDate === 'true') {?>
                <div class="wpsr-notification-footer">
                    <span class="review-time">
                        <?php
                        if(!empty($reviews[0]['review_time'])) {
                            $review_time = strtotime($reviews[0]['review_time']);
                            echo sprintf(__('%s ago'), human_time_diff($review_time));
                        }
                        ?>
                    </span>&nbsp;
                </div>
            <?php } ?>
        </div>
    </div>

    <?php if( $templateMeta['notification_settings']['display_close_button'] === 'true' ) {?>
        <span class="wpsr-close">
          <svg viewBox="0 0 16 16" style="fill: rgb(255, 255, 255);">
            <path d="M3.426 2.024l.094.083L8 6.586l4.48-4.479a1 1 0 011.497 1.32l-.083.095L9.414 8l4.48 4.478a1 1 0 01-1.32 1.498l-.094-.083L8 9.413l-4.48 4.48a1 1 0 01-1.497-1.32l.083-.095L6.585 8 2.106 3.522a1 1 0 011.32-1.498z"></path>
          </svg>
        </span>
    <?php } ?>
</div>
<?php }
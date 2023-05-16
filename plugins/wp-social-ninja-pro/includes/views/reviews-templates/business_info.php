<?php

use WPSocialReviews\App\Services\Platforms\Reviews\Helper;
use WPSocialReviews\Framework\Support\Arr;

extract($business_info);
extract($template_meta);
$meta_platform = Arr::get($template_meta, 'platform');
$platform_name = '';
$url = '';
$total_platforms = count($platforms);

if($total_platforms === 1) {
    $keys = array_keys($platforms);
    if(!empty($keys)) {
        $key = $keys[0];
        $platform = $platforms[$key];
        $url = $platform['url'];
        $platform_name = $platform['platform_name'];
    }
}
$platform_name_class = Helper::platformDynamicClassName($business_info);

$wrapperClass = ($template_meta['templateType'] === 'badge' || $template_meta['templateType'] === 'notification') ? 'wpsr-display-block' : '';
echo '<div class="wpsr-row">';
echo '<div class="wpsr-business-info '.$wrapperClass .' '. esc_attr($platform_name) . ' '.$platform_name_class.'">';

echo '<div class="wpsr-business-info-left">';

if(empty($custom_title_text)) {
    $rating_text = sizeof($meta_platform) > 1 ? __('Overall Rating', 'wp-social-ninja-pro') : __(' Rating', 'wp-social-ninja-pro');
} else {
    $rating_text = strlen($custom_title_text) ? $custom_title_text : '';
}

if ((!empty($platforms) && is_array($platforms)) && $display_header_business_name === true && $total_platforms > 1) {
    echo '<div class="wpsr-business-info-paltforms">';
    $count = [];
    foreach ($platforms as $index => $platform) {
        $platformName = Arr::get($platform, 'platform_name');
        if(isset($count[$platformName]) && $count[$platformName]){
            continue;
        }
        $count[$platformName] = 1;
        $image_size = sizeof($meta_platform) > 1 ?  'small' : '';
        $small_icon = Helper::platformIcon($platformName, $image_size);
        echo '<a href="' . esc_url($platform['url']) . '" target="_blank">';
        echo '<img src="'.esc_url($small_icon).'" alt="' . esc_attr($platformName) . '">';
        echo '</a>';
    }

    echo '<span>' . $rating_text . '</span>';
    echo '</div>';
}


if (!empty($platform_name) && $display_header_business_name === true && $total_platforms === 1) {
    $large_icon = Helper::platformIcon($platform_name, '');
    echo '<div class="wpsr-business-info-logo">';
    echo '<img src="'.esc_url($large_icon).'" alt="' . $platform_name . '"/>';
    echo '<span>' . $rating_text . '</span>';
    echo '</div>';
}

echo '<div class="wpsr-rating-and-count">';
if (isset($average_rating) && !empty($average_rating) && $display_header_rating === true) {
    echo '<span class="wpsr-total-rating">' . number_format($average_rating, 1) . '</span>';
    if( !($isBooking) ) {
        echo '<span class="wpsr-rating">' . Helper::generateRatingIcon(number_format($average_rating, 1)) . '</span>';
    }
}

if (isset($total_rating) && !empty($total_rating) && $display_header_reviews === true && strlen($custom_number_of_reviews_text)) {
    echo '<div class="wpsr-total-reviews">'.
         str_replace('{total_reviews}','<span>'. number_format($total_rating, 0) .'</span>', $custom_number_of_reviews_text)
        .'</div>';
}

echo '</div>';
echo '</div>';
do_action('wpsocialreviews/render_reviews_write_a_review_btn', $template_meta, $templateType, $business_info, $templateId, $translations);
echo '</div>';
echo '</div>';
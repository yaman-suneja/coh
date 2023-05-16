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
$html          = '';

$html .= '<div class="wpsr-business-info-right">';
$html .= (isset($url) && $total_platforms === 1) ? '<a target="_blank" class="wpsr-write-review" href="' . esc_url($url) . '">'.$custom_write_review_text. '</a>' : '';

if (!empty($platforms) && $total_platforms > 1) {
    $html .= '<div class="wpsr-write-review-modal-wrapper">';
    $html .= '<a class="wpsr-write-review wpsr-write-review-modal-btn">' . $custom_write_review_text . '</a>';
    $html .= '<div class="wpsr-write-review-modal">';
    $html .= '<p>' . Arr::get($translations, 'leave_a_review') . '</p>';

    if ((!empty($platforms) && is_array($platforms))) {
        $html .= '<div class="wpsr-business-info-paltforms-url">';
        foreach ($platforms as $platform) {
            $html .= '<a href="' . esc_url($platform['url']) . '" target="_blank">';
            $icon_small = Helper::platformIcon($platform['platform_name'], 'small');
            $html .= '<img src="'.esc_url($icon_small).'" alt="' . esc_attr($platform['platform_name']) . '">';
            $html .= '<div class="wpsr-paltforms-url">';
            $html .= '<span class="wpsr-platform">'.$platform['name'].'</span>';
            $html .= '<span class="wpsr-url">' . $platform['url'] . '</span>';
            $html .= '</div>';
            $html .= '</a>';
        }
        $html .= '</div>';
    }
    $html .= '</div>';
    $html .= '</div>';
}
$html .= '</div>';
echo $html;
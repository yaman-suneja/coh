<?php
use WPSocialReviews\App\Services\Platforms\Reviews\Helper;
use WPSocialReviews\Framework\Support\Arr;

$platform_name  = Arr::get($business_info, 'platform_name', '');
$badge_position = Arr::get($badge_settings, 'badge_position', 'default');
$custom_num_of_reviews_text     = Arr::get($badge_settings, 'custom_num_of_reviews_text');

$classes = [
    $badge_settings['display_reviews_on_click'] === 'true' ? 'wpsr-reviews-badge-btn' : 'wpsr-reviews-badge-html',
    'wpsr-'.$badge_settings['badge_position']
];

$attrs = [
    'data-badge_id' =>  $badge_settings['display_reviews_on_click'] === 'true' ? $templateId : '',
    'id'            =>  $badge_settings['display_reviews_on_click'] === 'true' ? 'wpsr-reviews-badge-'.$templateId : '',
    'class'         =>  implode( ' ', $classes),
    'href'          =>  $badge_settings['display_reviews_on_click'] === 'false' && isset($business_info['url']) ? $business_info['url'] : (!empty($business_info['platforms']) && $badge_settings['display_reviews_on_click'] === 'false' ? '' : '#'),
    'target'        =>  $badge_settings['display_reviews_on_click'] === 'false' && $platform_name ? '_blank' : ''
];

$attr = '';
foreach ($attrs as $key => $value){
    if($value){
        $attr .= $key .'="'.$value.'" ';
    }
}

$platform_name_class = Helper::platformDynamicClassName($business_info);

$html = '';
$html .= '<div class="wpsr-reviews-badge-wrapper '.esc_attr($platform_name_class).' wpsr-reviews-'.esc_attr($badge_settings['template']).' '.esc_attr($platform_name_class).'">';
$html .= ( $badge_settings['display_reviews_on_click'] === 'false' && (!empty($business_info['platforms']) && is_array($business_info['platforms'])) ) ? '<div '.$attr.'>' : '<a '.$attr.'>';
$html .= '<div class="wpsr-reviews-badge-wrapper-inner">';

$html .= '<div class="wpsr-business-info-logo">';
    if($badge_settings['display_platform_icon'] === 'true'){
        $image_size = $badge_settings['template'] === 'badge2' ? 'small' : '';
        if( $platform_name ){
            $icon = Helper::platformIcon($platform_name, $image_size);
            $html .= '<img src="'.esc_url($icon).'" alt="' . $platform_name . '"/>';
        }
        if(!empty($business_info['platforms']) && is_array($business_info['platforms'])){
            $html .= '<div class="wpsr-business-info-paltforms">';
            $count = [];
            foreach ($business_info['platforms'] as $index => $platform) {
                $platformName = Arr::get($platform, 'platform_name');
                if(isset($count[$platformName]) && $count[$platformName]){
                    continue;
                }
                $count[$platformName] = 1;
                $icon_small = Helper::platformIcon($platform['platform_name'], 'small');
                $html .= '<img src="'.esc_url($icon_small).'" alt="' . esc_attr($platform['platform_name']) . '">';
            }
            $html .= '</div>';
        }
    }
    if(!empty($badge_settings['custom_title']) && $badge_settings['template'] === 'badge1'){
        $html .= '<span class="wpsr-reviews-badge-title">' . $badge_settings['custom_title'] . '</span>';
    }
$html .= '</div>';

$html .= '<div class="wpsr-rating-and-count">';
    if(!empty($badge_settings['custom_title']) && $badge_settings['template'] === 'badge2'){
        $html .= '<span class="wpsr-reviews-badge-title">' . $badge_settings['custom_title'] . '</span>';
    }
    if (Arr::get($business_info, 'average_rating')) {
        $html .= '<div class="wpsr-total-rating">' . number_format($business_info['average_rating'],
                1) . '<span class="wpsr-rating">' . Helper::generateRatingIcon(number_format($business_info['average_rating'], 1)) . '</span></div>';
    }

    if (!empty($custom_num_of_reviews_text)) {
        if(Arr::get($business_info, 'total_rating') && strpos($custom_num_of_reviews_text, '{reviews_count}') !== false){
            $custom_num_of_reviews_text = str_replace('{reviews_count}', number_format($business_info['total_rating'], 0), $custom_num_of_reviews_text);
        }
        $html .= '<div class="wpsr-total-reviews">'. __($custom_num_of_reviews_text, 'wp-social-ninja-pro') . '</div>';
    }
$html .= '</div>';

$html .= '</div>';
$html .= ( $badge_settings['display_reviews_on_click'] === 'false' && (!empty($business_info['platforms']) && is_array($business_info['platforms'])) ) ? '</div>' : '</a>';

$html .= '</div>';
echo $html;
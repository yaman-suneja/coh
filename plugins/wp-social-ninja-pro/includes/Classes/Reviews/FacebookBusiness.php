<?php

namespace WPSocialReviewsPro\Classes\Reviews;

use WpFluent\Exception;
use WPSocialReviews\App\Services\Platforms\Reviews\BaseReview;
use WPSocialReviews\Framework\Support\Arr;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Handle Facebook Reviews Page Id and Api Key
 * @since 1.0.0
 */
class FacebookBusiness extends BaseReview
{
    private $remoteBaseUrl = 'https://graph.facebook.com/';
    private $place_id = null;
    private $api_key = null;

    public function __construct()
    {
        parent::__construct(
            'facebook',
            'wpsr_reviews_facebook_settings',
            'wpsr_facebook_reviews_update'
        );
    }

    public function handleCredentialSave($settings = array())
    {
        $apiKey  = $settings['api_key'];
        $placeId = $settings['source_id'];
        try {
            $businessInfo = $this->verifyCredential($apiKey, $placeId);

            // save caches when auto sync is on
            $apiSettings = get_option('wpsr_facebook_global_settings');
            if(Arr::get($apiSettings, 'global_settings.auto_syncing') === 'true'){
                $this->saveCache();
            }
            wp_send_json_success([
                'message'       => __('Facebook Business Reviews Successfully Saved', 'wp-social-ninja-pro'),
                'business_info' => $businessInfo
            ], 200);

        } catch (\Exception $exception) {
            wp_send_json_error([
                'message' => $exception->getMessage()
            ], 423);
        }
    }

    public function pushValidPlatform($platforms)
    {
        $businessInfos    = $this->getBusinessInfo();
        if ($businessInfos && sizeof($businessInfos) > 0) {
            $platforms['facebook'] = __('Facebook', 'wp-social-ninja-pro');
        }
        return $platforms;
    }

    public function verifyCredential($accessToken, $placeId)
    {
        $data = $this->fetchRemoteReviews($accessToken, $placeId);
        if (is_wp_error($data)) {
            throw new \Exception($data->get_error_message());
        }

        $this->place_id = $placeId;
        $this->api_key = $accessToken;
        $this->saveApiSettings($accessToken, $placeId);

        $reviews = isset($data['ratings']['data']) ? $data['ratings']['data'] : array();
        if (empty($reviews)) {
            throw new \Exception(__('We could not find any reviews from this page.', 'wp-social-ninja-pro'));
        }

        $this->syncRemoteReviews($reviews);
        $business_info = $this->saveBusinessInfo($data);
        update_option('wpsr_reviews_facebook_business_info', $business_info, 'no');

        return $business_info;
    }

    public function fetchRemoteReviews($accessToken, $pageId)
    {
        $total_reviews = apply_filters('wpsocialreviews/facebook_reviews_limit', 300);
        $api_url       = $this->remoteBaseUrl . $pageId . "?access_token=" . $accessToken . "&fields=name,link,ratings.fields(has_review,reviewer{id,name,picture.width(120).height(120)},created_time,rating,recommendation_type,review_text,open_graph_story{id}).limit(" . $total_reviews . "),overall_star_rating,rating_count";
        $response      = wp_remote_get($api_url);

        if (is_wp_error($response)) {
            $message = $response->get_error_message();
            return new \WP_Error(423, $message);
        }

        if ($response['response']['message'] != 'OK') {
            return new \WP_Error(423,
                Arr::get($response, 'error_message', __('Please enter a valid page id', 'wp-social-ninja-pro')));
        }

        return json_decode(wp_remote_retrieve_body($response), true);
    }

    public function formatData($review, $index)
    {
        return [
            'platform_name'       => $this->platform,
            'source_id'           => $this->place_id,
            'reviewer_name'       => isset($review['reviewer']['name']) ? $review['reviewer']['name'] : '',
            'review_title'        => $this->platform . '_' . $this->place_id.($index + 1),
            'reviewer_url'        => isset($review['reviewer']['id']) ? 'https://facebook.com/' . $review['reviewer']['id'] : '',
            'reviewer_img'        => isset($review['reviewer']['id']) ? 'https://graph.facebook.com/'.$review['reviewer']['id'].'/picture?access_token='.$this->api_key.'&type=square&width=100&height=100' : '',
            'reviewer_text'       => $review['review_text'],
            'rating'              => $review['recommendation_type'] === 'positive' ? 5 : 1,
            'review_time'         => date('Y-m-d H:i:s', strtotime($review['created_time'])),
            'recommendation_type' => $review['recommendation_type'],
            'review_approved'     => 1,
            'updated_at'          => date('Y-m-d H:i:s'),
            'created_at'          => date('Y-m-d H:i:s')
        ];
    }

    public function saveBusinessInfo($data = array())
    {
        $businessInfo = [];
        $infos         = $this->getBusinessInfo();

        $totalRatingValue = 0;
        if (Arr::get($data, 'ratings.data')) {
            foreach ($data['ratings']['data'] as $index => $review) {
                $recommendation_type = Arr::get($review, 'recommendation_type');
                $totalRatingValue    += $recommendation_type === 'positive' ? 5 : 1;
            }
        }

        if ($data && is_array($data)) {
            $ratings = Arr::get($data, 'ratings.data');
            $total_rating   = $ratings ? count($ratings) : null;
            $average_rating = $total_rating > 0 && $totalRatingValue > 0 ? $totalRatingValue / $total_rating : 0;
            $business_id    = Arr::get($data, 'id');

            $businessInfo['place_id']        = $business_id;
            $businessInfo['name']            = Arr::get($data, 'name');
            $businessInfo['url']             = Arr::get($data, 'link') . '/reviews';
            $businessInfo['address']         = Arr::get($data, '');
            $businessInfo['average_rating']  = round($average_rating, 2);
            $businessInfo['total_rating']    = $total_rating;
            $businessInfo['phone']           = Arr::get($data, '');
            $businessInfo['platform_name']   = $this->platform;
            $businessInfo['status']          = true;
            $infos[$business_id]             =  $businessInfo;
        }
        return $infos;
    }

    public function getBusinessInfo()
    {
        return get_option('wpsr_reviews_facebook_business_info');
    }

    public function getAdditionalInfo()
    {
        return get_option('wpsr_reviews_facebook_pages_list');
    }

    public function saveApiSettings($accessToken, $placeId)
    {
        $apiSettings  = $this->getApiSettings();
        if($accessToken && $placeId){
            $apiSettings[$placeId]['api_key'] = $accessToken;
            $apiSettings[$placeId]['place_id'] = $placeId;
        }
        return update_option($this->optionKey, $apiSettings, 'no');
    }

    public function getApiSettings()
    {
        $settings = get_option($this->optionKey);
        return $settings;
    }

    public function saveConfigs($accessToken = null)
    {
        try {
            $api_url  = $this->remoteBaseUrl . "me/accounts?limit=500&access_token=" . $accessToken;
            $response = wp_remote_get($api_url);
            if (is_wp_error($response)) {
                return $response;
            }
            $data = json_decode(wp_remote_retrieve_body($response), true);
            update_option('wpsr_reviews_facebook_pages_list', $data, 'no');
            $settings = get_option('wpsr_reviews_facebook_pages_list');
            wp_send_json_success(
                [
                    'settings' => $settings,
                    'message'  => __('You are Successfully Verified', 'wp-social-ninja-pro')
                ],
                200
            );
        } catch (\Exception $exception) {
            wp_send_json_error([
                'message' => $exception->getMessage()
            ], 423);
        }
    }

    public function doCronEvent()
    {
	    $expiredCaches = $this->cacheHandler->getExpiredCaches();
        $settings = get_option($this->optionKey);
        if (!empty($settings) && is_array($settings)) {
            foreach ($settings as $setting){
	            if (in_array($setting['place_id'], $expiredCaches)) {
		            $apiKey  = Arr::get($setting, 'api_key', '');
		            $placeId  = Arr::get($setting, 'place_id', '');
                    if($apiKey && $placeId){
                        try {
                            $this->verifyCredential($apiKey, $placeId);
                        } catch (\Exception $exception){
                            error_log($exception->getMessage());
                        }
                    }
		            $this->cacheHandler->createCache(
			            'wpsr_reviews_' . $this->platform . '_business_info_' . $setting['place_id'],
			            $setting['place_id']
		            );
	            }
            }
        }
    }
}



<?php

namespace WPSocialReviewsPro\Classes\Reviews;

use WPSocialReviews\Framework\Support\Arr;
use WPSocialReviews\App\Services\Libs\SimpleDom\Helper;
use WPSocialReviews\App\Services\Platforms\Reviews\BaseReview;
use WPSocialReviews\App\Services\Platforms\Reviews\Helper as ReviewsHelper;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Handle Aliexpress Reviews
 * @since 1.0.0
 */
class Aliexpress extends BaseReview
{
    private $remoteBaseUrl = 'https://aliexpress.com/';
    private $placeId = null;

    public function __construct()
    {
        parent::__construct(
            'aliexpress',
            'wpsr_reviews_aliexpress_settings',
            'wpsr_aliexpress_reviews_update'
        );
    }

    public function handleCredentialSave($credentials)
    {
        $downloadUrl = $credentials['url_value'];
        try {
            $businessInfo = $this->verifyCredential($downloadUrl);
            $message = ReviewsHelper::getNotificationMessage($businessInfo, $this->placeId);
            if (Arr::get($businessInfo, 'total_fetched_reviews') && Arr::get($businessInfo, 'total_fetched_reviews') > 0) {
                unset($businessInfo['total_fetched_reviews']);

                // save caches when auto sync is on
                $apiSettings = get_option('wpsr_aliexpress_global_settings');
                if(Arr::get($apiSettings, 'global_settings.auto_syncing') === 'true'){
                    $this->saveCache();
                }
                update_option('wpsr_reviews_aliexpress_business_info', $businessInfo, 'no');
            }

            wp_send_json_success([
                'message'       => $message,
                'business_info' => $businessInfo
            ], 200);
        } catch (\Exception $exception) {
            wp_send_json_error([
                'message' => $exception->getMessage()
            ], 423);
        }
    }

    public function verifyCredential($downloadUrl)
    {
        if (empty($downloadUrl)) {
	        throw new \Exception(
		        __('URL field should not be empty!', 'wp-social-ninja-pro')
	        );
        }

        //check weather a url is valid or not
        if (filter_var($downloadUrl, FILTER_VALIDATE_URL)) {
            ini_set('memory_limit', '600M');

            $business_url = strtok($downloadUrl, '?');
            $this->remoteBaseUrl = $business_url;

            $fileUrlContents = wp_remote_retrieve_body(wp_remote_get($downloadUrl));
            if (empty($fileUrlContents)) {
	            throw new \Exception(
		            __('Can\'t fetch reviews due to slow network, please try again', 'wp-social-ninja-pro')
	            );
            }

            $html = Helper::str_get_html($fileUrlContents);

            $scripts = $html->find('script');
            $script  = false;

            foreach ($scripts as $s) {
                if (strpos($s->innertext, 'runParams') !== false) {
                    $script = $s->innertext;
                }
            }

            preg_match('/sellerAdminSeq/', trim($script), $matches, PREG_OFFSET_CAPTURE);
            $stringPos      = $matches[0][1];
            $stringVal      = substr(trim($script), $stringPos, 100);
            $stringSegments = explode(',', $stringVal);
            //find product id and owner member id 
            $productId     = (int)filter_var($business_url, FILTER_SANITIZE_NUMBER_INT);
            $ownerMemberId = (int)filter_var($stringSegments[0], FILTER_SANITIZE_NUMBER_INT);
            $this->placeId = $productId;

            $matches = [];
            //find product title
            preg_match('/ogTitle/', trim($script), $matches, PREG_OFFSET_CAPTURE);
            $stringPos      = $matches[0][1];
            $stringVal      = substr(trim($script), $stringPos, 400);
            $stringSegments = explode(',', $stringVal);
            $titleString    = str_replace('ogTitle":', '', $stringSegments[0]);
            $titleFiltered  = str_replace('"', "", $titleString);
            $tittleArray    = explode('|', $titleFiltered);
            $title          = $tittleArray[1];

            $reviews = array();
            if (!empty($productId) && !empty($ownerMemberId)) {
                $curUrl          = 'https://feedback.aliexpress.com/display/productEvaluation.htm?v=2&productId=' . $productId . '&ownerMemberId=' . $ownerMemberId;
                $businessDetails = $this->getReviewDetails($title, $curUrl);

                for ($x = 1; $x <= 5; $x++) {
                    $curUrl = 'https://feedback.aliexpress.com/display/productEvaluation.htm?v=2&page=' . $x . '&productId=' . $productId . '&ownerMemberId=' . $ownerMemberId;
                    $fileUrlContents = wp_remote_retrieve_body(wp_remote_get($curUrl));

                    if (empty($fileUrlContents)) {
	                    throw new \Exception(
		                    __('Can\'t fetch reviews due to slow network, please try again', 'wp-social-ninja-pro')
	                    );
                    }

                    $html            = Helper::str_get_html($fileUrlContents);
                    if ($html->find('div.feedback-container', 0)) {
                        $reviewContents = $html->find('.feedback-item');
                        foreach ($reviewContents as $review) {
                            $reviews[] = [
                                'reviewer_name'   => $this->getReviewerName($review),
                                'reviewer_text'   => $this->getReviewText($review),
                                'reviewer_rating' => $this->getReviewRating($review),
                                'reviewer_url'    => 'https:' . $this->getReviewerUrl($review, $downloadUrl),
                                'review_date'     => date('Y-m-d H:i:s', strtotime($this->getReviewDate($review))),
                                'source_id'       => $this->placeId,
                            ];
                        }
                    } else {
                        break;
                    }
                }

                if (!empty($reviews)) {
                    $this->saveApiSettings([
                        'api_key'   => '703669e4-4907-4b21-90b5-f1b59354baf2',
                        'place_id'  => $this->placeId,
                        'url_value' => $downloadUrl
                    ]);

                    $this->syncRemoteReviews($reviews);
                    $businessInfo = $this->saveBusinessInfo($businessDetails);
                    $businessInfo['total_fetched_reviews'] = count($reviews);
                    return $businessInfo;
                } else {
	                throw new \Exception(
		                __('No reviews Found!', 'wp-social-ninja-pro')
	                );
                }
            } else {
	            throw new \Exception(
		            __('AliExpress reviews not found!', 'wp-social-ninja-pro')
	            );
            }
        } else {
	        throw new \Exception(
		        __('Please enter a valid url!', 'wp-social-ninja-pro')
	        );
        }
    }

    public function pushValidPlatform($platforms)
    {
        $settings    = $this->getApiSettings();
        if (!isset($settings['data']) && sizeof($settings) > 0) {
            $platforms['aliexpress'] = __('AliExpress', 'wp-social-ninja-pro');
        }
        return $platforms;
    }

    public function getReviewDetails($title, $downloadUrl)
    {
        $fileUrlContents = wp_remote_retrieve_body(wp_remote_get($downloadUrl));

        if (empty($fileUrlContents)) {
	        throw new \Exception(
		        __('Can\'t fetch reviews due to slow network, please try again', 'wp-social-ninja-pro')
	        );
        }

        //fix for lazy load base64 ""
        $fileUrlContents = str_replace('=="', '', $fileUrlContents);

        $html = Helper::str_get_html($fileUrlContents);

        $businessName = ReviewsHelper::removeSpecialChars($title);

        $businessDetails                   = array();
        $businessDetails['business_name']  = $businessName;
        $businessDetails['average_rating'] = strip_tags($this->getAverageRating($html));
        $businessDetails['review_url']     = $this->remoteBaseUrl;
        $businessDetails['total_reviews']  = $this->getTotalRating($html);

        return $businessDetails;
    }

    public function formatData($review, $index)
    {
        return [
            'platform_name' => $this->platform,
            'source_id'     => Arr::get($review, 'source_id'),
            'reviewer_name' => Arr::get($review, 'reviewer_name'),
            'review_title'  => $this->platform . '_' . ($index + 1),
            'reviewer_url'  => Arr::get($review, 'reviewer_url'),
            'reviewer_img'  => Arr::get($review, 'reviewer_image'),
            'reviewer_text' => Arr::get($review, 'reviewer_text'),
            'rating'        => intval(Arr::get($review, 'reviewer_rating')),
            'review_time'   => Arr::get($review, 'review_date'),
            'review_approved' => 1,
            'updated_at'    => date('Y-m-d H:i:s'),
            'created_at'    => date('Y-m-d H:i:s')
        ];
    }

    public function saveBusinessInfo($data = array())
    {
        $businessInfo = [];
        $infos        = $this->getBusinessInfo();
        if ($data && is_array($data)) {
            $placeId                          = $this->placeId;
            $businessInfo['place_id']         = $placeId;
            $businessInfo['name']             = Arr::get($data, 'business_name');
            $businessInfo['url']              = Arr::get($data, 'review_url');
            $businessInfo['address']          = '';
            $businessInfo['average_rating']   = Arr::get($data, 'average_rating');
            $businessInfo['total_rating']     = Arr::get($data, 'total_reviews');
            $businessInfo['phone']            = '';
            $businessInfo['platform_name']    = $this->platform;
            $businessInfo['status']           = true;
            $infos[$placeId]                  =  $businessInfo;
        }
        return $infos;
    }

    public function getBusinessInfo()
    {
      return get_option('wpsr_reviews_aliexpress_business_info');
    }

    public function saveApiSettings($settings)
    {
        $apiKey       = $settings['api_key'];
        $placeId      = $settings['place_id'];
        $businessUrl  = $settings['url_value'];
        $apiSettings  = $this->getApiSettings();

        if(isset($apiSettings['data']) && !$apiSettings['data']) {
            $apiSettings = [];
        }

        if($apiKey && $placeId && $businessUrl){
            $apiSettings[$placeId]['api_key'] = $apiKey;
            $apiSettings[$placeId]['place_id'] = $placeId;
            $apiSettings[$placeId]['url_value'] = $businessUrl;
        }
        return update_option($this->optionKey, $apiSettings, 'no');
    }

    public function getApiSettings()
    {
        $settings = get_option($this->optionKey);
        if (!$settings) {
            $settings = [
                'api_key'   => '',
                'place_id'  => '',
                'url_value' => '',
                'data'      => false
            ];
        }
        return $settings;
    }

    public function getAdditionalInfo()
    {
        return [];
    }

    //get business info
    public function getTotalRating($html)
    {
        $totalRating = '';
        if ($html->find('div.customer-reviews', 0)) {
            $totalRatingString = $html->find('div.customer-reviews', 0)->innertext;
            $totalRating       = (int)filter_var($totalRatingString, FILTER_SANITIZE_NUMBER_INT);
        }
        return $totalRating;
    }

    public function getAverageRating($html)
    {
        //find average rating
        $avgRating = '';
        if ($html->find('span.rate-score-number', 0)) {
            $avgRating = $html->find('span.rate-score-number', 0)->find('b', 0);
        }
        return $avgRating;
    }

    //get review info
    public function getReviewerName($review)
    {
        //find aliexpress reviewer name
        $reviewerName = '';
        if ($review->find('span.user-name', 0)) {
            if ($review->find('span.user-name', 0)->find('a', 0)) {
                $reviewerName = $review->find('span.user-name', 0)->find('a', 0)->innertext;
            } else {
                $reviewerName = '';
            }
        }
        return $reviewerName;
    }

    public function getReviewerUrl($review, $downloadUrl)
    {
        //find aliexpress reviewer url
        $reviewerUrl = '';
        if ($review->find('span.user-name', 0)) {
            if ($review->find('span.user-name', 0)->find('a', 0)) {
                $reviewerUrl = $review->find('span.user-name', 0)->find('a', 0)->href;
            } else {
                $reviewerUrl = $downloadUrl;
            }
        }
        return $reviewerUrl;
    }

    public function getReviewText($review)
    {
        //find aliexpress reviewer text
        $reviewerText = '';
        if ($review->find('.buyer-feedback')) {
            if ($review->find('.buyer-feedback', 0)->find('span', 0)) {
                $reviewerText = $review->find('.buyer-feedback', 0)->find('span', 0)->innertext;
            } else {
                if ($review->find('dt.buyer-addition-feedback', 0)) {
                    $reviewerText = trim($review->find('dt.buyer-addition-feedback', 0)->innertext);
                    $reviewFeed   = $reviewerText;
                    preg_match('/span/', $reviewFeed, $matchesStr, PREG_OFFSET_CAPTURE);
                    $stringPos    = $matchesStr[0][1];
                    $reviewerText = substr_replace($reviewerText, '', $stringPos - 1,
                        strlen($reviewerText));
                    if ($reviewerText) {
                        $reviewerText = trim($reviewerText);
                    } else {
                        $reviewerText = '';
                    }
                }
            }
        }
        return $reviewerText;
    }

    public function getReviewRating($review)
    {
        //find aliexpress start rating
        $reviewerRating = '';
        if ($review->find('.star-view', 0)) {
            $reviewerRatingAttr     = $review->find('.star-view', 0)->find('span',
                0)->getAttribute('style');
            $reviewerRatingStyleVal = intval(str_replace("width:", "", $reviewerRatingAttr));
            $reviewerRating         = intVal($reviewerRatingStyleVal / 20);
        }
        return $reviewerRating;
    }

    public function getReviewDate($review)
    {
        //find review date star rating
        $reviewDate = '';
        if ($review->find('span.r-time-new', 0)) {
            $reviewDate = $review->find('span.r-time-new', 0)->innertext;
        }
        return $reviewDate;
    }
}

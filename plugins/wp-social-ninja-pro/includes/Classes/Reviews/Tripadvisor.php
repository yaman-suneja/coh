<?php

namespace WPSocialReviewsPro\Classes\Reviews;

use WPSocialReviews\App\Services\Platforms\Reviews\BaseReview;
use WPSocialReviews\App\Services\Libs\SimpleDom\Helper;
use WPSocialReviews\App\Services\Platforms\Reviews\Helper as ReviewsHelper;
use WPSocialReviews\Framework\Support\Arr;
use WPSocialReviewsPro\Classes\Helper as ProHelper;

if (!defined('ABSPATH')) {
    exit;
}

//works in hotels, restaurants, vacation, attraction product and attraction reviews

/**
working platforms
 * hotels
 * restaurants
 * attraction
 */

/**
 * Handle Tripadvisor Reviews
 * @since 1.0.0
 */
/* Works well for restaurants and vacation rental and hotels reviews*/

class Tripadvisor extends BaseReview
{
    private $remoteBaseUrl = 'https://tripadvisor.com';
    private $placeId = null;

    public function __construct()
    {
        parent::__construct(
            'tripadvisor',
            'wpsr_reviews_tripadvisor_settings',
            'wpsr_tripadvisor_reviews_update'
        );
    }

    public function handleCredentialSave($settings = array())
    {
        $downloadUrl = $settings['url_value'];
        try {
            $businessInfo = $this->verifyCredential($downloadUrl);
            $message = ReviewsHelper::getNotificationMessage($businessInfo, $this->placeId);

            if (Arr::get($businessInfo, 'total_fetched_reviews') && Arr::get($businessInfo, 'total_fetched_reviews') > 0) {
                unset($businessInfo['total_fetched_reviews']);

                // save caches when auto sync is on
                $apiSettings = get_option('wpsr_tripadvisor_global_settings');
                if(Arr::get($apiSettings, 'global_settings.auto_syncing') === 'true'){
                    $this->saveCache();
                }
                update_option('wpsr_reviews_tripadvisor_business_info', $businessInfo, 'no');
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

        //make sure file get contents is turned on for this host
        $errorMessage     = '';
        $showUserReviews  = false;

        //make sure you have valid url, if not display message
        if (filter_var($downloadUrl, FILTER_VALIDATE_URL)) {
            ini_set('memory_limit', '600M');
            if (strpos($downloadUrl, '#')) {
                $downloadUrl = explode('#', $downloadUrl);
                $downloadUrl = $downloadUrl[0];
            }
            $stripVariableUrl = strtok($downloadUrl, '?');

            $urlArray = $this->downloadBusinessInfo($stripVariableUrl);

            if (Arr::get($urlArray, 'place_id', false)) {
                $this->placeId = Arr::get($urlArray, 'place_id').'-'.$this->platform;
            }
            $businessInfo = $this->saveBusinessInfo($urlArray);

            //find urls for pagination
            $tripadvisorUrl = array();
            if (strpos($downloadUrl, 'Restaurant_Review') !== false || strpos($downloadUrl,
                    'VacationRentalReview') !== false || strpos($downloadUrl, 'Restaurant_Review') !== false
                || strpos($downloadUrl, 'AttractionProductReview') !== false ||
                strpos($downloadUrl,'Hotel_Review') !== false || strpos($downloadUrl, 'Attraction_Review') !== false
            ) {
                $counter = 10;
                if (strpos($downloadUrl, 'Restaurant_Review') !== false
                    || strpos($downloadUrl, 'AttractionProductReview') !== false  || strpos($downloadUrl, 'Attraction_Review') !== false) {
                    $counter = 10;
                    $downloadUrl = str_replace('-or10-', '', $downloadUrl);
                }

                if (strpos($downloadUrl, 'VacationRentalReview') !== false || strpos($downloadUrl, 'Hotel_Review') !== false) {
                    $counter = 5;
                }

                //attraction reviews for 5 pagination
                if (isset($urlArray['page2']) && !empty($urlArray['page2']) && strpos($urlArray['page2'], 'Attraction_Review') !== false && strpos($urlArray['page2'],'or5') !== false) {
                    $counter = 5;
                }

                $url = str_replace('Reviews-or5', 'Reviews', $downloadUrl);
                $url = str_replace('Reviews-or10', 'Reviews', $url);
                $tripadvisorUrl[0] = $url;

                for ($i = 1; $i < 5; $i++) {
                    $paginateHtml     = "-or" . ($i * $counter) . ".html";
                    $tripadvisorUrl[] = str_replace(".html", $paginateHtml, $url);
                }
            } else {
                $tripadvisorUrl[0] = $urlArray['page1'];
                if ($urlArray['page2'] != "") {
                    $tripadvisorUrl[1] = $urlArray['page2'];
                } else {
                    $tripadvisorUrl[1] = '';
                }
            }

            sleep(rand(0, 2));

            $tripadvisorUrl = array_filter($tripadvisorUrl);
            $reviews        = [];
            $n              = 1;

            foreach ($tripadvisorUrl as $index => $urlValue) {
                $fileUrlContents = wp_remote_retrieve_body(wp_remote_get($urlValue));

                if (empty($fileUrlContents) && $index >= 1) {
                    break;
                }

                if (empty($fileUrlContents)) {
	                throw new \Exception(
		                __('Can\'t fetch reviews due to slow network, please try again', 'wp-social-ninja-pro')
	                );
                }

                //fix for lazy load base64 ""
                $fileUrlContents = str_replace('=="', '', $fileUrlContents);
                $html            = Helper::str_get_html($fileUrlContents);
                $i               = 1;

                //find lazyload image js variable and convert to array #\slazyImgs\s*=\s*(.*?);\s*$#s
                $startStringPos = stripos("$html", "var lazyImgs = [") + 16;
                $choppedStr     = substr("$html", $startStringPos);
                $endStringPos   = stripos("$choppedStr", "]");
                $finalString    = trim(substr("$html", $startStringPos, $endStringPos));
                $finalString    = str_replace(":true", ':"true"', $finalString);
                $finalString    = "[" . str_replace(":false", ':"false"', $finalString) . "]";
                $jsonLazyImg    = json_decode($finalString, true);

                //find next button url for next page reviews
                $pagination  = '';
                $pageNumbers = '';
                $pageUrls    = array();
                if ($html->find('div.pagination', 0)) {
                    $pagination = $html->find('div.pagination', 0);
                    if ($pagination) {
                        $pageNumbers = $html->find('div.pageNumbers', 0);
                    }
                }

                $reviewContainerDiv = array();
                //fix for hotel reviews..
                if ($html->find('div.hotels-hotel-review-community-content-review-list-parts-SingleReview__reviewContainer--2LYmA')) {
                    $reviewContainerDiv = $html->find('div.hotels-hotel-review-community-content-review-list-parts-SingleReview__reviewContainer--2LYmA');
                } else {
                    if ($html->find('div.review-container', 0)) {
                        $reviewContainerDiv = $html->find('div.review-container');
                    }else if($html->find('div[id=tab-data-qa-reviews-0]', 0)) {
                        if ($html->find('div[id=tab-data-qa-reviews-0]', 0)->find('div._1c8_1ITO', 0)) {
                            $reviewContainerDiv = $html->find('div[id=tab-data-qa-reviews-0]', 0)->find('div._1c8_1ITO', 0)->find('span[data-ft=true]');
                        }
                    }
                    else {
                        if ($html->find('div.reviewSelector', 0)) {
                            $reviewContainerDiv = $html->find('div.reviewSelector');
                        }
                    }
                }

                //for hotel reviews
                if(empty($reviewContainerDiv)) {
                    if($html->find('div.cWwQK')) {
                        $reviewContainerDiv = $html->find('div.cWwQK');
                    }
                }

                //for attraction
                if(empty($reviewContainerDiv) && $html->find('div[id=tab-data-qa-reviews-0]', 0)) {
                    if($html->find('div[id=tab-data-qa-reviews-0]', 0)->find('div.bPhtn', 0)) {
                        $reviewContainerDiv = $html->find('div[id=tab-data-qa-reviews-0]', 0)->find('div.bPhtn', 0)->find('span[data-ft=true]');
                    }
                }

                //for attraction too
                if (empty($reviewContainerDiv) && $html->find('div[id=taplc_attraction_operator_product_list_acr_responsive_0]', 0)) {
                    if($html->find('div.fzKhc', 0)) {
                        if( $html->find('div[id=taplc_attraction_operator_product_list_acr_responsive_0]', 0)->find('div.fzKhc', 0) ) {
                            if ($html->find('div[id=taplc_attraction_operator_product_list_acr_responsive_0]', 0)->find('div.fzKhc', 0)->find('div.eVykL')) {
                                $reviewContainerDiv = $html->find('div[id=taplc_attraction_operator_product_list_acr_responsive_0]', 0)->find('div.fzKhc', 0)->find('div.eVykL');
                            }
                        }
                    }
                }

                if(empty($reviewContainerDiv) && $html->find('div[id=tab-data-qa-reviews-0]', 0)) {
                    if ($html->find('div[id=tab-data-qa-reviews-0]', 0)->find('div.ebHXW', 0)) {
                        $reviewContainerDiv = $html->find('div[id=tab-data-qa-reviews-0]', 0)->find('div.ebHXW', 0)->find('span[data-ft=true]');
                    }
                }

                if(empty($reviewContainerDiv) && $html->find('div[id=tab-data-qa-reviews-0]', 0)) {
                    if ($html->find('div[id=tab-data-qa-reviews-0]', 0)->find('div.dHjBB', 0)) {
                        $reviewContainerDiv = $html->find('div[id=tab-data-qa-reviews-0]', 0)->find('div.dHjBB', 0)->find('span[data-ft=true]');
                    }
                }

                foreach ($reviewContainerDiv as $review) {
                    if ($i > 21) {
                        break;
                    }

                    $userImage = $this->getReviewerImage($review, $jsonLazyImg);

                    $rating = $this->getReviewRating($review);

                    $dateSubmitted = $this->getReviewDate($review);

                    $reviewText = $this->getReviewText($review, $showUserReviews, $i);

                    if ($rating > 0 && $reviewText != '') {
                        $pos          = strpos($userImage, 'default_avatars');
                        if ($pos === false) {
                            $userImage = str_replace("60s.jpg", "120s.jpg", $userImage);
                        }

                        $timestamp = '';
                        if(!empty($dateSubmitted)) {
                            $timestamp = ProHelper::unixTimeStamp($dateSubmitted);

                            $timestamp = date("Y-m-d H:i:s", $timestamp);
                        }

                            $reviews[]     = [
                                'source_id'          => $this->placeId,
                                'review_url'         => $urlValue,
                                'reviewer_name'      => trim($this->getReviewerName($review)),
                                'pagename'           => $downloadUrl,
                                'userpic'            => $userImage,
                                'rating'             => $rating,
                                'created_time'       => $timestamp,
                                'review_text'        => trim($reviewText),
                            ];

                    }
                    $i++;
                }

                //sleep for random 2 seconds
                sleep(rand(0, 2));
                $n++;
                // clean up memory
                if (!empty($html)) {
                    $html->clear();
                    unset($html);
                }
            }

            //remove duplicates
            $reviewerNames = [];
            $insertReviews = [];

            foreach ($reviews as $stat) {
                if (!in_array($stat['reviewer_name'], $reviewerNames)) {
                    $insertReviews[] = $stat;
                }
                $reviewerNames[] = $stat['reviewer_name'];
            }

            if (empty($insertReviews)) {
                $errorMessage = __(' Unable to find any new reviews.', 'wp-social-ninja-pro');
            }

            $this->saveApiSettings([
                'api_key'   => '479711fa-64ba-47ce-b63b-9c2ba8d663f9',
                'place_id'  => $this->placeId,
                'url_value' => $downloadUrl
            ]);
            $this->syncRemoteReviews($insertReviews);

            $businessInfo['total_fetched_reviews'] = count($insertReviews);
            return $businessInfo;
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
            $platforms['tripadvisor'] = __('Tripadvisor', 'wp-social-ninja-pro');
        }
        return $platforms;
    }

    public function formatData($review, $index)
    {
        return [
            'platform_name' => $this->platform,
            'source_id'     => Arr::get($review, 'source_id'),
            'reviewer_name' => Arr::get($review, 'reviewer_name'),
            'review_title'  => $this->platform . '_' . ($index + 1),
            'reviewer_url'  => Arr::get($review, 'review_url'),
            'reviewer_img'  => Arr::get($review, 'userpic'),
            'reviewer_text' => Arr::get($review, 'review_text'),
            'rating'        => Arr::get($review, 'rating'),
            'review_time'   => Arr::get($review, 'created_time'),
            'review_approved' => 1,
            'updated_at'    => date('Y-m-d H:i:s'),
            'created_at'    => date('Y-m-d H:i:s')
        ];
    }

    public function saveBusinessInfo($data = array())
    {
        $businessInfo  = [];
        $infos         = $this->getBusinessInfo();
        if ($data && is_array($data)) {
            $placeId                          = $this->placeId;
            $businessInfo['place_id']         = $placeId;
            $businessInfo['name']             = Arr::get($data, 'pagename', '');
            $businessInfo['url']              = Arr::get($data, 'page1', '');
            $businessInfo['address']          = '';
            $businessInfo['average_rating']   = Arr::get($data, 'avgrating');
            $businessInfo['total_rating']     = Arr::get($data, 'totalreviews');
            $businessInfo['phone']            = '';
            $businessInfo['platform_name']    = $this->platform;
            $businessInfo['status']           = true;
            $infos[$placeId]                  =  $businessInfo;
        }
        return $infos;
    }

    public function getBusinessInfo()
    {
        return get_option('wpsr_reviews_tripadvisor_business_info');
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

    public function downloadBusinessInfo($currentUrl)
    {
        if (strpos($currentUrl, 'VacationRentalReview') !== false) {
            $isVacationRentalReviews = true;
        } else {
            $isVacationRentalReviews = false;
        }

        $fileUrlContents = wp_remote_retrieve_body(wp_remote_get($currentUrl));

        if(empty($fileUrlContents)) {
	        throw new \Exception(
		        __('Can\'t fetch reviews due to slow network, please try again', 'wp-social-ninja-pro')
	        );
        }

        //fix for lazy load base64 ""
        $fileUrlContents = str_replace('=="', '', $fileUrlContents);

        $html            = Helper::str_get_html($fileUrlContents);

        if(empty($html)) {
            return;
        }

        $avgRating    = $this->getAverageRating($html);
        $totalReviews = $this->getTotalReviews($html);
        $businessName = $this->getBusinessName($html);

        //for hotels average ratings

        $reviewObject5 = $page2url = $rTitleLink = $nextBtnLink = $reviewObject = "";
        //check to see if on vacation rental or regular page
        if ($isVacationRentalReviews == true) {
            if ($html->find('div.reviewSelector')) {
                $reviewObject = $html->find('div.reviewSelector', 0);
            }
        } else {
            if ($html->find('div.review-container')) {
                $reviewObject  = $html->find('div.review-container', 0);
                $reviewObject5 = $html->find('div.review-container', 5);
            } else {
                //fix for hotel review, hotels-hotel-review-community-content-Card__card-
                if ($html->find("div[class*=ReviewTitle]", 0)) {
                    $rTitleLink = $html->find("div[class*=ReviewTitle]", 0)->find('a', 0)->href;

                    if ($html->find("div[class*=ReviewTitle]", 4)) {
                        $nextBtnLink = $html->find("div[class*=ReviewTitle]", 4)->find('a', 0)->href;
                    }
                }
            }
        }

        //find tripadvisor business name
        if (isset($reviewObject) && $reviewObject != "" && $rTitleLink == '') {
            $rTitleLink = $reviewObject->find('div.quote', 0)->find('a', 0)->href;
        }

        if(empty($reviewObject) && $nextBtnLink === "") {
            //fix hotels page url link issue
            if($html->find('div.ui_pagination', 0)) {
                if($html->find('div.ui_pagination', 0)->find('a.ui_button', 0)) {
                    $nextBtnLink = $html->find('div.ui_pagination', 0)->find('a.ui_button', 0)->href;
                }
            }
        }

        if ($reviewObject5 != "" && $nextBtnLink == '') {
            if($reviewObject5->find('div.quote', 0)) {
                if($reviewObject5->find('div.quote', 0)->find('a', 0)) {
                    $nextBtnLink = $reviewObject5->find('div.quote', 0)->find('a', 0)->href;
                }
            }
        }

        //for hotels reviews
        if ($rTitleLink === '' && $nextBtnLink === '') {
            if ($html->find('div.glasR4aX', 0)) {
                if($html->find('div.glasR4aX', 0)->find('a', 0)){
                    $rTitleLink = $html->find('div.glasR4aX', 0)->find('a', 0)->href;
                }
            }
        }

        //for attraction reviews
        if ($rTitleLink === '' && $nextBtnLink === '') {
            if ($html->find('div._2cigFICy', 0)) {
                if($html->find('div._2cigFICy', 0)->find('a', 0)) {
                    $rTitleLink = $html->find('div._2cigFICy', 0)->find('a', 0)->href;
                }
            }

            if($html->find('div._1w5PB8Rk', 1)) {
                if($html->find('div._1w5PB8Rk', 1)->find('a', 0)) {
                    $rTitleLink = $html->find('div._1w5PB8Rk', 1)->find('a', 0)->href;
                }
            }
        }

        $parseurl = parse_url($currentUrl);
        $siteUrl = $parseurl['scheme'] . '://' . $parseurl['host'];
        $newurl   = $siteUrl . $rTitleLink;
        $page2url = $siteUrl . $nextBtnLink;

        if(strcmp($newurl, $siteUrl) === 0) {
            $newurl = $currentUrl;
        }
        if(strcmp($page2url, $siteUrl) === 0) {
            $page2url = $currentUrl;
        }

        $placeId = '';
        if (!empty($businessName)) {
            $placeId = strtolower(str_replace(' ', '-', $businessName));
        }
        $response = array(
            "page1"        => $newurl,
            "page2"        => $page2url,
            "totalreviews" => $totalReviews,
            "avgrating"    => $avgRating,
            "pagename"     => $businessName,
            'place_id'     => preg_replace("/&#?[a-z0-9]+;/i","", $placeId)
        );
        $html->clear();
        unset($html);

        return $response;
    }

    //get business info
    public function getAverageRating($html)
    {
        $avgRating = '';

        //fix for hotels review
        if ($html->find('div[id=taplc_about_with_photos_react_0]', 0)) {
            if ($html->find('div[id=taplc_about_with_photos_react_0]', 0)->find('div.bSlOX', 0)) {
                if($html->find('div[id=taplc_about_with_photos_react_0]', 0)->find('div.bSlOX', 0)->find('span.bvcwU')) {
                    $avgRatingText = $html->find('div[id=taplc_about_with_photos_react_0]', 0)->find('div.bSlOX', 0)->find('span.bvcwU', 0)->plaintext;
                    if($avgRatingText) {
                        $avgRating = trim(str_replace(',', '.', $avgRatingText));
                    }
                }
            }
        }

        if ($html->find('div.ratingContainer', 0)) {
            if ($html->find('div.ratingContainer', 0)->find('span.ui_bubble_rating', 0)) {
                $avgRating = $html->find('div.ratingContainer', 0)->find('span.ui_bubble_rating', 0)->alt;
                $avgRating = str_replace(" of 5 bubbles", "", $avgRating);
                $avgRating = str_replace(",", ".", $avgRating);
                $avgRating = (float)$avgRating;;
            }
        }

        if ($avgRating === '') {
            if ($html->find('span._3cjYfwwQ')) {
                $avgRating = strip_tags($html->find('span._3cjYfwwQ', 0));
                $avgRating = (float)$avgRating;
            }
        }

        //for attraction average rating
        if ($avgRating === '') {
            if ($html->find('span._2Hy7Xxdm')) {
                $avgRating = strip_tags($html->find('span._2Hy7Xxdm', 0));
                $avgRating = (float)$avgRating;
            }

            if($avgRating === '') {
                if($html->find('div.diLqV', 0)) {
                    if($html->find('div.diLqV', 0)->find('div.fQJHy', 0)) {
                        if ($html->find('div.diLqV', 0)->find('div.fQJHy', 0)->find('div.WlYyy', 0)) {
                            $avgRating = strip_tags($html->find('div.diLqV', 0)->find('div.fQJHy', 0)->find('div.WlYyy', 0));
                        }
                    }
                }
            }
        }
        if ($avgRating === '') {
            if ($html->find('div[id=taplc_top_info_0]', 0)) {
                if ( $html->find('div[id=taplc_top_info_0]', 0)->find('svg.RWYkj', 0) ) {
                    $avgRating = $html->find('div[id=taplc_top_info_0]', 0)->find('svg.RWYkj', 0)->title;
                }

                $avgRating = strtok($avgRating, " ");
                if(!empty($avgRating)) {
                    $avgRating = str_replace(',', '.', $avgRating);
                }
            }
        }

        //backup method for hotels
        if ($avgRating === '') {
            if ($html->find('span.hotels-hotel-review-about-with-photos-Reviews__overallRating--vElGA', 0)) {
                $avgRating = $html->find('span.hotels-hotel-review-about-with-photos-Reviews__overallRating--vElGA',
                    0)->plaintext;
                $avgRating = str_replace(",", ".", $avgRating);
                $avgRating = preg_replace('/[^0-9.]+/', '', $avgRating);
            }
        }

        //if not found try backup method, currently used for restaurants
        if ($avgRating === '') {
            if ($html->find('div.rating_and_popularity', 0)) {
                if ($html->find('div.rating_and_popularity', 0)->find('span.ui_bubble_rating', 0)) {
                    $avgRating = $html->find('div.rating_and_popularity', 0)->find('span.ui_bubble_rating', 0)->alt;
                    $avgRating = str_replace(" of 5 bubbles", "", $avgRating);
                    //fix for comma
                    $avgRating = str_replace(",", ".", $avgRating);
                    $avgRating = (float)$avgRating;
                }
            }
        }

        //collect vacation rental reviews
        if ($avgRating === '') {
            if ($html->find('div.ratingSystem', 0)) {
                if ($html->find('div.ratingSystem', 0)->find('span.ui_bubble_rating', 0)) {
                    $avgRating = $html->find('div.ratingSystem', 0)->find('span.ui_bubble_rating', 0)->class;
                    $avgRating = str_replace(",", ".", $avgRating);
                    $avgRating = preg_replace('/[^0-9.]+/', '', $avgRating);
                    $avgRating = $avgRating / 10;
                }
            }
        }

        //collect attraction reviews
        if ($avgRating === '') {
            if ($html->find('div.ui_poi_review_rating ', 0)) {
                if ($html->find('div.ui_poi_review_rating ', 0)->find('span.ui_bubble_rating', 0)) {
                    $avgRating = $html->find('div.ui_poi_review_rating', 0)->find('span.ui_bubble_rating', 0)->class;
                    $avgRating = str_replace(",", ".", $avgRating);
                    $avgRating = preg_replace('/[^0-9.]+/', '', $avgRating);
                    $avgRating = $avgRating / 10;
                }
            }
        }

        //collect restaurant reviews
        if ($avgRating == '') {
            if ($html->find('span.restaurants-detail-overview-cards-RatingsOverviewCard__overallRating--nohTl', 0)) {
                $avgRating = $html->find('span.restaurants-detail-overview-cards-RatingsOverviewCard__overallRating--nohTl',
                    0)->plaintext;
                $avgRating = str_replace(",", ".", $avgRating);
                $avgRating = preg_replace('/[^0-9.]+/', '', $avgRating);
                //$avgRating = $avgRating/10;
            }
        }

        //attraction reviews
        if($avgRating === '') {
            if($html->find('div._3TrUt7dh')) {
                $avgRating = trim($html->find('div._3TrUt7dh', 0)->find('div._1QGef_ZJ', 0)->plaintext);
            }
        }

        if(!$avgRating) {
            if($html->find('div[id=tab-data-qa-reviews-0]', 0)) {
                if ($html->find('div[id=tab-data-qa-reviews-0]', 0)->find('div.RTVWf', 0)) {
                    if ($html->find('div[id=tab-data-qa-reviews-0]', 0)->find('div.RTVWf', 0)->find('svg.RWYkj', 0)) {
                        $avgRatingText = $html->find('div[id=tab-data-qa-reviews-0]', 0)->find('div.RTVWf', 0)->find('svg.RWYkj', 0)->title;
                        if(!empty($avgRatingText)) {
                            $avgRatingText = str_replace(" of 5 bubbles", "", $avgRatingText);
                            $avgRating = str_replace(",", ".", $avgRatingText);
                            $avgRating = (float)$avgRating;
                        }
                    }
                }
            }
        }

        if($avgRating === ''){
            if($html->find('span.bvcwU')) {
                $avgRating = trim($html->find('span.bvcwU', 0)->plaintext);
                $avgRating = str_replace(",", ".", $avgRating);
                $avgRating = (float)$avgRating;
            }
        }

        return (float)$avgRating;
    }

    public function getTotalReviews($html)
    {
        $totalReviews = '';

        //fix for hotel review
        if ($html->find('div[id=taplc_hotel_review_atf_hotel_info_web_component_0]', 0)) {
            if ($html->find('div[id=taplc_hotel_review_atf_hotel_info_web_component_0]', 0)->find('div.eCOqA', 0)) {
                if($html->find('div[id=taplc_hotel_review_atf_hotel_info_web_component_0]', 0)->find('div.eCOqA', 0)->find('span.HFUqL')) {
                    $totalReviewsText = $html->find('div[id=taplc_hotel_review_atf_hotel_info_web_component_0]', 0)->find('div.eCOqA', 0)->find('span.HFUqL', 0)->plaintext;
                    if($totalReviewsText) {
                        $totalReviewsText = trim(str_replace('reviews', '', $totalReviewsText));
                        $totalReviewsText = str_replace(',', '', $totalReviewsText);
                        $totalReviews = $totalReviewsText;
                    }
                }
            }
        }

        if( $html->find('div.ratingContainer', 0) ) {
            if ($html->find('div.ratingContainer', 0)->find('span.reviewCount', 0)) {
                $totalReviews = $html->find('div.ratingContainer', 0)->find('span.reviewCount', 0)->plaintext;
                $totalReviews = str_replace(",", "", $totalReviews);
                $totalReviews = intval($totalReviews);
            }
        }

        //for hotels total reviews
        if ($totalReviews === '') {
            if ($html->find('span._33O9dg0j')) {
                $totalReviews = $html->find('span._33O9dg0j', 0);
                $totalReviews = str_replace(array(',', ' reviews'), '', strip_tags($totalReviews));
                $totalReviews = intval($totalReviews);
            }
        }

        //for hotels total reviews
        if ($totalReviews === '') {
            if ($html->find('span._33O9dg0j')) {
                $totalReviews = $html->find('span._33O9dg0j', 0);
                $totalReviews = str_replace(array(',', ' reviews'), '', strip_tags($totalReviews));
                $totalReviews = intval($totalReviews);
            }
        }

        //for attraction total reviews
        if ($totalReviews === '') {
            if ($html->find('span._1uXQPaAr', 0)) {
                $totalReviews = strip_tags($html->find('span._1uXQPaAr', 0));
                $totalReviews = str_replace(',', '', $totalReviews);
                $totalReviews = str_replace(' Reviews', '', $totalReviews);
                $totalReviews = intval($totalReviews);
            }
        }

        //for attraction product total reviews
        if ($totalReviews === '') {
            if ($html->find('span._82HNRypW')) {
                $totalReviews = strip_tags($html->find('span._82HNRypW', 0));
                $totalReviews = str_replace(',', '', $totalReviews);
                $totalReviews = str_replace(' reviews', '', $totalReviews);
                $totalReviews = intval($totalReviews);
            }
        }

        if ($totalReviews === '') {
            //find total number here and end break loop early if total number less than 50. review-count
            if ($html->find('span.reviews_header_count', 0)) {
                $totalReviews = $html->find('span.reviews_header_count', 0)->plaintext;
                $totalReviews = str_replace(array('(', ',', ')'), '', $totalReviews);
                $totalReviews = trim(preg_replace('/\s*\([^)]*\)/', '', $totalReviews));
                $totalReviews = intVal($totalReviews);
            }
        }

        if ($totalReviews === '') {
            if ($html->find('span.hotels-hotel-review-about-with-photos-Reviews__seeAllReviews--3PpLR', 0)) {
                $totalReviews = $html->find('span.hotels-hotel-review-about-with-photos-Reviews__seeAllReviews--3PpLR',
                    0)->plaintext;
                $totalReviews = str_replace(",", "", $totalReviews);
                $totalReviews = intval($totalReviews);
            }
        }

        if( $totalReviews === '' ) {
            if($html->find('div.rating_and_popularity', 0)) {
                if ($html->find('div.rating_and_popularity', 0)->find('div.rating', 0)) {
                    $totalReviews = $html->find('div.rating_and_popularity', 0)->find('div.rating', 0)->plaintext;
                    $totalReviews = str_replace(",", "", $totalReviews);
                    $totalReviews = intval($totalReviews);
                }
            }
        }

        if ($totalReviews === '') {
            if($html->find('div.ratingSystem', 0)) {
                if ($html->find('div.ratingSystem', 0)->find('span.based-on-n-reviews', 0)) {
                    $totalReviews = $html->find('div.ratingSystem', 0)->find('span.based-on-n-reviews', 0)->plaintext;
                    $totalReviews = str_replace(",", "", $totalReviews);
                    $totalReviews = str_replace("-", "", $totalReviews);
                    $totalReviews = str_replace("based on ", "", $totalReviews);
                    $totalReviews = preg_replace('/[^0-9.]+/', '', $totalReviews);
                }
            }
        }

        if ($totalReviews === '') {
            if ($html->find('div.ui_poi_review_rating', 0)) {
                if ($html->find('div.ui_poi_review_rating', 0)->find('span.reviewCount', 0)) {
                    $totalReviews = $html->find('div.ui_poi_review_rating', 0)->find('span.reviewCount', 0)->plaintext;
                    $totalReviews = str_replace(",", "", $totalReviews);
                    $totalReviews = str_replace("-", "", $totalReviews);
                    $totalReviews = str_replace("based on ", "", $totalReviews);
                    $totalReviews = preg_replace('/[^0-9.]+/', '', $totalReviews);
                }
            }
        }

        if ($totalReviews === '') {
            if ($html->find('a.restaurants-detail-overview-cards-RatingsOverviewCard__ratingCount--DFxkG', 0)) {
                $totalReviews = $html->find('a.restaurants-detail-overview-cards-RatingsOverviewCard__ratingCount--DFxkG',
                    0)->plaintext;
                $totalReviews = str_replace(",", "", $totalReviews);
                $totalReviews = intval($totalReviews);
            }
        }

        //attraction reviews
        if($totalReviews === '') {
            if($html->find('div.zTTYS8QR')) {
                $totalReviews = trim($html->find('div.zTTYS8QR', 0)->find('span._2nPM5Opx', 0)->plaintext);
            }
            if($totalReviews === '' && $html->find('div.eGqVx', 0)) {
                if($html->find('div.eGqVx', 0)->find('span.cfIVb', 0)) {
                    $totalReviewsText = trim($html->find('div.eGqVx', 0)->find('span.cfIVb', 0)->plaintext);
                    $totalReviews = str_replace(',', '', $totalReviewsText);
                    $totalReviews = (int)$totalReviews;
                }
            }
        }

        if(!$totalReviews) {
            if($html->find('div[id=tab-data-qa-reviews-0]', 0)) {
                if ($html->find('div[id=tab-data-qa-reviews-0]', 0)->find('div.RTVWf', 0)) {
                    if ($html->find('div[id=tab-data-qa-reviews-0]', 0)->find('div.RTVWf', 0)->find('span.dDKKM', 0)) {
                        $totalReviewsText = $html->find('div[id=tab-data-qa-reviews-0]', 0)->find('div.RTVWf', 0)->find('span.dDKKM', 0)->plaintext;
                        if(!empty($totalReviewsText)) {
                            $totalReviewsText = str_replace(' reviews', '', $totalReviewsText);
                            $totalReviews = str_replace(',', '', $totalReviewsText);
                        }
                    }
                }
            }
        }

        return (int)$totalReviews;
    }

    public function getBusinessName($html)
    {
        $businessName = '';
        if ($html->find('.heading_title', 0)) {
            $businessName = $html->find('.heading_title', 0)->plaintext;
        }

        if ($businessName === '') {
            if ($html->find('div[id=taplc_top_info_0]', 0)) {
                if ( $html->find('div[id=taplc_top_info_0]', 0)->find('h1._3a1XQ88S', 0) ) {
                    $businessName = $html->find('div[id=taplc_top_info_0]', 0)->find('h1._3a1XQ88S', 0)->plaintext;
                }

                if($html->find('div[id=taplc_top_info_0]', 0)->find('h1.fHibz', 0)) {
                    $businessName = $html->find('div[id=taplc_top_info_0]', 0)->find('h1.fHibz', 0)->plaintext;
                }
            }
        }

        if ($businessName == '') {
            if ($html->find('h1[id=HEADING]', 0)) {
                $businessName = $html->find('h1[id=HEADING]', 0)->plaintext;
            }
        }

        if ($businessName == '') {
            if ($html->find('.altHeadInline', 0)) {
                if ($html->find('.altHeadInline', 0)->find('a', 0)) {
                    $businessName = $html->find('.altHeadInline', 0)->find('a', 0)->plaintext;
                }
            }
        }

        if ($businessName == '') {
            if ($html->find('.vrPgHdr', 0)) {
                if ($html->find('.vrPgHdr', 0)->find('a', 0)) {
                    $businessName = $html->find('.vrPgHdr', 0)->find('a', 0)->plaintext;
                }
            }
        }

        if ($businessName == '') {
            if ($html->find('h1[id=HEADING]', 0)) {
                $businessName = $html->find('h1[id=HEADING]', 0)->plaintext;
            }
        }

        if ($businessName == '') {
            if ($html->find('h1[class=ui_header h1]', 0)) {
                $businessName = $html->find('h1[class=ui_header h1]', 0)->plaintext;
            }
        }

        if ($businessName == '') {
            if ($html->find('h1[class=propertyHeading]', 0)) {
                $businessName = $html->find('h1[class=propertyHeading]', 0)->plaintext;
            }
        }

        if ($businessName == '') {
            if ($html->find('span[class=ui_header h1]', 0)) {
                $businessName = $html->find('span[class=ui_header h1]', 0)->plaintext;
            }
        }

        if ($businessName == '') {
            if ($html->find('span[class=ui_header h1]', 0)) {
                $businessName = $html->find('span[class=ui_header h1]', 0)->plaintext;
            }
        }

        if ($businessName == '') {
            if ($html->find('h1[class=restaurants-detail-top-info-TopInfo__restaurantName--1IKBe]', 0)) {
                $businessName = $html->find('h1[class=restaurants-detail-top-info-TopInfo__restaurantName--1IKBe]',
                    0)->plaintext;
            }
        }

        //page name for attraction product
        if ($businessName === '') {
            if ($html->find('span.IKwHbf8J')) {
                $businessName = $html->find('span.IKwHbf8J', 0)->plaintext;
            }
        }

        if ($businessName === '') {
            if ($html->find('div.Xewee')) {
                if ($html->find('div.Xewee', 0)->find('h1.WlYyy')) {
                    $businessName = strip_tags(trim($html->find('div.Xewee', 0)->find('h1.WlYyy', 0)));
                }
            }
        }

        //attraction
        if( $businessName === '' ) {
            if( $html->find('div.vgL7v_9B') ) {
                $businessName = strip_tags($html->find('div.vgL7v_9B', 0)->find('h1.qf3QTY0F', 0));
            }
            if($html->find('div.dYQAX', 0)) {
                if($html->find('div.dYQAX', 0)->find('div.MmEaS', 0)) {
                    $businessName = strip_tags($html->find('div.dYQAX', 0)->find('div.MmEaS', 0)->find('h1.WlYyy', 0));
                }
            }
        }

        if ($businessName === '') {
            if ($html->find('header.XqRzy')) {
                if ($html->find('header.XqRzy', 0)->find('div.epGWc')) {
                    if ($html->find('header.XqRzy', 0)->find('div.epGWc', 0)->find('h1.GeSzT')) {
                        $businessName = strip_tags(trim($html->find('header.XqRzy', 0)->find('div.epGWc', 0)->find('h1.GeSzT', 0)));
                    }
                }
            }
        }

        $businessName = ReviewsHelper::removeSpecialChars($businessName);

        return $businessName;
    }

    //get review info
    public function getReviewerName($review)
    {
        $reviewerName = '';
        if ($review->find('div.username', 0)) {
            $reviewerName = $review->find('div.username', 0)->plaintext;
        }

        if ($reviewerName === '') {
            if ($review->find('div.info_text', 0)) {
                $reviewerName = $review->find('div.info_text', 0)->find('div', 0)->plaintext;
            }
        }

        if ($reviewerName === '') {
            if ($review->find('span._2AAjjcx8', 0)) {
                if($review->find('span._2AAjjcx8', 0)->find('a._3WoyIIcL', 0)) {
                    $reviewerName = strip_tags($review->find('span._2AAjjcx8', 0)->find('a._3WoyIIcL', 0));
                }
            }
        }

        if($reviewerName === '') {
            if($review->find('div.bcaHz', 0)) {
                if($review->find('div.bcaHz', 0)->find('a.ui_header_link', 0)) {
                    $reviewerName = $review->find('div.bcaHz', 0)->find('a.ui_header_link', 0)->plaintext;
                }
            }
        }

        //attraction
        if($reviewerName === '') {
            if($review->find('span.WlYyy', 0)) {
                if($review->find('span.WlYyy', 0)->find('a.iPqaD', 0)) {
                    $reviewerName = $review->find('span.WlYyy', 0)->find('a.iPqaD', 0)->plaintext;
                }
            }
        }

        return $reviewerName;
    }

    public function getReviewText($review, $showUserReviews, $index)
    {
        $reviewText = '';
        if ($showUserReviews == true) {
            // find text
            if ($review->find('div.prw_reviews_text_summary_hsx', 0)) {
                $reviewText = $review->find('div.prw_reviews_text_summary_hsx', 0)->find('p', 0)->plaintext;
            }
            if ($reviewText == '') {
                if ($review->find('div.prw_reviews_resp_sur_review_text', 0)) {
                    $reviewText = $review->find('div.prw_reviews_resp_sur_review_text', 0)->find('p',
                        0)->plaintext;
                }
            }
            //if this is first one treat differently
            if ($index == 1) {
                if ($review->find('div.prw_reviews_resp_sur_review_text', 0)) {
                    $reviewText = $review->find('div.prw_reviews_resp_sur_review_text', 0)->find('p',
                        0)->plaintext;
                } else {
                    if ($review->find('div.prw_reviews_resp_sur_review_text_expanded', 0)) {
                        $reviewText = $review->find('div.prw_reviews_resp_sur_review_text_expanded',
                            0)->find('p', 0)->plaintext;
                    }
                }
            }
        } else {
            // find text
            if ($review->find('div.prw_reviews_text_summary_hsx', 0)) {
                $reviewText = $review->find('div.prw_reviews_text_summary_hsx', 0)->find('p', 0)->plaintext;
            }
        }

        //collect summery text for hotel reviews
        if ($reviewText == '') {
            if ($review->find('div.prw_reviews_text_summary_hsx', 0)) {
                $reviewText = $review->find('div.prw_reviews_text_summary_hsx', 0)->find('p', 0)->plaintext;
            }
        }
        if ($reviewText == '') {
            if ($review->find('div.vrReviewText', 0)) {
                $reviewText = $review->find('div.vrReviewText', 0)->find('p', 0)->plaintext;
            }
        }
        //if review text is blank try one more time, used to get top review on hotels
        if ($reviewText == '') {
            if ($review->find('div.entry', 0)) {
                $reviewText = $review->find('div.entry', 0)->find('p', 0)->plaintext;
            }
        }

        if ($reviewText === '') {
            if ($review->find('div._2nPM5Opx', 0)) {
                $reviewText = strip_tags($review->find('div._2nPM5Opx', 0)->find('span._2tsgCuqy', 0));
            }
        }

        if($reviewText === '') {
            if($review->find('div.dovOW', 0)) {
                if($review->find('div.dovOW', 0)->find('div.pIRBV', 0)) {
                    $reviewText = $review->find('div.dovOW', 0)->find('div.pIRBV', 0)->find('span', 0)->plaintext;
                }
            }
        }

        //for attraction
        if($reviewText === '') {
            if($review->find('div.duhwe', 0)) {
                if($review->find('div.duhwe', 0)->find('span.NejBf', 0)) {
                    $reviewText = $review->find('div.duhwe', 0)->find('span.NejBf', 0)->plaintext;
                }
            }
        }

        if($reviewText === '') {
            if ($review->find('div.duhwe')) {
                if ($review->find('div.duhwe', 0)->find('span.cSoNT')) {
                    $reviewText = strip_tags($review->find('div.duhwe', 0)->find('span.cSoNT', 0));
                }
            }
        }

        if($reviewText) {
            $strLen = strlen($reviewText) - 10;
            $extraTextPos = strpos($reviewText, '.More', $strLen);
            if($extraTextPos) {
                $reviewText = substr($reviewText, 0, $extraTextPos);
            }
        }

        return $reviewText;
    }

    public function getReviewRating($review)
    {
        $reviewRating = '';
        if ($review->find('span.ui_bubble_rating', 0)) {
            $tempRating = $review->find('span.ui_bubble_rating', 0)->class;
            $int        = filter_var($tempRating, FILTER_SANITIZE_NUMBER_INT);
            $reviewRating     = str_replace(0, "", $int);
        }

        if ($reviewRating === '') {
            if ($review->find('svg.zWXXYhVR', 0)) {
                $ratingText = trim($review->find('svg.zWXXYhVR', 0)->title);
                $bubblePosition = strpos($ratingText, 'bubbles');
                if($bubblePosition) {
                    $reviewRating = (int)$ratingText[$bubblePosition-2];
                }
            }
        }

        //attraction
        if($reviewRating === '') {
            if($review->find('svg.RWYkj', 0)) {
                $reviewRatingText = $review->find('svg.RWYkj', 0)->title;
                $reviewRatingText = strtok($reviewRatingText, " ");
                $reviewRating     = str_replace(',', '.', $reviewRatingText);
                $reviewRating     = (float)$reviewRating;
            }
        }

        return $reviewRating;
    }

    public function getReviewerImage($review, $jsonLazyImg)
    {
        $reviewerImage = '';
        // Find userimage ui_avatar, need to pull from lazy load varible
        if ($review->find('div.ui_avatar', 0)) {
            if ($review->find('div.ui_avatar', 0)->find('img.basicImg', 0)) {
                $userImageid = $review->find('div.ui_avatar', 0)->find('img.basicImg', 0)->id;
                //strip id from
                $userImageid = strrchr($userImageid, "_");
                //loop through array and return url
                if (is_array($jsonLazyImg)) {
                    for ($x = 0; $x <= count($jsonLazyImg); $x++) {
                        //get temp id
                        $tempid = $jsonLazyImg[$x]['id'];
                        $tempid = strrchr($tempid, "_");
                        if ($userImageid == $tempid) {
                            $reviewerImage = $jsonLazyImg[$x]['data'];
                            $x         = count($jsonLazyImg) + 1;
                        }
                    }
                }
            }
        }

        //if user image not found check
        $checkStringPos = strpos($reviewerImage, 'base64');
        if ($reviewerImage == '' || $checkStringPos > 0) {
            if ($review->find('div.ui_avatar', 0)) {
                if ($review->find('div.ui_avatar', 0)->find('img.basicImg', 0)) {
                    if ($review->find('div.ui_avatar', 0)->find('img.basicImg', 0)->{'data-lazyurl'}) {
                        $reviewerImage = $review->find('div.ui_avatar', 0)->find('img.basicImg',
                            0)->{'data-lazyurl'};
                    } else {
                        $reviewerImage = $review->find('div.ui_avatar', 0)->find('img.basicImg', 0)->src;
                    }
                }
            }
        }

        if ($reviewerImage == '') {
            if ($review->find('div.avatar', 0)) {
                if ($review->find('div.avatar', 0)->find('img.avatar', 0)) {
                    $reviewerImage = $review->find('div.avatar', 0)->find('img.avatar', 0)->{'src'};
                }
            }
        }

        if ($reviewerImage === '') {
            if ($review->find('div._2L7OTqqK', 0)) {
                if ($review->find('div._2L7OTqqK', 0)->find('picture._2f-Th360', 0)) {
                    $reviewerImage = trim($review->find('div._2L7OTqqK', 0)->find('picture._2f-Th360', 0)->find('img', 0)->src);
                }
            }
        }

        //collect user image for hotels reviews
        if ($reviewerImage == '' && $review->find('div.ui_avatar', 0)) {
            if ($review->find('div.ui_avatar', 0)->find('img.basicImg', 0)) {
                $reviewerImage = $review->find('div.ui_avatar', 0)->find('img.basicImg', 0)->src;
            }
        }

        //if user image not found check
        if ($reviewerImage == '' && $review->find('div.ui_avatar', 0)) {
            if ($review->find('div.ui_avatar', 0)->find('img.basicImg', 0)) {
                if ($review->find('div.ui_avatar', 0)->find('img.basicImg', 0)->{'data-lazyurl'}) {
                    $reviewerImage = $review->find('div.ui_avatar', 0)->find('img.basicImg',
                        0)->{'data-lazyurl'};
                }
            }
        }

        if ($reviewerImage === '') {
            if ($review->find('picture.Th360', 0)) {
                $reviewerImage = $review->find('picture.Th360', 0)->find('img', 0)->src;
            }
        }

        if ($reviewerImage === '') {
            if ($review->find('picture.dugSS', 0)) {
                $reviewerImage = $review->find('picture.dugSS', 0)->find('img', 0)->src;
            }
        }

        if($reviewerImage === '') {
            if ($review->find('div.xMxrO', 0)) {
                $reviewerImage = $review->find('div.xMxrO', 0)->find('a.bugwz', 0)->find('img', 0)->src;
            }
        }

        return $reviewerImage;
    }

    public function getReviewDate($review)
    {
        $dateSubmitted = '';
        if ($review->find('span.ratingDate', 0)) {
            $dateSubmitted = $review->find('span.ratingDate', 0)->title;
        }

        if ($dateSubmitted == '') {
            if ($review->find('span.ratingDate', 0)) {
                $dateSubmitted = $review->find('span.ratingDate', 0)->innertext;
                $dateSubmitted = preg_replace("(<([a-z]+)>.*?</\\1>)is", "", $dateSubmitted);
                $dateSubmitted = str_replace("Reviewed", "", $dateSubmitted);
                $dateSubmitted = str_replace("Beoordeeld", "", $dateSubmitted);
                $dateSubmitted = str_replace("op", "", $dateSubmitted);
                $dateSubmitted = str_replace('Recensito il', "", $dateSubmitted);
            }
        }

        $dateSubmitted = str_replace(' г.', "", $dateSubmitted);

        if($dateSubmitted === '') {
            if($review->find('div.bcaHz', 0)) {
                $dateSubmitted = $review->find('div.bcaHz', 0)->find('span', 0)->plaintext;
                if(!empty($dateSubmitted)) {
                    $pos = strpos($dateSubmitted,"review ");
                    $dateSubmitted = substr($dateSubmitted, $pos+strlen('review '));
                }
            }
        }

        //attraction
        if ($dateSubmitted === '') {
            if ($review->find('div.bNOAd', 0)) {
                if ($review->find('div.bNOAd', 0)->find('div.WlYyy', 0)) {
                    $dateSubmittedText = $review->find('div.bNOAd', 0)->find('div.WlYyy', 0);
                    if($dateSubmitted) {
                        $dateSubmittedText = strip_tags($dateSubmittedText);
                    }
                    $dateSubmitted = str_replace("Written ", "", $dateSubmittedText);
                }
            }
        }

        if ($dateSubmitted === '') {
            if ($review->find('div._1b1HH8jx', 0)) {
                if($review->find('div._1b1HH8jx', 0)->find('div._26S7gyB4', 0)) {
                    $dateSubmitted = trim($review->find('div._1b1HH8jx', 0)->find('div._26S7gyB4', 0));
                    $dateSubmitted = str_replace("Written ", "", $dateSubmitted);
                }
            }
        }

        if ($dateSubmitted === '') {
            if ($review->find('div.cLyMB')) {
                $dateSubmittedText = $review->find('div.cLyMB', 0);
                $dateSubmittedText = strip_tags(trim($dateSubmittedText));
                if(!empty($dateSubmittedText)) {
                    $dateArray = explode('•', $dateSubmittedText);
                    $dateSubmitted = trim($dateArray[0]);
                }
            }
        }

        if($dateSubmitted === '') {
            if ($review->find('div.eRduX')) {
                $dateSubmittedText = $review->find('div.eRduX', 0)->plaintext;
                if(!empty($dateSubmittedText)) {
                    $dateArray = explode('•', $dateSubmittedText);
                    $dateSubmitted = trim($dateArray[0]);
                }
            }
        }

        if($review->find('div.dovOW')) {
            if($review->find('div.dovOW',0)->find('span.euPKI', 0)) {
                $dateSubmittedText = strip_tags($review->find('div.dovOW',0)->find('span.euPKI', 0));
                $dateSubmitted = str_replace('Aufenthaltsdatum: ', '', $dateSubmittedText);
                $dateSubmitted = str_replace('Date of experience: ', '', $dateSubmitted);
                $dateSubmitted = str_replace('Date of stay: ', '', $dateSubmitted);
            }
        }

        return strip_tags($dateSubmitted);
    }
}

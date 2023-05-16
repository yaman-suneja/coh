<?php

namespace WPSocialReviewsPro\Classes\Feeds\Instagram;

use WPSocialReviews\App\Services\Platforms\Feeds\Instagram\InstagramFeed;
use WPSocialReviews\App\Services\Platforms\Feeds\Instagram\Common;
use WPSocialReviews\App\Services\Platforms\Feeds\CacheHandler;
use WPSocialReviews\Framework\Support\Arr;

if (!defined('ABSPATH')) {
    exit;
}

class HashtagFeed
{
    protected $cacheHandler;
    private $perPage = 50;

    public function __construct()
    {
        $this->cacheHandler = new CacheHandler('instagram');
    }

    public function validHashTags($hashtags)
    {
        $hashtags = str_replace('#', '', trim($hashtags));
        $hashtags = str_replace(' ', '', $hashtags);

        return $hashtags;
    }

    /**
     * Merge multiple hashtag response
     *
     * @param $accountIds
     * @param $hashtags
     * @param $hashtagType
     *
     * @return array
     * @since 1.3.0
     *
     */
    public function getMultipleHashtagResponse($accountIds, $hashtags, $hashtagType)
    {
        $hashtags = $this->validHashTags($hashtags);
        $hashtags = array_map('trim', explode(",", $hashtags));

        $connectedAccounts      = (new Common())->findConnectedAccounts();
        $businessAccountCounter = 0;
        foreach ($accountIds as $index => $accountId) {
            $accountDetails = Arr::get($connectedAccounts, $accountId, '');
            $userName = $connectedAccounts[$accountId]['username'];

            if ($accountDetails) {
                //if already it executes a business account response 
                if ($businessAccountCounter) {
                    break;
                }
                //if the selected account is from  personal account
                if ($accountDetails['api_type'] === 'personal') {
                    continue;
                } //else normally executes the business account response
                else {
                    if ($accountDetails['api_type'] === 'business') {
                        ++$businessAccountCounter;
                        $response   = array();
                        $hashtagIds = array();

                        foreach ($hashtags as $hashtag) {
                            $response = $this->getHashtagId($hashtag, $accountDetails);
                            if ((new Common())->instagramError($response)) {
                                return array($userName => $response);
                            }
                            $hashtagIds[] = $response;
                        }

                        $response = array();
                        foreach ($hashtagIds as $index => $hashtagId) {
                            if ($hashtagId) {
                                $response[$index] = $this->getHashtagFeed($hashtagId, $hashtagType,
                                    $accountDetails);
                                if ((new Common())->instagramError($response[$index])) {
                                    return array($userName => $response[$index]);
                                }
                            }
                        }

                        //merge all hashtag feeds
                        if (count($response)) {
                            $allFeeds = array();
                            foreach ($response as $index => $feeds) {
                                if(is_array($feeds)) {
                                    $allFeeds = array_merge($allFeeds, $feeds);
                                }
                            }

                            return $allFeeds;
                        }

                        return $response;
                    }
                }
            }
        }

        if (!$businessAccountCounter) {
            $message = __('You need a business account to get hashtag feed!!', 'wp-social-ninja-pro');

            return array('error_message' => $message);
        }
    }

    /**
     * Build hashtag feed api URL
     *
     * @param $hashtagId
     * @param $hashtagType
     * @param $accountDetails
     *
     * @return string
     * @since 1.3.0
     *
     */
    public function getFeedApiUrl($hashtagId, $hashtagType, $accountDetails)
    {
        $fields = [
            'id',
            'caption',
            'like_count',
            'comments_count',
            'media_type',
            'media_url',
            'permalink',
            'timestamp'
        ];

        $childrenFields = [
            'id',
            'media_url',
            'media_type',
            'permalink',
        ];
        $list           = implode(',', $childrenFields);
        $fields[]       = "children{{$list}}";
        $query          = [
            'fields'       => implode(',', $fields),
            'user_id'      => $accountDetails['user_id'],
            'access_token' => $accountDetails['access_token'],
            'limit'        => $this->perPage,
        ];

        return "https://graph.facebook.com/v9.0/{$hashtagId}/{$hashtagType}?" . http_build_query($query);
    }

    public function getNextPageUrlResponse($nextUrl, $response)
    {
        $posts   = [];
        $posts = array_merge($posts, $response['data']);
        $limit   = apply_filters('wpsocialreviews/instagram_hashtag_feeds_limit', 100);
        $perPage = $this->perPage;
        $pages = ceil($limit/$perPage);

        $x = 1;
        while($x < $pages){
            $x++;
            $nextUrlResponse = (new Common())->makeRequest($nextUrl);
            $posts['data'] = array_merge($posts, $nextUrlResponse['data']);
        }

        return $posts;
    }
    /**
     * Get hashtag feed by hashtag id from cache or request
     *
     * @param $hashtagId
     * @param $hashtagType
     * @param $accountDetails
     *
     * @return mixed
     * @since 1.3.0
     *
     */
    public function getHashtagFeed($hashtagId, $hashtagType, $accountDetails)
    {
        $accountId     = $accountDetails['user_id'];
        $feedCacheName = "hashtag_feed_id_${accountId}_hashtag_id_${hashtagId}_type_${hashtagType}";
        $userName = $accountDetails['username'];

        //if exists in cache then return it
	    $response = $this->cacheHandler->getFeedCache($feedCacheName);
        if (!$response) {
            //if not in cache then make a request
            $api_url  = $this->getFeedApiUrl($hashtagId, $hashtagType, $accountDetails);
            $response = (new Common())->makeRequest($api_url);
            if(isset($response['paging'])){
                $nextUrl = Arr::get($response, 'paging.next');
                if($nextUrl){
                    $response = $this->getNextPageUrlResponse($nextUrl, $response);
                }
            }

            if ((new Common())->instagramError($response)) {
                return array($userName => $response);
            }

            if (!(new Common())->instagramError($response)) {
                $response = Arr::get($response, 'data', []);
                if (!empty($response)) {
                    $this->cacheHandler->createCache($feedCacheName, $response);
                }
            } else {
                $response = $this->cacheHandler->getFeedCache($feedCacheName);
            }
        }

        return $response;
    }

    /**
     * Build hashtag id api URL
     *
     * @param $hashtag
     * @param $account
     *
     * @return string
     * @since 1.3.0
     *
     */
    public function getHashtagIdApi($hashtag, $account)
    {
        $q = [
            'q'            => $hashtag,
            'user_id'      => $account['user_id'],
            'access_token' => $account['access_token'],
            'limit'        => 1,
        ];

        return 'https://graph.facebook.com/ig_hashtag_search?' . http_build_query($q);
    }

    /**
     * Get hashtag node id of a specific tag
     *
     * @param $hashtag
     * @param $account
     *
     * @return string/null
     * @since 1.3.0
     *
     */
    public function getHashtagId($hashtag, $account)
    {
        $hashtagCache = "hashtag_{$hashtag}";
	    $hashtagId = $this->cacheHandler->getFeedCache($hashtagCache);
        if (!$hashtagId) {
            $api_url  = $this->getHashtagIdApi($hashtag, $account);
            $response = (new Common())->makeRequest($api_url);

            if ((new Common())->instagramError($response)) {
                return $response;
            }

            if (!(new Common())->instagramError($response)) {
                $data = Arr::get($response, 'data', []);
                if (count($data) === 0) {
                    return null;
                }
                $hashtag   = $data[0];
                $hashtagId = Arr::get($hashtag, 'id', null);
                if ($hashtagId) {
                    $this->cacheHandler->createCache($hashtagCache, $hashtagId);
                }
            } else {
                $hashtagId = $this->cacheHandler->getFeedCache($hashtagCache);
            }
        }

        return $hashtagId;
    }
}

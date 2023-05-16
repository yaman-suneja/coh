<?php

namespace WPSocialReviewsPro\Hooks;

use WPSocialReviews\App\Services\Platforms\Feeds\Instagram\Common;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * ActionHooks Class
 * @since 1.1.4
 */
class Backend
{
    public function __construct()
    {
        // reviews hooks
        add_filter('wpsocialreviews/airbnb_reviews_limit_end_point', array($this, 'airbnb_reviews_limit_end_point'));

        // twitter feed hooks
        add_filter('wpsocialreviews/set_twitter_transient_name', array($this, 'set_twitter_transient_name'), 10, 4);
        add_filter('wpsocialreviews/set_twitter_api_base_url', array($this, 'set_twitter_api_base_url'), 10, 2);
        add_filter('wpsocialreviews/twitter_set_get_field', array($this, 'twitter_set_get_field_end_point'), 10, 3);
        add_filter('wpsocialreviews/twitter_feed_response', array($this, 'twitter_feed_response'));
        add_filter('wpsocialreviews/twitter_feed_header_api_response', array($this, 'twitter_feed_header_api_response'),
            10, 3);

        add_filter('wpsocialreviews/youtube_playlist_api_url_details', array($this, 'youtube_playlist_api_url_details'),
            10, 3);
        add_filter('wpsocialreviews/youtube_search_api_url_details', array($this, 'youtube_search_api_url_details'), 10,
            3);
        add_filter('wpsocialreviews/youtube_live_streams_api_url_details',
            array($this, 'youtube_live_streams_api_url_details'), 10, 4);
        add_filter('wpsocialreviews/youtube_single_video_statistics', array($this, 'youtube_single_video_statistics'),
            10, 2);
        add_filter('wpsocialreviews/youtube_single_video_comments_api',
            array($this, 'youtube_single_video_comments_api'));
        add_filter('wpsocialreviews/youtube_single_video_comments', array($this, 'youtube_single_video_comments'), 10,
            4);
        add_filter('wpsocialreviews/youtube_api_parts', array($this, 'youtube_api_parts'), 10, 2);

        //instagram feed filters hooks
        add_filter('wpsocialreviews/fetch_instagram_comments', array($this, 'fetch_instagram_comments'), 10, 2);
        add_filter('wpsocialreviews/instagram_feeds_limit', array($this, 'instagram_feeds_limit'));
        add_filter('wpsocialreviews/instagram_feeds_by_popularity', array($this, 'feeds_by_popularity'), 10, 2);
        add_filter('wpsocialreviews/youtube_feeds_by_popularity', array($this, 'youtube_feeds_by_popularity'), 10, 2);
        add_filter('wpsocialreviews/twitter_feeds_by_popularity', array($this, 'twitter_feeds_by_popularity'), 10, 2);
        add_filter('wpsocialreviews/facebook_feeds_by_popularity', array($this, 'facebook_feeds_by_popularity'), 10, 2);
        add_filter('wpsocialreviews/feeds_by_random', array($this, 'feeds_by_random'));
        add_filter('wpsocialreviews/include_or_exclude_feed', array($this, 'include_or_exclude_feed'), 10, 3);
        add_filter('wpsocialreviews/hide_feed', array($this, 'hide_feed'), 10, 2);

        // facebook feed hooks
        add_filter('wpsocialreviews/facebook_timeline_feed_api_fields', array($this, 'facebook_timeline_feed_api_fields'));
        add_filter('wpsocialreviews/facebook_video_feed_api_details', array($this, 'facebook_video_feed_api_details'), 10, 4);
        add_filter('wpsocialreviews/facebook_photo_feed_api_details', array($this, 'facebook_photo_feed_api_details'), 10, 4);
    }

    public function airbnb_reviews_limit_end_point()
    {
        return 100;
    }

    public function hide_feed($hidePostIds, $feedId)
    {
        $hasHidePost = false;
        foreach ($hidePostIds as $id) {
            if (!empty($id) && !empty($feedId)) {
                if ($id === $feedId) {
                    $hasHidePost = true;
                    break;
                } elseif (strpos($feedId, $id) !== false) {
                    $hasHidePost = true;
                    break;
                }
            }
        }

        return $hasHidePost;
    }

    public function include_or_exclude_feed($hasIncludeWord, $includesWords, $post_caption)
    {
        $hasIncludeWord = false;
        foreach ($includesWords as $includeWord) {
            if (!empty($includeWord)) {
                $modified_include_word = trim(str_replace('+', ' ',
                    urlencode(str_replace(array('#', '@'), array(' HASHTAG', ' MENTION'), strtolower($includeWord)))));

                if (preg_match('/\b' . $modified_include_word . '\b/i', $post_caption, $matches)) {
                    $hasIncludeWord = true;
                }
            }
        }

        return $hasIncludeWord;
    }

    public function feeds_by_random($feeds)
    {
        $count = count($feeds);
        if ($count < 2) {
            return $feeds;
        }

        $currIdx = $count - 1;
        while ($currIdx !== 0) {
            $randIdx         = rand(0, $currIdx - 1);
            $temp            = $feeds[$currIdx];
            $feeds[$currIdx] = $feeds[$randIdx];
            $feeds[$randIdx] = $temp;
            $currIdx--;
        }

        return $feeds;
    }

    public function getLikesCount($feed)
    {
        return isset($feed['like_count']) ? $feed['like_count'] : 0;
    }

    public function getCommentsCount($feed)
    {
        return isset($feed['comments_count']) ? $feed['comments_count'] : 0;
    }

    public function feeds_by_popularity($feeds, $popularity_type)
    {
        $multiply = ($popularity_type === 'most_popular') ? -1 : 1;
        usort($feeds, function ($m1, $m2) use ($multiply) {
            $sum1 = $this->getLikesCount($m1) + $this->getCommentsCount($m1);
            $sum2 = $this->getLikesCount($m2) + $this->getCommentsCount($m2);


            if($sum1 == $sum2) {
                return 0;
            }

            if($sum1 < $sum2) {
                return -1 * $multiply;
            }
            return 1 * $multiply;
        });

        return $feeds;
    }

    public function getTwitterLikesCount($feed)
    {
        $feed = isset($feed['retweeted_status']) ? $feed['retweeted_status'] : $feed;

        return isset($feed['favorite_count']) ? $feed['favorite_count'] : 0;
    }

    public function getTwitterCommentsCount($feed)
    {
        $feed = isset($feed['retweeted_status']) ? $feed['retweeted_status'] : $feed;

        return isset($feed['retweet_count']) ? $feed['retweet_count'] : 0;
    }

    public function twitter_feeds_by_popularity($feeds, $popularity_type)
    {
        $multiply = ($popularity_type === 'most_popular') ? -1 : 1;
        usort($feeds, function ($m1, $m2) use ($multiply) {
            $sum1 = $this->getTwitterLikesCount($m1) + $this->getTwitterCommentsCount($m1);
            $sum2 = $this->getTwitterLikesCount($m2) + $this->getTwitterCommentsCount($m2);

            if($sum1 == $sum2) {
                return 0;
            }

            if($sum1 < $sum2) {
                return -1 * $multiply;
            }
            return 1 * $multiply;
        });

        return $feeds;
    }

    public function getFacebookLikesCount($feed)
    {
        $sum = 0;

        if(isset($feed['like']) && isset($feed['love']) && isset($feed['wow']) && isset($feed['haha']) && isset($feed['sad']) && isset($feed['angry'])) {
            $sum += $feed['like']['summary']['total_count'];
            $sum += $feed['love']['summary']['total_count'];
            $sum += $feed['wow']['summary']['total_count'];
            $sum += $feed['haha']['summary']['total_count'];
            $sum += $feed['sad']['summary']['total_count'];
            $sum += $feed['angry']['summary']['total_count'];
        }

        return $sum;
    }

    public function getFacebookCommentsCount($feed)
    {
       return isset($feed['comments']['summary']['total_count']) ? $feed['comments']['summary']['total_count'] : 0;
    }

    public function facebook_feeds_by_popularity($feeds, $popularity_type)
    {
        $multiply = ($popularity_type === 'most_popular') ? -1 : 1;
        usort($feeds, function ($m1, $m2) use ($multiply) {
            $sum1 = $this->getFacebookLikesCount($m1) + $this->getFacebookCommentsCount($m1);
            $sum2 = $this->getFacebookLikesCount($m2) + $this->getFacebookCommentsCount($m2);

            if($sum1 == $sum2) {
                return 0;
            }

            if($sum1 < $sum2) {
                return -1 * $multiply;
            }
            return 1 * $multiply;
        });

        return $feeds;
    }

    public function set_twitter_transient_name($transient_name, $feed_type, $count, $hashtag)
    {
        if ($feed_type === 'hashtag') {
            $hashtags       = $this->validateHashtags($hashtag);
            $hashtags       = implode(',', $hashtags);
            $hashtags       = str_replace(',', '', $hashtags);
            $hashtags       = preg_replace('/\s+/', '', $hashtags);
            $transient_name = $feed_type . '_' . $hashtags . '_num' . $count;
        } else {
            if ($feed_type === 'user_mentions') {
                $transient_name = $feed_type . '_num' . $count . '';
            }
        }

        return $transient_name;
    }

    public function set_twitter_api_base_url($feed_type, $base_feed_url)
    {
        $api_base_url = '';
        if ($feed_type === 'hashtag') {
            $api_base_url = $base_feed_url . 'search/tweets.json';
        } else {
            if ($feed_type === 'user_mentions') {
                $api_base_url = $base_feed_url . 'statuses/mentions_timeline.json';
            }
        }

        return $api_base_url;
    }

    public function twitter_set_get_field_end_point($feed_type, $count, $hashtag)
    {
        $get_field = '';
        if ($feed_type === 'hashtag') {
            //support multiple hashtags
            $hashtags        = $this->validateHashtags($hashtag);
            $endpoint_string = implode(' OR ', $hashtags);
            $get_field       = '?q=' . $endpoint_string . '&result_type=recent&include_entities=true&count=' . intval($count) . "&tweet_mode=extended";
        } else {
            if ($feed_type === 'user_mentions') {
                $get_field = '?count=' . intval($count) . "&tweet_mode=extended";
            }
        }

        return $get_field;
    }

    public function validateHashtags($hashtag)
    {
        $hashtags       = preg_replace("/#{2,}/", '', trim($hashtag));
        $hashtags       = str_replace("OR", ',', $hashtags);
        $hashtags       = str_replace(' ', '', $hashtags);
        $hashtags       = explode(',', $hashtags);
        $valid_hashtags = array();

        if (!empty($hashtags)) {
            foreach ($hashtags as $hashtag) {
                if (substr($hashtag, 0, 1) !== '#' && $hashtag !== '') {
                    $valid_hashtags[] .= '#' . $hashtag;
                } else {
                    $valid_hashtags[] .= $hashtag;
                }
            }
        }

        return $valid_hashtags;
    }

    public function twitter_feed_response($response)
    {
        return $response['statuses'];
    }

    public function twitter_feed_header_api_response($twitter, $name, $base_feed_url)
    {
        $requestMethod        = "GET";
        $userHeaderDetailsUrl = $base_feed_url . 'users/show.json';

        return $twitter->setGetfield('?screen_name=' . $name)->buildOauth($userHeaderDetailsUrl,
            $requestMethod)->performRequest();
    }

    public function getYoutubeLikesCount($feed)
    {
        return isset($feed['statistics']['likeCount']) ? $feed['statistics']['likeCount'] : 0;
    }

    public function getYoutubeCommentsCount($feed)
    {
        return isset($feed['statistics']['commentCount']) ? $feed['statistics']['commentCount'] : 0;
    }

    public function youtube_feeds_by_popularity($feeds, $popularity_type)
    {
        $multiply = ($popularity_type === 'most_popular') ? -1 : 1;
        usort($feeds, function ($m1, $m2) use ($multiply) {
            $sum1 = $this->getYoutubeLikesCount($m1) + $this->getYoutubeCommentsCount($m1);
            $sum2 = $this->getYoutubeLikesCount($m2) + $this->getYoutubeCommentsCount($m2);

            if($sum1 == $sum2) {
                return 0;
            }

            if($sum1 < $sum2) {
                return -1 * $multiply;
            }
            return 1 * $multiply;
        });

        return $feeds;
    }

    public function youtube_playlist_api_url_details($playlist_id, $total, $fetch_url)
    {
        if (empty($playlist_id) || !$playlist_id) {
            return array('error_message' => __('Please enter playlist id to fetch videos!! ', 'wp-social-ninja-pro'));
        }

        $feedCacheName     = 'playlist_feed_id_' . $playlist_id . '_num_' . $total;
        $youtubeFeedApiUrl = $fetch_url . 'playlistItems?part=id,snippet&maxResults=' . $total . '&playlistId=' . $playlist_id . '&';

        return array(
            'cache_name' => $feedCacheName,
            'api_url'    => $youtubeFeedApiUrl
        );
    }

    public function youtube_search_api_url_details($search_term, $total, $fetch_url)
    {
        if (empty($search_term)) {
            return array('error_message' => __('Please enter search term to fetch videos!! ', 'wp-social-ninja-pro'));
        }

        $feedCacheName     = 'search_feed_search_term_' . $search_term . '_num_' . $total;
        $youtubeFeedApiUrl = $fetch_url . 'search?part=id,snippet&q=' . $search_term . '&order=date&maxResults=' . $total . '&';

        return array(
            'cache_name' => $feedCacheName,
            'api_url'    => $youtubeFeedApiUrl
        );
    }

    public function youtube_live_streams_api_url_details($channel_id, $event_type, $total, $fetch_url)
    {
        if (empty($channel_id)) {
            return array('error_message' => __('Please enter a channel id to fetch videos!! ', 'wp-social-ninja-pro'));
        }

        if (strpos($channel_id, 'UC') === false) {
            $message = __('Please enter a valid channel id!! ', 'wp-social-ninja-pro');
            wp_send_json_error(['message' => $message], 423);
        }

        $feedCacheName     = 'live_streams_feed_id_' . $channel_id . '_num_' . $total . '_event_type_' . $event_type;
        $youtubeFeedApiUrl = $fetch_url . 'search?part=id,snippet&channelId=' . $channel_id . '&order=date&maxResults=' . $total . '&type=video&eventType=' . $event_type . '&';

        return array(
            'cache_name' => $feedCacheName,
            'api_url'    => $youtubeFeedApiUrl
        );
    }

    public function youtube_single_video_statistics($video_id, $fetch_url)
    {
        return $fetch_url . 'videos?part=contentDetails,statistics&id=' . $video_id . '&';
    }

    public function youtube_single_video_comments_api($video_id)
    {
        return 'https://www.googleapis.com/youtube/v3/commentThreads?textFormat=plainText&part=snippet&videoId=' . $video_id . '&maxResults=10&';
    }

    public function youtube_single_video_comments($videoIds, $feedCacheName, $youtubeApiKeyOrToken, $obj)
    {
        $comments = array();
        if (strpos($feedCacheName, 'channel_header') === false && strpos($youtubeApiKeyOrToken, 'key') !== false) {
            foreach ($videoIds as $videoId) {
                $commentApiUrl      = apply_filters('wpsocialreviews/youtube_single_video_comments_api', $videoId);
                $videoComment       = $obj->getAPIData($commentApiUrl . $youtubeApiKeyOrToken);
                $comments[$videoId] = $videoComment;
            }
        }

        return $comments;
    }

    public function youtube_api_parts($parts, $feed_type)
    {
        if ($feed_type === 'live_streams_feed') {
            return $parts . ',statistics,liveStreamingDetails,contentDetails';
        } else {
            return $parts . ',statistics,contentDetails';
        }
    }

    public function instagram_feeds_limit()
    {
        return 200;
    }

    public function fetch_instagram_comments($response, $accountDetails)
    {
        if (isset($accountDetails['api_type']) && $accountDetails['api_type'] === 'business') {
            $mediaIds = array_map(function ($value) {
                if ($value['comments_count'] > 0) {
                    return $value['id'];
                }
            }, $response);
            $mediaIds = array_filter($mediaIds);
            $mediaIds = array_slice($mediaIds, 0, 50);

            $fields = [
                'id',
                'username',
                'text',
                'timestamp',
                'like_count',
            ];

            $q = [
                'ids'          => implode(',', $mediaIds),
                'fields'       => implode(',', $fields),
                'access_token' => $accountDetails['access_token'],
                'limit'        => 10,
            ];

            $apiUrl = "https://graph.facebook.com/comments?" . http_build_query($q);

            if (filter_var($apiUrl, FILTER_VALIDATE_URL)) {
                $comments = (new Common())->makeRequest($apiUrl);
                foreach ($response as $idx => $media) {
                    $mediaId = $media['id'];
                    if (!isset($comments[$mediaId])) {
                        continue;
                    }
                    $response[$idx]['comments'] = $comments[$mediaId]['data'];
                }
            }
        }

        return $response;
    }

    public function facebook_timeline_feed_api_fields($fields)
    {
        $reactions = ',reactions.type(LIKE).limit(0).summary(total_count).as(like),reactions.type(LOVE).limit(0).summary(total_count).as(love),reactions.type(WOW).limit(0).summary(total_count).as(wow),reactions.type(HAHA).limit(0).summary(total_count).as(haha),reactions.type(SAD).limit(0).summary(total_count).as(sad),reactions.type(ANGRY).limit(0).summary(total_count).as(angry),reactions.type(THANKFUL).limit(0).summary(total_count).as(thankful)';
        return $fields . ',comments.summary(true)'.$reactions;
    }

    public function facebook_video_feed_api_details($remoteFetchUrl, $pageId, $totalFeed, $accessToken)
    {
        $fetchUrl = $remoteFetchUrl . $pageId . '/videos?fields=id,created_time,updated_time,description,from{name,id,picture{url},link},source,length,permalink_url,format{height,width,filter,picture}&limit='.$totalFeed.'&access_token=' . $accessToken;
        return $fetchUrl;
    }

    public function facebook_photo_feed_api_details($remoteFetchUrl, $pageId, $totalFeed, $accessToken)
    {
        $fetchUrl = $remoteFetchUrl . $pageId . '/photos?fields=id,created_time,updated_time,caption,link,images,name,from{name,id,picture{url},link}&type=uploaded&limit='.$totalFeed.'&access_token=' . $accessToken;
        return $fetchUrl;
    }
}
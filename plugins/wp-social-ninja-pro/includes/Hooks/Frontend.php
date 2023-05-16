<?php

namespace WPSocialReviewsPro\Hooks;

use WPSocialReviews\App\Hooks\Handlers\ShortcodeHandler;
use WPSocialReviews\App\Services\Platforms\Feeds\Instagram\InstagramFeed;
use WPSocialReviewsPro\Classes\Feeds\Twitter\ManageCard;

use WPSocialReviews\Framework\Foundation\App;
use WPSocialReviews\App\Services\Platforms\Feeds\Twitter\Helper as TwitterHelper;
use WPSocialReviews\App\Services\Platforms\Feeds\Youtube\Helper as YoutubeHelper;
use WPSocialReviews\App\Services\Platforms\Feeds\Instagram\Helper as InstagramHelper;
use WPSocialReviews\App\Services\Platforms\Feeds\Facebook\Helper as FacebookHelper;
use WPSocialReviews\App\Services\Helper as GlobalHelper;
use WPSocialReviews\Framework\Support\Arr;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * ActionHooks Class
 * @since 1.1.4
 */
class Frontend
{
    public function __construct()
    {
        // reviews hooks
        add_filter('wpsocialreviews/add_reviews_template', array($this, 'add_reviews_template'), 10, 3);
        //add_filter('wpsocialreviews/reviews_template_base_path', array($this, 'update_template_base_path'), 10, 2);
        add_action('wpsocialreviews/render_reviews_template_business_info',
            array($this, 'render_reviews_template_business_info'), 10, 5);
        add_filter('wpsocialreviews/add_reviews_badge_template', array($this, 'add_reviews_badge_template'), 10, 4);
        add_filter('wpsocialreviews/add_reviews_notification_template', array($this, 'add_reviews_notification_template'), 10, 3);
        add_action('wpsocialreviews/render_reviews_write_a_review_btn', array($this,'render_reviews_write_a_review_btn'), 10, 5);

        // chats hooks
        add_filter('wpsocialreviews/display_user_online_status', array($this, 'update_display_user_online_status'), 10,
            2);
        add_action('wpsocialreviews/render_chat_css', array($this, 'render_chat_css'));

        // twitter feed hooks
        add_filter('wpsocialreviews/add_twitter_template', array($this, 'add_twitter_template'));
        add_filter('wpsocialreviews/render_twitter_template_header', array($this, 'render_twitter_template_header'), 10,
            3);
        /**
         * Tweeter User Profile Banner
         *
         * @see wpsr_render_tweeter_profile_banner_html()
         * @since  1.2.4
         */
        add_action('wpsocialreviews/tweeter_profile_banner', array($this, 'wpsr_render_tweeter_profile_banner_html'),
            10, 2);
        /**
         * Tweeter User Profile Picture
         *
         * @see wpsr_render_tweeter_user_profile_picture_html()
         * @since  1.2.4
         */
        add_action('wpsocialreviews/tweeter_user_profile_picture',
            array($this, 'wpsr_render_tweeter_user_profile_picture_html'), 10, 2);
        /**
         * Tweeter User Profile Follow Button
         *
         * @see wpsr_render_tweeter_user_profile_follow_btn_html()
         * @since  1.2.4
         */
        add_action('wpsocialreviews/tweeter_user_profile_follow_btn',
            array($this, 'wpsr_render_tweeter_user_profile_follow_btn_html'), 10, 2);
        /**
         * Tweeter User Profile Info
         *
         * @see wpsr_render_tweeter_user_profile_info_username_html()
         * @see wpsr_render_tweeter_user_profile_info_name_html()
         * @since  1.2.4
         */
        add_action('wpsocialreviews/tweeter_user_profile_info',
            array($this, 'wpsr_render_tweeter_user_profile_info_name_html'), 5, 2);
        add_action('wpsocialreviews/tweeter_user_profile_info',
            array($this, 'wpsr_render_tweeter_user_profile_info_username_html'), 10, 2);
        /**
         * Tweeter User Profile Description
         *
         * @see wpsr_render_tweeter_user_profile_description_html()
         * @since  1.2.4
         */
        add_action('wpsocialreviews/tweeter_user_profile_description',
            array($this, 'wpsr_render_tweeter_user_profile_description_html'), 10, 2);

        /**
         * Tweeter User Profile Address
         *
         * @see wpsr_render_tweeter_user_address_html()
         * @since  1.2.4
         */
        add_action('wpsocialreviews/tweeter_user_address', array($this, 'wpsr_render_tweeter_user_address_html'), 10,
            2);

        /**
         * Tweeter User Profile Statistics
         *
         * @see wpsr_render_tweeter_user_profile_statistics_html()
         * @since  1.2.4
         */
        add_action('wpsocialreviews/tweeter_user_profile_statistics',
            array($this, 'wpsr_render_tweeter_user_profile_statistics_html'), 10, 3);
        add_action('wp_ajax_nopriv_wpsr_twitter_cards', array($this, 'generate_twitter_cards'));
        add_action('wp_ajax_wpsr_twitter_cards', array($this, 'generate_twitter_cards'));

        /**
         *
         * Youtube template hooks and filters
         * @since  1.2.5
         *
         */

        /**
         * Youtube Channel Statistics
         *
         * @see wpsr_render_channel_statistics_html()
         * @since  1.2.5
         */
        add_action('wpsocialreviews/youtube_channel_statistics', array($this, 'wpsr_render_channel_statistics_html'),
            10, 3);
        /**
         * Youtube Channel Subscribe Button
         *
         * @see wpsr_render_youtube_channel_subscribe_btn_html()
         * @since  1.2.5
         */
        add_action('wpsocialreviews/youtube_channel_subscribe_btn',
            array($this, 'wpsr_render_youtube_channel_subscribe_btn_html'), 10, 2);


        /**
         * Youtube Feed Description
         *
         * @see wpsr_render_youtube_feed_description_html()
         * @since  1.2.5
         */
        add_action('wpsocialreviews/youtube_feed_description',
            array($this, 'wpsr_render_youtube_feed_description_html'), 10, 2);

        /**
         * Youtube Feed Statistics
         *
         * @see wpsr_render_youtube_feed_statistics_html()
         * @since  1.2.5
         */
        add_action('wpsocialreviews/youtube_feed_statistics', array($this, 'wpsr_render_youtube_feed_statistics_html'),
            10, 5);
        add_action('wpsocialreviews/youtube_popup_content', array($this, 'youtube_popup_content_html'), 10, 3);

        /**
         * Youtube Channel Description
         *
         * @see wpsr_render_channel_description_html()
         * @since  1.2.5
         */
        add_action('wpsocialreviews/youtube_channel_description', array($this, 'wpsr_render_channel_description_html'),
            10, 2);


        /**
         * Youtube Prev Next Pagination
         *
         * @see wpsr_render_popup_title_html()
         * @since  1.2.5
         */
        add_action('wpsocialreviews/render_youtube_prev_next_pagination',
            array($this, 'render_youtube_prev_next_pagination'), 10, 4);

        /**
         * Retrieve youtube popupbox data
         *
         * @since  1.2.5
         */

        add_filter('wpsr_feed_items_by_page_instagram', array($this, 'getPaginatedInstaFeedHtml'), 10, 3);

        /**
         * Instagram Feed Statistics
         *
         * @see render_instagram_post_statistics_html()
         * @since  1.3.0
         */
        add_action('wpsocialreviews/instagram_post_statistics', array($this, 'render_instagram_post_statistics_html'),
            10, 2);

        /**
         * Instagram follow button HTML
         *
         * @see render_instagram_follow_button_html()
         * @since  1.3.0
         */
        add_action('wpsocialreviews/instagram_follow_button', array($this, 'render_instagram_follow_button_html'));


        /**
         * Instagram Header Statistics HTML
         *
         * @see render_instagram_header_statistics_html()
         * @since  1.3.0
         */
        add_action('wpsocialreviews/instagram_header_statistics',
            array($this, 'render_instagram_header_statistics_html'), 10, 3);

        /**
         * Instagram Trim Caption Words
         *
         * @see instagram_trim_caption_words()
         * @since  1.3.0
         */
        add_filter('wpsocialreviews/instagram_trim_caption_words', array($this, 'instagram_trim_caption_words'), 10, 2);


        /**
         * Instagram Ajax Load More
         *
         * @see instagram_load_more_ajax_handler()
         * @since  1.3.0
         */
        add_action('wp_ajax_load_more_instagram_ajax_handler', array($this, 'instagram_load_more_ajax_handler'));
        add_action('wp_ajax_nopriv_load_more_instagram_ajax_handler', array($this, 'instagram_load_more_ajax_handler'));

        /**
         * Facebook Feed Like button HTML
         *
         * @see render_facebook_feed_like_button_html()
         * @since  4.0.0
         */
        add_action('wpsocialreviews/facebook_feed_like_button', array($this, 'render_facebook_feed_like_button_html'), 10, 2);

        /**
         * Facebook Feed Share button HTML
         *
         * @see render_facebook_feed_share_button_html()
         * @since  4.0.0
         */
        add_action('wpsocialreviews/facebook_feed_share_button', array($this, 'render_facebook_feed_share_button_html'), 10, 2);

        /**
         * Facebook Feed Statistics HTML
         *
         * @see render_facebook_feed_statistics()
         * @since  4.0.0
         */
        add_action('wpsocialreviews/facebook_feed_statistics', array($this, 'render_facebook_feed_statistics'), 10, 3);

        /**
         * Facebook Feed Videos HTML
         *
         * @see render_facebook_feed_videos()
         * @since  4.0.0
         */
        add_action('wpsocialreviews/facebook_feed_videos', array($this, 'render_facebook_feed_videos'), 10, 2);
        add_action('wpsocialreviews/facebook_feed_summary_card_image', array($this, 'render_facebook_feed_summary_card_image'), 10, 2);
        add_action('wpsocialreviews/facebook_feed_image', array($this, 'render_facebook_feed_image'), 10, 2);
        add_action('wpsocialreviews/facebook_feed_photo_feed_image', array($this, 'render_facebook_feed_photo_feed_image'), 10, 3);
    }


    public function add_reviews_template($template, $reviews, $template_meta)
    {
        $templateMapping = [
            'grid6' => 'reviews-templates/template6',
            'grid7' => 'reviews-templates/template7',
            'grid8' => 'reviews-templates/template8',
            'grid9' => 'reviews-templates/template9',
        ];

        if (!isset($templateMapping[$template])) {
            return __('No templates found!! Please save template and try again', 'wp-social-ninja-pro');
        }

        return $this->loadView($templateMapping[$template], array(
            'reviews'       => $reviews,
            'template_meta' => $template_meta,
        ));
    }


//    public function update_template_base_path($path, $fileName)
//    {
//
//        $templates = [
//            'reviews-templates/template6',
//            'reviews-templates/template7',
//            'reviews-templates/template8',
//            'reviews-templates/template9',
//            'reviews-templates/header',
//
//            'feed-templates/twitter/header',
//            'feed-templates/twitter/template2',
//        ];
//
//        $filePath = '';
//
//        if (in_array($fileName, $templates)) {
//            $filePath = WPSOCIALREVIEWS_PRO_DIR . 'includes/views/';
//        } else {
//            $filePath = $path;
//        }
//
//        return $filePath;
//    }

    public function render_reviews_template_business_info($reviews = [], $business_info = [], $template_meta = [], $templateId = null, $translations = [])
    {
        if ((isset($template_meta['show_header']) && $template_meta['show_header'] === 'true') && !empty($business_info) && defined('WPSOCIALREVIEWS_PRO')) {
            $platformNames = array_column($business_info['platforms'], 'platform_name');
            $isBooking = false;
            if(in_array('booking.com', $platformNames)) {
                if(count(array_unique($platformNames)) === 1 && end($platformNames) === 'booking.com') {
                    $isBooking = true;
                }
            }

            echo $this->loadView('reviews-templates/business_info', array(
                'reviews'       => $reviews,
                'business_info' => $business_info,
                'template_meta' => $template_meta,
                'isBooking'     => $isBooking,
                'templateId'    => $templateId,
                'translations'  => $translations
            ));
        }
    }

    public function add_reviews_badge_template($templateId = null, $templateType = '', $business_info = [], $badge_settings = [])
    {
        return $this->loadView('reviews-templates/badge1', array(
            'templateId'     => $templateId,
            'templateType'   => $templateType,
            'business_info'  => $business_info,
            'badge_settings' => $badge_settings
        ));
    }

    public function render_reviews_write_a_review_btn($template_meta = [], $templateType = '', $business_info = [], $templateId = null, $translations = [])
    {
        $html = $this->loadView('reviews-templates/write-a-review-btn', array(
            'templateId'    => $templateId,
            'template_meta' => $template_meta,
            'templateType'  => $templateType,
            'business_info' => $business_info,
            'translations'  => $translations
        ));
        echo $html;
    }

    public function add_reviews_notification_template($templateId, $templateMeta, $reviews)
    {
        return $this->loadView('reviews-templates/notification', array(
            'templateId'     => $templateId,
            'templateMeta'   => $templateMeta,
            'reviews'        => $reviews
        ));
    }

    public function update_display_user_online_status($settings)
    {
        $days = array(
            __('Saturday', 'wp-social-ninja-pro'),
            __('Sunday', 'wp-social-ninja-pro'),
            __('Monday', 'wp-social-ninja-pro'),
            __('Tuesday', 'wp-social-ninja-pro'),
            __('Wednesday', 'wp-social-ninja-pro'),
            __('Thursday', 'wp-social-ninja-pro'),
            __('Friday', 'wp-social-ninja-pro')
        );

        //day params
        $dataParams                    = array();
        $dataParams['dayTimeSchedule'] = isset($settings['day_time_schedule']) ? $settings['day_time_schedule'] : 'false';
        $dataParams['dayLists']        = isset($settings['day_list']) ? $settings['day_list'] : $days;

        //time params
        $dataParams['timeSchedule'] = isset($settings['time_schedule']) ? $settings['time_schedule'] : 'false';
        $dataParams['startTime']    = isset($settings['start_time']) ? $settings['start_time'] : '';
        $dataParams['endTime']      = isset($settings['end_time']) ? $settings['end_time'] : '';

        return $dataParams;
    }

    public function render_chat_css($settings)
    {
        if ($settings['platform'] === 'messenger' && isset($settings['additional_settings']['chat_bubble_scroll_position'])) {
            ?>
            <style type="text/css">

                <?php if( $settings['additional_settings']['chat_bubble_position'] === 'top-left' || $settings['additional_settings']['chat_bubble_position'] === 'top-right') { ?>
                .wpsr-fm-chat-wrapper.wpsr-multiplatform-chat.wpsr-chat-messenger {
                    top: <?php echo $settings['additional_settings']['chat_bubble_scroll_position'] .'px !important;'; ?>
                }

                .wpsr-fm-chat-wrapper.wpsr-multiplatform-chat.wpsr-chat-messenger .wpsr-fm-chat-box-display {
                    top: <?php echo 100 + $settings['additional_settings']['chat_bubble_scroll_position'] .'px !important;'; ?>
                }

                <?php } ?>

                <?php if( $settings['additional_settings']['chat_bubble_position'] === 'bottom-left' || $settings['additional_settings']['chat_bubble_position'] === 'bottom-right') { ?>
                .wpsr-fm-chat-wrapper.wpsr-chat-messenger {
                    bottom: <?php echo 20 + $settings['additional_settings']['chat_bubble_scroll_position'] .'px;'; ?>

                }

                .wpsr-fm-chat-wrapper.wpsr-chat-messenger .wpsr-fm-chat-box-display {
                    margin-bottom: <?php echo 100 + $settings['additional_settings']['chat_bubble_scroll_position'] .'px;'; ?>
                }

                <?php } ?>
            </style>
        <?php }

    }


    public function add_twitter_template($data = [])
    {
        return $this->loadView('feeds-templates/twitter/template2', $data);
    }

    public function render_twitter_template_header($header = [], $feed_settings = [], $translations = [])
    {
        return $this->loadView('feeds-templates/twitter/header', array(
            'header'        => $header,
            'template_meta' => $feed_settings,
            'translations'  => $translations
        ));
    }

    /**
     *
     * Render User Profile Banner HTML
     *
     * @param $header
     * @param $template_meta
     *
     * @since 1.2.4
     *
     **/
    public function wpsr_render_tweeter_profile_banner_html($header = [], $template_meta = [])
    {
        if (isset($template_meta['header_settings']) && $template_meta['header_settings']['show_banner_image'] === 'false') {
            return;
        }
        if (isset($header['profile_banner_url'])) {
            ?>
            <div class="wpsr-twitter-user-profile-banner">
                <img src="<?php echo esc_url($header['profile_banner_url']); ?>" alt="">
            </div>
            <?php
        }
    }


    /**
     *
     * Render User Profile Picture HTML
     *
     * @param $header
     * @param $template_meta
     *
     * @since 1.2.4
     *
     **/
    public function wpsr_render_tweeter_user_profile_picture_html($header = [], $template_meta = [])
    {
        if (isset($template_meta['header_settings']) && $template_meta['header_settings']['show_avatar'] === 'false') {
            return;
        }
        ?>
        <a class="wpsr-twitter-user-profile-pic"
           href="<?php echo esc_url('https://twitter.com/' . $header['screen_name']); ?>"
           rel="noopener noreferrer"
           target="_blank">
            <img src="<?php echo esc_url($header['profile_image_url']); ?>" alt="">
        </a>
        <?php
    }

    /**
     *
     * Render User Profile Follow Button HTML
     *
     * @param $header
     * @param $settings
     *
     * @since 1.2.4
     *
     **/
    public function wpsr_render_tweeter_user_profile_follow_btn_html($header = [], $settings = [])
    {
        if ($settings['display_follow_button'] === 'false') {
            return;
        }
        ?>
        <a class="wpsr-twitter-user-follow-btn"
           href="<?php echo esc_url('https://twitter.com/intent/follow?screen_name=' . $header['screen_name']); ?>"
           rel="noopener" target="_blank">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18">
                <path d="M16.07 5.388c.219 4.83-3.395 10.216-9.79 10.216A9.765 9.765 0 0 1 1 14.06a6.94 6.94 0 0 0 5.1-1.421 3.446 3.446 0 0 1-3.218-2.385c.54.103 1.07.073 1.555-.06-1.656-.33-2.8-1.82-2.763-3.41.465.258.996.412 1.56.43A3.432 3.432 0 0 1 2.17 2.628a9.788 9.788 0 0 0 7.1 3.589C8.766 4.068 10.4 2 12.624 2c.99 0 1.885.417 2.513 1.084a6.925 6.925 0 0 0 2.188-.833 3.452 3.452 0 0 1-1.515 1.9 6.9 6.9 0 0 0 1.978-.54 6.96 6.96 0 0 1-1.718 1.777z"></path>
            </svg>
            <span><?php echo $settings['follow_button_text']; ?></span>
        </a>
        <?php
    }


    /**
     *
     * Render User Profile Name HTML
     *
     * @param $header
     * @param $template_meta
     *
     * @since 1.2.4
     *
     **/
    public function wpsr_render_tweeter_user_profile_info_name_html($header = [], $template_meta = [])
    {
        if (isset($template_meta['header_settings']) && $template_meta['header_settings']['show_name'] === 'false') {
            return;
        }
        ?>
        <a href="<?php echo esc_url('https://twitter.com/' . $header['screen_name']); ?>" rel="noopener noreferrer" target="_blank"
           class="wpsr-twitter-user-info-name">
            <?php echo $header['name']; ?>
            <?php if ($header['verified']) { ?>
                <span class="wpsr-tweeter-user-verified">
                 <?php echo TwitterHelper::getSvgIcons('verified') ?>
            </span>
            <?php } ?>
        </a>
        <?php
    }

    /**
     *
     * Render User Profile Username HTML
     *
     * @param $header
     * @param $template_meta
     *
     * @since 1.2.4
     *
     **/
    public function wpsr_render_tweeter_user_profile_info_username_html($header = [], $template_meta = [])
    {
        if (isset($template_meta['header_settings']) && $template_meta['header_settings']['show_user_name'] === 'false') {
            return;
        }
        ?>
        <a href="<?php echo esc_url('https://twitter.com/' . $header['screen_name']); ?>" rel="noopener noreferrer" target="_blank"
           class="wpsr-twitter-user-info-username">@<?php echo $header['screen_name']; ?></a>
        <?php
    }


    /**
     *
     * Render User Profile Description HTML
     *
     * @param $header
     * @param $template_meta
     *
     * @since 1.2.4
     *
     **/
    public function wpsr_render_tweeter_user_profile_description_html($header = [], $template_meta = [])
    {
        if (isset($template_meta['header_settings']) && $template_meta['header_settings']['show_description'] === 'false') {
            return;
        }
        if (isset($header['description'])) {
            ?>
            <div class="wpsr-twitter-user-bio">
                <p><?php echo TwitterHelper::formatTweet($header['description']); ?></p>
            </div>
            <?php
        }
    }

    /**
     *
     * Render User Profile Address HTML
     *
     * @param $header
     * @param $template_meta
     *
     * @since 1.2.4
     *
     **/
    public function wpsr_render_tweeter_user_address_html($header = [], $template_meta = [])
    {
        if (empty($header['location']) || (isset($template_meta['header_settings']) && $template_meta['header_settings']['show_location'] === 'false')) {
            return;
        }
        ?>
        <div class="wpsr-twitter-user-contact">
            <i class="wpsr-icon icon-map-marker"></i><span><?php echo $header['location']; ?></span>
        </div>
        <?php
    }

    /**
     *
     * Render User Profile Statistics HTML
     *
     * @param $header
     * @param $template_meta
     *
     * @since 1.2.4
     *
     **/
    public function wpsr_render_tweeter_user_profile_statistics_html($header = [], $template_meta = [], $translations = [])
    {
        ?>
        <div class="wpsr-twitter-user-statistics">
            <?php if (isset($template_meta['header_settings']) && $template_meta['header_settings']['show_total_tweets'] === 'true') { ?>
                <div class="wpsr-twitter-user-statistics-item">
                    <span class="wpsr-twitter-user-statistics-item-name"><?php echo Arr::get($translations, 'tweets'); ?></span>
                    <span class="wpsr-twitter-user-statistics-item-data"><?php echo GlobalHelper::shortNumberFormat($header['statuses_count']); ?></span>
                </div>
            <?php } ?>

            <?php if (isset($template_meta['header_settings']) && $template_meta['header_settings']['show_following'] === 'true') { ?>
                <div class="wpsr-twitter-user-statistics-item">
                    <span class="wpsr-twitter-user-statistics-item-name"><?php echo  Arr::get($translations, 'following'); ?></span>
                    <span class="wpsr-twitter-user-statistics-item-data"><?php echo GlobalHelper::shortNumberFormat($header['friends_count']); ?></span>
                </div>
            <?php } ?>

            <?php if (isset($template_meta['header_settings']) && $template_meta['header_settings']['show_followers'] === 'true') { ?>
                <div class="wpsr-twitter-user-statistics-item">
                    <span class="wpsr-twitter-user-statistics-item-name"><?php echo Arr::get($translations, 'followers'); ?></span>
                    <span class="wpsr-twitter-user-statistics-item-data"><?php echo GlobalHelper::shortNumberFormat($header['followers_count']); ?></span>
                </div>
            <?php } ?>
        </div>
        <?php
    }

    public function generate_twitter_cards()
    {
        check_ajax_referer('wpsr-ajax-nonce', 'security');
        $card_items = array();
        foreach ($_POST['wpsr_urls'] as $url) {
            $card_items[] = array(
                'id'  => sanitize_text_field($url['id']),
                'url' => esc_url_raw($url['url'])
            );
        }

        $cards        = (new ManageCard())->processUrl($card_items);
        $twitter_card = array();
        foreach ($cards as $card) {
            $url = $card['url'];
            // $twitter_card = $card['twitter_card'];

            $twitter_card[$card['id']] = array(
                'url'          => $url,
                'twitter_card' => $card['twitter_card'],
                'is_new'       => $card['is_new']
            );
        }
        echo wp_json_encode($twitter_card);
        die();
    }

    /**
     *
     * Render YouTube Channel Statistics HTML
     *
     * @param $header
     * @param $template_header_meta
     *
     * @since 1.2.5
     *
     **/
    public function wpsr_render_channel_statistics_html($header = [], $template_header_meta = [], $translations = [])
    {
        ?>
        <div class="wpsr-yt-header-channel-statistics">
            <?php if (Arr::get($template_header_meta, 'display_subscriber_counter') === 'true' && Arr::get($header,
                    'items.0.statistics.subscriberCount')) { ?>
                <div class="wpsr-yt-header-statistic-item">
                    <?php echo GlobalHelper::shortNumberFormat($header['items'][0]['statistics']['subscriberCount']) .' '. Arr::get($translations, 'subscribers'); ?>
                </div>
            <?php } ?>
            <?php if (Arr::get($template_header_meta, 'display_videos_counter') === 'true' && Arr::get($header,
                    'items.0.statistics.videoCount')) { ?>
                <div class="wpsr-yt-header-statistic-item">
                    <?php echo GlobalHelper::shortNumberFormat($header['items'][0]['statistics']['videoCount']) .' '. Arr::get($translations, 'videos'); ?>
                </div>
            <?php } ?>
            <?php if (Arr::get($template_header_meta, 'display_views_counter') === 'true' && Arr::get($header,
                    'items.0.statistics.viewCount')) { ?>
                <div class="wpsr-yt-header-statistic-item">
                    <?php echo GlobalHelper::shortNumberFormat($header['items'][0]['statistics']['viewCount']) .' '. Arr::get($translations, 'views'); ?>
                </div>
            <?php } ?>
        </div>
        <?php
    }

    /**
     *
     * Render YouTube Channel Description HTML
     *
     * @param $header
     * @param $template_header_meta
     *
     * @since 1.2.5
     *
     **/
    public function wpsr_render_channel_description_html($header = array(), $template_header_meta = [])
    {
        if (Arr::get($template_header_meta, 'display_description') === 'false') {
            return;
        }
        ?>
        <div class="wpsr-yt-header-channel-description">
            <p><?php echo esc_html($header['items'][0]['snippet']['description']); ?></p>
        </div>
        <?php
    }

    /**
     *
     * Render YouTube Channel Subscribe Button HTML
     *
     * @param $header
     * @param $settings
     *
     * @since 1.2.5
     *
     **/
    public function wpsr_render_youtube_channel_subscribe_btn_html($header = [], $settings = [])
    {
        if (Arr::get($settings, 'subscribe_button_text') === '') {
            return;
        }
        ?>
        <div class="wpsr-yt-header-subscribe-btn">
            <a href="<?php echo esc_url('https://www.youtube.com/channel/' . Arr::get($header,
                    'items.0.id') . '?sub_confirmation=1'); ?>"
               target="_blank" rel="noopener noreferrer"><?php echo Arr::get($settings, 'subscribe_button_text'); ?></a>
        </div>
        <?php
    }

    public function wpsr_render_youtube_feed_description_html($feed = [], $template_meta = [])
    {
        if (Arr::get($template_meta, 'video_settings.display_description') === 'false') {
            return;
        }
        ?>
        <p class="wpsr-yt-video-description">
            <?php echo substr($feed['snippet']['description'], 0, 100) . '...'; ?>
        </p>
        <?php
    }

    public function wpsr_render_youtube_feed_statistics_html($feed = [],
                                                             $template_meta = [],
                                                             $feed_info = [],
                                                             $index = null,
                                                             $templateId = null)
    {
        $videoId = YoutubeHelper::getVideoId($feed);
        ?>
        <?php
        if (Arr::get($template_meta, 'video_settings.display_channel_name') === 'true') {
            ?>
            <a href="<?php echo esc_url('https://www.youtube.com/channel/' . $feed['snippet']['channelId']); ?>"
               target="_blank" rel="noopener noreferrer" class="wpsr-yt-channel-title">
                <?php echo esc_html($feed['snippet']['channelTitle']); ?>
            </a>
        <?php } ?>

        <div class="wpsr-yt-video-statistics">
            <?php if (Arr::get($template_meta, 'video_settings.display_views_counter') === 'true' && Arr::get($feed,
                    'statistics.viewCount')) { ?>
                <div class="wpsr-yt-video-statistic-item">
                    <?php echo GlobalHelper::shortNumberFormat($feed['statistics']['viewCount']) . __(' Views',
                            'wp-social-ninja-pro'); ?>
                </div>
            <?php } ?>
            <?php if (Arr::get($template_meta,
                    'video_settings.display_date') === 'true' && $feed_info['feed_type'] !== 'live_streams_feed') { ?>
                <div class="wpsr-yt-video-statistic-item">
                    <?php
                    $publishedAt = date_format(date_create($feed['snippet']['publishedAt']), 'D M j H:i:s O Y');
                    echo sprintf(__('%s ago'), human_time_diff(strtotime($publishedAt)));
                    ?>
                </div>
            <?php } ?>
            <?php if ($feed_info['feed_type'] === 'live_streams_feed' && $feed_info['event_type'] !== 'live') { ?>
                <div class="wpsr-yt-video-statistic-item">
                    <?php if ($feed_info['event_type'] === 'completed') { ?>
                        <span>
                            <?php
                            if (isset($feed['snippet']['publishedAt'])) {
                                $publishedAt = date_format(date_create($feed['snippet']['publishedAt']), 'D M j H:i:s O Y');
                                $human_time_diff = sprintf(__('%s ago'), human_time_diff(strtotime($publishedAt)));

                                echo __('Streamed ', 'wp-social-ninja-pro') . $human_time_diff;
                            } ?>
                            </span>
                    <?php } ?>
                    <?php if ($feed_info['event_type'] === 'upcoming' && Arr::get($feed,
                            'liveStreamingDetails.scheduledStartTime')) { ?>
                        <span><?php echo date("Y-m-d H:i:s",
                                strtotime($feed['liveStreamingDetails']['scheduledStartTime'])); ?></span>
                    <?php } ?>
                </div>
            <?php } ?>
            <?php if (Arr::get($template_meta, 'video_settings.display_likes_counter') === 'true' && Arr::get($feed,
                    'statistics.likeCount')) { ?>
                <div class="wpsr-yt-video-statistic-item">
                    <?php echo GlobalHelper::shortNumberFormat($feed['statistics']['likeCount']) . __(' Likes',
                            'wp-social-ninja-pro'); ?>
                </div>
            <?php } ?>
            <?php if ((Arr::get($template_meta, 'video_settings.display_comments_counter') === 'true' && Arr::get($feed,
                    'statistics.commentCount'))) { ?>
                <div class="wpsr-yt-video-statistic-item">
                    <?php echo GlobalHelper::shortNumberFormat($feed['statistics']['commentCount']) . __(' Comments',
                            'wp-social-ninja-pro'); ?>
                </div>
            <?php } ?>
            <?php if ($feed_info['feed_type'] === 'live_streams_feed' && $feed_info['event_type'] === 'live') { ?>
                <div class="wpsr-yt-video-statistic-item">
                    <a class="wpsr-yt-video-playmode wpsr-yt-live-now-btn"
                       data-videoid="<?php echo esc_attr($videoId); ?>" data-index="<?php echo esc_attr($index); ?>"
                       data-playmode="<?php echo isset($template_meta['video_settings']['play_mode']) ? esc_attr($template_meta['video_settings']['play_mode']) : 'inline'; ?>"
                       data-template-id="<?php echo esc_attr($templateId); ?>" target="_blank" rel="noopener noreferrer">
                        <?php echo __('LIVE NOW', 'wp-social-ninja-pro'); ?>
                    </a>
                </div>
            <?php } ?>
        </div>
        <?php
    }

    public function youtube_popup_content_html($feed = [], $template_meta = [], $header = [])
    {
        $display_title = Arr::get($template_meta, 'popup_settings.display_title');
        $display_views_counter = Arr::get($template_meta, 'popup_settings.display_views_counter');
        $display_date = Arr::get($template_meta, 'popup_settings.display_date');

        $display_likes_counter = Arr::get($template_meta, 'popup_settings.display_likes_counter');
        $display_dislikes_counter = Arr::get($template_meta, 'popup_settings.display_dislikes_counter');

        $display_channel_logo = Arr::get($template_meta, 'popup_settings.display_channel_logo');
        $display_channel_name = Arr::get($template_meta, 'popup_settings.display_channel_name');
        $display_subscribers_counter = Arr::get($template_meta, 'popup_settings.display_subscribers_counter');
        $display_description = Arr::get($template_meta, 'popup_settings.display_description');
        $display_subscribe_button = Arr::get($template_meta, 'popup_settings.display_subscribe_button');
        $display_comments = Arr::get($template_meta, 'popup_settings.display_comments');

        if($display_title === 'false'
            && $display_views_counter === 'false'
            && $display_date === 'false'
            && $display_likes_counter === 'false'
            && $display_dislikes_counter === 'false'
            && $display_channel_logo === 'false'
            && $display_channel_name === 'false'
            && $display_subscribers_counter === 'false'
            && $display_description === 'false'
            && $display_subscribe_button === 'false'
            && $display_comments === 'false'
        ){
            return;
        }
        ?>
        <div class="wpsr-yt-popup-box-content">

        <?php if ($display_title === 'true') { ?>
        <h1 class="wpsr-yt-popup-video-title"><?php echo esc_html($feed['snippet']['title']) ?></h1>
        <?php } ?>
        <?php if($display_views_counter === 'true' || $display_date === 'true' || $display_likes_counter === 'true' || $display_dislikes_counter === 'true'){ ?>
        <div class="wpsr-yt-popup-video-info">

            <?php if($display_views_counter === 'true' || $display_date === 'true'){ ?>
            <div class="wpsr-yt-popup-video-info-left">
                <?php if ($display_views_counter === 'true' && Arr::get($feed, 'statistics')) { ?>
                    <span class="wpsr-yt-popup-video-views">
                        <?php echo GlobalHelper::shortNumberFormat($feed['statistics']['viewCount']) . __(' Views',
                                'wp-social-ninja-pro'); ?>
                    </span>
                <?php } ?>
                <?php if ($display_date === 'true') { ?>
                    <span class="wpsr-yt-popup-video-date"> <?php echo date_format(date_create($feed['snippet']['publishedAt']),
                            'M j, Y'); ?></span>
                <?php } ?>
            </div>
            <?php } ?>

            <?php if($display_likes_counter === 'true' || $display_dislikes_counter === 'true'){ ?>
                <div class="wpsr-yt-popup-video-info-right">
                <?php if ($display_likes_counter === 'true' && Arr::get($feed,
                        'statistics.likeCount') && Arr::get($feed, 'statistics.likeCount') >= 0) { ?>
                    <span class="wpsr-yt-popup-video-likes">
                        <svg viewBox="0 0 24 24" preserveAspectRatio="xMidYMid meet" focusable="false"
                             class="wpsr-yt-like-icon">
                            <g class="wpsr-yt-like-icon">
                            <path d="M1 21h4V9H1v12zm22-11c0-1.1-.9-2-2-2h-6.31l.95-4.57.03-.32c0-.41-.17-.79-.44-1.06L14.17 1 7.59 7.59C7.22 7.95 7 8.45 7 9v10c0 1.1.9 2 2 2h9c.83 0 1.54-.5 1.84-1.22l3.02-7.05c.09-.23.14-.47.14-.73v-1.91l-.01-.01L23 10z"
                                  class="wpsr-yt-like-icon"></path>
                            </g>
                        </svg>
                        <?php echo GlobalHelper::shortNumberFormat($feed['statistics']['likeCount']); ?>
                    </span>
                <?php } ?>
                <?php if ($display_dislikes_counter === 'true' && Arr::get($feed,
                        'statistics.dislikeCount') && Arr::get($feed, 'statistics.dislikeCount') >= 0) { ?>
                    <span class="wpsr-yt-popup-video-dislikes">
                         <svg viewBox="0 0 24 24" preserveAspectRatio="xMidYMid meet" focusable="false"
                              class="wpsr-yt-dislike-icon">
                            <g class="wpsr-yt-dislike-icon">
                            <path d="M15 3H6c-.83 0-1.54.5-1.84 1.22l-3.02 7.05c-.09.23-.14.47-.14.73v1.91l.01.01L1 14c0 1.1.9 2 2 2h6.31l-.95 4.57-.03.32c0 .41.17.79.44 1.06L9.83 23l6.59-6.59c.36-.36.58-.86.58-1.41V5c0-1.1-.9-2-2-2zm4 0v12h4V3h-4z"
                                  class="wpsr-yt-dislike-icon"></path>
                            </g>
                        </svg>
                    <?php echo GlobalHelper::shortNumberFormat($feed['statistics']['dislikeCount']); ?>
                    </span>
                <?php } ?>
            </div>
            <?php } ?>
        </div>
        <?php } ?>

        <?php if ( (!empty($header) && is_array($header)) && ($display_channel_logo === 'true' || $display_channel_name === 'true' || $display_subscribers_counter === 'true' || $display_description === 'true') || $display_subscribe_button === 'true') { ?>
        <div class="wpsr-yt-popup-video-meta">
            <?php if ($display_channel_logo === 'true') { ?>
                <div class="wpsr-yt-popup-video-meta-channel-logo">
                    <a target="_blank"
                       href="<?php echo esc_url('https://www.youtube.com/channel/' . $header['items'][0]['id']) ?>">
                        <img src="<?php echo esc_url($header['items'][0]['snippet']['thumbnails']['high']['url']); ?>"
                             alt="<?php echo esc_attr($header['items'][0]['snippet']['title']); ?>">
                    </a>
                </div>
            <?php } ?>
            <?php if($display_channel_name === 'true' || $display_subscribers_counter === 'true' || $display_description === 'true'){ ?>
            <div class="wpsr-yt-popup-video-meta-info">
                <?php if ($display_channel_name === 'true') { ?>
                    <a class="wpsr-yt-popup-video-meta-channel-name" target="_blank" rel="noopener noreferrer"
                       href="<?php echo esc_url('https://www.youtube.com/channel/' . $header['items'][0]['id']) ?>"
                       title="<?php echo esc_attr($header['items'][0]['snippet']['title']); ?>">
                        <?php echo esc_html($header['items'][0]['snippet']['title']); ?>
                    </a>
                <?php } ?>
                <?php if ($display_subscribers_counter === 'true') { ?>
                    <span class="wpsr-yt-popup-video-meta-channel-subscriber-count">
                        <?php echo GlobalHelper::shortNumberFormat($header['items'][0]['statistics']['subscriberCount']) . __(' Subscribers',
                                'wp-social-ninja-pro'); ?>
                    </span>
                <?php } ?>
                <?php if ($display_description === 'true') { ?>
                    <p class="wpsr-yt-popup-video-meta-description wpsr_show_less_content"><?php echo make_clickable(YoutubeHelper::formatContent($feed['snippet']['description'])); ?></p>
                <?php } ?>
            </div>
            <?php } ?>
            <?php if ($display_subscribe_button === 'true') { ?>
                <div class="wpsr-yt-popup-video-meta-btn">
                    <a href="<?php echo esc_url('https://www.youtube.com/channel/' . $header['items'][0]['id']) . '?sub_confirmation=1'; ?>"
                       target="_blank" rel="noopener">
                        <?php echo __('Subscribe', 'wp-social-ninja-pro'); ?>
                    </a>
                </div>
            <?php } ?>
        </div>
        <?php } ?>

        <?php if ( Arr::get($feed, 'comment.items') && ($display_comments === 'true')) { ?>
        <div class="wpsr-yt-popup-video-comments">
            <?php foreach ($feed['comment']['items'] as $index => $comment) { ?>
                <div class="wpsr-yt-popup-video-comment">
                    <div class="wpsr-yt-popup-video-comment-profile-pic">
                        <a href="<?php echo esc_url($comment['snippet']['topLevelComment']['snippet']['authorChannelUrl']); ?>"
                           target="_blank" rel="noopener">
                            <img src="<?php echo esc_url($comment['snippet']['topLevelComment']['snippet']['authorProfileImageUrl']); ?>"
                                 alt="<?php echo esc_attr($comment['snippet']['topLevelComment']['snippet']['authorDisplayName']); ?>">
                        </a>
                    </div>
                    <div class="wpsr-yt-popup-video-comment-info">
                        <div class="wpsr-yt-popup-video-comment-info-header">
                            <a href="<?php echo esc_url($comment['snippet']['topLevelComment']['snippet']['authorChannelUrl']); ?>"
                               target="_blank"
                               rel="noopener"
                               class="wpsr-yt-popup-video-comment-info-header-username"><?php echo esc_html($comment['snippet']['topLevelComment']['snippet']['authorDisplayName']); ?></a>
                            <span class="wpsr-yt-popup-video-comment-info-header-time">
                                <?php
                                $publishedAt = date_format(date_create($comment['snippet']['topLevelComment']['snippet']['publishedAt']), 'D M j H:i:s O Y');
                                echo sprintf(__('%s ago'), human_time_diff(strtotime($publishedAt)));
                                ?>
                            </span>
                        </div>
                        <div class="wpsr-yt-popup-video-comment-text">
                            <p class="wpsr-yt-popup-video-comment-text-inner wpsr_show_less_content"><?php echo make_clickable(YoutubeHelper::formatContent($comment['snippet']['topLevelComment']['snippet']['textOriginal'])); ?></p>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
        </div>
    <?php }
    }

    public function render_youtube_prev_next_pagination($templateId, $paginate, $total, $playMode)
    {
        echo '<ul class="wpsr-yt-prev-next wpsr-prev-next-default wpsr-prev-next-' . esc_attr($playMode) . '"
                    id="wpsr-yt-prev-next-' . esc_attr($templateId) . '"
                    data-template-id="' . esc_attr($templateId) . '"
                    data-paginate="' . esc_attr($paginate) . '"
                    data-pagenum="0"
                    data-total="' . esc_attr($total) . '">
                    <li><a href="#" rel="noopener" class="wpsr-pagi-prev wpsr-link-disable">' . __('Prev', 'wp-social-ninja-pro') . '</a></li>
                    <li><a href="#" rel="noopener" class="wpsr-pagi-next">' . __('Next', 'wp-social-ninja-pro') . '</a></li>
                </ul>';
    }

    /**
     *
     * Render Instagram Feed Statistics HTML
     *
     * @param $feed
     * @param $template_meta
     *
     * @since 1.3.0
     *
     **/
    public function render_instagram_post_statistics_html($feed = array(), $template_meta = array())
    {
        if (isset($feed['like_count']) || isset($feed['comments_count'])) {
            ?>

            <div class="wpsr-ig-post-statistics">
                <?php if (isset($feed['like_count']) && isset($template_meta['post_settings']['display_likes_counter']) && $template_meta['post_settings']['display_likes_counter'] === 'true') { ?>
                    <div class="wpsr-ig-post-single-statistic wpsr-ig-post-like-count">
                        <svg aria-label="Like" viewBox="0 0 48 48">
                            <path d="M34.6 6.1c5.7 0 10.4 5.2 10.4 11.5 0 6.8-5.9 11-11.5 16S25 41.3 24 41.9c-1.1-.7-4.7-4-9.5-8.3-5.7-5-11.5-9.2-11.5-16C3 11.3 7.7 6.1 13.4 6.1c4.2 0 6.5 2 8.1 4.3 1.9 2.6 2.2 3.9 2.5 3.9.3 0 .6-1.3 2.5-3.9 1.6-2.3 3.9-4.3 8.1-4.3m0-3c-4.5 0-7.9 1.8-10.6 5.6-2.7-3.7-6.1-5.5-10.6-5.5C6 3.1 0 9.6 0 17.6c0 7.3 5.4 12 10.6 16.5.6.5 1.3 1.1 1.9 1.7l2.3 2c4.4 3.9 6.6 5.9 7.6 6.5.5.3 1.1.5 1.6.5.6 0 1.1-.2 1.6-.5 1-.6 2.8-2.2 7.8-6.8l2-1.8c.7-.6 1.3-1.2 2-1.7C42.7 29.6 48 25 48 17.6c0-8-6-14.5-13.4-14.5z"></path>
                        </svg>
                        <span><?php echo GlobalHelper::numberWithCommas(esc_html($feed['like_count'])); ?></span>
                    </div>
                <?php } ?>

                <?php if (isset($feed['comments_count']) && isset($template_meta['post_settings']['display_comments_counter']) && $template_meta['post_settings']['display_comments_counter'] === 'true') { ?>
                    <div class="wpsr-ig-post-single-statistic wpsr-ig-post-comment-comment">
                        <svg aria-label="Comment" viewBox="0 0 48 48">
                            <path clip-rule="evenodd"
                                  d="M47.5 46.1l-2.8-11c1.8-3.3 2.8-7.1 2.8-11.1C47.5 11 37 .5 24 .5S.5 11 .5 24 11 47.5 24 47.5c4 0 7.8-1 11.1-2.8l11 2.8c.8.2 1.6-.6 1.4-1.4zm-3-22.1c0 4-1 7-2.6 10-.2.4-.3.9-.2 1.4l2.1 8.4-8.3-2.1c-.5-.1-1-.1-1.4.2-1.8 1-5.2 2.6-10 2.6-11.4 0-20.6-9.2-20.6-20.5S12.7 3.5 24 3.5 44.5 12.7 44.5 24z"
                                  fill-rule="evenodd"></path>
                        </svg>
                        <span><?php echo GlobalHelper::numberWithCommas(esc_html($feed['comments_count'])); ?></span>
                    </div>
                <?php } ?>
            </div>

            <?php
        }
    }

    /**
     *
     * Render Instagram follow button HTML
     *
     * @param $user
     * @param $settings
     *
     * @since 1.3.0
     *
     **/
    public function render_instagram_follow_button_html($settings = [])
    {
        $account = InstagramHelper::getUserAccountInfo($settings);
        if (isset($settings['follow_button_settings']['display_follow_button']) && isset($account['username']) && $settings['follow_button_settings']['display_follow_button'] === 'true') {
            ?>
            <div class="wpsr-ig-follow-btn">
                <a href="<?php echo esc_url('https://www.instagram.com/' . $account['username']); ?>" target="_blank" rel="noopener">
                    <?php
                    $follow_button_text = Arr::get($settings, 'follow_button_settings.follow_button_text');
                    echo __($follow_button_text, 'wp-social-ninja-pro');
                    ?>
                </a>
            </div>
            <?php
        }
    }


    /**
     *
     * Render Instagram Header Statistics HTML
     *
     * @param $user
     * @param $settings
     *
     * @since 1.3.0
     *
     **/
    public function render_instagram_header_statistics_html($user = [], $settings = [], $translations = [])
    {
        $media_count     = Arr::get($user, 'media_count', null);
        $followers_count = Arr::get($user, 'followers_count', 0);
        if ($media_count || $followers_count) {
            ?>
            <div class="wpsr-ig-header-statistics">
                <?php if ($media_count && Arr::get($settings, 'display_posts_counter') === 'true') { ?>
                    <div class="wpsr-ig-header-statistic-item">
                        <strong><?php echo GlobalHelper::shortNumberFormat($user['media_count']); ?> </strong>
                        <?php echo Arr::get($translations, 'posts') ?>
                    </div>
                <?php } ?>
                <?php if ($followers_count && Arr::get($settings, 'display_followers_counter') === 'true') { ?>
                    <div class="wpsr-ig-header-statistic-item">
                        <strong><?php echo GlobalHelper::shortNumberFormat($user['followers_count']); ?></strong>
                        <?php echo Arr::get($translations, 'followers'); ?>
                    </div>
                <?php } ?>
            </div>
        <?php }
    }

    /**
     *
     * Trim instagram caption
     *
     * @param $caption
     * @param $trim_words_count
     *
     * @since 1.3.0
     *
     **/
    public function instagram_trim_caption_words($caption, $trim_words_count)
    {
        return wp_trim_words($caption, $trim_words_count, '...');
    }


    /**
     *
     * Retrieve instagram load more data
     *
     * @since 1.2.5
     *
     **/
    public function getPaginatedInstaFeedHtml($content, $templateId, $page)
    {
        $app                 = App::getInstance();
        $shortcodeHandler = new ShortcodeHandler();

        $template_meta = $shortcodeHandler->templateMeta($templateId, 'instagram');
        $feed          = (new InstagramFeed())->getTemplateMeta($template_meta);
        $settings      = $shortcodeHandler->formatFeedSettings($feed);

        $templateMapping = [
            'template1' => 'public.feeds-templates.instagram.template1',
            'template2' => 'public.feeds-templates.instagram.template2',
        ];
        $template        = Arr::get($settings['feed_settings'], 'template', '');
        $file            = $templateMapping[$template];

        $pagination_settings = $shortcodeHandler->formatPaginationSettings($feed);
        $sinceId             = (($page - 1) * $pagination_settings['paginate']);
        $maxId               = ($sinceId + $pagination_settings['paginate']) - 1;


        return (string) $app->view->make($file, array(
            'templateId'    => $templateId,
            'feeds'         => $settings['feeds'],
            'template_meta' => $settings['feed_settings'],
            'sinceId'       => $sinceId,
            'maxId'         => $maxId,
        ));
    }

    public function render_facebook_feed_like_button_html($settings = [], $header = [])
    {
      $display_like_button = Arr::get($settings, 'like_button_settings.display_like_button');
      if($display_like_button === 'true') {
          ?>
            <div class="wpsr-fb-feed-btn">
                <a href="<?php echo esc_url($header['link']); ?>" target="_blank" rel="nofollow">
                            <span class="wpsr-fb-feed-btn-icon">
                              <svg class="wpsr-svg-fb-icon" width="12px" height="12px" viewBox="0 0 12 12" style="enable-background:new 0 0 12 12;">
                                  <path style="fill:#4672D2;" d="M10.4,0H1.6C0.7,0,0,0.7,0,1.6v8.8C0,11.3,0.7,12,1.6,12h4.3l0-4.3H4.8c-0.1,0-0.3-0.1-0.3-0.3l0-1.4
                                      c0-0.1,0.1-0.3,0.3-0.3h1.1V4.5c0-1.5,0.9-2.4,2.3-2.4h1.1c0.1,0,0.3,0.1,0.3,0.3v1.2c0,0.1-0.1,0.3-0.3,0.3l-0.7,0
                                      C8,3.8,7.8,4.1,7.8,4.6v1.2h1.7c0.2,0,0.3,0.1,0.3,0.3L9.6,7.5c0,0.1-0.1,0.2-0.3,0.2H7.8l0,4.3h2.6c0.9,0,1.6-0.7,1.6-1.6V1.6
                                      C12,0.7,11.3,0,10.4,0z"></path>
                              </svg>
                            </span>
                    <span class="wpsr-fb-feed-btn-text"><?php echo Arr::get($settings, 'like_button_settings.like_button_text'); ?></span>
                </a>
            </div>
    <?php }
    }

    public function render_facebook_feed_share_button_html($settings = [], $header = [])
    {
        $display_share_button = Arr::get($settings, 'share_button_settings.display_share_button');
        if($display_share_button === 'true') {
            ?>
            <div class="wpsr-fb-feed-btn wpsr-ml-15">
                <a class="wpsr-fb-feed-btn-share" href="<?php echo esc_url($header['link']); ?>" target="_blank" rel="nofollow">
                     <span class="wpsr-fb-feed-btn-icon">
                      <svg class="wpsr-svg-share-icon" width="12px" height="12px" viewBox="0 0 24 24">
                        <path id="XMLID_31_" d="M12.7,15.3c-4.5,0.1-8.4,2.5-10.7,6c-0.2,0.3-0.6,0.5-0.9,0.5c-0.1,0-0.2,0-0.3,0c-0.5-0.1-0.8-0.6-0.8-1c0,0,0-0.1,0-0.1c0-7.1,5.7-12.9,12.7-13V5.5c0-0.5,0.3-0.9,0.7-1.1c0.2-0.1,0.4-0.1,0.6-0.1c0.3,0,0.5,0.1,0.7,0.2l8.8,6c0.3,0.2,0.5,0.6,0.5,0.9c0,0.4-0.2,0.7-0.5,1l-8.8,6.1c-0.2,0.2-0.5,0.2-0.7,0.2c-0.2,0-0.4,0-0.6-0.1c-0.4-0.2-0.7-0.7-0.7-1.1V15.3z"></path>
                      </svg>
                    </span>
                    <span class="wpsr-fb-feed-btn-text"><?php echo Arr::get($settings, 'share_button_settings.share_button_text'); ?></span>
                </a>
            </div>
        <?php }
    }

    public function render_facebook_feed_statistics($feed = [], $template_meta = [], $translations = [])
    {
        if($template_meta['post_settings']['display_likes_count'] === 'true' || $template_meta['post_settings']['display_comments_count'] === 'true'){
            $totalReactions = FacebookHelper::getTotalFeedReactions($feed);
        ?>
        <div class="wpsr-fb-feed-statistics">
            <?php if($totalReactions && $template_meta['post_settings']['display_likes_count'] === 'true') { ?>
            <div class="wpsr-fb-feed-reactions">

                <?php if(Arr::get($feed, 'like.summary.total_count')){ ?>
                <div class="wpsr-fb-feed-reactions-icon-like wpsr-fb-feed-reactions-icon"></div>
                <?php } ?>

                <?php if(Arr::get($feed, 'love.summary.total_count')){ ?>
                <div class="wpsr-fb-feed-reactions-icon-love wpsr-fb-feed-reactions-icon"></div>
                <?php } ?>

                <?php if(Arr::get($feed, 'wow.summary.total_count')){ ?>
                <div class="wpsr-fb-feed-reactions-icon-wow wpsr-fb-feed-reactions-icon"></div>
                <?php } ?>

                <?php if(Arr::get($feed, 'sad.summary.total_count')){ ?>
                <div class="wpsr-fb-feed-reactions-icon-sad wpsr-fb-feed-reactions-icon"></div>
                <?php } ?>

                <?php if(Arr::get($feed, 'angry.summary.total_count')){ ?>
                <div class="wpsr-fb-feed-reactions-icon-angry wpsr-fb-feed-reactions-icon"></div>
                <?php } ?>

                <div class="wpsr-fb-feed-reaction-count">
                    <?php echo GlobalHelper::shortNumberFormat($totalReactions); ?>
                </div>
            </div>
            <?php } ?>

            <?php if( Arr::get($feed, 'comments') && Arr::get($feed, 'comments.summary.total_count') && $template_meta['post_settings']['display_comments_count'] === 'true'){ ?>
            <div class="wpsr-fb-feed-comments-count">
                <?php echo GlobalHelper::shortNumberFormat($feed['comments']['summary']['total_count']) .' '. Arr::get($translations, 'comments'); ?>
            </div>
            <?php } ?>
        </div>
        <?php
        }
    }

    public function render_facebook_feed_videos($feed = [], $template_meta = [])
    {
        $feed_type = Arr::get($template_meta, 'source_settings.feed_type');
        ?>
        <div>
            <?php if($feed_type === 'video_feed' && Arr::get($feed, 'format')){
                $large_media_url = Arr::get($feed, 'format.1.picture');
                $medium_media_url = Arr::get($feed, 'format.0.picture');

                $display_mode = Arr::get($template_meta, 'post_settings.display_mode');
                $permalink_url = $display_mode !== 'none' ? esc_url('https://www.facebook.com'.Arr::get($feed, 'permalink_url')) : '';
                $description  = Arr::get($feed, 'description');
                $attrs = [
                    'class'  => 'class="wpsr-fb-feed-video-preview wpsr-fb-feed-video-playmode wpsr-feed-link"',
                    'target' => $display_mode !== 'none' ? 'target="_blank"' : '',
                    'rel'    => 'rel="nofollow"',
                    'href'   =>  $display_mode !== 'none' ? 'href="'.$permalink_url.'"' : '',
                    'alt'    => 'alt="'.esc_attr($description).'"'
                ];
                ?>
                <a <?php echo implode(' ', $attrs); ?>>
                    <img src="<?php echo esc_url($large_media_url ? $large_media_url : $medium_media_url); ?>" alt="<?php esc_attr(Arr::get($feed, 'description')); ?>"/>

                    <?php if(Arr::get($feed, 'length') && $template_meta['post_settings']['display_duration'] === 'true') { ?>
                        <span class="wpsr-fb-feed-video-duration">
                            <?php echo FacebookHelper::secondsToMinutes(Arr::get($feed, 'length')); ?>
                        </span>
                    <?php } ?>

                    <?php if($template_meta['post_settings']['display_play_icon'] === 'true') { ?>
                        <div class="wpsr-fb-feed-video-play">
                            <div class="wpsr-fb-feed-video-play-icon"></div>
                        </div>
                    <?php } ?>
                </a>
            <?php } ?>

            <div class="wpsr-fb-feed-video-info">
                <?php if(Arr::get($feed, 'description') && $template_meta['post_settings']['display_description'] === 'true'){ ?>
                <h3>
                    <a href="<?php echo esc_url('https://www.facebook.com'.Arr::get($feed, 'permalink_url')); ?>" class="wpsr-fb-feed-video-playmode wpsr-feed-link" target="_blank" rel="nofollow">
                        <?php echo wp_trim_words($feed['description'], 10); ?>
                    </a>
                </h3>
                <?php } ?>
                <?php if($template_meta['post_settings']['display_date'] === 'true'){ ?>
                    <div class="wpsr-fb-feed-video-statistics">
                        <div class="wpsr-fb-feed-video-statistic-item">
                            <?php
                                /**
                                 * facebook_feed_date hook.
                                 *
                                 * @hooked FacebookFeedTemplateHandler::renderFeedDate 10
                                 * */
                                do_action('wpsocialreviews/facebook_feed_date', $feed);
                            ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
        <?php
    }

    public function render_facebook_feed_summary_card_image($feed = [], $attachment = [])
    {
        $full_picture = Arr::get($feed, 'full_picture');
        $image_src = Arr::get($attachment, 'media.image.src');
        ?>
        <img class="wpsr-fb-feed-url-summary-card-img" src="<?php echo esc_url($full_picture ? $full_picture : $image_src); ?>" alt="<?php echo esc_attr(Arr::get($feed, 'from.name')); ?>">
        <?php
    }

    public function render_facebook_feed_image($feed = [], $template_meta = [])
    {
        $message = Arr::get($feed, 'message');
        $media_url = Arr::get($feed, 'attachments.data.0.media.image.src');
        $type = Arr::get($feed, 'attachments.data.0.type');
        $full_picture = Arr::get($feed, 'full_picture');
        $image = $media_url ? $media_url : $full_picture;
        $status_type = Arr::get($feed, 'status_type');
        ?>
        <?php if($type !== 'native_templates'){ ?>
        <img class="wpsr-fb-feed-image-render" src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($message); ?>"/>
        <?php } ?>
        <?php if($status_type === 'added_video' && $template_meta['post_settings']['display_play_icon'] === 'true') { ?>
        <div class="wpsr-fb-feed-video-play">
            <div class="wpsr-fb-feed-video-play-icon"></div>
        </div>
        <?php }
    }

    public function render_facebook_feed_photo_feed_image($feed = [], $template_meta = [], $attrs = [])
    {
        $feed_type = Arr::get($template_meta, 'source_settings.feed_type');
        if($feed_type === 'photo_feed' && Arr::get($feed, 'images')){
            $large_media_url = Arr::get($feed, 'images.2.source');
            $medium_media_url = Arr::get($feed, 'images.0.source');
        ?>
            <a <?php echo implode(' ', $attrs); ?>>
                <img class="wpsr-feed-link-img" src="<?php echo esc_url($large_media_url ? $large_media_url : $medium_media_url); ?>" alt="<?php echo esc_attr(Arr::get($feed, 'name')); ?>"/>
            </a>
        <?php
        }
    }

    public function loadView($fileName, $data)
    {
        // normalize the filename
        $fileName = str_replace(array('../', './'), '', $fileName);
        $basePath = WPSOCIALREVIEWS_PRO_DIR . 'includes/views/';

        $filePath = $basePath . $fileName . '.php';

        extract($data);
        ob_start();
        include $filePath;

        return ob_get_clean();
    }
}

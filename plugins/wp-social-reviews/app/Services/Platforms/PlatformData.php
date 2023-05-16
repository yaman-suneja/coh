<?php
namespace WPSocialReviews\App\Services\Platforms;

use WPSocialReviews\App\Services\Platforms\Feeds\Facebook\FacebookFeed;
use WPSocialReviews\Framework\Support\Arr;
use WPSocialReviews\App\Services\Platforms\Feeds\Instagram\Common;
use WPSocialReviews\App\Services\Platforms\Feeds\CacheHandler;
use WPSocialReviews\App\Models\Review;

class PlatformData
{
    public $platform;

    private $wpsr_status_option_key = 'wpsr_statuses';

    private $wpsr_revoke_platform_data_option_key = '';

    private $wpsr_app_permission_revoked_status_key = 'wpsr_app_permission_revoked';

    public function __construct($platform)
    {
        $this->platform = $platform;
        $this->wpsr_revoke_platform_data_option_key = 'wpsr_'.$this->platform.'_revoke_platform_data';
        $this->registerHooks();
    }

    public function registerHooks() {
        $platform_hook = $this->platform === 'facebook_feed' || $this->platform === 'facebook' ? $this->platform : $this->platform.'_feed';

        add_action( 'wpsocialreviews/'.$platform_hook.'_api_connect_response', [$this, 'handlePlatformDataOnApiResponse'], 10, 1 );
        add_action( 'wpsocialreviews/before_display_'.$platform_hook, [$this, 'handleAppPermissionError'] );
        add_action( 'wpsocialreviews/'.$platform_hook.'_app_permission_revoked', [$this, 'handleAppPermissionStatus']);

        add_action( 'wpsocialreviews/before_display_'.$platform_hook, [$this, 'updateLastUsed'] );
        add_action( 'wpsr_scheduled_twicedaily', [$this, 'maybeDeleteOldData']);
    }

    public function handlePlatformDataOnApiResponse( $response )
    {
        if ( is_wp_error( $response ) ) {
            return;
        }

        if ( empty( $response['response'] ) || empty( $response['response']['code'] ) ) {
            return;
        }

        if ( $response['response']['code'] !== 200 ) {
            return;
        }

        $statuses_option = get_option( $this->wpsr_status_option_key, [] );

        if (empty( $statuses_option[$this->platform][ $this->wpsr_app_permission_revoked_status_key ] )) {
            return;
        }

        if($this->platform === 'instagram') {
            $connectedAccounts = (new Common())->findConnectedAccounts();

            $username = '';
            foreach ($connectedAccounts as $account){
                $username = Arr::get($account, 'username', '');
            }

            if ( empty($username) ) {
                return;
            }

            $api_response = json_decode( $response['body'], true );
            $api_response_username = Arr::get($api_response, 'username', '');

            if ( $username !== $api_response_username ) {
                return;
            }
        }

        if($this->platform === 'facebook_feed') {
            $connectedSources = (new FacebookFeed())->getConncetedSourceList();

            $page_id = '';
            foreach ($connectedSources as $source){
                $page_id = Arr::get($source, 'page_id', '');
            }

            if ( empty($page_id) ) {
                return;
            }

            $api_response = json_decode( $response['body'], true );
            $api_response_page_id = Arr::get($api_response, 'id', '');

            if ( $page_id !== $api_response_page_id ) {
                return;
            }
        }

        if($this->platform === 'facebook') {
            $connectedAccounts = $this->findConnectedFacebookAccounts();

            $place_id = '';
            foreach ($connectedAccounts as $account){
                $place_id = Arr::get($account, 'place_id', '');
            }

            if ( empty($place_id) ) {
                return;
            }
        }

        $this->deleteRevokedAccount( $statuses_option );
    }


    public function handleAppPermissionError()
    {
        $wpsr_statuses = get_option( $this->wpsr_status_option_key, [] );

        if ( empty( $wpsr_statuses[$this->platform][ $this->wpsr_app_permission_revoked_status_key ] ) ) {
            return;
        }

        $revoke_platform_data = get_option( $this->wpsr_revoke_platform_data_option_key, [] );
        $revoke_platform_data_timestamp = Arr::get($revoke_platform_data, 'revoke_platform_data_timestamp', 0);

        if ( !$revoke_platform_data_timestamp ) {
            return;
        }

        $current_timestamp = current_time( 'timestamp', true );
        if ( $current_timestamp < $revoke_platform_data_timestamp ) {
            return;
        }

        $this->deletePlatformData();
        $this->deleteRevokedAccount( $wpsr_statuses );
    }

    public function deletePlatformData()
    {
        if($this->platform === 'instagram') {
            $connectedAccounts = (new Common())->findConnectedAccounts();
            foreach ($connectedAccounts as $connectedAccount) {
                $has_error = Arr::get($connectedAccount, 'status') === 'error';
                $user_id = Arr::get($connectedAccount, 'user_id', '');
                if ($has_error) {
                    unset($connectedAccounts[$user_id]);
                }
            }

            update_option('wpsr_instagram_verification_configs', array('connected_accounts' => $connectedAccounts));

        }

        if($this->platform === 'facebook') {
            $connectedAccounts = $this->findConnectedFacebookAccounts();
            foreach ($connectedAccounts as $key => $connectedAccount) {
                $has_error = Arr::get($connectedAccount, 'status') === 'error';
                if ($has_error) {
                    unset($connectedAccounts[$key]);
                }
            }

            update_option('wpsr_reviews_facebook_settings', $connectedAccounts);
            Review::where('platform_name', $this->platform)->delete();
            delete_option('wpsr_reviews_facebook_business_info');
            delete_option('wpsr_reviews_facebook_pages_list');
            delete_option('wpsr_reviews_facebook_settings');
        }

        $cacheHandler = new CacheHandler($this->platform);
        $cacheHandler->clearCache();
    }

    public function handleAppPermissionStatus()
    {
        $wpsr_statuses = get_option( $this->wpsr_status_option_key, [] );

        // if wpsr_app_permission_revoked is true then we return
        if ( isset( $wpsr_statuses[$this->platform]['wpsr_app_permission_revoked'] ) && true === $wpsr_statuses[$this->platform]['wpsr_app_permission_revoked'] ) {
            return;
        }

        $this->updateAppPermissionRevokedStatus( $wpsr_statuses, true );

        $current_timestamp              = current_time( 'timestamp', true );
        $revoke_platform_data_timestamp = strtotime( '+3 days', $current_timestamp );

        update_option( $this->wpsr_revoke_platform_data_option_key, [
            'revoke_platform_data_timestamp' => $revoke_platform_data_timestamp
        ] );
    }

    protected function updateAppPermissionRevokedStatus( $wpsr_statuses, $is_revoked )
    {
        if ( $is_revoked ) {
            $wpsr_statuses[$this->platform][ $this->wpsr_app_permission_revoked_status_key ] = true;
        } else {
            unset( $wpsr_statuses[$this->platform][ $this->wpsr_app_permission_revoked_status_key ] );
        }
        update_option( $this->wpsr_status_option_key, $wpsr_statuses );
    }

    public function deleteRevokedAccount( $statuses_option )
    {
        $this->updateAppPermissionRevokedStatus( $statuses_option, false );

        delete_option( $this->wpsr_revoke_platform_data_option_key );
    }

    public function updateLastUsed()
    {
        $defaults = [];
        $wpsr_statuses = get_option( $this->wpsr_status_option_key, $defaults);

        if ( isset($wpsr_statuses[$this->platform]['last_used']) < time() - 3600 ) {
            $wpsr_statuses[$this->platform]['last_used'] = time();
            update_option( $this->wpsr_status_option_key, $wpsr_statuses );
        }
    }

    public function maybeDeleteOldData()
    {
        $wpsr_statuses = get_option( $this->wpsr_status_option_key, [] );

        if($wpsr_statuses[$this->platform]['last_used'] < time() - ( 21 * DAY_IN_SECONDS )){
            if($this->platform === 'instagram' || $this->platform === 'facebook_feed') {
                $cacheHandler = new CacheHandler($this->platform);
                $cacheHandler->clearCache();
            }

            if($this->platform === 'instagram') {
                update_option('wpsr_instagram_verification_configs', array('connected_accounts' => []));
            }

            if($this->platform === 'facebook') {
                Review::where('platform_name', $this->platform)->delete();
                delete_option('wpsr_reviews_facebook_business_info');
                delete_option('wpsr_reviews_facebook_pages_list');
                delete_option('wpsr_reviews_facebook_settings');
            }
        }
    }

    public function isAppPermissionError($response)
    {
        $error_code    = (int) Arr::get($response, 'error.code', 0);
        $error_subcode = (int) Arr::get($response, 'error.error_subcode', 0);

        //personal account access token or app authorized permissions error
        $error_codes_to_check = array(
            190,
        );

        //business account access token or app authorized permissions error
        $error_subcodes_to_check = array(
            458,
        );

        if ( in_array( $error_code, $error_codes_to_check, true ) ) {
            if ( strpos( $response['error']['message'], 'user has not authorized application' ) !== false ) {
                return true;
            }
            return in_array( $error_subcode, $error_subcodes_to_check, true );
        }

        return false;
    }

    public function findConnectedFacebookAccounts()
    {
        $accounts = get_option('wpsr_reviews_facebook_settings');
        if(empty($accounts)) {
            return [];
        }

        return $accounts;
    }
}

<?php

namespace WPSocialReviewsPro\Classes\Reviews;

use FluentForm\App\Services\Integrations\IntegrationManager;
use FluentForm\Framework\Foundation\Application;
use WPSocialReviews\App\Models\Review;
use WPSocialReviews\Framework\Support\Arr;

if (!defined('ABSPATH')) {
    exit;
}

class Fluentform extends IntegrationManager
{
    public function __construct(Application $app)
    {
        parent::__construct(
            $app,
            'WP Social Ninja',
            'wp_social_ninja',
            '_fluentform_wp_social_ninja_settings',
            'fluentform_wp_social_ninja_reviews',
            16
        );

        $this->hasGlobalMenu = false;
        $this->disableGlobalSettings = 'yes';

        $this->logo = defined('WPSOCIALREVIEWS_URL') ? WPSOCIALREVIEWS_URL . 'assets/images/icon/wp_social_ninja.png' : '';

        $this->description = 'WP Social Ninja is the all-in-one WordPress plugin for automatically integrating your social media reviews, news feeds, and chat functionalities.';
        $this->registerAdminHooks();

        add_filter('fluentform_notifying_async_wp_social_ninja', '__return_false');

        add_filter('wpsocialreviews/available_valid_reviews_platforms', array($this, 'pushValidPlatform'));
    }

    public function getGlobalFields($fields)
    {
        return [
            'logo'             => $this->logo,
            'menu_title'       => __('WP Social Ninja', 'wp-social-ninja-pro'),
            'menu_description' => __('WP Social Ninja is the all-in-one WordPress plugin for automatically integrating your social media reviews, news feeds, and chat functionalities.',
                'wp-social-ninja-pro'),
            'valid_message'    => __('Your WP Social Ninja integration activated', 'wp-social-ninja-pro'),
            'invalid_message'  => __('WP Social Ninja need to approve first ', 'wp-social-ninja-pro'),
            'save_button_text' => __('Approve WP Social Ninja', 'wp-social-ninja-pro'),
            'hide_on_valid'    => true,
            'discard_settings' => [
                'section_description' => 'Your WP Social Ninja integration activated',
                'button_text'         => 'Deactivate',
                'data'                => [
                    'status' => true
                ],
                'show_verify'         => false
            ]
        ];
    }

    public function getGlobalSettings($settings)
    {
        $globalSettings = get_option($this->optionKey);
        if (!$globalSettings) {
            $globalSettings = [];
        }
        $defaults = [
            'status' => ''
        ];

        return wp_parse_args($globalSettings, $defaults);
    }

    public function getMergeFields($list, $listId, $formId)
    {
        return [];
    }

    public function saveGlobalSettings($settings)
    {
        if ($settings['status'] == '' || $settings['status'] == 'false') {
            update_option($this->optionKey, ['status' => true], 'no');
        } else {
            update_option($this->optionKey, ['status' => false], 'no');

            return wp_send_json_success([
                'status'  => false,
                'message' => __('WP Social Ninja Module Deactivated!', 'wp-social-ninja-pro')
            ], 200);
        };

        return wp_send_json_success([
            'status'  => true,
            'message' => __('WP Social Ninja activated!', 'wp-social-ninja-pro')
        ], 200);
    }

    public function pushIntegration($integrations, $formId)
    {
        $integrations[$this->integrationKey] = [
            'title'                 => $this->title . ' Integration',
            'logo'                  => $this->logo,
            'is_active'             => $this->isConfigured(),
            'configure_title'       => 'Configuration required!',
            'global_configure_url'  => admin_url('admin.php?page=fluent_forms_settings#general-wp_social_ninja-settings'),
            'configure_message'     => 'Activate global settings first',
            'configure_button_text' => 'Activate Globally'
        ];

        return $integrations;
    }

    public function getIntegrationDefaults($settings, $formId)
    {
        return [
            'name'                  => '',
            'fieldEmailAddress'     => '',
            'custom_field_mappings' => (object)[],
            'default_fields'        => (object)[],
            'conditionals'          => [
                'conditions' => [],
                'status'     => false,
                'type'       => 'all'
            ],
            'enabled'               => true
        ];
    }

    public function getSettingsFields($settings, $formId)
    {
        return [
            'fields'              => [
                [
                    'key'         => 'name',
                    'label'       => 'Name',
                    'required'    => true,
                    'placeholder' => 'Your Feed Name',
                    'component'   => 'text'
                ],
                [
                    'key'                => 'custom_field_mappings',
                    'require_list'       => false,
                    'label'              => 'Map Fields',
                    'tips'               => 'Select which Fluent Form fields pair with their<br /> respective WP Social Ninja fields.',
                    'component'          => 'map_fields',
                    'field_label_remote' => 'WP Social Ninja Field',
                    'field_label_local'  => 'Form Field',
//                    'primary_fileds'     => [
//                        [
//                            'key'           => 'fieldEmailAddress',
//                            'label'         => 'Email Address',
//                            'required'      => false,
//                            'input_options' => 'emails'
//                        ]
//                    ],
                    'default_fields'     => [
                        array(
                            'name'     => 'ratings',
                            'label'    => esc_html__('Ratings', 'wp-social-ninja-pro'),
                            'required' => true
                        ),
                        array(
                            'name'     => 'name',
                            'label'    => esc_html__('Reviewer Name', 'wp-social-ninja-pro'),
                            'required' => true
                        ),
                        array(
                            'name'     => 'email',
                            'label'    => esc_html__('Reviewer Email', 'wp-social-ninja-pro'),
                            'required' => false
                        ),
                        array(
                            'name'     => 'title',
                            'label'    => esc_html__('Review Title', 'wp-social-ninja-pro'),
                            'required' => false
                        ),
                        array(
                            'name'     => 'comment',
                            'label'    => esc_html__('Comment', 'wp-social-ninja-pro'),
                            'required' => true
                        ),
                        array(
                            'name'     => 'image',
                            'label'    => esc_html__('Reviewer Image', 'wp-social-ninja-pro'),
                            'required' => false
                        ),
                        array(
                            'name'     => 'reviewer_url',
                            'label'    => esc_html__('Reviewer Url', 'wp-social-ninja-pro'),
                            'required' => false
                        ),
	                    array(
		                    'name'     => 'category',
		                    'label'    => esc_html__('Category', 'wp-social-ninja-pro'),
		                    'required' => false
	                    )
                    ]
                ],
                [
                    'require_list' => false,
                    'key'          => 'conditionals',
                    'label'        => 'Conditional Logics',
                    'tips'         => 'Allow WP Social Ninja integration conditionally based on your submission values',
                    'component'    => 'conditional_block'
                ]
            ],
            'button_require_list' => false,
            'integration_title'   => $this->title
        ];
    }

    public function pushValidPlatform($platforms)
    {
        $settings = get_option('wpsr_fluent_forms_global_settings');
        if (!$settings) {
            $settings = array(
                'global_settings' => array(
                    'manually_review_approved'  => 'false'
                )
            );
            update_option('wpsr_fluent_forms_global_settings', $settings, 'no');
        }

        $platforms['fluent_forms'] = __('Fluent Forms', 'wp-social-ninja-pro');
        return $platforms;
    }

    public function notify($feed, $formData, $entry, $form)
    {
        $feedData = Arr::get($feed['processedValues'], 'default_fields');

        $email = Arr::get($feedData, 'email', '');

        $reviewer_img = '';
        if(Arr::get($feedData, 'image')) {
            $reviewer_img = Arr::get($feedData, 'image');
        }

        if(empty($reviewer_img)) {
            if ($email) {
                $reviewer_img = get_avatar_url($email);
            } else {
                $userId = get_current_user_id();
                if ($userId) {
                    $reviewer_img = esc_url(get_avatar_url($userId));
                }
            }
        }

        $global_settings          =  get_option('wpsr_fluent_forms_global_settings');
        $manually_review_approved = Arr::get($global_settings, 'global_settings.manually_review_approved', 'false');
        $review_approved          = $manually_review_approved === 'true' ? 0 : 1;

        $insert_data  = [
            'platform_name' => 'fluent_forms',
            'source_id'     => intval($form->id),
	        'category'      => Arr::get($feedData, 'category', ''),
            'reviewer_name' => Arr::get($feedData, 'name', ''),
            'review_title'  => Arr::get($feedData, 'title', ''),
            'reviewer_url'  => Arr::get($feedData, 'reviewer_url', ''),
            'reviewer_img'  => $reviewer_img,
            'reviewer_text' => Arr::get($feedData, 'comment', ''),
            'rating'        => intval(Arr::get($feedData, 'ratings')),
            'review_approved' => $review_approved,
            'review_time'   => current_time('mysql'),
            'updated_at'    => current_time('mysql'),
            'created_at'    => current_time('mysql')
        ];

        $response = Review::create($insert_data);

        if (is_wp_error($response)) {
            do_action('ff_integration_action_result', $feed, 'failed', $response->get_error_message());
            return false;
        } else {
            do_action('ff_integration_action_result',
                $feed,
                'success',
                'WP Social Ninja data inserted. Review ID: '.$response->id
            );

            return true;
        }
    }


    public function isEnabled()
    {
        return true;
    }

    public function isConfigured()
    {
        return true;
    }
}

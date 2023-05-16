<?php
/*
Plugin Name:  WP Social Ninja Pro
Plugin URI:   https://wpsocialninja.com/
Description:  WP Social Ninja - is an all-in-one WordPress Social plugin to automatically integrate your social media reviews, news feeds, and chat functionalities on your website.
Version:      3.5.7
Author:       WPManageNinja LLC
Author URI:   https://wpmanageninja.com
License:      GPLv2 or later
Text Domain:  wp-social-ninja-pro
Domain Path:  /language
*/

if (!defined('ABSPATH')) {
    exit;
}

if (!defined('WPSOCIALREVIEWS_PRO')) {
    define('WPSOCIALREVIEWS_PRO_VERSION', '3.5.7');
    define('WPSOCIALREVIEWS_PRO', true);
    define('WPSOCIALREVIEWS_PRO_MAIN_FILE', __FILE__);
    define('WPSOCIALREVIEWS_PRO_URL', plugin_dir_url(__FILE__));
    define('WPSOCIALREVIEWS_PRO_DIR', plugin_dir_path(__FILE__));

    class WPSocialReviewsPro
    {

        private $proScriptLoaded = false;

        public function boot()
        {
	        if (!defined('WPSOCIALREVIEWS_VERSION')) {
                $this->injectDependency();
                return;
            }
            $this->textDomain();
            $this->loadDependencies();
            $this->registerHooks();
	        if (isset($_GET['page']) && $_GET['page'] == 'wpsocialninja.php' && is_admin()) {
		        add_action('admin_enqueue_scripts', array($this, 'loadSwiperScripts'));
	        }

	        add_action('wp_social_review_loading_layout_masonry', function ($templateId) {
                wp_enqueue_script('imagesloaded');
                wp_enqueue_script('jquery-masonry');
                do_action('wp_social_ninja_add_layout_script');
            });

	        add_action('wp_social_review_loading_layout_carousel', function ($templateId) {
                $this->loadSwiperScripts();
                do_action('wp_social_ninja_add_layout_script');
            });

        }

        /**
         * Notify the user about the WP Social Ninja dependency and instructs to install it.
         */
        protected function injectDependency()
        {
            add_action('admin_notices', function () {
                $pluginInfo = $this->getBasePluginInstallationDetails();

                $class = 'notice notice-error';

                $install_url_text = __('Click Here to Install the Plugin', 'wp-social-ninja-pro');

                if ($pluginInfo->action == 'activate') {
                    $install_url_text = __('Click Here to Activate the Plugin', 'wp-social-ninja-pro');
                }

                $message = 'WP Social Ninja PRO Requires WP Social Ninja Base Plugin, <b><a href="' . $pluginInfo->url
                           . '">' . $install_url_text . '</a></b>';

                printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), $message);
            });
        }

        /**
         * Get the WP Social Ninja plugin installation information e.g. the URL to install.
         *
         * @return \stdClass $activation
         */
        protected function getBasePluginInstallationDetails()
        {
            $activation = (object)[
                'action' => 'install',
                'url'    => ''
            ];

            $allPlugins = get_plugins();

            $plugin_path = 'wp-social-reviews/wp-social-reviews.php';

            if (isset($allPlugins[$plugin_path])) {
                $url = wp_nonce_url(
                    self_admin_url('plugins.php?action=activate&plugin=' . $plugin_path . ''),
                    'activate-plugin_' . $plugin_path . ''
                );

                $activation->action = 'activate';
            } else {
                $api = (object)[
                    'slug' => 'wp-social-reviews'
                ];

                $url = wp_nonce_url(
                    self_admin_url('update.php?action=install-plugin&plugin=' . $api->slug),
                    'install-plugin_' . $api->slug
                );
            }
            $activation->url = $url;

            return $activation;
        }

        public function is_plugins_active($plugin_file_path = null)
        {
            $installed_plugins_list = get_plugins();
            return isset($installed_plugins_list[$plugin_file_path]);
        }

        public function textDomain()
        {
            load_plugin_textdomain('wp-social-ninja-pro', false, basename(dirname(__FILE__)) . '/language');
        }

        public function loadDependencies()
        {
            require_once(WPSOCIALREVIEWS_PRO_DIR . 'includes/autoload.php');
        }

        public function registerHooks()
        {
            new \WPSocialReviewsPro\Hooks\Frontend();
            new \WPSocialReviewsPro\Hooks\Backend();
            (new \WPSocialReviewsPro\Hooks\License())->init();

            if (defined('WPSOCIALREVIEWS_PRO')) {
                (new \WPSocialReviewsPro\Classes\Reviews\YelpBusiness())->registerHooks();
                (new \WPSocialReviewsPro\Classes\Reviews\Trustpilot())->registerHooks();
                (new \WPSocialReviewsPro\Classes\Reviews\Tripadvisor())->registerHooks();
                (new \WPSocialReviewsPro\Classes\Reviews\Amazon())->registerHooks();
                (new \WPSocialReviewsPro\Classes\Reviews\Aliexpress())->registerHooks();
                (new \WPSocialReviewsPro\Classes\Reviews\Booking())->registerHooks();
                (new \WPSocialReviewsPro\Classes\Reviews\FacebookBusiness())->registerHooks();
            }

            if (defined('FLUENTFORM')) {
                new \WPSocialReviewsPro\Classes\Reviews\Fluentform(wpFluentForm());
            }

//            if (defined('WC_VERSION')) {
//                add_action('woocommerce_after_register_post_type', [(new \WPSocialReviewsPro\Classes\Reviews\Woocommerce()), 'registerHooks']);
//            }
        }

        public function loadSwiperScripts()
        {
            wp_enqueue_script('swiper', WPSOCIALREVIEWS_PRO_URL . 'assets/libs/swiper/swiper-bundle.min.js', array('jquery'),
                WPSOCIALREVIEWS_PRO_VERSION, true);
            wp_enqueue_style(
                'swiper',
                WPSOCIALREVIEWS_PRO_URL . 'assets/libs/swiper/swiper-bundle.min.css',
                array(),
                WPSOCIALREVIEWS_PRO_VERSION
            );
        }

    }

    add_action('plugins_loaded', function () {
        (new WPSocialReviewsPro())->boot();
    });
}

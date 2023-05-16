<?php
/**
Plugin Name: Product Video Gallery for Woocommerce
Description: Adding Product YouTube Video and Instantly transform the gallery on your WooCommerce Product page into a fully Responsive Stunning Carousel Slider.
Author: NikHiL Gadhiya
Author URI: https://www.technosoftwebs.com
Date: 22/02/2023
Version: 1.4.1.3
Text Domain: product-video-gallery-slider-for-woocommerce
WC requires at least: 2.3
WC tested up to: 7.4.0

@package WC_PRODUCT_VIDEO_GALLERY
-------------------------------------------------*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'NICKX_PLUGIN_URL', 'https://www.technosoftwebs.com/' );
define( 'NICKX_PLUGIN_VERSION', '1.4.1.3' );

require_once plugin_dir_path( __FILE__ ) . 'js/nickx_live.php';

/**
	Activation
 */
function nickx_activation_hook_callback() {
	set_transient( 'nickx-plugin_setting_notice', true, 0 );
	if ( empty( get_option( 'nickx_slider_layout' ) ) ) {
		update_option( 'nickx_slider_layout', 'horizontal' );
		update_option( 'nickx_slider_responsive', 'no' );
		update_option( 'nickx_sliderautoplay', 'no' );
		update_option( 'nickx_sliderfade', 'no' );
		update_option( 'nickx_slider_swipe', 'no' );
		update_option( 'nickx_arrowinfinite', 'yes' );
		update_option( 'nickx_arrowdisable', 'yes' );
		update_option( 'nickx_arrow_thumb', 'no' );
		update_option( 'nickx_hide_thumbnails', 'no' );
		update_option( 'nickx_hide_thumbnail', 'yes' );
		update_option( 'nickx_gallery_action', 'no' );
		update_option( 'nickx_adaptive_height', 'yes' );
		update_option( 'nickx_place_of_the_video', 'no' );
		update_option( 'nickx_videoloop', 'no' );
		update_option( 'nickx_vid_autoplay', 'no' );
		update_option( 'nickx_template', 'no' );
		update_option( 'nickx_controls', 'yes' );
		update_option( 'nickx_show_lightbox', 'yes' );
		update_option( 'nickx_show_zoom', 'yes' );
		update_option( 'nickx_zoomlevel', 1 );
		update_option( 'nickx_show_only_video', 'no' );
		update_option( 'nickx_thumbnails_to_show', 4 );
		update_option( 'nickx_arrowcolor', '#000' );
		update_option( 'nickx_arrowbgcolor', '#FFF' );
	}
}

register_activation_hook( __FILE__, 'nickx_activation_hook_callback' );

/**
	Settings Class
 */
class WC_PRODUCT_VIDEO_GALLERY {
	/** @var $extend Lic value */
	public $extend;

	function __construct() {
		$this->add_actions( new NICKX_LIC_CLASS() );
	}
	private function add_actions( $extend ) {
		$this->extend = $extend;
		add_action( 'admin_notices', array( $this, 'nickx_notice_callback_notice' ) );
		add_action( 'admin_menu', array( $this, 'wc_product_video_gallery_setup' ) );
		add_action( 'admin_init', array( $this, 'update_wc_product_video_gallery_options' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_video_url_field' ) );
		add_action( 'save_post', array( $this, 'save_wc_video_url_field' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'nickx_enqueue_scripts' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'wc_prd_vid_slider_settings_link' ) );
		add_shortcode( 'product_gallery_shortcode', array( $this, 'product_gallery_shortcode_callback' ) );
		add_filter( 'wc_get_template', array( $this, 'nickx_get_template' ), 99, 5 );
	}
	public function nickx_notice_callback_notice() {
		if ( get_transient( 'nickx-plugin_setting_notice' ) ) {
			echo '<div class="notice-info notice is-dismissible"><p><strong>Product Video Gallery for Woocommerce is almost ready.</strong> To Complete Your Configuration, <a href="' . esc_url( admin_url() ) . 'edit.php?post_type=product&page=wc-product-video">Complete the setup</a>.</p></div>';
			delete_transient( 'nickx-plugin_setting_notice' );
		}
	}
	public function wc_product_video_gallery_setup() {
		add_submenu_page( 'edit.php?post_type=product', 'Product Video Gallery for Woocommerce', 'Product Video WC', 'manage_options', 'wc-product-video', array( $this, 'wc_product_video_callback' ) );
	}
	public function product_gallery_shortcode_callback( $atts = array() ) {
		ob_start();
		echo '<span id="product_gallery_shortcode">';
		$lic_chk_stateus = $this->extend->is_nickx_act_lic();
		if ( $lic_chk_stateus ) {
			nickx_show_product_image('shortcode');
		} else {
			echo 'To use shortcode you need to activate license key...!!';
		}
		echo '</span>';
		return ob_get_clean();
	}
	public function nickx_get_template( $located, $template_name, $args, $template_path, $default_path ) {
		if ( is_product() && 'single-product/product-image.php' == $template_name && get_option( 'nickx_template' ) == 'yes' ) {
			$located = plugin_dir_path( __FILE__ ).'template/product-video-template.php';
		}
		return $located;
	}
	public function wc_product_video_callback() {
		wp_enqueue_media();
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );
		echo '<style type="text/css">
		.boxed{padding:30px 0}
		.techno_tabs label{font-family:sans-serif;font-weight:400;vertical-align:top;font-size:15px}
		.wc_product_video_aria .techno_main_tabs{float:left;border:1px solid #ccc;border-bottom:none;margin-right:.5em;font-size:14px;line-height:1.71428571;font-weight:600;background:#e5e5e5;text-decoration:none;white-space:nowrap}
		.wc_product_video_aria .techno_main_tabs a{display:block;padding:5px 10px;text-decoration:none;color:#555}
		.wc_product_video_aria .main-panel{overflow:hidden;border-bottom:1px solid #ccc}
		.wc_product_video_aria .techno_main_tabs.active a{background:#f1f1f1}
		.wc_product_video_aria .techno_main_tabs a:focus{box-shadow:none;outline:0 solid transparent}
		.wc_product_video_aria .techno_main_tabs{display:inline-block;float:left}
		.wc_product_video_aria .techno_main_tabs.active{margin-bottom:-1px}
		.techno_tabs.tab_premium label{vertical-align:middle}
		.col-50{width:46%;float:left}
		.submit_btn_cls p{text-align: right;}
		.col-50 img{width:183px;float:left}tr.primium_aria {opacity: 0.5;cursor: help;}
		.primium_aria label, .primium_aria input { pointer-events: none; cursor: not-allowed;}
		.content_right a{background:#00f;font-family:"Trebuchet MS",sans-serif!important;display:inline-block;text-decoration:none;color:#fff;font-weight:700;background-color:#538fbe;padding:10px 40px;font-size:20px;border:1px solid #2d6898;background-image:linear-gradient(bottom,#4984b4 0,#619bcb 100%);background-image:-o-linear-gradient(bottom,#4984b4 0,#619bcb 100%);background-image:-moz-linear-gradient(bottom,#4984b4 0,#619bcb 100%);background-image:-webkit-linear-gradient(bottom,#4984b4 0,#619bcb 100%);background-image:-ms-linear-gradient(bottom,#4984b4 0,#619bcb 100%);background-image:-webkit-gradient(linear,left bottom,left top,color-stop(0,#4984b4),color-stop(1,#619bcb) );-webkit-border-radius:5px;-moz-border-radius:5px;border-radius:5px;text-shadow:0 -1px 0 rgba(0,0,0,.5);-webkit-box-shadow:0 0 0 #2b638f,0 3px 15px rgba(0,0,0,.4),inset 0 1px 0 rgba(255,255,255,.3),inset 0 0 3px rgba(255,255,255,.5);-moz-box-shadow:0 0 0 #2b638f,0 3px 15px rgba(0,0,0,.4),inset 0 1px 0 rgba(255,255,255,.3),inset 0 0 3px rgba(255,255,255,.5);box-shadow:0 0 0 #2b638f,0 3px 15px rgba(0,0,0,.4),inset 0 1px 0 rgba(255,255,255,.3),inset 0 0 3px rgba(255,255,255,.5);margin-top:10px}</style>
		<div class="wc-product-video-title">
			<h1>Product Video Gallery for Woocommerce</h1>
		</div>';
		if ( isset( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( $_REQUEST['_wpnonce'], 'nickx-license-deactive' ) && isset( $_REQUEST['deactivate_techno_wc_product_video_license'] ) ) {
			if ( $this->extend->nickx_deactive() ) {
				echo '<div id="message" class="updated fade"><p><strong>You license Deactivated successfuly...!!!</strong></p></div>';
			} else {
				echo '<div id="message" class="updated fade" style="border-left-color:#a00;"><p><strong>' . esc_html( $this->extend->err ) . '</strong></p></div>';
			}
		}
		$lic_chk_stateus = $this->extend->is_nickx_act_lic();
		if ( isset( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( $_REQUEST['_wpnonce'], 'nickx-license-active' ) && isset( $_REQUEST['activate_license_techno'] ) && ! empty( $_POST['techno_wc_product_video_license_key'] ) ) {
			$lic_chk_stateus = $this->extend->nickx_act_call( sanitize_text_field( $_POST['techno_wc_product_video_license_key'] ) );
		}
		echo '<div class="wrap tab_wrapper wc_product_video_aria">
			<div class="main-panel">
				<div id="tab_dashbord" class="techno_main_tabs active"><a href="#dashbord">Dashbord</a></div>
				<div id="tab_premium" class="techno_main_tabs"><a href="#premium">Premium</a></div>
			</div>
			<div class="boxed" id="percentage_form">
				<div class="techno_tabs tab_dashbord">
					<div class="wrap woocommerce">
						<form method="post" action="options.php">';
							settings_fields( 'wc_product_video_gallery_options' );
							do_settings_sections( 'wc_product_video_gallery_options' ); echo '
							<h2>WC Product Video Gallery Settings</h2>
							<div id="wc_prd_vid_slider-description">
								<p>The following options are used to configure WC Product Video Gallery</p>
							</div>
							<table class="form-table">
								<tbody>
									<tr valign="top">
										<th scope="row" class="titledesc">
											<label for="nickx_slider_layout">Slider Layout </label>
										</th>
										<td class="forminp forminp-select">
											<select name="nickx_slider_layout" id="nickx_slider_layout" style="">
												<option value="horizontal" ' . selected( 'horizontal', get_option( 'nickx_slider_layout' ), false ) . '>Horizontal</option>
												<option value="left" ' . selected( 'left', get_option( 'nickx_slider_layout' ), false ) . '>Vertical Left</option>
												<option value="right" ' . selected( 'right', get_option( 'nickx_slider_layout' ), false ) . '>Vertical Right</option>
											</select>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row" class="titledesc"><label for="nickx_slider_responsive">Slider Responsive</label></th>
										<td class="forminp forminp-checkbox">
											<input name="nickx_slider_responsive" id="nickx_slider_responsive" type="checkbox" value="yes" ' . checked( 'yes', get_option( 'nickx_slider_responsive' ), false ) . '>
											<samll class="lbl_tc">This option set the slider layout as Horizontal on mobile.</samll>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row" class="titledesc"><label for="nickx_sliderautoplay">Slider Auto-play</label></th>
										<td class="forminp forminp-checkbox">
											<input name="nickx_sliderautoplay" id="nickx_sliderautoplay" type="checkbox" value="yes" ' . checked( 'yes', get_option( 'nickx_sliderautoplay' ), false ) . '>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row" class="titledesc"><label for="nickx_slider_swipe">Slider Swipe</label></th>
										<td class="forminp forminp-checkbox">
											<input name="nickx_slider_swipe" id="nickx_slider_swipe" type="checkbox" value="yes" ' . checked( 'yes', get_option( 'nickx_slider_swipe' ), false ) . '>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row" class="titledesc"><label for="nickx_sliderfade">Slider Fade</label></th>
										<td class="forminp forminp-checkbox">
											<input name="nickx_sliderfade" id="nickx_sliderfade" type="checkbox" value="yes" ' . checked( 'yes', get_option( 'nickx_sliderfade' ), false ) . '>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row" class="titledesc"><label for="nickx_arrowinfinite">Slider Infinite Loop</label></th>
										<td class="forminp forminp-checkbox">
											<input name="nickx_arrowinfinite" id="nickx_arrowinfinite" type="checkbox" value="yes" ' . checked( 'yes', get_option( 'nickx_arrowinfinite' ), false ) . '>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row" class="titledesc"><label for="nickx_arrowdisable">Arrow on Slider</label></th>
										<td class="forminp forminp-checkbox">
											<input name="nickx_arrowdisable" id="nickx_arrowdisable" type="checkbox" value="yes" ' . checked( 'yes', get_option( 'nickx_arrowdisable' ), false ) . '>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row" class="titledesc"><label for="nickx_arrow_thumb">Arrow on Thumbnails</label></th>
										<td class="forminp forminp-checkbox">
											<input name="nickx_arrow_thumb" id="nickx_arrow_thumb" type="checkbox" value="yes" ' . checked( 'yes', get_option( 'nickx_arrow_thumb' ), false ) . '>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row" class="titledesc"><label for="custom_icon">Video Thumbnail for all Products.</label></th>
										<td class="forminp forminp-checkbox">
											<img style="max-width:80px;max-height:80px;" id="custom_video_thumb" src="' . esc_url( wp_get_attachment_image_url( get_option( 'custom_icon' ), 'thumbnail' ) ) . '">
											<input type="hidden" name="custom_icon" id="custom_icon" value="' . esc_attr( get_option( 'custom_icon' ) ) . '"/>
											<lable type="submit" class="upload_image_button button">Select Thumbnail</lable>
											<lable type="submit" class="remove_image_button button">X</lable>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row" class="titledesc"><label for="nickx_show_lightbox">Light-box</label></th>
										<td class="forminp forminp-checkbox">
											<input name="nickx_show_lightbox" id="nickx_show_lightbox" type="checkbox" value="yes" ' . checked( 'yes', get_option( 'nickx_show_lightbox' ), false ) . '>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row" class="titledesc"><label for="nickx_show_zoom">Zoom style</label></th>
										<td class="forminp forminp-checkbox">
											<select name="nickx_show_zoom" id="nickx_show_zoom" style="">
												<option value="window" ' . selected( 'window', get_option( 'nickx_show_zoom' ), false ) . '>Window Right side</option>
												<option value="yes" ' . selected( 'yes', get_option( 'nickx_show_zoom' ), false ) . '>Inner</option>
												<option value="lens" ' . selected( 'lens', get_option( 'nickx_show_zoom' ), false ) . '>Lens</option>
												<option value="off" ' . selected( 'off', get_option( 'nickx_show_zoom' ), false ) . '>Off</option>
											</select>
										</td>
									</tr>									
									<tr valign="top">
										<th scope="row" class="titledesc"><label for="nickx_zoomlevel">Zoom Level</label></th>
										<td class="forminp forminp-checkbox">
											<input name="nickx_zoomlevel" id="nickx_zoomlevel" type="number" min="0.1" max="10" step="0.01" value="' . esc_attr( get_option( 'nickx_zoomlevel', 1 ) ) . '">
										</td>
									</tr>									
									<tr valign="top">
										<th scope="row" class="titledesc"><label for="nickx_template">Allow Template Filter</label></th>
										<td class="forminp forminp-checkbox">
											<input name="nickx_template" id="nickx_template" type="checkbox" value="yes" ' . checked( 'yes', get_option( 'nickx_template', 'no' ), false ) . '>
											<samll class="lbl_tc">Enable this if your single product pages edited with help of any page builders Divi Builder, Elementor Builder etc.</samll>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row" class="titledesc"><label for="nickx_gallery_action">Remove Action</label></th>
										<td class="forminp forminp-checkbox">
											<input name="nickx_gallery_action" id="nickx_gallery_action" type="checkbox" value="yes" ' . checked( 'yes', get_option( 'nickx_gallery_action', 'no' ), false ) . '>
											<samll class="lbl_tc">Enable this if your single product pages edited with help of Divi Builder.</samll>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row" class="titledesc"><label for="nickx_hide_thumbnails">Hide Thumbnails</label></th>
										<td class="forminp forminp-checkbox">
											<input name="nickx_hide_thumbnails" id="nickx_hide_thumbnails" type="checkbox" value="yes" ' . checked( 'yes', get_option( 'nickx_hide_thumbnails' ), false ) . '>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row" class="titledesc"><label for="nickx_hide_thumbnail">Hide Thumbnail</label></th>
										<td class="forminp forminp-checkbox">
											<input name="nickx_hide_thumbnail" id="nickx_hide_thumbnail" type="checkbox" value="yes" ' . checked( 'yes', get_option( 'nickx_hide_thumbnail', 'yes' ), false ) . '>
											<samll class="lbl_tc">Hide thumbnail if product have only one image/video.</samll>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row" class="titledesc"><label for="nickx_thumbnails_to_show">Thumbnails to show</label></th>
										<td class="forminp forminp-checkbox">
											<input name="nickx_thumbnails_to_show" id="nickx_thumbnails_to_show" type="number" min="3" max="8" value="' . esc_attr( get_option( 'nickx_thumbnails_to_show', 4 ) ) . '"><small> Set how many thumbnails to show. You can show min 3 and  max 8 thumbnails.</small>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row" class="titledesc"><label for="nickx_adaptive_height">Adaptive Height</label></th>
										<td class="forminp forminp-checkbox">
											<input name="nickx_adaptive_height" id="nickx_adaptive_height" type="checkbox" value="yes" ' . checked( 'yes', get_option( 'nickx_adaptive_height', 'yes' ), false ) . '>
											<samll class="lbl_tc">Slider height based on images automatically.</samll>
										</td>
									</tr>
									<tr valign="top" ' . ( ( $lic_chk_stateus ) ? '' : 'class="primium_aria" title="AVAILABLE IN PREMIUM VERSION"' ) . '">
										<th scope="row" class="titledesc"><label for="nickx_show_only_video">Show Only Video</label></th>
										<td class="forminp forminp-checkbox">
											<input name="nickx_show_only_video" id="nickx_show_only_video" type="checkbox" value="yes" ' . checked( 'yes', get_option( 'nickx_show_only_video', 'no' ), false ) . '>
											<samll>Only show the videos on gellery.</samll>
										</td>
									</tr>
									<tr valign="top" ' . ( ( $lic_chk_stateus ) ? '' : 'class="primium_aria" title="AVAILABLE IN PREMIUM VERSION"' ) . '">
										<th scope="row" class="titledesc"><label for="nickx_controls">Show Video Controls</label></th>
										<td class="forminp forminp-checkbox">
											<input name="nickx_controls" id="nickx_controls" type="checkbox" value="yes" ' . checked( 'yes', get_option( 'nickx_controls', 'yes' ), false ) . '>
											<samll class="lbl_tc">Only for Self Hosted Video</samll>
										</td>
									</tr>
									<tr valign="top" ' . ( ( $lic_chk_stateus ) ? '' : 'class="primium_aria" title="AVAILABLE IN PREMIUM VERSION"' ) . '">
										<th scope="row" class="titledesc"><label for="nickx_videoloop">Video Looping</label></th>
										<td class="forminp forminp-checkbox">
											<input name="nickx_videoloop" id="nickx_videoloop" type="checkbox" value="yes" ' . checked( 'yes', get_option( 'nickx_videoloop' ), false ) . '>
											<samll class="lbl_tc">Looping a video is allowing the video to play in a repeat mode.</samll>
											<p><samll>Auto play works only when <b>Place of The Video</b> is <b>Before Product Gallery Images</b>.</samll></p>
										</td>
									</tr>
									<tr valign="top" ' . ( ( $lic_chk_stateus ) ? '' : 'class="primium_aria" title="AVAILABLE IN PREMIUM VERSION"' ) . '">
										<th scope="row" class="titledesc"><label for="nickx_vid_autoplay">Auto Play Video</label></th>
										<td class="forminp forminp-checkbox">
											<input name="nickx_vid_autoplay" id="nickx_vid_autoplay" type="checkbox" value="yes" ' . checked( 'yes', get_option( 'nickx_vid_autoplay' ), false ) . '>
											<samll>Auto play works only when <b>Place of The Video</b> is <b>Before Product Gallery Images</b>.</samll>
											<p><samll>If you enable this option, the video will be muted by default, so you have to manually unmute the video.</samll></p>
											<p><samll>Please pass <b>autoplay=1</b> parameter with your video url if you are using YouTube or Vimeo video.</samll></p>
										</td>
									</tr>
									<tr valign="top" ' . ( ( $lic_chk_stateus ) ? '' : 'class="primium_aria" title="AVAILABLE IN PREMIUM VERSION"' ) . '">
										<th scope="row" class="titledesc"><label for="nickx_place_of_the_video">Place Of The Video</label></th>
										<td class="forminp forminp-checkbox">
											<select name="nickx_place_of_the_video" id="nickx_place_of_the_video" style="">
												<option value="no" ' . selected( 'no', get_option( 'nickx_place_of_the_video' ), false ) . '>After Product Gallery Images</option>
												<option value="second" ' . selected( 'second', get_option( 'nickx_place_of_the_video' ), false ) . '>After Product Image</option>
												<option value="yes" ' . selected( 'yes', get_option( 'nickx_place_of_the_video' ), false ) . '>Before Product Gallery Images</option>
											</select>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row" class="titledesc"><label for="nickx_arrowcolor">Arrow Color</label></th>
										<td class="forminp forminp-color">
											<input name="nickx_arrowcolor" id="nickx_arrowcolor" type="text" value="' . esc_attr( get_option( 'nickx_arrowcolor' ) ) . '" class="colorpick">
										</td>
									</tr>
									<tr valign="top">
										<th scope="row" class="titledesc"><label for="nickx_arrowbgcolor">Arrow Background Color</label></th>
										<td class="forminp forminp-color">
											<input name="nickx_arrowbgcolor" id="nickx_arrowbgcolor" type="text" value="' . esc_attr( get_option( 'nickx_arrowbgcolor' ) ) . '" class="colorpick">
										</td>
									</tr>
									<tr valign="top" ' . ( ( $lic_chk_stateus ) ? '' : 'class="primium_aria" title="AVAILABLE IN PREMIUM VERSION"' ) . '">
										<th scope="row" class="titledesc"><label for="nickx_shortcode">Shortcode</label></th>
										<td class="forminp forminp-info">
											<small id="nickx_shortcode">Use this <b>[product_gallery_shortcode]</b> shortcode if your product pages edited with help of any page builders (Divi Builder, Elementor Builder etc.)</small>
										</td>
									</tr>
								</tbody>
								<tfoot><tr><td class="submit_btn_cls">';
								submit_button();
								echo '</td></tr></tfoot>
							</table>
						</form>
					</div>
				</div>
				<div class="techno_tabs tab_premium" style="display:none;">';
		if ( isset( $_REQUEST['activate_license_techno'] ) && isset( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( $_REQUEST['_wpnonce'] ), 'nickx-license-active' ) ) {
			if ( $lic_chk_stateus ) {
				echo '<div id="message" class="updated fade"><p><strong>You license Activated successfuly...!!!</strong></p></div>
				<form method="POST">';
					wp_nonce_field( 'nickx-license-deactive' );
					echo '<div class="col-50">
						<h2> Thank You Phurchasing ...!!!</h2>
						<h4 class="paid_color">Deactivate Yore License:</h4>
						<p class="submit">
							<input type="submit" name="deactivate_techno_wc_product_video_license" value="Deactive" class="button button-primary">
						</p>
					</div>
				</form>';
			} else {
				$this->techno_wc_product_video_pro_html();
				echo '<div id="message" class="updated fade" style="border-left-color:#a00;"><p><strong>' . esc_html( $this->extend->err ) . '</strong></p></div>';
			}
		} elseif ( $this->extend->is_nickx_act_lic() ) {

			echo '<form method="POST">';
					wp_nonce_field( 'nickx-license-deactive' );
					echo '<div class="col-50">
					<h2> Thank You Phurchasing ...!!!</h2>
					<h4 class="paid_color">Deactivate Yore License:</h4>
					<p class="submit">
						<input type="submit" name="deactivate_techno_wc_product_video_license" value="Deactive" class="button button-primary">
					</p>
				</div>
			</form>';
		} else {
			$this->techno_wc_product_video_pro_html();
			echo esc_html( $this->extend->err );
		}
		echo '</div></div></div>
		<script type="text/javascript">
			jQuery(document).ready(function(e)
			{
				jQuery(".colorpick").each(function(w)
				{
					jQuery(this).wpColorPicker();
				});
				jQuery("div.techno_main_tabs").click(function(e)
				{
					jQuery(".techno_main_tabs").removeClass("active");
					jQuery(this).addClass("active");
					jQuery(".techno_tabs").hide();
					jQuery("."+this.id).show();
				});
				jQuery("tr.primium_aria").click(function(e) {
					jQuery("#tab_premium").trigger("click");
				});
				jQuery(".upload_image_button").click(function(e) {
					var send_attachment_bkp = wp.media.editor.send.attachment;
					wp.media.editor.send.attachment = function(props, attachment)
					{
						jQuery("#custom_icon").val(attachment.id);
						jQuery("#custom_video_thumb").attr("src",attachment.url).show();
						wp.media.editor.send.attachment = send_attachment_bkp;
					}
					wp.media.editor.open(this);
					return false;
	  			});
				jQuery(".remove_image_button").click(function(e) {
					var answer = confirm("Are you sure?");
					if (answer == true)
					{
						jQuery("#custom_icon").val("");
						jQuery("#custom_video_thumb").attr("src","").hide();
					}
					return false;
				});
			});
		</script>';
	}
	public function techno_wc_product_video_pro_html() {
		$pugin_path = plugin_dir_url( __FILE__ ); 
		echo '<form method="POST">';
		wp_nonce_field( 'nickx-license-active' );
		echo '<div class="col-50">
			<h2>Product Video Gallery for Woocommerce</h2>
			<h4 class="paid_color">Premium Features:</h4>
			<p class="paid_color">01. You Can Use Vimeo And Html5 Video(MP4, WebM, and Ogg).</p>
			<p class="paid_color">02. You Can Add Multiple videos.</p>
			<p class="paid_color">03. Change The Place Of The Video(After Product Gallery Images, After Product Image and Before Product Gallery Images).</p>
			<p class="paid_color">04. Video Looping (Looping a video is allowing the video to play in a repeat mode).</p>
			<p class="paid_color">05. Show Only Videos (Display only videos on gallery).</p>
			<p class="paid_color">06. Shortcode (Use shortcode if your product pages edited with help of any page builders <b>Divi Builder, Elementor Builder etc.</b>).</p>
			<p><label for="techno_wc_product_videokey">License Key : </label><input class="regular-text" type="text" id="techno_wc_product_video_license_key" name="techno_wc_product_video_license_key"></p>
			<p class="submit">
			<input type="submit" name="activate_license_techno" value="Activate" class="button button-primary">
			</p>
		</div>
		<div class="col-50">
			<div class="content_right" style="text-align: center;">
				<p style="font-size: 25px; font-weight: bold; color: #f00;">Buy Activation Key form Here...</p>
				<p><a href="https://www.technosoftwebs.com/wc-product-video-gallery/" target="_blank">Buy Now...</a></p>
			</div>
		</div>
		</form>';
	}
	public function update_wc_product_video_gallery_options( $value = '' ) {
		register_setting( 'wc_product_video_gallery_options', 'nickx_slider_layout' );
		register_setting( 'wc_product_video_gallery_options', 'nickx_slider_responsive' );
		register_setting( 'wc_product_video_gallery_options', 'nickx_sliderautoplay' );
		register_setting( 'wc_product_video_gallery_options', 'nickx_slider_swipe' );
		register_setting( 'wc_product_video_gallery_options', 'nickx_sliderfade' );
		register_setting( 'wc_product_video_gallery_options', 'nickx_arrowinfinite' );
		register_setting( 'wc_product_video_gallery_options', 'nickx_arrowdisable' );
		register_setting( 'wc_product_video_gallery_options', 'nickx_arrow_thumb' );
		register_setting( 'wc_product_video_gallery_options', 'nickx_show_lightbox' );
		register_setting( 'wc_product_video_gallery_options', 'nickx_show_zoom' );
		register_setting( 'wc_product_video_gallery_options', 'nickx_zoomlevel' );
		register_setting( 'wc_product_video_gallery_options', 'nickx_arrowcolor' );
		register_setting( 'wc_product_video_gallery_options', 'nickx_show_only_video' );
		register_setting( 'wc_product_video_gallery_options', 'custom_icon' );
		register_setting( 'wc_product_video_gallery_options', 'nickx_hide_thumbnails' );
		register_setting( 'wc_product_video_gallery_options', 'nickx_hide_thumbnail' );
		register_setting( 'wc_product_video_gallery_options', 'nickx_gallery_action' );
		register_setting( 'wc_product_video_gallery_options', 'nickx_template' );
		register_setting( 'wc_product_video_gallery_options', 'nickx_thumbnails_to_show' );
		register_setting( 'wc_product_video_gallery_options', 'nickx_arrowbgcolor' );
		register_setting( 'wc_product_video_gallery_options', 'nickx_adaptive_height' );
		if ( $this->extend->is_nickx_act_lic() ) {
			register_setting( 'wc_product_video_gallery_options', 'nickx_videoloop' );
			register_setting( 'wc_product_video_gallery_options', 'nickx_vid_autoplay' );
			register_setting( 'wc_product_video_gallery_options', 'nickx_controls' );
			register_setting( 'wc_product_video_gallery_options', 'nickx_place_of_the_video' );
		}
	}
	public function wc_prd_vid_slider_settings_link( $links ) {
		$links[] = '<a href="' . esc_url( admin_url() ) . 'edit.php?post_type=product&page=wc-product-video">Settings</a>';
		return $links;
	}
	public function add_video_url_field() {
		add_meta_box( 'video_url', 'Product Video Url', array( $this, 'video_url_field' ), 'product' );
	}
	public function get_video_field_html( $product_video_type, $product_video_url, $custom_thumbnail, $product_video_thumb_url, $product_video_thumb_id, $video_schema, $video_upload_date, $video_name, $video_description ) {
		echo '<tr>
			<td colspan="2">
				<div class="video_url_aria">
					<div>
						<label class="nickx_lbl nickx_product_video_type_lbl" for="nickx_product_video_type">Video Type</label>
						<select name="nickx_product_video_type[]" class="nickx_input">
							<option value="nickx_video_url_youtube" ' . selected( $product_video_type, 'nickx_video_url_youtube', false ) . '>Youtube Video</option>
							<option value="nickx_video_url_vimeo" ' . selected( $product_video_type, 'nickx_video_url_vimeo', false ) . '>Vimeo Video</option>
							<option value="nickx_video_url_local" ' . selected( $product_video_type, 'nickx_video_url_local', false ) . '>Self Hosted Video(MP4, WebM, and Ogg)</option>
							<option value="nickx_video_url_iframe" ' . selected( $product_video_type, 'nickx_video_url_iframe', false ) . '>Other (embedUrl)</option>
						</select>
					</div>
					<div style="display: inline-block;">
						<div style="display: inline-block; vertical-align: top;">
							<label class="nickx_lbl" for="nickx_video_text_urls">Video  Url</label>
						</div>
						<div style="display: inline-block;">
							<div>
								<input type="url" class="nickx_input nickx_video_text_urls" value="' . esc_url( $product_video_url ) . '" name="nickx_video_text_url[]" placeholder="URL of your video">
								<span><label style="display: none;" class="select_video_button button">Select Video</label><input type="hidden" name="video_attachment_id" id="video_attachment_id"></span>
							</div>
							<div>
								<small style="display: none;" class="nickx_url_info nickx_video_url_youtube">https://www.youtube.com/embed/.....</small>
								<small style="display: none;" class="nickx_url_info nickx_video_url_vimeo">https://player.vimeo.com/video/......</small>
								<small style="display: none;" class="nickx_url_info nickx_video_url_iframe">Your embed video url.</small>
								<small style="display: none;" class="nickx_url_info nickx_video_url_local">' . esc_url( get_site_url() ) . '/wp-content/upload/......</small>
							</div>
						</div>
					</div>
					<div>
						<div>							
							<input type="hidden" value="' . esc_attr( $custom_thumbnail ) . '" name="custom_thumbnail[]">
							<label class="nickx_tab"><input type="checkbox" class="custom_thumbnail" value="yes" ' . checked( 'yes', $custom_thumbnail, false ) . '> Use Custom video Thumbnail?</label>
						</div>
						<div class="select_video_thumbnail" style="display:' . ( ( $custom_thumbnail != 'yes' ) ? 'none' : 'block' ) . ';">
							<div class="video_thumbnail_aria">
								<img style="max-width:80px;max-height:80px;" class="product_video_thumb" src="' . esc_url( $product_video_thumb_url ) . '">
							</div>
							<div class="video_thumbnail_btn">
								<label class="select_video_thumb_button button">Select Video Thumbnail</label>
								<input type="hidden" value="' . esc_attr( $product_video_thumb_id ) . '" name="product_video_thumb_url[]" class="product_video_thumb_url">
								<lable type="submit" class="remove_image_button button">X</lable>
							</div>
						</div>
					</div>
					<div>
						<div>							
							<input type="hidden" value="' . esc_attr( $video_schema ) . '" name="video_schema[]">
							<label class="nickx_tab"><input type="checkbox" class="video_schema" value="yes" ' . checked( 'yes', $video_schema, false ) . '> Add Video Schema?</label>
						</div>
						<div class="select_video_schema" style="display:' . ( ( $video_schema != 'yes' ) ? 'none' : 'block' ) . ';">
							<div class="video_schema_aria">
								<label class="nickx_lbl_schema">Upload Date</label>
								<input type="date" value="' . esc_attr( $video_upload_date ) . '" name="nickx_video_upload_date[]"> <small>The date the video was first published.</small>
							</div>
							<div class="video_schema_aria">
								<label class="nickx_lbl_schema">Video Name</label>
								<input type="text" value="' . esc_attr( $video_name ) . '" name="nickx_video_name[]"> <small>The title of the video.</small>
							</div>
							<div class="video_schema_aria">
								<label class="nickx_lbl_schema">Video Description</label>
								<textarea name="nickx_video_description[]" rows="2" cols="20">' . $video_description . '</textarea><small>The description of the video.</small>
							</div>
						</div>
					</div>
				</div>
				<div class="video_delete_aria"><b class="button video-remove-btn" title="Remove Video"><span class="dashicons dashicons-remove"></span></b></div>
			</td>
		</tr>';
	}
	public function nickx_meta_extend_call( $product_id ) {
		wp_enqueue_script( 'media-upload' );
		wp_enqueue_media();
		$product_video_types     = get_post_meta( $product_id, '_nickx_product_video_type', true );
		$product_video_urls      = get_post_meta( $product_id, '_nickx_video_text_url', true ); 
		$product_video_thumb_ids = get_post_meta( $product_id, '_nickx_product_video_thumb_ids', true );
		$custom_thumbnails       = get_post_meta( $product_id, '_custom_thumbnail', true );
		$video_schemas           = get_post_meta( $product_id, '_video_schema', true );
		$video_upload_dates      = get_post_meta( $product_id, '_nickx_video_upload_date', true );
		$video_names             = get_post_meta( $product_id, '_nickx_video_name', true );
		$video_descriptions      = get_post_meta( $product_id, '_nickx_video_description', true );
		echo '
		<style type="text/css"> 
			.nickx_lbl,.video_delete_aria,.video_thumbnail_aria,.video_thumbnail_btn,.video_url_aria{display:inline-block}
			button.button.add_video{color:#fff;background-color:#5cb85c;border-color:#4cae4c}
			table.product_videos_tbl tbody tr td{background:#ddd;border:1px solid #aaa;padding:15px}
			.nickx_lbl{min-width:64px}
			.nickx_input{width:300px}
			.video_thumbnail_btn{vertical-align:bottom;padding-bottom:25px}
			b.button.video-remove-btn{padding:10px 20px 0;color:#b32d2e;background:#fff1f1;border-color:#b32d2e}
			.video_url_aria{width:92%}
			.video_delete_aria{text-align:right;width:7.4%;vertical-align:top}
			button.button.add_video span.dashicons.dashicons-insert{vertical-align:text-top}
			.video_schema_aria {display: inline-grid;}
		</style>
		<div class="nickx_product_video_url_section">
			<table class="product_videos_tbl" style="width: 100%;">
				<thead><tr><th style="text-align: left;">Select Video Source</th><td style="text-align: right;"><button type="button" class="button add_video"><b><span class="dashicons dashicons-insert"></span></b> Add Video</button></td></tr></thead>
				<tbody>';
				if ( is_array($product_video_urls) ) {
					foreach ($product_video_urls as $key => $product_video_url) {
						$product_video_type = $product_video_types[$key];						
						$product_video_thumb_url = wc_placeholder_img_src();
						$product_video_thumb_id = '';
						if ( ! empty( $product_video_thumb_ids[$key] ) ) {
							$product_video_thumb_id = $product_video_thumb_ids[$key];
							$product_video_thumb_url = wp_get_attachment_image_url( $product_video_thumb_id );
						}
						$custom_thumbnail  = (isset($custom_thumbnails[$key])) ? $custom_thumbnails[$key] : 'no';
						$video_schema      = (isset($video_schemas[$key])) ? $video_schemas[$key] : 'no';
						$video_upload_date = (isset($video_upload_dates[$key])) ? $video_upload_dates[$key] : '';
						$video_name        = (isset($video_names[$key])) ? $video_names[$key] : '';
						$video_description = (isset($video_descriptions[$key])) ? $video_descriptions[$key] : '';
						
						$this->get_video_field_html( $product_video_types[$key], $product_video_url, $custom_thumbnail, $product_video_thumb_url, $product_video_thumb_id, $video_schema, $video_upload_date, $video_name, $video_description );
					}
				} else {
					$product_video_thumb_url = wc_placeholder_img_src();
					if ( ! empty( $product_video_thumb_ids ) ) {
						$product_video_thumb_url = wp_get_attachment_image_url( $product_video_thumb_ids );
					}
					$this->get_video_field_html( $product_video_types, $product_video_urls, $custom_thumbnails, $product_video_thumb_url, $product_video_thumb_ids, $video_schemas, $video_upload_dates, $video_names, $video_descriptions );
				}
				echo'
				</tbody>
			</table>
		</div>'; ?>
		<script type="text/javascript">
			jQuery(document).ready(function($) {
				$(document).on('change','select[name^="nickx_product_video_type["]',function(e) {
					set_video_type(this);
				});
				$(document).on('change','input[name^="nickx_video_text_url["]',function(e) {
					let video_url = this.value;
					let video_aria = $(this).parents('.video_url_aria');
					if (video_url.indexOf("youtu") > 0) {
						video_aria.find('select[name^="nickx_product_video_type["]').val('nickx_video_url_youtube').change();
					} else if (video_url.indexOf("vimeo") > 0) {
						video_aria.find('select[name^="nickx_product_video_type["]').val('nickx_video_url_vimeo').change();
					} else if (video_url.indexOf(window.location.hostname) > 0) {
						video_aria.find('select[name^="nickx_product_video_type["]').val('nickx_video_url_local').change();
					} else {
						video_aria.find('select[name^="nickx_product_video_type["]').val('nickx_video_url_iframe').change();
					}
				});
				$(document).on('change','input.custom_thumbnail',function(e) {
					let video_aria = $(this).parents('.video_url_aria');
					if (this.checked) {
						video_aria.find(".select_video_thumbnail").show();
						video_aria.find('input[name^="custom_thumbnail["]').val('yes');
					} else {
						video_aria.find('input[name^="custom_thumbnail["]').val('no');
						video_aria.find(".select_video_thumbnail").hide();
					}
				});
				$(document).on('change','input.video_schema',function(e) {
					let video_aria = $(this).parents('.video_url_aria');
					if (this.checked) {
						video_aria.find(".select_video_schema").show();
						video_aria.find('input[name^="video_schema["]').val('yes');
					} else {
						video_aria.find(".select_video_schema").hide();
						video_aria.find('input[name^="video_schema["]').val('no');
					}
				});
				$('select[name^="nickx_product_video_type["]').each(function(e) {
					set_video_type(this);
				});
				$(document).on('click','.select_video_button',function(e) {
					let video_aria = $(this).parents('.video_url_aria');
					nickx_video_uploader = wp.media({ library: {type: "video"},title: "Select Video"});
					nickx_video_uploader.on("select", function(e) {
						var file = nickx_video_uploader.state().get("selection").first();
						var extension = file.changed.subtype;
						var video_url = file.changed.url;
						video_aria.find(".nickx_video_text_urls").val(video_url);
					});
					nickx_video_uploader.open();
				});
				$(document).on('click','.select_video_thumb_button',function(e) {				
					let video_aria = $(this).parents('.video_url_aria');
				  	nickx_video_thumb_uploader = wp.media({ library: {type: "image"},title: "Select Video Thumbnail"});
				  	nickx_video_thumb_uploader.on("select", function(e) {
						var file = nickx_video_thumb_uploader.state().get("selection").first();
						var id = file.attributes.id;
						var video_thumb_url = file.changed.url;
						video_aria.find(".product_video_thumb").attr("src",video_thumb_url).show();
						video_aria.find(".product_video_thumb_url").val(id);
				  	});
				  	nickx_video_thumb_uploader.open();
				});
				$(document).on('click','.remove_image_button',function(e) {
					let video_aria = $(this).parents('.video_url_aria');
					video_aria.find(".product_video_thumb").attr("src","").hide();
					video_aria.find(".product_video_thumb_url").val("");
					return false;
				});
				$(document).on('click','.product_videos_tbl b.button.video-remove-btn', function(e){
					$(this).parents('tr').remove();
				});
				$(document).on('click','.product_videos_tbl .add_video', function(e){
					const html = '<tr><td colspan="2"><div class="video_url_aria"><div><label class="nickx_lbl nickx_product_video_type_lbl" for="nickx_product_video_type">Video Type</label><select name="nickx_product_video_type[]" class="nickx_input"><option value="nickx_video_url_youtube">Youtube Video</option><option value="nickx_video_url_vimeo">Vimeo Video</option><option value="nickx_video_url_local">Self Hosted Video(MP4, WebM, and Ogg)</option><option value="nickx_video_url_iframe">Other (embedUrl)</option></select></div><div style="display: inline-block;"><div style="display: inline-block; vertical-align: top;"><label class="nickx_lbl" for="nickx_video_text_urls">Video Url</label></div><div style="display: inline-block;"><div><input type="url" class="nickx_input nickx_video_text_urls" name="nickx_video_text_url[]" placeholder="URL of your video"><span><label style="display: none;" class="select_video_button button">Select Video</label><input type="hidden" name="video_attachment_id" id="video_attachment_id"></span></div><div><small style="display: none;" class="nickx_url_info nickx_video_url_youtube">https://www.youtube.com/embed/.....</small><small style="display: none;" class="nickx_url_info nickx_video_url_vimeo">https://player.vimeo.com/video/......</small><small style="display: none;" class="nickx_url_info nickx_video_url_local">./wp-content/upload/......</small><small style="display: none;" class="nickx_url_info nickx_video_url_iframe">Your embed video url.</small></div></div></div><div><div><input type="checkbox" class="custom_thumbnail" value="yes"><input type="hidden" value="no" name="custom_thumbnail[]"><label class="nickx_tab" for="custom_thumbnail">Use Custom video Thumbnail?</label></div><div class="select_video_thumbnail" style="display:none;"><div class="video_thumbnail_aria"><img style="max-width:80px;max-height:80px;" class="product_video_thumb"></div><div class="video_thumbnail_btn"><label class="select_video_thumb_button button">Select Video Thumbnail</label><input type="hidden" name="product_video_thumb_url[]" class="product_video_thumb_url"><lable type="submit" class="remove_image_button button">X</lable></div></div></div></div><div class="video_delete_aria"><b class="button video-remove-btn" title="Remove Video"><span class="dashicons dashicons-remove"></span></b></div></td></tr>';
					$('.product_videos_tbl tbody').append(html);
				});
			});
			function set_video_type(video) {
				let video_type = video.value;
				let video_aria = jQuery(video).parents('.video_url_aria');
				video_aria.find(".nickx_url_info,.select_video_button").hide();
				video_aria.find("."+video_type).show();
				video_aria.find("label.nickx_tab").removeClass("active");
				video_aria.find("label[for="+video_type+"]").addClass("active");
				if (video_type=="nickx_video_url_local") {
					video_aria.find(".select_video_button").show();
				}
			}
		</script><?php
	}
	public function video_url_field() {
        wp_nonce_field( 'nickx_video_url_nonce_action', 'nickx_video_url_nonce' );
		$product_video_url = get_post_meta( get_the_ID(), '_nickx_video_text_url', true );		
		$product_video_thumb_id = get_post_meta( get_the_ID(), '_nickx_product_video_thumb_ids', true );
		if ( ! $this->extend->is_nickx_act_lic() ) {
			$product_video_url = is_array($product_video_url) ? $product_video_url[0] : $product_video_url;
			$product_video_thumb_id = is_array($product_video_thumb_id) ? $product_video_thumb_id[0] : $product_video_thumb_id;
			echo '<style type="text/css">.nickx_product_video_url_section ul li { display: inline-block; vertical-align: middle; padding: 0; margin: 0 auto; }button.button.add_video{color:#fff;background-color:#5cb85c;border-color:#4cae4c}</style>
			<div class="nickx_product_video_url_section">
			<div style="display: inline-block; width: 80%;">
			<ul>
				<li>
					<input type="radio" checked name="nickx_product_video_type[]" value="nickx_video_url_youtube" id="nickx_video_url_youtube">
					<label class="nickx_tab active" for="nickx_video_url_youtube">Youtube</label>
				</li>
				<li>
					<input type="radio" name="nickx_product_video_type" disabled>
					<label class="nickx_tab" for="nickx_video_url_vimeo">Vimeo' . wc_help_tip( '<p style="font-size: 25px; font-weight: bold;>available in premium version<br>Buy Activation Key form Setting Page</p>', true ) . '</label>
				</li>
				<li>
					<input type="radio" name="nickx_product_video_type" disabled>
					<label class="nickx_tab" for="nickx_video_url_local">WP Library' . wc_help_tip( '<p style="font-size: 25px; font-weight: bold;>available in premium version<br>Buy Activation Key form Setting Page</p>', true ) . '</label>
				</li>
			</ul><input type="hidden" value="' . esc_attr( $product_video_thumb_id ) . '" name="product_video_thumb_url[]" class="product_video_thumb_url">
			</div>
			<div style="display: inline-block;"><button type="button" class="button add_video" disabled><b><span class="dashicons dashicons-insert" style="vertical-align: middle;"></span></b> Add More Videos ' . wc_help_tip( '<p style="font-size: 25px; font-weight: bold;>available in premium version<br>Buy Activation Key form Setting Page</p>', true ) . '</button></div><div class="video-url-cls"><p>Type the URL of your Youtube Video, supports URLs of videos in websites only Youtube.</p><input class="video_input" style="width:100%;" type="url" class="nickx_video_text_url" value="' . esc_url( $product_video_url ) . '" name="nickx_video_text_url[]" Placeholder="https://www.youtube.com/embed/....."></div></div>';
		} else {
			$this->nickx_meta_extend_call( get_the_ID() );
		}
	}
	public function save_wc_video_url_field( $post_id ) {
		$nonce_name   = isset( $_POST['nickx_video_url_nonce'] ) ? $_POST['nickx_video_url_nonce'] : '';
		$nonce_action = 'nickx_video_url_nonce_action';
		if ( ! wp_verify_nonce( $nonce_name, $nonce_action ) ) {
			return;
		}
		if ( isset( $_POST['nickx_video_text_url'] ) ) {
			update_post_meta( $post_id, '_nickx_video_text_url', array_map( 'sanitize_url', $_POST['nickx_video_text_url'] ) );
		} else {
			delete_post_meta( $post_id, '_nickx_video_text_url' );
		}
		if ( isset( $_POST['nickx_product_video_type'] ) ) {
			update_post_meta( $post_id, '_nickx_product_video_type', array_map( 'sanitize_text_field', $_POST['nickx_product_video_type'] ) );
		} else {
			delete_post_meta( $post_id, '_nickx_product_video_type' );
		}
		if ( isset( $_POST['custom_thumbnail'] ) ) {
			update_post_meta( $post_id, '_custom_thumbnail', array_map( 'sanitize_text_field', $_POST['custom_thumbnail'] ) );
		} else {
			delete_post_meta( $post_id, '_custom_thumbnail' );
		}
		if ( isset( $_POST['product_video_thumb_url'] ) ) {
			update_post_meta( $post_id, '_nickx_product_video_thumb_ids', array_map( 'sanitize_text_field', $_POST['product_video_thumb_url'] ) );
		} else {
			delete_post_meta( $post_id, '_nickx_product_video_thumb_ids' );
		}
		if ( isset( $_POST['video_schema'] ) ) {
			update_post_meta( $post_id, '_video_schema', array_map( 'sanitize_text_field', $_POST['video_schema'] ) );
		} else {
			delete_post_meta( $post_id, '_video_schema' );
		}
		if ( isset( $_POST['nickx_video_upload_date'] ) ) {
			update_post_meta( $post_id, '_nickx_video_upload_date', array_map( 'sanitize_text_field', $_POST['nickx_video_upload_date'] ) );
		} else {
			delete_post_meta( $post_id, '_nickx_video_upload_date' );
		}
		if ( isset( $_POST['nickx_video_name'] ) ) {
			update_post_meta( $post_id, '_nickx_video_name', array_map( 'sanitize_text_field', $_POST['nickx_video_name'] ) );
		} else {
			delete_post_meta( $post_id, '_nickx_video_name' );
		}
		if ( isset( $_POST['nickx_video_description'] ) ) {
			update_post_meta( $post_id, '_nickx_video_description', array_map( 'sanitize_textarea_field', $_POST['nickx_video_description'] ) );
		} else {
			delete_post_meta( $post_id, '_nickx_video_description' );
		}
	}
	public function nickx_enqueue_scripts() {
		if ( ! is_admin() ) {
			if ( class_exists( 'WooCommerce' ) || is_product() || is_page_template( 'page-templates/template-products.php' ) ) {
				wp_enqueue_script( 'jquery' );
				if ( get_option( 'nickx_show_lightbox' ) == 'yes' ) {
					wp_enqueue_script( 'nickx-fancybox-js', plugins_url( 'js/jquery.fancybox.js', __FILE__ ), array( 'jquery' ), NICKX_PLUGIN_VERSION, true );
					wp_enqueue_style( 'nickx-fancybox-css', plugins_url( 'css/fancybox.css', __FILE__ ), '3.5.7', true );
				}
				if ( get_option( 'nickx_show_zoom' ) != 'off' ) {
					wp_enqueue_script( 'nickx-zoom-js', plugins_url( 'js/jquery.zoom.min.js', __FILE__ ), array( 'jquery' ), '1.7.4', true );
					wp_enqueue_script( 'nickx-elevatezoom-js', plugins_url( 'js/jquery.elevatezoom.min.js', __FILE__ ), array( 'jquery' ), '3.0.8', true );
				}
				wp_enqueue_style( 'nickx-fontawesome-css', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css', '1.0', true );
				wp_enqueue_style( 'nickx-front-css', plugins_url( 'css/nickx-front.css', __FILE__ ), NICKX_PLUGIN_VERSION, true );
				wp_register_script( 'nickx-front-js', plugins_url( 'js/nickx.front.js', __FILE__ ), array( 'jquery' ), NICKX_PLUGIN_VERSION, true );
				$video_type = get_post_meta( get_the_ID(), '_nickx_product_video_type', true );
				if( ( is_array( $video_type ) && in_array( 'nickx_video_url_vimeo', get_post_meta( get_the_ID(), '_nickx_product_video_type', true ) ) ) || get_post_meta( get_the_ID(), '_nickx_product_video_type', true ) == 'nickx_video_url_vimeo' ) {
					wp_enqueue_script( 'nickx-vimeo-js', 'https://player.vimeo.com/api/player.js', '1.0', true );
				}
				wp_enqueue_style( 'dashicons' );
				$options           = get_option( 'nickx_options' );
				$translation_array = array(
					'nickx_slider_layout'      => get_option( 'nickx_slider_layout' ),
					'nickx_slider_responsive'  => get_option( 'nickx_slider_responsive' ),
					'nickx_sliderautoplay'     => get_option( 'nickx_sliderautoplay' ),
					'nickx_sliderfade'         => get_option( 'nickx_sliderfade' ),
					'nickx_rtl'                => is_rtl(),
					'nickx_swipe'              => get_option( 'nickx_slider_swipe' ),
					'nickx_arrowinfinite'      => get_option( 'nickx_arrowinfinite' ),
					'nickx_arrowdisable'       => get_option( 'nickx_arrowdisable' ),
					'nickx_arrow_thumb'        => get_option( 'nickx_arrow_thumb' ),
					'nickx_hide_thumbnails'    => get_option( 'nickx_hide_thumbnails' ),
					'nickx_hide_thumbnail'     => get_option( 'nickx_hide_thumbnail' ),
					'nickx_adaptive_height'    => get_option( 'nickx_adaptive_height', 'yes' ),
					'nickx_thumbnails_to_show' => get_option( 'nickx_thumbnails_to_show', 4 ),
					'nickx_show_lightbox'      => get_option( 'nickx_show_lightbox' ),
					'nickx_show_zoom'          => get_option( 'nickx_show_zoom' ),
					'nickx_zoomlevel'          => get_option( 'nickx_zoomlevel', 1 ),
					'nickx_arrowcolor'         => get_option( 'nickx_arrowcolor' ),
					'nickx_arrowbgcolor'       => get_option( 'nickx_arrowbgcolor' ),
					'nickx_lic'                => $this->extend->is_nickx_act_lic(),
				);
				if ( $this->extend->is_nickx_act_lic() ) {
					$translation_array['nickx_place_of_the_video'] = get_option( 'nickx_place_of_the_video' );
					$translation_array['nickx_videoloop']          = get_option( 'nickx_videoloop' );
					$translation_array['nickx_vid_autoplay']       = get_option( 'nickx_vid_autoplay' );
				}
				wp_localize_script( 'nickx-front-js', 'wc_prd_vid_slider_setting', $translation_array );
				wp_enqueue_script( 'nickx-front-js' );
			}
		}
	}
}
function nickx_error_notice_callback_notice() {
	echo '<div class="error"><p><strong>Product Video Gallery for Woocommerce</strong> requires WooCommerce to be installed and active. You can download <a href="https://woocommerce.com/" target="_blank">WooCommerce</a> here.</p></div>';
}
add_action( 'plugins_loaded', 'nickx_remove_woo_hooks' );
function nickx_remove_woo_hooks() {
	if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
		require_once ABSPATH . '/wp-admin/includes/plugin.php';
	}
	if ( ( is_multisite() && is_plugin_active_for_network( 'woocommerce/woocommerce.php' ) ) || is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
		new WC_PRODUCT_VIDEO_GALLERY();
		remove_action( 'woocommerce_before_single_product_summary_product_images', 'woocommerce_show_product_thumbnails', 20 );
		remove_action( 'woocommerce_product_thumbnails', 'woocommerce_show_product_thumbnails', 20 );
		if ( get_option( 'nickx_hide_thumbnails' ) != 'yes' ) {
			add_action( 'woocommerce_product_thumbnails', 'nickx_show_product_thumbnails', 20 );
		}
		if ( get_option( 'nickx_gallery_action' ) != 'yes' ) {
			remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 10 );
			remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
			add_action( 'woocommerce_before_single_product_summary', 'nickx_show_product_image', 10 );
		}
		add_action('wp_head','nickx_get_nickx_video_schema');
	} else {
		add_action( 'admin_notices', 'nickx_error_notice_callback_notice' );
	}
}
function nickx_get_nickx_video_schema()
{
	if( is_product() ){
		$product_id = get_the_ID();
		$product_video_types = get_post_meta( $product_id, '_nickx_product_video_type', true );
		$product_video_urls  = get_post_meta( $product_id, '_nickx_video_text_url', true ); 
		$video_thumb_ids     = get_post_meta( $product_id, '_nickx_product_video_thumb_ids', true );
		$custom_thumbnails   = get_post_meta( $product_id, '_custom_thumbnail', true );
		$product_video_urls  = get_post_meta( $product_id, '_nickx_video_text_url', true );
		$video_schemas       = get_post_meta( $product_id, '_video_schema', true );
		$video_upload_dates  = get_post_meta( $product_id, '_nickx_video_upload_date', true );
		$video_names         = get_post_meta( $product_id, '_nickx_video_name', true );
		$video_descriptions  = get_post_meta( $product_id, '_nickx_video_description', true );
		if ( is_array($product_video_urls) ) {
			$extend = new NICKX_LIC_CLASS();
			foreach ($product_video_urls as $key => $product_video_url) {
				if( !empty( $product_video_url ) && isset($video_schemas[$key]) && $video_schemas[$key] == 'yes' && !empty( $video_names[$key] ) && !empty( $video_upload_dates[$key] ) && !empty( $video_descriptions[$key] ) ) {
					$product_video_type = $product_video_types[$key];
					$product_video_thumb_url = wc_placeholder_img_src();
					if ( ! empty( $video_thumb_ids[$key] ) ) {
						$product_video_thumb_url = wp_get_attachment_image_url( $video_thumb_ids[$key] );
					}
					if ( $product_video_type == 'nickx_video_url_youtube' ) {
						preg_match( '/^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/', $product_video_url, $matches );
						$product_video_url = 'https://www.youtube.com/embed/' . $matches[2] . '?rel=0';
					} 					
					echo '<script type="application/ld+json">
					{
					  "@context": "https://schema.org/",
					  "@type": "VideoObject",
					  "uploadDate": "' . $video_upload_dates[$key] . '",
					  "thumbnailUrl" : "' . $product_video_thumb_url . '",
					  "name": "' . $video_names[$key] . '",
					  "description" : "' . $video_descriptions[$key] . '",
					  "@id": "' . $product_video_url . '",
					  "embedUrl" : "' . $product_video_url . '"	  
					}
					</script>';
				}
				if(!$extend->is_nickx_act_lic()){
					break;
				}				
			}
		}
	}
}
function nickx_get_nickx_video_html( $product_video_url, $extend, $key = 1 )
{
	if ( strpos( $product_video_url, 'youtube' ) > 0 || strpos( $product_video_url, 'youtu' ) > 0 ) {
		return '<div class="tc_video_slide"><iframe id="nickx_yt_video_'.$key.'" style="display:none;" data-skip-lazy="true" width="100%" height="100%" class="product_video_iframe" video-type="youtube" data_src="' . esc_url( $product_video_url ) . '" src="" frameborder="0" allow="autoplay; accelerometer; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe><span class="product_video_iframe_light nickx-popup fa fa-expand fancybox-media" data-fancybox="product-gallery"></span></div>';
	} elseif ( strpos( $product_video_url, 'vimeo' ) > 0 && $extend->is_nickx_act_lic() ) {
		return '<div class="tc_video_slide"><iframe style="display:none;" data-skip-lazy="true" width="100%" height="450px" class="product_video_iframe" video-type="vimeo" src="' . esc_url( $product_video_url ) . '" frameborder="0" allow="autoplay; fullscreen" allowfullscreen=""></iframe><span href="' . esc_url( $product_video_url ) . '?enablejsapi=1&wmode=opaque" class="nickx-popup fa fa-expand fancybox-media" data-fancybox="product-gallery"></span></div>';
	} elseif ( strpos( $product_video_url, $_SERVER['SERVER_NAME'] ) > 0 && $extend->is_nickx_act_lic() ) {
		return '<div class="tc_video_slide"><video width="100%" height="100%" class="product_video_iframe" video-type="html5" ' . ( ( get_option( 'nickx_controls' ) == 'yes' ) ? 'controls' : '' ) . ' ' . ( ( get_option( 'nickx_vid_autoplay' ) == 'yes' && get_option( 'nickx_place_of_the_video' ) == 'yes' ) ? 'autoplay muted' : '' ) . ' playsinline><source src="' . esc_url( $product_video_url ) . '"><p>Your browser does not support HTML5</p></video><span href="' . esc_url( $product_video_url ) . '?enablejsapi=1&wmode=opaque" class="nickx-popup fa fa-expand fancybox-media" data-fancybox="product-gallery"></span></div>';
	} elseif ( $extend->is_nickx_act_lic() ) {
		return '<div class="tc_video_slide"><iframe style="display:none;" data-skip-lazy="true" width="100%" height="450px" class="product_video_iframe" video-type="iframe" src="' . esc_url( $product_video_url ) . '" frameborder="0" allow="autoplay; fullscreen" allowfullscreen=""></iframe></div>';
	} else {
		return '<div class="tc_video_slide"><iframe style="display:none;" data-skip-lazy="true" width="100%" height="100%" class="product_video_iframe" video-type="youtube" data_src="' . esc_url( $product_video_url ) . '" src="" frameborder="0" allow="autoplay; accelerometer; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>';
	}
}
function nickx_show_product_image($call_type = 'action') {
	global $post, $product, $woocommerce;
	if ( $call_type != 'action' || !$product->is_type( 'gift-card' ) ) {
		$show_thumb = 0;
		$product_video_urls = get_post_meta( get_the_ID(), '_nickx_video_text_url', true );
		$extend = new NICKX_LIC_CLASS();
		echo '<div class="images nickx_product_images_with_video loading '.(( get_option( 'nickx_show_lightbox' ) == 'yes' ) ? 'show_lightbox' : '').'">';
		if(wp_is_mobile()){
			echo '<span class="nickx-popup_trigger fa fa-expand"></span>';
		}
		echo '<div class="slider nickx-slider-for '.get_option( 'nickx_slider_responsive', 'no' ).'">';
		if ( has_post_thumbnail() || ! empty( $product_video_urls[0] ) ) {
			$attachment_ids    = ($product) ? $product->get_gallery_image_ids() : '';
			$imgfull_src       = get_the_post_thumbnail_url(get_the_ID(),'full');
			$htmlvideo         = '';
			if ( ! empty( $product_video_urls ) ) {
				if ( is_array($product_video_urls) ) {
					foreach ( $product_video_urls as $key => $product_video_url) {
						if( !empty( $product_video_url ) ) {
							$show_thumb++;
							$htmlvideo .= nickx_get_nickx_video_html($product_video_url,$extend,$key);
						}
						if(!$extend->is_nickx_act_lic()){
							break;
						}
					}
				}
				else{
					$show_thumb++;
					$htmlvideo .= nickx_get_nickx_video_html($product_video_urls,$extend);
				}
			}
			$product_image = get_the_post_thumbnail( $post->ID, 'woocommerce_single', array( 'data-skip-lazy' => 'true', 'data-zoom-image' => $imgfull_src ) );
			$html = '';
			if( get_option( 'nickx_show_only_video' ) == 'yes' && $extend->is_nickx_act_lic() ){
				$html .= $htmlvideo;
			} else {
				$html .= ( ( get_option( 'nickx_place_of_the_video' ) == 'yes' && $extend->is_nickx_act_lic() ) ? $htmlvideo : '' );
				if( !empty ( $product_image ) ){
					$show_thumb++;
					$html .= sprintf( '<div class="zoom woocommerce-product-gallery__image">%s<span href="%s" class="nickx-popup fa fa-expand" data-fancybox="product-gallery"></span></div>', $product_image, $imgfull_src );
				}
				$html .= ( ( get_option( 'nickx_place_of_the_video' ) == 'second' && $extend->is_nickx_act_lic() ) ? $htmlvideo : '' );
				foreach ( $attachment_ids as $attachment_id ) {
					$show_thumb++;
					$imgfull_src = wp_get_attachment_image_url( $attachment_id, 'full' );
					$html       .= '<div class="zoom">' . wp_get_attachment_image( $attachment_id, 'woocommerce_single', 0, array( 'data-skip-lazy' => 'true', 'data-zoom-image' => $imgfull_src ) ) . '<span href="' . esc_url( $imgfull_src ) . '" class="nickx-popup fa fa-expand" data-fancybox="product-gallery"></span></div>';
				}
				$html .= ( ( get_option( 'nickx_place_of_the_video' ) == 'no' && get_option( 'nickx_place_of_the_video' ) != 'yes' &&  get_option( 'nickx_place_of_the_video' ) != 'second' || ! $extend->is_nickx_act_lic() ) ? $htmlvideo : '' );
			}
			echo apply_filters( 'woocommerce_single_product_image_html', $html, $post->ID );
		} else {
			echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<div class="zoom woocommerce-product-gallery__image"><img class="attachment-woocommerce_single size-woocommerce_single wp-post-image" data-skip-lazy="true" src="%s" data-zoom-image="%s" alt="%s" /></div>', wc_placeholder_img_src(), wc_placeholder_img_src(), __( 'Placeholder', 'woocommerce' ) ), $post->ID );
		}
		echo '</div>';
		if( $show_thumb > 1 || get_option('nickx_hide_thumbnail') != 'yes' ){
			do_action( 'woocommerce_product_thumbnails' );
		}
		echo '</div>';
	} else {
		woocommerce_show_product_images();
	}
}
function nickx_get_video_thumbanil_html( $post, $thumbnail_size) {
	$gallery_thumbnail_size = wc_get_image_size( $thumbnail_size );
	$product_video_urls = get_post_meta( get_the_ID(), '_nickx_video_text_url', true );
	$wc_placeholder_img = wc_placeholder_img_src();
	if ( ! empty( $product_video_urls ) ) {
		$product_video_thumb_ids  = get_post_meta( get_the_ID(), '_nickx_product_video_thumb_ids', true );
		$custom_thumbnails        = get_post_meta( get_the_ID(), '_custom_thumbnail', true );
		if ( is_array($product_video_urls) ) {
			$extend = new NICKX_LIC_CLASS();
			foreach ($product_video_urls as $key => $product_video_url) {
				if( !empty( $product_video_url ) ) {
					$product_video_thumb_id   = isset($product_video_thumb_ids[$key]) ? $product_video_thumb_ids[$key] : '';
					$custom_thumbnail        = isset($custom_thumbnails[$key]) && !empty($product_video_thumb_id) ? 'custom_thumbnail="'.$custom_thumbnails[$key].'"' : '';
					$product_video_thumb_url = $wc_placeholder_img;
					$global_thumb = '';
					if ( $product_video_thumb_id ) {
						$product_video_thumb_url = wp_get_attachment_image_url( $product_video_thumb_id, $thumbnail_size );
					} elseif ($custom_icon = get_option( 'custom_icon' ) ) {
						$custom_thumbnail        = 'custom_thumbnail="yes"';
						if(is_numeric($custom_icon)){
							$product_video_thumb_url = wp_get_attachment_image_url( get_option( 'custom_icon' ), $thumbnail_size );
						} else {
							$product_video_thumb_url = $custom_icon;
						}
						$global_thumb = 'global-thumb="' . esc_url( $product_video_thumb_url ).'"';
					}
					echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', '<li title="video" class="video-thumbnail"><div class="video_icon_img" style="background: url( ' . plugins_url( 'css/mejs-controls.svg', __FILE__ ) . ' ) no-repeat;"></div><img width="' . $gallery_thumbnail_size['width'] . '" height="' . $gallery_thumbnail_size['height'] . '" data-skip-lazy="true" ' . $global_thumb . ' src="' . esc_url( $product_video_thumb_url ) . '" ' . $custom_thumbnail . ' class="product_video_img img_'.$key.' attachment-thumbnail size-thumbnail" alt="video-thumb-'.$key.'"></li>', '', $post->ID );
					if(!$extend->is_nickx_act_lic()){
						break;
					}
				}
			}
		}
		else{
			$product_video_thumb_urls = $wc_placeholder_img;
			$global_thumb = '';
			if ( $product_video_thumb_ids ) {
				$product_video_thumb_urls = wp_get_attachment_image_url( $product_video_thumb_ids, $thumbnail_size );
			} elseif ($custom_icon = get_option( 'custom_icon' ) ) {
				$custom_thumbnails        = 'custom_thumbnail="yes"';
				if(is_numeric($custom_icon)){
					$product_video_thumb_url = wp_get_attachment_image_url( get_option( 'custom_icon' ), $thumbnail_size );
				} else {
					$product_video_thumb_url = $custom_icon;
				}
				$global_thumb = 'global-thumb=" ' . esc_url( $product_video_thumb_urls ).' "';
			}
			echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', '<li title="video" class="video-thumbnail"><div class="video_icon_img" style="background: url( ' . plugins_url( 'css/mejs-controls.svg', __FILE__ ) . ' ) no-repeat;"></div><img width="' . $gallery_thumbnail_size['width'] . '" height="' . $gallery_thumbnail_size['height'] . '" data-skip-lazy="true" ' . $global_thumb . ' src="' . esc_url( $product_video_thumb_urls ) . '" ' . $custom_thumbnails . ' class="product_video_img img_0 attachment-thumbnail size-thumbnail" alt="video-thumb-0"></li>', '', $post->ID );
		}
	} else {
		return;
	}
}
function nickx_show_product_thumbnails() {
	global $post, $product, $woocommerce;
	if (empty($product->get_type()) || !$product->is_type( 'gift-card' ) ) {
		$extend         = new NICKX_LIC_CLASS();
		$attachment_ids = $product->get_gallery_image_ids();
		if ( has_post_thumbnail() ) {
			$thumbanil_id   = array( get_post_thumbnail_id() );
			$attachment_ids = array_merge( $thumbanil_id, $attachment_ids );
		}
		$thumbnail_size    = apply_filters( 'woocommerce_gallery_thumbnail_size', 'woocommerce_gallery_thumbnail' );
		if ( ( $attachment_ids && $product->get_image_id() ) || ! empty( get_post_meta( get_the_ID(), '_nickx_video_text_url', true ) ) ) {
			echo '<div id="nickx-gallery" class="slider nickx-slider-nav">';
			if( ( get_option( 'nickx_show_only_video' ) == 'yes' && $extend->is_nickx_act_lic() ) || empty( $attachment_ids )){
				nickx_get_video_thumbanil_html( $post, $thumbnail_size );
			} else {
				if ( ( get_option( 'nickx_place_of_the_video' ) == 'yes' || empty( $thumbanil_id[0] ) ) && $extend->is_nickx_act_lic() ) {
					nickx_get_video_thumbanil_html( $post, $thumbnail_size );
				}
				foreach ( $attachment_ids as $attachment_id ) {
					$props = wc_get_product_attachment_props( $attachment_id, $post );
					if ( ! $props['url'] ) {
						continue;
					}
					echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', sprintf( '<li class="product_thumbnail_item ' . ( ( !empty( $thumbanil_id[0] ) && $thumbanil_id[0] == $attachment_id ) ? 'wp-post-image-thumb' : '' ) . '" title="%s">%s</li>', esc_attr( $props['caption'] ), wp_get_attachment_image( $attachment_id, $thumbnail_size, 0, array( 'data-skip-lazy' => 'true' ) ) ), $attachment_id );
					if ( !empty( $thumbanil_id[0] ) && $thumbanil_id[0] == $attachment_id && get_option( 'nickx_place_of_the_video' ) == 'second' && $extend->is_nickx_act_lic() ) {
						nickx_get_video_thumbanil_html( $post, $thumbnail_size );
					}
				}
				if ( get_option( 'nickx_place_of_the_video' ) == 'no' && get_option( 'nickx_place_of_the_video' ) != 'yes' && get_option( 'nickx_place_of_the_video' ) != 'second' || ! $extend->is_nickx_act_lic() ) {
					nickx_get_video_thumbanil_html( $post, $thumbnail_size );
				}
			}
			echo '</div>';
		}
	} else {
		woocommerce_show_product_thumbnails();
	}
}

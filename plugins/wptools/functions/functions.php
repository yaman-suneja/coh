<?php
if (!defined('ABSPATH'))
	exit; // Exit if accessed directly
require_once(WPTOOLSPATH . "functions/functions_transiente_manager.php");
require_once(WPTOOLSPATH . "functions/functions_cron_manager.php");
if (!defined('WP_ALLOW_REPAIR')) {
	define('WP_ALLOW_REPAIR', true);
}
function wptools_options_go_settings(){
	$url = WPTOOLSHOMEURL.'admin.php?page=settings-wptools';
	 //wp_redirect( $url );
	//echo '<script>';
	//echo 'window.location.replace("'.$url.'");';
	//echo '</script>';
}

function wptools_menu()
{
	global $wptools_checkversion;
	add_menu_page(
		'WP Tools', 
		'WP Tools', 
		'manage_options', 
		'wp-tools', // slug 
		'wptools_options_dashboard', 
		 WPTOOLSIMAGES.'/tools.png' , 
		'100' 
	);
	
	// 		esc_attr__('Dashboard', "wptools"), // string $menu_title	
	
	add_submenu_page(
		'wp-tools', // $parent_slug
		'Dashboard', // string $page_title
		false,
		'manage_options', // string $capability
		'wptools_options31', // menu slug
		'wptools_options_dashboard', // callable function
		1 // position
	);
	
	add_submenu_page(
		'wp-tools', // $parent_slug
		'Settings', // string $page_title
		 esc_attr__("Settings", "wptools"), // string $menu_title
		'manage_options', // string $capability
		'settings-wptools', // menu slug
		'wptools_options_go_settings', // callable function
		1 // position
	);
	

	add_submenu_page(
		'wp-tools', // $parent_slug
		'Check mySQL Tables', // string $page_title
		esc_attr__("Check mySQL Tables", "wptools"), // string $menu_title
		'manage_options', // string $capability
		'wptools_options23', // menu slug
		'wptools_options_check_table', // callable function
		2 // position
	);
	add_submenu_page(
		'wp-tools', // $parent_slug
		'Edit robots txt', // string $page_title
		esc_attr__('Edit robots txt', "wptools"), // string $menu_title
		'manage_options', // string $capability
		'wptools_options24', // menu slug
		'wptools_options_robots',  // callable function
		4 // position
	);
	add_submenu_page(
		'wp-tools', // $parent_slug
		'Show PHP Errors', // string $page_title
		esc_attr__('Show PHP Errors', "wptools"), // string $menu_title
		'manage_options', // string $capability
		'wptools_options21', // menu slug
		'wptools_options', // callable function
		3 // position
	);
	add_submenu_page(
		'wp-tools', // $parent_slug
		'Show PHP Info', // string $page_title
		esc_attr__('Show PHP Info', "wptools"), // string $menu_title
		'manage_options', // string $capability
		'wptools_options22', // menu slug
		'wptools_options_php_info', // callable function
		3 // position
	);
	add_submenu_page(
		'wp-tools', // $parent_slug
		'Show .htaccess', // string $page_title
		esc_attr__('Show .htaccess', "wptools"), // string $menu_title
		'manage_options', // string $capability
		'wptools_options25', // menu slug
		'wptools_options_htaccess', // callable function
		3 // position
	);
	add_submenu_page(
		'wp-tools', // $parent_slug
		'Show wp-config.php', // string $page_title
		esc_attr__('Show wp-config.php', "wptools"), // string $menu_title	
		'manage_options', // string $capability
		'wptools_options26', // menu slug
		'wptools_options_wp_config', // callable function
		3 // position
	);
	add_submenu_page(
		'wp-tools', // $parent_slug
		'Show Cron Jobs', // string $page_title
		esc_attr__('Show Cron Jobs', "wptools"), // string $menu_title	
		'manage_options', // string $capability
		'wptools_options27', // menu slug
		'wptools_options_wpcron', // callable function
		3 // position
	);
	add_submenu_page(
		'wp-tools', // $parent_slug
		'Show File Permissions', // string $page_title
		esc_attr__('Show File Permissions', "wptools"), // string $menu_title	
		'manage_options', // string $capability
		'wptools_options29', // menu slug
		'wptools_options_permissions', // callable function
		3 // position
	);
	add_submenu_page(
		'wp-tools', // $parent_slug
		'Show Cookies', // string $page_title
		esc_attr__('Show Cookies', "wptools"), // string $menu_title	
		'manage_options', // string $capability
		'wptools_options28', // menu slug
		'wptools_options_cookies', // callable function
		3 // position
	);
	add_submenu_page(
		'wp-tools', // $parent_slug
		'Server Benchmark', // string $page_title
		esc_attr__('Server Benchmark', "wptools"), // string $menu_title	
		'manage_options', // string $capability
		'wptools_options30', // menu slug
		'wptools_options_benchmark', // callable function
		3 // position
	);
	add_submenu_page(
		'wp-tools', // $parent_slug
		'Database Detais', // string $page_title
		esc_attr__('Database Detais', "wptools"), // string $menu_title	
		'manage_options', // string $capability
		'wptools_options32', // menu slug
		'wptools_sql_details', // callable function
		3 // position
	);
	add_submenu_page(
		'wp-tools', // $parent_slug
		'Show Transients', // string $page_title
		esc_attr__('Show Transients', "wptools"), // string $menu_title	
		'manage_options', // string $capability
		'wptools_options33', // menu slug
		'wptools_transients_admin', // callable function
		30 // position
	);
	add_submenu_page(
		'wp-tools', // $parent_slug
		'Javascript and jQuery', // string $page_title
		esc_attr__('Javascrip and jQuery', "wptools"), // string $menu_title	
		'manage_options', // string $capability
		'wptools_options35', // menu slug
		'wptools_javacript', // callable function
		5 // position
	);
	if (is_multisite()) {
		add_submenu_page(
			'wp-tools', // $parent_slug
			'More Tools Same Author', // string $page_title
			esc_attr__('More Tools Same Author', "wptools"), // string $menu_title
			'manage_options', // string $capability
			'wptools_options34', // menu slug
			'wptools_more_plugins', // callable function
			32 // position
		);
	} else {
		add_submenu_page(
			'wp-tools', // $parent_slug
			'More New Tools', // string $page_title
			esc_attr__('More New Tools', "wptools"), // string $menu_title
			'manage_options', // string $capability
			'wptools_options39', // menu slug
			'wptools_new_more_plugins', // callable function
			33 // position
		);
	}
	if (empty($wptools_checkversion)) {
        add_submenu_page(
			'wp-tools', // $parent_slug
            'Go Pro', // string $page_title
            '<font color="#FF6600">'.esc_attr__('Go Pro', "wptools").'</font>', // string $menu_title
            'manage_options', // string $capability
            'wptools_my-custom-submenu-page9',
            'wptools_gopro_callback9',
            99
        );
    }
}
/* =============================== */

function wptools_load_upsell()
{
	Global $wptools_checkversion;
	wp_enqueue_style('wptools-more2', WPTOOLSURL . 'includes/more/more2.css');
	wp_register_script('wptools-more2-js', WPTOOLSURL . 'includes/more/more2.js', array('jquery'));
	wp_enqueue_script('wptools-more2-js');

	if(!empty($wptools_checkversion ))
	  return;

	if(isset($_COOKIE["wpt_dismiss"])) {

		$today = time();
		if (!update_option('bill_go_pro_hide', $today))
			add_option('bill_go_pro_hide', $today);
	  }


	$wptools_bill_go_pro_hide = trim(get_option('bill_go_pro_hide',''));

	// $wptools_bill_go_pro_hide = '';

    // $wptools_bill_go_pro_hide = '';
    if(empty($wptools_bill_go_pro_hide)) {
        $wtime = strtotime('-05 days');
        update_option('bill_go_pro_hide', $wtime);
        $wptools_bill_go_pro_hide =  $wtime;
    }
    $now = time();
    $delta = $now - $wptools_bill_go_pro_hide;
	// debug
	// 
    // $delta = time();
    if ($delta > (3600 * 24 * 6)) {

	   $list = 'enqueued';
	   if( !wp_script_is( 'bill-css-vendor-fix', $list ) ) {
		require_once(WPTOOLSPATH . 'includes/vendor/vendor.php');
		wp_enqueue_style('bill-css-vendor-fix', WPTOOLSURL . 'includes/vendor/vendor_fix.css');
		wp_register_script("bill-js-vendor", WPTOOLSURL . 'includes/vendor/vendor.js', array('jquery'), WPTOOLSVERSION, true);
		wp_enqueue_script('bill-js-vendor');
	   }
    }
	wp_register_script("bill-js-vendor-sidebar", WPTOOLSURL . 'includes/vendor/vendor-sidebar.js', array('jquery'), WPTOOLSVERSION, true);
	wp_enqueue_script('bill-js-vendor-sidebar');
		wp_enqueue_style('bill-css-vendor-wpt', WPTOOLSURL . 'includes/vendor/vendor.css');
		// var_dump(__LINE__);
}

function wptools_new_more_plugins()
{
	wptools_show_logo();
	$plugins_to_install = array();
	$plugins_to_install[0]["Name"] = "Anti Hacker Plugin";
	$plugins_to_install[0]["Description"] = "Firewall, Scanner, Login Protect, block user enumeration and TOR, disable Json WordPress Rest API, xml-rpc (xmlrpc) & Pingback and more security tools...";
	$plugins_to_install[0]["image"] = "https://ps.w.org/antihacker/assets/icon-256x256.gif?rev=2524575";
	$plugins_to_install[0]["slug"] = "antihacker";
	$plugins_to_install[1]["Name"] = "Stop Bad Bots";
	$plugins_to_install[1]["Description"] = "Stop Bad Bots, Block SPAM bots, Crawlers and spiders also from botnets. Save bandwidth, avoid server overload and content steal. Blocks also by IP.";
	$plugins_to_install[1]["image"] = "https://ps.w.org/stopbadbots/assets/icon-256x256.gif?rev=2524815";
	$plugins_to_install[1]["slug"] = "stopbadbots";
	$plugins_to_install[2]["Name"] = "WP Tools";
	$plugins_to_install[2]["Description"] = "More than 35 useful tools! It is a swiss army knife, to take your site to the next level.";
	$plugins_to_install[2]["image"] = "https://ps.w.org/wptools/assets/icon-256x256.gif?rev=2526088";
	$plugins_to_install[2]["slug"] = "wptools";
	$plugins_to_install[3]["Name"] = "reCAPTCHA For All";
	$plugins_to_install[3]["Description"] = "Protect ALL Pages of your site against bots (spam, hackers, fake users and other types of automated abuse)
	with invisible reCaptcha V3 (Google). You can also block visitors from China.";
	$plugins_to_install[3]["image"] = "https://ps.w.org/recaptcha-for-all/assets/icon-256x256.gif?rev=2544899";
	$plugins_to_install[3]["slug"] = "recaptcha-for-all";
	$plugins_to_install[4]["Name"] = "WP Memory";
	$plugins_to_install[4]["Description"] = "Check High Memory Usage, Memory Limit, PHP Memory, show result in Site Health Page and fix php low memory limit.";
	$plugins_to_install[4]["image"] = "https://ps.w.org/wp-memory/assets/icon-256x256.gif?rev=2525936";
	$plugins_to_install[4]["slug"] = "wp-memory";

	/*
	$plugins_to_install[5]["Name"] = "Truth Social";
	$plugins_to_install[5]["Description"] = "Tools and feeds for Truth Social new social media platform and Twitter.";
	$plugins_to_install[5]["image"] = "https://ps.w.org/toolstruthsocial/assets/icon-256x256.png?rev=2629666";
	$plugins_to_install[5]["slug"] = "toolstruthsocial";
	*/
	$plugins_to_install[5]["Name"] = "Database Backup";
	$plugins_to_install[5]["Description"] = "Database Backup with just one click.";
	$plugins_to_install[5]["image"] = "https://ps.w.org/database-backup/assets/icon-256x256.gif?rev=2862571";
	$plugins_to_install[5]["slug"] = "database-backup";

	$plugins_to_install[6]["Name"] = "Database Restore Bigdump";
	$plugins_to_install[6]["Description"] = "Database Restore with BigDump script.";
	$plugins_to_install[6]["image"] = "https://ps.w.org/bigdump-restore/assets/icon-256x256.gif?rev=2872393";
	$plugins_to_install[6]["slug"] = "bigdump-restore";


	$plugins_to_install[7]["Name"] = "Easy Update URLs";
	$plugins_to_install[7]["Description"] = "Fix your URLs at database after cloning or moving sites.";
	$plugins_to_install[7]["image"] = "https://ps.w.org/easy-update-urls/assets/icon-256x256.gif?rev=2866408";
	$plugins_to_install[7]["slug"] = "easy-update-urls";

	$plugins_to_install[8]["Name"] = "S3 Cloud Contabo";
	$plugins_to_install[8]["Description"] = "Connect you with your Contabo S3-compatible Object Storage.";
	$plugins_to_install[8]["image"] = "https://ps.w.org/s3cloud/assets/icon-256x256.gif?rev=2855916";
	$plugins_to_install[8]["slug"] = "s3cloud";

	$plugins_to_install[9]["Name"] = "Tools for S3 AWS Amazon";
	$plugins_to_install[9]["Description"] = "Connect you with your Amazon S3-compatible Object Storage.";
	$plugins_to_install[9]["image"] = "https://ps.w.org/toolsfors3/assets/icon-256x256.gif?rev=2862487";
	$plugins_to_install[9]["slug"] =  "toolsfors3";



?>
	<div style="padding-right:20px;">
		<br>
		<h1><?php esc_attr("Useful FREE Plugins of the same author.","wptools");?></h1>
		<div id="bill-wrap-install" class="bill-wrap-install" style="display:none">
			<h3><?php esc_attr_e("Please wait","wptools");?></h3>
			<big>
				<h4>
				<?php esc_attr_e("Installing plugin","wptools");?> <div id="billpluginslug">...</div>
				</h4>
			</big>
			<img src="/wp-admin/images/wpspin_light-2x.gif" id="billimagewaitfbl" style="display:none;margin-left:0px;margin-top:0px;" />
			<br />
		</div>
		<table style="margin-right:20px; border-spacing: 0 25px; " class="widefat" cellspacing="0" id="wptools-more-plugins-table">
			<tbody class="wptools-more-plugins-body">
				<?php
				$counter = 0;
				$total = count($plugins_to_install);
				for ($i = 0; $i < $total; $i++) {
					if ($counter % 2 == 0) {
						echo '<tr style="background:#f6f6f1;">';
					}
					++$counter;
					if ($counter % 2 == 1)
						echo '<td style="max-width:140px; max-height:140px; padding-left: 40px;" >';
					else
						echo '<td style="max-width:140px; max-height:140px;" >';
					echo '<img style="width:100px;" src="' . esc_url($plugins_to_install[$i]["image"]) . '">';
					echo '</td>';
					echo '<td style="width:40%;">';
					echo '<h3>' . esc_attr($plugins_to_install[$i]["Name"]) . '</h3>';
					echo esc_attr($plugins_to_install[$i]["Description"]);
					echo '<br>';
					echo '</td>';
					echo '<td style="max-width:140px; max-height:140px;" >';
					if (wptools_plugin_installed($plugins_to_install[$i]["slug"]))
						echo '<a href="#" class="button activate-now">'. esc_attr__('Installed', 'wptools').'</a>';
					else
						echo '<a href="#" id="' . esc_attr($plugins_to_install[$i]["slug"]) . '"class="button button-primary wt-bill-install-now">'. esc_attr__('Install', 'wptools').'</a>';
					echo '</td>';
					if ($counter % 2 == 1) {
						echo '<td style="width; 100px; border-left: 1px solid gray;">';
						echo '</td>';
					}
					if ($counter % 2 == 0) {
						echo '</tr>';
					}
				}
				?>
			</tbody>
		</table>

		<!-- Bill-11 -->
		<?php echo '<div id="wptools_nonce" style="display:none;" >'. wp_create_nonce('wptools_install_plugin'); ?>



	</div>


	<center>
	<a href="https://profiles.wordpress.org/sminozzi/#content-plugins" class="button button-primary">
	<?php esc_attr_e( 'More Plugins', 'wptools' ); ?>
	</a>
	</center>


<?php
}

if (!function_exists('wp_get_current_user')) {
	require_once(ABSPATH . "wp-includes/pluggable.php");
}
if (is_admin() or is_super_admin()) {
	add_action('admin_enqueue_scripts', 'wptools_load_upsell');
	add_action('wp_ajax_wptools_install_plugin', 'wptools_install_plugin');
}

function wptools_install_plugin()
{

	if (isset($_POST['slug'])) {
		$slug = sanitize_text_field($_POST['slug']);
	} else {
		echo 'Fail error (-5)';
		wp_die();
	}

	if (isset($_POST['nonce'])) {
        $nonce = sanitize_text_field($_POST['nonce']);
        if ( ! wp_verify_nonce( $nonce, 'wptools_install_plugin' ) ) 
             die('Bad Nonce');
    }
    else
       wp_die('nonce not set');
	   
    if ($slug != "database-backup" &&  $slug != "bigdump-restore" &&  $slug != "easy-update-urls" &&  $slug != "s3cloud" &&  $slug != "toolsfors3" && $slug != "antihacker" && $slug != "toolstruthsocial" && $slug != "stopbadbots" && $slug != "wptools" && $slug != "recaptcha-for-all" && $slug != "wp-memory") {
       wp_die('wrong slug');
    }


	$plugin['source'] = 'repo'; // $_GET['plugin_source']; // Plugin source.
	require_once ABSPATH . 'wp-admin/includes/plugin-install.php'; // Need for plugins_api.
	require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php'; // Need for upgrade classes.
	// get plugin information
	$api = plugins_api('plugin_information', array('slug' => $slug, 'fields' => array('sections' => false)));
	if (is_wp_error($api)) {
		echo 'Fail error (-1)';
		wp_die();
		// proceed
	} else {
		// Set plugin source to WordPress API link if available.
		if (isset($api->download_link)) {
			$plugin['source'] = $api->download_link;
			$source =  $api->download_link;
		} else {
			echo 'Fail error (-2)';
			wp_die();
		}
		$nonce = 'install-plugin_' . $api->slug;
		/*
        $type = 'web';
        $url = $source;
        $title = 'wptools';
        */
		$plugin = $slug;
		// verbose...
		//    $upgrader = new Plugin_Upgrader($skin = new Plugin_Installer_Skin(compact('type', 'title', 'url', 'nonce', 'plugin', 'api')));
		class wptools_QuietSkin extends \WP_Upgrader_Skin
		{
			public function feedback($string, ...$args)
			{ /* no output */
			}
			public function header()
			{ /* no output */
			}
			public function footer()
			{ /* no output */
			}
		}
		$skin = new wptools_QuietSkin(array('api' => $api));
		$upgrader = new Plugin_Upgrader($skin);
		// var_dump($upgrader);
		try {
			$upgrader->install($source);
			//	get all plugins
			$all_plugins = get_plugins();
			// scan existing plugins
			foreach ($all_plugins as $key => $value) {
				// get full path to plugin MAIN file
				// folder and filename
				$plugin_file = $key;
				$slash_position = strpos($plugin_file, '/');
				$folder = substr($plugin_file, 0, $slash_position);
				// match FOLDER against SLUG
				// if matched then ACTIVATE it
				if ($slug == $folder) {
					// Activate
					$result = activate_plugin(ABSPATH . 'wp-content/plugins/' . $plugin_file);
					if (is_wp_error($result)) {
						// Process Error
						echo 'Fail error (-3)';
						wp_die();
					}
				} // if matched
			}
		} catch (Exception $e) {
			echo 'Fail error (-4)';
			wp_die();
		}
	} // activation
	echo 'OK';
	wp_die();
}

function wptools_plugin_installed($slug)
{
	$all_plugins = get_plugins();
	foreach ($all_plugins as $key => $value) {
		$plugin_file = $key;
		$slash_position = strpos($plugin_file, '/');
		$folder = substr($plugin_file, 0, $slash_position);
		// match FOLDER against SLUG
		if ($slug == $folder) {
			return true;
		}
	}
	return false;
}

function wptools_bill_go_pro_hide()
{
    // $today = date('Ymd', strtotime('+06 days'));
    $today = time();
    if (!update_option('bill_go_pro_hide', $today))
        add_option('bill_go_pro_hide', $today);
    wp_die();
}
if (!function_exists('wp_get_current_user')) {
	require_once(ABSPATH . "wp-includes/pluggable.php");
}

/* =========================== */
function wptools_javacript()
{
	wptools_show_logo();
?>
	<h1><?php echo esc_attr('Javascript', 'wptools'); ?></h1>
	<br>
	<table class="wptools_admin_table">
		<tr>
			<td><?php echo esc_attr__('jQuery Version', 'wptools'); ?></td>
			<td>
				<div id='jquery_version'></div>
			</td>
		</tr>
		<tr>
			<td><?php echo esc_attr__('jQuery Migrate Version', 'wptools'); ?></td>
			<td>
				<div id='jquery_migrate_version'></div>
			</td>
		</tr>
	</table>
	<script>
		var jqversion = jQuery.fn.jquery
		var jqmversion = jQuery.migrateVersion;
		document.getElementById("jquery_version").innerHTML = jqversion;
		document.getElementById("jquery_migrate_version").innerHTML = jqmversion;
	</script>
	<hr />
	<?php
	echo '<a href="https://wptoolsplugin.com/remove-jquery-migrate/" >';
	echo esc_attr(__("Visit plugin's site for detais", 'wptools')) . '.';
	echo '</a>';
	?>
	<h2><?php echo esc_attr__('Last Javascript Errors (max 200)', 'wptools'); ?></h2>
	<?php
	$wptools_filename = ABSPATH . "error_log";
	$marray = wptools_read_file($wptools_filename, 3000);

	if (gettype($marray) != 'array' or count($marray) < 1) {

	// if (count($marray) < 1) {
		echo '<h3>';
		echo esc_attr__('No Javascript errors found last entries of log_error file.', 'wptools');
		echo '</h3>';
		return;
	}
	echo '<br />';

	echo '<a href="https://wptoolsplugin.com/site-language-error-can-crash-your-site/" >';
	echo esc_attr(__("Learn more about site code errors...", 'wptools'));
	echo '</a>';

	echo '<br />';

	echo '<br>';
	// echo '<div style="max-height:400px">';
	echo '<table class="widefat striped" style="max-width:98%;">';
	$wptoolsctd = 0;
	for ($i = 0; $i < count($marray); $i++) {
		if (stripos($marray[$i], 'javascript') !== false) {
			$wptoolsctd++;
			echo '<tr>';
			echo '<td>';


			$pos = strpos($marray[$i] , ']' );
			if ($pos !== false) {
				// Data
				echo 'Date: '.esc_attr(substr($marray[$i],0,$pos+1));
				echo '<br>';
				echo esc_attr(substr($marray[$i],$pos+1));

			}
			else
			 echo esc_attr(trim($marray[$i]));



			echo '</td>';
			echo '</tr>';
			if ($wptoolsctd > 199)
				break;
		}
	}
	?>
	</table>
   
	<?php
	if ($wptoolsctd < 1) {
		echo '<h3>';
		echo esc_attr(__('No Javascript errors found last 3000 entries of log_error file.', 'wptools'));
		echo '</h3>';
		return;
	}
}
function wptools_more_plugins()
{
	$url = WPTOOLSHOMEURL . "plugin-install.php?s=sminozzi&tab=search&type=author";
	?>
	<script>
		window.location.replace("<?php echo esc_url($url); ?>");
	</script>
<?php
}
function wptools_sql_details()
{
	global $wpdb;
	global $wptools_checkversion;
	wptools_show_logo();
	$wptools_show_dbtools = true;
	if (!defined('WP_ALLOW_REPAIR')) {
		define('WP_ALLOW_REPAIR', true);
	} else {
		if (WP_ALLOW_REPAIR == false) {
			echo '<br>';
			esc_attr_e("WP_ALLOW_REPAIR is defined as false on your wp-config.php","wptools");
			echo '<br>';
			esc_attr_e("That can disable WordPress Database Optimize and Repair Tool.","wptools");
			echo '<br>';
			esc_attr_e("Change that to true.","wptools");
			$wptools_show_dbtools = false;
		}
	}
	if ($wptools_show_dbtools) {
		echo '<br><big>';
		echo  esc_html(__('Open WordPress Optimize and Repair Database Tools', 'wptools')) . '&nbsp;&raquo;&nbsp;';
		if (!empty($wptools_checkversion)) {
			//echo '</big>';
			echo '<a target="_blank" class="button-primary"rel="noopener noreferrer" href="' . admin_url('maint/repair.php') . '">' . esc_html__('Open', 'wptools') . '</a>';
			echo '<br>(';
			echo esc_html(__("It is not necessary edit your wp-config.php file", "wptools"));
		} else
			echo esc_html(__("(Option available in Premium Version)", "wptools"));
		echo ')<br></big>';
	}
	$results = $wpdb->get_results("SHOW GLOBAL STATUS LIKE 'Uptime'");
	if (isset($results[0]->Value)) {
		$mysql_uptime = $results[0]->Value;
	}
	// get db name
	$results = $wpdb->get_results("SELECT DATABASE() as dbname");
	if (isset($results[0]->dbname)) {
		$mysql_dbname = $results[0]->dbname;
	}
?>
	<h1><?php echo esc_attr__('Database Information', 'wptools'); ?></h1>
	<hr />
	<h2><?php echo esc_attr__('Basic Information', 'wptools'); ?></h2>
	<table class="wptools_admin_table">
		<thead>
			<tr>
				<th><?php echo esc_attr__('Variable Name', 'wptools'); ?></th>
				<th><?php echo esc_attr__('Value', 'wptools'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td><?php echo esc_attr__('Variable Name', 'wptools'); ?></td>
				<td><?php echo esc_attr__('Value', 'wptools'); ?></td>
			</tr>
		</tfoot>
		<tbody>
			<tr>
				<td><?php echo esc_attr__('Database Software', 'wptools'); ?></td>
				<td><?php echo esc_attr(wptools_database_software()); ?></td>
			</tr>
			<tr>
				<td><?php echo esc_attr__('Database Version', 'wptools'); ?></td>
				<td><?php echo esc_attr(wptools_database_version()); ?></td>
			</tr>
			<tr>
				<td><?php echo esc_attr__('MySQL Uptime', 'wptools'); ?></td>
				<td>
					<?php
					if (isset($mysql_uptime)) {
						echo esc_attr(wptools_secondsToTime($mysql_uptime));
					} else {
						echo "-";
					}
					?>
				</td>
			</tr>
			<tr>
				<td><?php echo esc_attr__('Mysql Time', 'wptools'); ?></td>
				<td>
					<?php
					//if (ini_get('date.timezone')) 
					//{
					// echo 'date.timezone: ' . ini_get('date.timezone');
					// mysql –e “SELECT NOW();”
					echo esc_attr($wpdb->get_var("SELECT NOW()"));
					//}
					?>
				</td>
			</tr>

			<tr>
				<td><?php echo esc_attr__('Database Name', 'wptools'); ?></td>
				<td>
					<?php
					if (isset($mysql_dbname))
						echo esc_attr($mysql_dbname);
					?>
				</td>
			</tr>
			<tr>
				<td><?php echo esc_attr__('Database User', 'wptools'); ?></td>
				<td>
					<?php
					if (defined('DB_USER'))
						echo esc_attr(DB_USER);
					?>
				</td>
			</tr>
			<tr>
				<td><?php echo esc_attr__('Database Hosting', 'wptools'); ?></td>
				<td>
					<?php
					if (defined('DB_HOST'))
						echo esc_attr(DB_HOST);
					?>
				</td>
			</tr>
			<tr>
				<td><?php echo esc_attr__('Database Charset', 'wptools'); ?></td>
				<td>
					<?php
					if (defined('DB_CHARSET'))
						echo esc_attr(DB_CHARSET);
					?>
				</td>
			</tr>
			<tr>
				<td><?php echo esc_attr__('Table Prefix', 'wptools'); ?></td>
				<td>
					<?php
					global $table_prefix;
					if (isset($table_prefix))
						echo esc_attr($table_prefix);
					?>
				</td>
			</tr>



			<tr>
				<td><?php echo esc_attr__('Maximum No. of Connections', 'wptools'); ?></td>
				<td><?php echo esc_attr(database_max_no_connection()); ?></td>
			</tr>
			<tr>
				<td><?php echo esc_attr__('Maximum Packet Size', 'wptools'); ?></td>
				<td><?php echo esc_attr(wptools_database_max_packet_size()); ?></td>
			</tr>
			<tr>
				<td><?php echo esc_attr__('Database Disk Usage', 'wptools'); ?></td>
				<td><?php echo esc_attr(wptools_database_disk_usage()); ?></td>
			</tr>
			<tr>
				<td><?php echo esc_attr__('Index Disk Usage', 'wptools'); ?></td>
				<td><?php echo esc_attr(wptools_index_disk_usage()); ?></td>
			</tr>
		</tbody>
	</table>
	<br />
	<h2><?php echo esc_attr__('Advanced Information', 'wptools'); ?></h2>
	<table class="wptools_admin_table">
		<thead>
			<tr>
				<th><?php echo esc_attr__('Variable Name', 'wptools'); ?></th>
				<th><?php echo esc_attr__('Value', 'wptools'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td><?php echo esc_attr__('Variable Name', 'wptools'); ?></td>
				<td><?php echo esc_attr__('Value', 'wptools'); ?></td>
			</tr>
		</tfoot>
		<tbody>
			<?php
			global $wpdb;
			// $dbversion = $wpdb->get_var("SELECT VERSION() AS version");
			$dbinfo = $wpdb->get_results("SHOW VARIABLES");
			if (!empty($dbinfo)) {
				foreach ($dbinfo as $info) {
					echo '<tr><td >' . esc_attr($info->Variable_name) . '</td><td >' . esc_attr(htmlspecialchars($info->Value)) . '</td></tr>';
				}
			} else {
				echo '<tr><td>' . esc_attr(__('Something went wrong!', 'wptools')) . '</td><td>' . __('Something went wrong!', 'wptools') . '</td></tr>';
			}
			?>
		</tbody>
	</table>
<?php
}
function wptools_database_software()
{
	global $wpdb;
	$db_software_query = $wpdb->get_row("SHOW VARIABLES LIKE 'version_comment'");
	$db_software_dump = $db_software_query->Value;
	if (!empty($db_software_dump)) {
		$db_soft_array = explode(" ", trim($db_software_dump));
		$db_software = $db_soft_array[0];
	} else {
		$db_software = __('N/A', 'wptools');
	}
	return $db_software;
}
function wptools_database_version()
{
	global $wpdb;
	$db_version_dump = $wpdb->get_var("SELECT VERSION() AS version from DUAL");
	if (preg_match('/\d+(?:\.\d+)+/', $db_version_dump, $matches)) {
		$db_version = $matches[0];
	} else {
		$db_version = __('N/A', 'wptools');
	}
	return $db_version;
}
function database_max_no_connection()
{
	global $wpdb;
	$connection_max_query = $wpdb->get_row("SHOW VARIABLES LIKE 'max_connections'");
	$db_max_connection = $connection_max_query->Value;
	if (empty($db_max_connection)) {
		$db_max_connection = __('N/A', 'wptools');
	} else {
		$db_max_connection = number_format_i18n($db_max_connection, 0);
	}
	return $db_max_connection;
}
function wptools_database_max_packet_size()
{
	global $wpdb;
	$packet_max_query = $wpdb->get_row("SHOW VARIABLES LIKE 'max_allowed_packet'");
	$db_max_packet_size = $packet_max_query->Value;
	if (empty($db_max_packet_size)) {
		$db_max_packet_size = __('N/A', 'wptools');
	} else {
		$db_max_packet_size = wptools_format_filesize($db_max_packet_size);
	}
	return $db_max_packet_size;
}
function wptools_database_disk_usage()
{
	global $wpdb;
	$db_disk_usage = 0;
	$tablesstatus = $wpdb->get_results("SHOW TABLE STATUS");
	foreach ($tablesstatus as $tablestatus) {
		$db_disk_usage += $tablestatus->Data_length;
	}
	if (empty($db_disk_usage)) {
		$db_disk_usage = __('N/A', 'wptools');
	} else {
		$db_disk_usage = wptools_format_filesize($db_disk_usage);
	}
	return $db_disk_usage;
}
function wptools_index_disk_usage()
{
	global $wpdb;
	$db_wptools_index_disk_usage = 0;
	$tablesstatus = $wpdb->get_results("SHOW TABLE STATUS");
	foreach ($tablesstatus as $tablestatus) {
		$db_wptools_index_disk_usage += $tablestatus->Index_length;
	}
	if (empty($db_wptools_index_disk_usage)) {
		$db_wptools_index_disk_usage = __('N/A', 'wptools');
	} else {
		$db_wptools_index_disk_usage = wptools_format_filesize($db_wptools_index_disk_usage);
	}
	return $db_wptools_index_disk_usage;
}
function wptools_format_filesize($bytes)
{
	try {
		// if(gettype($bites))
		if (($bytes / pow(1024, 5)) > 1) {
			return number_format_i18n(($bytes / pow(1024, 5)), 0) . ' ' . __('PB', 'wptools');
		} elseif (($bytes / pow(1024, 4)) > 1) {
			return number_format_i18n(($bytes / pow(1024, 4)), 0) . ' ' . __('TB', 'wptools');
		} elseif (($bytes / pow(1024, 3)) > 1) {
			return number_format_i18n(($bytes / pow(1024, 3)), 0) . ' ' . __('GB', 'wptools');
		} elseif (($bytes / pow(1024, 2)) > 1) {
			return number_format_i18n(($bytes / pow(1024, 2)), 0) . ' ' . __('MB', 'wptools');
		} elseif ($bytes / 1024 > 1) {
			return number_format_i18n($bytes / 1024, 0) . ' ' . __('KB', 'wptools');
		} elseif ($bytes >= 0) {
			return number_format_i18n($bytes, 0) . ' ' . __('bytes', 'wptools');
		} else {
			return __('Unknown', 'wptools');
		}
	} catch (exception $e) {
		return '0';
	}
}
function wptools_format_filesize_kB($kiloBytes)
{
	if (($kiloBytes / pow(1024, 4)) > 1) {
		return number_format_i18n(($kiloBytes / pow(1024, 4)), 0) . ' ' . __('PB', 'wptools');
	} elseif (($kiloBytes / pow(1024, 3)) > 1) {
		return number_format_i18n(($kiloBytes / pow(1024, 3)), 0) . ' ' . __('TB', 'wptools');
	} elseif (($kiloBytes / pow(1024, 2)) > 1) {
		return number_format_i18n(($kiloBytes / pow(1024, 2)), 0) . ' ' . __('GB', 'wptools');
	} elseif (($kiloBytes / 1024) > 1) {
		return number_format_i18n($kiloBytes / 1024, 0) . ' ' . __('MB', 'wptools');
	} elseif ($kiloBytes >= 0) {
		return number_format_i18n($kiloBytes / 1, 0) . ' ' . __('KB', 'wptools');
	} else {
		return esc_attr__('Unknown', 'wptools');
	}
}
/*
function wptools_change_note_submenu_order($menu_ord)
{
	global $submenu;
	function wptools_str_replace_json($search, $replace, $subject)
	{
		return json_decode(str_replace($search, $replace, json_encode($subject)), true);
	}
	$key = 'WP Tools';
	$val = 'Settings';
	$submenu = wptools_str_replace_json($key, $val, $submenu);
}
*/
function wptools_check_memory()
{
	global $wptools_memory;
	try {
		$wptools_memory['limit'] = (int) ini_get('memory_limit');
		$wptools_memory['usage'] = function_exists('memory_get_usage') ? round(memory_get_usage() / 1024 / 1024, 0) : 0;
		if ($wptools_memory['usage'] == 0) {
			$wptools_memory['msg_type'] = 'notok';
			return;
		}
		if (!defined("WP_MEMORY_LIMIT")) {
			$wptools_memory['msg_type'] = 'notok';
			return;
		}
		$wptools_memory['wp_limit'] =  trim(WP_MEMORY_LIMIT);
		if ($wptools_memory['wp_limit'] > 9999999)
			$wptools_memory['wp_limit'] = ($wptools_memory['wp_limit'] / 1024) / 1024;
		if (!is_numeric($wptools_memory['usage'])) {
			$wptools_memory['msg_type'] = 'notok';
			return;
		}
		if (!is_numeric($wptools_memory['limit'])) {
			$wptools_memory['msg_type'] = 'notok';
			return;
		} else {
			if ($wptools_memory['limit'] > 9999999)
				$wptools_memory['limit']  = ($wptools_memory['limit'] / 1024) / 1024;
		}
		if ($wptools_memory['usage'] < 1) {
			$wptools_memory['msg_type'] = 'notok';
			return;
		}
		$wplimit = $wptools_memory['wp_limit'];
		$wplimit = substr($wplimit, 0, strlen($wplimit) - 1);
		$wptools_memory['wp_limit'] = $wplimit;
		$wptools_memory['percent'] = $wptools_memory['usage'] / $wptools_memory['wp_limit'];
		$wptools_memory['color'] = 'font-weight:normal;';
		if ($wptools_memory['percent'] > .7) $wptools_memory['color'] = 'font-weight:bold;color:#E66F00';
		if ($wptools_memory['percent'] > .85) $wptools_memory['color'] = 'font-weight:bold;color:red';
		$wptools_memory['msg_type'] = 'ok';
		return $wptools_memory;
	} catch (Exception $e) {
		$bill_install_memory['msg_type'] = 'notok(7)';
		return $bill_install_memory;
	}
}
function wptools_options_dashboard()
{
	wptools_show_logo();
	require_once WPTOOLSPATH . "dashboard/dashboard_container.php";
	return;
}
function wptools_options_benchmark()
{
	if (isset($_GET['page'])) {
		$page = sanitize_text_field($_GET['page']);
		if ($page != 'wptools_options30')
			return;
	}
	if (isset($_REQUEST['wptools_action']) and $_REQUEST['wptools_action'] ==  'wptools_update_performance_permissions') {
		if (isset($_REQUEST['wptools_exchange']) and $_REQUEST['wptools_exchange'] == 'yes')
			update_option('wptools_server_performance', 'yes');
		else
			update_option('wptools_server_performance', 'no');
	}
	$wptools_server_performance  = trim(sanitize_text_field(get_option('wptools_server_performance', 'no')));
	if ($wptools_server_performance == 'yes')
		$wptools_checkbox = 'checked';
	else
		$wptools_checkbox = '';
	wptools_show_logo();
	echo '<h1>' . esc_attr(__('Server Benchmark', 'wptools')) . '</h1>';
	echo '<div id="wptools_exchange" style="min-width:100%">';
	echo esc_attr(__("This plugin can benchmarks your server's performance through a variety of PHP and MySql tests.", 'wptools'));
	echo '<br>';
	echo esc_attr(__("The total time is in seconds. Lower time are better (faster).", 'wptools'));
	echo '<br>';
?>
	<br>
	<form method="post" class="alignleft" style="min-width:100%">&nbsp;
		<input type="hidden" name="wptools_action" value="wptools_update_performance_permissions" />
		<?php wp_nonce_field('performance_permissions'); ?>
		<input type="checkbox" id="wptools_exchange" name="wptools_exchange" value="yes" <?php echo esc_attr($wptools_checkbox); ?>>
		<label for="scales">
		    <?php esc_attr_e("Participate in Community Server Performance.","wptools");?>
			 <br>
			<?php esc_attr_e("Enabling this feature causes your site to share only server performance data with WP Tools Plugin.","wptools");?> <br>
			<?php esc_attr_e("In return your WordPress site receives Updated Aggregated Industry Average Data.","wptools");?> <br>
		</label>
		<input type="submit" class="button-primary" value="<?php echo esc_attr(__('Update', 'wptools')); ?>" />
		<br>
		<br>
	</form>
	<br>
	<br>
	<?php
	echo '</div>';
	echo '<a style="float:left;margin-botton:20px;" href="https://wptoolsplugin.com/benchmark-server-tool/">';
	 esc_attr_e("Learn more.", 'wptools');
	echo '</a>';
	echo '&nbsp;&nbsp;&nbsp;Please, wait...';
	print str_pad(' ',4096)."\n";
	flush();
    ob_end_flush();
	echo '<br><br>';
	require_once WPTOOLSPATH . "functions/functions_benchmark.php";
	$arr_cfg = array();
	// optional: mysql performance test
	$arr_cfg['db.host'] = DB_HOST;
	$arr_cfg['db.user'] = DB_USER;
	$arr_cfg['db.pw'] = DB_PASSWORD;
	$arr_cfg['db.name'] = DB_NAME;
	$showServerName = true;
	//$options = [];
	$benchmarkResult = wptools_test_benchmark($arr_cfg);



	echo '<div style="float:left">';
	echo wptools_print_html_result($benchmarkResult, $showServerName, 'System Info');
	echo '</div>';


	if ($wptools_server_performance == 'yes') {
		$r = get_transient('wptools_performance_share');
		if (!$r)
			wptools_performance_share($benchmarkResult);
		$r = get_transient('wptools_performance_share');
		$r = json_decode($r, true);
		$benchmarkIndustryResult = wptools_industry_benchmark($r);
	}



	if ($wptools_server_performance == 'yes' and gettype($benchmarkIndustryResult) != 'array')
		return;
	if ($wptools_server_performance == 'yes') {
		echo '<div style="float:left">';
		echo wptools_print_html_result($benchmarkIndustryResult, $showServerName, esc_attr__("Industry Average Data","wptools"));
		echo '</div>';
	}
}
function wptools_industry_benchmark($arr_cfg)
{
	//global $arr_cfg;
	$time_start = microtime(true);
	$arr_return = array();
	$arr_return['version'] = '1.1';
	$arr_return['sysinfo']['time'] = ''; // date("Y-m-d H:i:s");
	$arr_return['sysinfo']['php_version'] = '';
	$arr_return['sysinfo']['platform'] = '';
	$arr_return['sysinfo']['server_name'] = '';
	$arr_return['sysinfo']['server_addr'] = '';
	$arr_return['sysinfo']['mysql_version'] = '';
	// $arr_cfg['db.host'] = DB_HOST;

	
if(gettype($arr_cfg) != 'array')
   return $arr_return;

	if (isset($arr_cfg['db.host'])) {
		wptools_test_mysql($arr_return, $arr_cfg);
	}
	$arr_return['benchmark']['math'] =  $arr_cfg[0]["mymath"];
	$arr_return['benchmark']['string'] = $arr_cfg[0]["mystring"];

	if(isset($arr_cfg[0]["loops"]))
	  $arr_return['benchmark']['loops'] = $arr_cfg[0]["loops"];  
	else
   	$arr_return['benchmark']['loops'] = '';

	$arr_return['benchmark']['ifelse'] = $arr_cfg[0]["ifelse"];
	$arr_return['benchmark']['mysql_version'] = '';
	$arr_return['benchmark']['mysql_connect'] = $arr_cfg[0]["mysql_connect"];
	$arr_return['benchmark']['mysql_select_db'] = $arr_cfg[0]["mysql_select_db"];
	$arr_return['benchmark']['mysql_query_version'] = $arr_cfg[0]["mysql_query_version"];
	$arr_return['benchmark']['mysql_query_benchmark'] = $arr_cfg[0]["mysql_query_benchmark"];
	$arr_return['benchmark']['mysql_total'] = $arr_cfg[0]["mysql_total"];
	$arr_return['total'] = $arr_cfg[0]["total"] - $arr_cfg[0]["mysql_total"];
	/*
 $data['total']
 $data['benchmark']['mysql_total'])
 */
	return $arr_return;
}
function wptools_secondsToTime($seconds)
{
	$dtF = new \DateTime('@0');
	$dtT = new \DateTime("@$seconds");
	return $dtF->diff($dtT)->format('%a days, %h hours, %i minutes and %s seconds');
}
function wptools_options_check_table()
{
	global $wpdb;
	$prefix = $wpdb->prefix;
	$list_of_table = $wpdb->get_results("SHOW TABLE STATUS");
	/*
	 ["Name"]=> string(26) "wp_actionscheduler_actions" 
	 ["Engine"]=> string(6) "InnoDB" 
	 ["Version"]=> string(2) "10" 
	 ["Row_format"]=> string(7) "Compact"
	 **["Rows"]=> string(2) "22" 
	 ["Avg_row_length"]=> string(3) "744" 
	 **["Data_length"]=> string(5) "16384" 
	 ["Max_data_length"]=> string(1) "0" 
	 ["Index_length"]=> string(6) "114688" 
	 ["Data_free"]=> string(1) "0" 
	 ["Auto_increment"]=> string(3) "959" 
	 ["Create_time"]=> string(19) "2021-05-30 09:08:18" 
	 ["Update_time"]=> string(19) "2021-06-17 13:44:29" 
	 **["Check_time"]=> NULL 
	 ["Collation"]=> string(18) "utf8mb4_unicode_ci" 
	 ["Checksum"]=> NULL 
	 ["Create_options"]=> string(0) "" 
	 ["Comment"]=> string(0) "" } 
	 */
	wptools_show_logo();
	echo '<h1>' . esc_attr(__('Tables Information', 'wptools')) . '</h1>';
	// get mysql uptime
	global $wpdb;
	$wptools_url = admin_url() . 'admin.php?page=wptools_options32';
	echo '<br><big>';
	echo  esc_html__('Open Database Page if you find errors on tables (status not ok)', 'wptools') . '&nbsp;&raquo;&nbsp;';
	//echo '</big>';
	echo '<a target="_blank" class="button-primary"rel="noopener noreferrer" href="' . esc_url($wptools_url) . '">' . esc_html__('Dbase Page', 'wptools') . '</a>';
	echo '<br></big>';
	?>
	<br>
	<table class="wptools_admin_table">
		<tr>
			<th style="width:50px;"><strong><?php esc_attr_e("Status","wptools");?></strong></th>
			<th><strong><?php esc_attr_e("Table Name","wptools");?></strong></th>
			<th><strong><?php esc_attr_e("Engine","wptools");?></strong></th>
			<th><strong><?php esc_attr_e("Last Update","wptools");?></strong></th>
			<th><strong><?php esc_attr_e("Data Length (Aproximate)","wptools");?></strong></th>
		</tr>
		<?php
		foreach ($list_of_table as $check) {
		?>
			<tr>
				<td>
					<?php
					$table_name 	= preg_replace("/[&<>=#\(\)\[\]\{\}\?\"\' ]/", '', $check->Name);
					$table_name = trim($table_name);
					$query_result = $wpdb->get_results("CHECK TABLE `" . $table_name . "`");
					foreach ($query_result as $row) {
						if ($row->Msg_text) {
							echo esc_attr($row->Msg_text);
						}
					}
					?>
				</td>
				<td><?php echo esc_attr($check->Name); ?></td>
				<td><?php echo esc_attr($check->Engine); ?></td>
				<td><?php
					if (!empty($check->Update_time))
						echo esc_attr($check->Update_time);
					else
						echo esc_attr($check->Create_time);
					?></td>
				<td><?php echo esc_attr($check->Data_length); ?></td>
			</tr>
		<?php
		}
		?>
	</table>
	<hr>
	<h4><?php esc_attr_e("For more info about your database, look the page SHOW PHP INFO.","wptools");?>
	<?php
}
function wptools_php_max_input_vars()
{
	if (ini_get('max_input_vars')) {
		$php_max__input_vars = ini_get('max_input_vars');
	} else {
		$php_max__input_vars = __('N/A', 'wptools');
	}
	return $php_max__input_vars;
}
function wptools_php_max_upload_size()
{
	if (ini_get('upload_max_filesize')) {
		$php_max_upload_size = ini_get('upload_max_filesize');
		//$php_max_upload_size = $php_max_upload_size);
	} else {
		$php_max_upload_size = esc_attr__('N/A', 'wptools');
	}
	return $php_max_upload_size;
}
function wptools_php_max_post_size()
{
	if (ini_get('post_max_size')) {
		$php_max_post_size = ini_get('post_max_size');
		//$php_max_post_size = wptools_format_filesize($php_max_post_size);
	} else {
		$php_max_post_size = esc_attr__('N/A', 'wptools');
	}
	return $php_max_post_size;
}
function wptools_php_max_execution_time()
{
	if (ini_get('max_execution_time')) {
		$max_execute = ini_get('max_execution_time');
	} else {
		$max_execute = esc_attr__('N/A', 'wptools');
	}
	return $max_execute;
}
function wptools_check_limit()
{
	if (!ini_get('memory_limit'))
		return esc_attr__('N/A', 'wptools');
	$memory_limit = ini_get('memory_limit');
	if (preg_match('/^(\d+)(.)$/', $memory_limit, $matches)) {
		if ($matches[2] == 'G') {
			$memory_limit = $matches[1] . ' ' . 'GB'; // nnnG -> nnn GB
		} else if ($matches[2] == 'M') {
			$memory_limit = $matches[1] . ' ' . 'MB'; // nnnM -> nnn MB
		} else if ($matches[2] == 'K') {
			$memory_limit = $matches[1] . ' ' . 'KB'; // nnnK -> nnn KB
		} else if ($matches[2] == 'T') {
			$memory_limit = $matches[1] . ' ' . 'TB'; // nnnT -> nnn TB
		} else if ($matches[2] == 'P') {
			$memory_limit = $matches[1] . ' ' . 'PB'; // nnnP -> nnn PB
		}
	}
	return $memory_limit;
}
function wptools_options()
{
	//wptools_options21
	if (isset($_GET['page'])) {
		$page = sanitize_text_field($_GET['page']);
		if ($page != 'wptools_options21')
			return;
	}
	$wptools_count = 0;
	define('WPTOOLSPLUGINPATH', plugin_dir_path(__file__));
	$wptools_themePath = get_theme_root();
	$error_log_path = trim(ini_get('error_log'));
	if (!is_null($error_log_path) and $error_log_path != trim(ABSPATH . "error_log")) {
		$wptools_folders = array(
			$error_log_path,
			ABSPATH . "error_log",
			ABSPATH . "php_errorlog",
			WPTOOLSPLUGINPATH . "/error_log",
			WPTOOLSPLUGINPATH . "/php_errorlog",
			$wptools_themePath . "/error_log",
			$wptools_themePath . "/php_errorlog"
		);
	} else {
		$wptools_folders = array(
			ABSPATH . "error_log",
			ABSPATH . "php_errorlog",
			WPTOOLSPLUGINPATH . "/error_log",
			WPTOOLSPLUGINPATH . "/php_errorlog",
			$wptools_themePath . "/error_log",
			$wptools_themePath . "/php_errorlog"
		);
	}
	$wptools_admin_path = str_replace(get_bloginfo('url') . '/', ABSPATH, get_admin_url());
	array_push($wptools_folders, $wptools_admin_path . "/error_log");
	array_push($wptools_folders, $wptools_admin_path . "/php_errorlog");
	$wptools_plugins = array_slice(scandir(WPTOOLSPLUGINPATH), 2);
	foreach ($wptools_plugins as $wptools_plugin) {
		if (is_dir(WPTOOLSPLUGINPATH . "/" . $wptools_plugin)) {
			array_push($wptools_folders, WPTOOLSPLUGINPATH . "/" . $wptools_plugin . "/error_log");
			array_push($wptools_folders, WPTOOLSPLUGINPATH . "/" . $wptools_plugin . "/php_errorlog");
		}
	}
	$wptools_themes = array_slice(scandir($wptools_themePath), 2);
	foreach ($wptools_themes as $wptools_theme) {
		if (is_dir($wptools_themePath . "/" . $wptools_theme)) {
			array_push($wptools_folders, $wptools_themePath . "/" . $wptools_theme . "/error_log");
			array_push($wptools_folders, $wptools_themePath . "/" . $wptools_theme . "/php_errorlog");
		}
	}
	// echo WPTOOLSURL.'images/logo.png';
	echo '<br />';
	echo '<img src="' . esc_url(WPTOOLSURL) . 'images/logo.png" alt="logo">';
	echo '<h1>' . esc_attr__('Errors', 'wptools') . '</h1>';
	echo '<center>';
	echo '<h2>';
	echo esc_attr__("The lasts lines of the log files.", "wptools");
	echo '</h2>';
	echo '</center>';
	echo '<h4>';
	echo esc_attr__("For bigger files, download and open them in your local computer.", "wptools");
	
	echo '<br />';

	echo '<a href="https://wptoolsplugin.com/site-language-error-can-crash-your-site/" >';
	echo esc_attr(__("Learn more about errors and warnings...", 'wptools')) . '.';
	echo '</a>';

	echo '<br />';

	echo '</h4>';


	foreach ($wptools_folders as $wptools_folder) {
		foreach (glob($wptools_folder) as $wptools_filename) {
			if (strpos($wptools_filename, 'backup') != true) {
				echo '<hr>';
				echo '<strong>';
				echo esc_attr(wptools_sizeFilter(filesize($wptools_filename)));
				echo ' - ';
				echo esc_attr($wptools_filename);
				echo '</strong>';
				$wptools_count++;
				$marray = wptools_read_file($wptools_filename, 1000);


				if (gettype($marray) != 'array' or count($marray) < 1) {

					continue;

				}



				$total = count($marray);


				if (count($marray) > 0) {
					echo '<textarea style="width:100%" id="anti_hacker" rows="12">';
					
					

					if($total > 200)
					  $total = 200;
					
					for ($i = 0; $i < $total; $i++) {

						$pos = strpos($marray[$i] , ']' );

						if ($pos !== false) {

							// Data
							
							echo 'Date: '.esc_attr(substr($marray[$i],0,$pos+1));
							$marray[$i] = substr($marray[$i],$pos+1);
							echo PHP_EOL;
							


							$pos = strpos($marray[$i] , ':' );
							if ($pos !== false) {



								
								echo 'Type: '.esc_attr(substr($marray[$i],0,$pos));
								$marray[$i] = substr($marray[$i],$pos+1);
								echo PHP_EOL;
								


								// $pos = strpos($marray[$i] , 'on line' );
								$pos = strpos($marray[$i] , 'in /' );

								//$pos = strpos($marray[$i] , 'in /' );

									if ($pos !== false) {
										

										echo 'Message: '.trim(esc_attr(substr($marray[$i], 0, $pos)));
										echo PHP_EOL;
										echo 'In: '.trim(esc_attr(substr($marray[$i], $pos +2 )));
										echo PHP_EOL;
										$marray[$i] = substr($marray[$i],$pos+1);


									}
									else
									{ // in /
										echo 'Message: '.trim(esc_attr($marray[$i]));
										echo PHP_EOL;

									}
									
									


									// on line
									//echo trim(esc_attr(substr($marray[$i], $pos)));
									//echo PHP_EOL;
								//}
								/*
								else
								{
								   echo trim(esc_attr(substr($marray[$i], $pos + 1)));
								   echo PHP_EOL;						
								}
								*/


							}
							else { // :
							   echo esc_attr($marray[$i]);
							}
							   


						} 
						else  { // ']' 
					    	echo esc_attr($marray[$i]);
						}
						  



						echo PHP_EOL;


					} // end for...

					echo '</textarea>';
				}
				echo '<br />';
			}
		}
	}
	echo "<p>" . esc_attr(__("Log Files found", "wptools")) . ": " . esc_attr($wptools_count) . "</p>";
}
function wptools_read_file($file, $lines)
{
	try {
		$handle = fopen($file, "r");
	} catch (Exception $e) {
		return '';
	}
	if (!$handle)
		return '';

	$linecounter = $lines+50;
	$pos = -2;
	$beginning = false;
	$text = array();

	
	while ($linecounter > 0) {
		$t = " ";
		while ($t != "\n") {
			if (fseek($handle, $pos, SEEK_END) == -1) {
				$beginning = true;
				break;
			}
			$t = fgetc($handle);
			$pos--;
		}

		$linecounter--;

		if ($beginning)
			rewind($handle);
			
		$text[$lines - $linecounter - 1] = fgets($handle);
		if ($beginning)
			break;

		
	}

	fclose($handle);


	// remove trace...
	// return array_reverse($text); // array_reverse is optional: you can also just return the $text array which consists of the file's lines.
	
	$text = array_reverse($text);	

	$total = count($text);

	//var_dump($total);




	$text2 = array();
	for($i=0; $i < $total-1; $i++)
	{


		if (preg_match('/stack trace:$/i', $text[$i])) {

			//var_dump($text[$i]);

			//var_dump($text[$i-1]);

			if($i < $total-1)
			   $i++;
			else
			   break;

			/*
			var_dump(preg_match('!^\[(?P<time>[^\]]*)\] PHP\s+(?P<msg>\d+\. .*)$!', $text[$i], $parts));
			var_dump($parts);


			preg_match('!^(?P<msg>#\d+ .*)$!', $text[$i], $parts);
			var_dump($parts);
			*/

			//die();



			$stackTrace = $parts = [];
			//$log->next();

			// $i++;

			if($i < $total-1)
		    	$i++;
		    else
			    break;


			while ((preg_match('!^\[(?P<time>[^\]]*)\] PHP\s+(?P<msg>\d+\. .*)$!', $text[$i], $parts)
				|| preg_match('!^(?P<msg>#\d+ .*)$!', $text[$i], $parts)
				&&  $i < $total)
			) {
	
				$stackTrace[] = $parts['msg'];
				//var_dump($parts);
				//die();
				//$log->next();

				if($i < $total-1)
		        	$i++;
   		        else
			        break;
			}

			if (isset($stackTrace[0]) and substr($stackTrace[0], 0, 2) == '#0') {
				$stackTrace[] = $text[$i];
				// $log->next();

				if($i < $total-1)
				   $i++;
			     else
				   break;
			}
			// $prevError->trace = join("\n", $stackTrace);
		}
         
		/*
		if(isset($stackTrace)) {
		var_dump($stackTrace);
		die();
		}
		*/

		   $text2[] = $text[$i];




	}
	

	return array_reverse($text2);

	// return ($text2);
}
function wptools_sizeFilter($bytes)
{
	$label = array('Bytes', 'KB', 'MB', 'GB', 'TB', 'PB');
	for ($i = 0; $bytes >= 1024 && $i < (count($label) - 1); $bytes /= 1024, $i++);
	return (round($bytes, 2) . " " . $label[$i]);
}
function wptools_set_limit($limit = null)
{
	$old = wptools_get_limit();
	$limit = (int) $limit;
	@ini_set('memory_limit', $limit . 'M');
	$new = wptools_get_limit();
	if (!$new || ($new == $old and $limit != old)) {
		return false;
	}
	return true;
}
//
function wptools_current_upload_max_filesize()
{
	$upload_limit =  (int) ini_get('upload_max_filesize');
	return $upload_limit  * (1024 * 1024);
}
function wptools_set_upload_max_filesize()
{
	$upload_limit = (int) get_option('wptools_max_filesize', '0');
	$upload_limit = $upload_limit * (1024 * 1024);
	return $upload_limit;
}
///
function wptools_current_time_limit()
{
	$time_limit = (int) ini_get('max_execution_time');
	return $time_limit;
}
function wptools_set_time_limit($time_limit)
{
	$old_time_limit = wptools_current_time_limit();
	@ini_set('max_execution_time', $time_limit);
	$new_time_limit = wptools_current_time_limit();
	if (!$new_time_limit || ($new_time_limit == $old_time_limit and $time_limit != $old_time_limit)) {
		return false;
	}
	return true;
}
function wptools_get_limit()
{
	$limit = (int) ini_get('memory_limit');
	return $limit ? $limit : null;
}
function wptools_memory_test()
{
	global $wptools_memory, $wptools_usage_content, $wptools_label, $wptools_status, $wptools_description, $wptools_actions;
	$result = array(
		'badge' => array(
			'label' => $wptools_label,
			'color' => $wptools_memory['color'],
		),
		'test' => 'wptools_test',
		// status: Section the result should be displayed in. Possible values are good, recommended, or critical.
		'status' => $wptools_status,
		'label' => esc_attr__('Memory Usage', 'wptools'),
		'description' => $wptools_description . '  ' . $wptools_usage_content,
		'actions' => $wptools_actions
	);
	return $result;
}
function wptools_options_wp_config()
{
	wptools_show_logo();
	echo '<h1>' . esc_attr(__('wp-config.php', 'wptools')) . '</h1>';
	echo '<a href="https://wptoolsplugin.com/what-is-the-file-wp-config/" >';
	echo esc_attr(__("Visit plugin's site for detais", 'wptools')) . '.';
	echo '</a>';
	echo '<br>';
	echo '<br>';
	$file =  esc_url(ABSPATH) . "wp-config.php";
	echo esc_attr_e("File path:","wptools") . esc_attr($file);
	echo '<br>';
	$txt = '';
	if (!file_exists($file)) {
		echo esc_attr_e("File wp-config.php not found. Ask to your hosting company if is hidden.","wptools");
		echo '<br>';
	} else {
		$txt = trim(file_get_contents($file, true));
		if (empty($txt)) {
			echo esc_attr_e("Unable to read the file wp-config.php! Ask to your hosting company.","wptools");
			echo '<br>';
		}
	}
	echo '<form>';
	?>
		<textarea rows="16" cols="70"><?php echo esc_html($txt); ?></textarea>
		<br>
		<br>
	<?php
	echo '</form>';
}
function wptools_show_logo()
{
	echo '<div id="wptools_logo" style="margin-top:10px;">';
	// echo '<br>';
	echo '<img src="';
	echo esc_url(WPTOOLSIMAGES) . '/logo.png';
	echo '">';
	echo '<br>';
	echo '</div>';
}
function wptools_options_cookies()
{
	wptools_show_logo();
	echo '<h1>' . esc_attr(__('Show Cookies', 'wptools')) . '</h1>';
	echo '<a href="https://wptoolsplugin.com/what-are-cookies/" >';
	echo esc_attr(__("Visit plugin's site for detais", 'wptools')) . '.';
	echo '</a>';
	echo '<br>';
	?>
		<br>
		<table class="wptools_admin_table" align="center">
			<thead>
				<th><?php echo esc_attr(__('Name', 'wptools')); ?></th>
				<th><?php echo esc_attr(__('Value', 'wptools')); ?></th>
			</thead>
			<?php foreach ($_COOKIE as $name => $value) : ?>
				<tr>
					<td><?php echo esc_html($name); ?></td>
					<td><?php echo esc_html($value); ?></td>
				</tr>
			<?php endforeach; ?>
		</table>
	<?php
}
function wptools_options_permissions()
{
	wptools_show_logo();
	echo '<h1>' . esc_attr(__('Permission Scheme for WordPress', 'wptools')) . '</h1>';
	echo esc_attr(__('Typically', 'wptools'));
	echo ':';
	echo '<br>';
	echo esc_attr(__('Files', 'wptools'));
	echo ': 644';
	echo '<br>';
	echo esc_attr(__('Folders', 'wptools'));
	echo ': 755';
	echo '<br>';
	echo  esc_attr(__('File wp-config.php: 660', 'wptools'));
	echo '<br>';
	echo '<br>';
	// echo ABSPATH.'wp-config.php';
	if (file_exists(ABSPATH . 'wp-config.php'))
		echo esc_attr_e("wp-config.php currently permissions:","wptools") . esc_attr(decoct(fileperms(ABSPATH . 'wp-config.php') & 0777));
	echo '<br>';
	echo '<br>';
	echo '<br>';
	echo '<a href="https://wptoolsplugin.com/wordpress-file-permissions/" >';
	echo esc_attr(__("Visit plugin's site for detais", 'wptools')) . '.';
	echo '</a>';
	echo '<br>';
	$files = wptools_fetch_files(ABSPATH);
	if ($files === false) {
		echo '<h3>' . esc_attr(__('Unable to read files', 'wptools')) . '</h3>';
		return;
	}
	?>
		<table class="wptools_admin_table" align="center">
			<thead>
				<th><?php echo esc_attr(__('Permissions', 'wptools')); ?></th>
				<th><?php echo esc_attr(__('Files / Folders', 'wptools')); ?></th>
			</thead>
			<?php
			$ctdf = 0;
			$ctdd = 0;
			for ($i = 0; $i < count($files); $i++) {
				if (is_dir($files[$i])) {
					if ($files[$i] == 'wp-config.php') {
						$ctdd++;
						continue;
					}
					if (decoct(fileperms($files[$i]) & 0777) != '755') {
						$ctdd++;
						if ($ctdd < 51) {
							echo '<tr>';
							echo '<td>';
							echo esc_attr(decoct(fileperms($files[$i]) & 0777));
							echo '</td>';
							echo '<td>';
							echo esc_attr($files[$i]);
							echo '</td>';
							echo '<tr>';
						}
					}
				} else {
					if (@decoct(fileperms($files[$i]) & 0777) != '644') {
						$ctdf++;
						if ($ctdf < 51) {
							echo '<tr>';
							echo '<td>';
							try {
								echo esc_attr(@decoct(fileperms($files[$i]) & 0777));
							} catch (exception $e) {
							}
							// echo decoct(fileperms($files[$i]) & 0777);
							echo '</td>';
							echo '<td>';
							echo esc_attr($files[$i]);
							echo '</td>';
							echo '<tr>';
						}
					}
				}
			} ?>
		</table>
	<?php
	echo '<br>';
	echo '<br>';
	if ($ctdf > 0)
		echo esc_attr($ctdf) . ' ' . esc_attr__('Files with wrong permissions. This plugin will show max 50.', 'wptools');
	else
		echo esc_attr(__('No files found with wrong permissions.', 'wptools'));
	echo '<br>';
	echo '<br>';
	if ($ctdd > 0)
		echo esc_attr($ctdd) . ' ' . esc_attr(__('Folders with wrong permissions. This plugin will show max 50.', 'wptools'));
	else
		echo esc_attr(__('No folders found with wrong permissions.', 'wptools'));
	echo '<br>';
}
function wptools_fetch_files($dir)
{
	try {
		$x = scandir($dir);
	} catch (exception $e) {
		return false;
	}
	$result = array();
	foreach ($x as $filename) {
		if ($filename == '.') continue;
		if ($filename == '..') continue;
		$result[] = $dir . $filename;
		$filePath = $dir . $filename;
		if (is_dir($filePath)) {
			$filePath = $dir . $filename . '/';
			foreach (wptools_fetch_files($filePath) as $childFilename) {
				$result[] = $childFilename;
			}
		}
	}
	return $result;
}
function wptools_options_htaccess()
{
	wptools_show_logo();
	echo '<h1>' . esc_attr(__('.htaccess', 'wptools')) . '</h1>';
	echo '<a href="https://wptoolsplugin.com/what-is-the-file-htaccess/" >';
	echo esc_attr(__("Visit plugin's site for detais", 'wptools')) . '.';
	echo '</a>';
	echo '<br>';
	echo '<br>';
	$file =  esc_url(ABSPATH) . ".htaccess";
	echo  esc_attr__("File path:","wptools") . esc_attr($file);
	echo '<br>';
	$txt = '';
	if (!file_exists($file)) {
		echo esc_attr__("File .htaccess not found.","wptools");
		echo '<br>';
	} else {
		$txt = trim(file_get_contents($file, true));
		if (empty($txt)) {
			echo esc_attr__("Empty file .htaccess!","wptools").'<br>';
		}
	}
	echo '<form>';
	?>
		<textarea rows="16" cols="70"><?php echo esc_html($txt); ?></textarea>
		<br>
		<br>
	<?php
	echo '</form>';
}

function wptools_errors_today($onlytoday)
{
	$wptools_count = 0;



	//define('WPTOOLSPATH', plugin_dir_path(__file__));
	//WPTOOLSPATH
	$wptools_themePath = get_theme_root();
	$error_log_path = trim(ini_get('error_log'));
	if (!is_null($error_log_path) and $error_log_path != trim(ABSPATH . "error_log")) {
		$wptools_folders = array(
			$error_log_path,
			ABSPATH . "error_log",
			ABSPATH . "php_errorlog",
			WPTOOLSPATH . "/error_log",
			WPTOOLSPATH . "/php_errorlog",
			$wptools_themePath . "/error_log",
			$wptools_themePath . "/php_errorlog"
		);
	} else {
		$wptools_folders = array(
			ABSPATH . "error_log",
			ABSPATH . "php_errorlog",
			WPTOOLSPATH . "/error_log",
			WPTOOLSPATH . "/php_errorlog",
			$wptools_themePath . "/error_log",
			$wptools_themePath . "/php_errorlog"
		);
	}
	$wptools_admin_path = str_replace(get_bloginfo('url') . '/', ABSPATH, get_admin_url());
	array_push($wptools_folders, $wptools_admin_path . "/error_log");
	array_push($wptools_folders, $wptools_admin_path . "/php_errorlog");
	$wptools_plugins = array_slice(scandir(WPTOOLSPATH), 2);
	foreach ($wptools_plugins as $wptools_plugin) {
		if (is_dir(WPTOOLSPATH . "/" . $wptools_plugin)) {
			array_push($wptools_folders, WPTOOLSPATH . "/" . $wptools_plugin . "/error_log");
			array_push($wptools_folders, WPTOOLSPATH . "/" . $wptools_plugin . "/php_errorlog");
		}
	}
	$wptools_themes = array_slice(scandir($wptools_themePath), 2);
	foreach ($wptools_themes as $wptools_theme) {
		if (is_dir($wptools_themePath . "/" . $wptools_theme)) {
			array_push($wptools_folders, $wptools_themePath . "/" . $wptools_theme . "/error_log");
			array_push($wptools_folders, $wptools_themePath . "/" . $wptools_theme . "/php_errorlog");
		}
	}



	foreach ($wptools_folders as $wptools_folder) {


		//// if (gettype($wptools_folder) != 'array')
		//	continue;

		if(trim(empty($wptools_folder)))
			continue;



		foreach (glob($wptools_folder) as $wptools_filename) {
			if (strpos($wptools_filename, 'backup') != true) {
				$wptools_count++;
				$marray = wptools_read_file($wptools_filename, 20);

				if (gettype($marray) != 'array' or count($marray) < 1) {
					continue;
				}




				if (count($marray) > 0) {
					for ($i = 0; $i < count($marray); $i++) {
						// [05-Aug-2021 08:31:45 UTC]

						if (substr($marray[$i], 0, 1) != '[' or empty($marray[$i]))
							continue;
						$pos = strpos($marray[$i], ' ');
						$string = trim(substr($marray[$i], 1, $pos));
						if (empty($string))
							continue;
						// $data_array = explode('-',$string,);
						$last_date = strtotime($string);
						// var_dump($last_date);
                        
						if($onlytoday == 1) {
							if ((time() - $last_date) < 60 * 60 * 24)
							return true;
						}
						else {
							return true;	
						}
					}
				}
			}
		}
	}
	return false;
}
function bill_check_resources($par)
{
	// echo '<div class="notice notice-warning is-dismissible">';
	if (!$par)
		ob_start();
	?>
		<div id="wptools-steps3">
			<div class="wptools-block-title">
			<?php esc_attr_e("Server Check & Requirements","wptools");?>
			</div>
			<?php
			echo '<div style="padding:20px; margin:0px;"><big>';
			try{
			   $bill_install_memory = bill_install_check_memory();
			}
			catch(Exception $e)
			{
				$bill_install_memory  = 0;
			}
			echo '<h3>';
			echo esc_attr_e("Step 1 - Check Memory","wptools");  
			echo '</h3>';
			$bill_found_error = false;
			$bill_found_error_hosting = false;

			if(!is_array($bill_install_memory) or !isset($bill_install_memory)){
			
				esc_attr_e("General Failure (7) to get memory information.","wptools");
				echo '<br>';
				$bill_found_error = true;

			}
            else{

				if ($bill_install_memory['msg_type'] == 'ok') {
					if ($bill_install_memory['wp_limit'] - $bill_install_memory["usage"] < 30) {
						esc_attr_e("Your site free memory is too low and can happens memory fault error, also without install our plugin.","wptools");
						echo '<br>';
						esc_attr_e("The WordPress Memory Limit is","wptools");
						echo " " . esc_attr($bill_install_memory['wp_limit']) . " MB";
						echo '<br>';
						esc_attr_e("The WordPress Memory Usage is","wptools");
						echo " " . esc_attr($bill_install_memory['usage']) . " MB";
						echo '<br>';
						esc_attr_e("The free memory now is","wptools");
						echo ' ' . esc_attr(($bill_install_memory['wp_limit']) - esc_attr($bill_install_memory["usage"])) . ' MB';
						echo '<br>';
						esc_attr_e("For more details, visit our site","wptools");
						echo ' '.'<a href="https://wpmemory.com/">';
						esc_attr_e("WP MEMORY (Free WordPress Plugin to Fix Low Memory Limit)","wptools"); 
						echo '</a>';
						echo '<br>';
						$bill_found_error = true;
					}
				} elseif ($bill_install_memory['msg_type'] == 'notok(1)') {
					esc_attr_e("Unable to get ini_get('memory_limit')","wptools");
					echo "";
					echo '<br>';
					$bill_found_error = true;
				} elseif ($bill_install_memory['msg_type'] == 'notok(2)') {
					esc_attr_e("Unable to get memory_get_usage() ","wptools");
					echo '<br>';
					$bill_found_error = true;
				} elseif ($bill_install_memory['msg_type'] == 'notok(3)') {
					esc_attr_e("Memory Usage is not Numeric (3)","wptools");
					echo '<br>';
					$bill_found_error = true;
				} elseif ($bill_install_memory['msg_type'] == 'notok(4)') {
					esc_attr_e("Memory Usage < than 1'","wptools");
					echo '<br>';
					$bill_found_error = true;
				} elseif ($bill_install_memory['msg_type'] == 'notok(5)') {
					esc_attr_e("Possible error: On file wp-config.php memory symbol should be M.","wptools");
					echo '<br>';
					esc_attr_e('For example,  define("WP_MEMORY_LIMIT", "256M"); ',"wptools");
					echo '<br>';
					esc_attr_e("For more details, visit our site");
					echo ' '.'<a href="https://wpmemory.com/">';
					esc_attr_e("WP MEMORY (Free WordPress Plugin to Fix Low Memory Limit)","wptools"); 
					echo '</a>';
					echo '<br>';
					$bill_found_error = true;
				} elseif ($bill_install_memory['msg_type'] == 'notok(6)') {
					esc_attr_e("Memory Limit out of range. (6)","wptools");
					echo '<br>';
					$bill_found_error = true;
				} elseif ($bill_install_memory['msg_type'] == 'notok(7)') {
					esc_attr_e("General Failure (7) to get memory information.","wptools");
					echo '<br>';
					$bill_found_error = true;
				}
				if ($bill_found_error == false) {
					esc_attr_e("No Memory Problems found.","wptools");
					echo '<br>';
					echo  esc_attr_e("The free memory now is","wptools");
					echo ' ' . esc_attr($bill_install_memory['wp_limit'] - $bill_install_memory["usage"]) . ' MB.';
					echo '<br>';
					esc_attr_e("For more details, visit our site","wptools");
						echo ' '.'<a href="https://wpmemory.com/">';
						esc_attr_e("WP MEMORY (Free WordPress Plugin to Fix Low Memory Limit)","wptools"); 
						echo '</a>';
				}
		    }
			
			//$bill_found_error = false;
			echo '<h3>';
			esc_attr_e("Step 2 - Check for Javascript Errors","wptools");
			echo '</h3>';
			echo '<div id="bill_javascript_status">';
			esc_attr_e("Javascript is NOT working in your site and/or with your browser!","wptools");
			echo '</div>';
			/* WP Tools ========= BEGIN ================  */
			//$bill_found_error = false;
			echo '<h3>';
			esc_attr_e("Step 3 - Check for Hosting Resources","wptools");
			echo '</h3>';
			if (!defined('PHP_OS_FAMILY')) {
			?>
				<?php esc_attr_e("Maybe Your server is not Linux","wptools");?>
				<br>
				<?php esc_attr_e("and this plugin requires Linux.","wptools");?>
				<br>
				<?php esc_attr_e("Your Hosting Company dont have the constant: PHP_OS_FAMILY defined.","wptools");?>
				<br>
				<br>
				<?php
				$bill_found_error = true;
				$bill_found_error_hosting = true;
			} else {
				if (stripos(PHP_OS_FAMILY, 'linux') === false) {
				?>
					<?php esc_attr_e("Your server is not Linux","wptools");?>
					<br>
					<?php esc_attr_e("and this plugin requires Linux.","wptools");?>
					<br>
					<?php esc_attr_e("Your server is:","wptools");?>
					&nbsp; <?php echo esc_attr(PHP_OS_FAMILY); ?>
					<br>
					<br>
				<?php
					$bill_found_error = true;
					$bill_found_error_hosting = true;
				}
			}

			if(!wptools_check_if_obd_permitted()) {

				esc_attr_e("Plugin requirement:","wptools");?>
				&nbsp; 
				<?php esc_attr_e("Open_basedir restriction in effect. Talk with your hosting and request to disable it.","wptools");?>
			   <br>
			   <br>
			   <?php
			   $bill_found_error = true;
			   $bill_found_error_hosting = true;

			}

			//	if(wptools_check_if_obd_permitted() and is_readable('/proc/stat')) {

			if (!function_exists('sys_getloadavg')) {
				esc_attr_e("Plugin requirement:","wptools");?>
				 &nbsp; 
				 <?php esc_attr_e("PHP function sys_getloadavg not enabled. Talk with your hosting and request to enable it.","wptools");?>
				<br>
				<br>
				<?php
				$bill_found_error = true;
				$bill_found_error_hosting = true;
			}
			try {
				if (!wptools_check_if_obd_permitted() or !@is_readable('/proc/cpuinfo') or  !@file_get_contents('/proc/cpuinfo')    ) {
					esc_attr_e("Plugin requirement:","wptools");?>
					&nbsp; 
					<?php esc_attr_e("/proc/cpuinfo doesn't readable. Talk with your hosting and request to enable it.","wptools");?>
					<br>
					<br>
				<?php
					$bill_found_error = true;
					$bill_found_error_hosting = true;
				}
				if (!function_exists("disk_free_space") or !function_exists("disk_total_space")) {
					esc_attr_e("Request to your Hosting enable the PHP functions disk_free_space() and disk_total_space()","wptools");?>
					<br>
					<br>
					<?php
					$bill_found_error = true;
					$bill_found_error_hosting = true;
				}
			} catch (Exception $e) {
			}
			try {
				if (function_exists("shell_exec")) {
					$loadresult = @shell_exec('uptime');
					/*
						ob_start();
						var_dump($loadresult);
						$retorna = ob_get_contents();
						ob_end_clean();
						*/
					// if (trim($retorna) == 'NULL') {
					if (trim($loadresult) == NULL) {
					 	esc_attr_e("Request to your Hosting to enable the PHP function shell_exec() because is returning NULL.","wptools");?>
						<br>
						<br>
					<?php
						$bill_found_error = true;
						$bill_found_error_hosting = true;
					}
				}
			} catch (Exception $e) {
				// echo 'Message: ' .$e->getMessage();
			}
			try {
				if (!wptools_check_if_obd_permitted() or !@is_readable('/proc/stat')) {
					$bill_found_error = true;
					$bill_found_error_hosting = true;
					esc_attr_e("Plugin requirement:","wptools");?>
					&nbsp; 
					<?php esc_attr_e("/proc/stat doesn't readable. Talk with your hosting and request to enable it.","wptools");?>
					<br>
					<br />
				<?php
					$bill_found_error = true;
					$bill_found_error_hosting = true;
				}
			} catch (Exception $e) {
				// echo 'Message: ' .$e->getMessage();
			}
			try {
				if (!wptools_check_if_obd_permitted() or !@is_readable("/etc/os-release")) {
					esc_attr_e("Plugin requirement:","wptools");?>
					&nbsp; 
					<?php esc_attr_e("/etc/os-release doesn't readable. Talk with your hosting and request to enable it.","wptools");?>
					<br>
					<br />
				<?php
					$bill_found_error = true;
					$bill_found_error_hosting = true;
				}
			} catch (Exception $e) {
				// echo 'Message: ' .$e->getMessage();
				return false;
			}
			if (version_compare(PHP_VERSION, '7.0.0', '<')) {
				esc_attr_e("PHP Version 7.0 or bigger is required. My version:","wptools");
				echo ' ';
				echo esc_attr(PHP_VERSION) . "\n";
				$bill_found_error = true;
				$bill_found_error_hosting = true;
				echo '<br>';
				echo '<br>';
			}


            $get_numbercores = false;
            if (function_exists('sys_getloadavg')) {
				$loadavg = sys_getloadavg();
				if(gettype($loadavg) === 'array' and count($loadavg) > 2)
                   $get_numbercores = true;     
            }

			if( !$get_numbercores){
				esc_attr_e("Your hosting is blocking the PHP function sys_getloadavg().","wptools");
				$bill_found_error = true;
				$bill_found_error_hosting = true;
				echo '<br>';
				echo '<br>';

			}








            ?>
            <br>
			<?php esc_attr_e("if you can see warnings or empty fields on your dashboard, means your hosting company is blocking some php functions.","wptools");?>
			<br>
           <?php
			if ($bill_found_error_hosting != true) {
                esc_attr_e("Looks like our plugin can works fine with your server configuration.","wptools");?>
				<br>
			<?php
			} else {
				//echo '<br>';
				esc_attr_e("If we are wrong, please, contact our support at: https://wptoolsplugin.com","wptools");
				echo '<br>';
			}
			/* WP Tools ========= END ================  */
			$site = '#';
			/*
			$bd_msg = '<br /><br />';
			$bd_msg .= '<a href="' . esc_attr($site) . '" class="button button-primary">';
			$bd_msg .= esc_attr_e("Dismiss","wptools");
			$bd_msg .= '</a>';
			*/
			echo '<br /><br />';
			echo '</div>';
			echo '</big>';
			?>
		</div>
	<?php
	if (!$par)
		ob_end_clean();
	// Debug $bill_found_error = true;
	return $bill_found_error;
} // end function
//echo '<hr>';
/*
				echo '<pre>';
				var_dump($bill_install_memory);
				echo '</pre>';
				*/
//echo "Step 1 - Javascript errors";
// die();
//die();
function bill_install_check_memory()
{
	/*
				notok(1) = Unable to get ini_get('memory_limit')
				notok(2) = Unable to get memory_get_usage()
				notok(3) = Memory Usage is not Numeric
				notok(4) = Memory Usage < than 1
				notok(5) = symbol <> M
				notok(6) = Memory Limit out of range
				notok(7) = General Failure
				*/
	global $bill_install_memory;
	try {
		$bill_install_memory['limit'] = (int) ini_get('memory_limit');
		if (!is_numeric($bill_install_memory['limit'])) {
			$bill_install_memory['msg_type'] = 'notok(1)';
			return;
		}
		$bill_install_memory['usage'] = function_exists('memory_get_usage') ? round(memory_get_usage() / 1024 / 1024, 0) : 0;
		if ($bill_install_memory['usage'] == 0) {
			$bill_install_memory['msg_type'] = 'notok(2)';
			return;
		}
		if (!is_numeric($bill_install_memory['usage'])) {
			$bill_install_memory['msg_type'] = 'notok(3)';
			return;
		}
		if (!defined("WP_MEMORY_LIMIT")) {
			$bill_install_memory['wp_limit'] =  '40M';
			// return;
		} else
			$bill_install_memory['wp_limit'] =  trim(WP_MEMORY_LIMIT);

		if ($bill_install_memory['limit'] > 9999999) {
			// $bill_install_memory['msg_type'] = 'notok(5)';
			$bill_install_memory['wp_limit'] =  ($bill_install_memory['wp_limit']  / 1024) / 1024;
		}
		if ($bill_install_memory['usage'] < 1) {
			$bill_install_memory['msg_type'] = 'notok(4)';
			return;
		}
		$wplimit = $bill_install_memory['wp_limit'];
		$bill_install_memory['wp_limit_simbol'] = substr($wplimit, strlen($wplimit) - 1);
		if ($bill_install_memory['wp_limit_simbol'] != 'M') {
			$bill_install_memory['msg_type'] = 'notok(5)';
			return;
		}
		$wplimit = substr($wplimit, 0, strlen($wplimit) - 1);
		$bill_install_memory['wp_limit'] = $wplimit;
		if ($wplimit < 1 or $wplimit > 999) {
			$bill_install_memory['msg_type'] = 'notok(6)';
			return;
		}
		$bill_install_memory['percent'] = $bill_install_memory['usage'] / $bill_install_memory['wp_limit'];
		$bill_install_memory['msg_type'] = 'ok';
		return $bill_install_memory;
	} catch (Exception $e) {
		$bill_install_memory['msg_type'] = 'notok(7)';
		return $bill_install_memory;
	}
}
function wptools_update()
{
	global $wptools_checkversion;
	$wptools_termina = get_transient('wptools_termina');
	if (empty($wptools_checkversion) or $wptools_termina !== false)
		return;
	ob_start();
	$domain_name = get_site_url();
    $urlParts = parse_url($domain_name);
    $domain_name = preg_replace('/^www\./', '', $urlParts['host']);
	$myarray = array(
        'domain_name' => $domain_name,
        'wptools_checkversion' => $wptools_checkversion,
		'wptool_version' => WPTOOLSVERSION
    );
	$url = "https://wptoolsplugin.com/API/bill-api2.php";
	$response = wp_remote_post($url, array(
		'method' => 'POST',
		'timeout' => 5,
		'redirection' => 5,
		'httpversion' => '1.0',
		'blocking' => true,
		'headers' => array(),
		'body' => $myarray,
		'cookies' => array()
	));
	if (is_wp_error($response)) {
		$error_message = $response->get_error_message();
		// echo "Something went wrong: $error_message";
		set_transient('wptools_termina', DAY_IN_SECONDS, DAY_IN_SECONDS);
		ob_end_clean();
		return;
	}
	$r = trim($response['body']);
	$r = json_decode($r, true);
	$q = count($r);
    if ($q == 1) {
        $botip = trim($r[0]['ip']);
        if ($botip == '-9') {
			update_option('wptools_checkversion', '');
        }
		else
	    	$r = set_transient('wptools_termina', time(), (30 * DAY_IN_SECONDS));		
    }
	else {
		$r = set_transient('wptools_termina', time(), (DAY_IN_SECONDS));
	}
	ob_end_clean();
	return;
}
function wptools_options_robots()
{
	global $wptools_checkversion;
	wptools_show_logo();
	echo '<h1>' . esc_attr(__('Robots TXT', 'wptools')) . '</h1>';
	echo '<a href="https://wptoolsplugin.com/what-is-robots-txt/" >';
	echo esc_attr(__("Visit plugin's site for detais", 'wptools')) . '.';
	echo '</a>';
	echo '<br>';
	echo '<br>';
	$file =  esc_url(ABSPATH) . "robots.txt";
	$file_bkp =  esc_attr(ABSPATH) . "_robots.txt";
	echo esc_attr__("File Path","wptools").' '.esc_attr($file);
	echo '<br>';
	if (isset($_POST['wptools-robots-button'])) {
		if (isset($_POST['wtools-robots-textarea'])) {
			$txt = implode(PHP_EOL, array_map('sanitize_textarea_field', explode(PHP_EOL, $_POST['wtools-robots-textarea'])));
			// BKP 
			if (!$handle_bkp = fopen($file_bkp, 'w+')) {
				esc_attr_e("Cannot create BKP file","wptools");
				echo ' - ';
				esc_attr($file_bkp);
				return;
			}
			if (fwrite($handle_bkp, $txt) === FALSE) {
				esc_attr_e("Cannot write to BKP file","wptools");
				echo ' - ';
				esc_attr($file_bkp);
				return;
			}
			if (!$handle = fopen($file, 'w+')) {
				esc_attr_e("Cannot open file to save","wptools");
				echo ' - ';
				esc_attr($file_bkp);
				return;
			}
			if (fwrite($handle, $txt) === FALSE) {
				esc_attr_e("Cannot write to file","wptools");
				echo ' - ';
				esc_attr($file_bkp);
				return;
			}
			echo '<b>';
			echo "";
			esc_attr_e("Success, file robots.txt updated!","wptools");
			echo '<br>';
			esc_attr_e("Backup file created:","wptools");
			echo ' ' . esc_attr($file_bkp);
			echo '</b>';
			echo '<br>';
			esc_attr_e("If you save again, will overwrite the backup.","wptools");
			echo '<br>';
			echo '<br>';
			fclose($handle);
		} else {
			esc_attr_e("Fail to Get Post Content!","wptools");		
			return;
		}
	}
	if (!file_exists($file)) {
		echo '';
		esc_attr_e("File not found.","wptools");
		echo '<br>';
		esc_attr_e("To create, fill out the form below and click SAVE","wptools");
	} else {
		if (is_writable($file)) {
			$txt = trim(file_get_contents($file, true));
			$size = filesize($file);
		} else {
			// deveria ser 0660
			// if (copy($file, ABSPATH."_robots.txt")){
				if(is_writable($file_bkp) and unlink($file)) {
				    copy($file_bkp, $file);
					$txt = trim(file_get_contents($file, true));
					//$size = filesize($file);
				}
			// }
		//	echo substr(sprintf('%o', fileperms($file)), -4);
		//	var_dump(chmod($file,0660));
		if(!is_writable($file)){
				// echo substr(sprintf('%o', fileperms($file)), -4);
				esc_attr_e("File not writable. Check file permissions and user own. Unable to edit it.","wptools");
				echo '<br>';
				return;
			}
		}
		if (empty($txt)) {
			esc_attr_e("Empty file robots.txt!","wptools");
			echo '<br>';
			esc_attr_e("To create, fill out the form below and click SAVE","wptools");
		} else {
			esc_attr_e("Edit the file and click SAVE","wptools");
		}
	}
	echo '<form class="wptools-robots-form" method="post" action="admin.php?page=wptools_options24">' . "\n";
	if (!isset($txt))
		$txt = '';
	?>
		<textarea id="wtools-robots-textarea" name="wtools-robots-textarea" rows="12" cols="70"><?php echo esc_html($txt); ?></textarea>
		<br>
		<br>
		<?php esc_attr_e("Suggestion: Before to click SAVE, copy the content to your clipboard. Just in case to fail to save.","wptools");?>
		<br>
		<br>
		<?php
		// echo '<button id="wptools-robots-button" name="wptools-robots-button" class="button button-primary">Save</a>';
		if (!empty($wptools_checkversion)) {
			//echo '</big>';
			echo '<button id="wptools-robots-button" name="wptools-robots-button" class="button button-primary">Save</a>';
		} else
			echo esc_html(__("(Save Option Button available in Premium Version)", "wptools"));
		echo '</form>';
	}
	function wptools_options_php_info()
	{
		global $wptools_max_filesize;
		global $wptools_time_limit;
		global $wptools_memory_limit;
		global $wpdb;
		/*
				$phpTime = date('Y-m-d H:i:s');
				$timezone = new DateTimeZone(date_default_timezone_get());
				$offset = $timezone->getOffset(new DateTime($phpTime));
				$offsetHours = round(abs($offset)/3600);
				$str_offset = "-0$offsetHours:00";
				*/
		if (isset($_GET['page'])) {
			$page = sanitize_text_field($_GET['page']);
			if ($page != 'wptools_options22')
				return;
		}
		wptools_show_logo();
		/*
				falta por no settings: post_max_size
				@ini_set( 'post_max_size', '64M');
				*/
		?>
		<h1><?php echo esc_attr__('PHP Information', 'wptools'); ?></h1>
		<hr />
		<center>
			<h2><?php echo esc_attr__('Basic Information', 'wptools'); ?></h2>
			<table class="wptools_admin_table">
				<thead>
					<th><?php echo esc_attr(__('Name', 'wptools')); ?></th>
					<th><?php echo esc_attr(__('Value', 'wptools')); ?></th>
					<th><?php echo esc_attr(__('Current Modified Value', 'wptools')); ?></th>
				</thead>
				<tbody>
					<tr>
						<td><?php echo esc_attr(__('PHP Version', 'wptools')); ?></td>
						<td><?php echo esc_attr(PHP_VERSION); ?></td>
					</tr>
					<tr>
						<td><?php echo esc_attr__('Server Timezone', 'wptools'); ?></td>
						<td>
							<?php
							if (date_default_timezone_get()) {
								echo esc_attr(date_default_timezone_get());
								$phpTime = date('Y-m-d H:i:s');
								$timezone = new DateTimeZone(date_default_timezone_get());
								$offset = $timezone->getOffset(new DateTime($phpTime));
								$offsetHours = round(abs($offset) / 3600);
								$str_offset = ($offset >= 0 ? "+" : "-") . $offsetHours . ":00";
								// $str_offset = "-0$offsetHours:00";
								$dateTimeZoneRoma = new DateTimeZone("Europe/Rome");
								$dateTimeRoma = new DateTime("now", $dateTimeZoneRoma);
								$offset = $dateTimeZoneRoma->getOffset($dateTimeRoma);
								$offsetHours = round(abs($offset) / 3600);
								$str_offset = ($offset >= 0 ? "+" : "-") . $offsetHours . ":00";
								echo '    ' . esc_attr($str_offset);
							}
							?></td>
					</tr>
					<tr>
						<td><?php echo esc_attr(__('Server Time', 'wptools')); ?></td>
						<td>
							<?php echo $today = date("F j, Y, g:i a"); ?>
						</td>
					</tr>
					<tr>
						<td><?php echo esc_attr_e('PHP Max Memory Limit', 'wptools'); ?></td>
						<td>
							<?php echo esc_attr(wptools_check_limit()); ?>
						</td>
						<td> <?php
								if ($wptools_memory_limit > 0 and $wptools_memory_limit <= 512)
									echo  esc_attr($wptools_memory_limit) . ' MB';
								?>
					</tr>
					<tr>
						<td><?php esc_attr_e('PHP Max Execution Time', 'wptools'); ?></td>
						<td><?php echo esc_attr(wptools_php_max_execution_time()) . " " . esc_attr__("sec", "wptools");
							?></td>
						<td><?php
							if ($wptools_time_limit > 0 and $wptools_time_limit <= 360)
								echo esc_attr($wptools_time_limit) . " " . esc_attr(__("sec", "wptools"));
							?></td>
					</tr>
					<tr>
						<td><?php echo esc_attr__('PHP Max Upload Size', 'wptools'); ?></td>
						<td><?php echo esc_attr(wptools_php_max_upload_size()); ?></td>
						<td><?php
							if ($wptools_max_filesize > wptools_current_upload_max_filesize())
								echo esc_attr(wptools_format_filesize($wptools_max_filesize));
							?></td>
					</tr>
					<tr>
						<td><?php echo esc_attr(__('PHP Max Post Size', 'wptools')) . '*'; ?></td>
						<td><?php echo esc_attr(wptools_php_max_post_size()); ?></td>
					</tr>
					<tr>
						<td><?php esc_attr_e('PHP Max Input Vars', 'wptools') . '*'; ?></td>
						<td><?php echo esc_attr(wptools_php_max_input_vars()); ?></td>
					</tr>
				</tbody>
			</table>
			<br />
			<a href="https://wptoolsplugin.com/blog/">
				(*) 
				 <?php esc_attr_e("Check how to do it at our blog.","wptools");?>
			</a>

			<?php
			echo '<br />';
			echo '<br />';
			echo '<h2>';
			echo esc_attr__('Disabled Functions', 'wptools'); 
			echo '</h2>';
			echo '<div style="background: white; padding:10px;">';
			if(!empty(ini_get( 'disable_functions' )))
		    	echo str_replace( ",", ", ", ini_get( 'disable_functions' ) );
			else
			    esc_attr_e("Nothing","wptools");
			echo '</div>';
			?>

            <?php
			echo '<br />';
			echo '<br />';
			echo '<h2>';
			echo esc_attr__('Extensions Loaded', 'wptools'); 
			echo '</h2>';
			echo '<div style="background: white; padding:10px;">';
			echo esc_attr(implode(", ", get_loaded_extensions() )); 
			echo '</div>';
			echo '<br />';
			echo '<br />';
			?>







			<h2><?php esc_attr_e('Complete Information', 'wptools'); ?></h2>
		</center>
	<?php
		ob_start();
		phpinfo(INFO_ALL & ~INFO_LICENSE & ~INFO_CREDITS);
		preg_match('%<style type="text/css">(.*?)</style>.*?(<body>.*</body>)%s', ob_get_clean(), $matches);
		# $matches [1]; # Style information
		# $matches [2]; # Body information
		function wptools_phpnfo_css($i)
		{
			return ".phpinfodisplay " . preg_replace("/,/", ",.phpinfodisplay ", $i);
		}
		echo "<div class='phpinfodisplay'><style type='text/css'>\n",
		join(
			"\n",
			array_map('wptools_phpnfo_css',  preg_split('/\n/', $matches[1]))
		),
		"</style>\n",
		$matches[2],
		"\n</div>\n";
	}
	function wptools_OSName()
	{
		try {
		  if (false == function_exists("shell_exec") || !wptools_check_if_obd_permitted() || false == @is_readable("/etc/os-release")   ) {
			return false;
		  }
		  $os = shell_exec('cat /etc/os-release | grep "PRETTY_NAME"');
		  return explode("=", $os)[1];
		}
		catch (Exception $e) {
		  // echo 'Message: ' .$e->getMessage();
		  return false;
		}
	}
	function wptools_gopro_callback9()
	{
		$urlgopro = "https://wptoolsplugin.com/premium/";
	?>
		<script type="text/javascript">
			<!--
			window.location = "<?php echo esc_url($urlgopro); ?>";
			-->
		</script>
	<?php
	}

function wptools_error_test($tests)
{
    $tests['direct']['wptools_plugin'] = array(
        'label' => __('WP Memory Test', 'wptools'),
        'test' => 'wptools_memory_test'
    );
    return $tests;
}

 function wptools_site_health() {

		$output = '';

		$get_issues = get_transient( 'health-check-site-status-result' );

		$issue_counts = array();

		if ( false !== $get_issues ) {
			$issue_counts = json_decode( $get_issues, true );
		}

		if ( ! is_array( $issue_counts ) || ! $issue_counts ) {
			$issue_counts = array(
				'good'        => 0,
				'recommended' => 0,
				'critical'    => 0,
			);
		}

		$issues_total = $issue_counts['recommended'] + $issue_counts['critical'];

		$tests_total = $issue_counts['good'] + $issue_counts['recommended'] + ( $issue_counts['critical'] * 1.5 );

		$tests_failed = ( $issue_counts['recommended'] * 0.5 ) + ( $issue_counts['critical'] * 1.5 );

		$tests_score = 0;
		if ( $tests_total > 0 ) {
			$tests_score = 100 - ceil( ( $tests_failed / $tests_total ) * 100 );
		}

		if ( ( 80 <= $tests_score ) && ( 0 === (int) $issue_counts['critical'] ) ) {

			$site_health_dot_class = 'green';

		} else {

			$site_health_dot_class = 'orange';

		}

	
		//$issues_total = 0;
	
	
		// $output .= '<div class="wptools_site-health-dot ' . $site_health_dot_class . '"><span style="display:none;">' . $tests_score . '</span></div>';
		$output .= '<div class="wptools_site-health-dot ' . $site_health_dot_class . '"><span style="display:none;"></span></div>';

		if ( false === $get_issues ) {

			$output .= esc_attr__('Visit the','wptools').'<a href="' . esc_url( admin_url( 'site-health.php' ) ) . '">&nbsp;'.esc_attr__('Site Health screen','wptools').'&nbsp;</a>'.esc_attr__('to perform checks now.','wptools');

		} else {

			if ( $issues_total <= 0 ) {
				$output .= esc_attr__('Great job! Your site currently passes all site health','wptools').'<a href="' . esc_url( admin_url( 'site-health.php' ) ) . '" target="_blank">&nbsp;'.esc_attr__('checks','wptools').'</a>.';
			} elseif ( 1 === (int) $issue_counts['critical'] )  {
			//	$output .= 'Your site has <a href="' . esc_url( admin_url( 'site-health.php' ) ) . '" target="_blank">a critical issue</a> that should be addressed as soon as possible. ';
				$output .= esc_attr__('Your site has','wptools').'&nbsp;<a href="' . esc_url( admin_url( 'site-health.php' ) ) . '" target="_blank">&nbsp'.esc_attr__('a critical issue','wptools').'</a>&nbsp;'.esc_attr__('that should be addressed as soon as possible.','wptools');

			} elseif ( $issue_counts['critical'] > 1 ) {
			//	$output .= 'Your site has <a href="' . esc_url( admin_url( 'site-health.php' ) ) . '" target="_blank">critical issues</a> that should be addressed as soon as possible.';
				$output .= esc_attr__('Your site has a','wptools').'&nbsp;<a href="' . esc_url( admin_url( 'site-health.php' ) ) . '" target="_blank">&nbsp'.esc_attr__('critical issues','wptools').'</a>&nbsp;'.esc_attr__('that should be addressed as soon as possible.','wptools');

			} elseif ( 1 === (int) $issue_counts['recommended'] ) {
				$output .= esc_attr__('Looking good, but','wptools').'&nbsp;<a href="' . esc_url( admin_url( 'site-health.php' ) ) . '" target="_blank">&nbsp'.esc_attr__('one thing','wptools').'</a>&nbsp;'.esc_attr__('can be improved.','wptools');

			} else {
				$output .= esc_attr__('Looking good, but','wptools').'&nbsp;<a href="' . esc_url( admin_url( 'site-health.php' ) ) . '" target="_blank">&nbsp'.esc_attr__('some things','wptools').'</a>&nbsp;'.esc_attr__('can be improved.','wptools');
			}

		}

		return $output;

	}
	?>
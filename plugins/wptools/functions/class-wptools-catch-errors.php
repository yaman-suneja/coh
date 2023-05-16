<?php
if (!defined('ABSPATH')) {
	die('Invalid request.');
}
class wptools_catch_errors
{
	private function __construct()
	{
	}
	public static function init_actions()
	{
		add_action('wp_head', array(__CLASS__, 'fatal_error_handler'));
		add_action('admin_head', array(__CLASS__, 'fatal_error_handler'));
	}
	public static function fatal_error_handler()
	{
?>
		<script type="text/javascript">
			window.onerror = function(msg, url, line, col, error) {
				//console.log('ms: ' + msg);
				var message = [
					msg,
					'URL: ' + url,
					'Line: ' + line,
					'Column: ' + col,
					'Error object: ' + JSON.stringify(error)
				].join(' - ');

				
				var xhr = new XMLHttpRequest();
				var nonce = '<?php echo esc_js(wp_create_nonce('jquery-wptools')); ?>';
				xhr.open('POST', '<?php echo esc_js(admin_url('admin-ajax.php')); ?>');
				xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
				xhr.onload = function() {
					if (200 === xhr.status) {
						try {
							// response = JSON.parse( xhr.response );
						} catch (e) {}
					}
				};
				xhr.send(encodeURI('action=wptools_get_js_errors&_wpnonce=' + nonce + '&wptools_js_error_catched=' + message));
				return true;
			}
		</script>
<?php
	}
}
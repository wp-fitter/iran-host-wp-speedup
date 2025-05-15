<?php defined('ABSPATH') || die;
/**
 * SpeedUp Wordpress Dashboard for Websites hosted in Iran
 *
 * @author            Masoud Golchin
 * @copyright         Masoud Golchin
 * @license           GPL-3.0-or-later
 *
 * @wordpress-plugin
 *
 * Plugin Name:         Iran Host WP Speedup - افزایش سرعت پیشخوان وردپرس در هاست ایران
 * Plugin URI:          https://fabin.agency
 * Description:         Solve connectivity issues with external WordPress services using SOCKS proxy - حل مشکل ارتباط با هاست های خارجی با استفاده از پروکسی SOCKS
 * Version:             1.1.0
 * Requires at least:   5.0
 * Requires PHP:        7.2
 * Author:              Masoud Golchin
 * Author URI:          https://fabin.agency
 * License:             GPL v3
 * License URI:         https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:         iran-host-speedup
 * Domain Path:         /languages
 */

/**
 * Check if a text contains a specific string
 * 
 * @param string $text   Text to search in
 * @param string $string String to search for
 * @return bool          True if string is found, false otherwise
 */
function rtlTextHasString($text, $string) {
	return strpos($text, $string) !== false;
}

/**
 * Handle external requests by routing them through SOCKS proxy
 * for specific domains that are difficult to access from Iran
 * 
 * @param bool   $false       Whether to preempt an HTTP request's return value. Default false.
 * @param array  $parsed_args HTTP request arguments.
 * @param string $url         The request URL.
 * @return array|bool         False to let WordPress handle the request, or a response array to short-circuit
 */
function rtlBlockExternalHostRequests($false, $parsed_args, $url) {
	// Get plugin options
	$options = get_option('iran_host_speedup_options', array());
	
	// Default hosts list if not set in options
	$default_hosts = array(
		'elementor.com',
		'github.com',
		'yoast.com',
		'yoa.st',
		'api.wordpress.org',
		'w.org',
		'unyson.io',
		'siteorigin.com',
		'woocommerce.com',
		'rankmath.com',
		'akismet.com'
	);
	
	// Get hosts from options or use defaults
	$proxiedHosts = isset($options['hosts']) && !empty($options['hosts']) ? 
		array_map('trim', explode(',', $options['hosts'])) : $default_hosts;

	// Check if URL contains any of the hosts that need proxy
	$needsProxy = false;
	foreach ($proxiedHosts as $host) {
		if (!empty($host) && rtlTextHasString($url, $host)) {
			$needsProxy = true;
			break;
		}
	}

	// Check if proxy is enabled and if we have proxy settings
	$proxy_enabled = isset($options['proxy_enabled']) ? $options['proxy_enabled'] : true;
	$proxy_host = isset($options['proxy_host']) ? $options['proxy_host'] : '';
	$proxy_port = isset($options['proxy_port']) ? $options['proxy_port'] : '';
	$proxy_username = isset($options['proxy_username']) ? $options['proxy_username'] : '';
	$proxy_password = isset($options['proxy_password']) ? $options['proxy_password'] : '';

	// If proxy is not enabled or no host/port is set, skip proxy
	if (!$proxy_enabled || empty($proxy_host) || empty($proxy_port)) {
		return $false;
	}

	if ($needsProxy) {
		$context = [
			'http' => [
				'proxy' => 'socks5://' . (!empty($proxy_username) ? $proxy_username . ':' . $proxy_password . '@' : '') . $proxy_host . ':' . $proxy_port,
				'request_fulluri' => true,
			],
		];

		if (!empty($parsed_args['timeout'])) {
			$context['http']['timeout'] = $parsed_args['timeout'];
		}

		// Create a stream context with our proxy settings
		$stream_context = stream_context_create($context);

		// Get response using the proxy
		$response = @file_get_contents($url, false, $stream_context);

		// If we successfully got a response
		if ($response !== false) {
			// Get response headers
			$response_headers = [];
			foreach ($http_response_header as $header) {
				$response_headers[] = $header;
			}

			// Return response in the format WordPress expects
			return [
				'headers' => $response_headers,
				'body' => $response,
				'response' => [
					'code' => 200,
					'message' => 'OK',
				],
				'cookies' => [],
				'filename' => '',
			];
		}
	}

	return $false;
}

/**
 * Add the admin menu item
 */
function iran_host_speedup_admin_menu() {
	add_options_page(
		'تنظیمات افزایش سرعت وردپرس در هاست ایران', 
		'افزایش سرعت وردپرس',
		'manage_options',
		'iran-host-speedup',
		'iran_host_speedup_settings_page'
	);
}

/**
 * Register plugin settings
 */
function iran_host_speedup_register_settings() {
	register_setting('iran_host_speedup_options_group', 'iran_host_speedup_options', 'iran_host_speedup_validate_options');
}

/**
 * Validate plugin options
 */
function iran_host_speedup_validate_options($input) {
	$output = array();
	
	$output['proxy_enabled'] = isset($input['proxy_enabled']) ? true : false;
	
	$output['proxy_host'] = sanitize_text_field($input['proxy_host']);
	
	$output['proxy_port'] = intval($input['proxy_port']);
	
	$output['proxy_username'] = sanitize_text_field($input['proxy_username']);
	
	$output['proxy_password'] = sanitize_text_field($input['proxy_password']);
	
	$output['hosts'] = sanitize_textarea_field($input['hosts']);
	
	return $output;
}

/**
 * Create settings page
 */
function iran_host_speedup_settings_page() {
	// Get saved options
	$options = get_option('iran_host_speedup_options', array());
	
	// Set default values
	$proxy_enabled = isset($options['proxy_enabled']) ? $options['proxy_enabled'] : true;
	$proxy_host = isset($options['proxy_host']) ? $options['proxy_host'] : '';
	$proxy_port = isset($options['proxy_port']) ? $options['proxy_port'] : '';
	$proxy_username = isset($options['proxy_username']) ? $options['proxy_username'] : '';
	$proxy_password = isset($options['proxy_password']) ? $options['proxy_password'] : '';
	
	// Default hosts
	$default_hosts = array(
		'elementor.com',
		'github.com',
		'yoast.com',
		'yoa.st',
		'api.wordpress.org',
		'w.org',
		'unyson.io',
		'siteorigin.com',
		'woocommerce.com',
		'rankmath.com',
		'akismet.com'
	);
	
	$hosts = isset($options['hosts']) ? $options['hosts'] : implode(",\n", $default_hosts);
	?>
	<div class="wrap">
		<h2>تنظیمات افزایش سرعت وردپرس در هاست ایران</h2>
		<form method="post" action="options.php">
			<?php settings_fields('iran_host_speedup_options_group'); ?>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row">فعال بودن پروکسی</th>
					<td>
						<label for="iran_host_speedup_options[proxy_enabled]">
							<input type="checkbox" id="iran_host_speedup_options[proxy_enabled]" name="iran_host_speedup_options[proxy_enabled]" value="1" <?php checked($proxy_enabled); ?> />
							استفاده از پروکسی برای دسترسی به سرویس‌های خارجی
						</label>
					</td>
				</tr>
				<tr>
					<th scope="row">آدرس پروکسی</th>
					<td>
						<input type="text" id="iran_host_speedup_options[proxy_host]" name="iran_host_speedup_options[proxy_host]" value="<?php echo esc_attr($proxy_host); ?>" class="regular-text" placeholder="مثال: 127.0.0.1" />
						<p class="description">آدرس IP سرور پروکسی SOCKS5</p>
					</td>
				</tr>
				<tr>
					<th scope="row">پورت پروکسی</th>
					<td>
						<input type="number" id="iran_host_speedup_options[proxy_port]" name="iran_host_speedup_options[proxy_port]" value="<?php echo esc_attr($proxy_port); ?>" class="small-text" placeholder="مثال: 9050" />
						<p class="description">پورت سرور پروکسی SOCKS5</p>
					</td>
				</tr>
				<tr>
					<th scope="row">نام کاربری پروکسی (اختیاری)</th>
					<td>
						<input type="text" id="iran_host_speedup_options[proxy_username]" name="iran_host_speedup_options[proxy_username]" value="<?php echo esc_attr($proxy_username); ?>" class="regular-text" />
						<p class="description">نام کاربری برای احراز هویت پروکسی (در صورت نیاز)</p>
					</td>
				</tr>
				<tr>
					<th scope="row">رمز عبور پروکسی (اختیاری)</th>
					<td>
						<input type="password" id="iran_host_speedup_options[proxy_password]" name="iran_host_speedup_options[proxy_password]" value="<?php echo esc_attr($proxy_password); ?>" class="regular-text" />
						<p class="description">رمز عبور برای احراز هویت پروکسی (در صورت نیاز)</p>
					</td>
				</tr>
				<tr>
					<th scope="row">دامنه‌های نیازمند پروکسی</th>
					<td>
						<textarea id="iran_host_speedup_options[hosts]" name="iran_host_speedup_options[hosts]" rows="10" cols="50" class="large-text code"><?php echo esc_textarea($hosts); ?></textarea>
						<p class="description">هر دامنه را در یک خط جداگانه یا با کاما جدا کنید</p>
					</td>
				</tr>
			</table>
			<?php submit_button('ذخیره تنظیمات'); ?>
		</form>
	</div>
	<?php
}

/**
 * Initialize the plugin
 */
function iran_host_speedup_init() {
	// Hook our function to the pre_http_request filter
	add_filter('pre_http_request', 'rtlBlockExternalHostRequests', 10, 3);
}

// Add settings page
add_action('admin_menu', 'iran_host_speedup_admin_menu');
add_action('admin_init', 'iran_host_speedup_register_settings');

// Initialize plugin
add_action('init', 'iran_host_speedup_init');
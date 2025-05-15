# Iran Host WP Speedup

A WordPress plugin to solve connectivity issues with external WordPress services for websites hosted on Iranian servers.

## Description

Iran Host WP Speedup resolves connectivity problems that WordPress websites hosted in Iran frequently encounter when trying to access external WordPress services, themes, and plugin repositories. The plugin uses a SOCKS proxy to bypass connection limitations and significantly improves dashboard performance.

## Features

- Improves connectivity to WordPress.org APIs
- Enhances access to plugin and theme repositories
- Speeds up WordPress dashboard operations
- Resolves connection issues with popular services like Elementor, WooCommerce, Yoast, etc.
- **User-configurable proxy settings**
- Customizable list of domains requiring proxy

## Installation

1. Upload the plugin files to the `/wp-content/plugins/iran-host-speedup` directory, or install the plugin through the WordPress plugins screen directly
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Go to Settings > افزایش سرعت وردپرس to configure your SOCKS5 proxy settings
4. The plugin will handle connectivity issues based on your configuration

## Configuration

After installation, you need to provide your own SOCKS5 proxy details:

1. Go to WordPress admin area > Settings > افزایش سرعت وردپرس
2. Enter your SOCKS5 proxy host (IP address)
3. Enter the proxy port
4. If your proxy requires authentication, enter username and password
5. Customize the list of domains requiring proxy connection (optional)
6. Save settings

## Supported Services

The plugin currently helps connect to the following services:
- WordPress.org API
- Elementor
- GitHub
- Yoast SEO
- WooCommerce
- Akismet
- Rank Math
- SiteOrigin
- And more...

You can add or remove domains in the plugin settings page.

## Requirements

- WordPress 5.0 or higher
- PHP 7.2 or higher
- A working SOCKS5 proxy server

## Changelog

### 1.1.0
- Added settings page for configurable proxy settings
- Added customizable domains list
- Initial public release on GitHub

## License

This plugin is licensed under the GPL v3.

## Author

Developed by [Masoud Golchin](https://fabin.agency)

## Support

For support, please open an issue on the [GitHub repository](https://github.com/wp-fitter/iran-host-wp-speedup) or contact the developer.

## Persian Description

افزونه‌ای برای حل مشکل ارتباط با هاست‌های خارجی وردپرس برای سایت‌هایی که روی سرورهای داخل ایران میزبانی می‌شوند. این افزونه با استفاده از پروکسی SOCKS، مشکلات دسترسی به سرویس‌های خارجی را حل کرده و سرعت پیشخوان وردپرس را بهبود می‌بخشد. کاربران می‌توانند تنظیمات پروکسی و دامنه‌های مورد نیاز را از صفحه تنظیمات افزونه مشخص کنند. 
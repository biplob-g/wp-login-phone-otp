=== WP WhatsApp OTP Login ===
Contributors: Biplob Ghatak
Tags: whatsapp, otp, login, authentication, woocommerce, security, registration
Requires at least: 5.0
Tested up to: 6.4
Stable tag: 1.0
Requires PHP: 7.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Replace traditional WordPress login with secure WhatsApp OTP verification. Seamless integration with WooCommerce and customizable styling.

== Description ==

WP WhatsApp OTP Login modernizes your WordPress authentication by replacing the traditional username/password system with WhatsApp-based OTP verification. Perfect for e-commerce sites and membership platforms seeking enhanced security and user experience.

= 🔑 Key Features =

* WhatsApp number-based login and registration
* Secure OTP verification via WhatsApp messages
* Full WooCommerce integration
* Customizable button styling
* User profile WhatsApp field
* Admin settings panel
* International number support

= 🛒 WooCommerce Integration =

* Seamlessly replaces WooCommerce login/register forms
* Automatically maps WhatsApp numbers to billing information
* Removes password fields from checkout
* Streamlines customer experience

= 🎨 Customization Options =

* Button background color
* Text color
* Border radius
* Padding
* Hover effects

= 🔒 Security Features =

* 10-minute OTP expiration
* WordPress nonce protection
* Secure WhatsApp number storage
* Input sanitization
* API security measures

= ⚠️ Important Note =

This plugin completely replaces the traditional WordPress login system. Once activated:
* Username/password login is removed
* Email/password registration is disabled
* "Lost your password?" feature is removed

= 🔧 Technical Requirements =

* WordPress 5.0 or higher
* PHP 7.0 or higher
* Active WhatsApp Business API subscription
* SSL certificate (recommended)

== Installation ==

1. Upload `wp-whatsapp-otp-login` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Settings > WhatsApp OTP
4. Configure your WhatsApp Business API credentials
5. Customize button styling as needed

== Frequently Asked Questions ==

= Can I use any WhatsApp Business API provider? =
Yes, the plugin works with any WhatsApp Business API service provider.

= Can I customize the WhatsApp OTP messages? =
Currently, messages are template-based. Message customization is planned for future updates.

= Is this compatible with WooCommerce? =
Yes, the plugin fully integrates with WooCommerce login, registration, and checkout processes.

= What happens if a user doesn't receive the OTP? =
Users can request a new OTP, which invalidates any previous ones.

= Can I keep the traditional login alongside WhatsApp OTP? =
No, this plugin completely replaces the traditional WordPress login system.

= Does it support international WhatsApp numbers? =
Yes, the plugin supports international number formats with country codes.

== Screenshots ==

1. WhatsApp OTP Login Form
2. Admin Settings Panel
3. Button Customization Options
4. WooCommerce Integration
5. User Profile WhatsApp Field

== Changelog ==

= 1.0 =
* Initial release
* WhatsApp OTP authentication system
* WooCommerce integration
* Customizable button styling
* International number support
* Admin settings panel

== Upgrade Notice ==

= 1.0 =
Initial release of WP WhatsApp OTP Login

== Support ==

For support or feature requests, please visit our [GitHub repository](https://github.com/biplob-g/wp-whatsapp-login-otp) or contact support.

== Credits ==

Developed by [Biplob Ghatak](https://github.com/biplob-g)
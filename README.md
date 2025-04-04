# WP Phone OTP Login

[![WordPress](https://img.shields.io/wordpress/v/wp-phone-otp-login.svg)](https://wordpress.org/plugins/wp-phone-otp-login/)
[![License](https://img.shields.io/badge/license-GPL--2.0%2B-blue.svg)](https://www.gnu.org/licenses/gpl-2.0.html)

## 📱 Description

WP Phone OTP Login replaces the traditional WordPress authentication system with a phone-based OTP verification process. Users can register and login using their phone numbers, making the process more secure and user-friendly.

> **Important:** This plugin completely removes the standard WordPress login, registration, and password reset functionality.

## ✨ Features

* 📱 Phone number-based login/registration
* 🔐 OTP verification via SMS
* 🎨 Customizable button styling
* 🛒 WooCommerce integration
* 👤 User profile phone field
* ⚙️ Admin settings panel
* 🚫 Removes traditional login system

## ⚠️ Important Note

Once activated, this plugin:
* ❌ Removes username/password login
* ❌ Disables email/password registration
* ❌ Removes "Lost your password?" feature

## 🚀 Installation

1. Upload `wp-phone-otp-login` to `/wp-content/plugins/`
2. Activate via WordPress plugins menu
3. Navigate to Settings > Phone OTP
4. Configure API settings and customize styling

## ⚙️ Configuration

### API Settings Required
* `API Key` - Your SMS provider's API key
* `Base URL` - API endpoint URL
* `Company ID` - Your provider's company identifier

### Button Styling Options
* Background Color
* Text Color
* Border Radius
* Padding
* Hover Effects

## 📖 Usage

### Users
1. Visit WordPress login page
2. Enter phone number
3. Click "Send OTP"
4. Input received OTP
5. Complete registration if new user

### Administrators
1. Set up API credentials
2. Customize button appearance
3. Manage user phone numbers

## 🛒 WooCommerce Integration

* Replaces default login/register forms
* Maps phone numbers to billing info
* Removes password fields from checkout
* Streamlines customer experience

## 🔒 Security Features

* 10-minute OTP expiration
* WordPress nonce protection
* Secure phone number storage
* Input sanitization
* API security measures

## 💻 Technical Requirements

* WordPress 5.0+
* PHP 7.0+
* Active SMS API subscription
* SSL recommended

## ❓ FAQ

**Q: Works with any SMS provider?**
A: Yes, compatible with any HTTP API-based SMS service.

**Q: Can I customize OTP messages?**
A: Currently template-based. Customization coming in future versions.

**Q: WooCommerce compatible?**
A: Yes, fully integrated with WooCommerce features.

**Q: OTP not received?**
A: Users can request new OTP, invalidating previous ones.

**Q: Keep traditional login?**
A: No, plugin completely replaces default WordPress login.

**Q: Existing users?**
A: Can login with phone numbers once added to their profiles.

## 📝 Changelog

### 1.0
* Initial release
* Phone OTP system
* WooCommerce integration
* Button customization
* WordPress login replacement

## 🤝 Support

Need help? Found a bug? Visit our [GitHub repository](https://github.com/your-username/wp-phone-otp-login) or contact support.

## 👨‍💻 Credits

Developed by [Biplob Ghatak](https://github.com/your-username)

## 📄 License

GPL v2 or later. See [License](https://www.gnu.org/licenses/gpl-2.0.html) for details.

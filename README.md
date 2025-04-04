# WP WhatsApp OTP Login


[![License](https://img.shields.io/badge/license-GPL--2.0%2B-blue.svg)](https://www.gnu.org/licenses/gpl-2.0.html)

## 💬 Description

WP WhatsApp OTP Login replaces the traditional WordPress authentication system with a WhatsApp-based OTP verification process. Users can register and login using their WhatsApp numbers, making the process more secure and user-friendly.

> **Important:** This plugin completely removes the standard WordPress login, registration, and password reset functionality.

## ✨ Features

* 💬 WhatsApp number-based login/registration
* 🔐 OTP verification via WhatsApp message
* 🎨 Customizable button styling
* 🛒 WooCommerce integration
* 👤 User profile WhatsApp field
* ⚙️ Admin settings panel
* 🚫 Removes traditional login system

## ⚠️ Important Note

Once activated, this plugin:
* ❌ Removes username/password login
* ❌ Disables email/password registration
* ❌ Removes "Lost your password?" feature

## 🚀 Installation

1. Upload `wp whatsapp otp` to `/wp-content/plugins/`
2. Activate via WordPress plugins menu
3. Navigate to Settings > WhatsApp OTP
4. Configure API settings and customize styling

## ⚙️ Configuration

### API Settings Required
* `API Key` - Your WhatsApp Business API key
* `Base URL` - WhatsApp API endpoint URL
* `Company ID` - Your WhatsApp Business account identifier

### Button Styling Options
* Background Color
* Text Color
* Border Radius
* Padding
* Hover Effects

## 📖 Usage

### Users
1. Visit WordPress login page
2. Enter WhatsApp number
3. Click "Send OTP"
4. Receive OTP via WhatsApp message
5. Complete registration if new user

### Administrators
1. Set up WhatsApp Business API credentials
2. Customize button appearance
3. Manage user WhatsApp numbers

## 🛒 WooCommerce Integration

* Replaces default login/register forms
* Maps WhatsApp numbers to billing info
* Removes password fields from checkout
* Streamlines customer experience

## 🔒 Security Features

* 10-minute OTP expiration
* WordPress nonce protection
* Secure WhatsApp number storage
* Input sanitization
* API security measures

## 💻 Technical Requirements

* WordPress 5.0+
* PHP 7.0+
* Active WhatsApp Business API subscription
* SSL recommended

## ❓ FAQ

**❓ Q: Can I use any WhatsApp Business API provider?**  
Absolutely! this plugin is designed to work seamlessly with any WhatsApp Business API service provider, giving you the flexibility you need.

**✏️ Q: Is it possible to customize WhatsApp OTP messages?**  
At the moment, the messages are template-based, but stay tuned! Customization options are on the horizon for future updates.

**🛒 Q: Is this plugin compatible with WooCommerce?**  
Yes, indeed! It’s fully integrated with WooCommerce, enhancing your e-commerce experience.

**📩 Q: What if I don’t receive the OTP on WhatsApp?**  
No worries! Users can easily request a new OTP, which will invalidate any previous ones, ensuring you always have access.

**🔒 Q: Can I keep the traditional login method?**  
Unfortunately, no. This plugin completely replaces the default WordPress login, streamlining the process for your users.

**👤 Q: What about existing users?**  
Existing users can log in using their WhatsApp numbers once they’ve been added to their profiles, making the transition smooth.

**🌍 Q: Are international WhatsApp numbers supported?**  
Absolutely! this plugin supports international formats, including country codes, so you can connect with users worldwide.

## 📝 Changelog

### 1.0
* Initial release
* WhatsApp OTP system
* WooCommerce integration
* Button customization
* WordPress login replacement

## 🤝 Support

Need help? Found a bug? Visit our [GitHub repository](https://github.com/biplob-g/wp-whatsapp-login-otp) or contact support.

## 👨‍💻 Credits

Developed by [Biplob Ghatak](https://github.com/biplob-g)

## 📄 License

GPL v2 or later. See [License](https://www.gnu.org/licenses/gpl-2.0.html) for details.

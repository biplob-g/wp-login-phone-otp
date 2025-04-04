<?php
/**
 * Plugin Name: WP WhatsApp OTP Login
 * Description: Custom login and registration using phone number and OTP
 * Version: 1.0
 * Author: Biplob Ghatak
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Main plugin class
 */
class otp_Phone_Login {
    
    private $options;
    
    public function __construct() {
        // Load settings
        $this->options = get_option('otp_phone_login_options', array(
            'api_key' => '',
            'base_url' => '',
            'company_id' => '',
            'button_bg_color' => '#750092',
            'button_text_color' => '#ffffff',
            'button_border_radius' => '30px',
            'button_padding' => '10px 15px',
            'button_hover_bg_color' => '#620079'
        ));
        
        // Show admin notice if settings are missing
        add_action('admin_notices', array($this, 'display_missing_settings_notice'));
        
        // IMPORTANT: Force create the database column right away
        $this->create_phone_column();
        
        // Register activation hook (for new installations)
        register_activation_hook(__FILE__, array($this, 'activate_plugin'));
        
        // Add admin menu
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        
        // AJAX handlers
        add_action('wp_ajax_nopriv_send_otp', array($this, 'send_otp'));
        add_action('wp_ajax_nopriv_verify_otp', array($this, 'verify_otp'));
        add_action('wp_ajax_nopriv_register_user', array($this, 'register_user'));
        
        // Add phone field to user profile
        add_action('show_user_profile', array($this, 'add_phone_field_to_profile'));
        add_action('edit_user_profile', array($this, 'add_phone_field_to_profile'));
        add_action('personal_options_update', array($this, 'save_phone_field'));
        add_action('edit_user_profile_update', array($this, 'save_phone_field'));
        
        // Enqueue scripts and styles
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('login_enqueue_scripts', array($this, 'enqueue_scripts'));
        
        // Replace WordPress login/register pages
        add_action('login_form_login', array($this, 'override_wp_login_page'));
        add_action('login_form_register', array($this, 'override_wp_register_page'));
        
        // Disable password reset
        add_filter('allow_password_reset', '__return_false');
        add_filter('show_password_fields', '__return_false');
        add_action('login_form_lostpassword', array($this, 'disable_password_reset'));
        
        // Map user fields to WooCommerce billing fields
        add_action('user_register', array($this, 'map_user_to_woocommerce'));
        add_action('profile_update', array($this, 'map_user_to_woocommerce'));
        
        // WooCommerce integration
        add_action('init', array($this, 'woocommerce_integration'));
    }
    
    /**
     * Display admin notice if settings are missing
     */
    public function display_missing_settings_notice() {
        // Only show to admins
        if (!current_user_can('manage_options')) {
            return;
        }
        
        $missing = array();
        
        if (empty($this->options['api_key'])) {
            $missing[] = 'API Key';
        }
        
        if (empty($this->options['base_url'])) {
            $missing[] = 'Base URL';
        }
        
        if (empty($this->options['company_id'])) {
            $missing[] = 'Company ID';
        }
        
        if (!empty($missing)) {
            $setting_page = admin_url('options-general.php?page=otp-phone-login');
            echo '<div class="notice notice-error is-dismissible">';
            echo '<p><strong>Phone OTP Login Configuration Error:</strong> The following required settings are missing: ' . implode(', ', $missing) . '.</p>';
            echo '<p>Please <a href="' . esc_url($setting_page) . '">complete the configuration</a> for the Phone OTP Login plugin to work properly.</p>';
            echo '</div>';
        }
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_options_page(
            'Phone OTP Settings',
            'Phone OTP',
            'manage_options',
            'otp-phone-login',
            array($this, 'settings_page')
        );
    }
    
    /**
     * Register settings
     */
    public function register_settings() {
        register_setting(
            'otp_phone_login',
            'otp_phone_login_options',
            array($this, 'sanitize_settings')
        );
        
        add_settings_section(
            'otp_phone_login_section',
            'API Settings',
            function() {
                echo '<p>Configure the API settings for the Phone OTP Login plugin.</p>';
            },
            'otp-phone-login'
        );
        
        add_settings_field(
            'api_key',
            'API Key',
            array($this, 'api_key_field'),
            'otp-phone-login',
            'otp_phone_login_section'
        );
        
        add_settings_field(
            'base_url',
            'Base URL',
            array($this, 'base_url_field'),
            'otp-phone-login',
            'otp_phone_login_section'
        );
        
        add_settings_field(
            'company_id',
            'Company ID',
            array($this, 'company_id_field'),
            'otp-phone-login',
            'otp_phone_login_section'
        );
        // Button Styling Section
    add_settings_section(
        'otp_button_styling_section',
        'Button Styling',
        function() {
            echo '<p>Customize the appearance of buttons used in the login/registration form.</p>';
        },
        'otp-phone-login'
    );
    
    add_settings_field(
        'button_bg_color',
        'Button Background Color',
        array($this, 'button_bg_color_field'),
        'otp-phone-login',
        'otp_button_styling_section'
    );
    
    add_settings_field(
        'button_text_color',
        'Button Text Color',
        array($this, 'button_text_color_field'),
        'otp-phone-login',
        'otp_button_styling_section'
    );
    
    add_settings_field(
        'button_border_radius',
        'Button Border Radius',
        array($this, 'button_border_radius_field'),
        'otp-phone-login',
        'otp_button_styling_section'
    );
    
    add_settings_field(
        'button_padding',
        'Button Padding',
        array($this, 'button_padding_field'),
        'otp-phone-login',
        'otp_button_styling_section'
    );
    
    add_settings_field(
        'button_hover_bg_color',
        'Button Hover Background Color',
        array($this, 'button_hover_bg_color_field'),
        'otp-phone-login',
        'otp_button_styling_section'
    );
    }

    /**
 * Button background color field
 */
public function button_bg_color_field() {
    $value = isset($this->options['button_bg_color']) ? $this->options['button_bg_color'] : '#750092';
    echo '<input type="color" id="button_bg_color" name="otp_phone_login_options[button_bg_color]" value="' . esc_attr($value) . '" />';
    echo '<p class="description">Choose the background color for buttons.</p>';
}

/**
 * Button text color field
 */
public function button_text_color_field() {
    $value = isset($this->options['button_text_color']) ? $this->options['button_text_color'] : '#ffffff';
    echo '<input type="color" id="button_text_color" name="otp_phone_login_options[button_text_color]" value="' . esc_attr($value) . '" />';
    echo '<p class="description">Choose the text color for buttons.</p>';
}

/**
 * Button border radius field
 */
public function button_border_radius_field() {
    $value = isset($this->options['button_border_radius']) ? $this->options['button_border_radius'] : '30px';
    echo '<input type="text" id="button_border_radius" name="otp_phone_login_options[button_border_radius]" value="' . esc_attr($value) . '" class="regular-text" />';
    echo '<p class="description">Set the border radius for buttons (e.g., 5px, 10px, 30px).</p>';
}

/**
 * Button padding field
 */
public function button_padding_field() {
    $value = isset($this->options['button_padding']) ? $this->options['button_padding'] : '10px 15px';
    echo '<input type="text" id="button_padding" name="otp_phone_login_options[button_padding]" value="' . esc_attr($value) . '" class="regular-text" />';
    echo '<p class="description">Set the padding for buttons (e.g., 10px 15px).</p>';
}

/**
 * Button hover background color field
 */
public function button_hover_bg_color_field() {
    $value = isset($this->options['button_hover_bg_color']) ? $this->options['button_hover_bg_color'] : '#620079';
    echo '<input type="color" id="button_hover_bg_color" name="otp_phone_login_options[button_hover_bg_color]" value="' . esc_attr($value) . '" />';
    echo '<p class="description">Choose the background color for buttons when hovered.</p>';
}
    
    /**
     * API Key field
     */
    public function api_key_field() {
        $value = isset($this->options['api_key']) ? $this->options['api_key'] : '';
        echo '<input type="text" id="api_key" name="otp_phone_login_options[api_key]" value="' . esc_attr($value) . '" class="regular-text" />';
        echo '<p class="description">Enter your API Key provided by the service provider.</p>';
    }
    
    /**
     * Base URL field
     */
    public function base_url_field() {
        $value = isset($this->options['base_url']) ? $this->options['base_url'] : '';
        echo '<input type="text" id="base_url" name="otp_phone_login_options[base_url]" value="' . esc_attr($value) . '" class="regular-text" />';
        echo '<p class="description">Enter the base URL for the API (e.g., https://api.provider.com).</p>';
    }
    
    /**
     * Company ID field
     */
    public function company_id_field() {
        $value = isset($this->options['company_id']) ? $this->options['company_id'] : '';
        echo '<input type="text" id="company_id" name="otp_phone_login_options[company_id]" value="' . esc_attr($value) . '" class="regular-text" />';
        echo '<p class="description">Enter your Company ID provided by the service provider.</p>';
    }
    
    /**
     * Sanitize settings
     */
  /**
 * Sanitize settings
 */
public function sanitize_settings($input) {
    $new_input = array();
    $errors = false;
    
    // API Key validation - allow empty value but show warning
    if (empty($input['api_key'])) {
        add_settings_error(
            'otp_phone_login_options',
            'api_key_warning',
            'Warning: API Key is empty. The plugin will not function without a valid API Key.',
            'warning'
        );
        $new_input['api_key'] = '';
    } else {
        $new_input['api_key'] = sanitize_text_field($input['api_key']);
    }
    
    // Base URL validation - allow empty value but show warning
    if (empty($input['base_url'])) {
        add_settings_error(
            'otp_phone_login_options',
            'base_url_warning',
            'Warning: Base URL is empty. The plugin will not function without a valid Base URL.',
            'warning'
        );
        $new_input['base_url'] = '';
    } else {
        $new_input['base_url'] = esc_url_raw($input['base_url']);
        
        // Check if the URL is valid when provided
        if (!filter_var($new_input['base_url'], FILTER_VALIDATE_URL)) {
            add_settings_error(
                'otp_phone_login_options',
                'base_url_invalid',
                'Error: Base URL format is not valid. Please provide a correct URL format.',
                'error'
            );
            $errors = true;
        }
    }
    
    // Company ID validation - allow empty value but show warning
    if (empty($input['company_id'])) {
        add_settings_error(
            'otp_phone_login_options',
            'company_id_warning',
            'Warning: Company ID is empty. The plugin will not function without a valid Company ID.',
            'warning'
        );
        $new_input['company_id'] = '';
    } else {
        $new_input['company_id'] = sanitize_text_field($input['company_id']);
    }
    
    // Button styling fields - retain default values if not set
    $new_input['button_bg_color'] = isset($input['button_bg_color']) ? sanitize_hex_color($input['button_bg_color']) : '#750092';
    $new_input['button_text_color'] = isset($input['button_text_color']) ? sanitize_hex_color($input['button_text_color']) : '#ffffff';
    $new_input['button_border_radius'] = isset($input['button_border_radius']) ? sanitize_text_field($input['button_border_radius']) : '30px';
    $new_input['button_padding'] = isset($input['button_padding']) ? sanitize_text_field($input['button_padding']) : '10px 15px';
    $new_input['button_hover_bg_color'] = isset($input['button_hover_bg_color']) ? sanitize_hex_color($input['button_hover_bg_color']) : '#620079';
    
    // Return the new input regardless of errors since we're allowing empty values
    return $new_input;
}
    
    /**
     * Settings page
     */
    public function settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields('otp_phone_login');
                do_settings_sections('otp-phone-login');
                submit_button('Save Settings');
                ?>
            </form>
        </div>
        <?php
    }

    public function create_phone_column() {
        global $wpdb;
        
        try {
            // Check if the column already exists
            $column_exists = $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM INFORMATION_SCHEMA.COLUMNS 
                WHERE TABLE_SCHEMA = %s 
                AND TABLE_NAME = %s
                AND COLUMN_NAME = 'user_phone'",
                DB_NAME,
                $wpdb->users
            ));
            
            // Add column if it doesn't exist
            if (empty($column_exists)) {
                $result = $wpdb->query("ALTER TABLE {$wpdb->users} ADD COLUMN user_phone VARCHAR(20) DEFAULT NULL");
                
                if ($result === false) {
                    error_log('Failed to add user_phone column: ' . $wpdb->last_error);
                } else {
                    // Add an index for faster lookups
                    $wpdb->query("ALTER TABLE {$wpdb->users} ADD INDEX idx_user_phone (user_phone)");
                    error_log('Successfully added user_phone column to wp_users table');
                }
            }
        } catch (Exception $e) {
            error_log('Exception when creating phone column: ' . $e->getMessage());
        }
    }

    /**
     * Plugin activation: Add user_phone column to wp_users table
     */
    public function activate_plugin() {
        global $wpdb;
        
        // Check if the column already exists
        $column_exists = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = %s 
            AND TABLE_NAME = %s
            AND COLUMN_NAME = 'user_phone'",
            DB_NAME,
            $wpdb->users
        ));
        
        // Add column if it doesn't exist
        if (empty($column_exists)) {
            $wpdb->query("ALTER TABLE {$wpdb->users} ADD COLUMN user_phone VARCHAR(20) DEFAULT NULL");
            // Add an index for faster lookups
            $wpdb->query("ALTER TABLE {$wpdb->users} ADD INDEX idx_user_phone (user_phone)");
        }
    }
    
    /**
     * Plugin deactivation
     */
    public function deactivate_plugin() {
        // We'll keep the column for data preservation
    }
    
    /**
     * Add phone field to user profile in WordPress admin
     */
    public function add_phone_field_to_profile($user) {
        global $wpdb;
        $phone = $wpdb->get_var($wpdb->prepare(
            "SELECT user_phone FROM {$wpdb->users} WHERE ID = %d",
            $user->ID
        ));
        ?>
        <h3>Phone Number</h3>
        <table class="form-table">
            <tr>
                <th><label for="user_phone">Phone Number</label></th>
                <td>
                    <input type="tel" name="user_phone" id="user_phone" value="<?php echo esc_attr($phone); ?>" class="regular-text" />
                    <p class="description">User's phone number for OTP login.</p>
                </td>
            </tr>
        </table>
        <?php
    }
    
    /**
     * Save phone field from user profile
     */
    public function save_phone_field($user_id) {
        if (!current_user_can('edit_user', $user_id)) {
            return false;
        }
        
        if (isset($_POST['user_phone'])) {
            $phone = sanitize_text_field($_POST['user_phone']);
            
            global $wpdb;
            $result = $wpdb->update(
                $wpdb->users,
                array('user_phone' => $phone),
                array('ID' => $user_id),
                array('%s'),
                array('%d')
            );
            
            if ($result === false) {
                error_log('Failed to update user_phone: ' . $wpdb->last_error);
            }
            
            // Also update WooCommerce billing phone
            update_user_meta($user_id, 'billing_phone', $phone);
        }
    }
    
    /**
     * Enqueue scripts and styles
     */
    public function enqueue_scripts() {
        wp_enqueue_script('jquery');
        
        wp_enqueue_script(
            'phone-login-js', 
            plugin_dir_url(__FILE__) . 'js/phone-login.js', 
            array('jquery'), 
            time(), 
            true
        );
        
        wp_localize_script('phone-login-js', 'phone_login_vars', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('phone-login-nonce')
        ));
        
        wp_enqueue_style(
            'phone-login-css', 
            plugin_dir_url(__FILE__) . 'css/phone-login.css', 
            array(), 
            time()
        );
        // Add inline CSS for button styling
    $button_bg_color = isset($this->options['button_bg_color']) ? $this->options['button_bg_color'] : '#750092';
    $button_text_color = isset($this->options['button_text_color']) ? $this->options['button_text_color'] : '#ffffff';
    $button_border_radius = isset($this->options['button_border_radius']) ? $this->options['button_border_radius'] : '30px';
    $button_padding = isset($this->options['button_padding']) ? $this->options['button_padding'] : '10px 15px';
    $button_hover_bg_color = isset($this->options['button_hover_bg_color']) ? $this->options['button_hover_bg_color'] : '#620079';
    
    $custom_css = "
        .form-group button {
            background: {$button_bg_color} !important;
            color: {$button_text_color} !important;
            border-radius: {$button_border_radius} !important;
            padding: {$button_padding} !important;
        }
        .form-group button:hover {
            background: {$button_hover_bg_color} !important;
        }
    ";
    
    wp_add_inline_style('phone-login-css', $custom_css);
    }
    
    /**
     * WooCommerce integration
     */
    public function woocommerce_integration() {
        if (class_exists('WooCommerce')) {
            // Override WooCommerce login/register forms
            add_action('woocommerce_login_form_start', array($this, 'wc_start_phone_form'));
            add_action('woocommerce_login_form_end', array($this, 'wc_end_phone_form'));
            add_action('woocommerce_register_form_start', array($this, 'wc_start_phone_form'));
            add_action('woocommerce_register_form_end', array($this, 'wc_end_phone_form'));
            
            // Remove WooCommerce password fields from checkout
            add_filter('woocommerce_checkout_fields', array($this, 'remove_checkout_fields'));
        }
    }
    
    /**
     * Send OTP via AJAX
     */
    public function send_otp() {
        check_ajax_referer('phone-login-nonce', 'nonce');
        
        $phone = sanitize_text_field($_POST['phone']);
        
        if (empty($phone)) {
            wp_send_json_error(array('message' => 'Phone number is required'));
            return;
        }
        
        // Check if configuration is complete
        if (empty($this->options['api_key'])) {
            error_log('API Key is missing in phone login plugin');
            wp_send_json_error(['message' => 'Configuration error: API Key is missing. Please contact the administrator.']);
            return;
        }
        
        if (empty($this->options['base_url'])) {
            error_log('Base URL is missing in phone login plugin');
            wp_send_json_error(['message' => 'Configuration error: Base URL is missing. Please contact the administrator.']);
            return;
        }
        
        if (empty($this->options['company_id'])) {
            error_log('Company ID is missing in phone login plugin');
            wp_send_json_error(['message' => 'Configuration error: Company ID is missing. Please contact the administrator.']);
            return;
        }
        
        // Generate a 6-digit OTP
        $otp = $this->generate_otp();
        
        $ch = curl_init($this->options['base_url'] . '/chat/messages');

        $payload = json_encode([
            'phone_number_id' => '568688196321558',
            'myop_ref_id' => uniqid(),
            'customer_country_code' => '91',
            'customer_number' => $phone,
            'reply_to' => null,
            'data' => [
                'type' => 'template',
                'context' => [
                    'template_name' => 'user_registration',
                    'body' => [
                        'otp' => $otp
                    ],
                    'buttons' => [
                        [
                            'otp' => $otp,
                            'index' => 0
                        ]
                    ]
                ]
            ]
        ]);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json',
                'Authorization: Bearer ' . trim($this->options['api_key']),
                'X-MYOP-COMPANY-ID: ' . trim($this->options['company_id'])
            ]
        ]);

        $response = curl_exec($ch);
        $err = curl_error($ch);

        if ($err) {
            $error_message = "Failed to send OTP: Connection failed. Please try again later.";
            error_log("cURL Error in phone login plugin: " . $err);
            wp_send_json_error(['message' => $error_message]);
        } else {
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $response_data = json_decode($response, true);

            if ($http_code === 200 && isset($response_data['status']) && $response_data['status'] === 'success') {
                set_transient('phone_otp_' . $phone, $otp, 600);
                wp_send_json_success(['message' => 'OTP sent successfully']);
            } else {
                $user_friendly_message = 'Failed to send OTP. Please try again later.';
                
                // Add more specific error messages based on HTTP code
                if ($http_code === 401) {
                    $user_friendly_message = 'Authentication failed. Please contact the administrator.';
                    error_log('API Authentication failed. Invalid API Key or Company ID.');
                } elseif ($http_code === 404) {
                    $user_friendly_message = 'Service not available. Please contact the administrator.';
                    error_log('API endpoint not found. Invalid Base URL.');
                } elseif ($http_code === 400) {
                    $user_friendly_message = 'Invalid phone number format. Please try again.';
                    error_log('Bad request to API. Possibly invalid phone format.');
                }
                
                error_log('Phone Login API Error: HTTP Code: ' . $http_code . '. Response: ' . $response);
                wp_send_json_error(['message' => $user_friendly_message]);
            }
        }

        curl_close($ch);
    }

    /**
     * Generate OTP
     */
    private function generate_otp($length = 6) {
        return sprintf('%0' . $length . 'd', mt_rand(0, pow(10, $length) - 1));
    }

    /**
     * Verify OTP
     */
    public function verify_otp() {
        check_ajax_referer('phone-login-nonce', 'nonce');
        
        $phone = sanitize_text_field($_POST['phone']);
        $otp = sanitize_text_field($_POST['otp']);
        
        if (empty($phone) || empty($otp)) {
            wp_send_json_error(array('message' => 'Phone number and OTP are required'));
            return;
        }
        
        // Get stored OTP from transient
        $stored_otp = get_transient('phone_otp_' . $phone);
        
        // Verify OTP
        if (!$stored_otp) {
            wp_send_json_error(array('message' => 'OTP has expired. Please request a new OTP.'));
            return;
        }
        
        if ($stored_otp != $otp) {
            wp_send_json_error(array('message' => 'Invalid OTP. Please check and try again.'));
            return;
        }
        
        // Delete the transient once verified
        delete_transient('phone_otp_' . $phone);
        
        // Check if user exists with this phone number
        global $wpdb;
        $user_id = $wpdb->get_var($wpdb->prepare(
            "SELECT ID FROM {$wpdb->users} WHERE user_phone = %s LIMIT 1",
            $phone
        ));
        
        if ($user_id) {
            // User exists, log them in
            wp_set_auth_cookie($user_id);
            
            wp_send_json_success(array(
                'message' => 'Login successful!',
                'user_exists' => true,
                'redirect' => home_url()
            ));
        } else {
            // User doesn't exist
            wp_send_json_success(array(
                'message' => 'Phone verified. Please complete registration.',
                'user_exists' => false
            ));
        }
    }

    /**
     * Register user
     */
    public function register_user() {
        check_ajax_referer('phone-login-nonce', 'nonce');
        
        $phone = sanitize_text_field($_POST['phone']);
        
        if (empty($phone)) {
            wp_send_json_error(array('message' => 'Phone number is required'));
            return;
        }
        
        // Check if user exists with this phone number
        global $wpdb;
        $existing_user_id = $wpdb->get_var($wpdb->prepare(
            "SELECT ID FROM {$wpdb->users} WHERE user_phone = %s LIMIT 1",
            $phone
        ));
        
        if ($existing_user_id) {
            // User already exists, just log them in
            wp_set_auth_cookie($existing_user_id);
            
            wp_send_json_success(array(
                'message' => 'Login successful! Redirecting...',
                'redirect' => home_url()
            ));
            return;
        }
        
        // Create unique username using full phone number
        $base_username = $phone;
        $username = $base_username;
        $count = 1;
        
        while (username_exists($username)) {
            $username = $base_username . '_' . $count;
            $count++;
        }
        
        // Get site domain
        $site_domain = str_replace(array('https://', 'http://', 'www.'), '', home_url());
        
        // Create email using full phone number and domain name
        $email = $phone . '@' . $site_domain;
        $password = wp_generate_password();
        
        // Create the user
        $user_id = wp_create_user($username, $password, $email);
        
        if (is_wp_error($user_id)) {
            wp_send_json_error(array('message' => 'Failed to create account: ' . $user_id->get_error_message()));
            return;
        }
        
        // Set user role
        $user = new WP_User($user_id);
        $user->set_role('customer');
        
        // Save phone number directly to wp_users table
        $result = $wpdb->update(
            $wpdb->users,
            array('user_phone' => $phone),
            array('ID' => $user_id),
            array('%s'),
            array('%d')
        );
        
        if ($result === false) {
            error_log('Failed to update user_phone: ' . $wpdb->last_error);
        }
        
        // Also save to WooCommerce billing fields
        update_user_meta($user_id, 'billing_phone', $phone);
        update_user_meta($user_id, 'billing_email', $email);
        
        // Log the user in
        wp_set_auth_cookie($user_id);
        
        wp_send_json_success(array(
            'message' => 'Registration successful! Redirecting...',
            'redirect' => home_url()
        ));
    }

    /**
     * Map user fields to WooCommerce billing
     */
    public function map_user_to_woocommerce($user_id) {
        if (!class_exists('WooCommerce')) {
            return;
        }
        
        global $wpdb;
        $phone = $wpdb->get_var($wpdb->prepare(
            "SELECT user_phone FROM {$wpdb->users} WHERE ID = %d",
            $user_id
        ));
        
        if ($phone) {
            // Map phone number to billing_phone
            update_user_meta($user_id, 'billing_phone', $phone);
            
            // Get user object
            $user = get_user_by('ID', $user_id);
            if ($user) {
                // Map username to billing_email
                update_user_meta($user_id, 'billing_email', $user->user_email);
            }
        }
    }
    
    /**
     * Override WordPress login page
     */
    public function override_wp_login_page() {
        // Redirect logged-in users to home page
        if (is_user_logged_in()) {
            wp_redirect(home_url());
            exit;
        }
        
        if (isset($_REQUEST['action']) && $_REQUEST['action'] !== 'login') {
            return;
        }
        
        // Enqueue dashicons
        wp_enqueue_style('dashicons');
        
        $login_header_text = __('Login with Phone');
        
        // Output WordPress login header
        login_header($login_header_text, '', '');
        
        // Display our custom form
        $this->display_phone_login_form();
        
        // Output WordPress login footer
        login_footer();
        exit;
    }
    
    /**
     * Override WordPress register page
     */
    public function override_wp_register_page() {
        // Redirect logged-in users to home page
        if (is_user_logged_in()) {
            wp_redirect(home_url());
            exit;
        }
        
        if (isset($_REQUEST['action']) && $_REQUEST['action'] !== 'register') {
            return;
        }
        
        // Enqueue dashicons
        wp_enqueue_style('dashicons');
        
        $register_header_text = __('Register with Phone');
        
        // Output WordPress login header
        login_header($register_header_text, '', '');
        
        // Display our custom form
        $this->display_phone_login_form();
        
        // Output WordPress login footer
        login_footer();
        exit;
    }
    
    /**
     * Disable password reset page
     */
    public function disable_password_reset() {
        wp_redirect(wp_login_url());
        exit;
    }
    
    /**
     * Display phone login form
     */
  /**
 * Display phone login form
 */
public function display_phone_login_form() {
    echo '<div class="phone-login-container">';
    echo '<div class="phone-login-step" id="step-phone">';
    echo '<h2>' . __('Login with Phone') . '</h2>';
    echo '<form id="phone-login-form">';
    echo '<div class="form-group">';
    echo '<label for="phone">' . __('Phone Number') . '</label>';
    echo '<input type="text" id="phone" name="phone" placeholder="Enter your phone number" required>';
    echo '</div>';
    echo '<div class="form-group send-otp-button-container">';
    echo '<button type="submit" id="send-otp-button">' . __('Send OTP') . '</button>';
    echo '</div>';
    echo '</form>';
    echo '</div>';
    
    echo '<div class="phone-login-step" id="step-otp" style="display:none;">';
    echo '<h2>' . __('Enter OTP') . '</h2>';
    echo '<p>' . __('An OTP has been sent to your phone number.') . '</p>';
    echo '<form id="otp-verify-form">';
    echo '<div class="form-group">';
    echo '<label for="otp">' . __('OTP') . '</label>';
    echo '<input type="text" id="otp" name="otp" placeholder="Enter OTP" required>';
    echo '</div>';
    echo '<div class="form-group send-otp-button-container">';
    echo '<button type="submit" id="verify-otp-button">' . __('Verify OTP') . '</button>';
    echo '</div>';
    echo '</form>';
    echo '</div>';
    
    echo '<div class="phone-login-step" id="step-register" style="display:none;">';
    echo '<h2>' . __('Complete Registration') . '</h2>';
    echo '<p>' . __('Your phone number has been verified. Click below to complete registration.') . '</p>';
    echo '<form id="register-form">';
    echo '<div class="form-group">';
    echo '<button type="submit" id="register-button">' . __('Register') . '</button>';
    echo '</div>';
    echo '</form>';
    echo '</div>';
    
    echo '<div id="phone-login-messages"></div>';
    echo '</div>';
}
    
    /**
     * End WooCommerce form replacement
     */
    public function wc_end_phone_form() {
        // Close our custom form
        echo '</div>';
    }
    
    /**
     * Remove checkout fields
     */
    public function remove_checkout_fields($fields) {
        // Remove all password fields
        if (isset($fields['account']['account_password'])) {
            unset($fields['account']['account_password']);
        }
        
        if (isset($fields['account']['account_password-2'])) {
            unset($fields['account']['account_password-2']);
        }
        
        return $fields;
    }
}

// Initialize the plugin
new otp_Phone_Login();
jQuery(document).ready(function($) {
    // Debug output
    console.log("Phone Login script loaded successfully", phone_login_vars);
    
    // Variables to store user data
    let currentPhone = '';
    
    // Send OTP
    $(document).on('submit', '#phone-login-form', function(e) {
        e.preventDefault();
        
        const phone = $('#phone').val();
        currentPhone = phone;
        
        $('#send-otp-button').prop('disabled', true).text('Sending...');
        $('#phone-login-messages').removeClass('error success').empty();
        
        $.ajax({
            url: phone_login_vars.ajax_url,
            type: 'POST',
            data: {
                action: 'send_otp',
                phone: phone,
                nonce: phone_login_vars.nonce
            },
            success: function(response) {
                if (response.success) {
                    // Show OTP verification step
                    $('#step-phone').hide();
                    $('#step-otp').show();
                    $('#phone-login-messages').addClass('success').text(response.data.message);
                } else {
                    $('#phone-login-messages').addClass('error').text(response.data.message);
                    $('#send-otp-button').prop('disabled', false).text('Send OTP');
                }
            },
            error: function() {
                $('#phone-login-messages').addClass('error').text('Server error. Please try again.');
                $('#send-otp-button').prop('disabled', false).text('Send OTP');
            }
        });
    });
    
    // Verify OTP
    $(document).on('submit', '#otp-verify-form', function(e) {
        e.preventDefault();
        
        const otp = $('#otp').val();
        
        $('#verify-otp-button').prop('disabled', true).text('Verifying...');
        $('#phone-login-messages').removeClass('error success').empty();
        
        $.ajax({
            url: phone_login_vars.ajax_url,
            type: 'POST',
            data: {
                action: 'verify_otp',
                phone: currentPhone,
                otp: otp,
                nonce: phone_login_vars.nonce
            },
            success: function(response) {
                if (response.success) {
                    $('#phone-login-messages').addClass('success').text(response.data.message);
                    
                    if (response.data.user_exists) {
                        // User exists, redirect after login
                        setTimeout(function() {
                            window.location.href = response.data.redirect;
                        }, 1000);
                    } else {
                        // User doesn't exist, show registration form
                        $('#step-otp').hide();
                        $('#step-register').show();
                    }
                } else {
                    $('#phone-login-messages').addClass('error').text(response.data.message);
                    $('#verify-otp-button').prop('disabled', false).text('Verify OTP');
                }
            },
            error: function() {
                $('#phone-login-messages').addClass('error').text('Server error. Please try again.');
                $('#verify-otp-button').prop('disabled', false).text('Verify OTP');
            }
        });
    });
    
    // Register user
    $(document).on('submit', '#register-form', function(e) {
        e.preventDefault();
        
        $('#register-button').prop('disabled', true).text('Registering...');
        $('#phone-login-messages').removeClass('error success').empty();
        
        $.ajax({
            url: phone_login_vars.ajax_url,
            type: 'POST',
            data: {
                action: 'register_user',
                phone: currentPhone,
                nonce: phone_login_vars.nonce
            },
            success: function(response) {
                if (response.success) {
                    $('#phone-login-messages').addClass('success').text(response.data.message);
                    
                    // Redirect after registration
                    setTimeout(function() {
                        window.location.href = response.data.redirect;
                    }, 1000);
                } else {
                    $('#phone-login-messages').addClass('error').text(response.data.message);
                    $('#register-button').prop('disabled', false).text('Register');
                }
            },
            error: function() {
                $('#phone-login-messages').addClass('error').text('Server error. Please try again.');
                $('#register-button').prop('disabled', false).text('Register');
            }
        });
    });
});
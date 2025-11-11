<?php
if (!defined('ABSPATH')) exit; // Security check

$site_name = get_bloginfo('name');
$site_url = get_site_url();
$user_name = $user_data['first_name'];
$user_login = $user_data['user_login'];
$user_password = $user_data['user_pass'];

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2>Welcome to <?php echo esc_html($site_name); ?>!</h2>
        
        <p>Hello <?php echo esc_html($user_name); ?>,</p>
        
        <p>Your account has been successfully created. Below are your login credentials:</p>
        
        <div style="background: #f5f5f5; padding: 15px; margin: 20px 0;">
            <p><strong>Username:</strong> <?php echo esc_html($user_login); ?></p>
            <p><strong>Password:</strong> <?php echo esc_html($user_password); ?></p>
        </div>
        
        <p>You can login to your account using the following link:</p>
        <p><a href="<?php echo site_url('/sign-in/'); ?>" style="color: #0073aa;">Sign-In</a></p>
                
        <p>If you have any questions, please don't hesitate to contact us.</p>
        
        <p>Best regards,<br>
        <?php echo esc_html($site_name); ?> Team</p>
    </div>
</body>
</html>
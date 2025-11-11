<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2>Password Reset Request</h2>
        <p>Hello,</p>
        <p>We have recieved your password resest request</p>
        <p>Click the link below to reset your password:</p>
        <p>
            <a href="<?php echo esc_url($reset_link); ?>" style="display: inline-block; padding: 10px 20px; background-color: #0073aa; color: #ffffff; text-decoration: none; border-radius: 4px;">
                Reset Password
            </a>
        </p>
        <p>This password reset link will expire in 2 hours.</p>
        <p>If you're having trouble clicking the button, copy and paste the URL below into your web browser:</p>
        <p><?php echo esc_url($reset_link); ?></p>

        <p>If you didn't make this request, you can ignore this email.</p>
        
        <p>Best regards,<br>
        <?php echo esc_html(get_bloginfo('name')); ?></p>
    </div>
</body>
</html>
<?php
/* Template Name: CM Forgot Password */

get_header();
?>

<section class="sign-in-section">
    <div class="sign-in-container">

        <!-- Left Side Image -->
        <div class="sign-in-image">
            <img src="<?php echo CM_PLUGIN_URL; ?>img/sign-in.webp" alt="Forgot Password" />
        </div>

        <!-- Right Side Form -->
        <div class="sign-in-form-area">

            <div class="form-inner">
                <div class="back-link">
                    <a href="<?php echo site_url('/sign-in/'); ?>" class="forgot-password-btn">Back to Sign In</a>
                </div>
                <h2>Forgot Password</h2>
                <!-- <p class="subtitle">Enter your email address and we will send you a password reset link.</p> -->

                <form method="post" class="sign-in-form js-forget-password">
                    <input type="hidden" name="action" value="cm_reset_password">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="text" name="email" id="email" placeholder="Enter your email" />
                    </div>

                    <div class="sign-button">
                        <button type="submit" class="sign-in-btn">Send Reset Link</button>
                    </div>


                </form>
            </div>
        </div>

    </div>
</section>

<?php
get_footer();
?>
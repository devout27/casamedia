<?php
/* Template Name: Change Password */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if(!isset($_SESSION['_cm']) || $_SESSION['_cm'] != 'rp'){
    wp_redirect(site_url( '/sign-in/' ));
}

$user = $_SESSION['user'] ?? false;
$rp_id = $_SESSION['rp_id'] ?? null;
$rp_email = $_SESSION['rp_email'] ?? null;
$msg = $_SESSION['msg'] ?? null;

get_header();
?>

<section class="sign-in-section">
    <div class="sign-in-container">

        <!-- Left Side Image -->
        <div class="sign-in-image">
            <img src="<?php echo CM_PLUGIN_URL; ?>img/sign-in.webp" alt="Change Password" />
        </div>

        <!-- Right Side Form -->
        <div class="sign-in-form-area">
            <div class="form-inner">
                <div class="back-link">
                    <a href="#" class="forgot-password-btn">Back to Sign In</a>
                </div>
                <h2>Change Password</h2>

                <!-- <p class="subtitle">Enter your new password below.</p> -->

                <?php if($user) { ?>

                    <form method="post" class="sign-in-form js-change-password">

                        <input type="hidden" name="action" value="cm_change_password">
                        <div class="form-group password-group">
                            <label for="password">New Password</label>
                            <div class="password-field">
                                <input type="password" name="password" id="password" placeholder="Enter new password" />
                                <i class="fa-solid fa-eye toggle-password toggle-visible-eye" data-target="password"></i>
                            </div>
                        </div>

                        <div class="form-group password-group">
                            <label for="confirm_password">Confirm Password</label>
                            <div class="password-field">
                                <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm new password" />
                                <i class="fa-solid fa-eye toggle-password toggle-visible-eye" data-target="confirm_password"></i>
                            </div>
                        </div>

                        <div class="sign-button">
                            <button type="submit" class="sign-in-btn">Change Password</button>
                        </div>
                    </form>
                    
                <?php } else { ?>
                    <div class="error-box">
                        <p class="error-msg"><span><i class="fa-solid fa-info"></i></span><?php echo $msg; ?></p>
                    </div>
                <?php } ?> 
            </div>
        </div>

    </div>
</section>

<?php
get_footer();
?>
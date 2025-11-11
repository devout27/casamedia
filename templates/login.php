<?php

/* Template Name: CM Login */

if(is_user_logged_in()){

    $role = get_user_primary_role_by_id(get_current_user_id());

    if($role['slug'] === 'client'){
        wp_redirect(site_url('/client/dashboard/'));    
        exit;
    }

    wp_redirect(home_url());
    exit;
}

get_header();

?>

<section class="sign-in-section">
    <div class="sign-in-container">

        <!-- Left Side Image -->
        <div class="sign-in-image">
            <img src="<?php echo CM_PLUGIN_URL; ?>img/sign-in.webp" alt="Sign In" />
        </div>

        <!-- Right Side Form -->
        <div class="sign-in-form-area">
            <div class="form-inner">
                <h2>Sign In</h2>
                <!-- <p class="subtitle">Please sign in to continue</p> -->

                <form method="post" class="js-cm-login-form sign-in-form">
                    <input type="hidden" name="action" value="cm_user_login">
                    <div class="form-group ">
                        <label for="email">Email Address</label>
                        <input type="text" name="email" id="email" placeholder="Enter your email" />
                    </div>

                    <div class="form-group password-group">
                        <label for="password">Password</label>
                        <div class="password-field">
                            <input type="password" name="password" id="password" placeholder="Enter your password" />
                            <i class="fa-solid fa-eye toggle-password toggle-visible-eye" data-target="password"></i>
                        </div>
                    </div>

                    <div class="forgot-wrap">
                        <div class="remember-me">
                            <input type="checkbox" id="remember_me" name="remember_me" value="1">
                            <label for="remember_me"> Remember Me </label>
                        </div>
                        <a href="<?php echo site_url('/forget-password/'); ?>" class="forgot-password">Forgot Password?</a>
                    </div>
                    <div class="sign-button">
                        <button type="submit" class="sign-in-btn">Sign In</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</section>


<?php
get_footer();
?>
<?php

/* Template Name: CM Client Dashboard */

if (!is_user_logged_in()) {
    wp_redirect(site_url('/sign-in/'));
    exit;
}

get_header();

?>

<!-- Layout -->
<div class="layout">

    <?php include CM_PLUGIN_DIR . 'parts/client/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="content profile-content">
        <div class="main-panel">
            <div class="inner-panel">
                <!-- profile-sec -->
                <div class="profile-section">

                    <div class="profile-container">
                        <div class="top-bg">
                            <div class="profile-header">
                                <div class="profile-img-settings">
                                    <div class="img-profile">
                                        <!-- <img src="https://via.placeholder.com/120" class="img-prof" alt="Profile Photo"> -->
                                        <img src="<?php echo CM_PLUGIN_URL; ?>img/face16.jpg" class="img-prof" alt="Profile Photo" />
                                        <span class="edit_img"><i class="fa-solid fa-pen"></i></span>
                                    </div>

                                </div>
                                <div class="profile-details">
                                    <h2>
                                        Issam
                                    </h2>
                                    <p>issam@example.com</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Body Section -->

                    <div class="bottom-profile">
                        <div class="inner-bottom-profile">
                            <div class="input-field-wrapper">
                                <div class="input-group">
                                    <label for="fullname">Full Name</label>
                                    <input type="text" id="fullname" class="input-fields" placeholder="Enter your name">
                                </div>
                            </div>
                            <div class="input-field-wrapper">
                                <div class="input-group">
                                    <label for="email">Email</label>
                                    <input type="email" id="email" class="input-fields" placeholder="Enter your Email">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<?php
get_footer();
?>
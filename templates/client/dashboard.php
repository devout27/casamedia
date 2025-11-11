<?php

/* Template Name: CM Client Dashboard */

if(!is_user_logged_in()){
    wp_redirect(site_url('/sign-in/'));    
    exit;
}

$user_id = get_current_user_id();

get_header();

?>

<!-- Layout -->
<div class="layout">

    <?php include CM_PLUGIN_DIR . 'parts/client/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="content">
        <div class="main-panel">
            <div class="inner-panel">
                <div class="dashboard-sec">
                    <h1 class="title-main">Welcome, <?php echo get_name_by_id_($user_id); ?></h1>
                </div>
            </div>
        </div>
    </main>
</div>

<?php
get_footer();
?>
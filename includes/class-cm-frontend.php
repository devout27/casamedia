<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class CasaMedia_Frontend {
    public $admin_email, $db, $rp_timeout = 7200, $is_client_page = false;
    
    public $routes = [
        'cm_client_dashboard'   => 'client/dashboard',
        'cm_client_locations'   => 'client/locations',
        'cm_client_profile'     => 'client/profile',
    ];

    public function __construct() {
        global $wpdb;
        $this->db = $wpdb;
        $this->admin_email = get_option('admin_email') /* 'devouttest@gmail.com' */;

        ini_set('display_errors', 1);
        error_reporting(E_ALL);

        add_action('init', [$this, 'cm_front_init']);

        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);

        add_filter('template_include', [$this, 'cm_load_page_template']);

        add_filter('query_vars', [$this, 'cm_query_vars']);

        add_action('template_redirect', [$this, 'cm_template_redirect']);

        add_action('after_setup_theme', [$this, 'after_theme_options']);

        add_action('wp_ajax_cm_user_login', [$this, 'handle_user_login']);
        add_action('wp_ajax_nopriv_cm_user_login', [$this, 'handle_user_login']);

        add_action('wp_ajax_nopriv_cm_reset_password', [$this, 'handle_reset_password']);
        add_action('wp_ajax_nopriv_cm_change_password', [$this, 'handle_change_password']);

    }

    public function cm_front_init() {

        flush_rewrite_rules();

        if(isset($_POST['casamedia_user_logout']) && $_POST['casamedia_user_logout'] == 1){
            if(is_user_logged_in()){
                wp_logout();
                wp_redirect(site_url('/sign-in'));
                exit;
            }
        }
        
        if(isset($_REQUEST['_cm']) && $_REQUEST['_cm'] == true) {
            $_r = $_REQUEST['_r'] ?? null; $_t = $_REQUEST['_t'] ?? null; $_e = $_REQUEST['_e'] ?? null;
            $_SESSION['_cm'] = $_r;
            if($_r && $_r == 'rp') {
                $_SESSION['user'] = false;

                if($_t && $_t + $this->rp_timeout >= time()){
                    if($_e){
                        $_e = trim($_e);
                        $_e = base64_decode($_e);
                        if (is_email($_e)) {
                            $user = get_user_by('email', $_e);
                        } else {
                            $user = get_user_by('login', $_e);
                        }
                        if($user) {
                            $_SESSION['user'] = true;
                            $_SESSION['rp_id'] = $user->ID;
                            $_SESSION['rp_email'] = $user->user_email;
                        } else {
                            $_SESSION['msg'] = "This link does not seem to be associated with any account.";
                        }
                    } else {
                        $_SESSION['msg'] = "This link does not seem to be associated with any account.";
                    }
                }else{
                    $_SESSION['msg'] = 'The password reset link has expired. Please <a href="' . site_url('/forget-password/') . '" class="forgot-password">request a new one</a>.';
                }
                wp_redirect(site_url('/change-password/?_t=' . time()));
                exit;
            }
        }

        foreach ($this->routes as $arg => $path){
            add_rewrite_rule(
                '^' . $path . '/?$',
                'index.php?' . $arg . '=1',
                'top'
            );
        }
    }

    public function enqueue_assets() {
        wp_enqueue_script('jquery');
        
        foreach($this->routes as $var => $path){
            if(get_query_var($var)){
                $this->is_client_page = true;
                break;
            }
        }

        if($this->is_client_page) {

            wp_enqueue_style('casamedia-bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css', [], '7.0.1', 'all');
            wp_enqueue_script('casamedia-bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js', ['jquery'], '1.0.0', true);

        }

        wp_enqueue_style('casamedia-font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css', [], '7.0.1', 'all');
        wp_enqueue_style('casamedia-css', CM_PLUGIN_URL . 'css/style.css', [], time(), 'all');

        wp_enqueue_script('casamedia-magic-grid', 'https://unpkg.com/magic-grid/dist/magic-grid.min.js', ['jquery'], '1.0.0', true);
        wp_enqueue_script('casamedia-script', CM_PLUGIN_URL . 'js/script.js', ['jquery'], time(), true);

        wp_localize_script('casamedia-script', 'cm_obj', [
            'ajax_url'      => admin_url('admin-ajax.php'),
            'nonce'         => wp_create_nonce('casamedia_nonce'),
            'site_url'      => site_url('/'),
            'is_auth'       => is_user_logged_in(),
            'url'           => CM_PLUGIN_URL,
        ]);
    }

    public function cm_load_page_template($template) {
        if (is_singular() && $page_template = get_page_template_slug()) {
            $plugin_template = CM_PLUGIN_DIR . 'templates/' . $page_template;
    
            if (file_exists($plugin_template)) {
                return $plugin_template;
            }
        }
        return $template;
    }

    public function cm_query_vars($vars){
        $vars = array_merge($vars, array_keys($this->routes));
        return $vars;
    }

    public function cm_template_redirect() {
        foreach ($this->routes as $var => $path) {
            if (get_query_var($var)) {

                $template = CM_PLUGIN_DIR . 'templates/' . $path . '.php';

                $post = new stdClass();
                $post->ID = -1;
                $post->post_author = 1;
                $post->post_date = current_time('mysql');
                $post->post_date_gmt = current_time('mysql', 1);
                $post->post_content = '';
                $post->post_title = ucfirst(basename($path));
                $post->post_excerpt = '';
                $post->post_status = 'publish';
                $post->comment_status = 'closed';
                $post->ping_status = 'closed';
                $post->post_password = '';
                $post->post_name = sanitize_title(basename($path));
                $post->to_ping = '';
                $post->pinged = '';
                $post->post_modified = current_time('mysql');
                $post->post_modified_gmt = current_time('mysql', 1);
                $post->post_content_filtered = '';
                $post->post_parent = 0;
                $post->guid = home_url('/' . $path . '/');
                $post->menu_order = 0;
                $post->post_type = 'page';
                $post->post_mime_type = '';
                $post->comment_count = 0;
                $post->filter = 'raw';
                $post->ancestors = []; // Prevent nav-menu errors

                global $wp_query;
                $wp_query->post = $post;
                $wp_query->posts = [$post];
                $wp_query->queried_object = $post;
                $wp_query->queried_object_id = $post->ID;
                $wp_query->is_page = true;
                $wp_query->is_singular = true;
                $wp_query->is_home = false;
                $wp_query->is_404 = false;

                add_filter('template_include', function() use ($template) {
                    return $template;
                });

                return;
            }
        }
    }


    public function after_theme_options() {
        if(!current_user_can('administrator')) {
            show_admin_bar(false);
        }
    }

    public function handle_user_login () {
        $errors = []; $success = []; $db = $this->db;
        if(isset($_POST['email']) && !empty($_POST['email']) && isset($_POST['password']) && !empty($_POST['password'])){
            $remember_me = false;
            if(isset($_POST['remember_me']) && !empty($_POST['remember_me'])){
                $remember_me = true;
            }
            
            $email = sanitize_text_field($_POST['email']);

            if(is_email($_POST['email'])){
                $user = get_user_by('email', $email);
            }else{
                $user = get_user_by('login', $email);
            }
            if($user){
                $user_id = $user->ID;
                $user_data = get_userdata($user_id);
                if($user_data && wp_check_password(sanitize_text_field($_POST['password']), $user_data->user_pass, $user_id)){
                    $role = get_user_primary_role_by_id($user_id);
                    if(!$role || !$role['slug'] || !in_array($role['slug'], ['client', 'administrator'])){
                        $errors['email'] = 'You are unable to log in. Please contact us for further assistance.';
                    }else{
                        $is_deleted = get_user_meta($user_id, 'deleted_at', true);
                        
                        if($role['slug'] === 'administrator'){
                            $is_deleted = false;
                        }
                        
                        if(!$is_deleted){
                            wp_set_auth_cookie($user_id, $remember_me);
                            $success['msg'] = 'Please wait... Redirecting to the dashboard...';
                            $success['path'] = site_url('/client/dashboard/');
                            wp_send_json_success( compact('user', 'success') );
                            die;
                        }else{
                            $errors['email'] = 'Your account has been deleted. Please contact us further assistance.';
                        }
                    }
                }else{
                    $errors['password'] = 'The password is incorrect.';
                }
            }else{
                $errors['email'] = 'The email address is not registered.';
            }
            if(!empty($errors)){
                wp_send_json_error( compact('errors') );
                die;
            }
        }else{
            if(!isset($_POST['email']) || empty($_POST['email'])){
                $errors['email'] = 'This field is required.';
            }
            if(!isset($_POST['password']) || empty($_POST['password'])){
                $errors['password'] = 'This field is required.';
            }
            if(!empty($errors)){
                wp_send_json_error( compact('errors') );
                die;
            }
        }
    }

    public function handle_reset_password() {
        $db = $this->db;
        if (isset($_POST['email']) && !empty($_POST['email'])) {
            $email_or_username = sanitize_text_field($_POST['email']);

            if (is_email($email_or_username)) {
                $user = get_user_by('email', $email_or_username);
            } else {
                $user = get_user_by('login', $email_or_username);
            }

            if ($user) {
                $user_id = $user->ID;
                $role = get_user_primary_role_by_id($user_id);
                if (!$role || !$role['slug'] || !in_array($role['slug'], ['client'])) {
                    $msg = 'Resetting your password is not possible. Please contact us for further assistance.';
                    wp_send_json_error( compact('msg') );
                    
                } else {
                    $is_deleted = get_user_meta($user_id, 'deleted_at', true);
                    
                    if(!$is_deleted){

                        $reset_link = site_url('/?_cm=true&_r=rp&_t=' . time() . '&_e=' . base64_encode($user->user_email));
                        
                        $to = $user->user_email;
                        $subject = 'Password Reset';
                        ob_start();
                        include CM_EMAILS_DIR . 'reset-pass-email.php';
                        $message = ob_get_clean();
                        $headers = ['Content-Type: text/html'];
                        wp_mail($to, $subject, $message, $headers);
                        
                        $msg = "We have sent a link to reset password to your registered email address. Please check your inbox and spam folder for further instructions.";
                        $path = site_url( '/sign-in' );
                        wp_send_json_success( compact('msg', 'path') );
                    } else {
                        $msg = 'Your account has been deleted. Please contact us for further assistance.';
                        wp_send_json_error( compact('msg') );
                    }
                }
            } else {
                $msg = "No user found with this email address or username.";
                wp_send_json_error( compact('msg') );
            }
        } else {
            $msg = "Please enter a valid email address or username.";
            wp_send_json_error( compact('msg') );
        }
        die;
    }

    public function handle_change_password() {
        $errors = []; $success = [];

        // Basic required fields check
        if (empty($_POST['password'])) {
            $errors['password'] = 'This field is required.';
        }
        if (empty($_POST['confirm_password'])) {
            $errors['confirm_password'] = 'This field is required.';
        }
        if (!empty($errors)) {
            wp_send_json_error(compact('errors'));
            die;
        }

        $password = sanitize_text_field($_POST['password']);
        $confirm_password = sanitize_text_field($_POST['confirm_password']);

        // Validate password
        if ($password !== $confirm_password) {
            $errors['confirm_password'] = 'Passwords do not match.';
        }
        if (strlen($password) < 8) {
            $errors['password'] = 'Password must be at least 8 characters.';
        }

        // Verify reset session
        if (empty($_SESSION['user']) || $_SESSION['user'] !== true || empty($_SESSION['rp_id'])) {
            $errors['msg'] = 'The password reset link is invalid or has expired.';
        }

        if (!empty($errors)) {
            wp_send_json_error(compact('errors'));
            die;
        }

        $user_id = intval($_SESSION['rp_id']);
        $user = get_user_by('ID', $user_id);
        if (!$user) {
            $errors['msg'] = 'No account was found for this request.';
            wp_send_json_error(compact('errors'));
            die;
        }

        // Set new password
        wp_set_password($password, $user_id);

        // Clean up reset session data
        unset($_SESSION['user'], $_SESSION['rp_id'], $_SESSION['rp_email'], $_SESSION['_cm']);
        $success['msg'] = 'Your password has been changed successfully.';
        $success['path'] = site_url('/sign-in');

        wp_send_json_success(compact('success'));
        die;
    }

}
<?php

if (!defined('ABSPATH')) exit;

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/class-cm-clients-table.php';

class CMAdminClients {
    public static function render_clients_page() {
        global $wpdb;
        $table = new CMAdminClientsTable();

        $message = get_option('cm_client_message', false);
        if ($message) {
            echo '<div class="notice notice-success is-dismissible"><p>' . esc_html($message) . '</p></div>';
            delete_option('cm_client_message');
        }
        
        $table->prepare_items();
        ?>

        <div class="wrap custom-plugin-casamedia">
            <h1 class="wp-heading-inline">Clients List</h1>
            <div class="d-flex justify-content-between my-3">
                <div class="add-new-zip-btn">
                    <a href="#" class="page-title-action page-title-action-button" data-bs-toggle="modal" data-bs-target="#createClientModal" onclick="jQuery('#createClientModal form')[0].reset(); jQuery('#createClientModal .js-error').remove();">Add New</a>
                </div>
            </div>
            <form method="get">
                
                <?php
                    foreach ($_REQUEST as $key => $value) {
                        if ($key !== 'paged' && $key !== 'action' && $key !== 'action2' && !is_array($value)) {
                            echo '<input type="hidden" name="' . esc_attr($key) . '" value="' . esc_attr($value) . '">';
                        }
                    }
                    $table->views();
                    echo '<input type="hidden" name="page" value="' . esc_attr($_REQUEST['page']) . '">';
                    $table->search_box('Search Client', 'search_id');
                    $table->display(); 
                ?>
            </form>
        </div>

        <!-- Delete Modal -->
        <div class="modal fade casamedia-modal" id="deleteClientModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <form method="post" class="modal-content">
                    <div class="modal-body">
                        <div class="modal-header"><h5 class="modal-title">Delete</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"><span><i class="fa-solid fa-xmark"></i></span></button>
                        </div>
                        <?php wp_nonce_field('delete_client_action', 'delete_client_nonce'); ?>
                        <input type="hidden" name="action" value="delete_client">
                        <input type="hidden" name="id" id="delete-id">
                        <p class="delete-modal-msg">Are you sure you want to delete?</p>
                        <div class="delete-modal-btn text-center">
                            <button type="submit" class="btn btn-danger modal-btn-foot">Delete</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal fade casamedia-modal" id="trashClientModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <form method="post" class="modal-content">
                    <div class="modal-body">
                        <div class="modal-header">
                            <h5 class="modal-title">Trash</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"><span><i class="fa-solid fa-xmark"></i></span></button>
                        </div>
                        <?php wp_nonce_field('trash_client_action', 'trash_client_nonce'); ?>
                        <input type="hidden" name="action" value="trash_client">
                        <input type="hidden" name="id" id="trash-id">
                        <p class="delete-modal-msg">Are you sure you want to move this to the trash?</p>
                        <div class="delete-modal-btn text-center">
                            <button type="submit" class="btn btn-danger modal-btn-foot">Trash</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal fade casamedia-modal js-client-edit--modal cm-detail-modal-sec" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <form method="post" class="modal-content">
                    <div class="modal-body">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Client</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"><span><i class="fa-solid fa-xmark"></i></span></button>
                        </div>
                        <input type="hidden" name="action" value="cm_admin_update_client_details">
                        <div class="progress progress-field" style="display:none; height: 5px;">
                            <div class="progress-bar" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="row g-3 js-form-fields">
                        </div>
                        <div class="delete-modal-btn text-center">
                            <button type="submit" class="btn btn-primary modal-btn-foot">Save Changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal fade casamedia-modal js-client-create--modal cm-detail-modal-sec" data-bs-backdrop="static" id="createClientModal" data-bs-keyboard="false" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <form method="post" class="modal-content">
                    <div class="modal-body">
                        <div class="modal-header">
                            <h5 class="modal-title">Add Client</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"><span><i class="fa-solid fa-xmark"></i></span></button>
                        </div>
                        <input type="hidden" name="action" value="cm_admin_create_client_details">
                        <div class="progress progress-field" style="display:none; height: 5px;">
                            <div class="progress-bar" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="row g-3 js-form-fields">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="f-name">First Name <span>*</span></label>
                                    <input type="text" name="first_name" id="f-name" class="form-control" >
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="l-name">Last Name <span>*</span></label>
                                    <input type="text" name="last_name" id="l-name" class="form-control" >
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="email">Email <span>*</span></label>
                                    <input type="text" name="email" id="email" class="form-control" >
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="t-number">Telephone Number <span>*</span></label>
                                    <input type="text" name="phone" id="t-number" class="form-control" >
                                </div>
                            </div>

                            <div class="delete-modal-btn text-center">
                                <button type="submit" class="btn btn-primary modal-btn-foot">Submit</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal fade casamedia-modal js-change-client-password--modal" data-bs-backdrop="static" data-bs-keyboard="false" id="changePassClientModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <form method="post" class="modal-content">
                    <div class="modal-body">
                        <div class="modal-header">
                            <h5 class="modal-title">Change Password</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"><span><i class="fa-solid fa-xmark"></i></span></button>
                        </div>
                        <input type="hidden" name="action" value="cm_admin_update_client_password">
                        <input type="hidden" name="id" id="client-id">
                        <input type="hidden" name="wp_user" value="1">
                        <div class="progress progress-field" style="display:none; height: 5px;">
                            <div class="progress-bar" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <label for="password" class="form-label">New Password*</label>
                                <div class="toggle-visible text-dark position-relative">
                                    <input type="password" name="password" id="password" class="form-control " value="">
                                    <div class="toggle-visible-eye">
                                        <i class="fa-solid fa-eye"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="delete-modal-btn text-center">
                            <button type="submit" class="btn btn-primary modal-btn-foot">Reset</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal fade casamedia-modal js-change-client-email--modal" data-bs-backdrop="static" data-bs-keyboard="false" id="changeClientEmailModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <form method="post" class="modal-content">
                    <div class="modal-body">
                        <div class="modal-header">
                            <h5 class="modal-title">Change Email</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"><span><i class="fa-solid fa-xmark"></i></span></button>
                        </div>
                        <input type="hidden" name="action" value="cm_admin_update_client_email">
                        <input type="hidden" name="id" id="client-id">
                        <input type="hidden" name="wp_user" value="1">
                        <div class="progress progress-field" style="display:none; height: 5px;">
                            <div class="progress-bar" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <label for="email" class="form-label">New Email*</label>
                                <input type="email" name="email" id="email" class="form-control" value="">
                            </div>
                        </div>
                        <div class="delete-modal-btn text-center">
                            <button type="submit" class="btn btn-primary modal-btn-foot">Change</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- JavaScript to handle modal data population -->
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('.delete-btn').forEach(button => {
                    button.addEventListener('click', () => {
                        document.getElementById('delete-id').value = button.dataset.id;
                    });
                });
                document.querySelectorAll('.trash-btn').forEach(button => {
                    button.addEventListener('click', () => {
                        document.getElementById('trash-id').value = button.dataset.id;
                    });
                });
            });
        </script>

        <?php        
    }

    public static function clients_actions() {

        $redirect = false;

        $table = new CMAdminClientsTable();
        $table->process_bulk_action();


        if (isset($_POST['action']) && $_POST['action'] === 'trash_client') {
            check_admin_referer('trash_client_action', 'trash_client_nonce');
            $wp_user_id = $_POST['id'];
            if (get_userdata($wp_user_id)) {
                update_user_meta( $wp_user_id, 'deleted_at', current_time('mysql'));
            }
            update_option( 'cm_client_message', 'Client moved to trash successfully.' );
            $redirect = true;
        }

        if (isset($_REQUEST['action']) && $_REQUEST['action'] === 'restore_client') {
            // check_admin_referer('trash_client_action', 'trash_client_nonce');
            $wp_user_id = $_REQUEST['id'];
            if (get_userdata($wp_user_id)) {
                delete_user_meta( $wp_user_id, 'deleted_at');
            }
            update_option( 'cm_client_message', 'Client restored successfully.' );
            $redirect = true;
        }


        if (isset($_POST['action']) && $_POST['action'] === 'delete_client') {
            check_admin_referer('delete_client_action', 'delete_client_nonce');
            $wp_user_id = $_POST['id'];
            if (get_userdata($wp_user_id)) {
                wp_delete_user($wp_user_id);
            }
            update_option( 'cm_client_message', 'Client deleted successfully.' );
            $redirect = true;
        }

        if($redirect){
            wp_redirect(admin_url('admin.php?page=casamedia-clients'));
            exit;
        }
    }
}

add_action( 'wp_ajax_cm_admin_update_client_email', function(){
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    if(isset($_POST['id']) && !empty($_POST['id']) && isset($_POST['email']) && !empty($_POST['email'])){
        if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
            $error_email = 'Please enter a valid email address.';
            wp_send_json_error(['msg' => $error_email]);
            die;
        }
        global $wpdb;
        $wp_user_id = intval($_POST['id']);
        $new_login = sanitize_user($_POST['email'], true);
        $error_email = '';
        $existing__ = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $wpdb->users WHERE user_login = %s AND ID != %d", $_POST['email'], $wp_user_id));

        if ($existing__ > 0) {
            $error_email = 'Email '.$_POST['email'].' already exists.';
        } else {
            if ($wp_user_id) {
                $existing__ = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $wpdb->users WHERE user_login = %s ID != %d", $_POST['email'], $wp_user_id));
                if($existing__ == 0 ){
                    $user_data = [
                        'ID'         => $wp_user_id,
                        'user_email' => $new_login,
                    ];
                    remove_filter('send_email_change_email', '__return_false');
                    remove_filter('send_email_change_confirmation_email', '__return_false');
                    remove_action('profile_update', 'wp_send_user_email_change_email', 10);
                    $result = wp_update_user($user_data);
                    remove_action('profile_update', 'wp_send_user_email_change_email', 10);
                    remove_filter('send_email_change_email', '__return_false');
                    remove_filter('send_email_change_confirmation_email', '__return_false');

                    $wp_user_id = $wpdb->update(
                        $wpdb->users,
                        [ 'user_login' => $new_login ],
                        [ 'ID' => $wp_user_id ]
                    );

                    if (is_wp_error($wp_user_id)) {
                        $msg = $wp_user_id->get_error_message();
                        wp_send_json_error( compact('msg') );
                        die;
                    }

                    /* $user = get_user_by('email', $new_login);
                    $password = wp_generate_password(12, true);
                    wp_set_password($password, $wp_user_id); 

                    $to = $new_login;
                    $subject = 'Your New Login Details';
                    ob_start();
                    include MYMAID_PLUGIN_DIR . 'parts/reset-pass-email.php';
                    $message = ob_get_clean();
                    $headers = ['Content-Type: text/html'];
                    wp_mail($to, $subject, $message, $headers); */

                    clean_user_cache($wp_user_id);
                    wp_cache_delete($wp_user_id, 'users');
                    wp_cache_delete($new_login, 'userlogins');
                }
            }
        }
        if($error_email){
            wp_send_json_error(['msg' => $error_email]);
        }else{
            wp_send_json_success(['msg' => 'Email updated successfully.', 'to' => $new_login, 'existing__'=>$existing__]);
        }
    } else {
        wp_send_json_error(['msg' => 'Invalid request.']);
    }
    die;
});

add_action('wp_ajax_cm_admin_get_client_details', function() {
    $html = ''; $user_first_name = ''; $user_last_name = ''; $user_phone = '';
    global $wpdb;

    if (!isset($_POST['id']) || empty($_POST['id'])) {   
        wp_send_json_error(['html' => 'Invalid request.']);
        die;
    } 

    $user_id =  intval($_POST['id']);
    $user =  get_user_by('ID', $user_id);
    $user_email = $user->user_email;
    $user_phone = get_user_meta($user_id, 'phone', true);
    $user_first_name = $user->first_name;
    $user_last_name = $user->last_name;

    $html ='
    <input type="hidden" name="id" value="' . $user_id . '" />
    <div class="col-md-12">
        <div class="form-group">
            <label for="f-name">First Name <span>*</span></label>
            <input type="text" name="first_name" id="f-name" class="form-control" value="'. ($user_first_name ?? '') .'" >
        </div>
    </div>

    <div class="col-md-12">
        <div class="form-group">
            <label for="l-name">Last Name <span>*</span></label>
            <input type="text" name="last_name" id="l-name" class="form-control" value="'. ($user_last_name ?? '') .'" >
        </div>
    </div>

    <div class="col-md-12">
        <div class="form-group">
            <label for="t-number">Telephone Number <span>*</span></label>
            <input type="text" name="phone" id="t-number" class="form-control" value="'. ($user_phone ?? '') .'" >
        </div>
    </div> ';
    wp_send_json_success( compact('html') );
    die;
});

add_action( 'wp_ajax_cm_admin_update_client_details', function(){
    if(!isset($_POST['id']) || empty($_POST['id'])){
        wp_send_json_error(['msg' => 'Invalid request.']);
        die;
    }
    if(!isset($_POST['first_name']) || empty($_POST['first_name'])){
        $errors['first_name'] = 'This field is required.';
    }
    if(!isset($_POST['last_name']) || empty($_POST['last_name'])){
        $errors['last_name'] = 'This field is required.';
    }
    /* if(!isset($_POST['email']) || empty($_POST['email'])){
        $errors['email'] = 'This field is required.';
    }else if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
        $errors['email'] = 'Please enter a valid email address.';
    } */
    if(!isset($_POST['phone']) || empty($_POST['phone'])){
        $errors['phone'] = 'This field is required.';
    }else if(!preg_match('/^\+?[0-9]{1,4}?[0-9]{7,14}$/', $_POST['phone'])){
        $errors['phone'] = 'Please enter a valid phone number.';
    }
    if(!empty($errors)){
        wp_send_json_error(compact('errors'));
        die;
    }

    $user = get_user_by('ID', intval($_POST['id']));
    global $wpdb;
    $db = $wpdb;

    $wp_user_id = $user->ID;

    $wp_user = get_userdata($wp_user_id);
    $user_data = (array) $wp_user->data;

    $user_data__ = [
        'ID'            => $wp_user_id,
        'first_name'    => sanitize_text_field($_POST['first_name']),
        'last_name'     => sanitize_text_field($_POST['last_name']),
        'display_name'  => sanitize_text_field($_POST['first_name']).' '.sanitize_text_field($_POST['last_name']),
    ];

    $result = wp_update_user($user_data__);

    update_user_meta($wp_user_id, 'phone', sanitize_text_field($_POST['phone']));

    $success = ['msg' => 'Changes Saved!'];
    wp_send_json_success( compact('success'));
    die;
});



add_action( 'wp_ajax_cm_admin_update_client_password', function(){
    if(isset($_POST['id']) && !empty($_POST['id']) && isset($_POST['password']) && !empty($_POST['password'])){
        global $wpdb;
        $user_id = intval($_POST['id']);
        $new_password = sanitize_text_field($_POST['password']);

        wp_set_password($new_password, $user_id);

        // $to = get_email_by_id_($user_id);
        // $subject = 'Reset Password By Admin';
        // ob_start();
        
        // include_once MYMAID_PLUGIN_DIR.'parts/admin-emails/staff-update-pass-email.php';
        // $message = ob_get_clean(); 
        // $headers = ['Content-Type: text/html'];
        // wp_mail($to, $subject, $message, $headers);

        wp_send_json_success(['msg' => 'Password updated successfully.'/* , 'to' => $to */]);
    } else {
        wp_send_json_error(['msg' => 'Invalid request.']);
    }
    
});


add_action( 'wp_ajax_cm_admin_create_client_details', function(){

    global $wpdb;
    $db = $wpdb;

    /* if(!isset($_POST['id']) || empty($_POST['id'])){
        wp_send_json_error(['msg' => 'Invalid request.']);
        die;
    } */

    if(!isset($_POST['first_name']) || empty($_POST['first_name'])){
        $errors['first_name'] = 'This field is required.';
    }
    if(!isset($_POST['last_name']) || empty($_POST['last_name'])){
        $errors['last_name'] = 'This field is required.';
    }
    if(!isset($_POST['email']) || empty($_POST['email'])){
        $errors['email'] = 'This field is required.';
    }else if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
        $errors['email'] = 'Please enter a valid email address.';
    }else{
        $existing__ = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $wpdb->users WHERE user_login = %s", $_POST['email']));
        if ($existing__ > 0) {
            $errors['email'] = 'Email '.$_POST['email'].' already in use.';
        }
    }
    if(!isset($_POST['phone']) || empty($_POST['phone'])){
        $errors['phone'] = 'This field is required.';
    }else if(!preg_match('/^\+?[0-9]{1,4}?[0-9]{7,14}$/', $_POST['phone'])){
        $errors['phone'] = 'Please enter a valid phone number.';
    }
    
    if(!empty($errors)){
        wp_send_json_error(compact('errors'));
        die;
    }

    $password = wp_generate_password(12, true);

    $user_data = [
        'first_name' => sanitize_text_field($_POST['first_name']),
        'last_name'  => sanitize_text_field($_POST['last_name']),
        'user_login' => sanitize_text_field($_POST['email']),
        'user_pass'  => $password,
        'user_email' => sanitize_text_field($_POST['email']),
        'role'       => 'client',
    ];
            
    $wp_user_id = wp_insert_user($user_data);

    if (is_wp_error($wp_user_id)) {
        $msg = $wp_user_id->get_error_message();
        wp_send_json_error( compact('msg') );
        die;
    }

    update_user_meta($wp_user_id, 'is_verified_account', '1');
    update_user_meta($wp_user_id, 'phone', sanitize_text_field($_POST['phone']));

    $to = $user_data['user_email'];
    $subject = 'Welcome to Casa Media';
    ob_start();
    include CM_EMAILS_DIR . 'user-register-email.php';
    $message = ob_get_clean(); 
    $headers = ['Content-Type: text/html'];
    // $message = "Hi {$user_data['first_name']},\n\nYour account has been created successfully.\n\nLogin: {$user_data['user_login']}\nPassword: {$password}\n\nPlease log in and change your password.\n\nThank you!";
    wp_mail($to, $subject, $message, $headers);

    $success = ['msg' => 'Changes Saved!'];
    wp_send_json_success( compact('success'));
    die;
});
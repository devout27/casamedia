<?php

/**
 * Plugin Name: CasaMedia
 * Description: A plugin built for Client Portal.
 * Version: 1.0.0
 * Author: Devout Tech Team
 * Author URI: https://devouttechconsultants.com/
 * License: GPL2
 **/

if (!defined('ABSPATH')) {
    exit;
}

define('CM_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CM_PLUGIN_URL', plugin_dir_url(__FILE__));

define('CM_EMAILS_DIR', CM_PLUGIN_DIR . 'parts/emails/');

define('CM_ADMIN_CHANNEL', 'cm-admin-channel');
define('CM_ADMIN_NOTICE', 'cm-admin-notification');

define('CM_USER_CHANNEL', 'cm-user-channel');
define('CM_USER_NOTICE', 'cm-user-notification');

define('CM_ERROR_LOG', CM_PLUGIN_DIR . 'cm_error.log');
@ini_set('log_errors', 'On');

date_default_timezone_set('Europe/Amsterdam');

/* Admin */
require_once CM_PLUGIN_DIR . 'includes/class-cm-admin.php';
require_once CM_PLUGIN_DIR . 'includes/class-cm-clients.php';
require_once CM_PLUGIN_DIR . 'includes/cm-maps-photos-posts.php';

/* User End */
require_once CM_PLUGIN_DIR . 'includes/class-cm-frontend.php';

/* ------------------------------------------------------------------------ */
require_once CM_PLUGIN_DIR . 'includes/cm-functions.php';

/**
 * Class CasaMedia
 *
 * Singleton class for initializing the CasaMedia plugin.
 * Handles loading of admin and frontend components.
 *
 * Methods:
 * - get_instance(): Returns the singleton instance of CasaMedia.
 * - __construct(): Registers the 'plugins_loaded' action hook.
 * - init(): Initializes admin and frontend classes based on context.
 */

final class CasaMedia
{
    private static $instance = null;

    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('plugins_loaded', [$this, 'init']);
    }

    public function init() {
        if (is_admin() || current_user_can('administrator')) {
            new CasaMedia_Admin();
        }
        new CasaMedia_Frontend();
    }
}

CasaMedia::get_instance();

register_activation_hook(__FILE__, function () {

    global $wpdb;
    $charset = $wpdb->get_charset_collate();
    $charset_collate = "DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    $sql = [];

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    foreach ($sql as $query) {
        dbDelta($query);
    }
});

register_deactivation_hook(__FILE__, function () {
    // ---
});

<?php

if (!defined('ABSPATH')) {
    exit;
}

class CasaMedia_Admin {

    public $cm_templates = [
        'login.php'             => 'CasaMedia Login',
        'forgot-password.php'   => 'CasaMedia Forgot Password',
        'change-password.php'   => 'CasaMedia Change Password',
    ];

    public $_submenus = [];

    public function __construct() {
        global $wpdb;

        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
        add_action('admin_init', [$this, 'cm_admin_init']);
        add_filter('theme_page_templates', [$this, 'cm_register_page_templates']);
        add_action('init', [$this, 'cm_admin_options']);
        add_action('admin_footer', [$this, 'cm_admin_footer']);

        $this->_submenus = [
            ['page_title' => 'Clients List',    'menu_title' => 'Clients List',     'slug' => 'casamedia-clients',                          'handle' => ['CMAdminClients', 'render_clients_page']],
            ['page_title' => 'Location Photos', 'menu_title' => 'Location Photos',  'slug' => 'edit.php?post_type=cm_maps_photos',  'handle' => ''],
        ];
    }

    public function enqueue_admin_scripts() {
        global $post;
        if ((isset($_GET['page']) && strpos($_GET['page'], 'casamedia') !== false) || ($post && $post?->post_type == 'cm_maps_photos')) {
            wp_enqueue_script('jquery'); wp_enqueue_script('jquery-ui-sortable');

            wp_enqueue_media();
            // CSS
            wp_enqueue_style('casamedia-bootstrap-css', CM_PLUGIN_URL . 'css/bootstrap.min.css', [], '5.3.1', 'all');
            wp_enqueue_style('bootstrap-select', 'https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css', [], null, 'all');

            wp_enqueue_script('casamedia-bootstrap-js', CM_PLUGIN_URL . 'js/bootstrap.bundle.min.js', ['jquery'], '5.3.1', true);
            wp_enqueue_script('bootstrap-select', 'https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js', ['jquery'], null, true);
        }
        
        wp_enqueue_style('casamedia-admin-select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css');
        wp_enqueue_style('casamedia-admin-inputTags', CM_PLUGIN_URL . 'css/inputTags.css', [], null, 'all');
        wp_enqueue_style('bootstrap-normalize', 'https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.css', [], null, 'all');
        wp_enqueue_style('bootstrap-all.min', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css', [], null, 'all');
        wp_enqueue_style('jquery-tagsinput-css', 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.css');
        wp_enqueue_style('jquery-daterangepicker-css', 'https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css');
        wp_enqueue_style('casamedia-intlTelInput', 'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/css/intlTelInput.css', [], null, 'all');
        wp_enqueue_style('tippy-animations-scale', 'https://unpkg.com/tippy.js@6/animations/scale.css');
        wp_enqueue_style('casamedia-admin-style', CM_PLUGIN_URL . 'css/admin/style.css', [], time(), 'all');

        wp_enqueue_script('casamedia-admin-select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', array('jquery'));
        wp_enqueue_script('casamedia-admin-tagsinput', CM_PLUGIN_URL . 'js/inputTags.jquery.js', ['jquery'], null, true);
        wp_enqueue_script('bootstrap-locales', 'https://momentjs.com/downloads/moment-with-locales.js', ['jquery'], null, true);
        wp_enqueue_script('casamedia-intlTelInput-js', 'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/intlTelInput-jquery.min.js', ['jquery'], null, true);
        wp_enqueue_script('casamedia-intlTelInput-js-', 'https://cdn.jsdelivr.net/npm/intl-tel-input@17/build/js/intlTelInput.min.js', ['jquery'], null, true);
        wp_enqueue_script('casamedia-intlTelInput-js-utils', 'https://cdn.jsdelivr.net/npm/intl-tel-input@17/build/js/utils.js', ['jquery'], null, true);
        wp_enqueue_script('jquery-tagsinput-js', 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.min.js', ['jquery'], null, true);
        wp_enqueue_script('jquery-moment-js', 'https://cdn.jsdelivr.net/momentjs/latest/moment.min.js', ['jquery'], null, true);
        wp_enqueue_script('jquery-daterangepicker-js', 'https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js', ['jquery'], null, true);
        wp_enqueue_script('casamedia-popperjs-core', 'https://unpkg.com/@popperjs/core@2', ['jquery'], null, true);
        wp_enqueue_script('casamedia-admin-script', CM_PLUGIN_URL . 'js/admin/script.js', ['jquery', 'jquery-ui-sortable'], time() /* '1.0.20' */, true);

        wp_localize_script('casamedia-admin-script', 'cm_ajax_ob', [
            'ajax_url'      => admin_url('admin-ajax.php'),
            'nonce'         => wp_create_nonce('casamedia_nonce'),
            'admin_url'     => admin_url('admin.php?page='),
        ]);
    }

    public function add_admin_menu() {
        add_menu_page(
            'CasaMedia',
            'CasaMedia',
            'manage_options',
            'casamedia' , // 'casamedia',
            [$this, 'render_admin_page'],
            // 'dashicons-buddicons-groups',
            CM_PLUGIN_URL . 'img/Site-Favicon.svg',
            26
        );

        foreach($this->_submenus as $key => $menu) {
            add_submenu_page(
                'casamedia',
                $menu['page_title'],
                $menu['menu_title'],
                'manage_options',
                $menu['slug'],
                $menu['handle'],
            );
        }

        global $submenu;
        if (isset($submenu['casamedia'])) {
            unset($submenu['casamedia'][0]);
        } 
    }

    public function cm_register_page_templates($defualt_templates) {
        return array_merge($defualt_templates, $this->cm_templates);
    }

    public function render_admin_page() {
        echo 'Nothing...';
        // require_once CM_PLUGIN_DIR . 'partials/reander-calender.php';
    }

    public function cm_admin_options() {
        add_role(
            'client',
            'Client',
            [
                'read' => true,
                'edit_posts' => false,
                'delete_posts' => false,
            ]
        );
    }

    public function cm_admin_init() {
        CMAdminClients::clients_actions();
    }

    public function cm_admin_footer() {
        // ---
    }
    
}
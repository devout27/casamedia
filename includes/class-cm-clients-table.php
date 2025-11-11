<?php

if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class CMAdminClientsTable extends WP_List_Table {

    private $trash, $total_users;

    public function __construct() {
        global $wpdb;   
        parent::__construct([
            'singular' => 'client',
            'plural'   => 'clients',
            'ajax'     => false
        ]);

    }

    public function get_columns() {
        return [
            'cb'                => '<input type="checkbox" />',
            // 'name'              => 'Name',
            'first_name'        => 'First Name',
            'last_name'         => 'Last Name',
            'user_email'        => 'Email',
            'phone'             => 'Phone',
            'user_registered'   => 'Registered',
            'actions'           => 'Actions'
        ];
    }

    public function prepare_items() {
        global $wpdb;

        $per_page     = 20;
        $current_page = $this->get_pagenum();
        $offset       = ($current_page - 1) * $per_page;

        $columns = $this->get_columns();
        $hidden = [];
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = [$columns, $hidden, $sortable];

        $search_term = isset($_REQUEST['s']) ? sanitize_text_field($_REQUEST['s']) : '';

        $deleted_at = $_REQUEST['deleted_at'] ?? '';

        $order_by = isset($_REQUEST['orderby']) ? sanitize_text_field($_REQUEST['orderby']) : 'registered';
        $order = isset($_REQUEST['order']) && in_array(strtoupper($_REQUEST['order']), ['ASC', 'DESC']) ? strtoupper($_REQUEST['order']) : 'DESC';

        $allowed_orderby = [
            'user_registered' => 'user_registered',
            'first_name' => 'first_name',
            'last_name'  => 'last_name',
            'user_email' => 'user_email',
        ];
        
        $meta_query = [];

        if (!empty($deleted_at)) {
            $this->trash = true;
            $meta_query[] = [
                'key'     => 'deleted_at',
                'compare' => 'EXISTS',
            ];
        } else {
            $meta_query[] = [
                'key'     => 'deleted_at',
                'compare' => 'NOT EXISTS',
            ];
        }

        // Build query arguments for WP_User_Query
        $args = [
            'role'              => 'client',
            'number'            => $per_page,
            'paged'             => $current_page,
            'search'            => '*' . esc_attr($search_term) . '*',
            'search_columns'    => ['user_login', 'user_email', 'display_name'],
            // 'orderby'        => 'registered',
            // 'order'          => 'DESC',
            'meta_query'        => $meta_query,
        ];
        if (isset($allowed_orderby[$order_by])) {
            $args['orderby'] = $allowed_orderby[$order_by];
            $args['order'] = $order;
        }else{
            $args['orderby'] = 'registered';
            $args['order'] = 'DESC';
        }

        // Query users
        $user_query = new WP_User_Query($args);

        // Get user results and total count
        $users = array_map(function($user) {
            return [
                'ID'                => $user->ID,
                'first_name'        => $user->first_name,
                'last_name'         => $user->last_name,
                'user_login'        => $user->user_login,
                'user_email'        => $user->user_email,
                'display_name'      => $user->display_name,
                'roles'             => $user->roles,
                'user_registered'   =>  $user->user_registered,
            ];
        }, $user_query->get_results());
        $total_users = $user_query->get_total();
        $this->total_users = $total_users;
        // Set the items for display
        $this->items = (array)$users;

        // Set pagination arguments
        $this->set_pagination_args([
            'total_items' => $total_users,
            'per_page'    => $per_page,
            'total_pages' => ceil($total_users / $per_page),
        ]);
    }

    public function column_cb($item) {
        return sprintf('<input type="checkbox" name="client_ids[]" value="%s" />', $item['ID']);
    }

    public function column_default($item, $column_name) {
        switch ($column_name) {
            case 'name':
                return get_name_by_id_($item['ID']);
            case 'first_name':
                return ($item['first_name']);
            case 'last_name':
                return ($item['last_name']);
            case 'user_registered': 
                return date('D d M Y H:i', strtotime($item['user_registered']));
            case 'actions':
                return $this->column_actions($item);
            case 'phone':
                return get_user_meta( $item['ID'], 'phone', true )?:'--';
            default:
                return $item[$column_name] ?? '--';
        }
    }

    public function column_actions($item) {
        $edit_btn = '';
        if(!$this->trash) {
            $edit_btn .= '<button class="btn btn-sm btn-warning js-change-client-password--" type="button" data-id=' . $item['ID'] . ' data-bs-toggle="tooltip" data-bs-title="Change Password"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M18 8H20C20.5523 8 21 8.44772 21 9V21C21 21.5523 20.5523 22 20 22H4C3.44772 22 3 21.5523 3 21V9C3 8.44772 3.44772 8 4 8H6V7C6 3.68629 8.68629 1 12 1C15.3137 1 18 3.68629 18 7V8ZM5 10V20H19V10H5ZM11 14H13V16H11V14ZM7 14H9V16H7V14ZM15 14H17V16H15V14ZM16 8V7C16 4.79086 14.2091 3 12 3C9.79086 3 8 4.79086 8 7V8H16Z"></path></svg></button>';
            $edit_btn .= '<button class="btn btn-sm btn-warning js-change-client-email--" type="button" data-id=' . $item['ID'] . ' data-bs-toggle="tooltip" data-bs-title="Change Email"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M20 7.23792L12.0718 14.338L4 7.21594V19H14V21H3C2.44772 21 2 20.5523 2 20V4C2 3.44772 2.44772 3 3 3H21C21.5523 3 22 3.44772 22 4V13H20V7.23792ZM19.501 5H4.51146L12.0619 11.662L19.501 5ZM17.05 19.5485C17.0172 19.3706 17 19.1873 17 19C17 18.8127 17.0172 18.6294 17.05 18.4515L16.0359 17.866L17.0359 16.134L18.0505 16.7197C18.3278 16.4824 18.6489 16.2948 19 16.1707V15H21V16.1707C21.3511 16.2948 21.6722 16.4824 21.9495 16.7197L22.9641 16.134L23.9641 17.866L22.95 18.4515C22.9828 18.6294 23 18.8127 23 19C23 19.1873 22.9828 19.3706 22.95 19.5485L23.9641 20.134L22.9641 21.866L21.9495 21.2803C21.6722 21.5176 21.3511 21.7052 21 21.8293V23H19V21.8293C18.6489 21.7052 18.3278 21.5176 18.0505 21.2803L17.0359 21.866L16.0359 20.134L17.05 19.5485ZM20 20C20.5523 20 21 19.5523 21 19C21 18.4477 20.5523 18 20 18C19.4477 18 19 18.4477 19 19C19 19.5523 19.4477 20 20 20Z"></path></svg></button>';
            $edit_btn .= '<button class="btn btn-sm btn-primary edit-btn js-change-client-details--" type="button" data-id=' . $item['ID'] . '>Edit</button>';
        }else{
            $edit_btn .= '<a href="'.admin_url('admin.php?page=casamedia-clients&action=restore_client&id=' . intval($item['ID'])) .'" class="btn btn-sm btn-dark">Restore</a>';
        }

        $delete_btn = sprintf(
            '<button type="button" class="btn btn-sm btn-danger ' . ($this->trash ? 'delete' : 'trash') . '-btn" data-id="%d" data-bs-toggle="modal" data-bs-target="#' . ($this->trash? 'delete' : 'trash') . 'ClientModal">' . ($this->trash ? 'Delete' : 'Trash').'</button>',
            $item['ID']
        );

        return '<div class="action-buttons">' . $edit_btn . ' ' . $delete_btn  . '</div>';
    }

    public function get_sortable_columns() {
        return [
            /* 'first_name'        => ['first_name', false],
            'last_name'         => ['last_name', false],
            'user_email'        => ['user_email', false],
            'phone'             => ['phone', false], */
            'user_registered'   => ['user_registered', true]
        ];
    }

    public function single_row($item) {
        $class = '';    
        echo '<tr class="' . esc_attr($class) . '">';
        $this->single_row_columns($item);
        echo '</tr>';
    }

    public function get_bulk_actions() {
        if($this->trash){
            return [
                'bulk-delete' => 'Delete',
                'bulk-restore' => 'Restore',
            ];
        }
        return [
            'bulk-trash' => 'Trash',
        ];
    }

    public function process_bulk_action() {
        global $wpdb;
        if ($this->current_action() === 'bulk-delete' && !empty($_POST['client_ids'])) {
            $ids = array_map('absint', $_POST['client_ids']);
            foreach($ids as $id){
                $wp_user_id = $id;
                if (get_userdata($wp_user_id)) {
                    wp_delete_user($wp_user_id);
                }
            }
            update_option( 'cm_client_message', 'Clients deleted successfully.' );
            wp_redirect(admin_url('admin.php?page=casamedia-clients'));
            exit;
        }

        if ($this->current_action() === 'bulk-trash' && !empty($_REQUEST['client_ids'])) {
            foreach($_REQUEST['client_ids'] as $key => $id) {
                $wp_user_id = $id;
                if (get_userdata($wp_user_id)) {
                    update_user_meta( $wp_user_id, 'deleted_at', current_time('mysql'));    
                }
            }
            update_option( 'cm_client_message', 'Clients moved to trash successfully.' );
            wp_redirect(admin_url('admin.php?page=casamedia-clients'));
            exit;
        }

        if ($this->current_action() === 'bulk-restore' && !empty($_REQUEST['client_ids'])) {
            foreach($_REQUEST['client_ids'] as $key => $id) {
                $wp_user_id = $id;
                if (get_userdata($wp_user_id)) {
                    delete_user_meta( $wp_user_id, 'deleted_at');
                }
            }
            update_option( 'cm_client_message', 'Clients restored successfully.' );
            wp_redirect(admin_url('admin.php?page=casamedia-clients'));
            exit;
        }
    }
    /* protected function extra_tablenav($which) {
        if ($which === 'top') {
            $selected = $_REQUEST['service_type'] ?? '';
            ?>
            <div class="alignleft actions bulkactions">
                <select name="service_type">
                    <option value="">All Services</option>
                    <option value="home_cleaning" <?php selected($selected, 'home_cleaning'); ?>>Home Cleaning</option>
                    <option value="end_cleaning" <?php selected($selected, 'end_cleaning'); ?>>End Cleaning</option>
                </select>
                <?php submit_button('Filter', 'action', '', false); ?>
            </div>
            <?php
        }
    } */

    protected function get_views() {
    
        $options = [
            'All'   => ['query' => ['all' => ''], 'count' => count(get_users([
                'role' => 'client',
                'meta_query' => [
                    [
                        'key' => 'deleted_at',
                        'compare' => 'NOT EXISTS'
                    ]
                ],
                'count_total' => true,
            ]))],
            'Trash' => ['query' => ['deleted_at' => 'true'], 'count' => count(get_users([
                'role' => 'client',
                'meta_query' => [
                    [
                        'key' => 'deleted_at',
                        'compare' => 'EXISTS'
                    ]
                ],
                'count_total' => true,
            ]))],
        ];
    
        $views = [];
    
        foreach ($options as $key => $option) {
            $status = $_REQUEST[key($option['query'])] ?? ($_REQUEST['deleted_at'] ?? '');
            // echo $status;
            $url = add_query_arg([
                $option['query'],
                'page'   => $_REQUEST['page'],
            ], admin_url('admin.php'));

            $class = ($status === current($option['query'])) ? 'class="current"' : '';
            $label = $key . " <span class='count'>({$option['count']})</span>";
    
            $views[$key] = "<a href='$url' $class>$label</a>";
        }
    
        return $views;
    }
}

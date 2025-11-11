<?php

if (!defined('ABSPATH')) {
    exit;
}

add_action('init', 'register_maps_photos_post_type');

function register_maps_photos_post_type() {
    register_post_type('cm_maps_photos', [
        'labels' => [
            'name'                     => __('Location Photos', 'casamedia'),
            'singular_name'            => __('Location Photo', 'casamedia'),
            'menu_name'                => __('Location Photos', 'casamedia'),
            'name_admin_bar'           => __('Location Photo', 'casamedia'),
            'add_new'                  => __('Add Location', 'casamedia'),
            'add_new_item'             => __('Add New Location', 'casamedia'),
            'edit_item'                => __('Edit Location', 'casamedia'),
            // 'new_item'                 => __('New Location Photo', 'casamedia'),
            // 'view_item'                => __('View Location Photo', 'casamedia'),
            // 'view_items'               => __('View Location Photos', 'casamedia'),
            'search_items'             => __('Search Location', 'casamedia'),
            'not_found'                => __('No Location found', 'casamedia'),
            'not_found_in_trash'       => __('No Location found in Trash', 'casamedia'),
            'all_items'                => __('All Locations', 'casamedia'),
            // 'archives'                 => __('Location Photo Archives', 'casamedia'),
            // 'attributes'               => __('Location Photo Attributes', 'casamedia'),
            // 'insert_into_item'         => __('Insert into Location Photo', 'casamedia'),
            // 'uploaded_to_this_item'    => __('Uploaded to this Location Photo', 'casamedia'),
            // 'featured_image'           => __('Featured Image', 'casamedia'),
            // 'set_featured_image'       => __('Set featured image', 'casamedia'),
            // 'remove_featured_image'    => __('Remove featured image', 'casamedia'),
            // 'use_featured_image'       => __('Use as featured image', 'casamedia'),
            // 'filter_items_list'        => __('Filter Location Photos list', 'casamedia'),
            // 'items_list_navigation'    => __('Location Photos list navigation', 'casamedia'),
            // 'items_list'               => __('Location Photos list', 'casamedia'),
        ],
        'public'              => false, // not visible on front-end
        'show_ui'             => true,  // allows admin UI
        'show_in_menu'        => false, // hide from sidebar
        'supports'            => ['title',/*  'editor', 'thumbnail' */],
        'capability_type'     => 'post',
        'menu_position'       => null,
        'rewrite'             => false,
        'has_archive'         => false,
    ]);
}

add_action('add_meta_boxes', 'cm_maps_photos_meta_box');
add_action('save_post', 'cm_save_maps_photos_meta');
add_action('admin_enqueue_scripts', 'cm_admin_scripts');

function cm_admin_scripts() {
    if (get_current_screen()->post_type !== 'cm_maps_photos') return;
    
    wp_enqueue_media();
    wp_enqueue_script('google-maps', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyCMP-HZ-iK2gRgyU0U1Fp0qucgukNCJh7o&libraries=places');

}

function cm_maps_photos_meta_box() {
    add_meta_box(
        'cm_maps_photos_meta',
        'Location & Photos',
        'cm_maps_photos_meta_callback',
        'cm_maps_photos',
        'normal',
        'high'
    );

    add_meta_box(
        'cm_extra_meta',
        'Extra Details',
        'cm_extra_meta_callback',
        'cm_maps_photos',
        'normal',
        'default'
    );

    add_meta_box(
        'cm_client_users_meta',
        'Client Users',
        'cm_client_users_meta_callback',
        'cm_maps_photos',
        'side',
        'default'
    );
}

function cm_maps_photos_meta_callback($post) {
    wp_nonce_field('cm_maps_photos_meta', 'cm_maps_photos_nonce');
    
    $address = get_post_meta($post->ID, '_cm_address', true);
    $lat = get_post_meta($post->ID, '_cm_lat', true);
    $lng = get_post_meta($post->ID, '_cm_lng', true);
    $photos = get_post_meta($post->ID, '_cm_photos', true);
    ?>
    <div class="cm-map-photos-meta-box">
        <div class="form-group">
            <label for="cm_address" class="form-label">Address *</label>
            <input type="text" id="cm_address" name="cm_address" class="form-control" value="<?php echo esc_attr($address); ?>" required>
        </div>
        <input type="hidden" id="cm_lat" name="cm_lat" value="<?php echo esc_attr($lat); ?>">
        <input type="hidden" id="cm_lng" name="cm_lng" value="<?php echo esc_attr($lng); ?>">
        
        <div id="cm_map" style="height: 400px; margin: 20px 0;"></div>

        <div class="cm-photos-section">
            <h4 class="mb-3">Photos</h4>
            <div id="cm_photos_preview" class="cm-photos-preview">
                <?php
                if ($photos) {
                    foreach (explode(',', $photos) as $photo_id) {
                        echo '<div class="photo-item" data-id="' . esc_attr($photo_id) . '">';
                        echo wp_get_attachment_image($photo_id, 'thumbnail');
                        echo '<button type="button" class="remove-photo" data-bs-toggle="tooltip" data-bs-title="Remove">×</button>';
                        echo '</div>';
                    }
                }
                ?>
                <div id="cm_upload_photos" class="cm-select-maps-photos" data-bs-toggle="tooltip" data-bs-title="Click to Add Photos"><i class="fa-solid fa-plus"></i></div>
            </div>
            <input type="hidden" id="cm_photos" name="cm_photos" value="<?php echo esc_attr($photos); ?>">
        </div>
    </div>
    <?php
}

function cm_save_maps_photos_meta($post_id) {
    if (!isset($_POST['cm_maps_photos_nonce']) || 
        !wp_verify_nonce($_POST['cm_maps_photos_nonce'], 'cm_maps_photos_meta')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;
    if ('cm_maps_photos' != get_post_type($post_id)) return;

    if (empty($_POST['cm_address'])) {
        wp_die('Address is required');
    }

    update_post_meta($post_id, '_cm_address', sanitize_text_field($_POST['cm_address']));
    update_post_meta($post_id, '_cm_lat', sanitize_text_field($_POST['cm_lat']));
    update_post_meta($post_id, '_cm_lng', sanitize_text_field($_POST['cm_lng']));
    update_post_meta($post_id, '_cm_photos', sanitize_text_field($_POST['cm_photos']));

    if (isset($_POST['cm_description'])) {
        $allowed = wp_kses_allowed_html('post');
        update_post_meta($post_id, '_cm_description', wp_kses($_POST['cm_description'], $allowed));
    } else {
        delete_post_meta($post_id, '_cm_description');
    }

    if (isset($_POST['cm_tags'])) {
        $raw_tags = wp_strip_all_tags($_POST['cm_tags']);
        $tags_array = array_filter(array_map('trim', explode(',', $raw_tags)), 'strlen');
        $tags_array = array_map('sanitize_text_field', $tags_array);
        $tags_array = array_values(array_unique($tags_array));
        $tags_string = implode(',', $tags_array);
        update_post_meta($post_id, '_cm_tags', $tags_string);
    } else {
        delete_post_meta($post_id, '_cm_tags');
    }

    if (isset($_POST['cm_photographer'])) {
        update_post_meta($post_id, '_cm_photographer', sanitize_text_field($_POST['cm_photographer']));
    } else {
        delete_post_meta($post_id, '_cm_photographer');
    }

    $users = isset($_POST['cm_client_users']) ? array_map('intval', $_POST['cm_client_users']) : array();
    update_post_meta($post_id, '_cm_client_users', $users);

    $all_client_users = get_users(array('role' => 'client'));
    
    foreach ($all_client_users as $client_user) {

        $uid = (int) $client_user->ID;
        $user_maps = get_user_meta($uid, '_cm_maps_photos', true);
        $user_maps = json_decode($user_maps ?? '[]', true);
        if (!is_array($user_maps)) {
            $user_maps = array();
        }

        if (in_array($uid, $users, true)) {
            if (!in_array($post_id, $user_maps, true)) {
                $user_maps[] = (int) $post_id;
                update_user_meta($uid, '_cm_maps_photos', json_encode(array_values($user_maps)));
            }
        } else {

            if (in_array($post_id, $user_maps, true)) {
                $user_maps = array_values(array_diff($user_maps, array($post_id)));
                if (empty($user_maps)) {
                    delete_user_meta($uid, '_cm_maps_photos');
                } else {
                    update_user_meta($uid, '_cm_maps_photos', json_encode($user_maps));
                }
            }
        }
    }
}

add_filter('gettext', function($translated_text, $text, $domain) {
    global $post, $typenow;

    if ($typenow == 'cm_maps_photos') {
        switch ($text) {
            case 'Add Post':
                return __('Add New Location', 'casamedia');
            case 'Add title':
                return __('Add Location Title', 'casamedia');
            case 'Add New Post':
                return __('Add New Location', 'casamedia');
            case 'Edit Post':
                return __('Edit Location', 'casamedia');
            case 'Publish':
                return __('Save Location', 'casamedia');
            case 'Update':
                return __('Update Location', 'casamedia');
            case 'View Post':
                return __('View Location', 'casamedia');
        }
    }

    return $translated_text;
}, 10, 3);

function cm_extra_meta_callback($post) {

    $description = get_post_meta($post->ID, '_cm_description', true);
    $tags = get_post_meta($post->ID, '_cm_tags', true);
    $photographer = get_post_meta($post->ID, '_cm_photographer', true);

    // Description editor (allow media buttons)
    $editor_settings = array(
        'textarea_name' => 'cm_description',
        'media_buttons' => true,
        'textarea_rows' => 8,
        'teeny'         => false,
    );

    echo '<div class="cm-map-photos-meta-box">';
    echo '<label for="cm_description" class="form-label">Description</label>';
    wp_editor($description, 'cm_description', $editor_settings);

    echo '<div class="mb-3 mt-3"><label for="cm_tags" class="form-label">Tags</label>';
    echo '<input type="text" id="cm_tags" name="cm_tags" class="form-control" value="' . esc_attr($tags) . '" placeholder="Add tags..."></div>';

    echo '<div class="mb-3"><label for="cm_photographer" class="form-label">Photographer</label>';
    echo '<input type="text" id="cm_photographer" name="cm_photographer" class="form-control" value="' . esc_attr($photographer) . '" placeholder="Photographer name or credit"></div>';
    echo '</div>';
}

function cm_client_users_meta_callback($post) {
    
    $selected_users = get_post_meta($post->ID, '_cm_client_users', true);
    if (!is_array($selected_users)) $selected_users = array();
    
    $client_users = get_users(array(
        'role'       => 'client',
        'meta_query' => array(
            array(
                'key'     => 'deleted_at',
                'compare' => 'NOT EXISTS',
            ),
        ),
    ));
    
    echo '<div class="cm-client-users-select" > <select id="cm_client_users" class="select2 d-none" name="cm_client_users[]" multiple="multiple">';
    foreach ($client_users as $user) {
        $selected = in_array($user->ID, $selected_users) ? 'selected' : '';
        echo sprintf(
            '<option value="%d" %s>%s</option>',
            $user->ID,
            $selected,
            esc_html($user->display_name)
        );
    }
    echo '</select> </div>';
}

add_filter('manage_edit-cm_maps_photos_columns', 'cm_maps_photos_custom_columns');
function cm_maps_photos_custom_columns($columns) {
    $new = array();
    foreach ($columns as $key => $value) {
        $new[$key] = $value;
        if ($key === 'title') {
            $new['cm_address'] = __('Address', 'casamedia');
            $new['cm_photos_count'] = __('Photos', 'casamedia');
        }
    }
    return $new;
}

add_action('manage_cm_maps_photos_posts_custom_column', 'cm_maps_photos_custom_column_content', 10, 2);
function cm_maps_photos_custom_column_content($column, $post_id) {
    if ($column === 'cm_address') {
        $address = get_post_meta($post_id, '_cm_address', true);
        echo esc_html($address ?: '');
    }

    if ($column === 'cm_photos_count') {
        $photos = get_post_meta($post_id, '_cm_photos', true);

        $count = 0;
        if (is_string($photos) && $photos !== '') {
            $maybe_json = json_decode($photos, true);
            if (is_array($maybe_json)) {
                $count = count($maybe_json);
            } else {
                $ids = array_filter(array_map('trim', explode(',', $photos)), 'strlen');
                $count = count($ids);
            }
        } elseif (is_array($photos)) {
            $count = count($photos);
        }

        echo esc_html((int) $count);
    }
}
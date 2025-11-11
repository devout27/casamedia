<?php

/* Template Name: CM Client Locations */

if (!is_user_logged_in()) {
    wp_redirect(site_url('/sign-in/'));
    exit;
}

$user_id = get_current_user_id();

$map_ids = get_user_meta($user_id, '_cm_maps_photos', true);
$map_ids = json_decode($map_ids ?? '[]', true);



if (!is_array($map_ids)) {
    $map_ids = [];
}

$user_maps = [];

if(!empty($map_ids)){
    $user_maps = get_posts([
        'post_type' => 'cm_maps_photos',  // or your custom type
        'post__in'  => $map_ids,
        'orderby'   => 'post__in', // keep the same order as array
        'numberposts' => -1,
    ]);
}

get_header();

?>

<!-- Layout -->
<div class="layout">

    <?php include CM_PLUGIN_DIR . 'parts/client/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="content">
        <div class="main-panel">
            <div class="inner-panel">
                <div class="map-sec mb-4">
                    <div class="skeleton-loader map-skel cm-loader">
                        <div class="skeleton skeleton-map"></div>
                    </div>
                    <div class="inner-map">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d14610.314840408124!2d76.76814094741106!3d30.73192056098334!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390fed0be66ec96b%3A0xa5ff67f9527319fe!2sChandigarh!5e0!3m2!1sen!2sin!4v1762611219772!5m2!1sen!2sin"
                            width="600" height="450" style="border: 0" allowfullscreen="" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>


                <div class="pictures-sec all-sec mb-4">
                    <div class="inner-pic-sec">
                        <div class="inner-grid-sec">
                            <div class="left-panel">
                                <div class="left-pic">
                                    <div class="skeleton-loader images-skel cm-loader">
                                        <div class="skeleton skeleton-images"></div>
                                        <div class="skeleton skeleton-images"></div>
                                        <div class="skeleton skeleton-images"></div>
                                        <div class="skeleton skeleton-images"></div>
                                        <div class="skeleton skeleton-images"></div>
                                        <div class="skeleton skeleton-images"></div>
                                        <div class="skeleton skeleton-images"></div>
                                        <div class="skeleton skeleton-images"></div>
                                    </div>

                                    <div class="inner-pic-left">
                                        <div class="location-images">
                                            <img src="<?php echo CM_PLUGIN_URL . '/img/loc-img.webp'; ?>" alt="Chandigarh" class="item1" />
                                            <img src="<?php echo CM_PLUGIN_URL . '/img/loc-img.webp'; ?>" alt="Chandigarh" class="item2" />
                                            <img src="<?php echo CM_PLUGIN_URL . '/img/loc-img.webp'; ?>" alt="Chandigarh" class="item3" />
                                            <img src="<?php echo CM_PLUGIN_URL . '/img/loc-img.webp'; ?>" alt="Chandigarh" class="item4" />
                                            <img src="<?php echo CM_PLUGIN_URL . '/img/loc-img.webp'; ?>" alt="Chandigarh" class="item5" />
                                            <img src="<?php echo CM_PLUGIN_URL . '/img/loc-img.webp'; ?>" alt="Chandigarh" class="item6" />
                                            <img src="<?php echo CM_PLUGIN_URL . '/img/loc-img.webp'; ?>" alt="Chandigarh" class="item7" />
                                            <img src="<?php echo CM_PLUGIN_URL . '/img/loc-img.webp'; ?>" alt="Chandigarh" class="item8" />
                                            <img src="<?php echo CM_PLUGIN_URL . '/img/loc-img.webp'; ?>" alt="Chandigarh" class="item9" />
                                            <img src="<?php echo CM_PLUGIN_URL . '/img/loc-img.webp'; ?>" alt="Chandigarh" class="item10" />
                                            <img src="<?php echo CM_PLUGIN_URL . '/img/loc-img.webp'; ?>" alt="Chandigarh" class="item11" />
                                            <img src="<?php echo CM_PLUGIN_URL . '/img/loc-img.webp'; ?>" alt="Chandigarh" class="item12" />
                                            <img src="<?php echo CM_PLUGIN_URL . '/img/loc-img.webp'; ?>" alt="Chandigarh" class="item13" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="right-panel">
                                <div class="right-pic">
                                    <!-- Skeleton Loader -->
                                    <div class="skeleton-loader cm-loader">
                                        <div class="skeleton skeleton-select"></div>
                                        <div class="skeleton skeleton-title"></div>
                                        <div class="skeleton skeleton-description"></div>
                                        <div class="tags-skel">
                                            <div class="skeleton skeleton-tag"></div>
                                            <div class="skeleton skeleton-tag"></div>
                                            <div class="skeleton skeleton-tag"></div>
                                        </div>
                                        <div class="skeleton skeleton-line"></div>
                                        <div class="skeleton skeleton-line short"></div>
                                    </div>

                                    <div class="bottom-content">
                                        <div class="location-select">
                                            <label for="location">Choose Location:</label>
                                            <div class="select-wrapper">
                                                <select name="map_locations">
                                                    <?php foreach($user_maps as $map) { ?>
                                                        <option value="<?php echo $map->ID; ?>"><?php echo $map->post_title; ?></option>
                                                    <?php } if(empty($user_maps)) { ?>
                                                        <option value="" selected disabled>No Locations</option>
                                                    <?php } ?>
                                                </select>
                                                <i class="fa-solid fa-chevron-down"></i>
                                            </div>
                                        </div>
                                        <div class="description-sec">
                                            <div class="description text-com">
                                                <h2 class="loc-title">Description</h2>
                                                <p class="loc-desc">
                                                    Chandigarh is a city and union territory in India
                                                    that serves as the capital of the two neighboring
                                                    states of Punjab and Haryana. Known for its modern
                                                    architecture and urban design, Chandigarh was
                                                    planned by the renowned architect Le Corbusier in
                                                    the 1950s.
                                                </p>
                                            </div>

                                            <div class="tags text-com">
                                                <h2 class="loc-title">Tags</h2>
                                                <div class="tags-multi">
                                                    <span> Tag 1 </span>
                                                    <span> Tag 2 </span>
                                                    <span> Tag 3 </span>
                                                </div>
                                            </div>
                                            <div class="photographer text-com">
                                                <h2 class="loc-title">Photographer</h2>
                                                <p class="loc-desc">John Doe</p>
                                            </div>
                                        </div>
                                    </div>
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
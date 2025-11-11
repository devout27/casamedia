<?php 
if (!defined('ABSPATH')) exit;
?>

<aside class="sidebar">
    <ul>
        <li>
            <a href="<?php echo site_url( '/client/dashboard/' ); ?>"><i class="fa-solid fa-gauge"></i> Dashboard</a>
        </li>
        <li>
            <a href="<?php echo site_url( '/client/locations/' ); ?>"><i class="fa-solid fa-location-dot"></i> Locations</a>
        </li>

        <li>
            <a href="<?php echo site_url( '/client/profile/' ); ?>"><i class="fa-solid fa-user"></i> Profile</a>
        </li>
        <!-- <li>
            <a href="<?php echo site_url( '/client/profile/' ); ?>"><i class="fa-solid fa-gear"></i> Settings</a>
        </li> -->
    </ul>
</aside>
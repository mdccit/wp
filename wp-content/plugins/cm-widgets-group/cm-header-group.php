<?php
/*
Plugin Name: Cm Widgets
Description: A custom header widget to replace the Elementor header widget.
Version: 1.0
Author: Qualitapps
Author URI: https://qualitapps.com
*/

// Ensure WordPress environment is properly loaded.
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Register the custom widget with Elementor
add_action('elementor/widgets/widgets_registered', function ($widgets_manager) {
    require_once plugin_dir_path(__FILE__) . 'widgets/cm-header-widget-group.php';

    // Register with fully qualified namespace
    $widgets_manager->register(new \CM_Widgets_Group\CM_Header_Group_Widget());
});

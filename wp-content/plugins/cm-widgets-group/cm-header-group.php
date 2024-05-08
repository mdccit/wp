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
    // Include the widget class file using the correct Elementor namespace.
    require_once(__DIR__ . '/widgets/cm-header-widget-group.php');

    // Register the widget class.
    $widgets_manager->register(new \Elementor\CM_Header_Group_Widget());
});
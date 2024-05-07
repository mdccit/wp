<?php
/*
Plugin Name: Cm Widgets
Description: A custom header widget to replace the Elementor header widget.
Version: 1.0
Author: Qualitapps
Author URI: https://qualitapps.com
*/

// Register the custom widget with Elementor
add_action('elementor/widgets/widgets_registered', function ($widgets_manager) {
    // Include the widget class file
    require_once(__DIR__ . '/widgets/cm-header-widget-group.php');

    // Register the widget class
    $widgets_manager->register(new \CM_Widgets_Group\CM_Header_Group_Widget());
});
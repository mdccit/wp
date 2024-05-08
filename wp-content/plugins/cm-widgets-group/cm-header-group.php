<?php
/*
Plugin Name: Cm Widgets
Description: A custom header widget to replace the Elementor header widget.
Version: 1.1
Author: Qualitapps
Author URI: https://qualitapps.com
*/
use CM_Widgets_Group\OSF_Elementor_Header_Group;
// Ensure WordPress environment is properly loaded.
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Register the custom widget with Elementor
add_action('elementor/widgets/widgets_registered', function ($widgets_manager) {
    require_once plugin_dir_path(__FILE__) . 'widgets/cm-header-widget-group.php';

    // Register with fully qualified namespace
    $widgets_manager->register(new OSF_Elementor_Header_Group());
});

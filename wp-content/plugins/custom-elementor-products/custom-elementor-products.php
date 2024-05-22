<?php
/*
Plugin Name: Custom Elementor Products
Description: A custom Elementor widget to override the default products widget in Medilazar Core.
Version: 1.0
Author: Your Name
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Include the custom widget file
require_once plugin_dir_path(__FILE__) . 'elementor/elementor-init.php';

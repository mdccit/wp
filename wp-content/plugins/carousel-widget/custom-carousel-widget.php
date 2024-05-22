<?php
/*
Plugin Name: Custom Elementor Carousel
Description: A custom Elementor widget to override the default carousel.
Version: 1.0
Author: Qualitapps
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Include the custom widget file
require_once plugin_dir_path( __FILE__ ) . 'elementor/elementor-init.php';

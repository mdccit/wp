<?php
/*
Plugin Name: Radios Tools
Plugin URI: https://themeforest.net/user/themexriver
Description: After install the Radios WordPress Theme, you must need to install this "radios Tools" first to get all functions of radios WP Theme.
Author: Raziul Islam
Author URI: http://themexriver.com/
Version: 1.1
Text Domain: radios-tools
*/
if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

define( 'RADIOS_VERSION', '1.0' );
define( 'RADIOS_ICON_VER', '1.2' );
define( 'RADIOS_DIR_PATH',plugin_dir_path(__FILE__) );
define( 'RADIOS_DIR_URL',plugin_dir_url(__FILE__) );
define( 'RADIOS_INC_PATH', RADIOS_DIR_PATH . '/inc' );
define( 'RADIOS_PLUGIN_IMG_PATH', RADIOS_DIR_URL . '/assets/img' );

/**
 * Css Framework Load
 */
if ( file_exists(RADIOS_DIR_PATH.'/lib/codestar-framework/codestar-framework.php') ) {
    require_once  RADIOS_DIR_PATH.'/lib/codestar-framework/codestar-framework.php';
}

// Load Script
function radios_frontend_scripts() {
    wp_enqueue_style( 'radios-admin-style', RADIOS_DIR_URL . "assets/css/admin-style.css");
}
add_action( 'admin_enqueue_scripts', 'radios_frontend_scripts' );

/**
 * Post Widget With Thumbnail
 */
function radios_cw_wisget(){
    register_widget( 'Radios_Category_List' );
    register_widget( 'Radios_Social_Icons' );
    register_widget( 'Radios_Recent_Posts' );
}
add_action('widgets_init', 'radios_cw_wisget');

//* To remove the Font Awesome http request as well
add_action( 'elementor/frontend/after_enqueue_styles', function () { wp_dequeue_style( 'e-animations' ); } );



function radios_de_reg() {
    wp_deregister_style( 'elementor-icons-shared-0' );
}
add_action( 'wp_enqueue_scripts', 'radios_de_reg' );

include_once RADIOS_INC_PATH . "/custom-widget/category-list.php";
include_once RADIOS_INC_PATH . "/custom-widget/social-icon.php";
include_once RADIOS_INC_PATH . "/custom-widget/recent-post.php";
// include_once RADIOS_INC_PATH . "/radios-cpt.php";
include_once RADIOS_INC_PATH . "/options/theme-metabox.php";
include_once RADIOS_INC_PATH . "/options/theme-option.php";
include_once RADIOS_INC_PATH . "/helper.php";
include_once RADIOS_DIR_PATH . "/elementor/elementor-init.php";

<?php
/*
Plugin Name: CM Tools
Description: Tools for CM
Version: 1.0
Text Domain: cm
*/
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Register CM elementor.
 *
 * Include widget file and register widget class.
 *
 * @since 1.0.0
 * @param \Elementor\Widgets_Manager $widgets_manager Elementor widgets manager.
 * @return void
 */

function add_cm_elementor_widget_categories($elements_manager)
{

    $elements_manager->add_category(
        'cm_widgets',
        [
            'title' => __('CM Addons', 'cm'),
            'icon' => 'fa fa-plug',
        ]
    );
}
add_action('elementor/elements/categories_registered', 'add_cm_elementor_widget_categories');

function register_cm_elementor($widgets_manager)
{

    require_once(__DIR__ . '/widgets/products.php');
    $widgets_manager->register(new \CM_Products());
}
add_action('elementor/widgets/register', 'register_cm_elementor');
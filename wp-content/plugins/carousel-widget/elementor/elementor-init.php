<?php
if (!defined('ABSPATH')) {
    exit(); // Exit if accessed directly.
}

class Custom_Elementor_Widget_Init {
    private static $instance;

    public function __construct() {
        add_action('elementor/elements/categories_registered', array($this, 'add_widget_categories'));
        add_action('elementor/widgets/widgets_registered', array($this, 'register_widgets'));
    }

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function add_widget_categories($elements_manager) {
        $elements_manager->add_category(
            'custom_widgets',
            [
                'title' => __('Custom Widgets', 'custom-carousel-widget'),
                'icon' => 'fa fa-plug',
            ]
        );
    }

    public function register_widgets() {
        require_once plugin_dir_path(__FILE__) . 'widgets/custom-carousel.php';
    }
}

Custom_Elementor_Widget_Init::get_instance();

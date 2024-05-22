<?php
if (!defined('ABSPATH')) {
    exit(); // Exit if accessed directly.
}

class Custom_Elementor_Widget_Init {
    private static $instance;

    public function __construct() {
        add_action('elementor/widgets/widgets_registered', array($this, 'register_widgets'));
        add_action( 'elementor/elements/categories_registered', array($this,'_widget_categories') );
        //elementor widget registered
        add_action('elementor/editor/after_enqueue_styles',array($this,'editor_style'));
    }

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

	/**
		 * _widget_categories()
		 * @since 1.0.0
		 * */
		public function _widget_categories($elements_manager){
			$elements_manager->add_category(
				'products_widgets',
				[
					'title' => __( 'Product Addons', 'products-core' ),

				]
			);
			
		}

    public function register_widgets() {
        if (did_action('elementor/loaded')) {
            $elementor = \Elementor\Plugin::instance();
            $widgets_manager = $elementor->widgets_manager;
            
            // Unregister the original widget if it exists
            $widgets_manager->unregister('OSF_Elementor_Products');
            
            // Include and register the custom widget
            require_once plugin_dir_path(__FILE__) . 'widgets/custom-products-widget.php';
            $widgets_manager->register(new \Custom_Elementor_Products());
        }
    }
}

Custom_Elementor_Widget_Init::get_instance();

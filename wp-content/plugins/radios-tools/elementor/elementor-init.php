<?php
/**
 * All Elementor widget init
 * @package radios
 * @since 1.0.0
 */

if ( !defined('ABSPATH') ){
	exit(); // exit if access directly
}


if ( !class_exists('Rradios_Elementor_Widget_Init') ){

	class Rradios_Elementor_Widget_Init{
		/*
			* $instance
			* @since 1.0.0
			* */
		private static $instance;
		/*
		* construct()
		* @since 1.0.0
		* */
		public function __construct() {
			add_action( 'elementor/elements/categories_registered', array($this,'_widget_categories') );
			//elementor widget registered
			add_action('elementor/widgets/widgets_registered',array($this,'_widget_registered'));
			add_action('elementor/editor/after_enqueue_styles',array($this,'editor_style'));
		}
		/*
	   * getInstance()
	   * @since 1.0.0
	   * */
		public static function getInstance(){
			if ( null == self::$instance ){
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
				'radios_widgets',
				[
					'title' => __( 'Radios Addons', 'radios-core' ),
					'icon' => 'fa fa-plug',
				]
			);
			
		}

		/**
		 * _widget_registered()
		 * @since 1.0.0
		 * */
		public function _widget_registered(){
			if( !class_exists('Elementor\Widget_Base') ){
				return;
			}
			$elementor_widgets = array(	
				
				// radios Theme Widgets 

				// Home One
				'hero-banner-1',
				'info-box',
				'product_tab',
				'product_tab_v4',
				'product_tab_v5',
				'product-with-cate',
				'shop-banner-two',
				'shop-banner-4',
				'shop-banner-5',
				'shop-banner1',
				'shop-banner-three',
				'category-list',
				'category-gallery',
				'brand-carousel',
				'product_best_seller_tab',
				'shop-6-banner',
				'product-list-with-cate',
				'category-gallery-two',
				'shop-banner-7',
				'product-list-carousel',
				'contact-image',
				'contact-info-box',
				'breadcrumb',
				'video-popup',
				'accordion',
				'list-item',
				'about-info',
				'product_tab_v6',
				'product_tab_v7',
				'product-grid-carousel',
				'newslater',
				'category-carousel',
				'slider-banner',
				'product-carousel',
				'recent-view-product',
				'brand-two',
				'product-list',
				'banner-8',
				'banner-9',
				'testimonial',
				'treanding-product',
				'banner-10',				
				'category-grid',				
				'banner-11',				
				'deal-banner',				
			);
			
			$elementor_widgets = apply_filters('radios_elementor_widget',$elementor_widgets);

			if ( is_array($elementor_widgets) && !empty($elementor_widgets) ) {
				foreach ( $elementor_widgets as $widget ){
					$widget_file = 'plugins/elementor/widget/'.$widget.'.php';
					$template_file = locate_template($widget_file);
					if ( !$template_file || !is_readable( $template_file ) ) {
						$template_file = RADIOS_DIR_PATH.'/elementor/widgets/'.$widget.'.php';
					}
					if ( $template_file && is_readable( $template_file ) ) {
						include_once $template_file;
					}
				}
			}

		}

		public function editor_style(){
			$cs_icon = plugins_url( 'icon.png', __FILE__ );
			wp_add_inline_style( 'elementor-editor', '.elementor-element .icon .mg-custom-icon{content: url( '.$cs_icon.');width: 28px;}' );
		}



	}

	if ( class_exists('Rradios_Elementor_Widget_Init') ){
		Rradios_Elementor_Widget_Init::getInstance();
	}

}//end if
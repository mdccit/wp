<?php

/**
 * Elementor Single Widget
 * @package radios Extension
 * @since 1.0.0
 */

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

class Product_Category_Carousel extends Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve Elementor widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'radios-product-cate-car';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve Elementor widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Category Carousel', 'radios-tools' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve Elementor widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'mg-custom-icon';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the Elementor widget belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'radios_widgets' ];
	}

	
	protected function register_controls() {

		$this->start_controls_section(
			'menu_item_info',
			[
				'label' => esc_html__( 'Category Carousel Option', 'radios-tools' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);
		
		$repeater = new Repeater();
		$repeater->add_control(
			'image', [
				'label' => esc_html__( 'Image', 'radios-tools' ),
				'type' => Controls_Manager::MEDIA,
				'label_block' => true,
			]
		);
		$repeater->add_control(
			'title', [
				'label' => esc_html__( 'Title', 'radios-tools' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => [
					'active' => true,
				],
				'placeholder' => __( 'Enter your Title Here', 'radios-tools' ),
			]
		);
		$repeater->add_control(
			'link', [
				'label' => esc_html__( 'Link', 'radios-tools' ),
				'type' => Controls_Manager::URL,
				'label_block' => true,
				'dynamic'     => [
					'active' => true,
				],
				'placeholder' => __( 'Enter your Title Here', 'radios-tools' ),
			]
		);
		$repeater->add_control(
			'bg_color',
			[
				'label' => esc_html__( 'Bg Color', 'radios-tools' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .feature-one_block-inner' => 'background-color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'categorys',
			[
				'label' => esc_html__( 'Categorys', 'radios-tools' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'title_field' => '{{{ title }}}',
			]
		);
		$this->end_controls_section();
	}


	protected function render() {
		$settings = $this->get_settings_for_display();		

    ?>
   <div class="feature-carousel owl-carousel owl-theme">

        <!-- Feature One Block -->
        <?php foreach($settings['categorys'] as $item):?>
        <div class="feature-one_block elementor-repeater-item-<?php echo esc_attr($item['_id']);?>">
            <div class="feature-one_block-inner">
                <a href="<?php echo esc_url($item['link']['url']);?>">
                    <div class="feature-one_block-image">
                        <img src="<?php echo esc_url($item['image']['url']);?>" alt="" />
                    </div>
                    <div class="feature-one_block-name"><?php echo wp_kses($item['title'], true);?></div>
                </a>
            </div>
        </div>
        <?php endforeach;?>
    </div>

    <?php 
    }
		
	
}


Plugin::instance()->widgets_manager->register_widget_type( new Product_Category_Carousel() );
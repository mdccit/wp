<?php

/**
 * Elementor Single Widget
 * @package radios Tools
 * @since 1.0.0
 */

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

class Radios_Brand_Carousel_Two extends Widget_Base {

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
		return 'da-brand-carousel-two';
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
		return esc_html__( 'Radios Brand Two', 'radios-tools' );
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
			'radios_slider_option',
			[
				'label' => esc_html__( 'Banner Option', 'radios-tools' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);
		
        $repeater = new Repeater();

		$repeater->add_control(
			'brand_logo',
			[
				'label' => esc_html__( 'Brand Logo', 'radios-tools' ),
				'type' => Controls_Manager::MEDIA,
			]
		);

		$repeater->add_control(
			'link', [
				'label' => esc_html__( 'link', 'radios-tools' ),
				'type' => Controls_Manager::URL,
				'show_label' => false,
			]
		);

		$this->add_control(
			'brand_carousel',
			[
				'label' => esc_html__( 'Add Brand Item', 'radios-tools' ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
			]
		);
		
		

		$this->end_controls_section();
	}


	protected function render() {
		$settings = $this->get_settings_for_display();		

    ?>
    <div class="clients-one">
        <div class="inner-container">
            <div class="sponsors-outer">
                <!-- Sponsors Carousel -->
                <ul class="sponsors-carousel owl-carousel owl-theme">
                    <?php foreach($settings['brand_carousel'] as $item):?>
                        <li class="slide-item"><figure class="image-box"><a href="<?php echo esc_url($item['link']['url']);?>"> <img src="<?php echo esc_url($item['brand_logo']['url']);?>" alt=""></a></figure></li>
                    <?php endforeach;?>
                </ul>
            </div>
        </div>
    </div>
    <?php 
    }
		
	
}


Plugin::instance()->widgets_manager->register_widget_type( new Radios_Brand_Carousel_Two() );
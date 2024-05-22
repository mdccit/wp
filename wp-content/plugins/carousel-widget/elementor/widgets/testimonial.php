<?php

/**
 * Elementor Single Widget
 * @package radios Tools
 * @since 1.0.0
 */

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

class Radios_Testimonial extends Widget_Base {

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
		return 'radios-testimonial-id';
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
		return esc_html__( 'Testimonial', 'radios-tools' );
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
			'radios_faq_option',
			[
				'label' => esc_html__( 'Testimonial Option', 'radios-tools' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);
        $repeater = new Repeater();
		
		$repeater->add_control(
			'authore', [
				'label' => esc_html__( 'Authore', 'radios-tools' ),
				'type' => Controls_Manager::MEDIA,
                'label_block' => true,
			]
		);
		$repeater->add_control(
			'title', [
				'label' => esc_html__( 'Title', 'radios-tools' ),
				'type' => Controls_Manager::TEXTAREA,
                'label_block' => true,
			]
		);
		$repeater->add_control(
			'desgination', [
				'label' => esc_html__( 'Desgination', 'radios-tools' ),
				'type' => Controls_Manager::TEXT,
                'label_block' => true,
			]
		);
		$repeater->add_control(
			'description', [
				'label' => esc_html__( 'Description', 'radios-tools' ),
				'type' => Controls_Manager::TEXTAREA,
                'label_block' => true,
			]
		);
		$repeater->add_control(
			'rating',
			[
				'label' => esc_html__( 'Rating', 'radios-tools' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 5,
				'step' => 1,
			]
		);
		$this->add_control(
			'testimonials',
			[
				'label' => esc_html__( 'Add Item', 'radios-tools' ),
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
    <div class="testimonial-one">
        <div class="testimonial-carousel owl-carousel owl-theme">

            <!-- Testimonial Block One -->
            <?php foreach($settings['testimonials'] as $item):?>
            <div class="testimonial-block_one">
                <div class="testimonial-block_one-inner">
                    <div class="testimonial-block_one-author">
                        <img src="<?php echo esc_url($item['authore']['url']);?>" alt="" />
                    </div>
                    <div class="testimonial-block_one-rating">
                        <?php for($i = 0; $i < $item['rating']; $i++):?>
                            <span class="fa fa-star"></span>
                        <?php endfor;?>
                    </div>
                    <h5 class="testimonial-block_one-heading"><a href="#"><?php echo esc_html($item['title']);?></a></h5>
                    <div class="testimonial-block_one-designation"><?php echo esc_html($item['desgination']);?></div>
                    <div class="testimonial-block_one-text"><?php echo wp_kses($item['description'], true);?></div>
                </div>
            </div>
            <?php endforeach;?>
            
        </div>
    </div>
    <?php 
    }
		
	
}


Plugin::instance()->widgets_manager->register_widget_type( new Radios_Testimonial() );
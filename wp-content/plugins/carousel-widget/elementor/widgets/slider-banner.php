<?php

/**
 * Elementor Single Widget
 * @package radios Tools
 * @since 1.0.0
 */

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

class radios_slider_banner extends Widget_Base {

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
		return 'radios-slide-banner';
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
		return esc_html__( 'Radios Slider Banner', 'radios-tools' );
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
			'radios_icon_option',
			[
				'label' => esc_html__( 'Icon Box Option', 'radios-tools' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'slide_bg',
			[
				'label' => esc_html__( 'Slide BG', 'radios-tools' ),
				'type' => Controls_Manager::MEDIA,
			]
		);
        $repeater = new Repeater();

		
		$repeater->add_control(
			'product_img',
			[
				'label' => esc_html__( 'Product Image', 'radios-tools' ),
				'type' => Controls_Manager::MEDIA,
			]
		);
		$repeater->add_control(
			'title', [
				'label' => esc_html__( 'Title', 'radios-tools' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'text', [
				'label' => esc_html__( 'Text', 'radios-tools' ),
				'type' => Controls_Manager::TEXTAREA,
			]
		);
		$repeater->add_control(
			'price', [
				'label' => esc_html__( 'Price', 'radios-tools' ),
				'type' => Controls_Manager::TEXT,
			]
		);
		$repeater->add_control(
			'btn_label', [
				'label' => esc_html__( 'Button Label', 'radios-tools' ),
				'type' => Controls_Manager::TEXT,
			]
		);
		$repeater->add_control(
			'btn_link', [
				'label' => esc_html__( 'Button Link', 'radios-tools' ),
				'type' => Controls_Manager::URL,
			]
		);

		$this->add_control(
			'slider',
			[
				'label' => esc_html__( 'Add Slide Item', 'radios-tools' ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'title_field' => '{{{ title }}}',
			]
		);
		
		

		$this->end_controls_section();
	}


	protected function render() {
		$settings = $this->get_settings_for_display();		

    ?>
    <section class="slider-one" style="background-image: url(<?php echo esc_url($settings['slide_bg']['url']);?>)">
        <div class="main-slider-carousel owl-carousel owl-theme">


            <!-- Slide -->
            <?php foreach($settings['slider'] as $item):?>
            <div class="slide">
                <div class="auto-container">
                    <div class="row clearfix">
                
                        <!-- Image Column -->
                        <?php if(!empty($item['product_img']['url'])):?>
                            <div class="slider-one_image-column col-lg-6 col-md-12 col-sm-12">
                                <div class="slider-one_image">
                                    <img src="<?php echo esc_url($item['product_img']['url']);?>" alt="" />
                                </div>
                            </div>
                        <?php endif;?>
                        <!-- Content Column -->
                        <div class="slider-one_content-column col-lg-6 col-md-12 col-sm-12">
                            <div class="slider-one_content">
                                <div class="slider-one_title"><?php echo wp_kses($item['title'], true);?></div>
                                <h1 class="slider-one_heading"><?php echo wp_kses($item['text'], true);?></h1>
                                <div class="slider-one_price"><?php echo wp_kses($item['price'], true);?></div>
                                <?php if(!empty($item['btn_label'])):?>
                                <div class="slider-one_btn-box">
                                    <a href="<?php echo esc_url($item['btn_link']['url']);?>" class="theme-btn slider-one_btn"><?php echo esc_html($item['btn_label']);?></a>
                                </div>
                                <?php endif;?>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <?php endforeach;?>

        </div>
    </section>
    <?php 
    }
		
	
}


Plugin::instance()->widgets_manager->register_widget_type( new radios_slider_banner() );
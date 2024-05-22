<?php

/**
 * Elementor Single Widget
 * @package radios Extension
 * @since 1.0.0
 */

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

class Shop_Banner_One extends Widget_Base {

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
		return 'radios-shop-banner-1';
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
		return esc_html__( 'Shop Banner', 'radios-tools' );
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
			'shop_banner_info',
			[
				'label' => esc_html__( 'Shop Banner Option', 'radios-tools' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);
		
		$this->add_control(
			'banner_bg', [
				'label'       => esc_html__( 'Banner BG Image', 'radios-tools' ),
				'type'        => Controls_Manager::MEDIA,
			]
		);
		
		$this->add_control(
			'subtitle', [
				'label' => esc_html__( 'Sub Title', 'radios-tools' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => [
					'active' => true,
				],
				'placeholder' => __( 'Enter your Sub Title Here', 'radios-tools' ),
			]
		);
		$this->add_control(
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
		$this->add_control(
			'description', [
				'label' => esc_html__( 'Text', 'radios-tools' ),
				'type' => Controls_Manager::TEXTAREA,
				'label_block' => true,
				'dynamic'     => [
					'active' => true,
				],
				'placeholder' => __( 'Enter your Description Here', 'radios-tools' ),
			]
		);
        $this->add_control(
			'discount_text', [
				'label' => esc_html__( 'Discount Text', 'radios-tools' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => [
					'active' => true,
				],
				'placeholder' => __( 'Enter your Discount Text Here', 'radios-tools' ),
			]
		);
        $this->add_control(
			'discount_percent', [
				'label' => esc_html__( 'Discount Percent', 'radios-tools' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => [
					'active' => true,
				],
				'placeholder' => __( 'Enter your Discount Percent Text Here', 'radios-tools' ),
			]
		);
        $this->add_control(
			'price', [
				'label' => esc_html__( 'Price', 'radios-tools' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => [
					'active' => true,
				],
				'placeholder' => __( 'Enter your Price Text Here', 'radios-tools' ),
			]
		);
        $this->add_control(
			'button_label', [
				'label' => esc_html__( 'Button Label', 'radios-tools' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => [
					'active' => true,
				],
				'placeholder' => __( 'Enter your Button Text Here', 'radios-tools' ),
			]
		);
        $this->add_control(
			'button_link', [
				'label' => esc_html__( 'Button Link', 'radios-tools' ),
				'type' => Controls_Manager::URL,
				'label_block' => true,
				'dynamic'     => [
					'active' => true,
				],
				'placeholder' => __( 'Enter your Button Link Here', 'radios-tools' ),
			]
		);
        $this->add_control(
			'banner_img', [
				'label' => esc_html__( 'Banner Image', 'radios-tools' ),
				'type' => Controls_Manager::MEDIA,
				'label_block' => true,
				'dynamic'     => [
					'active' => true,
				],
				'placeholder' => __( 'Enter your Banner Image Here', 'radios-tools' ),
			]
		);
		$this->end_controls_section();
	}


	protected function render() {
		$settings = $this->get_settings_for_display();		

    ?>

    <div class="rd-banner ul_li" data-background="<?php echo esc_url($settings['banner_bg']['url']);?>">
        <div class="rd-banner__content">
            <?php if(!empty($settings['subtitle'])):?>
                <span><?php echo wp_kses( $settings['subtitle'] , true )?></span>
            <?php endif;?>
            <h3><?php echo wp_kses( $settings['title'] , true )?></h3>
            <?php if(!empty($settings['description'])):?>
                <p><?php echo wp_kses( $settings['description'] , true )?></p>
            <?php endif;?>
            <?php if(!empty($settings['discount_text'])):?>
            <div class="ul_li mt-20">
                <div class="upto-offer ul_li">
                    <span class="upto"><?php echo wp_kses( $settings['discount_text'] , true )?></span>
                    <span class="offer-no"><?php echo wp_kses( $settings['discount_percent'] , true )?></span>
                </div>
                <?php if(!empty($settings['price'])):?>
                <h4 class="price"><?php echo esc_html($settings['price']);?></h4>
                <?php endif;?>
            </div>
            <?php endif;?>
            <?php if(!empty($settings['button_label'])):?>
            <div class="banner__btn mt-40">
                <a class="thm-btn thm-btn__red" href="<?php echo esc_url($settings['button_link']['url']);?>">
                    <span class="btn-wrap"> 
                        <span><?php echo esc_html($settings['button_label']);?></span>
                        <span><?php echo esc_html($settings['button_label']);?></span>
                    </span>
                    <i class="far fa-long-arrow-right"></i>
                </a>
            </div>
            <?php endif;?>
        </div>
        <?php if(!empty($settings['banner_img']['url'])):?>
        <div class="rd-banner__img">
            <img src="<?php echo esc_url($settings['banner_img']['url']);?>" alt="<?php echo esc_attr($settings['banner_img']['alt']);?>">
        </div>
        <?php endif;?>
    </div>
    <?php 
    }
		
	
}


Plugin::instance()->widgets_manager->register_widget_type( new Shop_Banner_One() );
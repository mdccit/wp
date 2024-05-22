<?php

/**
 * Elementor Single Widget
 * @package radios Extension
 * @since 1.0.0
 */

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

class Shop_Banner_Eleven extends Widget_Base {

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
		return 'radios-shop-banner-11';
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
		return esc_html__( 'Shop Banner Eleven', 'radios-tools' );
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
			'banner_img', [
				'label'       => esc_html__( 'Banner Image', 'radios-tools' ),
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
				'placeholder' => __( 'Enter your Sub Title Here', 'radios-tools' ),
			]
		);
		
		$this->add_control(
			'desc', [
				'label' => esc_html__( 'Desc', 'radios-tools' ),
				'type' => Controls_Manager::TEXTAREA,
				'label_block' => true,
				'dynamic'     => [
					'active' => true,
				],
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
        
		$this->end_controls_section();
	}


	protected function render() {
		$settings = $this->get_settings_for_display();		

    ?>
    <div class="business-block_one">
        <div class="business-block_one-inner">
            <div class="business-block_one-image">
                <img src="<?php echo esc_url($settings['banner_img']['url']);?>" alt="" />
                <div class="business-block_one-content">
                    <div class="business-block_one-title"><?php echo esc_html($settings['subtitle']);?></div>
                    <h2 class="business-block_one-heading"><?php echo esc_html($settings['title']);?></h2>
                    <div class="business-block_one-text"><?php echo esc_html($settings['desc']);?></div>
                    <?php if(!empty($settings['button_label'])):?>
                        <div class="business-block_one-btn">
                            <a class="buy-btn theme-btn" href="<?php echo esc_url($settings['button_link']['url']);?>"><?php echo esc_html($settings['button_label']);?></a>
                        </div>
                    <?php endif;?>
                </div>
            </div>
        </div>
    </div>
    <?php 
    }
		
	
}


Plugin::instance()->widgets_manager->register_widget_type( new Shop_Banner_Eleven() );
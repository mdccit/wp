<?php

/**
 * Elementor Single Widget
 * @package radios Extension
 * @since 1.0.0
 */

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

class Shop_Banner_Seven extends Widget_Base {

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
		return 'radios-shop-banner-7';
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
		return esc_html__( 'Shop Banner Seven', 'radios-tools' );
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
		$repeater = new Repeater();
		$repeater->add_control(
			'banner_bg', [
				'label'       => esc_html__( 'Banner BG', 'radios-tools' ),
				'type'        => Controls_Manager::MEDIA,
			]
		);
		$repeater->add_control(
			'banner_img', [
				'label'       => esc_html__( 'Banner Image', 'radios-tools' ),
				'type'        => Controls_Manager::MEDIA,
			]
		);
		
		$repeater->add_control(
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
			'price_label', [
				'label' => esc_html__( 'Price Label', 'radios-tools' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => [
					'active' => true,
				],
				'placeholder' => __( 'Enter your Price Text Here', 'radios-tools' ),
			]
		);
        $repeater->add_control(
			'price_percent', [
				'label' => esc_html__( 'Price', 'radios-tools' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => [
					'active' => true,
				],
				'placeholder' => __( 'Enter your Price Text Here', 'radios-tools' ),
			]
		);
        $repeater->add_control(
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
        $repeater->add_control(
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
			'products_banner',
			[
				'label' => esc_html__( 'Add Product Banner Item', 'radios-tools' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
			]
		);
		$this->end_controls_section();
	}


	protected function render() {
		$settings = $this->get_settings_for_display();		

    ?>
    <div class="row mt-none-10">
    <?php foreach($settings['products_banner'] as $item):?>
        <div class="col-xl-6 col-lg-12 mt-30">
            <div class="vd-banner__item vd-banner__item-two pos-rel ul_li_between bg_img" data-background="<?php echo esc_url($item['banner_bg']['url']);?>">
                <div class="content">
                    <?php if(!empty($settings['subtitle'])):?>
                        <span><?php echo esc_html($settings['subtitle']);?></span>
                    <?php endif;?>
                    <h2><?php echo wp_kses($item['title'], true)?></h2>
                    <h3><?php echo wp_kses($item['price_label'], true);?>  <span><?php echo wp_kses($item['price_percent'], true);?></span></h3>
                    <?php if(!empty($item['button_label'])):?>
                        <div class="banner__btn mt-10">
                            <a href="<?php echo esc_url($item['button_link']['url']);?>"><?php echo wp_kses($item['button_label'], true)?></a>
                        </div>
                    <?php endif;?>
                </div>
                <div class="image">
                    <img src="<?php echo esc_url($item['banner_img']['url']);?>" alt="">
                </div>
            </div>
        </div>
        <?php endforeach;?>
    </div>
    <!-- tx baner start -->

    <?php 
    }
		
	
}


Plugin::instance()->widgets_manager->register_widget_type( new Shop_Banner_Seven() );
<?php

/**
 * Elementor Single Widget
 * @package radios Extension
 * @since 1.0.0
 */

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

class Shop_Banner_Four extends Widget_Base {

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
		return 'radios-shop-banner-4';
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
		return esc_html__( 'Shop Banner Four', 'radios-tools' );
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
				'label'       => esc_html__( 'Banner BG', 'radios-tools' ),
				'type'        => Controls_Manager::MEDIA,
			]
		);
		$this->add_control(
			'banner_img', [
				'label'       => esc_html__( 'Banner Image', 'radios-tools' ),
				'type'        => Controls_Manager::MEDIA,
			]
		);
		$this->add_control(
			'banner_img2', [
				'label'       => esc_html__( 'Banner 2 Image', 'radios-tools' ),
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
			'desc', [
				'label' => esc_html__( 'Description', 'radios-tools' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => [
					'active' => true,
				],
				'placeholder' => __( 'Enter your Description Here', 'radios-tools' ),
			]
		);
		
        
        $this->add_control(
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
        $this->add_control(
			'price_percent', [
				'label' => esc_html__( 'Price Percent', 'radios-tools' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => [
					'active' => true,
				],
				'placeholder' => __( 'Enter your Price Text Here', 'radios-tools' ),
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
		$this->end_controls_section();
		$this->start_controls_section(
			'shop_banner_two',
			[
				'label' => esc_html__( 'Shop Banner Two Option', 'radios-tools' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);
		
		$this->add_control(
			'banner2_bg', [
				'label'       => esc_html__( 'Banner BG', 'radios-tools' ),
				'type'        => Controls_Manager::MEDIA,
			]
		);
		$this->add_control(
			'banner_st_img', [
				'label'       => esc_html__( 'Banner  Image', 'radios-tools' ),
				'type'        => Controls_Manager::MEDIA,
			]
		);
		
		$this->add_control(
			'subtitle2', [
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
			'title2', [
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
			'price_label2', [
				'label' => esc_html__( 'Price Label', 'radios-tools' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => [
					'active' => true,
				],
				'placeholder' => __( 'Enter your Price Text Here', 'radios-tools' ),
			]
		);
        $this->add_control(
			'price_percent2', [
				'label' => esc_html__( 'Price Percent', 'radios-tools' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => [
					'active' => true,
				],
				'placeholder' => __( 'Enter your Price Text Here', 'radios-tools' ),
			]
		);
        $this->add_control(
			'button_label2', [
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
			'button_link2', [
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
    <!-- featured start -->
    <div class="featured pt-10">
        <div class="container mxw_1530">
            <div class="row mt-none-30">
                <div class="col-lg-9 mt-30">
                    <div class="featured__item featured__big bg_img ul_li_between" data-background="<?php echo esc_url($settings['banner_bg']['url']);?>">
                        <div class="featured__content">
                            <?php if(!empty($settings['subtitle'])):?>
                                <span><?php echo wp_kses($settings['subtitle'], true)?></span>
                            <?php endif;?>
                            <?php if(!empty($settings['title'])):?>
                                <h2><?php echo wp_kses($settings['title'], true)?></h2>
                            <?php endif;?>
                            <?php if(!empty($settings['desc'])):?>
                                <p><?php echo wp_kses($settings['desc'], true)?></p>
                            <?php endif;?>
                            <div class="ul_li mt-20">
                                <?php if(!empty($settings['price_percent'])):?>
                                <div class="upto-offer ul_li">
                                    <span class="upto"><?php echo wp_kses($settings['price_label'], true)?></span>
                                    <span class="offer-no"><?php echo wp_kses($settings['price_percent'], true)?></span>
                                </div>
                                <?php endif;?>
                                <h4 class="price"><?php echo wp_kses($settings['price'], true)?></h4>
                            </div>
                            <?php if(!empty($settings['button_label'])):?>
                            <div class="banner__btn mt-30">
                                <a class="thm-btn thm-btn__black" href="<?php echo esc_url($settings['button_link']['url']);?>">
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
                        <div class="featured__img">
                            <img src="<?php echo esc_url($settings['banner_img']['url']);?>" alt="">
                        </div>
                        <?php endif;?>
                        <?php if(!empty($settings['banner_2_img']['url'])):?>
                        <div class="featured__shape">
                            <img src="<?php echo esc_url($settings['banner_2_img']['url']);?>" alt="">
                        </div>
                        <?php endif;?>
                    </div>
                </div>
                <div class="col-lg-3 mt-30">
                    <div class="add-banner bg_img add-banner__2 add-banner__h555" data-background="<?php echo esc_url($settings['banner2_bg']['url']);?>">
                        <span><?php echo wp_kses($settings['subtitle2'], true);?></span>
                        <h2><?php echo wp_kses($settings['title2'], true);?></h2>
                        <div class="upto-offer ul_li mb-35">
                            <span class="upto"><?php echo wp_kses($settings['price_label2'], true);?></span>
                            <span class="offer-no"><?php echo wp_kses($settings['price_percent2'], true);?></span>
                        </div>
                        <?php if(!empty($settings['button_label2'])):?>
                        <a class="thm-btn thm-btn__transparent" href="<?php echo esc_url($settings['button_link2']['url']);?>">
                            <span class="btn-wrap"> 
                                <span><?php echo esc_html($settings['button_label2']);?></span>
                                <span><?php echo esc_html($settings['button_label2']);?></span>
                            </span>
                            <i class="far fa-long-arrow-right"></i>
                        </a>
                        <?php endif;?>
                        <?php if(!empty($settings['banner_st_img']['url'])):?>
                            <div class="add-banner__img">
                                <img src="<?php echo esc_url($settings['banner_st_img']['url']);?>" alt="<?php echo esc_attr($settings['banner_st_img']['alt']);?>">
                            </div>
                        <?php endif;?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- featured end -->

    <?php 
    }
		
	
}


Plugin::instance()->widgets_manager->register_widget_type( new Shop_Banner_Four() );
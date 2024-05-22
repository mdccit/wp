<?php

/**
 * Elementor Single Widget
 * @package radios Extension
 * @since 1.0.0
 */

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

class Product_Category_Gallery_v2 extends Widget_Base {

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
		return 'radios-product-cate-gallery2';
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
		return esc_html__( 'Product Category Gallery 2', 'radios-tools' );
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
			'pro_cate_gsal',
			[
				'label' => esc_html__( 'Product Category Gallery Option', 'radios-tools' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'section_title', [
				'label' => esc_html__( 'Section Title', 'radios-tools' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => [
					'active' => true,
				],
				'placeholder' => __( 'Section Title Here', 'radios-tools' ),
			]
		);
		
		$repeater = new Repeater();
		$repeater->add_control(
			'gallery',
			[
				'label' => esc_html__( 'Add Gallery', 'radios-tools' ),
				'type' => \Elementor\Controls_Manager::GALLERY,
				'default' => [],
			]
		);
		
		$repeater->add_control(
			'category_title', [
				'label' => esc_html__( 'Category Title', 'radios-tools' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => [
					'active' => true,
				],
				'placeholder' => __( 'Category Title Here', 'radios-tools' ),
			]
		);
		$repeater->add_control(
			'count', [
				'label' => esc_html__( 'Count', 'radios-tools' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => [
					'active' => true,
				],
			]
		);
        $repeater->add_control(
			'choose_category',
			[
				'label' => esc_html__( 'Choose Category', 'radios-tools' ),
				'type' => Controls_Manager::SELECT2,
				'multiple' => true,
				'options' => radios_product_category()
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
		
		$this->add_control(
			'product_categorys',
			[
				'label' => esc_html__( 'Add New Product Gallery Items', 'radios-tools' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
			]
		);
		$this->end_controls_section();
        $this->start_controls_section(
			'banner_shop',
			[
				'label' => esc_html__( 'Banner Shop Option', 'radios-tools' ),
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
        
	}


	protected function render() {
		$settings = $this->get_settings_for_display();		

    ?>
   <!-- product catagories start -->
   <div class="rd-product-category pt-50">
        <div class="container mxw_1530">
            <?php if(!empty($settings['section_title'])):?>
                <h2 class="section-heading mb-25"><span><?php echo wp_kses($settings['section_title'], true)?></span></h2>
            <?php endif;?>
            <div class="row mt-none-30">
                <div class="col-lg-9 mt-30">
                    <div class="rd-product-category__wrap">
                        <div class="row mt-none-50">
                            <?php foreach($settings['product_categorys'] as $item):?>
                            <div class="col-lg-4 col-md-4">
                                <div class="product-cat__item product-cat__item-two pos-rel mt-40"> 
                                    <div class="product-cat__images mt-20">
                                        <div class="tab-content" id="fc-myTabContent">
                                            <?php $i = 0; foreach($item['gallery'] as $gall): $i++?>
                                            <div class="tab-pane fade <?php if($i == 1){echo esc_attr('show active');}?>" id="fc-home<?php echo esc_attr($item['_id']); echo esc_attr($i);?>" role="tabpanel" aria-labelledby="fc-home-tab<?php echo esc_attr($item['_id']); echo esc_attr($i);?>">
                                                <div class="product-cat__img">
                                                    <img src="<?php echo esc_url($gall['url']);?>" alt="">                   
                                                </div>
                                            </div>
                                            <?php endforeach;?>
                                        </div>
                                        <ul class="nav product-cat__nav product-cat__nav-two nav-tabs" id="fc-myTab" role="tablist">
                                            <?php $i = 0; foreach($item['gallery'] as $gall): $i++?>
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link <?php if($i == 1){echo esc_attr('active');}?>" id="fc-home-tab<?php echo esc_attr($item['_id']); echo esc_attr($i);?>" data-bs-toggle="tab" data-bs-target="#fc-home<?php echo esc_attr($item['_id']); echo esc_attr($i);?>" type="button" role="tab" aria-controls="fc-home<?php echo esc_attr($item['_id']); echo esc_attr($i);?>" aria-selected="true">
                                                        <img src="<?php echo esc_url($gall['url']);?>" alt=""> 
                                                    </button>
                                                </li>
                                            <?php endforeach;?>
                                        </ul>
                                    </div>
                                    <div class="product-cat__content mt-20">
                                        <?php if($item['category_title']):?>
                                            <h3 class="title"><?php echo wp_kses($item['category_title'], true)?></h3>
                                        <?php endif;?>
                                        <ul class="list-unstyled">
                                            <?php foreach($item['choose_category'] as $cate):?>
                                                <li><a href="<?php echo esc_url( home_url( 'product-category' ) ) ?>/<?php echo esc_attr($cate);?>"><?php echo  esc_html(ucfirst(str_replace('-', ' ', $cate)));?></a></li>
                                            <?php endforeach;?>
                                        </ul>
                                    </div>
                                    <?php if(!empty($item['count'])):?>
                                        <span class="product-cat__number"><?php echo esc_html($item['count']);?></span>
                                    <?php endif;?>
                                </div>
                            </div>
                            <?php endforeach;?>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 mt-30">
                    <div class="add-banner__wrap ml-70">
                        <div class="add-banner add-banner__4 add-banner__h530 bg_img text-center" data-background="<?php echo esc_url($settings['banner_bg']['url']);?>">
                            <span><?php echo esc_html( $settings['subtitle'] );?></span>
                            <h3 class="text-capitalize"><?php echo esc_html( $settings['title'] );?></h3>
                            <span class="price"><?php echo esc_html($settings['price']);?></span>
                            <a class="thm-btn mt-40" href="<?php echo esc_url($settings['button_link']['url']);?>">
                                <span class="btn-wrap"> 
                                    <span><?php echo esc_html($settings['button_label']);?></span>
                                    <span><?php echo esc_html($settings['button_label']);?></span>
                                </span>
                                <i class="far fa-long-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            </div>
            </div>
            <!-- product catagories end -->            
    <?php 
    }
		
	
}


Plugin::instance()->widgets_manager->register_widget_type( new Product_Category_Gallery_v2() );
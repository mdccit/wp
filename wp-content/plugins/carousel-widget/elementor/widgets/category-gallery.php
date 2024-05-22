<?php

/**
 * Elementor Single Widget
 * @package radios Extension
 * @since 1.0.0
 */

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

class Product_Category_Gallery extends Widget_Base {

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
		return 'radios-product-cate-gallery';
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
		return esc_html__( 'Product Category Gallery', 'radios-tools' );
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
	}


	protected function render() {
		$settings = $this->get_settings_for_display();		

    ?>
     <div class="product-cat pt-60">
        <div class="container">
            <?php if(!empty($settings['section_title'])):?>
                <h2 class="section-heading mb-25"><span><?php echo wp_kses($settings['section_title'], true)?></span></h2>
            <?php endif;?>
            <div class="product-cat__wrap">
                <div class="row mt-none-50">
                <?php foreach($settings['product_categorys'] as $item):?>
                    <div class="col-lg-4">
                        
                        <div class="product-cat__item mt-50">
                            <div class="product-cat__images">
                                <div class="tab-content" id="fc-myTabContent2">
                                   <?php $i = 0; foreach($item['gallery'] as $gall): $i++?>
                                    <div class="tab-pane fade <?php if($i == 1){echo esc_attr('show active');}?>" id="fc-home2<?php echo esc_attr($item['_id']); echo esc_attr($i);?>" role="tabpanel" aria-labelledby="fc-home-tab2<?php echo esc_attr($item['_id']); echo esc_attr($i);?>">
                                        <div class="product-cat__img">
                                            <img src="<?php echo esc_url($gall['url']);?>" alt="">           
                                        </div>
                                    </div>
                                    <?php endforeach;?>
                                </div>
                                <ul class="nav product-cat__nav nav-tabs" id="fc-myTab2" role="tablist">
                                   <?php $i = 0; foreach($item['gallery'] as $gall): $i++?>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link <?php if($i == 1){echo esc_attr('active');}?>" id="fc-home-tab2" data-bs-toggle="tab" data-bs-target="#fc-home2<?php echo esc_attr($item['_id']); echo esc_attr($i);?>" type="button" role="tab" aria-controls="fc-home2<?php echo esc_attr($item['_id']); echo esc_attr($i);?>" aria-selected="true">
                                            <img src="<?php echo esc_url($gall['url']);?>" alt=""> 
                                        </button>
                                    </li>
                                    <?php endforeach;?>
                                </ul>
                            </div>
                            
                            <div class="product-cat__content">
                                <h3 class="title"><?php echo wp_kses( $item['category_title'], true );?></h3>
                                <?php if(!empty($item['choose_category'])):?>
                                <ul class="list-unstyled">
                                    <?php foreach($item['choose_category'] as $cate):?>
                                        <li><a href="<?php echo esc_url( home_url( 'product-category' ) ) ?>/<?php echo esc_attr($cate);?>">
                                                <?php echo  esc_html(ucfirst(str_replace('-', ' ', $cate)));?>
                                            </a></li>
                                    <?php endforeach;?>
                                </ul>
                                <?php endif;?>
                            </div>
                        </div>

                    </div>
                    <?php endforeach;?>
                </div>
            </div>
        </div>
    </div>
    
    <?php 
    }
		
	
}


Plugin::instance()->widgets_manager->register_widget_type( new Product_Category_Gallery() );
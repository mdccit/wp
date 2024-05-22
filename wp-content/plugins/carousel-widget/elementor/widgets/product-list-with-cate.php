<?php

/**
 * Elementor Single Widget
 * @package radios Extension
 * @since 1.0.0
 */

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

class Product_List_With_Cate extends Widget_Base {

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
		return 'vi-product-list-cate-id';
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
		return esc_html__( 'Product List With Category', 'radios-tools' );
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
			'product_options',
			[
				'label' => esc_html__( 'Product Option', 'radios-tools' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);
		
		$repeater = new Repeater();
		$repeater->add_control(
			'prod_sec_title', [
				'label' => esc_html__( 'Section Title', 'radios-tools' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => [
					'active' => true,
				],
				'placeholder' => __( 'Enter your Section Title Here', 'radios-tools' ),
			]
		);
		$repeater->add_control(
			'postorder',
			[
				'label'     => esc_html__( 'Post Order', 'radios-tools' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'ASC',
				'options'   => [
					'ASC'  => esc_html__( 'Ascending', 'radios-tools' ),
					'DESC' => esc_html__( 'Descending', 'radios-tools' ),
				],
			]
		);
        $repeater->add_control(
			'product_per_page',
			[
				'label'   => __( 'Product Per Page', 'magezix-core' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 1,
				'default' => 4,
			]
		);
        $repeater->add_control(
			'product_categories',
			[
				'type'        => Controls_Manager::SELECT2,
				'label'       => esc_html__( 'Select Product Categories', 'magezix-core' ),
				'options'     => radios_product_category(),
				'label_block' => true,
				'multiple'    => true,
			]
		);
        $repeater->add_control(
			'product_tags',
			[
				'type'        => Controls_Manager::SELECT2,
				'label'       => esc_html__( 'Product Tags', 'magezix-core' ),
				'multiple'    => true,
                  'options'     => array_flip(radios_item_tag_lists( 'tags', array(
                    'sort_order'  => 'ASC',
                    'taxonomies'    => 'product_tag',
                    'hide_empty'  => false,
                ) )),
			]
		);
		$this->add_control(
			'products_slider',
			[
				'label' => esc_html__( 'Add Product Sliders Item', 'radios-tools' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
			]
		);
		$this->end_controls_section();
        
        $this->start_controls_section(
			'product_categorys',
			[
				'label' => esc_html__( 'Product Category Option', 'radios-tools' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);
        $this->add_control(
			'category_bg', [
				'label' => esc_html__( 'Category BG', 'radios-tools' ),
				'type' => Controls_Manager::MEDIA,
			]
		);
        $this->add_control(
			'category_title', [
				'label' => esc_html__( 'Category Title', 'radios-tools' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => [
					'active' => true,
				],
				'placeholder' => __( 'Enter your Category Title Here', 'radios-tools' ),
			]
		);
		$repeater = new Repeater();

		$repeater->add_control(
			'category_name', [
				'label' => esc_html__( 'Category Name', 'radios-tools' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => [
					'active' => true,
				],
				'placeholder' => __( 'Enter your Category Name Here', 'radios-tools' ),
			]
		);
		$repeater->add_control(
			'category_link', [
				'label' => esc_html__( 'Category Link', 'radios-tools' ),
				'type' => Controls_Manager::URL,
				'label_block' => true,
				'dynamic'     => [
					'active' => true,
				],
				'placeholder' => __( 'Enter your Category Link Here', 'radios-tools' ),
			]
		);
		
		$this->add_control(
			'categoryes',
			[
				'label' => esc_html__( 'Add Category Item', 'radios-tools' ),
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
   <div class="pt-55">
    <div class="container mxw_1530">
        <div class="row mt-none-30">
            <div class="col-lg-10">
                <div class="rd-category__left">
                    <div class="row">
						<?php if($settings['categoryes']):?>
							<div class="col-lg-3 col-md-6 tab-product-col mt-30">
								<div class="product-category product-category__2 bg_img" data-background="<?php echo esc_url($settings['category_bg']['url']);?>">
									<?php if(!empty($settings['category_title'])):?>
										<h2 class="product-category__title"><?php echo wp_kses($settings['category_title'], true)?></h2>
									<?php endif;?>
									<ul class="list-unstyled">
										<?php foreach($settings['categoryes'] as $cate):?>
											<li class="cat-item-has-children"><a href="<?php echo esc_url($cate['category_link']['url']);?>"><?php echo esc_html($cate['category_name']);?></a></li>
										<?php endforeach;?>
									</ul>
								</div>
							</div>
						<?php endif;?>
                        <?php foreach($settings['products_slider'] as $prod):?>
                        <div class="col-lg-3 col-md-6 tab-product-col mt-30">
							<?php if(!empty($prod['prod_sec_title'])):?>
                            	<h2 class="section-heading mb-20 fs-18"><span><?php echo wp_kses($prod['prod_sec_title'], true)?></span></h2>
							<?php endif;?>
							<?php 
								$args = array(
									'post_type'      => 'product',
									'posts_per_page' => !empty( $prod['product_per_page'] ) ? $prod['product_per_page'] : 1,
									'ignore_sticky_posts' => true,
									'post_status'         => 'publish',
									'suppress_filters'    => false,
									'order'          => $prod['postorder'],
								);
								if( ! empty($prod['product_categories'] ) ){
									$args['product_cat'] = implode(',', $prod['product_categories']);
								}
								
								if(!empty($prod['product_tags'][0])) {
									$args['tax_query'] = array(
										array(
										'taxonomy' => 'product_tag',
										'field'    => 'ids',
										'terms'    => $prod['product_tags']
										)
									);
								}
								$query = new \WP_Query( $args );
								if ( $query->have_posts() ):                                
							?>
							<?php while ( $query->have_posts() ) {
                            $query->the_post();
                            $idd = get_the_ID();
                            $cat = '';
                            $product = wc_get_product( $idd );
                            $stock = get_post_meta( $idd, '_stock', true );
                            ?>
                            <div class="tx-widget__product-item style-2 tx-product ul_li">
								<div class="thumb">
									<a href="<?php echo esc_url(get_permalink( $idd )); ?>"><?php echo get_the_post_thumbnail($idd, 'large');?></a>
								</div>
								<div class="content">
									<h3><a href="<?php echo esc_url(get_permalink( $idd )); ?>"><?php echo esc_html(get_the_title($idd)); ?></a></h3>
									<h4 class="product__price"> <?php echo wp_kses_post($product->get_price_html()); ?> </h4>
								</div>
							</div>
							<?php 							
                                } wp_reset_query();
                            ?>
							<?php endif; ?>
                        </div>
                        <?php endforeach;?>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 mt-30">
                <div class="add-banner__wrap ml-none-70">
                    <div class="add-banner add-banner__4 add-banner__h476 br-5 bg_img text-center" data-background="<?php echo esc_url($settings['banner_bg']['url']);?>">
						<?php if(!empty($settings['subtitle'])):?>
                        	<span><?php echo wp_kses($settings['subtitle'], true)?></span>
						<?php endif;?>
						<?php if(!empty($settings['title'])):?>
                        	<h3 class="text-capitalize"><?php echo wp_kses($settings['title'], true)?></h3>
						<?php endif;?>
						<?php if(!empty($settings['price'])):?>
                        	<span class="price"><?php echo wp_kses($settings['price'], true)?></span>
						<?php endif;?>
						<?php if(!empty($settings['button_label'])):?>
                        <a class="thm-btn mt-40" href="<?php echo esc_url($settings['button_link']['url']);?>">
                            <span class="btn-wrap"> 
                                <span><?php echo esc_html($settings['button_label']);?></span>
                                <span><?php echo esc_html($settings['button_label']);?></span>
                            </span>
                            <i class="far fa-long-arrow-right"></i>
                        </a>
						<?php endif;?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    <?php 
    }
		
	
}


Plugin::instance()->widgets_manager->register_widget_type( new Product_List_With_Cate() );
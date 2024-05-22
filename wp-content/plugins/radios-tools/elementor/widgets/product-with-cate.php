<?php

/**
 * Elementor Single Widget
 * @package radios Extension
 * @since 1.0.0
 */

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

class Product_With_Cate extends Widget_Base {

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
		return 'vi-product-with-cate-id';
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
		return esc_html__( 'Featured Product With Category', 'radios-tools' );
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
			'about_txt_content',
			[
				'label' => esc_html__( 'About Tab Option', 'radios-tools' ),
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
				'placeholder' => __( 'Enter your Section Title Here', 'radios-tools' ),
			]
		);
		$repeater = new Repeater();
		
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
			'category_img', [
				'label' => esc_html__( 'Category Image', 'radios-tools' ),
				'type' => Controls_Manager::MEDIA,
				'label_block' => true,
			]
		);
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
	}


	protected function render() {
		$settings = $this->get_settings_for_display();		

    ?>
   <!-- rd slide product start -->
   <div class="rd-slide-product">
        <div class="container">
            <div class="row mt-none-30">
                <div class="col-lg-3 mt-30">
                    <div class="product-category" data-background="<?php echo esc_url($settings['category_bg']['url']);?>">
                        <?php if(!empty($settings['category_title'])):?>
                            <h2 class="section-heading mb-25"><span><span><?php echo wp_kses($settings['category_title'], true);?></span></span></h2>
                        <?php endif;?>
                        <ul class="list-unstyled">
                            <?php foreach($settings['categoryes'] as $cate):?>
                            <li class="cat-item-has-children"><a href="<?php echo esc_url($cate['category_link']['url']);?>"><img src="<?php echo esc_url($cate['category_img']['url']);?>" alt=""><?php echo wp_kses($cate['category_name'], true);?></a></li>
                            <?php endforeach;?>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-9 mt-30">
                    <div class="rd-slide-products">
                        <?php if(!empty($settings['section_title'])):?>
                            <h2 class="section-heading mb-25"><span><?php echo wp_kses($settings['section_title'], true)?></span></h2>
                        <?php endif;?>
                        <div class="rd-product__slide tx-arrow">
                        <?php 
                            foreach($settings['products_slider'] as $item):
                                $tax_query[] = array(
                                    'taxonomy' => 'product_visibility',
                                    'field'    => 'name',
                                    'terms'    => 'featured',
                                    'operator' => 'IN',
                                );
                                $args = array(
                                    'post_type'      => 'product',
                                    'posts_per_page' => !empty( $item['product_per_page'] ) ? $item['product_per_page'] : 1,
                                    'ignore_sticky_posts' => true,
                                    'post_status'         => 'publish',
                                    'suppress_filters'    => false,
                                    'order'          => $item['postorder'],
                                    'tax_query'           => $tax_query
                                );
                                if( ! empty($item['product_categories'] ) ){
                                    $args['product_cat'] = implode(',', $item['product_categories']);
                                }
                                
                                if(!empty($item['product_tags'][0])) {
                                    $args['tax_query'] = array(
                                        array(
                                        'taxonomy' => 'product_tag',
                                        'field'    => 'ids',
                                        'terms'    => $item['product_tags']
                                        )
                                    );
                                }
                                $query = new \WP_Query( $args );
                                if ( $query->have_posts() ):                                
                            ?>	
                            <div class="rd-product__slide-item">
                            <?php while ( $query->have_posts() ) {
                                $query->the_post();
                                $idd = get_the_ID();
                                $cat = '';
                                $product = wc_get_product( $idd );
                                $product = wc_get_product($idd);
                                
                                ?>
                                <div class="product__item">
                                    <div class="product__img text-center pos-rel">
                                        <a href="<?php echo esc_url(get_permalink( $idd )); ?>"><?php echo get_the_post_thumbnail($idd, 'radios-image-size1');?></a>
                                    </div>
                                    <div class="product__content">
                                        <div class="product__review ul_li_center">
                                            <?php 
                                                $rating  = $product->get_average_rating();
                                                $count   = $product->get_rating_count();
                                                echo wc_get_rating_html( $rating, $count );
                                            ?>
                                            <?php if($count):?>
                                            <span>(<?php echo esc_html($count);?>) Review</span>
                                            <?php endif;?>
                                        </div>
                                        <h2 class="product__title"><a href="<?php echo esc_url(get_permalink( $idd )); ?>"><?php echo esc_html(get_the_title($idd)); ?></a></h2>
                                        <h4 class="product__price"><?php echo wp_kses_post($product->get_price_html()); ?></h4>
                                    </div>
                                    <ul class="product__action">
										<li><?php echo do_shortcode( '[yith_compare_button product=' . get_the_ID() . ']' ); ?></li>
										<li><?php radios_add_to_cart_icon(true, false);?></li>
										<?php if(function_exists('YITH_WCWL')):?>
										<li><?php echo do_shortcode( '[yith_wcwl_add_to_wishlist]' );?></li>
										<?php endif;?>
                                    </ul>
                                    <?php 		
                                        /**
                                         * Hook: woocommerce_before_shop_loop_item_title.
                                         *
                                         * @hooked woocommerce_show_product_loop_sale_flash - 10
                                         * @hooked woocommerce_template_loop_product_thumbnail - 10
                                         */
                                        woocommerce_show_product_loop_sale_flash();
                                        do_action( 'woocommerce_before_shop_loop_item_title' );
                                    ?>
                                </div>
                                <?php 							
                                    } wp_reset_query();
                                ?>
                            </div>
                            <?php endif; endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- rd slide product end -->
    <?php 
    }
		
	
}


Plugin::instance()->widgets_manager->register_widget_type( new Product_With_Cate() );
<?php

/**
 * Elementor Single Widget
 * @package radios Extension
 * @since 1.0.0
 */

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

class Product_Grid_Carousel extends Widget_Base {

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
		return 'vi-product-grid-carousel-id';
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
		return esc_html__( 'Product Grid Carousel', 'radios-tools' );
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
			'products_info',
			[
				'label' => esc_html__( 'Product Grid Option', 'radios-tools' ),
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
		
		
		$this->add_control(
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
        $this->add_control(
			'product_per_page',
			[
				'label'   => __( 'Product Per Page', 'magezix-core' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 1,
				'default' => 4,
			]
		);
        $this->add_control(
			'product_categories',
			[
				'type'        => Controls_Manager::SELECT2,
				'label'       => esc_html__( 'Select Product Categories', 'magezix-core' ),
				'options'     => radios_product_category(),
				'label_block' => true,
				'multiple'    => true,
			]
		);
        $this->add_control(
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
			'title_length',
			[
				'label'     => __( 'Title Length', 'magezix-core' ),
				'type'      => Controls_Manager::NUMBER,
				'step'      => 1,
				'default'   => 20,
			]
		);
		$this->end_controls_section();
	}


	protected function render() {
		$settings = $this->get_settings_for_display();		

    ?>
    <div class="rd-product__bg pt-30">
        <div class="container mxw_1530">
            <div class="rd-product__slide-wrap">
                <?php if(!empty($settings['section_title'])):?>
                <h2 class="section-heading mb-30 ul_li_between"><span>
                       <?php echo wp_kses($settings['section_title'], true);?>
                </span> </h2>
                <?php endif;?>
                <div class="rd-product__slide-two">
                    <?php 

                        $args = array(
                            'post_type'      => 'product',
                            'posts_per_page' => !empty( $settings['product_per_page'] ) ? $settings['product_per_page'] : 1,
                            'ignore_sticky_posts' => true,
                            'post_status'         => 'publish',
                            'suppress_filters'    => false,
                            'order'          => $settings['postorder'],
                        );
                        

                        if( ! empty($settings['product_categories'] ) ){
                            $args['product_cat'] = implode(',', $settings['product_categories']);
                        }
                        
                        if(!empty($settings['product_tags'][0])) {
                            $args['tax_query'] = array(
                                array(
                                'taxonomy' => 'product_tag',
                                'field'    => 'ids',
                                'terms'    => $settings['product_tags']
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
                    <div class="tab-product__item tx-product text-center">
                        <div class="thumb">
                            <a href="<?php echo esc_url(get_permalink( $idd )); ?>"><?php echo get_the_post_thumbnail($idd, 'large');?></a>
                            <ul class="product__action style-2 ul_li">
                                <li><?php echo do_shortcode( '[yith_compare_button product=' . get_the_ID() . ']' ); ?></li>
                                    <li><?php radios_add_to_cart_icon(true, false);?></li>
                                    <?php if(function_exists('YITH_WCWL')):?>
                                    <li><?php echo do_shortcode( '[yith_wcwl_add_to_wishlist]' );?></li>
                                    <?php endif;?>
                            </ul>
                        </div>
                        <div class="content">
                            <div class="product__review ul_li_center">
                                <?php 
                                    $rating  = $product->get_average_rating();
                                    $count   = $product->get_rating_count();
                                    echo wc_get_rating_html( $rating, $count );
                                ?>
                                <?php if($count):?>
                                <span>(<?php echo esc_html($count);?>)</span>
                                <?php endif;?>
                            </div>
                            <h3 class="title"><a href="<?php echo esc_url(get_permalink( $idd )); ?>"><?php echo esc_html(get_the_title($idd)); ?></a></h3>
                            <span class="price">( <?php echo wp_kses_post($product->get_price_html()); ?> )</span>
                        </div>
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
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php 
    }
		
	
}


Plugin::instance()->widgets_manager->register_widget_type( new Product_Grid_Carousel() );
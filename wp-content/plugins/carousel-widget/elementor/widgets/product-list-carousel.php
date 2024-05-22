<?php

/**
 * Elementor Single Widget
 * @package radios Extension
 * @since 1.0.0
 */

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

class Product_List_Carousel extends Widget_Base {

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
		return 'vi-product-list-carousel-id';
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
		return esc_html__( 'Product List Carousel', 'radios-tools' );
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
				'label' => esc_html__( 'Product Option', 'radios-tools' ),
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
			'product_type',
			[
				'label' => esc_html__( 'Product Type Style', 'radios-tools' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'total_sales',
				'options' => [
					'total_sales'  => esc_html__( 'Top Selling', 'radios-tools' ),
					'_wc_average_rating' => esc_html__( 'Top Rated ', 'radios-tools' ),
				],
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
			'products_slides',
			[
				'label' => esc_html__( 'Add Product Item', 'radios-tools' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
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
    <div class="tx-widget__wrap">
        <div class="tx-widget widget__product">
            <h2 class="section-heading mb-20 fs-18"><span>Leatest Item</span></h2>
            <div class="tx-widget__product-slide tx-arrow">
            <?php 
                foreach($settings['products_slides'] as $item):
                    $args = array(
                        'post_type'      => 'product',
                        'posts_per_page' => !empty( $item['product_per_page'] ) ? $item['product_per_page'] : 1,
                        'ignore_sticky_posts' => true,
                        'post_status'         => 'publish',
                        'suppress_filters'    => false,
                        'order'          => $item['postorder'],
                        'meta_key'       => $item['product_type'],
                        'orderby'        => 'meta_value_num',
                        'meta_query'     => WC()->query->get_meta_query(),
                        'tax_query'      => WC()->query->get_tax_query(),
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
                <div class="tx-widget__product-single">
                <?php while ( $query->have_posts() ) {
                    $query->the_post();
                    $idd = get_the_ID();
                    $cat = '';
                    $product = wc_get_product( $idd );
                    $stock = get_post_meta( $idd, '_stock', true );
					$title = wp_trim_words( get_the_title(), $settings['title_length'], '' );
                ?>
                    <div class="tx-widget__product-item tx-product ul_li">
                        <div class="thumb">
                            <a href="<?php echo esc_url(get_permalink( $idd )); ?>"><?php echo get_the_post_thumbnail($idd, 'large');?></a>
                        </div>
                        <div class="content">
                            <h3><a href="<?php echo esc_url(get_permalink( $idd )); ?>"><?php echo esc_html($title);?></a></h3>
                            <h4 class="product__price"><?php echo wp_kses_post($product->get_price_html()); ?> </h4>
                        </div>
                    </div>
                    <?php 							
                        } wp_reset_query();
                    ?>
                </div>
                <?php endif; endforeach; ?>
            </div>
        </div>
    </div>
    <?php 
    }
		
	
}


Plugin::instance()->widgets_manager->register_widget_type( new Product_List_Carousel() );
<?php

/**
 * Elementor Single Widget
 * @package radios Extension
 * @since 1.0.0
 */

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

class Trending_Product extends Widget_Base {

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
		return 'vi-trending-product';
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
		return esc_html__( 'Trending Product', 'radios-tools' );
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
				'label' => esc_html__( 'Trending Option', 'radios-tools' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);
        $this->add_responsive_control(
			'col_xl',
			[
				'label' => esc_html__( 'Colum Xl', 'textdomain' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 12,
				'step' => 15,
				'default' => 3,
			]
		);
        $this->add_responsive_control(
			'col_lg',
			[
				'label' => esc_html__( 'Colum Lg', 'textdomain' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 12,
				'step' => 15,
				'default' => 6,
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
    <div class="row clearfix">
    <?php 

        $tax_query[] = array(
            'taxonomy' => 'product_visibility',
            'field'    => 'name',
            'terms'    => 'featured',
            'operator' => 'IN', // or 'NOT IN' to exclude feature products
        );

        $args = array(
            'post_type'      => 'product',
            'posts_per_page' => !empty( $settings['product_per_page'] ) ? $settings['product_per_page'] : 1,
            'ignore_sticky_posts' => true,
            'post_status'         => 'publish',
            'suppress_filters'    => false,
            'order'          => $settings['postorder'],
            'tax_query'           => $tax_query
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

        if(get_post_meta(get_the_ID(), 'groser_product_meta', true)) {
            $gproduct_meta = get_post_meta(get_the_ID(), 'groser_product_meta', true);
        } else {
            $gproduct_meta = array();
        }
        if( array_key_exists( 'disc_percent_off', $gproduct_meta )) {
            $disc_percent_off = $gproduct_meta['disc_percent_off'];
        } else {
            $disc_percent_off = true;
        }                    
        if( array_key_exists( 'offer_badge_label', $gproduct_meta )) {
            $offer_badge_label = $gproduct_meta['offer_badge_label'];
        } else {
            $offer_badge_label = '';
        } 
        if( array_key_exists( 'product-gallery', $gproduct_meta )) {
            $prduct_gallery = $gproduct_meta['product-gallery'];
        } else {
            $prduct_gallery = '';
        } 
        ?>
        <!-- Product Block Two -->
        <div class="product-block_two col-xl-<?php echo esc_attr($settings['col_xl']);?> col-lg-<?php echo esc_attr($settings['col_lg']);?> col-md-6 col-sm-6">
            <div class="product-block_two-inner">

                <!-- Product Info Tabs -->
                <div class="product-info-tabs">
                    <!-- Product Tabs -->
                    <div class="prod-tabs tabs-box">
                        <div class="image">
                            <?php echo get_the_post_thumbnail($idd, 'large');?>
                        </div>
                    </div>
                </div>
                <h5 class="product-block_two-heading"><a href="<?php echo esc_url(get_permalink( $idd )); ?>"><?php echo wp_trim_words(get_the_title(), $settings['title_length'], '..')  ;?></a></h5>
                <div class="product-block_two-price"><?php echo wp_kses_post($product->get_price_html()); ?></div>
                <div class="product-block_two-lower-box">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="product-block_two-brand">
                            <span><?php radios_post_author_avatars(28);?></span>
                            <?php the_author();?>
                        </div>
                        <?php if(!empty($offer_badge_label) || $disc_percent_off != false):?>
                        <div class="product-block_two-off">
                            <?php echo esc_html($offer_badge_label);?>
                        </div>
                        <?php endif;?>
                    </div>
                </div>

                <div class="product-block_two-overlay">
                    <div class="overlay-image">
                        <?php echo get_the_post_thumbnail($idd, 'large');?>
                    </div>
                    <ul class="product-block_two-options">
                        <li><?php radios_add_to_cart_icon(true, false);?></li>
                        <li><?php echo do_shortcode( '[yith_compare_button product=' . get_the_ID() . ']' ); ?></li>
                        <li><?php echo do_shortcode( '[yith_wcwl_add_to_wishlist]' );?></li>
                    </ul>
                </div>
            </div>
        </div>
        <?php 							
            } wp_reset_query();
        ?>
        <?php endif; ?>
        </div>
    <?php 
    }
		
	
}


Plugin::instance()->widgets_manager->register_widget_type( new Trending_Product() );
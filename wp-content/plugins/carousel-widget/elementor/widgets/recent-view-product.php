<?php

/**
 * Elementor Single Widget
 * @package radios Extension
 * @since 1.0.0
 */

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

class Product_Recent_View extends Widget_Base {

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
		return 'vi-recent-view-id';
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
		return esc_html__( 'Product Recent View', 'radios-tools' );
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
   <section class="recent-review">
        <div class="auto-container">
            <?php if(!empty($settings['section_title'])):?>
            <h4><?php echo wp_kses($settings['section_title'], true);?></h4>
            <?php endif;?>
            <div class="feature-carousel-two owl-carousel owl-theme">

                <!-- Feature Three Block -->
                <?php 

                    global $woocommerce;
                    // Get recently viewed product cookies data
                    $viewed_products = ! empty( $_COOKIE['woocommerce_recently_viewed'] ) ? (array) explode( '|', $_COOKIE['woocommerce_recently_viewed'] ) : array();
                    $viewed_products = array_filter( array_map( 'absint', $viewed_products ) );

                    $args = array(
                        'post_type'      => 'product',
                        'posts_per_page' => !empty( $settings['product_per_page'] ) ? $settings['product_per_page'] : 1,
                        'ignore_sticky_posts' => true,
                        'post_status'         => 'publish',
                        'suppress_filters'    => false,
                        'order'          => $settings['postorder'],
                        'post__in'       => $viewed_products,
                        'orderby'        => 'rand'
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
                <div class="feature-three_block">
                    <div class="feature-three_block-inner">
                        <div class="feature-three_block-image">
                            <div class="heart-box fa fa-heart"></div>
                            <a href="<?php echo esc_url(get_permalink( $idd )); ?>"><?php echo get_the_post_thumbnail($idd, 'large');?></a>
                        </div>
                        <div class="feature-two_block-rating">
                        <?php 
                            $rating  = $product->get_average_rating();
                            $count   = $product->get_rating_count();
                            echo wc_get_rating_html( $rating, $count );
                        ?>
                            <?php if($count):?>
                            <i>(<?php echo esc_html($count);?>)</i>
                            <?php endif;?>
                        </div>
                        <h5 class="feature-three_block-heading"><a href="<?php the_permalink();?>"><?php echo wp_trim_words(get_the_title(), $settings['title_length'], '..')  ;?></a></h5>
                        <div class="feature-three_block-price"><?php echo wp_kses_post($product->get_price_html()); ?></div>
                    </div>
                </div>
                <?php 							
                    } wp_reset_query();
                ?>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php 
    }
		
	
}


Plugin::instance()->widgets_manager->register_widget_type( new Product_Recent_View() );
<?php

/**
 * Elementor Single Widget
 * @package radios Extension
 * @since 1.0.0
 */

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

class Product_Tab_v6 extends Widget_Base {

	/**
	 * Get widget name.
	 *
	 *                                                                                                                                                                                                                                                                                                     Elementor widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'vi-product-tab-6-id';
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
		return esc_html__( 'Product Tab 6', 'radios-tools' );
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
			'product_tabs',
			[
				'label' => esc_html__( 'Product Tab Option', 'radios-tools' ),
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
			'is_active',
			[
				'label'          => esc_html__( 'Active Tab Item', 'radios-tools' ),
				'type'           => Controls_Manager::SWITCHER,
				'label_on'       => esc_html__( 'YES', 'radios-tools' ),
				'label_off'      => esc_html__( 'NO', 'radios-tools' ),
				'return_value'   => 'yes',
				'default'        => 'no',
				'style_transfer' => true,
			]
		);
        
		$repeater->add_control(
			'title', [
				'label' => esc_html__( 'Tab Title', 'radios-tools' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => [
					'active' => true,
				],
				'placeholder' => __( 'Enter your Tab Title Here', 'radios-tools' ),
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
			'products_tab',
			[
				'label' => esc_html__( 'Add Product Item', 'radios-tools' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'title_field' => '{{{ title }}}',
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
		$this->start_controls_section(
			'banner_option',
			[
				'label' => esc_html__( 'Product Banner Option', 'radios-tools' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'banner_bg', [
				'label'       => esc_html__( 'Banner BG Image', 'radios-tools' ),
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
			'discount_text', [
				'label' => esc_html__( 'Discount Text', 'radios-tools' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => [
					'active' => true,
				],
				'placeholder' => __( 'Enter your Discount Text Here', 'radios-tools' ),
			]
		);
        $this->add_control(
			'discount_percent', [
				'label' => esc_html__( 'Discount Percent', 'radios-tools' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => [
					'active' => true,
				],
				'placeholder' => __( 'Enter your Discount Percent Text Here', 'radios-tools' ),
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
        $this->add_control(
			'banner_img', [
				'label' => esc_html__( 'Banner Image', 'radios-tools' ),
				'type' => Controls_Manager::MEDIA,
				'label_block' => true,
				'dynamic'     => [
					'active' => true,
				],
				'placeholder' => __( 'Enter your Banner Image Here', 'radios-tools' ),
			]
		);
		$this->end_controls_section();
	}


	protected function render() {
		$settings = $this->get_settings_for_display();
    ?>
    <!-- rd tab product start -->
    <div class="rd-tab-product mt-60">
        <div class="container">
            <div class="product__nav-wrap ul_li_between mb-25">
                <?php if(!empty($settings['section_title'])):?>
                <h2 class="section-heading"><span><?php echo wp_kses($settings['section_title'], true);?></span></h2>
                <?php endif;?>
                <ul class="product__nav nav nav-tabs" id="myTab" role="tablist">
                    <?php foreach($settings['products_tab'] as $item):?>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?php if('yes' == $item['is_active']){echo esc_attr('active show');}?>" id="settings-tab<?php echo esc_attr($item['_id']);?>" data-bs-toggle="tab" data-bs-target="#settings<?php echo esc_attr($item['_id']);?>" type="button" role="tab" aria-controls="settings<?php echo esc_attr($item['_id']);?>" aria-selected="false"><?php echo esc_html($item['title']);?></button>
                    </li>
                    <?php endforeach;?>
                </ul>
            </div>
            <div class="tab-content">
            <?php 
                foreach($settings['products_tab'] as $item):
                    $args = array(
                        'post_type'      => 'product',
                        'posts_per_page' => !empty( $item['product_per_page'] ) ? $item['product_per_page'] : 1,
                        'ignore_sticky_posts' => true,
                        'post_status'         => 'publish',
                        'suppress_filters'    => false,
                        'order'          => $item['postorder'],
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
                <div class="tab-pane animated fadeInUp show <?php if('yes' == $item['is_active']){echo esc_attr('active show');}?>" id="settings<?php echo esc_attr($item['_id']);?>" role="tabpanel" aria-labelledby="settings-tab<?php echo esc_attr($item['_id']);?>">
                    <div class="row mt-none-30">
                        <?php while ( $query->have_posts() ) {
                            $query->the_post();
                            $idd = get_the_ID();
                            $cat = '';
                            $product = wc_get_product( $idd );
                            $stock = get_post_meta( $idd, '_stock', true );
                            $title = wp_trim_words( get_the_title(), $settings['title_length'], '' );
                        ?>
                        <div class="col-lg-2 col-md-4 mt-30">
                            <div class="rd-product__item tx-product has-border pos-rel">
                                <div class="product__img text-center pos-rel">
                                    <a href="<?php echo esc_url(get_permalink( $idd )); ?>"><?php echo get_the_post_thumbnail($idd, 'large');?></a>
                                </div>
                                
                                <div class="product__content">
                                <?php if(!empty($stock)):?>
                                    <span class="product__available">Available: <span><?php echo esc_attr($stock);?></span></span>
                                    <div class="product__progress progress">
                                        <div class="progress-bar" role="progressbar" style="width: <?php echo esc_attr($stock);?>%" aria-valuenow="<?php echo esc_attr($stock);?>" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <?php endif;?>
                                    <h2 class="product__title"><a href="<?php echo esc_url(get_permalink( $idd )); ?>" tabindex="0"><?php echo esc_html($title);?></a></h2>
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
                        </div> 
                        <?php 							
                            } wp_reset_query();
                        ?>
                    </div>
                </div>
                <?php endif; endforeach; ?>
            </div>
        </div>
    </div>
    <!-- rd tab product end -->
    <?php 
    }
		
	
}


Plugin::instance()->widgets_manager->register_widget_type( new Product_Tab_v6() );
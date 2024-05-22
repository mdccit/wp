<?php

/**
 * Elementor Single Widget
 * @package radios Extension
 * @since 1.0.0
 */

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

class Product_Tab_v5 extends Widget_Base {

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
		return 'vi-product-tab-5-id';
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
		return esc_html__( 'Product Tab 5', 'radios-tools' );
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
    <div class="tx-product-style-01">
        <div class="tx-product-wrap">
            <div class="product__nav-wrap style-3 ul_li_between mb-20">
                <?php if($settings['section_title']):?>
                    <h2 class="section-heading"><span><?php echo wp_kses($settings['section_title'], true);?></span></h2>
                <?php endif;?>
                <ul class="product__nav nav nav-tabs" id="myTab3" role="tablist">
                    <?php foreach($settings['products_tab'] as $item):?>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?php if('yes' == $item['is_active']){echo esc_attr('active show');}?>" id="ctx-tab-03<?php echo esc_attr($item['_id']);?>" data-bs-toggle="tab" data-bs-target="#ctx-tab3<?php echo esc_attr($item['_id']);?>" type="button" role="tab" aria-controls="ctx-tab3<?php echo esc_attr($item['_id']);?>" aria-selected="false"><?php echo esc_html($item['title']);?></button>
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
                <div class="tab-pane animated fadeInUp <?php if('yes' == $item['is_active']){echo esc_attr('active show');}?>" id="ctx-tab3<?php echo esc_attr($item['_id']);?>" role="tabpanel" aria-labelledby="ctx-tab-03<?php echo esc_attr($item['_id']);?>">
                    <div class="row g-0">
                        <?php while ( $query->have_posts() ) {
                            $query->the_post();
                            $idd = get_the_ID();
                            $cat = '';
                            $product = wc_get_product( $idd );
                            $stock = get_post_meta( $idd, '_stock', true );
                            ?>
                            <div class="col-1-of-5">
                                <div class="product__item style-2">
                                    <div class="product__img text-center pos-rel">
                                        <a href="<?php echo esc_url(get_permalink( $idd )); ?>"><?php echo get_the_post_thumbnail($idd, 'large');?></a>
                                        <ul class="product__action style-2 ul_li">
											<li><?php echo do_shortcode( '[yith_compare_button product=' . get_the_ID() . ']' ); ?></li>
											<li><?php radios_add_to_cart_icon(true, false);?></li>
											<?php if(function_exists('YITH_WCWL')):?>
											<li><?php echo do_shortcode( '[yith_wcwl_add_to_wishlist]' );?></li>
											<?php endif;?>
                                        </ul>
                                    </div>
                                    <div class="product__content">
                                        <div class="product__review ul_li">
                                            <?php 
                                                $rating  = $product->get_average_rating();
                                                $count   = $product->get_rating_count();
                                                echo wc_get_rating_html( $rating, $count );
                                            ?>
                                            <?php if($count):?>
                                            <span>(<?php echo esc_attr($count);?>)</span>
                                            <?php endif;?>
                                        </div>
										<?php if(!empty($stock)):?>
											<div class="product__progress progress mt-2 color-primary">
												<div class="progress-bar" role="progressbar" style="width: <?php echo esc_attr($stock);?>%" aria-valuenow="<?php echo esc_attr($stock);?>" aria-valuemin="0" aria-valuemax="100"></div>
											</div>
										<?php endif;?>
                                        <h2 class="product__title"><a href="<?php echo esc_url(get_permalink( $idd )); ?>"><?php echo esc_html(get_the_title($idd)); ?></a></h2>
                                        <h4 class="product__price"><?php echo wp_kses_post($product->get_price_html()); ?></h4>
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
    <?php 
    }
		
	
}


Plugin::instance()->widgets_manager->register_widget_type( new Product_Tab_v5() );
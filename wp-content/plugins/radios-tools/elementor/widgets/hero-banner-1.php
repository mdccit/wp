<?php

/**
 * Elementor Single Widget
 * @package radios Extension
 * @since 1.0.0
 */

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

class Hero_Banner_One extends Widget_Base {

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
		return 'radios-hero-banner-1';
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
		return esc_html__( 'Hero Banner', 'radios-tools' );
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
			'product_hero__content',
			[
				'label' => esc_html__( 'Product Slider Option', 'radios-tools' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'shape_bg', [
				'label' => esc_html__( 'Shape BG', 'radios-tools' ),
				'type' => Controls_Manager::MEDIA,
			]
		);
		$this->add_control(
			'discount_text', [
				'label' => esc_html__( 'Discount Title', 'radios-tools' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => [
					'active' => true,
				],
				'placeholder' => __( 'Enter your Discount Title Here', 'radios-tools' ),
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
			'text', [
				'label' => esc_html__( 'Text', 'radios-tools' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => [
					'active' => true,
				],
				'placeholder' => __( 'Enter your Text Here', 'radios-tools' ),
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
				'placeholder' => __( 'Enter your Price Here', 'radios-tools' ),
			]
		);
		$this->add_control(
			'progress_count', [
				'label' => esc_html__( 'Progress Count', 'radios-tools' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => [
					'active' => true,
				],
				'placeholder' => __( 'Enter your Progress Here', 'radios-tools' ),
			]
		);
		$this->add_control(
			'stock_available', [
				'label' => esc_html__( 'Stock Count', 'radios-tools' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => [
					'active' => true,
				],
				'placeholder' => __( 'Enter your Stock Here', 'radios-tools' ),
			]
		);
		$this->add_control(
			'stock_out_available', [
				'label' => esc_html__( 'Stock Out Count', 'radios-tools' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => [
					'active' => true,
				],
				'placeholder' => __( 'Enter your Out Here', 'radios-tools' ),
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
				'placeholder' => __( 'Enter your Button Label Here', 'radios-tools' ),
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
		$repeater = new Repeater();

		$repeater->add_control(
			'media_lg', [
				'label' => esc_html__( 'Large Image', 'radios-tools' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
			]
		);
		
		$repeater->add_control(
			'discount_price', [
				'label' => esc_html__( 'Discount Price', 'radios-tools' ),
				'type' => \Elementor\Controls_Manager::TEXT,
			]
		);
		$repeater->add_control(
			'discount_label', [
				'label' => esc_html__( 'Discount Label', 'radios-tools' ),
				'type' => \Elementor\Controls_Manager::TEXT,
			]
		);

		$this->add_control(
			'product_gallerys',
			[
				'label' => esc_html__( 'Product Gallery', 'radios-tools' ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'title_field' => '{{{ discount_label }}}',
			]
		);
		
		$this->end_controls_section();

		$this->start_controls_section(
			'product_carousel__content',
			[
				'label' => esc_html__( 'Product Carousel Option', 'radios-tools' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'prod_title', [
				'label' => esc_html__( 'Product Heading Title', 'radios-tools' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => [
					'active' => true,
				],
				'placeholder' => __( 'Enter your Product Title Here', 'radios-tools' ),
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
				'label'   => __( 'Product Per Page', 'radios-tools' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 1,
				'default' => 4,
			]
		);
        $this->add_control(
			'product_categories',
			[
				'type'        => Controls_Manager::SELECT2,
				'label'       => esc_html__( 'Select Product Categories', 'radios-tools' ),
				'options'     => radios_product_category(),
				'label_block' => true,
				'multiple'    => true,
			]
		);
        $this->add_control(
			'product_tags',
			[
				'type'        => Controls_Manager::SELECT2,
				'label'       => esc_html__( 'Product Tags', 'radios-tools' ),
				'multiple'    => true,
                  'options'     => array_flip(radios_item_tag_lists( 'tags', array(
                    'sort_order'  => 'ASC',
                    'taxonomies'    => 'product_tag',
                    'hide_empty'  => false,
                ) )),
			]
		);
		
		$this->end_controls_section();
	}


	protected function render() {
		$settings = $this->get_settings_for_display();		

    ?>
   <!-- hero start -->
   <div class="hero hero__height ul_li" data-background="<?php echo esc_url($settings['shape_bg']['url']);?>">
		<div class="container">
			<div class="row align-items-center mt-none-30">
				<div class="col-lg-9 mt-30">
					<div class="row align-items-center flex-row-reverse mt-none-30">
						<div class="col-lg-7 mt-30">
							<div class="hero__product">
								<div class="hero__product-wrap">
									<div class="hero__product-carousel">
										<?php foreach($settings['product_gallerys'] as $gall):?>
											<div class="hero__product-item">
												<img src="<?php echo esc_url($gall['media_lg']['url']);?>" alt="<?php echo esc_attr($gall['media_lg']['alt']);?>">
											</div>
										<?php endforeach;?>
									</div>
									<div class="hero__product-carousel-nav">
										<?php foreach($settings['product_gallerys'] as $gall):?>
											<div class="hero__product-item-nav">
												<div class="image">
													<img src="<?php echo esc_url($gall['media_lg']['url']);?>" alt="<?php echo esc_attr($gall['media_lg']['alt']);?>">
												</div>
											</div>
										<?php endforeach;?>
									</div>
									<?php if(!empty($gall['discount_price'])):?>
										<span class="hero__product-offer">
											<span class="discount"><?php echo wp_kses($gall['discount_price'], true);?></span>
											<span><?php echo wp_kses($gall['discount_label'], true);?></span>
										</span>
									<?php endif;?>
								</div>
							</div>
						</div>
						<div class="col-lg-5 mt-30">
							<div class="hero__content">
								<?php if(!empty($settings['discount_text'])):?>
									<span class="subtitle"><?php echo wp_kses($settings['discount_text'], true)?></span>
								<?php endif;?>
								<?php if(!empty($settings['title'])):?>
									<h2 class="title"><?php echo wp_kses($settings['title'], true)?></h2>
								<?php endif;?>

								<?php if(!empty($settings['text'])):?>
									<p><?php echo wp_kses($settings['text'], true)?></p>
								<?php endif;?>

								<?php if(!empty($settings['price'])):?>
									<h3 class="price"><?php echo wp_kses($settings['price'], true)?></h3>
								<?php endif;?>
								<?php if(!empty($settings['progress_count'])):?>
								<div class="mxw_343 mb-20">
									<div class="product__progress progress h-16 color-primary">
										<div class="progress-bar" role="progressbar" style="width: <?php echo esc_attr($settings['progress_count']);?>%" aria-valuenow="<?php echo esc_attr($settings['progress_count']);?>" aria-valuemin="0" aria-valuemax="100"></div>
									</div>
									<div class="ul_li_between mb-6">
										<?php if($settings['stock_available']):?>
										<span class="product__available"><?php echo wp_kses($settings['stock_available'], true)?></span>
										<?php endif;?>

										<?php if($settings['stock_available']):?>
											<span class="product__available"><?php echo wp_kses($settings['stock_out_available'], true)?></span>
										<?php endif;?>

									</div>
								</div>
								<?php endif;?>
								<?php if($settings['button_label']):?>
								<a class="hero__btn" href="<?php echo esc_url($settings['button_link']['url']);?>"><?php echo wp_kses($settings['button_label'], true)?> <i class="far fa-long-arrow-right"></i></a>
								<?php endif;?>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-3 col-md-6 mt-30">
					<div class="hot-deal__slide-wrap style-2 bg-white ">
						<?php if(!empty($settings['prod_title'])):?>
							<h2 class="section-heading mb-25"><span><?php echo wp_kses($settings['prod_title'], true);?></span></h2>
						<?php endif;?>
						<div class="hot-deal__slide tx-arrow">
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
								$product = wc_get_product($idd);
							?>
							
							<div class="hot-deal__item text-center">
								<div class="thumb">
									<a href="<?php echo esc_url(get_permalink( $idd )); ?>"><?php echo get_the_post_thumbnail($idd, 'large');?></a>
								</div>
								<div class="content">
									<?php 
										$rating  = $product->get_average_rating();
										$count   = $product->get_rating_count();
										echo wc_get_rating_html( $rating, $count );
									?>
									<h2 class="title mb-15"><a href="<?php echo esc_url(get_permalink( $idd )); ?>"><?php echo esc_html(get_the_title($idd)); ?></a></h2>
									<h4 class="product__price mb-20"><?php echo wp_kses_post($product->get_price_html()); ?></h4>
									<?php if($product->get_stock_quantity()):?>
									<div class="mxw_216 m-auto">
										<div class="product__progress progress mb-6 h-8 color-primary">
											<div class="progress-bar" role="progressbar" style="width: <?php echo $product->get_stock_quantity();?>%" aria-valuenow="<?php echo $product->get_stock_quantity();?>" aria-valuemin="0" aria-valuemax="100"></div>
										</div>
										<div class="ul_li_between">
											<span class="product__available">Available: <span><?php echo $product->get_stock_quantity();?></span></span>
										</div>
									</div>
									<?php endif;?>
								</div>
							</div>
							<?php 							
								} wp_reset_query();
								endif;
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- hero end -->
    <?php 
    }
		
	
}


Plugin::instance()->widgets_manager->register_widget_type( new Hero_Banner_One() );
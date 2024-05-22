<?php

/**
 * Elementor Single Widget
 * @package radios Extension
 * @since 1.0.0
 */

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

class Product_Carousel extends Widget_Base {

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
		return 'radios-product-car';
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
		return esc_html__( 'Product Carousel', 'radios-tools' );
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
			'menu_item_info',
			[
				'label' => esc_html__( 'Category Carousel Option', 'radios-tools' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);
		
		$repeater = new Repeater();
		
		$repeater->add_control(
			'subtitle', [
				'label' => esc_html__( 'Sub Title', 'radios-tools' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => [
					'active' => true,
				],
				'placeholder' => __( 'Enter your Title Here', 'radios-tools' ),
			]
		);
		$repeater->add_control(
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
		$repeater->add_control(
			'desc', [
				'label' => esc_html__( 'Desc', 'radios-tools' ),
				'type' => Controls_Manager::TEXTAREA,
				'label_block' => true,
				'dynamic'     => [
					'active' => true,
				],
				'placeholder' => __( 'Enter your Desc Here', 'radios-tools' ),
			]
		);
		$repeater->add_control(
			'list1_icon', [
				'label' => esc_html__( 'List Icon', 'radios-tools' ),
				'type' => Controls_Manager::ICONS,
			]
		);
		$repeater->add_control(
			'list1', [
				'label' => esc_html__( 'List', 'radios-tools' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => [
					'active' => true,
				],
			]
		);
		$repeater->add_control(
			'list2_icon', [
				'label' => esc_html__( 'List 2 Icon', 'radios-tools' ),
				'type' => Controls_Manager::ICONS,
			]
		);
		$repeater->add_control(
			'list2', [
				'label' => esc_html__( 'List 2', 'radios-tools' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => [
					'active' => true,
				],
			]
		);
		$repeater->add_control(
			'list3_icon', [
				'label' => esc_html__( 'List 3 Icon', 'radios-tools' ),
				'type' => Controls_Manager::ICONS,
			]
		);
		$repeater->add_control(
			'list3', [
				'label' => esc_html__( 'List 3', 'radios-tools' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => [
					'active' => true,
				],
			]
		);
		$repeater->add_control(
			'product_img', [
				'label' => esc_html__( 'Product Image', 'radios-tools' ),
				'type' => Controls_Manager::MEDIA,
				'label_block' => true,
				'dynamic'     => [
					'active' => true,
				],
			]
		);
		$repeater->add_control(
			'badge', [
				'label' => esc_html__( 'Badge', 'radios-tools' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => [
					'active' => true,
				],
			]
		);
		$repeater->add_control(
			'price', [
				'label' => esc_html__( 'Price', 'radios-tools' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => [
					'active' => true,
				],
			]
		);
		$repeater->add_control(
			'text', [
				'label' => esc_html__( 'Text', 'radios-tools' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => [
					'active' => true,
				],
			]
		);
		$repeater->add_control(
			'btn_label', [
				'label' => esc_html__( 'Button Label', 'radios-tools' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => [
					'active' => true,
				],
			]
		);
		$repeater->add_control(
			'btn_link', [
				'label' => esc_html__( 'Button Link', 'radios-tools' ),
				'type' => Controls_Manager::URL,
				'label_block' => true,
				'dynamic'     => [
					'active' => true,
				],
			]
		);
		
		$repeater->add_control(
			'bg_color',
			[
				'label' => esc_html__( 'Bg Color', 'radios-tools' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .product-block_one .product-block_one-inner::before' => 'background-color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'products',
			[
				'label' => esc_html__( 'Products', 'radios-tools' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'title_field' => '{{{ title }}}',
			]
		);
		$this->end_controls_section();
	}


	protected function render() {
		$settings = $this->get_settings_for_display();		

    ?>
    <div class="product-one">
		<div class="auto-container">
			<div class="single-item-carousel owl-carousel owl-theme">
				<?php foreach($settings['products'] as $item):?>
				<div class="slide elementor-repeater-item-<?php echo esc_attr($item['_id']);?>">
					<!-- Product Block One -->
					<div class="product-block_one">
						<div class="product-block_one-inner">
							<div class="row clearfix">
								<!-- Content Left -->
								<div class="product-block_one-content_left col-lg-6 col-md-12 col-sm-12">
									<div class="product-block_one-content_inner">
										<?php if(!empty($item['subtitle'])):?>
											<div class="product-block_one-title"><?php echo wp_kses($item['subtitle'], true);?></div>
										<?php endif;?>
										<h2 class="product-block_one-heading"><a href="<?php echo esc_url($item['btn_link']['url']);?>"><?php echo wp_kses($item['title'], true);?></a></h2>
										<div class="product-block_one-text"><?php echo wp_kses($item['desc'], true);?></div>
										<ul class="product-block_one-list">
											<?php if(!empty($item['list1'])):?>
												<li><span class="icon"><?php \Elementor\Icons_Manager::render_icon( $item['list1_icon'], [ 'aria-hidden' => 'true' ] ); ?></span><?php echo wp_kses($item['list1'], true);?></li>
											<?php endif;?>
											<?php if(!empty($item['list2'])):?>
												<li><span class="icon"><?php \Elementor\Icons_Manager::render_icon( $item['list2_icon'], [ 'aria-hidden' => 'true' ] ); ?></span><?php echo wp_kses($item['list2'], true);?></li>
											<?php endif;?>
											<?php if(!empty($item['list3'])):?>
												<li><span class="icon"><?php \Elementor\Icons_Manager::render_icon( $item['list3_icon'], [ 'aria-hidden' => 'true' ] ); ?></span><?php echo wp_kses($item['list3'], true);?></li>
											<?php endif;?>
										</ul>
									</div>
								</div>
								<!-- Content Image -->
								<?php if(!empty($item['product_img']['url'])):?>
									<div class="product-block_one_image col-lg-2 col-md-12 col-sm-12">
										<img src="<?php echo esc_url($item['product_img']['url']);?>" alt="" />
									</div>
								<?php endif;?>
								<!-- Content Left -->
								<div class="product-block_one-content_right col-lg-4 col-md-12 col-sm-12">
									<div class="product-block_one_right-inner">
										<?php if(!empty($item['badge'])):?>
											<div class="product-block_one-sale"><?php echo esc_html($item['badge']);?></div>
										<?php endif;?>
										<?php if(!empty($item['price'])):?>
											<div class="product-block_one-price"><?php echo esc_html($item['price']);?></div>
										<?php endif;?>
										<?php if(!empty($item['text'])):?>
											<div class="product-block_one-off"><?php echo esc_html($item['text']);?></div>
										<?php endif;?>

										<?php if(!empty($item['btn_label'])):?>
											<a href="<?php echo esc_url($item['btn_link']['url']);?>" class="shop-btn theme-btn"><?php echo esc_html($item['btn_label']);?></a>
										<?php endif;?>

									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php endforeach;?>
			</div>
		</div>
    </div>
    <?php 
    }
		
	
}


Plugin::instance()->widgets_manager->register_widget_type( new Product_Carousel() );
<?php

/**
 * Elementor Single Widget
 * @package radios Extension
 * @since 1.0.0
 */

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

class Product_Category_List extends Widget_Base {

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
		return 'radios-product-cate-list';
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
		return esc_html__( 'Menu Item Category', 'radios-tools' );
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
				'label' => esc_html__( 'Menu Itemn Option', 'radios-tools' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'style',
			[
				'label' => esc_html__( 'Style', 'radios-tools' ),
				'type' => Controls_Manager::SELECT,
				'default' => '1',
				'options' => [
					'1'  => esc_html__( 'Style One', 'radios-tools' ),
					'2' => esc_html__( 'Style Two', 'radios-tools' ),
				],
			]
		);
		$this->add_control(
			'shape_img', [
				'label' => esc_html__( 'Shape Image', 'radios-tools' ),
				'type' => Controls_Manager::MEDIA,
			]
		);
		$this->add_control(
			'title', [
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
			'parent_title', [
				'label' => esc_html__( 'Parrent Menu Title', 'radios-tools' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => [
					'active' => true,
				],
				'placeholder' => __( 'Enter your Parent Title Here', 'radios-tools' ),
			]
		);
		$repeater = new Repeater();
		
		$repeater->add_control(
			'menu_icon', [
				'label' => esc_html__( 'Meni ', 'radios-tools' ),
				'type' => Controls_Manager::ICONS,
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
			'link', [
				'label' => esc_html__( 'Link', 'radios-tools' ),
				'type' => Controls_Manager::URL,
				'label_block' => true,
				'dynamic'     => [
					'active' => true,
				],
				'placeholder' => __( 'Enter your Title Here', 'radios-tools' ),
			]
		);
		
		$this->add_control(
			'menuitems',
			[
				'label' => esc_html__( 'Menu Items', 'radios-tools' ),
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
    <div class="rd-category__wrap">
        <?php if(!empty($settings['title'])):?>
            <h2 class="section-heading mb-25"><span><?php echo wp_kses( $settings['title'], true );?></span></h2>
        <?php endif;?>
        <ul class="rd-category__list list-unstyled" data-background="<?php echo esc_url($settings['shape_img']['url']);?>">
            <?php if(!empty($settings['parent_title'])):?>
                <li class="title"><?php echo esc_html($settings['parent_title'])?></li>
            <?php endif;?>
            <?php foreach($settings['menuitems'] as $item):?>
                <li><a href="<?php echo esc_url($item['link']['url']);?>"><?php \Elementor\Icons_Manager::render_icon( $item['menu_icon'], [ 'aria-hidden' => 'true' ] ); ?><?php echo wp_kses( $item['title'], true )?></a></li>
            <?php endforeach;?>
        </ul>
    </div>

    <?php 
    }
		
	
}


Plugin::instance()->widgets_manager->register_widget_type( new Product_Category_List() );
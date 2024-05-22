<?php

/**
 * Elementor Single Widget
 * @package Papurfy Extension
 * @since 1.0.0
 */

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

class radios_About_Info extends Widget_Base {

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
		return 'vaid-about-info';
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
		return esc_html__( 'Radios About Info', 'radios-tools' );
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
			'about_info_options',
			[
				'label' => esc_html__( 'About Info Option', 'radios-tools' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'about_img', [
				'label'       => esc_html__( 'About Image', 'radios-tools' ),
				'type'        => Controls_Manager::MEDIA,
			]
		);
        $repeater = new Repeater();
        $repeater->add_control(
			'count', [
				'label'       => esc_html__( 'Count Number', 'radios-tools' ),
				'type'        => Controls_Manager::TEXT,
			]
		);
		$repeater->add_control(
			'title', [
				'label' => esc_html__( 'Title', 'radios-tools' ),
				'type' => Controls_Manager::TEXT,
                'label_block' => true,
			]
		);
		$repeater->add_control(
			'description', [
				'label' => esc_html__( 'Description', 'radios-tools' ),
				'type' => Controls_Manager::TEXTAREA,
                'label_block' => true,
			]
		);
		
		$this->add_control(
			'features',
			[
				'label' => esc_html__( 'Add New Features Item', 'radios-tools' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
                'title_field' => '{{{ count }}} {{{ title }}}',
			]
		);
        $this->add_control(
			'headingTitle', [
				'label' => esc_html__( 'Heading Title', 'radios-tools' ),
				'type' => Controls_Manager::TEXTAREA,
                'label_block' => true,
			]
		);
        $this->add_control(
			'headingdescription', [
				'label' => esc_html__( 'Heading Description', 'radios-tools' ),
				'type' => Controls_Manager::TEXTAREA,
                'label_block' => true,
			]
		);
        $repeater = new Repeater();
        $repeater->add_control(
			'is_active',
			[
				'label' => esc_html__( 'Active', 'radios-tools' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'YES', 'radios-tools' ),
				'label_off' => esc_html__( 'NO', 'radios-tools' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);
		$repeater->add_control(
			'title', [
				'label' => esc_html__( 'Title', 'radios-tools' ),
				'type' => Controls_Manager::TEXT,
                'label_block' => true,
			]
		);
        $repeater->add_control(
			'description',
			[
				'label' => esc_html__( 'Description', 'radios-tools' ),
				'type' => Controls_Manager::WYSIWYG,
				'placeholder' => esc_html__( 'Type your List Item description here', 'radios-tools' ),
			]
		);
		
		$this->add_control(
			'tabsitems',
			[
				'label' => esc_html__( 'Add New Tab Item', 'radios-tools' ),
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
    <!-- about info start -->
    <section class="about-info pt-75 pb-100">
        <div class="container">
            <div class="about-info__wrap bg_Img" data-background="assets/img/bg/bg_30.jpg">
                <div class="row align-items-center">
                    <div class="col-xl-4 col-lg-5">
                        <div class="about-info__box">
                            <?php foreach($settings['features'] as $feature):?>
                            <div class="about-info__item d-flex">
                                <span class="number"><?php echo esc_html($feature['count']);?></span>
                                <div class="content">
                                    <h4><?php echo wp_kses( $feature['title'], true );?></h4>
                                    <p><?php echo wp_kses( $feature['description'], true );?></p>
                                </div>
                            </div>
                            <?php endforeach;?>
                        </div>
                    </div>
                    <div class="col-xl-8 col-lg-7">
                        <div class="about-info__tab-wrap pl-150">
                            <h2><?php echo wp_kses( $settings['headingTitle'], true );?></h2>
                            <p><?php echo wp_kses( $settings['headingdescription'], true );?></p>
                            <div class="about-info__tab mt-25">
                                <ul class="nav nav-tabs" id="myTab" role="tablist">
                                    <?php foreach($settings['tabsitems'] as $item):?>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link <?php if('yes' == $item['is_active']){echo esc_attr('active');}?>" id="home-tab<?php echo esc_attr($item['_id'])?>" data-bs-toggle="tab" data-bs-target="#home<?php echo esc_attr($item['_id'])?>" type="button" role="tab" aria-controls="home" aria-selected="true"><?php echo esc_html($item['title']);?></button>
                                    </li>
                                    <?php endforeach;?>
                                </ul>
                                <div class="tab-content" id="myTabContent">
                                    <?php foreach($settings['tabsitems'] as $item):?>
                                    <div class="tab-pane animated fadeInUp <?php if('yes' == $item['is_active']){echo esc_attr('show active');}?>" id="home<?php echo esc_attr($item['_id'])?>" role="tabpanel" aria-labelledby="home-tab<?php echo esc_attr($item['_id'])?>">
                                        <div class="about-info__tab-content">
                                            <?php echo wp_kses( $item['description'], true );?>
                                        </div>
                                    </div>
                                    <?php endforeach;?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- about info end -->
    <?php 
    }
		
	
}


Plugin::instance()->widgets_manager->register_widget_type( new radios_about_info() );
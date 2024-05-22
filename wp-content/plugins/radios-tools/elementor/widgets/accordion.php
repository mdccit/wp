<?php

/**
 * Elementor Single Widget
 * @package radios Tools
 * @since 1.0.0
 */

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

class radios_Accordion extends Widget_Base {

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
		return 'radios-accordion-id';
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
		return esc_html__( 'radios Accordion', 'radios-tools' );
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
			'radios_faq_option',
			[
				'label' => esc_html__( 'Accordion Option', 'radios-tools' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);
        $repeater = new Repeater();
		$repeater->add_control(
			'active',
			[
				'label' => esc_html__( 'Active', 'radios-tools' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'radios-tools' ),
				'label_off' => esc_html__( 'Hide', 'radios-tools' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);
		$repeater->add_control(
			'title', [
				'label' => esc_html__( 'Title', 'radios-tools' ),
				'type' => Controls_Manager::TEXTAREA,
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
			'accordions',
			[
				'label' => esc_html__( 'Add New Accordions Item', 'radios-tools' ),
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
    <div class="faq_wrap">
        <ul class="accordion_box clearfix">
            <?php foreach($settings['accordions'] as $item):?>
            <li class="accordion block">
                <div class="acc-btn <?php if($item['active'] == 'yes'){ echo esc_attr('active');}?>">
                    <?php echo wp_kses( $item['title'], true )?>
                </div>
                <div class="acc_body <?php if($item['active'] == 'yes'){ echo esc_attr('current active-block');}?>">
                    <div class="content">
                        <?php echo wp_kses( $item['description'], true )?>
                    </div>
                </div>
            </li>
            <?php endforeach;?>
        </ul>
    </div>
    <?php 
    }
		
	
}


Plugin::instance()->widgets_manager->register_widget_type( new radios_Accordion() );
<?php

/**
 * Elementor Single Widget
 * @package radios Tools
 * @since 1.0.0
 */

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

class Radios_Info_Box extends Widget_Base {

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
		return 'da-info-box-id';
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
		return esc_html__( 'Radios Info Box', 'radios-tools' );
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
			'radios_slider_option',
			[
				'label' => esc_html__( 'Banner Option', 'radios-tools' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);
		
        $repeater = new Repeater();

		$repeater->add_control(
			'feedback_icon',
			[
				'label' => esc_html__( 'Info Icon', 'radios-tools' ),
				'type' => Controls_Manager::MEDIA,
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
			'text', [
				'label' => esc_html__( 'Text', 'radios-tools' ),
				'type' => Controls_Manager::TEXTAREA,
				'show_label' => false,
			]
		);

		$this->add_control(
			'features',
			[
				'label' => esc_html__( 'Add Features Box Item', 'radios-tools' ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'title_field' => '{{{ title }}}',
			]
		);
		
		

		$this->end_controls_section();
	}


	protected function render() {
		$settings = $this->get_settings_for_display();		

    ?>
        <!-- feature start -->
        <div class="feature pt-40 pb-30">
            <div class="container">
                <div class="feature__wrap ul_li">
                    <?php foreach($settings['features'] as $item):?>
                    <div class="feature__item ul_li">
                        <div class="icon">
                            <img src="<?php echo esc_url($item['feedback_icon']['url']);?>" alt="<?php echo esc_attr($item['feedback_icon']['alt']);?>">
                        </div>
                        <div class="content">
                            <h3><?php echo esc_html($item['title']);?></h3>
                            <p><?php echo esc_html($item['text']);?></p>
                        </div>
                    </div>
                    <?php endforeach;?>
                </div>
            </div>
        </div>
        <!-- feature end -->
    <?php 
    }
		
	
}


Plugin::instance()->widgets_manager->register_widget_type( new Radios_Info_Box() );
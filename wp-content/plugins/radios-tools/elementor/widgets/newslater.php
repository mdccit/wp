<?php

/**
 * Elementor Single Widget
 * @package radios Tools
 * @since 1.0.0
 */

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

class radios_newslater_Item extends Widget_Base {

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
		return 'vi-newslater-id';
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
		return esc_html__( 'Newsletter', 'radios-tools' );
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
			'radios_icon_option',
			[
				'label' => esc_html__( 'Icon Box Option', 'radios-tools' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);
		
		$this->add_control(
			'title', [
				'label' => esc_html__( 'Title', 'radios-tools' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
			]
		);

		$this->add_control(
			'text', [
				'label' => esc_html__( 'Text', 'radios-tools' ),
				'type' => Controls_Manager::TEXTAREA,
				'show_label' => false,
			]
		);
		$this->add_control(
			'newsletter_code', [
				'label' => esc_html__( 'Newsletter', 'radios-tools' ),
				'type' => Controls_Manager::TEXTAREA,
				'show_label' => false,
			]
		);


		$this->end_controls_section();
	}


	protected function render() {
		$settings = $this->get_settings_for_display();		

    ?>
    <div class="newslater gray-bg pt-55 pb-60">
            <div class="container mxw_1530">
            <div class="newslater__three ul_li">
                <div class="newslater__content">
                    <?php if(!empty($settings['title'])):?>
                        <h2 class="title"><?php echo wp_kses($settings['title'], true)?></h2>
                    <?php endif;?>

                    <?php if(!empty($settings['text'])):?>
                        <p><?php echo wp_kses($settings['text'], true)?></p>
                    <?php endif;?>

                </div>
                <?php if(!empty($settings['newsletter_code'])):?>
                    <div class="newslater__form">
                        <?php echo do_shortcode($settings['newsletter_code']);?>
                    </div>
                <?php endif;?>
            </div>
        </div>
    </div>
    <?php 
    }
		
	
}


Plugin::instance()->widgets_manager->register_widget_type( new radios_newslater_Item() );
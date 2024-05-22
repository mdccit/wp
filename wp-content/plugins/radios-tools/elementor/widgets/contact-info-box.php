<?php

/**
 * Elementor Single Widget
 * @package radios Tools
 * @since 1.0.0
 */

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

class radios_Contact_Info_Box extends Widget_Base {

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
		return 'rid-contact-info-box-id';
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
		return esc_html__( 'radios Contact Info', 'radios-tools' );
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
			'radios_contact_option',
			[
				'label' => esc_html__( 'Contact Info Option', 'radios-tools' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);
		
        $repeater = new Repeater();
        $repeater->add_control(
			'is_active',
			[
				'label' => esc_html__( 'Active Item', 'radios-tools' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'YES', 'radios-tools' ),
				'label_off' => esc_html__( 'NO', 'radios-tools' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);
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
			'item_description',
			[
				'label' => esc_html__( 'Description', 'radios-tools' ),
				'type' => Controls_Manager::WYSIWYG,
				'default' => esc_html__( 'Default description', 'radios-tools' ),
			]
		);

		$this->add_control(
			'contacts_info',
			[
				'label' => esc_html__( 'Add Contact Info Item', 'radios-tools' ),
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
    <section class="contact-info">
        <div class="container">
            <div class="row justify-content-center mt-none-30">
                <?php foreach($settings['contacts_info'] as $item):?>
                <div class="col-xl-3 col-lg-4 col-md-6 mt-30">
                    <div class="contact-info__item <?php if($item['is_active'] == 'yes'){echo esc_attr('active');}?> d-flex">
                        <span class="icon"><img src="<?php echo esc_url($item['feedback_icon']['url']);?>" alt="<?php echo esc_attr($item['feedback_icon']['alt']);?>"></span>
                        <div class="content">
                            <h3><?php echo wp_kses($item['title'], true)?></h3>
                            <?php echo wp_kses($item['item_description'], true)?>
                        </div>
                    </div>
                </div>
                <?php endforeach;?>
            </div>
        </div>
    </section>
    <?php 
    }
		
	
}


Plugin::instance()->widgets_manager->register_widget_type( new radios_Contact_Info_Box() );
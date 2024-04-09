<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Custom Manual Payment Gateway.
 */
class CM_WC_Gateway_Manual extends WC_Payment_Gateway {

    public $instructions;
    /**
     * Constructor for the gateway.
     */
    public function __construct() {
        $this->id                 = 'cm_manual'; // Unique ID for your payment gateway
        $this->icon               = ''; // URL of the icon that will be displayed on checkout page near your gateway name
        $this->has_fields         = false; // In case you need a custom credit card form
        $this->method_title       = 'CM Manual Payment';
        $this->method_description = 'Description of the custom manual payment method';

        // Load the settings.
        $this->init_form_fields();
        $this->init_settings();

        // Define user set variables
        $this->title = $this->get_option('title', 'Manual Payment');
        $this->description = $this->get_option('description', 'Default description');
        $this->instructions = $this->get_option('instructions', $this->description);

        // Actions
        add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
    }

    /**
     * Initialise Gateway Settings Form Fields.
     */
    public function init_form_fields() {
        $this->form_fields = array(
            'enabled' => array(
                'title'   => 'Enable/Disable',
                'type'    => 'checkbox',
                'label'   => 'Enable Custom Manual Payment',
                'default' => 'yes'
            ),
            'title' => array(
                'title'       => 'Title',
                'type'        => 'text',
                'description' => 'This controls the title which the user sees during checkout.',
                'default'     => 'Manual Payment',
                'desc_tip'    => true,
            ),
            'description' => array(
                'title'       => 'Description',
                'type'        => 'textarea',
                'description' => 'Payment method description that the customer will see on your checkout.',
                'default'     => 'Please remit payment to Store Name upon pickup or delivery.',
                'desc_tip'    => true,
            ),
        );
    }
}

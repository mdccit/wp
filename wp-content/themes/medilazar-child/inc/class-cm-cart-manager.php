<?php

namespace CM;


class Cart_Manager {

    private $session_manager;
    public function __construct($session_manager) {
        $this->session_manager = $session_manager;
        add_action('woocommerce_add_to_cart', array($this, 'cm_handle_add_to_cart'), 10, 6);
        add_action('woocommerce_before_cart', array($this, 'filter_cart_contents'));
        add_action('woocommerce_checkout_create_order', array($this, 'checkout_create_order'), 10, 2);
    }

    public function start_session() {
        // Method logic to start a session
        error_log("Session started within CM namespace");
    }

    function cm_handle_add_to_cart($cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data) {
        if (is_session_specific_user()) {
            global $woocommerce, $wpdb;
            $session_key = $this->session_manager->get_session_key_from_cookie(); // This function retrieves and validates the session key
    
            $table_name = $wpdb->prefix . 'cm_cart_data';
            
            // Attempt to fetch the session ID for the current session key and email
            $session_id = $wpdb->get_var($wpdb->prepare(
                "SELECT session_id FROM {$wpdb->prefix}cm_sessions WHERE session_key = %s ",
                $session_key
            ));
    
            if (!$session_id) {
                // If no session_id is found, possibly create a new session or handle the error
                return; // Exit the function or handle accordingly
            }
    
            // Serialize the current cart data
            $cart_data = serialize($woocommerce->cart->get_cart());
    
            // Check if a record already exists for the given session_id
            $exists = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM $table_name WHERE session_id = %d",
                $session_id
            ));
    
            if ($exists > 0) {
                // If a record exists, update the existing cart data
                $result = $wpdb->update(
                    $table_name,
                    array(
                        'cart_data' => $cart_data,
                        'updated_at' => current_time('mysql', 1) // Use GMT time
                    ),
                    array('session_id' => $session_id), 
                    array('%s', '%s'),
                    array('%d') 
                );
            } else {
                // If no record exists, insert a new one
                $result = $wpdb->insert(
                    $table_name,
                    array(
                        'session_id' => $session_id,
                        'cart_data' => $cart_data,
                        'created_at' => current_time('mysql', 1), // Use GMT time
                        'updated_at' => current_time('mysql', 1) // Use GMT time
                    ),
                    array('%d', '%s', '%s', '%s') // Value formats
                );
            }
    
            if (false === $result) {
                error_log('DB FAILED. Unable to insert/update cart data for session-specific user.');
            } else {
                error_log('DB SUCCESS. Cart data inserted/updated for session-specific user.');
            }
    
            // Prevent WooCommerce from adding the product to the default session cart
            if (!defined('DOING_AJAX') || !DOING_AJAX) {
                wp_redirect(wc_get_cart_url());
                exit;
            }
        } else {
            error_log('NOT SESSION SPECIFIC USER');
        }
    }
    
    

    function custom_checkout_create_order($order, $data) {
        global $session_manager;
        if (is_session_specific_user()) {
            global $wpdb;
            $session_key = $session_manager->get_session_key_from_cookie();
            $session_id = $session_manager->get_session_id_by_key($session_key);
            $table_name = $wpdb->prefix . 'cm_cart_data';
            $cart_data = $wpdb->get_var($wpdb->prepare("SELECT cart_data FROM $table_name WHERE session_id = %d", $session_id));
    
            if (!empty($cart_data)) {
                // You might need to adjust order items based on the serialized cart data
                // This part depends on how you want to handle session-specific order creation
            }
        }
    }


    function custom_filter_cart_contents() {
        if (is_session_specific_user()) {
            global $woocommerce, $wpdb;
            $session_key = $this->session_manager->get_session_key_from_cookie();
            $session_id = $this->session_manager->get_session_id_by_key($session_key);
    
            if (!$session_id) {
                // No session ID found, so clear the cart or handle accordingly.
                $woocommerce->cart->empty_cart();
                return;
            }
    
            $table_name = $wpdb->prefix . 'cm_cart_data';
            $session_cart_data_serialized = $wpdb->get_var($wpdb->prepare("SELECT cart_data FROM $table_name WHERE session_id = %d", $session_id));
    
            if (!empty($session_cart_data_serialized)) {
                $woocommerce->cart->empty_cart(); // Clear the cart to repopulate it with session-specific items
                $session_cart_data = unserialize($session_cart_data_serialized);
                
                // Repopulate the WooCommerce cart with the session-specific cart items
                foreach ($session_cart_data as $item_key => $item_value) {
                    $woocommerce->cart->add_to_cart($item_key, $item_value['quantity']);
                }
            }
        }
    }
    
    
}

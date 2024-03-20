<?php

namespace CM;


class Cart_Manager {

    private $session_manager;
    public function __construct($session_manager) {
        $this->session_manager = $session_manager;
        add_action('woocommerce_loaded', array($this, 'initialize_cart_handling'));
        add_action('woocommerce_before_cart', array($this, 'load_cart_data_for_session_specific_user'));
        // add_action('woocommerce_before_cart', array($this, 'cm_filter_cart_contents'));
        add_action('woocommerce_add_to_cart', array($this, 'cm_handle_add_to_cart'), 10, 6);     
        add_action('woocommerce_checkout_create_order', array($this, 'checkout_create_order'), 10, 2);
        add_action('wp_logout', 'handle_user_logout');
        
     
    }

    public function initialize_cart_handling() {
        add_action('woocommerce_before_calculate_totals', array($this, 'cm_filter_cart_contents'));
    }

    public function start_session() {
        // Method logic to start a session
        error_log("Session started within CM namespace");
    }

    function handle_user_logout() {
        global $session_manager, $wpdb;
        $session_id = $session_manager->get_session_id_from_cookie();
     
    }

    function cm_handle_add_to_cart($cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data) {
        global $session_manager, $wpdb, $woocommerce;
    
        error_log('cm_handle_add_to_cart called for product ID: ' . $product_id);
        if ($session_manager->is_session_specific_user()) {
            $session_key = $session_manager->get_session_key_from_cookie();
            $table_name = $wpdb->prefix . 'cm_cart_data';

            $session_id = $session_manager->get_session_id_by_key($session_key); // Retrieves session ID using session key
    
            // Fetch the session ID for the current session key
            // $session_id = $wpdb->get_var($wpdb->prepare(
            //     "SELECT session_id FROM {$wpdb->prefix}cm_sessions WHERE session_key = %s",
            //     $session_key
            // ));
    
            if (!$session_id) {
                error_log('No session ID found, exiting cm_handle_add_to_cart');
                return; // Exit if no session ID is found
            }
    
            // Fetch the existing cart data from the database
            $existing_cart_data_serialized = $wpdb->get_var($wpdb->prepare(
                "SELECT cart_data FROM $table_name WHERE session_id = %d",
                $session_id
            ));
    
            // Deserialize the existing cart data, if any
            $cart_data = $existing_cart_data_serialized ? unserialize($existing_cart_data_serialized) : [];
    
            // Construct the new cart item to add
            $new_cart_item = array(
                'product_id' => $product_id,
                'quantity' => $quantity,
                'variation_id' => $variation_id,
                'variation' => $variation,
                'cart_item_data' => $cart_item_data
            );
    
            // Add the new item to the cart data array
            // Note: You might need a unique key for each item or handle merging items with the same product_id and variations
            $cart_data[] = $new_cart_item;
    
            // Serialize the updated cart data
            $updated_cart_data_serialized = serialize($cart_data);
    
            // Update or insert the cart data back into the database
            if ($existing_cart_data_serialized) {
                // If cart data exists, update
               $result =  $wpdb->update(
                    $table_name,
                    array('cart_data' => $updated_cart_data_serialized, 'updated_at' => current_time('mysql', 1)),
                    array('session_id' => $session_id),
                    array('%s', '%s'),
                    array('%d')
                );
            } else {
                // If no cart data exists, insert
                $result = $wpdb->insert(
                    $table_name,
                    array(
                        'session_id' => $session_id,
                        'cart_data' => $updated_cart_data_serialized,
                        'created_at' => current_time('mysql', 1),
                        'updated_at' => current_time('mysql', 1)
                    ),
                    array('%d', '%s', '%s', '%s')
                );
            }
    
            error_log('Cart data processed for session-specific user.');

            if (false === $result) {
                error_log('DB FAILED. Unable to insert/update cart data for session-specific user.');
            } else {
                // WC()->cart->calculate_totals();
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
    
    


    function cm_checkout_create_order($order, $data) {
        global $session_manager;
        if ($session_manager->is_session_specific_user()) {
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


    function cm_filter_cart_contents() {
        global $session_manager;
        error_log("Getting customer cart contents");
        if ($session_manager->is_session_specific_user()) {
            global $woocommerce, $wpdb;
            $session_key = $this->session_manager->get_session_key_from_cookie();
            error_log("Session key for Cart: " . $session_key);
    
            $session_id = $this->session_manager->get_session_id_by_key($session_key);
            error_log("Session ID for cart: " . $session_id);
    
            if (!$session_id) {
                error_log("No session ID found, clearing cart");
               // $woocommerce->cart->empty_cart();
                return;
            } else {
                $table_name = $wpdb->prefix . 'cm_cart_data';
                $session_cart_data_serialized = $wpdb->get_var($wpdb->prepare("SELECT cart_data FROM $table_name WHERE session_id = %d", $session_id));
    
                if(is_serialized($session_cart_data_serialized)){

                    if (!empty($session_cart_data_serialized)) {
                        error_log("Found serialized cart data, repopulating cart");
                        $woocommerce->cart->empty_cart(); // Consider the implications of clearing the cart here
                        
                        $session_cart_data = unserialize($session_cart_data_serialized);
                        foreach ($session_cart_data as $item_key => $item_value) {
                            error_log('Adding to Cart.....');
                            $woocommerce->cart->add_to_cart($item_key, $item_value['quantity']);
                            
                            error_log('Added! Item  : ' .$item_key);
                        }
                    } else {
                        error_log("No cart data found for session ID: " . $session_id);
                    }
                }else{
                    error_log(" not serialized ");
                }
               
            }  

        }else{
            error_log(" Not a Session Specific user , cant load cart");
        }
    }
    

    public function load_cart_data_for_session_specific_user() {
        global $woocommerce, $wpdb,$session_manager;       
        $session_id = $session_manager->get_session_id_from_cookie();
    
        error_log(" load_cart_data_for_session_specific_user  session id : " . $session_id);
        if ($session_id) {
            $table_name = $wpdb->prefix . 'cm_cart_data';
            $cart_data_serialized = $wpdb->get_var($wpdb->prepare(
                "SELECT cart_data FROM $table_name WHERE session_id = %d",
                $session_id
            ));
    
            if ($cart_data_serialized) {
                $cart_data = unserialize($cart_data_serialized);
                if (is_array($cart_data)) {
                    $woocommerce->cart->empty_cart(true);
                    foreach ($cart_data as $cart_item) {
                        $woocommerce->cart->add_to_cart(
                            $cart_item['product_id'],
                            $cart_item['quantity'],
                            $cart_item['variation_id'],
                            $cart_item['variation'],
                            $cart_item['cart_item_data']
                        );
                    }
                }
            }
        }else{
            error_log(" load_cart_data_for_session_specific_user :  No session id found ");
        }
    }
    
    
}

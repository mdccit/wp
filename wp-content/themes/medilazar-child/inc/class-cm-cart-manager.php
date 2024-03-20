<?php

namespace CM;


class Cart_Manager {

    private $session_manager;
    public function __construct($session_manager) {
        $this->session_manager = $session_manager;
        // add_action('woocommerce_loaded', array($this, 'initialize_cart_handling'));
        // add_action('woocommerce_load_cart_from_session', array($this, 'load_cart_for_session_user'));
        add_action('woocommerce_before_cart', array($this, 'load_cart_data_for_session_specific_user'));
        // // add_action('woocommerce_before_cart', array($this, 'cm_filter_cart_contents'));
        add_action('woocommerce_add_to_cart', array($this, 'cm_handle_add_to_cart'), 10, 6);  
        // add_action('woocommerce_load_cart_from_session', array($this, 'load_cart_from_session')); 
        // add_filter('woocommerce_add_to_cart_validation', array($this,'cm_custom_add_to_cart'), 10, 6);  
        // add_action('woocommerce_checkout_create_order', array($this, 'checkout_create_order'), 10, 2);
        // add_action('wp_logout', 'handle_user_logout');
        // add_action('woocommerce_before_remove_from_cart', array($this,'cm_remove_cart_item'), 10, 2);
        // add_action('woocommerce_cart_updated', array($this,'cm_cart_updated'));            
     
    }

    public function initialize_cart_handling() {
        add_action('woocommerce_before_calculate_totals', array($this, 'cm_filter_cart_contents'));
    }

    
    public function cm_handle_add_to_cart($cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data) {
        global $session_manager, $wpdb, $woocommerce;
    
        error_log('cm_handle_add_to_cart called for product ID: ' . $product_id);
        if ($session_manager->is_session_specific_user()) {
            $session_key = $session_manager->get_session_key_from_cookie();
            $table_name = $wpdb->prefix . 'cm_cart_data';
            $session_id = $session_manager->get_current_session_id(); // Retrieves session ID using session key
            $user_id = get_current_user_id();
      
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
                        'user_id' => $user_id,
                        'cart_data' => $updated_cart_data_serialized,
                        'created_at' => current_time('mysql', 1),
                        'updated_at' => current_time('mysql', 1)
                    ),
                    array('%d', '%s', '%s', '%s')
                );
            }
    
            error_log('Cart data processed for session-specific user. SESSION ID : '. $session_id);

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
    

    public function cm_validation_add_to_cart($valid, $product_id, $quantity, $variation_id = '', $variations= '', $cart_item_data = array()) {
        global $session_manager;
        if ($session_manager->is_session_specific_user()) {
            // Custom add to cart logic that stores cart item in custom session-based storage
            return false; // Return false to prevent the default add to cart behavior
        }
        return $valid;
    }

    public function load_cart_data_for_session_specific_user() {
        global $woocommerce, $wpdb,$session_manager;      
      
        if ($session_manager->is_session_specific_user()) {
            $session_id = $session_manager->get_session_id_from_cookie();
            $user_id = get_current_user_id();
            error_log(" load_cart_data_for_session_specific_user  session id : " . $session_id);

                if ($session_id) {
                    $table_name = $wpdb->prefix . 'cm_cart_data';
                    $cart_data_serialized = $wpdb->get_var($wpdb->prepare(
                        "SELECT cart_data FROM $table_name WHERE session_id = %d AND user_id = %d",
                        $session_id,
                        $user_id
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
        }else{
              error_log(" NOT A SESSION SPECIFIC USER");
        }
    }
    

    public function populate_woocommerce_cart($cart_data) {
        if (!is_array($cart_data) || empty($cart_data)) {
            return;
        }

        WC()->cart->empty_cart();

        foreach ($cart_data as $cart_item) {
            $product_id = isset($cart_item['product_id']) ? $cart_item['product_id'] : 0;
            $product = wc_get_product($product_id);
    
            if (!$product) {
                error_log("Product with ID $product_id does not exist.");
                continue; // Skip adding this product to the cart
            }
    
            $quantity = isset($cart_item['quantity']) ? $cart_item['quantity'] : 1;
            $variation_id = isset($cart_item['variation_id']) ? $cart_item['variation_id'] : 0;
            $variations = isset($cart_item['variations']) ? $cart_item['variations'] : array();
            $cart_item_data = isset($cart_item['cart_item_data']) ? $cart_item['cart_item_data'] : array();
    
            // Add the item to WooCommerce's cart
            WC()->cart->add_to_cart($product_id, $quantity, $variation_id, $variations, $cart_item_data);
        }
        
        WC()->cart->calculate_totals();
    }

    
}

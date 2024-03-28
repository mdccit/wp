<?php

namespace CM;


class Cart_Manager {

    private $session_manager;
    public function __construct($session_manager) {
        $this->session_manager = $session_manager;
        add_action('woocommerce_load_cart_from_session', array($this, 'set_cart_data_for_session_specific_user'));
        add_action('woocommerce_add_to_cart', array($this, 'cm_handle_add_to_cart'), 10, 6);     
         
    }
  
    
    
    public function cm_handle_add_to_cart($cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data) {
        global $session_manager, $wpdb;
        if ($session_manager->is_session_specific_user()) {
            $table_name = $wpdb->prefix . 'cm_cart_data';
            $session_id = $session_manager->get_current_session_id(); // Retrieves session ID using session key
            $user_id = get_current_user_id();
      
            if (!$session_id) {      
                return; // Exit if no session ID is found
            }
    
            // Fetch the existing cart data from the database
            $existing_cart_data_serialized = $wpdb->get_var($wpdb->prepare(
                "SELECT cart_data FROM $table_name WHERE session_id = %d",
                $session_id
            ));
    
            // Deserialize the existing cart data, if any
            $cart_data = $existing_cart_data_serialized ? unserialize($existing_cart_data_serialized) : [];

            // Flag to check if product already exists in the cart
            $product_exists = false;

            foreach ($cart_data as &$item) {
                // Check if product ID match
                if ($item['product_id'] == $product_id) {
                    // Product exists, so update the quantity
                    $item['quantity'] += 1;
                    $product_exists = true;
                    break; 
                }
            }

    
            if (!$product_exists) {
                // Construct the new cart item to add
                $new_cart_item = array(
                    'product_id' => $product_id,
                    'quantity' => $quantity,
                    'variation_id' => $variation_id,
                    'variation' => $variation,
                    'cart_item_data' => $cart_item_data
                );
                
                // Add the new item to the cart data array
                $cart_data[] = $new_cart_item;
            }
            
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
    

    public function set_cart_data_for_session_specific_user() {
        global $woocommerce, $wpdb,$session_manager;      
      
        if ($session_manager->is_session_specific_user()) {
            $session_id = $session_manager->get_session_id_from_cookie();
            $user_id = get_current_user_id();

                if ($session_id) {

                    $table_name = $wpdb->prefix . 'cm_cart_data';
                    $cart_data_serialized = $wpdb->get_var($wpdb->prepare(
                        "SELECT cart_data FROM $table_name WHERE session_id = %d AND user_id = %d",
                        $session_id,
                        $user_id
                    ));
                      
                    if ($cart_data_serialized !== false && $cart_data_serialized !== null && $cart_data_serialized !== '') {

                           $cart_data = maybe_unserialize($cart_data_serialized);

                        if (is_array($cart_data)) {
                            WC()->session->set('cart', $cart_data);
                        }
                    }else{
                         $woocommerce->cart->empty_cart(true);
                         
                    }
                }else{
                    error_log(" set_cart_data_for_session_specific_user :  No session id found ");
                }
        }else{
              error_log(" NOT A SESSION SPECIFIC USER");
        }
    }


    public function load_cart_data_for_session_specific_user() {

        static $already_run = false;

        if ($already_run) {
            return; // Exit if this function has already run
        }
        $already_run = true; // Mark as run
        global $woocommerce, $wpdb,$session_manager;      
      
        if ($session_manager->is_session_specific_user()) {
            $session_id = $session_manager->get_session_id_from_cookie();
            $user_id = get_current_user_id();
          

                if ($session_id) {

                
                    $table_name = $wpdb->prefix . 'cm_cart_data';
                    $cart_data_serialized = $wpdb->get_var($wpdb->prepare(
                        "SELECT cart_data FROM $table_name WHERE session_id = %d AND user_id = %d",
                        $session_id,
                        $user_id
                    ));
               

                    if ($cart_data_serialized !== false && $cart_data_serialized !== null && $cart_data_serialized !== '') {

                  
                        $cart_data = maybe_unserialize($cart_data_serialized);

                        if (is_array($cart_data)) {
                          
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
                    }else{
                           $woocommerce->cart->empty_cart(true);
                      
                    }
                }else{
                    error_log(" set_cart_data_for_session_specific_user :  No session id found ");
                }
        }else{
              error_log(" CANT LOAD DATA , NOT A SESSION SPECIFIC USER");
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

    public function get_cart_data_for_session($session_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'cm_cart_data';
        $serialized_cart_data = $wpdb->get_var($wpdb->prepare(
            "SELECT cart_data FROM {$table_name} WHERE session_id = %d",
            $session_id
        ));
        if (!empty($serialized_cart_data)) {
            return maybe_unserialize($serialized_cart_data);
        }
        return false;
    }

    public function calculate_cart_total_for_session($session_id) {
        global $session_manager;
        $current_session_id = $session_manager->get_session_id_from_cookie();
        if ($session_manager->is_session_specific_user()) {
            if($session_id == $current_session_id){
                $cart_data = $this->get_cart_data_for_session($session_id);
                $total = 0;
                if (is_array($cart_data)) {
                    foreach ($cart_data as $item) {
                        $product = wc_get_product($item['product_id']);
                        if ($product) {
                            $total += $product->get_price() * $item['quantity'];
                        }
                    }
                }

                // $total = WC()->cart->get_cart_total();
                $formatted_cart_total = wc_price($total);
                return $formatted_cart_total;
            }
        }
      
    }
     
    function cm_handle_remove_from_cart($product_id) {
        global $session_manager, $wpdb;

         
        // Check if the user is a session-specific user
        if (isset($_COOKIE['cm_session_key'])) {
            $session_id = $session_manager->get_current_session_id();
            $session_specific_user = $session_manager->is_session_specific_user();
            
            if ($session_specific_user) {
                $table_name = $wpdb->prefix . 'cm_cart_data';
                
                // Fetch the serialized cart data for the session
                $serialized_cart_data = $wpdb->get_var($wpdb->prepare(
                    "SELECT cart_data FROM {$table_name} WHERE session_id = %s",
                    $session_id
                ));
                
                if ($serialized_cart_data) {
                    $cart_data = maybe_unserialize($serialized_cart_data);
                    $updated_cart_data = array();
                    
                    // Assume cart_data is an array of items; adjust according to actual structure
                    foreach ($cart_data as $item) {
                        // If the item's product_id matches the one to remove, skip adding it to updated_cart_data
                        if (isset($item['product_id']) && $item['product_id'] == $product_id) {
                            continue;
                        }
                        $updated_cart_data[] = $item;
                    }
                    
                    // Update the cart_data in the database with the modified array
                    $wpdb->update(
                        $table_name,
                        array('cart_data' => maybe_serialize($updated_cart_data)), // New cart data
                        array('session_id' => $session_id), // Where clause
                        array('%s'), // New cart data format
                        array('%s')  // Where format
                    );
                }

                // Proceed to remove the item from WooCommerce cart
                foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                    if ($cart_item['product_id'] == $product_id) {
                        WC()->cart->remove_cart_item($cart_item_key);
                    }
                }
            }
        }
    }

    function cm_handle_update_cart_item_quantity($product_id, $new_quantity) {
        global $session_manager, $wpdb;
    
        if (isset($_COOKIE['cm_session_key'])) {
            $session_id = $session_manager->get_current_session_id();
            $session_specific_user = $session_manager->is_session_specific_user();
            $user_id = get_current_user_id();
            
            if ($session_specific_user) {
                $table_name = $wpdb->prefix . 'cm_cart_data';
            
                // Fetch the serialized cart data for the session
                $serialized_cart_data = $wpdb->get_var($wpdb->prepare(
                    "SELECT cart_data FROM {$table_name} WHERE session_id = %s",
                    $session_id
                ));
                
                if ($serialized_cart_data) {
                    $cart_data = maybe_unserialize($serialized_cart_data);
                    $updated_cart_data = array();
                                
                // Update quantity in the serialized data
                foreach ($cart_data as &$item) {
                    if (isset($item['product_id']) && $item['product_id'] == $product_id) {
                        $item['quantity'] = $new_quantity; // Update the quantity
                        }
                        $updated_cart_data[] = $item;
                    }
    
                // Update the cart_data in the database with the modified array
                    $wpdb->update(
                        $table_name,
                        array('cart_data' => maybe_serialize($updated_cart_data)),
                        array('session_id' => $session_id),
                        array('%s'),
                        array('%s')
                    );
                } 
 
                // Update quantity in the WooCommerce cart
                $found_cart_item_key = $this->find_cart_item_key_by_product_id($product_id);
                if ($found_cart_item_key) {
                    WC()->cart->set_quantity($found_cart_item_key, $new_quantity, true);
                }
            }
        }
    }

    function cm_handle_update_cart_item_quantity_product_page($product_id, $new_quantity) {
        global $session_manager, $wpdb;
    
        if (isset($_COOKIE['cm_session_key'])) {
            $session_id = $session_manager->get_current_session_id();
            $session_specific_user = $session_manager->is_session_specific_user();
            $user_id = get_current_user_id();
            
            if ($session_specific_user) {
                $table_name = $wpdb->prefix . 'cm_cart_data';
    
                // Fetch the serialized cart data for the session
                $serialized_cart_data = $wpdb->get_var($wpdb->prepare(
                    "SELECT cart_data FROM {$table_name} WHERE session_id = %s",
                    $session_id
                ));
    
                $cart_data = $serialized_cart_data ? maybe_unserialize($serialized_cart_data) : array();
                $product_exists_in_cart = false;
                
                // Check if the product exists in the cart and update its quantity
                foreach ($cart_data as &$item) {
                    if (isset($item['product_id']) && (int) $item['product_id'] === (int) $product_id) {
                        // Add the new quantity to the current quantity instead of just updating it
                        $item['quantity'] += $new_quantity; // Increment the quantity
                        $product_exists_in_cart = true; // Mark that the product exists
                        if (!isset($item['variation_id'])) { // Ensure variation_id exists
                            $item['variation_id'] = 0;
                        }
                        if (!isset($item['variation'])) { // Ensure variation exists
                            $item['variation'] = array();
                        }
                        break; // Stop the loop once the product is found and updated
                    }
                }        
            
                // If the product doesn't exist in the cart, add it
                if (!$product_exists_in_cart) {
                    $cart_data[] = array(
                        'product_id' => $product_id,
                        'quantity' => $new_quantity, 
                        'variation_id' => 0, // Add this line
                        'variation' => array(),
                    );
                }

                $updated_cart_data_serialized = maybe_serialize($cart_data);
    
                // Update or insert the cart_data in the database
                if ($serialized_cart_data) {
                    // There's existing cart data, so update it
                    $wpdb->update(
                        $table_name,
                        array('cart_data' => $updated_cart_data_serialized),
                        array('session_id' => $session_id),
                        array('%s'),
                        array('%s')
                    );
                } else {
                    // No existing cart data, perform an insert
                    $wpdb->insert(
                        $table_name,
                        array(
                            'session_id' => $session_id,
                            'user_id' => $user_id,
                            'cart_data' => $updated_cart_data_serialized,
                            'created_at' => current_time('mysql', 1),
                            'updated_at' => current_time('mysql', 1)
                        ),
                        array('%s', '%d', '%s', '%s', '%s') // Ensure correct placeholders
                    );
                }
            }
        }
    }
    
    function find_cart_item_key_by_product_id($product_id) {
        foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
            if ($cart_item['product_id'] == $product_id || $cart_item['variation_id'] == $product_id) {
                return $cart_item_key;
            }
        }
        return false;
    }
 

    
}

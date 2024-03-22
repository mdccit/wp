<?php

namespace CM;


class Cart_Manager {

    private $session_manager;
    public function __construct($session_manager) {
        $this->session_manager = $session_manager;
        // add_action('woocommerce_loaded', array($this, 'initialize_cart_handling'));
        add_action('woocommerce_load_cart_from_session', array($this, 'set_cart_data_for_session_specific_user'));
        // add_action('woocommerce_before_cart', array($this, 'load_cart_data_for_session_specific_user'));
        // // add_action('woocommerce_before_cart', array($this, 'cm_filter_cart_contents'));
        add_action('woocommerce_add_to_cart', array($this, 'cm_handle_add_to_cart'), 10, 6);  
        // add_action('woocommerce_load_cart_from_session', array($this, 'load_cart_from_session')); 
        // add_filter('woocommerce_add_to_cart_validation', array($this,'cm_custom_add_to_cart'), 10, 6);  
        // add_action('woocommerce_checkout_create_order', array($this, 'checkout_create_order'), 10, 2);
        // add_action('wp_logout', 'handle_user_logout');
        // add_action('woocommerce_before_remove_from_cart', array($this,'cm_remove_cart_item'), 10, 2);
        // add_action('woocommerce_cart_updated', array($this,'cm_cart_updated'));  
        //add_action('woocommerce_cart_loaded_from_session', array($this,'update_session_cart_total'), 100);     
        // add_action('woocommerce_before_calculate_totals', array($this,'conditionally_remove_specific_cart_item'));
        // add_action('wp_ajax_cm_ajax_remove_product_from_cart',  array($this,'initialize_cart_handling'));
        // add_action('wp_ajax_nopriv_cm_ajax_remove_product_from_cart',  array($this,'initialize_cart_handling'));
        // add_action('wp_enqueue_scripts', array($this,'enqueue_my_custom_script'));
     
     
    }

    // public function initialize_cart_handling() {
    //     error_log('AJAX request received');
    //     check_ajax_referer('nonce-name-here', '_wpnonce');

    //     error_log(print_r($_POST, true));
    //     // The rest of your logic...
    // }
    
    

    
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
            error_log(" set_cart_data_for_session_specific_user  session id : " . $session_id);

                if ($session_id) {

                    error_log(" set_cart_data_for_session_specific_user  s : " . $session_id);
                    $table_name = $wpdb->prefix . 'cm_cart_data';
                    $cart_data_serialized = $wpdb->get_var($wpdb->prepare(
                        "SELECT cart_data FROM $table_name WHERE session_id = %d AND user_id = %d",
                        $session_id,
                        $user_id
                    ));
                    error_log(" set_cart_data_for_session_specific_user  cart_data_serialized: " .$cart_data_serialized);
                  
                    if ($cart_data_serialized !== false && $cart_data_serialized !== null && $cart_data_serialized !== '') {

                        error_log(" set_cart_data_for_session_specific_user   cart data serizlied s: " . $session_id);

                        error_log('Serialized cart data: ' . var_export($cart_data_serialized, true));
                        $cart_data = maybe_unserialize($cart_data_serialized);
                        error_log('Unserialized cart data: ' . var_export($cart_data, true));

                        if (is_array($cart_data)) {
                            error_log('Before adding the cart. Cart contents count: ' . WC()->cart->get_cart_contents_count());

                            WC()->session->set('cart', $cart_data);

       
                            // foreach ($cart_data as $cart_item) {
                            //     error_log('Attempting to add item to cart. Product ID: ' . $cart_item['product_id']);
                            //     $woocommerce->cart->add_to_cart(
                            //         $cart_item['product_id'],
                            //         $cart_item['quantity'],
                            //         $cart_item['variation_id'],
                            //         $cart_item['variation'],
                            //         $cart_item['cart_item_data']
                            //     );
                            // }

                            error_log('After adding the cart. Cart contents count: ' . WC()->cart->get_cart_contents_count());
                        }
                    }else{
                        error_log('Before emptying the cart. Cart contents count: ' . WC()->cart->get_cart_contents_count());
                        $woocommerce->cart->empty_cart(true);
                        error_log('After emptying the cart. Cart contents count: ' . WC()->cart->get_cart_contents_count());
                        
                        error_log(' No cart data found '. $session_id);
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
            error_log(" set_cart_data_for_session_specific_user  session id : " . $session_id);

                if ($session_id) {

                    error_log(" set_cart_data_for_session_specific_user  s : " . $session_id);
                    $table_name = $wpdb->prefix . 'cm_cart_data';
                    $cart_data_serialized = $wpdb->get_var($wpdb->prepare(
                        "SELECT cart_data FROM $table_name WHERE session_id = %d AND user_id = %d",
                        $session_id,
                        $user_id
                    ));
                    error_log(" set_cart_data_for_session_specific_user  cart_data_serialized: " .$cart_data_serialized);
                    error_log("Cart data serialized: " . var_export($cart_data_serialized, true));

                    if ($cart_data_serialized !== false && $cart_data_serialized !== null && $cart_data_serialized !== '') {

                        error_log(" set_cart_data_for_session_specific_user   cart data serizlied s: " . $session_id);

                        $cart_data = maybe_unserialize($cart_data_serialized);

                        if (is_array($cart_data)) {
                            error_log('Before adding the cart. Cart contents count: ' . WC()->cart->get_cart_contents_count());
       
                            foreach ($cart_data as $cart_item) {
                                error_log('Attempting to add item to cart. Product ID: ' . $cart_item['product_id']);
                                $woocommerce->cart->add_to_cart(
                                    $cart_item['product_id'],
                                    $cart_item['quantity'],
                                    $cart_item['variation_id'],
                                    $cart_item['variation'],
                                    $cart_item['cart_item_data']
                                );
                            }

                            error_log('After adding the cart. Cart contents count: ' . WC()->cart->get_cart_contents_count());
                        }
                    }else{
                        error_log('Before emptying the cart. Cart contents count: ' . WC()->cart->get_cart_contents_count());
                        $woocommerce->cart->empty_cart(true);
                        error_log('After emptying the cart. Cart contents count: ' . WC()->cart->get_cart_contents_count());
                        
                        error_log(' No cart data found '. $session_id);
                    }
                }else{
                    error_log(" set_cart_data_for_session_specific_user :  No session id found ");
                }
        }else{
              error_log(" NOT A SESSION SPECIFIC USER");
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
        return $total;
    }
       

    function update_session_cart_total() {
        global $session_manager;

        if($session_manager->is_session_specific_user() === true){
            // Assuming you have a way to get the current session ID
            $session_id = $session_manager->get_current_session_id(); // Define this function based on your session management
            $session_total = $this->calculate_cart_total_for_session($session_id);

            error_log(' SESSION TOTSL FOR SESSION ID : ' .$session_total);
            
            // Here you could update a session variable, a custom field, or output directly as needed
            WC()->session->set('session_cart_total', $session_total);
        }
    
    }


    function cm_ajax_remove_product_from_cart() {
        error_log('Removing Product : ');
        $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
        error_log('Removed Product : '. $product_id);
        $session_key = isset($_POST['cm_session_key']) ? sanitize_text_field($_POST['cm_session_key']) : '';
        
        if ($product_id > 0 && !empty($session_key)) {
            $this->cm_handle_remove_from_cart($product_id);
            wp_send_json_success('Product removed');
        } else {
            wp_send_json_error('Missing data');
        }
    }


    function cm_handle_remove_from_cart($product_id) {
        global $session_manager, $wpdb;

        // if (is_admin() && !defined('DOING_AJAX')) return;
        
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

    
}

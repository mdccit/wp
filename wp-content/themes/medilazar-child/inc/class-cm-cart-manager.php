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
                // Generate a unique cart item key
            $unique_cart_item_key = md5(uniqid($product_id . '_' . $variation_id . '_', true));

            // Flag to check if product already exists in the cart
            $product_exists = false;

            foreach ($cart_data as &$item) {
                // Check if product ID match
                if ($item['product_id'] == $product_id) {
                    // Product exists, so update the quantity
                    $item['quantity'] += $quantity;
                    $product_exists = true;
                    break; 
                }
            }

    
            if (!$product_exists) {
                // Construct the new cart item to add
                $new_cart_item = array(
                    'cart_item_key' => $unique_cart_item_key,
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

    public function get_cart_total_for_session_order($session_id) {
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

                return $total;
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
                // foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                //     if ($cart_item['product_id'] == $product_id) {
                //         WC()->cart->remove_cart_item($cart_item_key);
                //     }
                // }
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
     


    //PUNCHOUT ORDER

    function generate_punchout_order_message_cxml() {
        global $wpdb, $session_manager;
        $table_name = $wpdb->prefix . 'cm_cart_data'; // Adjust according to your table structure
    
        $session_id = $session_manager->get_current_session_id();
        // Retrieve the serialized cart data for a given session ID
        $serialized_cart_data = $wpdb->get_var($wpdb->prepare(
            "SELECT cart_data FROM $table_name WHERE session_id = %s",
            $session_id
        ));
    
        // Unserialize the cart data
        $cart_items = unserialize($serialized_cart_data);
    
        // Initialize the cXML items string
        $cxmlItems = '';
    
        // Array to store all UNSPSC codes
        $allCodes = [];
    
        foreach ($cart_items as $item) {
            $product = wc_get_product($item['product_id']);
            if (!$product) continue; 
    
            $categories = wp_get_post_terms($item['product_id'], 'product_cat', array('fields' => 'names'));
            if (!is_wp_error($categories)) {
                if (!empty($categories)) {
                    error_log('Categories: ' . implode(', ', $categories));
                } else {
                    error_log('No categories found for product ID: ' . $item['product_id']);
                }
            } else {
                error_log('Error retrieving categories: ' . $categories->get_error_message());
            }
    
            // Get UNSPSC codes for the product
            $unspsc_codes = $this->get_unspsc_codes($item['product_id']);
    
            // Collect all UNSPSC codes into an array
            foreach ($unspsc_codes as $unspsc_code) {
                $allCodes[] = esc_html($unspsc_code);
            }

               // Remove duplicate UNSPSC codes
        $uniqueCodes = array_unique($allCodes);
    
            // Construct cXML for each cart item
            $cxmlItems .= "<ItemIn quantity=\"" . esc_attr($item['quantity']) . "\">";
            $cxmlItems .= "<ItemID><SupplierPartID>" . esc_html($product->get_sku()) . "</SupplierPartID>";
            $cxmlItems .= "<SupplierPartAuxiliaryID>" . esc_html($product->get_sku()) . "</SupplierPartAuxiliaryID></ItemID>";
            $cxmlItems .= "<ItemDetail>";
            $cxmlItems .= "<UnitPrice><Money currency=\"EUR\">" . esc_html($product->get_price()) . "</Money></UnitPrice>";
            $cxmlItems .= "<Description xml:lang=\"es-ES\">" . esc_html($product->get_name()) . "</Description>";
            $cxmlItems .= "<UnitOfMeasure>UNIT</UnitOfMeasure>";
            $cxmlItems .= "<Classification domain=\"SPSC\"></Classification>";
            $cxmlItems .= "<Classification domain=\"UNSPSC\">" . implode(', ', $uniqueCodes) . "</Classification>";
            $cxmlItems .= "</ItemDetail>";
            $cxmlItems .= "</ItemIn>";
        }
    
        return $cxmlItems;
    }

    function get_unspsc_codes($product_id) {
        $taxonomy = 'product_cat';
        $terms = wp_get_post_terms($product_id, $taxonomy, array('fields' => 'all'));
    
        if (is_wp_error($terms) || empty($terms)) {
            return []; // Return an empty array if there are no terms or an error occurred
        }
    
        $hierarchy = array();
    
        // Build the hierarchy for the given product's terms
        foreach ($terms as $term) {
            if (!isset($hierarchy[$term->term_id])) {
                $hierarchy[$term->term_id] = array('term' => $term, 'children' => array());
            }
        }
    
        foreach ($terms as $term) {
            if ($term->parent != 0) {
                if (isset($hierarchy[$term->parent])) {
                    $hierarchy[$term->parent]['children'][] = $term;
                } else {
                    $hierarchy[$term->parent] = array('term' => null, 'children' => array($term));
                }
            }
        }
    
        $codes = [];
        
        // Process the hierarchy to generate UNSPSC codes
        foreach ($hierarchy as $parent) {
            if (!empty($parent['term']) && empty($parent['children'])) {
                // Case 1: Categories which don't have children
                $codes[] = $parent['term']->name; 
            } elseif (!empty($parent['term']) && !empty($parent['children'])) {
                // Case 2: Categories with children as 'category name - subcategory name'
                foreach ($parent['children'] as $child) {
                    $unspsc_code_with_child = $parent['term']->name . '-' . $child->name;
                    $codes[] = $unspsc_code_with_child;
                }
            } elseif (empty($parent['term']) && !empty($parent['children'])) {
                // Case 3: Subcategories which don't have a parent category
                foreach ($parent['children'] as $child) {
                    $is_orphan = true;
                    foreach ($hierarchy as $potential_parent) {
                        if (!empty($potential_parent['term']) && $child->parent == $potential_parent['term']->term_id) {
                            $is_orphan = false;
                            break;
                        }
                    }
                    if ($is_orphan) {
                        $codes[] = $child->name;
                    }
                }
            }
        }
    
        // Return unique and relevant UNSPSC codes
        return array_unique($codes);
    }
    
    
    // function get_unspsc_codes($product_id) {
    //     $taxonomy = 'product_cat';
    //     $terms = wp_get_post_terms($product_id, $taxonomy, array('fields' => 'all'));
    //     $hierarchy = array();
    
    //     // Initial setup for all terms, assume they might be top-level unless found otherwise
    //     foreach ($terms as $term) {
    //         if (!isset($hierarchy[$term->term_id])) {
    //             $hierarchy[$term->term_id] = array('term' => $term, 'children' => array());
    //         }
    //     }
    
    //     // Organize terms into hierarchy
    //     foreach ($terms as $term) {
    //         if ($term->parent != 0) {
    //             if (isset($hierarchy[$term->parent])) {
    //                 $hierarchy[$term->parent]['children'][] = $term;
    //             } else {
    //                 // This part handles cases where the parent is not assigned directly
    //                 $hierarchy[$term->parent] = array('term' => null, 'children' => array($term));
    //             }
    //         }
    //     }
    
    //     // Build separate UNSPSC codes for each top-level category
    //     $codes = [];
    //     foreach ($hierarchy as $parent) {
    //         if (!empty($parent['term']) && empty($parent['children'])) {
    //             // Parent term exists and has no children
    //             $codes[] = $parent['term']->name; // Include parent-only code
    //         } elseif (!empty($parent['term']) && !empty($parent['children'])) {
    //             // Parent term exists and has children, include only parent with subcategories
    //             foreach ($parent['children'] as $child) {
    //                 $unspsc_code_with_child = $parent['term']->name . '-' . $child->name;
    //                 $codes[] = $unspsc_code_with_child; // Include parent with children code
    //             }
    //         } elseif (empty($parent['term']) && !empty($parent['children'])) {
    //             // Handling orphaned sub-categories: include only subcategories without their own parent
    //             foreach ($parent['children'] as $child) {
    //                 $is_orphan = true;
    //                 foreach ($hierarchy as $potential_parent) {
    //                     if (!empty($potential_parent['term']) && $child->parent == $potential_parent['term']->term_id) {
    //                         $is_orphan = false;
    //                         break;
    //                     }
    //                 }
    //                 if ($is_orphan) {
    //                     $codes[] = $child->name; // Include orphaned sub-category code
    //                 }
    //             }
    //         }
    //     }
    
    //     // Filter out "category-sub category" codes if both parent and child exist standalone
    //     $filtered_codes = array_filter($codes, function($code) use ($codes) {
    //         if (strpos($code, '-') !== false) {
    //             list($parent, $child) = explode('-', $code);
    //             return !(in_array($parent, $codes) && in_array($child, $codes));
    //         }
    //         return true;
    //     });
    
    //     return array_unique($filtered_codes);
    // }
    
    function sendPunchOutOrder($cxmlData)
    {
        global $wpdb ,$session_manager;
        $table_name = $wpdb->prefix . 'cm_sessions';
    
        $session_id = $session_manager->get_session_id_from_cookie();
    
        // Retrieve the order_url for the given session_id from the database
        $order_url = $wpdb->get_var($wpdb->prepare(
            "SELECT order_url FROM $table_name WHERE session_id = %s",
            $session_id
        ));
    
        $test_order_url = "https://commercialmedica.requestcatcher.com/test";
    
        if (!$order_url) {
            wp_send_json_error('No order URL found for session_id: ' . $session_id);
            return false;
        }
    
        // Set up the request arguments
        $args = array(
            'body' => array('oracleCart' => $cxmlData),
            'timeout' => 45,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking' => true,
            'headers' => array(
                'Content-Type' => 'application/x-www-form-urlencoded; charset=ISO-8859-1'
            ),
            'cookies' => array()
        );

        // $base64EncodedCXML = base64_encode(utf8_encode($cxmlData));

        // // Prepare the HTTP request arguments
        // $args = array(
        //     'body' => 'oracleCart=' . $base64EncodedCXML,
        //     'timeout' => 45,
        //     'redirection' => 5,
        //     'httpversion' => '1.0',
        //     'blocking' => true,
        //     'headers' => array(
        //         'Content-Type' => 'application/octet-stream',
        //         'Content-Encoding' => 'identity'
        //     ),
        //     'cookies' => array()
        // );
        
        // Send the POST request
        $response = wp_remote_post($order_url, $args);
    
    
        // Check if the request was successful
        if (is_wp_error($response)) {
            // Handle error
            $error_message = $response->get_error_message();        
            wp_send_json_error( "Failed to send PunchOutOrderMessage: $error_message");
            return false;
        } else {
            // Handle success
         
            return true;
        }
    }
    
    function insert_order_request_to_db($order_data, $order_id, $order_type, $type, $sender_identity, $order_date) {

        global $wpdb;
        $table_name = $wpdb->prefix . 'cm_order_requests';
    
        // Serialize or prepare your order data as needed
        $serialized_order_data = maybe_serialize($order_data);
    
        $wpdb->insert(
            $table_name,
            array(
                'order_date' => $order_date,
                'order_data' => $serialized_order_data, 
                'order_id' => $order_id,
                'order_type' => $order_type,
                'type' => $type,
                'sender_identity' => $sender_identity,
                'created_at' => current_time('mysql', 1), // UTC time
            )
        );
    
        // Optional: Return the inserted record's ID
        return $wpdb->insert_id;
    }


    function set_order_address_from_cxml($addressElement) {
        $streets = [];
        foreach ($addressElement->PostalAddress->Street as $street) {
            $streets[] = (string)$street;
        }
        
        // Combine street address lines
        $address_1 = isset($streets[0]) ? $streets[0].',' : '';
        $address_2 = isset($streets[1]) ? $streets[1].',' : '';
        $address_3 = isset($streets[1]) ? $streets[2] : '';
    
        if (count($streets) > 2) {
            $address_2 .= ' ' . implode(', ', array_slice($streets, 2));
        }

        $deliver_to = isset($addressElement->PostalAddress->DeliverTo) ? (string)$addressElement->PostalAddress->DeliverTo : '';
        $name = (string)$addressElement->Name;
        $full_name = $deliver_to ? ($name . "\n" . ' Entregar a : '. $deliver_to) : $name;

        // Combine DeliverTo and Name
        $phoneNumber = (string)$addressElement->Phone->TelephoneNumber->Number;       
    
        $Address = [
            'first_name' => $full_name,
            'address_1' => $address_1,
            'address_2' => $address_2,
            'address_3' => $address_3,
            'city' => (string)$addressElement->PostalAddress->City,
            'state' => (string)$addressElement->PostalAddress->State,
            'postcode' => (string)$addressElement->PostalAddress->PostalCode,
            'country' => (string)$addressElement->PostalAddress->Country->isoCountryCode,
            'email' => (string)$addressElement->Email,
            'phone' => $phoneNumber
        ];
        
        return $Address;
    }
    
}

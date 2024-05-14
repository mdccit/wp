<?php
require_once get_stylesheet_directory() . '/inc/class-cm-session-manager.php';
require_once get_stylesheet_directory() . '/inc/class-cm-cart-manager.php';
require_once get_stylesheet_directory() . '/inc/class-cm-wc-gateway-manual.php';
require_once get_stylesheet_directory() . '/inc/class-cm-order-manager.php';


$session_manager = new \CM\Session_Manager();
$cart_manager = new \CM\Cart_Manager($session_manager);
$order_manager = new \CM\Order_Manager($session_manager);

// Register AJAX action for logged-in users
add_action('wp_ajax_cm_ajax_remove_product_from_cart', 'cm_ajax_remove_product_from_cart');
// Register AJAX action for logged-out users
add_action('wp_ajax_nopriv_cm_ajax_remove_product_from_cart', 'cm_ajax_remove_product_from_cart');

// Register AJAX action for logged-in users
add_action('wp_ajax_cm_ajax_update_product_from_cart', 'cm_ajax_update_product_from_cart');
add_action('wp_ajax_nopriv_cm_ajax_update_product_from_cart', 'cm_ajax_update_product_from_cart');

add_action('wp_ajax_cm_ajax_update_product_from_product_page', 'handle_cm_update_product_from_product_page');
add_action('wp_ajax_nopriv_cm_ajax_update_product_from_product_page', 'handle_cm_update_product_from_product_page');

add_action('wp_ajax_get_mini_cart_total_for_session', 'handle_get_mini_cart_total_for_session');
add_action('wp_ajax_nopriv_get_mini_cart_total_for_session', 'handle_get_mini_cart_total_for_session');

add_filter('woocommerce_cart_subtotal', function($cart_subtotal, $compound, $instance) {
        global $cart_manager, $session_manager;  
        // Retrieve the cart total for the session ID
        $current_session_id = $session_manager->get_session_id_from_cookie();
        $session_specific_user = $session_manager->is_session_specific_user();
     
        if($session_specific_user){          
            if($current_session_id){
                $cart_subtotal = $cart_manager->calculate_cart_total_for_session($current_session_id);
              
                return $cart_subtotal;    
            }        
        }

            return  $cart_subtotal;      
        
      
}, 10, 3);


function cm_add_body_class( $classes ) {
    global $session_manager;  

    // Retrieve the cart total for the session ID
    $current_session_id = $session_manager->get_session_id_from_cookie();
    if($current_session_id){
            $classes[] = 'cm-session-user';       
    }
    return $classes;
}
add_filter( 'body_class', 'cm_add_body_class' );

function cm_ajax_remove_product_from_cart() {
    
    global $cart_manager , $session_manager;
    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;

    $session_specific_user = $session_manager->is_session_specific_user();
    if($session_specific_user){
        if ($product_id > 0) {
            $cart_manager->cm_handle_remove_from_cart($product_id);
                        wp_send_json_success('Product removed');
        } else {
                        wp_send_json_error('Missing data');
        }
    }
}

function cm_ajax_update_product_from_cart() {
    global $cart_manager , $session_manager;

    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $quantity = isset($_POST['quantity']) ? (int) $_POST['quantity'] : 1;
    
    $session_specific_user = $session_manager->is_session_specific_user();
    if($session_specific_user){
        if ($product_id > 0) {
            $cart_manager->cm_handle_update_cart_item_quantity($product_id,$quantity);
            wp_send_json_success('Product Updated');
        } else {
            wp_send_json_error('Missing data for Update');
        }
    }
}

function handle_cm_update_product_from_product_page() {

    global $cart_manager , $session_manager;
    check_ajax_referer('update_mini_cart_nonce', 'nonce'); // Check the nonce for security
    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $quantity = isset($_POST['quantity']) ? (int) $_POST['quantity'] : 1;
    $session_specific_user = $session_manager->is_session_specific_user();
    if($session_specific_user){
        if ($product_id > 0) {
           $cart_manager->cm_handle_update_cart_item_quantity_product_page($product_id,$quantity);
           wp_send_json_success(array('message' => __('El producto se ha añadido correctamente a la cesta!', 'medilazar')));
        } else {
            wp_send_json_error('Missing data for Update');
        }
    }
}

function handle_get_mini_cart_total_for_session() {
    global $cart_manager, $session_manager;
    check_ajax_referer('update_mini_cart_nonce', 'nonce'); // Check the nonce for security

    $session_specific_user = $session_manager->is_session_specific_user();
    if($session_specific_user){
        $cart_total = [];
        $session_id = isset($_POST['session_id']) ? sanitize_text_field($_POST['session_id']) : '';

        // Retrieve the cart total for the session ID
        $current_session_id = $session_manager->get_session_id_from_cookie();
    
                if($session_id == $current_session_id){
                    $cart_total = $cart_manager->calculate_cart_total_for_session($session_id);            
                    wp_send_json_success(array('total' => $cart_total ));
                }else {
                    wp_send_json_error('Session ID Mismatch');
                }          
    }
    wp_die();
}

function enqueue_and_localize_cm_script() {

    global  $session_manager;  

    $session_specific_user = $session_manager->is_session_specific_user();
    if($session_specific_user){
        wp_enqueue_script('custom-session-total', get_stylesheet_directory_uri() . '/js/custom-session-total.js', array('jquery'), null, true);
        // Enqueue js-cookie
        wp_enqueue_script('js-cookie', get_template_directory_uri() . '/js/js.cookie.min.js', array(), '3.0.1', true);
        wp_localize_script('custom-session-total', 'myAjax', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('update_mini_cart_nonce'),
            'addedToCart' => __('Product successfully added to cart!', 'woocommerce'),
        ));      
    }
}

add_action('wp_enqueue_scripts', 'enqueue_and_localize_cm_script');

/**
 * Enqueue script and styles for child theme
 */
function cm_child_enqueue_styles_and_scripts()
{
	wp_enqueue_style('child-styles', get_stylesheet_directory_uri() . '/style.css', false, time(), 'all');
	wp_enqueue_style('cm-elementor-styles', get_stylesheet_directory_uri() . '/css/cm-elementor.css', false, time(), 'all');
	wp_enqueue_script("si_script", get_stylesheet_directory_uri() . "/js/custom.js", '', time());
    wp_localize_script('custom', 'cmAjax', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('update_guest_cart_nonce'),
    ));
}
add_action('wp_enqueue_scripts', 'cm_child_enqueue_styles_and_scripts', 110000);

/**
 * Add category column to Woocommerce order details page in admin
 */
function cm_action_woocommerce_admin_order_item_headers()
{ ?>
	<th class="item sortable" colspan="2" data-sort="string-ins"><?php _e('Categoria', 'woocommerce'); ?></th>
<?php
};

function cm_action_woocommerce_admin_order_item_values($_product, $item, $item_id)
{ ?>
	<td class="name" colspan="2">
		<?php
		$category_names = [];
		if ($_product) {
			$termsp = get_the_terms($_product->get_id(), 'product_cat');
			if (!empty($termsp)) {
				foreach ($termsp as $term) {
					$_categoryid = $term->term_id;
					if ($term = get_term_by('id', $_categoryid, 'product_cat')) {
						$category_names[] = $term->name;
					}
				}
			}
		}
		echo implode(', ', $category_names)
		?>
	</td>
<?php
};

add_action('woocommerce_admin_order_item_values', 'cm_action_woocommerce_admin_order_item_values', 10, 3);
add_action('woocommerce_admin_order_item_headers', 'cm_action_woocommerce_admin_order_item_headers', 10, 0);


/**
 * Override osf_single_product_quantity_label with 
 */
remove_action('woocommerce_before_add_to_cart_quantity', 'osf_single_product_quantity_label', 10);
add_action('woocommerce_before_add_to_cart_quantity', 'cm_single_product_quantity_label', 10);
function cm_single_product_quantity_label() {
	global $product;
	$min_value = apply_filters('woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product);
	$max_value = apply_filters('woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product);
	if ($max_value && $min_value !== $max_value) {
	echo '<label class="quantity_label">' . __('Cantidad:', 'medilazar') . ' </label>';
	}
}

/**
 * Change add to cart button text on single page
 */
function cm_woocommerce_add_to_cart_button_text_single() {
    return __( 'Añadir a la cesta', 'woocommerce' ); 
}
add_filter( 'woocommerce_product_single_add_to_cart_text', 'cm_woocommerce_add_to_cart_button_text_single' ); 



/*/////////////////////////////////////////////////////////////////
/////////////  Punchout XML Processing   ///////////////////////
*/////////////////////////////////////////////////////////////////


/**
* CM Session Table Creation Define Versioning 
**/
define('CM_SESSION_TABLE_VERSION', '1.4');
define('CM_SESSION_TABLE_VERSION_OPTION', 'cm_session_table_version');


/**
 * Creates the cm_sessions table in the database if it doesn't exist or updates it if the version has changed.
 *
 * This function checks if the cm_sessions table exists in the database. If it does not, or if the
 * version of the table has changed, it creates or updates the table accordingly. The table is used to
 * store session information for users, including the session ID, user ID, session key, session email,
 * creation time, and expiration time. The user ID is a foreign key that references the ID in the users table.
 *
 */
function create_cm_session_table() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $table_name = $wpdb->prefix . 'cm_sessions';
    
    // Define current version
    define('CURRENT_CM_SESSION_TABLE_VERSION', '1.8'); // Update this as you release new versions

    // Check if the table already exists
    $table_exists = $wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") == $table_name;

    // Retrieve the currently installed version of the table, if any
    $installed_ver = get_option(CM_SESSION_TABLE_VERSION_OPTION);

    // Proceed if the table does not exist or if the version has changed
    if (!$table_exists || $installed_ver != CURRENT_CM_SESSION_TABLE_VERSION) {
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
          session_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
          user_id BIGINT UNSIGNED NOT NULL,
          session_key VARCHAR(255) NOT NULL,
          session_email VARCHAR(255) NOT NULL,
          created_at DATETIME NOT NULL,
          expires_at DATETIME NOT NULL,
          FOREIGN KEY (user_id) REFERENCES {$wpdb->prefix}users(ID) ON DELETE CASCADE
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        // Update version and apply changes based on version
        if ($installed_ver < CURRENT_CM_SESSION_TABLE_VERSION) {
            $wpdb->query("ALTER TABLE $table_name ADD COLUMN session_status VARCHAR(255) NULL;");    
            $wpdb->query("ALTER TABLE $table_name ADD COLUMN buyer_cookie VARCHAR(255) NULL;");      
            $wpdb->query("ALTER TABLE $table_name ADD COLUMN payload_id VARCHAR(255) NULL;");
            $wpdb->query("ALTER TABLE $table_name ADD COLUMN operation VARCHAR(50) NULL;");
        }

        if ($installed_ver < CURRENT_CM_SESSION_TABLE_VERSION) {    
            $wpdb->query("ALTER TABLE $table_name ADD COLUMN `from` VARCHAR(255)  NULL;"); 
            $wpdb->query("ALTER TABLE $table_name ADD COLUMN `to` VARCHAR(255)  NULL;"); 
            $wpdb->query("ALTER TABLE $table_name ADD COLUMN request LONGTEXT NULL;");
            $wpdb->query("ALTER TABLE $table_name ADD COLUMN order_url LONGTEXT NULL;");
        }
        // For each new version, add additional conditional blocks here.

        // Update the version in the database to the current version after all updates
        update_option(CM_SESSION_TABLE_VERSION_OPTION, CURRENT_CM_SESSION_TABLE_VERSION);
    }
}


// setup the cm_sesisons table
add_action('after_setup_theme', 'create_cm_session_table');

/**
 * Registers a custom REST API route for punchout login.
 *
 * Adds a new route to the WordPress REST API under the 'comercialmedica/v1' namespace. The route
 * '/punchout_login' accepts POST requests and uses the 'handle_xml_request' function as
 * its callback to process the request.
 */

add_action('rest_api_init', function () {
    register_rest_route('comercialmedica/v1', '/punchout_login', array(
        'methods' => 'POST',
        'callback' => 'handle_punchout_login_request',
    ));
});


/**
 * Handles XML requests for user login.
 *
 * Processes an XML request containing login information, authenticates the user, and
 * generates a session key if successful. The function returns an XML response with
 * the result of the login attempt, including a login URL with the session key and
 * additional parameters if authentication is successful.
 * 
 * for xml loginResponse  with application/type x-www-url-encoded
 * for xcml  PunchOutSetupRequest  with application/type text/xml
 *
 * @param WP_REST_Request $request The request object containing the XML data.
 * @return WP_REST_Response The XML response with the login result.
 */

 function handle_punchout_login_request(WP_REST_Request $request){

    // Determine the content type of the request
    $content_type = $request->get_content_type();

 
        
    if ($content_type && $content_type['value'] == 'application/x-www-form-urlencoded') {     

        if(empty($request->get_param('loginRequest'))){
            return  xml_error_response('E', ' No XML data provided');
        }
         
        // Handle XML request
        $xml_data = $request->get_param('loginRequest');
      
        
        libxml_use_internal_errors(true); // Use internal libxml errors
        $xml = simplexml_load_string($xml_data);
        
        // Check if XML is valid
        if (!$xml) {
            $errors = libxml_get_errors(); // Retrieve XML parse errors
            libxml_clear_errors(); // Clear libxml error buffer
            $errorMsg = "Invalid XML format. Errors: " . implode(', ', array_map(function($error) {
                return trim($error->message);
            }, $errors));
            return  xml_error_response('E',  $errorMsg);
        } else {
          // Process XML data
          return handle_xml_request($xml);
        }   
        
    } elseif ($content_type && $content_type['value'] == 'text/xml') {
        // Handle cXML request
        $body = $request->get_body();
  
        // Ensure cXML data is not null or empty before processing
        if (empty($body)) {
            return cxml_failure_response(400, 'No XML data provided.', '','');
        }
  
        libxml_use_internal_errors(true); // Use internal libxml errors to capture XML parse errors


        try {
            $cxml = simplexml_load_string($body);

            if ($cxml === false) {
                $errors = libxml_get_errors(); // Retrieve XML parse errors
                libxml_clear_errors(); // Clear libxml error buffer
                $errorMsg = "Invalid XML format. Errors: " . implode(', ', array_map(function($error) {
                    return trim($error->message);
                }, $errors));

                libxml_use_internal_errors(false);
                return cxml_failure_response(400, $errorMsg, '','');
               
            }elseif (!isset($cxml->Request->PunchOutSetupRequest)) {
                return cxml_failure_response(400, 'Missing PunchOutSetupRequest element.', '','');
               
            } else {
                // Process cXML data
                return handle_cxml_request($body);
            }
        }catch(Exception $e){
             // Catch any other exceptions and handle them
            error_log("An error occurred: " . $e->getMessage());
            return cxml_failure_response(400,$e->getMessage(), '','');
        }     
 
    }
  }
  


/**
 * Handle request based on XML data 
 *
 *
 */
function handle_xml_request($xml) {
    global $wpdb; // Access the WordPress DB

    $returnCode = 'U';
    $response_message = 'An unexpected error occurred.';
    $loginURL = '';

    try {

        $username = (string)$xml->header->login->username;
        $password = (string)$xml->header->login->password;
        $userEmail = (string)$xml->body->loginInfo->userInfo->userContactInfo->userEmail; 
		$returnURL = (string)$xml->body->loginInfo->returnURL; 

        if (is_null($username) || is_null($password)) {
            $returnCode = 'E';
            $response_message = 'Username or password missing.';
        } elseif (!is_email($userEmail)) {
            $returnCode = 'E';
            $response_message = 'Invalid email address.';
        }elseif (empty($username) || empty($password)) { 
            $returnCode = 'A';
            $response_message = 'Username or password missing.';
        } elseif (empty($returnURL)) {
            $returnCode = 'E';
            $response_message = 'Return URL is missing.';
        } else {

			// Check if the user exists
			if (!username_exists($username)) {
				$returnCode = 'A';
				$response_message = 'User does not exist.';
			} else {
				$user = wp_authenticate($username, $password);

				if (!is_wp_error($user)) {
					wp_set_current_user($user->ID);
					wp_set_auth_cookie($user->ID);
	
					$returnCode = 'S';
	
					// Generate a unique session key
					$session_key = wp_generate_password(20, false);
	
					// Insert the session key and userEmail into the wp_cm_sessions table
				    $wpdb->insert(
						$wpdb->prefix . 'cm_sessions', 
						[
							'user_id' => $user->ID,
							'session_key' => $session_key,
							'session_email' => $userEmail, 
							'created_at' => current_time('mysql'),
							'expires_at' => date('Y-m-d H:i:s', time() + 60 * 60 * 24)
						],
						[
							'%d', // user_id
							'%s', // session_key
							'%s', // session_email
							'%s', // created_at
							'%s'  // expires_at
						]
					);
	
					// Construct the login URL with the WordPress site's URL, session key, userEmail, and additional parameters
					$loginURL = add_query_arg(array(
						'sessionKey' => $session_key, 
						'userEmail' => $userEmail
					), home_url());
	
					$response_message = ''; // No message needed for success
				} else {
					$returnCode = 'A';
					$response_message = 'Authentication Failure';
				}
			}
        
        }
    } catch (Exception $e) {
        $response_message = $e->getMessage();
    }

    // Prepare response data
    $response_data = [
        'returnCode' => $returnCode,
        'message' => $response_message,
        'loginURL' => $loginURL,
    ];

    // Generate and return the XML response
    $response_xml = generate_xml_response($response_data);
   // return new WP_REST_Response($response_xml, 200, ['Content-Type' => 'application/xml']);
   header('Content-Type: application/xml; charset=utf-8');
   echo $response_xml;
   return;
}


/**
 * Generates an XML response from an array of response data.
 *
 * Creates an XML document with a specified structure based on the provided response data.
 * The XML document includes a header with a version attribute and a return element with
 * a returnCode attribute. Depending on the returnCode, the body of the response may include
 * a loginURL element with a CDATA section containing the URL.
 *
 */

function generate_xml_response($response_data) {
    $dom = new DOMDocument('1.0', 'UTF-8');
    $dom->formatOutput = true;

    $response = $dom->createElement('response');
    $dom->appendChild($response);

    $header = $dom->createElement('header');
    $header->setAttribute('version', '1.0');
    $response->appendChild($header);

    $return = $dom->createElement('return');
    $return->setAttribute('returnCode', $response_data['returnCode']);
    $header->appendChild($return);

    // Handling success differently
    if ($response_data['returnCode'] === 'S') {
        if (!empty($response_data['loginURL'])) {
            $body = $dom->createElement('body');
            $response->appendChild($body);

            $loginURL = $dom->createElement('loginURL');
            $cdata = $dom->createCDATASection($response_data['loginURL']);
            $loginURL->appendChild($cdata);
            $body->appendChild($loginURL);
        }
    } else {
        // Include returnMessage for non-success codes
        if (!empty($response_data['message'])) {
            $returnMessage = $dom->createElement('returnMessage');
            $cdata = $dom->createCDATASection($response_data['message']);
            $returnMessage->appendChild($cdata);
            $return->appendChild($returnMessage);
        }

        // Ensure an empty loginURL is added to the body for non-success responses
        $body = $dom->createElement('body');
        $response->appendChild($body);
        $loginURL = $dom->createElement('loginURL');
        $body->appendChild($loginURL);
    }

    return $dom->saveXML();
}

/**
 * Handle request based on cXML data 
 *
 *
 */

function handle_cxml_request($cxml_body) {
    global $wpdb; // Access the WordPress DB

    // Initialize response parameters
    $returnCode = 'Failure';
    $response_message = 'An unexpected error occurred.';
    $loginURL = '';
    $requestBody = file_get_contents('php://input');


    try {
    
        // Load the cXML string as an object
        $cxml = new SimpleXMLElement($cxml_body);

        // Extract necessary information from the cXML
        $username = (string)$cxml->Header->Sender->Credential->Identity;
        $password = (string)$cxml->Header->Sender->Credential->SharedSecret;
        $userEmail = (string)$cxml->Request->PunchOutSetupRequest->Contact->Email;
        $returnURL = (string)$cxml->Request->PunchOutSetupRequest->BrowserFormPost->URL;
        $payloadID = (string)$cxml['payloadID'];
        $buyerCookie = (string)$cxml->Request->PunchOutSetupRequest->BuyerCookie;
        $operation = (string)$cxml->Request->PunchOutSetupRequest->attributes()->operation;
        $from = (string)$cxml->Header->From->Credential->Identity;
        $to = (string)$cxml->Header->To->Credential->Identity;
        $version =  (string)$cxml['version'];
        $language = (string) $cxml->attributes('xml', true)->lang;

        if (empty($returnURL)) {
            $returnCode = '400';
            $response_message = 'Return URL is missing.';
        } else{
                /// Check if the user exists
                if(!is_email($userEmail)){
                    $returnCode = '400';
                    $response_message = 'Invalid email address.';
                }elseif (is_null($username) || is_null($password)) {
                    $returnCode = '400';
                    $response_message = 'Username or password missing.';
                }elseif (empty($username) || empty($password)) { 
                    $returnCode = '400';
                    $response_message = 'Username or password missing.';
                } elseif(trim($username) === ''){
                    $returnCode = '400';
                    $response_message = 'username is empty.';
                }else if (!username_exists($username)) {
                    $returnCode = '400';
                    $response_message = 'User does not exist.';
                }elseif (empty($returnURL)) {
                    $returnCode = '400';
                    $response_message = 'Return URL is missing.';
                }
                 else {
                        $user = wp_authenticate($username, $password);

                            if (!is_wp_error($user)) {
                                wp_set_current_user($user->ID);
                                wp_set_auth_cookie($user->ID);

                                $returnCode = 'S';

                                // Generate a unique session key
                                $session_key = wp_generate_password(20, false);

                                // Insert the session key and userEmail into the wp_cm_sessions table
                                $wpdb->insert(
                                    $wpdb->prefix . 'cm_sessions', 
                                    [
                                        'user_id' => $user->ID,
                                        'session_key' => $session_key,
                                        'session_email' => $userEmail, 
                                        'created_at' => current_time('mysql'),
                                        'expires_at' => date('Y-m-d H:i:s', time() + 60 * 60 * 24),
                                        'buyer_cookie' => $buyerCookie,
                                        'payload_id' => $payloadID,
                                        'operation' => $operation,
                                        'from' => $from,
                                        'to' => $to,
                                        'request' => $requestBody, // Storing the whole cXML content
                                        'order_url' => $returnURL,
                                    ],
                                    [
                                        '%d', // user_id
                                        '%s', // session_key
                                        '%s', // session_email
                                        '%s', // created_at
                                        '%s', // expires_at
                                        '%s', // buyer_cookie
                                        '%s', // payload_id
                                        '%s',  // operation
                                        '%s',  // from
                                        '%s',  // to
                                        '%s',  // request
                                        '%s',  // order_url
                                    ]
                                );

                                // Construct the login URL with the WordPress site's URL, session key, userEmail, and if there are other additional parameters
                                $loginURL = add_query_arg(array(
                                    'sessionKey' => $session_key, 
                                    'userEmail' => $userEmail
                                ), home_url());

                                $response_message = ''; // No message needed for success
                            } else {
                                $returnCode = '401';
                                $response_message = 'Authentication Failure';
                            }
                        }

        }
        
    } catch (Exception $e) {
        $returnCode = '401';
        $response_message = 'Authentication Failure';
    }

    // Assuming you have a function to generate cXML responses
    $response_cxml = generate_cxml_response($returnCode, $response_message, html_entity_decode($loginURL),$payloadID, $language, $version);
    //return new WP_REST_Response($response_cxml, $returnCode, ['Content-Type' => 'text/xml']);
    header('Content-Type: application/xml; charset=utf-8');
	echo $response_cxml;
	return;
}

/**
 * Generates an cXML response from an array of response data.
 *
 * Creates an cXML document with a specified structure based on the provided response data.
 *
 */
function generate_cxml_response($returnCode, $response_message, $loginURL, $payloadIDFromRequest,$language = 'en-US',$version = '1.1.007') {

    $implementation = new DOMImplementation();
    $doctype = $implementation->createDocumentType('cXML', '', 'http://xml.cxml.org/schemas/cXML/1.1.010/cXML.dtd');
    $dom = $implementation->createDocument(null, '', $doctype);
    $dom->encoding = 'UTF-8';

    // Create the root cXML element
    $cxml = $dom->createElement('cXML');
    $dom->appendChild($cxml);

    $cxml->setAttribute('version', $version);
    $cxml->setAttribute('xml:lang', $language);
    $cxml->setAttribute('payloadID', $payloadIDFromRequest);
    $cxml->setAttribute('timestamp', date('c'));

    // Create and append the Response element
    $response = $dom->createElement('Response');
    $cxml->appendChild($response);

    // Set the Status element based on the returnCode
    $status = $dom->createElement('Status');
    $response->appendChild($status);
    $status->setAttribute('code', $returnCode === 'S' ? '200' : $returnCode);
    $status->setAttribute('text', $returnCode === 'S' ? 'OK' : $response_message);

    if ($returnCode === 'S') {
        // Include PunchOutSetupResponse for successful connections
        $punchOutSetupResponse = $dom->createElement('PunchOutSetupResponse');
        $response->appendChild($punchOutSetupResponse);

        $startPage = $dom->createElement('StartPage');
        $punchOutSetupResponse->appendChild($startPage);

        // Insert the login URL
        $urlElement = $dom->createElement('URL');
        $startPage->appendChild($urlElement);
        $cdata = $dom->createCDATASection($loginURL);
        $urlElement->appendChild($cdata);
    } 

    // Return the XML string
    return $dom->saveXML();
}

function cxml_failure_response($returnCode, $response_message, $loginURL, $payloadIDFromRequest){
    $response_cxml = generate_cxml_response($returnCode, $response_message, html_entity_decode($loginURL),$payloadIDFromRequest);
   // return new WP_REST_Response($response_cxml, $returnCode, ['Content-Type' => 'application/xml']);
    header('Content-Type: application/xml; charset=utf-8');
	echo $response_cxml;
	return;
}

function xml_error_response($returnCode, $response_message){
    $response_data = [
        'returnCode' => $returnCode,
        'message' => $response_message,
        'loginURL' => '',
    ];


    $response_xml = generate_xml_response($response_data);
   // return new WP_REST_Response($response_xml, 200, ['Content-Type' => 'application/xml']);
   header('Content-Type: application/xml; charset=utf-8');
   echo $response_xml;
   return;
}

/**
 * Logs in a user based on session key and email passed via URL parameters.
 *
 * Checks for 'sessionKey' and 'userEmail' GET parameters, validates them, and logs in the
 * corresponding user if the session is valid. If the session is invalid, the user is logged out.
 * After the login or logout action, the user is redirected to the home page.
 */
function cm_login_user_with_url_session_key() {
    global $session_manager, $wpdb;
    if (!isset($_GET['sessionKey']) && !isset($_GET['userEmail'])) {
        return;
    }

    $session_key = sanitize_text_field($_GET['sessionKey']);
    $session_email = sanitize_email($_GET['userEmail']);
    $user_id = $session_manager->validate_session_key($session_key, $session_email);

    if ($user_id) {
        // Generate a dynamic IV for each encryption
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));

        // Encrypt the session key with dynamic IV
        $encrypted_session_key = openssl_encrypt($session_key, 'aes-256-cbc', ENCRYPTION_KEY, 0, $iv);

        if ($encrypted_session_key === false) {
            throw new Exception('Encryption failed');
        }

        // Prepend the IV to the encrypted session key and base64-encode the entire string
        $encrypted_session_key_with_iv = base64_encode($iv . $encrypted_session_key);

        $expires_at = $session_manager->get_cm_session_expires_at($session_key);
        $expires_at_timestamp = strtotime($expires_at);
        $current_time = time();
        $expiration_period = $expires_at_timestamp - $current_time;

        if ($expiration_period > 0) {
            // The session key is valid, and we have a user ID, so log the user in
            wp_set_current_user($user_id);
            wp_set_auth_cookie($user_id);

            // Set the session cookie
            $set_cookie = $session_manager->set_cm_session_cookie($encrypted_session_key_with_iv, $expiration_period);
            
            // Check if the session ID has been used to add items in wp_cm_cart_data
            if($set_cookie === true) {
                $session_id = $session_manager->get_current_session_id(); // Ensure this function exists and correctly retrieves the session ID
                $session_manager->set_cm_session_id_cookie($session_id, $expiration_period);
                $session_manager->set_cm_session_email_cookie($session_email, $expiration_period);

                if($session_id) {
                    global $cart_manager;
                    $table_name = $wpdb->prefix . 'cm_cart_data';
                    $cart_data_exists = $wpdb->get_var($wpdb->prepare(
                        "SELECT COUNT(*) FROM $table_name WHERE session_id = %d",
                        $session_id
                    ));
                    
                    // If no cart data exists for this session ID, empty the cart
                    if ($cart_data_exists == 0) {
                        error_log(" Cart Data Clear : ". $session_id ."");
                    }else{
                        error_log(" Loading Cart Data on login : ". $session_id ."");
                        $cart_manager->set_cart_data_for_session_specific_user();
                        error_log(" Cart Loaded on login : ". $session_id ."");
                    }

                    // Redirect to the homepage on Login Success
                    wp_redirect(home_url());
                    exit;
                }else{
                    error_log("No Session ID Returned");
                }
            }
        } else {
            wp_logout();
            // Redirect to the WordPress main URL
            wp_redirect(home_url());
            exit;
        }
    } else {
        wp_logout();
        // Redirect to the WordPress main URL
        wp_redirect(home_url());
        exit;
    }
}

add_action('wp_loaded', 'cm_login_user_with_url_session_key');

// add_action('init', 'cm_login_user_with_url_session_key');

function load_user_cart_on_login( $user_login, $user ) {
    $session_key = get_user_meta($user->ID, 'cm_session_key', true);
    if ( ! empty( $session_key ) ) {
         error_log(' WP LOGIN :  SESSION KEY'. $session_key .'');
    }else{
        error_log(' WP LOGIN :  ERROR ');
    }
}
add_action('wp_login', 'load_user_cart_on_login', 10, 2);


/**
 * Adds custom error messages to the login page.
 *
 * Appends custom error messages to the default login message based on the 'login_error' GET parameter.
 * 
 * Currently handles 'invalid_session'
 * @return string The modified login message with custom error messages appended.
 */
function cm_login_error_message($message) {
    if (isset($_GET['login_error'])) {
        $error_code = sanitize_text_field($_GET['login_error']);
        if ('invalid_session' === $error_code) {
            $message .= '<div class="error"><p>Invalid session key. Please try again.</p></div>';
        } 
    }
    return $message;
}

add_filter('login_message', 'cm_login_error_message');


/* ////////////////////////////////////////////
*             MULTIPLE CART FUNCTIOALITY
*/ ////////////////////////////////////////////


define('CM_CART_DATA_TABLE_VERSION', '1.3');
define('CM_CART_DATA_TABLE_VERSION_OPTION', 'cm_cart_data_table_version');

/**
 * Creates or updates the cm_cart_data table.
 *
 * This function checks the currently installed version of the cm_cart_data table against 
 * a defined constant for the desired table version (CM_CART_DATA_TABLE_VERSION). If the installed
 * version is different from the desired version, or if the table does not exist, the function 
 * will create or update the table to match the structure defined within the function.
 *
 * The cm_cart_data table stores cart data associated with specific sessions, allowing for
 * multiple carts functionality. It includes fields for a unique cart identifier (cart_id),
 * a reference to the session ID (session_id), cart data in a longtext format (cart_data),
 * and timestamps for creation and last update.
 *
 * Upon successful creation or update, the function updates a WordPress option to the current
 * version of the table structure, ensuring that the check and update process only runs when necessary.
 *
 */
function create_cm_cart_data_table() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $table_name = $wpdb->prefix . 'cm_cart_data';

    // Check the current installed version
    $installed_ver = get_option(CM_CART_DATA_TABLE_VERSION_OPTION);

    if ($installed_ver != CM_CART_DATA_TABLE_VERSION) {
        // SQL to create or update the table
        $sql = "CREATE TABLE $table_name (
          cart_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
          session_id BIGINT UNSIGNED NOT NULL,
          cart_data LONGTEXT NOT NULL,
          created_at DATETIME NOT NULL,
          updated_at DATETIME NOT NULL,
          FOREIGN KEY (session_id) REFERENCES {$wpdb->prefix}cm_sessions(session_id) ON DELETE CASCADE
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

            // Update version and apply changes based on version
            if ($installed_ver < CURRENT_CM_SESSION_TABLE_VERSION) {
                $wpdb->query("ALTER TABLE $table_name ADD COLUMN user_id BIGINT UNSIGNED NOT NULL;");
            }

        // Update the version in the database
        update_option(CM_CART_DATA_TABLE_VERSION_OPTION, CM_CART_DATA_TABLE_VERSION);
    }
}

// create or update cart_data table after theme loads
add_action('init', 'create_cm_cart_data_table');


// Implement similar hooks for cart item removal, cart updates, etc.

add_action('wp_logout', 'clear_cm_session_key_cookie');

function clear_cm_session_key_cookie() {
    // Check if the cookie exists
    if (isset($_COOKIE['cm_session_key'])) {
        // Clear the cookie by setting its expiration time to the past
        setcookie('cm_session_key', '', time() - 3600, '/');
    }

    if (isset($_COOKIE['cm_session_id'])) {
        // Clear the cookie by setting its expiration time to the past
        setcookie('cm_session_id', '', time() - 3600, '/');
    }

    if (isset($_COOKIE['cm_session_email'])) {
        // Clear the cookie by setting its expiration time to the past
        setcookie('cm_session_email', '', time() - 3600, '/');
    }
}

function handle_update_cart_item_quantity() {
    check_ajax_referer('nonce-name-here', 'nonce'); // Verify nonce for security

    $product_id = isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0;
    $quantity = isset($_POST['quantity']) ? (int) $_POST['quantity'] : 1;

    if ($product_id > 0 && $quantity > 0) {
        // Update the cart item quantity logic
        WC()->cart->set_quantity($product_id, $quantity, $refresh_totals = true);

        wp_send_json_success('Quantity updated');
    } else {
        wp_send_json_error('Error updating quantity');
    }

    wp_die(); // Make sure to stop execution
}


add_filter('woocommerce_mini_cart_total', 'cm_mini_cart_total');

function cm_mini_cart_total($value) {
    global $cart_manager, $session_manager;

    $session_specific_user = $session_manager->is_session_specific_user();
     
    if ($session_specific_user) {     
        $session_id = $session_manager->get_session_id_from_cookie();
     
        // Assuming you have a function to get the total based on session ID
        $session_total = $cart_manager->calculate_cart_total_for_session($session_id);
        if ($session_total) {
            return wc_price($session_total);
        }
    }

    // Return the original total if not a session-specific user
    return $value;
}


add_action( 'woocommerce_account_dashboard', 'custom_dashboard_message_with_email' );

function custom_dashboard_message_with_email() {
    if ( isset( $_COOKIE['cm_session_email'] ) ) {
        $session_email = sanitize_email( $_COOKIE['cm_session_email'] );
        echo '<h3>' .__( 'Detalles de la cuenta' , 'medilazar' ). '</h3>';
        echo '<p>' .__( 'Correo electrónico' , 'medilazar' ). ' : ' . $session_email . '</p>';
    }
}

// Different checkout button for the session specific user
add_action('init', 'cm_punchout_proceed_to_checkout');

function cm_punchout_proceed_to_checkout() {
    global $session_manager;
    $session_specific_user = $session_manager->is_session_specific_user();

    if($session_specific_user) {
        remove_action( 'woocommerce_proceed_to_checkout', 'woocommerce_button_proceed_to_checkout', 20 );
        add_action( 'woocommerce_proceed_to_checkout', 'cm_punchout_button_proceed_to_checkout', 20 );
    }
}


function cm_punchout_button_proceed_to_checkout() {
    wc_get_template( 'cart/cm-punchout-proceed-to-checkout-button.php' );
}


// PUNCHOUT ORDER MESSAGE
function get_complete_punchout_order_cxml() {
    global $wpdb, $session_manager, $cart_manager;
    $table_name = $wpdb->prefix . 'cm_sessions';
    $session_id = $session_manager->get_session_id_from_cookie();

    $session_details = $wpdb->get_row($wpdb->prepare(
        "SELECT `from`, `to`, buyer_cookie, payload_id FROM $table_name WHERE session_id = %s",
        $session_id
    ));

    if (is_null($session_details)) {
        return ['error' => 'No session found'];
    }

    $cart_total = $cart_manager->get_cart_total_for_session_order($session_id);
    $fromIdentity = esc_html($session_details->to);
    $toIdentity = esc_html($session_details->from);
    $buyerCookie = esc_html($session_details->buyer_cookie);
    $payloadID = esc_html($session_details->payload_id);
    $currentDateTime = date('Y-m-d\TH:i:s');
    $cartItemsCxml = $cart_manager->generate_punchout_order_message_cxml(); // Ensure this method returns valid cXML string

    $cxml = '<?xml version="1.0" encoding="UTF-8"?>' .
            '<cXML payloadID="' . $payloadID . '" timestamp="' . $currentDateTime . '">' .
            "<Header>" .
            "<From><Credential domain=\"DUNS\"><Identity>$fromIdentity</Identity></Credential></From>" .
            "<To><Credential domain=\"DUNS\"><Identity>$toIdentity</Identity></Credential></To>" .
            "<Sender><Credential domain=\"DUNS\"><Identity>$fromIdentity</Identity></Credential><UserAgent/></Sender>" .
            "</Header>" .
            '<Message>' .
            '<PunchOutOrderMessage>' .
            '<BuyerCookie>' . $buyerCookie . '</BuyerCookie>' .
            '<PunchOutOrderMessageHeader operationAllowed="create">' .
            '<Total>' .
            '<Money currency="EUR">' . $cart_total . '</Money>' .
            '</Total>' .
            '</PunchOutOrderMessageHeader>' .
            $cartItemsCxml .
            '</PunchOutOrderMessage>' .
            '</Message>' .
            '</cXML>';

    $order_url = $wpdb->get_var($wpdb->prepare(
        "SELECT order_url FROM $table_name WHERE session_id = %s",
        $session_id
    ));

    if (!$cxml) {
        return ['error' => 'Failed to generate cXML data'];
    }

    // Update the session status before logging out
    $wpdb->update(
        $table_name,
        ['session_status' => 'order_created'],
        ['session_id' => $session_id]
    );

    setcookie('cm_session_key', '', time() - 3600, '/');
    setcookie('cm_session_id', '', time() - 3600, '/');
    wp_logout();

    return ['cxmlData' => $cxml, 'orderUrl' => $order_url];
}


function get_punchout_return_url() {
    global $wpdb, $session_manager;
    $table_name = $wpdb->prefix . 'cm_sessions';
    $session_id = $session_manager->get_session_id_from_cookie();

    $session_details = $wpdb->get_row($wpdb->prepare(
        "SELECT `from`, `to`, buyer_cookie, payload_id FROM $table_name WHERE session_id = %s",
        $session_id
    ));

    if (is_null($session_details)) {
        return ['error' => 'No session found'];
    }


    $order_url = $wpdb->get_var($wpdb->prepare(
        "SELECT order_url FROM $table_name WHERE session_id = %s",
        $session_id
    ));


    setcookie('cm_session_key', '', time() - 3600, '/');
    setcookie('cm_session_id', '', time() - 3600, '/');
    wp_logout();

    return [ 'orderUrl' => $order_url];
}

function delete_expired_cm_sessions() {

    error_log('running cron');
    global $wpdb;
    $table_name = $wpdb->prefix . 'cm_sessions';

    error_log('Cron the wp_cm_sessions table.');

    // Current date minus 7 days
    $date_threshold = date('Y-m-d H:i:s', strtotime('-1 days'));

    // SQL to delete old sessions not marked as 'order_created'
    $sql = $wpdb->prepare("DELETE FROM $table_name WHERE session_status <> %s AND expires_at < %s", 'order_created', $date_threshold);

    // Execute the query
    $result = $wpdb->query($sql);

    if ($result === false) {
        // Handle error; SQL query failed to execute
        error_log('Failed to delete expired sessions from the wp_cm_sessions table.');
    } else {
        // Success, output number of rows affected
        error_log('Deleted ' . $result . ' expired sessions from the wp_cm_sessions table.');
    }
}



// //// DELETING SESSION CART DATA
// if (!wp_next_scheduled('cm_session_delete_hook')) {
//     wp_schedule_event(time(), 'every_minute', 'cm_session_delete_hook');
// 
// // Function to delete expired CM sessions
// function delete_expired_cm_cart_data() {
//     error_log('Running cron: delete_expired_cm_sessions'); // Log execution start

//     global $wpdb;
//     $table_name = $wpdb->prefix . 'cm_sessions';

//     // Current date minus 7 days
//     $date_threshold = date('Y-m-d H:i:s', strtotime('-1 days'));

//     // SQL query to delete sessions not marked as 'order_created' and expired
//     $sql = $wpdb->prepare("DELETE FROM $table_name WHERE session_status <> %s AND expires_at < %s", 'order_created', $date_threshold);

//     // Execute the query
//     $result = $wpdb->query($sql);

//     if ($result === false) {
//         error_log('Failed to delete expired sessions from wp_cm_sessions table.');
//     } else {
//         error_log('Deleted ' . $result . ' expired sessions from wp_cm_sessions table.');
//     }
// }

// // Schedule the custom event if not already scheduled
// if (!wp_next_scheduled('cm_session_delete_hook')) {
//     wp_schedule_event(time(), 'every_minute', 'cm_session_delete_hook');
// }

// // Hook the function to the custom event
// add_action('cm_session_delete_hook', 'delete_expired_cm_sessions');


function add_custom_cron_interval($schedules) {
    $schedules['every_minute'] = array(
        'interval' => 60, // Interval in seconds
        'display'  => esc_html__('Every Minute'), // Display name for the interval
    );
    return $schedules;
}
add_filter('cron_schedules', 'add_custom_cron_interval');



// Return to ERP button for the session specific user
add_action('wp_footer', 'cm_render_punchout_return_button');

function cm_render_punchout_return_button() {

    if (is_cart()) {
        return;
    }
    global $session_manager;
    $session_specific_user = $session_manager->is_session_specific_user();

    if($session_specific_user) {
        // Ensure the URL points to where your process-punchout.php file is located relative to the site root.
        $punchout_page_url = get_stylesheet_directory_uri() . '/return-punchout.php'; // Adjust the path as needed.

        echo '<a href="' . esc_url($punchout_page_url) . '" id="punchout_return">' . __('Volver a ERP', 'medilazar') . '</a>';
    }
}


add_filter('woocommerce_package_rates', 'set_free_shipping_for_session_users', 10, 2);

function set_free_shipping_for_session_users($rates, $package) {
    global $session_manager;
    $session_specific_user = $session_manager->is_session_specific_user();

    if($session_specific_user) {
        foreach ($rates as $rate_key => $rate) {
            // Assuming 'free_shipping' is the method_id for your free shipping method.
            // Adjust this as necessary based on your setup.
            if ('free_shipping' !== $rate->method_id) {
                unset($rates[$rate_key]);
            }
        }
    }

    return $rates;
}



function create_cm_order_requests_table() {
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'cm_order_requests';
    $charset_collate = $wpdb->get_charset_collate();

    // Define the SQL for table creation/alteration
    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        order_data longtext NOT NULL,
        order_date varchar(255) DEFAULT '' NOT NULL,
        order_id varchar(255) DEFAULT '' NOT NULL,
        order_type varchar(255) DEFAULT '' NOT NULL,
        type varchar(255) DEFAULT '' NOT NULL,
        sender_identity varchar(255) DEFAULT '' NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    // Use dbDelta to create or alter the table as necessary
    dbDelta($sql);
}

add_action( 'after_setup_theme', 'create_cm_order_requests_table' );


function display_shipping_email_in_order_admin($order){
    $shipping_email = $order->get_meta('_shipping_email');
    if (!empty($shipping_email)) {
        echo '<p><strong>' . __('Email Address') . ':</strong> ' . esc_html($shipping_email) . '</p>';
    }
}
add_action('woocommerce_admin_order_data_after_shipping_address', 'display_shipping_email_in_order_admin', 10, 1);


// Add JavaScript to hide the billing email in the WooCommerce order edit screen based on a condition
add_action('admin_footer', 'conditionally_hide_billing_email_in_order_edit_js');

function conditionally_hide_billing_email_in_order_edit_js() {
    // Check if we're on the WooCommerce order edit screen
    $screen = get_current_screen();
    if ($screen && 'shop_order' === $screen->id) {
        // Get the current order ID
        $order_id = isset($_GET['post']) ? intval($_GET['post']) : 0;
        error_log('Order ID :'. $order_id);
        if ($order_id) {
            // Fetch the WooCommerce order object
            $order = wc_get_order($order_id);
       
            // Check for the custom meta value (adjust the meta key to match your implementation)
            if ($order->get_meta('_created_via_cxml')) {
                // Output JavaScript to hide the billing email
                echo '
                <script type="text/javascript">
                jQuery(document).ready(function($) {
                    // Select the email paragraph under billing details and hide it
                    var billingEmail = $(".order_data_column h3:contains(\'Billing\')").next(".address").find("p:contains(\'Email address:\')");
                    billingEmail.hide();
                });
                </script>
                ';
            }
        }
    }
}



function display_billing_email_in_order_admin($order){
    $shipping_email = $order->get_meta('_shipping_email');
    if (!empty($shipping_email)) {
        echo '<p><strong>' . __('Email Address') . ':</strong> ' . esc_html($shipping_email) . '</p>';
    }
}

add_action('woocommerce_admin_order_data_after_billing_address', 'display_billing_email_in_order_admin', 10, 1);




/**
 * Add the gateway to WooCommerce.
 */
function add_cm_wc_gateway_manual($methods) {
    $methods[] = 'CM_WC_Gateway_Manual'; // Your class name
    return $methods;
}
add_filter('woocommerce_payment_gateways', 'add_cm_wc_gateway_manual');


add_action('rest_api_init', function () {
    register_rest_route('comercialmedica/v1', '/punchout_order_request', array(
        'methods' => 'POST',
        'callback' => 'create_wc_order_from_cxml',
    ));
});


function format_address_from_cxml($address) {
    $name = (string)$address->Name;
    $streetLines = [];
    foreach ($address->Street as $street) {
        $streetLines[] = (string)$street;
    }
    $city = (string)$address->City;
    $state = (string)$address->State;
    $postalCode = (string)$address->PostalCode;
    $country = (string)$address->Country;

    return "$name, " . implode(", ", $streetLines) . ", $city, $state, $postalCode, $country";
}

function get_woocommerce_admin_email() {
    // Default to general admin email
    $admin_email = get_option('admin_email');
    return $admin_email;
}

// Create Manual Order From cXML


//Check permission
function cm_add_wc_gateway_manual($methods) {
    // if the user is an admin
    if (current_user_can('manage_options')) {
        $methods[] = 'CM_WC_Gateway_Manual';
    }

    return $methods;
}
add_filter('woocommerce_payment_gateways', 'cm_add_wc_gateway_manual');



add_filter('woocommerce_email_recipient_customer_processing_order', 'disable_cxml_customer_emails', 10, 2);
add_filter('woocommerce_email_recipient_customer_completed_order', 'disable_cxml_customer_emails', 10, 2);
add_filter('woocommerce_email_recipient_customer_on_hold_order', 'disable_cxml_customer_emails', 10, 2);

function disable_cxml_customer_emails($recipient, $order) {
    if (!$order) return $recipient; // Check if the order object exists
    if (is_a($order, 'WC_Order') && $order->get_meta('_created_via_cxml')) {
        return ''; // Return an empty string to stop the email for orders created via cXML
    }
    return $recipient;
}


// Custom function to identify orders created via cXML
// //Change the receipient of the emails
function change_new_order_email_recipient($recipient, $order) {
    // Ensure that you only modify the recipient for orders created via your cXML function
    if (is_a($order, 'WC_Order') && $order->get_meta('_created_via_cxml')) {
        $new_recipient = get_woocommerce_email_recipient();
        return $new_recipient;
    }
    return $recipient;
}
add_filter('woocommerce_email_recipient_new_order', 'change_new_order_email_recipient', 10, 2);


function get_woocommerce_email_recipient() {
    // Get all WooCommerce emails
    $mailer = WC()->mailer();
    $emails = $mailer->get_emails();

    // Loop through the emails to find 'New Order' email
    foreach ($emails as $email) {
        if ('WC_Email_New_Order' === get_class($email)) {
            // Output the recipient email for 'New Order'
            $recipient = $email->get_recipient();
            error_log('New Order email is sent to: ' . $recipient);
            return $recipient;
        }
    }
    return 'No recipient found';
}

add_action('init', 'get_woocommerce_email_recipient');


function create_wc_order_from_cxml(WP_REST_Request $request) {

    global $cart_manager, $order_manager;
    header('Content-Type: text/xml'); // Set correct content type for cXML response

    try{
        // Load the cXML content
        $cxml_content = $request->get_body();

        if (empty($cxml_content)) {
            return new WP_Error('cxml_error', 'No cXML content provided', array('status' => 400));
        }

        $cxml = simplexml_load_string($cxml_content);
        if (!$cxml) {
            return new WP_Error('cxml_error', 'Failed to parse cXML content', array('status' => 400));
        }

        if (!$cxml || !isset($cxml->Request->OrderRequest)) {
            wp_send_json_error('Invalid cXML structure');
            return;
        }

        $orderDate = (string) $cxml->Request->OrderRequest->OrderRequestHeader['orderDate'];
        $orderID = (string) $cxml->Request->OrderRequest->OrderRequestHeader['orderID'];
        $orderType = (string) $cxml->Request->OrderRequest->OrderRequestHeader['orderType'];
        $Type = (string) $cxml->Request->OrderRequest->OrderRequestHeader['type'];
        $senderIdentity = (string) $cxml->Header->Sender->Credential->Identity;
        $senderSecret = (string) $cxml->Header->Sender->Credential->SharedSecret;
        $totalAmount = (string) $cxml->Request->OrderRequest->OrderRequestHeader->Total->Money;
        // $currency = $cxml->Request->OrderRequest->OrderRequestHeader->Total->Money['currency'];
        $currency = '€';
        $contact = $cxml->Request->OrderRequest->OrderRequestHeader->Contact;
        

        $cart_manager->insert_order_request_to_db($cxml_content, $orderID, $orderType, $Type, $senderIdentity, $orderDate);

        // Get the user object based on the username
        $user = get_user_by('login', $senderIdentity);

            if (!$user) {
                // Handle the case where the user does not exist
                return new WP_Error('user_error', 'User not found', array('status' => 400));
            }
    

            // Authenticate the user's password
            if (!wp_check_password($senderSecret, $user->data->user_pass, $user->ID)) {

                // Prepare the email message
                $admin_email = get_woocommerce_admin_email(); 

                $to = $admin_email;
                $subject = 'Autenticación fallida para orden cXML';
                $message = "Intento fallido de autenticación para usuario: $senderIdentity.\n\n";
                $message .= "Este es el contenido cXML recibido:\n";
                $message .= htmlentities($cxml_content);  // Encode to display XML tags in HTML email
                $headers = array('Content-Type: text/html; charset=UTF-8');

                // Send the email to admin
                wp_mail($to, $subject, nl2br($message), $headers);


                return new WP_Error('authentication_error', 'Credenciales inválidas.', array('status' => 403));
            }


            // Create a new order
            $order = wc_create_order();

            // Process Extrinsic elements from OrderRequestHeader
            foreach ($cxml->Request->OrderRequest->OrderRequestHeader->Extrinsic as $extrinsic) {
                $name = (string)$extrinsic['name'];
                $value = (string)$extrinsic;
                if($value != ''){
                    $order->update_meta_data('_order_extrinsic_' . $name, $value);
                }
            
            }

                // Extract contact information
    
                $name = (string)$contact->Name;
                $email = (string)$contact->Email;
                $phone = (string)$contact->Phone->TelephoneNumber->Number;
                // Address parsing
                $address = $contact->PostalAddress;
                $street_lines = [];
                    foreach ($address->Street as $street) {
                        if (!empty($street)) {
                            $street_lines[] = (string)$street;
                        }
                    }
                $street_full = implode(", ", $street_lines);
                $city = (string)$address->City;
                $state = (string)$address->State;
                $postcode = (string)$address->PostalCode;
                $country = (string)$address->Country['isoCountryCode'];

            // Set the customer for the order
            $order->set_customer_id($user->ID);
            $order->update_meta_data('_order_date_cxml', $orderDate);
            $order->update_meta_data('_order_id_cxml', $orderID);
            $order->update_meta_data('_order_total_cxml', $totalAmount);
            $order->update_meta_data('_order_sender_cxml', $senderIdentity);
            $order->update_meta_data('_order_type_cxml', $orderType);
            $order->update_meta_data('_order_currency_cxml', $currency);
            $order->update_meta_data('_created_via_cxml', true); 
    
            // Save contact details to order meta
            $order->update_meta_data('_contact_name_xml', ' Mobre'.$name);
            $order->update_meta_data('Contact Email', $email);
            $order->update_meta_data('Contact Phone', $phone);
            $order->update_meta_data('Contact Street', $street_full);
            $order->update_meta_data('Contact City', $city);
            $order->update_meta_data('Contact State', $state);
            $order->update_meta_data('Contact Postal Code', $postcode);
            $order->update_meta_data('Contact Country', $country);

            $order_manager->update_order_meta_from_cxml($order, $senderIdentity, $totalAmount, $currency, $orderID);

        $order_total = 0;

        // Parse and add item(s)
        foreach ($cxml->Request->OrderRequest->ItemOut as $itemOut) {
            $quantity = (int)$itemOut['quantity'];     
            $requestedDeliveryDate = (string)$itemOut['requestedDeliveryDate'];
            $unitPrice = (float)$itemOut->ItemDetail->UnitPrice->Money;
            $line_total = $quantity * $unitPrice;
            
            $order_total += $line_total;

            $product_id = wc_get_product_id_by_sku((string)$itemOut->ItemID->SupplierPartID);
            $product = wc_get_product($product_id);
            if ($product) {
            $item_id =  $order->add_product($product, $quantity, array('subtotal' => $unitPrice, 'total' => $line_total));

                // Get the order item object
                $item = $order->get_item($item_id);
                
                if ($item && !empty($requestedDeliveryDate)) {
                    // Adding custom meta data to the order item
                    $item->add_meta_data('Fecha de Entrega Solicitada', $requestedDeliveryDate, true);
                    $item->save_meta_data(); // Save the meta data changes
                }

                if ($item) {
                    // Save delivery address details
                    $shipTo = $itemOut->ShipTo->Address;
                    $deliveryAddress = format_address_from_cxml($shipTo->PostalAddress);
                    $email = (string)$shipTo->Email;
                    $phone = (string)$shipTo->Phone->TelephoneNumber->Number;

                    $item->add_meta_data('Dirección de entrega', $deliveryAddress, true);
                    $item->add_meta_data('Correo electrónico', $email, true);
                    $item->add_meta_data('Teléfono', $phone, true);

                    $item->save();
                }

                foreach ($itemOut->ItemDetail->Extrinsic as $extrinsic) {
                    $name = (string)$extrinsic['name'];
                    $value = (string)$extrinsic;
                    
                    // Check if the name is one of the excluded fields
                    if (in_array($name, ['LINENUM', 'SHIPMENTNUM'])) {
                        continue; // Skip adding these meta data
                    }
                
                    // Add as item meta if not empty
                    if (!empty($value)) {
                        $item->add_meta_data($name, $value, true);
                        $item->save(); // Ensure this is save()
                    }
                }
                

            }     
        }

    // Set shipping address
        $shipTo = $cxml->Request->OrderRequest->OrderRequestHeader->ShipTo->Address;
        $shippingAddress = $cart_manager->set_order_address_from_cxml($shipTo);
    
        $order->set_address($shippingAddress, 'shipping');

        $order->update_meta_data('_shipping_email',(string) $shipTo->email);
        $order->update_meta_data('_billing_email',(string) $shipTo->email);

        $billTo = $cxml->Request->OrderRequest->OrderRequestHeader->BillTo->Address;

        $billingAddress = $cart_manager->set_order_address_from_cxml($billTo);

        // Set billing address
        $order->set_address($billingAddress, 'billing');
        // Assuming shipping is free and no additional calculations are needed    

        $order->set_payment_method('cm_manual');  
        $order->set_payment_method_title('Pago Directo');

        // $order->calculate_totals();
        $order->set_total($totalAmount);
        
        $order->save(); 

        error_log('Order Total After Calculation: ' . $order->get_total());

        // $order->set_total($totalAmount); 
        $manual_payment_gateway = new CM_WC_Gateway_Manual();
    
        // Process payment and update order status
        $result = $manual_payment_gateway->process_payment($order->get_id());

        if ($result['result'] == 'success') {
            error_log('Manual payment completed.');
        } else {
            error_log('Manual payment failed.');
        }
    
        $order->save();    
        error_log('Final Order Total: ' . $order->get_total());

        // Prepare cXML success response
        $response = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
        $response .= "<cXML payloadID=\"response" . time() . "\" timestamp=\"" . date('c') . "\">";
        $response .= "<Response>";
        $response .= "<Status code=\"200\" text=\"success\">Pedido creado con éxito</Status>";
        $response .= "<OrderID>" . $order->get_id() . "</OrderID>";
        $response .= "</Response>";
        $response .= "</cXML>";

        echo $response;
    }  catch (Exception $e) {
        // Prepare cXML error response
        $response = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
        $response .= "<cXML payloadID=\"response" . time() . "\" timestamp=\"" . date('c') . "\">";
        $response .= "<Response>";
        $response .= "<Status code=\"400\" text=\"failure\">Error al crear el pedido: " . $e->getMessage() . "</Status>";
        $response .= "</Response>";
        $response .= "</cXML>";

        echo $response;
    }
}

add_filter('woocommerce_order_item_display_meta_key', function($display_key, $meta) {
    // Check if the meta key starts with 'Extrinsic_' and modify the display key
    if (strpos($meta->key, 'Extrinsic_') === 0) {
        $display_key = str_replace('Extrinsic_', '', $meta->key);
    }
    return $display_key;
}, 10, 2);

add_action('woocommerce_admin_order_items_after_line_items', function($order_id) {
    $order = wc_get_order($order_id);
    foreach ($order->get_items() as $item_id => $item) {
        foreach ($item->get_meta_data() as $meta) {
            if (strpos($meta->key, 'Extrinsic_') === 0) {
                echo '<tr><td colspan="5">' . $meta->key . ': ' . $meta->value . '</td></tr>';
            }
        }
    }
}, 10, 1);


// add_action('woocommerce_email_after_order_table', 'add_shipping_address_to_emails', 20, 4);

// function add_shipping_address_to_emails($order, $sent_to_admin, $plain_text, $email) {
//     if ('customer_completed_order' === $email->id || 'customer_processing_order' === $email->id || 'customer_on_hold_order' === $email->id) {
//         echo '<h2>' . __('Shipping Address', 'woocommerce') . '</h2>';
//         echo '<p>' . $order->get_formatted_shipping_address() . '</p>';
//     }
// }


add_action( 'woocommerce_email_customer_details', 'remove_email_billing_address', 1 );

function remove_email_billing_address() {
    remove_action( 'woocommerce_email_customer_details', array( WC()->countries, 'email_address' ), 20 );
}


add_action('woocommerce_email_after_order_table', 'add_billing_and_shipping_address_to_emails', 20, 4);

function add_billing_and_shipping_address_to_emails($order, $sent_to_admin, $plain_text, $email) {
    if ('customer_completed_order' === $email->id || 'customer_processing_order' === $email->id || 'customer_on_hold_order' === $email->id) {
        echo '<h2>' . __('Dirección de envío', 'woocommerce') . '</h2>';
        echo '<p>' . $order->get_formatted_shipping_address() . '</p>';

        echo '<h2>' . __('Dirección de facturación', 'woocommerce') . '</h2>';
        echo '<p>' . $order->get_formatted_billing_address() . '</p>';
    }
}


// // Example code to check if meta data is being set correctly
// add_action('woocommerce_admin_order_item_headers', 'add_delivery_details_header');

// function add_delivery_details_header() {
//     echo '<th class="line_item_delivery">Delivery Details</th>'; // Adds a header for delivery details
// }


// function display_delivery_details_admin($product, $item, $item_id) {
//     $deliveryAddress = $item->get_meta('Delivery Address', true);
//     $email = $item->get_meta('Email', true);
//     $phone = $item->get_meta('Phone', true);

//     if (!empty($deliveryAddress) || !empty($email) || !empty($phone)) {
//         echo '<td class="delivery-details">';
//         if (!empty($deliveryAddress)) {
//             echo '<p><strong>Dirección de entrega:</strong> ' . esc_html($deliveryAddress) . '</p>';
//         }
//         if (!empty($email)) {
//             echo '<p><strong>Correo electrónico:</strong> ' . esc_html($email) . '</p>';
//         }
//         if (!empty($phone)) {
//             echo '<p><strong>Teléfono:</strong> ' . esc_html($phone) . '</p>';
//         }
//         echo '</td>';
//     } else {
//         echo '<td>No hay detalles</td>'; // To check if there are no details or the function isn't firing
//     }
// }

// add_action('woocommerce_admin_order_item_values', 'display_delivery_details_admin', 10, 3);


function custom_hide_order_itemmeta($hidden_meta_keys) {
    // Add the keys you want to hide from admin order views
    $hidden_meta_keys[] = 'Delivery Address';
    $hidden_meta_keys[] = 'Email';
    $hidden_meta_keys[] = 'Phone';

    return $hidden_meta_keys;
}
add_filter('woocommerce_hidden_order_itemmeta', 'custom_hide_order_itemmeta');

/*
//// User wise product restriction
add_action('template_redirect', 'restrict_product_access');
function restrict_product_access() {
    if (is_product()) {
        $product_id = get_the_ID();
        $product = wc_get_product($product_id);

        if (!$product) {
            error_log('Product is not set or found with ID: ' . $product_id);
            return;  // Stop if no product is found
        }

        $current_user = wp_get_current_user();
        // Retrieve the restricted SKUs using ACF
        $restricted_skus_raw = get_field('User_Restricted_Products', 'user_' . $current_user->ID);

        if (!$restricted_skus_raw) {
            error_log('No restricted SKUs found for user: ' . $current_user->ID);
            return;  // Stop if there are no restricted SKUs for the user
        }

        $restricted_skus = explode(',', $restricted_skus_raw);

        if (in_array($product->get_sku(), $restricted_skus)) {
            error_log('Access restricted for SKU: ' . $product->get_sku() . ' for user: ' . $current_user->ID);
            wp_redirect(home_url());  // Redirect to home page
            exit;
        }
    }
}


add_action('pre_get_posts', 'exclude_restricted_products');
function exclude_restricted_products($query) {
    if (!is_admin() && $query->is_main_query() && ($query->is_post_type_archive('product') || $query->is_tax(get_object_taxonomies('product')))) {
        $current_user = wp_get_current_user();
        // Retrieve the restricted SKUs using ACF
        $restricted_skus_raw = get_field('User_Restricted_Products', 'user_' . $current_user->ID);

        if (!empty($restricted_skus_raw)) {
            $restricted_skus = explode(',', $restricted_skus_raw);

            $restricted_ids = [];
            foreach ($restricted_skus as $sku) {
                $product_id = wc_get_product_id_by_sku(trim($sku));
                if ($product_id) {
                    $restricted_ids[] = $product_id;
                }
            }

            $query->set('post__not_in', array_merge((array) $query->get('post__not_in'), $restricted_ids));
        }
    }
}

*/

add_action('admin_enqueue_scripts', 'enqueue_custom_admin_styles');
function enqueue_custom_admin_styles() {
    wp_enqueue_style('cm-admin-css', get_stylesheet_directory_uri() . '/css/admin/admin-style.css');
}


add_action('add_meta_boxes', 'add_custom_order_meta_box');
function add_custom_order_meta_box() {
    add_meta_box(
        'custom_order_information',                       // ID of the meta box
        __('Información de Pedido Punchout', 'medilazar'), // Title of the meta box
        'custom_order_information_meta_box_content',     // Callback function to output content
        'shop_order',                                    // Post type
        'normal',                                        // Context (where on the screen)
        'low'                                        // Priority
    );
}


function custom_order_information_meta_box_content($post) {

    $order = wc_get_order($post->ID);
    $order_meta_data = $order->get_meta_data();
    $extrinsic_data = [];
    $order_id_cxml = $order->get_meta('_order_id_cxml', true);
    $order_date_cxml = $order->get_meta('_order_date_cxml', true);
    $total = $order->get_meta('_order_total_cxml', true);
    $currency = $order->get_meta('_order_currency_cxml', true);
    $sender = $order->get_meta('_order_sender_cxml', true);

        // Retrieve contact details from order meta
        $contact_name = $order->get_meta('_contact_name_xml');
        $contact_email = $order->get_meta('Contact Email');
        $contact_phone = $order->get_meta('Contact Phone');
        $contact_street = $order->get_meta('Contact Street');
        $contact_city = $order->get_meta('Contact City');
        $contact_state = $order->get_meta('Contact State');
        $contact_postal_code = $order->get_meta('Contact Postal Code');
        $contact_country = $order->get_meta('Contact Country');

        foreach ($order_meta_data as $meta) {
            if (strpos($meta->key, '_order_extrinsic_') === 0 && !empty($meta->value)) {
                $extrinsic_data[$meta->key] = $meta->value;
            }
        }

    echo '<div class="punchout_order_meta">';
    // Column 1: Order ID and Sender
    echo '<div class="order_meta_column">';
    echo '<p><strong>' . __('ID de Pedido :', 'medilazar') . '</strong> ' . esc_html($order_id_cxml) . '</p>';
    echo '<p><strong>' . __('Fecha/hora del pedido :', 'medilazar') . '</strong> ' . esc_html($order_date_cxml) . '</p>';
    // echo '<p><strong>' . __('Total del pedido :', 'medilazar') . '</strong> ' . esc_html($currency) .'' . esc_html($total) . '</p>';
    echo '</div>'; // Close column one

    // Column 2: Order Date
    echo '<div class="order_meta_column">';
    echo '<p>' . esc_html($contact_name) . '</p>';
    echo '<p>' . esc_html($contact_email) . '</p>';
    echo '<p>' . esc_html($contact_phone) . '</p>';
    echo '<p>' . esc_html($contact_street) . '</p>';
    echo '<p>' . esc_html($contact_city) . '</p>';
    echo '<p>' . esc_html($contact_state) . '</p>';
    echo '<p>' . esc_html($contact_postal_code) . '</p>';
    echo '<p><' . esc_html($contact_country) . '</p>';
    echo '</div>'; // Close column two

    // Column 3: Extrinsic
    if (!empty($extrinsic_data)) {
        echo '<div class="order_meta_column">';
        foreach ($extrinsic_data as $key => $value) {
            // Format the key to a more readable format if necessary
            $formatted_key = ucwords(str_replace('_', ' ', str_replace('_order_extrinsic_', '', $key)));
            echo '<p><strong>' . esc_html($formatted_key) . ':</strong> ' . esc_html($value) . '</p>';
        }
        echo '</div>';
    } else {
        echo '<p>No extrinsic data available.</p>';
    }
  

    echo '</div>'; // Close punchout_order_meta container
}


// Remove the Custom Fields meta box in WooCommerce Orders
add_action('add_meta_boxes', 'remove_custom_fields_meta_box', 100);

function remove_custom_fields_meta_box() {
    remove_meta_box('postcustom', 'shop_order', 'normal');
}



// Custom Product Prices


/*
add_filter('woocommerce_product_get_price', 'custom_role_based_pricing', 99, 2);
add_filter('woocommerce_product_get_regular_price', 'custom_role_based_pricing', 99, 2);

function custom_role_based_pricing($price, $product) {
    if (is_admin() && !defined('DOING_AJAX')) return $price;

    $user = wp_get_current_user();
    $fixed_discount = get_post_meta($product->get_id(), 'fixed_discount', true);

        $agreed_price = get_post_meta($product->get_id(), 'agreed_price', true);
        if (!empty($agreed_price)) {
            return $agreed_price;
        }

    if (!empty($fixed_discount) && $fixed_discount > 0) {
        $discounted_price = $price - ($price * ($fixed_discount / 100));
        return $discounted_price;
    }

    return $price;
}

*/

// function custom_role_based_pricing($price, $product) {
//     if (is_admin() && !defined('DOING_AJAX')) return $price;

//     $user = wp_get_current_user();

//     if (in_array('specific_role', $user->roles)) { // Replace 'specific_role' with the actual role ID
//         $agreed_price = get_post_meta($product->get_id(), 'agreed_price', true);
//         if (!empty($agreed_price)) {
//             return $agreed_price;
//         }

//         $fixed_discount = get_post_meta($product->get_id(), 'fixed_discount', true);
//         if (!empty($fixed_discount)) {
//             $discounted_price = $price - ($price * ($fixed_discount / 100));
//             return $discounted_price;
//         }

//         $price_request = get_post_meta($product->get_id(), 'price_request', true);
//         if ($price_request) {
//             return ''; // Return empty string for "Price on Request"
//         }
//     }

//     return $price; // Return default price if no conditions met
// }



/*

add_filter('woocommerce_is_purchasable', 'custom_purchasable_logic', 10, 2);
function custom_purchasable_logic($purchasable, $product) {
    $price_request = get_post_meta($product->get_id(), 'price_request', true);
    if ($price_request) {
        return true; // Ensure that products are purchasable even without a price
    }
    return $purchasable;
}



add_action('woocommerce_product_options_pricing', 'add_custom_rate_fields');
function add_custom_rate_fields() {
    echo '<div class="options_group">';

    for ($i = 1; $i <= 3; $i++) { // Example for 3 different rates
        woocommerce_wp_text_input(array(
            'id' => 'discount_rate_' . $i,
            'label' => sprintf(__('Discount Rate %d (%%):', 'medilazar'), $i),
            'desc_tip' => 'true',
            'description' => sprintf(__('Enter the discount rate %d.', 'medilazar'), $i),
            'type' => 'number',
            'custom_attributes' => array(
                'step' => 'any',
                'min' => '0'
            ),
        ));
    }

    echo '</div>';
}


add_action('woocommerce_admin_process_product_object', 'save_custom_rate_fields');
function save_custom_rate_fields($product) {
    for ($i = 1; $i <= 3; $i++) {
        if (isset($_POST['discount_rate_' . $i])) {
            $product->update_meta_data('discount_rate_' . $i, sanitize_text_field($_POST['discount_rate_' . $i]));
        }
    }
}


add_filter('woocommerce_product_get_price', 'apply_custom_discount_rates', 10, 2);
function apply_custom_discount_rates($price, $product) {
    $final_price = $price;
    for ($i = 1; $i <= 3; $i++) {
        $rate = $product->get_meta('discount_rate_' . $i);
        if (!empty($rate)) {
            $final_price -= ($final_price * ($rate / 100)); // Applying the discount rate
        }
    }
    return $final_price;
}


*/



//Removing Contents for UNAV Users

// function redirect_customers_from_myaccount() {
//     if (is_account_page()) {
//         if (current_user_can('customer')) {
//             wp_redirect(home_url()); // Redirect to the homepage
//             exit;
//         }
//     }
// }
// add_action('template_redirect', 'redirect_customers_from_myaccount');

// function remove_my_account_links_for_customers($items, $menu, $args) {
//     if (current_user_can('customer')) {
//         foreach ($items as $key => $item) {
//             if ($item->url === wc_get_page_permalink('myaccount')) {
//                 unset($items[$key]);
//             }
//         }
//     }
//     return $items;
// }

// add_filter('wp_get_nav_menu_items', 'remove_my_account_links_for_customers', 10, 3);


add_filter('woocommerce_cart_needs_shipping', 'remove_shipping_for_session_specific_users', 10, 1);
function remove_shipping_for_session_specific_users($needs_shipping) {
    // Access the session variable
    global $session_manager;  
    $session_specific_user = $session_manager->is_session_specific_user();

    // Check for your specific condition here
    if ($session_specific_user) {
        return false; // Disable shipping calculation
    }

    return $needs_shipping; // Otherwise, continue as normal
}

add_filter('woocommerce_coupons_enabled', 'remove_coupons_for_session_specific_users', 10, 1);
function remove_coupons_for_session_specific_users($enabled) {
    // Access the session variable using a global session manager or WooCommerce session
    global $session_manager;
    $session_specific_user = $session_manager->is_session_specific_user(); // Adjust this function to your session manager implementation

    // Disable coupons for session-specific users
    if ($session_specific_user) {
        return false; // Disable coupon usage
    }

    return $enabled; // Otherwise, enable coupons as usual
}


add_filter('wp_get_nav_menu_items', 'remove_contacto_menu_item', 10, 3);
function remove_contacto_menu_item($items, $menu, $args) {
    global $session_manager;
    $session_specific_user = $session_manager->is_session_specific_user(); // Adjust this based on your session check

    if ($session_specific_user) {
        foreach ($items as $key => $item) {
            if ($item->title == "Contacto") { // Check for the title of the menu item
                unset($items[$key]);
            }
        }
    }

    return $items;
}

// Conditionally hide the inner contents of the Account section with specific CSS.
add_action('wp_head', 'conditionally_hide_account_inner_css');
function conditionally_hide_account_inner_css() {

    global  $session_manager;  
    // Retrieve the cart total for the session ID
    $session_specific_user = $session_manager->is_session_specific_user();
 
    if($session_specific_user){ 
        // Check if the user should see the account inner content
            // Add custom inline styles with higher specificity to hide the inner contents
            echo "
            <style>
            .site-header-account .account-inner,
            .site-header-account .account-dropdown {
                display: none !important;
            }
            </style>
            ";

            echo "
            <style>
            .account .site-header-account,
            .account .account-dropdown {
                visibility: hidden;
            }
            </style>
            ";
            echo "
            <style>
            /* Set visibility hidden for specific WooCommerce header section contents */
            .elementor-section.elementor-element-b586295 .elementor-widget-container {
                visibility: hidden;
            }
            </style>
            ";
    }
}

// Restrict access to WooCommerce account pages for customers
add_action('template_redirect', 'restrict_account_access_for_customers');
function restrict_account_access_for_customers() {

    global  $session_manager;  
    // Retrieve the cart total for the session ID
    $session_specific_user = $session_manager->is_session_specific_user();
 
    if($session_specific_user){          
        if (is_page(['mi-cuenta', 'orders', 'downloads', 'edit-account', 'edit-address'])) {
            // Redirect to a different page or show an error message
            wp_redirect(home_url()); // Adjust the URL as needed
            exit;
        }
    }

    // Check if the user is attempting to log out
    if (isset($_GET['action']) && $_GET['action'] === 'logout') {
        // Redirect to a different page or show a message instead of logging out
        wp_redirect(home_url());
        exit;
    }
}

####   RESTRICT PRODUCT BY SUB CATEGORIES

add_action('pre_get_posts', 'restrict_products_by_user_subcategory');

function restrict_products_by_user_subcategory($query) {
    // Only run this on the frontend and not on admin pages
    if (!is_admin() && (is_shop() || is_product_category() || is_front_page())) {
        if ($query->is_main_query()) {
            $user_id = get_current_user_id();
            error_log('Running product restriction for User ID: ' . $user_id);

            // Get restricted categories from ACF field, assuming it returns comma-separated names
            $restricted_categories_names = get_field('User_Restricted_Products', 'user_' . $user_id);
            error_log('Fetched restricted categories: ' . $restricted_categories_names);

            if (!empty($restricted_categories_names)) {
                $category_names = explode(',', $restricted_categories_names);
                $category_ids = array();

                // Convert category names to term IDs
                foreach ($category_names as $category_name) {
                    $term = get_term_by('name', trim($category_name), 'product_cat');
                    if ($term) {
                        $category_ids[] = (int) $term->term_id;
                    }
                }

                if (!empty($category_ids)) {
                    $tax_query = $query->get('tax_query') ?: array();
                    $tax_query[] = array(
                        'taxonomy' => 'product_cat',
                        'field'    => 'term_id',
                        'terms'    => $category_ids,
                        'operator' => 'NOT IN',
                    );
                    $query->set('tax_query', $tax_query);
                    error_log('Tax Query Modified: ' . print_r($tax_query, true));
                }
        } else {
            error_log('No restricted categories set for User ID ' . $user_id);
        }
        }
    }
}


add_action('woocommerce_product_query', 'custom_handle_product_query_restriction');
function custom_handle_product_query_restriction($query) {
    if (!is_admin()) { // Ensure this runs only on the front end
        $user_id = get_current_user_id();
        $restricted_categories_names = get_field('User_Restricted_Products', 'user_' . $user_id);
        
        if ($restricted_categories_names) {
            $category_names = explode(',', $restricted_categories_names);
            $category_ids = [];
            
            foreach ($category_names as $category_name) {
                $term = get_term_by('name', trim($category_name), 'product_cat');
                if ($term) {
                    $category_ids[] = $term->term_id;
                }
            }
            
            if (!empty($category_ids)) {
                $tax_query = $query->get('tax_query') ?: [];
                $tax_query[] = [
                    'taxonomy' => 'product_cat',
                    'field'    => 'term_id',
                    'terms'    => $category_ids,
                    'operator' => 'NOT IN',
                ];
                $query->set('tax_query', $tax_query);
            }
        }
    }
}


add_action('template_redirect', 'block_direct_access_to_restricted_products');
function block_direct_access_to_restricted_products() {
    if (is_product()) {
        global $post;
        $product = wc_get_product($post);  // Properly get the product object

        if ($product) {
            $user_id = get_current_user_id();
            $restricted_categories_names = get_field('User_Restricted_Products', 'user_' . $user_id);

            if ($restricted_categories_names) {
                $category_names = explode(',', $restricted_categories_names);
                foreach ($category_names as $category_name) {
                    $term = get_term_by('name', trim($category_name), 'product_cat');
                    if ($term && has_term($term->term_id, 'product_cat', $product->get_id())) {
                        // Redirect to shop page or home page
                        wp_redirect(home_url());
                        exit;
                    }
                }
            }
        }
    }
}



add_filter('carousel_product_args', 'modify_carousel_product_args');

function modify_carousel_product_args($args) {
    $user_id = get_current_user_id();
    $restricted_categories_names = get_field('User_Restricted_Products', 'user_' . $user_id);

    if ($restricted_categories_names) {
        $category_names = explode(',', $restricted_categories_names);
        $category_ids = [];

        foreach ($category_names as $category_name) {
            $term = get_term_by('name', trim($category_name), 'product_cat');
            if ($term) {
                $category_ids[] = (int) $term->term_id;
            }
        }

        if (!empty($category_ids)) {
            $tax_query = (isset($args['tax_query']) && is_array($args['tax_query'])) ? $args['tax_query'] : [];
            $tax_query[] = [
                'taxonomy' => 'product_cat',
                'field'    => 'term_id',
                'terms'    => $category_ids,
                'operator' => 'NOT IN',
            ];
            $args['tax_query'] = $tax_query;
        }
    }

    return $args;
}


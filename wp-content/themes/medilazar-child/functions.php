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

function create_wc_order_from_cxml(WP_REST_Request $request) {

    global $cart_manager, $order_manager;
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
    $totalAmount = (string) $cxml->Request->OrderRequest->OrderRequestHeader->Total->Money;
    $currency = $cxml->Request->OrderRequest->OrderRequestHeader->Total->Money['currency'];
  
    $cart_manager->insert_order_request_to_db($cxml_content, $orderID, $orderType, $Type, $senderIdentity, $orderDate);
   

    // $shipTo = $cxml->Request->OrderRequest->OrderRequestHeader->ShipTo->Address;
    // if (empty($shipTo) || empty($shipTo->PostalAddress->Street) || empty($shipTo->PostalAddress->City)) {
    //     wp_send_json_error('Incomplete shipping address');
    //     return;
    // }

        // Create a new order
        $order = wc_create_order();

        // Process Extrinsic elements from OrderRequestHeader
        foreach ($cxml->Request->OrderRequest->OrderRequestHeader->Extrinsic as $extrinsic) {
            $name = (string)$extrinsic['name'];
            $value = (string)$extrinsic;
            $order->update_meta_data('_order_extrinsic_' . $name, $value);
        }

        // Get the user object based on the username
        $user = get_user_by('login', $senderIdentity);

        if (!$user) {
            // Handle the case where the user does not exist
            return new WP_Error('user_error', 'User not found', array('status' => 400));
        }

         // Set the customer for the order
    $order->set_customer_id($user->ID);
    $order->update_meta_data('_order_date_cxml', $orderDate);
    $order->update_meta_data('_order_id_cxml', $orderID);
    $order->update_meta_data('_order_total_cxml', $totalAmount);
    $order->update_meta_data('_order_sender_cxml', $senderIdentity);
    $order->update_meta_data('_order_type_cxml', $orderType);

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
        }

             // Process Extrinsic elements for each ItemOut
            foreach ($itemOut->ItemDetail->Extrinsic as $extrinsic) {
                $name = (string)$extrinsic['name'];
                $value = (string)$extrinsic;  
                $order->update_meta_data('_item_extrinsic_' . $name, $value);
            }         
    }

    // Set the calculated order total 
    // $order->set_total($order_total);  
    $order->calculate_taxes();
    $order->calculate_totals();


   // Set shipping address
    $shipTo = $cxml->Request->OrderRequest->OrderRequestHeader->ShipTo->Address;
    $shippingAddress = $cart_manager->set_order_address_from_cxml($shipTo);
 
    $order->set_address($shippingAddress, 'shipping');

    $order->update_meta_data('_shipping_email',(string) $shipTo->email);

    $billTo = $cxml->Request->OrderRequest->OrderRequestHeader->BillTo->Address;

    $billingAddress = $cart_manager->set_order_address_from_cxml($billTo);

     // Set billing address
    $order->set_address($billingAddress, 'billing');
    // Assuming shipping is free and no additional calculations are needed    

    $order->set_payment_method('cm_manual');  
    $manual_payment_gateway = new CM_WC_Gateway_Manual();
   
    // Process payment and update order status
    $manual_payment_gateway->process_payment($order->get_id()); 

    error_log($order->get_total());
    $order->save();    

    return $order->get_id();
}

// User wise product restriction
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


add_action('admin_enqueue_scripts', 'enqueue_custom_admin_styles');
function enqueue_custom_admin_styles() {
    wp_enqueue_style('cm-admin-css', get_stylesheet_directory_uri() . '/css/admin/admin-style.css');
}


add_action('add_meta_boxes', 'add_custom_order_meta_box');
function add_custom_order_meta_box() {
    add_meta_box(
        'custom_order_information',                       // ID of the meta box
        __('Información de Pedido Punchout (cXML)', 'medilazar'), // Title of the meta box
        'custom_order_information_meta_box_content',     // Callback function to output content
        'shop_order',                                    // Post type
        'normal',                                        // Context (where on the screen)
        'high'                                        // Priority
    );
}

function custom_order_information_meta_box_content($post) {

    $order = wc_get_order($post->ID);
    $order_id_cxml = $order->get_meta('_order_id_cxml', true);
    $order_date_cxml = $order->get_meta('_order_date_cxml', true);
    $total = $order->get_meta('_order_total_cxml', true);
    $sender = $order->get_meta('_order_sender_cxml', true);

    echo '<div class="punchout_order_meta">';
    // Column 1: Order ID and Sender
    echo '<div class="order_meta_column">';
    echo '<p><strong>' . __('ID de Pedido :', 'medilazar') . '</strong> ' . esc_html($order_id_cxml) . '</p>';
    echo '<p><strong>' . __('Sender :', 'medilazar') . '</strong> ' . esc_html($sender) . '</p>';
    echo '</div>'; // Close column one

    // Column 2: Order Date
    echo '<div class="order_meta_column">';
    echo '<p><strong>' . __('Fecha del Pedido :', 'medilazar') . '</strong> ' . esc_html($order_date_cxml) . '</p>';
    echo '</div>'; // Close column two

    // Column 3: Total
    echo '<div class="order_meta_column">';
    echo '<p><strong>' . __('Total :', 'medilazar') . '</strong> ' . esc_html($total) . '</p>';
    echo '</div>'; // Close column three

    echo '</div>'; // Close punchout_order_meta container
}


<?php

namespace CM;

define('ENCRYPTION_KEY', base64_decode('XOyFM2ZvbUisqHNRIuN8T9NAH4Rs4lRZZBWVT8VTDZE=')); // Todo - move to WP-Config 

class Session_Manager {
    public function __construct() {
        
    }
   
    /**
     * Retrieves and decrypts the session key from the 'cm_session_key' cookie.
     *
     * This method checks if the 'cm_session_key' cookie is set. If it is, the method
     * decodes the base64-encoded data from the cookie, extracts the initialization
     * vector (IV) and the encrypted session key, and then decrypts the session key
     * using AES-256-CBC encryption algorithm with a predefined encryption key.
     *
     * @return mixed Returns the decrypted session key if successful, false otherwise.
     */

    public function get_session_key_from_cookie() {
        if (!isset($_COOKIE['cm_session_key'])) {
            return false;
        }

        $encodedData = $_COOKIE['cm_session_key'];
        $combinedData = base64_decode($encodedData);

        $ivLength = openssl_cipher_iv_length('aes-256-cbc');
        $iv = substr($combinedData, 0, $ivLength);
        $encryptedSessionKey = substr($combinedData, $ivLength);

        $decryptedSessionKey = openssl_decrypt($encryptedSessionKey, 'aes-256-cbc', ENCRYPTION_KEY, 0, $iv);

        return $decryptedSessionKey !== false ? $decryptedSessionKey : false;
    }


    

    /**
     * Checks if the current session is associated with a specific user.
     *
     * This function validates the session key stored in the 'cm_session_key' cookie
     * to determine if the current session is specific to a user. The session key is
     * first decrypted and then validated for its format and authenticity.
     *
     * @global object $session_manager The session manager object responsible for handling session operations.
     *
     * @return bool Returns true if the session is specific to a user, false otherwise (indicating a normal user).
     */
    function is_session_specific_user() {

        global $session_manager;
        $decryptedSessionKey = $session_manager->getAndDecryptSessionKeyFromCookie();
       
        if (isset($_COOKIE['cm_session_key']) && !empty($_COOKIE['cm_session_key'])) {
           
            if ($session_manager->validate_session_key_format($decryptedSessionKey)) {
                if ($decryptedSessionKey && $session_manager->validateSessionCookieKey($decryptedSessionKey)) {
                    return true;
                } else {
                    return false;
                }
            }
        }
        return false; // This is a normal user
    }
    

    public function get_cm_session_expires_at($session_key) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'cm_sessions';

        // Prepare the SQL query to select the expires_at field
        $query = $wpdb->prepare(
            "SELECT expires_at FROM $table_name WHERE session_key = %s",
            $session_key
        );

        // Execute the query and get the result
        $expires_at = $wpdb->get_var($query);

        return $expires_at;
    }

    function validate_session_key_format($session_key) {
        return strlen($session_key) === 20;
    }

    function validate_session_key($session_key, $session_email) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'cm_sessions';
        $current_time = current_time('mysql');

    
        $session = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE session_key = %s AND session_email = %s AND expires_at > %s",
            $session_key,
            $session_email,
            $current_time
        ));
    
        if (null !== $session) {
    
            // Session is valid
            return $session->user_id;
        }

        // Invalid session
        return false;
    }
    

    function getAndDecryptSessionKeyFromCookie($cookieName = 'cm_session_key') {
        if (!isset($_COOKIE[$cookieName])) {
            return false; // Cookie not set
        }
    
        $encodedData = $_COOKIE[$cookieName];
        $combinedData = base64_decode($encodedData);
    
        $ivLength = openssl_cipher_iv_length('aes-256-cbc');
        $iv = substr($combinedData, 0, $ivLength);
        $encryptedSessionKey = substr($combinedData, $ivLength);
    
        $decryptedSessionKey = openssl_decrypt($encryptedSessionKey, 'aes-256-cbc', ENCRYPTION_KEY, 0, $iv);
    
        if ($decryptedSessionKey === false) {
         error_log('Decryption failed');
        }
    
        return $decryptedSessionKey;
    }
    
    function validateSessionCookieKey($sessionKey) {
    
        if (strlen($sessionKey) != 20) { // Assuming session keys are 20 characters long
            return false;
        }
    
        global $wpdb;
        $table_name = $wpdb->prefix . 'cm_sessions';
        $current_time = current_time('mysql');
    
        $session = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE session_key = %s AND expires_at > %s",
            $sessionKey,
            $current_time
        ));
    
        if (null !== $session) {
            // Session is valid
            return $session->user_id;
        }else{
            return false;
        }
    }
    
    /**
 * Sets a session key cookie for the user.
 *
 * @param string $session_key The session key to be set in the cookie.
 * @param int $expiration_period The number of seconds until the cookie should expire.
 * @param string $path The path on the server in which the cookie will be available on.
 * @param bool $secure Indicates that the cookie should only be transmitted over a secure HTTPS connection.
 * @param bool $httponly When TRUE the cookie will be made accessible only through the HTTP protocol.
 * @param string $samesite Prevents the browser from sending this cookie along with cross-site requests.
 */
function set_cm_session_cookie($encrypted_session_key_with_iv, $expiration_period = 86400, $path = '/', $secure = true, $httponly = true, $samesite = 'Lax') {
    $cookie_name = 'cm_session_key';
    
    $cookie_value = $encrypted_session_key_with_iv;
    $expiration = time() + $expiration_period;
    
    setcookie($cookie_name, $cookie_value, [
        'expires' => $expiration,
        'path' => $path,
        'secure' => $secure,
        'httponly' => $httponly,
        'samesite' => $samesite
    ]);

    return true;

}

function set_cm_session_id_cookie($session_id, $expiration_period = 86400, $path = '/', $secure = true, $httponly = true, $samesite = 'Lax') {
   
    $cookie_value = $session_id;
    $expiration = time() + $expiration_period;
    
    $session_id_cookie_name = 'cm_session_id';

    setcookie($session_id_cookie_name, $cookie_value, [
        'expires' => $expiration,
        'path' => $path,
        'secure' => $secure,
        'httponly' => false,
        'samesite' => $samesite
    ]);

    return true;
}

public function get_session_id_from_cookie() {
    if (!empty($_COOKIE['cm_session_key'])) {
       // Assume function to validate session key and return session ID
        return $this->get_current_session_id();
    }
    return false;
}


function set_cm_session_email_cookie($session_id, $expiration_period = 86400, $path = '/', $secure = true, $httponly = true, $samesite = 'Lax') {
   
    $cookie_value = $session_id;
    $expiration = time() + $expiration_period;
    
    $session_email_cookie_name = 'cm_session_email';

    setcookie($session_email_cookie_name, $cookie_value, [
        'expires' => $expiration,
        'path' => $path,
        'secure' => $secure,
        'httponly' => false,
        'samesite' => $samesite
    ]);

    return true;
}


/**
 * Retrieves the current session ID based on the decrypted session key stored in a cookie.
 *
 * This function decrypts the session key from the cookie and queries the database
 * to find the corresponding session ID. The session ID is then returned if found,
 * otherwise false is returned indicating no matching session was found.
 *
 * @global object $wpdb The WordPress database access abstraction object.
 *
 * @return mixed Returns the session ID if found, false otherwise.
 */
function get_current_session_id() {
    global $wpdb;
    
    $decrypted_session_key = $this->getAndDecryptSessionKeyFromCookie();
    $table_name = $wpdb->prefix . 'cm_sessions';
    $query = $wpdb->prepare("SELECT session_id FROM $table_name WHERE session_key = %s LIMIT 1", $decrypted_session_key);
    $session_id = $wpdb->get_var($query);

    if (empty($session_id)) {
        return false; // No matching session found
    }
    return $session_id;
}

}

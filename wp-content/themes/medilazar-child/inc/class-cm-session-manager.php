<?php

namespace CM;

define('ENCRYPTION_KEY', base64_decode('XOyFM2ZvbUisqHNRIuN8T9NAH4Rs4lRZZBWVT8VTDZE='));

class Session_Manager {
    public function __construct() {
        // Your constructor logic here
    }

    public function start_session() {
        // Method logic to start a session
        error_log("Session started within CM namespace");
    }

    
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

        error_log('Session Key : '.$decryptedSessionKey);

        return $decryptedSessionKey !== false ? $decryptedSessionKey : false;
    }


    function is_session_specific_user() {

        global $session_manager;
        $decryptedSessionKey = $session_manager->getAndDecryptSessionKeyFromCookie();
    
        error_log(' SESSION KEY :'. $decryptedSessionKey);
    
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
    

    public function expire_sessions_by_email($session_email) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'cm_sessions';

        $active_session_threshold = current_time('mysql', 1); // Considering sessions created within the last day as active

        $result = $wpdb->query($wpdb->prepare(
            "UPDATE $table_name SET expires_at = %s WHERE session_email = %s AND created_at >= DATE_SUB(%s, INTERVAL 1 DAY)",
            $active_session_threshold, $session_email, $active_session_threshold
        ));

        if (false === $result) {
            error_log('Failed to expire sessions by email.');
        } else {
            error_log('Sessions expired successfully by email.');
        }
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

        error_log('session validating');
    
        $session = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE session_key = %s AND session_email = %s AND expires_at > %s",
            $session_key,
            $session_email,
            $current_time
        ));
    
        if (null !== $session) {
            error_log(" Valid Session! ");
            // Session is valid
            return $session->user_id;
        }
    
        error_log("Warning Valid Session! ");
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
            error_log('Valid Sessoin');
            // Session is valid
            return $session->user_id;
        }else{
            error_log(' InvalidValid Sessoin : '.$sessionKey);
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


public function get_session_id_from_cookie() {
    if (!empty($_COOKIE['cm_session_key'])) {
       // Assume function to validate session key and return session ID
        return $this->get_current_session_id();
    }
    return false;
}

function get_current_session_id() {
    global $wpdb;
    
    $decrypted_session_key = $this->getAndDecryptSessionKeyFromCookie();
    // Assuming your sessions table is named 'wp_cm_sessions' and has columns 'session_id' and 'session_key'
    $table_name = $wpdb->prefix . 'cm_sessions';
    $query = $wpdb->prepare("SELECT session_id FROM $table_name WHERE session_key = %s LIMIT 1", $decrypted_session_key);
    $session_id = $wpdb->get_var($query);

    if (empty($session_id)) {
        return false; // No matching session found
    }


    error_log(" get current session id ". $session_id);
    return $session_id;
}





}

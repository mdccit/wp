jQuery(document).ready(function($) {


    function getSessionIdFromSessionCookie() {   
        // Get the session_id from the cookie
        var sessionId = Cookies.get('cm_session_id'); // Using js-cookie
    
        // Check if the cookie is not found
        if (typeof sessionId === 'undefined') {
            console.error('Session ID cookie not found.');
            return null;
        }
        return sessionId;
    }
   
    // PUNCHOUT ORDER MESSAGE
    $('#punchout_return').on('click', function(e) {
        e.preventDefault();  
        var session_id = getSessionIdFromSessionCookie();   
            e.preventDefault();
            jQuery.ajax({
                url: myAjax.ajaxurl, // Ensure myAjax.ajaxurl is defined somewhere in your PHP using wp_localize_script
                _ajax_nonce: myAjax.nonce,
                session_id: session_id,
                nonce: myAjax.nonce,
                method: 'POST',
                data: {
                    action: 'logout_user_and_redirect'
                },
                success: function (response) {
                    if (response.success) {
                        window.location.href = response.data.redirect_url; // Redirect URL passed from server
                    } else {
                        if (response.data && response.data.message) {
                            console.error('Failed to log out:', response.data.message);
                        } else {
                            console.error('Failed to log out: Unknown error'); // Fallback error message
                        }
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.error('AJAX error:', textStatus, errorThrown);
                }
                
            });
        
    });   


});

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
            var session_id = getSessionIdFromSessionCookie();   
            e.preventDefault();
            jQuery.ajax({
                url: punchoutAjax.ajaxurl, // Ensure myAjax.ajaxurl is defined somewhere in your PHP using wp_localize_script
                method: 'POST',
                dataType: 'json',
                data: {
                    _ajax_nonce: punchoutAjax.nonce,   
                    action: 'logout_user_and_redirect',
                    nonce: punchoutAjax.nonce,
                    session_id: session_id,
                },
                success: function (response) {
                    // if (response.success) {
                        window.location.href = response.data.redirect_url; // Redirect if needed or handle as per your application logic
                    // } else {
                    //     if (response.data && response.data.message) {
                    //         console.error('Failed to log out:', response.data.message);
                    //     } else {
                    //         console.error('Failed to log out: Unknown error'); // Fallback error message
                    //     }
                    // }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.error('AJAX error:', textStatus, errorThrown);
                }
                
            });
        
    });   



        // PUNCHOUT ORDER MESSAGE
        $('.wc-proceed-to-checkout a.checkout-button').on('click', function(e) {
            e.preventDefault();  
                if(getSessionIdFromSessionCookie !== null){         
                    create_complete_punchout_order_cxml();         
                }
        });
        
        function create_complete_punchout_order_cxml() {
    
            var session_id = getSessionIdFromSessionCookie();      
        
                $.ajax({
                        url: punchoutAjax.ajaxurl,
                        method: 'POST',
                        data: {
                            action: 'create_complete_punchout_order_cxml',
                            _ajax_nonce: punchoutAjax.nonce,
                            session_id: session_id,
                        },
                        dataType: 'json',
    
                        success: function(response) {   
                            console.log(response);         
                            window.location.href = response.data.redirect_url;   
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            console.error('AJAX error:', textStatus, errorThrown);
                        }
                });
                       
        }


});

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
                    console.log(response.data.redirect_url);
                        // window.location.href = response.data.redirect_url; // Redirect if needed or handle as per your application logic
               
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.error('AJAX error:', textStatus, errorThrown);
                }
                
            });
        
    });   



        // PUNCHOUT ORDER MESSAGE
        $('.wc-proceed-to-checkout a.cm-punchout-checkout-button').on('click', function(e) {
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
                            console.log(response.data.redirect_url);         
                            // window.location.href = response.data.redirect_url;   

                            if(response.success && response.data.cxmlData) {
                                var erpEndpoint1 = 'https://commercialmedica.requestcatcher.com/test'; 
                                var erpEndpoint2 = 'https://pcsf.cloud.punchoutexpress.com/simulator/cart/receive.php'; 
                                sendCxmlDataToERP(response.data.cxmlData, response.data.orderURL);      
                                sendCxmlDataToERP(response.data.cxmlData, erpEndpoint1);                                                
                                sendCxmlDataToERP(response.data.cxmlData, erpEndpoint2);
                            } else {
                                console.error('Error preparing cXML:', response.data.message);
                            }
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            console.error('AJAX error:', textStatus, errorThrown);
                        }
                });
                       
        }

        function sendCxmlDataToERP(cxmlData , orderURL) {
            var erpEndpoint = orderURL; 
            $.ajax({
                url: erpEndpoint,
                method: 'POST',
                contentType: 'application/x-www-form-urlencoded; charset=ISO-8859-1',
                data: 'oracleCart=' + encodeURIComponent(cxmlData),
                timeout: 45000, // 45 seconds
                success: function(response) {
                    console.log('Success:', response);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('ERP AJAX error:', textStatus, errorThrown);
                }
            });
        }
});

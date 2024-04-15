jQuery(document).ready(function($) {

    updateMiniCartTotal();         

    $(document).on('click', '.quantity-action.plus, .quantity-action.minus', function(e) {

            if(getSessionIdFromCookie !== null){
                    e.preventDefault();

                    var productId = $(this).closest('tr.woocommerce-cart-form__cart-item').find('.product-remove .remove').data('product_id');
                    var quantityInput = $(this).closest('.quantity').find('.qty');
                    var quantityValue = parseInt(quantityInput.val());
                
                    // Update the quantity input on the page
                    quantityInput.val(quantityValue).trigger('change');
                    $('button[name="update_cart"]').trigger('click');
                
                    updateCartItemQuantity(productId,quantityValue);    

            }
    });
        
    function updateCartItemQuantity(productId, newQuantity) {

            $.ajax({
                url: myAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'cm_ajax_update_product_from_cart',
                    product_id: productId,
                    quantity: newQuantity,
                    _ajax_nonce: myAjax.nonce,
                },
                success: function(response) {
                 updateMiniCartTotal();
                },
                error: function(error) {
                    console.error(error); // Handle errors
                }
            });
    }

    $('.single_add_to_cart_button').on('click', function(e) {
          
            // Prevent the default form submission if necessary
            if(getSessionIdFromCookie !== null){
                e.preventDefault();

                $('.single-product .single_add_to_cart_button').prop('disabled', true).addClass('adding');
    
                // Capture the quantity value
                var quantityValue = $('.input-text.qty.text').val();
        
                // Capture the product ID
                var productId = $(this).val(); // This assumes the button's value attribute holds the product ID
        
                // Log or use the captured values
                console.log("Updating Product ID: " + productId + ", Quantity: " + quantityValue);
                updateCartItemQuantityFromProductPage(productId,quantityValue);
            }
    });
        
    function updateCartItemQuantityFromProductPage(productId, newQuantity) {

            $.ajax({
                url: myAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'cm_ajax_update_product_from_product_page',
                    product_id: productId,
                    quantity: newQuantity,
                    _ajax_nonce: myAjax.nonce,
                },
                success: function(response) {
                    location.reload();
                    updateMiniCartTotal(); 
                    // If the server responded with a success message, display it
                    $('.woocommerce-notices-wrapper').html('<div class="woocommerce-message" role="alert">' + response.data.message+ '</div>');
                    $('html, body').animate({
                        scrollTop: $('.woocommerce-notices-wrapper').offset().top
                    }, 1000);             
     
                },
                error: function(jqXHR) {
                    var response = JSON.parse(jqXHR.responseText);
                    if (response && response.message) {
                        console.error("Server-side error: " + response.message);
                    } else {
                        console.error("AJAX error without a detailed description.");
                    }
                }
                
                
            });
    }

    $(document).on('click', 'td.product-remove .remove ', function(e) {
        if(getSessionIdFromCookie !== null){
            e.preventDefault();

            var productId = $(this).data('product_id');

                $.ajax({
                    url: myAjax.ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'cm_ajax_remove_product_from_cart',
                        _ajax_nonce: myAjax.nonce,
                        product_id: productId,                    
                    },
                    success: function(response) {   
                        console.log(response);         
                        updateMiniCartTotal();
                        $('button[name="update_cart"]').trigger('click');
                        location.reload();
                    },
                    error: function(error) {
                        console.error(error); // If there's an error, it will show up here
                    }
                }); 
        }
    });


    $(document).on('click', 'li.mini_cart_item .remove_from_cart_button', function(e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        if (getSessionIdFromCookie !== null) {
            var productId = $(this).data('product_id');
            var cartItemKey = $(this).data('cart_item_key');
        
            $.ajax({
                url: myAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'cm_ajax_remove_product_from_cart',
                    _ajax_nonce: myAjax.nonce,
                    product_id: productId,
                    cart_item_key: cartItemKey
                },
                success: function(response) {
                    updateMiniCartTotal();
                    },
                error: function(error) {
                    console.error('Error removing mini cart item:', error); // Modified log
                }
            });
        }
    });


    $('body').on('ajax_add_to_cart', function() {
        updateMiniCartTotal();   
    });


    function getSessionIdFromCookie() {   
        // Get the session_id from the cookie
        var sessionId = Cookies.get('cm_session_id'); // Using js-cookie
    
        // Check if the cookie is not found
        if (typeof sessionId === 'undefined') {
            console.error('Session ID cookie not found.');
            return null;
        }
        return sessionId;
    }

    function updateMiniCartTotal() {


        if(getSessionIdFromCookie !== null){       
            var session_id = getSessionIdFromCookie();
 
                $.ajax({
                    url: myAjax.ajaxurl,
                    method: 'POST',
                    data: {
                        action: 'get_mini_cart_total_for_session',
                        _ajax_nonce: myAjax.nonce,
                        session_id: session_id,
                        nonce: myAjax.nonce
                    },
                    success: function(response) {
                        if (response.success) {   
  
                            var $responseTotal = $(response.data.total);

                            var currencySymbol = '';
                            var amount = '';
                          
                            var currencySymbol = $responseTotal.find('.woocommerce-Price-currencySymbol').html();
                            // Extract the amount, assuming it's the text immediately following the currency symbol
                            var amount = $responseTotal.find('bdi').clone().children().remove().end().text().trim(); 
                            $('.cart-contents .amount').html('<font style="vertical-align: inherit;"><font style="vertical-align: inherit;">' +currencySymbol + amount + '</font></font>');
                     
                        }
                    },
                    error: function(error) {
                        console.error('Error updating mini cart:', error);
                    }
                });
            
        } 
    }
   
    // PUNCHOUT ORDER MESSAGE
    $('.wc-proceed-to-checkout a.checkout-button').on('click', function(e) {
        e.preventDefault();  
            if(getSessionIdFromCookie !== null){         
                create_complete_punchout_order_cxml();         
            }
    });
    
    function create_complete_punchout_order_cxml() {

        var session_id = getSessionIdFromCookie();      
    
            $.ajax({
                    url: myAjax.ajaxurl,
                    method: 'POST',
                    data: {
                        action: 'create_complete_punchout_order_cxml',
                        _ajax_nonce: myAjax.nonce,
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

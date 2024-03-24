jQuery(document).ready(function($) {


        updateMiniCartTotal();
   

        $(document).on('click', '.quantity-action.plus, .quantity-action.minus', function(e) {
            e.preventDefault();

            var productId = $(this).closest('tr.woocommerce-cart-form__cart-item').find('.product-remove .remove').data('product_id');
            var quantityInput = $(this).closest('.quantity').find('.qty');
            var quantityValue = parseInt(quantityInput.val());
              
            // Log the product ID and the new quantity
            console.log('Product ID:', productId, 'New Quantity:', quantityValue);
        
            // Update the quantity input on the page
            quantityInput.val(quantityValue).trigger('change');
        
            updateCartItemQuantity(productId,quantityValue);
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
                    console.log(response); // Handle the response
                    updateMiniCartTotal();

                
                    // Optionally refresh part of your page here, e.g., cart totals
                },
                error: function(error) {
                    console.error(error); // Handle errors
                }
            });
        }
        


    $(document).on('click', 'td.product-remove .remove', function(e) {
        var productId = $(this).data('product_id');

        console.log(productId);

            $.ajax({
                url: myAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'cm_ajax_remove_product_from_cart',
                    _ajax_nonce: myAjax.nonce,
                    product_id: productId,                    
                },
                success: function(response) {
                    console.log(response); // Check the console for the success message
                    updateMiniCartTotal();
               
                    location.reload();
                },
                error: function(error) {
                    console.error(error); // If there's an error, it will show up here
                }
            }); 
     });


     $('body').on('added_to_cart', function() {
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
         
        alert('updating');
        if(getSessionIdFromCookie !== null){
            // Assuming you have a function to get the session ID
            var session_id = getSessionIdFromCookie(); // Implement this function to retrieve session ID from a cookie or local storage

            console.log('session id' + session_id);

            if(session_id ){
                alert('cokkie set');

                $.ajax({
                    url: myAjax.ajaxurl,
                    method: 'POST',
                    data: {
                        action: 'get_mini_cart_total_for_session',
                        session_id: session_id,
                        nonce: myAjax.nonce
                    },
                    success: function(response) {
                        if (response.success) {
                            // Update the mini cart total with the response data
                           // $('.cart-contents .amount').text(response.data.total);
                           alert(response.data.total);
                           $('.mini-cart-total').text(response.data.total)
                            console.log('cart total ' + response.data.total);
                        }
                    },
                    error: function(error) {
                        console.error('Error updating mini cart:', error);
                    }
                });
            }
           
        }

        // Only update the mini-cart total if the cm_session_key cookie is present
        // if (sessionKey !== null && typeof sessionTotalData !== 'undefined') {
        //     var formattedSessionTotal = wc_price(sessionTotalData.sessionTotal);
        //     $('.woocommerce-mini-cart__total .woocommerce-Price-amount, .cart-subtotal .woocommerce-Price-amount').html(formattedSessionTotal);
        // }
    }


});

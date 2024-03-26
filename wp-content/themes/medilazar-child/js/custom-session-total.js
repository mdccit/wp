jQuery(document).ready(function($) {


        updateMiniCartTotal();
   

        $(document).on('click', '.quantity-action.plus, .quantity-action.minus', function(e) {

            if(getSessionIdFromCookie !== null){
                    e.preventDefault();

                    var productId = $(this).closest('tr.woocommerce-cart-form__cart-item').find('.product-remove .remove').data('product_id');
                    var quantityInput = $(this).closest('.quantity').find('.qty');
                    var quantityValue = parseInt(quantityInput.val());
                    
                    // Log the product ID and the new quantity
                    console.log('Product ID:', productId, 'New Quantity:', quantityValue);
                
                    // Update the quantity input on the page
                    quantityInput.val(quantityValue).trigger('change');
                
                    updateCartItemQuantity(productId,quantityValue);
                    updateMiniCartTotal();
                    // location.reload();

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
                    console.log(response); // Handle the response
                    updateMiniCartTotal();

                
                    // Optionally refresh part of your page here, e.g., cart totals
                },
                error: function(error) {
                    console.error(error); // Handle errors
                }
            });
        }
        


    $(document).on('click', 'td.product-remove .remove , .remove_from_cart_button ', function(e) {

        e.preventDefault();

        console.log('removing product');

        if(getSessionIdFromCookie !== null){
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
                        console.log(response); // Check the console for the success message
                        updateMiniCartTotal();
                        location.reload();
                    },
                    error: function(error) {
                        console.error(error); // If there's an error, it will show up here
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
            // Assuming you have a function to get the session ID
            var session_id = getSessionIdFromCookie(); // Implement this function to retrieve session ID from a cookie or local storage

            if(session_id ){
  
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
    
                            // $('.woocommerce-mini-cart__total .woocommerce-Price-amount.amount').html(response.data.total);
                            // $('.woocommerce-Price-amount').each(function() {
                            //     // Replace the current content with the new HTML structure from the response
                            //     $(this).replaceWith(response.data.total);
                            // });

                            var $responseTotal = $(response.data.total);

                            amount = response.data.total;

                            var currencySymbol = '';
                            var amount = '';
                            amount = response.data.total;
                            console.log('amount ' + amount);
                            var currencySymbol = $responseTotal.find('.woocommerce-Price-currencySymbol').html();

                            // Extract the amount, assuming it's the text immediately following the currency symbol
                            var amount = $responseTotal.find('bdi').clone().children().remove().end().text().trim();
                        
                          
                            $('.cart-contents .amount').html('<font style="vertical-align: inherit;"><font style="vertical-align: inherit;">' +currencySymbol + amount + '</font></font>');
                            // location.reload();
                        }
                    },
                    error: function(error) {
                        console.error('Error updating mini cart:', error);
                    }
                });
            }
        } 
    }

    function updateMiniCartTotalAndSubtotal(currencySymbol, amount) {
            // Here you can use the currencySymbol and amount to update the mini cart
            // This is an example, replace '.mini-cart-total' and '.mini-cart-subtotal' with your actual selectors
            $('.mini-cart-total').html(currencySymbol + amount);
            $('.mini-cart-subtotal').html(currencySymbol + amount);

    }


    // $('.button[name="update_cart"]').on('click', function(event) {
    //     // Prevent the default action of the button (optional)
    //     event.preventDefault();

    //     // Your custom logic here
    //     alert('Update cart button clicked!');
    // });

});

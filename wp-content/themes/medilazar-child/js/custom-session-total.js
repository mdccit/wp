jQuery(document).ready(function($) {

    function getCookie(name) {
        var value = "; " + document.cookie;
        var parts = value.split("; " + name + "=");
        if (parts.length == 2) return parts.pop().split(";").shift();
        else return null;
    }

    function wc_price(price) {
        return '$' + parseFloat(price).toFixed(2); // Simplified example
    }

    function updateMiniCartTotal() {
        var sessionKey = getCookie('cm_session_key');
        
        // Only update the mini-cart total if the cm_session_key cookie is present
        if (sessionKey !== null && typeof sessionTotalData !== 'undefined') {
            var formattedSessionTotal = wc_price(sessionTotalData.sessionTotal);
            $('.woocommerce-mini-cart__total .woocommerce-Price-amount, .cart-subtotal .woocommerce-Price-amount').html(formattedSessionTotal);
        }
    }

    // Initial update
    updateMiniCartTotal();

    // Update on WooCommerce AJAX cart refresh, conditional on the cookie
    $(document.body).on('updated_wc_div', function() {
        updateMiniCartTotal();
    });

   
        // $('td.product-remove .remove').click(function(e) {

        //     alert('test');
        //     e.preventDefault(); // Prevent the default link behavior

        //     console.log(myAjax.ajaxurl);
    
        //     var productId = $(this).data('product_id'); // Get the product ID from the data attribute
        //     var removeUrl = $(this).attr('href'); // The URL with remove_item query, if needed
        //     var nonce = removeUrl.split('_wpnonce=')[1]; // Assuming nonce is part of the URL
    
        //     console.log('Product ID: '.$productId);
        //     $.ajax({
        //         type: 'POST',
        //         url: myAjax.ajaxurl, // The AJAX URL passed from wp_localize_script
        //         data: {
        //             action: 'initialize_cart_handling', // Your AJAX action
        //             product_id: productId,
        //             // cm_session_key: 'your-session-key', // Retrieve this as needed for your logic
        //             // _wpnonce: nonce // Passing the nonce for verification
        //         },
        //         success: function(response) {
        //             if(response.success) {
        //                 alert('Product removed');
        //                 // Optionally refresh part of your page here, e.g., the cart
        //                 location.reload(); // Reload the page to reflect changes, or implement a more graceful update
        //             } else {
        //                 alert('Failed to remove product');
        //             }
        //         }
        //     });
        // });
    
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
                    location.reload();
                },
                error: function(error) {
                    console.error(error); // If there's an error, it will show up here
                }
            }); 
     });


});

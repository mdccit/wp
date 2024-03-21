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

        $('td.product-remove .remove').click(function(e) {
            e.preventDefault(); // Prevent the default link behavior

            console.log(myAjax.ajaxurl);
    
            var productId = $(this).data('product_id'); // Get the product ID from the data attribute
            var removeUrl = $(this).attr('href'); // The URL with remove_item query, if needed
            var nonce = removeUrl.split('_wpnonce=')[1]; // Assuming nonce is part of the URL
    
            $.ajax({
                type: 'POST',
                url: myAjax.ajaxurl, // The AJAX URL passed from wp_localize_script
                data: {
                    action: 'cm_ajax_remove_product_from_cart', // Your AJAX action
                    product_id: productId,
                    cm_session_key: 'your-session-key', // Retrieve this as needed for your logic
                    _wpnonce: nonce // Passing the nonce for verification
                },
                success: function(response) {
                    if(response.success) {
                        alert('Product removed');
                        // Optionally refresh part of your page here, e.g., the cart
                        location.reload(); // Reload the page to reflect changes, or implement a more graceful update
                    } else {
                        alert('Failed to remove product');
                    }
                }
            });
        });
    

    // $('.remove-product-button').on('click', function() {
    //     var productId = $(this).data('product-id');
    //     $.ajax({
    //         url: wc_cart_fragments_params.ajax_url,
    //         type: 'POST',
    //         data: {
    //             action: 'cm_remove_product_from_cart',
    //             product_id: productId,
    //             cm_session_key: Cookies.get('cm_session_key') // Assuming you're using js-cookie
    //         },
    //         success: function(response) {
    //             // Handle response, refresh cart/mini-cart if needed
    //             $(document.body).trigger('wc_fragment_refresh');
    //         }
    //     });
    // });


});
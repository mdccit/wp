jQuery(document).ready(function($) {
    // Move the custom button beside the Place Order button and show it
    $(document).on('click', '#sendPunchOutOrder', function(e) {
        e.preventDefault();
        alert('sending....');
    });
});

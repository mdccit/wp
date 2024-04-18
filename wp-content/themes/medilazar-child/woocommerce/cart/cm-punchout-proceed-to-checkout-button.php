<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<?php
// Check if the 'session_id' cookie is set
if (isset($_COOKIE['cm_session_id'])) {
    // Display the form for punchout checkout
    ?>
    <form action="<?php echo get_stylesheet_directory_uri(); ?>/process-punchout.php" method="post">
        <input type="hidden" name="action" value="process_punchout">
        <button type="submit" class="checkout-button cm-punchout-checkout-button button alt wc-forward<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>">
            <?php esc_html_e('Finalizar Compra', 'woocommerce'); ?>
        </button>
    </form>
    <?php
} else {
    // Display the regular WooCommerce checkout link
    ?>
    <a href="<?php echo esc_url(wc_get_checkout_url()); ?>" class="checkout-button cm-punchout-checkout-button button alt wc-forward<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>">
        <?php esc_html_e('Proceed to checkout', 'woocommerce'); ?>
    </a>
    <?php
}
?>

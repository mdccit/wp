<?php
defined( 'ABSPATH' ) || exit;

require_once get_stylesheet_directory() . '/inc/class-cm-session-manager.php';
$session_manager = new \CM\Session_Manager();

do_action( 'woocommerce_before_mini_cart' ); ?>

<ul class="woocommerce-mini-cart cart_list product_list_widget <?php echo esc_attr( $args['list_class'] ); ?>">
    <?php
    if ( ! WC()->cart->is_empty() ) :
        foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) :
            $_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
            $product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

            if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key ) ) :
                $product_name      = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );
                $thumbnail         = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
                $product_price     = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
                $product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
                ?>
                <li class="woocommerce-mini-cart-item <?php echo esc_attr( apply_filters( 'woocommerce_mini_cart_item_class', 'mini_cart_item', $cart_item, $cart_item_key ) ); ?>">  
                <?php echo wc_get_formatted_cart_item_data( $cart_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    <?php if ( empty( $product_permalink ) ) : ?>
                        <?php echo $thumbnail . $product_name; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    <?php else : ?>
                        <a href="<?php echo esc_url( $product_permalink ); ?>">
                            <?php echo $thumbnail . $product_name; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        </a>
                    <?php endif; ?>
                    <?php echo wc_get_formatted_cart_item_data( $cart_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    <?php echo apply_filters( 'woocommerce_widget_cart_item_quantity', '<span class="quantity">' . sprintf( '%s &times; %s', $cart_item['quantity'], $product_price ) . '</span>', $cart_item, $cart_item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    <?php 
                     if($session_manager->is_session_specific_user())
                        $new_cart_item_key = array_key_exists('cart_item_key',$cart_item) ? $cart_item['cart_item_key'] : $cart_item['key'];
                     else
                        $new_cart_item_key = $cart_item['key']; 
                    ?>
                                 
                    <a href="<?php echo esc_url( wc_get_cart_remove_url($new_cart_item_key) ); ?>" class="remove remove_from_cart_button" aria-label="<?php esc_attr_e( 'Remove this item', 'woocommerce' ); ?>" data-product_id="<?php echo esc_attr( $product_id ); ?>" data-cart_item_key="<?php echo esc_attr( $cart_item_key ); ?>">&times;</a>
                </li>
                <?php
            endif;
        endforeach;
    else :
        ?>
        <li class="empty"><?php esc_html_e( 'No products in the cart.', 'woocommerce' ); ?></li>
        <?php
    endif;
    ?>
</ul>

<?php do_action( 'woocommerce_after_mini_cart' ); ?>

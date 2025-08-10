<?php

/**
 * Review order table
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/review-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 5.2.0
 */

defined('ABSPATH') || exit;
?>

<div class="woocommerce-checkout-review-order-table">
    <?php
    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
        $_product     = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
        $product_name = apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key);

        if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key)) {
            $variation_data = wc_get_formatted_cart_item_data($cart_item, false); // ← this gets formatted variation HTML
    ?>
            <div class="checkout-summary__product">
                <div class="checkout-summary__product-image">
                    <?php echo $_product->get_image(); ?>
                </div>
                <div class="checkout-summary__product-info">
                    <h6 class="checkout-summary__product-name"><?php echo $product_name; ?></h6>

                    <?php if ($variation_data) : ?>
                        <div class="checkout-summary__product-variation">
                            <?php echo $variation_data; ?>
                        </div>
                    <?php endif; ?>

                    <div class="checkout-summary__product-price">
                        <span class="checkout-summary__product-quantity"><?php echo $cart_item['quantity']; ?> szt.</span>
                        <span class="checkout-summary__product-value">
                            <?php echo apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cart_item, $cart_item_key); ?>
                        </span>
                    </div>
                </div>
            </div>
    <?php
        }
    }
    ?>
    <div class="checkout-summary__totals">

        <div class="checkout-summary__subtotal">
            <div class="checkout-summary__subtotal-label"><?php esc_html_e('Koszt produktów', 'alko'); ?></div>
            <div class="checkout-summary__subtotal-value"><?php wc_cart_totals_subtotal_html(); ?></div>
        </div>

        <?php
        $coupons = WC()->cart->get_coupons();

        if (! empty($coupons)) :
            foreach ($coupons as $code => $coupon) :
        ?>
                <div class="checkout-summary__discount">
                    <div class="checkout-summary__discount-label">
                        <?php echo esc_html__('Rabat', 'alko'); ?>
                    </div>
                    <div class="checkout-summary__discount-value">
                        <?php wc_cart_totals_coupon_html($coupon); ?>
                    </div>
                </div>
        <?php
            endforeach;
        endif;
        ?>

        <?php if (WC()->cart->needs_shipping()) : ?>
            <div class="checkout-summary__shipping">
                <div class="checkout-summary__shipping-label"><?php esc_html_e('Opłata za dostawę', 'alko'); ?></div>
                <div class="checkout-summary__shipping-value">
                    <?php echo WC()->cart->get_cart_shipping_total(); ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="checkout-summary__total">
            <div class="checkout-summary__total-label"><?php esc_html_e('Suma', 'alko'); ?></div>
            <div class="checkout-summary__total-value"><?php wc_cart_totals_order_total_html(); ?></div>
        </div>

    </div>



</div>